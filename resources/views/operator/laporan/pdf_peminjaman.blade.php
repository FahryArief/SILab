<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Alat</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        /* Kop Surat */
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat h1 { margin: 0; font-size: 18px; text-transform: uppercase; font-weight: bold; }
        .kop-surat h2 { margin: 0; font-size: 16px; font-weight: normal; }
        .kop-surat p { margin: 2px 0 0 0; font-size: 11px; font-style: italic; }

        /* Judul Laporan */
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h3 { margin: 0; font-size: 14px; text-decoration: underline; }
        .judul p { margin: 5px 0 0 0; font-size: 12px; }

        /* Tabel Data */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }

        /* Tanda Tangan */
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { float: right; width: 250px; text-align: center; }
        .ttd-box p { margin: 0 0 60px 0; }

        /* Clearfix untuk float */
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
        <h3>LAPORAN REKAPITULASI PEMINJAMAN ALAT LABORATORIUM</h3>
        <p>Periode: {{ \Carbon\Carbon::parse($mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Peminjam</th>
                <th width="25%">Nama Alat</th>
                <th width="10%">Qty</th>
                <th width="25%">Tanggal Pinjam & Kembali</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjamans as $index => $pinjam)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $pinjam->user ? $pinjam->user->name : $pinjam->nama_peminjam }}</td>
                <td>{{ $pinjam->barangs->pluck('barcode')->implode(', ') }}</td>
                <td class="text-center">{{ $pinjam->barangs->count() }} Item</td>
                <td class="text-center">
                    {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') }} <br> s/d <br>
                    {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->format('d/m/Y') }}
                </td>
                <td class="text-center" style="text-transform: uppercase;">{{ str_replace('_', ' ', $pinjam->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data peminjaman pada periode ini.</td>
            </tr>
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
