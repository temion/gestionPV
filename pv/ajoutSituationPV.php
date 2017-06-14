<?php
require_once '../util.inc.php';
session_start();

if (!verifSessionOP()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if (!isset($_GET['idPV']) || $_GET['idPV'] == "") {
    header('Location: /gestionPV/index.php');
    exit;
}

update($bddPortailGestion, "pv_controle", "photos_jointes", etatCB($bddPortailGestion, 'photos_jointes'), "id_pv", "=", $_GET['idPV']);
update($bddPortailGestion, "pv_controle", "pieces_jointes", etatCB($bddPortailGestion, 'pieces_jointes'), "id_pv", "=", $_GET['idPV']);

update($bddPortailGestion, "pv_controle", "controle_interne", etatCB($bddPortailGestion, 'controle_interne'), "id_pv", "=", $_GET['idPV']);
update($bddPortailGestion, "pv_controle", "controle_externe", etatCB($bddPortailGestion, 'controle_externe'), "id_pv", "=", $_GET['idPV']);
update($bddPortailGestion, "pv_controle", "controle_peripherique", etatCB($bddPortailGestion, 'controle_peripherique'), "id_pv", "=", $_GET['idPV']);
update($bddPortailGestion, "pv_controle", "surface_peinte", etatCB($bddPortailGestion, 'peinture'), "id_pv", "=", $_GET['idPV']);

if (isset($_GET['nbAnnexes']) && is_numeric($_GET['nbAnnexes']))
    update($bddPortailGestion, "pv_controle", "nb_annexes", $_GET['nbAnnexes'], "id_pv", "=", $_GET['idPV']);

header('Location: /gestionPV/pv/modifPVOP.php?idPV=' . $_GET['idPV'] . '&ajout=1&modif=1');


/**
 * Retourne une valeur selon l'état de la checkbox dont le nom est passé en paramètre.
 *
 * @param PDO $bdd Base de données à modifier.
 * @param string $var Nom de la checkbox.
 * @return int Entier représentant le booléen dans la base (1 = vrai, 0 = faux).
 */
function etatCB($bdd, $var) {
    $valRet = selectPVParId($bdd, $_GET['idPV'])->fetch()[$var];
    if (isset($_GET[$var]))
        $valRet = 1;
    else
        $valRet = 0;

    return $valRet;
}
