<?php
require_once "../util.inc.php";
require_once "excelUtil.inc.php";

class ConvertisseurRapport extends PHPExcel {

    private $couleurs = ['bleu' => '426bf4', 'jaune' => 'f5f90c', 'gris' => 'c0c0c0'];

    private $rapport;
    private $feuille;
    private $celluleAct;

    private $bdd;
    private $bddPlanning;

    private $affaire;
    private $odp;
    private $societeClient;
    private $client;
    private $receveur;
    private $analyste;

    private $listePV;

    private $prep_controle;
    private $prep_discipline;
    private $prep_avancement;

    private $titre;

    private $bordures;

    function __construct($rapport) {
        parent::__construct();
        $this->rapport = $rapport;

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
        $this->titre = 'SCO' . explode(" ", $this->affaire['num_affaire'])[1];

        $this->dimensionnerColonnes();
        $this->remplirFeuille();
    }

    function recupBDD() {
        $this->bdd = connexion('portail_gestion');
        $this->bddPlanning = connexion('planning');

        $this->affaire = selectAffaireParId($this->bdd, $this->rapport['id_affaire'])->fetch();
        $this->odp = selectODPParId($this->bdd, $this->affaire['id_odp'])->fetch();
        $this->societeClient = selectSocieteParId($this->bdd, $this->affaire['id_societe'])->fetch();
        $this->client = selectClientParId($this->bdd, $this->odp['id_client'])->fetch();
        $this->receveur = selectUtilisateurParId($this->bddPlanning, $this->rapport['id_receveur'])->fetch();
        $this->analyste = selectUtilisateurParId($this->bddPlanning, $this->rapport['id_analyste'])->fetch();

        $this->listePV = selectPVParRapport($this->bdd, $this->rapport['id_rapport'])->fetchAll();

        $this->prep_controle = $this->bdd->prepare('SELECT * FROM type_controle WHERE id_type = ?');
        $this->prep_discipline = $this->bdd->prepare('SELECT * FROM type_discipline WHERE id_discipline = ?');
        $this->prep_avancement = $this->bdd->prepare('SELECT * FROM avancement WHERE id_avancement = ?');
    }

