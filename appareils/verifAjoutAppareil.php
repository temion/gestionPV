<?php
require_once "../util.inc.php";

if ($_POST['systeme'] == "" || $_POST['type'] == "" || $_POST['marque'] == "" || $_POST['serie'] == "") {
    header("Location: ajoutAppareil.php?erreur=1");
    exit;
}

$date_valid_correcte = verifEntreeDates('date_valid');
$date_calib_correcte = verifEntreeDates('date_calib');

if ($date_valid_correcte && $date_calib_correcte) {
    $date_valid = conversionDate($_POST['date_valid']);
    $date_calib = conversionDate($_POST['date_calib']);
    $bddPortailGestion->exec('INSERT INTO appareils VALUES (NULL, upper(' . $bddPortailGestion->quote($_POST['systeme']) . '), upper(' . $bddPortailGestion->quote($_POST['type']) . '), upper(' . $bddPortailGestion->quote($_POST['marque']) . '), upper(' . $bddPortailGestion->quote($_POST['serie']) . '), ' . $bddPortailGestion->quote($date_valid) . ', ' . $bddPortailGestion->quote($date_calib) . ')');
} else if ($date_valid_correcte) {
    $date_valid = conversionDate($_POST['date_valid']);
    $bddPortailGestion->exec('INSERT INTO appareils VALUES (NULL, upper(' . $bddPortailGestion->quote($_POST['systeme']) . '), upper(' . $bddPortailGestion->quote($_POST['type']) . '), upper(' . $bddPortailGestion->quote($_POST['marque']) . '), upper(' . $bddPortailGestion->quote($_POST['serie']) . '), ' . $bddPortailGestion->quote($date_valid) . ', NULL)');
} else if ($date_calib_correcte) {
    $date_calib = conversionDate($_POST['date_calib']);
    $bddPortailGestion->exec('INSERT INTO appareils VALUES (NULL, upper(' . $bddPortailGestion->quote($_POST['systeme']) . '), upper(' . $bddPortailGestion->quote($_POST['type']) . '), upper(' . $bddPortailGestion->quote($_POST['marque']) . '), upper(' . $bddPortailGestion->quote($_POST['serie']) . '), NULL, ' . $bddPortailGestion->quote($date_calib) . ')');
} else
    $bddPortailGestion->exec('INSERT INTO appareils VALUES (NULL, upper(' . $bddPortailGestion->quote($_POST['systeme']) . '), upper(' . $bddPortailGestion->quote($_POST['type']) . '), upper(' . $bddPortailGestion->quote($_POST['marque']) . '), upper(' . $bddPortailGestion->quote($_POST['serie']) . '), NULL, NULL)') or die(print_r($bddPortailGestion->errorInfo(), true));;

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