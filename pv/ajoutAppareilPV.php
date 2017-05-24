<?php
require_once '../util.inc.php';
session_start();

if (!verifSessionOP()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if (!isset($_GET['appareil']) || $_GET['appareil'] == "") {
    header('Location: /gestionPV/pv/modifPVOP.php?idPV=' . $_GET['idPV'] . '&ajout=0');
    exit;
}

insert($bddPortailGestion, "appareils_utilises", array("null", $_GET['appareil'], $_GET['idPV']));
header('Location: /gestionPV/pv/modifPVOP.php?idPV=' . $_GET['idPV'] . '&ajout=1&modif=1');
exit;