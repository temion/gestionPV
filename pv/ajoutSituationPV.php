<?php
    require_once '../util.inc.php';

    $bdd = connexion('portail_gestion');

    update($bdd, "pv_controle", "photos_jointes", etatCB($bdd, 'photos_jointes'), "id_pv", "=", $_GET['idPV']);
    update($bdd, "pv_controle", "pieces_jointes", etatCB($bdd, 'pieces_jointes'), "id_pv", "=", $_GET['idPV']);

    update($bdd, "pv_controle", "controle_interne", etatCB($bdd, 'controle_interne'), "id_pv", "=", $_GET['idPV']);
    update($bdd, "pv_controle", "controle_externe", etatCB($bdd, 'controle_externe'), "id_pv", "=", $_GET['idPV']);
    update($bdd, "pv_controle", "controle_peripherique", etatCB($bdd, 'controle_peripherique'), "id_pv", "=", $_GET['idPV']);

    if (isset($_GET['nbAnnexes']) && is_numeric($_GET['nbAnnexes']))
        update($bdd, "pv_controle", "nb_annexes", $_GET['nbAnnexes'], "id_pv", "=", $_GET['idPV']);

    header('Location: /gestionPV/pv/modifPVOP.php?idPV=' . $_GET['idPV'].'&ajout=1');


/**
 * Retourne une valeur selon l'état de la checkbox dont le nom est passé en paramètre.
 *
 * @param PDO $bdd Base de données à modifier.
 * @param string $var Nom de la checkbox.
 * @return int Entier représentant le booléen dans la base (1 = vrai, 0 = faux).
 */
function etatCB($bdd, $var) {
    $valRet = selectAllFromWhere($bdd, "pv_controle", "id_pv", "=", $_GET['idPV'])->fetch()[$var];
    if (isset($_GET[$var]))
        $valRet = 1;
    else
        $valRet = 0;

    return $valRet;
}
