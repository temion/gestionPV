<?php
include_once '../bdd/bdd.inc.php';

$bdd = connexion('portail_gestion');

if (isset($_FILES['pv_excel'])) {
    $errors = array();
    $file_name = $_FILES['pv_excel']['name'];
    $file_size = $_FILES['pv_excel']['size'];
    $file_tmp = $_FILES['pv_excel']['tmp_name'];
    $file_type = $_FILES['pv_excel']['type'];
    $tmp = explode(".", $_FILES['pv_excel']['name']);
    $file_ext = strtolower(end($tmp));

    $regEx = "#^SCO[0-9]+-[A-Z0-9]+-[0-9]+.xlsx$#";
    if (!preg_match($regEx, $file_name)) {
        header('Location: /gestionPV/pv/modifPVOP.php?erreurUpload=0&idPV='.$_POST['idPV']);
        exit;
    }

    mkdir("../PV_Excel/" . explode("-", $file_name)[0]);
    $chemin = "../PV_Excel/" . explode("-", $file_name)[0].'/'.$file_name;
    update($bdd, "pv_controle", "chemin_excel", $bdd->quote($chemin), "id_pv_controle", "=", $_POST['idPV']);
    move_uploaded_file($file_tmp, $chemin);

    header('Location: /gestionPV/pv/modifPVOP.php?erreurUpload=1&idPV='.$_POST['idPV']);
    exit;
}

?>