<?php
session_start();
include 'koneksi.php';

$login_success = "";
$reset_password_success = "";
$reset_password_error = "";

if (
    !isset($_SESSION['admin'], $_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['login_success'])) {
    $login_success = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}

if (isset($_SESSION['reset_password_success'])) {
    $reset_password_success = $_SESSION['reset_password_success'];
    unset($_SESSION['reset_password_success']);
}

if (isset($_SESSION['reset_password_error'])) {
    $reset_password_error = $_SESSION['reset_password_error'];
    unset($_SESSION['reset_password_error']);
}

if (isset($_POST['tambah'])) {

    $id = $_POST['id'] ?? '';

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama'] ?? ''
    );

    $harga = (int) ($_POST['harga'] ?? 0);
    $stok = (int) ($_POST['stok'] ?? 0);
    $kategori = (int) ($_POST['kategori'] ?? 0);

    $deskripsi = mysqli_real_escape_string(
        $koneksi,
        $_POST['deskripsi'] ?? ''
    );

    $nama_foto = '';

    if (
        isset($_FILES['poto']) &&
        !empty($_FILES['poto']['name']) &&
        $_FILES['poto']['error'] === UPLOAD_ERR_OK
    ) {

        $nama_file_asli = basename($_FILES['poto']['name']);
        $ekstensi = strtolower(
            pathinfo($nama_file_asli, PATHINFO_EXTENSION)
        );

        $ekstensi_diizinkan = ['jpg','jpeg','png','webp'
        ];

        if (in_array($ekstensi, $ekstensi_diizinkan)) {

            $nama_foto =
                time() . '_' .
                preg_replace(
                    '/[^a-zA-Z0-9._-]/',
                    '_',
                    $nama_file_asli
                );

            move_uploaded_file(
                $_FILES['poto']['tmp_name'],
                'img/' . $nama_foto
            );
        }
    }

    if ($id != '') {
        if ($nama_foto != '') {
            $query = "UPDATE produk SET nama='$nama',harga='$harga',stok='$stok',id_kategori='$kategori',poto='$nama_foto',deskripsi='$deskripsi'
            WHERE id='$id'";

        } else {

            $query = "UPDATE produk SET nama='$nama',harga='$harga', stok='$stok', id_kategori='$kategori', deskripsi='$deskripsi'
            WHERE id='$id'";
        }

    } else {

        $query = " INSERT INTO produk(nama, harga, stok, poto, id_kategori,deskripsi)
        VALUES('$nama','$harga','$stok','$nama_foto','$kategori','$deskripsi')";
    }
    mysqli_query($koneksi, $query);
    header("Location: dashboard.php#produk");
    exit;
}

if (isset($_GET['hapus_produk'])) {
    $id = (int) $_GET['hapus_produk'];
    mysqli_query($koneksi,"DELETE FROM produk WHERE id='$id'");
    header("Location: dashboard.php#produk");
    exit;
}

if (isset($_GET['hapus_pelanggan'])) {
    $id_pelanggan = (int) $_GET['hapus_pelanggan'];
    $cek_transaksi = mysqli_query(
        $koneksi,"SELECT COUNT(*) AS total FROM transaksi WHERE id_pelanggan='$id_pelanggan'"
    );
    $cek = mysqli_fetch_assoc($cek_transaksi);
    if (($cek['total'] ?? 0) > 0) {
        echo "
        <script>
            alert('Pelanggan tidak bisa dihapus karena masih memiliki transaksi.');
            window.location.href='dashboard.php#pelanggan';
        </script>
        ";

        exit;
    }

    mysqli_query(
        $koneksi,"DELETE FROM tb_user WHERE id='$id_pelanggan' AND role='pelanggan'"
    );

    header("Location: dashboard.php#pelanggan");
    exit;
}

if (isset($_GET['reset_password'])) {
    $id_pelanggan = (int) $_GET['reset_password'];
    $password_baru = '12345678';
    $query_reset = mysqli_query($koneksi,"UPDATE tb_user SET password='$password_baru' WHERE id='$id_pelanggan' AND role='pelanggan'");

    if ($query_reset) {
        $_SESSION['reset_password_success'] =
            $password_baru;
    } else {
        $_SESSION['reset_password_error'] =
            'Password gagal direset.';
    }

    header("Location: dashboard.php#pelanggan");
    exit;
}

