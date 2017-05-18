<?php
require_once "menu.php";

enTete("Accueil",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "style/menu.css", "style/index.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$bdd = connexion('portail_gestion');

if (isset($_POST['reset']) && $_POST['reset'] == 1) {
    $bdd->exec('truncate table rapports');
    $bdd->exec('truncate table appareils_utilises');
    $bdd->exec('truncate table pv_controle');
    $bdd->exec('truncate table conclusions_pv');
    $bdd->exec('truncate table constatations_pv');
    $bdd->exec('DELETE FROM appareils WHERE id_appareil > 15');
    $bdd->exec('ALTER TABLE rapports AUTO_INCREMENT = 1');
    $bdd->exec('ALTER TABLE appareils_utilises AUTO_INCREMENT = 1');
    $bdd->exec('ALTER TABLE pv_controle AUTO_INCREMENT = 1');
    $bdd->exec('ALTER TABLE appareils AUTO_INCREMENT = 1');
    $bdd->exec('ALTER TABLE conclusions_pv AUTO_INCREMENT = 1');
    $bdd->exec('ALTER TABLE constatations_pv AUTO_INCREMENT = 1');
    $bdd->exec('UPDATE type_controle SET num_controle = 0');
}

$historique = $bdd->query('select * from historique_activite order by date_activite desc')->fetchAll();
?>

<div id="contenu">
    <h1 id="titreDoc" class="ui blue center aligned huge header">Portail de gestion des PV</h1>

    <div class="ui message">
        <div class="header">
            Bienvenue !
        </div>
        <p>
            Bienvenue sur le système de gestion des PV. Utilisez le menu latéral pour accéder aux différentes
            fonctionnalités disponibles.
        </p>
    </div>
    <div id="boutonsUtilisateur">
        <form method="post" action="#">
            <?php
            // Permet d'indiquer visuellement quel droit a été sélectionné
            if (isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") {
                echo '<button name="utilisateur" value="CA" id="bLeft" class="ui active left attached button">Chargé d\'affaires</button>';
                echo '<button name="utilisateur" value="OP" id="bRight" class="ui right attached button">Opérateur</button>';
            } else if (isset($_SESSION['droit']) && $_SESSION['droit'] == "OP") {
                echo '<button name="utilisateur" value="CA" id="bLeft" class="ui left attached button">Chargé d\'affaires</button>';
                echo '<button name="utilisateur" value="OP" id="bRight" class="ui right active attached button">Opérateur</button>';
            } else {
                echo '<button name="utilisateur" value="CA" id="bLeft" class="ui left attached button">Chargé d\'affaires</button>';
                echo '<button name="utilisateur" value="OP" id="bRight" class="ui right attached button">Opérateur</button>';
            }
            ?>
        </form>
    </div>
    <form method="post" action="index.php">
        <button class="ui right floated red button" name="reset" value="1">REINITIALISER TABLES</button>
    </form>

    <?php if (sizeof($historique) > 0 && isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") { ?>
    <table class="ui celled table" id="historique">
        <thead>
            <tr>
                <th colspan="2" id="titreHistorique"> Activités récentes </th>
            </tr>
            <tr>
                <th>
                    Date
                </th>
                <th>
                    Libellé
                </th>
            </tr>
        </thead>
        <tbody>
        <?php
            $max = 5;
            if (sizeof($historique) < $max)
                $max = sizeof($historique);
            for ($i = 0; $i < $max; $i++) {
                echo '<tr><td>'.$historique[$i]['date_activite'].'</td><td><a href="'.$historique[$i]['page_action'].$historique[$i]['param'].'">'.$historique[$i]['libelle'].'</a></td>';
            }
        ?>
        </tbody>
    </table>

    <div style="width: 50%; height: 80%" id="test" class="ui large modal">
        <div class="header"><table style="width: 100%"><tr><td style="text-align: left;">Aperçu du PV</td><td style="text-align: right;"><i class="close icon"></i></td></tr></table></div>
    </div>

    <p>
        <button id="link" type="button">Test Ajax</button>
    </p>

    <?php } ?>
</div>
</body>

<script>
    $('#link').click(function(e) {
        e.preventDefault();
        jQuery.ajax({
            type: "POST",
            processData: false,
            url: "index.php?name=pdf",
            contentType: "application/xml; charset=utf-8",
            success: function(data)
            {
                var iframe = $('<iframe>');
                iframe.attr('src', 'http://localhost/gestionPV/pv/modifPVOP.php?idPV=8');
                iframe.css({"width": "100%", "height": "100%"});
                $('#test').append(iframe);
                $('#test').modal('show');
            }
        });
    });
</script>