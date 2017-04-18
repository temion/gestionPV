<?php
    $bdd = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');

    if ($_POST['systeme'] == "" || $_POST['type'] == "" || $_POST['marque'] == "" || $_POST['serie'] == "") {
        header("Location: ajoutAppareil.php?erreur=1");
        exit;
    }

    $date_valid_correcte = $date_calib_correcte = false;

    if ($_POST['date_valid'] != "") {
        if (!verifDates(strval($_POST['date_valid']))) {
            $date_valid_correcte = false;
            header("Location: ajoutAppareil.php?erreur=1");
            exit;
        } else {
            $date_valid_correcte = true;
        }
    }

    if ($_POST['date_calib'] != "") {
        if (!verifDates(strval($_POST['date_calib']))) {
            $date_calib_correcte = false;
            header("Location: ajoutAppareil.php?erreur=1");
            exit;
        } else {
            $date_calib_correcte = true;
        }
    }

    if ($date_valid_correcte && $date_calib_correcte) {
        $date_valid = genererDates($_POST['date_valid']);
        $date_calib = genererDates($_POST['date_calib']);
        $bdd->exec('insert into appareils VALUES (null, upper('.$bdd->quote($_POST['systeme']).'), upper('.$bdd->quote($_POST['type']).'), upper('.$bdd->quote($_POST['marque']).'), upper('.$bdd->quote($_POST['serie']).'), '.$bdd->quote($date_valid).', '.$bdd->quote($date_calib).')');
    } else if ($date_valid_correcte) {
        $date_valid = genererDates($_POST['date_valid']);
        $bdd->exec('insert into appareils VALUES (null, upper('.$bdd->quote($_POST['systeme']).'), upper('.$bdd->quote($_POST['type']).'), upper('.$bdd->quote($_POST['marque']).'), upper('.$bdd->quote($_POST['serie']).'), '.$bdd->quote($date_valid).', null)');
    } else if ($date_calib_correcte) {
        $date_calib = genererDates($_POST['date_calib']);
        $bdd->exec('insert into appareils VALUES (null, upper('.$bdd->quote($_POST['systeme']).'), upper('.$bdd->quote($_POST['type']).'), upper('.$bdd->quote($_POST['marque']).'), upper('.$bdd->quote($_POST['serie']).'), null, '.$bdd->quote($date_calib).')');
    } else
        $bdd->exec('insert into appareils VALUES (null, upper('.$bdd->quote($_POST['systeme']).'), upper('.$bdd->quote($_POST['type']).'), upper('.$bdd->quote($_POST['marque']).'), upper('.$bdd->quote($_POST['serie']).'), null, null)') or die(print_r($bdd->errorInfo(), true));;

    header("Location: ajoutAppareil.php?ajout=1");

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
?>