if (isset($_GET['hapus_transaksi'])) {
    $id_transaksi =
        (int) $_GET['hapus_transaksi'];
    mysqli_query($koneksi,"DELETE FROM detail WHERE id_transaksi='$id_transaksi'");
    mysqli_query($koneksi,"DELETE FROM transaksi WHERE id_transaksi='$id_transaksi'");

    header("Location: dashboard.php#transaksi");
    exit;
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = (int) $_GET['edit'];
    $query_edit = mysqli_query($koneksi,"SELECT * FROM produk WHERE id='$id_edit'"
    );

    if ($query_edit) {
        $edit_data = mysqli_fetch_assoc($query_edit);
    }
}

$detail_transaksi = null;
$detail_produk_transaksi = null;
if (isset($_GET['detail_transaksi'])) {
    $id_transaksi =
        (int) $_GET['detail_transaksi'];

    $query_detail_transaksi = mysqli_query(
        $koneksi,"SELECT transaksi.id_transaksi,transaksi.tanggal,transaksi.total_harga,transaksi.metode_pembayaran,transaksi.metode_pengiriman,
        tb_user.nama,tb_user.username,tb_user.email,tb_user.hp, tb_user.alamat
         FROM transaksi
         INNER JOIN tb_user
         ON transaksi.id_pelanggan = tb_user.id
         WHERE transaksi.id_transaksi='$id_transaksi'"
    );

    if ($query_detail_transaksi) {
        $detail_transaksi =
            mysqli_fetch_assoc(
                $query_detail_transaksi
            );
    }

    $detail_produk_transaksi = mysqli_query(
        $koneksi,"SELECT detail.id_detail,detail.id_produk,detail.jumlah,
        produk.nama,produk.harga,produk.poto
        FROM detail INNER JOIN produk ON detail.id_produk = produk.id WHERE detail.id_transaksi='$id_transaksi'"
    );
}

$query_pelanggan = mysqli_query(
    $koneksi,"SELECT COUNT(*) AS total FROM tb_user WHERE role='pelanggan'"
);

$data_pelanggan =
    mysqli_fetch_assoc($query_pelanggan);

$query_transaksi = mysqli_query(
    $koneksi,"SELECT COUNT(*) AS total FROM transaksi"
);

$data_transaksi =
    mysqli_fetch_assoc($query_transaksi);

$data_pelanggan_list = mysqli_query(
    $koneksi,"SELECT id,nama,username,email,hp FROM tb_user WHERE role='pelanggan'ORDER BY id DESC LIMIT 5"
);

$data_transaksi_list = mysqli_query(
    $koneksi,"SELECT transaksi.id_transaksi,transaksi.tanggal,transaksi.total_harga,transaksi.metode_pembayaran,transaksi.metode_pengiriman,tb_user.nama
     FROM transaksi
     INNER JOIN tb_user ON transaksi.id_pelanggan = tb_user.id
     ORDER BY transaksi.id_transaksi DESC
     LIMIT 5"
);

?>

