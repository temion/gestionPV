<?php
    include_once '../util.inc.php';

    $bdd = connexion('portail_gestion');

    if (!isset($_GET['appareil']) || $_GET['appareil'] == "") {
        header('Location: /gestionPV/pv/modifPVOP.php?idPV=' . $_GET['idPV'].'&ajout=0');
        exit;
    }

    insert($bdd, "appareils_utilises", array("null", $_GET['appareil'], $_GET['idPV']));
    header('Location: /gestionPV/pv/modifPVOP.php?idPV=' . $_GET['idPV'].'&ajout=1');
    exit;