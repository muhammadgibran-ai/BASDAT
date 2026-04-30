<?php
require_once __DIR__ . '/../../koneksi.php';

$limit = 5;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$start = ($page - 1) * $limit;
$totalRows = 0;
$totalPages = 1;
$transaksiRows = array();
$queryError = null;

$countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi");
if ($countResult) {
    $countRow = mysqli_fetch_assoc($countResult);
    $totalRows = (int) ($countRow['total'] ?? 0);
    $totalPages = max(1, (int) ceil($totalRows / $limit));
    if ($page > $totalPages) {
        $page = $totalPages;
        $start = ($page - 1) * $limit;
    }
} else {
    $queryError = 'Gagal menghitung data transaksi: ' . mysqli_error($conn);
}

if ($queryError === null) {
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
        ORDER BY t.tanggal_jam DESC, t.id_transaksi DESC
        LIMIT $start, $limit"
    );

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $transaksiRows[] = $row;
        }
    } else {
        $queryError = 'Gagal memuat data transaksi: ' . mysqli_error($conn);
    }
}

$displayStart = $totalRows > 0 ? $start + 1 : 0;
$displayEnd = $totalRows > 0 ? min($start + $limit, $totalRows) : 0;
$viewState = 'data';
if ($queryError !== null) {
    $viewState = 'error';
} elseif (empty($transaksiRows)) {
    $viewState = 'empty';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Data Transaksi - SPBU Hasanuddin</title>
    <meta name="description" content="Daftar data transaksi pada database db_spbu">
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
        :root { --trx-sidebar: 320px; --trx-gap: clamp(24px, 4vw, 56px); }
        body { background: #f4f1ea; overflow-x: hidden; }
        .sidebar-navigation { width: var(--trx-sidebar); overflow: hidden; }
        .sidebar-navigation .logo { height: 122px; padding: 16px 24px; }
        .sidebar-navigation .logo a { height: 90px; line-height: 90px; }
        .sidebar-navigation nav { position: absolute; top: 146px; left: 0; right: 0; width: 100%; max-height: calc(100vh - 236px); overflow-y: auto; transform: none; padding: 14px 34px 30px; }
        .sidebar-navigation ul { margin-left: 0; }
        .sidebar-navigation li { padding: 8px 0; }
        .sidebar-navigation .social-icons { left: 0; right: 0; bottom: 22px; }
        .transaksi-slider, .page-content { width: calc(100% - var(--trx-sidebar)); margin-left: var(--trx-sidebar); float: none; }
        .transaksi-slider .content-section, .page-content .content-section { left: auto; width: 100%; min-width: 0; transform: none; }
        .transaksi-hero { width: 100%; min-height: 100vh; height: 100vh; display: block; background: linear-gradient(120deg, rgba(15, 20, 44, 0.84), rgba(69, 72, 154, 0.62)), url('../../img/SPBUMaster.jpg') center center / cover no-repeat; }
        .transaksi-hero .info { position: static; width: 100%; min-height: 100vh; display: flex; align-items: center; padding: 72px var(--trx-gap); text-align: left; transform: none; }
        .transaksi-hero .info > div { max-width: 760px; }
        .hero-kicker { display: inline-block; margin-bottom: 22px; padding: 8px 18px; border-radius: 999px; background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; font-size: 12px; font-weight: 700; letter-spacing: 1.6px; text-transform: uppercase; }
        .transaksi-hero .info h1 { margin-bottom: 18px; font-size: 54px; line-height: 1.08; }
        .transaksi-hero .info p { font-size: 17px; line-height: 1.9; color: rgba(255, 255, 255, 0.9); }
        .hero-actions { margin-top: 34px; }
        .hero-actions .button { display: inline-block; margin-right: 14px; margin-bottom: 14px; }
        #ringkasan, #daftar-transaksi { padding-left: var(--trx-gap); padding-right: var(--trx-gap); padding-bottom: 80px; }
        #daftar-transaksi { padding-top: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
        .stats-card, .table-panel, .info-strip { background: #fff; border-radius: 18px; box-shadow: 0 18px 40px rgba(24, 30, 48, 0.08); }
        .stats-card { padding: 28px; text-align: left; }
        .stats-card span { display: inline-block; margin-bottom: 12px; color: #45489a; font-size: 12px; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase; }
        .stats-card > strong { display: block; margin-bottom: 14px; color: #232323; font-size: 42px; line-height: 1; }
        .stats-card p { margin: 0; color: #6b6f7a; font-size: 15px; line-height: 1.8; }
        .info-strip { margin-top: 26px; padding: 18px 22px; border-left: 5px solid #45489a; color: #3f4652; font-size: 15px; line-height: 1.8; }
        .info-strip.is-error { border-left-color: #bb2d3b; background: #fff5f5; color: #8d2130; }
        .table-panel { overflow: hidden; }
        .table-panel-header { padding: 24px 28px; display: flex; align-items: center; justify-content: space-between; gap: 16px; border-bottom: 1px solid #eceaf3; text-align: left; }
        .table-panel-header h4 { margin: 0 0 6px; color: #232323; font-size: 24px; font-weight: 700; }
        .table-panel-header p { margin: 0; color: #6b6f7a; font-size: 14px; }
        .panel-actions { display: flex; flex-wrap: wrap; gap: 12px; }
        .panel-actions a { display: inline-block; min-width: 136px; padding: 12px 18px; border-radius: 999px; text-align: center; text-decoration: none; font-size: 13px; font-weight: 700; transition: all 0.3s ease; }
        .panel-actions .primary-action { background: #45489a; color: #fff; }
        .panel-actions .primary-action:hover { background: #2f326f; color: #fff; }
        .panel-actions .secondary-action { background: #f0eef9; color: #45489a; }
        .panel-actions .secondary-action:hover { background: #e1ddf6; color: #2f326f; }
        .table-responsive-wrap { overflow-x: auto; padding: 0 28px 10px; }
        .transaksi-table { width: 100%; min-width: 1240px; border-collapse: collapse; }
        .transaksi-table thead th { padding: 16px 14px; background: #f6f5fb; color: #232323; font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; border-bottom: 1px solid #eceaf3; text-align: left; }
        .transaksi-table tbody td { padding: 16px 14px; border-bottom: 1px solid #eceaf3; color: #49515d; font-size: 14px; text-align: left; vertical-align: middle; }
        .transaksi-table tbody tr:hover { background: #faf9fd; }
        .trx-id { display: inline-flex; align-items: center; padding: 7px 12px; border-radius: 999px; background: #f0eef9; color: #45489a; font-weight: 700; font-size: 12px; }
        .trx-price { font-weight: 700; color: #232323; }
        .trx-meta { color: #707883; font-size: 12px; margin-top: 4px; line-height: 1.5; }
        .trx-action-cell { min-width: 320px; white-space: nowrap; }
        .action-group { display: flex; flex-wrap: nowrap; gap: 8px; min-width: max-content; }
        .action-group a { display: inline-block; min-width: 64px; padding: 8px 14px; border-radius: 999px; text-decoration: none; text-align: center; font-size: 12px; font-weight: 700; transition: all 0.25s ease; white-space: nowrap; }
        .action-group .detail-link { background: #eff8ff; color: #175cd3; }
        .action-group .detail-link:hover { background: #dbeafe; color: #1d4ed8; }
        .action-group .edit-link { background: #45489a; color: #fff; }
        .action-group .edit-link:hover { background: #31347a; color: #fff; }
        .action-group .delete-link { background: #fff0f2; color: #b42338; }
        .action-group .delete-link:hover { background: #ffd9df; color: #8d1630; }
        .action-group .print-link { background: #eefbf3; color: #027a48; }
        .action-group .print-link:hover { background: #d7f5e3; color: #05603a; }
        .empty-state { padding: 60px 28px 70px; text-align: center; }
        .empty-state h4 { margin: 0 0 10px; color: #232323; font-size: 24px; font-weight: 700; }
        .empty-state p { max-width: 520px; margin: 0 auto 24px; color: #6b6f7a; font-size: 15px; line-height: 1.8; }
        .pagination-wrap { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 22px 28px 28px; flex-wrap: wrap; }
        .pagination-caption { color: #6b6f7a; font-size: 14px; text-align: left; }
        .pagination-list { display: flex; flex-wrap: wrap; gap: 10px; }
        .pagination-list a, .pagination-list span { display: inline-block; min-width: 42px; height: 42px; line-height: 42px; border-radius: 999px; text-align: center; text-decoration: none; font-size: 13px; font-weight: 700; }
        .pagination-list a { background: #f0eef9; color: #45489a; }
        .pagination-list a:hover { background: #45489a; color: #fff; }
        .pagination-list .current { background: #232323; color: #fff; }
        @media (max-width: 1399px) { :root { --trx-sidebar: 296px; } }
        @media (max-width: 991px) { .stats-grid { grid-template-columns: 1fr; } }
        @media (max-width: 767px) {
            .transaksi-slider, .page-content { width: 100%; margin-left: 0; }
            .transaksi-hero .info { left: 0; width: 100%; max-width: none; min-height: 100vh; justify-content: center; padding: 0 30px; text-align: center; }
            .transaksi-hero .info > div { max-width: 620px; margin: 0 auto; }
            .transaksi-hero .info h1 { font-size: 38px; }
            #ringkasan, #daftar-transaksi { padding-left: 20px; padding-right: 20px; }
            .table-panel-header, .pagination-wrap { padding-left: 20px; padding-right: 20px; }
            .table-responsive-wrap { padding-left: 20px; padding-right: 20px; }
            .panel-actions a { width: 100%; }
            .hero-actions .button { display: block; margin-right: 0; }
            .hero-actions .button a { width: 100%; text-align: center; }
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
            <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
        </button>
        <div id="main-nav" class="collapse navbar-collapse">
            <nav><ul class="nav navbar-nav">
                <li><a href="#top">Hero</a></li>
                <li><a href="#ringkasan">Ringkasan</a></li>
                <li><a href="#daftar-transaksi">Tabel Transaksi</a></li>
                <li><a href="transaksitambah.php">Tambah Transaksi</a></li>
                <li><a href="../../home.php">Kembali Ke Home</a></li>
            </ul></nav>
        </div>
    </header>
    <div class="sidebar-navigation hidde-sm hidden-xs">
        <div class="logo"><a href="../../home.php">SPBU<em>Transaksi</em></a></div>
        <nav><ul>
            <li><a href="#top"><span class="rect"></span><span class="circle"></span>Hero Transaksi</a></li>
            <li><a href="#ringkasan"><span class="rect"></span><span class="circle"></span>Ringkasan</a></li>
            <li><a href="#daftar-transaksi"><span class="rect"></span><span class="circle"></span>Tabel Transaksi</a></li>
            <li><a href="transaksitambah.php"><span class="rect"></span><span class="circle"></span>Tambah Transaksi</a></li>
            <li><a href="../../home.php"><span class="rect"></span><span class="circle"></span>Kembali Ke Home</a></li>
        </ul></nav>
        <ul class="social-icons">
            <li><a href="#ringkasan"><i class="fa fa-bar-chart"></i></a></li>
            <li><a href="#daftar-transaksi"><i class="fa fa-table"></i></a></li>
            <li><a href="transaksitambah.php"><i class="fa fa-plus"></i></a></li>
        </ul>
    </div>
    <div class="slider transaksi-slider">
        <div class="Modern-Slider content-section" id="top">
            <div class="item item-1">
                <div class="img-fill transaksi-hero">
                    <div class="info"><div>
                        <span class="hero-kicker">Tabel Transaksi db_spbu</span>
                        <h1>Kelola Data Transaksi<br><em>SPBU Hasanuddin</em></h1>
                        <p>Halaman ini menampilkan seluruh data transaksi dari database <strong>db_spbu</strong> lengkap dengan relasi ke tabel <strong>bbm</strong>, <strong>selang</strong>, <strong>pompa</strong>, dan <strong>operator</strong>.</p>
                        <div class="hero-actions">
                            <div class="white-button button"><a href="#daftar-transaksi">Lihat Tabel</a></div>
                            <div class="accent-button button"><a href="transaksitambah.php">Tambah Transaksi</a></div>
                        </div>
                    </div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-content">
        <section id="ringkasan" class="content-section">
            <div class="section-heading">
                <h1>Ringkasan<br><em>Transaksi</em></h1>
                <p>Kolom utama yang dipakai: <strong>id_transaksi</strong>, <strong>tanggal_jam</strong>, <strong>liter</strong>, <strong>total</strong>, <strong>id_bbm</strong>, <strong>id_selang</strong>, dan <strong>id_operator</strong>.</p>
            </div>
            <div class="section-content">
                <div class="stats-grid">
                    <div class="stats-card"><span>Total Data</span><strong><?php echo number_format($totalRows, 0, ',', '.'); ?></strong><p>Jumlah seluruh data transaksi yang tersimpan pada tabel <strong>transaksi</strong>.</p></div>
                    <div class="stats-card"><span>Halaman Aktif</span><strong><?php echo $page; ?>/<?php echo $totalPages; ?></strong><p>Menampilkan data <strong><?php echo $displayStart; ?> - <?php echo $displayEnd; ?></strong> dari total data transaksi yang tersedia.</p></div>
                    <div class="stats-card"><span>Relasi Data</span><strong>4 Tabel</strong><p>Data transaksi terhubung ke tabel <strong>bbm</strong>, <strong>selang</strong>, <strong>pompa</strong>, dan <strong>operator</strong>.</p></div>
                </div>
                <div class="info-strip<?php echo $queryError !== null ? ' is-error' : ''; ?>">
                    <?php if ($queryError !== null) : ?>
                        <?php echo htmlspecialchars($queryError, ENT_QUOTES, 'UTF-8'); ?>
                    <?php else : ?>
                        Menampilkan data <strong><?php echo $displayStart; ?> - <?php echo $displayEnd; ?></strong> dari total <strong><?php echo number_format($totalRows, 0, ',', '.'); ?></strong> transaksi yang ada pada database <strong>db_spbu</strong>.
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <section id="daftar-transaksi" class="content-section">
            <div class="section-heading">
                <h1>Tabel<br><em>Transaksi</em></h1>
                <p>Semua unsur tabel transaksi dilengkapi aksi detail, ubah, hapus, dan cetak yang sesuai modul.</p>
            </div>
            <div class="table-panel">
                <div class="table-panel-header">
                    <div><h4>Daftar Data Transaksi</h4><p>Data terbaru ditampilkan paling atas berdasarkan <strong>tanggal_jam</strong>, lalu <strong>id_transaksi</strong> dari tabel <strong>transaksi</strong>.</p></div>
                    <div class="panel-actions">
                        <a class="primary-action" href="transaksitambah.php">Tambah Data</a>
                        <a class="secondary-action" href="../../home.php">Kembali ke Home</a>
                    </div>
                </div>
                <?php if ($viewState === 'data') : ?>
                    <div class="table-responsive-wrap">
                        <table class="transaksi-table">
                            <thead>
                                <tr>
                                    <th>No.</th><th>ID Transaksi</th><th>Tanggal & Jam</th><th>Jenis BBM</th><th>Selang</th><th>Pompa</th><th>Operator</th><th>Liter</th><th>Harga per Liter</th><th>Total</th><th class="trx-action-cell">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaksiRows as $index => $data) : ?>
                                    <tr>
                                        <td><?php echo $start + $index + 1; ?></td>
                                        <td><span class="trx-id"><?php echo htmlspecialchars($data['id_transaksi'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                        <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime((string) $data['tanggal_jam'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars((string) $data['jenis_bbm'], ENT_QUOTES, 'UTF-8'); ?><div class="trx-meta"><?php echo htmlspecialchars((string) $data['id_bbm'], ENT_QUOTES, 'UTF-8'); ?></div></td>
                                        <td><?php echo htmlspecialchars((string) $data['no_selang'], ENT_QUOTES, 'UTF-8'); ?><div class="trx-meta"><?php echo htmlspecialchars((string) $data['id_selang'], ENT_QUOTES, 'UTF-8'); ?></div></td>
                                        <td><?php echo htmlspecialchars((string) $data['no_pompa'], ENT_QUOTES, 'UTF-8'); ?><div class="trx-meta"><?php echo htmlspecialchars((string) $data['id_pompa'], ENT_QUOTES, 'UTF-8'); ?></div></td>
                                        <td><?php echo htmlspecialchars((string) $data['nama_operator'], ENT_QUOTES, 'UTF-8'); ?><div class="trx-meta"><?php echo htmlspecialchars((string) $data['id_operator'], ENT_QUOTES, 'UTF-8'); ?></div></td>
                                        <td><?php echo number_format((float) $data['liter'], 2, ',', '.'); ?><div class="trx-meta">L</div></td>
                                        <td class="trx-price">Rp <?php echo number_format((float) $data['harga_per_liter_per_jenis'], 0, ',', '.'); ?></td>
                                        <td class="trx-price">Rp <?php echo number_format((float) $data['total'], 0, ',', '.'); ?></td>
                                        <td class="trx-action-cell"><div class="action-group">
                                            <a class="detail-link" href="transaksidetail.php?id_transaksi=<?php echo urlencode($data['id_transaksi']); ?>">Detail</a>
                                            <a class="edit-link" href="transaksiubah.php?id_transaksi=<?php echo urlencode($data['id_transaksi']); ?>">Ubah</a>
                                            <a class="delete-link" href="transaksihapus.php?id_transaksi=<?php echo urlencode($data['id_transaksi']); ?>" onclick="return confirm('Yakin ingin menghapus data transaksi ini?');">Hapus</a>
                                            <a class="print-link" href="transaksicetak.php?id_transaksi=<?php echo urlencode($data['id_transaksi']); ?>" target="_blank">Cetak</a>
                                        </div></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-wrap">
                        <div class="pagination-caption">Menampilkan data <strong><?php echo $displayStart; ?> - <?php echo $displayEnd; ?></strong> dari total <strong><?php echo number_format($totalRows, 0, ',', '.'); ?></strong> transaksi.</div>
                        <div class="pagination-list">
                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                <?php if ($i === $page) : ?><span class="current"><?php echo $i; ?></span><?php else : ?><a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a><?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="empty-state">
                        <?php if ($viewState === 'error') : ?>
                            <h4>Data transaksi belum bisa ditampilkan</h4>
                            <p>Periksa kembali koneksi atau struktur tabel <strong>transaksi</strong> pada database <strong>db_spbu</strong>.</p>
                        <?php else : ?>
                            <h4>Belum ada data transaksi</h4>
                            <p>Silakan tambahkan data transaksi baru agar tabel ini mulai terisi dan dapat dipantau dari halaman master transaksi.</p>
                            <div class="panel-actions" style="justify-content:center;"><a class="primary-action" href="transaksitambah.php">Tambah Transaksi</a></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
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
