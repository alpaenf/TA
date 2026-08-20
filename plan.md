# PLAN PENGEMBANGAN TA

## Judul
**Implementasi Optical Character Recognition (OCR) Berbasis Web dengan Integrasi Artificial Intelligence untuk Otomatisasi dan Validasi Pencatatan Meter Air pada KP-SPAMS Damar Wulan**

---

## 1. Latar Belakang

Proses pencatatan meter air pada KP-SPAMS Damar Wulan masih melibatkan identifikasi meter melalui QR Code dan penginputan angka meter secara manual. Proses tersebut berpotensi menimbulkan kesalahan input dan membutuhkan waktu karena petugas harus membaca angka meter secara langsung kemudian mengetikkannya ke dalam sistem.

Penelitian ini diarahkan untuk mengembangkan sistem berbasis web yang dapat mengotomatisasi proses tersebut. QR Code digunakan sebagai identitas meter/pelanggan, sedangkan kamera digunakan untuk mengambil citra angka meter. Selanjutnya, OCR digunakan untuk mengenali angka meter secara otomatis sehingga petugas tidak perlu mengetik angka secara manual.

Selain pembacaan menggunakan OCR, sistem akan menggunakan Artificial Intelligence dengan algoritma **Isolation Forest** untuk mendeteksi anomali berdasarkan histori pencatatan meter. Hasil pembacaan yang tidak wajar dapat ditandai untuk dilakukan verifikasi oleh petugas sebelum disimpan.

Dengan pendekatan tersebut, proses pencatatan diharapkan menjadi lebih singkat, mengurangi input manual, dan menurunkan risiko kesalahan pencatatan.

---

## 2. Tujuan Pengembangan

1. Mengembangkan sistem berbasis web untuk otomatisasi pencatatan meter air.
2. Mengintegrasikan QR Code sebagai identitas meter/pelanggan.
3. Mengimplementasikan OCR untuk membaca angka meter dari citra kamera.
4. Mengimplementasikan **Isolation Forest** untuk mendeteksi anomali hasil pencatatan berdasarkan histori meter.
5. Mengurangi kebutuhan input angka meter secara manual.
6. Mengevaluasi akurasi pembacaan OCR dan kemampuan sistem dalam mendeteksi pencatatan yang terindikasi anomali.
7. Membandingkan efisiensi proses pencatatan sebelum dan setelah menggunakan sistem.

---

## 3. Konsep Utama Sistem

### Alur yang ditargetkan

**Scan QR → Kamera → Foto Meter → OCR → Validasi Anomali → Konfirmasi/Simpan**

### Detail proses

1. Petugas membuka halaman pencatatan.
2. Petugas melakukan scan QR Code pada meter.
3. Sistem mengambil identitas meter/pelanggan dari QR Code.
4. Sistem menampilkan informasi meter dan histori pencatatan terakhir.
5. Kamera digunakan untuk mengambil foto angka meter.
6. Sistem melakukan preprocessing citra jika diperlukan.
7. OCR membaca angka pada meter.
8. Sistem mendapatkan nilai meter hasil OCR.
9. Nilai tersebut dibandingkan dengan data histori pencatatan.
10. Model Isolation Forest melakukan deteksi anomali berdasarkan fitur yang telah ditentukan.
11. Jika hasil dianggap normal, sistem menampilkan hasil untuk dikonfirmasi.
12. Jika terindikasi anomali, sistem memberikan peringatan dan meminta verifikasi petugas.
13. Data pencatatan disimpan ke database setelah dikonfirmasi.

> **Prinsip utama:** petugas tidak lagi mengetik angka meter secara manual. QR digunakan untuk identifikasi meter, sedangkan kamera + OCR digunakan untuk membaca angka.

---

## 4. Peran Setiap Teknologi

| Teknologi | Peran |
|---|---|
| QR Code | Identifikasi meter/pelanggan |
| Kamera | Mengambil citra meter |
| Image Processing | Meningkatkan kualitas citra sebelum OCR |
| OCR | Mengubah angka pada citra menjadi data numerik |
| Isolation Forest | Mendeteksi hasil pencatatan yang tidak wajar/anomali |
| Web Application | Antarmuka proses pencatatan |
| Database | Menyimpan data pelanggan, meter, histori, dan hasil validasi |
| Groq API | Opsional sebagai komponen tambahan untuk analisis/penjelasan hasil validasi, bukan algoritma AI utama |

