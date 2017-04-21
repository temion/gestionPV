<?php
include_once "menu.php";
enTete("Accueil",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "style/index.css", "style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

if (isset($_POST['reset']) && $_POST['reset'] == 1) {
    $bdd = connexion('portail_gestion');
    $bdd->exec('truncate table pv_controle');
    $bdd->exec('truncate table appareils_utilises');
    $bdd->exec('truncate table controles_sur_pv');
    $bdd->exec('delete from appareils where id_appareil > 15');
    $bdd->exec('alter table pv_controle auto_increment = 1');
    $bdd->exec('alter table appareils_utilises auto_increment = 1');
    $bdd->exec('alter table controles_sur_pv auto_increment = 1');
    $bdd->exec('alter table appareils auto_increment = 1');
    $bdd->exec('update type_controle set num_controle = 0');
}
?>

<div id="contenu">
    <h1 id="titre" class="ui blue center aligned huge header">Portail de gestion des PV</h1>

    <div class="ui message">
        <div class="header">
            Bienvenue !
        </div>
        <p>
            Bienvenue sur le système de gestion des PV. Utilisez le menu latéral pour accéder aux différentes fonctionnalités disponibles.
        </p>
    </div>
    <form method="post" action="index.php">
        <button class="ui right floated red button" name="reset" value="1">REINITIALISER TABLES</button>
    </form>
</div>

