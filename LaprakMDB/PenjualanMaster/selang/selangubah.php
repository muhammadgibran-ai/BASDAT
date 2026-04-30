<?php
require_once __DIR__ . '/../../koneksi.php';

$idSelang = isset($_GET['id_selang']) ? trim($_GET['id_selang']) : '';
if ($idSelang === '') {
    header('Location: selanglihat.php');
    exit;
}

$pompaOptions = array();
$pompaLookup = array();
$formError = null;

$pompaResult = mysqli_query($conn, "SELECT id_pompa, no_pompa FROM pompa ORDER BY id_pompa ASC");
if ($pompaResult) {
    while ($row = mysqli_fetch_assoc($pompaResult)) {
        $pompaOptions[] = $row;
        $pompaLookup[$row['id_pompa']] = $row['no_pompa'];
    }
} else {
    $formError = 'Gagal memuat daftar pompa: ' . mysqli_error($conn);
}

$idSelangEscaped = mysqli_real_escape_string($conn, $idSelang);
$selectQuery = mysqli_query($conn, "SELECT id_selang, no_selang, id_pompa FROM selang WHERE id_selang = '$idSelangEscaped'");
$data = $selectQuery ? mysqli_fetch_assoc($selectQuery) : null;

if (!$data) {
    header('Location: selanglihat.php');
    exit;
}

$noSelang = $data['no_selang'];
$idPompa = $data['id_pompa'];

