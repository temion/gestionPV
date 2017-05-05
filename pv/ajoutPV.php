<?php
    include_once '../util.inc.php';

    $bdd = connexion('portail_gestion');

    verifEntrees();

    $controle = selectAllFromWhere($bdd, "type_controle", "id_type", "=", $_GET['controle'])->fetch();
    $nouvelleVal = $controle['num_controle'] + 1;
    update($bdd, "type_controle", "num_controle", $nouvelleVal, "id_type", "=", $_GET['controle']);

    $valeurs = array("null", $_GET['idRapport'], $_GET['equipement'], $_GET['discipline'], $_GET['controle'], $nouvelleVal, $_GET['controleur'], "false", "false", 0, "false", "false", "false", $bdd->quote(conversionDate($_GET['date_debut'])), $bdd->quote(conversionDate($_GET['date_fin'])), "null", "null");
    insert($bdd, "pv_controle", $valeurs);

    header('Location: /gestionPV/pv/modifRapportCA.php?idRapport=' . $_GET['idRapport'].'&ajout=1');
    exit;

    function verifEntrees() {
        if (condition()) {
            header('Location: /gestionPV/pv/modifRapportCA.php?idRapport=' . $_GET['idRapport'] . '&ajout=0');
            exit;
        }

        if (!isset($_GET['controleur']) || $_GET['controleur'] == "")
            $_GET['controleur'] = "null";
    }

    function condition() {
        return (!isset($_GET['equipement']) || $_GET['equipement'] == "" || !isset($_GET['controle']) ||
                $_GET['controle'] == "" || !isset($_GET['discipline']) || $_GET['discipline'] == "" ||
                !isset($_GET['date_debut']) || !verifFormatDates($_GET['date_debut']) ||
                !isset($_GET['date_fin']) || !verifFormatDates($_GET['date_fin']));
    }