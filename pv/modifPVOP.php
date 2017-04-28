<?php
    include_once "../menu.php";
    verifSession();
    enTete("Modification de PV",
        array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
        array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bdd = connexion('portail_gestion');

    update($bdd, "pv_controle", "photos_jointes", etatCB($bdd, 'photos_jointes'), "id_pv_controle", "=", $_POST['idPV']);
    update($bdd, "pv_controle", "pieces_jointes", etatCB($bdd, 'pieces_jointes'), "id_pv_controle", "=", $_POST['idPV']);

    update($bdd, "pv_controle", "controle_interne", etatCB($bdd, 'controle_interne'), "id_pv_controle", "=", $_POST['idPV']);
    update($bdd, "pv_controle", "controle_externe", etatCB($bdd, 'controle_externe'), "id_pv_controle", "=", $_POST['idPV']);
    update($bdd, "pv_controle", "controle_peripherique", etatCB($bdd, 'controle_peripherique'), "id_pv_controle", "=", $_POST['idPV']);

    if (isset($_POST['nbAnnexes']) && is_numeric($_POST['nbAnnexes'])) {
        $nbAnnexes = $_POST['nbAnnexes'];
        update($bdd, "pv_controle", "nb_annexes", $nbAnnexes, "id_pv_controle", "=", $_POST['idPV']);
    } else if (isset($_POST['nbAnnexes']) && !is_numeric($_POST['nbAnnexes'])) {
        $nbAnnexes = "";
    }

    $pv = selectAllFromWhere($bdd, "pv_controle", "id_pv_controle", "=", $_POST['idPV'])->fetch();
    $type_controle = selectAllFromWhere($bdd, "type_controle", "id_type", "=", $pv['id_type_controle'])->fetch();

    $rapport = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $pv['id_rapport'])->fetch();
    $affaire = selectAllFromWhere($bdd, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();

    if (isset($_POST['appareil']) && $_POST['appareil'] != "") {
        insert($bdd, "appareils_utilises", array("null", $_POST['appareil'], $_POST['idPV']));
    }

    $appareils = $bdd->query('select * from appareils where appareils.id_appareil not in (select appareils_utilises.id_appareil from appareils_utilises where id_pv_controle = '.$pv['id_pv_controle'].')')->fetchAll();
    $appareilsUtilises = selectAllFromWhere($bdd, "appareils_utilises", "id_pv_controle", "=", $pv['id_pv_controle'])->fetchAll();
    $typeAppareilsUtilises = $bdd->query('select * from appareils where appareils.id_appareil in (select appareils_utilises.id_appareil from appareils_utilises where id_pv_controle = '.$pv['id_pv_controle'].')')->fetchAll();
?>

    <div id="contenu">
        <?php $nomPV = explode(" ", $affaire['num_affaire']); ?>
        <h1 class="ui blue center aligned huge header">Modification du PV <?php echo "SCO ".$nomPV[1]."-".$type_controle['code']."-".sprintf("%03d", $pv['num_ordre']); ?></h1>
        <table id="ensTables">
            <tr>
                <td class="partieTableau">
                    <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionExcel.php"' ?>>
                        <?php creerApercuDetails($rapport, $pv['date']); ?>
                        <table>
                            <?php creerApercuDocuments($rapport); ?>
                            <tr>
                                <td>

                                </td>
                                <td>
                                    <?php
                                        echo '<input type="hidden" name="idPV" value="'.$pv['id_pv_controle'].'">';
                                    ?>
                                    <button id="boutonGenere" class="ui right floated blue button">Générer au format Excel</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
                <td class="partieTableau">
                    <form method="post" action="modifPVOP.php">
                        <table>
                            <tr>
                                <th colspan="2"><h4 class="ui dividing header">Appareils utilisés</h4></th>
                            </tr>

                            <tr>
                                <td>
                                    <label for="appareil"> Appareil à ajouter : </label>
                                    <select class="ui search dropdown listeAjout" name="appareil">
                                        <option selected> </option>
                                        <?php
                                            for ($i = 0; $i < sizeof($appareils); $i++) {
                                                echo '<option value="'.$appareils[$i]['id_appareil'].'">'.$appareils[$i]['systeme'].' '.$appareils[$i]['type'].' ('.$appareils[$i]['num_serie'].')</option>';
                                            }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <label> Appareils déjà ajoutés : </label>
                                    <select disabled size=4 class="ui search dropdown listeUtilises">
                                        <?php
                                            for ($i = 0; $i < sizeof($typeAppareilsUtilises); $i++) {
                                                echo '<option>'.$typeAppareilsUtilises[$i]['systeme'].' '.$typeAppareilsUtilises[$i]['type'].' ('.$typeAppareilsUtilises[$i]['num_serie'].')</option>';
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button class="ui right floated blue button">Ajouter cet appareil</button></td>
                            </tr>
                            <tr>
                                <?php
                                    afficherMessageAjout('appareil', "L'appareil a bien été ajouté !", "Aucun appareil n'a été indiqué !");
                                ?>
                            </tr>
                        </table>
                        <?php
                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv_controle'].'">';
                        ?>
                    </form>
                    <form method="post" action="modifPVOP.php">
                        <table>
                            <tr>
                                <th colspan="3"><h4 class="ui dividing header">Situation de contrôle & annexes</h4></th>
                            </tr>

                            <tr>
                                <td>
                                    <label class="labelCB"> Contrôle interne ? </label>
                                    <?php
                                        if ($pv['controle_interne'] == 1)
                                            echo '<input checked type="checkbox" name="controle_interne">';
                                        else
                                            echo '<input type="checkbox" name="controle_interne">';
                                    ?>
                                </td>
                                <td>
                                    <label class="labelCB"> Contrôle externe ? </label>
                                    <?php
                                        if ($pv['controle_externe'] == 1)
                                            echo '<input checked type="checkbox" name="controle_externe">';
                                        else
                                            echo '<input type="checkbox" name="controle_externe">';
                                    ?>
                                </td>
                                <td>
                                    <label class="labelCB"> Contrôle périphérique ? </label>
                                    <?php
                                        if ($pv['controle_peripherique'] == 1)
                                            echo '<input checked type="checkbox" name="controle_peripherique">';
                                        else
                                            echo '<input type="checkbox" name="controle_peripherique">';
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label class="labelCB"> Photos jointes ? </label>
                                    <?php
                                        if ($pv['photos_jointes'] == 1)
                                            echo '<input checked type="checkbox" name="photos_jointes">';
                                        else
                                            echo '<input type="checkbox" name="photos_jointes">';
                                    ?>
                                </td>
                                <td>
                                    <label class="labelCB"> Pièces jointes ? </label>
                                    <?php
                                        if ($pv['pieces_jointes'] == 1)
                                            echo '<input checked type="checkbox" name="pieces_jointes">';
                                        else
                                            echo '<input type="checkbox" name="pieces_jointes">';
                                    ?>
                                </td>
                                <td>
                                    <label> Annexes : </label>
                                    <div class="ui input">
                                        <?php
                                            if ($pv['nb_annexes'] != 0)
                                                echo '<input type="number" name="nbAnnexes" placeholder="Nombre d\'annexes" value="'.$pv['nb_annexes'].'">';
                                            else
                                                echo '<input type="number" name="nbAnnexes" placeholder="Nombre d\'annexes" value = "0">';
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <?php afficherMessageAjout('nbAnnexes', "Les modifications ont bien été prises en compte !", "Erreur dans la modification"); ?>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td><input type="hidden" name="validerCheckbox" value="1"><button class="ui right floated blue button">Valider</button></td>
                            </tr>
                        </table>
                        <?php
                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv_controle'].'">';
                        ?>
                    </form>
                </td>
            </tr>
        </table>
        <form method="post" action="listePVOP.php">
            <button class="ui right floated blue button">Retour à la liste des affaires</button>
        </form>
    </div>
    </body>
</html>

<?php

/**
 * Affiche un message indiquant le succès où l'échec de la requète de l'utilisateur.
 *
 * @param String $conditionSucces Condition permettant de vérifier le succès.
 * @param String $messageSucces Message en cas de succès.
 * @param String $messageErreur Message en cas d'échec.
 */
function afficherMessageAjout($conditionSucces, $messageSucces, $messageErreur) {
    if (isset($_POST[$conditionSucces]) && $_POST[$conditionSucces] != "") {
        echo '<td><div class="ui message">';
        echo '<div class="header"> Succès !</div>';
        echo '<p id="infosAction">'.$messageSucces.'</p>';
        echo '</div></td>';
    } else if (isset($_POST[$conditionSucces])) {
        echo '<td><div class="ui message">';
        echo '<div class="header"> Erreur </div>';
        echo '<p id="infosAction">'.$messageErreur.'</p>';
        echo '</div></td>';
    }
}

/**
 * Retourne une valeur selon l'état de la checkbox dont le nom est passé en paramètre.
 *
 * @param PDO $bdd Base de données à modifier.
 * @param string $var Nom de la checkbox.
 * @return int Entier représentant le booléen dans la base (1 = vrai, 0 = faux).
 */
function etatCB($bdd, $var) {
    $valRet = selectAllFromWhere($bdd, "pv_controle", "id_pv_controle", "=", $_POST['idPV'])->fetch()[$var];
    if (isset($_POST[$var]) && isset($_POST['validerCheckbox']) && $_POST['validerCheckbox'] == 1) // Si la case n'a pas été cochée
        $valRet = 1;
    else if (isset($_POST['validerCheckbox']) && $_POST['validerCheckbox'] == 1)
        $valRet = 0;

    return $valRet;
}