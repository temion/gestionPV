<?php
require_once "../menu.php";

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

enTete("Ajout d'un nouvel équipement",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/ajout.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$societes = selectAll($bddPortailGestion, "societe", "nom_societe")->fetchAll();
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
                            <option selected></option>
                            <?php
                            for ($i = 0; $i < sizeof($societes); $i++) {
                                echo '<option>' . $societes[$i]['nom_societe'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>* Désignation de l'équipement : </label>
                        <div class="ui input">
                            <input type="text" name="designation" placeholder="Nom (ex. : Bac 3)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>* Type d'équipement : </label>
                        <div class="ui input">
                            <input type="text" name="type" placeholder="Type (ex. : Toit fixe)">
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
                            <input type="text" name="diametre" placeholder="Diamètre (mm)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Hauteur : </label>
                        <div class="ui input">
                            <input type="text" name="hauteur" placeholder="Hauteur (mm)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Hauteur du produit </label>
                        <div class="ui input">
                            <input type="text" name="hauteur_produit" placeholder="Hauteur produit (mm)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Volume : </label>
                        <div class="ui input">
                            <input type="text" name="volume" placeholder="Volume (m3)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Nombre de génératrices : </label>
                        <div class="ui input">
                            <input type="text" name="nb_generatrices" placeholder="Nombre de génératrices">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <button class="ui right floated blue button">Valider</button>
    </form>
</div>

<div class="ui large modal" id="modalAide">
    <div class="header">Aide</div>
    <div>
        <p>
            Ici, vous pouvez ajouter de nouveaux équipements à contrôler à la base. Après avoir rempli toutes les informations,
            elles seront vérifiées, et un nouvel équipement possédant les caractéristiques indiquées sera ajouté à la base.
        </p>
        <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
        </button>
    </div>
</div>
</body>
</html>
