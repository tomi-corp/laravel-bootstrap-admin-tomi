# Standar Desain Admin TOMI

Dokumen ini menjadi acuan desain UI untuk banyak halaman admin TOMI ke depan, bukan hanya satu halaman. Fokusnya adalah pola halaman daftar, halaman detail, halaman konfigurasi, form data utama, form identitas KTP, data rekening, dokumen pendukung, dan alur navigasi antar halaman.

Detail `Admin Perusahaan` di dokumen ini dipakai sebagai contoh implementasi awal. Pola yang sama dapat diterapkan ke halaman `users`, `karyawan`, `perusahaan`, `lender`, pengurus/PIC, referral, dan konfigurasi produk.

## Prinsip Desain Lintas Halaman

- Setiap modul utama memiliki halaman daftar sebagai titik masuk.
- Setiap baris data harus memiliki identitas utama yang jelas, seperti ID/kode, nama, status, dan relasi penting.
- Aksi `Detail`, `Edit`, dan `Konfigurasi` harus dipisahkan sesuai fungsi.
- Halaman konfigurasi tidak digabung terlalu padat ke modal tambah/edit jika isinya kompleks.
- Form personal memakai pola identitas KTP.
- Form organisasi memakai pola profil organisasi, alamat, PIC, rekening, logo, dan dokumen pendukung.
- Desain baru harus mengikuti konvensi tampilan data di `AGENTS.md`.
- Jika struktur database belum tersedia, desain boleh mencatat kebutuhan field, tetapi implementasi harus menunggu konfirmasi migrasi.

## Pola Halaman Daftar

Halaman daftar dipakai untuk modul seperti:

- Perusahaan
- Karyawan
- Lender
- Pengurus/PIC
- Referral
- User
- Produk dan konfigurasi produk

Komponen umum halaman daftar:

- Breadcrumb sesuai posisi modul.
- Judul halaman sesuai nama modul.
- Kartu ringkasan jika modul membutuhkan total/status.
- Tombol tambah data di kanan atas area tabel.
- Tabel utama dengan pencarian, pagination, responsive detail, dan kolom aksi.

Kolom umum tabel:

- ID / Kode
- Nama
- Relasi utama, seperti perusahaan, lender, atau produk
- Email atau telepon jika relevan
- Status
- Tanggal penting jika relevan
- Aksi

Kolom aksi umum:

- `Detail`, untuk melihat ringkasan data.
- `Edit`, untuk mengubah data utama.
- `Konfigurasi`, untuk masuk ke halaman pengaturan lanjutan.
- `Hapus`, hanya jika modul memang mengizinkan penghapusan.

## Contoh Halaman Daftar Perusahaan

Halaman `Admin Perusahaan` menampilkan:

- Breadcrumb `Admin > Perusahaan`.
- Judul halaman `Perusahaan`.
- Dua kartu ringkasan:
  - `Perusahaan Aktif`
  - `Perusahaan Nonaktif`
- Tombol `Tambah Perusahaan` di kanan atas tabel.
- Tabel `companiesTable` untuk daftar perusahaan.

Kolom utama tabel:

- Perusahaan ID / Kode
- Nama
- PIC Perusahaan
- PIC Internal
- Email
- Telepon
- Status
- Lender
- Aksi

Kolom detail responsif disimpan sebagai kolom tersembunyi DataTables, seperti kode unik, alamat, bank, referral, set pinjaman, tanggal aktif, dan max plafon.

## Pola Navigasi ke Konfigurasi

Pola navigasi ini berlaku untuk banyak modul, bukan hanya perusahaan. Halaman daftar menjadi tempat memilih data, lalu tombol `Konfigurasi` mengarah ke halaman konfigurasi spesifik berdasarkan ID data tersebut.

Contoh modul yang dapat memakai pola ini:

