<?php
    //Script pour générer un fichier Excel
    require_once("../PHPExcel/Classes/PHPExcel.php");

    require_once("../PHPExcel/Classes/PHPExcel/IOFactory.php");

    include_once "../util.inc.php";

    $bddAffaire = connexion('portail_gestion');

    $pv = selectAllFromWhere($bddAffaire, "pv_controle", "id_pv_controle", "=", $_POST['idPV'])->fetch();
    $rapport = selectAllFromWhere($bddAffaire, "rapports", "id_rapport", "=", $pv['id_rapport'])->fetch();
    $affaire = selectAllFromWhere($bddAffaire, "affaire", "id_affaire", "=", $rapport['id_affaire'])->fetch();
    $societeClient = selectAllFromWhere($bddAffaire, "societe", "id_societe", "=", $affaire['id_societe'])->fetch();
    $client = selectAllFromWhere($bddAffaire, "client", "id_client", "=", $societeClient['ref_client'])->fetch();
    $receveur = selectAllFromWhere($bddAffaire, "utilisateurs", "id_utilisateur", "=", $rapport['id_receveur'])->fetch();
    $analyste = selectAllFromWhere($bddAffaire, "utilisateurs", "id_utilisateur", "=", $rapport['id_analyste'])->fetch();

    $typeControle = selectAllFromWhere($bddAffaire, "type_controle", "id_type", "=", $pv['id_type_controle'])->fetch();

    $appareils = $bddAffaire->query('select * from appareils where id_appareil in (select id_appareil from appareils_utilises where id_pv_controle = '.$pv['id_pv_controle'].')')->fetchAll();

    $bddEquipement = connexion('theodolite');
    $equipement = selectAllFromWhere($bddEquipement, "equipement", "idEquipement", "=", $rapport['id_equipement'])->fetch();
    $ficheTechniqueEquipement = selectAllFromWhere($bddEquipement, "ficheTechniqueEquipement", "idEquipement", "=", $equipement['idEquipement'])->fetch();

    $classeur = new PHPExcel;

    $classeur->setActiveSheetIndex(0);

    $feuille = $classeur->getActiveSheet();

    $couleurValeur = 'c0c0c0';

    $bordures = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        )
    );

    $feuille->getColumnDimension('A')->setWidth(20);
    $feuille->getColumnDimension('I')->setWidth(20);

    // Présentation PV
    $celluleAct = 1; // Cellule active
    presentationPV($affaire, $typeControle, $pv);

    // Détails de l'affaire
    $celluleAct = $celluleAct + 2;
    detailsAffaire($societeClient, $equipement, $client, $ficheTechniqueEquipement, $affaire, $pv);

    $celluleAct = $celluleAct + 2;
    colorerCellule($classeur, 'A'.$celluleAct.':L'.$celluleAct, '426bf4'); // Bleu

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
    $celluleAct++;
    constatations();

    // Partie conclusions
    $celluleAct = $celluleAct + 2;
    conclusions();

    // Partie signatures
    $celluleAct = $celluleAct + 2;
    signatures($pv);

    // Sauvegarde du fichier et redirection vers la liste des PV
    header('Location: /gestionPV/pv/listePVOP.php?pdfG=1&nomPV='.sauvegarde($affaire, $typeControle, $pv));
    exit;
?>

<?php

/**
 * Colore la cellule du classeur avec la couleur passée en paramètre.
 *
 * @param PHPExcel $classeur Classeur dans lequel se trouve la cellule.
 * @param int $cellule Cellule à colorer.
 * @param string $couleur Code RGB hexadécimal de la couleur.
 */
function colorerCellule($classeur, $cellule, $couleur){
    $classeur->getActiveSheet()->getStyle($cellule)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
            'rgb' => $couleur
        )
    ));
}

/**
 * Crée une ligne à ajouter dans le tableur comprenant les différentes informations de l'appareil à l'indice i.
 *
 * @param array $appareils Liste des appareils de la base.
 * @param int $ind Indice de l'appareil à afficher.
 */
