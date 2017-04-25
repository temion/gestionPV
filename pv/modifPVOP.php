<?php
    include_once "../menu.php";
    verifSession();
    enTete("Modification de PV",
        array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
        array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bdd = connexion('portail_gestion');

    $pv = selectAllFromWhere($bdd, "pv_controle", "id_pv_controle", "=", $_POST['idPV'])->fetch();
    $type_controle = selectAllFromWhere($bdd, "type_controle", "id_type", "=", $pv['id_type_controle'])->fetch();

    $affaireInspection = selectAllFromWhere($bdd, "affaire_inspection", "id_affaire_inspection", "=", $pv['id_affaire_inspection'])->fetch();
    $affaire = selectAllFromWhere($bdd, "affaire", "id_affaire", "=", $affaireInspection['id_affaire'])->fetch();

    if (isset($_POST['appareil']) && $_POST['appareil'] != "") {
        insert($bdd, "appareils_utilises", array("null", $_POST['appareil'], $_POST['idPV']));
    }

//    if (isset($_POST['photosJointes']) && $_POST['photosJointes'] != "") {
//        if ($pv['photos_jointes'] == false) {
//            update($bdd, "pv_controle", "photos_jointes", "true", "id_pv_controle", "=", $pv['id_pv_controle']);
//        }
//    } else {
//        if ($pv['photos_jointes'] == true) {
//            update($bdd, "pv_controle", "photos_jointes", "false", "id_pv_controle", "=", $pv['id_pv_controle']);
//        }
//    }

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
                        <?php creerApercuDetails($affaireInspection); ?>
                        <table>
                            <?php creerApercuDocuments($affaireInspection); ?>
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
                                    <label> Appareil à ajouter : </label>
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
                                <th colspan="3"><h4 class="ui dividing header">Annexes</h4></th>
                            </tr>

                            <tr>
                                <td>
                                    <label class="labelCB"> Photos jointes ? </label>
                                    <?php
                                        if ($pv['photos_jointes'] == true) {
                                            echo '<input checked type="checkbox" name="photosJointes">';
                                        } else {
                                            echo '<input type="checkbox" name="photosJointes">';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <label class="labelCB"> Pièces jointes ? </label>
                                    <input type="checkbox" name="piecesJointes">
                                </td>
                                <td>
                                    <label> Nombre d'annexes : </label>
                                    <div class="ui input">
                                        <input type="number" name="nbAnnexes" placeholder="Nombre d'annexes">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td><button class="ui right floated blue button">Valider</button></td>
                            </tr>
                        </table>
                        <?php
                        echo '<input type="hidden" name="idPV" value="'.$pv['id_pv_controle'].'">';
                        ?>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    </body>
</html>

<?php

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