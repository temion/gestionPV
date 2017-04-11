<?php
    $personneClient = $_POST['personneClient'];
    $societeClient = $_POST['societeClient'];
    $numCommande = $_POST['numCommande'];
    $lieu = $_POST['lieu'];
    $debut = $_POST['debutControle'];

    $bdd = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
//    $bdd->exec('insert into activite(nom_activite, description) values (\'Test\', \'Oui\')');

    $infosClients = $bdd->query('select * from client where nom like \''.$personneClient.'\'')->fetch();
    $infosSociete = $bdd->query('select * from societe where nom_societe like \''.$societeClient.'\'')->fetch();

    if (empty($infosClients))
        echo $personneClient.' ';
    else
        echo $infosClients['nom'].'('.$infosClients['id_client'].') ';

    if (empty($infosSociete)) {
        echo $societeClient;
        $bdd->exec('insert into societe(nom_societe, ref_client) values (\'Test\', 10000)');
    }
    else
        echo $infosSociete['nom_societe'].'('.$infosSociete['id_societe'].') ';

    echo $numCommande.' '.$lieu.' '.$debut;
?>