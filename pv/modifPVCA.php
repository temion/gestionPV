<?php

    include_once "../menu.php";
    verifSession("OP");
    enTete("Modification des PV",
        array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
        array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bdd = connexion('portail_gestion');

    $pv = selectAllFromWhere($bdd, "pv_controle", "id_pv", "=", $_GET['idPV'])->fetch();
    $type_controle = selectAllFromWhere($bdd, "type_controle", "id_type", "=", $pv['id_type_controle'])->fetch();

    $rapport = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $pv['id_rapport'])->fetch();
    $affaire = selectAllFromWhere($bdd, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();
    $societe = selectAllFromWhere($bdd, "societe", "id_societe", "=", $affaire['id_societe'])->fetch();
    $odp = selectAllFromWhere($bdd, "odp", "id_odp", "=", $affaire['id_odp'])->fetch();
    $client = selectAllFromWhere($bdd, "client", "id_client", "=", $odp['id_client'])->fetch();
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
                                </tr>

                                <tr>
                                    <td>
                                        <div class="field">
                                            <label>Personne rencontrée : </label>
                                            <label> <?php echo $client['nom']; ?> </label>
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
                                </tr>

                                <tr>
                                    <td>
                                        <div class="field">
                                            <label>Lieu : </label>
                                            <label> <?php echo  $affaire['lieu_intervention']; ?> </label>
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
                                </tr>
                            </table>
                            <table>
                                <?php creerApercuDocuments($rapport); ?>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <?php
                                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                                            echo '<button id="boutonGenere" class="ui left floated blue button">Télécharger le fichier Excel</button>';
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>

                    <td class="partieTableau">
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
                                            echo '<input type="hidden" name="lienRetour" value="modifPVCA">';
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
            <form method="get" action="listePVCA.php">
                <button class="ui right floated blue button">Retour à la liste des PV</button>
            </form>
        </div>
    </body>
</html>

