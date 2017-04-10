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
                                <input type="text" name="diamEquipement" placeholder="Diamètre (m)">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>N° Commande client : </label>
                            <div class="field">
                                <input type="text" name="numCommande" placeholder="Numéro de commande">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Hauteur : </label>
                            <div class="field">
                                <input type="text" name="hauteurEquipement" placeholder="Hauteur (m)">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Lieu : </label>
                            <div class="field">
                                <input type="text" name="lieu" placeholder="Lieu de l'affaire">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Hauteur produit : </label>
                            <div class="field">
                                <input type="text" name="hauteurProduit" placeholder="Hauteur du produit (m)">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Début du contrôle : </label>
                            <div class="field">
                                <input type="text" name="debutControle" placeholder="Date de début">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Volume : </label>
                            <div class="field">
                                <input type="text" name="volumeEquipement" placeholder="Volume (m²)">
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="field">
                            <label>Nombre de génératrices : </label>
                            <div class="field">
                                <input type="text" name="nbGeneratrices" placeholder="Nombre de génératrices">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <label>Distance entre 2 points : </label>
                            <div class="field">
                                <input type="text" name="distancePoints" placeholder="Distance (m)">
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>