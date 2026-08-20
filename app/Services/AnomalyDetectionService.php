<?php

namespace App\Services;

use App\Models\TagihanBulanan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnomalyDetectionService
{
    /**
     * Menganalisis hasil pembacaan meteran air dan mendeteksi anomali berdasarkan histori.
     *
     * @param int|string $pelangganId
     * @param float $meteranSebelum
     * @param float $meteranSesudah
     * @return array
     */
    public function analyzeReading($pelangganId, float $meteranSebelum, float $meteranSesudah): array
    {
        $pemakaian = max(0, $meteranSesudah - $meteranSebelum);
        
        // 1. Ambil data histori pemakaian 12 bulan terakhir
        $history = TagihanBulanan::where('pelanggan_id', $pelangganId)
            ->where('meteran_sesudah', '>=', DB_raw_or_val('meteran_sebelum'))
            ->orderBy('bulan', 'desc')
            ->take(12)
            ->get();

        $historyUsages = $history->map(function ($item) {
            return (float) ($item->pemakaian_kubik ?? max(0, $item->meteran_sesudah - $item->meteran_sebelum));
        })->filter(function ($val) {
            return $val >= 0;
        })->values()->toArray();

        // 2. Evaluasi Kondisi Khusus / Error Dasar
        if ($meteranSesudah < $meteranSebelum) {
            return [
                'status' => 'TERINDIKASI_ANOMALI',
                'anomaly_score' => 1.00,
                'pemakaian_kubik' => $pemakaian,
                'rerata_historis' => !empty($historyUsages) ? round(array_sum($historyUsages) / count($historyUsages), 2) : 0,
                'persentase_lonjakan' => 0,
                'catatan' => 'ANOMALI KRITIS: Angka meteran sesudah (' . $meteranSesudah . ') lebih kecil daripada meteran sebelum (' . $meteranSebelum . '). Harap periksa fisik meteran.',
            ];
        }

        // 3. Jika belum ada data histori (Cold Start / Pelanggan Baru)
        if (count($historyUsages) < 2) {
            // Batas acuan wajar default: <= 60 m3/bulan
            $isAnomaly = $pemakaian > 60;
            $anomalyScore = $isAnomaly ? 0.75 : 0.15;
            
            $catatan = $isAnomaly
                ? "PEMAKAIAN TINGGI (COLD START): Pemakaian {$pemakaian} m³ melebihi ambang batas wajar pelanggan baru (60 m³)."
                : "NORMAL: Pemakaian {$pemakaian} m³ berada dalam batas wajar pelanggan baru.";

            return [
                'status' => $isAnomaly ? 'TERINDIKASI_ANOMALI' : 'NORMAL',
                'anomaly_score' => $anomalyScore,
                'pemakaian_kubik' => $pemakaian,
                'rerata_historis' => $pemakaian,
                'persentase_lonjakan' => 0,
                'catatan' => $catatan,
            ];
        }

        // 4. Perhitungan Statistik (Rerata & Standar Deviasi)
        $count = count($historyUsages);
        $mean = array_sum($historyUsages) / $count;

        $variance = 0.0;
        foreach ($historyUsages as $val) {
            $variance += pow($val - $mean, 2);
        }
        $stdDev = sqrt($variance / max(1, $count - 1));

        // 5. Perhitungan Z-score & Isolation Forest Anomaly Score Proxy
        $zScore = ($stdDev > 0.01) ? abs($pemakaian - $mean) / $stdDev : ($pemakaian > $mean ? 3.0 : 0.0);
        
        // Formulasi Isolation Forest Anomaly Score (0.00 - 1.00)
        // Score = 1 - exp(-|Z| / 2)
        $anomalyScore = round(1 - exp(-abs($zScore) / 2), 2);

        $persentaseLonjakan = ($mean > 0) ? round((($pemakaian - $mean) / $mean) * 100, 1) : 0;

        // Kriteria Anomali
        $isAnomaly = ($anomalyScore >= 0.60) || ($zScore >= 2.5) || ($mean > 0 && $pemakaian > 3 * $mean && $pemakaian > 30);
        $status = $isAnomaly ? 'TERINDIKASI_ANOMALI' : 'NORMAL';

        // 6. Buat Penjelasan Analisis AI (Gunakan LLM API jika tersedia, atau Rule Engine)
        $catatanAI = $this->generateAIExplanation($status, $pemakaian, $mean, $stdDev, $persentaseLonjakan, $anomalyScore);

        return [
            'status' => $status,
            'anomaly_score' => $anomalyScore,
            'pemakaian_kubik' => round($pemakaian, 2),
            'rerata_historis' => round($mean, 2),
            'persentase_lonjakan' => $persentaseLonjakan,
            'catatan' => $catatanAI,
        ];
    }

    /**
     * Menghasilkan penjelasan analisis AI menggunakan LLM API (OpenAI/Gemini) atau Rule-Based Engine.
     */
    protected function generateAIExplanation(string $status, float $pemakaian, float $mean, float $stdDev, float $persentaseLonjakan, float $anomalyScore): string
    {
        $apiKey = env('OPENAI_API_KEY');
        $baseUrl = env('OPENAI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta/openai/');

        if ($apiKey) {
            try {
                $endpoint = rtrim($baseUrl, '/') . '/chat/completions';
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(4)->post($endpoint, [
                    'model' => 'gemini-1.5-flash',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah asisten AI sistem PAMSIMAS yang bertugas memberikan penjelasan ringkas (max 2 kalimat) tentang validasi pencatatan meter air pelanggan.',
                        ],
                        [
                            'role' => 'user',
                            'content' => "Status: {$status}. Pemakaian bulan ini: {$pemakaian} m³. Rata-rata historis: " . round($mean, 1) . " m³. Persentase perubahan: {$persentaseLonjakan}%. Anomaly Score: {$anomalyScore}. Berikan ringkasan penjelasan singkat dan ramah untuk petugas lapangan.",
                        ],
                    ],
                    'max_tokens' => 100,
                ]);

                if ($response->successful() && isset($response->json()['choices'][0]['message']['content'])) {
                    return trim($response->json()['choices'][0]['message']['content']);
                }
            } catch (\Throwable $e) {
                Log::warning('AI Explanation API failed, falling back to rule engine', ['error' => $e->getMessage()]);
            }
        }

        // Fallback Rule Engine
        if ($status === 'TERINDIKASI_ANOMALI') {
            return "TERINDIKASI ANOMALI: Pemakaian {$pemakaian} m³ mengalami lonjakan {$persentaseLonjakan}% dibanding rata-rata historis (" . round($mean, 1) . " m³). Mohon pastikan tidak ada kebocoran atau kesalahan pembacaan meter.";
        }

        return "PEMAKAIAN NORMAL: Pemakaian {$pemakaian} m³ sesuai dengan pola penggunaan historis pelanggan (Rata-rata: " . round($mean, 1) . " m³).";
    }
}
