<?php
    include_once "../menu.php";
    enTete("Informations affaire",
            array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
            array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    if ($_POST['num_affaire'] == "" || $_POST['num_equipement'] == "" || $_POST['demandeRecue'] == "" || $_POST['demandeAnalysee'] == "" ||
        $_POST['obtentionOffre'] == "" || $_POST['numAvenant'] == "" ||
        $_POST['procedure'] == "" || $_POST['codeInter'] == "") {
        header("Location: creationPV.php?erreur=1");
    }


    $bddAffaire = connexion('portail_gestion');
    $affaire = $bddAffaire->query('SELECT * FROM affaire WHERE num_affaire LIKE ' . $bddAffaire->quote($_POST['num_affaire']))->fetch();
    // Id du récepteur de la demande
    $idReceveur = $bddAffaire->query('select * from utilisateurs where nom like '.$bddAffaire->quote($_POST['demandeRecue']))->fetch();
    // Id de l'analyste de la demande
    $idAnalyste = $bddAffaire->query('select * from utilisateurs where nom like '.$bddAffaire->quote($_POST['demandeAnalysee']))->fetch();

    $appelOffre = 1;
    if (!isset($_POST['appelOffre'])) // Si la case n'a pas été cochée
        $appelOffre = 0;

    $bddEquipement = connexion('theodolite');
    $equipement = $bddEquipement->query('SELECT * FROM equipement WHERE concat(Designation, \' \', type) LIKE ' . $bddEquipement->quote($_POST['num_equipement']))->fetch();

    $bddAffaire->exec('insert into affaire_inspection values(null, '.$affaire['id_affaire'] . ' , 
                                                                             '.$equipement['idEquipement'] . ' ,
                                                                             '.$idReceveur['id_utilisateur']. ',
                                                                             '.$idAnalyste['id_utilisateur']. ', 
                                                                             '.$bddAffaire->quote($_POST['obtentionOffre']).',
                                                                             '.$appelOffre.',
                                                                             '.$_POST['numAvenant'].',
                                                                             '.$bddAffaire->quote($_POST['procedure']).',
                                                                             '.$bddAffaire->quote($_POST['codeInter']).',
                                                                             now())');

    $affaireInspection = $bddAffaire->query('select * from affaire_inspection where id_affaire_inspection = last_insert_id()')->fetch();
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">L'affaire n°<?php echo $affaireInspection['id_affaire_inspection'] ?> a bien été crée !</h1>

            <form class="ui form" method="post" action="modifPVCA.php">
                <?php creerApercuDetails($affaireInspection); ?>
                <table>
                    <?php creerApercuDocuments($affaireInspection); ?>
                    <tr>
                        <td colspan="2"><button class="ui right floated blue button">Valider</button></td>
                    </tr>
                </table>
                <?php
                    echo '<input type="hidden" name="idAffaire" value="'.$affaireInspection['id_affaire_inspection'].'">';
                ?>
            </form>
        </div>
    </body>
</html>