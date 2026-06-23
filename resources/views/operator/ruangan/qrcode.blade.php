<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Code - {{ $ruangan->nama_ruangan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f3f4f6; }
        .card {
            background: white; border-radius: 16px; padding: 40px; text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08); max-width: 380px; width: 100%;
        }
        .logo { font-size: 11px; font-weight: 800; color: #6366f1; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 20px; }
        .qr-container { background: #f8fafc; border-radius: 12px; padding: 24px; margin: 16px 0; display: inline-block; border: 2px dashed #e2e8f0; }
        .room-name { font-size: 22px; font-weight: 800; color: #1e293b; margin-top: 16px; }
        .room-code { font-size: 12px; font-family: monospace; color: #94a3b8; margin-top: 4px; letter-spacing: 1px; }
        .instruction { font-size: 11px; color: #94a3b8; margin-top: 20px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
        .btn-print {
            margin-top: 20px; padding: 10px 32px; background: #4f46e5; color: white; border: none;
            border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;
        }
        .btn-print:hover { background: #4338ca; }
        @media print {
            body { background: white; }
            .btn-print { display: none; }
            .card { box-shadow: none; border: 2px solid #e2e8f0; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">Inventaris TRPL</div>

        <div class="qr-container">
            {!! QrCode::size(200)->generate(url('/scan-ruangan/' . $ruangan->kode_ruangan)) !!}
        </div>

        <div class="room-name">{{ $ruangan->nama_ruangan }}</div>
        <div class="room-code">{{ $ruangan->kode_ruangan }}</div>

        <div class="instruction">
            Scan QR Code di atas untuk melihat<br>daftar inventaris di ruangan ini.
        </div>

        <button class="btn-print" onclick="window.print()">🖨️ Cetak QR Code</button>
    </div>
</body>
</html>
