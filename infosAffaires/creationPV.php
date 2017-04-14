<?php
    $bddAffaires = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');

    $affaires = $bddAffaires->query('select * from affaire order by num_affaire asc')->fetchAll();
    $equipement = $bddEquipement->query('select * from equipement')->fetchAll();

    if (isset($_GET['num_affaire'])) {
        $affaireSelectionnee = $bddAffaires->query('select * from affaire where num_affaire like \''.$_GET['num_affaire'].'\'')->fetch();
        $societe = $bddAffaires->query('select * from societe where id_societe = '.$affaireSelectionnee['id_societe'])->fetch();
        $personneRencontree = $bddAffaires->query('select * from client where id_client = '.$societe['ref_client'])->fetch();
        $numCommande = 0;
        $dateDebut = 0;
    }

    if (isset($_GET['num_equipement'])) {
        $equipementSelectionne = $bddEquipement->query('select * from equipement where concat(Designation, \' \', type) LIKE \''.$_GET['num_equipement'].'\'')->fetch();
        $ficheTechniqueEquipement = $bddEquipement->query('select * from fichetechniqueequipement where idEquipement = '.$equipementSelectionne['idEquipement'])->fetch();
    }

    $typeControles = $bddAffaires->query('select * from type_controle')->fetchAll();





?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Création de PV</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css"/>
        <link rel="stylesheet" href="../style/creaPV.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"></script>
    </head>

    <body>
        <h1 class="ui blue center aligned huge header">Création d'un PV</h1>
        <form method="post" action="infosPV.php">
            <table>
                <tr>
                    <th colspan="2"><h4 class="ui dividing header">Affaire & équipement</h4></th>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Numéro de l'affaire : </label>
                            <div class="field">
                                <?php
                                    $url = "creationPV.php?num_affaire=";
                                    if (isset($_GET['num_equipement']))
                                        $url = "creationPV.php?num_equipement=".$_GET['num_equipement']."&num_affaire="; // Stockage de l'url pour l'aperçu du PV
                                ?>

                                <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="ui search dropdown" name="num_affaire">
                                    <?php
                                        for ($i = 0; $i < sizeof($affaires); $i++) {
                                            // Garde en mémoire l'élément sélectionné
                                            if (isset($_GET['num_affaire']) && $affaires[$i]['num_affaire'] == $_GET['num_affaire'])
                                                echo '<option selected>'.$affaires[$i]['num_affaire'].'</option>';
                                            else
                                                echo '<option>'.$affaires[$i]['num_affaire'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Numéro de l'équipement à inspecter : </label>
                            <div class="field">
                                <?php
                                    $url = "creationPV.php?num_equipement=";
                                    if (isset($_GET['num_affaire']))
                                        $url = "creationPV.php?num_affaire=".$_GET['num_affaire']."&num_equipement="; // Stockage de l'url pour l'aperçu du PV
                                ?>

                                <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="ui search dropdown" name="num_equipement">';
                                    <?php
                                        for ($i = 0; $i < sizeof($equipement); $i++) {
                                            // Garde en mémoire l'élément sélectionné
                                            if (isset($_GET['num_equipement']) &&  $equipement[$i]['Designation'].' '.$equipement[$i]['Type'] == $_GET['num_equipement'])
                                                echo '<option selected>'.$equipement[$i]['Designation'].' '.$equipement[$i]['Type'].'</option>';
                                            else
                                                echo '<option>'.$equipement[$i]['Designation'].' '.$equipement[$i]['Type'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <th colspan="5"><h4 class="ui dividing header">Contrôles à effectuer</h4></th>
                </tr>

                <tr>
                    <?php
                        creerChampControle(1, $typeControles, "controle1");
                        creerChampControle(2, $typeControles, "controle2");
                        creerChampControle(3, $typeControles, "controle3");
                        creerChampControle(4, $typeControles, "controle4");
                        creerChampControle(5, $typeControles, "controle5");

                        function creerChampControle($nbControle, $controles, $nom) {
                            echo '<td><label>Contrôle n° '.$nbControle.'</label>';
                            echo '<div class="field">';
                            echo '<select class="ui search dropdown controles" name="'.$nom.'">';
                            echo '<option selected> </option>';
                            for ($i = 0; $i < sizeof($controles); $i++) {
                                echo '<option>'.$controles[$i]['libelle'].' ('.$controles[$i]['code'].')</option>';
                            }
                            echo '</select>';
                            echo '</div>';
                            echo '</td>';
                        }
                    ?>
                </tr>
            </table>
            <button class="ui right floated blue button">Valider</button>
        </form>

        <table id="apercu">
            <tr>
                <th colspan="2">
                    <h3 class="ui dividing left aligned header">Aperçu du PV <?php if (isset($_GET['num_affaire'])) echo $affaireSelectionnee['num_affaire'] ?> </h3>
                </th>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Clients : </label>
                        <label>
                            <?php
                                if (isset($_GET['num_affaire'])) {
                                    echo $societe['nom_societe'];
                                }
                            ?>
                        </label>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>N° Equipement : </label>
                        <label>
                            <?php
                                if (isset($_GET['num_equipement'])) {
                                    echo $equipementSelectionne['Designation'].' '.$equipementSelectionne['Type'];
                                }
                            ?>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Personne rencontrée : </label>
                        <label>
                            <?php
                                if (isset($_GET['num_affaire'])) {
                                    echo $personneRencontree['nom'];
                                }
                            ?>
                        </label>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Diamètre : </label>
                        <label>
                            <?php
                                if (isset($ficheTechniqueEquipement))
                                    echo $ficheTechniqueEquipement['diametre'].' m';
                            ?>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <div class="field">
                            <label>Numéro de commande client : </label>
                            <label>
                                <?php
                                    if (isset($_GET['num_affaire'])) {
                                        echo $numCommande;
                                    }
                                ?>
                            </label>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Hauteur : </label>
                        <label>
                            <?php
                                if (isset($ficheTechniqueEquipement))
                                    echo $ficheTechniqueEquipement['hauteurEquipement'].' m';
                                ?>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Lieu : </label>
                        <label>
                            <?php
                                if (isset($_GET['num_affaire'])) {
                                    echo $affaireSelectionnee['lieu_intervention'];
                                }
                            ?>
                        </label>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Hauteur produit : </label>
                        <label>
                            <?php /* ToDo */ ?>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <div class="field">
                            <label>Début du contrôle : </label>
                            <label>
                                <?php
                                    if (isset($_GET['num_affaire'])) {
                                        echo $dateDebut;
                                    }
                                ?>
                            </label>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Volume : </label>
                        <label>
                            <?php /* ToDo */ ?>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Nombre de génératrices : </label>
                        <label>
                            <?php
                                if (isset($ficheTechniqueEquipement))
                                    echo $ficheTechniqueEquipement['nbGeneratrice'];
                            ?>
                        </label>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Distance entre 2 points : </label>
                        <label>
                            <?php /* ToDo */ ?>
                        </label>
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
                            <input type="text" name="procedure" placeholder="Code d'interprétation">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>

<script>
    $(function() {
        $("select[name='controle1']").on("change", function () {
            $("select[name='controle2']").prop('disabled', false);
        });

        $("select[name='controle2']").on("change", function () {
            $("select[name='controle3']").prop('disabled', false);
        });

        $("select[name='controle3']").on("change", function () {
            $("select[name='controle4']").prop('disabled', false);
        });

        $("select[name='controle4']").on("change", function () {
            $("select[name='controle5']").prop('disabled', false);
        });
    });
</script>
