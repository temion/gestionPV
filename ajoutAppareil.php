<?php
    include_once "menu.php";
enTete("Ajout d'un nouvel appareil",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "style/ajoutAppareil.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));
?>

<div id="contenu">
    <h1 id="titreMenu" class="ui blue center aligned huge header">Ajout d'un appareil à la base</h1>
    <?php
        if (isset($_GET['erreur']) && $_GET['erreur'] == 1) {
            echo '<div class="ui message">';
            echo '<div class="header"> Erreur ! </div>';
            echo '<p id="erreur"> Veuillez remplir tous les champs précédés par un astérisque avec des valeurs valides. </p>';
            echo '</div>';
        }

        if (isset($_GET['ajout']) && $_GET['ajout'] == 1) {
            echo '<div class="ui message">';
            echo '<div class="header"> Succès ! </div>';
            echo '<p> Votre appareil a bien été ajouté à la base ! </p>';
            echo '</div>';
        }
    ?>

    <form method="post" action="verifAjoutAppareil.php">
        <table>
            <tr>
                <td>
                    <div class="field">
                        <label>* Type d'appareil : </label>
                        <div class="ui input">
                            <input type="text" name="type" placeholder="Type (ex. : Théodolite)">
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
                            <input type="text" name="date_valid" placeholder="JJ-MM-AAAA">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>* Date de calibration : </label>
                        <div class="ui input">
                            <input type="text" name="date_calib" placeholder="JJ-MM-AAAA">
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

<?php
    fonctionMenu();
?>
