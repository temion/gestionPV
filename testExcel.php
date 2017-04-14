<?php
    //Script pour générer un fichier Excel
    require_once ("PHPExcel/Classes/PHPExcel.php");

    require_once ("PHPExcel/Classes/PHPExcel/IOFactory.php");

    $bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');

    $pv = $bddAffaire->query('select * from pv_controle where id_pv = '.$_GET['idPV'])->fetch();
    $affaire = $bddAffaire->query('select * from affaire where id_affaire = '.$pv['id_affaire'])->fetch();
    $societeClient = $bddAffaire->query('select * from societe where id_societe = '.$affaire['id_societe'])->fetch();
    $client = $bddAffaire->query('select * from client where id_client = '.$societeClient['ref_client'])->fetch();
    $receveur = $bddAffaire->query('select * from utilisateurs where id_utilisateur = '.$pv['id_receveur'])->fetch();
    $analyste = $bddAffaire->query('select * from utilisateurs where id_utilisateur = '.$pv['id_analyste'])->fetch();

    // création des objets de base et initialisation des informations d'entête

    $classeur = new PHPExcel;

    $classeur->setActiveSheetIndex(0);

    $feuille = $classeur->getActiveSheet();

    $feuille->setTitle("PV n°".$pv['id_pv']);

    // INFORMATIONS GÉNÉRALES //

    $feuille->mergeCells('A1:F1');
    $feuille->setCellValue('A1', "PV n°".$pv['id_pv']);
    $feuille->getCell('A1')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $feuille->setCellValue('A2', "Client :");
    $feuille->setCellValue('B2', $societeClient['nom_societe']);
    $feuille->setCellValue('A3', "Nom (Coord.) :");
    $feuille->setCellValue('B3', $client['nom']);
    $feuille->setCellValue('A4', "Appel d'offre :");
    if ($pv['appel_offre'] == 1)
        $feuille->setCellValue('B4', "X");

    $feuille->setCellValue('A5', "Oral ");
    if ($pv['obtention'] == 'Oral')
        $feuille->setCellValue('B5', 'X');

    $feuille->setCellValue('A6', "Mail ");
    if ($pv['obtention'] == 'Mail')
        $feuille->setCellValue('B6', 'X');

    $feuille->getColumnDimension('A')->setWidth(20);
    $feuille->getColumnDimension('B')->setWidth(25);

    $feuille->setCellValue('C2', "Lieu : ");
    $feuille->setCellValue('D2', $affaire['lieu_intervention']);
    $feuille->setCellValue('C3', "Tél :");
    $feuille->setCellValue('D3', $client['tel']);
    $feuille->setCellValue('C4', "Avenant affaire n° :");
    $feuille->setCellValue('D4', $pv['avenant_affaire']);

    $feuille->getColumnDimension('C')->setWidth(20);
    $feuille->getColumnDimension('D')->setWidth(25);

    $feuille->setCellValue('E2', "Demande reçue par :");
    $feuille->setCellValue('F2', $receveur['nom']);
    $feuille->setCellValue('E3', "Date : ");
    $feuille->setCellValue('F3', $pv['date']);
    $feuille->setCellValue('E4', "Demande analysée par :");
    $feuille->setCellValue('F4', $analyste['nom']);

    $feuille->getColumnDimension('E')->setWidth(20);
    $feuille->getColumnDimension('F')->setWidth(25);

    // DÉTAILS DES MISSIONS //

    $feuille->setCellValue('A8', "Titre : ");

    $feuille->mergeCells('B8:F8');
    $feuille->setCellValue('B8', $affaire['libelle']);
    $feuille->getCell('B8')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // STYLE //

    colorerCellule($classeur, 'B2:B6', 'FFFF00');
    colorerCellule($classeur, 'D2:D4', 'FFFF00');
    colorerCellule($classeur, 'F2:F4', 'FFFF00');
    colorerCellule($classeur, 'B8', 'FFFF00');
    colorerCellule($classeur, 'A8', '808080');

    $bordures = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )
        )
    );

    $feuille->getStyle("A1:F4")->applyFromArray($bordures);
    $feuille->getStyle("A5:B6")->applyFromArray($bordures);
    $feuille->getStyle("A8:F8")->applyFromArray($bordures);

    $writer = PHPExcel_IOFactory::createWriter($classeur, 'Excel2007');

    $writer->save('./PV_PDF/pv_'.$pv['id_pv'].'.xls');

    header('Location: listePV.php?pdfG=1'); // Attribut pour modifier l'affichage de la page listePV

    function colorerCellule($classeur, $cellule, $couleur){

        $classeur->getActiveSheet()->getStyle($cellule)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => $couleur
            )
        ));
    }
?>


