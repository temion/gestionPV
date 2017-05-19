<?php
require_once "../util.inc.php";
require_once "ConvertisseurPV.php";

$bddAffaire = connexion('portail_gestion');

$pv = selectPVParId($bddAffaire, $_POST['idPV'])->fetch();
$convertisseur = new ConvertisseurPV($pv);

conversionPDF($convertisseur);
regenerationExcel($convertisseur);

$convertisseur->telecharger($convertisseur->sauvegarde());

/**
 * Permet la génération automatique et le téléchargement en PDF des PV.
 *
 * @param ConvertisseurPV $convertisseur Convertisseur et système de sauvegarde des fichiers.
 */
function conversionPDF($convertisseur) {
    if (isset($_POST['pdf']) && $_POST['pdf'] == 1) {
        $chemin = str_replace("'", "", $convertisseur->getPV()['chemin_pdf']);
        if (file_exists($chemin))
            unlink($chemin);

        header('Location: ../creationPDF/creationPDF.php?idPV=' . $convertisseur->getPV()['id_pv']);
        $convertisseur->telecharger($chemin);
        exit;
    }
}

/**
 * Permet la regénération du fichier Excel, ou le téléchargement du fichier déjà existant.
 *
 * @param ConvertisseurPV $convertisseur Convertisseur et système de sauvegarde des fichiers.
 */
function regenerationExcel($convertisseur) {
    if (isset($convertisseur->getPV()['chemin_excel']) && $convertisseur->getPV()['chemin_excel'] != null) {
        $chemin = str_replace("'", "", $convertisseur->getPV()['chemin_excel']);
        if (file_exists($chemin)) {
            if (isset($_POST['reset']) && $_POST['reset'] == 1) {
                // Si l'utilisateur a cliqué sur regénérer, on supprime le fichier déjà présent
                // et on le regénère
                unlink($chemin);
            } else {
                // Si le PV a déjà été généré, on récupère le fichier déjà présent
                $convertisseur->telecharger($chemin);
                exit;
            }
        }
    }
}