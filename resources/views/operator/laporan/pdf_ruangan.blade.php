<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Ruangan</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; color: #333; line-height: 1.4; }
        .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { margin: 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        .kop-surat h2 { margin: 0; font-size: 16px; font-weight: normal; }
        .kop-surat p { margin: 2px 0 0 0; font-size: 11px; font-style: italic; }
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h3 { margin: 0; font-size: 14px; text-decoration: underline; }
        .judul p { margin: 5px 0 0 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; font-size: 10px; }
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
        <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h2>
        <h1>POLITEKNIK NEGERI LAMPUNG</h1>
        <h2>PROGRAM STUDI TEKNOLOGI REKAYASA PERANGKAT LUNAK</h2>
        <p>Jl. Soekarno Hatta No.10, Rajabasa, Kec. Rajabasa, Kota Bandar Lampung, Lampung 31414</p>
    </div>

    <div class="judul">
        <h3>LAPORAN DATA RUANGAN LABORATORIUM</h3>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <div class="ringkasan">
        <table>
            <tr><td>Total Ruangan</td><td>: {{ $ruangans->count() }} ruangan</td></tr>
            <tr><td>Total Kapasitas</td><td>: {{ $ruangans->sum('kapasitas') }} orang</td></tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Kode</th>
                <th width="18%">Nama Ruangan</th>
                <th width="12%">Lokasi</th>
                <th width="8%">Kapasitas</th>
                <th width="25%">Fasilitas</th>
                <th width="12%">Jumlah Data Barang Disimpan di Ruangan</th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ruangans as $index => $ruangan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $ruangan->kode_ruangan ?? '-' }}</td>
                <td>{{ $ruangan->nama_ruangan }}</td>
                <td>{{ $ruangan->lokasi ?? '-' }}</td>
                <td class="text-center">{{ $ruangan->kapasitas ?? '-' }}</td>
                <td>{{ $ruangan->fasilitas ?? '-' }}</td>
                <td class="text-center">{{ $ruangan->barangs_count ?? 0 }}</td>
                <td>{{ $ruangan->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">Tidak ada data ruangan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-container clearfix">
        <div class="ttd-box">
            <p>Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Kepala Laboratorium TRPL</p>
            <strong>__________________________</strong><br>
            NIP.
        </div>
    </div>

</body>
</html>
