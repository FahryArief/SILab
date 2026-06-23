<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Barang Inventaris</title>
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
        th, td { border: 1px solid #000; padding: 5px 8px; text-align: left; font-size: 10px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
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
        <h3>LAPORAN DATA INVENTARIS BARANG LABORATORIUM</h3>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <div class="ringkasan">
        <table>
            <tr><td>Total Barang</td><td>: {{ $barangs->count() }} item</td></tr>
            <tr><td>Kondisi Baik</td><td>: {{ $barangs->where('kondisi', 'Baik')->count() }} item</td></tr>
            <tr><td>Kondisi Rusak Ringan</td><td>: {{ $barangs->where('kondisi', 'Rusak Ringan')->count() }} item</td></tr>
            <tr><td>Kondisi Rusak Berat</td><td>: {{ $barangs->where('kondisi', 'Rusak Berat')->count() }} item</td></tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Kode</th>
                <th width="18%">Nama Barang</th>
                <th width="10%">Merk</th>
                <th width="12%">Kategori</th>
                <th width="12%">Ruangan</th>
                <th width="8%">Pemilik</th>
                <th width="10%">Kondisi</th>
                <th width="8%">Status</th>
                <th width="10%">Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangs as $index => $barang)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $barang->barcode }}</td>
                <td>{{ $barang->nama_barang }}</td>
                <td>{{ $barang->merk ?? '-' }}</td>
                <td>{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                <td>{{ $barang->ruangan->nama_ruangan ?? '-' }}</td>
                <td class="text-center">{{ $barang->kepemilikan ?? '-' }}</td>
                <td class="text-center">{{ $barang->kondisi }}</td>
                <td class="text-center">{{ $barang->status_peminjaman }}</td>
                <td class="text-right">{{ $barang->harga ? 'Rp ' . number_format($barang->harga, 0, ',', '.') : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">Tidak ada data barang.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($barangs->sum('harga') > 0)
    <p style="text-align: right; font-weight: bold;">Total Nilai Aset: Rp {{ number_format($barangs->sum('harga'), 0, ',', '.') }}</p>
    @endif

    <div class="ttd-container clearfix">
        <div class="ttd-box">
            <p>Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Kepala Laboratorium TRPL</p>
            <strong>__________________________</strong><br>
            NIP.
        </div>
    </div>

</body>
</html>
