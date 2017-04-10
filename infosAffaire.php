<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Informations affaire</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css"/>
        <link rel="stylesheet" href="style/style.css"/>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"></script>
    </head>

    <body>
        <?php
            $bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
            $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');

            $infosAffaire = $bddAffaire->query('select * from affaire where num_affaire like \'SCOPEO '.$_POST['numAffaire'].'\'')->fetch();

            $infosSociete = $bddAffaire->query('select * from societe where id_societe = '.$infosAffaire['id_societe'])-> fetch();
            $infosOdp = $bddAffaire->query('select * from odp where id_odp = '.$infosAffaire['id_odp'])-> fetch();
            $infosClient = $bddAffaire->query('select * from client where id_client = '.$infosOdp['id_client'])-> fetch();

            $infosEquipement = $bddEquipement->query('select * from equipement where idEquipement = '.$_POST['idEquipement'])->fetch();
            $ficheTechniqueEquipement = $bddEquipement->query('select * from fichetechniqueequipement where idEquipement = '.$_POST['idEquipement'])->fetch();
            $diametreEquipement = $bddEquipement->query('select diametre from fichetechniqueequipement where idEquipement = '.$_POST['idEquipement'])->fetch();



        ?>
        <table>
            <tr>
                <td>
                    <div class="field">
                        <label>Clients : </label>
                        <div class="field">
                            <?php echo $infosSociete['nom_societe']; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>N° Equipement : </label>
                        <div class="field">
                            <?php echo $infosEquipement['Designation'].' '.$infosEquipement['Type']; ?>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div Client="field">
                        <label>Personne rencontrée : </label>
                        <div class="field">
                            <?php echo $infosClient['nom']; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Diamètre : </label>
                        <div class="field">
                            <?php echo $ficheTechniqueEquipement['diametre']; ?>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>N° Commande client : </label>
                        <div class="field">
                            <?php echo $infosAffaire['commande']; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Hauteur : </label>
                        <div class="field">
                            <?php  echo $ficheTechniqueEquipement['hauteurEquipement'] ?>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Lieu : </label>
                        <div class="field">
                            <?php echo $infosAffaire['lieu_intervention']; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Hauteur produit : </label>
                        <div class="field">

                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Début du contrôle : </label>
                        <div class="field">
                            <?php echo $infosAffaire['date_ouv']; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Volume : </label>
                        <div class="field">

                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Nombre de génératrices : </label>
                        <div class="field">
                            <?php echo $ficheTechniqueEquipement['nbGeneratrice']; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Distance entre 2 points : </label>
                        <div class="field">

                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>