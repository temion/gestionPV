<?php
require_once "../menu.php";
verifSession("OP");
enTete("Modification d'un appareil existant",
    array("https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css", "../style/ajout.css", "../style/menu.css"),
    array("https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js", "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js", "https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"));

$appareil = selectAppareilParId($bddPortailGestion, $_POST['idAppareil'])->fetch();
?>

<div id="contenu">
    <h1 id="titreMenu" class="ui blue center aligned huge header">Modification de
        l'appareil <?php echo $appareil['systeme'] . ' ' . $appareil['type'] . ' (' . $appareil['num_serie'] . ')' ?></h1>

    <form method="post" class="ui form" action="verifModifAppareil.php">
        <table class="ui celled table">
            <thead>
            <tr>
                <th></th>
                <th> Système</th>
                <th> Type d'appareil</th>
                <th> Marque</th>
                <th> Numéro de série</th>
                <th> Valide jusqu'au</th>
                <th> Date de calibration</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="titreLigne"> Valeurs existantes</td>

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
                <td class="titreLigne"> Nouvelles valeurs</td>

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
                    echo '<button name="idAppareil" value="' . $_POST['idAppareil'] . '" class="ui right floated blue button"> Valider </button>'
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
    <form action="listeAppareils.php">
        <button style="margin-top: 2vh;" class="ui right floated blue button"> Retour à la liste des appareils </button>
    </form>
</div>

<div class="ui large modal" id="modalAide">
    <div class="header">Aide</div>
    <div>
        <p>
            Cette page indique les caractéristiques actuelles de l'appareil, et vous permet d'en modifier certains champs.
            Une fois les champs souhaités remplis, cliquez sur Valider et les informations concernant l'appareil seront mises à jour.
        </p>
        <button onclick="$('#modalAide').modal('hide')" id="fermerModal" class="ui right floated blue button"> OK
        </button>
    </div>
</div>

</body>
</html>
