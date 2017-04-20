<?php
    //Script pour générer un fichier Excel
    require_once("../PHPExcel/Classes/PHPExcel.php");

    require_once("../PHPExcel/Classes/PHPExcel/IOFactory.php");

    $bdd = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');

    $pv = $bdd->query('select * from pv_controle where id_pv = '.$_GET['idPV'])->fetch();
    $affaire = $bdd->query('select * from affaire where id_affaire = '.$pv['id_affaire'])->fetch();
    $societeClient = $bdd->query('select * from societe where id_societe = '.$affaire['id_societe'])->fetch();
    $client = $bdd->query('select * from client where id_client = '.$societeClient['ref_client'])->fetch();
    $receveur = $bdd->query('select * from utilisateurs where id_utilisateur = '.$pv['id_receveur'])->fetch();
    $analyste = $bdd->query('select * from utilisateurs where id_utilisateur = '.$pv['id_analyste'])->fetch();

    $appareils = $bdd->query('select * from appareils where id_appareil in (select id_appareil from appareils_utilises where id_pv = '.$_GET['idPV'].')')->fetchAll();

    $controle = $bdd->prepare('select * from type_controle where id_type = ?');

    // création des objets de base et initialisation des informations d'entête

    $classeur = new PHPExcel;

    $classeur->setActiveSheetIndex(0);

    $feuille = $classeur->getActiveSheet();

    $feuille->setTitle("PV n°".$pv['id_pv']);


    $bordures = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        )
    );

    $celluleAct = 6; // Cellule active

    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Détail de l'affaire");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Clients :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $societeClient['nom_societe']);

    $feuille->mergeCells('E'.$celluleAct.':F'.$celluleAct);
    $feuille->setCellValue('E'.$celluleAct,"Numéro équipement");

    $feuille->mergeCells('G'.$celluleAct.':H'.$celluleAct);
    $feuille->setCellValue('G'.$celluleAct, "wow");

