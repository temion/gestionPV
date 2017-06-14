<?php
require_once "../util.inc.php";
session_start();

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if (!isset($_POST['idEquipement']) || $_POST['idEquipement'] == "") {
    header('Location: /gestionPV/index.php');
    exit;
}

$modifs = 0;

$prepareIdSociete = $bddPortailGestion->prepare('SELECT * FROM societe WHERE replace(nom_societe, \' \', \'\') LIKE replace(?, \' \', \'\')');

$attributs = array('societe', 'designation', 'type', 'diametre', 'hauteur', 'hauteur_produit', 'volume', 'nb_generatrices');
for ($i = 0; $i < sizeof($attributs); $i++)
    modifAttribut($bddInspections, $attributs[$i]);

$equipement = selectReservoirParId($bddInspections, $_POST['idEquipement'])->fetch();
if (isset($equipement['diametre']) && $equipement['diametre'] != "" && isset($equipement['nb_generatrices']) && $equipement['nb_generatrices'] != "") {
    $distance = (($equipement['diametre']*pi()) / $equipement['nb_generatrices']) / 1000;
    $bddInspections->exec('UPDATE reservoirs_gestion_pv SET distance_points = '.$distance.' WHERE id_reservoir = ' . $_POST['idEquipement']);
}

header('Location: listeEquipements.php?modifs=' . $modifs);
exit;
?>

<?php

/**
 * Modifie dans la base l'attribut sélectionné avec la valeur entrée.
 *
 * @param PDO $bddInspections Base de données des réservoirs.
 * @param string $attr Attribut à modifier.
 */
function modifAttribut($bddInspections, $attr) {
    global $modifs, $prepareIdSociete;
    if (isset($_POST[$attr]) && $_POST[$attr] != "") {
        if ($attr == 'societe') {
            $prepareIdSociete->execute(array($_POST['societe']));
            $idSociete = $prepareIdSociete->fetchAll();

            $bddInspections->exec('UPDATE reservoirs_gestion_pv SET id_societe = '.$idSociete[0]['id_societe'].' WHERE id_reservoir = ' . $_POST['idEquipement']);
        } else if ($attr == 'volume') {
            $bddInspections->exec('UPDATE reservoirs_gestion_pv SET ' . $attr . ' = upper(' . $bddInspections->quote(strval(intval($_POST[$attr])*1000)) . ') WHERE id_reservoir = ' . $_POST['idEquipement']);
        } else {
            $bddInspections->exec('UPDATE reservoirs_gestion_pv SET ' . $attr . ' = upper(' . $bddInspections->quote($_POST[$attr]) . ') WHERE id_reservoir = ' . $_POST['idEquipement']);
        }

        $modifs++;
    }
}

?>