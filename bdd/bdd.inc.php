<?php

$bddPortailGestion = connexion('portail_gestion');
$bddInspections = connexion('inspections');
$bddPlanning = connexion('planning');

/**
 * Retourne un objet connexion vers la base dont le nom est indiqué en paramètre.
 *
 * @param string $base Nom de la base de données.
 * @return PDO Connexion vers la base souhaitée.
 */
function connexion($base) {
    $pdo = new PDO('mysql:host=localhost; dbname=' . $base . '; charset=utf8', 'root', '');

    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}

/**
 * Retourne l'ensemble des données de la table passée en paramètre.
 *
 * @param PDO $base Base de données.
 * @param String $table Table à consulter.
 * @return mixed Résultat du select.
 */
function selectAll($base, $table) {
    return $base->query('SELECT * FROM ' . $table);
}

/**
 * Retourne l'ensemble des données de la table passée en paramètre où la valeur de colonneCondition respecte condition.
 *
 * @param PDO $base Base de données.
 * @param String $table Table à consulter.
 * @param String $colonneCondition Colonne sur laquelle s'applique la condition.
 * @param String $ope Operateur de comparaison (Like, =, >, ...).
 * @param String $condition Condition à respecter.
 * @return mixed Résultat du select.
 */
function selectAllFromWhere($base, $table, $colonneCondition, $ope, $condition) {
    if ($condition == "last_insert_id()")
        $testCondition = $colonneCondition . ' ' . $ope . ' ' . $condition;
    else
        $testCondition = $colonneCondition . ' ' . $ope . ' ' . $base->quote($condition);

    return $base->query('SELECT * FROM ' . $table . ' WHERE ' . $testCondition);
}

/**
 * Insère dans la table passée en paramètre les valeurs entrées sous forme de tableau.
 *
 * @param PDO $base Base de données.
 * @param String $table Table à modifier.
 * @param array $tabValeurs Valeurs à insérer.
 */
function insert($base, $table, $tabValeurs) {
    $valeurs = "";
    for ($i = 0; $i < sizeof($tabValeurs); $i++) {
        if ($i == sizeof($tabValeurs) - 1)
            $valeurs .= $tabValeurs[$i]; // Empèche la dernière virgule
        else
            $valeurs .= $tabValeurs[$i] . ', ';
    }

    $base->exec('INSERT INTO ' . $table . ' VALUES (' . $valeurs . ')') or die (print_r($base->errorInfo(), true));
}

/**
 * Modifie la table passée en paramètre en remplaçant les valeurs indiquées des tuples respectant la condition indiquée.
 *
 * @param PDO $base Base de données.
 * @param String $table Table à modifier.
 * @param String $colonneModif Colonne à modifier dans les tuples concernés.
 * @param String $nouvelleValeur Nouvelle valeur dans les tuples concernés.
 * @param String $colonneCondition Colonne sur laquelle s'applique la condition.
 * @param String $ope Opérateur de comparaison (Like, =, >, ...).
 * @param String $condition Condition à respecter.
 */
function update($base, $table, $colonneModif, $nouvelleValeur, $colonneCondition, $ope, $condition) {
    $testCondition = $colonneCondition . ' ' . $ope . ' ' . $base->quote($condition);
    $base->exec('update ' . $table . ' set ' . $colonneModif . ' = ' . $base->quote($nouvelleValeur) . ' where ' . $testCondition);
}

/**
 * Retourne les différentes informations sur chaque élément du rapport impliquant le PV.
 *
 * @param array $rapport Rapport contenant le PV.
 * @return array Tableau comprenant toutes les informations concernant le rapport impliquant le PV.
 */
