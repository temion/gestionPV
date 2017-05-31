<?php
require_once "../menu.php";

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if (!isset($_GET['start']))
    $_GET['start'] = 1;

$nbLignes = 19; // Nombre de lignes par page

// Empèche les entrées utilisateur par requète http
while ($_GET['start'] % $nbLignes != 1) {
    $_GET['start']--;
}

enTete("Historique",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/historique.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$prepareUtilisateur = $bddPlanning->prepare('select * from utilisateurs where id_utilisateur = ?');

$valeurs = array();

$colonneTri = "";

if (isset($_GET['tri'])) {
    if (strstr($_GET['tri'], "par") != "")
        $colonneTri = "id_utilisateur";
    else if (strstr($_GET['tri'], "date") != "")
        $colonneTri = "date_activite";
    else if (strstr($_GET['tri'], "libelle") != "")
        $colonneTri = "libelle";

    if (strstr($_GET['tri'], "Asc") != "")
        $historique = selectAllAsc($bddPortailGestion, "historique_activite", $colonneTri)->fetchAll();
    else if (strstr($_GET['tri'], "Desc") != "")
        $historique = selectAllDesc($bddPortailGestion, "historique_activite", $colonneTri)->fetchAll();
} else
    $historique = selectAll($bddPortailGestion, "historique_activite")->fetchAll();

$nbAct = $bddPortailGestion->query('select count(*) from historique_activite;')->fetch()[0];
?>

<div id="contenu">
    <h1 id="titreDoc" class="ui blue center aligned huge header">Historique</h1>
    <table class="ui celled table" id="historique">
        <thead>
            <tr>
                <th>
                    <?php creerLien('Date'); ?>
                </th>
                <th>
                    <?php creerLien('Libellé'); ?>
                </th>
                <th>
                    <?php creerLien('Par'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php
            $sautLigne = 0;
            while ($sautLigne  < $_GET['start'] - 1) { // On boucle pour sauter les lignes précédant la ligne souhaitée
                $sautLigne++;
            }

            for ($i = 0; $i < $nbLignes; $i++) {
                $indice = $i + $sautLigne;
                if ($indice < $nbAct) {
                    $prepareUtilisateur->execute(array($historique[$indice]['id_utilisateur']));
                    $utilisateur = $prepareUtilisateur->fetch();
                    echo '<tr><td>' . conversionDate(explode(" ", $historique[$indice]['date_activite'])[0]) .
                         ' ' . explode(" ", $historique[$indice]['date_activite'])[1] .
                         '</td><td><a href="../' . $historique[$indice]['page_action'] . $historique[$indice]['param'] . '">' . $historique[$indice]['libelle'] . '</a></td>';

                    echo '<td>' . $utilisateur['nom'] . '</td></tr>';
                }
            }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th style="background-color: #f0f0f0;" colspan="3">
                    <div class="ui right floated pagination menu">
                        <?php

                        // Stocke le tri si il existe
                        $href = "historique.php";
                        if (isset($_GET['tri']) && $_GET['tri'] != "")
                            $href .= "?tri=".$_GET['tri']."&start=";
                        else
                            $href .= "?start=";

                        if ($_GET['start'] > 1) {
                            echo '<a href='.$href.($_GET['start'] - $nbLignes).' class="icon item">';
                            echo '<i class="left chevron icon"></i>';
                            echo '</a>';
                        }
                            $pageActuelle = ceil($_GET['start'] / $nbLignes);

                            if ($pageActuelle > 2)
                                echo '<a href='.$href.($_GET['start'] - 2*$nbLignes).' class="item">'.($pageActuelle - 2).'</a>';
                            if ($pageActuelle > 1)
                                echo '<a href='.$href.($_GET['start'] - $nbLignes).' class="item">'.($pageActuelle - 1).'</a>';

                            echo '<a class="item" id="selected">'.$pageActuelle.'</a>';

                            if ($pageActuelle < ceil($nbAct / $nbLignes))
                                echo '<a href='.$href.($_GET['start'] + $nbLignes).' class="item">'.($pageActuelle + 1).'</a>';

                            if ($pageActuelle < ceil($nbAct / $nbLignes) - 1)
                                echo '<a href='.$href.($_GET['start'] + 2*$nbLignes).' class="item">'.($pageActuelle + 2).'</a>';
                        ?>
                        <?php
                        if ($_GET['start'] + $nbLignes < $nbAct) {
                            echo '<a href='.$href.($_GET['start'] + $nbLignes).' class="icon item">';
                            echo '<i class="right chevron icon"></i>';
                            echo '</a>';
                        } ?>
                    </div>
                </th>
            </tr>
        </tfoot>
    </table>

    <div class="ui large modal" id="modalAide">
        <div class="header">Aide</div>
        <div>
            <p>
                Cette liste indique l'intégralité des activités enregistrées dans la base. Vous pouvez les trier par
                date, libellé ou utilisateur en cliquant sur les titres de colonne, et naviguer dans le tableau
                à l'aide des flèches et des numéros de page placés en bas.
            </p>
            <p>
                Vous pouvez également accéder aux informations du document concerné par un évènement en cliquant sur le
                libellé de ce dernier.
            </p>
            <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
            </button>
        </div>
    </div>
</div>

<?php
    /**
     * Crée un lien avec le texte passé en paramètre, et passant son nom en paramètre GET.
     *
     * @param string $lien Nom du lien à créer.
     */
    function creerLien($lien) {
        if ($lien == 'Libellé')
            $lienMinuscule = strtolower('Libelle');
        else
            $lienMinuscule = strtolower($lien);

        $href = $lienMinuscule . 'Asc';
        if (isset($_GET['tri']) && $_GET['tri'] == $href) {
            $href = $lienMinuscule . 'Desc';
        }

        if (isset($_GET['start']) && $_GET['start'] != "")
            $href .= '&start='.$_GET['start'];

        echo '<a href=historique.php?tri='.$href .'>'.$lien.'</a>';
    }

?>
