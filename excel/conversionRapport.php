<?php
require_once "../util.inc.php";
require_once "excelUtil.inc.php";
require_once "ConvertisseurRapport.php";

$rapport = selectRapportParId($bddPortailGestion, $_POST['idRapport'])->fetch();
$convertisseur = new ConvertisseurRapport($rapport);

$convertisseur->telecharger($convertisseur->sauvegarde());