function infosBDD($rapport) {
    $bddAffaire = connexion('portail_gestion');

    $affaire = selectAffaireParId($bddAffaire, $rapport['id_affaire'])->fetch();
    $societe = selectSocieteParId($bddAffaire, $affaire['id_societe'])->fetch();
    $odp = selectODPParId($bddAffaire, $affaire['id_odp'])->fetch();
    $client = selectClientParId($bddAffaire, $odp['id_client'])->fetch();

    return array("affaire" => $affaire, "societe" => $societe, "client" => $client);
}

/** REQUETES SQL */

/** PV */

/**
 * Retourne le PV possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant du PV.
 * @return mixed Informations du PV stockées dans la base.
 */
function selectPVParId($base, $id) {
    return selectAllFromWhere($base, "pv_controle", "id_pv", "=", $id);
}

/**
 * Retourne les PV étant compris dans le rapport passé en paramètre.
 *
 * @param PDO $base Base de données.
 * @param int $idRapport Identifiant du rapport.
 * @return mixed Informations des PV du rapport.
 */
function selectPVParRapport($base, $idRapport) {
    return selectAllFromWhere($base, "pv_controle", "id_rapport", "=", $idRapport);
}

/**
 * Retourne le dernier PV ajouté à la base de données.
 *
 * @param PDO $base Base de données.
 * @return mixed Informations du dernier PV crée.
 */
function selectDernierPV($base) {
    return selectAllFromWhere($base, "pv_controle", "id_pv", "=", "last_insert_id()");
}

/** Rapport */

/**
 * Retourne les informations du rapport possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant du rapport.
 * @return mixed Informations du rapport stockées dans la base.
 */
function selectRapportParId($base, $id) {
    return selectAllFromWhere($base, "rapports", "id_rapport", "=", $id);
}

/**
 * Retourne les rapports concernant l'affaire passée en paramètre.
 *
 * @param PDO $base Base de données.
 * @param int $idAffaire Identifiant de l'affaire.
 * @return mixed Informations des rapports de l'affaire.
 */
function selectRapportParAffaire($base, $idAffaire) {
    return selectAllFromWhere($base, "rapports", "id_affaire", "=", $idAffaire);
}

/**
 * Retourne le dernier rapport ajouté à la base de données.
 *
 * @param PDO $base Base de données.
 * @return mixed Informations du dernier rapport crée.
 */
function selectDernierRapport($base) {
    return selectAllFromWhere($base, "rapports", "id_rapport", "=", "last_insert_id()");
}

/** Affaire */

/**
 * Retourne les informations de l'affaire possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant de l'affaire.
 * @return mixed Informations de l'affaire stockées dans la base.
 */
function selectAffaireParId($base, $id) {
    return selectAllFromWhere($base, "affaire", "id_affaire", "=", $id);
}

/**
 * Retourne les informations de l'affaire possédant le nom passé en paramètre.
 *
 * @param PDO $base Base de données.
 * @param string $nom Nom de l'affaire.
 * @return mixed Informations de l'affaire.
 */
function selectAffaireParNom($base, $nom) {
    return selectAllFromWhere($base, "affaire", "num_affaire", "like", $nom);
}

/** Utilisateur */

/**
 * Retourne les informations de l'utilisateur possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant de l'utilisateur.
 * @return mixed Informations de l'utilisateur stockées dans la base.
 */
function selectUtilisateurParId($base, $id) {
    return selectAllFromWhere($base, "utilisateurs", "id_utilisateur", "=", $id);
}

/**
 * Retourne les informations de l'utilisateur possédant le login passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $login Login de l'utilisateur.
 * @return mixed Informations de l'utilisateur stockées dans la base.
 */
function selectUtilisateurParLogin($base, $login) {
    return selectAllFromWhere($base, "utilisateurs", "trim(login)", "like", $login);
}

/**
 * Retourne les informations de l'utilisateur possédant le nom passé en paramètre.
 *
 * @param PDO $base Base de données.
 * @param string $nom Nom de l'utilisateur.
 * @return mixed Informations de l'utilisateur.
 */
function selectUtilisateurParNom($base, $nom) {
    return selectAllFromWhere($base, "utilisateurs", "nom", "like", $nom);
}

