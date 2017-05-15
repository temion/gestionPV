<?php
require_once "../util.inc.php";
require_once "excelUtil.inc.php";

// Redéfinit le comportement en cas d'erreur, pour gérer la sauvegarde lorsque le fichier est ouvert
set_error_handler(function () {
    // Les erreurs ne bloquent plus l'exécution de l'application.
});

$bdd = connexion('portail_gestion');

$rapport = selectRapportParId($bdd, $_POST['idRapport'])->fetch();
$affaire = selectAffaireParId($bdd, $rapport['id_affaire'])->fetch();
$odp = selectODPParId($bdd, $affaire['id_odp'])->fetch();
$societeClient = selectSocieteParId($bdd, $affaire['id_societe'])->fetch();
$client = selectClientParId($bdd, $odp['id_client'])->fetch();
$receveur = selectUtilisateurParId($bdd, $rapport['id_receveur'])->fetch();
$analyste = selectUtilisateurParId($bdd, $rapport['id_analyste'])->fetch();

$listePV = selectPVParRapport($bdd, $rapport['id_rapport'])->fetchAll();
$type_controle = $bdd->prepare('SELECT * FROM type_controle WHERE id_type = ?');
$prep_discipline = $bdd->prepare('SELECT * FROM type_discipline WHERE id_discipline = ?');
$prep_avancement = $bdd->prepare('select * from avancement where id_avancement = ?');

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

$celluleAct = 1;
presentationRapport($affaire);

$celluleAct++;
detailsRapport($rapport, $affaire, $societeClient, $client, $receveur, $analyste);

$celluleAct++;
$celluleAct++;

remplirCellules($feuille, 'A' . $celluleAct, 'B' . $celluleAct, "Titre : ");
remplirCellules($feuille, 'C' . $celluleAct, 'L' . $celluleAct, $affaire['libelle']);

