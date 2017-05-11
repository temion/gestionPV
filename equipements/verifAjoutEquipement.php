<?php
require_once "../util.inc.php";

$bdd = connexion('portail_gestion');

if ($_POST['societe'] == "" || $_POST['nom'] == "") {
    header("Location: ajoutEquipement.php?erreur=1");
    exit;
}

$valeurs = array("diametre"=>definirValeur('diametre'), "hauteur"=>definirValeur('hauteur'), "hauteur_produit"=>definirValeur('hauteur_produit'), "volume"=>definirValeur('volume'), "distance"=>definirValeur('distance'));

$societe = $bdd->query('select * from societe where replace(nom_societe, \' \', \'\') like replace('.$bdd->quote($_POST['societe'].'%').' , \' \', \'\')')->fetch(); // Replace pour éviter les bugs de comparaison de chaînes à espace.
$bdd->exec('insert into equipements values(null, '.$societe['id_societe'].', '.$bdd->quote($_POST['nom']).', '.definirValeur($_POST['diametre']).', '.definirValeur($_POST['hauteur']).', '.definirValeur($_POST['hauteur_produit']).', '.definirValeur($_POST['volume']).', '.definirValeur($_POST['distance']).')') or die (print_r($bdd->errorInfo(), true));

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