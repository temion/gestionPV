<?php
require_once "menu.php";

enTete("Accueil",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "style/menu.css", "style/index.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

if (!isset($_SESSION['connexion']) || $_SESSION['connexion'] != 1) {
    header('Location: /gestionPV/connexion/connexion.php');
    exit;
}

if (isset($_POST['reset']) && $_POST['reset'] == 1) {
    $bddPortailGestion->exec('truncate table rapports');
    $bddPortailGestion->exec('truncate table appareils_utilises');
    $bddPortailGestion->exec('truncate table pv_controle');
    $bddPortailGestion->exec('truncate table conclusions_pv');
    $bddPortailGestion->exec('truncate table constatations_pv');
    $bddPortailGestion->exec('DELETE FROM appareils WHERE id_appareil > 15');
    $bddPortailGestion->exec('ALTER TABLE rapports AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE appareils_utilises AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE pv_controle AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE appareils AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE conclusions_pv AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE constatations_pv AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('UPDATE type_controle SET num_controle = 0');
}

$historique = $bddPortailGestion->query('SELECT * FROM historique_activite ORDER BY date_activite DESC')->fetchAll();
$prepareUtilisateur = $bddPlanning->prepare('select * from utilisateurs where id_utilisateur = ?');
?>

<div id="contenu">
    <h1 id="titreDoc" class="ui blue center aligned huge header">Portail de gestion des PV</h1>

    <div class="ui message">
        <div class="header">
            Bienvenue !
        </div>
        <p>
            Bienvenue sur le système de gestion des PV. Utilisez le menu latéral pour accéder aux différentes
            fonctionnalités disponibles. Si vous n'avez jamais utilisé cet outil auparavant, vous trouverez pour chaque
            page une aide, accessible via l'icône présente en bas du menu latéral.
        </p>
    </div>
<!--    <div id="boutonsUtilisateur">-->
<!--        <form method="post" action="#">-->
<!--            --><?php
//            // Permet d'indiquer visuellement quel droit a été sélectionné
//            if (isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") {
//                echo '<button name="utilisateur" value="CA" id="bLeft" class="ui active left attached button">Chargé d\'affaires</button>';
//                echo '<button name="utilisateur" value="OP" id="bRight" class="ui right attached button">Opérateur</button>';
//            } else if (isset($_SESSION['droit']) && $_SESSION['droit'] == "OP") {
//                echo '<button name="utilisateur" value="CA" id="bLeft" class="ui left attached button">Chargé d\'affaires</button>';
//                echo '<button name="utilisateur" value="OP" id="bRight" class="ui right active attached button">Opérateur</button>';
//            } else {
//                echo '<button name="utilisateur" value="CA" id="bLeft" class="ui left attached button">Chargé d\'affaires</button>';
//                echo '<button name="utilisateur" value="OP" id="bRight" class="ui right attached button">Opérateur</button>';
//            }
//            ?>
<!--        </form>-->
<!--    </div>-->
    <form method="post" action="index.php">
        <button class="ui right floated red button" name="reset" value="1">REINITIALISER TABLES</button>
    </form>

    <?php if (sizeof($historique) > 0 && isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") { ?>
        <table class="ui celled table" id="historique">
            <thead>
            <tr>
                <th colspan="2" id="titreHistorique"> Activités récentes</th>
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
                $prepareUtilisateur->execute(array($historique[$i]['id_utilisateur']));
                $utilisateur = $prepareUtilisateur->fetch();
                echo '<tr><td>' . $historique[$i]['date_activite'] . '</td><td><a href="' . $historique[$i]['page_action'] . $historique[$i]['param'] . '">' . $historique[$i]['libelle'] . ' par '.$utilisateur['nom'].'</a></td>';
            }
            ?>
            </tbody>
        </table>

    <?php } ?>

<!--    <div style="width: 50%; height: 80%" id="test" class="ui large modal">-->
<!--        <div class="header">-->
<!--            <table style="width: 100%">-->
<!--                <tr>-->
<!--                    <td style="text-align: left;">Aperçu du PV</td>-->
<!--                    <td style="text-align: right;"><i class="close icon"></i></td>-->
<!--                </tr>-->
<!--            </table>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <p>-->
<!--        <button id="link" type="button">Test Ajax</button>-->
<!--    </p>-->
</div>

<div class="ui large modal" id="modalAide">
    <div id="headerModal" class="header">Aide</div>
    <div>
        <?php
        if (isset($_SESSION['droit']) && $_SESSION['droit'] == 'CA') { ?>
            <p>
                Vous pouvez créer de nouveaux rapports dans la section "PV > Création de rapport".
                La section "PV > Liste des rapports d'inspection" regroupe l'ensemble des rapports déjà crées, et vous
                permet d'obtenir des informations sur chacun.
                Enfin, "PV > Liste des PV" vous donne accès à l'ensemble des PV présents dans la base, regroupés par
                affaire.
            </p>
            <p>
                Dans les sections "Appareils" et "Equipements", vous pouvez ajouter des appareils d'inspection ainsi que
                des équipements
                à inspecter dans la base de données.
            </p>
            <p>
                Vous avez également accès à un planning, qui indique les dates à laquelle des contrôles sont effectués.
            </p>
            <p>
                Enfin, le tableau "Activités récentes" vous indique les derniers ajouts et modifications effectués. En
                cliquant sur le libellé
                d'une activité, vous serez redirigé vers la page correspondant au rapport ou au PV concerné.
            </p>
        <?php } else { ?>
            <p>
                Dans la section "PV > Liste des PV existants", vous avez accès à tous les PV crées par les chargés
                d'affaire,
                regroupés par affaire. Cette liste indique les informations des PV, et vous permet d'en sélectionner un
                afin de le modifier.
            </p>
            <p>
                La "Liste des appareils existants" et la "Liste des équipements existants" regroupent les appareils
                d'inspection
                et les équipements à inspecter présents dans la base.
            </p>
        <?php } ?>

        <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK </button>
    </div>
</div>

</body>

<script>
    $('#link').click(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            processData: false,
            url: "index.php?name=pdf",
            contentType: "application/xml; charset=utf-8",
            success: function (data) {
                var iframe = $('<iframe>');
                iframe.attr('src', 'http://localhost/gestionPV/pv/modifPVOP.php?idPV=8');
                iframe.css({"width": "100%", "height": "100%"});
                $('#test').append(iframe);
                $('#test').modal('show');
            }
        });
    });
</script>