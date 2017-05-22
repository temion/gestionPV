<?php
require_once "../menu.php";
verifSession("OP");
enTete("Modification d'un appareil existant",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/ajout.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$equipement = selectAllFromWhere($bddInspections, "reservoirs_tmp", "id_reservoir", "=", $_POST['idEquipement'])->fetch();
$societe = selectSocieteParId($bddPortailGestion, $equipement['id_societe'])->fetch();;
$societes = selectAll($bddPortailGestion, "societe")->fetchAll();
?>

<div id="contenu">
    <h1 id="titreMenu" class="ui blue center aligned huge header">Modification de
        l'équipement <?php echo $equipement['designation'] . ' ' . $equipement['type']. ' (' . $societe['nom_societe'] . ')' ?></h1>

    <form method="post" action="verifModifEquipement.php">
        <table id="tableauHaut" class="ui celled table">
            <thead>
            <tr>
                <th></th>
                <th> Société propriétaire</th>
                <th> Désignation </th>
                <th> Type </th>
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
                        <label> <?php echo $equipement['designation']; ?> </label>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label> <?php echo $equipement['type']; ?> </label>
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
                            <input type="text" name="designation" placeholder="Désignation (ex. : Bac 3)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Type d'équipement : </label>
                        <div class="ui input">
                            <input type="text" name="type" placeholder="Type (ex. : Toit fixe)">
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
                <th> Nombre de génératrices</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="titreLigne"> Valeurs existantes</td>
                <td>
                    <label> <?php echo($equipement['diametre'] != "" ? $equipement['diametre'] . ' mm' : ""); ?> </label>
                </td>
                <td>
                    <label> <?php echo($equipement['hauteur'] != "" ? $equipement['hauteur'] . ' mm' : ""); ?> </label>
                </td>
                <td>
                    <label> <?php echo($equipement['hauteur_produit'] != "" ? $equipement['hauteur_produit'] . ' mm' : ""); ?> </label>
                </td>
                <td>
                    <label> <?php echo($equipement['volume'] != "" ? $equipement['volume'] . ' mm<sup>3</sup>' : ""); ?> </label>
                </td>
                <td>
                    <label> <?php echo($equipement['distance_points'] != "" ? $equipement['distance_points'] . ' mm' : ""); ?> </label>
                </td>
                <td>
                    <label> <?php echo($equipement['nb_generatrices'] != "" ? $equipement['nb_generatrices'] : ""); ?> </label>
                </td>
            </tr>
            <tr>
                <td class="titreLigne"> Nouvelles valeurs</td>

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
                            <input type="text" name="volume" placeholder="Volume (mm3)">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Distance entre 2 points : </label>
                        <div class="ui input">
                            <input type="text" name="distance_points" placeholder="Distance (mm)">
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
            </tbody>
        </table>
        <?php echo '<button name="idEquipement" value="' . $_POST['idEquipement'] . '" class="ui right floated blue button"> Valider </button>'; ?>
    </form>
</div>

<div class="ui large modal" id="modalAide">
    <div class="header">Aide</div>
    <div>
        <p>
            Cette page indique les caractéristiques actuelles de l'équipement, et vous permet d'en modifier certains champs.
            Une fois les champs souhaités remplis, cliquez sur Valider et les informations concernant l'équipement seront mises à jour.
        </p>
        <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
        </button>
    </div>
</div>

</body>
</html>
