<?php
    include_once "../menu.php";
    verifSession("OP");
    enTete("Modification de rapport",
             array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
             array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bdd = connexion('portail_gestion');
    $rapport = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $_GET['idRapport'])->fetch();
    $affaire = selectAllFromWhere($bdd, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();

    if (isset($_GET['inspection']) && $_GET['inspection'] == "true") {
        if (isset($_GET['equipement_inspecte']) && $_GET['equipement_inspecte'] != "") {
            insert($bdd, "equipements_inspectes", array("null", $rapport['id_rapport'], $_GET['equipement_inspecte']));
        } else {
            $_GET['equipement_inspecte'] = "";
        }
    }

//    $rapport = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $_GET['idRapport'])->fetch();

    if (isset($_GET['controle']) && $_GET['controle'] != "") {
        if (verifFormatDates($_GET['date_debut'])) {
            $controle = selectAllFromWhere($bdd, "type_controle", "concat(libelle, ' (', code, ')')", "like", $_GET['controle'])->fetch();
            $nouvelleVal = $controle['num_controle'] + 1;
            insert($bdd, "pv_controle", array("null", $controle['id_type'], $_GET['idRapport'], $nouvelleVal, "false", "false", 0, "false", "false", "false", $bdd->quote(conversionDate($_GET['date_debut'])), "null"));
            update($bdd, "type_controle", "num_controle", $nouvelleVal, "id_type", "=", $controle['id_type']);
        } else {
            $_GET['controle'] = "";
        }
    }

    $controles = selectAll($bdd, "type_controle")->fetchAll();
    $controlesEffectues = selectAllFromWhere($bdd, "pv_controle", "id_rapport", "=", $rapport['id_rapport'])->fetchAll();
    $typeControle = $bdd->prepare('select * from type_controle where id_type = ?');

    $bddEquipement = connexion('theodolite');
    $listeEquipement = selectAll($bddEquipement, "equipement")->fetchAll();
    $equipementsInspectes = selectAllFromWhere($bdd, "equipements_inspectes", "id_rapport", "=", $rapport['id_rapport'])->fetchAll();
    $designationEquipementsInspectes = $bddEquipement->prepare('select * from equipement where idEquipement = ?');

    $desEquipement = array();
    for ($i = 0; $i < sizeof($equipementsInspectes); $i++) {
        $designationEquipementsInspectes->execute(array($equipementsInspectes[$i]['id_equipement']));
        array_push($desEquipement, $designationEquipementsInspectes->fetchAll());
    }
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">Modification du rapport <?php echo $affaire['num_affaire']; ?></h1>
            <table id="ensTables">
                <tr>
                    <td class="partieTableau">
                        <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionRapport.php"' ?>>
                            <?php creerApercuDetails($rapport); ?>
                            <table>
                                <?php creerApercuDocuments($rapport); ?>
                                <tr>
                                    <td>
                                        <?php
                                            echo '<input type="hidden" name="idRapport" value="'.$rapport['id_rapport'].'">';
                                        ?>
                                    </td>
                                    <td>
                                        <button id="boutonGenere" class="ui right floated blue button">Télécharger le fichier Excel</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                    <td class="partieTableau">
                        <form method="get" action="modifRapportCA.php">
                            <table>
                                <!-- ToDo -->
                                <tr>
                                    <th colspan="2"><h4 class="ui dividing header">Équipements à inspecter</h4></th>
                                </tr>

                                <tr>
                                    <td>
                                        <label> Équipement à inspecter: </label>
                                        <select class="ui search dropdown listeAjout" name="equipement_inspecte">
                                            <option selected> </option>
                                            <?php
                                            for ($i = 0; $i < sizeof($listeEquipement); $i++) {
                                                echo '<option value="'.$listeEquipement[$i]['idEquipement'].'">'.$listeEquipement[$i]['Designation'].' '.$listeEquipement[$i]['Type'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <label> Équipements déjà ajoutés : </label>
                                        <select disabled size=4 class="ui search dropdown listeAjout">
                                            <?php
                                                for ($i = 0; $i < sizeof($desEquipement); $i++) {
                                                    echo '<option>'.$desEquipement[$i][0]['Designation'].' '.$desEquipement[$i][0]['Type'].'</option>';
                                                }
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <?php
                                        afficherMessageAjout('equipement_inspecte', "L'équipement a bien été ajouté !", "Aucun équipement n'a été indiqué.");
                                    ?>
                                    <td colspan="2"><button class="ui right floated blue button" name="inspection">Ajouter cet équipement</button></td>
                                </tr>
                                <?php
                                    echo '<input type="hidden" name="idRapport" value="'.$_GET['idRapport'].'">';
                                    echo '<input type="hidden" name="inspection" value="true">'; // Flag pour indiquer que le formulaire a été envoyé
                                ?>
                            </table>
                        </form>
                            <!-- ToDo -->
                        <form method="get" action="modifRapportCA.php">
                            <table>
                                <tr>
                                    <th colspan="2"><h4 class="ui dividing header">Contrôles à effectuer</h4></th>
                                </tr>


                                <tr>
                                    <td>
                                        <label> Contrôles à effectuer : </label>
                                        <select class="ui search dropdown listeAjout" name="controle">
                                            <option selected> </option>
                                            <?php
                                                for ($i = 0; $i < sizeof($controles); $i++) {
                                                    echo '<option>'.$controles[$i]['libelle'].' ('.$controles[$i]['code'].')</option>';
                                                }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <label> Contrôles déjà ajoutés : </label>
                                        <select disabled size=4 class="ui search dropdown listeAjout">
                                            <?php
                                            for ($i = 0; $i < sizeof($controlesEffectues); $i++) {
                                                $typeControle->execute(array($controlesEffectues[$i]['id_type_controle']));
                                                $infosControle = $typeControle->fetch();
                                                echo '<option>'.$infosControle['libelle'].' ('.$infosControle['code'].') - '.conversionDate($controlesEffectues[$i]['date']).'</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <label> Début prévu du contrôle le : </label>
                                        <div class="ui input" >
                                            <input style="width: 20em" type="date" name="date_debut" placeholder="(JJ-MM-AAAA)">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <?php
                                        afficherMessageAjout('controle', "Le contrôle a bien été ajouté !", "Aucun contrôle n'a été indiqué.");
                                    ?>
                                    <td colspan="2"><button class="ui right floated blue button">Ajouter ce contrôle</button></td>
                                </tr>
                                <?php
                                    echo '<input type="hidden" name="idRapport" value="'.$_GET['idRapport'].'">';
                                ?>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>
            <form method="post" action="listeRapportsCA.php">
                <button class="ui right floated blue button">Retour à la liste des rapports</button>
            </form>
        </div>
    </body>
</html>

<script>
    $(function () {
        $("#controleGenere").on("change", function () {
            console.log($("#controleGenere").val())
            if ($("#controleGenere").val().length >  0)
                $("#boutonGenere").prop('disabled', false);
            else
                $("#boutonGenere").prop('disabled', true);
        })
    });
</script>

<?php

/**
 * Affiche le message passé en paramètre.
 *
 * @param string $conditionSucces Condition d'affichage du message de succès.
 * @param string $messageSucces Message à afficher en cas de succès.
 * @param string $messageErreur Message à afficher en cas d'ererur.
 */
function afficherMessageAjout($conditionSucces, $messageSucces, $messageErreur) {
    if (isset($_GET[$conditionSucces]) && $_GET[$conditionSucces] != "") {
        echo '<td><div class="ui message">';
        echo '<div class="header"> Succès !</div>';
        echo '<p id="infosAction">'.$messageSucces.'</p>';
        echo '</div></td>';
    } else if (isset($_GET[$conditionSucces])) {
        echo '<td><div class="ui message">';
        echo '<div class="header"> Erreur </div>';
        echo '<p id="infosAction">'.$messageErreur.'</p>';
        echo '</div></td>';
    }
}