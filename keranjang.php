<?php
session_start();

$koneksi = mysqli_connect("localhost", "root", "", "db_toko");

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

/* TAMBAH KE KERANJANG */
if (isset($_POST["add"])) {
    $id_produk = $_GET["id"];
    $jumlah = (int) $_POST["jumlah"];
    if ($jumlah < 1) { $jumlah = 1; }

    if (isset($_SESSION["cart"][$id_produk])) {
        $_SESSION["cart"][$id_produk]['jumlah'] += $jumlah;
    } else {
        $_SESSION["cart"][$id_produk] = [
            'id' => $id_produk,
            'nama' => $_POST["hidden_nama"],
            'harga' => $_POST["hidden_harga"],
            'poto' => $_POST["hidden_foto"],
            'jumlah' => $jumlah
        ];
    }

    echo '<script>window.location="keranjang.php"</script>';
    exit;
}

if (isset($_GET["aksi"])) {
    if ($_GET["aksi"] == "hapus") {
        $id_produk = $_GET["id"];
        if (isset($_SESSION["cart"][$id_produk])) { unset($_SESSION["cart"][$id_produk]); }
        echo '<script>window.location="keranjang.php"</script>';
        exit;
    }
}

if (isset($_POST["proses_beli"])) {
    /* Cek login */
    if (!isset($_SESSION['id_user']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
        echo "<script>window.location='login.php';</script>";
        exit;
    }

    /* Cek keranjang */
    if (empty($_SESSION["cart"])) {
        echo "<script>alert('Keranjang masih kosong.');window.location='keranjang.php';</script>";
        exit;
    }

    /* Ambil metode pembayaran */
    $metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran'] ?? '');

    /* Ambil metode pengiriman */
    $metode_pengiriman = mysqli_real_escape_string($koneksi, $_POST['metode_pengiriman'] ?? '');

    /* Validasi */
    if ($metode_pembayaran == '' || $metode_pengiriman == '') {
        echo "<script>alert('Silakan pilih metode pembayaran dan metode pengiriman.');window.location='keranjang.php';</script>";
        exit;
    }

    /* Hitung total */
    $total = 0;
    foreach ($_SESSION["cart"] as $value) {
        $harga = (int) $value["harga"];
        $jumlah = (int) $value["jumlah"];
        $total += $harga * $jumlah;
    }

    /* ID pelanggan */
    $id_user = $_SESSION['id_user'];

    /* Simpan transaksi */
    $query = mysqli_query(
        $koneksi,
        "INSERT INTO transaksi(tanggal,id_pelanggan,total_harga, metode_pembayaran,metode_pengiriman)
        VALUES('" . date("Y-m-d") . "','$id_user','$total','$metode_pembayaran', '$metode_pengiriman')"
    );

    if (!$query) {
        die("Error insert transaksi: " . mysqli_error($koneksi));
    }

    /* Ambil ID transaksi */
    $id_transaksi = mysqli_insert_id($koneksi);

    /* Simpan detail produk */
    foreach ($_SESSION["cart"] as $value) {
        $id_produk = (int) $value['id'];
        $jumlah = (int) $value['jumlah'];

        $query_detail = mysqli_query(
            $koneksi,
            "INSERT INTO detail(id_transaksi,id_produk,jumlah)
            VALUES('$id_transaksi','$id_produk','$jumlah')"
        );

        if (!$query_detail) {
            die("Error insert detail: " . mysqli_error($koneksi));
        }
    }

    unset($_SESSION["cart"]);
    $_SESSION['checkout_success'] = true;
    header("Location: cetak.php?id=$id_transaksi");
    exit;
}

$jumlah_cart = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $jumlah_cart += (int) $item['jumlah'];
    }
}

