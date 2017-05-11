<?php
require_once "../util.inc.php";
require_once "excelUtil.inc.php";

// Redéfinit le comportement en cas d'erreur, pour gérer la sauvegarde lorsque le fichier est ouvert
set_error_handler(function () {
    // Les erreurs ne bloquent plus l'exécution de l'application.
});

$bddAffaire = connexion('portail_gestion');

$pv = selectAllFromWhere($bddAffaire, "pv_controle", "id_pv", "=", $_POST['idPV'])->fetch();

if (isset($_POST['pdf']) && $_POST['pdf'] == 1) {
    $chemin = str_replace("'", "", $pv['chemin_pdf']);
    if (!file_exists($chemin))
        header('Location: ../creationPDF/creationPDF.php?idPV=' . $pv['id_pv']);
    else
        telecharger($chemin);

    exit;
}

// Permet de télécharger toujours la dernière version du PV
if (isset($pv['chemin_excel']) && $pv['chemin_excel'] != null) {
    $chemin = str_replace("'", "", $pv['chemin_excel']);
    if (file_exists($chemin)) {
        if (isset($_POST['reset']) && $_POST['reset'] == 1) {
            unlink($chemin);
        } else {
            telecharger(str_replace("'", "", $pv['chemin_excel']));
            exit;
        }
    }
}

$rapport = selectAllFromWhere($bddAffaire, "rapports", "id_rapport", "=", $pv['id_rapport'])->fetch();
$affaire = selectAllFromWhere($bddAffaire, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();
$odp = selectAllFromWhere($bddAffaire, "odp", "id_odp", "=", $affaire['id_odp'])->fetch();
$societeClient = selectAllFromWhere($bddAffaire, "societe", "id_societe", "=", $affaire['id_societe'])->fetch();
$client = selectAllFromWhere($bddAffaire, "client", "id_client", "=", $odp['id_client'])->fetch();
$receveur = selectAllFromWhere($bddAffaire, "utilisateurs", "id_utilisateur", "=", $rapport['id_receveur'])->fetch();
$analyste = selectAllFromWhere($bddAffaire, "utilisateurs", "id_utilisateur", "=", $rapport['id_analyste'])->fetch();

$typeControle = selectAllFromWhere($bddAffaire, "type_controle", "id_type", "=", $pv['id_type_controle'])->fetch();
$discipline = selectAllFromWhere($bddAffaire, "type_discipline", "id_discipline", "=", $pv['id_discipline'])->fetch();

$constatations = selectAllFromWhere($bddAffaire, "constatations_pv", "id_pv", "=", $pv['id_pv'])->fetchAll();
$conclusions = selectAllFromWhere($bddAffaire, "conclusions_pv", "id_pv", "=", $pv['id_pv'])->fetchAll();

$appareils = $bddAffaire->query('SELECT * FROM appareils WHERE id_appareil IN (SELECT id_appareil FROM appareils_utilises WHERE id_pv_controle = ' . $pv['id_pv'] . ')')->fetchAll();

$bddEquipement = connexion('theodolite');
$equipement = selectAllFromWhere($bddEquipement, "equipement", "idEquipement", "=", $pv['id_equipement'])->fetch();
$ficheTechniqueEquipement = selectAllFromWhere($bddEquipement, "ficheTechniqueEquipement", "idEquipement", "=", $equipement['idEquipement'])->fetch();

$classeur = new PHPExcel;

$classeur->setActiveSheetIndex(0);

$feuille = $classeur->getActiveSheet();

$bordures = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('rgb' => '000000')
        )
    )
);

$feuille->getColumnDimension('A')->setWidth(19);
$feuille->getColumnDimension('B')->setWidth(9);
$feuille->getColumnDimension('C')->setWidth(8);
$feuille->getColumnDimension('D')->setWidth(8);
$feuille->getColumnDimension('E')->setWidth(13);
$feuille->getColumnDimension('F')->setWidth(10);
$feuille->getColumnDimension('G')->setWidth(10);
$feuille->getColumnDimension('H')->setWidth(10);

// Présentation PV
$celluleAct = 1; // Cellule active
presentationPV($affaire, $typeControle, $discipline, $pv);

