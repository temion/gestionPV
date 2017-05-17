<?php
require_once '../menu.php';
enTete("Connexion au portail de gestion de PV",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/menu.css", "../style/index.css", "../style/connexion.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

?>

<div id="contenu">
    <form method="post" action="verifConnexion.php">
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
                <td><button class="ui blue right floated button">Valider</button></td>
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
    </form>
</div>
