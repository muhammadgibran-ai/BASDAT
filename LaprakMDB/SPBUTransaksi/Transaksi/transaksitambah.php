<?php
require_once __DIR__ . '/../../koneksi.php';

$idTransaksi = '';
$tanggalJam = date('Y-m-d\TH:i');
$liter = '';
$total = '';
$idBbm = '';
$idSelang = '';
$idOperator = '';
$formError = null;

$bbmOptions = mysqli_query($conn, "SELECT id_bbm, jenis_bbm, harga_per_liter_per_jenis FROM bbm ORDER BY id_bbm ASC");
$selangOptions = mysqli_query($conn, "SELECT s.id_selang, s.no_selang, s.id_pompa, p.no_pompa FROM selang s LEFT JOIN pompa p ON s.id_pompa = p.id_pompa ORDER BY s.id_selang ASC");
$operatorOptions = mysqli_query($conn, "SELECT id_operator, nama_operator FROM operator ORDER BY id_operator ASC");

if (isset($_POST['proses'])) {
    $idTransaksi = trim($_POST['id_transaksi'] ?? '');
    $tanggalJam = trim($_POST['tanggal_jam'] ?? '');
    $liter = trim($_POST['liter'] ?? '');
    $idBbm = trim($_POST['id_bbm'] ?? '');
    $idSelang = trim($_POST['id_selang'] ?? '');
    $idOperator = trim($_POST['id_operator'] ?? '');

    if ($idTransaksi === '' || $tanggalJam === '' || $liter === '' || $idBbm === '' || $idSelang === '' || $idOperator === '') {
        $formError = 'Semua field transaksi wajib diisi.';
    } else {
        $idTransaksiEscaped = mysqli_real_escape_string($conn, $idTransaksi);
        $tanggalJamEscaped = mysqli_real_escape_string($conn, str_replace('T', ' ', $tanggalJam) . ':00');
        $literEscaped = mysqli_real_escape_string($conn, $liter);
        $idBbmEscaped = mysqli_real_escape_string($conn, $idBbm);
        $idSelangEscaped = mysqli_real_escape_string($conn, $idSelang);
        $idOperatorEscaped = mysqli_real_escape_string($conn, $idOperator);

        $hargaResult = mysqli_query($conn, "SELECT harga_per_liter_per_jenis FROM bbm WHERE id_bbm = '$idBbmEscaped' LIMIT 1");
        $hargaData = $hargaResult ? mysqli_fetch_assoc($hargaResult) : null;

        if (!$hargaData) {
            $formError = 'Harga BBM tidak ditemukan.';
        } else {
            $totalValue = (float) $liter * (float) $hargaData['harga_per_liter_per_jenis'];
            $totalEscaped = mysqli_real_escape_string($conn, number_format($totalValue, 2, '.', ''));
            $total = number_format($totalValue, 2, '.', '');

            $insertQuery = "INSERT INTO transaksi (id_transaksi, tanggal_jam, liter, total, id_bbm, id_selang, id_operator)
                VALUES ('$idTransaksiEscaped', '$tanggalJamEscaped', '$literEscaped', '$totalEscaped', '$idBbmEscaped', '$idSelangEscaped', '$idOperatorEscaped')";

            if (mysqli_query($conn, $insertQuery)) {
                header('Location: transaksilihat.php');
                exit;
            }

            $formError = 'Gagal menambahkan transaksi: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Tambah Transaksi - SPBU Hasanuddin</title>
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
            --trx-sidebar: 320px;
            --trx-gap: clamp(24px, 4vw, 56px);
        }

        body {
            background: #f4f1ea;
            overflow-x: hidden;
        }

        .sidebar-navigation {
            width: var(--trx-sidebar);
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

        .transaksi-slider,
        .page-content {
            width: calc(100% - var(--trx-sidebar));
            margin-left: var(--trx-sidebar);
            float: none;
        }

        .transaksi-slider .content-section,
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
            background: linear-gradient(120deg, rgba(15, 20, 44, 0.82), rgba(69, 72, 154, 0.58)), url('../../img/SPBUMaster.jpg') center center / cover no-repeat;
        }

        .form-hero .info {
            position: static;
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 72px var(--trx-gap);
            text-align: left;
            transform: none;
        }

        .form-hero .info>div {
            max-width: 700px;
        }

        .hero-kicker {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 18px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
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

        #form-transaksi {
            padding: 80px var(--trx-gap);
        }

        .form-shell {
            max-width: 980px;
            margin: 0 auto;
            padding: 34px;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 20px 45px rgba(24, 30, 48, 0.08);
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

        .form-shell input:focus,
        .form-shell select:focus {
            border-color: #45489a;
            box-shadow: 0 0 0 3px rgba(69, 72, 154, 0.12);
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

        .preview-box {
            padding: 18px 20px;
            border-radius: 16px;
            background: #f6f5fb;
            border: 1px solid #eceaf3;
        }

        .preview-label {
            display: block;
            margin-bottom: 8px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.1px;
            text-transform: uppercase;
        }

        .preview-value {
            color: #232323;
            font-size: 28px;
            font-weight: 700;
        }

        .preview-help {
            margin-top: 8px;
            color: #69707c;
            font-size: 14px;
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
            color: #fff;
        }

        .form-actions a {
            background: #f0eef9;
            color: #45489a;
        }

        .form-actions a:hover {
            background: #e1ddf6;
            color: #2f326f;
        }

        @media (max-width: 1399px) {
            :root {
                --trx-sidebar: 296px;
            }
        }

        @media (max-width: 991px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .transaksi-slider,
            .page-content {
                width: 100%;
                margin-left: 0;
            }

            .form-hero .info {
                left: 0;
                width: 100%;
                max-width: none;
                min-height: 100vh;
                justify-content: center;
                padding: 0 30px;
                text-align: center;
            }

            .form-hero .info>div {
                max-width: 620px;
                margin: 0 auto;
            }

            .form-hero .info h1 {
                font-size: 38px;
            }

            #form-transaksi {
                padding-left: 20px;
                padding-right: 20px;
            }

            .form-shell {
                padding: 26px 22px;
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
                    <li><a href="#top">Hero</a></li>
                    <li><a href="#form-transaksi">Form</a></li>
                    <li><a href="transaksilihat.php">Tabel Transaksi</a></li>
                    <li><a href="../../home.php">Kembali Ke Home</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="sidebar-navigation hidde-sm hidden-xs">
        <div class="logo">
            <a href="../../home.php">SPBU<em>Tambah</em></a>
        </div>
        <nav>
            <ul>
                <li>
                    <a href="#top">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Hero Form
                    </a>
                </li>
                <li>
                    <a href="#form-transaksi">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Form Transaksi
                    </a>
                </li>
                <li>
                    <a href="transaksilihat.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Tabel Transaksi
                    </a>
                </li>
                <li>
                    <a href="../../home.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Kembali Ke Home
                    </a>
                </li>
            </ul>
        </nav>
        <ul class="social-icons">
            <li><a href="#form-transaksi"><i class="fa fa-edit"></i></a></li>
            <li><a href="transaksilihat.php"><i class="fa fa-table"></i></a></li>
            <li><a href="../../home.php"><i class="fa fa-home"></i></a></li>
        </ul>
    </div>

    <div class="slider transaksi-slider">
        <div class="Modern-Slider content-section" id="top">
            <div class="item item-1">
                <div class="img-fill form-hero">
                    <div class="info">
                        <div>
                            <span class="hero-kicker">Input Data Transaksi</span>
                            <h1>Tambah Data Transaksi<br>Ke db_spbu</h1>
                            <p>Form ini sekarang langsung menyimpan data ke tabel <strong>transaksi</strong> dengan kolom <strong>id_transaksi</strong>, <strong>tanggal_jam</strong>, <strong>liter</strong>, <strong>total</strong>, <strong>id_bbm</strong>, <strong>id_selang</strong>, dan <strong>id_operator</strong>.</p>
                            <div class="hero-actions">
                                <div class="white-button button">
                                    <a href="#form-transaksi">Isi Form</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section id="form-transaksi" class="content-section">
            <div class="section-heading">
                <h1>Form<br><em>Tambah</em></h1>
                <p>Isi seluruh field transaksi untuk menambahkan data baru ke tabel <strong>transaksi</strong> pada database <strong>db_spbu</strong>.</p>
            </div>
            <div class="form-shell">
                <h4>Form Tambah Transaksi</h4>
                <p>Lengkapi seluruh field transaksi. Nilai total akan dihitung otomatis dari harga BBM per liter dikalikan jumlah liter.</p>

                <?php if ($formError !== null) : ?>
                    <div class="form-status"><?php echo htmlspecialchars($formError, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="form-grid">
                        <div>
                            <label for="id_transaksi">ID Transaksi</label>
                            <input id="id_transaksi" name="id_transaksi" type="text" value="<?php echo htmlspecialchars($idTransaksi, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Contoh: TRX0006" maxlength="7" required>
                        </div>
                        <div>
                            <label for="tanggal_jam">Tanggal dan Jam</label>
                            <input id="tanggal_jam" name="tanggal_jam" type="datetime-local" value="<?php echo htmlspecialchars($tanggalJam, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div>
                            <label for="liter">Liter</label>
                            <input id="liter" name="liter" type="number" step="0.01" min="0" value="<?php echo htmlspecialchars($liter, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Contoh: 10.50" required>
                        </div>
                        <div>
                            <label for="id_bbm">BBM</label>
                            <select id="id_bbm" name="id_bbm" required>
                                <option value="">Pilih BBM</option>
                                <?php if ($bbmOptions) : ?>
                                    <?php while ($bbm = mysqli_fetch_assoc($bbmOptions)) : ?>
                                        <option value="<?php echo htmlspecialchars($bbm['id_bbm'], ENT_QUOTES, 'UTF-8'); ?>" data-harga="<?php echo htmlspecialchars((string) $bbm['harga_per_liter_per_jenis'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $idBbm === $bbm['id_bbm'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($bbm['id_bbm'] . ' - ' . $bbm['jenis_bbm'] . ' (Rp ' . number_format((float) $bbm['harga_per_liter_per_jenis'], 0, ',', '.') . '/L)', ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label for="id_selang">Selang</label>
                            <select id="id_selang" name="id_selang" required>
                                <option value="">Pilih Selang</option>
                                <?php if ($selangOptions) : ?>
                                    <?php while ($selang = mysqli_fetch_assoc($selangOptions)) : ?>
                                        <option value="<?php echo htmlspecialchars($selang['id_selang'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $idSelang === $selang['id_selang'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($selang['id_selang'] . ' - ' . $selang['no_selang'] . ' / ' . $selang['no_pompa'], ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label for="id_operator">Operator</label>
                            <select id="id_operator" name="id_operator" required>
                                <option value="">Pilih Operator</option>
                                <?php if ($operatorOptions) : ?>
                                    <?php while ($operator = mysqli_fetch_assoc($operatorOptions)) : ?>
                                        <option value="<?php echo htmlspecialchars($operator['id_operator'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $idOperator === $operator['id_operator'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($operator['id_operator'] . ' - ' . $operator['nama_operator'], ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group-full">
                            <div class="preview-box">
                                <span class="preview-label">Estimasi Total</span>
                                <div id="total_preview" class="preview-value"><?php echo htmlspecialchars($total !== '' ? 'Rp ' . number_format((float) $total, 0, ',', '.') : 'Rp 0', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="preview-help">Total akan mengikuti harga BBM per liter yang dipilih dan jumlah liter yang Anda masukkan.</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="proses">Simpan</button>
                        <a href="transaksilihat.php">Kembali</a>
                    </div>
                </form>
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
    <script>
        (function() {
            var literInput = document.getElementById('liter');
            var bbmSelect = document.getElementById('id_bbm');
            var totalPreview = document.getElementById('total_preview');

            function formatRupiah(amount) {
                return 'Rp ' + amount.toLocaleString('id-ID', {
                    maximumFractionDigits: 0
                });
            }

            function updateTotal() {
                var selectedOption = bbmSelect.options[bbmSelect.selectedIndex];
                var harga = selectedOption ? parseFloat(selectedOption.getAttribute('data-harga') || '0') : 0;
                var liter = parseFloat(literInput.value || '0');
                var total = harga * liter;

                if (!isFinite(total)) {
                    total = 0;
                }

                totalPreview.textContent = formatRupiah(total);
            }

            literInput.addEventListener('input', updateTotal);
            bbmSelect.addEventListener('change', updateTotal);
            updateTotal();
        })();
    </script>
</body>

</html>