function creerLigneAppareil($appareils, $ind) {
    global $celluleAct, $classeur, $feuille, $bordures, $couleurValeur;

    $celluleAct++;

    $feuille->mergeCells('A' . $celluleAct . ':B' . $celluleAct);
    $feuille->setCellValue('A' . $celluleAct, "Système :");

    $feuille->mergeCells('C' . $celluleAct . ':D' . $celluleAct);
    $feuille->setCellValue('C' . $celluleAct, $appareils[$ind]['systeme']);

    $feuille->mergeCells('E' . $celluleAct . ':F' . $celluleAct);
    $feuille->setCellValue('E'.$celluleAct, "Marque :");

    $feuille->mergeCells('G' . $celluleAct . ':H' . $celluleAct);
    $feuille->setCellValue('G'.$celluleAct, $appareils[$ind]['marque']);

    $feuille->mergeCells('I' . $celluleAct . ':J' . $celluleAct);
    $feuille->setCellValue('I' . $celluleAct, "Date de calibration : ");

    $feuille->mergeCells('K' . $celluleAct . ':L' . $celluleAct);
    $feuille->setCellValue('K' . $celluleAct, $appareils[$ind]['date_calib']);

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'G'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);

    $celluleAct++;

    $feuille->mergeCells('A' . $celluleAct . ':B' . $celluleAct);
    $feuille->setCellValue('A' . $celluleAct, "Type :");

    $feuille->mergeCells('C' . $celluleAct . ':D' . $celluleAct);
    $feuille->setCellValue('C' . $celluleAct, $appareils[$ind]['type']);

    $feuille->mergeCells('E' . $celluleAct . ':F' . $celluleAct);
    $feuille->setCellValue('E'.$celluleAct, "N° de série :");

    $feuille->mergeCells('G' . $celluleAct . ':H' . $celluleAct);
    $feuille->setCellValue('G'.$celluleAct, $appareils[$ind]['num_serie']);

    $feuille->mergeCells('I' . $celluleAct . ':J' . $celluleAct);
    $feuille->setCellValue('I' . $celluleAct, "Date de validation : ");

    $feuille->mergeCells('K' . $celluleAct . ':L' . $celluleAct);
    $feuille->setCellValue('K' . $celluleAct, $appareils[$ind]['date_valid']);

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'G'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);

    $celluleAct++;
}

/**
 * Ecrit l'entête du PV comprenant les coordonnées de la société ainsi que le code du PV.
 *
 * @param array $affaire Informations de la base de données sur l'affaire concernée.
 * @param array $typeControle Informations de la base de données sur le type de contrôle effectué.
 * @param array $pv Informations de la base de données sur le PV généré.
 */
function presentationPV($affaire, $typeControle, $pv) {
    global $classeur, $feuille, $celluleAct;

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "Sarl SCOPEO");

    $celluleAct++;
    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "Route du Hoc");

    $celluleAct++;
    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "76600 Le Havre");

    $celluleAct++;
    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "Tél : 02.35.30.11.30");

    $celluleAct++;
    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "Fax : 02.35.26.12.06");

    $celluleAct++;
    $feuille->mergeCells('A'.$celluleAct.':D'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Procès verbal");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->setCellValue('E'.$celluleAct, "Inspection & contrôle");
    $feuille->getCell('E'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $feuille->mergeCells('I'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('I'.$celluleAct, "SCO ".explode(" ", $affaire['num_affaire'])[1].' ? '.$typeControle['code'].' '.sprintf("%03d", $pv['num_ordre']));
    $feuille->getCell('I'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    colorerCellule($classeur, 'A'.$celluleAct.':L'.$celluleAct, '426bf4'); // Bleu
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
    global $classeur, $feuille, $celluleAct, $bordures, $couleurValeur;

    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Détail de l'affaire");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    // Clients + Numéro équipement
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Clients :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $societeClient['nom_societe']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Numéro équipement : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, $equipement['Designation'].' '.$equipement['Type']);

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);

    // Personne rencontrée + Diamètre
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Personne rencontrée :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $client['nom']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Diamètre équipement : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, ($ficheTechniqueEquipement['diametre']/1000).' m');

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);

    // Num commande + Hauteur
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Numéro commande client :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $affaire['commande']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Hauteur : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, ($ficheTechniqueEquipement['hauteurEquipement']/1000).' m');

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);

    // Lieu + Hauteur produit
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Lieu :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $affaire['lieu_intervention']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Hauteur produit : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "?");

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);

    // Début contrôle + Volume
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Début du contrôle :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $pv['date']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Volume : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "?");

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);

    // Nombre génératrices + Distance entre 2 points
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Nbre génératrices :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $ficheTechniqueEquipement['nbGeneratrice']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Distance entre 2 points : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "?");

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);
}

/**
 * Ecrit la partie concernant les documents de référence.
 *
 * @param array $rapport Informations de la base de données sur l'affaire dans lequel se trouve le PV
 */
function documentsReference($rapport) {
    global $classeur, $feuille, $celluleAct, $bordures, $couleurValeur;

    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Document de référence");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Suivant procédure :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $rapport['procedure_controle']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Code d'interprétation : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, $rapport['code_inter']);

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);

    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);
}

/**
 * Ecrit les informations relatives aux situations de contrôle effectués.
 *
 * @param array $pv Informations de la base de données sur le PV généré.
 */
