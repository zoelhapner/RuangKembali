<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
{{-- <title>Penawaran {{ $offer->offer_number }}</title> --}}

<style>
@page {
    margin: 140px 30px 110px 30px;
}

/* ================= BODY ================= */
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    line-height: 1.5;
    margin: 0;
    padding: 0;
}

/* ================= HEADER & FOOTER ================= */
.header {
    position: fixed;
    top: -110px;
    left: 0;
    right: 0;
    width: 100%;
}

.footer {
    position: fixed;
    bottom: -70px;
    left: 0;
    right: 0;
    width: 100%;
}
    .title {
        text-align: center;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 20px;
    }
    .kop {
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 5px;
    }
    .subkop {
        text-align: center;
        font-size: 14px;
        margin-bottom: 25px;
        line-height: 1.6;
    }
    .subkop .lokasi {
        margin-bottom: 5px;
    }

.subkop .antara,
.subkop .dan {
    margin: 5px 0;
}

.subkop .pihak {
    margin: 5px 0;
    font-weight: bold;
}
.garis {
    display: block;
    border-bottom: 1px solid #000;
    padding-bottom: 6px;
    width: 100%;
}

    .section-title {
        font-weight: bold;
        margin-top: 25px;
        margin-bottom: 10px;
        text-align: center;
        text-transform: uppercase;
    }
    table.ttd {
        width: 100%;
        margin-top: 40px;
        text-align: center;
    }
    .no-border td {
    border: none !important;
    padding: 4px 0;
}
</style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('images/header-penawaran.jpg') }}" style="width:100%;">
</div>

<!-- ================= FOOTER ================= -->
<div class="footer">
    <img src="{{ public_path('images/footer-penawaran.jpg') }}" style="width:100%;">
</div>
<div class="kop">
    <u>KONTRAK</u><br>
    PELAKSANAAN PEKERJAAN {{ strtoupper($offer->project->project_name ?? '-') }}
</div>

<div class="subkop">
    <div class="lokasi">
        {{ strtoupper($offer->project->project_location ?? '-') }},
        {{ strtoupper($offer->project->city->name ?? '-') }},
        {{ strtoupper($offer->project->province->name ?? '-') }}
    </div>
    <div class="antara">
            antara
    </div>
    <div class="pihak">
            {{ strtoupper($offer->project->customer->display_name_with_title) }}

    </div>
    <div class="dan">
            dengan
    </div>
    <div class="pihak garis">
            Ir. Ar. Dwiantosa Ahmad F., IAI., IPP
    </div>
</div>

