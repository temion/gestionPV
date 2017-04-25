<?php
    //Script pour générer un fichier Excel
    require_once("../PHPExcel/Classes/PHPExcel.php");

    require_once("../PHPExcel/Classes/PHPExcel/IOFactory.php");

    include_once "../util.inc.php";

    $bddAffaire = connexion('portail_gestion');

    $pv = selectAllFromWhere($bddAffaire, "pv_controle", "id_pv_controle", "=", $_POST['idPV'])->fetch();
    $affaire_inspection = selectAllFromWhere($bddAffaire, "affaire_inspection", "id_affaire_inspection", "=", $pv['id_affaire_inspection'])->fetch();
    $affaire = selectAllFromWhere($bddAffaire, "affaire", "id_affaire", "=", $affaire_inspection['id_affaire'])->fetch();
    $societeClient = selectAllFromWhere($bddAffaire, "societe", "id_societe", "=", $affaire['id_societe'])->fetch();
    $client = selectAllFromWhere($bddAffaire, "client", "id_client", "=", $societeClient['ref_client'])->fetch();
    $receveur = selectAllFromWhere($bddAffaire, "utilisateurs", "id_utilisateur", "=", $affaire_inspection['id_receveur'])->fetch();
    $analyste = selectAllFromWhere($bddAffaire, "utilisateurs", "id_utilisateur", "=", $affaire_inspection['id_analyste'])->fetch();

    $typeControle = selectAllFromWhere($bddAffaire, "type_controle", "id_type", "=", $pv['id_type_controle'])->fetch();

    $appareils = $bddAffaire->query('select * from appareils where id_appareil in (select id_appareil from appareils_utilises where id_pv_controle = '.$pv['id_pv_controle'].')')->fetchAll();

    $bddEquipement = connexion('theodolite');
    $equipement = selectAllFromWhere($bddEquipement, "equipement", "idEquipement", "=", $affaire_inspection['id_equipement'])->fetch();
    $ficheTechniqueEquipement = selectAllFromWhere($bddEquipement, "ficheTechniqueEquipement", "idEquipement", "=", $equipement['idEquipement'])->fetch();

    $classeur = new PHPExcel;

    $classeur->setActiveSheetIndex(0);

    $feuille = $classeur->getActiveSheet();

    $feuille->setTitle("PV n°".$pv['id_pv_controle']);

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
    $celluleAct = 4; // Cellule active

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

    // Détails de l'affaire
    $celluleAct++;

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

    // Partie documents référence
    $celluleAct = $celluleAct + 2;

    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Document de référence");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Suivant procédure :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $affaire_inspection['procedure_controle']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Code d'interprétation : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, $affaire_inspection['code_inter']);

    $feuille->getStyle('A'.$celluleAct.':L'.$celluleAct)->applyFromArray($bordures);
    colorerCellule($classeur, 'C'.$celluleAct, $couleurValeur);
    colorerCellule($classeur, 'K'.$celluleAct, $couleurValeur);

    // Partie matériel utilisé
    $celluleAct = $celluleAct + 2;

    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Matériel utilisé");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    for ($i = 0; $i < sizeof($appareils); $i++) {
        creerLigneAppareil($appareils, $i);
    }

    // Partie constatations
    $celluleAct++;
    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Constatations");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    // Partie conclusions
    $celluleAct = $celluleAct + 2;
    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Conclusions");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    // Partie signatures
    $celluleAct = $celluleAct + 2;
    colorerCellule($classeur, 'A'.$celluleAct.':L'.$celluleAct, '426bf4'); // Bleu

    $celluleAct = $celluleAct + 2;
    $feuille->mergeCells('C'.$celluleAct.':G'.($celluleAct + 3));
    $feuille->mergeCells('H'.$celluleAct.':L'.($celluleAct + 3));

    $feuille->setCellValue('A'.$celluleAct, "Date : ");
    $feuille->setCellValue('B'.$celluleAct, date("d.m.y"));

    $feuille->setCellValue('C'.$celluleAct, "Nom et visa du contrôleur");
    $feuille->getCell('C'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $feuille->getCell('C'.$celluleAct)->getStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
    $feuille->setCellValue('H'.$celluleAct, "Nom et visa du vérificateur");
    $feuille->getCell('H'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $feuille->getCell('H'.$celluleAct)->getStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    $celluleAct++;
    $feuille->setCellValue('A'.$celluleAct, "Photos jointes : ");
    $feuille->setCellValue('B'.$celluleAct, ($pv['photos_jointes'] == 1 ? "OUI" : "NON"));

    $celluleAct++;
    $feuille->setCellValue('A'.$celluleAct, "Pièces jointes : ");
    $feuille->setCellValue('B'.$celluleAct, ($pv['pieces_jointes'] == 1 ? "OUI" : "NON"));

    $celluleAct++;
    $feuille->setCellValue('A'.$celluleAct, "Nombre d'annexes : ");
    $feuille->setCellValue('B'.$celluleAct, $pv['nb_annexes']);

    // Sauvegarde du fichier
    $writer = PHPExcel_IOFactory::createWriter($classeur, 'Excel2007');
    mkdir('../PV_Excel/pv_'.$pv['id_pv_controle']);
    $writer->save('../PV_Excel/pv_'.$pv['id_pv_controle'].'/SCO'.explode(" ",$affaire['num_affaire'])[1].'-'.$typeControle['code'].'-'.sprintf("%03d", $pv['num_ordre']).'.xls');

    header('Location: /gestionPV/pv/listePVOP.php?pdfG=1'); // Attribut pour modifier l'affichage de la page listePV
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


