<?php
    $bdd = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');

    $affaires = $bdd->query('select distinct num_affaire from affaire')->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Choix d'affaire</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css"/>
        <link rel="stylesheet" href="../style/style.css"/>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"></script>
    </head>

    <body>
        <form method="post" action="infosAffaire.php">
            <label>N° Affaire : </label>
            <select class="ui search dropdown" name="num_affaire">
                <?php
                    for ($i = 0; $i < sizeof($affaires); $i++) {
                        echo '<option>'.$affaires[$i].'</option>';
                    }
                ?>
            </select>
            <button class="ui right floated blue button">Valider</button>
        </form>
    </body>
</html>