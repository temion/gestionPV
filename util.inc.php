<?php

include_once "bdd/bdd.inc.php";

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
        echo '<td><div class="ui message">';
        echo '<div class="header">'.$titreSucces.'</div>';
        echo '<p id="infosAction">'.$messageSucces.'</p>';
        echo '</div></td>';
    } else if (isset($_GET[$conditionSucces])) {
        echo '<td><div class="ui message">';
        echo '<div class="header">'.$titreErreur.'</div>';
        echo '<p id="infosAction">'.$messageErreur.'</p>';
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
        return checkdate($tab[1], $tab[0], $tab[2]);

    return false;
}

/**
 * Affiche les différentes informations concernant l'affaire passée en paramètre.
 *
 * @param array $affaire Informations de l'affaire.
 * @param array $societe Informations de la société.
 * @param array $equipement Informations de l'équipement inspecté.
 * @param array $client Informations du client rencontré.
 * @param array $ficheTechniqueEquipement Informations techniques de l'équipement inspecté.
 * @param array $pv Informations du PV.
 */
function creerApercuModif($affaire, $societe, $equipement, $client, $ficheTechniqueEquipement, $pv) {
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
                <label>N° Equipement : </label>
                <label> <?php echo $equipement['Designation'].' '.$equipement['Type']; ?> </label>
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
                <label> <?php echo ($ficheTechniqueEquipement['diametre']/1000).' m'; ?> </label>
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
                <label> <?php echo ($ficheTechniqueEquipement['hauteurEquipement']/1000).' m'; ?> </label>
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
                <label> ? </label>
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
                <label>Volume : </label>
                <label> ? </label>
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
                <label>Distance entre 2 points : </label>
                <label> ? </label>
            </div>
        </td>
    </tr>
    <tr>
        <td>

        </td>
        <td>
            <div class="field">
                <label>Nombre de génératrices : </label>
                <label> <?php echo $ficheTechniqueEquipement['nbGeneratrice']; ?> </label>
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

?>