colorerCellule($classeur, 'A' . $celluleAct, $gris);
colorerCellule($classeur, 'C' . $celluleAct, $jaune);
$feuille->getCell('C' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$feuille->getStyle('A' . $celluleAct . ':L' . $celluleAct)->applyFromArray($bordures);

$celluleAct++;
$celluleAct++;

creerListeLivrables($listePV, $affaire);

$feuille->getColumnDimension('G')->setWidth(15);
$feuille->getColumnDimension('H')->setWidth(15);
$feuille->getColumnDimension('I')->setWidth(15);
$feuille->getColumnDimension('J')->setWidth(15);


sauvegarde($affaire);

header('Location: /gestionPV/pv/listeRapportsCA.php?excelG=1&nomRapport=' . sauvegarde($affaire));

dns_get_record("");
restore_error_handler();
exit;

/**
 * Ecrit l'entête du rapport comprenant les coordonnées de la société ainsi que le numéro de l'affaire.
 *
 * @param array $affaire Informations de l'affaire concernée.
 */
function presentationRapport($affaire) {
    global $classeur, $feuille, $celluleAct, $bleu, $bordures;

    remplirCellules($feuille, 'K' . $celluleAct, 'L' . $celluleAct, "Sarl SCOPEO");

    $celluleAct++;
    remplirCellules($feuille, 'K' . $celluleAct, 'L' . $celluleAct, "Route du Hoc");

    $celluleAct++;
    remplirCellules($feuille, 'K' . $celluleAct, 'L' . $celluleAct, "76600 Le Havre");

    $celluleAct++;
    remplirCellules($feuille, 'K' . $celluleAct, 'L' . $celluleAct, "Tél : 02.35.30.11.30");

    $celluleAct++;
    remplirCellules($feuille, 'K' . $celluleAct, 'L' . $celluleAct, "Fax : 02.35.26.12.06");

    $celluleAct++;
    $feuille->mergeCells('A' . $celluleAct . ':L' . $celluleAct);
    $feuille->setCellValue('A' . $celluleAct, "Descriptif de l'affaire " . $affaire['num_affaire']);
    $feuille->getCell('A' . $celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    colorerCellule($classeur, 'A' . $celluleAct . ':L' . $celluleAct, $bleu); // Bleu
    $feuille->getStyle('A' . $celluleAct . ':L' . $celluleAct)->applyFromArray($bordures);
}

/**
 * Ajoute sur le PDF les détails du rapport.
 *
 * @param array $rapport Tableau contenant les informations du rapport concerné.
 * @param array $affaire Tableau contenant les informations concernant l'affaire suivie.
 * @param array $societeClient Tableau contenant les informations concernant la société cliente.
 * @param array $client Tableau contenant les informations concernant la personne rencontrée.
 * @param array $receveur Tableau contenant les informations concernant la personne ayant reçu l'affaire.
 * @param array $analyste Tableau contenant les informations concernant la personne ayant analysé l'affaire.
 */
function detailsRapport($rapport, $affaire, $societeClient, $client, $receveur, $analyste) {
    global $classeur, $feuille, $celluleAct, $jaune, $bordures;

    creerLigneDetails("Client : ", $societeClient['nom_societe'], "Lieu : ", $affaire['lieu_intervention'], "Demande reçue par : ", $receveur['nom']);
    $feuille->getStyle('A' . $celluleAct . ':L' . $celluleAct)->applyFromArray($bordures);

    $celluleAct++;
    creerLigneDetails("Nom (Coord.) : ", $client['nom'], "Téléphone : ", $client['tel'], "Demande analysée par : ", $analyste['nom']);
    $feuille->getStyle('A' . $celluleAct . ':L' . $celluleAct)->applyFromArray($bordures);

    $celluleAct++;
    creerLigneDetails("Appel d'offre ? ", ($rapport['appel_offre'] == 1 ? "OUI" : "NON"), "Avenant affaire n° : ", $rapport['avenant_affaire'], "Obtention de l'offre : ", $rapport['obtention']);
    $feuille->getStyle('A' . $celluleAct . ':L' . $celluleAct)->applyFromArray($bordures);

    $celluleAct++;
    remplirCellules($feuille, 'I' . $celluleAct, "", "Date : ");
    remplirCellules($feuille, 'J' . $celluleAct, "", conversionDate(explode(" ", $rapport['date'])[0]));
    $feuille->getStyle('I' . $celluleAct . ':L' . $celluleAct)->applyFromArray($bordures);

    remplirCellules($feuille, 'K' . $celluleAct, "", "Heure : ");
    remplirCellules($feuille, 'L' . $celluleAct, "", explode(" ", $rapport['date'])[1]);

    colorerCellule($classeur, 'J' . $celluleAct, $jaune);
    colorerCellule($classeur, 'L' . $celluleAct, $jaune);
}

/**
 * Crée une ligne comportant les différents champs indiqués en paramètre.
 *
 * @param string $enonce1 Enoncé du 1er champ.
 * @param string $valeur1 Valeur du 1er champ.
 * @param string $enonce2 Enoncé du 2ème champ.
 * @param string $valeur2 Valeur du 2ème champ.
 * @param string $enonce3 Enoncé du 3ème champ.
 * @param string $valeur3 Valeur du 3ème champ.
 */
function creerLigneDetails($enonce1, $valeur1, $enonce2, $valeur2, $enonce3, $valeur3) {
    global $feuille, $celluleAct, $classeur, $jaune;

    remplirCellules($feuille, 'A' . $celluleAct, 'B' . $celluleAct, $enonce1);
    remplirCellules($feuille, 'C' . $celluleAct, 'D' . $celluleAct, $valeur1);

    remplirCellules($feuille, 'E' . $celluleAct, 'F' . $celluleAct, $enonce2);
    remplirCellules($feuille, 'G' . $celluleAct, 'H' . $celluleAct, $valeur2);

    remplirCellules($feuille, 'I' . $celluleAct, 'J' . $celluleAct, $enonce3);
    remplirCellules($feuille, 'K' . $celluleAct, 'L' . $celluleAct, $valeur3);

    colorerCellule($classeur, 'C' . $celluleAct, $jaune);
    colorerCellule($classeur, 'G' . $celluleAct, $jaune);
    colorerCellule($classeur, 'K' . $celluleAct, $jaune);
}

/**
 * Crée la liste des différents livrables à effectuer.
 *
 * @param array $listePV Liste des livrables.
 * @param array $affaire Tableau contenant les informations concernant l'affaire suivie.
 */
function creerListeLivrables($listePV, $affaire) {
    global $classeur, $feuille, $celluleAct, $gris, $bordures;

    remplirCellules($feuille, 'A' . $celluleAct, 'B' . $celluleAct, "Liste des livrables : ");
    $feuille->getStyle('A' . $celluleAct . ':B' . $celluleAct)->applyFromArray($bordures);

    colorerCellule($classeur, 'A' . $celluleAct, $gris);

    remplirCellules($feuille, 'D' . $celluleAct, 'E' . $celluleAct, "Numéro d'affaire");
    remplirCellules($feuille, 'F' . $celluleAct, "", "Discipline");
    remplirCellules($feuille, 'G' . $celluleAct, "", "Type de contrôle");
    remplirCellules($feuille, 'H' . $celluleAct, "", "Numéro d'ordre");
    remplirCellules($feuille, 'I' . $celluleAct, "", "Début prévu le");
    remplirCellules($feuille, 'J' . $celluleAct, "", "Fin prévue le");
    remplirCellules($feuille, 'K' . $celluleAct, 'L'.$celluleAct, "Avancement");

    colorerCellule($classeur, 'D' . $celluleAct . ':L' . $celluleAct, $gris);
    $feuille->getStyle('D' . $celluleAct . ':L' . $celluleAct)->applyFromArray($bordures);

    $celluleAct++;
    for ($i = 0; $i < sizeof($listePV); $i++) {
        creerInfosPV($listePV[$i], $affaire);
    }
}

/**
 * Crée une ligne contenant les informations du PV passé en paramètre.
 *
 * @param array $pv Tableau contenant les informations du PV;
 * @param array $affaire Tableau contenant les informations concernant l'affaire suivie.
 */
function creerInfosPV($pv, $affaire) {
    global $feuille, $celluleAct, $type_controle, $prep_discipline, $prep_avancement, $bordures;

    $type_controle->execute(array($pv['id_type_controle']));
    $type = $type_controle->fetch();

    $prep_discipline->execute(array($pv['id_discipline']));
    $discipline = $prep_discipline->fetch();

    $prep_avancement->execute(array($pv['id_avancement']));
    $avancement = $prep_avancement->fetch();

    remplirCellules($feuille, 'D' . $celluleAct, 'E' . $celluleAct, 'SCO ' . explode(" ", $affaire['num_affaire'])[1]);
    remplirCellules($feuille, 'F' . $celluleAct, "", $discipline['code']);
    remplirCellules($feuille, 'G' . $celluleAct, "", $type['code']);
    remplirCellules($feuille, 'H' . $celluleAct, "", $pv['num_ordre']);
    remplirCellules($feuille, 'I' . $celluleAct, "", conversionDate($pv['date_debut']));
    remplirCellules($feuille, 'J' . $celluleAct, "", conversionDate($pv['date_fin']));
    remplirCellules($feuille, 'K' . $celluleAct, 'L' . $celluleAct, $avancement['stade']);

    $feuille->getStyle('D' . $celluleAct . ':L' . $celluleAct)->applyFromArray($bordures);

    $celluleAct++;
}

/**
 * Sauvegarde le fichier Excel et retourne le nom du fichier crée.
 *
 * @param array $affaire Tableau contenant les informations de l'affaire.
 * @return string Titre du fichier enregistré.
 */
function sauvegarde($affaire) {
    global $classeur, $feuille;

    global $affaire;

    $titre = 'SCO' . explode(" ", $affaire['num_affaire'])[1];
    $rep = '../documents/Rapports_Excel/' . $titre . '/';
    $cheminFichier = $rep . $titre . '.xlsx';

    $feuille->setTitle('Rapport_affaire_' . $titre);
    $writer = PHPExcel_IOFactory::createWriter($classeur, 'Excel2007');

    if (!is_dir($rep))
        mkdir($rep);

    try {
        $writer->save($cheminFichier);
        telecharger($cheminFichier);
    } catch (PHPExcel_Writer_Exception $e) {
        header('Location: /gestionPV/pv/listeRapportsCA.php?erreur=&');
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