---

## 5. Metode AI: Isolation Forest

### Alasan penggunaan

Isolation Forest dipilih karena fokus penelitian adalah mendeteksi **anomali pada hasil pencatatan meter**, bukan melakukan klasifikasi pelanggan.

Model dapat digunakan untuk mengenali pola penggunaan berdasarkan histori pencatatan.

Contoh:

- Meter sebelumnya: 1.250
- Hasil OCR sekarang: 1.270
- Pemakaian: 20 m³
- Pola historis: sekitar 15–25 m³/bulan
- Hasil: normal

Sedangkan:

- Meter sebelumnya: 1.250
- Hasil OCR sekarang: 9.250
- Pemakaian: 8.000 m³
- Pola historis: sekitar 15–25 m³/bulan
- Hasil: terindikasi anomali

### Catatan penting

Isolation Forest tidak digunakan untuk membaca angka pada gambar. Pembacaan angka merupakan tugas OCR.

Pembagian tanggung jawab:

**OCR = membaca angka**

**Isolation Forest = mendeteksi apakah hasil pencatatan tersebut menyimpang dari pola historis**

---

## 6. Fitur untuk Deteksi Anomali

Fitur awal yang dapat dipertimbangkan:

1. Nilai meter saat ini.
2. Nilai meter sebelumnya.
3. Selisih meter saat ini dengan sebelumnya.
4. Rata-rata pemakaian historis.
5. Median pemakaian historis.
6. Standar deviasi pemakaian historis.
7. Persentase perubahan pemakaian.
8. Jumlah pencatatan historis yang tersedia.

### Fitur utama yang direkomendasikan

**Konsumsi periode = Meter saat ini - Meter sebelumnya**

Model terutama digunakan untuk mempelajari pola konsumsi historis, sehingga nilai yang sangat menyimpang dapat ditandai sebagai anomali.

---

## 7. Arsitektur Sistem

```text
┌─────────────────────┐
│      PETUGAS        │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│      Scan QR        │
│ Identitas Meter     │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│      Kamera         │
│ Foto Angka Meter    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Image Preprocessing │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│        OCR          │
│ Baca Angka Meter    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────────────┐
│ Histori Pencatatan Meter    │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────┐
│  Isolation Forest   │
│ Anomaly Detection   │
└──────────┬──────────┘
           │
       ┌───┴────┐
       ▼        ▼
    Normal    Anomali
       │        │
       ▼        ▼
   Konfirmasi  Verifikasi
       │        │
       └───┬────┘
           ▼
┌─────────────────────┐
│      Database       │
│ Simpan Pencatatan   │
└─────────────────────┘
```

---

## 8. Fitur Sistem

### A. Pencatatan Meter

- Scan QR Code.
- Identifikasi meter otomatis.
- Akses kamera.
- Pengambilan foto meter.
- Pembacaan angka menggunakan OCR.
- Menampilkan hasil OCR.
- Validasi otomatis.
- Konfirmasi pencatatan.
- Penyimpanan data.

### B. Histori Meter

- Riwayat pencatatan per meter.
- Nilai meter setiap periode.
- Riwayat konsumsi.
- Grafik penggunaan.
- Status validasi.

### C. Anomaly Detection

- Analisis pola penggunaan.
- Deteksi pencatatan tidak wajar.
- Status normal/anomali.
- Alasan atau indikator anomali.
- Verifikasi manual jika diperlukan.

### D. Dashboard

- Jumlah meter.
- Jumlah pencatatan.
- Pencatatan normal.
- Pencatatan terindikasi anomali.
- Statistik penggunaan.

---

## 9. Struktur Data Awal

### Tabel customers

- id
- customer_code
- name
- address
- qr_code

### Tabel water_meters

- id
- customer_id
- meter_number
- installation_date
- status

### Tabel meter_readings

- id
- meter_id
- reading_value
- reading_date
- image_path
- ocr_confidence
- validation_status
- anomaly_score
- verification_status

### Tabel usage_history

Dapat menggunakan data `meter_readings` sebagai sumber histori untuk membentuk fitur anomaly detection.

