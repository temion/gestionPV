<?php
require_once "../util.inc.php";

$bdd = connexion('portail_gestion');
$modifs = 0;

$attributs = array('societe', 'nom_equipement', 'diametre', 'hauteur', 'hauteur_produit', 'volume', 'distance_points');
for ($i = 0; $i < sizeof($attributs); $i++)
    modifAttribut($bdd, $attributs[$i]);

header('Location: listeEquipements.php?modifs=' . $modifs);
exit;
?>

<?php

/**
 * Modifie dans la base l'attribut sélectionné avec la valeur entrée.
 *
 * @param PDO $bdd Base de données à mettre à jour.
 * @param string $attr Attribut à modifier.
 */
function modifAttribut($bdd, $attr) {
    global $modifs;
    if (isset($_POST[$attr]) && $_POST[$attr] != "") {
        if ($attr == 'societe') {
            $bdd->exec('UPDATE equipements SET id_societe = (SELECT id_societe FROM societe WHERE replace(nom_societe, \' \', \'\') LIKE replace(' . $bdd->quote($_POST['societe'] . '%') . ' , \' \', \'\')) WHERE id_equipement = ' . $_POST['idEquipement']);
        } else {
            $bdd->exec('UPDATE equipements SET ' . $attr . ' = upper(' . $bdd->quote($_POST[$attr]) . ') WHERE id_equipement = ' . $_POST['idEquipement']);
        }

        $modifs++;
    }
}

?>