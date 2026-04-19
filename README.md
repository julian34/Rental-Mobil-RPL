# Car Rental System

Car Rental System adalah aplikasi yang dirancang untuk membantu pengelolaan usaha rental mobil secara lebih terstruktur, cepat, dan efisien. Sistem ini berfokus pada proses inti bisnis rental, mulai dari pengelolaan data mobil, data pelanggan, transaksi penyewaan, pembayaran, hingga pengembalian kendaraan.

Project ini cocok digunakan sebagai:

- bahan belajar pengembangan sistem informasi
- contoh project CRUD dan transaksi
- dasar pengembangan aplikasi rental mobil berbasis web, desktop, atau mobile
- referensi tugas kuliah, skripsi, atau portfolio GitHub

## Core Features

Aplikasi ini memiliki beberapa fitur inti yang menjadi dasar operasional sistem rental mobil:

### 1. Vehicle Management

Mengelola seluruh data kendaraan yang tersedia untuk disewakan.  
Informasi yang dikelola meliputi:

- nomor polisi
- merk dan tipe mobil
- tahun kendaraan
- warna
- harga sewa
- status mobil
- kondisi kendaraan

Status mobil biasanya dibedakan menjadi:

- tersedia
- sedang disewa
- dalam perawatan
- tidak aktif

### 2. Customer Management

Menyimpan dan mengelola data pelanggan yang melakukan penyewaan mobil.  
Data pelanggan dapat mencakup:

- nama lengkap
- alamat
- nomor telepon
- nomor KTP
- nomor SIM

Fitur ini penting untuk mendukung validasi identitas dan pencatatan riwayat transaksi pelanggan.

### 3. Reservation and Rental Transaction

Mengelola proses pemesanan dan penyewaan mobil.  
Sistem memungkinkan pengguna untuk:

- memilih mobil yang tersedia
- menentukan tanggal sewa
- menentukan durasi peminjaman
- membuat transaksi rental
- mencatat status transaksi

Bagian ini merupakan inti utama dari aplikasi karena menghubungkan data pelanggan dengan kendaraan yang disewa.

### 4. Payment Handling

Mencatat proses pembayaran dari setiap transaksi rental.  
Fitur ini biasanya mencakup:

- total biaya sewa
- metode pembayaran
- status pembayaran
- pembayaran penuh atau uang muka

Dengan fitur ini, sistem dapat membantu pencatatan keuangan secara lebih rapi dan mengurangi kesalahan manual.

### 5. Vehicle Return

Mengelola proses pengembalian kendaraan setelah masa sewa selesai.  
Pada tahap ini sistem dapat:

- mencatat tanggal kembali
- menghitung keterlambatan
- menghitung denda
- mencatat kondisi mobil setelah dipakai
- memperbarui status mobil menjadi tersedia kembali

### 6. Reporting

Menyediakan laporan operasional untuk membantu pemilik usaha memantau bisnis rental.  
Laporan yang umum ditampilkan:

- daftar transaksi
- laporan pendapatan
- data mobil yang sedang disewa
- data mobil tersedia
- riwayat pelanggan
- laporan denda

## Main Actors

Sistem rental mobil umumnya melibatkan aktor berikut:

### Admin

Bertugas mengelola data mobil, data pelanggan, transaksi rental, pembayaran, pengembalian, dan laporan.

### Customer

Melakukan pemesanan atau penyewaan mobil sesuai kebutuhan.

### Owner

Melihat laporan usaha dan memantau keseluruhan operasional rental.

## Use Case Diagram

Diagram berikut menggambarkan interaksi antara aktor dan sistem rental mobil. File sumber diagram: [`UseCaseDiagram.drawio`](../UseCaseDiagram.drawio)

![Use Case Diagram](../UCD.drawio.png)

### Aktor dan Use Case

#### Customer

| No | Use Case | Keterangan |
|----|----------|------------|
| 1 | Lihat Daftar Mobil | Melihat daftar mobil yang tersedia untuk disewa |
| 2 | Penyewahan Mobil | Melakukan proses penyewaan mobil |
| 3 | Lihat Riwayat Transaksi | Melihat riwayat transaksi penyewaan yang pernah dilakukan |

#### Kasir

| No | Use Case | Keterangan |
|----|----------|------------|
| 1 | Proses Pembayaran | Memproses pembayaran dari transaksi rental |
| 2 | Konfirmasi Serah Terima Mobil | Mengkonfirmasi penyerahan mobil kepada pelanggan |
| 3 | Konfirmasi Pengembalian Mobil | Mengkonfirmasi pengembalian mobil dari pelanggan |
| 4 | Hitung Keterlambatan & Denda | Menghitung denda jika terjadi keterlambatan pengembalian |
| 5 | Lihat Daftar Transaksi | Melihat seluruh daftar transaksi rental |
| 6 | Penugasan Pengantaran | Menugaskan pengantaran mobil ke pelanggan |

