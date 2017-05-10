<?php

    include_once "../menu.php";
    verifSession("OP");
    enTete("Modification des PV",
        array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
        array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bdd = connexion('portail_gestion');

    $pv = selectAllFromWhere($bdd, "pv_controle", "id_pv", "=", $_GET['idPV'])->fetch();
    $type_controle = selectAllFromWhere($bdd, "type_controle", "id_type", "=", $pv['id_type_controle'])->fetch();
    $discipline = selectAllFromWhere($bdd, "type_discipline", "id_discipline", "=", $pv['id_discipline'])->fetch();

    $rapport = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $pv['id_rapport'])->fetch();
    $affaire = selectAllFromWhere($bdd, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();
    $societe = selectAllFromWhere($bdd, "societe", "id_societe", "=", $affaire['id_societe'])->fetch();
    $odp = selectAllFromWhere($bdd, "odp", "id_odp", "=", $affaire['id_odp'])->fetch();
    $client = selectAllFromWhere($bdd, "client", "id_client", "=", $odp['id_client'])->fetch();

    $bddEquipement = connexion('theodolite');

    $equipement = selectAllFromWhere($bddEquipement, "equipement", "idEquipement", "=", $pv['id_equipement'])->fetch();
    $ficheTechniqueEquipement = selectAllFromWhere($bddEquipement, "fichetechniqueequipement", "idEquipement", "=", $pv['id_equipement'])->fetch();

    $titre = "SCO".explode(" ",$affaire['num_affaire'])[1].'-'.$type_controle['code'].'-'.$discipline['code'].'-'.sprintf("%03d", $pv['num_ordre']);
?>

        <div id="contenu">
            <?php $nomPV = explode(" ", $affaire['num_affaire']); ?>
            <h1 class="ui blue center aligned huge header">Modification du PV <?php echo $titre; ?></h1>
            <table id="ensTables">
                <tr>
                    <td class="partieTableau">
                        <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionPV.php"' ?>>
                            <?php creerApercuModif($affaire, $societe, $equipement, $client, $ficheTechniqueEquipement, $pv); ?>
                            <table>
                                <?php creerApercuDocuments($rapport); ?>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <?php
                                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                                            if ($pv['chemin_pdf'] != null) {
                                                $chemin = str_replace("'", "", $pv['chemin_pdf']);
                                                if (file_exists($chemin))
                                                    echo '<button id="boutonGenere" name="pdf" value="1" class="ui left floated blue button">Télécharger le fichier PDF</button>';
                                            } else
                                                echo '<button disabled style="pointer-events: auto;" title="Aucun fichier n\'a encore été uploadé" id="boutonGenere" class="ui left floated blue button">Télécharger le fichier PDF</button>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                                            if ($pv['chemin_excel'] != null) {
                                                $chemin = str_replace("'", "", $pv['chemin_excel']);
                                                if (file_exists($chemin))
                                                    echo '<button id="boutonGenere" class="ui left floated blue button">Télécharger le fichier Excel</button>';
                                            } else
                                                echo '<button disabled style="pointer-events: auto;" id="boutonGenere" title="Aucun fichier n\'a encore été uploadé" class="ui left floated blue button">Télécharger le fichier Excel</button>';
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>

                    <td class="partieTableau">
                        <table>
                            <tr>
                                <th colspan="2"><h4 class="ui dividing header">Uploader les fichiers</h4></th>
                            </tr>

                            <form enctype="multipart/form-data" action="uploadPV.php" method="post">
                                <tr>
                                    <td>
                                        <input type="hidden" name="taille_max" value="30000" />
                                        <input name="pv_excel" type="file" />
                                    </td>
                                    <td>
                                        <?php
                                        echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
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
                                        <input type="hidden" name="taille_max" value="30000" />
                                        <input name="pv_pdf" type="file" />
                                    </td>
                                    <td>
                                        <?php
                                        echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                                        echo '<input type="hidden" name="nomFichier" value="pv_pdf">';
                                        echo '<input type="hidden" name="lienRetour" value="modifPVCA">';
                                        ?>
                                        <button class="ui right floated blue button">Uploader au format PDF</button>
                                    </td>
                                </tr>
                            </form>
                            <tr>
                                <?php
                                    afficherMessage('erreurUpload', "Erreur", "Erreur dans l'upload du fichier", "Succès !", "Le fichier a bien été uploadé !");
                                ?>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <form method="get" action="listePVCA.php">
                <?php echo '<input type="hidden" name="nomPV" value="'.$titre.'">'; ?>
                <button class="ui right floated blue button">Retour à la liste des PV</button>
            </form>
        </div>
    </body>
</html>