if (isset($_POST['proses'])) {
    $noSelang = trim($_POST['no_selang'] ?? '');
    $idPompa = trim($_POST['id_pompa'] ?? '');

    if ($noSelang === '' || $idPompa === '') {
        $formError = 'Nomor selang dan ID pompa wajib diisi.';
    } elseif (!isset($pompaLookup[$idPompa])) {
        $formError = 'ID pompa harus dipilih dari daftar yang tersedia.';
    } else {
        $noSelangEscaped = mysqli_real_escape_string($conn, $noSelang);
        $idPompaEscaped = mysqli_real_escape_string($conn, $idPompa);
        $updateQuery = "UPDATE selang SET no_selang = '$noSelangEscaped', id_pompa = '$idPompaEscaped' WHERE id_selang = '$idSelangEscaped'";

        if (mysqli_query($conn, $updateQuery)) {
            header('Location: selanglihat.php');
            exit;
        }

        $formError = 'Gagal mengubah data selang: ' . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Ubah Selang - SPBU Hasanuddin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../css/fontAwesome.css">
    <link rel="stylesheet" href="../../css/light-box.css">
    <link rel="stylesheet" href="../../css/owl-carousel.css">
    <link rel="stylesheet" href="../../css/templatemo-style.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <script src="../../js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    <style>
        :root {
            --selang-sidebar: 320px;
            --selang-gap: clamp(24px, 4vw, 56px);
        }

        body {
            background: #f4f1ea;
            overflow-x: hidden;
        }

        .sidebar-navigation {
            width: var(--selang-sidebar);
            overflow: hidden;
        }

        .sidebar-navigation .logo {
            height: 122px;
            padding: 16px 24px;
        }

        .sidebar-navigation .logo a {
            height: 90px;
            line-height: 90px;
        }

        .sidebar-navigation nav {
            position: absolute;
            top: 146px;
            left: 0;
            right: 0;
            width: 100%;
            max-height: calc(100vh - 236px);
            overflow-y: auto;
            transform: none;
            padding: 14px 34px 30px;
        }

        .sidebar-navigation ul {
            margin-left: 0;
        }

        .sidebar-navigation li {
            padding: 8px 0;
        }

        .sidebar-navigation .social-icons {
            left: 0;
            right: 0;
            bottom: 22px;
        }

        .selang-slider,
        .page-content {
            width: calc(100% - var(--selang-sidebar));
            margin-left: var(--selang-sidebar);
            float: none;
        }

        .selang-slider .content-section,
        .page-content .content-section {
            left: auto;
            width: 100%;
            min-width: 0;
            transform: none;
        }

        .form-hero {
            width: 100%;
            min-height: 100vh;
            height: 100vh;
            display: block;
            background: linear-gradient(120deg, rgba(14, 18, 35, 0.82), rgba(94, 114, 235, 0.52)), url('../../img/SPBUMaster.jpg') center center / cover no-repeat;
        }

        .form-hero .info {
            position: static;
            top: auto;
            right: auto;
            left: auto;
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 72px var(--selang-gap);
            text-align: left;
            transform: none;
        }

        .form-hero .info > div {
            max-width: 680px;
        }

        .hero-kicker {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 18px;
            border-radius: 999px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }

        .form-hero .info h1 {
            font-size: 52px;
            line-height: 1.08;
            margin-bottom: 18px;
        }

        .form-hero .info p {
            font-size: 17px;
            line-height: 1.9;
            color: rgba(255,255,255,0.9);
        }

        #form-selang {
            padding: 80px var(--selang-gap);
        }

        .form-shell {
            max-width: 920px;
            margin: 0 auto;
            padding: 34px;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 20px 45px rgba(24,30,48,0.08);
            text-align: left;
        }

        .form-shell h4 {
            margin: 0 0 8px;
            color: #232323;
            font-size: 28px;
            font-weight: 700;
        }

        .form-shell p {
            margin: 0 0 28px;
            color: #69707c;
            font-size: 15px;
            line-height: 1.8;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 24px;
        }

        .form-group-full {
            grid-column: 1 / -1;
        }

        .form-shell label {
            display: block;
            margin-bottom: 10px;
            color: #232323;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .form-shell input,
        .form-shell select {
            width: 100%;
            border: 1px solid #ddd8ef;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 15px;
            color: #232323;
            background: #fbfaff;
            outline: none;
        }

        .form-shell input[readonly] {
            background: #f0eef9;
            color: #5a5f73;
            cursor: not-allowed;
        }

        .form-shell input:focus,
        .form-shell select:focus {
            border-color: #45489a;
            box-shadow: 0 0 0 3px rgba(69,72,154,0.12);
        }

        .form-status {
            margin-bottom: 20px;
            padding: 16px 18px;
            border-radius: 14px;
            background: #fff5f5;
            border-left: 4px solid #bb2d3b;
            color: #8d2130;
            font-size: 14px;
            line-height: 1.7;
        }

        .form-note {
            margin-top: 8px;
            color: #69707c;
            font-size: 13px;
            line-height: 1.7;
        }

        .form-actions {
            margin-top: 28px;
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .form-actions button,
        .form-actions a {
            display: inline-block;
            min-width: 150px;
            height: 46px;
            line-height: 46px;
            border: none;
            border-radius: 999px;
            text-align: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.25s ease;
        }

        .form-actions button {
            background: #45489a;
            color: #fff;
        }

        .form-actions button:hover {
            background: #31347a;
        }

        .form-actions a {
            background: #f0eef9;
            color: #45489a;
        }

        .form-actions a:hover {
            background: #e2def5;
            color: #31347a;
        }

        @media (max-width: 1399px) {
            :root {
                --selang-sidebar: 296px;
            }
        }

        @media (max-width: 767px) {
            .selang-slider,
            .page-content {
                width: 100%;
                margin-left: 0;
            }

            .form-hero .info {
                width: 100%;
                max-width: none;
                min-height: 100vh;
                justify-content: center;
                padding: 0 30px;
                text-align: center;
            }

            .form-hero .info > div {
                max-width: 620px;
                margin: 0 auto;
            }

            .form-hero .info h1 {
                font-size: 38px;
            }

            #form-selang {
                padding: 70px 20px;
            }

            .form-shell {
                padding: 24px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-actions button,
            .form-actions a {
                width: 100%;
            }
        }

        .slider {
            display: none;
        }

        .page-content {
            min-height: 100vh;
            padding-top: 44px;
            display: flex;
            flex-direction: column;
        }

        #ringkasan {
            display: none;
        }

        .page-content .section-heading {
            margin-bottom: 30px;
        }

        .page-content > .content-section {
            flex: 0 0 auto;
        }

        .page-content > .footer {
            margin-top: auto;
            display: block;
            height: auto;
            line-height: 1.6;
            padding: 24px 20px;
        }

        .page-content > .footer p {
            margin: 0;
            line-height: 1.7;
        }

        .page-content > .content-section:first-of-type {
            padding-top: 44px !important;
        }

        #ringkasan + .content-section {
            padding-top: 44px !important;
        }

        .responsive-nav li:has(> a[href="#top"]),
        .responsive-nav li:has(> a[href="#ringkasan"]),
        .sidebar-navigation nav li:has(> a[href="#top"]),
        .sidebar-navigation nav li:has(> a[href="#ringkasan"]),
        .sidebar-navigation .social-icons li:has(> a[href="#ringkasan"]) {
            display: none;
        }

        @media (max-width: 767px) {
            .page-content {
                padding-top: 24px;
            }

            .page-content > .content-section:first-of-type,
            #ringkasan + .content-section {
                padding-top: 24px !important;
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
                <ul class="nav navbar-nav">
                    <li><a href="#top">Header</a></li>
                    <li><a href="#form-selang">Form Ubah</a></li>
                    <li><a href="selanglihat.php">Daftar Selang</a></li>
                    <li><a href="../../home.php">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="sidebar-navigation hidde-sm hidden-xs">
        <div class="logo">
            <a href="selanglihat.php">Ubah<em>Selang</em></a>
        </div>
        <nav>
            <ul>
                <li><a href="#top"><span class="rect"></span><span class="circle"></span>Hero Ubah</a></li>
                <li><a href="#form-selang"><span class="rect"></span><span class="circle"></span>Form Selang</a></li>
                <li><a href="selanglihat.php"><span class="rect"></span><span class="circle"></span>Tabel Selang</a></li>
                <li><a href="../../home.php"><span class="rect"></span><span class="circle"></span>Kembali Home</a></li>
            </ul>
        </nav>
        <ul class="social-icons">
            <li><a href="#form-selang"><i class="fa fa-pencil"></i></a></li>
            <li><a href="selanglihat.php"><i class="fa fa-table"></i></a></li>
        </ul>
    </div>

    <div class="slider selang-slider">
        <div class="Modern-Slider content-section" id="top">
            <div class="item item-1">
                <div class="img-fill form-hero">
                    <div class="info">
                        <div>
                            <span class="hero-kicker">Edit Data Selang</span>
                            <h1>Perbarui Data Selang<br>Secara Langsung</h1>
                            <p>Halaman ubah ini sudah disesuaikan ke tabel <strong>selang</strong>.</p>
                            <div class="white-button button">
                                <a href="#form-selang">Ubah Sekarang</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section id="form-selang" class="content-section">
            <div class="section-heading">
                <h1>Form<br><em>Ubah</em></h1>
                <p>ID selang dipertahankan, sedangkan nomor selang dan ID pompa dapat diperbarui.</p>
            </div>
            <div class="section-content">
                <div class="form-shell">
                    <h4>Ubah Data Selang</h4>
                    <p>Perbarui informasi selang yang dipilih lalu simpan perubahan ke database.</p>

                    <?php if ($formError !== null) { ?>
                        <div class="form-status"><?php echo htmlspecialchars($formError, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php } ?>

                    <form action="" method="post">
                        <div class="form-grid">
                            <div>
                                <label for="id_selang">ID Selang</label>
                                <input id="id_selang" name="id_selang" type="text" value="<?php echo htmlspecialchars($data['id_selang'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                            </div>
                            <div>
                                <label for="no_selang">Nomor Selang</label>
                                <input id="no_selang" name="no_selang" type="text" value="<?php echo htmlspecialchars($noSelang, ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="form-group-full">
                                <label for="id_pompa">ID Pompa</label>
                                <select id="id_pompa" name="id_pompa" required>
                                    <option value="">Pilih pompa</option>
                                    <?php foreach ($pompaOptions as $pompa) { ?>
                                        <option value="<?php echo htmlspecialchars($pompa['id_pompa'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $idPompa === $pompa['id_pompa'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pompa['id_pompa'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($pompa['no_pompa'], ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="proses">Simpan Perubahan</button>
                            <a href="selanglihat.php">Kembali ke Tabel</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="footer">
            <p>Copyright &copy; 2019 Company Name
                . Design: TemplateMo</p>
        </section>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
    <script src="../../js/vendor/bootstrap.min.js"></script>
    <script src="../../js/plugins.js"></script>
    <script src="../../js/main.js"></script>
</body>

</html>





