<?php

require_once("../PHPExcel/Classes/PHPExcel.php");

require_once("../PHPExcel/Classes/PHPExcel/IOFactory.php");

$gris = 'c0c0c0';
$jaune = 'f5f90c';
$bleu = '426bf4';

/**
 * Fusionne les cellules passées en paramètre, et y insère le contenu.
 *
 * @param PHPExcel_Worksheet $feuille Feuille à modifier.
 * @param string $celluleA Première cellule.
 * @param string $celluleB Dernière cellule.
 * @param string $contenu Contenu à intégrer dans les cellules.
 */
function remplirCellules($feuille, $celluleA, $celluleB, $contenu) {
    if ($celluleB == "")
        $celluleB = $celluleA;

    $feuille->mergeCells($celluleA.':'.$celluleB);
    $feuille->setCellValue($celluleA, $contenu);
}

/**
 * Colore la cellule du classeur avec la couleur passée en paramètre.
 *
 * @param PHPExcel $classeur Classeur dans lequel se trouve la cellule.
 * @param string $cellule Cellule à colorer.
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
 * Crée dans le fichier Excel un champ d'informations aux cellules indiquées avec le contenu précisé en paramètre;
 *
 * @param PHPExcel_Worksheet $feuille Feuille à modifier.
 * @param int $celluleAct Numéro de ligne actuel;
 * @param string $celluleEnonce1 Indice de colonne de la 1ère cellule contenant l'énoncé du champ.
 * @param string $celluleEnonce2 Indice de colonne de la 2ème cellule contenant l'énoncé du champ.
 * @param string $contenuEnonce Enoncé du champ.
 * @param string $celluleValeur1 Indice de colonne de la 1ère cellule contenant la valeur du champ.
 * @param string $celluleValeur2 Indice de colonne de la 2ème cellule contenant la valeur du champ.
 * @param string $contenuValeur Valeur du champ.
 */
function creerChamp($feuille, $celluleAct, $celluleEnonce1, $celluleEnonce2, $contenuEnonce, $celluleValeur1, $celluleValeur2, $contenuValeur) {
    remplirCellules($feuille, $celluleEnonce1.$celluleAct, $celluleEnonce2.$celluleAct, $contenuEnonce);
    remplirCellules($feuille, $celluleValeur1.$celluleAct, $celluleValeur2.$celluleAct, $contenuValeur);
}

?>