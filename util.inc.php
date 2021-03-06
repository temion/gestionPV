<?php
require_once "bdd/bdd.inc.php";

/**
 * Affiche les messages d'erreur ou de succès selon l'action de l'utilisateur.
 *
 * @param string $conditionSucces Variables POST indiquant si l'action a bien été effectuée.
 * @param string $titreSucces Titre de la boîte de texte en cas de succès.
 * @param string $messageSucces Message en cas de succès de l'action.
 * @param string $titreErreur Titre de la boîte de texte en cas d'erreur.
 * @param string $messageErreur Message en cas d'erreur.
 */
function afficherMessage($conditionSucces, $titreSucces, $messageSucces, $titreErreur, $messageErreur) {
    if (isset($_GET[$conditionSucces]) && $_GET[$conditionSucces] >= 1) {
        // Zone de message en cas de "succès"
        echo '<td><div class="ui message">';
        echo '<div class="header">' . $titreSucces . '</div>';
        echo '<p id="infosAction">' . $messageSucces . '</p>';
        echo '</div></td>';
    } else if (isset($_GET[$conditionSucces])) {
        // Zone de message en cas d'"échec"
        echo '<td><div class="ui message">';
        echo '<div class="header">' . $titreErreur . '</div>';
        echo '<p id="infosAction">' . $messageErreur . '</p>';
        echo '</div></td>';
    }
}

/**
 * Transforme la date du format AAAA-MM-JJ au format JJ-MM-AAAA.
 *
 * @param string $date Date à convertir.
 * @return string Nouvelle date.
 */
function conversionDate($date) {
    if ($date != "") {
        $tab = explode("-", $date);
        return $tab[2] . '-' . $tab[1] . '-' . $tab[0];
    }

    return "";
}

/**
 * Vérifie le format dans lequel l'utilisateur a rentré la date.
 *
 * @param string $date Date à vérifier.
 * @return bool Vrai si la format est bien JJ-MM-AAAA.
 */
function verifFormatDates($date) {
    $tab = explode("-", $date);
    if (sizeof($tab) == 3)
        return checkdate($tab[1], $tab[0], $tab[2]);    // Date convertie pour permettre sa vérification

    return false;
}

/**
 * Vérifie qu'une session est ouverte, pour éviter l'accès direct aux différentes fonctionnalités du portail.
 *
 * @return bool Vrai si une session valide (CA ou OP) est ouverte.
 */
function verifSession() {
    return (isset($_SESSION['droit']) && $_SESSION['droit'] == "CA" || $_SESSION['droit'] == "OP");
}

/**
 * Vérifie qu'une session est ouverte en tant que chargé d'affaires.
 *
 * @return bool Vrai si une session chargé d'affaires est ouverte.
 */
function verifSessionCA() {
    return (isset($_SESSION['droit']) && $_SESSION['droit'] == "CA");
}

/**
 * Vérifie qu'une session est ouverte en tant qu'opérateur.
 *
 * @return bool Vrai si une session opérateur est ouverte.
 */
function verifSessionOP() {
    return (isset($_SESSION['droit']) && $_SESSION['droit'] == "OP");
}

/**
 * Affiche les différentes informations concernant l'affaire passée en paramètre.
 *
 * @param array $affaire Informations de l'affaire.
 * @param array $societe Informations de la société.
 * @param array $reservoir Informations du réservoir inspecté.
 * @param array $client Informations du client rencontré.
 * @param array $controleur Informations de la personne responsable du contrôle.
 * @param array $pv Informations du PV.
 */
