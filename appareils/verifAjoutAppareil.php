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
    $valeurs = array("null", strtoupper($bddPortailGestion->quote($_POST['systeme'])), strtoupper($bddPortailGestion->quote($_POST['type'])), strtoupper($bddPortailGestion->quote($_POST['marque'])), strtoupper($bddPortailGestion->quote($_POST['serie'])), $bddPortailGestion->quote($date_valid), $bddPortailGestion->quote($date_calib));
} else if ($date_valid_correcte) {
    $date_valid = conversionDate($_POST['date_valid']);
    $valeurs = array("null", strtoupper($bddPortailGestion->quote($_POST['systeme'])), strtoupper($bddPortailGestion->quote($_POST['type'])), strtoupper($bddPortailGestion->quote($_POST['marque'])), strtoupper($bddPortailGestion->quote($_POST['serie'])), $bddPortailGestion->quote($date_valid), "null");
} else if ($date_calib_correcte) {
    $date_calib = conversionDate($_POST['date_calib']);
    $valeurs = array("null", strtoupper($bddPortailGestion->quote($_POST['systeme'])), strtoupper($bddPortailGestion->quote($_POST['type'])), strtoupper($bddPortailGestion->quote($_POST['marque'])), strtoupper($bddPortailGestion->quote($_POST['serie'])), "null", $bddPortailGestion->quote($date_calib));
} else
    $valeurs = array("null", strtoupper($bddPortailGestion->quote($_POST['systeme'])), strtoupper($bddPortailGestion->quote($_POST['type'])), strtoupper($bddPortailGestion->quote($_POST['marque'])), strtoupper($bddPortailGestion->quote($_POST['serie'])), "null", "null");

insert($bddPortailGestion, "appareils", $valeurs);

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