<?php
require_once '../util.inc.php';

$bdd = connexion('portail_gestion');

verifEntrees();

$controle = selectControleParId($bdd, $_GET['controle'])->fetch();
$nouvelleVal = $controle['num_controle'] + 1;
update($bdd, "type_controle", "num_controle", $nouvelleVal, "id_type", "=", $_GET['controle']);

$valeurs = array("null", $_GET['idRapport'], $_GET['reservoir'], $_GET['discipline'], $_GET['controle'], $nouvelleVal,
                 $_GET['controleur'], "false", "false", 0, "false", "false", "false",
                 $bdd->quote(conversionDate($_GET['date_debut'])), $bdd->quote(conversionDate($_GET['date_fin'])), 1,
                 "null", "null", "null");

insert($bdd, "pv_controle", $valeurs);

$pvCree = selectDernierPV($bdd)->fetch();
$rapport = selectRapportParId($bdd, $pvCree['id_rapport'])->fetch();
$affaire = selectAffaireParId($bdd, $rapport['id_affaire'])->fetch();
$type_controle = selectControleParId($bdd, $pvCree['id_type_controle'])->fetch();
$discipline = selectDisciplineParId($bdd, $pvCree['id_discipline'])->fetch();

$titre = "SCO" . explode(" ", $affaire['num_affaire'])[1] . '-' . $discipline['code'] . '-' . $type_controle['code'] . '-' . sprintf("%03d", $pvCree['num_ordre']);

ajouterHistorique($bdd, "Création du PV ".$titre, "pv/modifPVCA.php?idPV=", $pvCree['id_pv']);

header('Location: /gestionPV/pv/modifRapportCA.php?idRapport=' . $_GET['idRapport'] . '&ajout=1');
exit;

/**
 * Vérifie les valeurs, et détermine la valeur du responsable du contrôle.
 */
function verifEntrees() {
    if (condition()) {
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
    return (!isset($_GET['reservoir']) || $_GET['reservoir'] == "" || !isset($_GET['controle']) ||
        $_GET['controle'] == "" || !isset($_GET['discipline']) || $_GET['discipline'] == "" ||
        !isset($_GET['date_debut']) || !verifFormatDates($_GET['date_debut']) ||
        !isset($_GET['date_fin']) || !verifFormatDates($_GET['date_fin']));
}