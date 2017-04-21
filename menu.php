<?php
include_once "util.inc.php";

function enTete($titre, $styles, $scripts) {
    ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title><?php echo $titre; ?></title>

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
            <h1 class="ui center aligned blue header"><a href="/gestionPV/"> Gestion des PV </a></h1>
            <div class="item">
                <div class="header">PV</div>
                <div class="menu">
                    <a href="/gestionPV/pv/creationPV.php" class="item lienMenu">
                        Création de PV
                    </a>
                    <a href="/gestionPV/pv/listePV.php" class="item lienMenu">
                        Liste des PV existants
                    </a>
                </div>
            </div>
            <div class="item">
                <div class="header">Appareils</div>
                <div class="menu">
                    <a href="/gestionPV/appareils/ajoutAppareil.php" class="item lienMenu">
                        Ajout d'appareils
                    </a>
                    <a href="/gestionPV/appareils/listeAppareils.php" class="item lienMenu">
                        Liste des appareils existants
                    </a>
                </div>
            </div>
            <div class="item">
                <div class="header">Équipements</div>
                <div class="menu">
                    <a href="/gestionPV/equipements/ajoutEquipement.php" class="item lienMenu">
                        Ajout d'équipements
                    </a>
                    <a href="/gestionPV/equipements/listeEquipements.php" class="item lienMenu">
                        Liste des équipements existants
                    </a>
                </div>
            </div>
        </div>
    <?php
        fonctionMenu();
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
