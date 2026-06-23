<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Label QR Batch - {{ $nama_barang }}</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #e2e8f0;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Header + Controls */
        .header-info {
            background: white; border-radius: 12px; padding: 16px 24px;
            margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            text-align: center; max-width: 600px; width: 100%;
        }
        .header-info h2 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0; }
        .header-info p { font-size: 12px; color: #64748b; margin: 0; }

        .size-selector {
            background: white; border-radius: 12px; padding: 16px 24px;
            margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            text-align: center; max-width: 600px; width: 100%;
        }
        .size-selector h3 { font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .size-options { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; }
        .size-btn {
            padding: 6px 14px; border: 2px solid #e2e8f0; border-radius: 8px; background: white;
            font-size: 11px; font-weight: 700; color: #64748b; cursor: pointer; transition: all 0.2s;
        }
        .size-btn:hover { border-color: #6366f1; color: #6366f1; }
        .size-btn.active { border-color: #6366f1; background: #6366f1; color: white; }
        .size-btn .size-dim { display: block; font-size: 8px; font-weight: 500; margin-top: 1px; opacity: 0.7; }

        .btn-actions { display: flex; gap: 8px; margin-bottom: 16px; }
        .btn-print {
            padding: 10px 24px; background: #4f46e5; color: white; border: none;
            border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;
        }
        .btn-print:hover { background: #4338ca; }
        .btn-back {
            padding: 10px 24px; background: white; color: #334155;
            border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px;
            font-weight: 600; cursor: pointer; text-decoration: none;
        }
        .btn-back:hover { background: #f8fafc; }

        /* ===== PAGE LAYOUTS PER SIZE ===== */
        .page {
            width: 210mm; min-height: 297mm; padding: 4mm;
            margin: 10mm auto; border: 1px solid #D3D3D3; border-radius: 5px;
            background: white; box-shadow: 0 0 5px rgba(0,0,0,0.1);
            display: grid; justify-content: center; align-content: start;
            box-sizing: border-box;
        }

        /* Kecil: 7 cols × 16 rows = 112 per page */
        .page.grid-kecil { grid-template-columns: repeat(7, 28mm); gap: 1mm; }
        /* Sedang: 4 cols × 10 rows = 40 per page */
        .page.grid-sedang { grid-template-columns: repeat(4, 48mm); gap: 2mm; }
        /* Standar: 3 cols × 8 rows = 24 per page */
        .page.grid-standar { grid-template-columns: repeat(3, 66mm); gap: 2mm; }
        /* Besar: 2 cols × 5 rows = 10 per page */
        .page.grid-besar { grid-template-columns: repeat(2, 98mm); gap: 2mm; }

        .label-box {
            border: 1px dashed #ccc; box-sizing: border-box;
            text-align: center; display: flex; flex-direction: column;
            justify-content: center; align-items: center; overflow: hidden; background: white;
        }

        /* ===== LABEL SIZE STYLES ===== */

        /* Kecil: 28×17mm */
        .label-box.lbl-kecil { width: 28mm; height: 17mm; padding: 0.5mm; }
        .label-box.lbl-kecil .lab-title { display: none; }
        .label-box.lbl-kecil .nama-barang { font-size: 5px; margin-bottom: 0; }
        .label-box.lbl-kecil .qr-img svg { width: 10mm !important; height: 10mm !important; }
        .label-box.lbl-kecil .info { font-size: 4px; letter-spacing: 0; }

        /* Sedang: 48×27mm */
        .label-box.lbl-sedang { width: 48mm; height: 27mm; padding: 1mm; }
        .label-box.lbl-sedang .lab-title { font-size: 5px; letter-spacing: 1px; margin-bottom: 1px; }
        .label-box.lbl-sedang .nama-barang { font-size: 6.5px; margin-bottom: 1px; }
        .label-box.lbl-sedang .qr-img svg { width: 14mm !important; height: 14mm !important; }
        .label-box.lbl-sedang .info { font-size: 5px; }

        /* Standar: 66×34mm (default) */
        .label-box.lbl-standar { width: 66mm; height: 33.9mm; padding: 2mm; }
        .label-box.lbl-standar .lab-title { font-size: 7px; letter-spacing: 2px; margin-bottom: 2px; }
        .label-box.lbl-standar .nama-barang { font-size: 8px; margin-bottom: 1px; }
        .label-box.lbl-standar .qr-img svg { width: 18mm !important; height: 18mm !important; }
        .label-box.lbl-standar .info { font-size: 6px; letter-spacing: 0.5px; }

        /* Besar: 98×55mm */
        .label-box.lbl-besar { width: 98mm; height: 55mm; padding: 3mm; }
        .label-box.lbl-besar .lab-title { font-size: 9px; letter-spacing: 2px; margin-bottom: 3px; }
        .label-box.lbl-besar .nama-barang { font-size: 11px; margin-bottom: 2px; }
        .label-box.lbl-besar .qr-img svg { width: 30mm !important; height: 30mm !important; }
        .label-box.lbl-besar .info { font-size: 8px; letter-spacing: 0.5px; }

        .lab-title { font-weight: 800; color: #6366f1; text-transform: uppercase; width: 100%; }
        .nama-barang { font-weight: 900; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; }
        .qr-img { margin: 1mm 0; }
        .info { font-family: monospace; color: #94a3b8; width: 100%; }

        @media print {
            body { margin: 0; padding: 0; background: white; }
            .page {
                width: 210mm; height: 297mm; padding: 4mm; margin: 0;
                border: none; border-radius: 0; box-shadow: none; page-break-after: always;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="header-info no-print">
        <h2>{{ $nama_barang }}</h2>
        <p>{{ $barangs->count() }} label QR Code siap cetak</p>
    </div>

    <div class="size-selector no-print">
        <h3>Pilih Ukuran Label</h3>
        <div class="size-options">
            <button class="size-btn" onclick="setSize('kecil', 112)">
                Kecil <span class="size-dim">28×17mm · 112/hal</span>
            </button>
            <button class="size-btn" onclick="setSize('sedang', 40)">
                Sedang <span class="size-dim">48×27mm · 40/hal</span>
            </button>
            <button class="size-btn active" onclick="setSize('standar', 24)">
                Standar <span class="size-dim">66×34mm · 24/hal</span>
            </button>
            <button class="size-btn" onclick="setSize('besar', 10)">
                Besar <span class="size-dim">98×55mm · 10/hal</span>
            </button>
        </div>
    </div>

    <div class="btn-actions no-print">
        <a href="{{ route('barang.index') }}" class="btn-back">← Kembali</a>
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Semua Label</button>
    </div>

    {{-- All labels rendered, JS handles regrouping into pages --}}
    <div id="pages-container">
        {{-- Default: standar (24 per page) --}}
        @php $chunked = $barangs->chunk(24); @endphp
        @foreach($chunked as $chunk)
        <div class="page grid-standar">
            @foreach($chunk as $barang)
            <div class="label-box lbl-standar" data-barcode="{{ $barang->barcode }}" data-nama="{{ $barang->nama_barang }}">
                <div class="lab-title">Inventaris TRPL</div>
                <div class="nama-barang">{{ $barang->nama_barang }}</div>
                <div class="qr-img">
                    {!! QrCode::size(68)->generate(url('/scan-barang/' . $barang->barcode)) !!}
                </div>
                <div class="info">{{ $barang->barcode }}</div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <script>
        // Collect all labels once at load
        const allLabels = [];
        document.querySelectorAll('.label-box').forEach(el => {
            allLabels.push(el.cloneNode(true));
        });

        const perPageMap = { kecil: 112, sedang: 40, standar: 24, besar: 10 };

        function setSize(size, perPage) {
            // Update buttons
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            event.currentTarget.classList.add('active');

            const container = document.getElementById('pages-container');
            container.innerHTML = '';

            // Re-chunk labels into pages
            for (let i = 0; i < allLabels.length; i += perPage) {
                const page = document.createElement('div');
                page.className = 'page grid-' + size;

                const chunk = allLabels.slice(i, i + perPage);
                chunk.forEach(label => {
                    const clone = label.cloneNode(true);
                    clone.className = 'label-box lbl-' + size;
                    page.appendChild(clone);
                });

                container.appendChild(page);
            }
        }
    </script>
</body>
</html>
