<?php
require_once __DIR__ . '/../../koneksi.php';

if (isset($_GET['id_selang']) && $_GET['id_selang'] !== '') {
    $idSelang = mysqli_real_escape_string($conn, $_GET['id_selang']);
    mysqli_query($conn, "DELETE FROM selang WHERE id_selang = '$idSelang'");
}

header('Location: selanglihat.php');
exit;
?>


