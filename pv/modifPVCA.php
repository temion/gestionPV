<?php
require_once "../menu.php";
verifSession("OP");
enTete("Modification des PV",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$bdd = connexion('portail_gestion');

$pv = selectPVParId($bdd, $_GET['idPV'])->fetch();

if (isset($_GET['avancement']) && $_GET['avancement'] != "") {
    update($bdd, "pv_controle", "id_avancement", $_GET['avancement'], "id_pv", "=", $_GET['idPV']);
}

if (isset($_GET['commentaires']) && $_GET['ajoutComm'] == 1) {
    update($bdd, "pv_controle", "commentaires", $_GET['commentaires'], "id_pv", "=", $_GET['idPV']);
}

// Reselect afin de mettre à jour les informations
$pv = selectPVParId($bdd, $_GET['idPV'])->fetch();

$type_controle = selectControleParId($bdd, $pv['id_type_controle'])->fetch();
$discipline = selectDisciplineParId($bdd, $pv['id_discipline'])->fetch();
$avancements = selectAll($bdd, "avancement")->fetchAll();

$rapport = selectRapportParId($bdd, $pv['id_rapport'])->fetch();
$affaire = selectAffaireParId($bdd, $rapport['id_affaire'])->fetch();
$societe = selectSocieteParId($bdd, $affaire['id_societe'])->fetch();
$odp = selectODPParId($bdd, $affaire['id_odp'])->fetch();
$client = selectClientParId($bdd, $odp['id_client'])->fetch();
$controleur = selectUtilisateurParId($bdd, $pv['id_controleur'])->fetch();

$bddInspection = connexion('inspections');

$reservoir = selectReservoirParId($bddInspection, $pv['id_reservoir'])->fetch();

$titre = "SCO" . explode(" ", $affaire['num_affaire'])[1] . '-' . $discipline['code'] . '-' . $type_controle['code'] . '-' . sprintf("%03d", $pv['num_ordre']);
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
                                if ($pv['chemin_excel'] != null) {
                                    $chemin = str_replace("'", "", $pv['chemin_excel']);
                                    if (file_exists($chemin))
                                        echo '<button class="ui left floated blue button">Télécharger le fichier Excel</button>';
                                } else
                                    echo '<button disabled style="pointer-events: auto;" title="Aucun fichier n\'a encore été uploadé" class="ui left floated blue button">Télécharger le fichier Excel</button>';
                                ?>
                            </td>
                        </tr>
                    </table>
                </form>
            </td>

            <td class="partieTableau">
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
                                                echo '<option selected value="'.$avancements[$i]['id_avancement'].'">'.$avancements[$i]['stade'].'</option>';
                                            else
                                                echo '<option value="'.$avancements[$i]['id_avancement'].'">'.$avancements[$i]['stade'].'</option>';
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
                    <tr>
                        <th colspan="2"><h4 class="ui dividing header">Uploader les fichiers</h4></th>
                    </tr>

                    <form enctype="multipart/form-data" action="uploadPV.php" method="post">
                        <tr>
                            <td>
                                <input type="hidden" name="taille_max" value="30000"/>
                                <input name="pv_excel" type="file"/>
                            </td>
                            <td>
                                <?php
                                echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                                echo '<input type="hidden" name="nomFichier" value="pv_excel">';
                                echo '<input type="hidden" name="lienRetour" value="modifPVCA">';
                                ?>
                                <button class="ui right floated blue button">Uploader au format Excel</button>
                            </td>
                        </tr>
                    </form>

                    <form enctype="multipart/form-data" action="uploadPV.php" method="post">
                        <tr>
                            <td>
                                <input type="hidden" name="taille_max" value="30000"/>
                                <input name="pv_pdf" type="file"/>
                            </td>
                            <td>
                                <?php
                                echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                                echo '<input type="hidden" name="nomFichier" value="pv_pdf">';
                                echo '<input type="hidden" name="lienRetour" value="modifPVCA">';
                                ?>
                                <button class="ui right floated blue button">Uploader au format PDF</button>
                            </td>
                        </tr>
                    </form>

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
                                <button class="ui right floated blue button" name="ajoutComm" value="1">Ajouter ces commentaires</button>
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
</div>

<div class="ui small modal" id="modalDetails">
    wow
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
    });
</script>