    function remplirFeuille() {
        $this->celluleAct = 1;
        $this->presentationRapport();

        $this->celluleAct++;
        $this->detailsRapport();

        $this->celluleAct++;
        $this->celluleAct++;

        remplirCellules($this->feuille, 'A' . $this->celluleAct, 'B' . $this->celluleAct, "Titre : ");
        remplirCellules($this->feuille, 'C' . $this->celluleAct, 'L' . $this->celluleAct, $this->affaire['libelle']);

        colorerCellule($this, 'A' . $this->celluleAct, $this->couleurs['gris']);
        colorerCellule($this, 'C' . $this->celluleAct, $this->couleurs['jaune']);
        $this->feuille->getCell('C' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->feuille->getStyle('A' . $this->celluleAct . ':L' . $this->celluleAct)->applyFromArray($this->bordures);

        $this->celluleAct++;
        $this->celluleAct++;

        $this->creerListeLivrables();
    }

    /**
     * Ecrit l'entête du rapport comprenant les coordonnées de la société ainsi que le numéro de l'affaire.
     */
    function presentationRapport() {
        remplirCellules($this->feuille, 'K' . $this->celluleAct, 'L' . $this->celluleAct, "Sarl SCOPEO");

        $this->celluleAct++;
        remplirCellules($this->feuille, 'K' . $this->celluleAct, 'L' . $this->celluleAct, "Route du Hoc");

        $this->celluleAct++;
        remplirCellules($this->feuille, 'K' . $this->celluleAct, 'L' . $this->celluleAct, "76600 Le Havre");

        $this->celluleAct++;
        remplirCellules($this->feuille, 'K' . $this->celluleAct, 'L' . $this->celluleAct, "Tél : 02.35.30.11.30");

        $this->celluleAct++;
        remplirCellules($this->feuille, 'K' . $this->celluleAct, 'L' . $this->celluleAct, "Fax : 02.35.26.12.06");

        $this->celluleAct++;
        $this->feuille->mergeCells('A' . $this->celluleAct . ':L' . $this->celluleAct);
        $this->feuille->setCellValue('A' . $this->celluleAct, "Descriptif de l'affaire " . $this->affaire['num_affaire']);
        $this->feuille->getCell('A' . $this->celluleAct)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        colorerCellule($this, 'A' . $this->celluleAct . ':L' . $this->celluleAct, $this->couleurs['bleu']); // Bleu
        $this->feuille->getStyle('A' . $this->celluleAct . ':L' . $this->celluleAct)->applyFromArray($this->bordures);
    }

    /**
     * Ajoute sur le PDF les détails du rapport.
     */
    function detailsRapport() {
        $this->creerLigneDetails("Client : ", $this->societeClient['nom_societe'], "Lieu : ", $this->affaire['lieu_intervention'], "Demande reçue par : ", $this->receveur['nom']);
        $this->feuille->getStyle('A' . $this->celluleAct . ':L' . $this->celluleAct)->applyFromArray($this->bordures);

        $this->celluleAct++;
        $this->creerLigneDetails("Nom (Coord.) : ", $this->client['nom'], "Téléphone : ", $this->client['tel'], "Demande analysée par : ", $this->analyste['nom']);
        $this->feuille->getStyle('A' . $this->celluleAct . ':L' . $this->celluleAct)->applyFromArray($this->bordures);

        $this->celluleAct++;
        $this->creerLigneDetails("Appel d'offre ? ", ($this->rapport['appel_offre'] == 1 ? "OUI" : "NON"), "Avenant affaire n° : ", $this->rapport['avenant_affaire'], "Obtention de l'offre : ", $this->rapport['obtention']);
        $this->feuille->getStyle('A' . $this->celluleAct . ':L' . $this->celluleAct)->applyFromArray($this->bordures);

        $this->celluleAct++;
        remplirCellules($this->feuille, 'I' . $this->celluleAct, "", "Date : ");
        remplirCellules($this->feuille, 'J' . $this->celluleAct, "", conversionDate(explode(" ", $this->rapport['date'])[0]));
        $this->feuille->getStyle('I' . $this->celluleAct . ':L' . $this->celluleAct)->applyFromArray($this->bordures);

        remplirCellules($this->feuille, 'K' . $this->celluleAct, "", "Heure : ");
        remplirCellules($this->feuille, 'L' . $this->celluleAct, "", explode(" ", $this->rapport['date'])[1]);

        colorerCellule($this, 'J' . $this->celluleAct, $this->couleurs['jaune']);
        colorerCellule($this, 'L' . $this->celluleAct, $this->couleurs['jaune']);
    }

    /**
     * Crée la liste des différents livrables à effectuer.
     */
    function creerListeLivrables() {
        remplirCellules($this->feuille, 'A' . $this->celluleAct, 'B' . $this->celluleAct, "Liste des livrables : ");
        $this->feuille->getStyle('A' . $this->celluleAct . ':B' . $this->celluleAct)->applyFromArray($this->bordures);

        colorerCellule($this, 'A' . $this->celluleAct, $this->couleurs['gris']);

        remplirCellules($this->feuille, 'D' . $this->celluleAct, 'E' . $this->celluleAct, "Numéro d'affaire");
        remplirCellules($this->feuille, 'F' . $this->celluleAct, "", "Discipline");
        remplirCellules($this->feuille, 'G' . $this->celluleAct, "", "Type de contrôle");
        remplirCellules($this->feuille, 'H' . $this->celluleAct, "", "Numéro d'ordre");
        remplirCellules($this->feuille, 'I' . $this->celluleAct, "", "Début prévu le");
        remplirCellules($this->feuille, 'J' . $this->celluleAct, "", "Fin prévue le");
        remplirCellules($this->feuille, 'K' . $this->celluleAct, 'L' . $this->celluleAct, "Avancement");

        colorerCellule($this, 'D' . $this->celluleAct . ':L' . $this->celluleAct, $this->couleurs['gris']);
        $this->feuille->getStyle('D' . $this->celluleAct . ':L' . $this->celluleAct)->applyFromArray($this->bordures);

        $this->celluleAct++;
        for ($i = 0; $i < sizeof($this->listePV); $i++) {
            $this->creerInfosPV($this->listePV[$i]);
        }
    }

    /**
     * Crée une ligne contenant les informations du PV passé en paramètre.
     *
     * @param array $pv Tableau contenant les informations du PV.
     */
    function creerInfosPV($pv) {
        $this->prep_controle->execute(array($pv['id_type_controle']));
        $type = $this->prep_controle->fetch();

        $this->prep_discipline->execute(array($pv['id_discipline']));
        $discipline = $this->prep_discipline->fetch();

        $this->prep_avancement->execute(array($pv['id_avancement']));
        $avancement = $this->prep_avancement->fetch();

        remplirCellules($this->feuille, 'D' . $this->celluleAct, 'E' . $this->celluleAct, 'SCO ' . explode(" ", $this->affaire['num_affaire'])[1]);
        remplirCellules($this->feuille, 'F' . $this->celluleAct, "", $discipline['code']);
        remplirCellules($this->feuille, 'G' . $this->celluleAct, "", $type['code']);
        remplirCellules($this->feuille, 'H' . $this->celluleAct, "", $pv['num_ordre']);
        remplirCellules($this->feuille, 'I' . $this->celluleAct, "", conversionDate($pv['date_debut']));
        remplirCellules($this->feuille, 'J' . $this->celluleAct, "", conversionDate($pv['date_fin']));
        remplirCellules($this->feuille, 'K' . $this->celluleAct, 'L' . $this->celluleAct, $avancement['stade']);

        $this->feuille->getStyle('D' . $this->celluleAct . ':L' . $this->celluleAct)->applyFromArray($this->bordures);

        $this->celluleAct++;
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
        remplirCellules($this->feuille, 'A' . $this->celluleAct, 'B' . $this->celluleAct, $enonce1);
        remplirCellules($this->feuille, 'C' . $this->celluleAct, 'D' . $this->celluleAct, $valeur1);

        remplirCellules($this->feuille, 'E' . $this->celluleAct, 'F' . $this->celluleAct, $enonce2);
        remplirCellules($this->feuille, 'G' . $this->celluleAct, 'H' . $this->celluleAct, $valeur2);

        remplirCellules($this->feuille, 'I' . $this->celluleAct, 'J' . $this->celluleAct, $enonce3);
        remplirCellules($this->feuille, 'K' . $this->celluleAct, 'L' . $this->celluleAct, $valeur3);

        colorerCellule($this, 'C' . $this->celluleAct, $this->couleurs['jaune']);
        colorerCellule($this, 'G' . $this->celluleAct, $this->couleurs['jaune']);
        colorerCellule($this, 'K' . $this->celluleAct, $this->couleurs['jaune']);
    }

    function dimensionnerColonnes() {
        $this->feuille->getColumnDimension('G')->setWidth(15);
        $this->feuille->getColumnDimension('H')->setWidth(15);
        $this->feuille->getColumnDimension('I')->setWidth(15);
        $this->feuille->getColumnDimension('J')->setWidth(15);
    }

    /**
     * Sauvegarde le fichier Excel et retourne le nom du fichier crée.
     * @return string Chemin du fichier généré.
     */
    function sauvegarde() {
        $rep = '../documents/Rapports_Excel/' . $this->titre . '/';
        $cheminFichier = $rep . $this->titre . '.xlsx';

        $this->feuille->setTitle('Rapport_affaire_' . $this->titre);
        $writer = PHPExcel_IOFactory::createWriter($this, 'Excel2007');

        if (!is_dir($rep))
            mkdir($rep);

        try {
            $writer->save($cheminFichier);
        } catch (PHPExcel_Writer_Exception $e) {
            header('Location: /gestionPV/pv/listeRapportsCA.php?erreur=1');
            exit;
        }

        return $cheminFichier;
    }

    /**
     * Télécharge le fichier dont le chemin est passé en paramètre.
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

}