<?php
require_once "../menu.php";
require_once '../historique/gestionHistoriqueUtil.inc.php';

if (!verifSessionOP()) {
    header('Location: /gestionPV/index.php');
    exit;
}

if (!isset($_GET['idPV']) || $_GET['idPV'] == "") {
    header('Location: /gestionPV/index.php');
    exit;
}

enTete("Modification de PV",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

if (isset($_GET['constatation']) && $_GET['constatation'] != "") {
    if (isset($_GET['typeConstatation']) && $_GET['typeConstatation'] != "")
        insert($bddPortailGestion, "constatations_pv", array("null", $_GET['idPV'], $bddPortailGestion->quote($_GET['typeConstatation']), $bddPortailGestion->quote($_GET['constatation'])));
    else
        insert($bddPortailGestion, "constatations_pv", array("null", $_GET['idPV'], "null", $bddPortailGestion->quote($_GET['constatation'])));

    $_GET['modif'] = 1;
}

if (isset($_GET['conclusion']) && $_GET['conclusion'] != "") {
    insert($bddPortailGestion, "conclusions_pv", array("null", $_GET['idPV'], $bddPortailGestion->quote($_GET['conclusion'])));
    $_GET['modif'] = 1;
}

$pv = selectPVParId($bddPortailGestion, $_GET['idPV'])->fetch();
$type_controle = selectControleParId($bddPortailGestion, $pv['id_type_controle'])->fetch();
$discipline = selectDisciplineParId($bddPortailGestion, $pv['id_discipline'])->fetch();

$rapport = selectRapportParId($bddPortailGestion, $pv['id_rapport'])->fetch();
$affaire = selectAffaireParId($bddPortailGestion, $rapport['id_affaire'])->fetch();
$societe = selectSocieteParId($bddPortailGestion, $affaire['id_societe'])->fetch();
$odp = selectODPParId($bddPortailGestion, $affaire['id_odp'])->fetch();
$client = selectClientParId($bddPortailGestion, $odp['id_client'])->fetch();
$controleur = selectUtilisateurParId($bddPlanning, $pv['id_controleur'])->fetch();

$reservoir = selectReservoirParId($bddInspections, $pv['id_reservoir'])->fetch();

$appareils = $bddPortailGestion->query('SELECT * FROM appareils WHERE appareils.id_appareil NOT IN (SELECT appareils_utilises.id_appareil FROM appareils_utilises WHERE id_pv_controle = ' . $pv['id_pv'] . ')')->fetchAll();
$appareilsUtilises = selectAppareilsUtilisesParPV($bddPortailGestion, $pv['id_pv'])->fetchAll();
$typeAppareilsUtilises = $bddPortailGestion->query('SELECT * FROM appareils WHERE appareils.id_appareil IN (SELECT appareils_utilises.id_appareil FROM appareils_utilises WHERE id_pv_controle = ' . $pv['id_pv'] . ')')->fetchAll();

$titre = "SCO" . explode(" ", $affaire['num_affaire'])[1] . '-' . $discipline['code'] . '-' . $type_controle['code'] . '-' . sprintf("%03d", $pv['num_ordre']);

if (isset($_GET['modif']) && $_GET['modif'] = 1)
    ajouterHistorique($bddPortailGestion, "Modification opérateur du PV " . $titre, "pv/modifPVCA.php?idPV=", $pv['id_pv']);
?>

<?php
creerModal("constatation");
creerModal("conclusion");
?>

    <div id="contenu">
        <?php $nomPV = explode(" ", $affaire['num_affaire']); ?>
        <h1 class="ui blue center aligned huge header">Modification du PV <?php echo $titre; ?></h1>
        <table id="ensTables">
            <tr>
                <td class="partieTableau">
                    <form class="ui form" method="post" <?php echo 'action="/gestionPV/excel/conversionPV.php"' ?>>
                        <?php creerApercuModif($affaire, $societe, $reservoir, $client, $controleur, $pv); ?>
                        <table>
                            <?php creerApercuDocuments($rapport); ?>
                            <tr>
                                <td>
                                    <?php
                                    if ($pv['chemin_excel'] != null) {
                                        $chemin = str_replace("'", "", $pv['chemin_excel']);
                                        if (file_exists($chemin)) {
                                            echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                                            echo '<button class="ui left floated green button" title="Restaure le fichier du serveur dans son état d\'origine" name="reset" value="1">Regénérer le fichier</button>';
                                        } else {
                                            echo '<button disabled style="pointer-events: auto" class="ui left floated green button" title="Aucun fichier n\'a été généré pour le moment " name="reset" value="1"> Regénérer le fichier</button>';
                                        }
                                    } else {
                                        echo '<button disabled style="pointer-events: auto" class="ui left floated green button" title="Aucun fichier n\'a été généré pour le moment " name="reset" value="1"> Regénérer le fichier</button>';
                                    }
                                    ?>
                                </td>
                                <td></td>
                                <td>
                                    <?php
                                    echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                                    echo '<button id="boutonGenere" class="ui right floated blue button">Générer/Télécharger le fichier Excel</button>';
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
                <td class="partieTableau">
                    <form method="get" action="ajoutAppareilPV.php">
                        <table>
                            <tr>
                                <th colspan="2"><h4 class="ui dividing header">Appareils utilisés</h4></th>
                            </tr>

                            <tr>
                                <td>
                                    <label for="appareil"> Appareil à ajouter : </label>
                                    <select class="ui search dropdown listeAjout" name="appareil">
                                        <option selected></option>
                                        <?php
                                        for ($i = 0; $i < sizeof($appareils); $i++) {
                                            echo '<option value="' . $appareils[$i]['id_appareil'] . '">' . $appareils[$i]['systeme'] . ' ' . $appareils[$i]['type'] . ' (' . $appareils[$i]['num_serie'] . ')</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <label> Appareils déjà ajoutés : </label>
                                    <select disabled size=2 class="ui search dropdown listeUtilises">
                                        <?php
                                        for ($i = 0; $i < sizeof($typeAppareilsUtilises); $i++) {
                                            echo '<option>' . $typeAppareilsUtilises[$i]['systeme'] . ' ' . $typeAppareilsUtilises[$i]['type'] . ' (' . $typeAppareilsUtilises[$i]['num_serie'] . ')</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input type="hidden" name="idPV" value="<?php echo $pv['id_pv']; ?>">
                                    <button class="ui right floated blue button">Ajouter cet appareil</button>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <?php
                                    afficherMessageAjout('appareil', "L'appareil a bien été ajouté !", "Aucun appareil n'a été indiqué !");
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <?php
                        echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                        ?>
                    </form>
                    <form method="get" action="ajoutSituationPV.php">
                        <table id="tableCB">
                            <tr>
                                <th colspan="3"><h4 class="ui dividing header">Situation de contrôle & annexes</h4></th>
                            </tr>

                            <tr>
                                <td>
                                    <label class="labelCB"> Contrôle interne ? </label>
                                    <?php
                                    if ($pv['controle_interne'] == 1)
                                        echo '<input checked type="checkbox" name="controle_interne">';
                                    else
                                        echo '<input type="checkbox" name="controle_interne">';
                                    ?>
                                </td>
                                <td>
                                    <label class="labelCB"> Contrôle externe ? </label>
                                    <?php
                                    if ($pv['controle_externe'] == 1)
                                        echo '<input checked type="checkbox" name="controle_externe">';
                                    else
                                        echo '<input type="checkbox" name="controle_externe">';
                                    ?>
                                </td>
                                <td>
                                    <label class="labelCB"> Contrôle périphérique ? </label>
                                    <?php
                                    if ($pv['controle_peripherique'] == 1)
                                        echo '<input checked type="checkbox" name="controle_peripherique">';
                                    else
                                        echo '<input type="checkbox" name="controle_peripherique">';
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label class="labelCB"> Photos jointes ? </label>
                                    <?php
                                    if ($pv['photos_jointes'] == 1)
                                        echo '<input checked type="checkbox" name="photos_jointes">';
                                    else
                                        echo '<input type="checkbox" name="photos_jointes">';
                                    ?>
                                </td>
                                <td>
                                    <label class="labelCB"> Pièces jointes ? </label>
                                    <?php
                                    if ($pv['pieces_jointes'] == 1)
                                        echo '<input checked type="checkbox" name="pieces_jointes">';
                                    else
                                        echo '<input type="checkbox" name="pieces_jointes">';
                                    ?>
                                </td>
                                <td>
                                    <label class="labelCB"> Surface peinte ? </label>
                                    <?php
                                    if ($pv['surface_peinte'] == 1)
                                        echo '<input checked type="checkbox" name="peinture">';
                                    else
                                        echo '<input type="checkbox" name="peinture">';
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2"> <?php afficherMessageAjout('nbAnnexes', "Les modifications ont bien été prises en compte !", "Erreur dans la modification"); ?> </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td>
                                    <label> Annexes : </label>
                                    <div class="ui input">
                                        <?php
                                        if ($pv['nb_annexes'] != 0)
                                            echo '<input type="number" name="nbAnnexes" placeholder="Nombre d\'annexes" value="' . $pv['nb_annexes'] . '">';
                                        else
                                            echo '<input type="number" name="nbAnnexes" placeholder="Nombre d\'annexes" value = "0">';
                                        ?>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <input type="hidden" name="idPV" value="<?php echo $pv['id_pv']; ?>">
                                    <button class="ui right floated blue button">Valider</button>
                                </td>
                            </tr>
                        </table>
                        <?php
                        echo '<input type="hidden" name="idPV" value="' . $pv['id_pv'] . '">';
                        ?>
                    </form>
                    <table>
                        <tr>
                            <th colspan="2"><h4 class="ui dividing header">Constatations & conclusions</h4></th>
                        </tr>

                        <tr>
                            <td>
                                <button id="boutonConstatation" class="ui left floated blue button">Ajouter une
                                    constatation
                                </button>
                            </td>
                            <td>
                                <button id="boutonConclusion" class="ui right floated blue button">Ajouter une
                                    conclusion
                                </button>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <form method="get" action="listePVOP.php">
            <?php echo '<input type="hidden" name="nomPV" value="' . $titre . '">'; ?>
            <button id="retour" class="ui right floated blue button">Retour à la liste des PV</button>
        </form>
    </div>

    <div class="ui large modal" id="modalAide">
        <div class="header">Aide</div>
        <div>
            <p>
                Cette page vous indique les différentes informations sur le PV sélectionné. En tant qu'opérateur, vous
                pouvez
                également indiquer les appareils utilisés pour effectuer le contrôle parmi ceux présents dans la base,
                indiquer la situation du contrôle, le nombre et le type d'annexes, et enfin les constatations et
                conclusions
                effectuées.
            </p>
            <p>
                Une fois ces informations rentrées, vous pouvez cliquer sur le bouton "Générer/Télécharger le
                fichier
                Excel", qui vous génerera automatiquement un fichier Excel comprenant toutes les informations indiquées,
                qui sera stocké sur le serveur. Vous pouvez ensuite télécharger le fichier, et le regénérer si besoin.
            </p>
            <p>
                <strong> Attention : </strong> "Télécharger le fichier Excel" ne fait que télécharger le fichier présent
                sur le serveur
                - il ne génère un fichier que si aucun autre fichier concernant ce PV n'est présent. Si un fichier est déjà présent sur le
                serveur,
                utilisez le bouton "Regénérer le fichier" afin de le mettre à jour avec les nouvelles informations
                entrées dans le formulaire.
            </p>
            <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
            </button>
        </div>
    </div>

    </body>
    </html>

    <script>
        $("#boutonConstatation").on("click", function () {
            $('#modalConstatation').modal('show');
        });

        $("#boutonConclusion").on("click", function () {
            $('#modalConclusion').modal('show');
        });
    </script>

<?php

/**
 * Affiche un message indiquant le succès où l'échec de la requète de l'utilisateur.
 *
 * @param String $conditionSucces Condition permettant de vérifier le succès.
 * @param String $messageSucces Message en cas de succès.
 * @param String $messageErreur Message en cas d'échec.
 */
function afficherMessageAjout($conditionSucces, $messageSucces, $messageErreur) {
    if (isset($_GET[$conditionSucces]) && $_GET[$conditionSucces] != "") {
        echo '<div class="ui message">';
        echo '<div class="header"> Succès !</div>';
        echo '<p id="infosAction">' . $messageSucces . '</p>';
        echo '</div>';
    } else if (isset($_GET[$conditionSucces])) {
        echo '<div class="ui message">';
        echo '<div class="header"> Erreur </div>';
        echo '<p id="infosAction">' . $messageErreur . '</p>';
        echo '</div>';
    }
}

/**
 * Fonction permettant de créer les popups de création de constatations et conclusions.
 *
 * @param string $nom Constatation ou conclusion, permet de définir le contenu du popup.
 */
function creerModal($nom) {
    $nomMaj = ucfirst($nom);
    $id = "modal" . $nomMaj;
    $idType = "type" . $nomMaj;
    ?>
    <div id="<?php echo $id; ?>" class="ui large modal">
        <div style="text-align: left;" class="header"><?php echo $nomMaj; ?><i class="close icon"></i></div>
        <div class="content">
            <form method="get" action="modifPVOP.php">
                <div class="ui form">
                    <?php if ($nom != "conclusion") { ?>
                        <div class="field">
                            <label>Type de <?php echo $nom; ?></label>
                            <input type="text" name="<?php echo $idType; ?>">
                        </div>
                    <?php } ?>
                    <div class="field">
                        <label><?php echo $nomMaj; ?></label>
                        <textarea rows="2" name="<?php echo $nom; ?>"></textarea>
                    </div>
                    <input type="hidden" name="idPV" value="<?php echo $_GET['idPV']; ?>">
                    <button style="margin: 0 1em 0.5em 0;" class="ui right floated blue button">Valider
                        cette <?php echo $nom; ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

?>