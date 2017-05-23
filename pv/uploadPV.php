<?php
require_once '../bdd/bdd.inc.php';
session_start();

upload($_POST['nomFichier'], $bddPortailGestion);

/**
 * Upload le fichier correspondant à la variable $_FILES[$nomFichier].
 *
 * @param string $nomFichier Nom du fichier dans la superglobale $_FILES.
 * @param PDO $bddPortailGestion Connexion à la base 'portail_gestion'.
 */
function upload($nomFichier, $bddPortailGestion) {
    echo '<h1> ' . $nomFichier . ' </h1>';
    if (isset($_FILES[$nomFichier])) {
        echo '<h1> Oui </h1>';
        $errors = array();
        $file_name = $_FILES[$nomFichier]['name'];
        $file_size = $_FILES[$nomFichier]['size'];
        $file_tmp = $_FILES[$nomFichier]['tmp_name'];
        $file_type = $_FILES[$nomFichier]['type'];
        $tmp = explode(".", $_FILES[$nomFichier]['name']);
        $file_ext = strtolower(end($tmp));

        $nomValid = "#^SCO[0-9]+-[A-Z0-9]+-[A-Z0-9]+-[0-9]+";

        $regEx = $nomValid;
        if ($nomFichier == 'pv_excel')
            $regEx .= ".xlsx$#";
        else if ($nomFichier == 'pv_pdf')
            $regEx .= ".pdf$#";
        else
            erreur();

        echo '<h1>' . $regEx . '</h1>';
        if (!preg_match($regEx, $file_name))
            erreur();

        if ($nomFichier == 'pv_excel') {
            $rep = 'PV_Excel';
            $colonne = "chemin_excel";
        } else if ($nomFichier == 'pv_pdf') {
            $rep = 'PV_PDF';
            $colonne = "chemin_pdf";
        }

        mkdir("../documents/$rep/" . explode("-", $file_name)[0]);
        $chemin = "../documents/$rep/" . explode("-", $file_name)[0] . '/' . $file_name;
        update($bddPortailGestion, "pv_controle", $colonne, $bddPortailGestion->quote($chemin), "id_pv", "=", $_POST['idPV']);
        move_uploaded_file($file_tmp, $chemin);

        header('Location: /gestionPV/pv/' . $_POST['lienRetour'] . '.php?erreurUpload=0&idPV=' . $_POST['idPV']);
        exit;
    }
}

/**
 * Retourne à la page précédente en renvoyant une erreur.
 */
function erreur() {
    header('Location: /gestionPV/pv/' . $_POST['lienRetour'] . '.php?erreurUpload=1&idPV=' . $_POST['idPV']);
    exit;
}

?>