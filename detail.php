<?php
session_start();
include 'koneksi.php';

$id = $_GET['id'];
$hasil = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = '$id'");
while ($data = mysqli_fetch_array($hasil)) {
    $nama = $data['nama'];
    $harga = $data['harga'];
    $deskripsi = $data['deskripsi'];
    $poto = $data['poto'];
}
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $nama ?> | Beadly Accesorise</title>
<link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{margin:0;background:#fff;color:#222;font-family:'Inter',sans-serif}
h1,h2,h3,h4,h5{
  font-family:'Playfair Display',serif}

/* NAVBAR */
.navbar-custom{
  height:82px;
  display:flex;
  align-items:center;
  background:#fff;
  border-bottom:1px solid #eee}

.navbar-inner{
  width:100%;
  display:flex;
  align-items:center;
  justify-content:space-between}

.logo{display:flex;
align-items:center;
text-decoration:none}

.logo img{
  width:115px;
height:auto;
object-fit:contain}
.menu{
  display:flex;
  align-items:center;
  gap:30px;
  margin:0;
  padding:0;
  list-style:none}
.menu a{
  position:relative;
  color:#555;
  font-size:12px;
  font-weight:500;
  text-decoration:none;transition:.3s}
.menu a::after{
  content:"";
  position:absolute;
  left:0;
  bottom:-7px;
  width:0;
  height:1px;
  background:#b89a7a;
  transition:.3s
}
.menu a:hover{
  color:#b89a7a
}
.menu a:hover::after{
  width:100%
}

.nav-icons{
  display:flex;
  align-items:center;
  gap:5px
}
.icon-btn{
  width:42px;
  height:42px;
  display:flex;
  align-items:center;
  justify-content:center;
  border:none;
  background:transparent;
  color:#333;
  border-radius:50%;
  text-decoration:none;
  transition:.3s
}
.icon-btn:hover{background:#f7f3ef;color:#b89a7a;transform:translateY(-2px)}

/* DETAIL */
.detail-section{
  padding:70px 0 90px
}
.breadcrumb-custom{
  margin-bottom:35px;
  color:#aaa;
  font-size:11px
}
.breadcrumb-custom a{
  color:#999;
  text-decoration:none;
  transition:.3s
}
.breadcrumb-custom a:hover{
  color:#b89a7a
}
.breadcrumb-custom span{
  margin:0 8px;
  color:#d2d2d2
}
.product-card{
  overflow:hidden;
  background:#fff;
  border:1px solid #eee;
  border-radius:18px;
  box-shadow:0 15px 40px rgba(0,0,0,.05)
}
.product-image-area{
  padding:25px;
  background:#faf8f6
}
.product-image{
  width:100%;
  height:560px;
  display:block;
  object-fit:cover;
  border-radius:14px
}
.product-info{
  padding:50px 55px
}
.product-category{
  margin-bottom:12px;
  color:#b89a7a;
  font-size:11px;
  font-weight:600;
  letter-spacing:2px;
  text-transform:uppercase
}
.product-name{
  margin-bottom:18px;
  color:#222;
  font-size:42px;
  font-weight:500;
  line-height:1.2
}
.product-price{
  margin-bottom:28px;
  color:#b89a7a;
  font-size:24px;
  font-weight:600
}
.product-line{
  width:55px;
  height:1px;
  margin-bottom:25px;
  background:#b89a7a
}
.product-description{
  margin-bottom:35px;
  color:#777;
  font-size:13px;
  line-height:1.9
}

/* QUANTITY */
.quantity-label{
  display:block;
  margin-bottom:10px;
  color:#444;
  font-size:12px;
  font-weight:600
}
.quantity-input{
  width:90px;
  height:45px;
  padding:0 12px;
  border:1px solid #e2e2e2;
  border-radius:8px;
  background:#fafafa;
  color:#333;
  font-size:13px;
  outline:none;
  transition:.3s
}
.quantity-input:focus{
  border-color:#b89a7a;
  background:#fff;
  box-shadow:0 0 0 3px rgba(184,154,122,.1)
}

/* BUTTON */
.btn-cart{
  height:50px;
  padding:0 30px;
  border:none;
  border-radius:30px;
  background: #4f3c27;
  color:#fff;
  font-size:12px;
  font-weight:500;
  letter-spacing:.3px;
  transition:.3s
}
.btn-cart:hover{
  background:#b89a7a;
  color:#fff;
  transform:translateY(-2px);
  box-shadow:0 10px 25px rgba(184,154,122,.22)
}
.back-link{
  display:inline-flex;
  align-items:center;
  gap:7px;
  margin-top:30px;
  color:#888;
  font-size:12px;
  text-decoration:none;
  transition:.3s
}
.back-link:hover{
  color:#b89a7a;
  transform:translateX(-3px)
}

/* FOOTER */
.footer{
  padding:30px 0;
  border-top:1px solid #eee;b
  background:#fff;
  color:#aaa;
  font-size:10px;
  text-align:center}

/* RESPONSIVE */
@media(max-width:991px){
    .menu{
      gap:15px
    }
    .product-info{
      padding:40px
    }
    .product-name{
      font-size:35px
    }
    .product-image{
      height:480px
    }
}
@media(max-width:767px){
    .navbar-custom{
      height:auto;
      padding:15px 0
    }
    .navbar-inner{
      flex-wrap:wrap;
      gap:15px
    }
    .logo img{
      width:100px
    }
    .menu{
      order:3;
      width:100%;
      justify-content:center;
      gap:18px;
      overflow-x:auto;
      padding-bottom:5px
    }
    .menu a{
      white-space:nowrap;
      font-size:11px
    }
    .detail-section{
      padding:35px 0 60px
    }
    .breadcrumb-custom{
      margin-bottom:20px
    }
    .product-card{
      border-radius:14px
    }
    .product-image-area{
      padding:15px
    }
    .product-image{
      height:400px;
      border-radius:10px
    }
    .product-info{
      padding:30px 25px
    }
    .product-name{
      font-size:32px
    }
    .product-price{
      font-size:21px
    }
}
@media(max-width:400px){
    .product-image{
      height:330px
    }
    .product-name{
      font-size:28px
    }
    .btn-cart{
      width:100%
    }
}
</style>
</head>

<body>

<header class="navbar-custom">
  <div class="container">
    <div class="navbar-inner">
      <a href="index.php" class="logo"><img src="assets/img/beadly_logo.png" alt="Beadly Accesorise"></a>
      
      <ul class="menu">
        <li><a href="index.php">Beranda</a></li>
        <li><a href="index.php#gelang">Gelang</a></li>
        <li><a href="index.php#cincin">Cincin</a></li>
        <li><a href="index.php#mac">Kalung</a></li>
        <li><a href="index.php#aksesori">Gantungan kunci</a></li>
      </ul>
      
      <div class="nav-icons">
        <a href="profile.php" class="icon-btn" title="Profile">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
          </svg>
        </a>
        
        <a href="keranjang.php" class="icon-btn" title="Keranjang">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1-1 0m7 0a1 1 0 1 1 0 2 2 2 0 0 1-2 0"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</header>
<main>
  <section class="detail-section">
    <div class="container">
      <div class="breadcrumb-custom">
    </div>

    <div class="product-card">
      <div class="row g-0 align-items-center">
        <div class="col-lg-6">
          <div class="product-image-area">
            <img src="img/<?= $poto ?>" alt="<?= $nama ?>" class="product-image">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="product-info">
            <div class="product-category">Beadly Collection</div>
            <h1 class="product-name"><?= $nama ?></h1>
            <div class="product-price">Rp <?= number_format($harga,0,',','.') ?></div>
            <div class="product-line"></div>
            <p class="product-description"><?= $deskripsi ?></p>
            
            <form method="post" action="keranjang.php?id=<?= $id ?>">
              <input type="hidden" name="hidden_foto" value="<?= $poto ?>">
              <input type="hidden" name="hidden_nama" value="<?= $nama ?>">
              <input type="hidden" name="hidden_harga" value="<?= $harga ?>">
              <label for="jumlah" class="quantity-label">Jumlah</label>
              <input type="number" name="jumlah" id="jumlah" value="1" min="1" class="quantity-input mb-4">
              <br>
              <button type="submit" name="add" class="btn-cart">Tambah ke Keranjang</button>
            </form>
            <a href="index.php#produk" class="back-link">← Kembali ke Produk</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</main>

<footer class="footer">© 2026–2027 Beadly Accesorise</footer>

<script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>