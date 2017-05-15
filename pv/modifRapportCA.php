<?php
require_once "../menu.php";
verifSession("OP");
enTete("Modification de rapport",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/modifRapport.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$bdd = connexion('portail_gestion');

if (isset($_GET['ajoutRapport']) && $_GET['ajoutRapport'] == 1) {
    creerRapport($bdd);
    $_GET['idRapport'] = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", "last_insert_id()")->fetch()['id_rapport'];
    $rapport = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $_GET['idRapport'])->fetch();
    $affaire = selectAllFromWhere($bdd, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();
    ajouterHistorique($bdd, "Création du rapport de l'affaire ".$affaire['num_affaire'],"pv/modifRapportCA.php?idRapport=", $rapport['id_rapport']);
}

$rapport = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $_GET['idRapport'])->fetch();
$affaire = selectAllFromWhere($bdd, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();
$societe = selectAllFromWhere($bdd, "societe", "id_societe", "=", $affaire['id_societe'])->fetch();
$odp = selectAllFromWhere($bdd, "odp", "id_odp", "=", $affaire['id_odp'])->fetch();
$client = selectAllFromWhere($bdd, "client", "id_client", "=", $odp['id_client'])->fetch();

$receveur = selectAllFromWhere($bdd, "utilisateurs", "id_utilisateur", "=", $rapport['id_receveur'])->fetch();
$analyste = selectAllFromWhere($bdd, "utilisateurs", "id_utilisateur", "=", $rapport['id_analyste'])->fetch();

$controles = selectAll($bdd, "type_controle")->fetchAll();

$disciplines = selectAll($bdd, "type_discipline")->fetchAll();

$bddEquipement = connexion('theodolite');
$listeEquipement = selectAll($bddEquipement, "equipement")->fetchAll();

$listeUtilisateurs = selectAll($bdd, "utilisateurs")->fetchAll();
?>

    <div id="contenu">
        <h1 class="ui blue center aligned huge header">Modification du
            rapport <?php echo $affaire['num_affaire']; ?></h1>
        <?php
        afficherMessage('ajout', "Succès !", "Le PV a bien été crée.",
            "Erreur", "Veuillez remplir tous les champs précédés d'un astérisque par des valeurs valides.");
        ?>
        <div class="ensTables">
            <table>
                <tr>
                    <th colspan="3"><h4 class="ui dividing header">Détails de l'affaire</h4></th>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label class="desc">Clients : </label>
                            <label> <?php echo $societe['nom_societe']; ?> </label>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label class="desc">Lieu : </label>
                            <label> <?php echo $affaire['lieu_intervention']; ?> </label>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label class="desc">Demande reçue par : </label>
                            <label> <?php echo $receveur['nom']; ?> </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label class="desc">Nom (Coord.) : </label>
                            <label> <?php echo $client['nom']; ?> </label>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label class="desc">Téléphone : </label>
                            <label> <?php echo $client['tel']; ?> </label>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label class="desc">Demande analysée par : </label>
                            <label> <?php echo $analyste['nom']; ?> </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label class="desc">Appel d'offre ? </label>
                            <label> <?php echo($rapport['appel_offre'] ? "Oui" : "Non"); ?> </label>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label class="desc">Avenant affaire n° : </label>
                            <label> <?php echo $rapport['avenant_affaire']; ?> </label>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label class="desc">Obtention de l'offre par : </label>
                            <label> <?php echo $rapport['obtention']; ?> </label>
                        </div>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <th colspan="2"><h4 class="ui dividing header">Documents de référence</h4></th>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label class="desc">Procédure de contrôle : </label>
                            <label> <?php echo $rapport['procedure_controle']; ?> </label>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label class="desc">Code d'interprétation : </label>
                            <label> <?php echo $rapport['code_inter']; ?> </label>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="ensTables">
            <form method="get" action="ajoutPV.php">
                <table>
                    <tr>
                        <th colspan="2"><h4 class="ui dividing header">Ajout d'un PV de contrôle</h4></th>
                    </tr>

                    <tr>
                        <td>
                            <label class="desc" for="appareil"> * Équipement à contrôler : </label>
                            <select class="ui search dropdown listeAjout" name="equipement">
                                <option selected></option>
                                <?php
                                for ($i = 0; $i < sizeof($listeEquipement); $i++) {
                                    echo '<option value="' . $listeEquipement[$i]['idEquipement'] . '">' . $listeEquipement[$i]['Type'] . ' ' . $listeEquipement[$i]['Designation'] . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <label class="desc" for="controleur"> Responsable du contrôle : </label>
                            <select class="ui search dropdown listeAjout" name="controleur">
                                <option selected></option>
                                <?php
                                for ($i = 0; $i < sizeof($listeUtilisateurs); $i++) {
                                    if ($listeUtilisateurs[$i]['nom'] != 'root')
                                        echo '<option value="' . $listeUtilisateurs[$i]['id_utilisateur'] . '">' . $listeUtilisateurs[$i]['nom'] . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label class="desc" for="appareil"> * Contrôle à effectuer : </label>
                            <select class="ui search dropdown listeAjout" name="controle">
                                <option selected></option>
                                <?php
                                for ($i = 0; $i < sizeof($controles); $i++) {
                                    echo '<option value="' . $controles[$i]['id_type'] . '">' . $controles[$i]['libelle'] . ' (' . $controles[$i]['code'] . ')</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <label class="desc" for="appareil"> * Discipline du PV : </label>
                            <select class="ui search dropdown listeAjout" name="discipline">
                                <option selected></option>
                                <?php
                                for ($i = 0; $i < sizeof($disciplines); $i++) {
                                    echo '<option value="' . $disciplines[$i]['id_discipline'] . '">' . $disciplines[$i]['libelle'] . ' (' . $disciplines[$i]['code'] . ')</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label class="desc"> * Date de début prévue : </label>
                            <div class="ui input">
                                <input type="date" name="date_debut" placeholder="(JJ-MM-AAAA)">
                            </div>
                        </td>

                        <td>
                            <label class="desc"> * Date de fin prévue : </label>
                            <div class="ui input">
                                <input type="date" name="date_fin" placeholder="(JJ-MM-AAAA)">
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>

                        </td>
                        <td>
                            <input type="hidden" name="idRapport" value="<?php echo $rapport['id_rapport']; ?>">
                            <button class="ui right floated blue button">Ajouter ce PV au rapport</button>
                        </td>
                    </tr>
                </table>
            </form>

            <div class="boutons">
                <form method="get" action="listeRapportsCA.php">
                    <button class="ui right floated blue button">Retour à la liste des rapports</button>
                </form>
                <form method="get" action="listePVCA.php">
                    <input type="hidden" name="numAffaire" value="<?php echo $affaire['num_affaire']; ?>">
                    <button class="ui right floated blue button">Détails des PV du rapport</button>
                </form>
                <form method="post" action="../excel/conversionRapport.php">
                    <input type="hidden" name="idRapport" value="<?php echo $rapport['id_rapport']; ?>">
                    <button class="ui right floated blue button">Générer le rapport au format Excel</button>
                </form>
            </div>
        </div>
    </div>
    </body>
    </html>

    <script>
        $(function () {
            $("#controleGenere").on("change", function () {
                console.log($("#controleGenere").val())
                if ($("#controleGenere").val().length > 0)
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
        echo '<p id="infosAction">' . $messageSucces . '</p>';
        echo '</div></td>';
    } else if (isset($_GET[$conditionSucces])) {
        echo '<td><div class="ui message">';
        echo '<div class="header"> Erreur </div>';
        echo '<p id="infosAction">' . $messageErreur . '</p>';
        echo '</div></td>';
    }
}

function creerRapport($bdd) {
    if ($_GET['num_affaire'] == "" || $_GET['demandeRecue'] == "" || $_GET['demandeAnalysee'] == "" ||
        $_GET['obtentionOffre'] == "" || $_GET['numAvenant'] == "" ||
        $_GET['procedure'] == "" || $_GET['codeInter'] == ""
    ) {
        header("Location: creationRapport.php?erreur=1");
        exit;
    }

    $affaire = selectAllFromWhere($bdd, "affaire", "num_affaire", "like", $_GET['num_affaire'])->fetch();

    // Id du récepteur de la demande
    $idReceveur = selectAllFromWhere($bdd, "utilisateurs", "nom", "like", $_GET['demandeRecue'])->fetch();
    // Id de l'analyste de la demande
    $idAnalyste = selectAllFromWhere($bdd, "utilisateurs", "nom", "like", $_GET['demandeAnalysee'])->fetch();

    $appelOffre = 1;
    if (!isset($_GET['appelOffre'])) // Si la case n'a pas été cochée
        $appelOffre = 0;

    $valeursTmp = array("null", $affaire['id_affaire'], $idReceveur['id_utilisateur'], $idAnalyste['id_utilisateur'],
        $bdd->quote($_GET['obtentionOffre']), $appelOffre, $bdd->quote($_GET['numAvenant']), $bdd->quote($_GET['procedure']),
        $bdd->quote($_GET['codeInter']), "now()");

    insert($bdd, "rapports", $valeursTmp);
}