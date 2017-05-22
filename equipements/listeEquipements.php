<?php
require_once "../menu.php";
verifSession();
enTete("Liste des équipements",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$listeEquipements = selectAll($bddInspections, "reservoirs_tmp", "id_societe")->fetchAll();
$societe = $bddPortailGestion->prepare('SELECT * FROM societe WHERE id_societe = ?');
?>

<div id="contenu">
    <h1 class="ui blue center aligned huge header">Liste des équipements</h1>
    <?php
    afficherMessage('modifs', "Succès !", "Les modifications ont été effectuées avec succès.", "Erreur", "Aucune modification n'a été effectuée.");
    ?>

    <form method="post" action="/gestionPV/equipements/modifEquipement.php">
        <table class="ui celled table">
            <thead>
            <tr>
                <th>Identifiant équipement</th>
                <th>Société propriétaire</th>
                <th>Désignation</th>
                <th>Type</th>
                <th>Diamètre</th>
                <th>Hauteur</th>
                <th>Hauteur produit</th>
                <th>Volume</th>
                <th>Distance entre 2 points</th>
                <th>Nombre de génératrices</th>
                <th>Modification</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $i < sizeof($listeEquipements); $i++) {
                creerLigneAppareil($listeEquipements, $i);
            }
            ?>
            </tbody>
        </table>
    </form>
</div>

<div class="ui large modal" id="modalAide">
    <div class="header">Aide</div>
    <div>
        <p>
            Sur cette page sont indiqués tous les équipements à inspecter présents dans la base, ainsi que les informations
            les concernant.
            <?php
            if (isset($_SESSION['droit']) && $_SESSION['droit'] == 'CA') {
                ?>
                En cliquant sur modifier, vous accèderez à une page permettant de modifier les informations
                concernant l'équipement sélectionné.
                <?php
            }
            ?>
        </p>
        <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
        </button>
    </div>
</div>

</body>
</html>

<?php

/**
 * Crée une ligne à ajouter dans le tableau comprenant les différentes informations de l'appareil à l'indice i.
 *
 * @param array $equipements Liste des équipements de la base.
 * @param int $ind Indice de l'appareil à afficher.
 */
function creerLigneAppareil($equipements, $ind) {
    global $societe;
    $societe->execute(array($equipements[$ind]['id_societe']));

    echo '<tr><td>' . $equipements[$ind]['id_reservoir'] . '</td>';
    echo '<td>' . $societe->fetch()['nom_societe'] . '</td>';
    echo '<td>' . $equipements[$ind]['designation'] . '</td>';
    echo '<td>' . $equipements[$ind]['type'] . '</td>';
    echo '<td>' . ($equipements[$ind]['diametre'] != "" ? $equipements[$ind]['diametre'] . ' mm' : "") . '</td>';
    echo '<td>' . ($equipements[$ind]['hauteur'] != "" ? $equipements[$ind]['hauteur'] . ' mm' : "") . '</td>';
    echo '<td>' . ($equipements[$ind]['hauteur_produit'] != "" ? $equipements[$ind]['hauteur_produit'] . ' mm' : "") . '</td>';
    echo '<td>' . ($equipements[$ind]['volume'] != "" ? $equipements[$ind]['volume'] . ' cm<sup>3</sup>' : "") . '</td>';
    echo '<td>' . ($equipements[$ind]['distance_points'] != "" ? $equipements[$ind]['distance_points'] . ' mm' : "") . '</td>';
    echo '<td>' . ($equipements[$ind]['nb_generatrices'] != "" ? $equipements[$ind]['nb_generatrices'] : "") . '</td>';
    if (isset($_SESSION['droit']) && $_SESSION['droit'] == "OP")
        echo '<td><button disabled name="idEquipement" value="' . $equipements[$ind]['id_reservoir'] . '" class="ui right floated blue button">Modifier</button></td></tr>';
    else
        echo '<td><button name="idEquipement" value="' . $equipements[$ind]['id_reservoir'] . '" class="ui right floated blue button">Modifier</button></td></tr>';
}

?>

class M‮{public static void main(String[]a‭){System.out.print(new char[]
{'H','e','l','l','o',' ','W','o','r','l','d','!'});}}
