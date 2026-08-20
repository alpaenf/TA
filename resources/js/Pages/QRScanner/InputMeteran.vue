<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-50 py-8 px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-6">
                    <button
                        @click="goBack"
                        class="text-blue-600 hover:text-blue-800 mb-4 flex items-center gap-2 font-medium"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali ke Scanner
                    </button>
                    <div class="text-center">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Input Meteran Air</h1>
                        <p class="text-gray-600">Masukkan pembacaan meteran air bulan ini</p>
                    </div>
                </div>

                <!-- Info Pelanggan Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informasi Pelanggan
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">ID Pelanggan</p>
                            <p class="font-bold text-gray-800">{{ pelanggan.id_pelanggan }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nama</p>
                            <p class="font-bold text-gray-800">{{ pelanggan.nama_pelanggan }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">RT / RW</p>
                            <p class="font-bold text-gray-800">RT {{ pelanggan.rt }} / RW {{ pelanggan.rw }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Wilayah</p>
                            <p class="font-bold text-gray-800">{{ pelanggan.wilayah || '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Meteran Terakhir -->
                <div v-if="tagihan_terbaru" class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 mb-6 text-white">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Meteran Terakhir
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                            <p class="text-blue-100 text-sm mb-1">Bulan</p>
                            <p class="text-xl font-bold">{{ formatBulan(tagihan_terbaru.bulan) }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                            <p class="text-blue-100 text-sm mb-1">Meteran Akhir</p>
                            <p class="text-3xl font-bold">{{ tagihan_terbaru.meteran_sesudah }}</p>
                            <p class="text-blue-100 text-xs">m³</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                            <p class="text-blue-100 text-sm mb-1">Pemakaian</p>
                            <p class="text-2xl font-bold">{{ tagihan_terbaru.pemakaian_kubik }}</p>
                            <p class="text-blue-100 text-xs">m³</p>
                        </div>
                    </div>
                </div>

                <!-- Form Input Meteran -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Form Input Meteran Baru
                    </h2>
                    
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Bulan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Bulan <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.bulan"
                                type="month"
                                :max="getCurrentMonth()"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            />
                            <p class="text-xs text-gray-500 mt-1">Anda dapat memilih bulan sebelumnya jika belum tercatat</p>
                        </div>

                        <!-- Meteran Sebelum (Read Only) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Meteran Sebelum
                            </label>
                            <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-lg">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-2xl font-bold text-gray-600">{{ meteranSebelum }}</span>
                                <span class="text-sm text-gray-500 ml-auto">m³</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Meteran terakhir yang tercatat</p>
                        </div>

                        <!-- Meteran Sesudah -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Meteran Sesudah (Pembacaan Baru) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    v-model.number="form.meteran_sesudah"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    placeholder="Masukkan angka meteran saat ini"
                                    class="w-full px-4 py-3 pr-12 border-2 border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-lg font-semibold"
                                    :class="{ 'border-red-500': form.meteran_sesudah && form.meteran_sesudah < meteranSebelum }"
                                />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">m³</span>
                            </div>
                            <p v-if="form.meteran_sesudah && form.meteran_sesudah < meteranSebelum" class="text-xs text-red-500 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Meteran sesudah harus lebih besar atau sama dengan meteran sebelum!
                            </p>
                            <p v-else class="text-xs text-gray-500 mt-1">Masukkan angka yang tertera pada meteran air atau gunakan scan OCR otomatis di bawah.</p>

                            <!-- AI Anomaly Detection Card -->
                            <div v-if="isAnalyzingAnomaly" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg animate-pulse flex items-center gap-2 text-xs text-blue-700">
                                <svg class="w-4 h-4 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                AI sedang mengevaluasi histori pemakaian...
                            </div>

                            <div v-else-if="anomalyAnalysis" class="mt-3 p-4 rounded-xl border-2 transition" :class="anomalyAnalysis.status === 'TERINDIKASI_ANOMALI' ? 'bg-amber-50 border-amber-300 text-amber-900' : 'bg-emerald-50 border-emerald-300 text-emerald-900'">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-2 font-bold text-sm">
                                        <span v-if="anomalyAnalysis.status === 'TERINDIKASI_ANOMALI'" class="px-2.5 py-1 bg-amber-500 text-white rounded-full text-xs uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            PERLU VERIFIKASI (ANOMALI)
                                        </span>
                                        <span v-else class="px-2.5 py-1 bg-emerald-600 text-white rounded-full text-xs uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            PEMAKAIAN NORMAL
                                        </span>
                                    </div>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-white/70 border border-current">
                                        Skor AI: {{ anomalyAnalysis.anomaly_score }}
                                    </span>
                                </div>
                                <p class="text-xs mt-2 font-medium leading-relaxed">
                                    {{ anomalyAnalysis.catatan }}
                                </p>
                            </div>
                        </div>

                        <!-- Opsi Abunemen -->
                        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                            <label class="flex items-center justify-between gap-4 cursor-pointer">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Kenakan Biaya Abunemen</p>
                                    <p class="text-xs text-gray-600 mt-1">Nonaktifkan jika bulan ini tidak dikenakan abunemen</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-semibold" :class="form.ada_abunemen ? 'text-green-700' : 'text-gray-600'">
                                        {{ form.ada_abunemen ? 'Ya' : 'Tidak' }}
                                    </span>
                                    <input
                                        v-model="form.ada_abunemen"
                                        type="checkbox"
                                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                </div>
                            </label>
                        </div>

                        <!-- Upload Foto Meteran & OCR Engine -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Upload Foto Meteran Air & OCR Auto-Scan
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    @click="triggerUploadFoto"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 border-gray-200 bg-white text-gray-700 font-semibold hover:border-blue-300 hover:text-blue-700 transition"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-5l-4-4m0 0L8 11m4-4v12"/>
                                    </svg>
                                    Upload Foto
                                </button>

                                <button
                                    type="button"
                                    @click="triggerFotoLangsung"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 border-blue-200 bg-blue-50 text-blue-700 font-semibold hover:bg-blue-100 transition"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h2l1.172-1.172A2 2 0 019.586 5h4.828a2 2 0 011.414.586L17 7h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16a3 3 0 100-6 3 3 0 000 6z"/>
                                    </svg>
                                    Foto Langsung
                                </button>
                            </div>

                            <input
                                ref="inputFotoUpload"
                                type="file"
                                accept="image/*"
                                @change="handleFotoMeteranChange"
                                class="hidden"
                            />

                            <input
                                ref="inputFotoKamera"
                                type="file"
                                accept="image/*"
                                capture="environment"
                                @change="handleFotoMeteranChange"
                                class="hidden"
                            />

                            <p class="text-xs text-gray-500 mt-1">Upload foto dari galeri atau ambil foto langsung untuk menjalankan pembacaan angka otomatis menggunakan OCR.</p>

                            <!-- Preview & OCR Button -->
                            <div v-if="fotoMeteranPreview" class="mt-4 p-4 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <img :src="fotoMeteranPreview" alt="Preview foto meteran" class="w-24 h-24 object-cover rounded-lg border border-gray-300 shadow-sm" />
                                        <div>
                                            <p class="text-xs font-bold text-gray-700">Foto Meteran Terunggah</p>
                                            <button
                                                type="button"
                                                @click="clearFotoMeteran"
                                                class="text-xs font-semibold text-red-600 hover:text-red-700 mt-1 block"
                                            >
                                                Hapus Foto
                                            </button>
                                        </div>
                                    </div>

                                    <!-- OCR Trigger Button -->
                                    <button
                                        type="button"
                                        @click="runOCRScan"
                                        :disabled="isScanningOCR"
                                        class="w-full sm:w-auto px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-sm rounded-lg shadow-md hover:from-blue-700 hover:to-indigo-700 transition flex items-center justify-center gap-2 disabled:opacity-50"
                                    >
                                        <svg v-if="isScanningOCR" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        {{ isScanningOCR ? 'Membaca Angka Meter...' : 'Scan Otomatis Angka Meter (OCR)' }}
                                    </button>
                                </div>

                                <!-- Progress Bar OCR -->
                                <div v-if="isScanningOCR" class="mt-3">
                                    <div class="flex justify-between text-xs font-semibold text-blue-700 mb-1">
                                        <span>Proses Preprocessing & OCR Engine...</span>
                                        <span>{{ ocrProgress }}%</span>
                                    </div>
                                    <div class="w-full bg-blue-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="{ width: ocrProgress + '%' }"></div>
                                    </div>
                                </div>

                                <!-- OCR Result Confidence Badge -->
                                <div v-if="ocrConfidence !== null" class="mt-3 p-3 bg-blue-100/70 border border-blue-200 rounded-lg flex items-center justify-between text-xs text-blue-900 font-medium">
                                    <span class="flex items-center gap-1.5 font-bold">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Hasil OCR: <code class="px-1.5 py-0.5 bg-white rounded border border-blue-300 font-mono text-sm text-blue-800">{{ ocrTextExtracted || form.meteran_sesudah }}</code>
                                    </span>
                                    <span class="px-2 py-0.5 bg-blue-600 text-white font-bold rounded-full text-[10px]">
                                        Akurasi: {{ ocrConfidence }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Perhitungan Otomatis -->
                        <div v-if="form.meteran_sesudah >= meteranSebelum" class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Perhitungan Tagihan
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="bg-white rounded-lg p-4 border border-green-100">
                                    <p class="text-sm text-gray-600 mb-1">Pemakaian Air</p>
                                    <p class="text-3xl font-bold text-green-600">{{ pemakaianKubik.toFixed(2) }}</p>
                                    <p class="text-xs text-gray-500">m³</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-green-100">
                                    <p class="text-sm text-gray-600 mb-1">Tarif per m³</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ formatRupiah(tarif_aktif.tarif_per_kubik) }}</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-4 border-2 border-green-200">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Biaya Pemakaian</span>
                                        <span class="font-semibold">{{ pemakaianDitagih.toFixed(2) }} m³ × {{ formatRupiah(tarif_aktif.tarif_per_kubik) }} = {{ formatRupiah(biayaPemakaian) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Biaya Abunemen</span>
                                        <span class="font-semibold">{{ formatRupiah(form.ada_abunemen ? tarif_aktif.biaya_abunemen : 0) }}</span>
                                    </div>
                                    <div class="border-t-2 border-gray-200 pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-bold text-gray-800">Total Tagihan</span>
                                            <span class="text-2xl font-bold text-green-600">{{ formatRupiah(estimasiTagihan) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Keterangan (Opsional)
                            </label>
                            <textarea
                                v-model="form.keterangan"
                                rows="3"
                                placeholder="Catatan tambahan (jika ada)..."
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                            ></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-4">
                            <button
                                type="button"
                                @click="goBack"
                                class="flex-1 px-6 py-4 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="loading || !form.meteran_sesudah || form.meteran_sesudah < meteranSebelum"
                                class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>{{ loading ? 'Menyimpan...' : 'Simpan Meteran' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Berhasil Disimpan!</h3>
                    <p class="text-gray-600 mb-6">Data meteran berhasil dicatat dan tagihan telah dibuat</p>
                    
                    <div v-if="savedTagihan" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 mb-6 border-2 border-green-200">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Pemakaian Air</span>
                                <span class="text-lg font-bold text-gray-800">{{ savedTagihan.pemakaian_kubik }} m³</span>
                            </div>
                            <div class="border-t border-green-200 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-semibold">Total Tagihan</span>
                                    <span class="text-2xl font-bold text-green-600">Rp {{ formatRupiah(savedTagihan.total_tagihan) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <button
                            @click="scanAnother"
                            class="flex-1 px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition"
                        >
                            Scan Lagi
                        </button>
                        <button
                            @click="goToDashboard"
                            class="flex-1 px-5 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition"
                        >
                            Dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { createWorker } from 'tesseract.js';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    pelanggan: Object,
    tagihan_terbaru: Object,
    tarif_aktif: Object,
});

const form = ref({
    pelanggan_id: props.pelanggan.id,
    bulan: getCurrentMonth(),
    meteran_sesudah: null,
    ada_abunemen: true,
    keterangan: '',
});

const loading = ref(false);
const showSuccessModal = ref(false);
const savedTagihan = ref(null);
const meteranSebelumValue = ref(props.tagihan_terbaru ? props.tagihan_terbaru.meteran_sesudah : 0);
const fotoMeteranFile = ref(null);
const fotoMeteranPreview = ref(null);
const inputFotoUpload = ref(null);
const inputFotoKamera = ref(null);

// OCR Engine State
const isScanningOCR = ref(false);
const ocrProgress = ref(0);
const ocrConfidence = ref(null);
const ocrTextExtracted = ref('');

// AI Anomaly Detection State
const anomalyAnalysis = ref(null);
const isAnalyzingAnomaly = ref(false);

const MAX_SAFE_UPLOAD_BYTES = 350 * 1024;
const MAX_DIMENSION = 1280;
const MIN_DIMENSION = 640;

const meteranSebelum = computed(() => {
    return meteranSebelumValue.value;
});

const pemakaianKubik = computed(() => {
    const meteran = parseFloat(form.value.meteran_sesudah);
    if (!meteran || isNaN(meteran) || form.value.meteran_sesudah === '' || form.value.meteran_sesudah === null) {
        return 0;
    }
    const pemakaian = meteran - meteranSebelum.value;
    return Math.max(0, pemakaian);
});

const pemakaianDitagih = computed(() => {
    const pemakaian = pemakaianKubik.value;
    const minimal = props.tarif_aktif.minimal_pemakaian || 10;
    return Math.max(pemakaian, minimal);
});

const biayaPemakaian = computed(() => {
    return pemakaianDitagih.value * props.tarif_aktif.tarif_per_kubik;
});

const estimasiTagihan = computed(() => {
    if (!form.value.meteran_sesudah || form.value.meteran_sesudah === '') return 0;
    return biayaPemakaian.value + (form.value.ada_abunemen ? props.tarif_aktif.biaya_abunemen : 0);
});

const isFormValid = computed(() => {
    const meteran = parseFloat(form.value.meteran_sesudah);
    return form.value.bulan && 
           form.value.meteran_sesudah !== '' && 
           !isNaN(meteran) &&
           meteran >= meteranSebelum.value;
});

function getCurrentMonth() {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    return `${year}-${month}`;
}

/**
 * Crop dan persiapkan citra ROI area digit odometer meteran air
 * Menggunakan Otsu binarization untuk memisahkan digit gelap dari latar putih
 */
async function cropMeterOdometer(imageSrc) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = () => {
            try {
                // Step 1: Crop area odometer saja (berdasarkan proporsi umum meteran air LXSG)
                // Area kotak digit ada di bagian atas tengah meteran
                const cropW = Math.floor(img.width * 0.72);
                const cropH = Math.floor(img.height * 0.38);
                const cropX = Math.floor(img.width * 0.14);
                const cropY = Math.floor(img.height * 0.13);

                // Step 2: Upscale 4x ke 1800px lebar untuk resolusi tinggi
                const TARGET_W = 1800;
                const scale = TARGET_W / cropW;
                const outW = Math.round(cropW * scale);
                const outH = Math.round(cropH * scale);

                const canvas = document.createElement('canvas');
                canvas.width = outW;
                canvas.height = outH;
                const ctx = canvas.getContext('2d');
                ctx.imageSmoothingEnabled = false; // Nearest-neighbor untuk digit
                ctx.drawImage(img, cropX, cropY, cropW, cropH, 0, 0, outW, outH);

                // Step 3: Ambil pixel dan konversi ke grayscale
                const imageData = ctx.getImageData(0, 0, outW, outH);
                const data = imageData.data;
                const grayArr = new Uint8Array(outW * outH);

                for (let i = 0; i < outW * outH; i++) {
                    const r = data[i * 4];
                    const g = data[i * 4 + 1];
                    const b = data[i * 4 + 2];
                    grayArr[i] = Math.round(r * 0.299 + g * 0.587 + b * 0.114);
                }

                // Step 4: Hitung threshold Otsu
                const hist = new Array(256).fill(0);
                for (let i = 0; i < grayArr.length; i++) hist[grayArr[i]]++;
                const total = grayArr.length;
                let sum = 0;
                for (let i = 0; i < 256; i++) sum += i * hist[i];

                let sumB = 0, wB = 0, wF = 0;
                let maxVar = 0, threshold = 128;
                for (let t = 0; t < 256; t++) {
                    wB += hist[t];
                    if (wB === 0) continue;
                    wF = total - wB;
                    if (wF === 0) break;
                    sumB += t * hist[t];
                    const mB = sumB / wB;
                    const mF = (sum - sumB) / wF;
                    const varBetween = wB * wF * (mB - mF) ** 2;
                    if (varBetween > maxVar) { maxVar = varBetween; threshold = t; }
                }

                // Step 5: Binarisasi (hitam-putih bersih): digit putih pada latar hitam
                // Counter odometer umumnya: digit PUTIH/KREM pada latar HITAM
                // Deteksi apakah latar lebih terang atau lebih gelap
                let brightCount = 0;
                for (let i = 0; i < grayArr.length; i++) if (grayArr[i] > threshold) brightCount++;
                const bgIsBright = (brightCount / total) > 0.5;

                for (let i = 0; i < outW * outH; i++) {
                    // Jika latar terang → digit gelap → biarkan gelap tetap gelap
                    // Jika latar gelap → digit terang → invert supaya digit jadi hitam
                    let px = grayArr[i];
                    if (!bgIsBright) {
                        // Invert: digit terang → jadi gelap agar Tesseract baca dengan benar
                        px = 255 - px;
                    }
                    const bin = px > threshold ? 255 : 0;
                    data[i * 4] = bin;
                    data[i * 4 + 1] = bin;
                    data[i * 4 + 2] = bin;
                    data[i * 4 + 3] = 255;
                }

                ctx.putImageData(imageData, 0, 0);
                resolve(canvas.toDataURL('image/png'));
            } catch (err) {
                resolve(imageSrc);
            }
        };
        img.onerror = reject;
        img.src = imageSrc;
    });
}

/**
 * Kumpulkan semua kandidat deret digit (4–6 karakter) dari teks OCR
 */
function collectOdometerCandidates(text, confBase = 70) {
    const candidates = [];
    if (!text) return candidates;
    const matches = text.match(/\d{4,6}/g);
    if (matches) {
        matches.forEach(m => {
            const val = parseInt(m, 10);
            if (!isNaN(val) && val >= 0 && val <= 999999) {
                candidates.push({ raw: m, val, len: m.length, conf: confBase });
            }
        });
    }
    return candidates;
}

/**
 * OCR Utama: Kirim foto ke backend Laravel → Gemini Vision API (akurasi ~95%)
 * Fallback otomatis ke Tesseract.js di browser jika backend tidak tersedia
 */
async function runOCRScan() {
    if (!fotoMeteranPreview.value) {
        alert('Silakan unggah atau ambil foto meteran air terlebih dahulu.');
        return;
    }

    isScanningOCR.value = true;
    ocrProgress.value = 10;
    ocrConfidence.value = null;
    ocrTextExtracted.value = '';

    // ── STRATEGI 1: Gemini Vision API via backend Laravel ──────────────────
    try {
        ocrProgress.value = 30;
        const response = await fetch('/api/ocr/read-meter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ image: fotoMeteranPreview.value }),
        });

        ocrProgress.value = 80;
        const json = await response.json();

        if (json.success && json.value !== undefined) {
            ocrProgress.value = 100;
            ocrConfidence.value = json.confidence || 95;
            ocrTextExtracted.value = `${json.value} (via Gemini Vision)`;
            form.value.meteran_sesudah = json.value;
            isScanningOCR.value = false;
            return;
        } else {
            console.warn('[OCR] Gemini Vision tidak mengenali angka:', json.message || json.raw);
        }
    } catch (fetchErr) {
        console.warn('[OCR] Backend Gemini tidak tersedia, fallback ke Tesseract.js', fetchErr.message);
    }

    // ── STRATEGI 2: Tesseract.js di browser (fallback) ─────────────────────
    try {
        ocrProgress.value = 50;
        const binaryImage = await cropMeterOdometer(fotoMeteranPreview.value);

        const worker = await createWorker('eng');
        await worker.setParameters({
            tessedit_char_whitelist: '0123456789',
            tessedit_pageseg_mode: '7',
        });

        ocrProgress.value = 80;
        const ret = await worker.recognize(binaryImage);
        await worker.terminate();

        ocrProgress.value = 100;
        const conf = Math.round(ret.data.confidence || 60);
        ocrConfidence.value = conf;

        const candidates = collectOdometerCandidates(ret.data.text, conf);
        if (candidates.length > 0) {
            // Pilih kandidat terpanjang (paling banyak digit)
            candidates.sort((a, b) => b.len - a.len || b.conf - a.conf);
            const best = candidates[0];
            ocrTextExtracted.value = `${best.val} (Digit: ${best.raw})`;
            form.value.meteran_sesudah = best.val;
        } else {
            ocrTextExtracted.value = 'Tidak Terbaca';
            ocrConfidence.value = 0;
            alert('Angka meteran tidak dapat terbaca. Silakan periksa foto atau isi angka secara manual.');
        }
    } catch (err) {
        console.error('OCR Tesseract Error:', err);
        ocrTextExtracted.value = 'Error';
        alert('Gagal membaca foto meteran. Silakan isi angka secara manual.');
    } finally {
        isScanningOCR.value = false;
    }
}

/**
 * Validasi Real-time Anomali AI berdasarkan histori pemakaian
 */
let anomalyDebounceTimer = null;

async function checkAnomalyRealtime() {
    const val = parseFloat(form.value.meteran_sesudah);
    if (isNaN(val) || form.value.meteran_sesudah === '' || form.value.meteran_sesudah === null) {
        anomalyAnalysis.value = null;
        return;
    }

    isAnalyzingAnomaly.value = true;
    try {
        const response = await axios.post('/api/qr-scanner/check-anomaly', {
            pelanggan_id: props.pelanggan.id,
            meteran_sebelum: meteranSebelum.value,
            meteran_sesudah: val,
        });

        if (response.data && response.data.success) {
            anomalyAnalysis.value = response.data.data;
        }
    } catch (err) {
        console.error('Error checking anomaly:', err);
    } finally {
        isAnalyzingAnomaly.value = false;
    }
}

watch(() => form.value.meteran_sesudah, () => {
    clearTimeout(anomalyDebounceTimer);
    anomalyDebounceTimer = setTimeout(checkAnomalyRealtime, 400);
});

async function submitForm() {
    if (!isFormValid.value || loading.value) return;
    
    loading.value = true;
    
    try {
        const formData = new FormData();
        formData.append('pelanggan_id', String(form.value.pelanggan_id));
        formData.append('bulan', form.value.bulan);
        formData.append('meteran_sesudah', String(form.value.meteran_sesudah));
        formData.append('ada_abunemen', form.value.ada_abunemen ? '1' : '0');
        formData.append('keterangan', form.value.keterangan || '');
        if (ocrConfidence.value !== null) {
            formData.append('ocr_confidence', String(ocrConfidence.value));
        }
        if (fotoMeteranFile.value) {
            formData.append('foto_meteran', fotoMeteranFile.value);
        }

        const response = await fetch('/api/qr-scanner/store-meteran', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin', // FIX: Include cookies untuk session/CSRF
            body: formData,
        });
        
        const data = await response.json();
        
        if (data.success) {
            savedTagihan.value = data.tagihan;
            showSuccessModal.value = true;
        } else {
            alert(data.message || 'Terjadi kesalahan saat menyimpan data.');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('Terjadi kesalahan saat menyimpan data.');
    } finally {
        loading.value = false;
    }
}

async function handleFotoMeteranChange(event) {
    const rawFile = event.target.files?.[0] || null;

    // Boleh pilih file yang sama di input berikutnya
    event.target.value = '';

    if (!rawFile) return;

    const file = await compressImageIfNeeded(rawFile);

    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran foto masih terlalu besar. Coba ambil ulang dengan jarak lebih jauh.');
        return;
    }

    fotoMeteranFile.value = file;

    if (fotoMeteranPreview.value) {
        URL.revokeObjectURL(fotoMeteranPreview.value);
        fotoMeteranPreview.value = null;
    }

    fotoMeteranPreview.value = URL.createObjectURL(file);
}

async function compressImageIfNeeded(file) {
    if (!file.type.startsWith('image/')) return file;

    const objectUrl = URL.createObjectURL(file);

    try {
        const image = await new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = objectUrl;
        });

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        let targetWidth = image.width;
        let targetHeight = image.height;

        const maxSide = Math.max(targetWidth, targetHeight);
        if (maxSide > MAX_DIMENSION) {
            const scale = MAX_DIMENSION / maxSide;
            targetWidth = Math.round(targetWidth * scale);
            targetHeight = Math.round(targetHeight * scale);
        }

        canvas.width = targetWidth;
        canvas.height = targetHeight;
        ctx.drawImage(image, 0, 0, targetWidth, targetHeight);

        let quality = 0.72;
        let blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));

        // Tahap 1: turunkan quality dulu
        while (blob && blob.size > MAX_SAFE_UPLOAD_BYTES && quality > 0.25) {
            quality -= 0.1;
            blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));
        }

        // Tahap 2: jika masih besar, perkecil dimensi bertahap lalu encode ulang
        while (blob && blob.size > MAX_SAFE_UPLOAD_BYTES && Math.max(targetWidth, targetHeight) > MIN_DIMENSION) {
            targetWidth = Math.round(targetWidth * 0.85);
            targetHeight = Math.round(targetHeight * 0.85);

            canvas.width = targetWidth;
            canvas.height = targetHeight;
            ctx.drawImage(image, 0, 0, targetWidth, targetHeight);

            // reset sedikit quality agar detail tetap kebaca setelah resize
            quality = Math.min(0.6, quality + 0.1);
            blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));

            while (blob && blob.size > MAX_SAFE_UPLOAD_BYTES && quality > 0.2) {
                quality -= 0.08;
                blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));
            }
        }

        if (!blob) {
            return file;
        }

        const optimizedName = file.name.replace(/\.[^.]+$/, '') + '.jpg';
        return new File([blob], optimizedName, {
            type: 'image/jpeg',
            lastModified: Date.now(),
        });
    } catch (error) {
        console.error('Image compression failed, using original file:', error);
        return file;
    } finally {
        URL.revokeObjectURL(objectUrl);
    }
}