<!doctype html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta name="viewport"content="width=device-width, initial-scale=1">
    <title>Dashboard - Beadly</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="assets/dist/css/bootstrap.min.css"rel="stylesheet">
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect"href="https://fonts.gstatic.com"crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@500;600&display=swap"rel="stylesheet">

    <style>

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: #fff;
            color: #222;
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Playfair Display', serif;
        }

        .navbar {
            height: 65px;
            background: rgba(255, 255, 255, .96) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid #eee !important;
            box-shadow:0 2px 15px rgba(88, 195, 21, 0.03) !important;
            z-index: 1050;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 23px !important;
            font-weight: 600;
            color: #222 !important;
            letter-spacing: .4px;
        }

        .navbar-toggler {
            border: none;
            padding: 8px;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .sidebar {
            background: #fff !important;
            border-right: 1px solid #eee !important;
            box-shadow: none !important;
            top: 65px;
            min-height:calc(100vh - 65px) !important;
        }

        .sidebar .offcanvas {
            background: #fff !important;
        }

        .sidebar .offcanvas-header {
            border-bottom:
            1px solid #f1f1f1;
            padding: 20px;
        }

        .sidebar .offcanvas-title {
            font-family:'Playfair Display', serif;
            font-size: 21px;
            font-weight: 600;
            color: #222;
        }

        .sidebar .offcanvas-body {
            padding: 22px 18px !important;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            color: #666;
            font-size: 13px;
            font-weight: 500;
            padding: 12px 15px;
            margin-bottom: 6px;
            border-radius: 11px;
            transition:all .3s ease;
        }

        .sidebar .nav-link i {
            color: #999;
            font-size: 17px;
            transition:all .3s ease;
        }

        .sidebar .nav-link:hover {
            color: #b89a7a;
            background: #faf7f3;
            transform:translateX(3px);
        }

        .sidebar .nav-link:hover i {
            color: #b89a7a;
        }

        .sidebar .nav-link.active {
            color: #fff !important;
            background: #111 !important;
        }

        .sidebar .nav-link.active i {
            color: #fff !important;
        }

        .sidebar hr {
            border-color: #eee;
            margin: 20px 5px;
        }

        main {
            background: #fff;
            padding-top: 25px;
        }

        .content-section {
            scroll-margin-top: 85px;
        }

        #dashboard {
            padding-top: 35px !important;
        }

        #produk,
        #pelanggan,
        #transaksi {
            padding-top: 20px;
        }

        .dashboard-title {
            color: #222;
            font-size: 38px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .section-title {
            position: relative;
            color: #222;
            font-size: 31px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .section-title::after {
            content: "";
            display: block;
            width: 45px;
            height: 2px;
            background: #b89a7a;
            margin-top: 10px;
        }

        .text-muted {
            color: #888 !important;
            font-size: 13px;
        }

        .card {
            background: #fff !important;
            border: 1px solid #eee !important;
            border-radius: 16px !important;
            box-shadow:0 8px 30pxrgba(0, 0, 0, .035) !important;
            overflow: hidden;
        }

        .card.p-4 {
            padding: 28px !important;
            transition:
                all .35s ease;
        }

        .card.p-4:hover {
            transform:
                translateY(-6px);

            box-shadow:
                0 18px 40px
                rgba(0, 0, 0, .08) !important;
        }

        .card.p-4 p {
            color: #999 !important;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .card.p-4 h2 {
            font-family: 'Inter', sans-serif;
            font-size: 31px;
            font-weight: 600;
            color: #222;
        }

        .card.p-4 .fs-2 {
            color: #b89a7a !important;
            opacity: .9;
        }

        .card-body {
            padding: 25px;
        }

        .card h5 {
            color: #222;
            font-size: 21px;
            font-weight: 500;
        }

        /* =========================
           FORM
        ========================= */

        .form-label {
            color: #444;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            min-height: 46px;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            background: #fafafa;
            color: #333;
            font-size: 13px;
            padding: 10px 14px;
            transition:all .3s ease;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .form-control:hover,
        .form-select:hover {
            border-color: #d8d8d8;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #b89a7a;
            background: #fff;
            box-shadow:0 0 0 3px rgba(184, 154, 122, .12);
            outline: none;
        }

        textarea.form-control {
            min-height: 105px;
            padding-top: 12px;
        }

        input[type="file"] {
            padding: 9px 12px;
        }

        input[type="file"]::file-selector-button {
            border: none;
            background: #f5f1ed;
            color: #555;
            border-radius: 7px;
            padding: 6px 12px;
            margin-right: 10px;
            font-size: 12px;
            transition: .3s;
        }

        input[type="file"]::file-selector-button:hover {
            background: #b89a7a;
            color: #fff;
        }

        .btn {
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
            transition:all .3s ease;
        }

        .btn-dark {
            background: #111 !important;
            border-color: #111 !important;
            padding: 10px 22px;
        }

        .btn-dark:hover {
            background: #b89a7a !important;
            border-color: #b89a7a !important;
            transform:translateY(-2px);
            box-shadow:0 8px 18px rgba(184, 154, 122, .22);
        }

        .btn-outline-dark {
            border-color: #ddd;
            color: #444;
            background: #fff;
            padding: 7px 15px;
        }

        .btn-outline-dark:hover {
            background: #111;
            border-color: #111;
            color: #fff;
            transform:translateY(-1px);
        }

        .btn-outline-secondary {
            border-color: #ddd;
            color: #666;
            background: #fff;
            padding: 10px 20px;
        }

        .btn-outline-secondary:hover {
            background: #f5f1ed;
            border-color: #d8c5b1;
            color: #8d735b;
        }

        .btn-outline-danger {
            border-color: #ead4d4;
            color: #a55;
            background: #fff;
            padding: 7px 15px;
        }

        .btn-outline-danger:hover {
            background: #a55;
            border-color: #a55;
            color: #fff;
            transform: translateY(-1px);
        }

        .table-responsive {
            border-radius: 11px;
        }

        .table {
            margin-bottom: 0;
            color: #444;
            font-size: 13px;
        }

        .table thead th {
            background: #faf8f5;
            color: #777;
            border-bottom:1px solid #eee;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .8px;
            text-transform: uppercase;
            padding: 15px 12px;
        }

        .table tbody td {
            padding: 15px 12px;
            border-color: #f1f1f1;
        }

        .table-hover tbody tr {
            transition:all .2s ease;
        }

        .table-hover tbody tr:hover {
            background: #fdfbf9;
        }

        .table th {
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .product-img {
            width: 58px;
            height: 58px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #eee;
            background: #fafafa;
            transition:all .3s ease;
        }

        .product-img:hover {
            transform:scale(1.06);
            box-shadow:0 5px 15pxrgba(0, 0, 0, .10);
        }

        .money {
            white-space: nowrap;
            color: #b89a7a;
            font-weight: 600;
        }

        .detail-box {
            padding: 15px;
            background: #faf8f5;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .detail-box small {
            display: block;
            margin-bottom: 5px;
            font-size: 11px;
        }

        @media (max-width: 767px) {

            .navbar {
                height: 60px;
            }

            .navbar-brand {
                font-size: 21px !important;
            }

            .sidebar {
                border-right: 0 !important;

                top: 0;
            }

            .sidebar .offcanvas {
                width: 280px;
            }

            main {
                margin-left: 0 !important;
                padding-top: 15px;
            }

            .dashboard-title {
                font-size: 30px;
            }

            .section-title {
                font-size: 27px;
            }

            .card-body {
                padding: 18px;
            }

            .card.p-4 {
                padding: 22px !important;
            }

            .table {
                font-size: 12px;
            }

            .table thead th {
                font-size: 9px;
            }

            .product-img {
                width: 50px;

                height: 50px;
            }

            .content-section {
                scroll-margin-top: 20px;
            }

            .btn {
                font-size: 12px;
            }
        }

    </style>

</head>

<body>

<?php if ($reset_password_success != ""): ?>
<script>

Swal.fire({
    icon: 'success',
    title: 'Password Berhasil Direset!',
    html: 'Password baru pelanggan adalah <b><?= htmlspecialchars($reset_password_success) ?></b>',
    confirmButtonText: 'OK',
    confirmButtonColor: '#111'
});

</script>

<?php endif; ?>
<?php if ($reset_password_error != ""): ?>

<script>

Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '<?= htmlspecialchars($reset_password_error) ?>',
    confirmButtonColor: '#111'
});

</script>

<?php endif; ?>

<?php if ($login_success != ""): ?>

<script>

Swal.fire({
    icon: 'success',
    title: 'Login Berhasil!',
    text: '<?= htmlspecialchars($login_success) ?>',
    confirmButtonText: 'Lanjutkan',
    confirmButtonColor: '#111'
});

</script>

<?php endif; ?>

<script>

function konfirmasiLogout(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Yakin ingin logout?',
        text: 'Kamu akan keluar dari akun admin.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#111',
        cancelButtonColor: '#b89a7a'

    }).then((result) => {

        if (result.isConfirmed) {
            window.location.href =
                'logout.php';

        }

    });
}

