<?php
require_once "../menu.php";
verifSession("OP");
enTete("Liste des rapports",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$bddAffaire = connexion('portail_gestion');
$bddEquipement = connexion('theodolite');

// Ensemble des affaires disponibles
$numRapports = selectAll($bddAffaire, "rapports")->fetchAll();

$selectAffaire = $bddAffaire->prepare('SELECT * FROM affaire WHERE id_affaire = ?');
$selectEquipement = $bddEquipement->prepare('SELECT * FROM equipement WHERE idEquipement = ?');
$comptePV = $bddAffaire->prepare('SELECT count(*) FROM pv_controle WHERE id_rapport = ?');
?>

    <div id="contenu">
        <h1 class="ui blue center aligned huge header">Liste des rapports d'inspection</h1>
        <?php
        if (isset($_GET['nomRapport'])) {
            afficherMessage('excelG', "Succès", "Le rapport de l'affaire " . $_GET['nomRapport'] . " a été généré avec succès !", "", "");
        }
        if (isset($_GET['erreur'])) {
            afficherMessage('erreur', "Erreur", "Erreur dans la sauvegarde du fichier.", "", "");
        }
        ?>
        <table class="ui celled table">
            <thead>
            <tr>
                <th>Identifiant rapport</th>
                <th>Numéro d'affaire</th>
                <th>Nombre de PV</th>
                <th>Modification</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $i < sizeof($numRapports); $i++) {
                creerLigneRapport($numRapports[$i]);
            }
            ?>
            </tbody>
        </table>
    </div>
    </body>
    </html>

<?php

/**
 * Crée une ligne à ajouter dans le tableau comprenant les différentes informations du rapport passée en paramètre.
 *
 * @param array $rapport Rapport à afficher.
 */
function creerLigneRapport($rapport) {
    global $selectAffaire;
    global $selectEquipement;
    global $comptePV;

    $selectAffaire->execute(array($rapport['id_affaire']));
    $affaire = $selectAffaire->fetch();


    $comptePV->execute(array($rapport['id_rapport']));
    $nbPV = $comptePV->fetch();

    echo '<tr><td>' . $rapport['id_rapport'] . '</td>';
    echo '<td>' . $affaire['num_affaire'] . '</td>';
    echo '<td>' . $nbPV[0] . '</td>';
    echo '<td><form method="get" action="modifRapportCA.php"><button name="idRapport" value="' . $rapport['id_rapport'] . '" class="ui right floated blue button">Modifier</button></form></td>';
}

?>