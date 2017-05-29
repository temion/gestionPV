<?php
require_once '../util.inc.php';
require_once 'ConvertisseurPV.php';
require_once 'ConvertisseurRapport.php';
session_start();

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if (!isset($_POST['idRapport']) || $_POST['idRapport'] == "") {
    header('Location: /gestionPV/index.php');
    exit;
}

$rapport = selectRapportParId($bddPortailGestion, $_POST['idRapport'])->fetch();
$affaire = selectAffaireParId($bddPortailGestion, $rapport['id_affaire'])->fetch();


$pvsRapport = selectPVParRapport($bddPortailGestion, $rapport['id_rapport'])->fetchAll();

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

$cheminRep = "../documents/PV_Excel/SCO" . explode(" ", $affaire['num_affaire'])[1] . "/";

$fichiers = scandir($cheminRep);

$nomZip = 'SCO' . explode(" ", $affaire['num_affaire'])[1] . '.zip';

$zip = new ZipArchive;
$zip->open($nomZip, ZipArchive::CREATE);

archiver($zip, "Rapports_Excel", cheminRepertoire("Rapports_Excel"));
archiver($zip, "PV_Excel", cheminRepertoire("PV_Excel"));

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

/**
 * Ajoute dans l'archive zip passée en paramètre un répertoire contenant les fichiers du répertoire passé en paramètre.
 *
 * @param ZipArchive $zip Archive zip à remplir.
 * @param string $repertoireZip Nom du répertoire à créer dans l'archive.
 * @param string $repertoire Chemin du répertoire à ajouter dans l'archive.
 */
function archiver($zip, $repertoireZip, $repertoire) {
    global $affaire;

    if (file_exists($repertoire)) {
        $fichiers = scandir($repertoire);

        foreach ($fichiers as $fichier) {
            if ($fichier != "." && $fichier != "..") {
                $cheminFichier = $repertoire . $fichier;
                $zip->addFile($cheminFichier, 'SCO' . explode(" ", $affaire['num_affaire'])[1] . '/' . $repertoireZip . '/' . basename($cheminFichier));
            }
        }
    }
}

/**
 * Renvoie le chemin des fichiers de l'affaire concernée en fonction du paramètre.
 *
 * @param string $documents Nom du type de fichier (rapports, pv excel, ...)
 * @return string Chemin des documents sur l'affaire concernée.
 */
function cheminRepertoire($documents) {
    global $affaire;

    return "../documents/" . $documents . "/SCO" . explode(" ", $affaire['num_affaire'])[1] . "/";
}