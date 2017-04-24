<?php
include_once "../menu.php";
enTete("Modification de PV",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$bdd = connexion('portail_gestion');

$pv = $bdd->query('select * from affaire_inspection where id_pv = '.$_POST['idPV'])->fetch();
$controle = $bdd->query('select * from controles_sur_pv where id_controle_pv = '.$_POST['idControle'])->fetch();
$type_controle = $bdd->query('select * from type_controle where id_type = '.$controle['id_type_controle'])->fetch();

if (isset($_POST['appareil']) && $_POST['appareil'] != "") {
    $bdd->exec('insert into appareils_utilises values (null, '.$_POST['appareil'].', '.$_POST['idControle'].', '.$_POST['idPV'].')') or die(print_r($bdd, true));
}

$appareils = $bdd->query('select * from appareils where appareils.id_appareil not in (select appareils_utilises.id_appareil from appareils_utilises where id_pv = '.$pv['id_pv'].')')->fetchAll();

$appareilsUtilises = $bdd->query('select * from appareils_utilises where id_pv = '.$pv['id_pv'])->fetchAll();
$typeAppareilsUtilises = $bdd->query('select * from appareils where appareils.id_appareil in (select appareils_utilises.id_appareil from appareils_utilises where id_pv = '.$pv['id_pv'].')')->fetchAll();
?>

    <div id="contenu">
        <h1 class="ui blue center aligned huge header">Modification du PV <?php echo $pv['id_pv'].' '.$type_controle['libelle']; ?></h1>
        <table id="ensTables">
            <tr>
                <td class="partieTableau">
                    <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionExcel.php"' ?>>
                        <?php creerApercuDetails($pv); ?>
                        <table>
                            <?php creerApercuDocuments($pv); ?>
                            <tr>
                                <td>

                                </td>
                                <td>
                                    <?php
                                        echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                                        echo '<input type="hidden" name="idControle" value="'.$controle['id_controle_pv'].'">';
                                    ?>
                                    <button id="boutonGenere" class="ui right floated blue button">Générer sous format Excel</button>
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
                                    afficherMessageAjout('appareil', "L'appareil a bien été ajouté !", "Aucun appareil ou contrôle associé n'a été indiqué.");
                                ?>
                            </tr>
                        </table>
                        <?php
                            echo '<input type="hidden" name="idPV" value="'.$_POST['idPV'].'">';
                            echo '<input type="hidden" name="idControle" value="'.$_POST['idControle'].'">';
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