$grand_total = 0;
if (!empty($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $item) {
        $grand_total += (int) $item["harga"] * (int) $item["jumlah"];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Beadly Accesorise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: 
        border-box; 
    }
        html { 
            scroll-behavior: smooth; 
        }
        body {
        margin: 0; 
        background: #f8f6f3; 
        color: #222; 
        font-family: 'Inter', sans-serif; 
    }
        h1,h2,h3,h4,h5 { 
            font-family: 'Playfair Display', serif; 
        }

        .navbar-custom {
            height: 82px; 
            display: flex; 
            align-items: center; 
            background: #fff;
            border-bottom: 1px solid #eee; 
            position: relative; 
            z-index: 1050;
        }

        .navbar-inner {
            width: 100%; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
        }

        .logo { 
            display: flex; 
            align-items: center; 
            text-decoration: none; 
        }
        .logo img { 
            width: 115px; 
            height: auto; 
            object-fit: contain; 
        }

        .menu {
            display: flex; 
            align-items: center; 
            gap: 30px; 
            margin: 0; 
            padding: 0; 
            list-style: none;
        }

        .menu a {
            position: relative; 
            color: #555; 
            font-size: 12px; 
            font-weight: 500;
            text-decoration: none;
            transition: .3s;
        }

        .menu a::after {
            content: ""; 
            position: absolute; 
            left: 0; 
            bottom: -7px; 
            width: 0;
            height: 1px; 
            background: #b89a7a;
            transition: .3s;
        }

        .menu a:hover { 
            color: #b89a7a;
         }
        .menu a:hover::after { 
            width: 100%; 
        }

        .nav-icons {
            display: flex; 
            align-items: center; 
            gap: 5px;
        }

        .icon-btn {
            position: relative; 
            width: 42px; 
            height: 42px; 
            display: flex;
            align-items: center; 
            justify-content: center; 
            border: none;
            background: transparent; 
            color: #333; 
            border-radius: 50%;
            text-decoration: none; 
            transition: .3s;
        }

        .icon-btn:hover {
            background: #f7f3ef; 
            color: #b89a7a; 
            transform: translateY(-2px);
        }

        .cart-link { 
            position: relative;
         }

        .cart-badge {
            position: absolute; 
            top: 3px; 
            right: 3px; 
            min-width: 17px; 
            height: 17px;
            padding: 0 4px; 
            background-color: #b89a7a; 
            color: #fff; 
            border-radius: 50%;
            font-family: 'Inter', sans-serif; 
            font-size: 9px; 
            font-weight: 600;
            line-height: 17px; 
            text-align: center; 
            pointer-events: none;
        }

        .container-cart {
            max-width: 1100px; 
            margin: 0 auto; 
            padding: 70px 20px 80px;
        }

        .page-title {
            text-align: center; 
            font-family: 'Playfair Display', serif; 
            font-size: 36px;
            font-weight: 600; 
            margin-bottom: 35px; 
            color: #222;
        }

        .cart-card {
            background: #fff; 
            border-radius: 18px; 
            border: 1px solid #ece8e3;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.04); 
            overflow: hidden;
        }

        .table-wrapper { 
            padding: 25px 30px 10px; 
        }
        .table {
             margin-bottom: 0; 
             vertical-align: middle; 
            }

        .table thead th {
            border-bottom: 1px solid #e9e5e0; 
            color: #777; 
            font-size: 13px;
            font-weight: 600; 
            padding: 15px 12px; 
            white-space: nowrap;
        }

        .table tbody td {
            border-bottom: 1px solid #f0ece8;
            padding: 20px 12px; 
            font-size: 14px;
        }

        .product-info {
            display: flex; 
            align-items: center; 
            gap: 15px; 
            min-width: 230px;
        }

        .product-image {
            width: 70px; 
            height: 70px; 
            border-radius: 12px; 
            object-fit: cover; 
            border: 1px solid #eee;
        }

        .product-name { 
            font-weight: 600; 
            color: #222;
         }

        .quantity-badge {
            display: inline-flex; 
            align-items: center; 
            justify-content: center;
            min-width: 35px; 
            height: 35px; 
            padding: 0 10px; 
            border-radius: 8px;
            background: #f4f0eb; 
            font-size: 13px; 
            font-weight: 600;
        }

        .price { 
            white-space: nowrap; 
        }

        .btn-hapus {
            display: inline-flex; 
            align-items: center; 
            gap: 5px; 
            border: none;
            background: transparent; 
            color: #777; 
            font-size: 13px; 
            text-decoration: none; 
            transition: 0.3s;
        }

        .btn-hapus:hover { 
            color: #b89a7a; 
        }

        .checkout-box {
            margin-top: 10px; 
            padding: 30px; 
            border-top: 1px solid #eee; 
            background: #fff;
        }

        .checkout-section {
             margin-bottom: 28px; 
            }

        .checkout-title {
            font-size: 14px; 
            font-weight: 600; 
            margin-bottom: 14px; 
            color: #333;
        }

        .option-group {
            display: flex; 
            flex-wrap: wrap; 
            gap: 12px;
        }

        .option-item { 
            position: relative; 
        }

        .option-item input {
            position: absolute; 
            opacity: 0; 
            pointer-events: none;
        }

        .option-label {
            display: inline-flex; 
            align-items: center; 
            justify-content: center;
            min-width: 145px; 
            padding: 13px 18px; 
            border: 1px solid #ddd;
            border-radius: 10px; 
            background: #fff; 
            color: #555; 
            font-size: 13px;
            cursor: pointer; 
            transition: 0.3s;
        }

        .option-label:hover {
            border-color: #b89a7a; 
            color: #b89a7a;
        }

        .option-item input:checked + .option-label {
            background: #111; 
            border-color: #111; 
            color: #fff;
        }

        .checkout-bottom {
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            gap: 20px; 
            padding-top: 25px; 
            border-top: 1px solid #eee;
        }

        .total-label {
            display: block; 
            font-size: 13px; 
            color: #777; 
            margin-bottom: 6px;
        }

        .total-price {
            font-size: 25px; 
            font-weight: 700; 
            color: #222;
        }

        .btn-beli {
            min-width: 190px; 
            padding: 14px 25px; 
            border: none; 
            border-radius: 10px;
            background: #4f3c27; 
            color: #fff; 
            font-size: 14px; 
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-beli:hover { 
            background: #b89a7a; 
        }

        .empty-cart { 
            padding: 80px 20px; 
            text-align: center; 
        }

        .empty-cart i {
            font-size: 60px; 
            color: #c8b39b; 
            margin-bottom: 20px;
        }

        .empty-cart h3 {
            font-family: 'Playfair Display', serif; 
            font-size: 28px; 
            margin-bottom: 10px;
        }

        .empty-cart p {
            color: #777; 
            font-size: 14px; 
            margin-bottom: 25px;
        }

        .btn-belanja {
            display: inline-block; 
            padding: 13px 24px; 
            background: #4f3c27;
            color: #fff; 
            border-radius: 10px; 
            text-decoration: none; 
            font-size: 14px;
            font-weight: 600; 
            transition: 0.3s;
        }

        .btn-belanja:hover { 
        background: #b89a7a; 
        color: #fff; 
        }

        .footer {
            padding: 30px 0; 
            border-top: 1px solid #eee; 
            background: #fff;
            color: #aaa; 
            font-size: 10px; 
            text-align: center;
        }

        @media (max-width: 991px) {
            .menu { 
                gap: 15px; 
            }
        }

        @media (max-width: 767px) {
            .navbar-custom { 
                height: auto; 
                padding: 15px 0; 
            }
            .navbar-inner { 
                flex-wrap: wrap; 
                gap: 15px; 
            }
            .logo img { 
                width: 100px; 
            }

            .menu {
                order: 3; 
                width: 100%; 
                justify-content: center;
                gap: 18px;
                overflow-x: auto; 
                padding-bottom: 5px;
            }

            .menu a { 
                white-space: nowrap; 
                font-size: 11px; 
            }
            .container-cart { 
                padding-top: 50px; 
            }
            .table-wrapper { 
                padding: 20px 15px 5px; 
            }
            .checkout-box { 
                padding: 25px 20px; 
            }

            .checkout-bottom {
                flex-direction: column; 
                align-items: stretch;
            }

            .btn-beli { 
                width: 100%; 
            }
            .option-label { 
                min-width: auto; width: 100%; 
            }

            .option-group {
                display: grid; 
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<!-- ==================================================
     NAVBAR
================================================== -->

<header class="navbar-custom">
    <div class="container">
        <div class="navbar-inner">

            <!-- LOGO -->
            <a href="index.php" class="logo">
                <img src="assets/img/beadly_logo.png" alt="Beadly Accesorise">
            </a>

            <!-- MENU -->
            <ul class="menu">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="index.php#gelang">Gelang</a></li>
                <li><a href="index.php#cincin">Cincin</a></li>
                <li><a href="index.php#mac">Kalung</a></li>
                <li><a href="index.php#aksesori">Gantungan kunci</a></li>
            </ul>

            <!-- ICON -->
            <div class="nav-icons">

                <!-- PROFILE -->
                <a href="profile.php" class="icon-btn" title="Profile">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                    </svg>
                </a>

                <!-- KERANJANG -->
                <a href="keranjang.php" class="icon-btn cart-link" title="Keranjang">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-2 0m-7 1a1 1 0 1 1 0 2 2 2 0 0 1-2 0m7 0a1 1 0 1 1 0 2 2 2 0 0 1-2 0" />
                    </svg>

                    <?php if ($jumlah_cart > 0) { ?>
                        <span class="cart-badge"><?= $jumlah_cart ?></span>
                    <?php } ?>
                </a>

            </div>
        </div>
    </div>
</header>

<!-- ==================================================
     CONTENT
================================================== -->

<div class="container-cart">

    <h1 class="page-title">Keranjangmu</h1>

    <?php if (!empty($_SESSION["cart"])) { ?>

        <!-- FORM CHECKOUT -->
        <form method="post" action="keranjang.php" id="checkoutForm">

            <!-- SATU CARD BESAR -->
            <div class="cart-card">

                <!-- =========================
                     TABEL PRODUK
                ========================== -->

                <div class="table-wrapper">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($_SESSION["cart"] as $value) {
                                    $subtotal = (int) $value["harga"] * (int) $value["jumlah"];
                                ?>

                                    <tr>
                                        <!-- PRODUK -->
                                        <td>
                                            <div class="product-info">
                                                <img src="img/<?= htmlspecialchars($value['poto']) ?>" alt="<?= htmlspecialchars($value['nama']) ?>" class="product-image">
                                                <div class="product-name"><?= htmlspecialchars($value["nama"]) ?></div>
                                            </div>
                                        </td>

                                        <!-- JUMLAH -->
                                        <td>
                                            <span class="quantity-badge"><?= $value["jumlah"] ?></span>
                                        </td>

                                        <!-- HARGA -->
                                        <td class="price">
                                            Rp<?= number_format($value["harga"], 0, ",", ".") ?>
                                        </td>

                                        <!-- TOTAL -->
                                        <td class="price">
                                            Rp<?= number_format($subtotal, 0, ",", ".") ?>
                                        </td>

                                        <!-- AKSI -->
                                        <td>
                                            <a href="keranjang.php?aksi=hapus&id=<?= $value['id'] ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?')">
                                                <i class="bi bi-trash3"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- =========================
                     CHECKOUT
                ========================== -->

                <div class="checkout-box">

                    <!-- METODE PEMBAYARAN -->
                    <div class="checkout-section">
                        <div class="checkout-title">Metode Pembayaran</div>

                        <div class="option-group">

                            <!-- TRANSFER BANK -->
                            <div class="option-item">
                                <input type="radio" id="transfer" name="metode_pembayaran" value="Transfer Bank">
                                <label for="transfer" class="option-label">Transfer Bank</label>
                            </div>

                            <!-- E-WALLET -->
                            <div class="option-item">
                                <input type="radio" id="ewallet" name="metode_pembayaran" value="E-Wallet">
                                <label for="ewallet" class="option-label">E-Wallet</label>
                            </div>

                            <!-- COD -->
                            <div class="option-item">
                                <input type="radio" id="cod" name="metode_pembayaran" value="COD">
                                <label for="cod" class="option-label">COD</label>
                            </div>

                        </div>
                    </div>

                    <!-- METODE PENGIRIMAN -->
                    <div class="checkout-section">
                        <div class="checkout-title">Metode Pengiriman</div>

                        <div class="option-group">

                            <!-- AMBIL DI TOKO -->
                            <div class="option-item">
                                <input type="radio" id="toko" name="metode_pengiriman" value="Ambil di Toko">
                                <label for="toko" class="option-label">Ambil di Toko</label>
                            </div>

                            <!-- KURIR -->
                            <div class="option-item">
                                <input type="radio" id="kurir" name="metode_pengiriman" value="Kurir">
                                <label for="kurir" class="option-label">Kurir</label>
                            </div>

                        </div>
                    </div>

                    <!-- TOTAL -->
                    <div class="checkout-bottom">

                        <div>
                            <span class="total-label">Total yang Harus Dibayar</span>
                            <div class="total-price">
                                Rp<?= number_format($grand_total, 0, ",", ".") ?>
                            </div>
                        </div>

                        <!-- SUDAH LOGIN -->
                        <?php if (
                            isset($_SESSION['id_user']) &&
                            isset($_SESSION['role']) &&
                            $_SESSION['role'] === 'pelanggan'
                        ) { ?>

                            <button type="submit" name="proses_beli" class="btn-beli">
                                Beli Sekarang
                            </button>

                        <?php } else { ?>

                            <!-- BELUM LOGIN -->
                            <button type="button" class="btn-beli" onclick="loginDulu()">
                                Beli Sekarang
                            </button>

                        <?php } ?>

                    </div>
                </div>
            </div>
        </form>

    <?php } else { ?>

        <div class="cart-card">
            <div class="empty-cart">
                <i class="bi bi-bag-x"></i>
                <h3>Keranjangmu masih kosong</h3>
                <p>Yuk pilih produk yang kamu sukadan masukkan ke keranjang.</p>
                <a href="index.php" class="btn-belanja">Mulai Belanja</a>
            </div>
        </div>

    <?php } ?>

</div>

<footer class="footer">© <?= date("Y") ?> Beadly Accesorise —Handmade with love ♡</footer>

<script>
function loginDulu() {
    Swal.fire({
        title: 'Login Diperlukan',
        text: 'Kamu harus login terlebih dahulu untuk melakukan pembelian.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Login Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#111',
        cancelButtonColor: '#aaa'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'login.php';
        }
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>