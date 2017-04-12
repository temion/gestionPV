<?php
    $bddAffaires = new PDO('mysql:host=localhost; dbname=portail_gestion; charset=utf8', 'root', '');
    $affaires = $bddAffaires->query('select distinct num_affaire from affaire')->fetchAll(PDO::FETCH_COLUMN);
    $typeControles = $bddAffaires->query('select * from type_controle')->fetchAll();

    $bddEquipement = new PDO('mysql:host=localhost; dbname=theodolite; charset=utf8', 'root', '');
    $equipement = $bddEquipement->query('select * from equipement')->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Création de PV</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.css"/>
        <link rel="stylesheet" href="../style/creaPV.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/1.11.8/semantic.min.js"></script>
    </head>

    <body>
        <h1 class="ui blue center aligned huge header">Création d'un PV</h1>
        <form method="post" action="infosPV.php">
            <table>
                <tr>
                    <th colspan="2"><h4 class="ui dividing header">Affaire & équipement</h4></th>
                </tr>
                <tr>
                    <td>
                        <div class="field">
                            <label>Numéro de l'affaire : </label>
                            <div class="field">
                                <select class="ui search dropdown" name="num_affaire">
                                    <?php
                                        for ($i = 0; $i < sizeof($affaires); $i++) {
                                            echo '<option>'.$affaires[$i].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="field">
                            <label>Numéro de l'équipement à inspecter : </label>
                            <div class="field">
                                <select class="ui search dropdown" name="num_equipement">
                                    <?php
                                        for ($i = 0; $i < sizeof($equipement); $i++) {
                                            echo '<option>'.$equipement[$i]['Designation'].' '.$equipement[$i]['Type'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <th colspan="5"><h4 class="ui dividing header">Contrôles à effectuer</h4></th>
                </tr>
                <tr>
                    <td>
                        <label>Contrôle n° 1</label>
                        <div class="field">
                            <select class="ui search dropdown controles" name="controle1">
                                <option selected> </option>
                                <?php
                                    for ($i = 0; $i < sizeof($typeControles); $i++) {
                                        echo '<option>'.$typeControles[$i]['libelle'].' ('.$typeControles[$i]['code'].')</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </td>

                    <td>
                        <label>Contrôle n° 2</label>
                        <div class="field">
                            <select class="ui search dropdown controles" name="controle2" disabled>
                                <option selected> </option>
                                <?php
                                    for ($i = 0; $i < sizeof($typeControles); $i++) {
                                        echo '<option>'.$typeControles[$i]['libelle'].' ('.$typeControles[$i]['code'].')</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </td>

                    <td>
                        <label>Contrôle n° 3</label>
                        <div class="field">
                            <select class="ui search dropdown controles" name="controle3" disabled>
                                <option selected> </option>
                                <?php
                                    for ($i = 0; $i < sizeof($typeControles); $i++) {
                                        echo '<option>'.$typeControles[$i]['libelle'].' ('.$typeControles[$i]['code'].')</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </td>

                    <td>
                        <label>Contrôle n° 4</label>
                        <div class="field">
                            <select class="ui search dropdown controles" name="controle4" disabled>
                                <option selected> </option>
                                <?php
                                    for ($i = 0; $i < sizeof($typeControles); $i++) {
                                        echo '<option>'.$typeControles[$i]['libelle'].' ('.$typeControles[$i]['code'].')</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </td>

                    <td>
                        <label>Contrôle n° 5</label>
                        <div class="field">
                            <select class="ui search dropdown controles" name="controle5" disabled>
                                <option selected> </option>
                                <?php
                                    for ($i = 0; $i < sizeof($typeControles); $i++) {
                                        echo '<option>'.$typeControles[$i]['libelle'].' ('.$typeControles[$i]['code'].')</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
            </table>
            <button class="ui right floated blue button">Valider</button>
        </form>
    </body>
</html>

<script>
    $(function() {
        $("select[name='controle1']").on("change", function () {
            $("select[name='controle2']").prop('disabled', false);
        });

        $("select[name='controle2']").on("change", function () {
            $("select[name='controle3']").prop('disabled', false);
        });

        $("select[name='controle3']").on("change", function () {
            $("select[name='controle4']").prop('disabled', false);
        });

        $("select[name='controle4']").on("change", function () {
            $("select[name='controle5']").prop('disabled', false);
        });
    })
</script>
