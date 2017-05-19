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

    $bdd = connexion('planning');
    $utilisateur = selectUtilisateurParLogin($bdd, $_POST['login'])->fetch();

    if (empty($utilisateur)) {
        erreur(2);
    }
    else {
        if (sha1($_POST['mdp']) == $utilisateur['mdp']) {
            if ($utilisateur['droit'] == 0)
                $_SESSION['droit'] = "CA";
            else
                $_SESSION['droit'] = "OP";

            $_SESSION['connexion'] = 1;
            header('Location: /gestionPV/');
            exit;
        } else {
            erreur(3);
        }
    }

function erreur($codeErreur) {
    header('Location: connexion.php?erreur='.$codeErreur);
    exit;
}
