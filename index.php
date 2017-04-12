<?php
    $bddAffaires = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');

    $societes = $bddAffaires->query('select * from societe')->fetchAll();
    $client = $bddAffaires->query('select * from client')->fetchAll();

    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');
    $equipement = $bddEquipement->query('select * from equipement')->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Gestion de rapport</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css"/>
        <link rel="stylesheet" href="style/style.css"/>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"></script>
    </head>

    <body>
        <h1 class="ui blue center aligned huge header">Gestion de rapport</h1>
        <form class="ui form" method="post" action="traitementAffaire.php">
            <table>
                <tr>
                    <th colspan="2"><h4 class="ui dividing header">Détail de l'affaire</h4></th>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Clients : </label>
                            <select class="ui search dropdown" name="societe_client">
                                <?php
                                    for ($i = 0; $i < sizeof($societes); $i++) {
                                        echo '<option>'.$societes[$i]['nom_societe'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>N° Equipement : </label>
                            <div class="field">
                                <select class="ui search dropdown" name="num_equipement">
                                    <?php
                                    for ($i = 0; $i < sizeof($equipement); $i++) {
                                        echo '<option>'.$equipement[$i]['Designation'].' '.$equipement[$i]['Type'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Personne rencontrée : </label>
                            <select class="ui search dropdown" name="personne_client">
                                <?php
                                for ($i = 0; $i < sizeof($client); $i++) {
                                    echo '<option>'.$client[$i]['nom'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Diamètre : </label>
                            <div class="field">
                                <input type="text" name="diam_equipement" placeholder="Diamètre (m)">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>N° Commande client : </label>
                            <div class="field">
                                <input type="text" name="num_commande" placeholder="Numéro de commande">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Hauteur : </label>
                            <div class="field">
                                <input type="text" name="hauteur_equipement" placeholder="Hauteur (m)">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Lieu : </label>
                            <div class="field">
                                <input type="text" name="lieu" placeholder="Lieu de l'affaire">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Hauteur produit : </label>
                            <div class="field">
                                <input type="text" name="hauteur_produit" placeholder="Hauteur du produit (m)">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Début du contrôle : </label>
                            <div class="field">
                                <input type="text" name="debut_controle" placeholder="Date de début">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Volume : </label>
                            <div class="field">
                                <input type="text" name="volume_equipement" placeholder="Volume (m²)">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Nombre de génératrices : </label>
                            <div class="field">
                                <input type="text" name="nb_generatrices" placeholder="Nombre de génératrices">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Distance entre 2 points : </label>
                            <div class="field">
                                <input type="text" name="distance_points" placeholder="Distance (m)">
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <th colspan="2"><h4 class="ui dividing header">Document de référence</h4></th>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Suivant procédure : </label>
                            <div class="field">
                                <input type="text" name="procedure" placeholder="Procédure suivie">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Code d'interprétation : </label>
                            <div class="field">
                                <input type="text" name="codeInter" placeholder="Code d'interprétation">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"><button class="ui right floated blue button">Valider</button></td>
                </tr>
            </table>

            <?php
                if (isset($_GET['erreur']))
                    echo '<h2 class="ui red center aligned huge header">Veuillez remplir tous les champs</h2>';
            ?>
        </form>
    </body>
</html>
