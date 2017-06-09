<?php
require_once "menu.php";

if (!verifSession()) {
    header('Location: /gestionPV/connexion/connexion.php');
    exit;
}

if (!isset($_SESSION['connexion']) || $_SESSION['connexion'] != 1) {
    header('Location: /gestionPV/connexion/connexion.php');
    exit;
}

enTete("Accueil",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "style/menu.css", "style/index.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

if (isset($_GET['reset']) && $_GET['reset'] == 1) {
    $bddPortailGestion->exec('truncate table rapports');
    $bddPortailGestion->exec('truncate table appareils_utilises');
    $bddPortailGestion->exec('truncate table pv_controle');
    $bddPortailGestion->exec('truncate table conclusions_pv');
    $bddPortailGestion->exec('truncate table constatations_pv');
    $bddPortailGestion->exec('truncate table historique_activite');
    $bddPortailGestion->exec('truncate table archives_activites');
    $bddPortailGestion->exec('truncate table controle_auto;');
    $bddPortailGestion->exec('DELETE FROM appareils WHERE id_appareil > 15');
    $bddPortailGestion->exec('ALTER TABLE rapports AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE appareils_utilises AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE pv_controle AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE appareils AUTO_INCREMENT = 16');
    $bddPortailGestion->exec('ALTER TABLE conclusions_pv AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE constatations_pv AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE historique_activite AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE archives_activites AUTO_INCREMENT = 1');
    $bddPortailGestion->exec('ALTER TABLE controle_auto AUTO_INCREMENT = 1');
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

    <form method="get" action="index.php">
        <button class="ui right floated red button" name="reset" value="1">REINITIALISER TABLES</button>
    </form>

    <?php if (sizeof($historique) > 0 && isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") { ?>
        <table class="ui celled table" id="historique">
            <thead>
            <tr>
                <th colspan="3" id="titreHistorique">Activités récentes <a href="historique/historique.php">(Détails)</a></th>
            </tr>
            <tr>
                <th>
                    Date
                </th>
                <th>
                    Libellé
                </th>
                <th>
                    Par
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
                echo '<tr>';
                echo '<td>' . conversionDate(explode(" ", $historique[$i]['date_activite'])[0]) . ' ' . explode(" ", $historique[$i]['date_activite'])[1] .'</td>';
                echo '<td>'. (verifPage($bddPortailGestion, $historique[$i]) ? '<a href="' . $historique[$i]['page_action'] . $historique[$i]['param'].'">'. $historique[$i]['libelle'].'</a>' : $historique[$i]['libelle']). '</td>';
                echo '<td>' . $utilisateur['nom'] . '</td></tr>';
            }
            ?>
            </tbody>
        </table>

    <?php } ?>
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
                Enfin, la section "Gestion" vous donne accès à différents outils tels que l'historique complet des activités
                ou encore le programmateur de PV. Vous avez également accès à un planning, qui indique les dates à laquelle des contrôles sont effectués.
            </p>
            <p>
                Au centre de la page se trouve également un tableau, représentant les 5 dernières actions
                (création/modification de rapport/PV) effectuées par les utilisateurs sur le portail. En cliquant sur le
                libellé d'une des actions, vous serez redirigé vers la page du document concerné, si celui-ci existe encore.
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

<?php
/**
 * Vérifie que l'élément actuel de l'historique existe encore dans la base, afin de rendre actif ou non son lien.
 *
 * @param $bdd Base de données dans lequel se trouve l'historique.
 * @param $activite Activité à vérifier.
 *
 * @return bool Vrai si l'élément est toujours présent dans la base.
 */
function verifPage($bdd, $activite) {
    if (strchr($activite['page_action'], "Rapport"))
        return sizeof(selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $activite['param'])->fetchAll()) > 0;
    else if (strchr($activite['page_action'], "PV"))
        return sizeof(selectAllFromWhere($bdd, "pv_controle", "id_pv", "=", $activite['param'])->fetchAll()) > 0;
    else
        return false;
}
?>