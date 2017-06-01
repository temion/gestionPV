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
    <?php
        if (sizeof($archives) > 0) {
            ?>
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
                    echo '<tr><td>' . $archives[$i]['annee_activites'] . '</td>';
                    echo '<td>' . $archives[$i]['nb_activites'] . '</td>';
                    echo '<td><form method="post" action="archives.php">';
                    echo '<button name="idArchive" value="' . $archives[$i]['id_archive'] . '" class="ui blue button">Télécharger au format Excel</button></form></td></tr>';
                }
                ?>
                </tbody>
            </table>
            <?php
        } else {
    ?>
            <div class="ui message">
                <div class="header">
                    Aucune archive disponible !
                </div>
                <p>
                    Pour le moment, aucune archivage des activités n'a été effectué. Un archivage de la base s'effectue
                    automatiquement chaque année. Vous pourrez sur cette page télécharger les fichiers Excel comportant
                    les informations des activités effectuées lors des années précédentes.
                </p>
            </div>
    <?php
        }
    ?>

    <div class="ui large modal" id="modalAide">
        <div class="header">Aide</div>
        <div>
            <p>
                Cette liste indique les archives disponibles des activités effectuées sur ce portail les années précédentes.
                En cliquant sur "Télécharger", vous obtiendrez un fichier Excel contenant le résumé des activités effectuées
                durant l'année souhaitée.
            </p>
            <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK </button>
        </div>
    </div>
</div>