//    $feuille->setCellValue('A3', "Nom (Coord.) :");
//    $feuille->setCellValue('B3', $client['nom']);
//    $feuille->setCellValue('A4', "Appel d'offre :");
//    if ($pv['appel_offre'] == 1)
//        $feuille->setCellValue('B4', "X");
//
//    $feuille->setCellValue('A5', "Oral ");
//    if ($pv['obtention'] == 'Oral')
//        $feuille->setCellValue('B5', 'X');
//
//    $feuille->setCellValue('A6', "Mail ");
//    if ($pv['obtention'] == 'Mail')
//        $feuille->setCellValue('B6', 'X');
//
//    $feuille->getColumnDimension('A')->setWidth(20);
//    $feuille->getColumnDimension('B')->setWidth(25);
//
//    $feuille->setCellValue('C2', "Lieu : ");
//    $feuille->setCellValue('D2', $affaire['lieu_intervention']);
//    $feuille->setCellValue('C3', "Tél :");
//    $feuille->setCellValue('D3', $client['tel']);
//    $feuille->setCellValue('C4', "Avenant affaire n° :");
//    $feuille->setCellValue('D4', $pv['avenant_affaire']);
//
//    $feuille->getColumnDimension('C')->setWidth(20);
//    $feuille->getColumnDimension('D')->setWidth(25);
//
//    $feuille->setCellValue('E2', "Demande reçue par :");
//    $feuille->setCellValue('F2', $receveur['nom']);
//    $feuille->setCellValue('E3', "Date : ");
//    $feuille->setCellValue('F3', $pv['date']);
//    $feuille->setCellValue('E4', "Demande analysée par :");
//    $feuille->setCellValue('F4', $analyste['nom']);
//
//    $feuille->getColumnDimension('E')->setWidth(20);
//    $feuille->getColumnDimension('F')->setWidth(25);
//
//    // DÉTAILS DES MISSIONS //
//
//    $feuille->setCellValue('A8', "Titre : ");
//
//    $feuille->mergeCells('B8:F8');
//    $feuille->setCellValue('B8', $affaire['libelle']);
//    $feuille->getCell('B8')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//
//    $feuille->mergeCells('A10:B10');
//    $feuille->setCellValue('A10', "Début de la mission prévu le : ");
//
//    $feuille->mergeCells('A11:B11');
//    $feuille->setCellValue('A11', "Fin de la mission prévue le : ");
//
//    $feuille->mergeCells('A13:E13');
//    $feuille->setCellValue('A13', "Liste des livrables : ");
//    $feuille->getCell('A13')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//
//    if ($pv['id_controle1'] != "") {
//        $controle->execute(array($pv['id_controle1']));
//        ecrireControle($feuille, $affaire,14, $controle->fetch());
//        $max = 14;
//    }
//
//    if ($pv['id_controle2'] != "") {
//        $controle->execute(array($pv['id_controle2']));
//        ecrireControle($feuille, $affaire,15, $controle->fetch());
//        $max = 15;
//    }
//
//    if ($pv['id_controle3'] != "") {
//        $controle->execute(array($pv['id_controle3']));
//        ecrireControle($feuille, $affaire,16, $controle->fetch());
//        $max = 16;
//    }
//
//    if ($pv['id_controle4'] != "") {
//        $controle->execute(array($pv['id_controle4']));
//        ecrireControle($feuille, $affaire,17, $controle->fetch());
//        $max = 17;
//    }
//    if ($pv['id_controle5'] != "") {
//        $controle->execute(array($pv['id_controle5']));
//        ecrireControle($feuille, $affaire,18, $controle->fetch());
//        $max = 18;
//    }
//
//    $max = $max + 2;
//
//    // Appareils utilisés
//
//    $feuille->mergeCells('A'.$max.':F'.$max);
//    $feuille->setCellValue('A'.$max, "Matériel utilisé" );
//    colorerCellule($classeur, 'A'.$max, '808080');
//    $feuille->getCell('A'.$max)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//    $feuille->getStyle('A'.$max)->applyFromArray($bordures);
//
//    $max++;
//
//    for ($i = 0; $i < sizeof($appareils); $i++) {
//        $feuille->setCellValue('A'.$max, "Système");
//        $feuille->setCellValue('B'.$max, $appareils[$i]['systeme']);
//        $feuille->setCellValue('C'.$max, "Marque");
//        $feuille->setCellValue('D'.$max, $appareils[$i]['marque']);
//        $feuille->setCellValue('E'.$max, "Date de calibration");
//        $feuille->setCellValue('F'.$max, $appareils[$i]['date_calib']);
//
//        $max++;
//
//        $feuille->setCellValue('A'.$max, "Type");
//        $feuille->setCellValue('B'.$max, $appareils[$i]['type']);
//        $feuille->setCellValue('C'.$max, "Numéro de série");
//        $feuille->setCellValue('D'.$max, $appareils[$i]['num_serie']);
//        $feuille->setCellValue('E'.$max, "Valide jusqu'au");
//        $feuille->setCellValue('F'.$max, $appareils[$i]['date_valid']);
//
//        colorerCellule($classeur, 'A'.($max-1).':A'.$max, '808080');
//        colorerCellule($classeur, 'C'.($max-1).':C'.$max, '808080');
//        colorerCellule($classeur, 'E'.($max-1).':E'.$max, '808080');
//
//        $feuille->getStyle('A'.($max-1).':F'.$max)->applyFromArray($bordures);
//
//        $max++;
//    }
//
//    // STYLE //
//
//    colorerCellule($classeur, 'B2:B6', 'FFFF00'); // Jaune
//    colorerCellule($classeur, 'D2:D4', 'FFFF00');
//    colorerCellule($classeur, 'F2:F4', 'FFFF00');
//    colorerCellule($classeur, 'B8', 'FFFF00');
//    colorerCellule($classeur, 'A8', '808080'); // Gris
//    colorerCellule($classeur, 'A13', 'FFFF00');
//
//    $feuille->getStyle("A1:F4")->applyFromArray($bordures);
//    $feuille->getStyle("A5:B6")->applyFromArray($bordures);
//    $feuille->getStyle("A8:F8")->applyFromArray($bordures);
//    $feuille->getStyle("A10:C11")->applyFromArray($bordures);

    $writer = PHPExcel_IOFactory::createWriter($classeur, 'Excel2007');

    $writer->save('../PV_PDF/pv_'.$pv['id_pv'].'.xls');

    header('Location: /gestionPV/pv/listePV.php?pdfG=1'); // Attribut pour modifier l'affichage de la page listePV

    function colorerCellule($classeur, $cellule, $couleur){
        $classeur->getActiveSheet()->getStyle($cellule)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => $couleur
            )
        ));
    }

    function ecrireControle($feuille, $affaire, $i, $infosControle) {
        $feuille->setCellValue('A'.$i, "SCO");
        $feuille->setCellValue('B'.$i, explode(" ", $affaire['num_affaire'])[1]); // Explode divise la chaine de caractères

        $feuille->setCellValue('D'.$i, $infosControle['code']);
        $feuille->setCellValue('E'.$i, $infosControle['num_controle']);

        $bordures = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );

        $feuille->getStyle('A'.($i - 1).':E'.$i)->applyFromArray($bordures);
    }
?>


