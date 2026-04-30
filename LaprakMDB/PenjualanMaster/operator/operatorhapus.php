<?php
require_once __DIR__ . '/../../koneksi.php';

if (isset($_GET['id_operator']) && $_GET['id_operator'] !== '') {
    $idOperator = mysqli_real_escape_string($conn, $_GET['id_operator']);
    mysqli_query($conn, "DELETE FROM operator WHERE id_operator = '$idOperator'");
}

header('Location: operatorlihat.php');
exit;
?>
