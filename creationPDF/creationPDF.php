<?php
require_once '../bdd/bdd.inc.php';
require_once 'PDFWriter.php';

if (!isset($_GET['idPV'])) {
    header("Location: /gestionPV");
    exit;
}

$bdd = connexion('portail_gestion');

$pv = selectPVParId($bdd, $_GET['idPV'])->fetch();
$rapport = selectRapportParId($bdd, $pv['id_rapport'])->fetch();
$affaire = selectAffaireParId($bdd, $rapport['id_affaire'])->fetch();
$odp = selectODPParId($bdd, $affaire['id_odp'])->fetch();
$societeClient = selectSocieteParId($bdd, $affaire['id_societe'])->fetch();
$client = selectClientParId($bdd, $odp['id_client'])->fetch();
$receveur = selectUtilisateurParId($bdd, $rapport['id_receveur'])->fetch();
$analyste = selectUtilisateurParId($bdd, $rapport['id_analyste'])->fetch();

$typeControle = selectControleParId($bdd, $pv['id_type_controle'])->fetch();
$discipline = selectDisciplineParId($bdd, $pv['id_discipline'])->fetch();

$bddEquipement = connexion('theodolite');
$equipement = selectEquipementParId($bddEquipement, $pv['id_equipement'])->fetch();
$ficheTechniqueEquipement = selectFicheTechniqueParEquipement($bddEquipement, $equipement['idEquipement'])->fetch();

$constatations = selectConstatationsParPV($bdd, $pv['id_pv'])->fetchAll();
$conclusions = selectConclusionsParPV($bdd, $pv['id_pv'])->fetchAll();

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