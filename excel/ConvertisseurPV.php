<?php
require_once "../util.inc.php";
require_once "excelUtil.inc.php";

class ConvertisseurPV extends PHPExcel {

    private $couleurs = ['bleu' => '426bf4', 'jaune' => 'f5f90c', 'gris' => 'c0c0c0'];

    private $pv;
    private $feuille;
    private $celluleAct;

    private $bordures;

    private $bddAffaire;
    private $bddPlanning;
    private $bddInspection;

    private $rapport;
    private $affaire;
    private $odp;
    private $societeClient;
    private $client;

    private $receveur;
    private $analyste;

    private $typeControle;
    private $discipline;

    private $constatations;
    private $conclusions;

    private $appareils;

    private $reservoir;

    private $titre;

    function __construct($pv) {
        parent::__construct();
        $this->pv = $pv;

        $this->creerFeuille();
    }

    function creerFeuille() {
        $this->setActiveSheetIndex(0);

        $this->feuille = $this->getActiveSheet();
        $this->feuille->getProtection()->setSheet(true);

        $this->bordures = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );

        $this->recupBDD();
        $this->titre = "SCO" . explode(" ", $this->affaire['num_affaire'])[1] . '-' . $this->discipline['code'] . '-' . $this->typeControle['code'] . '-' . sprintf("%03d", $this->pv['num_ordre']);

