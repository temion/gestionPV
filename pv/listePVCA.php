<?php
include_once "../menu.php";
verifSession();
enTete("Liste des PV générés",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));
$bddAffaire = connexion('portail_gestion');
$bddEquipement = connexion('theodolite');

if (isset($_GET['nomPV']) && $_GET['nomPV'] != "") {
    $_GET['numAffaire'] = "SCOPEO ".explode("O", explode("-", $_GET['nomPV'])[0])[1]; // Permet de retourner directement sur les PV de la même affaire que le PV généré.
}

// Ensemble des affaires disponibles
$numRapports = $bddAffaire->query('select * from affaire where affaire.id_affaire in (select rapports.id_affaire from rapports where rapports.id_rapport in (select pv_controle.id_rapport from pv_controle where chemin_fichier is not null))')->fetchAll();

// Ensemble des PV disponibles pour l'affaire sélectionnée
$listePV = $bddAffaire->prepare('SELECT * FROM pv_controle WHERE pv_controle.id_rapport IN (SELECT id_rapport FROM rapports WHERE rapports.id_affaire IN (SELECT id_affaire FROM affaire WHERE num_affaire LIKE ?)) and chemin_fichier is not null;');

// Infos concernant les PV dans la liste
$selectTypeControle = $bddAffaire->prepare('select * from type_controle where id_type = ?');
$selectRapport = $bddAffaire->prepare('select * from rapports where id_rapport = ?');
$selectAffaire = $bddAffaire->prepare('select * from affaire where affaire.id_affaire in (select rapports.id_affaire from rapports where id_rapport = ?)');
$selectEquipement = $bddEquipement->prepare('select * from equipement where idEquipement = ?');
?>

    <div id="contenu">
        <h1 class="ui blue center aligned huge header">Liste des PV</h1>
        <?php
        if (isset($_GET['nomPV']))
            afficherMessage('excelG', "Succès", "Le PV ".$_GET['nomPV']." a été généré avec succès !", "", "");
        if (isset($_GET['erreur']))
            afficherMessage('erreur', "Erreur", "Erreur dans la sauvegarde du fichier.", "", "");
        ?>
        <form method="get" action="listePVCA.php" id="choixAffaire">
            <label for="numAffaire"> Choix de l'affaire : </label>
            <?php $url = "listePVCA.php?numAffaire=" ?>
            <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="ui search dropdown" name="numAffaire">
                <option selected> </option>
                <?php
                for ($i = 0; $i < sizeof($numRapports); $i++) {
                    if (isset($_GET['numAffaire']) && $numRapports[$i]['num_affaire'] == $_GET['numAffaire'])
                        echo '<option selected>'.$numRapports[$i]['num_affaire'].'</option>';
                    else
                        echo '<option>'.$numRapports[$i]['num_affaire'].'</option>';
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
                <th>Informations</th>
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
 * Crée une ligne à ajouter dans le tableau comprenant les différentes informations du PV passé en paramètre.
 *
 * @param array $PV PV à afficher.
 */
function creerLignePV($PV) {
    global $selectAffaire;
    global $selectRapport;
    global $selectEquipement;
    global $selectTypeControle;

    $selectRapport->execute(array($PV['id_rapport']));
    $affaireInspectionPV = $selectRapport->fetch();

    $selectAffaire->execute(array($PV['id_rapport']));
    $affaire = $selectAffaire->fetch();

    $selectEquipement->execute(array($PV['id_equipement']));
    $equipement = $selectEquipement->fetch();

    $selectTypeControle->execute(array($PV['id_type_controle']));
    $typeControle = $selectTypeControle->fetch();

    echo '<tr><td>' . $PV['id_pv'] . '</td><td>';
    echo $affaire['num_affaire'] . '</td><td>';
    echo $equipement['Designation'].' '.$equipement['Type'].'</td><td>';
    echo $typeControle['libelle'].' '.$PV['num_ordre'].' - Début prévu le '.conversionDate($PV['date_debut']).'</td>';
    echo '<td>';
    echo '<form method="get" action="modifPVCA.php"><button name="idPV" value="' . $PV['id_pv'] . '" class="ui right floated blue button">Infos</button></form>';
}

?>