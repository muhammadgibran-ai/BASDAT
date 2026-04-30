<?php
require_once __DIR__ . '/../../koneksi.php';

if (isset($_GET['id_bbm']) && $_GET['id_bbm'] !== '') {
    $idBbm = mysqli_real_escape_string($conn, $_GET['id_bbm']);
    mysqli_query($conn, "DELETE FROM bbm WHERE id_bbm = '$idBbm'");
}

header('Location: bbmlihat.php');
exit;
?>