</script>

<header class="navbar sticky-top bg-white border-bottom p-0 shadow-sm">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="#dashboard">Beadly</a>
    <div class="d-md-none">
        <button class="navbar-toggler me-2"type="button"data-bs-toggle="offcanvas"data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>

</header>
<div class="container-fluid">
    <div class="row">
        <div class="sidebar col-md-3 col-lg-2 p-0 position-fixed"style="min-height:100vh;">
            <div class="offcanvas-md offcanvas-end bg-white"tabindex="-1"id="sidebarMenu">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">Beadly</h5>
                    <button type="button"class="btn-close"data-bs-dismiss="offcanvas"data-bs-target="#sidebarMenu"></button>
                </div>

                <div class="offcanvas-body d-md-flex flex-column p-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active"href="#dashboard"><i class="bi bi-house me-2"></i>Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active"href="#produk"><i class="bi bi-box-seam me-2"></i>Produk</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#pelanggan"><i class="bi bi-people me-2"></i>Pelanggan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"href="#transaksi"><i class="bi bi-receipt me-2"></i>Transaksi</a>
                        </li>
                    </ul>

                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link"  href="logout.php"onclick="konfirmasiLogout(event)">
                                <i class="bi bi-box-arrow-right me-2"></i>Sign out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4"style="margin-left:16.666667%;">
            <section id="dashboard"class="content-section pt-4 pb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 dashboard-title">Dashboard</h1>
                        <p class="text-muted">Selamat datang di halaman admin Beadly.</p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Total Pelanggan </p>
                                    <h2 class="mb-0"><?= $data_pelanggan['total'] ?? 0; ?></h2>
                                </div>
                                <div class="fs-2 text-secondary">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Total Transaksi</p>
                                    <h2 class="mb-0"><?= $data_transaksi['total'] ?? 0; ?></h2>
                                </div>

                                <div class="fs-2 text-secondary">
                                    <i class="bi bi-receipt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </section>
            <section id="produk"class="content-section pb-5">
                <div class="mb-3">
                    <h2 class="section-title mb-1"> Data Produk</h2>
                    <p class="text-muted">Kelola produk Beadly.</p>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4">
                            <?= $edit_data? 'Edit Produk': 'Tambah Produk'?>
                        </h5>
                        <form action=""method="POST"enctype="multipart/form-data">
                            <input type="hidden"name="id"value="<?= $edit_data['id'] ?? ''; ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text"name="nama"class="form-control"required value="<?= htmlspecialchars($edit_data['nama'] ?? ''); ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Harga</label>
                                    <input type="number"name="harga"class="form-control"required min="0"value="<?= $edit_data['harga'] ?? ''; ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label"> Stok</label>
                                    <input type="number"name="stok"class="form-control"required min="0" value="<?= $edit_data['stok'] ?? ''; ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-select"required>
                                        <option value="">Pilih Kategori</option>
                                        <?php
                                        $result_kategori =mysqli_query($koneksi,"SELECT * FROM tb_kategori ORDER BY id_kategori ASC");
                                        while ( $list =mysqli_fetch_assoc($result_kategori) 
                                        ):
                                        ?>
                                            <option  value="<?= $list['id_kategori']; ?>"
                                                <?= (isset($edit_data['id_kategori']) && $edit_data['id_kategori'] == $list['id_kategori'] )? 'selected': '';?>>
                                                <?= htmlspecialchars(
                                                    $list['nama_kategori']
                                                ); ?>
                                            </option>
                                        <?php endwhile; ?>

                                    </select>

                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Foto Produk</label>
                                    <input type="file"name="poto"class="form-control"accept=".jpg,.jpeg,.png,.webp">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi"class="form-control"rows="3">
                                    <?= htmlspecialchars($edit_data['deskripsi'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <button type="submit"name="tambah"class="btn btn-dark">
                                <?= $edit_data
                                    ? 'Update Produk'
                                    : 'Tambah Produk';
                                ?>
                            </button>

                            <?php if ($edit_data): ?>
                                <a href="dashboard.php#produk"class="btn btn-outline-secondary">  
                                    Batal
                                </a>

                            <?php endif; ?>
                        </form>

                    </div>

                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Foto</th>
                                        <th>Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $no_produk = 1;
                                    $hasil_produk =mysqli_query($koneksi,"SELECT produk.*,tb_kategori.nama_kategori FROM produk
                                    LEFT JOIN tb_kategori ON produk.id_kategori = tb_kategori.id_kategori ORDER BY produk.id DESC");
                                    if ($hasil_produk && mysqli_num_rows($hasil_produk) > 0):
                                        while ($produk = mysqli_fetch_assoc($hasil_produk)):
                                    ?>

                                    <tr>
                                        <td>
                                            <?= $no_produk++; ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $produk['nama']
                                            ); ?>
                                        </td>

                                        <td class="money">Rp<?= number_format($produk['harga'],0,',','.'); ?></td>

                                        <td>
                                            <?= $produk['stok'] ?? 0; ?>
                                        </td>

                                        <td>

                                            <?php if (
                                                !empty($produk['poto']) &&
                                                file_exists(
                                                    'img/' .
                                                    $produk['poto']
                                                )
                                            ): ?>

                                                <img src="img/<?= htmlspecialchars($produk['poto']); ?>"
                                                    class="product-img"
                                                    alt="<?= htmlspecialchars($produk['nama']); ?>">

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    Tidak ada
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td>
                                            <?= htmlspecialchars(
                                                $produk['nama_kategori'] ?? '-'
                                            ); ?>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars(
                                                $produk['deskripsi'] ?? '-'
                                            ); ?>
                                        </td>


                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="dashboard.php?edit=<?= $produk['id']; ?>#produk"class="btn btn-sm btn-outline-dark">
                                                    Edit
                                                </a>

                                                <a href="dashboard.php?hapus_produk=<?= $produk['id']; ?>#produk"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                                    Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <?php
                                        endwhile;
                                    else: ?>

                                    <tr>
                                        <td colspan="8"class="text-center text-muted py-4">
                                            Belum ada data produk.
                                        </td>
                                    </tr>

                                    <?php endif; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section id="pelanggan"class="content-section pb-5">
                <div class="mb-3">
                    <h2 class="section-title mb-1">
                        Data Pelanggan
                    </h2>
                    <p class="text-muted">
                        Data pelanggan Beadly.
                    </p>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>No. HP</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $no_pelanggan = 1;
                                    if (
                                        $data_pelanggan_list &&
                                        mysqli_num_rows(
                                            $data_pelanggan_list
                                        ) > 0
                                    ):

                                        while (
                                            $pelanggan =
                                            mysqli_fetch_assoc(
                                                $data_pelanggan_list
                                            )
                                        ):

                                    ?>

                                    <tr>
                                        <td>
                                            <?= $no_pelanggan++; ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $pelanggan['nama']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $pelanggan['username']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $pelanggan['email']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $pelanggan['hp']); ?>
                                        </td>

                                        <td>

                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="dashboard.php?reset_password=<?= $pelanggan['id']; ?>#pelanggan"
                                                    class="btn btn-sm btn-outline-dark"
                                                    onclick="return confirm('Yakin ingin mereset password pelanggan ini?')">
                                                    Reset Password
                                                </a>

                                                <a href="dashboard.php?hapus_pelanggan=<?= $pelanggan['id']; ?>#pelanggan"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                                    Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                        endwhile;
                                    else:
                                    ?>
                                    <tr>
                                        <td colspan="6"class="text-center text-muted py-4">
                                            Belum ada data pelanggan.
                                        </td>
                                    </tr>

                                    <?php endif; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section id="transaksi"class="content-section pb-5">
                <div class="mb-3">
                    <h2 class="section-title mb-1">Data Transaksi</h2>
                    <p class="text-muted">Data transaksi Beadly.</p>
                </div>

                <?php if ($detail_transaksi): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Detail Transaksi#<?= $detail_transaksi['id_transaksi']; ?></h5>
                            <div class="d-flex gap-2">
                                <a href="cetak.php?id=<?= $detail_transaksi['id_transaksi']; ?>"target="_blank"class="btn btn-sm btn-dark">
                                    <i class="bi bi-printer me-1"></i>
                                    Cetak Invoice
                                </a>
                                <a href="dashboard.php#transaksi"class="btn btn-sm btn-outline-secondary">
                                    Tutup
                                </a>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="detail-box">
                                    <small class="text-muted">Nama Pelanggan</small>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(
                                            $detail_transaksi['nama']
                                        ); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-box">
                                    <small class="text-muted">Tanggal</small>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(
                                            $detail_transaksi['tanggal']
                                        ); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-box">
                                    <small class="text-muted"> Username</small>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(
                                            $detail_transaksi['username']
                                        ); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-box">
                                    <small class="text-muted"> No. HP </small>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(
                                            $detail_transaksi['hp']
                                        ); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="detail-box">
                                    <small class="text-muted">Alamat</small>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(
                                            $detail_transaksi['alamat']
                                        ); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-box">
                                    <small class="text-muted">Metode Pembayaran</small>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(
                                            $detail_transaksi['metode_pembayaran']
                                        ); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-box">
                                    <small class="text-muted">Metode Pengiriman</small>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(
                                            $detail_transaksi['metode_pengiriman']
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="mb-3">Produk yang Dibeli</h6>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $no_detail = 1;
                                    if (
                                        $detail_produk_transaksi &&
                                        mysqli_num_rows(
                                            $detail_produk_transaksi
                                        ) > 0
                                    ):

                                        while (
                                            $detail =
                                            mysqli_fetch_assoc(
                                                $detail_produk_transaksi
                                            )
                                        ):

                                            $subtotal =
                                                $detail['harga'] *
                                                $detail['jumlah'];

                                    ?>
                                    <tr>
                                        <td>
                                            <?= $no_detail++; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if (
                                                    !empty($detail['poto']) &&
                                                    file_exists(
                                                        'img/' .
                                                        $detail['poto']
                                                    )
                                                ): ?>
                                                    <img src="img/<?= htmlspecialchars($detail['poto']); ?>"
                                                        class="product-img"alt="<?= htmlspecialchars($detail['nama']); ?>">

                                                <?php endif; ?>

                                                <span>
                                                    <?= htmlspecialchars(
                                                        $detail['nama']
                                                    ); ?>
                                                </span>
                                            </div>
                                        </td>

                                        <td>Rp<?= number_format($detail['harga'],0,',','.'); ?></td>                                                                  
                                        <td><?= $detail['jumlah']; ?></td>
                                        <td>Rp<?= number_format($subtotal,0,',','.'); ?> </td>

                                    </tr>

                                    <?php
                                        endwhile;
                                    else:?>

                                    <tr>
                                        <td colspan="5"class="text-center text-muted py-4">Tidak ada detail produk.</td>
                                    </tr>
                                    <?php endif; ?>

                                </tbody>

                                <tfoot>

                                    <tr>
                                        <th colspan="4" class="text-end">Total</th>
                                        <th>Rp<?= number_format($detail_transaksi['total_harga'],0,',','.'); ?></th>
                                    </tr>

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">

                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Transaksi</th>
                                        <th>Pelanggan</th>
                                        <th>Tanggal</th>
                                        <th>Total Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    $no_transaksi = 1;
                                    if ($data_transaksi_list && mysqli_num_rows(
                                        $data_transaksi_list) > 0
                                    ):
                                        while ($transaksi =mysqli_fetch_assoc(
                                            $data_transaksi_list)
                                        ):
                                    ?>

                                    <tr>

                                        <td><?= $no_transaksi++; ?></td>                                       
                                        <td>#<?= $transaksi['id_transaksi']; ?></td>                                                                                    
                                        <td><?= htmlspecialchars($transaksi['nama']); ?></td>
                                        <td><?= htmlspecialchars($transaksi['tanggal']); ?></td>
                                        <td class="money">Rp<?= number_format($transaksi['total_harga'],0,',','.'); ?></td>
                                        <td>

                                            <div class="d-flex flex-wrap gap-1">

                                                <a href="dashboard.php?detail_transaksi=<?= $transaksi['id_transaksi']; ?>#transaksi"
                                                    class="btn btn-sm btn-outline-dark">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>

                                                <a href="cetak.php?id=<?= $transaksi['id_transaksi']; ?>"
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-dark">
                                                    <i class="bi bi-printer me-1"></i>Cetak Invoice
                                                </a>

                                                <a href="dashboard.php?hapus_transaksi=<?= $transaksi['id_transaksi']; ?>#transaksi"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
                                                    <i class="bi bi-trash me-1"></i>Hapus
                                                </a>

                                            </div>
                                        </td>

                                    </tr>

                                    <?php
                                    endwhile;
                                else:
                                    ?>

                                    <tr>
                                        <td colspan="6"class="text-center text-muted py-4"> Belum ada transaksi.</td>
                                    </tr>

                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>


<script src="assets/dist/js/bootstrap.bundle.min.js"></script>
<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const links =
            document.querySelectorAll(
                ".sidebar .nav-link[href^='#']"
            );

        links.forEach(
            function (link) {

                link.addEventListener(
                    "click",
                    function () {

                        links.forEach(
                            function (item) {
                                item.classList.remove(
                                    "active"
                                );

                            }
                        );

                        this.classList.add(
                            "active"
                        );
                    }
                );
            }
        );
    }
);

</script>

</body>
</html>