<?php
session_start();
$profile_success = "";

if (isset($_SESSION['profile_success'])) {
    $profile_success = $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}

include 'koneksi.php';

if (!isset($_SESSION['id_user'], $_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$jumlah_cart = 0;

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $jumlah_cart += $item['jumlah'];
    }
}

$query_user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id='$id_user'");
$data = mysqli_fetch_assoc($query_user);

if (!$data) {
    echo "<script>
            alert('Data profile tidak ditemukan');
            window.location='index.php';
          </script>";
    exit;
}
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
if (isset($_POST['simpan'])) {
    $id = $data['id'];

    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $hp = mysqli_real_escape_string($koneksi, $_POST['hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    $cek_username = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username' AND id != '$id'");

    if (mysqli_num_rows($cek_username) > 0) {
        echo "<script>
                alert('Username sudah digunakan oleh pengguna lain');
                window.location='profile.php?aksi=edit';
              </script>";
        exit;
    }

    if (!empty($password_baru)) {
        if ($password_lama !== $data['password']) {
            echo "<script>
                    alert('Password lama salah.');
                    window.location='profile.php?aksi=edit';
                  </script>";
            exit;
        }

        if ($password_baru !== $konfirmasi_password) {
            echo "<script>
                    alert('Konfirmasi password baru tidak sesuai.');
                    window.location='profile.php?aksi=edit';
                  </script>";
            exit;
        }

        $password_query = ", password='$password_baru'";
    } else {
        $password_query = "";
    }

    $update = mysqli_query($koneksi, "UPDATE tb_user SET nama='$nama',email='$email',username='$username',hp='$hp',alamat='$alamat'$password_query 
    WHERE id='$id'");

    if ($update) {
        $_SESSION['username'] = $username;
        $_SESSION['nama'] = $nama;
        $_SESSION['profile_success'] = "Profile berhasil diperbarui ✨";

        header("Location: profile.php");
        exit;
    } else {
        echo "<script>
                alert('Profile gagal diperbarui');
                window.location='profile.php?aksi=edit';
              </script>";
        exit;
    }
}
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile - Beadly</title>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            font-family: 'Inter', sans-serif;
            background-color: #fff;
            color: #222;
        }

        h1,
        h2,
        h3,
        h4,
        h5 {
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

        .menu a.active {
            color: #b89a7a;
        }

        .menu a.active::after {
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

        .profile-section {
            padding: 70px 0 80px;
            min-height: 100vh;
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
            margin: 18px auto 45px;
        }

        .profile-card {
            max-width: 1000px;
            margin: auto;
            border: 1px solid #eeeeee;
            border-radius: 20px;
            background: #fff;
            overflow: hidden;
            transition: 0.4s;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .profile-card:hover {
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.08);
        }

        .profile-header {
            text-align: center;
            padding: 35px 25px 25px;
            border-bottom: 1px solid #eeeeee;
        }

        .profile-icon {
            width: 80px;
            height: 80px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #f8f1eb;
            color: #b89a7a;
            font-size: 35px;
        }

        .profile-name {
            font-family: 'Playfair Display', serif;
            font-size: 25px;
            margin-top: 15px;
            margin-bottom: 4px;
        }

        .profile-role {
            color: #b89a7a;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .profile-body {
            padding: 30px;
        }

        .profile-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            margin-bottom: 12px;
            background-color: #fafafa;
            border: 1px solid #eeeeee;
            border-radius: 14px;
        }

        .profile-item-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background-color: #f8f1eb;
            color: #b89a7a;
            font-size: 18px;
        }

        .profile-label {
            color: #999;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .profile-value {
            color: #222;
            font-size: 14px;
            font-weight: 500;
            word-break: break-word;
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

        .btn-outline-beadly {
            background-color: transparent;
            color: #222;
            border: 1px solid #ddd;
            border-radius: 30px;
            padding: 10px 22px;
            font-size: 13px;
            transition: 0.3s;
        }

        .btn-outline-beadly:hover {
            background-color: #b89a7a;
            border-color: #b89a7a;
            color: #fff;
        }

        .btn-logout {
            background-color: transparent;
            color: #222;
            border: 1px solid #ddd;
            border-radius: 30px;
            padding: 10px 22px;
            font-size: 13px;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background-color: #b89a7a;
            border-color: #b89a7a;
            color: #fff;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 7px;
        }

        .form-control {
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 13px;
            background-color: #fafafa;
        }

        .form-control:focus {
            border-color: #b89a7a;
            box-shadow: 0 0 0 3px rgba(184, 154, 122, 0.12);
            background-color: #fff;
        }

        .transaction-section {
            padding: 80px 0;
            background-color: #fafafa;
            border-top: 1px solid #eeeeee;
        }

        .transaction-card {
            background-color: #fff;
            border: 1px solid #eeeeee;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .transaction-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        }

        .transaction-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #eeeeee;
        }

        .transaction-id {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: #222;
        }

        .transaction-date {
            font-size: 12px;
            color: #888;
        }

        .transaction-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .transaction-label {
            display: block;
            color: #999;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .transaction-value {
            font-size: 14px;
            font-weight: 600;
            color: #222;
        }

        .transaction-action {
            margin-top: 18px;
            padding-top: 15px;
            border-top: 1px solid #eeeeee;
            text-align: right;
        }

        .btn-invoice {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background-color: #4f3c27;
            color: #fff;
            text-decoration: none;
            border-radius: 30px;
            padding: 9px 17px;
            font-size: 12px;
            transition: 0.3s;
        }

        .btn-invoice:hover {
            background-color: #b89a7a;
            color: #fff;
        }

        .empty-transaction {
            text-align: center;
            padding: 45px 20px;
            background-color: #fff;
            border: 1px solid #eeeeee;
            border-radius: 18px;
            color: #888;
        }

        .empty-transaction i {
            font-size: 35px;
            color: #b89a7a;
        }

        .empty-transaction h5 {
            margin-top: 12px;
            color: #555;
        }

        .empty-transaction p {
            font-size: 13px;
        }

        .footer-beadly {
            background-color: #fff;
            border-top: 1px solid #eeeeee;
            padding: 55px 0;
        }

        .footer-beadly p {
            color: #888;
            font-size: 13px;
        }

        .back-top {
            color: #222;
            text-decoration: none;
            font-size: 13px;
        }

        .back-top:hover {
            color: #b89a7a;
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

            .profile-section {
                padding-top: 50px;
            }

            .section-title {
                font-size: 32px;
            }

            .profile-body {
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            .transaction-info {
                grid-template-columns: 1fr;
            }

            .transaction-top {
                display: block;
            }

            .transaction-date {
                display: block;
                margin-top: 5px;
            }

            .btn-invoice {
                width: 100%;
                justify-content: center;
            }

            .btn-logout {
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>

<script>
function konfirmasiLogout(event) {
    event.preventDefault();

    Swal.fire({
        title: 'Yakin ingin logout?',
        text: 'Kamu akan keluar dari akun.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#111',
        cancelButtonColor: '#b89a7a'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
}
</script>

<?php if ($profile_success != "") { ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= htmlspecialchars($profile_success) ?>',
    confirmButtonText: 'Oke',
    confirmButtonColor: '#4f3c27;'
});
</script>
<?php } ?>

<header class="navbar-custom">
    <div class="container">
        <div class="navbar-inner">
            <a href="index.php" class="logo">
                <img src="assets/img/beadly_logo.png" alt="Beadly Accesorise">
            </a>
            <ul class="menu">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
                <li><a href="#riwayat">Riwayat Transaksi</a></li>
            </ul>

            <div class="nav-icons">
                <a href="profile.php" class="icon-btn" title="Profile">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                    </svg>
                </a>

                <a href="keranjang.php" class="icon-btn cart-link" title="Keranjang">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0-2 0m-7 1a1 1 0 1 1 0 2 2 2 0 0 1-2 0m7 0a1 1 0 1 1 0 2 2 2 0 0 1-2 0" />
                    </svg>

                    <?php if ($jumlah_cart > 0) { ?>
                        <span class="cart-badge"><?= $jumlah_cart ?></span>
                    <?php } ?>
                </a>

            </div>
        </div>
    </div>
</header>
<section class="profile-section" id="profile">
    <div class="container">

        <div class="text-center">
            <span class="section-label">My Account</span>
            <h1 class="section-title">Profile Saya</h1>
            <div class="section-line"></div>
        </div>

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-icon">
                    <i class="bi bi-person"></i>
                </div>

                <h2 class="profile-name">
                    <?= htmlspecialchars($data['nama'] ?: $data['username']) ?>
                </h2>

                <div class="profile-role">
                    <?= htmlspecialchars($data['role']) ?>
                </div>
            </div>

            <div class="profile-body">
                <?php if ($aksi == "edit") { ?>
                    <h4 class="mb-4">Edit Profile</h4>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="hp" class="form-control" value="<?= htmlspecialchars($data['hp']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($data['alamat']) ?></textarea>
                        </div>
                        <div style="margin:30px 0 20px;padding-top:25px;border-top:1px solid #eeeeee;">
                            <h5 style="margin-bottom:5px;">Ubah Password</h5>
                            <p style="font-size:12px;color:#999;margin-bottom:20px;">
                                Kosongkan bagian password jika tidak ingin mengubah password.
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password baru">
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="simpan" class="btn btn-beadly">
                                <i class="bi bi-check-lg me-1"></i>
                                Simpan Perubahan
                            </button>
                            <a href="profile.php" class="btn btn-outline-beadly">
                                Batal
                            </a>
                        </div>

                    </form>

                <?php } else { ?>
                    <div class="profile-item">
                        <div class="profile-item-icon">
                            <i class="bi bi-person"></i>
                        </div>

                        <div>
                            <div class="profile-label">Nama</div>
                            <div class="profile-value">
                                <?= htmlspecialchars($data['nama'] ?: '-') ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-item-icon">
                            <i class="bi bi-person-circle"></i>
                        </div>

                        <div>
                            <div class="profile-label">Username</div>
                            <div class="profile-value">
                                <?= htmlspecialchars($data['username']) ?>
                            </div>
                        </div>
                    </div>

                    <div class="profile-item">
                        <div class="profile-item-icon">
                            <i class="bi bi-envelope"></i>
                        </div>

                        <div>
                            <div class="profile-label">Email</div>
                            <div class="profile-value">
                                <?= htmlspecialchars($data['email'] ?: '-') ?>
                            </div>
                        </div>
                    </div>

                    <div class="profile-item">
                        <div class="profile-item-icon">
                            <i class="bi bi-telephone"></i>
                        </div>

                        <div>
                            <div class="profile-label">No. HP</div>

                            <div class="profile-value">
                                <?= htmlspecialchars($data['hp'] ?: '-') ?>
                            </div>
                        </div>
                    </div>

                    <div class="profile-item">
                        <div class="profile-item-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>

                        <div>
                            <div class="profile-label">Alamat</div>
                            <div class="profile-value">
                                <?= htmlspecialchars($data['alamat'] ?: '-') ?>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="profile.php?aksi=edit" class="btn btn-beadly">
                            <i class="bi bi-pencil me-1"></i>
                            Edit Profile
                        </a>
                        <a href="logout.php" class="btn btn-logout ms-2" onclick="konfirmasiLogout(event)">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            Logout
                        </a>
                    </div>

                <?php } ?>

            </div>
        </div>
    </div>
</section>

<section class="transaction-section" id="riwayat">
    <div class="container">
        <div class="text-center">
            <span class="section-label">Order History</span>
            <h1 class="section-title">Riwayat Transaksi</h1>
            <div class="section-line"></div>
        </div>

        <?php
        $query_transaksi = mysqli_query(
            $koneksi,"SELECT * FROM transaksi WHERE id_pelanggan='$id_user' ORDER BY tanggal DESC, id_transaksi DESC"
        );

        if (mysqli_num_rows($query_transaksi) > 0) {
            while ($transaksi = mysqli_fetch_assoc($query_transaksi)) {
        ?>

                <div class="transaction-card">
                    <div class="transaction-top">
                        <div class="transaction-id">
                            Transaksi #<?= $transaksi['id_transaksi'] ?>
                        </div>
                        <div class="transaction-date">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?= date('d F Y', strtotime($transaksi['tanggal'])) ?>
                        </div>
                    </div>

                    <div class="transaction-info">
                        <div>
                            <span class="transaction-label">ID Transaksi</span>
                            <span class="transaction-value">
                                #<?= $transaksi['id_transaksi'] ?>
                            </span>
                        </div>
                        <div>
                            <span class="transaction-label">Tanggal</span>
                            <span class="transaction-value">
                                <?= date('d-m-Y', strtotime($transaksi['tanggal'])) ?>
                            </span>
                        </div>
                        <div>
                            <span class="transaction-label">Total Pembayaran</span>
                            <span class="transaction-value">
                                Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                            </span>
                        </div>
                        <div>
                            <span class="transaction-label">Detail</span>

                            <?php
                            $id_transaksi = $transaksi['id_transaksi'];
                            $query_detail = mysqli_query(
                                $koneksi,
                                "SELECT detail.jumlah,produk.nama
                                 FROM detail
                                 JOIN produk
                                 ON detail.id_produk = produk.id
                                 WHERE detail.id_transaksi='$id_transaksi'"
                            );

                            $produk_transaksi = [];

                            while ($detail = mysqli_fetch_assoc($query_detail)) {
                                $produk_transaksi[] = $detail['nama'] . ' (' . $detail['jumlah'] . ')';
                            }

                            if (!empty($produk_transaksi)) {
                                echo htmlspecialchars(implode(', ', $produk_transaksi));
                            } else {
                                echo '-';
                            }
                            ?>
                        </div>

                    </div>
                    <div class="transaction-action">
                        <a href="cetak.php?id=<?= $transaksi['id_transaksi'] ?>" target="_blank" class="btn-invoice">
                            <i class="bi bi-printer"></i>
                            Cetak Invoice
                        </a>
                    </div>
                </div>

        <?php
            }
        } else {
        ?>

            <div class="empty-transaction">
                <i class="bi bi-receipt"></i>
                <h5>Belum Ada Transaksi</h5>
                <p>Riwayat pembelian kamu akan muncul di sini.</p>
                <a href="index.php" class="btn btn-beadly mt-2">
                    Belanja Sekarang
                </a>
            </div>

        <?php } ?>

    </div>
</section>

<footer class="footer-beadly">
    <div class="container text-center">

        <img src="assets/img/beadly_logo.png" width="110" class="mb-3" alt="Beadly">
        <p>© 2026 Beadly —Simple beads, beautiful style.Handmade in Cirebon</p>
        <a href="#" class="back-top">Back to top ↑</a>

    </div>
</footer>

<script src="assets/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>