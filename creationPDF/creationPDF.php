<?php
require_once '../bdd/bdd.inc.php';
require_once 'PDFWriter.php';

if (!isset($_GET['idPV'])) {
    header("Location: /gestionPV");
    exit;
}

$bdd = connexion('portail_gestion');

$pv = selectAllFromWhere($bdd, "pv_controle", "id_pv", "=", $_GET['idPV'])->fetch();
$rapport = selectAllFromWhere($bdd, "rapports", "id_rapport", "=", $pv['id_rapport'])->fetch();
$affaire = selectAllFromWhere($bdd, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();
$odp = selectAllFromWhere($bdd, "odp", "id_odp", "=", $affaire['id_odp'])->fetch();
$societeClient = selectAllFromWhere($bdd, "societe", "id_societe", "=", $affaire['id_societe'])->fetch();
$client = selectAllFromWhere($bdd, "client", "id_client", "=", $odp['id_client'])->fetch();
$receveur = selectAllFromWhere($bdd, "utilisateurs", "id_utilisateur", "=", $rapport['id_receveur'])->fetch();
$analyste = selectAllFromWhere($bdd, "utilisateurs", "id_utilisateur", "=", $rapport['id_analyste'])->fetch();

$typeControle = selectAllFromWhere($bdd, "type_controle", "id_type", "=", $pv['id_type_controle'])->fetch();
$discipline = selectAllFromWhere($bdd, "type_discipline", "id_discipline", "=", $pv['id_discipline'])->fetch();

$bddEquipement = connexion('theodolite');
$equipement = selectAllFromWhere($bddEquipement, "equipement", "idEquipement", "=", $pv['id_equipement'])->fetch();
$ficheTechniqueEquipement = selectAllFromWhere($bddEquipement, "ficheTechniqueEquipement", "idEquipement", "=", $equipement['idEquipement'])->fetch();


$constatations = selectAllFromWhere($bdd, "constatations_pv", "id_pv", "=", $pv['id_pv'])->fetchAll();
$conclusions = selectAllFromWhere($bdd, "conclusions_pv", "id_pv", "=", $pv['id_pv'])->fetchAll();

$titre = "SCO" . explode(" ", $affaire['num_affaire'])[1] . '-' . $discipline['code'] . '-' . $typeControle['code'] . '-' . sprintf("%03d", $pv['num_ordre']);

$infosEnTete = array("SARL SCOPEO", "Route du Hoc", "76600 Le Havre", "Tél : 02.35.30.11.30", "Fax : 02.35.26.12.06");

$pdf = new PDFWriter();
$pdf->ecrireHTML(file_get_contents('stylePDF.css'), 1);

$pdf->enTete($infosEnTete);
$pdf->ecrireTitre($titre);
$pdf->detailsAffaire($societeClient, $equipement, $client, $ficheTechniqueEquipement, $affaire, $pv);
$pdf->detailsDocuments($rapport);
$pdf->situationControle($pv);
$pdf->constatations($constatations);
$pdf->conclusions($conclusions);
$pdf->signatures($pv);

$fichier = str_replace(" ", "-", $titre) . '.pdf';
$pdf->SetTitle($fichier);

$rep = '../documents/PV_PDF/' . explode("-", $fichier)[0] . '/';
mkdir($rep);
$chemin = $rep.$fichier;

update($bdd, "pv_controle", "chemin_pdf", $chemin, "id_pv", "=", $pv['id_pv']);

$pdf->Output($fichier, 'D'); // Permet le téléchargement du fichier par l'utilisateur
$pdf->Output($chemin, 'F'); // Stocke le fichier sur le serveur
?>