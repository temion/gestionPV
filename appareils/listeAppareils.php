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
        if (isset($_GET['pdfG']) && $_GET['pdfG'] == 1) {
            echo '<div class="ui message">';
            echo '<div class="header"> Succès ! </div>';
            echo '<p> L\'appareil a été généré avec succès ! </p>';
            echo '</div>';
        }
        ?>

        <form method="get" action="/gestionPV/appareils/modifAppareil.php">
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
                    echo '<td>'.$listeAppareils[$i]['date_valid'].'</td>';
                    echo '<td>'.$listeAppareils[$i]['date_calib'].'</td>';
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
?>