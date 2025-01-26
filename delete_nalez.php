<?php
$id = $_GET['id'];

if (!isset($id)) {
    header('Location: index.php');
    exit();
}

require_once 'api/db.php';

$nalez = get_nalez_by_id($id);
delete_nalez($id);
unlink($nalez['foto_url']);
header('Location: index.php');
exit();