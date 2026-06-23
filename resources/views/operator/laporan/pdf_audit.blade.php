<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Audit Inventaris</title>
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
        .section-title { font-size: 13px; font-weight: bold; margin: 25px 0 10px 0; border-bottom: 1px solid #000; padding-bottom: 5px; }
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
        <h3>LAPORAN HASIL AUDIT INVENTARIS</h3>
        <p>Periode: {{ $periode->nama_periode }}</p>
        <p>Tanggal: {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="ringkasan">
        <table>
            <tr><td>Status Audit</td><td>: {{ ucfirst($periode->status) }}</td></tr>
            <tr><td>Tipe Audit</td><td>: {{ $periode->tipe == 'barang' ? 'Barang' : ($periode->tipe == 'ruangan' ? 'Ruangan' : 'Barang & Ruangan') }}</td></tr>
            @if($periode->catatan)<tr><td>Catatan</td><td>: {{ $periode->catatan }}</td></tr>@endif
        </table>
    </div>

    {{-- AUDIT BARANG --}}
    @if($auditBarangs && $auditBarangs->count() > 0)
    <div class="section-title">A. Hasil Audit Barang ({{ $auditBarangs->count() }} item diperiksa)</div>
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Kode</th>
                <th width="20%">Nama Barang</th>
                <th width="12%">Ruangan</th>
                <th width="10%">Kondisi</th>
                <th width="15%">Tanggal Audit</th>
                <th width="15%">Teknisi</th>
                <th width="15%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($auditBarangs as $index => $ab)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $ab->barang->barcode ?? '-' }}</td>
                <td>{{ $ab->barang->nama_barang ?? '-' }}</td>
                <td>{{ $ab->barang->ruangan->nama_ruangan ?? '-' }}</td>
                <td class="text-center">{{ $ab->kondisi }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($ab->tanggal_audit)->format('d/m/Y') }}</td>
                <td>{{ $ab->teknisi->name ?? '-' }}</td>
                <td>{{ $ab->catatan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $baik = $auditBarangs->where('kondisi', 'Baik')->count();
        $rr = $auditBarangs->where('kondisi', 'Rusak Ringan')->count();
        $rb = $auditBarangs->where('kondisi', 'Rusak Berat')->count();
    @endphp
    <p><strong>Ringkasan:</strong> Baik: {{ $baik }} | Rusak Ringan: {{ $rr }} | Rusak Berat: {{ $rb }}</p>
    @endif

    {{-- AUDIT RUANGAN --}}
    @if($auditRuangans && $auditRuangans->count() > 0)
    <div class="section-title">{{ $auditBarangs && $auditBarangs->count() > 0 ? 'B' : 'A' }}. Hasil Audit Ruangan ({{ $auditRuangans->count() }} ruangan diperiksa)</div>
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="18%">Nama Ruangan</th>
                <th width="35%">Fasilitas & Kondisi</th>
                <th width="13%">Tanggal Audit</th>
                <th width="13%">Teknisi</th>
                <th width="17%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($auditRuangans as $index => $ar)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $ar->ruangan->nama_ruangan ?? '-' }}</td>
                <td>
                    @if($ar->fasilitas_audit && is_array($ar->fasilitas_audit))
                        @foreach($ar->fasilitas_audit as $fasilitas => $kondisi)
                            {{ $fasilitas }}: <strong>{{ $kondisi }}</strong>{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">{{ \Carbon\Carbon::parse($ar->tanggal_audit)->format('d/m/Y') }}</td>
                <td>{{ $ar->teknisi->name ?? '-' }}</td>
                <td>{{ $ar->catatan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
