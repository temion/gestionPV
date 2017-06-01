<?php
require_once '../util.inc.php';
require_once '../historique/gestionHistoriqueUtil.inc.php';
session_start();

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

verifEntrees();

$controle = selectControleParId($bddPortailGestion, $_GET['controle'])->fetch();
$nouvelleVal = $controle['num_controle'] + 1;
update($bddPortailGestion, "type_controle", "num_controle", $nouvelleVal, "id_type", "=", $_GET['controle']);

// Sélection du dernier numéro du contrôle rentré pour le rapport courant.
$numOrdreActuel = $bddPortailGestion->query('SELECT max(num_ordre) FROM pv_controle WHERE id_rapport = ' . $_GET['idRapport'] . ' AND id_type_controle = ' . $_GET['controle'])->fetch();

if ($_GET['date_debut'] != "null")
    $_GET['date_debut'] = $bddPortailGestion->quote(conversionDate($_GET['date_debut']));

if ($_GET['date_fin'] != "null")
    $_GET['date_fin'] = $bddPortailGestion->quote(conversionDate($_GET['date_fin']));

$valeurs = array("null", $_GET['idRapport'], $_GET['reservoir'], $_GET['discipline'], $_GET['controle'], $numOrdreActuel[0] + 1,
                $_GET['controleur'], "false", "false", 0, "false", "false", "false",
                $_GET['date_debut'], $_GET['date_fin'], 1,
                "null", "null", "null");

insert($bddPortailGestion, "pv_controle", $valeurs);

$pvCree = selectDernierPV($bddPortailGestion)->fetch();
$rapport = selectRapportParId($bddPortailGestion, $pvCree['id_rapport'])->fetch();
$affaire = selectAffaireParId($bddPortailGestion, $rapport['id_affaire'])->fetch();
$type_controle = selectControleParId($bddPortailGestion, $pvCree['id_type_controle'])->fetch();
$discipline = selectDisciplineParId($bddPortailGestion, $pvCree['id_discipline'])->fetch();

$titre = "SCO" . explode(" ", $affaire['num_affaire'])[1] . '-' . $discipline['code'] . '-' . $type_controle['code'] . '-' . sprintf("%03d", $pvCree['num_ordre']);

ajouterHistorique($bddPortailGestion, "Création du PV " . $titre, "pv/modifPVCA.php?idPV=", $pvCree['id_pv']);

header('Location: /gestionPV/pv/modifRapportCA.php?idRapport=' . $_GET['idRapport'] . '&ajout=1');
exit;

/**
 * Vérifie les valeurs, et détermine la valeur du responsable du contrôle.
 */
function verifEntrees() {
    if (!condition()) {
        header('Location: /gestionPV/pv/modifRapportCA.php?idRapport=' . $_GET['idRapport'] . '&ajout=0');
        exit;
    }

    if (!isset($_GET['controleur']) || $_GET['controleur'] == "")
        $_GET['controleur'] = "null";
}

/**
 * Vérifie que les valeurs entrées sont correctes.
 *
 * @return bool Vrai si les valeurs sont erronées, faux si toutes les valeurs sont correctes.
 */
function condition() {
    $b = (isset($_GET['reservoir']) && $_GET['reservoir'] != "" && isset($_GET['controle']) &&
          $_GET['controle'] != "" && isset($_GET['discipline']) && $_GET['discipline'] != "");

    if (isset($_GET['date_debut']) && $_GET['date_debut'] != "")
        $b = verifFormatDates($_GET['date_debut']);
    else
        $_GET['date_debut'] = "null";

    if (isset($_GET['date_fin']) && $_GET['date_fin'] != "")
        $b = verifFormatDates($_GET['date_fin']);
    else
        $_GET['date_fin'] = "null";

    if (isset($_GET['date_debut']) && $_GET['date_debut'] != "null" && isset($_GET['date_fin']) && $_GET['date_fin'] != "null")
        $b = date_create($_GET['date_debut']) <= date_create($_GET['date_fin']);

    return $b;
}