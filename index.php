<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>index</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css"/>
        <link rel="stylesheet" href="style/style.css"/>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"></script>
    </head>

    <body>
    <form class="ui form">
        <table>
            <tr>
                <th colspan="2"><h4 class="ui dividing header">Gestion de rapport</h4></th>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Clients : </label>
                        <div class="field">
                            <input type="text" name="societeClient" placeholder="Nom de la société">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>N° Equipement : </label>
                        <div class="field">
                            <input type="text" name="numEquipement" placeholder="N° Equipement">
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="field">
                        <label>Personne rencontrée : </label>
                        <div class="field">
                            <input type="text" name="personneClient" placeholder="Personne rencontrée">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="field">
                        <label>Diamètre : </label>
                        <div class="field">
                            <input type="text" name="diamEquipement" placeholder="Diamètre">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </form>
    </body>
</html>