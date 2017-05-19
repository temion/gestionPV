<?php
require_once "../menu.php";
verifSession("OP");
enTete("Modification d'un appareil existant",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/ajout.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$equipement = selectAllFromWhere($bddPortailGestion, "equipements", "id_equipement", "=", $_POST['idEquipement'])->fetch();
$societe = selectSocieteParId($bddPortailGestion, $equipement['id_societe'])->fetch();;
$societes = selectAll($bddPortailGestion, "societe")->fetchAll();
?>

<div id="contenu">
    <h1 id="titreMenu" class="ui blue center aligned huge header">Modification de
        l'équipement <?php echo $equipement['nom_equipement'] . ' (' . $societe['nom_societe'] . ')' ?></h1>

    <form method="post" action="verifModifEquipement.php">
        <table id="tableauHaut" class="ui celled table">
            <thead>
            <tr>
                <th></th>
                <th> Société propriétaire</th>
                <th> Nom</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="titreLigne"> Valeurs existantes</td>

                <td>
                    <div class="field">
                        <label> <?php echo $societe['nom_societe']; ?> </label>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label> <?php echo $equipement['nom_equipement']; ?> </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="titreLigne"> Nouvelles valeurs</td>

                <td>
                    <div class="field">
                        <label>Société propriétaire : </label>
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
                        <label>Nom de l'équipement : </label>
                        <div class="ui input">
                            <input type="text" name="nom_equipement" placeholder="Nom (ex. : FB75 Toit fixe)">
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <table id="tableauBas" class="ui celled table">
            <thead>
            <tr>
                <th></th>
                <th> Diamètre</th>
                <th> Hauteur</th>
                <th> Hauteur produit</th>
                <th> Volume</th>
                <th> Distance entre 2 points</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="titreLigne"> Valeurs existantes</td>
                <td>
                    <label> <?php echo($equipement['diametre'] != "" ? $equipement['diametre'] . ' m' : ""); ?> </label>
                </td>
                <td>
                    <label> <?php echo($equipement['hauteur'] != "" ? $equipement['hauteur'] . ' m' : ""); ?> </label>
                </td>
                <td>
                    <label> <?php echo($equipement['hauteur_produit'] != "" ? $equipement['hauteur_produit'] . ' m' : ""); ?> </label>
                </td>
                <td>
                    <label> <?php echo($equipement['volume'] != "" ? $equipement['volume'] . ' m²' : ""); ?> </label>
                </td>
                <td>
                    <label> <?php echo($equipement['distance_points'] != "" ? $equipement['distance_points'] . ' m' : ""); ?> </label>
                </td>
            </tr>
            <tr>
                <td class="titreLigne"> Nouvelles valeurs</td>

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
                            <input type="text" name="distance_points" placeholder="Distance (m)">
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <?php echo '<button name="idEquipement" value="' . $_POST['idEquipement'] . '" class="ui right floated blue button"> Valider </button>'; ?>
    </form>
</div>
</body>
</html>
