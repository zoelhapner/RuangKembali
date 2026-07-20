<style>
    body {
        font-family: "Times New Roman", serif;
        font-size: 14px;
        line-height: 1.5;
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
    }
    .section-title {
        font-weight: bold;
        margin-top: 25px;
        margin-bottom: 10px;
        text-transform: uppercase;
    }
    .indent {
        text-indent: 30px;
        text-align: justify;
    }
    table.ttd {
        width: 100%;
        margin-top: 40px;
        text-align: center;
    }
</style>

<div class="kop">
    <u>KONTRAK</u><br>
    {{ strtoupper($offer->project->project_name ?? '-') }}
</div>

<div class="subkop">
    {{ strtoupper($offer->project->project_location ?? '-') }}
        {{ strtoupper($offer->project->city->name ?? '-') }}
            {{ strtoupper($offer->project->province->name ?? '-') }}
            antara
            <u>{{ strtoupper($offer->contact_name) }}</u>
            dengan
            <u>(Ir. Ar. Dwiantosa Ahmad F., IAI., IPP)</u>

</div>

<p class="indent">
    Nomor &nbsp;&nbsp;: {{ $offer->contract_number ?? '086.TAJ / SPK / XI / 2025' }} <br>
    Tanggal : {{ \Carbon\Carbon::parse($offer->contract_date ?? now())->translatedFormat('d F Y') }}
</p>


<p class="indent">
    Pada hari ini, {{ $hari }}, tanggal {{ $tanggal_terbilang }},
    bulan {{ $bulan }}, tahun {{ $tahun_terbilang }},
    kami yang bertanda tangan di bawah ini:
</p>

<!-- PIHAK PERTAMA -->
<p class="section-title">PIHAK PERTAMA</p>

<table>
<tr><td width="120">Nama</td><td>: {{ $offer->contact_name }}</td></tr>
<tr><td>NIK</td><td>: {{ $project->customer->user->nik ?? '..................' }}</td></tr>
<tr><td>Alamat</td><td>: {{ $project->customer->user->address }}</td></tr>
<tr><td>Telepon</td><td>: {{ $project->customer->user->phone }}</td></tr>
</table>

<p class="indent">
Dalam hal ini bertindak sebagai Pemilik Rumah dan Pemberi Tugas,
selanjutnya disebut sebagai **Pihak Pertama**.
</p>

<!-- PIHAK KEDUA -->
<p class="section-title">PIHAK KEDUA</p>

<table>
<tr><td width="120">Nama</td><td>: Ir. Ar. DWIANTOSA AHMAD FATHONY, IAI., IPP.</td></tr>
<tr><td>NIK</td><td>: 3509190306920005</td></tr>
<tr><td>Alamat</td><td>: JL. Kertajaya II / 155, Jember Kidul, Jember</td></tr>
<tr><td>Telepon</td><td>: 0852 3687 3007</td></tr>
</table>

<p class="indent">
Dalam hal ini bertindak sebagai Pelaksana Pekerjaan Desain Rumah Hunian,
selanjutnya disebut sebagai **Pihak Kedua**.
</p>

<p class="indent">
Kedua belah pihak telah sepakat mengadakan Kontrak Pelaksanaan Pekerjaan
Desain Rumah Hunian yang berlokasi di
{{ $offer->project->project_location ?? '.............' }}.
</p>

<!-- PASAL 1 -->
<p class="section-title">Pasal 1 – Tujuan Kontrak</p>
<p class="indent">
Tujuan kontrak ini adalah sebagai ikatan kesepakatan kedua belah pihak 
sebagai dasar Pelaksanaan Pekerjaan Desain Rumah Hunian
{{ strtoupper($offer->contact_name) }} di {{ $offer->project->project_location }}.
Tujuan kontrak ini adalah sebagai ikatan kesepakatan kedua belah pihak sebagai dasar dalam Pelaksanaan Pekerjaan Desain Rumah Hunian Ibu Dian ……………. Kabupaten Bondowoso, Jawa Timur.
</p>

<!-- PASAL 2 -->
<p class="section-title">Pasal 2 – Bentuk Pekerjaan</p>
<p class="indent">
Pihak Kedua melaksanakan pekerjaan sebagai berikut:
</p>
<ol>
    <li>Pembuatan Desain Denah Rumah Hunian</li>
    <li>Pembuatan Fasade, 3D Model dan Render</li>
    <li>Pembuatan Gambar Kerja / Detail Engineering Design (DED):
        <ul>
            <li>Arsitektur</li>
            <li>Struktur</li>
            <li>MEP</li>
        </ul>
    </li>
</ol>

<!-- PASAL 3 -->
<p class="section-title">Pasal 3 – Sistem Pekerjaan</p>
<p class="indent">
Sistem pekerjaan mengacu pada Surat Penawaran Jasa Desain yang telah disetujui kedua belah pihak…
</p>

<!-- PASAL 4 – BIAYA -->
<p class="section-title">Pasal 4 – Biaya</p>
<p class="indent">
Biaya total pekerjaan desain adalah sebesar:
<b>Rp {{ number_format($offer->grand_total, 0, ',', '.') }}</b>
({{ terbilang($offer->grand_total) }} rupiah).
</p>

<p>RAB tidak termasuk:</p>
<ul>
    <li>Pajak-pajak atas pelaksanaan desain</li>
    <li>Perizinan PBG</li>
</ul>

<!-- PASAL 5 – PEMBAYARAN -->
<p class="section-title">Pasal 5 – Sistem Pembayaran</p>

<p>Term pembayaran:</p>
<ol>
    <li>DP 70% = Rp {{ number_format($offer->grand_total * 0.7, 0, ',', '.') }}</li>
    <li>Pelunasan 30% = Rp {{ number_format($offer->grand_total * 0.3, 0, ',', '.') }}</li>
</ol>

<p>Pembayaran ditransfer ke rekening:</p>
<ul>
    <li><b>DWIANTOSA AHMAD FATHONY</b></li>
    <li>BANK BCA</li>
    <li>No. Rek: 024 157 5429</li>
</ul>

<!-- PASAL 6 – WAKTU PELAKSANAAN -->
<p class="section-title">Pasal 6 – Jangka Waktu Pelaksanaan</p>
<p class="indent">
Pekerjaan desain diselesaikan maksimal **60 hari kerja**.
</p>

<!-- PASAL 7 – REVISI -->
<p class="section-title">Pasal 7 – Revisi</p>
<p class="indent">
Pihak Pertama mendapatkan 3x revisi mayor dan revisi minor unlimited selama tahap draft desain.
</p>

<!-- PASAL 8 – LAIN-LAIN -->
<p class="section-title">Pasal 8 – Lain-lain</p>
<p class="indent">
Jika terjadi Force Majeure maka bukan tanggung jawab kedua belah pihak.
</p>

<!-- TANDA TANGAN -->
<table class="ttd">
<tr>
    <td>Pihak Pertama<br><br><br><br><br><u>{{ strtoupper($offer->contact_name) }}</u></td>
    <td>Pihak Kedua<br><br><br><br><br><u>(Ir. Ar. Dwiantosa Ahmad F., IAI., IPP)</u></td>
</tr>
</table>
