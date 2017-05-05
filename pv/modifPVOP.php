<?php
    include_once "../menu.php";
    verifSession();
    enTete("Modification de PV",
        array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
        array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bdd = connexion('portail_gestion');
    $bddEquipement = connexion('theodolite');

    if (isset($_GET['constatation']) && $_GET['constatation'] != "") {
        if (isset($_GET['typeConstatation']) && $_GET['typeConstatation'] != "")
            insert($bdd, "constatations_pv", array("null", $_GET['idPV'], $bdd->quote($_GET['typeConstatation']), $bdd->quote($_GET['constatation'])));
        else
            insert($bdd, "constatations_pv", array("null", $_GET['idPV'], "null", $bdd->quote($_GET['constatation'])));
    }

    if (isset($_GET['conclusion']) && $_GET['conclusion'] != "") {
        insert($bdd, "conclusions_pv", array("null", $_GET['idPV'], $bdd->quote($_GET['conclusion'])));
    }

    $pv = selectAllFromWhere($bdd, "pv_controle", "id_pv", "=", $_GET['idPV'])->fetch();
    $type_controle = selectAllFromWhere($bdd, "type_controle", "id_type", "=", $pv['id_type_controle'])->fetch();

    $rapport = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $pv['id_rapport'])->fetch();
    $affaire = selectAllFromWhere($bdd, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();
    $societe = selectAllFromWhere($bdd, "societe", "id_societe", "=", $affaire['id_societe'])->fetch();
    $odp = selectAllFromWhere($bdd, "odp", "id_odp", "=", $affaire['id_odp'])->fetch();
    $client = selectAllFromWhere($bdd, "client", "id_client", "=", $odp['id_client'])->fetch();
    $equipement = selectAllFromWhere($bddEquipement, "equipement", "idEquipement", "=", $pv['id_equipement'])->fetch();
    $ficheTechniqueEquipement = selectAllFromWhere($bddEquipement, "fichetechniqueequipement", "idEquipement", "=", $pv['id_equipement'])->fetch();

    $appareils = $bdd->query('select * from appareils where appareils.id_appareil not in (select appareils_utilises.id_appareil from appareils_utilises where id_pv_controle = '.$pv['id_pv'].')')->fetchAll();
    $appareilsUtilises = selectAllFromWhere($bdd, "appareils_utilises", "id_pv_controle", "=", $pv['id_pv'])->fetchAll();
    $typeAppareilsUtilises = $bdd->query('select * from appareils where appareils.id_appareil in (select appareils_utilises.id_appareil from appareils_utilises where id_pv_controle = '.$pv['id_pv'].')')->fetchAll();

    $titre = "SCO".explode(" ",$affaire['num_affaire'])[1].'-'.$type_controle['code'].'-'.sprintf("%03d", $pv['num_ordre']);
?>

        <?php
            creerModal("constatation");
            creerModal("conclusion");
        ?>

        <div id="contenu">
            <?php $nomPV = explode(" ", $affaire['num_affaire']); ?>
            <h1 class="ui blue center aligned huge header">Modification du PV <?php echo "SCO ".$nomPV[1]."-".$type_controle['code']."-".sprintf("%03d", $pv['num_ordre']); ?></h1>
            <table id="ensTables">
                <tr>
                    <td class="partieTableau">
                        <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionPV.php"' ?>>














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
                                            <label> <?php echo  $affaire['lieu_intervention']; ?> </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="field">
                                            <label>Hauteur produit : </label>
                                            <label> ? </label>
                                            <!--                    <div class="field">-->
                                            <!--                        <input type="text" name="hauteur_produit" placeholder="Hauteur du produit (m)">-->
                                            <!--                    </div>-->
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
                                            <!--                    <div class="field">-->
                                            <!--                        <input type="text" name="volume_equipement" placeholder="Volume (m²)">-->
                                            <!--                    </div>-->
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <div class="field">
                                            <label>Distance entre 2 points : </label>
                                            <label> ? </label>
                                            <!--                    <div class="field">-->
                                            <!--                        <input type="text" name="distance_points" placeholder="Distance (m)">-->
                                            <!--                    </div>-->
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


























                            <table>
                                <?php creerApercuDocuments($rapport); ?>
                                <tr>
                                    <td>
                                        <?php
                                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                                            echo '<button class="ui left floated red button" name="reset" value="1"> Regénérer le fichier</button>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                                            echo '<button id="boutonGenere" class="ui right floated blue button">Télécharger le fichier Excel</button>';
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                    <td class="partieTableau">
                        <form method="get" action="ajoutAppareilPV.php">
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
                                    <td>
                                        <input type="hidden" name="idPV" value="<?php echo $pv['id_pv']; ?>">
                                        <button class="ui right floated blue button">Ajouter cet appareil</button></td>
                                </tr>

                                <tr>
                                    <td>
                                        <?php
                                            afficherMessageAjout('appareil', "L'appareil a bien été ajouté !", "Aucun appareil n'a été indiqué !");
                                        ?>
                                    </td>
                                </tr>
                            </table>
                            <?php
                                echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                            ?>
                        </form>
                        <form method="get" action="ajoutSituationPV.php">
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
                                    <td colspan="2"> <?php afficherMessageAjout('nbAnnexes', "Les modifications ont bien été prises en compte !", "Erreur dans la modification"); ?> </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <input type="hidden" name="idPV" value="<?php echo $pv['id_pv']; ?>">
                                        <button class="ui right floated blue button">Valider</button>
                                    </td>
                                </tr>
                            </table>
                            <?php
                                echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                            ?>
                        </form>
                        <table>
                            <tr>
                                <th colspan="2"><h4 class="ui dividing header">Constatations & conclusions</h4></th>
                            </tr>

                            <tr>
                                <td><button id="boutonConstatation" class="ui left floated blue button">Ajouter une constatation</button></td>
                                <td><button id="boutonConclusion" class="ui right floated blue button">Ajouter une conclusion</button></td>
                            </tr>
                        </table>
                        <form enctype="multipart/form-data" action="uploadPV.php" method="post">
                            <table>
                                <tr>
                                    <th colspan="2"><h4 class="ui dividing header">Uploader le fichier Excel complété</h4></th>
                                </tr>

                                <tr>
                                    <td>
                                        <input type="hidden" name="taille_max" value="30000" />
                                        <input name="pv_excel" type="file" />
                                    </td>
                                    <td>
                                        <?php
                                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                                            echo '<input type="hidden" name="lienRetour" value="modifPVOP">';
                                        ?>
                                        <button class="ui right floated blue button">Envoyer le fichier</button>
                                    </td>
                                </tr>

                                <tr>
                                    <?php
                                        afficherMessage('erreurUpload', "Succès !", "Le fichier a bien été uploadé !", "Erreur", "Erreur dans l'upload du fichier");
                                    ?>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>
            <form method="get" action="listePVOP.php">
                <?php echo '<input type="hidden" name="nomPV" value="'.$titre.'">'; ?>
                <button id="retour" class="ui right floated blue button">Retour à la liste des PV</button>
            </form>
        </div>
    </body>
</html>

<script>
    $("#boutonConstatation").on("click", function() {
        $('#modalConstatation').modal('show');
    });

    $("#boutonConclusion").on("click", function() {
        $('#modalConclusion').modal('show');
    });
</script>

<?php

/**
 * Affiche un message indiquant le succès où l'échec de la requète de l'utilisateur.
 *
 * @param String $conditionSucces Condition permettant de vérifier le succès.
 * @param String $messageSucces Message en cas de succès.
 * @param String $messageErreur Message en cas d'échec.
 */
function afficherMessageAjout($conditionSucces, $messageSucces, $messageErreur) {
    if (isset($_GET[$conditionSucces]) && $_GET[$conditionSucces] != "") {
        echo '<div class="ui message">';
        echo '<div class="header"> Succès !</div>';
        echo '<p id="infosAction">'.$messageSucces.'</p>';
        echo '</div>';
    } else if (isset($_GET[$conditionSucces])) {
        echo '<div class="ui message">';
        echo '<div class="header"> Erreur </div>';
        echo '<p id="infosAction">'.$messageErreur.'</p>';
        echo '</div>';
    }
}

/**
 * Fonction permettant de créer les popups de création de constatations et conclusions.
 *
 * @param string $nom Constatation ou conclusion, permet de définir le contenu du popup.
 */
function creerModal($nom) {
    $nomMaj = ucfirst($nom);
    $id = "modal".$nomMaj;
    $idType = "type".$nomMaj;
?>
    <div id="<?php echo $id; ?>" class="ui large modal">
        <div style="text-align: left;" class="header"><?php echo $nomMaj;?><i class="close icon"></i></div>
        <div class="content">
            <form method="get" action="modifPVOP.php">
                <div class="ui form">
                    <?php if ($nom != "conclusion") { ?>
                    <div class="field">
                        <label>Type de <?php echo $nom; ?></label>
                        <input type="text" name="<?php echo $idType; ?>">
                    </div>
                    <?php } ?>
                    <div class="field">
                        <label><?php echo $nomMaj; ?></label>
                        <textarea rows="2" name="<?php echo $nom; ?>"></textarea>
                    </div>
                    <input type="hidden" name="idPV" value="<?php echo $_GET['idPV']; ?>">
                    <button style="margin: 0 1em 0.5em 0;" class="ui right floated blue button">Valider cette <?php echo $nom; ?></button>
                </div>
            </form>
        </div>
    </div>
<?php
}
?>