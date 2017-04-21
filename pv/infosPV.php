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


    $bddAffaire = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $affaire = $bddAffaire->query('SELECT * FROM affaire WHERE num_affaire LIKE ' . $bddAffaire->quote($_POST['num_affaire']))->fetch();
    // Id du récepteur de la demande
    $idReceveur = $bddAffaire->query('select * from utilisateurs where nom like '.$bddAffaire->quote($_POST['demandeRecue']))->fetch();
    // Id de l'analyste de la demande
    $idAnalyste = $bddAffaire->query('select * from utilisateurs where nom like '.$bddAffaire->quote($_POST['demandeAnalysee']))->fetch();

    $appelOffre = 1;
    if (!isset($_POST['appelOffre'])) // Si la case n'a pas été cochée
        $appelOffre = 0;

    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');
    $equipement = $bddEquipement->query('SELECT * FROM equipement WHERE concat(Designation, \' \', type) LIKE ' . $bddEquipement->quote($_POST['num_equipement']))->fetch();

    $bddAffaire->exec('insert into pv_controle values(null, '.$affaire['id_affaire'] . ' , 
                                                                      '.$equipement['idEquipement'] . ' ,
                                                                      '.$idReceveur['id_utilisateur']. ',
                                                                      '.$idAnalyste['id_utilisateur']. ', 
                                                                      '.$bddAffaire->quote($_POST['obtentionOffre']).',
                                                                      '.$appelOffre.',
                                                                      '.$_POST['numAvenant'].',
                                                                      '.$bddAffaire->quote($_POST['procedure']).',
                                                                      '.$bddAffaire->quote($_POST['codeInter']).',
                                                                      now())');

    $pv = $bddAffaire->query('select * from pv_controle where id_pv = last_insert_id()')->fetch();
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">Le PV n°<?php echo $pv['id_pv'] ?> a bien été crée !</h1>

            <form class="ui form" method="post" action="modifPV.php">
                <?php creerApercuDetails($pv); ?>
                <table>
                    <?php creerApercuDocuments($pv); ?>
                    <tr>
                        <td colspan="2"><button class="ui right floated blue button">Valider</button></td>
                    </tr>
                </table>
                <?php
                    echo '<input type="hidden" name="idPV" value="'.$pv['id_pv'].'">';
                ?>
            </form>
        </div>
    </body>
</html>