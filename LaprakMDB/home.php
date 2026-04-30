<?php
require_once __DIR__ . '/koneksi.php';

function tableExists($conn, $tableName)
{
    if (!$conn) {
        return false;
    }

    $safeTableName = mysqli_real_escape_string($conn, $tableName);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$safeTableName'");

    return $result && mysqli_num_rows($result) > 0;
}

function getTableCount($conn, $tableName)
{
    if (!$conn || !tableExists($conn, $tableName)) {
        return null;
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$tableName`");

    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_assoc($result);

    return isset($row['total']) ? (int) $row['total'] : 0;
}

function resolveModuleStat($conn, $preferredTables)
{
    foreach ($preferredTables as $index => $tableName) {
        $count = getTableCount($conn, $tableName);

        if ($count !== null) {
            return array(
                'available' => true,
                'count' => $count,
                'table' => $tableName,
                'fallback' => $index > 0,
            );
        }
    }

    return array(
        'available' => false,
        'count' => 0,
        'table' => null,
        'fallback' => false,
    );
}

$masterModules = array(
    array(
        'title' => 'Master BBM',
        'summary' => 'Kelola daftar BBM yang tampil di sistem master.',
        'icon' => 'fa fa-tint',
        'list_url' => 'PenjualanMaster/bbm/bbmlihat.php',
        'add_url' => 'PenjualanMaster/bbm/bbmtambah.php',
        'preferred_tables' => array('bbm'),
    ),
    array(
        'title' => 'Master Operator',
        'summary' => 'Akses data operator dan halaman pengelolaan yang tersedia.',
        'icon' => 'fa fa-users',
        'list_url' => 'PenjualanMaster/operator/operatorlihat.php',
        'add_url' => 'PenjualanMaster/operator/operatortambah.php',
        'preferred_tables' => array('operator'),
    ),
    array(
        'title' => 'Master Pompa',
        'summary' => 'Pantau data pompa serta akses cepat ke halaman master pompa.',
        'icon' => 'fa fa-dashboard',
        'list_url' => 'PenjualanMaster/pompa/pompalihat.php',
        'add_url' => 'PenjualanMaster/pompa/pompatambah.php',
        'preferred_tables' => array('pompa'),
    ),
    array(
        'title' => 'Master Selang',
        'summary' => 'Kelola data selang dengan tampilan yang seragam dengan modul lain.',
        'icon' => 'fa fa-random',
        'list_url' => 'PenjualanMaster/selang/selanglihat.php',
        'add_url' => 'PenjualanMaster/selang/selangtambah.php',
        'preferred_tables' => array('selang'),
    ),
    array(
        'title' => 'Transaksi',
        'summary' => 'Akses tabel transaksi SPBU lengkap dengan relasi BBM, selang, pompa, dan operator.',
        'icon' => 'fa fa-exchange',
        'list_url' => 'SPBUTransaksi/Transaksi/transaksilihat.php',
        'add_url' => 'SPBUTransaksi/Transaksi/transaksitambah.php',
        'preferred_tables' => array('transaksi'),
    ),
);

$missingModules = array();
$fallbackModules = array();
$uniqueTableCounts = array();
$statusType = 'success';
$statusMessage = 'Dashboard home sudah disesuaikan dengan tampilan index.php dan menu modul master maupun transaksi yang tersedia.';

foreach ($masterModules as $index => $module) {
    $masterModules[$index]['stat'] = array(
        'available' => false,
        'count' => 0,
        'table' => null,
        'fallback' => false,
    );
}

if (!$conn) {
    $statusType = 'danger';
    $statusMessage = 'Koneksi ke database db_spbu gagal, jadi statistik belum bisa dimuat.';
} else {
    foreach ($masterModules as $index => $module) {
        $stat = resolveModuleStat($conn, $module['preferred_tables']);
        $masterModules[$index]['stat'] = $stat;

        if (!$stat['available']) {
            $missingModules[] = $module['title'];
            continue;
        }

        if ($stat['fallback']) {
            $fallbackModules[] = $module['title'];
        }

        if (!isset($uniqueTableCounts[$stat['table']])) {
            $uniqueTableCounts[$stat['table']] = $stat['count'];
        }
    }

    if (!empty($missingModules) && !empty($fallbackModules)) {
        $statusType = 'warning';
        $statusMessage = 'Sebagian tabel utama belum tersedia. Modul ' . implode(', ', $fallbackModules) . ' memakai tabel cadangan yang tersedia, sedangkan ' . implode(', ', $missingModules) . ' masih tampil 0.';
    } elseif (!empty($fallbackModules)) {
        $statusType = 'warning';
        $statusMessage = 'Sebagian modul master masih memakai tabel cadangan yang tersedia: ' . implode(', ', $fallbackModules) . '.';
    } elseif (!empty($missingModules)) {
        $statusType = 'warning';
        $statusMessage = 'Tabel untuk modul ' . implode(', ', $missingModules) . ' belum ditemukan, jadi nilainya masih 0.';
    }
}

$totalModules = count($masterModules);
$readyModules = $totalModules - count($missingModules);
$activeTableCount = count($uniqueTableCounts);
$totalRecords = array_sum($uniqueTableCounts);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Home - SPBU Hasanuddin</title>
    <meta name="description" content="Dashboard master SPBU Hasanuddin">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/fontAwesome.css">
    <link rel="stylesheet" href="css/light-box.css">
    <link rel="stylesheet" href="css/owl-carousel.css">
    <link rel="stylesheet" href="css/templatemo-style.css">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

    <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    <style>
        :root {
            --dashboard-sidebar: 320px;
            --dashboard-gap: clamp(24px, 4vw, 56px);
            --dashboard-max: 1220px;
        }

        body {
            background: #f3f1ec;
            overflow-x: hidden;
        }

        .sidebar-navigation {
            width: var(--dashboard-sidebar);
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
            padding: 12px 34px 30px;
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

        .dashboard-slider,
        .page-content.home-dashboard {
            width: calc(100% - var(--dashboard-sidebar));
            margin-left: var(--dashboard-sidebar);
            float: none;
        }

        .dashboard-slider {
            position: relative;
        }

        .dashboard-slider .content-section {
            left: auto;
            min-width: 0;
            width: 100%;
            transform: none;
        }

        .page-content.home-dashboard {
            background: linear-gradient(180deg, #f3f1ec 0%, #fbfaf7 50%, #f0ece4 100%);
        }

        .home-dashboard .content-section {
            left: auto;
            width: 100%;
            padding: 88px var(--dashboard-gap) 0;
            transform: none;
        }

        .home-dashboard .section-heading,
        .home-dashboard .section-content,
        .dashboard-notice {
            max-width: var(--dashboard-max);
            margin-left: auto;
            margin-right: auto;
        }

        .home-dashboard .section-heading {
            margin-bottom: 42px;
        }

        .dashboard-slide {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(22, 28, 36, 0.94), rgba(187, 96, 35, 0.86));
        }

        .dashboard-slide .info {
            position: static;
            top: auto;
            right: auto;
            left: auto;
            display: block;
            min-height: 100vh;
            text-align: left;
            transform: none;
        }

        .hero-layout {
            min-height: 100vh;
            max-width: 1320px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            padding: 72px var(--dashboard-gap);
        }

        .hero-copy {
            max-width: 680px;
            color: #ffffff;
            flex: 1 1 auto;
        }

        .hero-pill {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .hero-copy h1 {
            margin: 0 0 18px;
            font-size: 58px;
            line-height: 1.1;
            color: #ffffff;
        }

        .hero-copy h1 em {
            font-style: normal;
            color: #f3b36a;
        }

        .hero-copy p {
            max-width: 560px;
            font-size: 18px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 30px;
        }

        .hero-actions .button {
            display: inline-block;
            margin-right: 12px;
            margin-bottom: 12px;
        }

        .hero-panel {
            width: 100%;
            max-width: 360px;
            flex: 0 0 360px;
            padding: 32px 28px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(4px);
        }

        .hero-panel img {
            width: 96px;
            height: 96px;
            object-fit: contain;
            display: block;
            margin-bottom: 22px;
        }

        .hero-panel h4 {
            margin: 0 0 8px;
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
        }

        .hero-panel p {
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .hero-stat {
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(15, 18, 24, 0.28);
        }

        .hero-stat strong {
            display: block;
            font-size: 34px;
            line-height: 1;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .hero-stat span {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .dashboard-notice {
            margin-bottom: 50px;
            padding: 18px 22px;
            border-radius: 16px;
            font-size: 15px;
            line-height: 1.7;
            border: 1px solid transparent;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.06);
        }

        .dashboard-notice.notice-success {
            background: #eef8ef;
            border-color: #b7d9ba;
            color: #31573a;
        }

        .dashboard-notice.notice-warning {
            background: #fff6e6;
            border-color: #efc779;
            color: #76520d;
        }

        .dashboard-notice.notice-danger {
            background: #fff0ef;
            border-color: #e4a7a2;
            color: #8c2e26;
        }

        .dashboard-grid,
        .master-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 28px;
        }

        .dashboard-card,
        .master-card,
        .overview-panel {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 16px 40px rgba(31, 35, 41, 0.08);
        }

        .dashboard-card {
            padding: 28px;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card:before {
            content: '';
            position: absolute;
            inset: 0 0 auto auto;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(223, 154, 84, 0.22) 0%, rgba(223, 154, 84, 0) 70%);
        }

        .card-icon {
            width: 62px;
            height: 62px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f3a043, #d36a1f);
            color: #ffffff;
            font-size: 24px;
            margin-bottom: 18px;
        }

        .card-kicker {
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.8px;
            color: #bb6a27;
            text-transform: uppercase;
        }

        .dashboard-card h4,
        .master-card h4,
        .overview-block h4 {
            margin: 0 0 10px;
            color: #1d232b;
            font-weight: 700;
        }

        .card-value {
            font-size: 42px;
            line-height: 1;
            font-weight: 800;
            color: #bb6a27;
            margin: 16px 0 14px;
        }

        .dashboard-card p,
        .master-card p,
        .overview-block p,
        .overview-block li {
            color: #5f6771;
            line-height: 1.8;
        }

        .card-meta,
        .master-meta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #f4efe8;
            color: #7b5a2f;
            font-size: 13px;
            font-weight: 600;
        }

        .card-meta.is-muted,
        .master-meta.is-muted {
            background: #f3f4f6;
            color: #707883;
        }

        .master-card {
            padding: 28px;
        }

        .master-head {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .master-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #1e2833;
            color: #f3b36a;
            font-size: 22px;
            flex-shrink: 0;
        }

        .master-actions {
            margin-top: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .master-link {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.25s ease;
        }

        .master-link.primary {
            background: #bb6a27;
            color: #ffffff;
        }

        .master-link.primary:hover {
            background: #96521b;
            color: #ffffff;
        }

        .master-link.secondary {
            background: #f4efe8;
            color: #6d481f;
        }

        .master-link.secondary:hover {
            background: #ead9c3;
            color: #6d481f;
        }

        .overview-panel {
            padding: 30px;
        }

        .overview-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .overview-block ul {
            padding-left: 18px;
            margin: 0;
        }

        .table-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .table-tag {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #1e2833;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
        }

        .table-tag span {
            color: #f3b36a;
            margin-left: 6px;
        }

        @media (max-width: 1399px) {
            :root {
                --dashboard-sidebar: 296px;
            }
        }

        @media (max-width: 1199px) {
            .hero-layout {
                flex-direction: column;
                align-items: flex-start;
                justify-content: center;
                min-height: auto;
                padding-top: 96px;
                padding-bottom: 72px;
            }

            .hero-panel {
                width: 100%;
                max-width: 520px;
                flex: 1 1 auto;
            }
        }

        @media (max-width: 991px) {
            .sidebar-navigation {
                display: none;
            }

            .dashboard-slider,
            .page-content.home-dashboard {
                width: 100%;
                margin-left: 0;
            }

            .dashboard-slide,
            .dashboard-slide .info,
            .hero-layout {
                min-height: auto;
            }

            .dashboard-grid,
            .master-grid,
            .overview-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-notice {
                margin-left: var(--dashboard-gap);
                margin-right: var(--dashboard-gap);
            }
        }

        @media (max-width: 767px) {
            .hero-layout {
                padding: 90px 24px 60px;
            }

            .home-dashboard .content-section {
                padding: 72px 24px 0;
            }

            .dashboard-notice {
                margin: 0 24px 38px;
            }

            .hero-copy h1 {
                font-size: 40px;
            }

            .hero-copy p {
                font-size: 16px;
            }

            .dashboard-card,
            .master-card,
            .overview-panel {
                padding: 22px;
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
                    <li><a href="#top">Dashboard</a></li>
                    <li><a href="#stats">Statistik</a></li>
                    <li><a href="#master">Master</a></li>
                    <li><a href="#overview">Overview</a></li>
                    <li><a href="PenjualanMaster/bbm/bbmlihat.php">BBM</a></li>
                    <li><a href="PenjualanMaster/operator/operatorlihat.php">Operator</a></li>
                    <li><a href="PenjualanMaster/pompa/pompalihat.php">Pompa</a></li>
                    <li><a href="PenjualanMaster/selang/selanglihat.php">Selang</a></li>
                    <li><a href="SPBUTransaksi/Transaksi/transaksilihat.php">Transaksi</a></li>
                    <li><a href="index.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="sidebar-navigation hidde-sm hidden-xs">
        <div class="logo">
            <a href="home.php">SPBU<em>Master</em></a>
        </div>
        <nav>
            <ul>
                <li>
                    <a href="#top">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="#stats">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Statistik
                    </a>
                </li>
                <li>
                    <a href="#master">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Menu Master
                    </a>
                </li>
                <li>
                    <a href="PenjualanMaster/bbm/bbmlihat.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Master BBM
                    </a>
                </li>
                <li>
                    <a href="PenjualanMaster/operator/operatorlihat.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Master Operator
                    </a>
                </li>
                <li>
                    <a href="PenjualanMaster/pompa/pompalihat.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Master Pompa
                    </a>
                </li>
                <li>
                    <a href="PenjualanMaster/selang/selanglihat.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Master Selang
                    </a>
                </li>
                <li>
                    <a href="SPBUTransaksi/Transaksi/transaksilihat.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Transaksi
                    </a>
                </li>
                <li>
                    <a href="index.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
        <ul class="social-icons">
            <li><a href="#stats"><i class="fa fa-bar-chart"></i></a></li>
            <li><a href="#master"><i class="fa fa-database"></i></a></li>
            <li><a href="SPBUTransaksi/Transaksi/transaksilihat.php"><i class="fa fa-exchange"></i></a></li>
            <li><a href="#overview"><i class="fa fa-folder-open"></i></a></li>
        </ul>
    </div>

    <div class="slider dashboard-slider">
        <div class="Modern-Slider content-section" id="top">
            <div class="item item-1">
                <div class="img-fill dashboard-slide">
                    <div class="info">
                        <div class="hero-layout">
                            <div class="hero-copy">
                                <span class="hero-pill">Home Dashboard</span>
                                <h1>SPBU Hasanuddin<br><em>Master Control</em></h1>
                                <p>Halaman home ini berfokus sebagai pusat kontrol untuk modul master dan tabel transaksi yang ada di project.</p>
                                <div class="hero-actions">
                                    <div class="white-button button">
                                        <a href="#master">Buka Menu Master</a>
                                    </div>
                                    <div class="white-button button">
                                        <a href="SPBUTransaksi/Transaksi/transaksilihat.php">Buka Transaksi</a>
                                    </div>
                                    <div class="accent-button button">
                                        <a href="#stats">Lihat Statistik</a>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-panel">
                                <img src="logo_spbu.png" alt="Logo SPBU Hasanuddin">
                                <h4>Ringkasan Cepat</h4>
                                <p>Semua tombol diarahkan ke modul master di folder <strong>PenjualanMaster</strong> dan modul transaksi di folder <strong>SPBUTransaksi</strong> agar sesuai dengan struktur project. </p>
                                <div class="hero-stats">
                                    <div class="hero-stat">
                                        <strong><?php echo $totalModules; ?></strong>
                                        <span>Modul tersedia</span>
                                    </div>
                                    <div class="hero-stat">
                                        <strong><?php echo $readyModules; ?></strong>
                                        <span>Modul siap dibaca dari tabel aktif</span>
                                    </div>
                                    <div class="hero-stat">
                                        <strong><?php echo $totalRecords; ?></strong>
                                        <span>Total data dari tabel yang sedang dipakai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content home-dashboard">
        <div class="dashboard-notice notice-<?php echo htmlspecialchars($statusType, ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8'); ?>
        </div>

        <section id="stats" class="content-section">
            <div class="section-heading">
                <h1>Statistik<br><em>Master</em></h1>
                <p>Ringkasan ini mempertahankan fungsi hitung data, lalu menyesuaikannya dengan modul master dan transaksi yang benar-benar ada di project.</p>
            </div>
            <div class="section-content">
                <div class="dashboard-grid">
                    <?php foreach ($masterModules as $module) { ?>
                        <div class="dashboard-card">
                            <div class="card-icon">
                                <i class="<?php echo htmlspecialchars($module['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                            </div>
                            <span class="card-kicker">Dashboard Card</span>
                            <h4><?php echo htmlspecialchars($module['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                            <div class="card-value"><?php echo $module['stat']['count']; ?></div>
                            <p><?php echo htmlspecialchars($module['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php if ($module['stat']['available']) { ?>
                                <div class="card-meta<?php echo $module['stat']['fallback'] ? '' : ' is-muted'; ?>">
                                    Sumber tabel: <?php echo htmlspecialchars($module['stat']['table'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php } else { ?>
                                <div class="card-meta is-muted">
                                    Belum ada tabel yang cocok untuk modul ini
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>

        <section id="master" class="content-section">
            <div class="section-heading">
                <h1>Menu<br><em>Master</em></h1>
                <p>Bagian ini menyesuaikan home dengan master utama serta akses cepat menuju tabel transaksi.</p>
            </div>
            <div class="section-content">
                <div class="master-grid">
                    <?php foreach ($masterModules as $module) { ?>
                        <div class="master-card">
                            <div class="master-head">
                                <div class="master-icon">
                                    <i class="<?php echo htmlspecialchars($module['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                                </div>
                                <div>
                                    <h4><?php echo htmlspecialchars($module['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <?php if ($module['stat']['available']) { ?>
                                        <div class="master-meta<?php echo $module['stat']['fallback'] ? '' : ' is-muted'; ?>">
                                            Total data: <?php echo $module['stat']['count']; ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="master-meta is-muted">
                                            Total data: 0
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <p><?php echo htmlspecialchars($module['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="master-actions">
                                <a class="master-link primary" href="<?php echo htmlspecialchars($module['list_url'], ENT_QUOTES, 'UTF-8'); ?>">Lihat Data</a>
                                <a class="master-link secondary" href="<?php echo htmlspecialchars($module['add_url'], ENT_QUOTES, 'UTF-8'); ?>">Tambah Data</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>

        <section id="overview" class="content-section">
            <div class="section-heading">
                <h1>Overview<br><em>Sistem</em></h1>
                <p>Catatan singkat tentang sumber data yang sedang dipakai dashboard setelah home disesuaikan dengan struktur master.</p>
            </div>
            <div class="section-content">
                <div class="overview-panel">
                    <div class="overview-grid">
                        <div class="overview-block">
                            <h4>Status Integrasi</h4>
                            <p><?php echo htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                            <p>Jumlah tabel aktif yang sedang dipakai dashboard: <strong><?php echo $activeTableCount; ?></strong>.</p>
                        </div>
                        <div class="overview-block">
                            <h4>Tabel Aktif</h4>
                            <?php if (!empty($uniqueTableCounts)) { ?>
                                <div class="table-tags">
                                    <?php foreach ($uniqueTableCounts as $tableName => $count) { ?>
                                        <div class="table-tag">
                                            <?php echo htmlspecialchars($tableName, ENT_QUOTES, 'UTF-8'); ?>
                                            <span><?php echo $count; ?> data</span>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <p>Belum ada tabel aktif yang bisa dipakai oleh dashboard.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="footer">
            <p>Copyright &copy; 2019 Company Name
                . Design: TemplateMo</p>
        </section>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

    <script>
        var didScroll;
        var lastScrollTop = 0;
        var delta = 5;
        var navbarHeight = $('header').outerHeight();

        $(window).scroll(function() {
            didScroll = true;
        });

        setInterval(function() {
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
