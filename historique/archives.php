<?php
require_once "../menu.php";
require_once "../excel/ConvertisseurHistorique.php";

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if (isset($_POST['idArchive']) && $_POST['idArchive'] != "") {
    $c = new ConvertisseurHistorique(0);
    $archiveTelechargee = selectAllFromWhere($bddPortailGestion, 'archives_activites', 'id_archive', '=', $_POST['idArchive'])->fetch();
    $c->telecharger($archiveTelechargee['chemin_fichier']);
}

enTete("Archives des activités",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$archives = selectAll($bddPortailGestion, 'archives_activites', 'annee_activites')->fetchAll();
?>

<div id="contenu">
    <h1 id="titreDoc" class="ui blue center aligned huge header">Archives des activités</h1>
    <table class="ui celled table" id="historique">
            <thead>
                <tr>
                    <th>
                        Année de l'archive
                    </th>
                    <th>
                        Nombre d'activités
                    </th>
                    <th>
                        Télécharger
                    </th>
                </tr>
            </thead>
        <tbody>
        <?php
            for ($i = 0; $i < sizeof($archives); $i++) {
                echo '<tr><td>'.$archives[$i]['annee_activites'].'</td>';
                echo '<td>'.$archives[$i]['nb_activites'].'</td>';
                echo '<td><form method="post" action="archives.php">';
                echo '<button name="idArchive" value="'.$archives[$i]['id_archive'].'" class="ui blue button">Télécharger au format Excel</button></form></td></tr>';
            }
        ?>
        </tbody>
    </table>
</div>