// Détails de l'affaire
$celluleAct = $celluleAct + 2;
detailsAffaire($societeClient, $equipement, $client, $ficheTechniqueEquipement, $affaire, $pv);

$celluleAct = $celluleAct + 2;
colorerCellule($classeur, 'A' . $celluleAct . ':H' . $celluleAct, $bleu); // Bleu

// Partie documents référence
$celluleAct = $celluleAct + 2;
documentsReference($rapport);

// Partie situation de contrôle
$celluleAct = $celluleAct + 2;
situationControle($pv);

// Partie matériel utilisé
$celluleAct = $celluleAct + 2;
materielUtilise($appareils);

// Partie constatations
$celluleAct = $celluleAct + 2;
constatations($constatations);

// Partie conclusions
$celluleAct = $celluleAct + 2;
conclusions($conclusions);

// Partie signatures
$celluleAct = $celluleAct + 2;
signatures($pv);

// Sauvegarde du fichier et redirection vers la liste des PV
sauvegarde($affaire, $typeControle, $discipline, $pv, $bddAffaire);
exit;
?>

<?php

/**
 * Crée une ligne à ajouter dans le tableur comprenant les différentes informations de l'appareil à l'indice i.
 *
 * @param array $appareils Liste des appareils de la base.
 * @param int $ind Indice de l'appareil à afficher.
 */
