<?php
    //Script pour générer un fichier Excel
    require_once("../PHPExcel/Classes/PHPExcel.php");

    require_once("../PHPExcel/Classes/PHPExcel/IOFactory.php");

    $bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');

    $pv = $bddAffaire->query('select * from pv_controle where id_pv = '.$_POST['idPV'])->fetch();
    $affaire = $bddAffaire->query('select * from affaire where id_affaire = '.$pv['id_affaire'])->fetch();
    $societeClient = $bddAffaire->query('select * from societe where id_societe = '.$affaire['id_societe'])->fetch();
    $client = $bddAffaire->query('select * from client where id_client = '.$societeClient['ref_client'])->fetch();
    $receveur = $bddAffaire->query('select * from utilisateurs where id_utilisateur = '.$pv['id_receveur'])->fetch();
    $analyste = $bddAffaire->query('select * from utilisateurs where id_utilisateur = '.$pv['id_analyste'])->fetch();

    $controle = $bddAffaire->query('select * from type_controle where concat(libelle, \' (\', code, \')\') like '.$bddAffaire->quote($_POST['controleGenere']))->fetch();
    $controleEffectue = $bddAffaire->query('select * from controles_sur_pv where id_type_controle = '.$controle['id_type'].' and id_pv = '.$pv['id_pv'])->fetch();

    $appareils = $bddAffaire->query('select * from appareils where id_appareil in (select id_appareil from appareils_utilises where id_pv = '.$_POST['idPV'].' and id_controle_associe = '.$controleEffectue['id_type_controle'].')')->fetchAll();

    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');
    $equipement = $bddEquipement->query('select * from equipement where idEquipement = '.$pv['id_equipement'])->fetch();
    $ficheTechniqueEquipement = $bddEquipement->query('select * from ficheTechniqueEquipement where idEquipement = '.$equipement['idEquipement'])->fetch();

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

    // Présentation PV
    $celluleAct = 4; // Cellule active

    $feuille->mergeCells('A'.$celluleAct.':D'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Procès verbal");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->setCellValue('E'.$celluleAct, "Inspection & contrôle");
    $feuille->getCell('E'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $feuille->mergeCells('I'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('I'.$celluleAct, $affaire['num_affaire'].' ? '.$controle['code'].' '.$controleEffectue['num_ordre']);
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
    $feuille->setCellValue('I'.$celluleAct,"Numéro équipement");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, $equipement['Designation'].' '.$equipement['Type']);

    // Personne rencontrée + Diamètre
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Personne rencontrée :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $client['nom']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Diamètre équipement");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, $ficheTechniqueEquipement['diametre']);

    // Num commande + Hauteur
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Numéro commande client :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $affaire['commande']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Hauteur");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, $ficheTechniqueEquipement['hauteurEquipement']);

    // Lieu + Hauteur produit
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Lieu :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $affaire['lieu_intervention']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Hauteur produit");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "?");

    // Début contrôle + Volume
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Début du contrôle :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, "?");

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Volume : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "?");

    // Nombre génératrices + Distance entre 2 points
    $celluleAct++;

    $feuille->mergeCells('A'.$celluleAct.':B'.$celluleAct);
    $feuille->setCellValue('A'.$celluleAct, "Nbre génératrices :");

    $feuille->mergeCells('C'.$celluleAct.':D'.$celluleAct);
    $feuille->setCellValue('C'.$celluleAct, $ficheTechniqueEquipement['nbGeneratrice']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Distance entre 2 points");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, "?");

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
    $feuille->setCellValue('C'.$celluleAct, $pv['procedure_controle']);

    $feuille->mergeCells('E'.$celluleAct.':H'.$celluleAct);

    $feuille->mergeCells('I'.$celluleAct.':J'.$celluleAct);
    $feuille->setCellValue('I'.$celluleAct,"Code d'interprétation : ");

    $feuille->mergeCells('K'.$celluleAct.':L'.$celluleAct);
    $feuille->setCellValue('K'.$celluleAct, $pv['code_inter']);

    // Partie matériel utilisé
    $celluleAct = $celluleAct + 2;

    $feuille->mergeCells('A'.$celluleAct.':L'.$celluleAct);

    $feuille->setCellValue('A'.$celluleAct, "Matériel utilisé");
    $feuille->getCell('A'.$celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    colorerCellule($classeur, 'A'.$celluleAct, '808080'); // Gris

    for ($i = 0; $i < sizeof($appareils); $i++) {
        creerLigneAppareil($appareils, $i);
    }

    $writer = PHPExcel_IOFactory::createWriter($classeur, 'Excel2007');
    mkdir('../PV_Excel/pv_'.$pv['id_pv']);
    $writer->save('../PV_Excel/pv_'.$pv['id_pv'].'/pv_'.$pv['id_pv'].'_'.$controle['code'].''.$controleEffectue['num_ordre'].'.xls');

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

<?php

/**
 * Crée une ligne à ajouter dans le tableur comprenant les différentes informations de l'appareil à l'indice i.
 *
 * @param array $appareils Liste des appareils de la base.
 * @param int $ind Indice de l'appareil à afficher.
 */
function creerLigneAppareil($appareils, $ind) {
    global $celluleAct, $feuille;

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
}


