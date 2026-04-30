<?php
require_once __DIR__ . '/../../koneksi.php';

$idTransaksi = isset($_GET['id_transaksi']) ? trim($_GET['id_transaksi']) : '';

if ($idTransaksi === '') {
    exit('ID transaksi tidak ditemukan.');
}

$idTransaksiEscaped = mysqli_real_escape_string($conn, $idTransaksi);
$query = mysqli_query(
    $conn,
    "SELECT
        t.id_transaksi,
        t.tanggal_jam,
        t.liter,
        t.total,
        b.id_bbm,
        b.jenis_bbm,
        b.harga_per_liter_per_jenis,
        s.id_selang,
        s.no_selang,
        s.id_pompa,
        p.no_pompa,
        o.id_operator,
        o.nama_operator
    FROM transaksi t
    LEFT JOIN bbm b ON t.id_bbm = b.id_bbm
    LEFT JOIN selang s ON t.id_selang = s.id_selang
    LEFT JOIN pompa p ON s.id_pompa = p.id_pompa
    LEFT JOIN operator o ON t.id_operator = o.id_operator
    WHERE t.id_transaksi = '$idTransaksiEscaped'
    LIMIT 1"
);

$data = $query ? mysqli_fetch_assoc($query) : null;

if (!$data) {
    exit('Data transaksi tidak ditemukan.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Kontan <?php echo htmlspecialchars($data['id_transaksi'], ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        body {
            margin: 0;
            padding: 18px;
            background: #f1f1f1;
            font-family: "Courier New", monospace;
            color: #111;
        }

        .receipt {
            width: 380px;
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            border: 2px solid #222;
            padding: 20px 18px 24px;
            box-sizing: border-box;
        }

        .topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .brand-logo-left img {
            display: block;
            width: 78px;
            height: auto;
        }

        .brand-logo-right img {
            display: block;
            width: 74px;
            height: auto;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .header h1,
        .header h2,
        .header p {
            margin: 0;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .header h2 {
            margin-top: 4px;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .header p {
            margin-top: 5px;
            font-size: 12px;
        }

        .divider {
            border-top: 1px solid #222;
            margin: 12px 0 14px;
        }

        .rows {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .row-item {
            display: grid;
            grid-template-columns: 145px 12px 1fr;
            align-items: start;
            column-gap: 8px;
            font-size: 15px;
            line-height: 1.35;
            text-transform: uppercase;
        }

        .row-label {
            font-weight: 700;
            letter-spacing: 0.6px;
        }

        .row-colon {
            font-weight: 700;
            text-align: center;
        }

        .row-value {
            word-break: break-word;
        }

        .stamp {
            margin: 18px 0 10px;
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 2px;
            color: rgba(40, 110, 190, 0.22);
            line-height: 1.1;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 12px;
            padding-top: 14px;
            border-top: 1px solid #222;
            font-size: 13px;
            line-height: 1.5;
            text-transform: uppercase;
        }

        .footer p {
            margin: 0;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .receipt {
                border: 1px solid #222;
                box-shadow: none;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="topbar">
            <div class="brand-logo-left">
                <img src="../../logo_spbu.png" alt="Logo SPBU">
            </div>
            <div class="brand-logo-right">
                <img src="../../logo_pastipas.png" alt="Logo Pasti Pas">
            </div>
        </div>

        <div class="header">
            <h1>SPBU 7490110</h1>
            <h2>NOTA KONTAN</h2>
            <p>ID: <?php echo htmlspecialchars($data['id_transaksi'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <div class="divider"></div>

        <div class="rows">
            <div class="row-item">
                <div class="row-label">TGL / JAM</div>
                <div class="row-colon">:</div>
                <div class="row-value"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime((string) $data['tanggal_jam'])), ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="row-item">
                <div class="row-label">NO POMPA</div>
                <div class="row-colon">:</div>
                <div class="row-value"><?php echo htmlspecialchars((string) $data['no_pompa'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="row-item">
                <div class="row-label">NO SELANG</div>
                <div class="row-colon">:</div>
                <div class="row-value"><?php echo htmlspecialchars((string) $data['no_selang'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="row-item">
                <div class="row-label">JENIS BBM</div>
                <div class="row-colon">:</div>
                <div class="row-value"><?php echo htmlspecialchars((string) $data['jenis_bbm'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="row-item">
                <div class="row-label">LITER</div>
                <div class="row-colon">:</div>
                <div class="row-value"><?php echo number_format((float) $data['liter'], 2, ',', '.'); ?> L</div>
            </div>
            <div class="row-item">
                <div class="row-label">HARGA</div>
                <div class="row-colon">:</div>
                <div class="row-value">RP <?php echo number_format((float) $data['harga_per_liter_per_jenis'], 0, ',', '.'); ?></div>
            </div>
            <div class="row-item">
                <div class="row-label">TOTAL</div>
                <div class="row-colon">:</div>
                <div class="row-value">RP <?php echo number_format((float) $data['total'], 0, ',', '.'); ?></div>
            </div>
            <div class="row-item">
                <div class="row-label">OPERATOR</div>
                <div class="row-colon">:</div>
                <div class="row-value"><?php echo htmlspecialchars((string) $data['nama_operator'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        </div>

        <div class="stamp">
            HASANUDDIN<br>7490110
        </div>

        <div class="footer">
            <p>PREMIUM U/ GOL. TIDAK MAMPU</p>
            <p>MARI GUNAKAN PERTAMAX</p>
            <p>TERIMAKASIH &amp; SELAMAT JALAN</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