function creerLigneAppareil($appareils, $ind) {
    global $celluleAct, $classeur, $feuille, $bordures, $gris;

    $celluleAct++;

    creerChamp($feuille, $celluleAct, 'A', 'B', "Système : ", 'C', 'D', $appareils[$ind]['systeme']);
    creerChamp($feuille, $celluleAct, 'E', 'F', "Marque : ", 'G', 'H', $appareils[$ind]['marque']);

    $feuille->getStyle('A' . $celluleAct . ':H' . $celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C' . $celluleAct, $gris);
    colorerCellule($classeur, 'G' . $celluleAct, $gris);

    $celluleAct++;

    creerChamp($feuille, $celluleAct, 'A', 'B', "Type : ", 'C', 'D', $appareils[$ind]['type']);
    creerChamp($feuille, $celluleAct, 'E', 'F', "N° de série : ", 'G', 'H', $appareils[$ind]['num_serie']);

    $feuille->getStyle('A' . $celluleAct . ':H' . $celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C' . $celluleAct, $gris);
    colorerCellule($classeur, 'G' . $celluleAct, $gris);

    $celluleAct++;
    creerChamp($feuille, $celluleAct, 'A', 'B', "Date de calibration : ", 'C', 'D', $appareils[$ind]['date_calib']);
    creerChamp($feuille, $celluleAct, 'E', 'F', "Date de validation : ", 'G', 'H', $appareils[$ind]['date_valid']);
    $feuille->getStyle('A' . $celluleAct . ':H' . $celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C' . $celluleAct, $gris);
    colorerCellule($classeur, 'G' . $celluleAct, $gris);

    $celluleAct++;
}

/**
 * Ecrit l'entête du PV comprenant les coordonnées de la société ainsi que le code du PV.
 *
 * @param array $affaire Informations de la base de données sur l'affaire concernée.
 * @param array $typeControle Informations de la base de données sur le type de contrôle effectué.
 * @param array $discipline Informations de la base de données sur le type de discipline effectué.
 * @param array $pv Informations de la base de données sur le PV généré.
 */
function presentationPV($affaire, $typeControle, $discipline, $pv) {
    global $classeur, $feuille, $celluleAct, $bleu;

    remplirCellules($feuille, 'G' . $celluleAct, 'H' . $celluleAct, "Sarl SCOPEO");

    $celluleAct++;
    remplirCellules($feuille, 'G' . $celluleAct, 'H' . $celluleAct, "Route du Hoc");

    $celluleAct++;
    remplirCellules($feuille, 'G' . $celluleAct, 'H' . $celluleAct, "76600 Le Havre");

    $celluleAct++;
    remplirCellules($feuille, 'G' . $celluleAct, 'H' . $celluleAct, "Tél : 02.35.30.11.30");

    $celluleAct++;
    remplirCellules($feuille, 'G' . $celluleAct, 'H' . $celluleAct, "Fax : 02.35.26.12.06");

    $celluleAct++;
    remplirCellules($feuille, 'A' . $celluleAct, 'B' . $celluleAct, "Procès Verbal");
    $feuille->getCell('A' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    remplirCellules($feuille, 'D' . $celluleAct, 'E' . $celluleAct, "Inspection & contrôle");
    $feuille->getCell('E' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    remplirCellules($feuille, 'G' . $celluleAct, 'H' . $celluleAct, "SCO " . explode(" ", $affaire['num_affaire'])[1] . ' ' . $discipline['code'] . ' ' . $typeControle['code'] . ' ' . sprintf("%03d", $pv['num_ordre']));
    $feuille->getCell('I' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    colorerCellule($classeur, 'A' . $celluleAct . ':H' . $celluleAct, $bleu); // Bleu
}

/**
 * Ecrit les détails de l'affaire.
 *
 * @param array $societeClient Informations de la base de données sur la société cliente à laquelle est adressé le PV.
 * @param array $equipement Informations de la base de données sur l'équipement inspecté.
 * @param array $client Informations de la base de données sur la personne rencontrée.
 * @param array $ficheTechniqueEquipement Informations de la base de données sur les caractéristiques techniques de l'équipement inspecté.
 * @param array $affaire Informations de la base de données sur l'affaire concernée.
 * @param array $pv Informations de la base de données sur le PV généré.
 */
function detailsAffaire($societeClient, $equipement, $client, $ficheTechniqueEquipement, $affaire, $pv) {
    global $classeur, $feuille, $celluleAct, $gris;

    remplirCellules($feuille, 'A' . $celluleAct, 'H' . $celluleAct, "Détails de l'affaire");
    $feuille->getCell('A' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A' . $celluleAct, $gris); // Gris

    // Clients + Numéro équipement
    creerLigneInfos("Clients : ", $societeClient['nom_societe'], "Numéro équipement : ", $equipement['Designation'] . ' ' . $equipement['Type']);

    // Personne rencontrée + Diamètre
    creerLigneInfos("Personne rencontrée : ", $client['nom'], "Diamètre équipement : ", ($ficheTechniqueEquipement['diametre'] / 1000) . ' m');

    // Num commande + Hauteur
    creerLigneInfos("Numéro commande client : ", $affaire['commande'], "Hauteur : ", ($ficheTechniqueEquipement['hauteurEquipement'] / 1000) . ' m');

    // Lieu + Hauteur produit
    creerLigneInfos("Lieu : ", $affaire['lieu_intervention'], "Hauteur produit : ", "?");

    // Début contrôle + Volume
    creerLigneInfos("Début du contrôle : ", $pv['date'], "Volume : ", "?");

    // Nombre génératrices + Distance entre 2 points
    creerLigneInfos("Nbre génératrices : ", $ficheTechniqueEquipement['nbGeneratrice'], "Distance entre 2 points : ", "?");
}

/**
 * Ecrit la partie concernant les documents de référence.
 *
 * @param array $rapport Informations de la base de données sur l'affaire dans lequel se trouve le PV
 */
function documentsReference($rapport) {
    global $classeur, $feuille, $celluleAct, $gris;

    remplirCellules($feuille, 'A' . $celluleAct, 'H' . $celluleAct, "Documents de référence : ");
    $feuille->getCell('A' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A' . $celluleAct, $gris); // Gris

    creerLigneInfos("Suivant procédure : ", $rapport['procedure_controle'], "Code d'interprétation : ", $rapport['code_inter']);
}

/**
 * Ecrit les informations relatives aux situations de contrôle effectués.
 *
 * @param array $pv Informations de la base de données sur le PV généré.
 */
function situationControle($pv) {
    global $classeur, $feuille, $celluleAct, $bordures, $gris;

    remplirCellules($feuille, 'A' . $celluleAct, 'H' . $celluleAct, "Situation de contrôle : ");
    $feuille->getCell('A' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A' . $celluleAct, $gris); // Gris

    $celluleAct++;

    creerChamp($feuille, $celluleAct, 'A', 'B', "Contrôle interne ? ", 'C', 'D', ($pv['controle_interne'] == 1 ? "OUI" : "NON"));
    creerChamp($feuille, $celluleAct, 'E', 'F', "Contrôle externe ? ", 'G', 'H', ($pv['controle_externe'] == 1 ? "OUI" : "NON"));

    $feuille->getStyle('A' . $celluleAct . ':H' . $celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C' . $celluleAct, $gris);
    colorerCellule($classeur, 'G' . $celluleAct, $gris);

    $celluleAct++;
    creerChamp($feuille, $celluleAct, 'A', 'B', "Contrôle périphérique ? ", 'C', 'D', ($pv['controle_peripherique'] == 1 ? "OUI" : "NON"));

    $feuille->getStyle('A' . $celluleAct . ':D' . $celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C' . $celluleAct, $gris);
}

/**
 * Ecrit les informations relatives au matériel utilisé pour le contrôle.
 *
 * @param array $appareils Informations de la base de données sur les appareils utilisés.
 */
function materielUtilise($appareils) {
    global $classeur, $feuille, $celluleAct, $gris;

    remplirCellules($feuille, 'A' . $celluleAct, 'H' . $celluleAct, "Matériel utilisé");
    $feuille->getCell('A' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A' . $celluleAct, $gris); // Gris

    for ($i = 0; $i < sizeof($appareils); $i++) {
        creerLigneAppareil($appareils, $i);
    }
}

/**
 * Représente la partie où l'opérateur indique ses observations et constatations.
 *
 * @param array $constatations Constatations effectuées.
 */
function constatations($constatations) {
    global $classeur, $feuille, $celluleAct, $gris;

    remplirCellules($feuille, 'A' . $celluleAct, 'H' . $celluleAct, "Constatations");
    $feuille->getCell('A' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A' . $celluleAct, $gris); // Gris

    $celluleAct++;

    for ($i = 0; $i < sizeof($constatations); $i++) {
        if ($constatations[$i]['type_constatation'] != null) {
            $feuille->setCellValue('A' . $celluleAct, ($i + 1) . ') ' . $constatations[$i]['type_constatation']);
            $celluleAct++;
        }
        $feuille->setCellValue('A' . $celluleAct, $constatations[$i]['constatation']);
        $celluleAct = $celluleAct + 2;
    }
}

/**
 * Représente la partie où sont inscrites les conclusions du contrôle.
 *
 * @param array $conclusions Conclusions faites sur le PV.
 */
function conclusions($conclusions) {
    global $classeur, $feuille, $celluleAct, $gris;

    remplirCellules($feuille, 'A' . $celluleAct, 'H' . $celluleAct, "Conclusions");
    $feuille->getCell('A' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A' . $celluleAct, $gris); // Gris

    $celluleAct++;

    for ($i = 0; $i < sizeof($conclusions); $i++) {
        $feuille->setCellValue('A' . $celluleAct, $conclusions[$i]['conclusion']);
        $celluleAct = $celluleAct + 2;
    }

    // Boucle permettant d'aligner tous les éléments du tableur
    for ($i = 0; $i < $celluleAct; $i++) {
        $feuille->getCell('C' . $i)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $feuille->getCell('G' . $i)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    }
}

/**
 * Représente la partie où se trouvent les signatures et la présence ou non d'annexes.
 *
 * @param array $pv Informations de la base de données sur le PV généré.
 */
function signatures($pv) {
    global $classeur, $feuille, $celluleAct, $bordures, $gris, $bleu;

    colorerCellule($classeur, 'A' . $celluleAct . ':H' . $celluleAct, $bleu); // Bleu

    $celluleAct = $celluleAct + 2;
    $feuille->getStyle('A' . $celluleAct . ':H' . ($celluleAct + 3))->applyFromArray($bordures);

    $feuille->mergeCells('C' . $celluleAct . ':E' . ($celluleAct + 3));
    $feuille->mergeCells('F' . $celluleAct . ':H' . ($celluleAct + 3));

    $feuille->setCellValue('A' . $celluleAct, "Date : ");
    $feuille->setCellValue('B' . $celluleAct, date("d.m.y"));
    colorerCellule($classeur, 'B' . $celluleAct, $gris);

    $feuille->setCellValue('C' . $celluleAct, "Nom et visa du contrôleur");
    $feuille->getCell('C' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $feuille->getCell('C' . $celluleAct)->getStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
    $feuille->setCellValue('F' . $celluleAct, "Nom et visa du vérificateur");
    $feuille->getCell('F' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $feuille->getCell('F' . $celluleAct)->getStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    $celluleAct++;
    $feuille->setCellValue('A' . $celluleAct, "Photos jointes : ");
    $feuille->setCellValue('B' . $celluleAct, ($pv['photos_jointes'] == 1 ? "OUI" : "NON"));
    colorerCellule($classeur, 'B' . $celluleAct, $gris);

    $celluleAct++;
    $feuille->setCellValue('A' . $celluleAct, "Pièces jointes : ");
    $feuille->setCellValue('B' . $celluleAct, ($pv['pieces_jointes'] == 1 ? "OUI" : "NON"));
    colorerCellule($classeur, 'B' . $celluleAct, $gris);

    $celluleAct++;
    $feuille->setCellValue('A' . $celluleAct, "Nombre d'annexes : ");
    $feuille->setCellValue('B' . $celluleAct, $pv['nb_annexes']);
    colorerCellule($classeur, 'B' . $celluleAct, $gris);
}

/**
 * Crée une ligne affichant les différents champs passés en paramètre.
 *
 * @param string $enonce1 Enoncé du 1er champ.
 * @param string $valeur1 Valeur du 1er champ.
 * @param string $enonce2 Enoncé du 2ème champ.
 * @param string $valeur2 Valeur du 2ème champ.
 */
function creerLigneInfos($enonce1, $valeur1, $enonce2, $valeur2) {
    global $classeur, $feuille, $celluleAct, $bordures, $gris;

    $celluleAct++;
    creerChamp($feuille, $celluleAct, 'A', 'B', $enonce1, 'C', 'D', $valeur1);
    creerChamp($feuille, $celluleAct, 'E', 'F', $enonce2, 'G', 'H', $valeur2);

    $feuille->getStyle('A' . $celluleAct . ':H' . $celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C' . $celluleAct, $gris);
    colorerCellule($classeur, 'G' . $celluleAct, $gris);
}

/**
 * Sauvegarde le fichier Excel sur le serveur et retourne le nom du fichier crée.
 *
 * @param array $affaire Informations de la base de données sur l'affaire concernée.
 * @param array $typeControle Informations de la base de données sur le type de contrôle effectué.
 * @param array $discipline Informations de la base de données sur le type de discipline effectué.
 * @param array $pv Informations de la base de données sur le PV généré.
 * @param PDO $bdd Base de données contenant les informations.
 * @return string $nomPV Nom du fichier crée.
 */
function sauvegarde($affaire, $typeControle, $discipline, $pv, $bdd) {
    global $classeur, $feuille;

    $titre = "SCO" . explode(" ", $affaire['num_affaire'])[1] . '-' . $discipline['code'] . '-' . $typeControle['code'] . '-' . sprintf("%03d", $pv['num_ordre']);
    $rep = '../documents/PV_Excel/' . explode("-", $titre)[0] . '/';
    $cheminFichier = $rep . $titre . '.xlsx';

    $feuille->setTitle($titre);
    $writer = PHPExcel_IOFactory::createWriter($classeur, 'Excel2007');

    if (!is_dir($rep))
        mkdir($rep);

    try {
        $writer->save($cheminFichier);
        update($bdd, "pv_controle", "chemin_excel", $bdd->quote($cheminFichier), "id_pv", "=", $_POST['idPV']);
        telecharger($cheminFichier);
    } catch (PHPExcel_Writer_Exception $e) {
        header('Location: /gestionPV/pv/listePVOP.php?erreur=1');
        exit;
    }

    return $titre;
}

/**
 * Télécharge le fichier crée.
 * @param string $cheminFichier Chemin du fichier a télécharger.
 */
function telecharger($cheminFichier) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($cheminFichier) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($cheminFichier));
    readfile($cheminFichier);
}