/** Contrôle */

/**
 * Retourne les informations du type de contrôle possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant du type de contrôle.
 * @return mixed Informations du type de contrôle stockées dans la base de données.
 */
function selectControleParId($base, $id) {
    return selectAllFromWhere($base, "type_controle", "id_type", "=", $id);
}

/** Discipline */

/**
 * Retourne les informations de la discipline possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant de la discipline.
 * @return mixed Informations de la discipline stockées dans la base de données.
 */
function selectDisciplineParId($base, $id) {
    return selectAllFromWhere($base, "type_discipline", "id_discipline", "=", $id);
}

/** Societe */

/**
 * Retourne les informations de la société possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant de la société.
 * @return mixed Informations de la société stockées dans la base de données.
 */
function selectSocieteParId($base, $id) {
    return selectAllFromWhere($base, "societe", "id_societe", "=", $id);
}

/** ODP */

/**
 * Retourne les informations de l'offre de prix possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant de l'offre de prix.
 * @return mixed Informations de l'offre de prix stockées dans la base de données.
 */
function selectODPParId($base, $id) {
    return selectAllFromWhere($base, "odp", "id_odp", "=", $id);
}

/** Client */

/**
 * Retourne les informations du client possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant de l'offre de prix.
 * @return mixed Informations de l'offre de prix stockées dans la base de données.
 */
function selectClientParId($base, $id) {
    return selectAllFromWhere($base, "client", "id_client", "=", $id);
}

/** Equipement */

/**
 * Retourne les informations de l'équipement possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant de l'équipement.
 * @return mixed Informations de l'équipement stockées dans la base de données.
 */
function selectEquipementParId($base, $id) {
    return selectAllFromWhere($base, "equipement", "idEquipement", "=", $id);
}

/** Fiche Technique Equipement */

/**
 * Retourne les informations techniques de l'équipement possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $idEquipement Identifiant de l'équipement.
 * @return mixed Informations techniques de l'équipement stockées dans la base de données.
 */
function selectFicheTechniqueParEquipement($base, $idEquipement) {
    return selectAllFromWhere($base, "fichetechniqueequipement", "idEquipement", "=", $idEquipement);
}

/** Appareil */

/**
 * Retourne les informations de l'appareil possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $id Identifiant de l'appareil.
 * @return mixed Informations de l'appareil stockées dans la base de données.
 */
function selectAppareilparId($base, $id) {
    return selectAllFromWhere($base, "appareils", "id_appareil", "=", $id);
}

/** Appareils utilises */

/**
 * Retourne les informations des appareils utilisés pour le PV possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $idPV Identifiant du PV.
 * @return mixed Informations des appareils utilisés stockées dans la base de données.
 */
function selectAppareilsUtilisesParPV($base, $idPV) {
    return selectAllFromWhere($base, "appareils_utilises", "id_pv_controle", "=", $idPV);
}

/** Réservoirs */
function selectReservoirParId($base, $id) {
    return selectAllFromWhere($base, "reservoirs", "id_reservoir", "=", $id);
}

/** Constatations */

/**
 * Retourne les constatations effectuées sur le PV possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $idPV Identifiant du PV.
 * @return mixed Constatations faites sur le PV.
 */
function selectConstatationsParPV($base, $idPV) {
    return selectAllFromWhere($base, "constatations_pv", "id_pv", "=", $idPV);
}

/** Conclusions */

/**
 * Retourne les conclusions effectuées sur le PV possédant l'id passé en paramètre dans la base.
 *
 * @param PDO $base Base de données.
 * @param int $idPV Identifiant du PV.
 * @return mixed Conclusions faites sur le PV.
 */
function selectConclusionsParPV($base, $idPV) {
    return selectAllFromWhere($base, "conclusions_pv", "id_pv", "=", $idPV);
}