<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Informations affaire</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css"/>
        <link rel="stylesheet" href="../style/style.css"/>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"></script>
    </head>

    <body>
        <?php
            $bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');

            $infosAffaire = $bddAffaire->query('select * from affaire where num_affaire like \''.$_POST['num_affaire'].'\'')->fetch();
            $societe = $bddAffaire->query('select * from societe where id_societe = \''.$infosAffaire['id_societe'].'\'')->fetch();
            $client = $bddAffaire->query('select * from client where id_client = '.$societe['ref_client'])->fetch();
            $responsable = $bddAffaire->query('select * from utilisateurs where id_utilisateur = '.$infosAffaire['responsable'])->fetch();
        ?>

        <table>
            <tr>
                <th colspan="4"><h4 class="ui dividing header"><?php echo $infosAffaire['libelle'] ?></h4></th>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Client : </label>
                        <?php
                            echo $societe['nom_societe'];
                        ?>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Lieu : </label>
                        <?php
                            echo $infosAffaire['lieu_intervention'];
                        ?>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Demande reçue par : </label>
                        <?php
                            echo $responsable['login'];
                        ?>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Nom client : </label>
                        <?php
                            echo $client['nom'];
                        ?>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Téléphone : </label>
                        <?php
                            echo $client['tel'];
                        ?>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Date : </label>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Heure : </label>
                    </div>
                </td>
            </tr>
        </table>

    </body>
</html>