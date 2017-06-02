<?php
require_once "../menu.php";

if (!verifSessionOP()) {
    header('Location: /gestionPV/index.php');
    exit;
}

enTete("Liste des PV",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));
$bddAffaire = connexion('portail_gestion');
$bddPlanning = connexion('planning');
$bddInspections = connexion('inspections');

if (isset($_GET['nomPV']) && $_GET['nomPV'] != "") {
    $_GET['numAffaire'] = "SCOPEO " . explode("O", explode("-", $_GET['nomPV'])[0])[1]; // Permet de retourner directement sur les PV de la même affaire que le PV généré.
}

// Ensemble des affaires disponibles
$numRapports = $bddAffaire->query('SELECT * FROM affaire WHERE affaire.id_affaire IN (SELECT rapports.id_affaire FROM rapports)')->fetchAll();

// Ensemble des PV disponibles pour l'affaire sélectionnée
$listePV = $bddAffaire->prepare('SELECT * FROM pv_controle WHERE pv_controle.id_rapport IN (SELECT id_rapport FROM rapports WHERE rapports.id_affaire IN (SELECT id_affaire FROM affaire WHERE num_affaire LIKE ?));');

// Infos concernant les PV dans la liste
$selectTypeControle = $bddAffaire->prepare('SELECT * FROM type_controle WHERE id_type = ?');
$selectDiscipline = $bddAffaire->prepare('SELECT * FROM type_discipline WHERE id_discipline = ?');
$selectRapport = $bddAffaire->prepare('SELECT * FROM rapports WHERE id_rapport = ?');
$selectAffaire = $bddAffaire->prepare('SELECT * FROM affaire WHERE id_affaire = ?');
$selectUtilisateur = $bddPlanning->prepare('SELECT * FROM utilisateurs WHERE id_utilisateur = ?');
$selectAvancement = $bddAffaire->prepare('SELECT * FROM avancement WHERE id_avancement = ?');
$selectReservoir = $bddInspections->prepare('SELECT * FROM reservoirs_tmp WHERE id_reservoir = ?');
?>

<div id="contenu">
    <h1 class="ui blue center aligned huge header">Liste des PV</h1>
    <?php
    if (isset($_GET['nomPV']))
        afficherMessage('excelG', "Succès", "Le PV " . $_GET['nomPV'] . " a été généré avec succès !", "", "");
    if (isset($_GET['erreur']))
        afficherMessage('erreur', "Erreur", "Erreur dans la sauvegarde du fichier.", "", "");

    if (sizeof($numRapports) == 0) { ?>
    <div class="ui message">
        <div class="header">
            Aucun rapport disponible !
        </div>
        <p>
            Pour le moment, aucun rapport n'a été crée. Les PV à remplir apparaîtront sur cette page au fur et à mesure
            de leur création.
        </p>
    </div>
    <?php } else { ?>
    <form method="get" action="listePVOP.php" id="choixAffaire">
        <label for="numAffaire"> Choix de l'affaire : </label>
        <?php $url = "listePVOP.php?numAffaire=" ?>
        <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)'
                class="ui search dropdown" name="numAffaire">
            <option selected></option>
            <?php
            for ($i = 0; $i < sizeof($numRapports); $i++) {
                if (isset($_GET['numAffaire']) && $numRapports[$i]['num_affaire'] == $_GET['numAffaire'])
                    echo '<option selected>' . $numRapports[$i]['num_affaire'] . '</option>';
                else
                    echo '<option>' . $numRapports[$i]['num_affaire'] . '</option>';
            }
            ?>
        </select>
    </form>
    <table class="ui celled table">
        <thead>
        <tr>
            <th>Identifiant PV</th>
            <th>Numéro d'affaire</th>
            <th>Réservoir à inspecter</th>
            <th>Contrôle (Dates)</th>
            <th>Responsable</th>
            <th>Avancement</th>
            <th>Modification</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (isset($_GET['numAffaire']) && $_GET['numAffaire'] != "") {
            $listePV->execute(array($_GET['numAffaire']));
            $PVs = $listePV->fetchAll();
            for ($i = 0; $i < sizeof($PVs); $i++) {
                creerLignePV($PVs[$i], $selectUtilisateur, $selectRapport, $selectAffaire, $selectTypeControle, $selectReservoir, $selectAvancement, $selectDiscipline, "modifPVOP.php");
            }
        }
        ?>
        </tbody>
    </table>
    <?php } ?>
</div>

<div class="ui large modal" id="modalAide">
    <div class="header">Aide</div>
    <div>
        <p>
            Cette liste contient l'ensemble des PV présents dans la base, regroupés par affaire. En choisissant une
            affaire, tous les PV correspondants apparaissent, avec les informations principales de chacun. En cliquant
            sur
            "Modifier", vous serez redirigé vers la page du PV correspondant.
        </p>
        <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
        </button>
    </div>
</div>

</body>
</html>