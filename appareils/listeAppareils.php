<?php
include_once "../menu.php";
enTete("Liste des appareils",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));
$bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');

$listeAppareils = $bddAffaire->query('select * from appareils')->fetchAll();
?>

    <div id="contenu">
        <h1 class="ui blue center aligned huge header">Liste des appareils</h1>
        <?php
            if (isset($_GET['modifs']) && $_GET['modifs'] != 0) {
                echo '<div class="ui message">';
                echo '<div class="header"> Succès ! </div>';
                if ($_GET['modifs'] == 1)
                    echo '<p> La modification a été effectuée avec succès ! </p>';
                else
                    echo '<p> Les '.$_GET['modifs'].' modifications ont été effectuées avec succès ! </p>';
                echo '</div>';
            } else if (isset($_GET['modifs']) && $_GET['modifs'] == 0) {
                echo '<div class="ui message">';
                echo '<div class="header"> Erreur </div>';
                echo '<p> Aucune modification n\'a été effectué ! </p>';
                echo '</div>';
            }
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
                    echo '<tr><td>'.$listeAppareils[$i]['id_appareil'].'</td>';
                    echo '<td>'.$listeAppareils[$i]['systeme'].'</td>';
                    echo '<td>'.$listeAppareils[$i]['type'].'</td>';
                    echo '<td>'.$listeAppareils[$i]['marque'].'</td>';
                    echo '<td>'.$listeAppareils[$i]['num_serie'].'</td>';
                    echo '<td>'.conversionDate($listeAppareils[$i]['date_valid']).'</td>';
                    echo '<td>'.conversionDate($listeAppareils[$i]['date_calib']).'</td>';
                    echo '<td><button name="idAppareil" value="'.$listeAppareils[$i]['id_appareil'].'" class="ui right floated blue button">Modifier</button></td></tr>';
                }
                ?>
                </tbody>
            </table>
        </form>
    </div>
    </body>
    </html>

<?php
fonctionMenu();

function conversionDate($date) {
    if ($date != "") {
        $tab = explode("-", $date);
        return $tab[2] . '-' . $tab[1] . '-' . $tab[0];
    }

    return "";
}

?>