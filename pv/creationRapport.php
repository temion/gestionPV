<?php
    include_once "../menu.php";
    verifSession("OP");
    enTete("Création de rapport",
                array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/creaPV.css", "../style/menu.css"),
                array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bddAffaires = connexion('portail_gestion');
    $affaires = selectAll($bddAffaires, "affaire")->fetchAll();
    $utilisateurs = selectAll($bddAffaires, "utilisateurs")->fetchAll();

    if (isset($_GET['num_affaire'])) {
        $affaireSelectionnee = selectAllFromWhere($bddAffaires, "affaire", "num_affaire", "like", $_GET['num_affaire'])->fetch();
        if ($affaireSelectionnee['id_societe'] != "") {
            $societe = selectAllFromWhere($bddAffaires, "societe", "id_societe", "=", $affaireSelectionnee['id_societe'])->fetch();
            $odp = selectAllFromWhere($bddAffaires, "odp", "id_odp", "=", $affaireSelectionnee['id_odp'])->fetch();
            $personneRencontree = selectAllFromWhere($bddAffaires, "client", "id_client", "=", $odp['id_client'])->fetch();
            $numCommande = 0;
            $dateDebut = 0;
        }
    }

    $bddEquipement = connexion('theodolite');

    if (isset($_GET['num_affaire']) && $_GET['num_affaire'] != "")
        $equipement = selectAllFromWhere($bddEquipement, "equipement", "idSociete", "=", $societe['id_societe'])->fetchAll();
    else
        $equipement = selectAll($bddEquipement, "equipement")->fetchAll();


    if (isset($_GET['num_equipement'])) {
        $equipementSelectionne = selectAllFromWhere($bddEquipement, "equipement", "concat(Designation, ' ', Type)", "like", $_GET['num_equipement'])->fetch();
        if ($equipementSelectionne['idEquipement'] != "")
            $ficheTechniqueEquipement = selectAllFromWhere($bddEquipement, "fichetechniqueequipement", "idEquipement", "=", $equipementSelectionne['idEquipement'])->fetch();
    }

    $typeControles = selectAll($bddAffaires, "type_controle")->fetchAll();
?>

        <div id="contenu">

            <h1 id="titreMenu" class="ui blue center aligned huge header">Création d'un rapport</h1>
            <?php
                afficherMessage('erreur', "Erreur", "Veuillez remplir tous les champs précédés par un astérisque.", "", "");
            ?>

            <form method="get" action="infosRapport.php">
                <table>
                    <thead>
                        <tr>
                            <th colspan="2"><h4 class="ui dividing header">Affaire & équipement</h4></th>
                            <th colspan="2"><h4 class="ui dividing header">Responsable de l'affaire</h4></th>
                        </tr>
                    </thead>

                    <tr>
                        <td>
                            <div class="field">
                                <label>* Numéro de l'affaire : </label>
                                <div class="field">
                                    <?php
                                        $url = "creationRapport.php?num_affaire=";
                                        if (isset($_GET['num_equipement']))
                                            $url = "creationRapport.php?num_equipement=".$_GET['num_equipement']."&num_affaire="; // Stockage de l'url pour l'aperçu du PV
                                    ?>

                                    <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="ui search dropdown" name="num_affaire">
                                        <option selected label="defaut"> </option>
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
                                        $url = "creationRapport.php?num_equipement=";
                                        if (isset($_GET['num_affaire']))
                                            $url = "creationRapport.php?num_affaire=".$_GET['num_affaire']."&num_equipement="; // Stockage de l'url pour l'aperçu du PV
                                    ?>

                                    <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="ui search dropdown" name="num_equipement">
                                        <option selected label="defaut"> </option>
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
                                        <option selected label="defaut"> </option>
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
                                        <option selected label="defaut"> </option>
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
                        <th colspan="3"><h4 class="ui dividing header">Détails de l'affaire</h4></th>
                    </tr>
                    <tr>
                        <td>
                            <label class="labelCB"> Appel d'offre ? </label>
                            <input type="checkbox" name="appelOffre">
                        </td>
                        <td>
                            <label>* Obtention de l'offre </label>
                            <select class="ui search dropdown voieOffre" name="obtentionOffre">
                                <option selected label="defaut"> </option>
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
                            <h3 class="ui dividing left aligned header">Aperçu du rapport <?php if (isset($_GET['num_affaire'])) echo $affaireSelectionnee['num_affaire'] ?> </h3>
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
                                            echo ($ficheTechniqueEquipement['diametre']/1000).' m';
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
                                            echo ($ficheTechniqueEquipement['hauteurEquipement']/1000).' m';
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

                    <tr>
                        <td>

                        </td>
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
                    </tr>
                </table>

                <button class="ui right floated blue button">Valider</button>
            </form>
        </div>
    </body>
</html>