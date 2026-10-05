<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jadwal Penggunaan Lab</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; color: #333; line-height: 1.4; }
        .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-brand { width: 100%; border-collapse: collapse; margin: 0 0 6px 0; }
        .kop-brand td { border: none; padding: 0; vertical-align: middle; }
        .kop-brand .brand-left, .kop-brand .brand-right { width: 15%; }
        .kop-brand .brand-left { text-align: left; padding-left: 100px; }
        .kop-brand .brand-right { text-align: right; padding-right: 100px; }
        .kop-brand .brand-copy { width: 100%; text-align: center; }
        .kop-brand img { width: 90px; height: 90px; object-fit: contain; }
        .kop-surat h1 { margin: 0; font-size: 16px; text-transform: uppercase; font-weight: bold; }
        .kop-surat p { margin: 2px 0 0 0; font-size: 11px; font-style: italic; }
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h3 { margin: 0; font-size: 14px; text-decoration: underline; }
        .judul p { margin: 5px 0 0 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        th, td { border: 1px solid #000; padding: 5px 8px; text-align: left; font-size: 10px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .ringkasan { margin-bottom: 20px; }
        .ringkasan table { width: auto; }
        .ringkasan td { border: none; padding: 2px 10px 2px 0; }
        .ringkasan td:first-child { font-weight: bold; }
        .ruangan-heading { background-color: #e2e8f0; font-weight: bold; font-size: 11px; padding: 6px 8px; border: 1px solid #000; }
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { float: right; width: 250px; text-align: center; }
        .ttd-box p { margin: 0 0 60px 0; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <table class="kop-brand"><tr>
            <td class="brand-left"><img src="{{ public_path('images/polinela.png') }}" alt="Logo POLINELA"></td>
            <td class="brand-copy">
                <h1>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h1>
                <h1>POLITEKNIK NEGERI LAMPUNG</h1>
                <h1>PROGRAM STUDI TEKNOLOGI REKAYASA PERANGKAT LUNAK</h1>
                <p>Jl. Soekarno Hatta No.10, Rajabasa, Kec. Rajabasa, Kota Bandar Lampung, Lampung 31414</p>
            </td>
            <td class="brand-right"><img src="{{ public_path('images/trpl.png') }}" alt="Logo TRPL"></td>
        </tr></table>
    </div>

    <div class="judul">
        <h3>LAPORAN JADWAL PENGGUNAAN LABORATORIUM</h3>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <div class="ringkasan">
        <table>
            <tr><td>Tahun Ajaran</td><td>: {{ $tahunAjaran->nama_tahun }} ({{ $tahunAjaran->semester }})</td></tr>
            @if($ruanganFilter)
            <tr><td>Ruangan</td><td>: {{ $ruanganFilter->nama_ruangan }}</td></tr>
            @else
            <tr><td>Ruangan</td><td>: Semua Ruangan</td></tr>
            @endif
            <tr><td>Total Jadwal</td><td>: {{ $jadwals->flatten(1)->count() }} jadwal</td></tr>
        </table>
    </div>

    @forelse($jadwals as $namaRuangan => $jadwalRuangan)
    <table>
        <thead>
            <tr><th colspan="5" class="ruangan-heading" style="text-align:left;">{{ $namaRuangan }}</th></tr>
            <tr>
                <th width="6%">No</th>
                <th width="14%">Hari</th>
                <th width="18%">Waktu</th>
                <th width="37%">Mata Kuliah</th>
                <th width="25%">Dosen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwalRuangan as $index => $jadwal)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $jadwal->hari }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</td>
                <td>{{ $jadwal->mata_kuliah }}</td>
                <td>{{ $jadwal->dosen ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @empty
    <table>
        <tbody>
            <tr><td class="text-center">Tidak ada jadwal kuliah untuk Tahun Ajaran dan filter yang dipilih.</td></tr>
        </tbody>
    </table>
    @endforelse

    <div class="ttd-container clearfix">
        <div class="ttd-box">
            <p>Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Kepala Laboratorium TRPL</p>
            <strong><u>Dani Rofianto, S.Mat., M.Kom.</u></strong><br>
            NIP. 199311262022031005
        </div>
    </div>

</body>
</html>
