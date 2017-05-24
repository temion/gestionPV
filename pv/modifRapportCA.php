<?php
require_once "../menu.php";

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if (!isset($_GET['idRapport']) ||$_GET['idRapport'] == "") {
    header('Location: /gestionPV/index.php');
    exit;
}

enTete("Modification de rapport",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/modifRapport.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));


if (isset($_GET['ajoutRapport']) && $_GET['ajoutRapport'] == 1) {
    creerRapport($bddPortailGestion, $bddPlanning);
    $_GET['idRapport'] = selectDernierRapport($bddPortailGestion)->fetch()['id_rapport'];
    $rapport = selectRapportParId($bddPortailGestion, $_GET['idRapport'])->fetch();
    $affaire = selectAffaireParId($bddPortailGestion, $rapport['id_affaire'])->fetch();
    ajouterHistorique($bddPortailGestion, "Création du rapport de l'affaire " . $affaire['num_affaire'], "pv/modifRapportCA.php?idRapport=", $rapport['id_rapport']);
}

$rapport = selectRapportParId($bddPortailGestion, $_GET['idRapport'])->fetch();
$affaire = selectAffaireParId($bddPortailGestion, $rapport['id_affaire'])->fetch();
$societe = selectSocieteParId($bddPortailGestion, $affaire['id_societe'])->fetch();
$odp = selectODPParId($bddPortailGestion, $affaire['id_odp'])->fetch();
$client = selectClientParId($bddPortailGestion, $odp['id_client'])->fetch();

$receveur = selectUtilisateurParId($bddPlanning, $rapport['id_receveur'])->fetch();
$analyste = selectUtilisateurParId($bddPlanning, $rapport['id_analyste'])->fetch();

$controles = selectAll($bddPortailGestion, "type_controle")->fetchAll();

$disciplines = selectAll($bddPortailGestion, "type_discipline")->fetchAll();

$listeReservoirs = selectAll($bddInspections, "reservoirs_tmp", "id_societe")->fetchAll();

$listeUtilisateurs = selectAll($bddPlanning, "utilisateurs")->fetchAll();

$prepareSociete = $bddPortailGestion->prepare('select * from societe where id_societe = ?');
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
                            <select class="ui search dropdown listeAjout" name="reservoir">
                                <option selected></option>
                                <?php
                                for ($i = 0; $i < sizeof($listeReservoirs); $i++) {
                                    $prepareSociete->execute(array($listeReservoirs[$i]['id_societe']));
                                    $nomSociete = trim($prepareSociete->fetch()['nom_societe']);
                                    echo '<option value="' . $listeReservoirs[$i]['id_reservoir'] . '">' . $nomSociete . ' : ' . $listeReservoirs[$i]['designation'] . ' ' . $listeReservoirs[$i]['type'] .'</option>';
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

            <br/>

            <div class="boutons">
                <form method="post" action="../excel/ensembleRapport.php">
                    <input type="hidden" name="idRapport" value="<?php echo $rapport['id_rapport']; ?>">
                    <button class="ui right floated green button">Générer tous les fichiers du rapport au format Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="ui large modal" id="modalAide">
        <div class="header">Aide</div>
        <div>
            <p>
                Cette page vous indique les différentes informations sur le rapport sélectionné. Vous pouvez également y
                ajouter un nouveau PV, accéder aux PV déjà existants à l'aide du bouton "Détails des PV du rapport", ou
                encore obtenir un fichier Excel regroupant les informations du rapport. Enfin, en cliquant sur
                "Générer tous les fichiers du rapport au format Excel", vous générerez sur le serveur les fichiers Excel
                correspondant à tous les PV du rapport ainsi que le fichier Excel du rapport, et obtiendrez une archive
                disponible en téléchargement, contenant l'ensemble de ces fichiers.
            </p>

            <p>
                <strong>Attention :</strong> Cette dernière action peut prendre un certain temps si les fichiers Excel
                du rapport ne sont pas déjà présents sur le serveur.
            </p>
            <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
            </button>
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

/**
 * Si les paramètres sont corrects, crée un nouveau rapport avec ces paramètres dans la base.
 *
 * @param PDO $bdd Base de données.
 * @param PDO $bddPlanning Base de données comportant les informations utilisateurs.
 */
function creerRapport($bdd, $bddPlanning) {
    if ($_GET['num_affaire'] == "" || $_GET['demandeRecue'] == "" || $_GET['demandeAnalysee'] == "" ||
        $_GET['obtentionOffre'] == "" || $_GET['numAvenant'] == "" ||
        $_GET['procedure'] == "" || $_GET['codeInter'] == ""
    ) {
        header("Location: creationRapport.php?erreur=1");
        exit;
    }

    $affaire = selectAffaireParNom($bdd, $_GET['num_affaire'])->fetch();

    // Id du récepteur de la demande
    $idReceveur = selectUtilisateurParNom($bddPlanning, $_GET['demandeRecue'])->fetch();
    // Id de l'analyste de la demande
    $idAnalyste = selectUtilisateurParNom($bddPlanning, $_GET['demandeAnalysee'])->fetch();

    $appelOffre = 1;
    if (!isset($_GET['appelOffre'])) // Si la case n'a pas été cochée
        $appelOffre = 0;

    $valeursTmp = array("null", $affaire['id_affaire'], $idReceveur['id_utilisateur'], $idAnalyste['id_utilisateur'],
        $bdd->quote($_GET['obtentionOffre']), $appelOffre, $bdd->quote($_GET['numAvenant']), $bdd->quote($_GET['procedure']),
        $bdd->quote($_GET['codeInter']), "now()");

    insert($bdd, "rapports", $valeursTmp);
}