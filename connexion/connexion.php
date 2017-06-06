<?php
session_start();
session_destroy();
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title> Connexion au portail de gestion de PV </title>

    <link rel="icon" href="/gestionPV/images/logo_scopeo.ico"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css"/>
    <link rel="stylesheet" href="../style/connexion.css"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"></script>
</head>

<body>
<div id="header">
    <img src="../images/scopeo.png">
</div>
<div id="contenu">
    <h1 id="titre">Connexion au portail de gestion de PV</h1>
    <form method="post" action="verifConnexion.php">
        <div id="bordures">
            <table>
                <tr>
                    <td>
                        <div class="field">
                            <label>Login : </label>
                        </div>
                    </td>
                    <td>
                        <div class="ui input">
                            <input type="text" name="login" placeholder="Login">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="field">
                            <label>Mot de passe : </label>
                        </div>
                    </td>
                    <td>
                        <div class="ui input">
                            <input type="password" name="mdp" placeholder="">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button class="ui blue right floated button">Valider</button>
                    </td>
                </tr>
                <?php if (isset($_GET['erreur']) && $_GET['erreur'] != "") { ?>
                    <tr>
                        <td colspan="2">
                            <div class="ui message">
                                <div class="header">
                                    Erreur !
                                </div>
                                <p>
                                    <?php
                                    if ($_GET['erreur'] == 1) {
                                        $erreur = "Tous les champs n'ont pas été remplis.";
                                    } else if ($_GET['erreur'] == 2) {
                                        $erreur = "L'utilisateur rentré n'existe pas.";
                                    } else {
                                        $erreur = "La combinaison login/mdp est incorrecte.";
                                    }

                                    echo $erreur;
                                    ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </form>
<!--    <div style="text-align: center; margin-top: 2vh">-->
<!--        (Identifiants test : op (droit opérateur)/ca (droit chargé d'affaires) -- mdp)-->
<!--    </div>-->
</div>
</body>
</html>
