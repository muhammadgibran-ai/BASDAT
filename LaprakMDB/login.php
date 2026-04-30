<?php
require_once __DIR__ . '/koneksi.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Login - SPBU Hasanuddin</title>
    <meta name="description" content="Halaman login SPBU Hasanuddin">
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
        .login-wrap {
            max-width: 620px;
            margin: 0 auto;
            min-height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e6e6e6;
            border-radius: 10px;
            padding: 38px 32px 30px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .login-logo img {
            max-width: 120px;
            height: auto;
        }

        .login-heading {
            text-align: center;
            margin-bottom: 26px;
        }

        .login-heading h1 {
            margin: 0 0 10px;
            color: #232323;
            font-size: 42px;
            font-weight: 700;
        }

        .login-heading h1 em {
            font-style: normal;
            color: #45489a;
        }

        .login-heading p {
            margin: 0;
            color: #667085;
            font-size: 16px;
            line-height: 1.8;
        }

        .login-card fieldset {
            margin-bottom: 16px;
        }

        .login-card .form-control {
            height: 52px;
            border-radius: 8px;
        }

        .login-card .btn {
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

        .login-card .btn:hover,
        .login-card .btn:focus {
            background: linear-gradient(135deg, #31347a, #4a4fb2);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 18px 30px rgba(69, 72, 154, 0.3);
        }

        .login-help {
            margin-top: 18px;
            text-align: center;
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
            .login-wrap {
                max-width: 100%;
                min-height: auto;
            }

            .login-card {
                padding: 30px 22px 24px;
            }

            .login-heading h1 {
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
                <div class="login-wrap">
                    <div class="login-card">
                        <div class="login-logo">
                            <img src="logo_spbu.png" alt="Logo SPBU Hasanuddin">
                        </div>
                        <div class="login-heading">
                            <h1>Login</h1>
                            <p>Masukkan email dan password Anda untuk masuk ke sistem pengelolaan SPBU Hasanuddin.</p>
                        </div>
                        <form action="home.php" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <input name="email" type="email" class="form-control" placeholder="Email" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-12">
                                    <fieldset>
                                        <input name="password" type="password" class="form-control" placeholder="Password" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 text-center">
                                    <fieldset>
                                        <button type="submit" class="btn">Login</button>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 login-help">
                                    <p>Belum ada akun? <a href="register.php">Daftar</a></p>
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

</body>

</html>
