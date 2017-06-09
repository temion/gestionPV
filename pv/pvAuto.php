<?php
require_once "../menu.php";

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

enTete("Programmation de PV",
       array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/pvAuto.css", "../style/menu.css"),
       array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

if (isset($_GET['activer']) && $_GET['activer'] == 1)
    if (isset($_GET['idControleAuto']) && $_GET['idControleAuto'] != "")
        update($bddPortailGestion, "controle_auto", "generation_auto", "1", "id_controle_auto", "=", $_GET['idControleAuto']);

if (isset($_GET['desactiver']) && $_GET['desactiver'] == 1)
    if (isset($_GET['idControleAuto']) && $_GET['idControleAuto'] != "")
        update($bddPortailGestion, "controle_auto", "generation_auto", "0", "id_controle_auto", "=", $_GET['idControleAuto']);

if (isset($_GET['supprimer']) && $_GET['supprimer'] == 1)
    if (isset($_GET['idControleAuto']) && $_GET['idControleAuto'] != "")
        $bddPortailGestion->exec("delete from controle_auto where id_controle_auto = ".$_GET['idControleAuto']);

if (isset($_GET['valider']) && $_GET['valider'] == 1) {
    if (valeursValides())
        insert($bddPortailGestion, "controle_auto", array("null", $_GET['idSociete'], $_GET['idReservoir'], $_GET['idControle'], $_GET['idDiscipline'], 1));
    else
        $erreur = 1;
}

// Ensemble des sociétés
$societes = selectAll($bddPortailGestion, "societe")->fetchAll();

$listeReservoirs = selectAll($bddInspections, "reservoirs_gestion_pv", "id_societe")->fetchAll();
$prepareSociete = $bddPortailGestion->prepare('select * from societe where id_societe = ?');

$controles = selectAll($bddPortailGestion, "type_controle")->fetchAll();
$disciplines = selectAll($bddPortailGestion, "type_discipline")->fetchAll();

$prepareControleAuto = $bddPortailGestion->prepare('select * from controle_auto where id_societe = ?');
$prepareReservoir = $bddInspections->prepare('select * from reservoirs_gestion_pv where id_reservoir = ?');
$prepareControle = $bddPortailGestion->prepare('select * from type_controle where id_type = ?');
$prepareDiscipline = $bddPortailGestion->prepare('select * from type_discipline where id_discipline = ?');
?>

<div id="contenu">
    <h1 class="ui blue center aligned huge header">Programmation de PV</h1>
    <?php
        if (isset($erreur) && $erreur == 1) {
            ?>
            <table>
                <tr style="width: 50%">
                    <td style="width: inherit;">
                       <div class="ui message">
                           <div class="header">Valeurs incorrectes !</div>
                           <p id="infosAction">Veuillez remplir tous les champs présents.</p>
                       </div>
                    </td>
                </tr>
            </table>
            <?php
        }
    ?>
    <form action="pvAuto.php" method="get">
        <table>
            <tr>
                <td>
                    <label class="desc" for="idSociete">Entreprise concernée par le PV : </label>
                    <select onchange="document.location='pvAuto.php?idSociete='.concat(this.options[this.selectedIndex].value)" class="ui search dropdown listeAjout" name="idSociete">
                        <option selected></option>
                        <?php
                        for ($i = 0; $i < sizeof($societes); $i++) {
                            // Garde en mémoire l'élément sélectionné
                            if (isset($_GET['idSociete']) && $societes[$i]['id_societe'] == $_GET['idSociete'])
                                echo '<option value="'.$societes[$i]['id_societe'].'" selected>' . $societes[$i]['nom_societe'] . '</option>';
                            else
                                echo '<option value="'.$societes[$i]['id_societe'].'">' . $societes[$i]['nom_societe'] . '</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <label  class="desc" for="appareil">Réservoir à contrôler : </label>
                    <select class="ui search dropdown listeAjout" name="idReservoir">
                        <option selected></option>
                        <?php
                        for ($i = 0; $i < sizeof($listeReservoirs); $i++) {
                            $prepareSociete->execute(array($listeReservoirs[$i]['id_societe']));
                            $nomSociete = trim($prepareSociete->fetch()['nom_societe']);
                            echo '<option value="' . $listeReservoirs[$i]['id_reservoir'] . '">' . $nomSociete . ' : ' . $listeReservoirs[$i]['designation'] . ' ' . $listeReservoirs[$i]['type'] .'</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <label class="desc" for="appareil">Contrôle à effectuer : </label>
                    <select class="ui search dropdown listeAjout" name="idControle">
                        <option selected></option>
                        <?php
                        for ($i = 0; $i < sizeof($controles); $i++) {
                            echo '<option value="' . $controles[$i]['id_type'] . '">' . $controles[$i]['libelle'] . ' (' . $controles[$i]['code'] . ')</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <label class="desc" for="discipline">Discipline du PV : </label>
                    <select class="ui search dropdown listeAjout" name="idDiscipline">
                        <option selected></option>
                        <?php
                        for ($i = 0; $i < sizeof($disciplines); $i++) {
                            echo '<option value="' . $disciplines[$i]['id_discipline'] . '">' . $disciplines[$i]['libelle'] . ' (' . $disciplines[$i]['code'] . ')</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <button name="valider" value="1" class="ui blue button">Créer ce PV</button>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_GET['idSociete']) && $_GET['idSociete'] != "") {
        $prepareSociete->closeCursor();
        $prepareSociete->execute(array($_GET['idSociete']));
        $societe = $prepareSociete->fetch();

        $prepareControleAuto->execute(array($societe['id_societe']));
        $controlesAuto = $prepareControleAuto->fetchAll();
        if (sizeof($controlesAuto) > 0) {
        ?>
        <table class="ui celled table">
            <thead>
            <tr>
                <th style="text-align: center" colspan="5">PV programmés pour les affaires impliquant la société <?php echo $societe['nom_societe']; ?></th>
            </tr>
            <tr>
                <th>
                    Équipement à contrôler
                </th>
                <th>
                    Contrôle à effectuer
                </th>
                <th>
                    Discipline du contrôle
                </th>
                <th>
                    Activer/Désactiver la génération automatique
                </th>
                <th>
                    Supprimer la programmation
                </th>
            </tr>
            </thead>
            <tbody>

            <?php
                for ($i = 0; $i < sizeof($controlesAuto); $i++) {
                    $prepareReservoir->execute(array($controlesAuto[$i]['id_reservoir']));
                    $reservoir = $prepareReservoir->fetch();

                    $prepareSociete->closeCursor();
                    $prepareSociete->execute(array($reservoir['id_societe']));
                    $societe = $prepareSociete->fetch();

                    $prepareControle->execute(array($controlesAuto[$i]['id_controle']));
                    $controle = $prepareControle->fetch();

                    $prepareDiscipline->execute(array($controlesAuto[$i]['id_discipline']));
                    $discipline = $prepareDiscipline->fetch();

                    echo '<tr>';
                    echo '<td>'.$societe['nom_societe'].' : '.$reservoir['designation'].' '.$reservoir['type'].'</td>';
                    echo '<td>'.$controle['libelle'].' ('.$controle['code'].')</td>';
                    echo '<td>'.$discipline['libelle'].' ('.$discipline['code'].')</td>';
                    echo '<td><form method="get" action="pvAuto.php">';
                    echo '<input type="hidden" name="idControleAuto" value="'.$controlesAuto[$i]['id_controle_auto'].'">';
                    echo '<input type="hidden" name="idSociete" value="'.$_GET['idSociete'].'">';
                    if ($controlesAuto[$i]['generation_auto'] == 1)
                        echo '<button name="desactiver" value="1" class="ui blue button">  Désactiver la génération automatique </button>';
                    else
                        echo '<button name="activer" value="1" class="ui blue button">  Activer la génération automatique </button>';
                    echo '</form></td>';
                    echo '<td><form method="get" action="pvAuto.php">';
                    echo '<input type="hidden" name="idControleAuto" value="'.$controlesAuto[$i]['id_controle_auto'].'">';
                    echo '<input type="hidden" name="idSociete" value="'.$_GET['idSociete'].'">';
                    echo '<button name="supprimer" value="1" class="ui blue button"> Supprimer cette programmation </button>';
                    echo '</form></td>';
                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>
            <?php
        }
    }
    ?>

    <div class="ui large modal" id="modalAide">
        <div class="header">Aide</div>
        <div>
            <p>
                Cette page vous indique les PV programmés et vous permet d'en programmer de nouveaux.
                En sélectionnant une société dans la liste, les PV programmés pour celle-ci apparaîtront sous forme de tableau.
                Vous pouvez les supprimer ou encore déterminer si oui ou non ils doivent être générés automatiquement à chaque création de rapport concernant la société en question.
            </p>
            <p>
                Pour programmer de nouveaux PV, il vous suffit de sélectionner une entreprise, et de remplir les autres champs demandés.
                En cliquant sur "Créer ce PV", celui-ci apparaîtra alors dans le tableau des PV programmés, et sera dès lors générable automatiquement lors de la création de rapports.
            </p>
            <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
            </button>
        </div>
    </div>

</div>

<?php
/**
 * Retourne vrai si les valeurs rentrées pour le PV sont correctes.
 *
 * @return bool Vrai si tous les champs ont été remplis avec des valeurs correctes.
 */
function valeursValides() {
    return (isset($_GET['idSociete']) && $_GET['idSociete'] != "" && isset($_GET['idReservoir']) && $_GET['idReservoir'] != ""
         && isset($_GET['idControle']) && $_GET['idControle'] != "" && isset($_GET['idDiscipline']) && $_GET['idDiscipline'] != "");
}