function creerApercuModif($affaire, $societe, $reservoir, $client, $controleur, $pv) {
    ?>
    <table>
        <tr>
            <th colspan="2"><h3 class="ui right aligned header"><?php echo $affaire['num_affaire']; ?></h3></th>
        </tr>
        <tr>
            <th colspan="2"><h4 class="ui dividing header">Détail de l'affaire</h4></th>
        </tr>

        <tr>
            <td>
                <div class="field">
                    <label>Clients : </label>
                    <label> <?php echo $societe['nom_societe']; ?> </label>
                </div>
            </td>
            <td>
                <div class="field">
                    <label>Réservoir : </label>
                    <label> <?php echo $reservoir['designation'] . ' ' . $reservoir['type']; ?> </label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="field">
                    <label>Personne rencontrée : </label>
                    <label> <?php echo $client['nom']; ?> </label>
                </div>
            </td>
            <td>
                <div class="field">
                    <label>Diamètre : </label>
                    <label> <?php echo ($reservoir['diametre'] / 1000) . ' m'; ?> </label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="field">
                    <div class="field">
                        <label>Numéro de commande client : </label>
                        <label> <?php echo $affaire['commande']; ?> </label>
                    </div>
                </div>
            </td>
            <td>
                <div class="field">
                    <label>Hauteur : </label>
                    <label> <?php echo ($reservoir['hauteur'] / 1000) . ' m'; ?> </label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="field">
                    <label>Lieu : </label>
                    <label> <?php echo $affaire['lieu_intervention']; ?> </label>
                </div>
            </td>
            <td>
                <div class="field">
                    <label>Hauteur produit : </label>
                    <label> <?php echo ($reservoir['hauteur_produit'] / 1000) . ' m'; ?> </label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="field">
                    <label>Responsable du contrôle : </label>
                    <label> <?php echo $controleur['nom']; ?></label>
                </div>
            </td>
            <td>
                <div class="field">
                    <label>Volume : </label>
                    <label> <?php echo ($reservoir['volume'] / 1000) . ' m<sup>3</sup>'; ?> </label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="field">
                    <label>Date de début du contrôle : </label>
                    <label> <?php echo conversionDate($pv['date_debut']); ?> </label>
                </div>
            </td>
            <td>
                <div class="field">
                    <label>Distance entre 2 points : </label>
                    <label> <?php echo ($reservoir['distance_points'] / 1000) . ' m'; ?> </label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="field">
                    <label>Date de fin du contrôle : </label>
                    <label> <?php echo conversionDate($pv['date_fin']); ?> </label>
                </div>
            </td>
            <td>
                <div class="field">
                    <label>Nombre de génératrices : </label>
                    <label> <?php echo $reservoir['nb_generatrices']; ?> </label>
                </div>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Affiche les différentes informations concernant les documents de référence nécessaires pour l'aperçu du PV.
 *
 * @param array $pv Informations du PV stockées dans la base de données.
 */
function creerApercuDocuments($pv) {
    ?>
    <tr>
        <th colspan="3"><h4 class="ui dividing header">Document de référence</h4></th>
    </tr>

    <tr>
        <td>
            <div class="field">
                <label>Suivant procédure : </label>
                <label> <?php echo $pv['procedure_controle']; ?> </label>
            </div>
        </td>
        <td></td>
        <td>
            <div class="field">
                <label>Code d'interprétation : </label>
                <label> <?php echo $pv['code_inter']; ?> </label>
            </div>
        </td>
    </tr>
    <?php
}

/**
 * Remplis une ligne de tableau avec les informations de base du pv passé en paramètre.
 *
 * @param array $pv PV à afficher.
 * @param PDOStatement $prepareUtilisateur PreparedStatement pour obtenir le nom du responsable du contrôle.
 * @param PDOStatement $prepareRapport PreparedStatement pour obtenir le rapport.
 * @param PDOStatement $prepareAffaire PreparedStatement pour obtenir l'affaire.
 * @param PDOStatement $prepareControle PreparedStatement pour obtenir le type de contrôle.
 * @param PDOStatement $prepareReservoir PreparedStatement pour obtenir les informations du réservoir inspecté.
 * @param PDOStatement $prepareAvancement PreparedStatement pour obtenir le stade d'avancement du contrôle.
 * @param PDOStatement $prepareDiscipline PreparedStatement pour obtenir la discipline du contrôle.
 * @param string $lienRetour Lien de la page à atteindre en cliquant sur Modifier.
 */
function creerLignePV($pv, $prepareUtilisateur, $prepareRapport, $prepareAffaire, $prepareControle, $prepareReservoir, $prepareAvancement, $prepareDiscipline, $lienRetour) {
    $prepareUtilisateur->execute(array($pv['id_controleur']));
    $controleur = $prepareUtilisateur->fetch();
    $prepareUtilisateur->closeCursor();

    $prepareRapport->execute(array($pv['id_rapport']));
    $prepareAffaire->execute(array($prepareRapport->fetch()['id_affaire']));
    $affaire = $prepareAffaire->fetch();
    $prepareRapport->closeCursor();
    $prepareAffaire->closeCursor();

    $prepareControle->execute(array($pv['id_type_controle']));
    $controle = $prepareControle->fetch();
    $prepareControle->closeCursor();

    $prepareReservoir->execute(array($pv['id_reservoir']));
    $reservoir = $prepareReservoir->fetch();
    $prepareReservoir->closeCursor();

    $prepareAvancement->execute(array($pv['id_avancement']));
    $avancement = $prepareAvancement->fetch();
    $prepareAvancement->closeCursor();

    $prepareDiscipline->execute(array($pv['id_discipline']));
    $discipline = $prepareDiscipline->fetch();
    $prepareDiscipline->closeCursor();

    $titrePV = "SCO" . explode(" ", $affaire['num_affaire'])[1] . '-' . $discipline['code'] . '-' . $controle['code'] . '-' . sprintf("%03d", $pv['num_ordre']);

    if (isset($_SESSION['droit']) && $_SESSION['droit'] == "OP" && $_SESSION['id_connecte'] == $controleur['id_utilisateur'])
        echo '<tr class="pvAControler">';
    else
        echo '<tr>';
    echo '<td><strong>' . $pv['id_pv'] . '</strong> : ' . $titrePV . '</td>';
    echo '<td>' . $affaire['num_affaire'] . '</td>';
    echo '<td>' . $reservoir['designation'] . ' ' . $reservoir['type'] . '</td>';
    echo '<td>' . $controle['libelle'] . ' ' . $pv['num_ordre'] . ' (' . $controle['code'] . ') <br/>';

    if ($pv['date_debut'] != "" && $pv['date_fin'] != "")
        echo 'du ' . conversionDate($pv['date_debut']) . ' au ' . conversionDate($pv['date_fin']) . '</td>';
    else if ($pv['date_debut'] != "" && $pv['date_fin'] == "")
        echo 'début du contrôle le '.conversionDate($pv['date_debut']);
    else if ($pv['date_debut'] == "" && $pv['date_fin'] != "")
        echo 'fin du contrôle le '.conversionDate($pv['date_fin']);

    echo '<td>' . $controleur['nom'] . '</td>';
    echo '<td>' . $avancement['stade'] . '</td>';
    echo '<td><form method="get" action="' . $lienRetour . '"><button name="idPV" value="' . $pv['id_pv'] . '" class="ui right floated blue button">Modifier</button></form></td>';
    echo '</tr>';
}

/**
 * Supprime de la base le PV possédant l'identifiant passé en paramètre.
 *
 * @param PDO $bdd Base de données des PV.
 * @param int $idPV Identifiant du PV dans la base.
 */
function supprimerPV($bdd, $idPV) {
    $bdd->query('delete from pv_controle where id_pv = ' .$idPV);
    $bdd->query('delete from appareils_utilises where id_pv_controle = '.$idPV);
    $bdd->query('delete from constatations_pv where id_pv = '.$idPV);
    $bdd->query('delete from conclusions_pv where id_pv = '.$idPV);
}

/**
 * Supprime de la base le rapport possédant l'identifiant passé en paramètre, ainsi que tous les PV qui le composent.
 *
 * @param PDO $bdd Base de données des PV.
 * @param int $idRapport Identifiant du Rapport dans la base.
 */
function supprimerRapport($bdd, $idRapport) {
    $pvRapport = selectPVParRapport($bdd, $idRapport)->fetchAll();

    for ($i = 0; $i < sizeof($pvRapport); $i++) {
        supprimerPV($bdd, $pvRapport[$i]['id_pv']);
    }

    $bdd->query('delete from rapports where id_rapport = '.$idRapport);
}
?>