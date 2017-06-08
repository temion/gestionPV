<?php
include_once "../bdd/bdd.inc.php";

if (isset($_POST['id'])) {
    $controle = selectControlesAutoParId($bddPortailGestion, $_POST['id'])->fetch();
    if ($controle['generation_auto'] == 0)
        $val = 1;
    else
        $val = 0;

    update($bddPortailGestion, "controle_auto", "generation_auto", strval($val), "id_controle_auto", "=", $_POST['id']);
}

if (isset($_POST['id_societe']) && isset($_POST['valeur'])) {
    $controles = selectControlesAutoParSociete($bddPortailGestion, $_POST['id_societe'])->fetchAll();

    for ($i = 0; $i < sizeof($controles); $i++) {
        update($bddPortailGestion, "controle_auto", "generation_auto", $_POST['valeur'], "id_controle_auto", "=", $controles[$i]['id_controle_auto']);
    }
}
