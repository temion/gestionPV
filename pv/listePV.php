<?php
    include_once "../menu.php";
    enTete("Liste des PV",
            array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/listes.css", "../style/menu.css"),
            array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));
    $bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');

    $listePV = $bddAffaire->query('select * from pv_controle')->fetchAll();
    $selectAffaire = $bddAffaire->prepare('select * from affaire where id_affaire = ?');
    $selectEquipement = $bddEquipement->prepare('select * from equipement where idEquipement = ?');
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">Liste des PV</h1>
            <?php
                afficherMessage('pdfG', "Succès", "Votre PV a été généré avec succès !", "", "");
            ?>
            <form method="post" action="modifPV.php">
                <table class="ui celled table">
                    <thead>
                        <tr>
                            <th>Identifiant PV</th>
                            <th>Numéro d'affaire</th>
                            <th>Equipement à inspecter</th>
                            <th>Modification</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            for ($i = 0; $i < sizeof($listePV); $i++) {
                                creerLignePV($listePV, $i);
                            }
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
    </body>
</html>

<?php

/**
 * Crée une ligne à ajouter dans le tableau comprenant les différentes informations du PV à l'indice i.
 *
 * @param array $PVs Liste des pv de la base.
 * @param int $ind Indice de l'appareil à afficher.
 */
function creerLignePV($PVs, $ind) {
    global $selectAffaire;
    global $selectEquipement;

    echo '<tr><td>'.$PVs[$ind]['id_pv'].'</td><td>';
    $selectAffaire->execute(array($PVs[$ind]['id_affaire']));
    $num_affaire = $selectAffaire->fetch();
    echo $num_affaire['num_affaire'].'</td><td>';
    $selectEquipement->execute(array($PVs[$ind]['id_equipement']));
    $nom_equipement = $selectEquipement->fetch();
    echo $nom_equipement['Designation'].' '.$nom_equipement['Type'].'</td>';
    echo '<td><button name="idPV" value="'.$PVs[$ind]['id_pv'].'" class="ui right floated blue button">Modifier</button></td></tr>';
}

?>