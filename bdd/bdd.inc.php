<?php

/**
 * Retourne un objet connexion vers la base dont le nom est indiqué en paramètre.
 *
 * @param string $base Nom de la base de données.
 * @return PDO Connexion vers la base souhaitée.
 */
function connexion($base) {
    $pdo = new PDO('mysql:host=localhost; dbname='.$base.'; charset=utf8', 'root', '');

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
    return $base->query('select * from '.$table);
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
        $testCondition = $colonneCondition.' '.$ope.' '.$condition;
    else
        $testCondition = $colonneCondition.' '.$ope.' '.$base->quote($condition);

    return $base->query('select * from '.$table.' where '.$testCondition);
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

    $base->exec('insert into '.$table.' values ('.$valeurs.')') or die (print_r($base->errorInfo(), true));
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
    $testCondition = $colonneCondition.' '.$ope.' '.$base->quote($condition);
    $base->exec('update '.$table.' set '.$colonneModif.' = '.$base->quote($nouvelleValeur).' where '.$testCondition);
}

/**
 * Retourne les différentes informations sur chaque élément de l'affaire impliquant le PV.
 *
 * @param array $affaireInspection Affaire concernant le PV.
 * @return array Tableau comprenant toutes les informations concernant l'affaire impliquant le PV.
 */
function infosBDD($affaireInspection) {
    $bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');

    $affaire = $bddAffaire->query('select * from affaire where id_affaire = '.$affaireInspection['id_affaire'])->fetch();
    $societe = $bddAffaire->query('select * from societe where id_societe = '.$affaire['id_societe'])->fetch();
    $client = $bddAffaire->query('select * from client where id_client = '.$societe['ref_client'])->fetch();

    $equipement = $bddEquipement->query('select * from equipement where idEquipement = '.$affaireInspection['id_equipement'])->fetch();
    $ficheTechniqueEquipement = $bddEquipement->query('select * from fichetechniqueequipement where idEquipement = '.$affaireInspection['id_equipement'])->fetch();

    return array("affaire"=>$affaire, "societe"=>$societe, "client"=>$client, "equipement"=>$equipement, "ficheTechniqueEquipement"=>$ficheTechniqueEquipement);
}
