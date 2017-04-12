<?php
/**
 * Created by PhpStorm.
 * User: t.emion
 * Date: 12/04/2017
 * Time: 14:26
 */
    $bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $pv = $bddAffaire->query('select * from pv_controle where id_pv = '.$_GET['idPv'])->fetch();
    $affaire = $bddAffaire->query('select * from affaire where id_affaire = '.$pv['id_affaire'])->fetch();
    $societe = $bddAffaire->query('select * from societe where id_societe = '.$affaire['id_societe'])->fetch();
    $client = $bddAffaire->query('select * from client where id_client = '.$societe['ref_client'])->fetch();

    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');
    $equipement = $bddEquipement->query('select * from equipement where idEquipement = '.$pv['id_equipement'])->fetch();
    $ficheTechniqueEquipement = $bddEquipement->query('select * from fichetechniqueequipement where idEquipement = '.$pv['id_equipement'])->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Modification de PV</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css"/>
        <link rel="stylesheet" href="../style/style.css"/>
        <link rel="stylesheet" href="../style/creaPV.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"></script>
    </head>

    <body>
        <h1 class="ui blue center aligned huge header">Modification du PV </h1>

        <form class="ui form" method="post" action="listePV.php">
            <table>
                <tr>
                    <th colspan="2"><h3 class="ui right aligned header"><?php echo $affaire['num_affaire']; ?></h3></th>
                </tr>
                <tr>
                    <th colspan="2"><h4 class="ui dividing header">Détail de l'affaire</h4></th>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Clients : </label>
                            <label> <?php echo $societe['nom_societe']; ?> </label>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>N° Equipement : </label>
                            <label> <?php echo $equipement['Designation'].' '.$equipement['Type']; ?> </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Personne rencontrée : </label>
                            <label> <?php echo $client['nom']; ?> </label>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Diamètre : </label>
                            <label> <?php echo $ficheTechniqueEquipement['diametre'].' m'; ?> </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <div class="field">
                                <label>Numéro de commande client : </label>
                                <label> <?php echo $affaire['commande']; ?> </label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Hauteur : </label>
                            <label> <?php echo $ficheTechniqueEquipement['hauteurEquipement'].' m'; ?> </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Lieu : </label>
                            <label> <?php echo $affaire['lieu_intervention']; ?> </label>
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
                            <div class="field">
                                <label>Début du contrôle : </label>
                                <label> <?php echo $affaire['date_ouv']; ?> </label>
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
                            <label> <?php echo $ficheTechniqueEquipement['nbGeneratrice']; ?> </label>
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
                            <label>Suivant procédure : </label>
                            <div class="field">
                                <input type="text" name="procedure" placeholder="Procédure suivie">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"><button class="ui right floated blue button">Valider</button></td>
                </tr>
            </table>
        </form>
    </body>
</html>