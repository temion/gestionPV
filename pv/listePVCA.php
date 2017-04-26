<?php
    include_once "../menu.php";
    verifSession("OP");
    enTete("Liste des PV",
        array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
        array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    $bddAffaire = connexion('portail_gestion');
    $bddEquipement = connexion('theodolite');

    // Ensemble des affaires disponibles
    $numAffairesInspection = selectAll($bddAffaire, "affaire_inspection")->fetchAll();

    $selectAffaire = $bddAffaire->prepare('select * from affaire where id_affaire = ?');
    $selectEquipement = $bddEquipement->prepare('select * from equipement where idEquipement = ?');
    $comptePV = $bddAffaire->prepare('select count(*) from pv_controle where id_affaire_inspection = ?');
?>

    <div id="contenu">
        <h1 class="ui blue center aligned huge header">Liste des affaires d'inspection</h1>
        <table class="ui celled table">
            <thead>
            <tr>
                <th>Identifiant affaire</th>
                <th>Numéro d'affaire</th>
                <th>Equipement à inspecter</th>
                <th>Nombre de PV</th>
                <th>Modification</th>
            </tr>
            </thead>
            <tbody>
            <?php
                for ($i = 0; $i < sizeof($numAffairesInspection); $i++) {
                    creerLigneAffaire($numAffairesInspection[$i]);
                }
            ?>
            </tbody>
        </table>
    </div>
    </body>
</html>

<?php


/**
 * Crée une ligne à ajouter dans le tableau comprenant les différentes informations de l'affaire passée en paramètre.
 *
 * @param array $affaireInspection Affaire à afficher.
 */
function creerLigneAffaire($affaireInspection) {
    global $selectAffaire;
    global $selectEquipement;
    global $comptePV;

    $selectAffaire->execute(array($affaireInspection['id_affaire']));
    $affaire = $selectAffaire->fetch();

    $selectEquipement->execute(array($affaireInspection['id_equipement']));
    $equipement = $selectEquipement->fetch();

    $comptePV->execute(array($affaireInspection['id_affaire_inspection']));
    $nbPV = $comptePV->fetch();

    echo '<tr><td>'.$affaireInspection['id_affaire_inspection'].'</td>';
    echo '<td>'.$affaire['num_affaire'].'</td>';
    echo '<td>'.$equipement['Designation'].' '.$equipement['Type'].'</td>';
    echo '<td>'.$nbPV[0].'</td>';
    echo '<td><form method="post" action="modifPVCA.php"><button name="idAffaire" value="'.$affaireInspection['id_affaire_inspection'].'" class="ui right floated blue button">Modifier</button></form></td></tr>';
}
?>