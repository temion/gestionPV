<?php
require_once '../util.inc.php';
require_once '../menu.php';
enTete("Accueil",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$bdd = connexion('portail_gestion');

$selectUtilisateurs = selectAll($bdd, "utilisateurs")->fetchAll();
$preparePeriodesControle = $bdd->prepare('select id_pv, date_debut, date_fin from pv_controle join utilisateurs on id_controleur = id_utilisateur where upper(nom) like ?');

?>

<div id="contenu">
    <form method="get" action="testPlanning.php">
        <label> Choix de l'utilisateur : </label>
        <?php $url = "testPlanning.php?utilisateur=" ?>
        <select onchange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="ui search dropdown">
            <option selected></option>
            <?php
            for ($i = 0; $i < sizeof($selectUtilisateurs); $i++) {
                echo '<option>' . $selectUtilisateurs[$i]['nom'] . '</option>';
            }
            ?>
        </select>
    </form>

    <?php
    if (isset($_GET['utilisateur']) && $_GET['utilisateur'] != "") {
        $preparePeriodesControle->execute(array($_GET['utilisateur']));
        $periodesControle = $preparePeriodesControle->fetchAll();

        for ($i = 0; $i < sizeof($periodesControle); $i++) {
            echo '<h4>' . conversionDate($periodesControle[$i]['date_debut']) . ' - ' . conversionDate($periodesControle[$i]['date_fin']) . ' (' . $periodesControle[$i]['id_pv'] . ')</h4>';
        }
    }
    ?>
</div>