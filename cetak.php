<?php
session_start();
$koneksi = mysqli_connect("localhost", "root", "", "db_toko");
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
/* STATUS CHECKOUT */
$checkout_success = false;
if (isset($_SESSION['checkout_success'])) {
    $checkout_success = true;
    unset($_SESSION['checkout_success']);
}
/* CEK ID TRANSAKSI */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID transaksi tidak ditemukan.");
}
$id_transaksi = intval($_GET['id']);

$query_transaksi = mysqli_query(
    $koneksi,
    "SELECT * FROM transaksi
     WHERE id_transaksi = '$id_transaksi'"
);

$transaksi = mysqli_fetch_assoc($query_transaksi);

if (!$transaksi) {
    die("Data transaksi tidak ditemukan.");
}

$query_detail = mysqli_query(
    $koneksi,"SELECT detail.*,produk.nama,produk.harga,produk.poto 
     FROM detail
     JOIN produk
     ON detail.id_produk = produk.id
     WHERE detail.id_transaksi = '$id_transaksi'"
);

$nomor_invoice =
    "INV-" .
    date("Ymd", strtotime($transaksi['tanggal'])) .
    "-" .
    str_pad($id_transaksi, 4, "0", STR_PAD_LEFT);

$nama_pembeli = "Pelanggan";
if (isset($_SESSION['nama'])) {
    $nama_pembeli = $_SESSION['nama'];
} elseif (isset($_SESSION['username'])) {
    $nama_pembeli = $_SESSION['username'];
}