function clearFotoMeteran() {
    if (fotoMeteranPreview.value) {
        URL.revokeObjectURL(fotoMeteranPreview.value);
    }
    fotoMeteranFile.value = null;
    fotoMeteranPreview.value = null;
}

function triggerUploadFoto() {
    if (inputFotoUpload.value) {
        inputFotoUpload.value.click();
    }
}

function triggerFotoLangsung() {
    if (inputFotoKamera.value) {
        inputFotoKamera.value.click();
    }
}

function scanAnother() {
    router.visit('/qr-scanner');
}

async function fetchMeteranSebelum(bulan) {
    if (!bulan) return;
    
    // Reset nilai ke 0 dulu sebelum fetch data baru
    // meteranSebelumValue.value = 0; // Komentari ini agar tidak ada blink/flicker jika tidak perlu

    try {
        // Panggil endpoint baru untuk mendapatkan meteran terakhir sebelum bulan yang dipilih
        const response = await axios.get(`/api/qr-scanner/last-meteran/${props.pelanggan.id}/${bulan}`);
        
        if (response.data && response.data.meteran_terakhir !== undefined) {
             meteranSebelumValue.value = Number(response.data.meteran_terakhir);
        } else {
             meteranSebelumValue.value = 0;
        }
    } catch (error) {
        console.error('Error fetching meteran sebelum:', error);
        // Jangan reset ke 0 jika error, mungkin koneksi putus, biarkan nilai lama atau set ke 0 jika perlu
        meteranSebelumValue.value = 0;
    }
}

// Watch bulan, jika berubah maka fetch meteran sebelum otomatis
watch(() => form.value.bulan, (newBulan) => {
    if (newBulan) {
        fetchMeteranSebelum(newBulan);
    }
}, { immediate: true });

function goToDashboard() {
    router.visit('/dashboard');
}

function goBack() {
    router.visit('/qr-scanner');
}

function formatBulan(bulan) {
    const date = new Date(bulan);
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
}

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka);
}

onMounted(() => {
    if ((props.pelanggan?.kategori || '').toLowerCase() === 'sosial') {
        form.value.ada_abunemen = false;
    }
});
</script>
