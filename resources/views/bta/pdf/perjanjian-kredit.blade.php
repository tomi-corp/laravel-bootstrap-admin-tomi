<!DOCTYPE html>
<html>

<head>
    <title>Perjanjian Kredit - BTA</title>
    <style>
        /* 1. SETUP KERTAS & MARGIN */
        @page {
            margin: 0cm;
            /* Margin 0 agar watermark nempel pinggir */
        }

        .list-transparan li {
            opacity: 0.8;
            /* Nilai dari 0.0 sampai 1.0 */
            /* list-style: none; */
            margin-bottom: 5px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            /* Standar surat resmi */
            line-height: 1.3;
            /* Margin konten agar tidak tertutup header/watermark */
            margin-top: 3.5cm;
            margin-left: 3.5cm;
            margin-right: 2.5cm;
            margin-bottom: 2.5cm;
            text-align: justify;
            /* Teks rata kanan-kiri */
        }

        /* 2. BACKGROUND GELOMBANG (Hanya di Kiri) */
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 30%;
            /* Lebar area background */
            z-index: -1000;
        }

        .watermark img {
            height: 100%;
            width: auto;
            opacity: 0.6;
        }

        /* 3. KOP SURAT (Logo) */
        header {
            position: fixed;
            top: 1cm;
            left: 3.5cm;
            right: 2.5cm;
            height: 3cm;
        }

        .logo img {
            height: 45px;
            /* Sesuaikan ukuran logo */
        }

        .line-header {
            border-bottom: 2px solid #000;
            /* Garis tebal kop surat */
            margin-top: 10px;
            width: 100%;
        }

        /* 4. TYPOGRAPHY (Judul, Pasal, dll) */
        .u-bold {
            font-weight: bold;
            text-decoration: none;
            /* Matikan underline biasa */
            border-bottom: 2px solid #000;
            /* Ganti dgn border 2px (Lebih Tebal) */
            display: inline-block;
            /* Agar garis pas sepanjang teks saja */
            line-height: 1.1;
            /* Jarak spasi agar garis tidak nempel huruf */
        }

        .judul-utama {
            text-align: center;
            font-size: 16pt;
            margin-bottom: 1px;
            text-transform: uppercase;
        }

        .info-surat {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 16pt;
            font-family: 'Calibri', sans-serif;
        }

        .info-surat .rekening {
            text-align: left;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 16pt;
            font-family: 'Calibri', sans-serif;
            padding-left: 100px
        }

        .pasal-title {
            text-align: center;
            font-weight: bold;
            margin-top: 15px;
            text-transform: uppercase;
        }

        .pasal-sub {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        /* 5. TABEL PERATAAN (Titik Dua Sejajar) */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .table-data td {
            vertical-align: top;
            padding-bottom: 3px;
        }

        .col-no {
            width: 25px;
            font-weight: bold;
        }

        .col-lbl {
            width: 140px;
        }

        .col-sep {
            width: 15px;
            text-align: center;
        }

        /* Helper Classes */
        .bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .indent {
            padding-left: 20px;
        }

        /* Untuk poin a, b, c */

        /* Page Break (Pemisah Halaman jika perlu) */
        .page-break {
            page-break-after: always;
        }

        /* Tanda Tangan */
        .ttd-wrapper {
            margin-top: 30px;
            width: 100%;
        }

        .ttd-table {
            width: 100%;
            text-align: center;
        }

        .ttd-space {
            height: 120px;
            vertical-align: middle;
        }

        .materai-img {
            width: 80px;
            /* Ukuran gambar materai sesuai Picture3.jpg */
            margin: 5px 0;
        }

        /* Ruang tanda tangan */
    </style>
</head>

<body>

    <div class="watermark">
        <img src="{{ public_path('assets/images/picture2.png') }}" alt="bg">
    </div>

    <header>
        <div class="logo">
            <img src="{{ public_path('assets/images/picture1.png') }}" alt="logo">
        </div>
        <div class="line-header"></div>
    </header>

    <main>
        <div class="judul-utama">
            <span class="u-bold">PERJANJIAN KREDIT</span>
        </div>
        <div class="info-surat">
            <div>
                Nomor: {{ $data['no_surat'] }}/BTA/KK/{{ $data['bulan'] }}/{{ $data['tahun'] }}
            </div>
            <div class="rekening" style="font-weight: bold; font-size: 12pt; margin-top: 2px;">
                Nomor Rekening:
            </div>
        </div>


        <p style="margin-top: 40px;">Yang bertandatangan dibawah ini :</p>

        <table class="table-data">
            <tr>
                <td class="col-no">1.</td>
                <td colspan="3" class="bold">TB. FRANDY PRIYA PURWANTO, S.H.</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3">
                    Dalam hal ini sah bertindak dan untuk atas nama <span class="bold">PT BANK PEREKONOMIAN RAKYAT
                        TATA ASIA</span>,
                    suatu Perseroan Terbatas yang beralamat di Gedung Wisma Bumiputera Ground Floor - Mezzanine,
                    Jl. Asia Afrika No. 141-149 Kebon Pisang Sumur Kota Bandung, untuk selanjutnya disebut sebagai <span
                        class="bold">“BANK”</span>.
                </td>
            </tr>
        </table>

        <table class="table-data" style="margin-top: 10px;">
            <tr>
                <td class="col-no">2.</td>
                <td colspan="3" class="bold underline">{{ strtoupper($data['nama_debitur']) }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="col-lbl">Warga Negara</td>
                <td class="col-sep">:</td>
                <td>Indonesia</td>
            </tr>
            <tr>
                <td></td>
                <td class="col-lbl">Beralamat di</td>
                <td class="col-sep">:</td>
                <td>{{ $data['alamat'] }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="col-lbl">No. KTP</td>
                <td class="col-sep">:</td>
                <td>{{ $data['no_ktp'] }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3" style="padding-top: 5px;">
                    Sebagai penerima fasilitas Pinjaman Kredit yang diberikan oleh BANK untuk selanjutnya disebut
                    sebagai <span class="bold">“DEBITUR”</span>.
                </td>
            </tr>
        </table>

        <p>
            BANK dan DEBITUR dengan ini telah sepakat untuk mengadakan Perjanjian Kredit dengan ketentuan dan
            syarat-syarat sebagai berikut :
        </p>

        <div class="pasal-title">Pasal 1</div>
        <div class="pasal-sub">DEFINISI</div>
        <p>Untuk keperluan Fasilitas Kredit, istilah-istilah dibawah ini mempunyai arti sebagaimana diuraikan sebagai
            berikut:</p>
        <ol style="margin-left: -20px;">
            <li><b>Fasilitas Pinjaman Kredit</b> adalah pemberian fasilitas kredit yang dilakukan BANK kepada DEBITUR
                dengan jangka waktu dan suku bunga yang telah ditentukan dan disepakati antara BANK dengan DEBITUR.</li>
            <li><b>Angsuran</b> adalah sejumlah uang yang dibayarkan oleh DEBITUR kepada BANK pada setiap bulan dengan
                jumlah dan tanggal sebagaimana tersebut dalam Perjanjian ini.</li>
            <li><b>Jangka Waktu</b> adalah jangka waktu dimulainya fasilitas Pinjaman Kredit sebagaimana diatur dalam
                perjanjian ini sampai dengan Perjanjian Kredit ini berakhir.</li>
            <li><b>Biaya-biaya</b> adalah sejumlah uang yang dibayarkan untuk biaya-biaya, termasuk biaya administrasi,
                provisi, asuransi dan lain-lain dalam pelaksanaan proses Perjanjian Kredit ini.</li>
            <li><b>Utang</b> adalah semua jumlah uang yang terutang oleh DEBITUR pada suatu waktu kepada BANK yang
                meliputi jumlah pokok, bunga, biaya-biaya, denda dan atau kewajiban-kewajiban lain berdasarkan
                Perjanjian Kredit ini.</li>
        </ol>

        <div class="pasal-title">Pasal 2</div>
        <div class="pasal-sub">JUMLAH PINJAMAN, BUNGA, JANGKA WAKTU,<br>ANGSURAN DAN TUJUAN PENGGUNAAN</div>
        <p>Atas permintaan DEBITUR, BANK setuju untuk memberikan fasilitas kredit kepada DEBITUR dengan ketentuan
            sebagai berikut :</p>

        <table class="table-data">
            <tr>
                <td style="width: 150px;">Jumlah Pinjaman</td>
                <td class="col-sep">:</td>
                <td>Rp. {{ number_format($data['plafond'], 0, ',', '.') }},- ({{ $data['terbilang_plafond'] }} Rupiah)
                </td>
            </tr>
            <tr>
                <td>Bunga Kredit</td>
                <td class="col-sep">:</td>
                <td>{{ $data['bunga'] }}% per tahun</td>
            </tr>
            <tr>
                <td>Jangka Waktu</td>
                <td class="col-sep">:</td>
                <td>{{ $data['jangka'] }} Bulan</td>
            </tr>
            <tr>
                <td>Angsuran Per bulan</td>
                <td class="col-sep">:</td>
                <td>Rp. {{ number_format($data['angsuran'], 0, ',', '.') }},- ({{ $data['terbilang_angsuran'] }}
                    Rupiah)</td>
            </tr>
            <tr>
                <td>Tujuan Penggunaan</td>
                <td class="col-sep">:</td>
                <td>{{ $data['tujuan'] }}</td>
            </tr>
        </table>

        <div class="pasal-title">Pasal 3</div>
        <div class="pasal-sub">JAMINAN PEMBAYARAN DAN AGUNAN</div>
        <p>Untuk menjamin bahwa DEBITUR akan melakukan pembayaran tepat waktu yang telah disepakati oleh para pihak
            berdasarkan Perjanjian Kredit ini, maka DEBITUR dengan ini mengikatkan diri dan menyerahkan:</p>
        <p style="margin-left: 20px;">
            @if (isset($data['agunan']))
                {{ $data['agunan'] }}
            @else
                <ul class="list-transparan">
                    <li>
                        <p></p>
                    </li>
                    <li>
                        <p></p>
                    </li>
                    <li>
                        <p></p>
                    </li>
                </ul>
            @endif
        </p>

        <div class="pasal-title">Pasal 4</div>
        <div class="pasal-sub">KUASA</div>
        <p>1. DEBITUR dengan ini memberi kuasa kepada BANK untuk memblokir, membekukan, mendebet dan mempergunakan dana
            yang tersimpan pada BANK baik dalam tabungan atau deposito dan dana yang tersimpan seperti dimaksud pada
            pasal 3 ayat 1 s/d 3 milik DEBITUR, untuk dipergunakan pada pembayaran seluruh utang, provisi, denda, premi
            asuransi, bea materai & biaya lainnya, berkenaan dengan pemberian Fasilitas Kredit ini.</p>
        <p>2. Semua kuasa yang tercantum dalam Perjanjian Kredit ini merupakan bagian yang tidak terpisahkan dan oleh
            karena itu kuasa-kuasa tersebut tidak dapat ditarik kembali dan atau dibatalkan dengan cara apapun atau
            disebabkan hal-hal yang dimaksud dalam pasal 1813, 1814 dan 1816 kitab Undang-Undang Hukum Perdata selama
            utang DEBITUR kepada BANK berdasarkan Perjanjian Kredit belum LUNAS.</p>

        <div class="pasal-title">Pasal 5</div>
        <div class="pasal-sub">PEMBAYARAN UTANG</div>
        <p>1. Pembayaran atas utang dilakukan DEBITUR sesuai dengan jadwal pembayaran yang terlampir pada skedul
            angsuran pada Perjanjian Kredit ini.</p>
        <p>2. DEBITUR diperbolehkan untuk melakukan pelunasan sebagian dan atau seluruh kredit sebelum jatuh tempo
            kredit, dengan memperhatikan dan menyetujui ketentuan biaya-biaya, bunga berjalan, bunga kedepan dan atau
            pinalti yang berlaku pada BANK.</p>
        <p>3. Apabila DEBITUR terlambat membayar utang pokok dan atau bunga sesuai jadwal yang telah ditentukan, maka
            DEBITUR dikenakan denda sebesar 3 permil per hari atas jumlah utang pokok dan atau yang harus dibayar secara
            sekaligus dan tunai bersamaan dengan utang pokok dan atau yang tertunggak.</p>

        <div class="pasal-title">Pasal 6</div>
        <div class="pasal-sub">HUKUM YANG BERLAKU</div>
        <p>DEBITUR dengan ini menyatakan pengakuan dengan sebenar-benarnya serta menjamin kepada BANK, sebagaimana BANK
            menerima pernyataan dan pengakuan DEBITUR, bahwa:</p>
        <ol style="margin-left: -20px;">
            <li>DEBITUR berhak dan berwenang sepenuhnya untuk menandatangani Perjanjian ini, tidak dalam tekanan atau
                paksaan dari pihak manapun.</li>
            <li>DEBITUR menjamin bahwa segala surat dan dokumen serta akta yang DEBITUR tandatangani dan/atau gunakan
                berkaitan dengan Perjanjian Kredit ini adalah benar, keberadaannya sah, dan tindakan DEBITUR tidak
                melanggar atau bertentangan dengan ketentuan dan/atau hukum yang berlaku.</li>
            <li>DEBITUR adalah Perseorangan yang tunduk pada hukum Negara Republik Indonesia.</li>
            <li>Diadakannya Perjanjian ini dan/atau Addendum Perjanjian ini tidak akan bertentangan dengan suatu
                Perjanjian yang telah ada atau yang akan diadakan oleh DEBITUR dengan pihak ketiga lainnya.</li>
            <li>Sepanjang tidak bertentangan dengan peraturan perundang-undangan yang berlaku, DEBITUR berjanji dan
                dengan ini mengikatkan diri mendahulukan untuk membayar dan melunasi kewajiban DEBITUR kepada BANK dari
                kewajiban lainnya.</li>
            <li>Dalam hal-hal yang berkaitan dengan ayat 1 s/ 5 Pasal ini, DEBITUR berjanji dan dengan ini mengikatkan
                diri untuk membebaskan BANK dari segala tuntutan atau gugatan yang datang dari pihak mana pun dan/atau
                atas alasan apapun.</li>
        </ol>

        <div class="pasal-title">Pasal 7</div>
        <div class="pasal-sub">PENYELESAIAN PERSELISIHAN</div>
        <p>1. Apabila di kemudian hari terjadi perbedaan pendapat atau penafsiran atas hal-hal yang tercantum di
            dalam Perjanjian ini atau terjadi perselisihan atau sengketa dalam pelaksanaan Perjanjian ini, Para Pihak
            sepakat untuk menyelesaikannya secara musyawarah untuk mencapai mufakat.</p>
        <p>2. Apabila kedua belah pihak tidak berhasil menyelesaikan perselisihan secara musyawarah untuk mufakat maka
            kedua belah pihak sepakat untuk menempuh penyelesaian melalui jalur hukum. Untuk penyelesaian melalui
            saluran hukum, kedua belah pihak sepakat memilih tempat kedudukan hukum yang tepat pada Pengadilan Negeri
            Bandung / tempat yang ditunjuk oleh pihak <b>BANK</b></p>

        <div class="pasal-title">Pasal 8</div>
        <div class="pasal-sub">PENGALIHAN HAK DAN KEWAJIBAN</div>
        <p>BANK berhak untuk mengalihkan sebagian atau seluruh hak dan kewajiban yang timbul sehubungan dengan
            Perjanjian Kredit ini berikut setiap perubahan atau pembaharuan kepada pihak lain, tanpa persetujuan
            terlebih dahulu dari DEBITUR.Untuk keperluan tersebut maka DEBITUR dengan ini memberikan Kuasa kepada BANK
            dan apabila dalam pengalihan oleh BANK tersebut masih diperlukan Kuasa dari DEBITUR maka kata demi kata
            Kuasa tersebut telah diberikan oleh DEBITUR dalam Perjanjian Kredit ini.</p>

        <div class="pasal-title">Pasal 9</div>
        <div class="pasal-sub">KETENTUAN PENUTUP</div>
        <ol style="margin-left: -20px;">
            <li>BANK dan DEBITUR dengan ini, sepakat dan setuju untuk melakukan seluruh ketentuan-ketentuan yang diatur
                di dalam Perjanjian ini.</li>
            <li>Sebelum Perjanjian Kredit ini ditandatangani oleh DEBITUR, DEBITUR mengakui dengan sebenarnya, dan tidak
                lain dari yang sebenarnya, bahwa DEBITUR telah membaca dengan cermat atau dibacakan kepadanya seluruh
                isi Perjanjian Kredit ini, sehingga oleh karena itu DEBITUR memahami sepenuhnya segala yang akan menjadi
                akibat hukum setelah DEBITUR menandatangani Perjanjian ini.</li>
            <li>Perjanjian ini mengikat Para Pihak yang sah, para pengganti atau pihak-pihak yang menerima hak dari
                masing-masing Para Pihak.</li>
            <li>Perjanjian ini memuat, dan karenanya menggantikan semua pengertian dan kesepakatan yang telah dicapai
                oleh Para Pihak sebelum ditandatanganinya Perjanjian ini, baik tertulis, lisan maupun elektronik,
                mengenai hal yang sama.</li>
            <li>Jika salah satu atau sebagian ketentuan-ketentuan dalam Perjanjian ini menjadi batal atau tidak berlaku,
                maka tidak mengakibatkan seluruh Perjanjian ini menjadi batal atau tidak berlaku seluruhnya.</li>
            <li>Apabila ada hal-hal yang belum diatur atau belum cukup diatur dalam Perjanjian ini, maka BANK dan
                DEBITUR akan mengaturnya Bersama secara musyawarah untuk mufakat dalam suatu Perjanjian Tambahan
                (Addendum) yang ditandatangani oleh Para Pihak dan merupakan satu kesatuan yang tidak terpisahkan dari
                Perjanjian ini.</li>
            <li>Perjanjian ini telah disesuaikan dengan ketentuan Peraturan Perundang-undangan, termasuk ketentuan
                Peraturan Otoritas Jasa Keuangan.</li>
        </ol>

        <p>Demikian Perjanjian Kredit ini dibuat dan ditandatangani di Bandung oleh BANK dan DEBITUR, yang masing-masing
            disimpan oleh BANK dan DEBITUR, dan masing-masing berlaku sebagai aslinya.</p>

        <div class="ttd-wrapper">
            <table class="ttd-table">
                <tr>
                    <td style="width: 50%;">Bandung, ______________</td>
                    <td style="width: 50%;"></td>
                </tr>
                <tr>
                    <td><b>PT. BPR TATA ASIA,</b></td>
                    <td><b>DEBITUR,</b></td>
                </tr>
                <tr>
                    <td class="ttd-space"></td>
                    <td class="ttd-space">
                        <img src="{{ public_path('assets/images/Picture3.jpg') }}" class="materai-img">
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="u-bold">TB. FRANDY PRIYA PURWANTO, S.H.</span><br>
                        Direktur Utama
                    </td>
                    <td>
                        <span class="u-bold">{{ strtoupper($data['nama_debitur']) }}</span><br>
                        &nbsp;
                    </td>
                </tr>
            </table>
        </div>
    </main>
</body>

</html>