$grand_total = 0;?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"content="width=device-width, initial-scale=1.0">
    <title>
        <?= $nomor_invoice ?> | Beadly
    </title>
    <link href="assets/dist/css/bootstrap.min.css"rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Inter:wght@400;500;600&display=swap"rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f8f6f3;
            color: #222;
            font-family: 'Inter', sans-serif;
        }

        h1, h2, h3,h4 {
            font-family: 'Playfair Display', serif;
        }

        .invoice-wrapper {
            padding: 50px 15px;
        }

        .invoice {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 55px;
            border-radius: 18px;
            box-shadow:
                0 15px 50px rgba(0, 0, 0, 0.06);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }

        .logo img {
            width: 120px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            margin: 0;
            font-size: 36px;
            font-weight: 500;
        }

        .invoice-title p {
            margin: 6px 0 0;
            color: #b89a7a;
            font-size: 13px;
            font-weight: 600;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin: 35px 0;
        }

        .info-box h4 {
            font-size: 15px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .info-box p {
            margin: 4px 0;
            color: #777;
            font-size: 12px;
        }

        .info-right {
            text-align: right;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .invoice-table thead {
            background: #faf8f6;
        }

        .invoice-table th {
            padding: 14px;
            font-size: 11px;
            color: #666;
            font-weight: 600;
            border-bottom: 1px solid #eee;
        }

        .invoice-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
            color: #555;
            vertical-align: middle;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-info img {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
            background: #faf8f6;
        }

        .product-name {
            color: #333;
            font-weight: 500;
        }

        .price {
            color: #b89a7a;
            font-weight: 600;
        }
        
        .invoice-total {
            width: 330px;
            margin-left: auto;
            margin-top: 25px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 12px;
            color: #777;
        }

        .total-final {
            border-top: 1px solid #ddd;
            margin-top: 8px;
            padding-top: 15px;
            color: #222;
            font-size: 16px;
            font-weight: 600;
        }

        .total-final span:last-child {
            color: #b89a7a;
        }

        .invoice-footer {
            margin-top: 50px;
            padding-top: 25px;
            border-top: 1px solid #eee;
            text-align: center;
        }

        .invoice-footer p {
            margin: 5px 0;
            color: #999;
            font-size: 11px;
        }

        .thank-you {
            font-family: 'Playfair Display', serif;
            font-size: 18px !important;
            color: #333 !important;
        }

        .invoice-actions {
            max-width: 900px;
            margin: 20px auto 0;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-invoice {
            border: none;
            border-radius: 30px;
            padding: 11px 25px;
            font-size: 12px;
            text-decoration: none;
            cursor: pointer;
            transition: .3s;
        }

        .btn-print {
            background: #4f3c27;
            color: #fff;
        }

        .btn-print:hover {
            background: #b89a7a;
            color: #fff;
        }

        .btn-home {
            background: #fff;
            color: #555;
            border: 1px solid #ddd;
        }

        .btn-home:hover {
            border-color: #b89a7a;
            color: #b89a7a;
        }
        @media print {
            body {
                background: #fff;
            }

            .invoice-wrapper {
                padding: 0;
            }

            .invoice {
                max-width: 100%;
                padding: 30px;
                box-shadow: none;
                border-radius: 0;
            }

            .invoice-actions {
                display: none;
            }
        }

        @media (max-width: 600px) {
            .invoice {
                padding: 25px 18px;
            }

            .invoice-header {
                flex-direction: column;
                gap: 20px;
            }

            .invoice-title {
                text-align: left;
            }

            .invoice-title h1 {
                font-size: 30px;
            }

            .invoice-info {
                flex-direction: column;
                gap: 20px;
            }

            .info-right {
                text-align: left;
            }

            .invoice-table {
                min-width: 650px;
            }

            .invoice-total {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="invoice-wrapper">
    <div class="invoice">
        <div class="invoice-header">
            <div class="logo">
                <img src="assets/img/beadly_logo.png"alt="Beadly">
            </div>
            <div class="invoice-title">
                <h1>Invoice</h1>
                <p><?= $nomor_invoice ?></p>
            </div>
        </div>
        <div class="invoice-info">
            <div class="info-box">
                <h4>Ditagihkan Kepada</h4>
                <p><?= htmlspecialchars($nama_pembeli) ?></p>
                <p>ID Pelanggan:<?= htmlspecialchars($transaksi['id_pelanggan']) ?></p>
            </div>
            
            <div class="info-box info-right">
                <h4>Detail Pesanan </h4>
                <p>Nomor Invoice: <?= $nomor_invoice ?></p>
                <p>Tanggal: <?= date("d F Y",strtotime($transaksi['tanggal'])) ?></p>
                <p>Metode Pembayaran:<strong style="color:#b89a7a;"><?= htmlspecialchars($transaksi['metode_pembayaran']) ?> </strong></p>
                <p>Metode Pengiriman:<strong style="color:#b89a7a;"> <?= htmlspecialchars($transaksi['metode_pengiriman']) ?> </strong> </p>
                <p>Status:<strong style="color:#b89a7a;">Selesai</strong></p>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th style="text-align:left;">Produk</th>
                        <th style="text-align:center;">Jumlah</th>
                        <th style="text-align:right;">Harga</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while ($detail =mysqli_fetch_assoc($query_detail)) {
                    $subtotal =$detail['jumlah'] *$detail['harga'];
                    $grand_total += $subtotal;
                ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="img/<?= htmlspecialchars($detail['poto']) ?>" alt="<?= htmlspecialchars($detail['nama']) ?>">
                                <span class="product-name">
                                    <?= htmlspecialchars($detail['nama']) ?>
                                </span>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <?= $detail['jumlah'] ?>
                        </td>
                        <td style="text-align:right;"class="price">
                            Rp <?= number_format($detail['harga'], 0,',','.') ?>
                        </td>
                        <td style="text-align:right;"class="price">
                            Rp <?= number_format($subtotal,0,',','.') ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        
        <div class="invoice-total">
            <div class="total-row">
                <span>Subtotal</span>
                <span>
                    Rp <?= number_format( $grand_total,0,',', '.') ?>
                </span>
            </div>
            
            <div class="total-row total-final">
                <span>Total  </span>
                <span>
                    Rp <?= number_format( $transaksi['total_harga'], 0, ',', '.' ) ?>
                </span>
            </div>
        </div>
        
        <div class="invoice-footer">
            <p class="thank-you">Terima kasih telah berbelanja di Beadly ♡</p>
            <p>Simple beads, beautiful style.</p>
            <p>Handmade in Cirebon.</p>
        </div>
    </div>
    
    <div class="invoice-actions">
        <button onclick="window.print()"class="btn-invoice btn-print">
            Cetak Invoice
        </button>

        <a href="index.php" class="btn-invoice btn-home">
            Kembali ke Beranda
        </a>
    </div>
</div>

<?php if ($checkout_success) { ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil Dibeli! ✨',
    text: 'Pesanan kamu berhasil dibuat.',
    confirmButtonText: 'Lihat Pesanan',
    confirmButtonColor: '#111',
    allowOutsideClick: false
});
</script>
<?php } ?>
</body>
</html>