function situationControle($pv) {
    global $classeur, $feuille, $celluleAct, $bordures, $couleurValeur;

    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Situation de contrôle");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Contrôle interne : ");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, ($pv['controle_interne'] == 1 ? "OUI" : "NON"));

    $feuille->mergeCells('E'.$celluleAct.':F'.$celluleAct);
    $feuille->setCellValue('E'.$celluleAct, "Contrôle externe : ");

    $feuille->mergeCells('G'.$celluleAct.':H'.$celluleAct);
    $feuille->setCellValue('G'.$celluleAct, ($pv['controle_externe'] == 1 ? "OUI" : "NON"));

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct, "Contrôle périphérique : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, ($pv['controle_peripherique'] == 1 ? "OUI" : "NON"));

    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'G'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);
}

/**
 * Ecrit les informations relatives au matériel utilisé pour le contrôle.
 *
 * @param array $appareils Informations de la base de données sur les appareils utilisés.
 */
function materielUtilise($appareils) {
    global $classeur, $feuille, $celluleAct;

    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Matériel utilisé");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    for ($i = 0; $i < sizeof($appareils); $i++) {
        creerLigneAppareil($appareils, $i);
    }
}

/**
 * Représente la partie où l'opérateur indique ses observations et constatations.
 */
function constatations() {
    global $classeur, $feuille, $celluleAct;

    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Constatations");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris
}

/**
 * Représente la partie où sont inscrites les conclusions du contrôle.
 */
function conclusions() {
    global $classeur, $feuille, $celluleAct;

    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Conclusions");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    for ($i = 0; $i < $celluleAct; $i++) {
        $feuille->getCell('C'.$i)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $feuille->getCell('G'.$i)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $feuille->getCell('K'.$i)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    }
}

/**
 * Représente la partie où se trouvent les signatures et la présence ou non d'annexes.
 *
 * @param array $pv Informations de la base de données sur le PV généré.
 */
function signatures($pv) {
    global $classeur, $feuille, $celluleAct, $bordures, $couleurValeur;

    colorerCellule($classeur, 'A'.$celluleAct.':L'.$celluleAct, '426bf4'); // Bleu

    $celluleAct = $celluleAct + 2;
    $feuille->getStyle('A'.$celluleAct.':L'.($celluleAct + 3))->applyFromArray($bordures);

    $feuille->mergeCells('C'.$celluleAct.':G'.($celluleAct + 3));
    $feuille->mergeCells('H'.$celluleAct.':L'.($celluleAct + 3));

    $feuille->setCellValue('A'.$celluleAct, "Date : ");
    $feuille->setCellValue('B'.$celluleAct, date("d.m.y"));
    colorerCellule($classeur, 'B'.$celluleAct, $couleurValeur);

    $feuille->setCellValue('C'.$celluleAct, "Nom et visa du contrôleur");
    $feuille->getCell('C'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $feuille->getCell('C'.$celluleAct)->getStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
    $feuille->setCellValue('H'.$celluleAct, "Nom et visa du vérificateur");
    $feuille->getCell('H'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $feuille->getCell('H'.$celluleAct)->getStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    $celluleAct++;
    $feuille->setCellValue('A'.$celluleAct, "Photos jointes : ");
    $feuille->setCellValue('B'.$celluleAct, ($pv['photos_jointes'] == 1 ? "OUI" : "NON"));
    colorerCellule($classeur, 'B'.$celluleAct, $couleurValeur);

    $celluleAct++;
    $feuille->setCellValue('A'.$celluleAct, "Pièces jointes : ");
    $feuille->setCellValue('B'.$celluleAct, ($pv['pieces_jointes'] == 1 ? "OUI" : "NON"));
    colorerCellule($classeur, 'B'.$celluleAct, $couleurValeur);

    $celluleAct++;
    $feuille->setCellValue('A'.$celluleAct, "Nombre d'annexes : ");
    $feuille->setCellValue('B'.$celluleAct, $pv['nb_annexes']);
    colorerCellule($classeur, 'B'.$celluleAct, $couleurValeur);
}

/**
 * Sauvegarde le fichier Excel et retourne le nom du fichier crée.
 *
 * @param array $affaire Informations de la base de données sur l'affaire concernée.
 * @param array $typeControle Informations de la base de données sur le type de contrôle effectué.
 * @param array $pv Informations de la base de données sur le PV généré.
 * @return string $nomPV Nom du fichier crée.
 */
function sauvegarde($affaire, $typeControle, $pv) {
    global $classeur, $feuille;

    $nomPV = "SCO".explode(" ",$affaire['num_affaire'])[1].'-'.$typeControle['code'].'-'.sprintf("%03d", $pv['num_ordre']);
    $nomRep = explode("-", $nomPV)[0];

    $feuille->setTitle($nomPV);
    $writer = PHPExcel_IOFactory::createWriter($classeur, 'Excel2007');
    mkdir('../PV_Excel/'.$nomRep);
    $writer->save('../PV_Excel/'.$nomRep.'/'.$nomPV.'.xlsx');

    return $nomPV;
}

