<?php
require_once "../menu.php";

if (!verifSessionCA()) {
    header('Location: /gestionPV/index.php');
    exit;
}

enTete("Création de rapport",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/creationRapport.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$affaires = $bddPortailGestion->query('SELECT * FROM affaire WHERE affaire.id_affaire NOT IN (SELECT rapports.id_affaire FROM rapports)')->fetchAll();    // Permet d'empécher la création de 2 rapports sur la même affaire.
//$affaires = selectAll($bddPortailGestion, "affaire")->fetchAll();
$utilisateurs = selectAll($bddPlanning, "utilisateurs")->fetchAll();

if (isset($_GET['num_affaire'])) {
    $affaireSelectionnee = selectAffaireParNom($bddPortailGestion, $_GET['num_affaire'])->fetch();
    if ($affaireSelectionnee['id_societe'] != "") {
        $societe = selectSocieteParId($bddPortailGestion, $affaireSelectionnee['id_societe'])->fetch();
        $odp = selectODPParId($bddPortailGestion, $affaireSelectionnee['id_odp'])->fetch();
        $personneRencontree = selectClientParId($bddPortailGestion, $odp['id_client'])->fetch();
        $numCommande = 0;
        $dateDebut = 0;
    }
}

$typeControles = selectAll($bddPortailGestion, "type_controle")->fetchAll();

if (!isset($_GET['start']) || $_GET['start'] == "")
    $_GET['start'] = 0;
?>

<div id="contenu">

    <h1 id="titreMenu" class="ui blue center aligned huge header">Création d'un rapport</h1>
    <?php
    afficherMessage('erreur', "Erreur", "Veuillez remplir tous les champs précédés par un astérisque.", "", "");
    ?>

    <form method="get" action="modifRapportCA.php">
        <table id="ensTables">
            <tr>
                <td id="infosRapport">
                    <?php infosRapport(); ?>
                </td>
                <td id="tableauPVAuto">
                    <?php
                        if (isset($_GET['num_affaire'])) {
                            tableauPVAuto($bddPortailGestion, $bddInspections, $affaireSelectionnee);
                        } else { ?>
                            <div class="ui message">
                                <div class="header">
                                    PV programmés
                                </div>
                                <p>
                                   Ici apparaîtront les PV programmés pour la société concernée par l'affaire sélectionnée.
                                </p>
                            </div>
                        <?php
                        }
                    ?>
                </td>
            </tr>
        </table>

        <table id="apercu">
            <tr>
                <th colspan="2">
                    <h3 class="ui dividing left aligned header">Aperçu de
                        l'affaire <?php if (isset($_GET['num_affaire'])) echo $affaireSelectionnee['num_affaire'] ?> </h3>
                </th>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Clients : </label>
                        <label>
                            <?php
                            if (isset($societe)) {
                                echo $societe['nom_societe'];
                            }
                            ?>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Personne rencontrée : </label>
                        <label>
                            <?php
                            if (isset($personneRencontree)) {
                                echo $personneRencontree['nom'];
                            }
                            ?>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <div class="field">
                            <label>Numéro de commande client : </label>
                            <label>
                                <?php
                                if (isset($numCommande)) {
                                    echo $numCommande;
                                }
                                ?>
                            </label>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Lieu : </label>
                        <label>
                            <?php
                            if (isset($affaireSelectionnee)) {
                                echo $affaireSelectionnee['lieu_intervention'];
                            }
                            ?>
                        </label>
                    </div>
                </td>
            </tr>
        </table>

        <input type="hidden" name="ajoutRapport" value="1">
        <button class="ui right floated blue button">Valider</button>
    </form>
</div>

<div class="ui large modal" id="modalAide">
    <div class="header">Aide</div>
    <div>
        <p>
            Ici, vous pouvez créer de nouveaux rapports, qui seront ajoutés dans la base de données. Pour ce faire, vous
            devez
            remplir les informations requises. En entrant un numéro d'affaire, vous obtiendrez en bas de la page un bref
            aperçu
            comportant les détails de l'affaire choisie. Une fois que les informations sélectionnées vous conviennent,
            cliquez sur "Valider"
            et votre PV sera ajouté à la base, et accessible sur ce portail.
        </p>
        <p>
            A droite sont indiqués dans un tableau, si ils ont été crées, les PV programmés pour la société concernée par l'affaire.
            Vous pouvez sélectionner les PV que vous souhaitez automatiquement générer en les cochant. En cliquant sur "Valider" en bas
            de la page, le rapport sera crée, ainsi que l'ensemble des PV cochés.
        </p>
        <p>
            Pour programmer des PV pour une société, rendez-vous dans la section "Gestion > Automatisation des PV".
        </p>
        <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
        </button>
    </div>
</div>
</body>
</html>

<?php
/**
 * Crée les différents champs du formulaire à remplir pour créer le rapport.
 */
function infosRapport() {
    global $affaires, $utilisateurs;
?>

<table>
    <thead>
        <tr>
            <th colspan="3"><h4 class="ui dividing header">Détails de l'affaire</h4></th>
        </tr>
    </thead>

    <tr>
        <td>
            <div class="field">
                <label>* Numéro de l'affaire : </label>
                <div class="field"><?php $url = "creationRapport.php?num_affaire="; ?>
                    <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="inputEl ui search dropdown" name="num_affaire">
                        <option selected></option>
                        <?php
                        for ($i = 0; $i < sizeof($affaires); $i++) {
                            // Garde en mémoire l'élément sélectionné
                            if (isset($_GET['num_affaire']) && $affaires[$i]['num_affaire'] == $_GET['num_affaire'])
                                echo '<option selected>' . $affaires[$i]['num_affaire'] . '</option>';
                            else
                                echo '<option>' . $affaires[$i]['num_affaire'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </td>
        <td>
            <div class="field">
                <label>* Demande reçue par : </label>
                <div class="field">
                    <select class="inputEl ui search dropdown" name="demandeRecue">
                        <option selected></option>
                        <?php
                        for ($i = 0; $i < sizeof($utilisateurs); $i++) {
                            if ($utilisateurs[$i]['nom'] != 'root') {
                                if (isset($_GET['demandeRecue']) && $_GET['demandeRecue'] != "" && $utilisateurs[$i]['nom'] == $_GET['demandeRecue'])
                                    echo '<option selected>' . $utilisateurs[$i]['nom'] . '</option>';
                                else
                                    echo '<option>' . $utilisateurs[$i]['nom'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </td>
        <td>
            <div class="field">
                <label>* Demande analysée par : </label>
                <div class="field">
                    <select class="inputEl ui search dropdown" name="demandeAnalysee">
                        <option selected></option>
                        <?php
                        for ($i = 0; $i < sizeof($utilisateurs); $i++) {
                            if ($utilisateurs[$i]['nom'] != 'root') {
                                if (isset($_GET['demandeAnalysee']) && $_GET['demandeRecue'] != "" && $utilisateurs[$i]['nom'] == $_GET['demandeAnalysee'])
                                    echo '<option selected>' . $utilisateurs[$i]['nom'] . '</option>';
                                else
                                    echo '<option>' . $utilisateurs[$i]['nom'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <label class="labelCB"> Appel d'offre ? </label>
            <?php
                if (isset($_GET['appelOffre']) && $_GET['appelOffre'] == "1")
                    echo '<input checked class="inputEl" type="checkbox" name="appelOffre">';
                else
                    echo '<input class="inputEl" type="checkbox" name="appelOffre">';
            ?>
        </td>
        <td>
            <label>* Obtention de l'offre </label>
            <select class="inputEl ui search dropdown voieOffre" name="obtentionOffre">
                <option selected></option>
                <?php
                if (isset($_GET['obtentionOffre']) && $_GET['obtentionOffre'] == "Oral") {
                    echo '<option selected>Oral</option>';
                    echo '<option>Mail</option>';
                }
                else if (isset($_GET['obtentionOffre']) && $_GET['obtentionOffre'] == "Mail") {
                    echo '<option>Oral</option>';
                    echo '<option selected>Mail</option>';
                } else {
                    echo '<option>Oral</option>';
                    echo '<option>Mail</option>';
                }
                ?>
            </select>
        </td>
        <td>
            <label>* Avenant affaire n° : </label>
            <div class="ui input">
                <input class="inputEl" type="text" name="numAvenant" placeholder="Numéro avenant affaire"
                       value="<?php if (isset($_GET['numAvenant'])) echo $_GET['numAvenant']; ?>">
            </div>
        </td>
    </tr>
</table>

<table>
    <tr>
        <th colspan="2"><h4 class="ui dividing header">Document de référence</h4></th>
    </tr>

    <tr>
        <td>
            <label>* Suivant procédure : </label>
            <div class="ui input">
                <input class="inputEl" type="text" name="procedure" placeholder="Procédure suivie"
                       value="<?php if (isset($_GET['procedure'])) echo $_GET['procedure']; ?>">
            </div>
        </td>
        <td>
            <div class="field">
                <label>* Code d'interprétation : </label>
                <div class="ui input">
                    <input class="inputEl" type="text" name="codeInter" placeholder="Code d'interprétation"
                           value="<?php if (isset($_GET['codeInter'])) echo $_GET['codeInter']; ?>">
                </div>
            </div>
        </td>
    </tr>
</table>
<?php
}

/**
 * Crée le tableau indiquant les PV préprogrammés pour la société concernée par l'affaire.
 *
 * @param PDO $bddPortailGestion Base de données des PV.
 * @param PDO $bddInspections Base de données des réservoirs.
 * @param array $affaireSelectionnee Affaire sélectionnée par l'utilisateur.
 */
function tableauPVAuto($bddPortailGestion, $bddInspections, $affaireSelectionnee) {
    $societe = selectSocieteParId($bddPortailGestion, $affaireSelectionnee['id_societe'])->fetch();
    $controlesAuto = selectControlesAutoParSociete($bddPortailGestion, $societe['id_societe'])->fetchAll();

    $prepareControleAuto = $bddPortailGestion->prepare('select * from controle_auto where id_controle_auto = ?');

    if (isset($societe) && sizeof($controlesAuto) > 0) {
        $nbLignes = 5;
        ?>
        <table class="ui celled table">
            <thead>
            <tr>
                <th colspan="4">PV programmés pour les rapports concernant la
                    société <?php echo $societe['nom_societe']; ?> impliquée dans
                    l'affaire <?php echo $affaireSelectionnee['num_affaire']; ?></th>
            </tr>
            <tr>
                <th>
                    Réservoir à inspecter
                </th>
                <th>
                    Type de contrôle
                </th>
                <th>
                    Discipline du contrôle
                </th>
                <th>
                    <?php
                        if (verifGeneration($controlesAuto))
                            echo 'Ajouter <a href="" id="toutDecocher">(Tout décocher)</a>';
                        else
                            echo 'Ajouter <a href="" id="toutCocher">(Tout cocher)</a>';

                    ?>

                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $i < $nbLignes; $i++) {
                if (isset($controlesAuto[$i + $_GET['start']]) && $controlesAuto[$i + $_GET['start']] != "") {
                    $prepareControleAuto->execute(array($controlesAuto[$i + $_GET['start']]['id_controle_auto']));
                    $controleAuto = $prepareControleAuto->fetch();
                    $prepareControleAuto->closeCursor();
                    ecrireLigne($bddPortailGestion, $bddInspections, $controleAuto);
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <th style="background-color: #f0f0f0;" colspan="4">
                    <div class="ui right floated pagination menu">
                        <?php
                        if ($_GET['start'] >= $nbLignes) {
                            echo '<a class="icon item pagePrecedente">';
                            echo '<i class="left chevron icon"></i>';
                            echo '</a>';
                        }


                        $pageActuelle = ceil($_GET['start'] / $nbLignes) + 1;

                        if ($pageActuelle > 2)
                            echo '<a class="doublePagePrecedente item">' . ($pageActuelle - 2) . '</a>';
                        if ($pageActuelle > 1)
                            echo '<a class="pagePrecedente item">' . ($pageActuelle - 1) . '</a>';

                        echo '<a style="background-color: #91d1f7;" class="item" id="selected">' . $pageActuelle . '</a>';

                        if ($pageActuelle < ceil(sizeof($controlesAuto) / $nbLignes))
                            echo '<a class="pageSuivante item">' . ($pageActuelle + 1) . '</a>';

                        if ($pageActuelle < ceil(sizeof($controlesAuto) / $nbLignes) - 1)
                            echo '<a class="doublePageSuivante item">' . ($pageActuelle + 2) . '</a>';


                        if ($_GET['start'] < sizeof($controlesAuto) - 5) {
                            echo '<a class="icon item pageSuivante">';
                            echo '<i class="right chevron icon"></i>';
                            echo '</a>';
                        }
                        ?>
                    </div>
                </th>
            </tr>
            </tfoot>
        </table>
        <?php
    } else { ?>
        <div class="ui message">
            <div class="header">
                Pas de PV programmés !
            </div>
            <p>
                Aucun PV n'a été programmé pour les affaires concernant la société <?php echo $societe['nom_societe']; ?>.
                Si vous souhaitez en programmer, rendez-vous dans la section "Gestion > Automatisation des PV".
            </p>
        </div>
    <?php }
}

/**
 * Parcours l'ensemble des contrôles programmés pour la société actuelle, et retourne vrai si ils sont tous à générer, faux sinon.
 *
 * @param array $controlesAuto Tableau contenant les informations des différents PV de contrôle programmés.
 * @return bool Vrai si tous les PV doivent être générés.
 */
function verifGeneration($controlesAuto) {
    for ($i = 0; $i < sizeof($controlesAuto); $i++) {
        if ($controlesAuto[$i]['generation_auto'] == 0)
            return false;
    }

    return true;
}

/**
 * Ecrit une ligne du tableau des PV préprogrammés.
 *
 * @param PDO $bddPortailGestion Base de données des PV.
 * @param PDO $bddInspections Base de données des réservoirs.
 * @param array $controleAuto PV de contrôle à représenter sur la ligne.
 */
function ecrireLigne($bddPortailGestion, $bddInspections, $controleAuto) {
    $prepareReservoir = $bddInspections->prepare('select * from reservoirs_gestion_pv where id_reservoir = ?');
    $prepareControle = $bddPortailGestion->prepare('select * from type_controle where id_type = ?');
    $prepareDiscipline = $bddPortailGestion->prepare('select * from type_discipline where id_discipline = ?');

    $prepareReservoir->execute(array($controleAuto['id_reservoir']));
    $reservoir = $prepareReservoir->fetch();

    $prepareControle->execute(array($controleAuto['id_controle']));
    $typeControle = $prepareControle->fetch();

    $prepareDiscipline->execute(array($controleAuto['id_discipline']));
    $discipline = $prepareDiscipline->fetch();

    echo '<tr>';
    echo '<td>' . $reservoir['designation'] .' '.$reservoir['type'].'</td>';
    echo '<td>' . $typeControle['libelle'] . ' ('.$typeControle['code'].')</td>';
    echo '<td>' . $discipline['libelle'] . ' ('.$discipline['code'].')</td>';
    echo '<td><input class="cbAuto" '. ($controleAuto['generation_auto'] == 1 ? "checked" : "").' type="checkbox" name="'.strval($controleAuto['id_controle_auto']).'"></td>';
    echo '</tr>';
}
?>

<script>
    $(".cbAuto").on("change", function(e) {
        var data = e.currentTarget.valueOf().name;

        $.ajax({
            method: "post",
            url: "modifAutoAjax.php",
            data: "id=" + data,
            cache: false,
            success: function (data) {},
        });
    });

    $("#toutCocher").on("click", function(e) {
        var id_societe = <?php echo $societe['id_societe']; ?>;

        $.ajax({
            method: "post",
            url: "modifAutoAjax.php",
            data: "id_societe=" + id_societe + "&valeur=1",
            cache: false,
            success: function (data) {},
        });

        document.location = pageActuelle();
    })

    $("#toutDecocher").on("click", function(e) {
        var id_societe = <?php echo $societe['id_societe']; ?>;

        $.ajax({
            method: "post",
            url: "modifAutoAjax.php",
            data: "id_societe=" + id_societe + "&valeur=0",
            cache: false,
            success: function (data) {},
        });

        document.location = pageActuelle();
    })

    function recupererDonnees() {
        var donnees = "creationRapport.php?";

        donnees += $("select[name='num_affaire']").attr('name') + "=" + $("select[name='num_affaire']").val() + "&";
        donnees += $("select[name='demandeRecue']").attr('name') + "=" + $("select[name='demandeRecue']").val() + "&";
        donnees += $("select[name='demandeAnalysee']").attr('name') + "=" + $("select[name='demandeAnalysee']").val() + "&";
        donnees += $("input[name='appelOffre']").attr('name') + "=" + (($("input[name='appelOffre']").is(":checked")) ? "1" : "0") + "&";
        donnees += $("select[name='obtentionOffre']").attr('name') + "=" + $("select[name='obtentionOffre']").val() + "&";
        donnees += $("input[name='numAvenant']").attr('name') + "=" + $("input[name='numAvenant']").val() + "&";
        donnees += $("input[name='procedure']").attr('name') + "=" + $("input[name='procedure']").val() + "&";
        donnees += $("input[name='codeInter']").attr('name') + "=" + $("input[name='codeInter']").val() + "&";

        return donnees;
    }

    function pageActuelle() {
        var start = <?php echo $_GET['start']; ?>;

        return recupererDonnees().concat("start=" + (start));
    }

    function pagePrecedente() {
        var nbLignes = 5;
        var start = <?php echo $_GET['start']; ?>;

        return recupererDonnees().concat("start=" + (start - nbLignes));
    }

    function doublePagePrecedente() {
        var nbLignes = 5;
        var start = <?php echo $_GET['start']; ?>;

        return recupererDonnees().concat("start=" + (start - 2*nbLignes));
    }

    function doublePageSuivante() {
        var nbLignes = 5;
        var start = <?php echo $_GET['start']; ?>;

        return recupererDonnees().concat("start=" + (start + 2*nbLignes));
    }

    function pageSuivante() {
        var nbLignes = 5;
        var start = <?php echo $_GET['start']; ?>;

        return recupererDonnees().concat("start=" + (start + nbLignes));
    }

    $('.doublePagePrecedente').on("click", function() {
        window.location = doublePagePrecedente();
    })

    $('.pagePrecedente').on("click", function() {
        window.location = pagePrecedente();
    })

    $('.pageSuivante').on("click", function() {
        window.location = pageSuivante();
    })

    $('.doublePageSuivante').on("click", function() {
        window.location = doublePageSuivante();
    })
</script>
