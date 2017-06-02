<?php
require_once "../menu.php";
require_once '../historique/gestionHistoriqueUtil.inc.php';

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if (!isset($_GET['idPV']) || $_GET['idPV'] == "") {
    header('Location: /gestionPV/index.php');
    exit;
}

enTete("Modification de PV",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$pv = selectPVParId($bddPortailGestion, $_GET['idPV'])->fetch();

majDates("date_fin", "date_debut", $bddPortailGestion);
majDates("date_debut", "date_fin", $bddPortailGestion);
majDates("date_fin", "date_debut", $bddPortailGestion);

if (isset($_GET['controleur']) && $_GET['controleur'] != "") {
    update($bddPortailGestion, "pv_controle", "id_controleur", $_GET['controleur'], "id_pv", "=", $_GET['idPV']);
    $modifs = 1;
}

if (isset($_GET['avancement']) && $_GET['avancement'] != "") {
    update($bddPortailGestion, "pv_controle", "id_avancement", $_GET['avancement'], "id_pv", "=", $_GET['idPV']);
    $modifs = 1;
}

if (isset($_GET['commentaires']) && $_GET['ajoutComm'] == 1) {
    update($bddPortailGestion, "pv_controle", "commentaires", $_GET['commentaires'], "id_pv", "=", $_GET['idPV']);
}

// Reselect afin de mettre à jour les informations
$pv = selectPVParId($bddPortailGestion, $_GET['idPV'])->fetch();

$type_controle = selectControleParId($bddPortailGestion, $pv['id_type_controle'])->fetch();
$discipline = selectDisciplineParId($bddPortailGestion, $pv['id_discipline'])->fetch();
$avancements = selectAll($bddPortailGestion, "avancement")->fetchAll();

$rapport = selectRapportParId($bddPortailGestion, $pv['id_rapport'])->fetch();
$affaire = selectAffaireParId($bddPortailGestion, $rapport['id_affaire'])->fetch();
$societe = selectSocieteParId($bddPortailGestion, $affaire['id_societe'])->fetch();
$odp = selectODPParId($bddPortailGestion, $affaire['id_odp'])->fetch();
$client = selectClientParId($bddPortailGestion, $odp['id_client'])->fetch();
$controleur = selectUtilisateurParId($bddPlanning, $pv['id_controleur'])->fetch();
$listeUtilisateurs = selectAll($bddPlanning, "utilisateurs")->fetchAll();

$bddInspection = connexion('inspections');

$reservoir = selectReservoirParId($bddInspection, $pv['id_reservoir'])->fetch();

$titre = "SCO" . explode(" ", $affaire['num_affaire'])[1] . '-' . $discipline['code'] . '-' . $type_controle['code'] . '-' . sprintf("%03d", $pv['num_ordre']);

if (isset($modifs) && $modifs == 1)
    ajouterHistorique($bddPortailGestion, "Modification chargé d'affaires du PV " . $titre, "pv/modifPVCA.php?idPV=", $pv['id_pv']);
?>

<div id="contenu">
    <?php $nomPV = explode(" ", $affaire['num_affaire']); ?>
    <h1 class="ui blue center aligned huge header">Modification du PV <?php echo $titre; ?></h1>
    <table id="ensTables">
        <tr>
            <td class="partieTableau">
                <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionPV.php"' ?>>
                    <?php creerApercuModif($affaire, $societe, $reservoir, $client, $controleur, $pv); ?>
                    <table>
                        <?php creerApercuDocuments($rapport); ?>
                        <tr>
                        <tr>
                            <th colspan="3"><h4 class="ui dividing header">Télécharger les fichiers</h4></th>
                        </tr>
                        <td>
                            <?php
                            echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                            echo '<button name="pdf" value="1" title="Génère automatiquement un PV au format PDF" class="ui left floated blue button">Télécharger le fichier PDF</button>';
                            ?>
                        </td>
                        <td>

                        </td>
                        <td>
                            <?php
                            echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                            echo '<button class="ui left floated blue button">Télécharger le fichier Excel</button>';
                            ?>
                        </td>
                        </tr>
                    </table>
                </form>
            </td>

            <td class="partieTableau">




                <!-- ToDo -->
                <table>
                    <tr>
                        <th colspan="3"><h4 class="ui dividing header">Modifier les détails du PV</h4></th>
                    </tr>

                    <form method="get" action="modifPVCA.php">
                        <tr>
                            <td>
                                <label class="desc">Date de début prévue : </label>
                                <div class="ui input">
                                    <input type="date" name="date_debut" placeholder="<?php echo ($pv['date_debut'] == "" ? "JJ-MM-AAAA" : conversionDate($pv['date_debut'])); ?>">
                                </div>
                            </td>
                            <td>
                                <label class="desc">Date de fin prévue : </label>
                                <div class="ui input">
                                    <input type="date" name="date_fin" placeholder="<?php echo ($pv['date_fin'] == "" ? "JJ-MM-AAAA" : conversionDate($pv['date_fin'])); ?>">
                                </div>
                            </td>
                            <td>
                                <label class="desc" for="controleur"> Responsable du contrôle : </label>
                                <select style="width: 20vh;" class="ui search dropdown" name="controleur">
                                    <option selected></option>
                                    <?php
                                    for ($i = 0; $i < sizeof($listeUtilisateurs); $i++) {
                                        if ($listeUtilisateurs[$i]['id_utilisateur'] == $pv['id_controleur'])
                                            echo '<option selected value="' . $listeUtilisateurs[$i]['id_utilisateur'] . '">' . $listeUtilisateurs[$i]['nom'] . '</option>';
                                        else if ($listeUtilisateurs[$i]['nom'] != 'root')
                                            echo '<option value="' . $listeUtilisateurs[$i]['id_utilisateur'] . '">' . $listeUtilisateurs[$i]['nom'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php
                                if (isset($erreurs) && $erreurs == 1) {
                                    echo '<div class="ui message">';
                                    echo '<div class="header">Erreur</div>';
                                    echo '<p id="infosAction">Erreur dans les dates rentrées</p>';
                                    echo '</div>';
                                }
                                ?>
                            </td>
                            <td>
                                <input type="hidden" name="idPV" value="<?php echo $pv['id_pv']; ?>">
                                <button class="ui blue button">Valider les modifications</button>
                            </td>
                        </tr>
                    </form>
                </table>
                <!-- ToDo -->



                <table>
                    <tr>
                        <th colspan="2"><h4 class="ui dividing header">Modifier l'avancement du PV</h4></th>
                    </tr>

                    <form action="modifPVCA.php" method="get">
                        <tr>
                            <td>
                                <select class="ui search dropdown listeAjout" name="avancement">
                                    <?php
                                    for ($i = 0; $i < sizeof($avancements); $i++) {
                                        if ($avancements[$i]['id_avancement'] == $pv['id_avancement'])
                                            echo '<option selected value="' . $avancements[$i]['id_avancement'] . '">' . $avancements[$i]['stade'] . '</option>';
                                        else
                                            echo '<option value="' . $avancements[$i]['id_avancement'] . '">' . $avancements[$i]['stade'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="idPV" value="<?php echo $pv['id_pv']; ?>">
                                <button class="ui right floated blue button">Valider</button>
                            </td>
                        </tr>
                    </form>
                </table>

                <table>
                    <!--                    <tr>-->
                    <!--                        <th colspan="2"><h4 class="ui dividing header">Uploader les fichiers</h4></th>-->
                    <!--                    </tr>-->
                    <!---->
                    <!--                    <form enctype="multipart/form-data" action="uploadPV.php" method="post">-->
                    <!--                        <tr>-->
                    <!--                            <td>-->
                    <!--                                <input type="hidden" name="taille_max" value="30000"/>-->
                    <!--                                <input name="pv_excel" type="file"/>-->
                    <!--                            </td>-->
                    <!--                            <td>-->
                    <!--                                --><?php
                    //                                echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                    //                                echo '<input type="hidden" name="nomFichier" value="pv_excel">';
                    //                                echo '<input type="hidden" name="lienRetour" value="modifPVCA">';
                    //                                ?>
                    <!--                                <button class="ui right floated blue button">Uploader au format Excel</button>-->
                    <!--                            </td>-->
                    <!--                        </tr>-->
                    <!--                    </form>-->
                    <!---->
                    <!--                    <form enctype="multipart/form-data" action="uploadPV.php" method="post">-->
                    <!--                        <tr>-->
                    <!--                            <td>-->
                    <!--                                <input type="hidden" name="taille_max" value="30000"/>-->
                    <!--                                <input name="pv_pdf" type="file"/>-->
                    <!--                            </td>-->
                    <!--                            <td>-->
                    <!--                                --><?php
                    //                                echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                    //                                echo '<input type="hidden" name="nomFichier" value="pv_pdf">';
                    //                                echo '<input type="hidden" name="lienRetour" value="modifPVCA">';
                    //                                ?>
                    <!--                                <button class="ui right floated blue button">Uploader au format PDF</button>-->
                    <!--                            </td>-->
                    <!--                        </tr>-->
                    <!--                    </form>-->

                    <tr>
                        <th colspan="2"><h4 class="ui dividing header">Ajouter un commentaire</h4></th>
                    </tr>
                    <form method="get" action="modifPVCA.php">
                        <tr>
                            <td colspan="2">
                                <div class="ui form">
                                    <div class="field">
                                        <label>Commentaires</label>
                                        <textarea name="commentaires"><?php echo $pv['commentaires']; ?></textarea>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <?php
                                echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                                ?>
                                <button class="ui right floated blue button" name="ajoutComm" value="1">Ajouter ces
                                    commentaires
                                </button>
                            </td>
                        </tr>
                    </form>
                    <tr>
                        <td></td>
                        <?php
                        afficherMessage('erreurUpload', "Erreur", "Erreur dans l'upload du fichier", "Succès !", "Le fichier a bien été uploadé !");
                        ?>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <form method="get" action="listePVCA.php">
        <?php echo '<input type="hidden" name="nomPV" value="' . $titre . '">'; ?>
        <button class="ui right floated blue button">Retour à la liste des PV</button>
    </form>
    <button id="boutonSuppr" class="ui right floated red button">Supprimer le PV</button>
</div>

<div class="ui large modal" id="modalAide">
    <div class="header">Aide</div>
    <div>
        <p>
            Cette page vous indique les différentes informations sur le PV sélectionné. Vous pouvez également y modifier
            l'avancement,
            laisser un commentaire à propos du PV qui sera disponible pour tous les chargés d'affaire, ou encore
            télécharger les fichiers
            Excel et PDF correspondants. De plus, vous pouvez réuploader des fichiers Excel/PDF si besoin, qui
            remplaceront ceux déjà
            présents sur le serveur et seront donc accessibles à tous.

        <p>
            <strong> Attention : </strong> réuploader des fichiers Excel ne modifie pas les informations stockées dans
            la base.
        </p>
        </p>
        <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
        </button>
    </div>
</div>

<div class="ui small modal" id="modalConf">
    <div class="header">Attention</div>
    <div>
        <p>
            Une fois supprimé, vous ne pourrez plus travailler sur ce PV. Etes-vous sûrs de vouloir le supprimer ?
        </p>
        <form method="get" action="listePVCA.php">
            <?php echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">'; ?>
            <?php echo '<input type="hidden" name="nomPV" value="' . $titre . '">'; ?>
            <button class="ui right floated red button"> Supprimer le PV </button>
        </form>
    </div>
</div>

</body>
</html>

<script>
    $(function () {
        $("#detailsFichier").on("click", function () {
            $('.small.modal')
                .modal('show')
            ;
        });

        $("#boutonSuppr").on("click", function () {
            $('.small.modal')
                .modal('show')
            ;
        });
    });
</script>

<?php
function majDates($dateA, $dateB, $bddPortailGestion) {
    global $pv, $modifs, $erreurs;

    if (isset($_GET[$dateA]) && $_GET[$dateA] != "") {
        if (verifFormatDates($_GET[$dateA])) {
            if (isset($pv[$dateB]) && $pv[$dateB] != "") {

                if ($dateA == "date_debut")
                    $condition = date_create($_GET['date_debut']) <= date_create($pv['date_fin']);
                else
                    $condition = date_create($_GET['date_fin']) >= date_create($pv['date_debut']);

                if ($condition) {
                    update($bddPortailGestion, "pv_controle", $dateA, conversionDate($_GET[$dateA]), "id_pv", "=", $_GET['idPV']);
                    $modifs = 1;
                } else {
                    $erreurs = 1;
                }
            } else {
                update($bddPortailGestion, "pv_controle", $dateA, conversionDate($_GET[$dateA]), "id_pv", "=", $_GET['idPV']);
                $modifs = 1;
            }
        } else {
            $erreurs = 1;
        }
    }

    $pv = selectPVParId($bddPortailGestion, $_GET['idPV'])->fetch();
}