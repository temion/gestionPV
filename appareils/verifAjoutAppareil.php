<?php
require_once "../util.inc.php";

$bdd = connexion('portail_gestion');

if ($_POST['systeme'] == "" || $_POST['type'] == "" || $_POST['marque'] == "" || $_POST['serie'] == "") {
    header("Location: ajoutAppareil.php?erreur=1");
    exit;
}

$date_valid_correcte = verifEntreeDates('date_valid');
$date_calib_correcte = verifEntreeDates('date_calib');

if ($date_valid_correcte && $date_calib_correcte) {
    $date_valid = conversionDate($_POST['date_valid']);
    $date_calib = conversionDate($_POST['date_calib']);
    $bdd->exec('INSERT INTO appareils VALUES (NULL, upper(' . $bdd->quote($_POST['systeme']) . '), upper(' . $bdd->quote($_POST['type']) . '), upper(' . $bdd->quote($_POST['marque']) . '), upper(' . $bdd->quote($_POST['serie']) . '), ' . $bdd->quote($date_valid) . ', ' . $bdd->quote($date_calib) . ')');
} else if ($date_valid_correcte) {
    $date_valid = conversionDate($_POST['date_valid']);
    $bdd->exec('INSERT INTO appareils VALUES (NULL, upper(' . $bdd->quote($_POST['systeme']) . '), upper(' . $bdd->quote($_POST['type']) . '), upper(' . $bdd->quote($_POST['marque']) . '), upper(' . $bdd->quote($_POST['serie']) . '), ' . $bdd->quote($date_valid) . ', NULL)');
} else if ($date_calib_correcte) {
    $date_calib = conversionDate($_POST['date_calib']);
    $bdd->exec('INSERT INTO appareils VALUES (NULL, upper(' . $bdd->quote($_POST['systeme']) . '), upper(' . $bdd->quote($_POST['type']) . '), upper(' . $bdd->quote($_POST['marque']) . '), upper(' . $bdd->quote($_POST['serie']) . '), NULL, ' . $bdd->quote($date_calib) . ')');
} else
    $bdd->exec('INSERT INTO appareils VALUES (NULL, upper(' . $bdd->quote($_POST['systeme']) . '), upper(' . $bdd->quote($_POST['type']) . '), upper(' . $bdd->quote($_POST['marque']) . '), upper(' . $bdd->quote($_POST['serie']) . '), NULL, NULL)') or die(print_r($bdd->errorInfo(), true));;

header("Location: ajoutAppareil.php?ajout=1");
?>

<?php

/**
 * Effectue les vérifications de date nécessaires, et indique via un booléen si la date est correcte.
 *
 * @param string $date Date à vérifier.
 * @return bool Vrai si la date est valide.
 */
function verifEntreeDates($date) {
    $date_correcte = false;
    if ($_POST[$date] != "") {
        if (!verifFormatDates(strval($_POST[$date]))) {
            header("Location: ajoutAppareil.php?erreur=1");
            exit;
        } else {
            $date_correcte = true;
        }
    }

    return $date_correcte;
}

?>