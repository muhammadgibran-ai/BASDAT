<?php
require_once __DIR__ . '/../../koneksi.php';

if (isset($_GET['id_transaksi']) && $_GET['id_transaksi'] !== '') {
    $idTransaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi = '$idTransaksi'");
}

header('Location: transaksilihat.php');
exit;
?>
