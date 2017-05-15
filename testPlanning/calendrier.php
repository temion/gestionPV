<?php
require_once '../util.inc.php';
require_once "../menu.php";
verifSession("OP");

enTete("Liste des PV générés",
array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css", "calendrier.css"),
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
$prepareControle = $bdd->prepare('select * from type_controle where id_type = ?');
$prepareAvancement = $bdd->prepare('select * from avancement where id_avancement = ?');

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
                    echo '<td class="casesVides"></td>';
                    $cellulesSup++;
                }

                while ($nbJours < $nbJoursMax) {
                    $dateJour = sprintf("%02d", $nbJours + 1).'-'.sprintf("%02d", array_search($_GET['mois'], array_keys($mois)) + 1).'-'.$_GET['annee'];
                    // Passe à la ligne à la fin de la semaine
                    if (($nbJours + $cellulesSup) % 7 == 0) {
                        echo '</tr><tr>';
                    }

                    // Colore le jour actuel
                    $nbJours++;
                    if ($nbJours == date('j') && $_GET['mois'] == date('F') && $_GET['annee'] == date('Y'))
                        echo '<td id="jourActuel"><a class="jour" href="calendrier.php?mois=' .$_GET['mois'].'&annee='.$_GET['annee'].'&dateSelect='.$dateJour.'">'.$nbJours.'</a></td>';
                    else if (isset($_GET['dateSelect']) && $nbJours == explode("-", $_GET['dateSelect'])[0])
                        echo '<td id="jourSelect"><a class="jour" href="calendrier.php?mois='.$_GET['mois'].'&annee='.$_GET['annee'].'&dateSelect='.$dateJour.'">'.$nbJours.'</a></td>';
                    else
                        echo '<td><a class="jour" href="calendrier.php?mois='.$_GET['mois'].'&annee='.$_GET['annee'].'&dateSelect='.$dateJour.'">'.$nbJours.'</a></td>';
                }

                // Complète le calendrier avec des cases grisées
                while (($nbJoursMax + $cellulesSup) % 7 != 0) {
                    echo '<td class="casesVides"></td>';
                    $nbJoursMax++;
                }
            ?>
        </tbody>
    </table>
    <table id="tabBoutons">
        <tr>
            <td>
                <?php
                echo '<form method="get" action="calendrier.php">';
                echo '<input type="hidden" name="mois" value="'.getDatePrecedente()[0].'">';
                echo '<input type="hidden" name="annee" value="'.getDatePrecedente()[1].'">';
                echo '<button class="ui left floated blue button changementDate"> Mois précédent </button>';
                echo '</form>';
                ?>
            </td>
            <td>
                <?php
                echo '<form method="get" action="calendrier.php">';
                echo '<input type="hidden" name="mois" value="'.getDateSuivante()[0].'">';
                echo '<input type="hidden" name="annee" value="'.getDateSuivante()[1].'">';
                echo '<button class="ui right floated blue button changementDate"> Mois suivant </button>';
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

<script>
    $(function() {
        $("body").on("keydown", function(event) {
            if (event.keyCode == 37) {
                var mois = "<?php echo getDatePrecedente()[0]; ?>";
                var annee = "<?php echo getDatePrecedente()[1]; ?>";
                window.location = "calendrier.php?mois=" + mois  + "&annee=" + annee;
            }

            if (event.keyCode == 39) {
                var mois = "<?php echo getDateSuivante()[0]; ?>";
                var annee = "<?php echo getDateSuivante()[1]; ?>";
                window.location = "calendrier.php?mois=" + mois  + "&annee=" + annee;
            }
        })
    })
</script>

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
    global $prepareDates, $prepareUtilisateur, $prepareRapport, $prepareAffaire, $prepareControle, $prepareEquipement, $prepareAvancement;

    $date = conversionDate($_GET['dateSelect']);
    $prepareDates->execute(array($date));
    $pvs = $prepareDates->fetchAll();

    if (sizeof($pvs) != 0) {
        ?>
        <table id="tabPV" class="ui celled table">
            <thead>
                <tr>
                    <th colspan="7" id="titreTabPV">Contrôles du <?php echo $date; ?></th>
                </tr>
                <tr>
                    <th>Identifiant PV</th>
                    <th>Numéro d'affaire</th>
                    <th>Equipement à inspecter</th>
                    <th>Contrôle</th>
                    <th>Responsable</th>
                    <th>Avancement</th>
                    <th>Modification</th>
                </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $i < sizeof($pvs); $i++) {
                creerLignePV($pvs[$i], $prepareUtilisateur, $prepareRapport, $prepareAffaire, $prepareControle, $prepareEquipement, $prepareAvancement);
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}
?>