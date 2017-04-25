<?php
    include_once "../menu.php";
    verifSession("OP");
    enTete("Ajout d'un nouvel équipement",
        array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/ajout.css", "../style/menu.css"),
        array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bdd = connexion('portail_gestion');
    $societes = $bdd->query('select * from societe')->fetchAll();
?>

<div id="contenu">
    <h1 id="titreMenu" class="ui blue center aligned huge header">Ajout d'un équipement à la base</h1>
    <?php
        afficherMessage('erreur', "Erreur", "Veuillez remplir tous les champs précédés par un astérisque avec des valeurs valides.", "", "");
        afficherMessage('ajout', "Succès !", "L'équipement a bien été ajouté à la base !", "", "");
    ?>

    <form method="post" action="verifAjoutEquipement.php">
        <table>
            <tr>
                <td>
                    <div class="field">
                        <label>* Société propriétaire : </label>
                        <select class="ui search dropdown" name="societe">
                            <option selected> </option>
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
                        <label>* Nom de l'équipement : </label>
                        <div class="ui input">
                            <input type="text" name="nom" placeholder="Nom (ex. : FB75 Toit fixe)">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <div class="field">
                        <label>Diamètre : </label>
                        <div class="ui input">
                            <input type="text" name="diametre" placeholder="Diamètre (m)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Hauteur : </label>
                        <div class="ui input">
                            <input type="text" name="hauteur" placeholder="Hauteur (m)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Hauteur du produit </label>
                        <div class="ui input">
                            <input type="text" name="hauteur_produit" placeholder="Hauteur produit (m)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Volume : </label>
                        <div class="ui input">
                            <input type="text" name="volume" placeholder="Volume (m²)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Distance entre 2 points : </label>
                        <div class="ui input">
                            <input type="text" name="distance" placeholder="Distance (m)">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <button class="ui right floated blue button">Valider</button>
    </form>
</div>
</body>
</html>
