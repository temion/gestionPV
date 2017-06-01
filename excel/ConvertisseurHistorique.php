<?php
require_once "excelUtil.inc.php";

class ConvertisseurHistorique extends PHPExcel {

    private $couleurs = ['bleu' => '426bf4', 'jaune' => 'f5f90c', 'gris' => 'c0c0c0'];

    private $feuille;
    private $bordures;
    private $celluleAct;

    private $annee;
    private $historique;
    private $prepareUtilisateur;

    private $bddPortailGestion;
    private $bddPlanning;

    function __construct($annee) {
        parent::__construct();
        $this->annee = $annee;

        $this->creerFeuille();
    }

    /**
     * Crée la feuille Excel, dimensionne les colonnes, et fait appel aux méthodes de récupération
     * et d'écriture des données.
     */
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
        $this->dimensionnerColonnes();
        $this->remplirFeuille();
    }

    function recupBDD() {
        $this->bddPortailGestion = connexion('portail_gestion');
        $this->bddPlanning = connexion('planning');

        $this->historique = selectAllFromWhere($this->bddPortailGestion, 'historique_activite', 'date_activite', 'like', $this->annee.'%')->fetchAll();
        $this->prepareUtilisateur = $this->bddPlanning->prepare('select * from utilisateurs where id_utilisateur = ?');
    }

    function dimensionnerColonnes() {
        $this->feuille->getColumnDimension('A')->setWidth(25);
        $this->feuille->getColumnDimension('B')->setWidth(50);
        $this->feuille->getColumnDimension('C')->setWidth(25);
    }

    function remplirFeuille() {
        $this->celluleAct = 1;

        remplirCellules($this->feuille, 'A' . $this->celluleAct, "", "Date");
        remplirCellules($this->feuille, 'B' . $this->celluleAct, "", "Libellé");
        remplirCellules($this->feuille, 'C' . $this->celluleAct, "", "Par");

        colorerCellule($this, 'A'.$this->celluleAct.':C'.$this->celluleAct, $this->couleurs['gris']);

        for ($i = 0; $i < sizeof($this->historique); $i++) {
            $this->celluleAct++;

            $this->prepareUtilisateur->execute(array($this->historique[$i]['id_utilisateur']));
            $utilisateur = $this->prepareUtilisateur->fetch();

            remplirCellules($this->feuille, 'A' . $this->celluleAct, "", $this->historique[$i]['date_activite']);
            remplirCellules($this->feuille, 'B' . $this->celluleAct, "", $this->historique[$i]['libelle']);
            remplirCellules($this->feuille, 'C' . $this->celluleAct, "", $utilisateur['nom']);
        }

        $this->feuille->getStyle('A1:C' . $this->celluleAct)->applyFromArray($this->bordures);
    }

    function getHistorique() {
        return $this->historique;
    }

    /**
     * Sauvegarde le fichier Excel sur le serveur et retourne le nom du fichier crée.
     *
     * @return string Le chemin du fichier généré.
     */
    function sauvegarde() {
        $rep = '../documents/Histo_Excel/';
        $cheminFichier = $rep . $this->annee . '.xlsx';

        $this->feuille->setTitle($this->annee);
        $writer = PHPExcel_IOFactory::createWriter($this, 'Excel2007');

        if (!is_dir($rep))
            mkdir($rep);

        if (file_exists($cheminFichier))
            unlink($cheminFichier);

        try {
            $writer->save($cheminFichier);
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
        exit;
    }

}