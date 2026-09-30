<?php
session_start();

$login_success = "";

if (isset($_SESSION['login_success'])) {
    $login_success = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}

$jumlah_cart = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $jumlah_cart += $item['jumlah'];
    }
}

include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport"content="width=device-width, initial-scale=1">
    <title>Beadly - Simple beads, beautiful style.</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="assets/dist/css/bootstrap.min.css"rel="stylesheet">
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect"href="https://fonts.gstatic.com"crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Inter:wght@400;500;600&display=swap"rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 100px;
        }

        body {
            margin: 0;
            background: #fff;
            color: #694242;
            font-family: 'Inter', sans-serif;
        }

        h1,h2,h3,h4,h5 {
            font-family: 'Playfair Display', serif;
        }

        .navbar-custom {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 82px;
            display: flex;
            align-items: center;
            background: #fff;
            border-bottom: 1px solid #eee;
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

        .icon-btn i {
            font-size: 20px;
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

        .carousel {
            margin-top: 82px;
        }

        .carousel-item {
            height: 650px;
            position: relative;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(0, 0, 0, 0.03),
                rgba(0, 0, 0, 0.50)
            );
        }

        .carousel-caption {
            z-index: 2;
        }

        .carousel-caption h1 {
            font-size: 55px;
            font-weight: 400;
        }

        .carousel-caption p {
            font-size: 17px;
            margin-top: 10px;
        }

        .carousel-caption .btn {
            border: none;
            padding: 11px 25px;
            font-size: 14px;
            transition: 0.3s;
        }

        .carousel-caption .btn:hover {
            background-color: #b89a7a;
            color: #fff;
        }

        .product-section {
            padding: 80px 0;
            background-color: #fff;
        }

        .section-label {
            letter-spacing: 4px;
            font-size: 11px;
            color: #b89a7a;
            text-transform: uppercase;
        }

        .section-title {
            font-size: 40px;
            font-weight: 400;
            margin-top: 8px;
        }

        .section-line {
            width: 60px;
            height: 2px;
            background-color: #b89a7a;
            margin: 18px auto 50px;
        }

        .product-card {
            border: 1px solid #eeeeee;
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
            transition: all 0.4s ease;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.10) !important;
        }


        .product-image-wrapper {
            height: 320px;
            overflow: hidden;
            position: relative;
            background-color: #fff;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.08);
        }

        .product-body {
            padding: 24px;
        }

        .product-name {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .product-price {
            color: #b89a7a;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-description {
            color: #777;
            font-size: 13px;
            line-height: 1.6;
            min-height: 42px;
            margin-bottom: 8px;
        }

        .stok-tersedia {
            display: inline-block;
            margin-bottom: 4px;
            padding: 5px 10px;
            border-radius: 20px;
            background: #f5efe9;
            color: #b89a7a;
            font-size: 11px;
            font-weight: 500;
        }

        .stok-habis {
            display: inline-block;
            margin-bottom: 4px;
            padding: 5px 10px;
            border-radius: 20px;
            background: #f1f1f1;
            color: #999;
            font-size: 11px;
            font-weight: 500;
        }

        .btn-beadly {
            background-color: #4f3c27;
            color: #fff;
            border-radius: 30px;
            padding: 11px 22px;
            font-size: 13px;
            letter-spacing: 0.4px;
            border: none;
            transition: 0.3s;
        }

        .btn-beadly:hover {
            background-color: #b89a7a;
            color: #fff;
        }

        .btn-stok-habis {
            background-color: #ddd;
            color: #999;
            border-radius: 30px;
            padding: 11px 22px;
            font-size: 13px;
            letter-spacing: 0.4px;
            border: none;
            cursor: not-allowed;
            opacity: 1;
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

            .carousel {
                margin-top: 115px;
            }

            .carousel-item {
                height: 450px;
            }

            .carousel-caption h1 {
                font-size: 35px;
            }

            .carousel-caption p {
                font-size: 14px;
            }

            .section-title {
                font-size: 32px;
            }

            .product-image-wrapper {
                height: 280px;
            }
        }

        @media (max-width: 400px) {
            .product-image-wrapper {
                height: 250px;
            }
        }
    </style>

</head>

<body>
    <?php if ($login_success != "") { ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Login Berhasil!',
        text: '<?= htmlspecialchars($login_success) ?>',
        confirmButtonText: 'Mulai Belanja',
        confirmButtonColor: ' #4f3c27'
    });

</script>

