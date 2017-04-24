<?php
include_once "../menu.php";

enTete("Liste des appareils",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));
$bdd = connexion('portail_gestion');

$listeAppareils = $bdd->query('select * from appareils')->fetchAll();
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">Liste des appareils</h1>
            <?php
                afficherMessage('modifs', "Succès !", "Les modifications ont été effectuées avec succès.", "Erreur", "Aucune modification n'a été effectuée.");
            ?>

            <form method="post" action="/gestionPV/appareils/modifAppareil.php">
                <table class="ui celled table">
                    <thead>
                    <tr>
                        <th>Identifiant appareil</th>
                        <th>Système</th>
                        <th>Type</th>
                        <th>Marque</th>
                        <th>Numéro de série</th>
                        <th>Date de validité</th>
                        <th>Date de dernière calibration</th>
                        <th>Modification</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($i = 0; $i < sizeof($listeAppareils); $i++) {
                        creerLigneAppareil($listeAppareils, $i);
                    }
                    ?>
                    </tbody>
                </table>
            </form>
        </div>
    </body>
</html>

<?php

/**
 * Crée une ligne à ajouter dans le tableau comprenant les différentes informations de l'appareil à l'indice i.
 *
 * @param array $appareils Liste des appareils de la base.
 * @param int $ind Indice de l'appareil à afficher.
 */
function creerLigneAppareil($appareils, $ind) {
    echo '<tr><td>'.$appareils[$ind]['id_appareil'].'</td>';
    echo '<td>'.$appareils[$ind]['systeme'].'</td>';
    echo '<td>'.$appareils[$ind]['type'].'</td>';
    echo '<td>'.$appareils[$ind]['marque'].'</td>';
    echo '<td>'.$appareils[$ind]['num_serie'].'</td>';
    echo '<td>'.conversionDate($appareils[$ind]['date_valid']).'</td>';
    echo '<td>'.conversionDate($appareils[$ind]['date_calib']).'</td>';
    if (isset($_SESSION['droit']) && $_SESSION['droit'] == "OP")
        echo '<td><button disabled name="idAppareil" value="'.$appareils[$ind]['id_appareil'].'" class="ui right floated blue button">Modifier</button></td></tr>';
    else
        echo '<td><button name="idAppareil" value="'.$appareils[$ind]['id_appareil'].'" class="ui right floated blue button">Modifier</button></td></tr>';
}

?>