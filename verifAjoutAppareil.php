<?php
    if ($_POST['type'] == "" || $_POST['marque'] == "" ||$_POST['serie'] == "" || $_POST['date_valid'] == "" || $_POST['date_calib'] == "")
        header("Location: ajoutAppareil.php?erreur=1");

    if (!verifDates($_POST['date_valid']) || !verifDates($_POST['date_calib']))
        header("Location: ajoutAppareil.php?erreur=1");

    function verifDates($date) {
        echo '<h1>'.$date.'</h1>';
        $tab = explode("-", $date);
        if (sizeof($tab) == 3)
            return checkdate(explode("-", $date)[1], explode("-", $date)[0], explode("-", $date)[2]);

        return false;
    }

    $bdd = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $bdd->exec('insert into appareils VALUES (null, '.$_POST['type'].', '.$_POST['marque'].', '.$_POST['num_serie'].', '.$_POST['date_valid'].', '.$_POST['date_calib'].')');

    header("Location: ajoutAppareil.php?ajout=1");
?>