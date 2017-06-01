<?php
require_once "../util.inc.php";
require_once "../excel/ConvertisseurHistorique.php";

/**
 * Ajoute dans la base l'action passée en paramètre.
 *
 * @param PDO $bdd Base de données.
 * @param string $libelle Libellé de l'action.
 */
function ajouterHistorique($bdd, $libelle, $pageAction, $param) {
    $dernierAjout = $bdd->query('SELECT * FROM historique_activite WHERE date_activite IN (SELECT max(date_activite) FROM historique_activite)')->fetch();

    // Si la dernière modification date de l'année précédente
    if (intval(explode("-", $dernierAjout['date_activite'])[0]) == intval(date("Y")) - 1) {
        archiverHistorique($bdd, explode("-", $dernierAjout['date_activite'])[0]);
    }

    // Vérification du dernier élément de l'historique, pour éviter les redondances dans le tableau
    if ($bdd->quote($libelle) != $bdd->quote($dernierAjout['libelle']))
        insert($bdd, "historique_activite", array("null", $_SESSION['id_connecte'], $bdd->quote($libelle), $bdd->quote($pageAction), $bdd->quote($param), "now()"));
}

function archiverHistorique($bdd, $annee) {
    $convertisseur = new ConvertisseurHistorique($annee);

    $chemin = $convertisseur->sauvegarde();

    $historique = $convertisseur->getHistorique();
    for ($i = 0; $i < sizeof($historique); $i++) {
        $bdd->query('delete from historique_activite where id_activite = '.$historique[$i]['id_activite']);
    }

    $bdd->query('alter table historique_activite auto_increment = 1');
    insert($bdd, "archives_activites", array("null", intval($annee), sizeof($historique), $bdd->quote($chemin)));
}
