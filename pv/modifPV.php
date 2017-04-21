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

    if (isset($_POST['appareil']) && $_POST['appareil'] != "") {
        if (isset($_POST['controleAssocie']) && $_POST['controleAssocie'] != "") {
            $controleAssocie = $bddAffaire->query('select * from type_controle where concat(libelle, \' (\', code, \')\') like '.$bddAffaire->quote($_POST['controleAssocie']))->fetch();
            $appareil = $bddAffaire->query('select * from appareils where concat(systeme, \' \', type, \' (\', num_serie, \')\') like '.$bddAffaire->quote($_POST['appareil']))->fetch();
            $bddAffaire->exec('insert into appareils_utilises values (null, '.$appareil['id_appareil'].', '.$controleAssocie['id_type'].', '.$_POST['idPV'].')');
        } else {
            $_POST['appareil'] = "";
        }
    }

    if (isset($_POST['controle']) && $_POST['controle'] != "") {
        $controle = $bddAffaire->query('select * from type_controle where concat(libelle, \' (\', code, \')\') like '.$bddAffaire->quote($_POST['controle']))->fetch();
        $bddAffaire->exec('insert into controles_sur_pv values (null, '.$controle['id_type'].', '.$_POST['idPV'].', '.($controle['num_controle'] + 1).')');
        $bddAffaire->exec('update type_controle set num_controle = num_controle + 1 where id_type = '.$controle['id_type']);
    }

    $controlesUtilises = $bddAffaire->query('select * from type_controle where id_type in (select id_type_controle from controles_sur_pv where id_pv = '.$_POST['idPV'].')')->fetchAll();
    $controles = $bddAffaire->query('select * from type_controle where id_type not in (select id_type_controle from controles_sur_pv where id_pv = '.$_POST['idPV'].')')->fetchAll();

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
                        <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionExcel.php"' ?>>
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
                                            <label> ? </label>
<!--                                            <div class="field">-->
<!--                                                <input type="text" name="hauteur_produit" placeholder="Hauteur du produit (m)">-->
<!--                                            </div>-->
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
                                            <label> ? </label>
<!--                                            <div class="field">-->
<!--                                                <input type="text" name="volume_equipement" placeholder="Volume (m²)">-->
<!--                                            </div>-->
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
                                            <label> ? </label>
<!--                                            <div class="field">-->
<!--                                                <input type="text" name="distance_points" placeholder="Distance (m)">-->
<!--                                            </div>-->
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
                                <tr>
                                    <tr>
                                        <th colspan="2"><h4 class="ui dividing header">Générer les PV</h4></th>
                                    </tr>
                                    <td>
                                        <label> Choix du PV de contrôle à générer : </label>
                                        <select class="ui search dropdown" id="controleGenere" name="controleGenere">
                                            <option selected></option>
                                            <?php
                                            for ($i = 0; $i < sizeof($controlesUtilises); $i++) {
                                                echo '<option>'.$controlesUtilises[$i]['libelle'].' ('.$controlesUtilises[$i]['code'].')</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php
                                            echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                                        ?>
                                        <button disabled id="boutonGenere" class="ui right floated blue button">Générer sous format Excel</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                    <td class="partieTableau">
                        <form method="post" action="modifPV.php">
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
                                        <select disabled size=4 class="ui search dropdown listeUtilises">
                                            <?php
                                            for ($i = 0; $i < sizeof($controlesUtilises); $i++) {
                                                echo '<option>'.$controlesUtilises[$i]['libelle'].' ('.$controlesUtilises[$i]['code'].')</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>

                                    <?php
                                        afficherMessageAjout('controle', "Le contrôle a bien été ajouté !", "Aucun contrôle n'a été indiqué.");
                                    ?>
                                    <td colspan="2"><button class="ui right floated blue button">Ajouter ce contrôle</button></td>
                                </tr>
                            </table>
                            <?php
                                echo '<input type="hidden" name="idPV" value="'.$_POST['idPV'].'">';
                            ?>
                        </form>
                        <form method="post" action="modifPV.php">
                            <table>
                                <tr>
                                    <th colspan="2"><h4 class="ui dividing header">Appareils utilisés</h4></th>
                                </tr>

                                <tr>
                                    <td>
                                        <label> Appareil à ajouter : </label>
                                        <select class="ui search dropdown listeAjout" name="appareil">
                                            <option selected> </option>
                                            <?php
                                                for ($i = 0; $i < sizeof($appareils); $i++) {
                                                    echo '<option>'.$appareils[$i]['systeme'].' '.$appareils[$i]['type'].' ('.$appareils[$i]['num_serie'].')</option>';
                                                }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <label> Employé pour le contrôle : </label>
                                        <select class="ui search dropdown" name="controleAssocie">
                                            <option selected> </option>
                                            <?php
                                            for ($i = 0; $i < sizeof($controlesUtilises); $i++) {
                                                echo '<option>'.$controlesUtilises[$i]['libelle'].' ('.$controlesUtilises[$i]['code'].')</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label> Appareils déjà ajoutés : </label>
                                        <select disabled size=4 class="ui search dropdown listeUtilises">
                                            <?php
                                            for ($i = 0; $i < sizeof($appareilsUtilises); $i++) {
                                                echo '<option>'.$appareilsUtilises[$i]['systeme'].' '.$appareilsUtilises[$i]['type'].' ('.$appareilsUtilises[$i]['num_serie'].')</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><button class="ui right floated blue button">Ajouter cet appareil</button></td>
                                </tr>
                                <tr>
                                    <?php
                                        afficherMessageAjout('appareil', "L'appareil a bien été ajouté !", "Aucun appareil ou contrôle associé n'a été indiqué.");
                                    ?>
                                </tr>
                            </table>
                            <?php
                                echo '<input type="hidden" name="idPV" value="'.$_POST['idPV'].'">';
                            ?>
                        </form>
                    </td>
                </tr>
            </table>
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

function afficherMessageAjout($conditionSucces, $messageSucces, $messageErreur) {
    if (isset($_POST[$conditionSucces]) && $_POST[$conditionSucces] != "") {
        echo '<td><div class="ui message">';
        echo '<div class="header"> Succès !</div>';
        echo '<p id="infosAction">'.$messageSucces.'</p>';
        echo '</div></td>';
    } else if (isset($_POST[$conditionSucces])) {
        echo '<td><div class="ui message">';
        echo '<div class="header"> Erreur </div>';
        echo '<p id="infosAction">'.$messageErreur.'</p>';
        echo '</div></td>';
    }
}