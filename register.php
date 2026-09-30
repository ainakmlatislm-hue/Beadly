<?php

session_start();

include 'koneksi.php';

$pesan = "";
$success = "";

if (isset($_POST['register'])) {

    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $hp       = mysqli_real_escape_string($koneksi, $_POST['hp']);
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    // Cek username sudah digunakan atau belum
    $cek_username = mysqli_query(
        $koneksi,"SELECT * FROM tb_user WHERE username='$username'"
    );

    if (mysqli_num_rows($cek_username) > 0) {
        $pesan = "Username sudah digunakan.";

    } else {

        // Cek email sudah digunakan atau belum
        $cek_email = mysqli_query(
            $koneksi,
            "SELECT * FROM tb_user WHERE email='$email'"
        );

        if (mysqli_num_rows($cek_email) > 0) {

            $pesan = "Email sudah terdaftar.";

        } else {

            // Role otomatis pelanggan
            $role = "pelanggan";

            $query = mysqli_query(
                $koneksi,
                "INSERT INTO tb_user 
                (nama, email, username, password, hp, alamat, role)
                VALUES
                ('$nama', '$email', '$username', '$password', '$hp', '$alamat', '$role')"
            );

            if ($query) {

                $_SESSION['register_success'] =
                    "Akun berhasil dibuat. Silakan login.";

                header("Location: login.php");
                exit;

            } else {

                $pesan = "Pendaftaran gagal. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Beadly Accesorise</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Inter:wght@400;500;600&display=swap"rel="stylesheet">

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
            padding: 35px 20px;
        }

        h1,h2,h3 {
            font-family: 'Playfair Display', serif;
        }

        .register-card {
            width: 100%;
            max-width: 500px;
            background: #fff;
            padding: 42px 45px 38px;
            border: 1px solid #eee;
            border-radius: 22px;
            box-shadow:
                0 20px 50px rgba(0, 0, 0, .06);
        }

        .brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand img {
            width: 100px;
            height: auto;
            margin-bottom: 16px;
        }

        .brand h1 {
            margin: 0 0 8px;
            color: #222;
            font-size: 32px;
            font-weight: 500;
        }

        .brand p {
            margin: 0;
            color: #999;
            font-size: 12px;
            line-height: 1.7;
        }

        .form-label {
            margin-bottom: 7px;
            color: #444;
            font-size: 12px;
            font-weight: 600;
        }

        .form-control {
            height: 47px;
            padding: 0 15px;
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

        .form-control:hover {
            border-color: #d8d0c8;
        }

        .form-control:focus {
            border-color: #b89a7a;
            background: #fff;
            box-shadow:0 0 0 3px rgba(184,154,122,.12);
            outline: none;
        }

        textarea.form-control {
            height: 75px;
            padding-top: 12px;
            padding-bottom: 12px;
            resize: none;
        }

        .btn-register {
            height: 50px;
            border: none;
            border-radius: 30px;
            background: #4f3c27;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: .3px;
            transition: .3s;
        }

        .btn-register:hover {
            background: #b89a7a;
            color: #fff;
            transform: translateY(-2px);
            box-shadow:0 10px 25px rgba(184,154,122,.22);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 25px 0;
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

        .login-text {
            margin: 0;
            color: #888;
            font-size: 12px;
            text-align: center;
        }

        .login-text a {
            color: #b89a7a;
            font-weight: 600;
            text-decoration: none;
            transition: .3s;
        }

        .login-text a:hover {
            color: #8f7358;
            text-decoration: underline;
        }

        .back-home {
            display: block;
            margin-top: 17px;
            color: #aaa;
            font-size: 10px;
            text-align: center;
            text-decoration: none;
            transition: .3s;
        }

        .back-home:hover {
            color: #b89a7a;
        }

        .copyright {
            margin: 22px 0 0;
            color: #aaa;
            font-size: 9px;
            text-align: center;
        }


        @media (max-width: 500px) {

            body {
                padding: 20px 15px;
            }

            .register-card {
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
    title: 'Registrasi Gagal',
    text: '<?= htmlspecialchars($pesan) ?>',
    confirmButtonText: 'Coba Lagi',
    confirmButtonColor: '#111'
});

</script>

<?php } ?>


<?php if (isset($_SESSION['register_success'])) { ?>

<script>

Swal.fire({
    icon: 'success',
    title: 'Registrasi Berhasil!',
    text: '<?= $_SESSION['register_success'] ?>',
    confirmButtonText: 'Login Sekarang',
    confirmButtonColor: '#111'
}).then(() => {
    window.location.href = 'login.php';
});

</script>

<?php unset($_SESSION['register_success']); ?>

<?php } ?>


<div class="register-card">>

    <div class="brand">
        <img src="assets/img/beadly_logo.png"alt="Beadly Accesorise">
        <h1>Buat Akun</h1>
        <p>Daftarkan dirimu untuk mulai berbelanja di Beadly Accesorise.</p>
    </div>

    <form method="post">

        <div class="mb-3">
            <label for="nama"class="form-label">Nama Lengkap</label>
            <input  type="text" name="nama"id="nama"class="form-control"placeholder="Masukkan nama lengkap"required>
        </div>

        <div class="mb-3">
            <label  for="email" class="form-label">Email</label>
            <input type="email"name="email"id="email"class="form-control"placeholder="Masukkan email"required>
        </div>

        <div class="mb-3">
            <label for="username"class="form-label">Username</label>
            <input type="text" name="username"id="username" class="form-control"placeholder="Buat username"required>
        </div>

        <div class="mb-3">
            <label for="password"class="form-label">Password</label>
            <input type="password"name="password" id="password"class="form-control"placeholder="Buat password" required>
        </div>

        <div class="mb-3">
            <label for="hp"class="form-label">Nomor HP</label>  
            <input type="tel" name="hp" id="hp" class="form-control"placeholder="Masukkan nomor HP"required>
        </div>

        <div class="mb-3">
            <label for="alamat"class="form-label">Alamat</label>
            <textarea name="alamat"id="alamat"class="form-control"placeholder="Masukkan alamat lengkap"required></textarea>
        </div>

        <button type="submit"name="register"class="btn btn-register w-100">Daftar</button>
    </form>
    <div class="divider">ATAU</div>
    <p class="login-text">
        Sudah punya akun?
        <a href="login.php"> Sign In</a>
    </p>
    <a href="index.php"class="back-home">← Kembali ke Beranda</a>
    <p class="copyright"> © 2026–2027 Beadly Accesorise   </p>

</div>

<script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>