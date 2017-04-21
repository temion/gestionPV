<?php
include_once "../menu.php";

$bdd = connexion('portail_gestion');
$appareil = $bdd->query('select * from appareils where id_appareil = '.$_POST['idAppareil'])->fetch();

enTete("Modification d'un appareil existant",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/ajout.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));
?>

        <div id="contenu">
            <h1 id="titreMenu" class="ui blue center aligned huge header">Modification de l'appareil <?php echo $appareil['systeme'].' '.$appareil['type'].' ('.$appareil['num_serie'].')' ?></h1>

            <form method="post" action="verifModifAppareil.php">
                <table class="ui celled table">
                    <thead>
                        <tr>
                            <th>  </th>
                            <th> Système </th>
                            <th> Type d'appareil </th>
                            <th> Marque </th>
                            <th> Numéro de série </th>
                            <th> Valide jusqu'au </th>
                            <th> Date de calibration </th>
                            <th>  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="titreLigne"> Valeurs existantes </td>

                            <td>
                                <div class="field">
                                    <label> <?php echo $appareil['systeme']; ?> </label>
                                </div>
                            </td>
                            <td>
                                <div class="field">
                                    <label> <?php echo $appareil['type']; ?> </label>
                                </div>
                            </td>
                            <td>
                                <div class="field">
                                    <label> <?php echo $appareil['marque']; ?> </label>
                                </div>
                            </td>
                            <td>
                                <label> <?php echo $appareil['num_serie']; ?> </label>
                            </td>
                            <td>
                                <label> <?php echo conversionDate($appareil['date_valid']); ?> </label>
                            </td>
                            <td>
                                <label> <?php echo conversionDate($appareil['date_calib']); ?> </label>
                            </td>
                            <td>

                            </td>
                        </tr>
                        <tr>
                            <td class="titreLigne"> Nouvelles valeurs </td>

                            <td>
                                <div class="ui input">
                                    <input type="text" name="systeme" placeholder="Système (ex. : Théodolite)">
                                </div>
                            </td>
                            <td>
                                <div class="ui input">
                                    <input type="text" name="type" placeholder="Type (ex. TS06)">
                                </div>
                            </td>
                            <td>
                                <div class="ui input">
                                    <input type="text" name="marque" placeholder="Marque de l'appareil">
                                </div>
                            </td>
                            <td>
                                <div class="ui input">
                                    <input type="text" name="serie" placeholder="Numéro de série">
                                </div>
                            </td>
                            <td>
                                <div class="ui input">
                                    <input type="date" name="date_valid" placeholder="JJ-MM-AAAA">
                                </div>
                            </td>
                            <td>
                                <div class="ui input">
                                    <input type="date" name="date_calib" placeholder="JJ-MM-AAAA">
                                </div>
                            </td>
                            <td>
                                <?php
                                    echo '<button name="idAppareil" value="'.$_POST['idAppareil'].'" class="ui right floated blue button"> Valider </button>'
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </body>
</html>
