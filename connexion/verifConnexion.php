<?php
session_start();
require_once '../util.inc.php';

/* Code d'erreurs de connexion :     */
/* 1 = Champs non remplis            */
/* 2 = Utilisateur inexistant        */
/* 3 = Combinaison login/pwd erronée */

if (!isset($_POST['login']) || !isset($_POST['mdp']) || $_POST['login'] == "" || $_POST['mdp'] == "") {
    erreur(1);
}

$login = htmlspecialchars($_POST['login']);
$mdp   = htmlspecialchars($_POST['mdp']);

$utilisateur = selectUtilisateurParLogin($bddPlanning, $login)->fetch();

if (empty($utilisateur)) {
    erreur(2);
} else {
    if (sha1($mdp) == $utilisateur['mdp']) {
        if ($utilisateur['droit'] == 0)
            $_SESSION['droit'] = "CA";
        else
            $_SESSION['droit'] = "OP";

        $_SESSION['id_connecte'] = $utilisateur['id_utilisateur'];
        $_SESSION['connexion'] = 1;
        header('Location: /gestionPV/');
        exit;
    } else
        erreur(3);
}

/**
 * Renvoie à la page de connexion, avec un code d'erreur différent selon l'entrée de l'utilisateur :
 *  1 - Champs non remplis.
 *  2 - Utilisateur inexistant.
 *  3 - Combinaison login/pwd erronée.
 *
 * @param int $codeErreur Code d'erreur.
 */
function erreur($codeErreur) {
    header('Location: connexion.php?erreur=' . $codeErreur);
    exit;
}
