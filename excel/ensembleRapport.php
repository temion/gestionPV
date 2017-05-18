<?php
require_once '../util.inc.php';
require_once 'ConvertisseurPV.php';
require_once 'ConvertisseurRapport.php';

$bdd = connexion('portail_gestion');

$rapport = selectRapportParId($bdd, $_POST['idRapport'])->fetch();
$affaire = selectAffaireParId($bdd, $rapport['id_affaire'])->fetch();


$pvsRapport = selectPVParRapport($bdd, $rapport['id_rapport'])->fetchAll();

// Génère le rapport Excel
$c = new ConvertisseurRapport($rapport);
$c->sauvegarde();

// Génère l'ensemble des PV sous Excel
foreach ($pvsRapport as $pv) {
    if (!file_exists(str_replace("'", "", $pv['chemin_excel']))) {
        $c = new ConvertisseurPV($pv);
        $c->sauvegarde();
    }
}

$cheminRep = "../documents/PV_Excel/SCO".explode(" ", $affaire['num_affaire'])[1]."/";

$fichiers = scandir($cheminRep);

$nomZip = 'SCO'.explode(" ", $affaire['num_affaire'])[1].'.zip';

$zip = new ZipArchive;
$zip->open($nomZip, ZipArchive::CREATE);

archiver($zip, "Rapports_Excel", cheminRepertoire("Rapports_Excel"));
archiver($zip, "PV_Excel", cheminRepertoire("PV_Excel"));
//archiver($zip, "PV_PDF", cheminRepertoire("PV_PDF"));

$zip->close();

header('Content-Type: application/zip');
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="' . basename($nomZip) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
readfile($nomZip);

if (file_exists($nomZip))
    unlink($nomZip);

function archiver($zip, $repertoireZip, $repertoire) {
    global $affaire;

    if (file_exists($repertoire)) {
        $fichiers = scandir($repertoire);

        foreach ($fichiers as $fichier) {
            if ($fichier != "." && $fichier != "..") {
                $cheminFichier = $repertoire . $fichier;
                $zip->addFile($cheminFichier, 'SCO'.explode(" ", $affaire['num_affaire'])[1].'/'.$repertoireZip.'/'.basename($cheminFichier));
            }
        }
    }
}

function cheminRepertoire($documents) {
    global $affaire;

    return "../documents/".$documents."/SCO".explode(" ", $affaire['num_affaire'])[1]."/";
}