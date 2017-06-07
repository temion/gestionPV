<?php
require_once "util.inc.php";

session_start();
if (!verifSession()) {
    header('Location: /gestionPV/connexion/connexion.php');
    exit;
}

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
    <link rel="icon" href="/gestionPV/images/logo_scopeo.ico"/>

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
                <div class="header">Gestion</div>

                <div class="menu">
                    <a href="/gestionPV/pv/pvAuto.php" class="item lienMenu">
                        Automatisation des PV
                    </a>
                </div>

                <div class="menu">
                    <a href="/gestionPV/planning/calendrier.php" class="item lienMenu">
                        Planning des PV
                    </a>
                </div>

                <div class="menu">
                    <a href="/gestionPV/historique/historique.php" class="item lienMenu">
                        Historique des activités effectuées
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

<script>
    $(function () {
        var liens = $(".lienMenu");
        for (var i = 0; i < liens.length; i++) {
            if (liens[i].id != "deconnexion") {
                liens[i].classList.remove("active", "blue");    // Retire l'attribut actif de tous les liens
                if ($(location).attr('href').startsWith(liens[i].href))
                    liens[i].classList.add("active", "blue");   // Rajoute l'attribut actif au lien actuel
            }
        }

        if (window.matchMedia("(min-width: 1500px)").matches) {
            grandEcran();
        } else {
            petitEcran();
        }
    })

    $(window).on("resize", function () {
        if (window.matchMedia("(min-width: 1500px)").matches)
            grandEcran();
        else
            petitEcran();
    });

    function grandEcran() {
        $("#menu").removeClass("hidden");
        if ($("#menuMobile").length != 0) {
            $("#menuMobile").remove();
        }
    }

    function petitEcran() {
        $("#menu").addClass("hidden");
        if ($("#menuMobile").length == 0) {
            var session = '<?php echo $_SESSION['droit']; ?>';

            var enTeteMenu = "<div class='ui dropdown' id='menuMobile'><i class='content big icon'></i><div id='dropdownMenu' class='menu'>";
            enTeteMenu += "<div class='item'><h1 class='ui center aligned blue header'><a href='/gestionPV/'> Gestion des PV </a></h1></div>";

            var menuPV = "<div class='header'> PV </div>";
            if (session == "CA") {
                menuPV += "<div class='item'><a href='/gestionPV/pv/creationRapport.php'>Création de rapport</a></div>";
                menuPV += "<div class='item'><a href='/gestionPV/pv/listeRapportsCA.php'>Liste des rapports existants</a></div>";
                menuPV += "<div class='item'><a href='/gestionPV/pv/listePVCA.php'>Liste des PV</a></div>";
            } else
                menuPV += "<div class='item'><a href='/gestionPV/pv/listePVOP.php'>Liste des PV</a></div>";


            var menuAppareils = "<div class='header'> Appareils </div>";
            if (session == "CA")
                menuAppareils += "<div class='item'><a href='/gestionPV/appareils/ajoutAppareil.php'>Ajout d'appareils</a></div>";
            menuAppareils += "<div class='item'><a href='/gestionPV/appareils/listeAppareils.php'>Liste des appareils existants</a></div>";

            var menuEquipements = "<div class='header'> Équipements </div>";
            if (session == "CA")
                menuEquipements += "<div class='item'><a href='/gestionPV/equipements/ajoutEquipement.php'>Ajout d'équipements</a></div>";
            menuEquipements += "<div class='item'><a href='/gestionPV/equipements/listeEquipements.php'>Liste des équipements existants</a></div>";

            if (session == "CA") {
                var menuPlanning = "<div class='header'> Planning </div>";
                menuPlanning += "<div class='item'><a href='/gestionPV/planning/calendrier.php'>Planning des PV</a></div>";
            }

            var deconnexion = "<div class='header' id='deconnexion'><a style='color: red' href='/gestionPV/connexion/connexion.php'>Déconnexion</a></div>";

            var finMenu = "</div></div>";

            var menuMobile = $(enTeteMenu + menuPV + menuAppareils + menuEquipements + (session == "CA" ? menuPlanning : "") + deconnexion + finMenu);

            $("#contenu").before(menuMobile);

            $("#menuMobile").on("mouseover", function() {$(".content.big.icon").addClass("blue")});
            $("#menuMobile").on("mouseleave", function() {$(".content.big.icon").removeClass("blue")});
            $("#menuMobile").dropdown();
        }
    }

    $(".circle.help.icon").on('click', function () {
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
<?php } ?>