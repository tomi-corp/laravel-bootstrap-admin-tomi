<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perjanjian Kredit - PT. BPR Tata Asia</title>
    <style>
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 11pt;
            line-height: 1.15;
        }


        .container {
            width: 159.2mm;
            /* 210mm - (25.4mm * 2) */
            margin: 25.4mm auto;
            padding: 0;
            box-sizing: border-box;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-justify {
            text-align: justify;
            text-align-last: left;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .mt-1 {
            margin-top: 10px;
        }

        .mt-2 {
            margin-top: 20px;
        }

        .mt-3 {
            margin-top: 30px;
        }

        .mt-4 {
            margin-top: 40px;
        }

        .mb-1 {
            margin-bottom: 10px;
        }

        .mb-2 {
            margin-bottom: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 16pt;
            margin: 0;
            text-decoration: underline;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .header p {
            font-size: 11pt;
            margin: 3px 0;
            line-height: 1.4;
        }

        .nomor {
            text-align: center;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        .pasal {
            margin-top: 25px;
        }

        .pasal-title {
            font-weight: bold;
            margin-bottom: 12px;
            font-size: 12pt;
            text-align: center;
            margin-bottom: 0 !important;
        }

        .pasal-subtitle {
            font-weight: bold;
            margin-bottom: 12px;
            font-size: 12pt;
            text-align: center;
        }

        .indent {
            margin-left: 40px;
            text-align: justify;
        }

        .indent-2 {
            margin-left: 80px;
        }

        .signature-section {
            margin-top: 70px;
        }

        .signature-box {
            display: inline-block;
            width: 45%;
            vertical-align: top;
            text-align: center;
        }

        .signature-left {
            float: left;
        }

        .signature-right {
            float: right;
        }

        .signature-name {
            margin-top: 80px;
            font-weight: bold;
            font-size: 12pt;
        }

        .signature-title {
            margin-top: 5px;
            font-size: 11pt;
        }

        .page-break {
            page-break-after: always;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
            border: none;
            table-layout: fixed;
        }

        table,
        th,
        td {
            /* border: 1px solid #000; */
            border: none;
        }

        th,
        td {
            padding: 10px 12px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            width: 35%;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 20mm;
            width: 160mm;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .nomor-rekening {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .long-text {
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PERJANJIAN KREDIT</h1>
            <p>Nomor: {{ $nomor ?? '___________' }}/BTA/KK/{{ $bulan ?? '___' }}/{{ $tahun ?? '2026' }}</p>
            <p class="nomor-rekening">Nomor Rekening: {{ $nomor_rekening ?? '____________________' }}</p>
        </div>

        <!-- Pembuka -->
        <p class="text-justify">
            Yang bertandatangan dibawah ini :
        </p>

        <p class="indent">
            1. <span class="bold">TB. FRANDY PRIYA PURWANTO, S.H.</span><br>
            Dalam hal ini sah bertindak dan untuk atas nama <span class="bold">PT BANK PEREKONOMIAN RAKYAT TATA ASIA</span>, suatu Perseroan Terbatas yang beralamat di Gedung Wisma Bumiputera Ground Floor - Mezzanine, Jl. Asia Afrika No. 141-149 Kebon Pisang Sumur Kota Bandung, untuk selanjutnya disebut sebagai <span class="bold">"BANK"</span>.
        </p>

        <p class="indent mt-2">
            2. <span class="bold">{{ $debitur_nama ?? 'MUHAMMAD HIMAWAN ARDIANSYAH' }}</span>,<br>
            Warga Negara Indonesia, beralamat di <span class="bold">{{ $debitur_alamat ?? 'KP. PIPITAN RT 008/002 PIPITAN WALANTAKA KOTA SERANG' }}</span>, pemegang Kartu Tanda Penduduk Nomor: <span class="bold">{{ $debitur_ktp ?? '3604100405950714' }}</span>, sebagai penerima fasilitas Pinjaman Kredit yang diberikan oleh BANK untuk selanjutnya disebut sebagai <span class="bold">"DEBITUR"</span>.
        </p>

        <p class="text-justify mt-2">
            BANK dan DEBITUR dengan ini telah sepakat untuk mengadakan Perjanjian Kredit dengan ketentuan dan syarat-syarat sebagai berikut:
        </p>

        <!-- Pasal 1 -->
        <div class="pasal">
            <p class="pasal-title">Pasal 1</p>
            <p class="pasal-subtitle">DEFINISI</p>
            <p class="text-justify">
                Untuk keperluan Fasilitas Kredit, istilah-istilah dibawah ini mempunyai arti sebagaimana diuraikan sebagai berikut:
            </p>
            <p class="indent">
                1. <span class="bold">Fasilitas Pinjaman Kredit</span> adalah pemberian fasilitas kredit yang dilakukan BANK kepada DEBITUR dengan jangka waktu dan suku bunga yang telah ditentukan dan disepakati antara BANK dengan DEBITUR.
            </p>
            <p class="indent">
                2. <span class="bold">Angsuran</span> adalah sejumlah uang yang dibayarkan oleh DEBITUR kepada BANK pada setiap bulan dengan jumlah dan tanggal sebagaimana tersebut dalam Perjanjian ini.
            </p>
            <p class="indent">
                3. <span class="bold">Jangka Waktu</span> adalah jangka waktu dimulainya fasilitas Pinjaman Kredit sebagaimana diatur dalam perjanjian ini sampai dengan Perjanjian Kredit ini berakhir.
            </p>
            <p class="indent">
                4. <span class="bold">Biaya-biaya</span> adalah sejumlah uang yang dibayarkan untuk biaya-biaya, termasuk biaya administrasi, provisi, asuransi dan lain-lain dalam pelaksanaan proses Perjanjian Kredit ini.
            </p>
            <p class="indent">
                5. <span class="bold">Utang</span> adalah semua jumlah uang yang terutang oleh DEBITUR pada suatu waktu kepada BANK yang meliputi jumlah pokok, bunga, biaya-biaya, denda dan atau kewajiban-kewajiban lain berdasarkan Perjanjian Kredit ini.
            </p>
        </div>

        <!-- Pasal 2 -->
        <div class="pasal">
            <p class="pasal-title">Pasal 2</p>
            <p class="pasal-title">JUMLAH PINJAMAN, BUNGA, JANGKA WAKTU, ANGSURAN DAN TUJUAN PENGGUNAAN</p>
            <p class="text-justify">
                Atas permintaan DEBITUR, BANK setuju untuk memberikan fasilitas kredit kepada DEBITUR dengan ketentuan sebagai berikut:
            </p>
            <table>
                <tr>
                    <th>1. Jumlah Pinjaman</th>
                    <td>Rp. {{ number_format($jumlah_pinjaman ?? 0, 0, ',', '.') ?? '_____________' }},- <br>
                        ({{ $jumlah_pinjaman_terbilang ?? '_______________________________________________________' }})
                    </td>
                </tr>
                <tr>
                    <th>2. Bunga Kredit</th>
                    <td>{{ $bunga ?? '___' }}% per tahun</td>
                </tr>
                <tr>
                    <th>3. Jangka Waktu</th>
                    <td>{{ $jangka_waktu ?? '___' }} Bulan</td>
                </tr>
                <tr>
                    <th>4. Angsuran Per bulan</th>
                    <td>Rp. {{ number_format($angsuran ?? 0, 0, ',', '.') ?? '_____________' }},- <br>
                        ({{ $angsuran_terbilang ?? '_______________________________________________________' }})
                    </td>
                </tr>
                <tr>
                    <th>5. Tujuan Penggunaan</th>
                    <td>{{ $tujuan_penggunaan ?? 'Konsumtif' }}</td>
                </tr>
            </table>
        </div>

        <!-- Pasal 3 -->
        <div class="pasal">
            <p class="pasal-title">Pasal 3</p>
            <p class="pasal-title">JAMINAN PEMBAYARAN DAN AGUNAN</p>
            <p class="text-justify">
                Untuk menjamin bahwa DEBITUR akan melakukan pembayaran tepat waktu yang telah disepakati oleh para pihak berdasarkan Perjanjian Kredit ini, maka DEBITUR dengan ini mengikatkan diri dan menyerahkan:
            </p>
            <p class="indent">1. {{ $jaminan_1 ?? '_______________________________________________________________' }}</p>
            <p class="indent">2. {{ $jaminan_2 ?? '_______________________________________________________________' }}</p>
            <p class="indent">3. {{ $jaminan_3 ?? '_______________________________________________________________' }}</p>
            <p class="indent">4. {{ $jaminan_4 ?? '_______________________________________________________________' }}</p>
        </div>

        <!-- Pasal 4 -->
        <div class="pasal">
            <p class="pasal-title">Pasal 4</p>
            <p class="pasal-title">KUASA</p>
            <p class="indent">
                1. DEBITUR dengan ini memberi kuasa kepada BANK untuk memblokir, membekukan, mendebet dan mempergunakan dana yang tersimpan pada BANK baik dalam tabungan atau deposito dan dana yang tersimpan seperti dimaksud pada pasal 3 ayat 1 s/d 3 milik DEBITUR, untuk dipergunakan pada pembayaran seluruh utang, provisi, denda, premi asuransi, bea materai & biaya lainnya, berkenaan dengan pemberian Fasilitas Kredit ini.
            </p>
            <p class="indent mt-1">
                2. Semua kuasa yang tercantum dalam Perjanjian Kredit ini merupakan bagian yang tidak terpisahkan dan oleh karena itu kuasa-kuasa tersebut tidak dapat ditarik kembali dan atau dibatalkan dengan cara apapun atau disebabkan hal-hal yang dimaksud dalam pasal 1813, 1814 dan 1816 kitab Undang-Undang Hukum Perdata selama utang DEBITUR kepada BANK berdasarkan Perjanjian Kredit belum LUNAS.
            </p>
        </div>

        <!-- Pasal 5 -->
        <div class="pasal">
            <p class="pasal-title">Pasal 5</p>
            <p class="pasal-title">PEMBAYARAN UTANG</p>
            <p class="indent">
                1. Pembayaran atas utang dilakukan DEBITUR sesuai dengan jadwal pembayaran yang terlampir pada skedul angsuran pada Perjanjian Kredit ini.
            </p>
            <p class="indent mt-1">
                2. DEBITUR diperbolehkan untuk melakukan pelunasan sebagian dan atau seluruh kredit sebelum jatuh tempo kredit, dengan memperhatikan dan menyetujui ketentuan biaya-biaya, bunga berjalan, bunga kedepan dan atau pinalti yang berlaku pada BANK.
            </p>
            <p class="indent mt-1">
                3. Apabila DEBITUR terlambat membayar utang pokok dan atau bunga sesuai jadwal yang telah ditentukan, maka DEBITUR dikenakan denda sebesar 3 permil per hari atas jumlah utang pokok dan atau yang harus dibayar secara sekaligus dan tunai bersamaan dengan utang pokok dan atau yang tertunggak.
            </p>
        </div>

        <!-- Pasal 6 -->
        <div class="pasal">
            <p class="pasal-title">Pasal 6</p>
            <p class="pasal-title">HUKUM YANG BERLAKU</p>
            <p class="text-justify">
                DEBITUR dengan ini menyatakan pengakuan dengan sebenar-benarnya serta menjamin kepada BANK, sebagaimana BANK menerima pernyataan dan pengakuan DEBITUR, bahwa:
            </p>
            <p class="indent">
                1. DEBITUR berhak dan berwenang sepenuhnya untuk menandatangani Perjanjian ini, tidak dalam tekanan atau paksaan dari pihak manapun.
            </p>
            <p class="indent mt-1">
                2. DEBITUR menjamin bahwa segala surat dan dokumen serta akta yang DEBITUR tandatangani dan/atau gunakan berkaitan dengan Perjanjian Kredit ini adalah benar, keberadaannya sah, dan tindakan DEBITUR tidak melanggar atau bertentangan dengan ketentuan dan/atau hukum yang berlaku.
            </p>
            <p class="indent mt-1">
                3. DEBITUR adalah Perseorangan yang tunduk pada hukum Negara Republik Indonesia.
            </p>
            <p class="indent mt-1">
                4. Diadakannya Perjanjian ini dan/atau Addendum Perjanjian ini tidak akan bertentangan dengan suatu Perjanjian yang telah ada atau yang akan diadakan oleh DEBITUR dengan pihak ketiga lainnya.
            </p>
            <p class="indent mt-1">
                5. Sepanjang tidak bertentangan dengan peraturan perundang-undangan yang berlaku, DEBITUR berjanji dan dengan ini mengikatkan diri mendahulukan untuk membayar dan melunasi kewajiban DEBITUR kepada BANK dari kewajiban lainnya.
            </p>
            <p class="indent mt-1">
                6. Dalam hal-hal yang berkaitan dengan ayat 1 s/d 5 Pasal ini, DEBITUR berjanji dan dengan ini mengikatkan diri untuk membebaskan BANK dari segala tuntutan atau gugatan yang datang dari pihak mana pun dan/atau atas alasan apapun.
            </p>
        </div>

        <!-- Pasal 7 -->
        <div class="pasal">
            <p class="pasal-title">Pasal 7</p>
            <p class="pasal-title">PENYELESAIAN PERSELISIHAN</p>
            <p class="indent">
                1. Apabila di kemudian hari terjadi perbedaan pendapat atau penafsiran atas hal-hal yang tercantum di dalam Perjanjian ini atau terjadi perselisihan atau sengketa dalam pelaksanaan Perjanjian ini, Para Pihak sepakat untuk menyelesaikannya secara musyawarah untuk mencapai mufakat.
            </p>
            <p class="indent mt-1">
                2. Apabila kedua belah pihak tidak berhasil menyelesaikan perselisihan secara musyawarah untuk mufakat maka kedua belah pihak sepakat untuk menempuh penyelesaian melalui jalur hukum. Untuk penyelesaian melalui saluran hukum, kedua belah pihak sepakat memilih tempat kedudukan hukum yang tepat pada Pengadilan Negeri Bandung / tempat yang ditunjuk oleh pihak BANK.
            </p>
        </div>

        <!-- Pasal 8 -->
        <div class="pasal">
            <p class="pasal-title">Pasal 8</p>
            <p class="pasal-title">KETENTUAN PENUTUP</p>
            <p class="indent">
                1. BANK dan DEBITUR dengan ini, sepakat dan setuju untuk melakukan seluruh ketentuan-ketentuan yang diatur di dalam Perjanjian ini.
            </p>
            <p class="indent mt-1">
                2. Sebelum Perjanjian Kredit ini ditandatangani oleh DEBITUR, DEBITUR mengakui dengan sebenarnya, dan tidak lain dari yang sebenarnya, bahwa DEBITUR telah membaca dengan cermat atau dibacakan kepadanya seluruh isi Perjanjian Kredit ini, sehingga oleh karena itu DEBITUR memahami sepenuhnya segala yang akan menjadi akibat hukum setelah DEBITUR menandatangani Perjanjian ini.
            </p>
            <p class="indent mt-1">
                3. Perjanjian ini mengikat Para Pihak yang sah, para pengganti atau pihak-pihak yang menerima hak dari masing-masing Para Pihak.
            </p>
            <p class="indent mt-1">
                4. Perjanjian ini memuat, dan karenanya menggantikan semua pengertian dan kesepakatan yang telah dicapai oleh Para Pihak sebelum ditandatanganinya Perjanjian ini, baik tertulis, lisan maupun elektronik, mengenai hal yang sama.
            </p>
            <p class="indent mt-1">
                5. Jika salah satu atau sebagian ketentuan-ketentuan dalam Perjanjian ini menjadi batal atau tidak berlaku, maka tidak mengakibatkan seluruh Perjanjian ini menjadi batal atau tidak berlaku seluruhnya.
            </p>
            <p class="indent mt-1">
                6. Apabila ada hal-hal yang belum diatur atau belum cukup diatur dalam Perjanjian ini, maka BANK dan DEBITUR akan mengaturnya Bersama secara musyawarah untuk mufakat dalam suatu Perjanjian Tambahan (Addendum) yang ditandatangani oleh Para Pihak dan merupakan satu kesatuan yang tidak terpisahkan dari Perjanjian ini.
            </p>
            <p class="indent mt-1">
                7. Perjanjian ini telah disesuaikan dengan ketentuan Peraturan Perundang-undangan, termasuk ketentuan Peraturan Otoritas Jasa Keuangan.
            </p>
        </div>

        <!-- Penutup -->
        <p class="text-justify mt-3">
            Demikian Perjanjian Kredit ini dibuat dan ditandatangani di {{ $tempat_ttd ?? 'Bandung' }} oleh BANK dan DEBITUR, yang masing-masing disimpan oleh BANK dan DEBITUR, dan masing-masing berlaku sebagai aslinya.
        </p>

        <!-- Signature Section -->
        <div class="signature-section mt-4">
            <div style="display: flex; justify-content: space-between; width: 100%;">
                <div class="signature-box signature-left">
                    <p>{{ $tempat_ttd ?? 'Bandung' }}, {{ \Carbon\Carbon::parse($tanggal_ttd ?? now())->format('d F Y') }}</p>
                    <p><span class="bold">PT. BPR TATA ASIA,</span></p>
                    <div class="signature-name mt-4">
                        TB. FRANDY PRIYA PURWANTO, S.H.<br>
                        Direktur Utama
                    </div>
                </div>

                <div class="signature-box signature-right">
                    <p><span class="bold">DEBITUR,</span></p>
                    <div class="signature-name mt-4">
                        {{ $debitur_nama ?? 'MUHAMMAD HIMAWAN ARDIANSYAH' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>