- Perusahaan ke konfigurasi produk perusahaan.
- Lender ke konfigurasi produk lender.
- Karyawan ke konfigurasi limit/plafon jika dibutuhkan.
- Produk ke konfigurasi tenor, DSR, biaya, referral, atau plafon.
- User ke pengaturan akses, role, atau preferensi jika dibutuhkan.

Alur umum:

```text
Admin > {Modul}
  -> klik Konfigurasi pada salah satu data
  -> Admin > {Modul} > Konfigurasi
  -> tampil konfigurasi untuk data terpilih
```

Tujuan halaman konfigurasi umum:

- Menampilkan header berisi ID/kode, nama, status, dan relasi utama.
- Menampilkan konfigurasi berdasarkan data yang dipilih.
- Memisahkan pekerjaan konfigurasi dari modal tambah/edit agar halaman daftar tetap ringan.
- Tetap menyediakan tombol `Kembali` ke halaman daftar.

## Contoh Navigasi Perusahaan ke Konfigurasi

Halaman `Admin Perusahaan` menjadi halaman daftar utama untuk memilih perusahaan sebelum masuk ke konfigurasi. Setiap baris perusahaan harus memiliki identitas yang jelas dan aksi yang mengarah ke halaman konfigurasi perusahaan tersebut.

Kolom identitas perusahaan:

- `Perusahaan ID / Kode`, memakai `id_code_perusahaan` jika tersedia.
- `Nama Perusahaan`, ditampilkan huruf besar.
- `Lender`, untuk menunjukkan pemilik/mitra lender yang terkait.
- `Status`, memakai badge aktif/nonaktif.

Kolom aksi:

- `Detail`, untuk membuka ringkasan atau modal detail perusahaan tanpa meninggalkan halaman.
- `Konfigurasi`, untuk masuk ke halaman konfigurasi perusahaan.

Alur navigasi:

```text
Admin > Perusahaan
  -> klik Konfigurasi pada salah satu perusahaan
  -> Admin > Perusahaan > Konfigurasi
  -> tampil konfigurasi produk untuk perusahaan terpilih
```

Tujuan halaman konfigurasi:

- Menampilkan header berisi `Perusahaan ID / Kode`, nama perusahaan, lender, dan status.
- Menampilkan konfigurasi produk berdasarkan perusahaan yang dipilih.
- Memisahkan pekerjaan konfigurasi dari modal tambah/edit perusahaan agar tabel utama tetap ringan.
- Tetap menyediakan tombol `Kembali` ke halaman daftar perusahaan.

Pola route yang sudah ada dan bisa dijadikan acuan:

```text
admin.ksp.konfigurasi
admin.product-configurations.tenor.companies
admin.product-configurations.tenor.edit
admin.product-configurations.dsr.companies
admin.product-configurations.dsr.edit
```

Jika tombol `Konfigurasi` dipasang pada tabel `companiesTable`, URL konfigurasi dapat memakai data `configuration_url` dari response detail perusahaan atau route konfigurasi yang menerima ID perusahaan terenkripsi.

Halaman konfigurasi perusahaan idealnya memiliki tab atau bagian:

- Profil singkat perusahaan
- Tomi Dana
- Tomi Dana Plus
- Tomi Gawai
- Tomi Gaji
- Tomi Invoice
- Referral
- Tenor / DSR / Max Plafon sesuai produk

Tombol `Detail` dan `Konfigurasi` tidak boleh memiliki fungsi yang sama:

- `Detail` hanya untuk melihat informasi perusahaan.
- `Konfigurasi` untuk mengubah aturan produk, tenor, referral, plafon, dan status fitur perusahaan.

## Pola Form Modal

Modal dipakai untuk tambah/edit data yang masih ringkas. Jika form sudah memiliki banyak tab, referral, dokumen, konfigurasi tenor, atau plafon, pertimbangkan memindahkan konfigurasi lanjutan ke halaman tersendiri.

Aturan umum modal:

- Gunakan ukuran modal sesuai kompleksitas form.
- Body modal boleh scroll, footer tetap terlihat.
- Tombol utama berada di footer.
- Field error ditampilkan dekat input.
- Jangan memakai modal untuk proses konfigurasi panjang jika halaman khusus lebih jelas.

## Contoh Modal Perusahaan

Modal perusahaan memakai ukuran `modal-xl`, posisi tengah, dan isi modal dapat di-scroll.

Bagian modal:

- Header: judul `Tambah Perusahaan` atau mode edit.
- Body: form data perusahaan dan tab produk.
- Footer: tombol `Batal` dan `Simpan`.

Field data perusahaan berada di atas tab produk:

- Nama Perusahaan
- Email Perusahaan
- PIC Perusahaan
- PIC Internal
- Telepon
- Provinsi
- Kota
- Kecamatan
- Kalurahan
- Alamat Lengkap
- Bank
- No Rekening
- Atas Nama Rekening
- Direktur Utama
- Nomor PKS
- Lender

Field teks utama menggunakan `text-uppercase`, kecuali field email.

## Pola Tab Produk

Tab produk dipakai ketika satu data utama memiliki beberapa konfigurasi produk. Pola ini dapat digunakan di halaman perusahaan, lender, atau produk jika data yang diatur masih berada dalam satu konteks.

Aturan umum tab produk:

- Nama tab harus singkat dan sesuai nama produk.
- Tab aktif memakai warna utama aplikasi.
- Field dasar produk berada di bagian atas.
- Referral, tenor, DSR, plafon, dan biaya dipisahkan dalam area yang mudah dipindai.
- Jika satu tab terlalu panjang, pindahkan bagian kompleks ke halaman konfigurasi khusus.

## Contoh Tab Produk Perusahaan

Modal perusahaan memiliki tab produk:

- Tomi Dana
- Tomi Dana Plus
- Tomi Gawai
- Tomi Gaji
- Tomi Invoice

Tab aktif memakai warna utama `#536de6` pada teks dan border bawah.

## Contoh Desain Tomi Dana

Tab Tomi Dana menampilkan field konfigurasi dasar di bagian atas:

- Status
- Set Pinjaman
- Referral
- Tanggal Aktif
- Payment Date
- H-Min Jatuh Tempo

Setelah field dasar, terdapat area referral:

- Tombol `Tambah Refferal`
- Catatan kecil `Maksimal 2 refferal`
- Tabel daftar referral

Kolom tabel referral:

- Refferal
- Nama
- NIK
- Telepon
- Email
- Aksi

Di bawah area referral terdapat daftar konfigurasi Tomi Dana. Area ini tersembunyi secara default dengan class `d-none` pada `#tomi_dana_configuration_wrapper`, lalu ditampilkan oleh JavaScript ketika diperlukan.

Daftar konfigurasi Tomi Dana memiliki tombol `Tambah Konfigurasi` dan tabel `companyTenorTable`.

Kolom konfigurasi:

- Tenor
- Biaya Bunga
- Biaya Asuransi
- Biaya Provisi
- Biaya Layanan
- Biaya Admin
- Lender
- Tomi
- Refferal 1
- Refferal 2
- Maximal Nominal
- Minimal Nominal
- Aksi

## Contoh Desain Tomi Dana Plus

Tab Tomi Dana Plus dibuat mengikuti pola Tomi Dana.

Field dasar berada di bagian atas:

- Status
- Set Pinjaman
- Tanggal Aktif
- Payment Date
- H-Min Jatuh Tempo

Setelah field dasar, terdapat area referral:

- Tombol `Tambah Refferal`
- Catatan kecil `Maksimal 2 refferal`
- Tabel daftar referral

Kolom tabel referral sama dengan Tomi Dana:

- Refferal
- Nama
- NIK
- Telepon
- Email
- Aksi

Saat ini Tomi Dana Plus tidak memiliki tabel konfigurasi tenor terpisah di tampilan ini.

## Contoh Desain Tomi Gawai

Tab Tomi Gawai berisi area referral:

- Tombol `Tambah Refferal`
- Catatan kecil `Maksimal 2 refferal`
- Tabel daftar referral

Kolom tabel referral:

- Refferal
- Nama
- NIK
- Telepon
- Email
- Aksi

## Contoh Desain Tomi Gaji

Tab Tomi Gaji berisi:

- Max Plafon
- Tombol `Tambah Refferal`
- Catatan kecil `Maksimal 2 refferal`
- Tabel daftar referral

Field `Max Plafon` memakai input tampilan rupiah dan hidden input untuk nilai angka asli.

## Contoh Desain Tomi Invoice

Tab Tomi Invoice berisi:

- Max Plafon
- Tombol `Tambah Refferal`
- Catatan kecil `Maksimal 2 refferal`
- Tabel daftar referral

Field `Max Plafon` memakai format tampilan rupiah.

## Modal Form Refferal

Form referral disimpan di partial:

```text
resources/views/admin/companies/partials/referral-form.blade.php
```

Form referral tidak langsung tampil di halaman. Setiap produk memiliki slot referral tersembunyi untuk maksimal dua referral. Ketika tombol `Tambah Refferal` atau edit diklik, isi form dipindahkan ke `#referralFormModalBody`.

Modal referral memakai ukuran `modal-lg`, posisi tengah, dan body dapat di-scroll.

Field referral:

- NIK
- Nama
- Status
- Tempat Lahir
- Tanggal Lahir
- Gender
- Gol. Darah
- Provinsi
- Kota/Kabupaten
- Kecamatan
- Kelurahan/Desa
- RT
- RW
- Alamat
- Kode Pos
- Agama
- Status Perkawinan
- Pekerjaan
- Kewarganegaraan
- Telepon/WhatsApp
- Email
- Bank
- No. Rekening
- Nama Pemilik Rekening
- Catatan

Prefix field dibentuk dari kombinasi produk dan index:

```text
{prefix}_referral_{index}
```

Contoh:

```text
tomi_dana_referral_1_name
tomi_dana_plus_referral_2_phone_number
```

## Modal Konfigurasi Tenor

Modal konfigurasi tenor dipakai untuk konfigurasi Tomi Dana.

Modal memakai ukuran `modal-lg`, posisi tengah, dan body dapat di-scroll.

Field konfigurasi tenor:

- Tenor
- Biaya Bunga
- Biaya Asuransi
- Biaya Provisi
- Biaya Layanan
- Biaya Admin
- Lender
- Tomi
- Refferal 1
- Refferal 2
- Maximal Nominal
- Minimal Nominal

Field persentase memakai input group dengan suffix `%`.

Field nominal rupiah memakai input tampilan dan hidden input nilai asli.

## Aturan Layout

- Modal utama perusahaan dibatasi tinggi `calc(100vh - 3.5rem)`.
- Body modal utama dibatasi tinggi `calc(100vh - 12rem)` dan dapat di-scroll.
- Footer modal dibuat tetap terlihat dengan `flex-shrink: 0`.
- Modal referral dan konfigurasi tenor memiliki `z-index: 1065`.
- Backdrop modal tambahan memiliki `z-index: 1060`.
- Tombol tambah referral berada di kanan area referral.
- Teks `Maksimal 2 refferal` berada sebelum tombol tambah secara urutan flex.
- Tabel referral memakai `table-sm`, `table-bordered`, dan `text-center`.

## Konvensi Tampilan Data

- Field teks utama ditampilkan huruf besar dengan class `text-uppercase`, kecuali email.
- Email tetap menggunakan format asli.
- Rupiah ditampilkan dengan format `Rp. 1.000.000`.
- Angka biasa mengikuti format ribuan Indonesia.
- Field referral maksimal dua orang per produk.

## Standar Form Data User, Karyawan, Perusahaan, dan Lender

Setiap desain form data utama harus memakai pola data yang konsisten dengan form identitas KTP. Pola ini dipakai untuk `users`, `karyawan`, pengurus/PIC perusahaan, pengurus/PIC lender, dan referral.

