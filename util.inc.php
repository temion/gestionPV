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
 * Affiche les différentes informations concernant les documents de référence nécessaires pour l'aperçu du PV.
 *
 * @param array $pv Informations du PV stockées dans la base de données.
 */
function creerApercuDocuments($pv) {
    ?>
    <tr>
        <th colspan="2"><h4 class="ui dividing header">Document de référence</h4></th>
    </tr>

    <tr>
        <td>
            <div class="field">
                <label>Suivant procédure : </label>
                <label> <?php echo $pv['procedure_controle']; ?> </label>
            </div>
        </td>
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