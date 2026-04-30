<?php
require_once __DIR__ . '/koneksi.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Register - SPBU Hasanuddin</title>
    <meta name="description" content="Halaman pendaftaran akun SPBU Hasanuddin">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/fontAwesome.css">
    <link rel="stylesheet" href="css/light-box.css">
    <link rel="stylesheet" href="css/owl-carousel.css">
    <link rel="stylesheet" href="css/templatemo-style.css?v=<?php echo filemtime("css/templatemo-style.css"); ?>">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

    <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    <style>
        .register-wrap {
            max-width: 760px;
            margin: 0 auto;
            min-height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-card {
            background: #ffffff;
            border: 1px solid #e6e6e6;
            border-radius: 10px;
            padding: 38px 32px 30px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
        }

        .register-logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .register-logo img {
            max-width: 120px;
            height: auto;
        }

        .register-heading {
            text-align: center;
            margin-bottom: 26px;
        }

        .register-heading h1 {
            margin: 0 0 10px;
            color: #232323;
            font-size: 42px;
            font-weight: 700;
        }

        .register-heading h1 em {
            font-style: normal;
            color: #45489a;
        }

        .register-heading p {
            margin: 0;
            color: #667085;
            font-size: 16px;
            line-height: 1.8;
        }

        .register-card .row {
            margin-left: -10px;
            margin-right: -10px;
        }

        .register-card [class*="col-"] {
            padding-left: 10px;
            padding-right: 10px;
        }

        .register-card fieldset {
            margin-bottom: 16px;
        }

        .register-card .form-control {
            height: 52px;
            border-radius: 8px;
        }

        .register-card .btn {
            margin-top: 14px;
            min-width: 160px;
            height: 50px;
            line-height: 50px;
            padding: 0 28px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, #45489a, #5a5fc7);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.4px;
            box-shadow: 0 14px 28px rgba(69, 72, 154, 0.24);
            transition: all 0.25s ease;
        }

        .register-card .btn:hover,
        .register-card .btn:focus {
            background: linear-gradient(135deg, #31347a, #4a4fb2);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 18px 30px rgba(69, 72, 154, 0.3);
        }

        .register-help {
            margin-top: 18px;
            text-align: center;
        }

        .register-success-modal .modal-content {
            border-radius: 14px;
            border: none;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
        }

        .register-success-modal .modal-dialog {
            display: flex;
            align-items: center;
            min-height: calc(100vh - 40px);
            margin: 20px auto;
        }

        .register-success-modal .modal-body {
            padding: 34px 28px 30px;
            text-align: center;
        }

        .register-success-modal h3 {
            margin: 0 0 12px;
            color: #232323;
            font-size: 28px;
            font-weight: 700;
        }

        .register-success-modal p {
            margin: 0 0 22px;
            color: #667085;
            font-size: 15px;
            line-height: 1.8;
        }

        .register-success-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .register-success-actions a,
        .register-success-actions button {
            min-width: 160px;
            height: 48px;
            line-height: 48px;
            padding: 0 22px;
            border: none;
            border-radius: 999px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.25s ease;
        }

        .register-success-actions .login-action {
            background: linear-gradient(135deg, #45489a, #5a5fc7);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(69, 72, 154, 0.22);
        }

        .register-success-actions .login-action:hover,
        .register-success-actions .login-action:focus {
            background: linear-gradient(135deg, #31347a, #4a4fb2);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .register-success-actions .cancel-action {
            background: #f0eef9;
            color: #45489a;
        }

        .register-success-actions .cancel-action:hover,
        .register-success-actions .cancel-action:focus {
            background: #e1ddf6;
            color: #2f326f;
        }

        .page-content {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-content .footer {
            margin-top: auto;
        }

        @media (max-width: 767px) {
            .register-wrap {
                max-width: 100%;
                min-height: auto;
            }

            .register-card {
                padding: 30px 22px 24px;
            }

            .register-heading h1 {
                font-size: 34px;
            }
        }
    </style>
</head>

<body>

    <header class="nav-down responsive-nav hidden-lg hidden-md">
        <button type="button" id="nav-toggle" class="navbar-toggle" data-toggle="collapse" data-target="#main-nav">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div id="main-nav" class="collapse navbar-collapse">
            <nav>
                <ul>
                    <li><a href="index.php#top">Beranda</a></li>
                    <li><a href="index.php#featured">Keunggulan</a></li>
                    <li><a href="index.php#projects">Galeri</a></li>
                    <li><a href="index.php#video">Profil</a></li>
                    <li><a href="index.php#blog">Info SPBU</a></li>
                    <li><a href="index.php#contact">Hubungi Kami</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="sidebar-navigation hidde-sm hidden-xs">
        <div class="logo">
            <a href="index.php#top">SPBU<em>Hasanuddin</em></a>
        </div>
        <nav>
            <ul>
                <li>
                    <a href="index.php#top">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Beranda
                    </a>
                </li>
                <li>
                    <a href="index.php#featured">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Keunggulan
                    </a>
                </li>
                <li>
                    <a href="index.php#projects">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Galeri
                    </a>
                </li>
                <li>
                    <a href="index.php#video">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Profil
                    </a>
                </li>
                <li>
                    <a href="index.php#blog">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Info SPBU
                    </a>
                </li>
                <li>
                    <a href="index.php#contact">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Hubungi Kami
                    </a>
                </li>
                <li>
                    <a href="login.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Login
                    </a>
                </li>
            </ul>
        </nav>
        <ul class="social-icons">
            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
            <li><a href="#"><i class="fa fa-rss"></i></a></li>
            <li><a href="#"><i class="fa fa-behance"></i></a></li>
        </ul>
    </div>

    <div class="page-content">
        <section class="content-section">
            <div class="section-content">
                <div class="register-wrap">
                    <div class="register-card">
                        <div class="register-logo">
                            <img src="logo_spbu.png" alt="Logo SPBU Hasanuddin">
                        </div>
                        <div class="register-heading">
                            <h1>Register</h1>
                            <p>Buat akun baru untuk mengakses sistem dengan mengisi data diri, username, email, dan password dengan benar.</p>
                        </div>

                        <form id="registerForm" action="register.php" method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <input name="nama" type="text" class="form-control" placeholder="Nama Lengkap" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset>
                                        <input name="username" type="text" class="form-control" placeholder="Username" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset>
                                        <input name="email" type="email" class="form-control" placeholder="Email" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset>
                                        <input name="password" type="password" class="form-control" placeholder="Password" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-12">
                                    <fieldset>
                                        <input name="password_confirm" type="password" class="form-control" placeholder="Konfirmasi Password" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 text-center">
                                    <fieldset>
                                        <button type="submit" class="btn">Daftar</button>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 register-help">
                                    <p>Sudah punya akun? <a href="login.php">Login di sini</a>.</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <section class="footer">
            <p>Copyright &copy; 2019 Company Name . Design: TemplateMo</p>
        </section>
    </div>

    <div class="modal fade register-success-modal" id="registerSuccessModal" tabindex="-1" role="dialog" aria-labelledby="registerSuccessLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h3 id="registerSuccessLabel">Register selesai</h3>
                    <p>Akun berhasil didaftarkan. Lanjutkan ke halaman login untuk masuk ke sistem.</p>
                    <div class="register-success-actions">
                        <a href="login.php" class="login-action">Ke Login</a>
                        <button type="button" class="cancel-action" data-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>
    <script>
        (function() {
            var form = document.getElementById('registerForm');
            if (!form) {
                return;
            }

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                $('#registerSuccessModal').modal('show');
            });
        })();
    </script>
</body>

</html>
