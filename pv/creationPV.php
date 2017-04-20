<?php
    include_once "../menu.php";
    enTete("Création de PV",
                array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/creaPV.css", "../style/menu.css"),
                array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bddAffaires = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $affaires = $bddAffaires->query('select * from affaire order by num_affaire asc')->fetchAll();
    $utilisateurs = $bddAffaires->query('select * from utilisateurs')->fetchAll();

    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');
    $equipement = $bddEquipement->query('select * from equipement')->fetchAll();


    if (isset($_GET['num_affaire'])) {
        $affaireSelectionnee = $bddAffaires->query('select * from affaire where num_affaire like \''.$_GET['num_affaire'].'\'')->fetch();
        if ($affaireSelectionnee['id_societe'] != "") {
            $societe = $bddAffaires->query('SELECT * FROM societe WHERE id_societe = ' . $affaireSelectionnee['id_societe'])->fetch();
            $personneRencontree = $bddAffaires->query('SELECT * FROM client WHERE id_client = ' . $societe['ref_client'])->fetch();
            $numCommande = 0;
            $dateDebut = 0;
        }
    }

    if (isset($_GET['num_equipement'])) {
        $equipementSelectionne = $bddEquipement->query('select * from equipement where concat(Designation, \' \', type) LIKE \''.$_GET['num_equipement'].'\'')->fetch();
        if ($equipementSelectionne['idEquipement'] != "")
            $ficheTechniqueEquipement = $bddEquipement->query('select * from fichetechniqueequipement where idEquipement = '.$equipementSelectionne['idEquipement'])->fetch();
    }

    $typeControles = $bddAffaires->query('select * from type_controle')->fetchAll();
?>

        <div id="contenu">

            <h1 id="titreMenu" class="ui blue center aligned huge header">Création d'un PV</h1>
            <?php
                afficherMessage('erreur', "Erreur", "Veuillez remplir tous les champs précédés par un astérisque.", "", "");
            ?>

            <form method="post" action="infosPV.php">
                <table>
                    <tr>
                        <th colspan="2"><h4 class="ui dividing header">Affaire & équipement</h4></th>
                        <th colspan="2"><h4 class="ui dividing header">Responsable de l'affaire</h4></th>
                    </tr>

                    <tr>
                        <td>
                            <div class="field">
                                <label>* Numéro de l'affaire : </label>
                                <div class="field">
                                    <?php
                                        $url = "creationPV.php?num_affaire=";
                                        if (isset($_GET['num_equipement']))
                                            $url = "creationPV.php?num_equipement=".$_GET['num_equipement']."&num_affaire="; // Stockage de l'url pour l'aperçu du PV
                                    ?>

                                    <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="ui search dropdown" name="num_affaire">
                                        <option selected> </option>
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
                                <label>* Numéro de l'équipement à inspecter : </label>
                                <div class="field">
                                    <?php
                                        $url = "creationPV.php?num_equipement=";
                                        if (isset($_GET['num_affaire']))
                                            $url = "creationPV.php?num_affaire=".$_GET['num_affaire']."&num_equipement="; // Stockage de l'url pour l'aperçu du PV
                                    ?>

                                    <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="ui search dropdown" name="num_equipement">';
                                        <option selected> </option>
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
                        <td>
                            <div class="field">
                                <label>* Demande reçue par : </label>
                                <div class="field">
                                    <select class="ui search dropdown" name="demandeRecue">
                                        <option selected> </option>
                                        <?php
                                            for ($i = 0; $i < sizeof($utilisateurs); $i++) {
                                                if ($utilisateurs[$i]['nom'] != 'root')
                                                    echo '<option>'.$utilisateurs[$i]['nom'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="field">
                                <label>* Demande analysée par : </label>
                                <div class="field">
                                    <select class="ui search dropdown" name="demandeAnalysee">
                                        <option selected> </option>
                                        <?php
                                            for ($i = 0; $i < sizeof($utilisateurs); $i++) {
                                                if ($utilisateurs[$i]['nom'] != 'root')
                                                    echo '<option>'.$utilisateurs[$i]['nom'].'</option>';
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
                        <th colspan="5"><h4 class="ui dividing header">Détails de l'affaire</h4></th>
                    </tr>
                    <tr>
                        <td>
                            <label id="cbVoieOffre"> Appel d'offre ? </label>
                            <input type="checkbox" name="appelOffre">
                        </td>
                        <td>
                            <label>* Obtention de l'offre </label>
                            <select class="ui search dropdown voieOffre" name="obtentionOffre">
                                <option selected> </option>
                                <option> Oral </option>
                                <option> Mail </option>
                            </select>
                        </td>
                        <td>
                            <label>* Avenant affaire n° : </label>
                            <div class="ui input">
                                <input type="text" name="numAvenant" placeholder="Numéro avenant affaire">
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
                            <label>* Suivant procédure : </label>
                            <div class="ui input">
                                <input type="text" name="procedure" placeholder="Procédure suivie">
                            </div>
                        </td>
                        <td>
                            <div class="field">
                                <label>* Code d'interprétation : </label>
                                <div class="ui input">
                                    <input type="text" name="codeInter" placeholder="Code d'interprétation">
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

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
                                        if (isset($societe)) {
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
                                        if (isset($equipementSelectionne)) {
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
                                        if (isset($personneRencontree)) {
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
                                            if (isset($numCommande)) {
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
                                        if (isset($affaireSelectionnee)) {
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
                                            if (isset($dateDebut)) {
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

                <button class="ui right floated blue button">Valider</button>
            </form>
        </div>
    </body>
</html>
