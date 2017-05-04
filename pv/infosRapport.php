<?php
    include_once "../menu.php";
    verifSession("OP");
    enTete("Informations affaire",
            array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/infos.css", "../style/menu.css"),
            array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

    if ($_GET['num_affaire'] == "" || $_GET['num_equipement'] == "" || $_GET['demandeRecue'] == "" || $_GET['demandeAnalysee'] == "" ||
        $_GET['obtentionOffre'] == "" || $_GET['numAvenant'] == "" ||
        $_GET['procedure'] == "" || $_GET['codeInter'] == "") {
        header("Location: creationRapport.php?erreur=1");
    }

    $bddAffaire = connexion('portail_gestion');

    $affaire = selectAllFromWhere($bddAffaire, "affaire", "num_affaire", "like", $_GET['num_affaire'])->fetch();
    // Id du récepteur de la demande
    $idReceveur = selectAllFromWhere($bddAffaire, "utilisateurs", "nom", "like", $_GET['demandeRecue'])->fetch();
    // Id de l'analyste de la demande

    $idAnalyste = selectAllFromWhere($bddAffaire, "utilisateurs", "nom", "like", $_GET['demandeAnalysee'])->fetch();

    $bddEquipement = connexion('theodolite');
    $equipement = selectAllFromWhere($bddEquipement, "equipement", "concat(Designation, ' ', Type)", "like", $_GET['num_equipement'])->fetch();

    $appelOffre = 1;
    if (!isset($_GET['appelOffre'])) // Si la case n'a pas été cochée
        $appelOffre = 0;

    $valeursTmp =  array("null", $affaire['id_affaire'], $equipement['idEquipement'], $idReceveur['id_utilisateur'], $idAnalyste['id_utilisateur'],
                         $bddAffaire->quote($_GET['obtentionOffre']), $appelOffre, $_GET['numAvenant'], $bddAffaire->quote($_GET['procedure']),
                         $bddAffaire->quote($_GET['codeInter']), "now()");

    insert($bddAffaire, "rapports", $valeursTmp);

    $rapport = selectAllFromWhere($bddAffaire, "rapports", "id_rapport", "=", "last_insert_id()")->fetch();
?>

        <div id="contenu">
            <h1 class="ui blue center aligned huge header">Le rapport a bien été crée !</h1>

            <form class="ui form" method="get" action="modifRapportCA.php">
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