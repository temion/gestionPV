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

$valeurs = array("diametre" => definirValeur('diametre'), "hauteur" => definirValeur('hauteur'), "hauteur_produit" => definirValeur('hauteur_produit'), "volume" => definirValeur('volume'), "distance" => definirValeur('distance'));

$societe = $bddPortailGestion->query('SELECT * FROM societe WHERE replace(nom_societe, \' \', \'\') LIKE replace(' . $bddPortailGestion->quote($_POST['societe'] . '%') . ' , \' \', \'\')')->fetch(); // Replace pour éviter les bugs de comparaison de chaînes à espace.

//$bddPortailGestion->exec('INSERT INTO equipements VALUES(NULL, ' . $societe['id_societe'] . ', ' . $bddPortailGestion->quote($_POST['nom']) . ', ' . definirValeur($_POST['diametre']) . ', ' . definirValeur($_POST['hauteur']) . ', ' . definirValeur($_POST['hauteur_produit']) . ', ' . definirValeur($_POST['volume']) . ', ' . definirValeur($_POST['distance']) . ')') or die (print_r($bddPortailGestion->errorInfo(), true));
$bddInspections->exec('insert into reservoirs_tmp values(NULL, '.$bddInspections->quote($_POST['designation']).', '.$bddInspections->quote($_POST['type']).', '.$societe['id_societe'].', '. definirValeur($_POST['diametre']) . ', ' . definirValeur($_POST['hauteur']) . ', ' . definirValeur($_POST['hauteur_produit']) . ', ' . definirValeur($_POST['volume']) . ', ' . definirValeur($_POST['distance']).', '.definirValeur($_POST['nb_generatrices'].')'));
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

/**
 * Crée en fonction de l'entrée utilisateur la valeur de la variable passée en paramètre.
 *
 * @param string $post Nom de la variable.
 * @return string Valeur de la variable.
 */
function definirValeur($post) {
    return (isset($post) && $post != "") ? $post : "null";
}

?>