<table class="no-border" width="100%">
    <tr>
        <!-- KIRI -->
        <td width="60%">
            <table class="no-border">
                <tr>
                    <td width="30%">Nomor</td>
                    <td>: {{ $offer->contract_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: {{ \Carbon\Carbon::parse($offer->contract_date ?? now())->translatedFormat('d F Y') }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p>
    Pada hari ini, {{ $hari }}, tanggal {{ $tanggal_terbilang }},
    bulan {{ $bulan }}, tahun {{ $tahun_terbilang }},
    kami yang bertanda tangan di bawah ini:
</p>


<table>
<tr><td width="120">Nama</td><td>: {{ $offer->project->customer->display_name_with_title }}</td></tr>
<tr><td>NIK</td><td>: {{ $project->customer->user->identity_number ?? '..................' }}</td></tr>
<tr><td>Alamat</td><td>: {{ $project->customer->user->address }}</td></tr>
<tr><td>Telepon</td><td>: {{ $project->customer->user->phone }}</td></tr>
</table>

<p >
Dalam hal ini bertindak sebagai Pemilik Rumah dan Pemberi Tugas,
selanjutnya disebut sebagai <strong>Pihak Pertama</strong>.
</p>


<table>
<tr><td width="120">Nama</td><td>: Ir. Ar. DWIANTOSA AHMAD FATHONY, IAI., IPP.</td></tr>
<tr><td>NIK</td><td>: 3509190306920005</td></tr>
<tr><td>Alamat</td><td>: JL. Kertajaya II / 155, Jember Kidul, Jember</td></tr>
<tr><td>Telepon</td><td>: 0852 3687 3007</td></tr>
</table>

<p >
Dalam hal ini bertindak sebagai Pelaksana Pekerjaan Desain Rumah Hunian,
selanjutnya disebut sebagai <strong>Pihak Kedua</strong>.
</p>

<p >
Kedua belah pihak telah sepakat untuk mengadakan ikatan Kontrak Pelaksanaan Pekerjaan
<strong>{{ strtoupper($offer->project->project_name ?? '-') }}</strong> yang terletak di
{{ $offer->project->project_location ?? '.............' }}.
</p>

<p>
<strong>Pihak Pertama</strong> bersedia membayar seluruh biaya pelaksanaan pekerjaan, 
sedangkan <strong>Pihak Kedua</strong> bersedia untuk melaksanakan pekerjaan {{ strtoupper($offer->project->project_name ?? '-') }} 
tersebut sesuai dengan data dan kebutuhan yang telah disepakati kedua belah pihak. 
Dengan ketentuan yang disebutkan dalam pasal-pasal sebagai berikut :
</p>
<!-- PASAL 1 -->
<p class="section-title text-center">
    Pasal 1<br>
    Tujuan Kontrak
</p>

<p >
Tujuan kontrak ini adalah sebagai ikatan kesepakatan kedua belah pihak yang tertuang dalam dokumen kontrak,
sebagai dasar dalam Pelaksanaan Pekerjaan {{ strtoupper($offer->project->project_name) }} 
<strong>{{ strtoupper($offer->project->customer->display_name_with_title) }}</strong>
yang berlokasi di
<strong>{{ $offer->project->project_location }}</strong>
{{ $offer->project->city->name }}, {{ $offer->project->province->name }}.
</p>


{{-- PASAL 2 --}}
<p class="section-title text-center">
    Pasal 2<br>
    Bentuk Pekerjaan
</p>

<p >
Bentuk pekerjaan yang akan dilaksanakan oleh <strong>Pihak Kedua</strong>
adalah sebagai berikut:
</p>

<ol>
    <li >
        Pihak Pertama dalam kedudukannya seperti tersebut di atas memberi tugas
        kepada Pihak Kedua dan selanjutnya Pihak Kedua menerima tugas tersebut
        untuk melaksanakan pekerjaan-pekerjaan tersebut di bawah ini:
    </li>

    <li >
        Rincian Tugas Perencanaan Desain Arsitektur adalah sebagai berikut:
    </li>
</ol>

{{-- RINCIAN DINAMIS DARI DESIGN PACKAGE --}}
<ol type="a" style="margin-left: 30px;">

@foreach($designItems as $category => $items)
    <li>
        <strong>{{ $category }}</strong>
        <ol style="margin-top:6px; margin-left:20px;" type="1">
            @foreach($items as $item)
                <li>{{ $item->item_name }}</li>
            @endforeach
        </ol>
    </li>
@endforeach

</ol>


<!-- PASAL 3 -->
<p class="section-title text-center">
    Pasal 3<br>
    Sistem Pekerjaan
</p>
<p >
<p>Sistem pekerjaan yang disepakati oleh kedua belah pihak adalah sebagai berikut :</p>

<ol>
    <li><strong>Pihak Pertama</strong> menggunakan sistem penunjukan langsung dengan menyediakan anggaran biaya yang diperlukan.</li>
    <li><strong>Pihak Pertama</strong> menyetujui hasil Surat Penawaran Jasa Desain yang telah dibuat oleh <strong>Pihak Kedua</strong>.</li>
    <li><strong>Pihak Kedua</strong> dalam melaksanakan Pekerjaan Desain wajib memenuhi ketentuan berupa Analisa lahan, kebutuhan ruang <strong>Pihak Pertama</strong>, 
    referensi desain yang diinginkan oleh <strong>Pihak Pertama</strong> dan spesifikasi bahan material yang telah disepakati bersama.</li>
</ol>
</p>

<!-- PASAL 4 – BIAYA -->
<p class="section-title text-center">
    Pasal 4<br>
    Biaya
</p>

<p >
Biaya Pelaksanaan Pekerjaan {{ strtoupper($offer->project->project_name) }}  
{{ strtoupper($offer->project->customer->display_name_with_title) }} yang telah disepakati kedua belah pihak adalah senilai
<b>Rp {{ number_format($offer->grand_total, 0, ',', '.') }}</b>
({{ terbilang($offer->grand_total) }} rupiah).
<p>
Anggaran Biaya tesebut di atas akan digunakan untuk membiayai Pelaksanaan Pekerjaan 
{{ strtoupper($offer->project->project_name) }} 
sesuai desain gambar dan spesifikasi yang telah ditetapkan.
</p>
</p>
<p><strong>RAB tidak termasuk : </strong></p>

<ol>
    <li>Pajak – pajak yang di timbulkan atas pelaksanaan desain rumah termasuk, pajak pribadi, pajak membangun sendiri dan lain-lain.</li>
    <li>Persetujuan Bangunan Gedung  ( PBG ) mulai dari, lurah / kepala desa, camat dan pihak ciptakarya {{ $offer->project->city->name }}.</li>
</ol>

<!-- PASAL 5 – PEMBAYARAN -->
<p class="section-title text-center">
    Pasal 5<br>
    Sistem Pembayaran
</p>

<p>
Pembayaran atas pekerjaan desain dilakukan dengan sistem bertahap yaitu:
</p>

<p class="indent">
<strong>Tahap I</strong> : Pembayaran Uang Muka sebesar
<strong>70%</strong> x <strong>Rp {{ number_format($offer->grand_total, 0, ',', '.') }}</strong>
= <strong>Rp {{ number_format($offer->grand_total * 0.7, 0, ',', '.') }}</strong>
( {{ ucwords(terbilang($offer->grand_total * 0.7)) }} Rupiah )
yang dibayarkan pada saat pekerjaan akan dimulai
(setelah kontrak ditandatangani).
</p>

<p class="indent">
<strong>Tahap II</strong> : Pembayaran Pelunasan sebesar
<strong>30%</strong> x <strong>Rp {{ number_format($offer->grand_total, 0, ',', '.') }}</strong>
= <strong>Rp {{ number_format($offer->grand_total * 0.3, 0, ',', '.') }}</strong>
( {{ ucwords(terbilang($offer->grand_total * 0.3)) }} Rupiah )
yang dibayarkan setelah pekerjaan desain telah selesai
<strong>100%</strong>.
</p>

<p class="indent">
Pelunasan dibayarkan pada saat file digital dan hardcopy desain
telah diterima oleh <strong>Pihak Pertama</strong>.
</p>

<p>
<strong>Pembayaran dilakukan melalui transfer ke rekening: </strong>
</p>

<table class="no-border" style="margin-left:30px;">
    <tr>
        <td width="120">Penerima</td>
        <td>: <strong>DWIANTOSA AHMAD FATHONY</strong></td>
    </tr>
    <tr>
        <td>Bank</td>
        <td>: BANK BCA</td>
    </tr>
    <tr>
        <td>No. Rekening</td>
        <td>: 024 157 5429</td>
    </tr>
</table>


<!-- PASAL 6 – WAKTU PELAKSANAAN -->
<p class="section-title text-center">
    Pasal 6<br>
    Jangka Waktu Pelaksanaan
</p>

<p>
Jangka waktu pelaksanaan pekerjaan desain rumah hunian {{ strtoupper($offer->project->customer->display_name_with_title) }} estimasi maksimal adalah 60( enam puluh ) hari kerja, 
terhitung sejak disepakati awal mulai pekerjaan yaitu pada tanggal {{ \Carbon\Carbon::parse($project->start_date ?? now())->translatedFormat('d F Y') }} dan estimasi selesai pada 
tanggal {{ \Carbon\Carbon::parse($project->end_date ?? now())->translatedFormat('d F Y') }}.
</p>
<p>
Apabila terjadi keterlambatan pengerjaan dari waktu yang telah ditentukan, maka <strong>Pihak Kedua</strong> wajib membayar denda kepada <strong>Pihak Pertama</strong> sebesar 0,5 per mil dari nilai kontrak untuk setiap hari 
keterlambatan atau Rp. 12.500 ( dua belas ribu lima ratus rupiah ) perhari. Maksimum 0,1% atau Rp. 250.000 ( dua ratus lima puluh ribu rupiah ).
</p>
<p>
Sedangkan apabila terjadi keterlambatan pembayaran <strong>Pihak Pertama</strong> terhadap termin atau tahapan pembayaran yang diajukan oleh <strong>Pihak Kedua</strong>, 
maka <strong>Pihak Pertama</strong> wajib membayar denda pada <strong>Pihak Kedua</strong> sebesar 0.5% dari besarnya jumlah termin yang diajukan untuk setiap hari keterlambatan, 
terhitung 3 ( tiga ) hari sejak tanggal tanda terima pengajuan berkas termin. Maksimum 1% dari jumlah termin yang diajukan.
</p>

<!-- PASAL 7 – REVISI -->
<p class="section-title text-center">
    Pasal 7<br>
    Revisi
</p>

<ol>
    <li>
        <strong>Jumlah Revisi</strong>
        <ul>
            <li><strong>Pihak Pertama</strong> mendapatkan 3 (tiga) kali revisi mayor untuk desain
                (denah, tampak, dan visualisasi 3D).</li>
            <li>Revisi mayor adalah perubahan yang mempengaruhi layout, luasan ruang, struktur utama, fasad utama.
            </li>
            <li>Revisi minor unlimited selama masih dalam tahap draft desain (belum final).</li>
        </ul>
    </li>

    <li>
        Batasan Revisi
        <ul>
            <li>Revisi tidak mengubah brief awal secara total, misalnya:
                <ul>
                    <li>Mengganti jumlah lantai setelah desain jadi.</li>
                    <li>Mengubah konsep desain secara drastis (misalnya dari minimalis menjadi klasik).</li>
                </ul>
            </li>
            <li>Jika <strong>Pihak Pertama</strong> meminta perubahan besar yang keluar dari kesepakatan awal,
                maka:
                <ul>
                    <li>Dihitung sebagai desain baru,</li>
                    <li>Ada biaya tambahan sesuai kesepakatan.</li>
                </ul>
            </li>
        </ul>
    </li>

    <li>
        Waktu Revisi
        <ul>
            <li>Lama proses revisi : 2 – 5 hari kerja tergantung Tingkat kompleksitas..</li>
            <li>Perubahan besar yang merubah modeling memerlukan waktu lebih lama.</li>
        </ul>
    </li>

    <li>
        Mekanisme Revisi
        <ul>
            <li><strong>Pihak Pertama</strong> menyampaikan revisi secara tertulis (WA).</li>
            <li><strong>Pihak Kedua</strong> akan mengonfirmasi revisi yang disetujui beserta estimasi waktu pengerjaan.</li>
            <li>Revisi dikerjakan sesuai antrian pekerjaan dan diserahkan dalam bentuk gambar update softcopy.</li>
        </ul>
    </li>

    <li>
        Revisi setelah Gambar Kerja / Detail Engineering Design
        <ul>
            <li>Revisi setelah gambar kerja selesai hanya dapat dilakukan untuk
                kesalahan teknis yang berasal dari <strong>Pihak Kedua</strong>.</li>
            <li>Apabila perubahan berasal dari <strong>Pihak Pertama</strong> saat gambar kerja sudah jadi,
                maka akan dikenakan biaya tambahan revisi gambar kerja sesuai item yang diubah.</li>
        </ul>
    </li>

    <li>
        Revisi saat Proyek Pembangunan Berjalan
        <ul>
            <li>Desain yang telah disetujui menjadi acuan pelaksanaan pembangunan.</li>
            <li>Perubahan di lapangan atas permintaan <strong>Pihak Pertama</strong> dapat mengakibatkan:
                <ul>
                    <li>Penambahan biaya pekerjaan gambar revisi,</li>
                    <li>Penyesuaian jadwal pengerjaan.</li>
                </ul>
            </li>
        </ul>
    </li>

    <li>
        Ketentuan Komunikasi
        <ul>
            <li>Semua revisi harus didiskusikan di grup proyek ( optional ).</li>
            <li><strong>Pihak Pertama</strong> wajib memberikan feedback dalam waktu maksimal 3 hari agar proses tidak tertunda.</li>
        </ul>
    </li>
</ol>

<!-- PASAL 8 – LAIN-LAIN -->
<p class="section-title text-center">
    Pasal 8<br>
    Lain - Lain
</p>

<p>
<strong>Pihak Pertama</strong> dan <strong>Pihak Kedua</strong> akan bersama- sama mematuhi dengan baik dan bertanggung jawab terhadap seluruh kesepakatan kerja yang telah disepakati.
</p>
<p>
Namun apabila terjadi Force Majeure atau bencana alam yang diluar kemampuan Para Pihak, maka hal tersebut bukan menjadi tanggungan Para Pihak.
</p>
<p>
    Demikian Kontrak Kerja ini telah di sepakati dan di tanda tangani diatas materai yang cukup, untuk dilaksanakan dengan penuh rasa tanggung jawab tanpa adanya campur tangan dari pihak lain. 
</p>

<table width="100%" style="margin-top:20px;">
<tr>

    <td width="50%" style="text-align:center; vertical-align:top;">Pemilik Rumah
        {{-- <strong>Pemilik Rumah</strong> --}}

        <div style="height:140px;">
            <!-- ruang tanda tangan -->
        </div>

        <u>{{ strtoupper($offer->project->customer->display_name_with_title) }}</u>
    </td>

    <td width="50%" style="text-align:center; vertical-align:top;">PT. Tosa Ahmad Jaya<br>
        <strong>Antosa Architect</strong>

        <div style="height:120px;">
            @if($offer->approved_at)
                <img src="{{ public_path('images/ttd-dwiantosa.png') }}"
                     style="height:140px;">
            @endif
        </div>

        <u>Ir. Ar. Dwiantosa Ahmad F., IAI., IPP</u>
    </td>

</tr>
</table>

</body>
</html>