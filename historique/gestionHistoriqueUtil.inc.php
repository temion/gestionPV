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

/**
 * Supprime les tuples de la table "historique_activite", et ajoute un tuple dans "archives_activite" indiquant
 * le chemin du fichier Excel d'archive sur le serveur.
 *
 * @param PDO $bdd Base de données.
 * @param string $annee Année des activités à archiver.
 */
function archiverHistorique($bdd, $annee) {
    $convertisseur = new ConvertisseurHistorique($annee);

    $chemin = $convertisseur->sauvegarde();

    $historique = $convertisseur->getHistorique();

    $bdd->query('truncate table historique_activite');
    $bdd->query('alter table historique_activite auto_increment = 1');
    insert($bdd, "archives_activites", array("null", intval($annee), sizeof($historique), $bdd->quote($chemin)));
}

/**
 * Vérifie que l'élément actuel de l'historique existe encore dans la base, afin de rendre actif ou non son lien.
 *
 * @param $bdd Base de données dans lequel se trouve l'historique.
 * @param $activite Activité à vérifier.
 *
 * @return bool Vrai si l'élément est toujours présent dans la base.
 */
function verifPage($bdd, $activite) {
    if (strchr($activite['page_action'], "Rapport"))
        return sizeof(selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $activite['param'])->fetchAll()) > 0;
    else if (strchr($activite['page_action'], "PV"))
        return sizeof(selectAllFromWhere($bdd, "pv_controle", "id_pv", "=", $activite['param'])->fetchAll()) > 0;
    else
        return false;
}