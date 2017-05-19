<?php
session_start();
require_once "util.inc.php";

if (isset($_POST['utilisateur']) && $_POST['utilisateur'] != "") {
    $_SESSION['droit'] = $_POST['utilisateur'];
}

/**
 * Définit le titre, les feuilles de style et les scripts de la page.
 *
 * @param string $titre Titre de la page.
 * @param array $styles Chemin des feuilles de style.
 * @param array $scripts Chemin des fichiers de script.
 */
function enTete($titre, $styles, $scripts) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titre; ?></title>

    <?php
    for ($i = 0; $i < sizeof($styles); $i++) {
        echo '<link rel="stylesheet" href="' . $styles[$i] . '"/>';
    }

    for ($i = 0; $i < sizeof($scripts); $i++) {
        echo '<script src="' . $scripts[$i] . '"></script>';
    }
    ?>
</head>

<body>
    <div id="menu" class="ui vertical menu">
        <h1 class="ui center aligned blue header"><a href="/gestionPV/"> Gestion des PV </a></h1>
        <?php if (isset($_SESSION['connexion']) && $_SESSION['connexion'] == 1) { ?>
            <div class="item">
                <div class="header">PV</div>
                <div class="menu">
                    <?php
                    if (isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") { ?>
                        <a href="/gestionPV/pv/creationRapport.php" class="item lienMenu">
                            Création de rapport
                        </a>
                        <a href="/gestionPV/pv/listeRapportsCA.php" class="item lienMenu">
                            Liste des rapports d'inspection existants
                        </a><a href="/gestionPV/pv/listePVCA.php" class="item lienMenu">
                            Liste des PV
                        </a>
                        <?php
                    } else { ?>
                        <a href="/gestionPV/pv/listePVOP.php" class="item lienMenu">
                            Liste des PV existants
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="item">
                <div class="header">Appareils</div>
                <div class="menu">
                    <?php
                    if (isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") { ?>
                        <a href="/gestionPV/appareils/ajoutAppareil.php" class="item lienMenu">
                            Ajout d'appareils
                        </a>
                        <?php
                    }
                    ?>
                    <a href="/gestionPV/appareils/listeAppareils.php" class="item lienMenu">
                        Liste des appareils existants
                    </a>
                </div>
            </div>
            <div class="item">
                <div class="header">Équipements</div>
                <div class="menu">
                    <?php
                    if (isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") { ?>
                        <a href="/gestionPV/equipements/ajoutEquipement.php" class="item lienMenu">
                            Ajout d'équipements
                        </a>
                        <?php
                    }
                    ?>
                    <a href="/gestionPV/equipements/listeEquipements.php" class="item lienMenu">
                        Liste des équipements existants
                    </a>
                </div>
            </div>
            <?php if (isset($_SESSION['droit']) && $_SESSION['droit'] == "CA") { ?>
                <div class="item">
                    <div class="header">Planning</div>

                    <div class="menu">
                        <a href="/gestionPV/planning/calendrier.php" class="item lienMenu">
                            Planning des PV
                        </a>
                    </div>
                </div>
            <?php } ?>
        <div class="item">
            <div id="deconnexion" class="menu">
                <a style="color: red;" href="/gestionPV/connexion/connexion.php" class="item">
                    DÉCONNEXION
                </a>
            </div>
        </div>
        <?php } ?>
        <div id="help" class="item">
            <i id="iconeAide" class="help big circle icon"></i>
        </div>
    </div>

<?php
fonctionMenu();
}

/**
 * Vérifie qu'une session est ouverte, pour éviter l'accès direct aux différentes fonctionnalités du portail.
 *
 * @param string $droit Type de droit à empêcher.
 */
function verifSession($droit = "") {
    if (!isset($_SESSION['droit']) || $_SESSION['droit'] == $droit)
        header('Location: /gestionPV/index.php');
}

/**
 * Permet la coloration des liens actifs du menu.
 */
function fonctionMenu() {
    ?>
    <script>
        $(function () {
            var liens = $(".lienMenu");
            for (var i = 0; i < liens.length; i++) {
                if (liens[i].id != "deconnexion") {
                    liens[i].classList.remove("active", "blue");
                    if ($(location).attr('href').startsWith(liens[i].href))
                        liens[i].classList.add("active", "blue");
                }
            }
        })

        $(".circle.help.icon").on('click', function() {
            $('#modalAide').modal('show');
        });

        $("#iconeAide").on('mouseover', function () {
            console.log('mouseover');
            $("#iconeAide").addClass("blue");
        });

        $("#iconeAide").on('mouseleave', function () {
            console.log('mouseleave');
            $("#iconeAide").removeClass("blue");
        });
    </script>
    <?php
}

?>