<?php } ?>
<header class="navbar-custom">
    <div class="container">
        <div class="navbar-inner">
            <a href="index.php"class="logo">
                <img src="assets/img/beadly_logo.png" alt="Beadly Accesorise">
            </a>
            <ul class="menu">
                <li><a href="index.php#produk">Semua Produk</a></li>
                <li><a href="index.php#gelang">Gelang</a></li>
                <li><a href="index.php#cincin">Cincin</a></li>
                <li><a href="index.php#kalung">Kalung</a></li>
                <li><a href="index.php#aksesoris">Gantungan Kunci</a></li>       
            </ul>

            <div class="nav-icons">
                <?php if (
                    isset($_SESSION['id_user']) &&
                    isset($_SESSION['role']) &&
                    $_SESSION['role'] === 'pelanggan'
                ) { ?>

                    <a href="profile.php" class="icon-btn" title="Profile" >
                        <i class="bi bi-person-circle"></i>
                    </a>
                <?php } else { ?>

                    <a  href="#" class="icon-btn" title="Profile"onclick="loginProfile(event)">
                        <i class="bi bi-person-circle"></i>
                    </a>
                <?php } ?>
                
                <a href="keranjang.php"class="icon-btn cart-link"title="Keranjang">
                    <svg xmlns="http://www.w3.org/2000/svg"width="20"height="20"fill="currentColor"viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1-1 0m7 0a1 1 0 1 1 0 2 1 1 0 0 1-2 0"/>
                    </svg>
                    <?php if ($jumlah_cart > 0) { ?>
                    <span class="cart-badge">
                        <?= $jumlah_cart ?>
                    </span>
                    <?php } ?>
                </a>

            </div>
        </div>
    </div>
