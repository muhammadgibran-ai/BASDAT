<?php
require_once __DIR__ . '/../../koneksi.php';

$limit = 5;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$start = ($page - 1) * $limit;
$totalRows = 0;
$totalPages = 1;
$selangRows = array();
$queryError = null;

$countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM selang");
if ($countResult) {
    $countRow = mysqli_fetch_assoc($countResult);
    $totalRows = (int) ($countRow['total'] ?? 0);
    $totalPages = max(1, (int) ceil($totalRows / $limit));
    if ($page > $totalPages) {
        $page = $totalPages;
        $start = ($page - 1) * $limit;
    }
} else {
    $queryError = 'Gagal menghitung data selang: ' . mysqli_error($conn);
}

if ($queryError === null) {
    $result = mysqli_query($conn, "SELECT id_selang, no_selang, id_pompa FROM selang ORDER BY created_at DESC, CAST(NULLIF(REGEXP_REPLACE(id_selang, '[^0-9]', ''), '') AS UNSIGNED) DESC, id_selang DESC LIMIT $start, $limit");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $selangRows[] = $row;
        }
    } else {
        $queryError = 'Gagal memuat data selang: ' . mysqli_error($conn);
    }
}

$displayStart = $totalRows > 0 ? $start + 1 : 0;
$displayEnd = $totalRows > 0 ? min($start + $limit, $totalRows) : 0;
$viewState = 'data';
if ($queryError !== null) {
    $viewState = 'error';
} elseif (empty($selangRows)) {
    $viewState = 'empty';
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Master Selang - SPBU Hasanuddin</title>
    <meta name="description" content="Daftar data selang pada database db_spbu">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

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

        .selang-hero {
            width: 100%;
            min-height: 100vh;
            height: 100vh;
            display: block;
            background: linear-gradient(120deg, rgba(15, 20, 44, 0.84), rgba(69, 72, 154, 0.62)), url('../../img/SPBUMaster.jpg') center center / cover no-repeat;
        }

        .selang-hero .info {
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

        .selang-hero .info > div {
            max-width: 720px;
        }

        .hero-kicker {
            display: inline-block;
            margin-bottom: 22px;
            padding: 8px 18px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }

        .selang-hero .info h1 {
            margin-bottom: 18px;
            font-size: 54px;
            line-height: 1.08;
        }

        .selang-hero .info p {
            font-size: 17px;
            line-height: 1.9;
            color: rgba(255, 255, 255, 0.9);
        }

        .hero-actions {
            margin-top: 34px;
        }

        .hero-actions .button {
            display: inline-block;
            margin-right: 14px;
            margin-bottom: 14px;
        }

        #ringkasan,
        #daftar-selang {
            padding-left: var(--selang-gap);
            padding-right: var(--selang-gap);
            padding-bottom: 80px;
        }

        #daftar-selang {
            padding-top: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }

        .stats-card,
        .table-panel,
        .info-strip {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 18px 40px rgba(24, 30, 48, 0.08);
        }

        .stats-card {
            padding: 28px;
            text-align: left;
        }

        .stats-card span {
            display: inline-block;
            margin-bottom: 12px;
            color: #45489a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        .stats-card > strong {
            display: block;
            margin-bottom: 14px;
            color: #232323;
            font-size: 42px;
            line-height: 1;
        }

        .stats-card p {
            margin: 0;
            color: #6b6f7a;
            font-size: 15px;
            line-height: 1.8;
        }

        .stats-card p strong {
            display: inline;
            font-size: inherit;
            line-height: inherit;
            color: #232323;
            font-weight: 700;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .info-strip {
            margin-top: 26px;
            padding: 18px 22px;
            border-left: 5px solid #45489a;
            color: #3f4652;
            font-size: 15px;
            line-height: 1.8;
        }

        .info-strip.is-error {
            border-left-color: #bb2d3b;
            background: #fff5f5;
            color: #8d2130;
        }

        .table-panel {
            overflow: hidden;
        }

        .table-panel-header {
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #eceaf3;
            text-align: left;
        }

        .table-panel-header h4 {
            margin: 0 0 6px;
            color: #232323;
            font-size: 24px;
            font-weight: 700;
        }

        .table-panel-header p {
            margin: 0;
            color: #6b6f7a;
            font-size: 14px;
        }

        .panel-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .panel-actions a {
            display: inline-block;
            min-width: 150px;
            padding: 12px 18px;
            border-radius: 999px;
            text-align: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .panel-actions .primary-action {
            background: #45489a;
            color: #ffffff;
        }

        .panel-actions .primary-action:hover {
            background: #2f326f;
            color: #ffffff;
        }

        .panel-actions .secondary-action {
            background: #f0eef9;
            color: #45489a;
        }

        .panel-actions .secondary-action:hover {
            background: #e1ddf6;
            color: #2f326f;
        }

        .table-responsive-wrap {
            overflow-x: auto;
            padding: 0 28px 10px;
        }

        .selang-table {
            width: 100%;
            min-width: 680px;
            border-collapse: collapse;
        }

        .selang-table thead th {
            padding: 18px 16px;
            background: #f6f5fb;
            color: #232323;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-bottom: 1px solid #eceaf3;
            text-align: left;
        }

        .selang-table tbody td {
            padding: 18px 16px;
            border-bottom: 1px solid #eceaf3;
            color: #49515d;
            font-size: 15px;
            text-align: left;
            vertical-align: middle;
        }

        .selang-table tbody tr:hover {
            background: #faf9fd;
        }

        .selang-id {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: #f0eef9;
            color: #45489a;
            font-weight: 700;
        }

        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .action-group a {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.25s ease;
        }

        .action-group .edit-link {
            background: #45489a;
            color: #ffffff;
        }

        .action-group .edit-link:hover {
            background: #31347a;
            color: #ffffff;
        }

        .action-group .delete-link {
            background: #fff0f2;
            color: #b42338;
        }

        .action-group .delete-link:hover {
            background: #ffd9df;
            color: #8d1630;
        }

        .empty-state {
            padding: 60px 28px 70px;
            text-align: center;
        }

        .empty-state h4 {
            margin: 0 0 10px;
            color: #232323;
            font-size: 24px;
            font-weight: 700;
        }

        .empty-state p {
            max-width: 480px;
            margin: 0 auto 24px;
            color: #6b6f7a;
            font-size: 15px;
            line-height: 1.8;
        }

        .pagination-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 22px 28px 28px;
            flex-wrap: wrap;
        }

        .pagination-caption {
            color: #6b6f7a;
            font-size: 14px;
            text-align: left;
        }

        .pagination-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pagination-list a,
        .pagination-list span {
            display: inline-block;
            min-width: 42px;
            height: 42px;
            line-height: 42px;
            border-radius: 999px;
            text-align: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .pagination-list a {
            background: #f0eef9;
            color: #45489a;
        }

        .pagination-list a:hover {
            background: #45489a;
            color: #ffffff;
        }

        .pagination-list .current {
            background: #232323;
            color: #ffffff;
        }

        @media (max-width: 1399px) {
            :root {
                --selang-sidebar: 296px;
            }
        }

        @media (max-width: 991px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .selang-slider,
            .page-content {
                width: 100%;
                margin-left: 0;
            }

            .selang-hero .info {
                width: 100%;
                max-width: none;
                min-height: 100vh;
                justify-content: center;
                padding: 0 30px;
                text-align: center;
            }

            .selang-hero .info > div {
                max-width: 620px;
                margin: 0 auto;
            }

            .selang-hero .info h1 {
                font-size: 38px;
            }

            #ringkasan,
            #daftar-selang {
                padding-left: 20px;
                padding-right: 20px;
            }

            .table-panel-header,
            .pagination-wrap {
                padding-left: 20px;
                padding-right: 20px;
            }

            .table-responsive-wrap {
                padding-left: 20px;
                padding-right: 20px;
            }

            .panel-actions a {
                width: 100%;
            }

            .hero-actions .button {
                display: block;
                margin-right: 0;
            }

            .hero-actions .button a {
                width: 100%;
                text-align: center;
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
                    <li><a href="#top">Hero</a></li>
                    <li><a href="#ringkasan">Ringkasan</a></li>
                    <li><a href="#daftar-selang">Tabel Selang</a></li>
                    <li><a href="selangtambah.php">Tambah Selang</a></li>
                    <li><a href="../../home.php">Dashboard</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="sidebar-navigation hidde-sm hidden-xs">
        <div class="logo">
            <a href="../../home.php">SPBU<em>Selang</em></a>
        </div>
        <nav>
            <ul>
                <li>
                    <a href="#top">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Hero Selang
                    </a>
                </li>
                <li>
                    <a href="#ringkasan">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Ringkasan
                    </a>
                </li>
                <li>
                    <a href="#daftar-selang">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Tabel Selang
                    </a>
                </li>
                <li>
                    <a href="selangtambah.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Tambah Selang
                    </a>
                </li>
                <li>
                    <a href="../../home.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Kembali Home
                    </a>
                </li>
            </ul>
        </nav>
        <ul class="social-icons">
            <li><a href="#ringkasan"><i class="fa fa-bar-chart"></i></a></li>
            <li><a href="#daftar-selang"><i class="fa fa-table"></i></a></li>
            <li><a href="selangtambah.php"><i class="fa fa-plus"></i></a></li>
        </ul>
    </div>

    <div class="slider selang-slider">
        <div class="Modern-Slider content-section" id="top">
            <div class="item item-1">
                <div class="img-fill selang-hero">
                    <div class="info">
                        <div>
                            <span class="hero-kicker">Master Selang db_spbu</span>
                            <h1>Kelola Data Selang<br>Dengan Tampilan Baru</h1>
                            <p>Halaman ini menampilkan seluruh data langsung dibaca dari tabel <strong>selang</strong> pada database <strong>db_spbu</strong> dengan relasi ke <strong>id_pompa</strong>.</p>
                            <div class="hero-actions">
                                <div class="white-button button">
                                    <a href="#daftar-selang">Lihat Tabel Selang</a>
                                </div>
                                <div class="accent-button button">
                                    <a href="selangtambah.php">Tambah Selang</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section id="ringkasan" class="content-section">
            <div class="section-heading">
                <h1>Ringkasan<br><em>Selang</em></h1>
            </div>
            <div class="section-content">
                <div class="stats-grid">
                    <div class="stats-card">
                        <span>Total Data</span>
                        <strong><?php echo number_format($totalRows, 0, ',', '.'); ?></strong>
                        <p>Jumlah keseluruhan baris yang tersimpan pada tabel <strong>selang</strong>.</p>
                    </div>
                    <div class="stats-card">
                        <span>Halaman Aktif</span>
                        <strong><?php echo $page; ?>/<?php echo $totalPages; ?></strong>
                        <p>Pagination tetap aktif untuk menjaga tampilan daftar selang tetap rapi.</p>
                    </div>
                    <div class="stats-card">
                        <span>Kolom DB</span>
                        <strong>4</strong>
                        <p>Kolom utama yang dipakai: <strong>id_selang</strong>, <strong>no_selang</strong>, <strong>id_pompa</strong>, dan <strong>created_at</strong>.</p>
                    </div>
                </div>
                <div class="info-strip<?php echo $queryError !== null ? ' is-error' : ''; ?>">
                    <?php if ($queryError !== null) { ?>
                        <?php echo htmlspecialchars($queryError, ENT_QUOTES, 'UTF-8'); ?>
                    <?php } else { ?>
                        Menampilkan data <strong><?php echo $displayStart; ?> - <?php echo $displayEnd; ?></strong> dari total <strong><?php echo number_format($totalRows, 0, ',', '.'); ?></strong> data selang yang ada pada database <strong>db_spbu</strong>.
                    <?php } ?>
                </div>
            </div>
        </section>

        <section id="daftar-selang" class="content-section">
            <div class="section-heading">
                <h1>Tabel <em>Selang</em></h1>
                <p>Semua unsur tabel daftar selang dengan aksi tambah, ubah, dan hapus yang sesuai modul.</p>
            </div>
            <div class="section-content">
                <div class="table-panel">
                    <div class="table-panel-header">
                        <div>
                            <h4>Daftar Data Selang</h4>
                            <p>Data terbaru ditampilkan paling atas berdasarkan <strong>waktu input</strong>, lalu <strong>id_selang</strong> dari tabel <strong>selang</strong>.</p>
                        </div>
                        <div class="panel-actions">
                            <a class="primary-action" href="selangtambah.php">Tambah Selang</a>
                            <a class="secondary-action" href="../../home.php">Kembali ke Home</a>
                        </div>
                    </div>

                    <?php if ($viewState === 'data') { ?>
                        <div class="table-responsive-wrap">
                            <table class="selang-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>ID Selang</th>
                                        <th>Nomor Selang</th>
                                        <th>ID Pompa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($selangRows as $index => $data) { ?>
                                        <tr>
                                            <td><?php echo $start + $index + 1; ?></td>
                                            <td><span class="selang-id"><?php echo htmlspecialchars($data['id_selang'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                            <td><?php echo htmlspecialchars($data['no_selang'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($data['id_pompa'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <div class="action-group">
                                                    <a class="edit-link" href="selangubah.php?id_selang=<?php echo urlencode($data['id_selang']); ?>">Ubah</a>
                                                    <a class="delete-link" href="selanghapus.php?id_selang=<?php echo urlencode($data['id_selang']); ?>" onclick="return confirm('Yakin ingin menghapus data selang ini?');">Hapus</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="pagination-wrap">
                            <div class="pagination-caption">
                                Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?>.
                            </div>
                            <div class="pagination-list">
                                <?php if ($page > 1) { ?>
                                    <a href="?page=<?php echo $page - 1; ?>">Prev</a>
                                <?php } ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                    <?php if ($i === $page) { ?>
                                        <span class="current"><?php echo $i; ?></span>
                                    <?php } else { ?>
                                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    <?php } ?>
                                <?php } ?>

                                <?php if ($page < $totalPages) { ?>
                                    <a href="?page=<?php echo $page + 1; ?>">Next</a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } elseif ($viewState === 'empty') { ?>
                        <div class="empty-state">
                            <h4>Data Selang Belum Ada</h4>
                            <p>Tabel <strong>selang</strong> sudah berhasil dibaca, tetapi belum memiliki data untuk ditampilkan.</p>
                            <div class="accent-button button">
                                <a href="selangtambah.php">Input Selang Pertama</a>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="empty-state">
                            <h4>Data Tidak Bisa Ditampilkan</h4>
                            <p>Periksa kembali koneksi atau struktur tabel <strong>selang</strong> pada database <strong>db_spbu</strong>.</p>
                        </div>
                    <?php } ?>
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

    <script>
        var didScroll;
        var lastScrollTop = 0;
        var delta = 5;
        var navbarHeight = $('header').outerHeight();

        $(window).scroll(function () {
            didScroll = true;
        });

        setInterval(function () {
            if (didScroll) {
                hasScrolled();
                didScroll = false;
            }
        }, 250);

        function hasScrolled() {
            var st = $(this).scrollTop();

            if (Math.abs(lastScrollTop - st) <= delta) {
                return;
            }

            if (st > lastScrollTop && st > navbarHeight) {
                $('header').removeClass('nav-down').addClass('nav-up');
            } else if (st + $(window).height() < $(document).height()) {
                $('header').removeClass('nav-up').addClass('nav-down');
            }

            lastScrollTop = st;
        }
    </script>
</body>

</html>



