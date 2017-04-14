<?php
    function enTete($titre, $styles, $scripts) {
        ?>
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title><?php echo $titre; ?></title>

                <link rel="stylesheet" href="style/menu.css"/>
                <?php
                    for ($i = 0; $i < sizeof($styles); $i++) {
                        echo '<link rel="stylesheet" href="'.$styles[$i].'"/>';
                    }

                    for ($i = 0; $i < sizeof($scripts); $i++) {
                        echo '<script src="'.$scripts[$i].'"></script>';
                    }
                ?>

            </head>

            <body>
            <div id="menu" class="ui vertical menu">
                <h1 class="ui center aligned blue header"> Gestion des PV </h1>
                <a href="creationPV.php" class="active blue item lienMenu">
                    Création de PV
                </a>
                <a href="listePV.php" class="item lienMenu">
                    Liste des PV existants
                </a>
            </div>
        <?php
    }

    function fonctionMenu() {
        ?>
            <script>
                $(function () {
                    var liens = $(".lienMenu");
                    for (var i = 0; i < liens.length; i++) {
                        liens[i].classList.remove("active", "blue");
                        if (liens[i].href == $(location).attr('href'))
                            liens[i].classList.add("active", "blue");
                    }
                })
            </script>
        <?php
    }
?>