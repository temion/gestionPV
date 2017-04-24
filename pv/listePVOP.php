<?php
    include_once "../menu.php";
    enTete("Liste des PV",
            array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
            array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));
    $bddAffaire = connexion('portail_gestion');
    $bddEquipement = connexion('theodolite');

    // Ensemble des affaires disponibles
    $numAffairesInspection = $bddAffaire->query('select distinct * from affaire where affaire.id_affaire in (select affaire_inspection.id_affaire from affaire_inspection)')->fetchAll();

    // Ensemble des PV disponibles pour l'affaire sélectionnée
    $listePV = $bddAffaire->prepare('SELECT * FROM pv_controle WHERE pv_controle.id_affaire_inspection IN (SELECT id_affaire_inspection FROM affaire_inspection WHERE affaire_inspection.id_affaire IN (SELECT id_affaire FROM affaire WHERE num_affaire LIKE ?));');

    // Infos concernant les PV dans la liste
    $selectTypeControle = $bddAffaire->prepare('select * from type_controle where id_type = ?');
    $selectAffaireInspectionPV = $bddAffaire->prepare('select * from affaire_inspection where id_affaire_inspection = ?');
    $selectAffaire = $bddAffaire->prepare('select * from affaire where affaire.id_affaire in (select affaire_inspection.id_affaire from affaire_inspection where id_affaire_inspection = ?)');
    $selectEquipement = $bddEquipement->prepare('select * from equipement where idEquipement = ?');
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">Liste des PV</h1>
            <?php
                afficherMessage('pdfG', "Succès", "Votre PV a été généré avec succès !", "", "");
            ?>
            <form method="get" action="listePVOP.php" id="choixAffaire">
                <label for="numAffaire"> Choix de l'affaire : </label>
                <?php $url = "listePVOP.php?numAffaire=" ?>
                <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="ui search dropdown" name="numAffaire">
                    <option selected> </option>
                    <?php
                        for ($i = 0; $i < sizeof($numAffairesInspection); $i++) {
                            if (isset($_GET['numAffaire']) && $numAffairesInspection[$i]['num_affaire'] == $_GET['numAffaire'])
                                echo '<option selected>'.$numAffairesInspection[$i]['num_affaire'].'</option>';
                            else
                                echo '<option>'.$numAffairesInspection[$i]['num_affaire'].'</option>';
                        }
                    ?>
                </select>
            </form>
            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Identifiant PV</th>
                        <th>Numéro d'affaire</th>
                        <th>Equipement à inspecter</th>
                        <th>Contrôle</th>
                        <th>Modification</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (isset($_GET['numAffaire']) && $_GET['numAffaire'] != "") {
                            $listePV->execute(array($_GET['numAffaire']));
                            $PVs = $listePV->fetchAll();
                            for ($i = 0; $i < sizeof($PVs); $i++) {
                                creerLignePV($PVs[$i]);
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>

<?php

/**
 * Crée une ligne à ajouter dans le tableau comprenant les différentes informations du PV à l'indice i.
 *
 * @param array $PV PV à afficher.
 */
function creerLignePV($PV) {
    global $selectAffaire;
    global $selectAffaireInspectionPV;
    global $selectEquipement;
    global $selectTypeControle;

    $selectAffaireInspectionPV->execute(array($PV['id_affaire_inspection']));
    $affaireInspectionPV = $selectAffaireInspectionPV->fetch();

    $selectAffaire->execute(array($PV['id_affaire_inspection']));
    $affaire = $selectAffaire->fetch();

    $selectEquipement->execute(array($affaireInspectionPV['id_equipement']));
    $equipement = $selectEquipement->fetch();

    $selectTypeControle->execute(array($PV['id_type_controle']));
    $typeControle = $selectTypeControle->fetch();

    echo '<tr><td>' . $PV['id_pv_controle'] . '</td><td>';
    echo $affaire['num_affaire'] . '</td><td>';
    echo $equipement['Designation'].' '.$equipement['Type'].'</td><td>';
    echo $typeControle['libelle'].' '.$PV['num_ordre'].' - Début prévu le '.$PV['date'].'</td>';
    echo '<td>';
    if (isset($_SESSION['droit']) && $_SESSION['droit'] == "OP") {
        echo '<form method="post" action="modifPVOP.php"><button name="idPV" value="' . $PV['id_pv_controle'] . '" class="ui right floated blue button">Modifier</button></form>';
    } else if (isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") {
        echo '<form method="post" action="modifPVCA.php"><button name="idAffaire" value="' . $PV['id_affaire_inspection'] . '" class="ui right floated red button">Modifier</button></form></td></tr>';
    } else {
        echo '<form method="post" action="modifPVOP.php"><button name="idPV" value="' . $PV['id_pv_controle'] . '" class="ui right floated blue button">Modifier (opérateur)</button></form>';
        echo '<form method="post" action="modifPVCA.php"><button name="idAffaire" value="' . $PV['id_affaire_inspection'] . '" class="ui right floated red button">Modifier (chargé d\'affaires)</button></form></td></tr>';
    }
}

?>