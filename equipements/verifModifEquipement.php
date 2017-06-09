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

$attributs = array('societe', 'designation', 'type', 'diametre', 'hauteur', 'hauteur_produit', 'volume', 'distance_points', 'nb_generatrices');
for ($i = 0; $i < sizeof($attributs); $i++)
    modifAttribut($bddInspections, $attributs[$i]);

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
        } else {
            $bddInspections->exec('UPDATE reservoirs_gestion_pv SET ' . $attr . ' = upper(' . $bddInspections->quote($_POST[$attr]) . ') WHERE id_reservoir = ' . $_POST['idEquipement']);
        }

        $modifs++;
    }
}

?>