Field identitas KTP yang menjadi acuan:

- NIK
- Nama
- Status
- Tempat Lahir
- Tanggal Lahir
- Gender
- Gol. Darah
- Provinsi
- Kota/Kabupaten
- Kecamatan
- Kelurahan/Desa
- RT
- RW
- Alamat KTP
- Kode Pos
- Agama
- Status Perkawinan
- Pekerjaan
- Kewarganegaraan
- Telepon/WhatsApp
- Email
- File KTP

Field rekening yang menjadi acuan:

- Bank
- No. Rekening
- Nama Pemilik Rekening
- File Buku Rekening/Buku Tabungan

Khusus data perusahaan:

- Tetap menampilkan field profil perusahaan yang sudah ada.
- Tambahkan area logo perusahaan pada desain form jika kolom/path logo tersedia.
- Tambahkan area buku rekening perusahaan pada desain form jika kolom/path file tersedia.
- Data PIC perusahaan mengikuti pola identitas KTP jika form PIC dibuat sebagai data lengkap, bukan hanya nama singkat.

Khusus data lender:

- Tetap menampilkan field profil lender yang sudah ada.
- Logo lender menjadi bagian dari form profil lender.
- Buku rekening lender menjadi bagian dari form rekening lender.
- Pengurus/PIC lender mengikuti pola identitas KTP seperti form `lender-officers`.

Khusus data karyawan:

- Form manual dan template import karyawan harus selaras untuk NIK, nama karyawan, alamat KTP, telepon/WhatsApp, status karyawan, data perusahaan, data lender, bank, nomor rekening, dan nama rekening.
- Jika upload File KTP atau Buku Rekening belum tersedia di struktur data karyawan, desain boleh menandainya sebagai kebutuhan migrasi, bukan langsung mengubah field yang belum ada.

## Template Desain Modul Baru

Gunakan struktur ini saat mendesain halaman baru:

```text
Admin > {Nama Modul}
  Ringkasan data
  Tabel daftar data
  Aksi: Detail / Edit / Konfigurasi / Hapus

Admin > {Nama Modul} > Detail
  Profil singkat
  Identitas atau data organisasi
  Data rekening
  Dokumen pendukung
  Riwayat atau status jika relevan

Admin > {Nama Modul} > Konfigurasi
  Header data terpilih
  Tab atau section konfigurasi
  Tombol simpan per bagian
  Tombol kembali
```

Checklist sebelum membuat desain modul baru:

- Tentukan ID/kode utama yang tampil di tabel.
- Tentukan relasi penting, seperti perusahaan, lender, produk, atau user.
- Tentukan apakah data cukup diedit lewat modal atau butuh halaman detail.
- Tentukan apakah konfigurasi perlu halaman sendiri.
- Tentukan apakah membutuhkan form identitas KTP.
- Tentukan apakah membutuhkan data rekening dan dokumen buku rekening.
- Tentukan apakah membutuhkan logo atau file pendukung.
- Pastikan field baru tidak diasumsikan tersedia jika belum ada kolom/tabel.

## Catatan Implementasi

- Jangan mengubah `id` dan `name` field tanpa mengecek JavaScript dan controller terkait.
- Tombol referral bergantung pada class `.add-referral-button` dan atribut `data-prefix`.
- Tabel referral bergantung pada id `{prefix}_referral_table_body`.
- Slot form referral bergantung pada `.feature-referral-slot`, `data-prefix`, dan `data-index`.
- Form referral modal bergantung pada container `#referralFormModalBody`.
- Konfigurasi Tomi Dana bergantung pada `#tomi_dana_configuration_wrapper`, `#companyTenorTable`, dan `#pendingTenorConfigBody`.
- Ikuti aturan migrasi di `AGENTS.md`: jangan membuat kolom baru untuk logo, File KTP, atau Buku Rekening tanpa permintaan eksplisit.