        $this->dimensionnerColonnes();
        $this->remplirFeuille();
    }

    function recupBDD() {
        $this->bddAffaire = connexion('portail_gestion');
        $this->bddPlanning = connexion('planning');
        $this->bddInspection = connexion('inspections');

        $this->rapport = selectRapportParId($this->bddAffaire, $this->pv['id_rapport'])->fetch();
        $this->affaire = selectAffaireParId($this->bddAffaire, $this->rapport['id_affaire'])->fetch();
        $this->odp = selectODPParId($this->bddAffaire, $this->affaire['id_odp'])->fetch();
        $this->societeClient = selectSocieteParId($this->bddAffaire, $this->affaire['id_societe'])->fetch();
        $this->client = selectClientParId($this->bddAffaire, $this->odp['id_client'])->fetch();

        $this->receveur = selectUtilisateurParId($this->bddPlanning, $this->rapport['id_receveur'])->fetch();
        $this->analyste = selectUtilisateurParId($this->bddPlanning, $this->rapport['id_analyste'])->fetch();

        $this->typeControle = selectControleParId($this->bddAffaire, $this->pv['id_type_controle'])->fetch();
        $this->discipline = selectDisciplineParId($this->bddAffaire, $this->pv['id_discipline'])->fetch();

        $this->constatations = selectConstatationsParPV($this->bddAffaire, $this->pv['id_pv'])->fetchAll();
        $this->conclusions = selectConclusionsParPV($this->bddAffaire, $this->pv['id_pv'])->fetchAll();

        $this->appareils = $this->bddAffaire->query('SELECT * FROM appareils WHERE id_appareil IN (SELECT id_appareil FROM appareils_utilises WHERE id_pv_controle = ' . $this->pv['id_pv'] . ')')->fetchAll();

        $this->reservoir = selectReservoirParId($this->bddInspection, $this->pv['id_reservoir'])->fetch();
    }

    function remplirFeuille() {
        // Présentation PV
        $this->celluleAct = 1; // Cellule active
        $this->presentationPV();

        // Détails de l'affaire
        $this->celluleAct = $this->celluleAct + 2;
        $this->detailsAffaire();

        $this->celluleAct = $this->celluleAct + 2;
        colorerCellule($this, 'A' . $this->celluleAct . ':H' . $this->celluleAct, $this->couleurs['bleu']);

        // Partie documents référence
        $this->celluleAct = $this->celluleAct + 2;
        $this->documentsReference();

        // Partie situation de contrôle
        $this->celluleAct = $this->celluleAct + 2;
        $this->situationControle();

        // Partie matériel utilisé
        $this->celluleAct = $this->celluleAct + 2;
        $this->materielUtilise();

        // Partie constatations
        $this->celluleAct = $this->celluleAct + 2;
        $this->constatations();

        // Partie conclusions
        $this->celluleAct = $this->celluleAct + 2;
        $this->conclusions();

        // Partie signatures
        $this->celluleAct = $this->celluleAct + 2;
        $this->signatures();
    }

    /**
     * Ecrit l'entête du PV comprenant les coordonnées de la société ainsi que le code du PV.
     */
    function presentationPV() {
        remplirCellules($this->feuille, 'G' . $this->celluleAct, 'H' . $this->celluleAct, "Sarl SCOPEO");

        $this->celluleAct++;
        remplirCellules($this->feuille, 'G' . $this->celluleAct, 'H' . $this->celluleAct, "Route du Hoc");

        $this->celluleAct++;
        remplirCellules($this->feuille, 'G' . $this->celluleAct, 'H' . $this->celluleAct, "76600 Le Havre");

        $this->celluleAct++;
        remplirCellules($this->feuille, 'G' . $this->celluleAct, 'H' . $this->celluleAct, "Tél : 02.35.30.11.30");

        $this->celluleAct++;
        remplirCellules($this->feuille, 'G' . $this->celluleAct, 'H' . $this->celluleAct, "Fax : 02.35.26.12.06");

        $this->celluleAct++;
        remplirCellules($this->feuille, 'A' . $this->celluleAct, 'B' . $this->celluleAct, "Procès Verbal");
        $this->feuille->getCell('A' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        remplirCellules($this->feuille, 'D' . $this->celluleAct, 'E' . $this->celluleAct, "Inspection & contrôle");
        $this->feuille->getCell('E' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        remplirCellules($this->feuille, 'G' . $this->celluleAct, 'H' . $this->celluleAct, $this->titre);
        $this->feuille->getCell('I' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        colorerCellule($this, 'A' . $this->celluleAct . ':H' . $this->celluleAct, $this->couleurs['bleu']); // Bleu
    }

    /**
     * Ecrit les détails de l'affaire.
     */
    function detailsAffaire() {
        remplirCellules($this->feuille, 'A' . $this->celluleAct, 'H' . $this->celluleAct, "Détails de l'affaire");
        $this->feuille->getCell('A' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        colorerCellule($this, 'A' . $this->celluleAct, $this->couleurs['gris']); // Gris

        // Clients + Numéro équipement
        $this->creerLigneInfos("Clients : ", $this->societeClient['nom_societe'], "Numéro équipement : ", $this->reservoir['designation'] . ' ' . $this->reservoir['type']);

        // Personne rencontrée + Diamètre
        $this->creerLigneInfos("Personne rencontrée : ", $this->client['nom'], "Diamètre équipement : ", ($this->reservoir['diametre'] / 1000) . ' m');

        // Num commande + Hauteur
        $this->creerLigneInfos("Numéro commande client : ", $this->affaire['commande'], "Hauteur : ", ($this->reservoir['hauteur'] / 1000) . ' m');

        // Lieu + Hauteur produit
        $this->creerLigneInfos("Lieu : ", $this->affaire['lieu_intervention'], "Hauteur produit : ", ($this->reservoir['hauteur_produit'] / 1000) . ' m');

        // Début contrôle + Volume
        $this->creerLigneInfos("Début du contrôle : ", conversionDate($this->pv['date_debut']), "Volume : ", ($this->reservoir['volume'] / 1000) . ' m');

        // Nombre génératrices + Distance entre 2 points
        $this->creerLigneInfos("Nbre génératrices : ", $this->reservoir['nb_generatrices'], "Distance entre 2 points : ", ($this->reservoir['distance_points'] / 1000) . ' m');
    }

    /**
     * Ecrit la partie concernant les documents de référence.
     */
    function documentsReference() {
        remplirCellules($this->feuille, 'A' . $this->celluleAct, 'H' . $this->celluleAct, "Documents de référence : ");
        $this->feuille->getCell('A' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        colorerCellule($this, 'A' . $this->celluleAct, $this->couleurs['gris']); // Gris

        $this->creerLigneInfos("Suivant procédure : ", $this->rapport['procedure_controle'], "Code d'interprétation : ", $this->rapport['code_inter']);
    }

    /**
     * Ecrit les informations relatives aux situations de contrôle effectués.
     */
    function situationControle() {
        remplirCellules($this->feuille, 'A' . $this->celluleAct, 'H' . $this->celluleAct, "Situation de contrôle : ");
        $this->feuille->getCell('A' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        colorerCellule($this, 'A' . $this->celluleAct, $this->couleurs['gris']); // Gris

        $this->celluleAct++;

        creerChamp($this->feuille, $this->celluleAct, 'A', 'B', "Contrôle interne ? ", 'C', 'D', ($this->pv['controle_interne'] == 1 ? "OUI" : "NON"));
        creerChamp($this->feuille, $this->celluleAct, 'E', 'F', "Contrôle externe ? ", 'G', 'H', ($this->pv['controle_externe'] == 1 ? "OUI" : "NON"));

        $this->feuille->getStyle('A' . $this->celluleAct . ':H' . $this->celluleAct)->applyFromArray($this->bordures);
        colorerCellule($this, 'C' . $this->celluleAct, $this->couleurs['gris']);
        colorerCellule($this, 'G' . $this->celluleAct, $this->couleurs['gris']);

        $this->celluleAct++;
        creerChamp($this->feuille, $this->celluleAct, 'A', 'B', "Contrôle périphérique ? ", 'C', 'D', ($this->pv['controle_peripherique'] == 1 ? "OUI" : "NON"));

        $this->feuille->getStyle('A' . $this->celluleAct . ':D' . $this->celluleAct)->applyFromArray($this->bordures);
        colorerCellule($this, 'C' . $this->celluleAct, $this->couleurs['gris']);
    }

    /**
     * Ecrit les informations relatives au matériel utilisé pour le contrôle.
     */
    function materielUtilise() {
        remplirCellules($this->feuille, 'A' . $this->celluleAct, 'H' . $this->celluleAct, "Matériel utilisé");
        $this->feuille->getCell('A' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        colorerCellule($this, 'A' . $this->celluleAct, $this->couleurs['gris']); // Gris

        for ($i = 0; $i < sizeof($this->appareils); $i++) {
            $this->creerLigneAppareil($i);
        }
    }

    /**
     * Représente la partie où l'opérateur indique ses observations et constatations.
     */
    function constatations() {
        remplirCellules($this->feuille, 'A' . $this->celluleAct, 'H' . $this->celluleAct, "Constatations");
        $this->feuille->getCell('A' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        colorerCellule($this, 'A' . $this->celluleAct, $this->couleurs['gris']); // Gris

        $this->celluleAct++;

        for ($i = 0; $i < sizeof($this->constatations); $i++) {
            if ($this->constatations[$i]['type_constatation'] != null) {
                $this->feuille->setCellValue('A' . $this->celluleAct, ($i + 1) . ') ' . $this->constatations[$i]['type_constatation']);
                $this->celluleAct++;
            }
            $this->feuille->setCellValue('A' . $this->celluleAct, $this->constatations[$i]['constatation']);
            $celluleAct = $this->celluleAct + 2;
        }
    }

    /**
     * Représente la partie où sont inscrites les conclusions du contrôle.
     */
    function conclusions() {
        remplirCellules($this->feuille, 'A' . $this->celluleAct, 'H' . $this->celluleAct, "Conclusions");
        $this->feuille->getCell('A' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        colorerCellule($this, 'A' . $this->celluleAct, $this->couleurs['gris']); // Gris

        $this->celluleAct++;

        for ($i = 0; $i < sizeof($this->conclusions); $i++) {
            $this->feuille->setCellValue('A' . $this->celluleAct, $this->conclusions[$i]['conclusion']);
            $this->celluleAct = $this->celluleAct + 2;
        }

        // Boucle permettant d'aligner tous les éléments du tableur
        for ($i = 0; $i < $this->celluleAct; $i++) {
            $this->feuille->getCell('C' . $i)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->feuille->getCell('G' . $i)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }
    }


    /**
     * Représente la partie où se trouvent les signatures et la présence ou non d'annexes.
     */
    function signatures() {
        colorerCellule($this, 'A' . $this->celluleAct . ':H' . $this->celluleAct, $this->couleurs['bleu']); // Bleu

        $this->celluleAct = $this->celluleAct + 2;
        $this->feuille->getStyle('A' . $this->celluleAct . ':H' . ($this->celluleAct + 3))->applyFromArray($this->bordures);

        $this->feuille->mergeCells('C' . $this->celluleAct . ':E' . ($this->celluleAct + 3));
        $this->feuille->mergeCells('F' . $this->celluleAct . ':H' . ($this->celluleAct + 3));

        $this->feuille->setCellValue('A' . $this->celluleAct, "Date : ");
        $this->feuille->setCellValue('B' . $this->celluleAct, date("d.m.y"));
        colorerCellule($this, 'B' . $this->celluleAct, $this->couleurs['gris']);

        $this->feuille->setCellValue('C' . $this->celluleAct, "Nom et visa du contrôleur");
        $this->feuille->getCell('C' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->feuille->getCell('C' . $this->celluleAct)->getStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $this->feuille->setCellValue('F' . $this->celluleAct, "Nom et visa du vérificateur");
        $this->feuille->getCell('F' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->feuille->getCell('F' . $this->celluleAct)->getStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        $this->celluleAct++;
        $this->feuille->setCellValue('A' . $this->celluleAct, "Photos jointes : ");
        $this->feuille->setCellValue('B' . $this->celluleAct, ($this->pv['photos_jointes'] == 1 ? "OUI" : "NON"));
        colorerCellule($this, 'B' . $this->celluleAct, $this->couleurs['gris']);

        $this->celluleAct++;
        $this->feuille->setCellValue('A' . $this->celluleAct, "Pièces jointes : ");
        $this->feuille->setCellValue('B' . $this->celluleAct, ($this->pv['pieces_jointes'] == 1 ? "OUI" : "NON"));
        colorerCellule($this, 'B' . $this->celluleAct, $this->couleurs['gris']);

        $this->celluleAct++;
        $this->feuille->setCellValue('A' . $this->celluleAct, "Nombre d'annexes : ");
        $this->feuille->setCellValue('B' . $this->celluleAct, $this->pv['nb_annexes']);
        colorerCellule($this, 'B' . $this->celluleAct, $this->couleurs['gris']);
    }


    /**
     * Redimensionne les colonnes de la feuille.
     */
    function dimensionnerColonnes() {
        $this->feuille->getColumnDimension('A')->setWidth(19);
        $this->feuille->getColumnDimension('B')->setWidth(9);
        $this->feuille->getColumnDimension('C')->setWidth(8);
        $this->feuille->getColumnDimension('D')->setWidth(8);
        $this->feuille->getColumnDimension('E')->setWidth(13);
        $this->feuille->getColumnDimension('F')->setWidth(10);
        $this->feuille->getColumnDimension('G')->setWidth(10);
        $this->feuille->getColumnDimension('H')->setWidth(10);
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
        $this->celluleAct++;
        creerChamp($this->feuille, $this->celluleAct, 'A', 'B', $enonce1, 'C', 'D', $valeur1);
        creerChamp($this->feuille, $this->celluleAct, 'E', 'F', $enonce2, 'G', 'H', $valeur2);

        $this->feuille->getStyle('A' . $this->celluleAct . ':H' . $this->celluleAct)->applyFromArray($this->bordures);
        colorerCellule($this, 'C' . $this->celluleAct, $this->couleurs['gris']);
        colorerCellule($this, 'G' . $this->celluleAct, $this->couleurs['gris']);
    }

    /**
     * Crée une ligne à ajouter dans le tableur comprenant les différentes informations de l'appareil à l'indice i.
     *
     * @param int $ind Indice de l'appareil à afficher.
     */
    function creerLigneAppareil($ind) {
        $this->celluleAct++;

        creerChamp($this->feuille, $this->celluleAct, 'A', 'B', "Système : ", 'C', 'D', $this->appareils[$ind]['systeme']);
        creerChamp($this->feuille, $this->celluleAct, 'E', 'F', "Marque : ", 'G', 'H', $this->appareils[$ind]['marque']);

        $this->feuille->getStyle('A' . $this->celluleAct . ':H' . $this->celluleAct)->applyFromArray($this->bordures);
        colorerCellule($this, 'C' . $this->celluleAct, $this->couleurs['gris']);
        colorerCellule($this, 'G' . $this->celluleAct, $this->couleurs['gris']);

        $this->celluleAct++;

        creerChamp($this->feuille, $this->celluleAct, 'A', 'B', "Type : ", 'C', 'D', $this->appareils[$ind]['type']);
        creerChamp($this->feuille, $this->celluleAct, 'E', 'F', "N° de série : ", 'G', 'H', $this->appareils[$ind]['num_serie']);

        $this->feuille->getStyle('A' . $this->celluleAct . ':H' . $this->celluleAct)->applyFromArray($this->bordures);
        colorerCellule($this, 'C' . $this->celluleAct, $this->couleurs['gris']);
        colorerCellule($this, 'G' . $this->celluleAct, $this->couleurs['gris']);

        $this->celluleAct++;
        creerChamp($this->feuille, $this->celluleAct, 'A', 'B', "Date de calibration : ", 'C', 'D', $this->appareils[$ind]['date_calib']);
        creerChamp($this->feuille, $this->celluleAct, 'E', 'F', "Date de validation : ", 'G', 'H', $this->appareils[$ind]['date_valid']);
        $this->feuille->getStyle('A' . $this->celluleAct . ':H' . $this->celluleAct)->applyFromArray($this->bordures);
        colorerCellule($this, 'C' . $this->celluleAct, $this->couleurs['gris']);
        colorerCellule($this, 'G' . $this->celluleAct, $this->couleurs['gris']);

        $this->celluleAct++;
    }


    /**
     * Sauvegarde le fichier Excel sur le serveur et retourne le nom du fichier crée.
     *
     * @return string Le chemin du fichier généré.
     */
    function sauvegarde() {
        $rep = '../documents/PV_Excel/' . explode("-", $this->titre)[0] . '/';
        $cheminFichier = $rep . $this->titre . '.xlsx';

        $this->feuille->setTitle($this->titre);
        $writer = PHPExcel_IOFactory::createWriter($this, 'Excel2007');

        if (!is_dir($rep))
            mkdir($rep);

        try {
            $writer->save($cheminFichier);
            update($this->bddAffaire, "pv_controle", "chemin_excel", $this->bddAffaire->quote($cheminFichier), "id_pv", "=", $this->pv['id_pv']);
        } catch (PHPExcel_Writer_Exception $e) {
            header('Location: /gestionPV/index.php?erreur=1');
            exit;
        }

        return $cheminFichier;
    }

    /**
     * Télécharge le fichier crée.
     *
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

    /**
     * @return mixed Les informations du PV converti.
     */
    function getPV() {
        return $this->pv;
    }
}