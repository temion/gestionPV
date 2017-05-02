<?php
    include_once "../menu.php";
    verifSession("OP");
    enTete("Informations affaire",
            array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
            array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    if ($_POST['num_affaire'] == "" || $_POST['num_equipement'] == "" || $_POST['demandeRecue'] == "" || $_POST['demandeAnalysee'] == "" ||
        $_POST['obtentionOffre'] == "" || $_POST['numAvenant'] == "" ||
        $_POST['procedure'] == "" || $_POST['codeInter'] == "") {
        header("Location: creationRapport.php?erreur=1");
    }

    $bddAffaire = connexion('portail_gestion');

    $affaire = selectAllFromWhere($bddAffaire, "affaire", "num_affaire", "like", $_POST['num_affaire'])->fetch();
    // Id du récepteur de la demande
    $idReceveur = selectAllFromWhere($bddAffaire, "utilisateurs", "nom", "like", $_POST['demandeRecue'])->fetch();
    // Id de l'analyste de la demande

    $idAnalyste = selectAllFromWhere($bddAffaire, "utilisateurs", "nom", "like", $_POST['demandeAnalysee'])->fetch();

    $bddEquipement = connexion('theodolite');
    $equipement = selectAllFromWhere($bddEquipement, "equipement", "concat(Designation, ' ', Type)", "like", $_POST['num_equipement'])->fetch();

    $appelOffre = 1;
    if (!isset($_POST['appelOffre'])) // Si la case n'a pas été cochée
        $appelOffre = 0;

    $valeursTmp =  array("null", $affaire['id_affaire'], $equipement['idEquipement'], $idReceveur['id_utilisateur'], $idAnalyste['id_utilisateur'],
                         $bddAffaire->quote($_POST['obtentionOffre']), $appelOffre, $_POST['numAvenant'], $bddAffaire->quote($_POST['procedure']),
                         $bddAffaire->quote($_POST['codeInter']), "now()");

    insert($bddAffaire, "rapports", $valeursTmp);

    $rapport = selectAllFromWhere($bddAffaire, "rapports", "id_rapport", "=", "last_insert_id()")->fetch();
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">Le rapport a bien été crée !</h1>

            <form class="ui form" method="post" action="modifRapportCA.php">
                <?php creerApercuDetails($rapport); ?>
                <table>
                    <?php creerApercuDocuments($rapport); ?>
                    <tr>
                        <td colspan="2"><button class="ui right floated blue button">Valider</button></td>
                    </tr>
                </table>
                <?php
                    echo '<input type="hidden" name="idRapport" value="'.$rapport['id_rapport'].'">';
                ?>
            </form>
        </div>
    </body>
</html>