<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Draft Kontrakk {{ $project->project_name }}</title>

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
Dalam hal ini bertindak sebagai Pelaksana Pekerjaan Desain Rumah Hunian milik {{ $offer->project->customer->display_name_with_title }} di {{ strtoupper($offer->project->city?->name ?? '-') }}
 dan selanjutnya disebut sebagai <strong>Pihak Kedua</strong>.
</p>

<p >
Kedua belah pihak telah sepakat untuk mengadakan ikatan Kontrak PELAKSANAAN PEKERJAAN
<strong>{{ strtoupper($offer->project->project_name ?? '-') }}</strong> yang terletak di
{{ $offer->project->project_location ?? '.............' }}
{{ $offer->project->subDistrict->name }}, {{ $offer->project->district->name }}, {{ $offer->project->city->name }}, {{ $offer->project->province->name }}.
</p>

<p>
<strong>Pihak Pertama</strong> bersedia membayar seluruh biaya pelaksanaan pekerjaan, 
sedangkan <strong>Pihak Kedua</strong> bersedia untuk melaksanakan pekerjaan {{ strtoupper($offer->project->project_name ?? '-') }} 
tersebut sesuai dengan  gambar dan perencanaan teknik yang telah disepakati kedua belah pihak. 
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
yang terletak di
<strong>{{ $offer->project->project_location }}</strong>
{{ $offer->project->subDistrict->name }}, {{ $offer->project->district->name }}, {{ $offer->project->city->name }}, {{ $offer->project->province->name }}.
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
        Pekerjaan Perencanaan dan Pelaksanaan {{ strtoupper($offer->project->project_name) }}.
    </li>

    <li >
        Perencanaan berupa desain, gambar, spesifikasi dan RAB yang dilaksanakan sesuai dengan
        permintaan Pihak Pertama.
    </li>
    <li >
        Perencanaan berupa desain, gambar, spesifikasi dan RAB yang dilaksanakan sesuai dengan
        permintaan Pihak Pertama.
    </li>
    <li >
        Pelaksanaan Pembangunan Homestay harus sesuai dengan : desain, gambar, spesifikasi serta
        jangka waktu pelaksanaan yang telah disepakati oleh kedua belah pihak.
    </li>

    <li >
        Adapun Rincian Anggaran Biaya yang dilaksanakan adalah sebagai berikut:
    </li>
</ol>

<ol type="A" style="margin-left: 30px;">

@foreach($categories as $category)

    <li>

        <strong>
            {{ preg_replace('/^HARGA SATUAN\s*/i', '', $category->name) }}
        </strong>

        <ol type="1" style="margin-top:6px; margin-left:20px;">

            @foreach($category->uraians as $uraian)

                <li>

                    <strong>{{ $uraian->name }}</strong>

                    <ol type="a" style="margin-left:20px; margin-top:4px;">

                        @foreach($uraian->items as $item)

                            <li>
                                {{ $item->job_name }}
                            </li>

                        @endforeach

                    </ol>

                </li>

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
    <li><strong>Pihak Pertama</strong> menyetujui hasil Rincian Anggaran Biaya (RAB) yang telah dibuat oleh <strong>Pihak Kedua</strong>.</li>
    <li><strong>Pihak Kedua</strong> dalam melaksanakan Pekerjaan {{ strtoupper($offer->project->project_name ?? '-') }} wajib memenuhi
            ketentuan berupa desain gambar dan spesifikasi yang telah ditentukan, serta menyesuaikan
            dengan kondisi dan situasi dilokasi.</li>
    <li>Apabila terjadi kecelakaan kerja atau sakit pada para pekerja didalam lingkup Pekerjaan {{ strtoupper($offer->project->project_name ?? '-') }}, 
        biaya yang ditimbulkan sepenuhnya menjadi tanggung jawab <strong>Pihak Kedua</strong>.</li>
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
    <li>Pajak – pajak yang di timbulkan atas pelaksanaan {{ strtoupper($offer->project->project_name) }} termasuk pajak pelaksanaan, pajak pribadi, pajak membangun sendiri dan lain-lain.</li>
    <li>Persetujuan Bangunan Gedung  ( PBG ) mulai dari, lurah / kepala desa, camat dan pihak ciptakarya {{ $offer->project->city->name }}.</li>
</ol>

<!-- PASAL 5 – PEMBAYARAN -->
<p class="section-title text-center">
    Pasal 5<br>
    Sistem Pembayaran
</p>

<p>
Pembayaran atas pekerjaan pembangunan dilakukan dengan sistem bertahap yaitu:
</p>

<p class="indent">
Tahap I : Pembayaran Uang Muka = 30% x <strong>Rp {{ number_format($offer->grand_total, 0, ',', '.') }}</strong>
= <strong>Rp {{ number_format($offer->grand_total * 0.3, 0, ',', '.') }}</strong>
( {{ ucwords(terbilang($offer->grand_total * 0.3)) }} Rupiah )
Setelah kontrak di tanda tangani, progress pekerjaan mulai
berjalan.
</p>

<p class="indent">
Tahap II : Pembayaran 30% x <strong>Rp {{ number_format($offer->grand_total, 0, ',', '.') }}</strong>
= <strong>Rp {{ number_format($offer->grand_total * 0.3, 0, ',', '.') }}</strong>
( {{ ucwords(terbilang($offer->grand_total * 0.3)) }} Rupiah )
Ketika progress pekerjaan telah mencapai 60 %
</p>

