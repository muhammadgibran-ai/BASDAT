<?php
require_once __DIR__ . '/../../koneksi.php';

if (isset($_GET['id_pompa']) && $_GET['id_pompa'] !== '') {
    $idPompa = mysqli_real_escape_string($conn, $_GET['id_pompa']);
    mysqli_query($conn, "DELETE FROM pompa WHERE id_pompa = '$idPompa'");
}

header('Location: pompalihat.php');
exit;
?>

