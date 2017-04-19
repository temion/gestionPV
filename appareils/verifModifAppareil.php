<?php
    $bdd = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $modifs = 0;

    if (isset($_POST['systeme']) && $_POST['systeme'] != "") {
        $bdd->exec('UPDATE appareils SET systeme = upper(' . $bdd->quote($_POST['systeme']) . ') WHERE id_appareil = ' . $_POST['idAppareil']);
        $modifs++;
    }

    if (isset($_POST['type']) && $_POST['type'] != "") {
        $bdd->exec('update appareils set type = upper('.$bdd->quote($_POST['type']).') where id_appareil = '.$_POST['idAppareil']);
        $modifs++;
    }

    if (isset($_POST['marque']) && $_POST['marque'] != "") {
        $bdd->exec('update appareils set marque = upper('.$bdd->quote($_POST['marque']).') where id_appareil = '.$_POST['idAppareil']);
        $modifs++;
    }

    if (isset($_POST['serie']) && $_POST['serie'] != "") {
        $bdd->exec('update appareils set num_serie = upper('.$bdd->quote($_POST['marque']).') where id_appareil = '.$_POST['idAppareil']);
        $modifs++;
    }

    if (isset($_POST['date_valid']) && verifDates($_POST['date_valid'])) {
        $bdd->exec('update appareils set date_valid = '.genererDates($_POST['date_valid']).' where id_appareil = '.$_POST['idAppareil']);
        $modifs++;
    }

    if (isset($_POST['date_calib']) && verifDates($_POST['date_calib'])) {
        $bdd->exec('update appareils set date_calib = '.genererDates($_POST['date_calib']).' where id_appareil = '.$_POST['idAppareil']);
        $modifs++;
    }

    function verifDates($date) {
        $tab = explode("-", $date);
        if (sizeof($tab) == 3)
            return checkdate($tab[1], $tab[0], $tab[2]);

        return false;
    }

    function genererDates($date) {
        $tab = explode("-", $date);
        return $tab[2].'-'.$tab[1].'-'.$tab[0];
    }

    header('Location: listeAppareils.php?modifs='.$modifs);
    exit;