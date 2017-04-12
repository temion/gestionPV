<?php
    // Données de l'affaire
    $societeClient = $personneClient = $numCommande = $lieu = $debut = $nb_generatrices = null;
    $variablesAffaire = array($societeClient, $personneClient, $numCommande, $lieu, $debut, $nb_generatrices);
    $postAffaire = array($_POST['societe_client'], $_POST['personne_client'], $_POST['num_commande'], $_POST['lieu'], $_POST['debut_controle'], $_POST['nb_generatrices']);

    // Données de l'équipement contrôlé
    $numEquipement = $diametre = $hauteurEquipement = $hauteurProduit = $volume = $distancePoints = null;
    $variablesEquipement = array($numEquipement, $diametre, $hauteurEquipement, $hauteurProduit, $volume, $distancePoints);
    $postEquipement = array($_POST['num_equipement'], $_POST['diam_equipement'], $_POST['hauteur_equipement'], $_POST['hauteur_produit'], $_POST['volume_equipement'], $_POST['distance_points']);

    verifSaisies($variablesAffaire, $postAffaire);
    verifSaisies($variablesEquipement, $postEquipement);

    /*
     * Fonction permettant de vérifier que toutes les données ont été entrées.
     */
    function verifSaisies($var, $post) {
        for ($i = 0; $i < sizeof($var); $i++) {
            if (isset($post[$i]))
                $var[$i] = $post[$i];
        }

        for ($i = 0; $i < sizeof($var); $i++) {
            if ($var[$i] == null)
                header('Location: index.php?erreur='.$var[$i]);
        }
    }
//    echo '<h1>'.$_POST['num_commande'].'</h1>';
//    echo '<h1>'.$_POST['diam_equipement'].'</h1>';
//    if (isset($_POST['num_commande'])) {
//        $numCommande = $_POST['num_commande'];
//        echo '<h1>' . $numCommande . '</h1>';
//    } else {
//        echo '<h1>Non numCommande</h1>';
//    }

//    $personneClient = $_POST['personne_client'];
//    $numCommande = $_POST['num_commande'];
//    $lieu = $_POST['lieu'];
//    $debut = $_POST['debut_controle'];
//    $nb_generatrices = $_POST['nb_generatrices'];
    


//    $bdd = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
//
//    $infosClients = $bdd->query('select * from client where nom like \''.$personneClient.'\'')->fetch();
//    $infosSociete = $bdd->query('select * from societe where nom_societe like \''.$societeClient.'\'')->fetch();
//
//    if (empty($infosClients))
//        echo $personneClient.' ';
//    else
//        echo $infosClients['nom'].'('.$infosClients['id_client'].') ';
//
//    if (empty($infosSociete)) {
//        echo $societeClient;
//    }
//    else
//        echo $infosSociete['nom_societe'].'('.$infosSociete['id_societe'].') ';
//
//    echo $numCommande.' '.$lieu.' '.$debut;
?>