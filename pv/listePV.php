<?php
    include_once "../menu.php";
    enTete("Liste des PV",
            array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
            array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));
    $bddAffaire = connexion('portail_gestion');
    $bddEquipement = connexion('theodolite');

    $affaires = $bddAffaire->query('select distinct num_affaire from affaire join pv_controle where pv_controle.id_affaire = affaire.id_affaire')->fetchAll();
    $listePV = $bddAffaire->prepare('select * from pv_controle where pv_controle.id_affaire in (select affaire.id_affaire from affaire where num_affaire like ?)');
    $controles_sur_pv = $bddAffaire->prepare('select * from controles_sur_pv where id_pv = ?');
    $controle = $bddAffaire->prepare('select * from type_controle where id_type = ?');
    $selectAffaire = $bddAffaire->prepare('select * from affaire where id_affaire = ?');
    $selectEquipement = $bddEquipement->prepare('select * from equipement where idEquipement = ?');
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">Liste des PV</h1>
            <?php
                afficherMessage('pdfG', "Succès", "Votre PV a été généré avec succès !", "", "");
            ?>
            <form method="get" action="listePV.php" id="choixAffaire">
                <label for="numAffaire"> Choix de l'affaire : </label>
                <?php $url = "listePV.php?numAffaire=" ?>
                <select onChange='document.location="<?php echo $url ?>".concat(this.options[this.selectedIndex].value)' class="ui search dropdown" name="numAffaire">
                    <option selected> </option>
                    <?php
                        for ($i = 0; $i < sizeof($affaires); $i++) {
                            if (isset($_GET['numAffaire']) && $affaires[$i][0] == $_GET['numAffaire'])
                                echo '<option selected>'.$affaires[$i][0].'</option>';
                            else
                                echo '<option>'.$affaires[$i][0].'</option>';
                        }
                    ?>
                </select>
            </form>
            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Identifiant PV</th>
                        <th>Numéro d'affaire</th>
                        <th>Equipement à inspecter</th>
                        <th>Contrôle</th>
                        <th>Modification</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (isset($_GET['numAffaire']) && $_GET['numAffaire'] != "") {
                            $listePV->execute(array($_GET['numAffaire']));
                            $PVs = $listePV->fetchAll();
                            for ($i = 0; $i < sizeof($PVs); $i++) {
                                creerLignePV($PVs[$i]);
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>

<?php

/**
 * Crée une ligne à ajouter dans le tableau comprenant les différentes informations du PV à l'indice i.
 *
 * @param array $PV PV à afficher.
 */
function creerLignePV($PV) {
    global $selectAffaire;
    global $selectEquipement;
    global $controles_sur_pv;
    global $controle;

    $controles_sur_pv->execute(array($PV['id_pv']));
    $controles_a_effectuer = $controles_sur_pv->fetchAll();

    for ($i = 0; $i < sizeof($controles_a_effectuer); $i++) {
        $controle->execute(array($controles_a_effectuer[$i]['id_type_controle']));
        $typeControle = $controle->fetch();
        echo '<tr><td>' . $PV['id_pv'] . '</td><td>';
        $selectAffaire->execute(array($PV['id_affaire']));
        $num_affaire = $selectAffaire->fetch();
        echo $num_affaire['num_affaire'] . '</td><td>';
        $selectEquipement->execute(array($PV['id_equipement']));
        $nom_equipement = $selectEquipement->fetch();
        echo $nom_equipement['Designation'] . ' ' . $nom_equipement['Type'] . '</td>';
        echo '<td>'.$typeControle['libelle'].' '.$controles_a_effectuer[$i]['num_ordre'].' - Début prévu le '.$controles_a_effectuer[$i]['date'].'</td>';
        echo '<td>';
        if (isset($_SESSION['droit']) && $_SESSION['droit'] == "OP") {
            echo '<form method="post" action="modifPVOP.php"><button name="idPV" value="' . $PV['id_pv'] . '" class="ui right floated blue button">Modifier</button>';
            echo '<input type="hidden" name="idControle" value="' . $controles_a_effectuer[$i]['id_controle_pv'] . '"></form>';
        } else if (isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") {
            echo '<form method="post" action="modifPVCA.php"><button name="idPV" value="' . $PV['id_pv'] . '" class="ui right floated red button">Modifier</button></form></td></tr>';
        } else {
            echo '<form method="post" action="modifPVOP.php"><button name="idPV" value="' . $PV['id_pv'] . '" class="ui right floated blue button">Modifier (opérateur)</button>';
            echo '<input type="hidden" name="idControle" value="' . $controles_a_effectuer[$i]['id_controle_pv'] . '"></form>';
            echo '<form method="post" action="modifPVCA.php"><button name="idPV" value="' . $PV['id_pv'] . '" class="ui right floated red button">Modifier (chargé d\'affaires)</button></form></td></tr>';
        }
    }
}

?>