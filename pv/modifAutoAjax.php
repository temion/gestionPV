<?php
include_once "../bdd/bdd.inc.php";

$controle = selectControlesAutoParId($bddPortailGestion, $_POST['id'])->fetch();
if ($controle['generation_auto'] == 0)
    $val = 1;
else
    $val = 0;

update($bddPortailGestion, "controle_auto", "generation_auto", strval($val), "id_controle_auto", "=", $_POST['id']);