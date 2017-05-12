<?php
require_once '../util.inc.php';
require_once "../menu.php";
verifSession("OP");

enTete("Liste des PV générés",
array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

if (!isset($_GET['mois']) || $_GET['mois'] == "") {
    $_GET['mois'] = date('F');
}

if (!isset($_GET['annee']) || $_GET['annee'] == "") {
    $_GET['annee'] = date('Y');
}

$joursFR = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
$joursEN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

$mois = array("January" => array("Janvier", 31), "February" => array("Février", estBissextile(intval($_GET['annee'])) ? 29 : 28), "March" => array("Mars", 31),
              "April" => array("Avril", 30), "May" => array("Mai", 31), "June" => array("Juin", 30),
              "July" => array("Juillet", 31), "August" => array("Août", 30), "September" => array("Septembre", 31),
              "October" => array("Octobre", 30), "November" => array("Novembre", 30), "December" => array("Décembre", 31));

$moisFR = $mois[$_GET['mois']][0];

$nbJoursMax = $mois[$_GET['mois']][1];
$nbJours = 0;

$premierJour = strtotime("First day of ".$_GET['mois']." ".$_GET['annee']);
$premierJour = date("l", $premierJour);

$bdd = connexion('portail_gestion');
$prepareDates = $bdd->prepare('SELECT * FROM pv_controle WHERE ? BETWEEN date_debut AND date_fin;');
$prepareRapport = $bdd->prepare('select * from rapports where id_rapport = ?');
$prepareAffaire = $bdd->prepare('select * from affaire where id_affaire = ?');
$prepareUtilisateur = $bdd->prepare('select * from utilisateurs where id_utilisateur = ?');

$bddEquipement = connexion('theodolite');
$prepareEquipement = $bddEquipement->prepare('SELECT * FROM equipement WHERE idEquipement = ?');
?>

<div id="contenu">
    <h1 class="ui blue center aligned huge header">Planning</h1>
    <h3 class="ui dividing header"><?php echo $moisFR.' '.$_GET['annee']; ?></h3>
    <table class="ui celled table">
        <thead>
            <tr>
                <?php
                    for ($i = 0; $i < sizeof($joursFR); $i++) {
                        echo '<th>'.$joursFR[$i].'</th>';
                    }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
                // Permet le décalage des cellules au début du mois
                $cellulesSup = 0;
                echo '<tr>';
                while ($joursEN[$cellulesSup] != $premierJour) {
                    echo '<td style="background-color: lightgray"></td>';
                    $cellulesSup++;
                }

                while ($nbJours < $nbJoursMax) {
                    $dateJour = sprintf("%02d", $nbJours + 1).'-'.sprintf("%02d", array_search($_GET['mois'], array_keys($mois)) + 1).'-'.$_GET['annee'];
                    // Passe à la ligne à la fin de la semaine
                    if (($nbJours + $cellulesSup) % 7 == 0) {
                        echo '</tr><tr>';
                    }

                    // Colore le jour actuel
                    if (++$nbJours == date('j') && $_GET['mois'] == date('F') && $_GET['annee'] == date('Y'))
                        echo '<td style="background-color: #91d1f7"><a href="calendrier.php?mois=' .$_GET['mois'].'&annee='.$_GET['annee'].'&dateSelect='.$dateJour.'">'.$nbJours.'</a></td>';
                    else
                        echo '<td><a href="calendrier.php?mois='.$_GET['mois'].'&annee='.$_GET['annee'].'&dateSelect='.$dateJour.'">'.$nbJours.'</a></td>';
                }

                // Complète le calendrier avec des cases grisées
                while (($nbJoursMax + $cellulesSup) % 7 != 0) {
                    echo '<td style="background-color: lightgray"></td>';
                    $nbJoursMax++;
                }
            ?>
        </tbody>
    </table>
    <table>
        <tr>
            <td>
                <?php
                echo '<form method="get" action="calendrier.php">';
                echo '<input type="hidden" name="mois" value="'.getDatePrecedente()[0].'">';
                echo '<input type="hidden" name="annee" value="'.getDatePrecedente()[1].'">';
                echo '<button style="width: 15vh;" class="ui left floated blue button"> Mois précédent </button>';
                echo '</form>';
                ?>
            </td>
            <td>
                <?php
                echo '<form method="get" action="calendrier.php">';
                echo '<input type="hidden" name="mois" value="'.getDateSuivante()[0].'">';
                echo '<input type="hidden" name="annee" value="'.getDateSuivante()[1].'">';
                echo '<button style="width: 15vh;" class="ui right floated blue button"> Mois suivant </button>';
                echo '</form>';
                ?>
            </td>
        </tr>
    </table>
    <?php
    if (isset($_GET['dateSelect']) && $_GET['dateSelect'] != "")
        creerTableInfos();
    ?>
</div>

<?php
/**
 * Indique si l'année passée en paramètre est bissextile ou non.
 *
 * @param string $annee Année à vérifier.
 * @return bool Vrai (1) si l'année est bissextile.
 */
function estBissextile($annee) {
    return ($annee % 4 == 0 && $annee % 100 != 0) || $annee % 400 == 0;
}

/**
 * Passe au mois précédent.
 *
 * @return array Tableau comprenant le mois et l'année précédents.
 */
function getDatePrecedente() {
    global $mois;

    $anneePrecedente = $_GET['annee'];
    if ($_GET['mois'] == 'January') {
        $anneePrecedente--;
        $moisPrecedent = array_keys($mois)[11];
    } else {
        $moisPrecedent = array_keys($mois)[array_search($_GET['mois'], array_keys($mois)) - 1];
    }

    return array($moisPrecedent, $anneePrecedente);
}

/**
 * Passe au mois suivant.
 *
 * @return array Tableau comprenant le mois et l'année suivants.
 */
function getDateSuivante() {
    global $mois;

    $anneeSuivante = $_GET['annee'];
    if ($_GET['mois'] == 'December') {
        $anneeSuivante++;
        $moisSuivant = array_keys($mois)[0];
    } else {
        $moisSuivant = array_keys($mois)[array_search($_GET['mois'], array_keys($mois)) + 1];
    }

    return array($moisSuivant, $anneeSuivante);
}

/**
 * Crée un tableau référençant les différents PV actifs à la date sélectionnée.
 */
function creerTableInfos() {
    global $prepareDates;

    $date = conversionDate($_GET['dateSelect']);
    $prepareDates->execute(array($date));
    $pvs = $prepareDates->fetchAll();

    if (sizeof($pvs) != 0) {
        ?>
        <table class="ui celled table">
            <thead>
                <tr>
                    <th>Identifiant PV</th>
                    <th>Numéro d'affaire</th>
                    <th>Equipement à inspecter</th>
                    <th>Contrôle</th>
                    <th>Responsable</th>
                    <th>Modification</th>
                </tr>
            </thead>
            <tbody>
        <?php
            for ($i = 0; $i < sizeof($pvs); $i++) {
                creerLignePV($pvs[$i]);
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}

/**
 * Remplis une ligne de tableau avec les informations de base du pv passé en paramètre.
 *
 * @param array $pv PV à afficher.
 */
function creerLignePV($pv) {
    global $prepareUtilisateur, $prepareRapport, $prepareAffaire, $prepareEquipement;

    $prepareUtilisateur->execute(array($pv['id_controleur']));
    $controleur = $prepareUtilisateur->fetch();

    $prepareRapport->execute(array($pv['id_rapport']));
    $prepareAffaire->execute(array($prepareRapport->fetch()['id_affaire']));
    $affaire = $prepareAffaire->fetch();

    $prepareEquipement->execute(array($pv['id_equipement']));
    $equipement = $prepareEquipement->fetch();

    echo '<tr>';
    echo '<td>'.$pv['id_pv'].'</td>';
    echo '<td>'.$affaire['num_affaire'].'</td>';
    echo '<td>'.$equipement['Designation'].' '.$equipement['Type'].'</td>';
    echo '<td> Contrôle du '.conversionDate($pv['date_debut']).' au '.conversionDate($pv['date_fin']).'</td>';
    echo '<td>'.$controleur['nom'].'</td>';
    echo '<td><form method="get" action="../pv/modifPVCA.php"><button name="idPV" value="' . $pv['id_pv'] . '" class="ui right floated blue button">Modifier</button></form></td>';
    echo '</tr>';
}
?>