</header>
<div id="myCarousel"class="carousel slide"data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button"data-bs-target="#myCarousel"data-bs-slide-to="0"class="active"></button>
        <button type="button"data-bs-target="#myCarousel"data-bs-slide-to="1"></button>
        <button type="button"data-bs-target="#myCarousel"data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/img/carousel1.png"alt="Beadly">
            <div class="carousel-overlay"></div>
            <div class="container">
                <div class="carousel-caption text-start pb-5"> 
                    <h1>Beadly Accessories </h1>
                    <p>Simple beads, beautiful style.</p>
                    <a  class="btn btn-light rounded-pill px-4 mt-2"href="#produk"> Lihat Produk</a>
                </div>
            </div>
        </div>

        <div class="carousel-item">
            <img src="assets/img/carousel2.png"alt="Gelang">
            <div class="carousel-overlay"></div>
            <div class="container">
                <div class="carousel-caption pb-5">
                    <h1> Accessories Collection</h1>
                    <p>Temukan accessories manik-manik favoritmu.</p>
                </div>
            </div>
        </div>

        <div class="carousel-item">
            <img src="assets/img/carousel2.png"alt="Aksesoris">
            <div class="carousel-overlay"></div>
            <div class="container">
                <div class="carousel-caption text-end pb-5">
                    <h1>Pretty Little Things</h1>
                    <p>Aksesoris cantik untuk melengkapi gayamu. </p>
                    <a  class="btn btn-light rounded-pill px-4 mt-2"href="#produk">Jelajahi koleksi</a>
                </div>
            </div>
        </div>
    </div>

    <button  class="carousel-control-prev"  type="button" data-bs-target="#myCarousel"data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next"  type="button"data-bs-target="#myCarousel"data-bs-slide="next"        >
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
<div class="product-section"id="produk">
    <div class="container">
        <div class="text-center">
            <span class="section-label">
                Handmade With Love
            </span>
            <h1 class="section-title">
                Semua Produk
            </h1>
            <div class="section-line"></div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php
            $query = mysqli_query($koneksi,"SELECT * FROM produk");
            while ($data = mysqli_fetch_array($query)) {
            ?>
                <div class="col">
                    <div class="card product-card shadow-sm">
                        <div class="product-image-wrapper">
                            <img src="img/<?= htmlspecialchars($data['poto']) ?>"class="product-image"
                            alt="<?= htmlspecialchars($data['nama']) ?>">
                        </div>

                        <div class="product-body">
                            <h5 class="product-name">
                                <?= htmlspecialchars($data['nama']) ?>
                            </h5>
                            <p class="product-price">
                                Rp <?= number_format($data['harga'],0,',','.') ?>
                            </p>

                            <p class="product-description">
                                <?= htmlspecialchars($data['deskripsi']) ?>
                            </p>

                            <?php if ($data['stok'] <= 0) { ?>
                                <span class="stok-habis">
                                    Stok Habis
                                </span>
                                <button  type="button"class="btn btn-stok-habis w-100 mt-3"disabled>Stok Habis</button>

                            <?php } else { ?>

                                <span class="stok-tersedia">
                                    Stok: <?= $data['stok'] ?>
                                </span>
                                <a href="detail.php?id=<?= $data['id'] ?>"class="btn btn-beadly w-100 mt-3">
                                    Lihat Detail
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="product-section"id="gelang">
    <div class="container">
        <div class="text-center">
            <span class="section-label">
                Bracelet Collection
            </span>
            <h1 class="section-title">
                Gelang
            </h1>
            <div class="section-line"></div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">

            <?php
            $query = mysqli_query($koneksi,"SELECT * FROM produk WHERE id_kategori = 1");
            while ($data = mysqli_fetch_array($query)) {?>

                <div class="col">
                    <div class="card product-card shadow-sm">
                        <div class="product-image-wrapper">
                            <img src="img/<?= htmlspecialchars($data['poto']) ?>"class="product-image"alt="<?= htmlspecialchars($data['nama']) ?>">
                        </div>

                        <div class="product-body">
                            <h5 class="product-name">
                                <?= htmlspecialchars($data['nama']) ?>
                            </h5>
                            <p class="product-price">
                                Rp<?= number_format($data['harga'],0,',','.') ?>
                            </p>
                            <p class="product-description">
                                <?= htmlspecialchars($data['deskripsi']) ?>
                            </p>
                            <?php if ($data['stok'] <= 0) { ?>
                                <span class="stok-habis">
                                    Stok Habis
                                </span>
                                <button  type="button"class="btn btn-stok-habis w-100 mt-3"disabled>Stok Habis</button>
                            <?php } else { ?>
                                <span class="stok-tersedia">
                                    Stok: <?= $data['stok'] ?>
                                </span>
                                <a  href="detail.php?id=<?= $data['id'] ?>"class="btn btn-beadly w-100 mt-3">
                                    Lihat Detail
                                </a>

                            <?php } ?>

                        </div>
                    </div>
                </div>

            <?php } ?>

        </div>
    </div>
</div>

<div class="product-section"id="cincin">
    <div class="container">
        <div class="text-center">
            <span class="section-label">
                Ring Collection
            </span>
            <h1 class="section-title">
                Cincin
            </h1>
            <div class="section-line"></div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php
            $query = mysqli_query($koneksi,"SELECT * FROM produk WHERE id_kategori = 3");
            while ($data = mysqli_fetch_array($query)) {?>
                <div class="col">
                    <div class="card product-card shadow-sm">
                        <div class="product-image-wrapper">
                            <img src="img/<?= htmlspecialchars($data['poto']) ?>"class="product-image"alt="<?= htmlspecialchars($data['nama']) ?>">
                        </div>

                        <div class="product-body">
                            <h5 class="product-name">
                                <?= htmlspecialchars($data['nama']) ?>
                            </h5>
                            <p class="product-price">
                                Rp<?= number_format($data['harga'],0,',','.') ?>
                            </p>
                            <p class="product-description">
                                <?= htmlspecialchars($data['deskripsi']) ?>
                            </p>
                            <?php if ($data['stok'] <= 0) { ?>
                                <span class="stok-habis">
                                    Stok Habis
                                </span>
                                <button  type="button"class="btn btn-stok-habis w-100 mt-3" disabled>Stok Habis</button>n>
                            <?php } else { ?>
                                <span class="stok-tersedia">
                                    Stok: <?= $data['stok'] ?>
                                </span>
                                <a href="detail.php?id=<?= $data['id'] ?>"class="btn btn-beadly w-100 mt-3">Lihat Detail</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="product-section"id="kalung">
    <div class="container">
        <div class="text-center">
            <span class="section-label">
                Necklace Collection
            </span>
            <h1 class="section-title">
                Kalung
            </h1>
            <div class="section-line"></div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php
            $query = mysqli_query($koneksi,"SELECT * FROM produk WHERE id_kategori = 2");
            while ($data = mysqli_fetch_array($query)) {?>
                <div class="col">
                    <div class="card product-card shadow-sm">
                        <div class="product-image-wrapper">
                            <img src="img/<?= htmlspecialchars($data['poto']) ?>"class="product-image"alt="<?= htmlspecialchars($data['nama']) ?>">
                        </div>

                        <div class="product-body">
                            <h5 class="product-name">
                                <?= htmlspecialchars($data['nama']) ?>
                            </h5>
                            <p class="product-price">
                                Rp<?= number_format($data['harga'],0,',','.') ?>
                            </p>
                            <p class="product-description">
                                <?= htmlspecialchars($data['deskripsi']) ?>
                            </p>
                            <?php if ($data['stok'] <= 0) { ?>
                                <span class="stok-habis">
                                    Stok Habis
                                </span>
                                <button  type="button"class="btn btn-stok-habis w-100 mt-3" disabled>Stok Habis</button>n>
                            <?php } else { ?>
                                <span class="stok-tersedia">
                                    Stok: <?= $data['stok'] ?>
                                </span>
                                <a href="detail.php?id=<?= $data['id'] ?>"class="btn btn-beadly w-100 mt-3">Lihat Detail</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="product-section"id="cincin">
    <div class="container">
        <div class="text-center">
            <span class="section-label">
                Ring Collection
            </span>
            <h1 class="section-title">
                Cincin
            </h1>
            <div class="section-line"></div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php
            $query = mysqli_query($koneksi,"SELECT * FROM produk WHERE id_kategori = 3");
            while ($data = mysqli_fetch_array($query)) {?>
                <div class="col">
                    <div class="card product-card shadow-sm">
                        <div class="product-image-wrapper">
                            <img src="img/<?= htmlspecialchars($data['poto']) ?>"class="product-image"alt="<?= htmlspecialchars($data['nama']) ?>">
                        </div>

                        <div class="product-body">
                            <h5 class="product-name">
                                <?= htmlspecialchars($data['nama']) ?>
                            </h5>
                            <p class="product-price">
                                Rp<?= number_format($data['harga'],0,',','.') ?>
                            </p>
                            <p class="product-description">
                                <?= htmlspecialchars($data['deskripsi']) ?>
                            </p>
                            <?php if ($data['stok'] <= 0) { ?>
                                <span class="stok-habis">
                                    Stok Habis
                                </span>
                                <button  type="button"class="btn btn-stok-habis w-100 mt-3" disabled>Stok Habis</button>n>
                            <?php } else { ?>
                                <span class="stok-tersedia">
                                    Stok: <?= $data['stok'] ?>
                                </span>
                                <a href="detail.php?id=<?= $data['id'] ?>"class="btn btn-beadly w-100 mt-3">Lihat Detail</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="product-section"id="aksesoris">
    <div class="container">
        <div class="text-center">
            <span class="section-label">
                Keychain
            </span>
            <h1 class="section-title">
                Gantungan Kunci
            </h1>
            <div class="section-line"></div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php
            $query = mysqli_query($koneksi,"SELECT * FROM produk WHERE id_kategori = 4");
            while ($data = mysqli_fetch_array($query)) {?>
                <div class="col">
                    <div class="card product-card shadow-sm">
                        <div class="product-image-wrapper">
                            <img src="img/<?= htmlspecialchars($data['poto']) ?>"class="product-image"alt="<?= htmlspecialchars($data['nama']) ?>">
                        </div>

                        <div class="product-body">
                            <h5 class="product-name">
                                <?= htmlspecialchars($data['nama']) ?>
                            </h5>
                            <p class="product-price">
                                Rp<?= number_format($data['harga'],0,',','.') ?>
                            </p>
                            <p class="product-description">
                                <?= htmlspecialchars($data['deskripsi']) ?>
                            </p>
                            <?php if ($data['stok'] <= 0) { ?>
                                <span class="stok-habis">
                                    Stok Habis
                                </span>
                                <button  type="button"class="btn btn-stok-habis w-100 mt-3" disabled>Stok Habis</button>n>
                            <?php } else { ?>
                                <span class="stok-tersedia">
                                    Stok: <?= $data['stok'] ?>
                                </span>
                                <a href="detail.php?id=<?= $data['id'] ?>"class="btn btn-beadly w-100 mt-3">Lihat Detail</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>


<footer class="footer">
    <div class="container text-center">
        <img src="assets/img/beadly_logo.png"width="110"class="mb-3"alt="Beadly">
        <p>© 2026–2027 Beadly Accesorise</p>
    </div>
</footer>

<script src="assets/dist/js/bootstrap.bundle.min.js"></script>

<script>
function loginProfile(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Login Diperlukan',
        text: 'Kamu harus login terlebih dahulu untuk melihat profile.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Login Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#4d3326',
        cancelButtonColor: '#aaa'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'login.php';
        }
    });
}
</script>
</body>
</html>