---

## 10. Mekanisme Validasi

### Kondisi Normal

Jika hasil OCR:

- tidak lebih kecil dari pencatatan sebelumnya;
- konsumsi masih berada dalam pola historis;
- tidak terdeteksi sebagai anomali oleh Isolation Forest;

maka sistem memberikan status:

**NORMAL**

### Kondisi Anomali

Jika hasil OCR menghasilkan pola konsumsi yang sangat menyimpang, sistem memberikan status:

**PERLU VERIFIKASI**

Petugas kemudian dapat:

- mengambil foto ulang;
- memeriksa angka meter;
- mengoreksi hasil jika memang diperlukan;
- mengonfirmasi pencatatan.

---

## 11. Evaluasi Penelitian

### A. Evaluasi OCR

Metrik yang dapat digunakan:

- Character Accuracy
- Exact Match Accuracy
- Word Accuracy jika relevan
- Error Rate

Pengujian dilakukan menggunakan kumpulan foto meter dengan kondisi berbeda, misalnya:

- pencahayaan baik;
- pencahayaan rendah;
- sudut pengambilan berbeda;
- angka kurang jelas;
- kondisi lingkungan berbeda.

### B. Evaluasi Isolation Forest

Metrik yang dapat digunakan setelah tersedia label/ground truth:

- Precision
- Recall
- F1-Score
- Confusion Matrix
- Accuracy, jika distribusi kelas memungkinkan

### C. Evaluasi Efisiensi

Bandingkan proses:

**Metode lama**
> Scan QR → membaca meter → mengetik angka → simpan

dengan:

**Metode usulan**
> Scan QR → foto meter → OCR → validasi → simpan

Parameter:

- waktu rata-rata pencatatan;
- jumlah langkah/input manual;
- jumlah kesalahan input;
- jumlah pencatatan yang perlu dikoreksi.

---

## 12. Dataset

Dataset penelitian perlu terdiri dari:

1. Foto meter air.
2. Nilai meter sebenarnya (ground truth).
3. ID meter.
4. Riwayat pencatatan.
5. Nilai hasil OCR.
6. Label anomali untuk evaluasi model.

### Ground Truth

Nilai sebenarnya harus diverifikasi dari angka yang benar-benar terlihat pada meter, bukan hanya menggunakan hasil OCR sebagai acuan.

---

## 13. Tahapan Pengembangan

### Tahap 1 - Analisis

- Observasi proses pencatatan yang berjalan.
- Identifikasi alur kerja petugas.
- Identifikasi data meter dan histori.
- Menentukan kebutuhan sistem.

### Tahap 2 - Pengumpulan Dataset

- Mengumpulkan foto meter.
- Mencatat nilai sebenarnya.
- Mengumpulkan histori pencatatan.
- Menentukan data normal dan anomali untuk pengujian.

### Tahap 3 - Pengembangan OCR

- Input foto.
- Preprocessing.
- OCR.
- Ekstraksi angka.
- Pengujian akurasi.

### Tahap 4 - Pengembangan Anomaly Detection

- Membentuk fitur konsumsi.
- Menyiapkan data historis.
- Melatih Isolation Forest.
- Menentukan threshold/parameter.
- Menguji hasil deteksi.

### Tahap 5 - Integrasi Web

- QR scanner.
- Kamera.
- OCR.
- Anomaly detection.
- Database.
- Dashboard.
- Histori.

### Tahap 6 - Pengujian

- Functional testing.
- OCR accuracy testing.
- Anomaly detection testing.
- Usability/efficiency testing.
- Perbandingan dengan proses lama.

### Tahap 7 - Evaluasi

- Analisis hasil pengujian.
- Identifikasi kelemahan.
- Evaluasi pengurangan input manual.
- Evaluasi pengurangan kesalahan pencatatan.

---

## 14. Batasan Penelitian

1. Sistem berfokus pada pencatatan angka meter air.
2. QR Code digunakan sebagai identitas meter/pelanggan.
3. OCR digunakan untuk membaca angka meter dari citra.
4. Isolation Forest digunakan untuk deteksi anomali berdasarkan histori.
5. AI tidak menggantikan petugas ketika hasil terindikasi anomali.
6. Hasil anomali tetap dapat diverifikasi oleh petugas.
7. Groq API bukan algoritma utama penelitian dan hanya digunakan jika diperlukan sebagai komponen tambahan.
8. Penelitian berfokus pada pengurangan input manual dan peningkatan akurasi/validitas pencatatan.