#### Administrator

| No | Use Case | Keterangan |
|----|----------|------------|
| 1 | Kelola Data Mobil | Menambah, mengubah, dan menghapus data mobil |
| 2 | Kelola User | Mengelola data pengguna sistem |
| 3 | Kelola Perawatan Mobil | Mengelola jadwal dan catatan perawatan mobil |
| 4 | Lihat Laporan Pendapatan | Melihat laporan pendapatan dari transaksi rental |
| 5 | Lihat Daftar Transaksi | Melihat seluruh daftar transaksi rental |

#### Staff

| No | Use Case | Keterangan |
|----|----------|------------|
| 1 | Lihat Tugas Perawatan | Melihat daftar tugas perawatan mobil yang ditugaskan |
| 2 | Lihat Tugas Pengantaran | Melihat daftar tugas pengantaran mobil ke pelanggan |

### Relasi Antar Use Case

#### Include

| Dari | Ke | Penjelasan |
|------|----|------------|
| Lihat Daftar Transaksi | Lihat Laporan Pendapatan | Melihat daftar transaksi mencakup data untuk laporan pendapatan |
| Konfirmasi Pengembalian Mobil | Proses Pembayaran | Pengembalian mobil selalu melibatkan proses pembayaran |
| Proses Pembayaran | Lihat Daftar Transaksi | Pembayaran selalu tercatat ke dalam daftar transaksi |
| Lihat Tugas Perawatan | Kelola Perawatan Mobil | Tugas perawatan mengacu pada data kelola perawatan mobil |
| Lihat Riwayat Transaksi | Lihat Daftar Transaksi | Riwayat transaksi customer mengacu pada daftar transaksi |
| Penugasan Pengantaran | Lihat Tugas Pengantaran | Penugasan pengantaran tercatat sebagai tugas pengantaran |
| Lihat Daftar Mobil | Kelola Perawatan Mobil | Data daftar mobil mencakup informasi status perawatan |

#### Extend

| Dari | Ke | Penjelasan |
|------|----|------------|
| Hitung Keterlambatan & Denda | Konfirmasi Pengembalian Mobil | Perhitungan denda dilakukan hanya jika ada keterlambatan |
| Penyewahan Mobil | Proses Pembayaran | Penyewaan dapat memicu proses pembayaran |
| Konfirmasi Serah Terima Mobil | Penugasan Pengantaran | Serah terima dapat memicu penugasan pengantaran jika diperlukan |

## Main Workflow

Alur utama sistem secara umum adalah sebagai berikut:

1. Admin menambahkan data mobil ke sistem
2. Admin atau pelanggan memilih mobil yang tersedia
3. Sistem membuat transaksi rental
4. Pembayaran dicatat sesuai metode yang dipilih
5. Mobil diserahkan kepada pelanggan
6. Setelah masa sewa selesai, kendaraan dikembalikan
7. Sistem menghitung denda jika ada keterlambatan atau kerusakan
8. Data transaksi masuk ke laporan

## Core Modules

Struktur modul utama pada project ini biasanya terdiri dari:

- authentication
- user management
- customer management
- car management
- reservation
- rental transaction
- payment
- return process
- reporting

## Database Core Entities

Beberapa entitas utama yang biasanya ada dalam database:

- users
- customers
- cars
- reservations
- rentals
- payments
- returns
- reports

## Why This Project?

Project ini dibuat untuk menyelesaikan masalah umum pada usaha rental mobil yang masih dikelola secara manual, seperti:

- pencatatan data yang tidak rapi
- risiko mobil double booking
- kesulitan mengecek status kendaraan
- perhitungan biaya dan denda yang tidak konsisten
- laporan usaha yang lambat dibuat

Dengan sistem ini, proses bisnis rental mobil menjadi lebih tertata, transparan, dan mudah dikembangkan.

## Suitable For

Project ini cocok untuk:

- mahasiswa yang sedang belajar sistem informasi
- developer yang ingin membuat aplikasi rental kendaraan
- pemilik usaha rental mobil skala kecil hingga menengah
- kebutuhan portfolio project GitHub

## Future Development

Beberapa pengembangan yang bisa ditambahkan:

- booking online
- upload dokumen pelanggan
- notifikasi jatuh tempo sewa
- integrasi pembayaran digital
- dashboard statistik
- manajemen supir
- multi-branch rental system

## Conclusion

Car Rental System adalah solusi digital untuk mengelola proses rental mobil secara terintegrasi. Fokus utama sistem ini adalah memastikan pengelolaan kendaraan, pelanggan, transaksi, pembayaran, dan pengembalian dapat berjalan dengan lebih efektif, akurat, dan mudah dipantau.
