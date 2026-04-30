<?php
require_once __DIR__ . '/../../koneksi.php';

$idTransaksi = isset($_GET['id_transaksi']) ? trim($_GET['id_transaksi']) : '';

if ($idTransaksi === '') {
    header('Location: transaksilihat.php');
    exit;
}

$idTransaksiEscaped = mysqli_real_escape_string($conn, $idTransaksi);
$result = mysqli_query(
    $conn,
    "SELECT
        t.id_transaksi,
        t.tanggal_jam,
        t.liter,
        t.total,
        t.id_bbm,
        t.id_selang,
        t.id_operator,
        b.jenis_bbm,
        b.harga_per_liter_per_jenis,
        s.no_selang,
        s.id_pompa,
        p.no_pompa,
        o.nama_operator
    FROM transaksi t
    LEFT JOIN bbm b ON t.id_bbm = b.id_bbm
    LEFT JOIN selang s ON t.id_selang = s.id_selang
    LEFT JOIN pompa p ON s.id_pompa = p.id_pompa
    LEFT JOIN operator o ON t.id_operator = o.id_operator
    WHERE t.id_transaksi = '$idTransaksiEscaped'
    LIMIT 1"
);

$data = $result ? mysqli_fetch_assoc($result) : null;

if (!$data) {
    header('Location: transaksilihat.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Detail Transaksi - SPBU Hasanuddin</title>
    <meta name="description" content="Detail satu data transaksi pada database db_spbu">
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
        :root { --detail-sidebar: 320px; --detail-gap: clamp(24px, 4vw, 56px); }
        body { background: #f4f1ea; overflow-x: hidden; }
        .sidebar-navigation { width: var(--detail-sidebar); overflow: hidden; }
        .sidebar-navigation .logo { height: 122px; padding: 16px 24px; }
        .sidebar-navigation .logo a { height: 90px; line-height: 90px; }
        .sidebar-navigation nav { position: absolute; top: 146px; left: 0; right: 0; width: 100%; max-height: calc(100vh - 236px); overflow-y: auto; transform: none; padding: 14px 34px 30px; }
        .sidebar-navigation ul { margin-left: 0; }
        .sidebar-navigation li { padding: 8px 0; }
        .sidebar-navigation .social-icons { left: 0; right: 0; bottom: 22px; }
        .detail-slider, .page-content { width: calc(100% - var(--detail-sidebar)); margin-left: var(--detail-sidebar); float: none; }
        .detail-slider .content-section, .page-content .content-section { left: auto; width: 100%; min-width: 0; transform: none; }
        .detail-hero { width: 100%; min-height: 100vh; display: block; background: linear-gradient(120deg, rgba(15, 20, 44, 0.84), rgba(69, 72, 154, 0.62)), url('../../img/SPBUMaster.jpg') center center / cover no-repeat; }
        .detail-hero .info { position: static; width: 100%; min-height: 100vh; display: flex; align-items: center; padding: 80px var(--detail-gap) 56px; text-align: left; transform: none; }
        .detail-hero .info > div { max-width: 760px; }
        .hero-kicker { display: inline-block; margin-bottom: 22px; padding: 8px 18px; border-radius: 999px; background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; font-size: 12px; font-weight: 700; letter-spacing: 1.6px; text-transform: uppercase; }
        .detail-hero .info h1 { margin-bottom: 18px; font-size: 50px; line-height: 1.08; }
        .detail-hero .info p { font-size: 17px; line-height: 1.9; color: rgba(255, 255, 255, 0.9); }
        .hero-actions { margin-top: 34px; }
        .hero-actions .button { display: inline-block; margin-right: 14px; margin-bottom: 14px; }
        #detail-transaksi { padding: 28px var(--detail-gap) 80px; }
        .detail-panel { background: #fff; border-radius: 18px; box-shadow: 0 18px 40px rgba(24, 30, 48, 0.08); }
        .detail-panel { overflow: hidden; }
        .detail-header { padding: 24px 28px; display: flex; align-items: center; justify-content: space-between; gap: 16px; border-bottom: 1px solid #eceaf3; }
        .detail-header > div:first-child { flex: 1 1 auto; text-align: left; }
        .detail-header h4 { margin: 0 0 6px; color: #232323; font-size: 24px; font-weight: 700; }
        .detail-header p { margin: 0; color: #6b6f7a; font-size: 14px; }
        .detail-actions { display: flex; flex-wrap: wrap; gap: 12px; }
        .detail-actions a { display: inline-block; min-width: 130px; padding: 12px 18px; border-radius: 999px; text-decoration: none; text-align: center; font-size: 13px; font-weight: 700; }
        .detail-actions .primary-action { background: #45489a; color: #fff; }
        .detail-actions .secondary-action { background: #f0eef9; color: #45489a; }
        .detail-body { padding: 28px; }
        .detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 22px; }
        .detail-item { padding: 20px; border-radius: 16px; background: #faf9fd; border: 1px solid #eceaf3; }
        .detail-label { display: block; margin-bottom: 10px; color: #707883; font-size: 12px; font-weight: 700; letter-spacing: 1.1px; text-transform: uppercase; }
        .detail-value { color: #232323; font-size: 20px; font-weight: 700; line-height: 1.5; }
        .detail-subvalue { margin-top: 8px; color: #6b7280; font-size: 14px; line-height: 1.7; }

        .slider {
            display: none;
        }

        .page-content {
            min-height: 100vh;
            padding-top: 44px;
            display: flex;
            flex-direction: column;
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

        .responsive-nav li:has(> a[href="#top"]),
        .sidebar-navigation nav li:has(> a[href="#top"]) {
            display: none;
        }

        @media (max-width: 991px) { .detail-grid { grid-template-columns: 1fr; } }
        @media (max-width: 767px) {
            .detail-slider, .page-content { width: 100%; margin-left: 0; }
            .detail-hero .info { padding: 0 30px; text-align: center; justify-content: center; }
            #detail-transaksi { padding-left: 20px; padding-right: 20px; }
            .detail-header { flex-direction: column; align-items: flex-start; }
            .detail-actions a, .hero-actions .button a { width: 100%; text-align: center; }
            .hero-actions .button { display: block; margin-right: 0; }
            .page-content { padding-top: 24px; }
        }
    </style>
</head>
<body>
    <header class="nav-down responsive-nav hidden-lg hidden-md">
        <button type="button" id="nav-toggle" class="navbar-toggle" data-toggle="collapse" data-target="#main-nav">
            <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
        </button>
        <div id="main-nav" class="collapse navbar-collapse">
            <nav><ul class="nav navbar-nav">
                <li><a href="#top">Hero</a></li>
                <li><a href="#detail-transaksi">Detail</a></li>
                <li><a href="transaksilihat.php">Kembali</a></li>
                <li><a href="transaksicetak.php?id_transaksi=<?php echo urlencode($data['id_transaksi']); ?>" target="_blank">Cetak</a></li>
            </ul></nav>
        </div>
    </header>
    <div class="sidebar-navigation hidde-sm hidden-xs">
        <div class="logo"><a href="transaksilihat.php">SPBU<em>Detail</em></a></div>
        <nav><ul>
            <li><a href="#top"><span class="rect"></span><span class="circle"></span>Hero Detail</a></li>
            <li><a href="#detail-transaksi"><span class="rect"></span><span class="circle"></span>Data Lengkap</a></li>
            <li><a href="transaksicetak.php?id_transaksi=<?php echo urlencode($data['id_transaksi']); ?>" target="_blank"><span class="rect"></span><span class="circle"></span>Cetak Data</a></li>
            <li><a href="transaksilihat.php"><span class="rect"></span><span class="circle"></span>Kembali</a></li>
        </ul></nav>
        <ul class="social-icons">
            <li><a href="#detail-transaksi"><i class="fa fa-file-text-o"></i></a></li>
            <li><a href="transaksicetak.php?id_transaksi=<?php echo urlencode($data['id_transaksi']); ?>" target="_blank"><i class="fa fa-print"></i></a></li>
            <li><a href="transaksilihat.php"><i class="fa fa-table"></i></a></li>
        </ul>
    </div>
    <div class="slider detail-slider">
        <div class="Modern-Slider content-section" id="top">
            <div class="item item-1">
                <div class="img-fill detail-hero">
                    <div class="info"><div>
                        <span class="hero-kicker">DETAIL DATA TRANSAKSI</span>
                        <h1>Detail Transaksi<br><em>SPBU Hasanuddin</em></h1>
                        <p>Halaman ini disesuaikan dan menampilkan seluruh informasi dari satu data <strong>transaksi</strong> pada database <strong>db_spbu</strong>.</p>
                        <div class="hero-actions">
                            <div class="white-button button"><a href="#detail-transaksi">Lihat Detail</a></div>
                            <div class="accent-button button"><a href="transaksicetak.php?id_transaksi=<?php echo urlencode($data['id_transaksi']); ?>" target="_blank">Cetak Transaksi</a></div>
                        </div>
                    </div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-content">
        <section id="detail-transaksi" class="content-section">
            <div class="section-heading">
                <h1>Panel<br><em>Detail</em></h1>
                <p>Semua unsur detail transaksi ditampilkan lengkap dalam bentuk panel informasi agar mudah dibaca dan dicetak.</p>
            </div>
            <div class="detail-panel">
                <div class="detail-header">
                    <div><h4>Informasi Lengkap Transaksi</h4><p>Seluruh field inti dan relasi dari satu data transaksi ditampilkan dalam satu halaman.</p></div>
                    <div class="detail-actions">
                        <a class="primary-action" href="transaksicetak.php?id_transaksi=<?php echo urlencode($data['id_transaksi']); ?>" target="_blank">Cetak</a>
                        <a class="secondary-action" href="transaksilihat.php">Kembali</a>
                    </div>
                </div>
                <div class="detail-body">
                    <div class="detail-grid">
                        <div class="detail-item"><span class="detail-label">ID Transaksi</span><div class="detail-value"><?php echo htmlspecialchars($data['id_transaksi'], ENT_QUOTES, 'UTF-8'); ?></div></div>
                        <div class="detail-item"><span class="detail-label">Tanggal & Jam</span><div class="detail-value"><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime((string) $data['tanggal_jam'])), ENT_QUOTES, 'UTF-8'); ?></div></div>
                        <div class="detail-item"><span class="detail-label">BBM</span><div class="detail-value"><?php echo htmlspecialchars((string) $data['jenis_bbm'], ENT_QUOTES, 'UTF-8'); ?></div><div class="detail-subvalue">ID BBM: <?php echo htmlspecialchars((string) $data['id_bbm'], ENT_QUOTES, 'UTF-8'); ?></div></div>
                        <div class="detail-item"><span class="detail-label">Harga per Liter</span><div class="detail-value">Rp <?php echo number_format((float) $data['harga_per_liter_per_jenis'], 0, ',', '.'); ?></div></div>
                        <div class="detail-item"><span class="detail-label">Selang</span><div class="detail-value"><?php echo htmlspecialchars((string) $data['no_selang'], ENT_QUOTES, 'UTF-8'); ?></div><div class="detail-subvalue">ID Selang: <?php echo htmlspecialchars((string) $data['id_selang'], ENT_QUOTES, 'UTF-8'); ?></div></div>
                        <div class="detail-item"><span class="detail-label">Pompa</span><div class="detail-value"><?php echo htmlspecialchars((string) $data['no_pompa'], ENT_QUOTES, 'UTF-8'); ?></div><div class="detail-subvalue">ID Pompa: <?php echo htmlspecialchars((string) $data['id_pompa'], ENT_QUOTES, 'UTF-8'); ?></div></div>
                        <div class="detail-item"><span class="detail-label">Operator</span><div class="detail-value"><?php echo htmlspecialchars((string) $data['nama_operator'], ENT_QUOTES, 'UTF-8'); ?></div><div class="detail-subvalue">ID Operator: <?php echo htmlspecialchars((string) $data['id_operator'], ENT_QUOTES, 'UTF-8'); ?></div></div>
                        <div class="detail-item"><span class="detail-label">Liter</span><div class="detail-value"><?php echo number_format((float) $data['liter'], 2, ',', '.'); ?> L</div></div>
                        <div class="detail-item"><span class="detail-label">Total</span><div class="detail-value">Rp <?php echo number_format((float) $data['total'], 0, ',', '.'); ?></div></div>
                        <div class="detail-item"><span class="detail-label">Validasi Perhitungan</span><div class="detail-value">Rp <?php echo number_format((float) $data['harga_per_liter_per_jenis'] * (float) $data['liter'], 0, ',', '.'); ?></div><div class="detail-subvalue">Nilai ini dihitung dari harga BBM per liter x jumlah liter.</div></div>
                    </div>
                </div>
            </div>
        </section>
        <section class="footer">
            <p>Copyright &copy; 2019 Company Name . Design: TemplateMo</p>
        </section>
    </div>
    <script src="../../js/vendor/jquery-1.11.2.min.js"></script>
    <script src="../../js/vendor/bootstrap.min.js"></script>
    <script src="../../js/plugins.js"></script>
    <script src="../../js/main.js"></script>
</body>
</html>
