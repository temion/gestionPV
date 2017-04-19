<?php
    include_once "../menu.php";
    enTete("Modification de PV",
                 array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
                 array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $pv = $bddAffaire->query('select * from pv_controle where id_pv = '.$_POST['idPV'])->fetch();
    $affaire = $bddAffaire->query('select * from affaire where id_affaire = '.$pv['id_affaire'])->fetch();
    $societe = $bddAffaire->query('select * from societe where id_societe = '.$affaire['id_societe'])->fetch();
    $client = $bddAffaire->query('select * from client where id_client = '.$societe['ref_client'])->fetch();

    if (isset($_POST['ajout']) && $_POST['ajout'] == 1) {
        $appareil = $bddAffaire->query('select * from appareils where concat(systeme, \' \', type, \' (\', num_serie, \')\') like '.$bddAffaire->quote($_POST['appareil']))->fetch();

        $bddAffaire->exec('insert into appareils_utilises values (null, '.$appareil['id_appareil'].', '.$_POST['idPV'].')');
    }

    $appareilsUtilises = $bddAffaire->query('select * from appareils where id_appareil in (select id_appareil from appareils_utilises where id_pv = '.$_POST['idPV'].')')->fetchAll();
    $appareils = $bddAffaire->query('select * from appareils where id_appareil not in (select id_appareil from appareils_utilises where id_pv = '.$_POST['idPV'].')')->fetchAll();

    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');
    $equipement = $bddEquipement->query('select * from equipement where idEquipement = '.$pv['id_equipement'])->fetch();
    $ficheTechniqueEquipement = $bddEquipement->query('select * from fichetechniqueequipement where idEquipement = '.$pv['id_equipement'])->fetch();
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">Modification du PV </h1>
            <table id="ensTables">
                <tr>
                    <td class="partieTableau">
                        <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionExcel.php?idPV='.$pv['id_pv'].'"' ?>>
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
                                            <label> <?php echo $ficheTechniqueEquipement['diametre'].' m'; ?> </label>
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
                                            <label> <?php echo $ficheTechniqueEquipement['hauteurEquipement'].' m'; ?> </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="field">
                                            <label>Lieu : </label>
                                            <label> <?php echo $affaire['lieu_intervention']; ?> </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="field">
                                            <label>Hauteur produit : </label>
                                            <div class="field">
                                                <input type="text" name="hauteur_produit" placeholder="Hauteur du produit (m)">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="field">
                                            <div class="field">
                                                <label>Début du contrôle : </label>
                                                <label> <?php echo $affaire['date_ouv']; ?> </label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="field">
                                            <label>Volume : </label>
                                            <div class="field">
                                                <input type="text" name="volume_equipement" placeholder="Volume (m²)">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="field">
                                            <label>Nombre de génératrices : </label>
                                            <label> <?php echo $ficheTechniqueEquipement['nbGeneratrice']; ?> </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="field">
                                            <label>Distance entre 2 points : </label>
                                            <div class="field">
                                                <input type="text" name="distance_points" placeholder="Distance (m)">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <table>
                                <tr>
                                    <th colspan="2"><h4 class="ui dividing header">Document de référence</h4></th>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="field">
                                            <label>Suivant procédure : </label>
                                            <label> <?php echo $pv['procedure_controle']; ?> </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="field">
                                            <label>Code d'interprétation : </label>
                                            <label> <?php echo $pv['code_inter']; ?> </label>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <button class="ui right floated blue button">Générer en PDF</button>
                        </form>
                    </td>
                    <td class="partieTableau">
                        <form method="post" action="modifPV.php">
                            <table>
                                <tr>
                                    <th colspan="2"><h4 class="ui dividing header">Appareils utilisés</h4></th>
                                </tr>
                                <?php
                                    if (isset($_POST['ajout']) && $_POST['ajout'] == 1) {
                                        echo '<tr><td><div class="ui message">';
                                        echo '<div class="header"> Succès ! </div>';
                                        echo '<p id="infosAction"> L\'appareil a bien été ajouté ! </p>';
                                        echo '</div></td></tr>';
                                    }
                                ?>

                                <tr>
                                    <td>
                                        <label> Appareil à ajouter : </label>
                                        <?php
                                            echo '<input type="hidden" name="idPV" value="'.$_POST['idPV'].'">';
                                            echo '<input type="hidden" name="ajout" value="1">'; // Modifier message si rien sélectionné
                                        ?>
                                        <select class="ui search dropdown" name="appareil">
                                            <option selected> </option>
                                            <?php
                                                for ($i = 0; $i < sizeof($appareils); $i++) {
                                                    echo '<option>'.$appareils[$i]['systeme'].' '.$appareils[$i]['type'].' ('.$appareils[$i]['num_serie'].')</option>';
                                                }
                                            ?>
                                        </select>
                                    </td>
                                    <td colspan="2"><button class="ui right floated blue button">Ajouter cet appareil</button></td>
                                </tr>
                                <tr>
                                    <td>
                                        <label> Appareils déjà ajoutés : </label>
                                        <select id="listeAppareilsUtilises" disabled size=4 class="ui search dropdown">
                                            <?php
                                            for ($i = 0; $i < sizeof($appareilsUtilises); $i++) {
                                                echo '<option>'.$appareilsUtilises[$i]['systeme'].' '.$appareilsUtilises[$i]['type'].' ('.$appareilsUtilises[$i]['num_serie'].')</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>