<?php
require_once "../util.inc.php";
session_start();

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if ($_POST['societe'] == "" || $_POST['designation'] == "" || $_POST['type'] == "") {
    header("Location: ajoutEquipement.php?erreur=1");
    exit;
}

$societe = $bddPortailGestion->query('SELECT * FROM societe WHERE replace(nom_societe, \' \', \'\') LIKE replace(' . $bddPortailGestion->quote($_POST['societe'] . '%') . ' , \' \', \'\')')->fetch(); // Replace pour éviter les bugs de comparaison de chaînes à espace.

$valeurs = array("null", $bddInspections->quote($_POST['designation']), $bddInspections->quote($_POST['type']), $societe['id_societe'], definirValeur('diametre'), definirValeur('hauteur'), definirValeur('hauteur_produit'), definirValeur('volume'), calculDistance(), definirValeur('nb_generatrices'));

insert($bddInspections, "reservoirs_gestion_pv", $valeurs);
header("Location: ajoutEquipement.php?ajout=1");
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

function calculDistance() {
    if (isset($_POST['diametre']) && $_POST['diametre'] != "" && isset($_POST['nb_generatrices']) && $_POST['nb_generatrices'] != "")
        return (($_POST['diametre'] * pi()) / $_POST['nb_generatrices']) / 1000;

    return 0;
}

/**
 * Crée en fonction de l'entrée utilisateur la valeur de la variable passée en paramètre.
 *
 * @param string $post Nom de la variable.
 * @return string Valeur de la variable.
 */
function definirValeur($post) {
    if ($post == 'volume')
        return (isset($_POST[$post]) && $_POST[$post] != "") ? strval(intval($_POST[$post])*1000) : "0";

    return (isset($_POST[$post]) && $_POST[$post] != "") ? $_POST[$post] : "0";
}

?>