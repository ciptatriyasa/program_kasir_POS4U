<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            width: 280px; 
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            line-height: 1.3;
        }
        .info {
            margin-bottom: 5px;
        }
        .info p {
            margin: 0;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        
        .items table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px dashed #000;
            margin-top: 5px;
            padding-top: 5px;
        }
        .items td {
            padding: 2px 0; /* Sedikit spasi antar item */
        }
        .items .item-name {
            font-weight: normal;
            font-size: 12px;
            text-align: left;
            /* Nama produk mengambil lebar penuh */
            padding-bottom: 0; 
        }
        .items .item-price {
            font-size: 12px;
            text-align: right; 
            vertical-align: top; /* Jaga agar subtotal sejajar dgn baris qty */
        }
        .items .item-qty {
            font-size: 12px;
            padding-left: 10px; /* Indentasi untuk "qty x harga" */
            text-align: left;
            padding-top: 0;
        }

        .total {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .total p {
            margin: 0;
            display: flex;
            justify-content: space-between;
            font-weight: normal;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
        }

        @media print {
            body {
                width: 90%;
                margin: 0;
                padding: 0;
            }
            @page {
                margin: 7mm;
            }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 1000);">
    
    <div class="header">
        <h1>POS4U</h1>
        <p>Jl. Kampus Bukit Jimbaran, Kuta Selatan.</p>
        <p>Badung, Bali 80364</p>
        <p>Telp: 08123456789</p>
    </div>

    <div class="info">
        <p><span>Tanggal</span> <span><?= esc(date('Y-m-d H:i:s', strtotime($penjualan['tanggal_penjualan']))) ?></span></p>
        <p><span>Kasir</span> <span><?= esc($penjualan['kasir_nama'] ?? 'N/A') ?></span></p>
        <p><span>No Transaksi</span> <span><?= esc($penjualan['id']) ?></span></p>
    </div>

    <div class="items">
        <table>
            <tbody>
                <?php 
                $total_qty = 0; 
                foreach ($detail as $d): 
                    $total_qty += $d['jumlah'];
                    
                    // === PERUBAHAN DI SINI: HITUNG HARGA SATUAN ===
                    $harga_satuan = ($d['jumlah'] > 0) ? ($d['subtotal'] / $d['jumlah']) : 0;
                ?>
                
                <tr>
                    <td class="item-name" colspan="2"><?= esc($d['nama_produk'] ?? 'Produk Dihapus') ?></td>
                </tr>
                <tr>
                    <td class="item-qty">
                        <?= esc($d['jumlah']) ?> x <?= rupiah_format($harga_satuan) // PERBAIKAN ?>
                    </td>
                    <td class="item-price">
                        <?= rupiah_format($d['subtotal']) // PERBAIKAN ?>
                    </td>
                </tr>
                
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="total">
        <p><span>Total QTY</span> <span><?= $total_qty ?></span></p>
        <p><span>Sub Total</span> <span><?= rupiah_format($penjualan['total_harga']) // PERBAIKAN ?></span></p>
        
        <p><span>Pembayaran</span> <span><?= rupiah_format($penjualan['uang_dibayar'] ?? 0) // PERBAIKAN ?></span></p>
        <p><span>Kembalian</span> <span><?= rupiah_format($penjualan['kembalian'] ?? 0) // PERBAIKAN ?></span></p>
    </div>

    <div class="footer">
        <p>Terima kasih telah berbelanja</p>
    </div>
</body>
</html>