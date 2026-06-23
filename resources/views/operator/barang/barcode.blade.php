<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Code - {{ $barang->nama_barang }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; background: #f3f4f6; gap: 20px; padding: 20px; }

        /* Size Selector */
        .size-selector {
            background: white; border-radius: 12px; padding: 20px 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06); text-align: center; max-width: 500px; width: 100%;
        }
        .size-selector h3 { font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .size-options { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
        .size-btn {
            padding: 8px 16px; border: 2px solid #e2e8f0; border-radius: 8px; background: white;
            font-size: 12px; font-weight: 700; color: #64748b; cursor: pointer; transition: all 0.2s;
        }
        .size-btn:hover { border-color: #6366f1; color: #6366f1; }
        .size-btn.active { border-color: #6366f1; background: #6366f1; color: white; }
        .size-btn .size-dim { display: block; font-size: 9px; font-weight: 500; margin-top: 2px; opacity: 0.7; }

        /* Label Preview Container */
        .preview-area {
            background: white; border-radius: 16px; padding: 40px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08); text-align: center;
        }

        /* Label Styles per Size */
        .label {
            border: 2px dashed #e2e8f0; display: inline-flex; flex-direction: column;
            justify-content: center; align-items: center; background: white; transition: all 0.3s;
        }

        /* Kecil: 20x12mm — minimal, QR + code only */
        .label.size-kecil { width: 20mm; height: 12mm; padding: 1mm; }
        .label.size-kecil .lab-title { display: none; }
        .label.size-kecil .nama-barang { display: none; }
        .label.size-kecil .qr-img svg { width: 8mm !important; height: 8mm !important; }
        .label.size-kecil .info { font-size: 4px; letter-spacing: 0; }
        .label.size-kecil .item-extra { display: none; }

        /* Sedang: 40x25mm */
        .label.size-sedang { width: 40mm; height: 25mm; padding: 1.5mm; }
        .label.size-sedang .lab-title { font-size: 5px; letter-spacing: 1px; margin-bottom: 1px; }
        .label.size-sedang .nama-barang { font-size: 7px; margin-bottom: 1px; }
        .label.size-sedang .qr-img svg { width: 13mm !important; height: 13mm !important; }
        .label.size-sedang .info { font-size: 5px; }
        .label.size-sedang .item-extra { display: none; }

        /* Standar: 66x34mm (default) */
        .label.size-standar { width: 66mm; height: 34mm; padding: 2mm; }
        .label.size-standar .lab-title { font-size: 7px; letter-spacing: 2px; margin-bottom: 2px; }
        .label.size-standar .nama-barang { font-size: 8px; margin-bottom: 1px; }
        .label.size-standar .qr-img svg { width: 18mm !important; height: 18mm !important; }
        .label.size-standar .info { font-size: 6px; letter-spacing: 0.5px; }
        .label.size-standar .item-extra { display: none; }

        /* Besar: 90x50mm */
        .label.size-besar { width: 90mm; height: 50mm; padding: 3mm; }
        .label.size-besar .lab-title { font-size: 8px; letter-spacing: 2px; margin-bottom: 3px; }
        .label.size-besar .nama-barang { font-size: 11px; margin-bottom: 2px; }
        .label.size-besar .qr-img svg { width: 28mm !important; height: 28mm !important; }
        .label.size-besar .info { font-size: 8px; letter-spacing: 0.5px; }
        .label.size-besar .item-extra { font-size: 6px; color: #94a3b8; margin-top: 1px; }

        .lab-title { font-weight: 800; color: #6366f1; text-transform: uppercase; width: 100%; }
        .nama-barang { font-weight: 900; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; }
        .qr-img { margin: 1mm 0; }
        .info { font-family: monospace; color: #94a3b8; width: 100%; }
        .item-extra { display: none; }

        .btn-print {
            margin-top: 16px; padding: 10px 32px; background: #4f46e5; color: white; border: none;
            border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;
        }
        .btn-print:hover { background: #4338ca; }

        @media print {
            body { background: white; padding: 0; }
            .size-selector, .btn-print { display: none !important; }
            .preview-area { box-shadow: none; padding: 0; border-radius: 0; }
        }
    </style>
</head>
<body>
    <div class="size-selector no-print">
        <h3>Pilih Ukuran Label</h3>
        <div class="size-options">
            <button class="size-btn" onclick="setSize('kecil')">
                Kecil <span class="size-dim">20×12mm</span>
            </button>
            <button class="size-btn" onclick="setSize('sedang')">
                Sedang <span class="size-dim">40×25mm</span>
            </button>
            <button class="size-btn active" onclick="setSize('standar')">
                Standar <span class="size-dim">66×34mm</span>
            </button>
            <button class="size-btn" onclick="setSize('besar')">
                Besar <span class="size-dim">90×50mm</span>
            </button>
        </div>
    </div>

    <div class="preview-area">
        <div class="label size-standar" id="labelBox">
            <div class="lab-title">Inventaris TRPL</div>
            <div class="nama-barang">{{ $barang->nama_barang }}</div>
            <div class="qr-img">
                {!! QrCode::size(200)->generate(url('/scan-barang/' . $barang->barcode)) !!}
            </div>
            <div class="info">{{ $barang->barcode }}</div>
            <div class="item-extra">{{ $barang->kategori->nama_kategori ?? '' }} · {{ $barang->ruangan->nama_ruangan ?? '' }}</div>
        </div>

        <button class="btn-print no-print" onclick="window.print()">🖨️ Cetak Label</button>
    </div>

    <script>
        function setSize(size) {
            const label = document.getElementById('labelBox');
            label.className = 'label size-' + size;
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>
