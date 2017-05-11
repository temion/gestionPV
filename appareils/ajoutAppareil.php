<?php
    require_once "../menu.php";
    verifSession("OP");
    enTete("Ajout d'un nouvel appareil",
        array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/ajout.css", "../style/menu.css"),
        array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));
?>

        <div id="contenu">
            <h1 id="titreMenu" class="ui blue center aligned huge header">Ajout d'un appareil à la base</h1>
            <?php
                afficherMessage('erreur', "Erreur", "Veuillez remplir tous les champs précédés par un astérisque avec des valeurs valides.", "", "");
                afficherMessage('ajout', "Succès !", "Votre appareil a bien été ajouté à la base !", "", "");
            ?>

            <form method="post" action="verifAjoutAppareil.php">
                <table>
                    <tr>
                        <td>
                            <div class="field">
                                <label>* Système : </label>
                                <div class="ui input">
                                    <input type="text" name="systeme" placeholder="Système (ex. : Théodolite)">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="field">
                                <label>* Type d'appareil : </label>
                                <div class="ui input">
                                    <input type="text" name="type" placeholder="Type (ex. : TS06)">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="field">
                                <label>* Marque : </label>
                                <div class="ui input">
                                    <input type="text" name="marque" placeholder="Marque de l'appareil">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="field">
                                <label>* Numéro de série : </label>
                                <div class="ui input">
                                    <input type="text" name="serie" placeholder="Numéro de série">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="field">
                                <label>* Valide jusqu'au : </label>
                                <div class="ui input">
                                    <input type="date" name="date_valid" placeholder="JJ-MM-AAAA">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="field">
                                <label>* Date de calibration : </label>
                                <div class="ui input">
                                    <input type="date" name="date_calib" placeholder="JJ-MM-AAAA">
                                </div>
                            </div>
                        </td>
                        <td>
                            <button class="ui right floated blue button">Valider</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </body>
</html>
