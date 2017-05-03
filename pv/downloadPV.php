<?php
include_once '../bdd/bdd.inc.php';

$bdd = connexion('portail_gestion');
$pv = selectAllFromWhere($bdd, "pv_controle", "id_pv_controle", "=", $_POST['idPV'])->fetch();
$file = str_replace("'", "", $pv['chemin_excel']); // Supprime les guillemets rajoutés lors du stockage dans la base

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}
exit;
?>
