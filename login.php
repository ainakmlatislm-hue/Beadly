<?php

session_start();
$logout_success = "";

if (isset($_GET['status']) && $_GET['status'] == 'logout') {
    $logout_success = "Kamu berhasil keluar dari akun.";
}
include 'koneksi.php';
$pesan = "";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $login = mysqli_query(
        $koneksi,
        "SELECT * FROM tb_user WHERE username='$username' AND password='$password'"
    );

    if (mysqli_num_rows($login) > 0) {

        $data = mysqli_fetch_array($login);

        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == 'admin') {
        $_SESSION['admin'] = $data['username'];
        $_SESSION['login_success'] = "Selamat datang kembali, Admin ✨";

        header('Location: dashboard.php');
        exit;

        } elseif ($data['role'] == 'pelanggan') {
            $_SESSION['id_user'] = $data['id'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['username'] = $data['username'];
            $nama = $data['nama'];
            $_SESSION['login_success'] = "Selamat datang, " . $data['nama'] . " ✨";
            header('Location: index.php');
            exit;
        }

    } else {
    $pesan = "Username atau password salah.";
}
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | Beadly Accesorise</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #faf8f6;
            color: #222;
            font-family: 'Inter', sans-serif;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 30px;
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
        }

        .login-card {
            width: 1500px;
            max-width: 430px;
            background: #fff;
            padding: 45px 45px 40px;
            border: 1px solid #eee;
            border-radius: 22px;
            box-shadow:0 20px 50px rgba(0, 0, 0, .06);
            position: relative;
        }

        .brand {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand img {
            width: 105px;
            height: auto;
            margin-bottom: 18px;
        }

        .brand h1 {
            margin: 0 0 8px;
            font-size: 32px;
            font-weight: 500;
            color: #222;
        }

        .brand p {
            margin: 0;
            color: #999;
            font-size: 12px;
            line-height: 1.7;
        }

        .form-label {
            margin-bottom: 8px;
            color: #444;
            font-size: 12px;
            font-weight: 600;
        }

        .form-control {
            height: 50px;
            padding: 0 16px;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            background: #fafafa;
            color: #333;
            font-size: 13px;
            transition: .3s;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .form-control:focus {
            border-color: #b89a7a;
            background: #fff;

            box-shadow: 0 0 0 3px rgba(184,154,122,.12);

            outline: none;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .form-check-input {
            width: 15px;
            height: 15px;

            margin: 0;

            border-color: #d8d8d8;
        }

        .form-check-input:checked {
            background-color: #b89a7a;
            border-color: #b89a7a;
        }

        .form-check-label {
            color: #888;
            font-size: 11px;
        }

        .btn-login {
            height: 50px;
            border: none;
            border-radius: 30px;
            background: #4f3c27;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            transition: .3s;
        }

        .btn-login:hover {
            background: #b89a7a;
            color: #fff;

            transform: translateY(-2px);

            box-shadow:
                0 10px 25px rgba(184,154,122,.22);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 28px 0;
            color: #aaa;
            font-size: 9px;
            letter-spacing: 2px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }

        .register {
            margin: 0;
            color: #888;
            font-size: 12px;
            text-align: center;
        }

        .register a {
            color: #b89a7a;
            font-weight: 600;
            text-decoration: none;
            transition: .3s;
        }

        .register a:hover {
            color: #8f7358;
            text-decoration: underline;
        }

        .copyright {
            margin: 25px 0 0;
            color: #aaa;
            font-size: 9px;
            text-align: center;
        }

        .back-home {
            display: block;
            margin-top: 18px;
            color: #aaa;
            font-size: 10px;
            text-align: center;
            text-decoration: none;
            transition: .3s;
        }

        .back-home:hover {
            color: #b89a7a;
        }

        @media (max-width: 500px) {

            body {
                padding: 20px;
            }

            .login-card {
                padding: 35px 25px 30px;
                border-radius: 18px;
            }

            .brand h1 {
                font-size: 28px;
            }

            .brand img {
                width: 90px;
            }
        }
    </style>
</head>

<body>

    <?php if ($pesan != "") { ?>

        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '<?= $pesan ?>',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#111'
         });
        </script>

    <?php } ?>

    <?php if ($logout_success != "") { ?>

        <script>
            Swal.fire({
                icon: 'success',
                title: 'Logout Berhasil!',
                text: '<?= $logout_success ?>',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#111'
            });
        </script>

    <?php } ?>


    <div class="login-card">
        <div class="brand">
            <img src="assets/img/beadly_logo.png"
                 alt="Beadly Accesorise">
            <h1>Selamat Datang</h1>
            <p>Masuk ke akunmu untuk melanjutkan belanja di Beadly Accesorise.</p>
        </div>

        <form method="post">
            <div class="mb-3">

                <label for="username" class="form-label">Username</label>
                <input type="text"name="username"id="username"class="form-control"placeholder="Masukkan username"required>

            </div>

            <div class="mb-3">

                <label for="password" class="form-label">Password</label>
                <input type="password"name="password"id="password"class="form-control" placeholder="Masukkan password"required>

            </div>

            <div class="form-check mb-4">

                <input class="form-check-input"type="checkbox"id="remember">
                <label class="form-check-label"for="remember">Remember me</label>

            </div>

            <button
                type="submit"name="login"class="btn btn-login w-100">Masuk
            </button>
        </form>

        <div class="divider">ATAU</div>

        <p class="register">Belum punya akun?
            <a href="register.php">Register</a>
        </p>

        <a href="index.php" class="back-home">← Kembali ke Beranda</a>
        <p class="copyright"> © 2026–2027 Beadly Accesorise</p>
    </div>

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>