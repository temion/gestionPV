<?php
require_once "../util.inc.php";
require_once "excelUtil.inc.php";
require_once "ConvertisseurRapport.php";
session_start();

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if (!isset($_POST['idRapport']) || $_POST['idRapport'] == "") {
    header('Location: /gestionPV/index.php');
    exit;
}

$rapport = selectRapportParId($bddPortailGestion, $_POST['idRapport'])->fetch();
$convertisseur = new ConvertisseurRapport($rapport);

$convertisseur->telecharger($convertisseur->sauvegarde());