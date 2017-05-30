<?php
require_once "../menu.php";

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

enTete("Historique",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/historique.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$prepareUtilisateur = $bddPlanning->prepare('select * from utilisateurs where id_utilisateur = ?');
$historique = selectAll($bddPortailGestion, "historique_activite")->fetchAll();
?>

<div id="contenu">
    <h1 id="titreDoc" class="ui blue center aligned huge header">Historique</h1>
    <table style="width: auto; margin: auto;" class="ui celled table" id="historique">
        <thead>
            <tr>
                <th>
                    Date
                </th>
                <th>
                    Libellé
                </th>
                <th>
                    Par
                </th>
            </tr>
        </thead>
        <tbody>
        <?php
            for ($i = 0; $i < sizeof($historique); $i++) {
                $prepareUtilisateur->execute(array($historique[$i]['id_utilisateur']));
                $utilisateur = $prepareUtilisateur->fetch();
                echo '<tr><td>' . $historique[$i]['date_activite'] . '</td><td><a href="' . $historique[$i]['page_action'] . $historique[$i]['param'] . '">' . $historique[$i]['libelle'].'</a></td>';
                echo '<td>'.$utilisateur['nom'].'</td></tr>';
            }
        ?>
        </tbody>
    </table>
</div>