---

## 15. Target Hasil Akhir

Produk akhir yang ditargetkan adalah aplikasi web dengan alur:

**Scan QR → Ambil Foto Meter → OCR → Deteksi Anomali → Konfirmasi → Simpan**

Target manfaat:

- Mengurangi pengetikan angka secara manual.
- Mengurangi risiko kesalahan input.
- Mempercepat proses pencatatan.
- Menyediakan histori penggunaan.
- Memberikan peringatan terhadap pencatatan yang tidak wajar.
- Menyediakan data pencatatan yang lebih terstruktur.

---

## 16. Fokus Kontribusi TA

Kontribusi utama penelitian diarahkan pada tiga hal:

### 1. Otomatisasi
Mengubah proses input angka meter dari manual menjadi pembacaan otomatis menggunakan OCR.

### 2. Intelligent Validation
Menggunakan Isolation Forest untuk mendeteksi hasil pencatatan yang menyimpang dari pola historis.

### 3. Efisiensi
Membandingkan proses pencatatan manual dengan proses otomatis untuk mengetahui dampaknya terhadap waktu dan kesalahan input.

---

## 17. Pertanyaan Penelitian

1. Seberapa akurat OCR dalam membaca angka meter air dari citra kamera?
2. Seberapa baik Isolation Forest mendeteksi anomali pada hasil pencatatan meter?
3. Seberapa besar pengurangan waktu dan input manual setelah sistem diterapkan?
4. Apakah integrasi OCR dan anomaly detection dapat mengurangi kesalahan pencatatan meter air?

---

## 18. Catatan Pengembangan

Hal yang harus dipastikan sebelum implementasi:

- Jenis/tampilan meter air yang digunakan di KP-SPAMS Damar Wulan.
- Posisi dan bentuk angka meter.
- Ketersediaan QR Code pada setiap meter.
- Jumlah histori pencatatan yang tersedia.
- Jumlah sampel foto yang dapat dikumpulkan.
- Kondisi pengambilan foto di lapangan.
- Metode OCR yang akan digunakan.
- Parameter dan fitur terbaik untuk Isolation Forest.

**Prioritas pengembangan:**

1. Pastikan OCR dapat membaca angka meter dengan akurasi yang memadai.
2. Pastikan histori meter cukup untuk membangun pola konsumsi.
3. Implementasikan Isolation Forest.
4. Integrasikan seluruh proses ke web.
5. Ukur dampaknya terhadap waktu dan kesalahan input.

---

## 19. Gambaran MVP

Versi pertama sistem cukup memiliki:

- Login petugas.
- Scan QR.
- Kamera.
- OCR angka meter.
- Data meter sebelumnya.
- Perhitungan konsumsi.
- Isolation Forest.
- Status normal/anomali.
- Konfirmasi.
- Penyimpanan histori.

Fitur tambahan seperti dashboard statistik, grafik, laporan, dan integrasi Groq dapat dikembangkan setelah alur inti berhasil.

---

## 20. Kesimpulan Rencana

Penelitian diarahkan untuk mengembangkan sistem pencatatan meter air berbasis web yang menggabungkan **QR Code, kamera, OCR, dan Artificial Intelligence dengan algoritma Isolation Forest**.

QR Code digunakan untuk mengidentifikasi meter, OCR digunakan untuk membaca angka meter, sedangkan Isolation Forest digunakan untuk mendeteksi pencatatan yang menyimpang berdasarkan histori penggunaan.

Dengan demikian, petugas tidak perlu lagi melakukan input angka meter secara manual. Proses utama menjadi:

**Identifikasi Meter → Ambil Foto → Baca Otomatis → Validasi AI → Konfirmasi → Simpan**

Keberhasilan penelitian akan dinilai tidak hanya dari sistem yang berhasil dibuat, tetapi juga dari **akurasi OCR, performa deteksi anomali, pengurangan kesalahan input, dan efisiensi waktu pencatatan**.