<p class="indent">
Tahap III : Pembayaran 30% x <strong>Rp {{ number_format($offer->grand_total, 0, ',', '.') }}</strong>
= <strong>Rp {{ number_format($offer->grand_total * 0.3, 0, ',', '.') }}</strong>
( {{ ucwords(terbilang($offer->grand_total * 0.3)) }} Rupiah )
Ketika progress pekerjaan telah mencapai 90 %
</p>

<p class="indent">
Tahap IV : Pembayaran 10% x <strong>Rp {{ number_format($offer->grand_total, 0, ',', '.') }}</strong>
= <strong>Rp {{ number_format($offer->grand_total * 0.1, 0, ',', '.') }}</strong>
( {{ ucwords(terbilang($offer->grand_total * 0.1)) }} Rupiah )
Ketika progress pekerjaan telah mencapai 100 %
</p>

<p class="indent">
Pelunasan dibayarkan pada saat Berita Acara Serah Terima Pekerjaan ditandatangani
kedua belah pihak dan <strong>Pihak Pertama</strong> menyatakan bisa menerima dengan baik
Pekerjaan {{ strtoupper($offer->project->project_name) }} tersebut.
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
        <td>: 0241575429</td>
    </tr>
</table>


<!-- PASAL 6 – WAKTU PELAKSANAAN -->
<p class="section-title text-center">
    Pasal 6<br>
    Jangka Waktu Pelaksanaan
</p>

<p>
Jangka waktu pelaksanaan pekerjaan {{ strtoupper($offer->project->project_name) }}
estimasi maksimal adalah <strong>{{ $job_duration }}</strong> hari kerja
( {{ ucwords($job_duration_text) }} hari ),
terhitung sejak disepakati awal mulai pekerjaan yaitu pada tanggal
{{ \Carbon\Carbon::parse($project->start_date ?? now())->translatedFormat('d F Y') }}
dan estimasi selesai pada
{{ \Carbon\Carbon::parse($project->end_date ?? now())->translatedFormat('d F Y') }}.
</p>

<p>
Apabila terjadi keterlambatan pengerjaan dari waktu yang telah ditentukan, maka <strong>Pihak Kedua</strong> wajib membayar denda kepada <strong>Pihak Pertama</strong> sebesar 0,05 per mil dari nilai kontrak untuk setiap hari 
keterlambatan atau Rp. 1.000.000 ( satu juta rupiah ) perhari. Maksimum 0,2% atau Rp. 4.000.000 ( empat juta rupiah ).
</p>
<p>
Sedangkan apabila terjadi keterlambatan pembayaran <strong>Pihak Pertama</strong> terhadap termin atau tahapan pembayaran yang diajukan oleh <strong>Pihak Kedua</strong>, 
maka <strong>Pihak Pertama</strong> wajib membayar denda pada <strong>Pihak Kedua</strong> sebesar 0.05% dari besarnya jumlah termin yang diajukan untuk setiap hari keterlambatan, 
terhitung 3 ( Tiga ) hari sejak tanggal tanda terima pengajuan berkas termin. Maksimum 0,2% dari jumlah termin yang diajukan.
</p>

<!-- PASAL 7 – REVISI -->
<p class="section-title text-center">
    Pasal 7<br>
    Perubahan
</p>

<p>
Apabila pada waktu pengerjaan pelaksanaan konstruksi terdapat perubahan-perubahan terhadap
luasan, posisi dan bentuk serta penambahan material bangunan diluar dari perjanjian yang telah
disepakati oleh kedua belah pihak, maka <strong>Pihak Pertama</strong> wajib membayar setiap perubahan
pembongkaran dan pemasangan kembali yang besarnya disesuaikan dengan kondisi dan situasi yang
ada, serta harus dilakukan negosiasi terlebih dahulu oleh kedua belah pihak.
</p>
<p>
Sebaliknya apabila terjadi perubahan pengurangan volume bangunan atau pengurangan material
maka, dapat dilakukan addendum kontrak atau dikompensasi pekerjaan lain yang disepakati oleh
kedua belah pihak.
</p>

<p class="section-title text-center">
    Pasal 8<br>
    Masa Pemeliharaan
</p>

<p >
    <ol>
        <li>Masa pemeliharaan berlaku selama 90 ( sembilan puluh ) hari, setelah selesai pekerjaan/serah
            terima hasil pekerjaan yang diikuti dengan penandatanganan berita acara penyerahan bangunan.</li>
        <li>Apabila dalam masa pemeliharaan tersebut terdapat kerusakan yang disebabkan bukan dari
            pekerjaan <strong>Pihak Kedua</strong>, maka <strong>Pihak Pertama</strong> tidak berhak menuntut <strong>Pihak Kedua</strong> untuk
            mengerjakannya.</li>
    </ol>
</p>
<p class="indent">
Namun, <strong>Pihak Kedua</strong> dapat memperbaiki kerusakan tersebut sesuai dengan formulir perubahan
dengan biaya yang ditanggung oleh <strong>Pihak Pertama</strong> sebesar Rp. 50.000/m2 ( tidak termasuk
biaya material yang diganti/rusak).
</p>
<!-- PASAL 8 – LAIN-LAIN -->
<p class="section-title text-center">
    Pasal 9<br>
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