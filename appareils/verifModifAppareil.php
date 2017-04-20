<?php
    include_once "../util.inc.php";

    $bdd = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $modifs = 0;

    $attributs = array('systeme', 'type', 'marque', 'serie');
    for ($i = 0; $i < sizeof($attributs); $i++)
        modifAttribut($bdd, $attributs[$i]);

    modifDates($bdd, 'date_valid');
    modifDates($bdd, 'date_calib');

    header('Location: listeAppareils.php?modifs='.$modifs);
    exit;
?>

<?php

/**
 * Modifie dans la base l'attribut sélectionné avec la valeur entrée.
 *
 * @param PDO $bdd Base de données à mettre à jour.
 * @param string $attr Attribut à modifier.
 */
function modifAttribut($bdd, $attr) {
    global $modifs;
    if (isset($_POST[$attr]) && $_POST[$attr] != "") {
        $bdd->exec('UPDATE appareils SET '.$attr.' = upper(' . $bdd->quote($_POST[$attr]) . ') WHERE id_appareil = ' . $_POST['idAppareil']);
        $modifs++;
    }
}

/**
 * Modifie dans la base l'attribut date sélectionné avec la valeur entrée.
 *
 * @param PDO $bdd Base de données à mettre à jour.
 * @param string $date Attribut date à modifier.
 */
function modifDates($bdd, $date) {
    global $modifs;
    if (isset($_POST[$date]) && verifFormatDates($_POST[$date])) {
        $bdd->exec('update appareils set '.$date.' = '.$bdd->quote(conversionDate($_POST[$date])).' where id_appareil = '.$_POST['idAppareil']);
        $modifs++;
    }
}

?>