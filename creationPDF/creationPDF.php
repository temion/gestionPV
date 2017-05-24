<?php
require_once '../bdd/bdd.inc.php';
require_once 'PDFWriter.php';

session_start();

if (!isset($_GET['idPV'])) {
    header("Location: /gestionPV");
    exit;
}

$pv = selectPVParId($bddPortailGestion, $_GET['idPV'])->fetch();
$rapport = selectRapportParId($bddPortailGestion, $pv['id_rapport'])->fetch();
$affaire = selectAffaireParId($bddPortailGestion, $rapport['id_affaire'])->fetch();
$odp = selectODPParId($bddPortailGestion, $affaire['id_odp'])->fetch();
$societeClient = selectSocieteParId($bddPortailGestion, $affaire['id_societe'])->fetch();
$client = selectClientParId($bddPortailGestion, $odp['id_client'])->fetch();
$receveur = selectUtilisateurParId($bddPlanning, $rapport['id_receveur'])->fetch();
$analyste = selectUtilisateurParId($bddPlanning, $rapport['id_analyste'])->fetch();

$typeControle = selectControleParId($bddPortailGestion, $pv['id_type_controle'])->fetch();
$discipline = selectDisciplineParId($bddPortailGestion, $pv['id_discipline'])->fetch();

$reservoir = selectReservoirParId($bddInspections, $pv['id_reservoir'])->fetch();

$appareils = $bddPortailGestion->query('SELECT * FROM appareils WHERE id_appareil IN (SELECT id_appareil FROM appareils_utilises WHERE id_pv_controle = ' . $pv['id_pv'] . ')')->fetchAll();

$constatations = selectConstatationsParPV($bddPortailGestion, $pv['id_pv'])->fetchAll();
$conclusions = selectConclusionsParPV($bddPortailGestion, $pv['id_pv'])->fetchAll();

$titre = "SCO" . explode(" ", $affaire['num_affaire'])[1] . '-' . $discipline['code'] . '-' . $typeControle['code'] . '-' . sprintf("%03d", $pv['num_ordre']);

$infosEnTete = array("SARL SCOPEO", "Route du Hoc", "76600 Le Havre", "Tél : 02.35.30.11.30", "Fax : 02.35.26.12.06");

$pdf = new PDFWriter();
$pdf->ecrireHTML(file_get_contents('../style/stylePDF.css'), 1);

$pdf->enTete($infosEnTete);
$pdf->ecrireTitre($titre);
$pdf->detailsAffaire($societeClient, $reservoir, $client, $affaire, $pv);
$pdf->detailsDocuments($rapport);
$pdf->situationControle($pv);
$pdf->materielUtilise($appareils);
$pdf->constatations($constatations);
$pdf->conclusions($conclusions);
$pdf->signatures($pv);

$fichier = str_replace(" ", "-", $titre) . '.pdf';
$pdf->SetTitle($fichier);

$rep = '../documents/PV_PDF/' . explode("-", $fichier)[0] . '/';
if (!is_dir($rep))
    mkdir($rep);
$chemin = $rep . $fichier;

update($bddPortailGestion, "pv_controle", "chemin_pdf", $chemin, "id_pv", "=", $pv['id_pv']);

$pdf->Output($fichier, 'D'); // Permet le téléchargement du fichier par l'utilisateur
$pdf->Output($chemin, 'F'); // Stocke le fichier sur le serveur
?>