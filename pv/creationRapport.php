<?php
require_once "../menu.php";
verifSession("OP");
enTete("Création de rapport",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/creaPV.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$bddAffaires = connexion('portail_gestion');
$affaires = $bddAffaires->query('select * from affaire where affaire.id_affaire not in (select rapports.id_affaire from rapports)')->fetchAll();    // Permet d'empécher la création de 2 rapports sur la même affaire.
$utilisateurs = selectAll($bddAffaires, "utilisateurs")->fetchAll();

if (isset($_GET['num_affaire'])) {
    $affaireSelectionnee = selectAffaireParNom($bddAffaires, $_GET['num_affaire'])->fetch();
    if ($affaireSelectionnee['id_societe'] != "") {
        $societe = selectSocieteParId($bddAffaires, $affaireSelectionnee['id_societe'])->fetch();
        $odp = selectODPParId($bddAffaires, $affaireSelectionnee['id_odp'])->fetch();
        $personneRencontree = selectClientParId($bddAffaires, $odp['id_client'])->fetch();
        $numCommande = 0;
        $dateDebut = 0;
    }
}

$typeControles = selectAll($bddAffaires, "type_controle")->fetchAll();
?>

<div id="contenu">

    <h1 id="titreMenu" class="ui blue center aligned huge header">Création d'un rapport</h1>
    <?php
    afficherMessage('erreur', "Erreur", "Veuillez remplir tous les champs précédés par un astérisque.", "", "");
    ?>

    <form method="get" action="modifRapportCA.php">
        <table>
            <thead>
            <tr>
                <th colspan="3"><h4 class="ui dividing header">Détails de l'affaire</h4></th>
            </tr>
            </thead>

            <tr>
                <td>
                    <div class="field">
                        <label>* Numéro de l'affaire : </label>
                        <div class="field">
                            <?php
                            $url = "creationRapport.php?num_affaire=";
                            ?>
                            <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)'
                                    class="ui search dropdown" name="num_affaire">
                                <option selected label="defaut"></option>
                                <?php
                                for ($i = 0; $i < sizeof($affaires); $i++) {
                                    // Garde en mémoire l'élément sélectionné
                                    if (isset($_GET['num_affaire']) && $affaires[$i]['num_affaire'] == $_GET['num_affaire'])
                                        echo '<option selected>' . $affaires[$i]['num_affaire'] . '</option>';
                                    else
                                        echo '<option>' . $affaires[$i]['num_affaire'] . '</option>';
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
                                <option selected label="defaut"></option>
                                <?php
                                for ($i = 0; $i < sizeof($utilisateurs); $i++) {
                                    if ($utilisateurs[$i]['nom'] != 'root')
                                        echo '<option>' . $utilisateurs[$i]['nom'] . '</option>';
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
                                <option selected label="defaut"></option>
                                <?php
                                for ($i = 0; $i < sizeof($utilisateurs); $i++) {
                                    if ($utilisateurs[$i]['nom'] != 'root')
                                        echo '<option>' . $utilisateurs[$i]['nom'] . '</option>';
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
                <td>
                    <label class="labelCB"> Appel d'offre ? </label>
                    <input type="checkbox" name="appelOffre">
                </td>
                <td>
                    <label>* Obtention de l'offre </label>
                    <select class="ui search dropdown voieOffre" name="obtentionOffre">
                        <option selected label="defaut"></option>
                        <option> Oral</option>
                        <option> Mail</option>
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
                    <h3 class="ui dividing left aligned header">Aperçu de
                        l'affaire <?php if (isset($_GET['num_affaire'])) echo $affaireSelectionnee['num_affaire'] ?> </h3>
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
            </tr>
        </table>

        <input type="hidden" name="ajoutRapport" value="1">
        <button class="ui right floated blue button">Valider</button>
    </form>
</div>
</body>
</html>