<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OCRController extends Controller
{
    public function readMeter(Request $request)
    {
        $request->validate(["image" => "required|string"]);

        $imageData = $request->input("image");
        if (str_contains($imageData, ",")) {
            [, $imageData] = explode(",", $imageData, 2);
        }

        $mimeType = "image/jpeg";
        $orig = $request->input("image");
        if (str_contains($orig, "image/png")) $mimeType = "image/png";
        elseif (str_contains($orig, "image/webp")) $mimeType = "image/webp";

        try {
            $apiKey = env("GOOGLE_VISION_API_KEY", env("OPENAI_API_KEY"));
            $baseUrl = "https://generativelanguage.googleapis.com/v1beta";

            if (!$apiKey) {
                return response()->json(["success" => false, "message" => "API key tidak dikonfigurasi."], 500);
            }

            $prompt = "Kamu adalah sistem pembaca angka meteran air.\nLihat foto meteran air ini dan baca angka pada KOTAK COUNTER ODOMETER (baris angka di tengah atas meteran, contoh: 000051).\nABAIKAN nomor seri, merek, dan semua teks lain.\nJawab HANYA dengan angka bulat saja. Contoh: 51";

            $resp = Http::timeout(30)->post(
                "{$baseUrl}/models/gemini-1.5-flash:generateContent?key={$apiKey}",
                [
                    "contents" => [[
                        "parts" => [
                            ["text" => $prompt],
                            ["inline_data" => ["mime_type" => $mimeType, "data" => $imageData]],
                        ],
                    ]],
                    "generationConfig" => ["temperature" => 0, "maxOutputTokens" => 20],
                ]
            );

            if (!$resp->successful()) {
                Log::error("Gemini OCR error", ["status" => $resp->status(), "body" => $resp->body()]);
                return response()->json(["success" => false, "message" => "API error: " . $resp->status()], 500);
            }

            $rawText = trim($resp->json("candidates.0.content.parts.0.text") ?? "");
            preg_match("/\d+/", $rawText, $m);
            $value = isset($m[0]) ? (int)$m[0] : null;

            if ($value === null || $value < 0 || $value > 999999) {
                return response()->json(["success" => false, "raw" => $rawText, "message" => "Angka tidak terdeteksi."]);
            }

            return response()->json(["success" => true, "value" => $value, "raw" => $rawText, "confidence" => 95]);
        } catch (\Exception $e) {
            Log::error("OCR Exception: " . $e->getMessage());
            return response()->json(["success" => false, "message" => "Gagal membaca foto meteran."], 500);
        }
    }
}
