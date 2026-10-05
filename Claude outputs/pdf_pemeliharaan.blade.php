<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemeliharaan Laboratorium</title>
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
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px 8px; text-align: left; font-size: 10px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .ringkasan { margin-bottom: 20px; }
        .ringkasan table { width: auto; }
        .ringkasan td { border: none; padding: 2px 10px 2px 0; }
        .ringkasan td:first-child { font-weight: bold; }
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
        <h3>LAPORAN PEMELIHARAAN LABORATORIUM</h3>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <div class="ringkasan">
        <table>
            <tr><td>Periode</td><td>: {{ \Carbon\Carbon::parse($mulai)->translatedFormat('d F Y') }} &ndash; {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</td></tr>
            <tr><td>Total Riwayat</td><td>: {{ $pemeliharaans->count() }} kejadian</td></tr>
            <tr><td>Total Biaya</td><td>: Rp {{ number_format($pemeliharaans->sum('biaya'), 0, ',', '.') }}</td></tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="9%">Tanggal</th>
                <th width="7%">Jenis Aset</th>
                <th width="18%">Nama Aset</th>
                <th width="13%">Jenis Perbaikan</th>
                <th width="14%">Kondisi (Sebelum &rarr; Sesudah)</th>
                <th width="10%">Biaya</th>
                <th width="11%">Teknisi</th>
                <th width="14%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemeliharaans as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $p->tanggal_pemeliharaan->translatedFormat('d M Y') }}</td>
                <td class="text-center">{{ $p->jenis_aset == 'barang' ? 'Barang' : 'Ruangan' }}</td>
                <td>{{ $p->nama_aset_snapshot }}</td>
                <td>{{ $p->jenis_perbaikan }}</td>
                <td class="text-center">{{ $p->kondisi_sebelum ?? '-' }} &rarr; {{ $p->kondisi_sesudah ?? '-' }}</td>
                <td class="text-center">{{ $p->biaya ? 'Rp ' . number_format($p->biaya, 0, ',', '.') : '-' }}</td>
                <td>{{ $p->teknisi->name ?? '-' }}</td>
                <td>{{ $p->catatan ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">Tidak ada riwayat pemeliharaan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-container clearfix">
        <div class="ttd-box">
            <p>Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Kepala Laboratorium TRPL</p>
            <strong><u>Dani Rofianto, S.Mat., M.Kom.</u></strong><br>
            NIP. 199311262022031005
        </div>
    </div>

</body>
</html>
