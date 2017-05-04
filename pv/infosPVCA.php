<?php

    include_once "../menu.php";
    verifSession("OP");
    enTete("Liste des PV",
        array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
        array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    if (isset($_GET['idPV']) && $_GET['idPV'] != "")
        $_POST['idPV'] = $_GET['idPV'];

    $bdd = connexion('portail_gestion');

    $pv = selectAllFromWhere($bdd, "pv_controle", "id_pv_controle", "=", $_POST['idPV'])->fetch();
    $type_controle = selectAllFromWhere($bdd, "type_controle", "id_type", "=", $pv['id_type_controle'])->fetch();

    $rapport = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $pv['id_rapport'])->fetch();
    $affaire = selectAllFromWhere($bdd, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();
?>

        <div id="contenu">
            <?php $nomPV = explode(" ", $affaire['num_affaire']); ?>
            <h1 class="ui blue center aligned huge header">Modification du PV <?php echo "SCO ".$nomPV[1]."-".$type_controle['code']."-".sprintf("%03d", $pv['num_ordre']); ?></h1>
            <table id="ensTables">
                <tr>
                    <td class="partieTableau">
                        <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionPV.php"' ?>>
                            <?php creerApercuDetails($rapport, $pv['date']); ?>
                            <table>
                                <?php creerApercuDocuments($rapport); ?>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <?php
                                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv_controle'].'">';
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
                                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv_controle'].'">';
                                            echo '<input type="hidden" name="lienRetour" value="infosPVCA">';
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
            <form method="post" action="listePVCA.php">
                <button class="ui right floated blue button">Retour à la liste des PV</button>
            </form>
        </div>
    </body>
</html>

