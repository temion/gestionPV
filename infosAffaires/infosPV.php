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
        $affaire = $bddAffaire->query('SELECT * FROM affaire WHERE num_affaire LIKE ' . $bddAffaire->quote($_POST['num_affaire']))->fetch();

        $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');
        $equipement = $bddEquipement->query('SELECT * FROM equipement WHERE concat(Designation, \' \', type) LIKE ' . $bddEquipement->quote($_POST['num_equipement']))->fetch();

        // Ajout du pv dans la base
        $bddAffaire->exec('insert into pv_controle(id_affaire, id_equipement) values('. $affaire['id_affaire'] . ' , ' . $equipement['idEquipement'] . ')');

        $controle1 = $controle2 = $controle3 = $controle4 = $controle5 = null;
        $variablesControles = array($controle1, $controle2, $controle3, $controle4, $controle5);

        $postControles = verifPost();
        $variablesControles = definirControles($variablesControles, $postControles, $bddAffaire);

        if ($variablesControles[0] != null)
            $bddAffaire->exec('update pv_controle set id_controle1 = '.$variablesControles[0]['id_type'].' where id_affaire = '.$affaire['id_affaire'].' and id_equipement = '.$equipement['idEquipement']);
        if ($variablesControles[1] != null)
            $bddAffaire->exec('update pv_controle set id_controle2 = '.$variablesControles[1]['id_type'].' where id_affaire = '.$affaire['id_affaire'].' and id_equipement = '.$equipement['idEquipement']);
        if ($variablesControles[2] != null)
            $bddAffaire->exec('update pv_controle set id_controle3 = '.$variablesControles[2]['id_type'].' where id_affaire = '.$affaire['id_affaire'].' and id_equipement = '.$equipement['idEquipement']);
        if ($variablesControles[3] != null)
            $bddAffaire->exec('update pv_controle set id_controle4 = '.$variablesControles[3]['id_type'].' where id_affaire = '.$affaire['id_affaire'].' and id_equipement = '.$equipement['idEquipement']);
        if ($variablesControles[4] != null)
            $bddAffaire->exec('update pv_controle set id_controle5 = '.$variablesControles[4]['id_type'].' where id_affaire = '.$affaire['id_affaire'].' and id_equipement = '.$equipement['idEquipement']);

        function verifPost()
        {
            $tab = array();
            if (isset($_POST['controle1']))
                $tab[0] = $_POST['controle1'];
            if (isset($_POST['controle2']))
                $tab[1] = $_POST['controle2'];
            if (isset($_POST['controle3']))
                $tab[2] = $_POST['controle3'];
            if (isset($_POST['controle4']))
                $tab[3] = $_POST['controle4'];
            if (isset($_POST['controle5']))
                $tab[4] = $_POST['controle5'];

            return $tab;
        }

        function definirControles($var, $post, $base)
        {
            for ($i = 0; $i < sizeof($post); $i++) {
                if (isset($post[$i])) {
                    $var[$i] = $base->query('SELECT * FROM type_controle WHERE concat(libelle, \' (\', code, \')\') LIKE ' . $base->quote($post[$i]))->fetch();
                }
            }

            return $var;
        }

        $pv = $bddAffaire->query('select * from pv_controle where id_affaire = '.$affaire['id_affaire'].' and id_equipement = '.$equipement['idEquipement'])->fetch();

        $affaire = $bddAffaire->query('select * from affaire where id_affaire = '.$pv['id_affaire'])->fetch();
        $societe = $bddAffaire->query('select * from societe where id_societe = '.$affaire['id_societe'])->fetch();
        $client = $bddAffaire->query('select * from client where id_client = '.$societe['ref_client'])->fetch();

        $equipement = $bddEquipement->query('select * from equipement where idEquipement = '.$pv['id_equipement'])->fetch();
        $ficheTechniqueEquipement = $bddEquipement->query('select * from fichetechniqueequipement where idEquipement = '.$pv['id_equipement'])->fetch();
    ?>

    <body>
        <h1 class="ui blue center aligned huge header">Votre PV a bien été crée !</h1>

        <form class="ui form" method="post" action="traitementAffaire.php">
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