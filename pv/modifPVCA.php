<?php
    include_once "../menu.php";
    enTete("Modification de PV",
                 array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
                 array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bdd = connexion('portail_gestion');
    $affaireInspection = $bdd->query('select * from affaire_inspection where id_affaire_inspection = '.$_POST['idAffaire'])->fetch();

    if (isset($_POST['controle']) && $_POST['controle'] != "") {
        if (verifFormatDates($_POST['date_debut'])) {
            $controle = $bdd->query('SELECT * FROM type_controle WHERE concat(libelle, \' (\', code, \')\') LIKE ' . $bdd->quote($_POST['controle']))->fetch();
            $bdd->exec('INSERT INTO pv_controle VALUES (NULL, ' . $controle['id_type'] . ', ' . $_POST['idAffaire'] . ', ' . ($controle['num_controle'] + 1) . ', ' . $bdd->quote(conversionDate($_POST['date_debut'])) . ')') or die(print_r($bdd->errorInfo(), true));
            $bdd->exec('UPDATE type_controle SET num_controle = num_controle + 1 WHERE id_type = ' . $controle['id_type']);
        } else {
            $_POST['controle'] = "";
        }
    }

    $controles = $bdd->query('select * from type_controle')->fetchAll();
    $controlesEffectues = $bdd->query('select * from pv_controle where id_affaire_inspection = '.$affaireInspection['id_affaire_inspection'])->fetchAll();
    $typeControle = $bdd->prepare('select * from type_controle where id_type = ?');

    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">Modification du PV </h1>
            <table id="ensTables">
                <tr>
                    <td class="partieTableau">
                        <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionExcel.php"' ?>>
                            <?php creerApercuDetails($affaireInspection); ?>
                            <table>
                                <?php creerApercuDocuments($affaireInspection); ?>
                                <tr>
                                    <td>
                                        <?php
                                            echo '<input type="hidden" name="idPV" value="'.$affaireInspection['id_affaire_inspection'].'">';
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                    <td class="partieTableau">
                        <form method="post" action="modifPVCA.php">
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
                                        <label> Début prévu du contrôle le : </label>
                                        <div class="ui input">
                                            <input type="date" name="date_debut" placeholder="(JJ-MM-AAAA)">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label> Contrôles déjà ajoutés : </label>
                                        <select disabled size=4 class="ui search dropdown listeUtilises">
                                            <?php
                                            for ($i = 0; $i < sizeof($controlesEffectues); $i++) {
                                                $typeControle->execute(array($controlesEffectues[$i]['id_type_controle']));
                                                $infosControle = $typeControle->fetch();
                                                echo '<option>'.$infosControle['libelle'].' ('.$infosControle['code'].') - '.conversionDate($controlesEffectues[$i]['date']).'</option>';
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
                                echo '<input type="hidden" name="idAffaire" value="'.$_POST['idAffaire'].'">';
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