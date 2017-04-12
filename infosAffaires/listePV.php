<?php
    $bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');

    $listePV = $bddAffaire->query('select * from pv_controle')->fetchAll();
    $selectAffaire = $bddAffaire->prepare('select * from affaire where id_affaire = ?');
    $selectEquipement = $bddEquipement->prepare('select * from equipement where idEquipement = ?');
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Liste des PV</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css"/>
        <link rel="stylesheet" href="../style/style.css"/>
        <link rel="stylesheet" href="../style/listePV.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"></script>
    </head>

    <body>
        <h1 class="ui blue center aligned huge header">Liste des PV</h1>
        <table class="ui celled table">
            <thead>
                <tr>
                    <th>Identifiant PV</th>
                    <th>Numéro d'affaire</th>
                    <th>Equipement à inspecter</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < sizeof($listePV); $i++) {
                    echo '<tr><td><a href="modifPV.php?idPv='.$listePV[$i]['id_pv'].'">'.$listePV[$i]['id_pv'].'</a></td><td>';
                    $selectAffaire->execute(array($listePV[$i]['id_affaire']));
                    $num_affaire = $selectAffaire->fetch();
                    echo $num_affaire['num_affaire'].'</td><td>';
                    $selectEquipement->execute(array($listePV[$i]['id_equipement']));
                    $nom_equipement = $selectEquipement->fetch();
                    echo $nom_equipement['Designation'].' '.$nom_equipement['Type'].'</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </body>
</html>