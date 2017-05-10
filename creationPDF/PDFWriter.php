<?php

    require_once 'C:\wamp64\www\gestionPV\lib\vendor\mpdf\mpdf\mpdf.php';

    class PDFWriter extends mPDF {

        function enTete($infos) {
            for ($i = 0; $i < sizeof($infos); $i++)
                $this->ecrireHTML("<div id='enTete'>".$infos[$i]."</div>");
        }

        function ecrireTitre($titre) {
            $this->ecrireHTML("<table id='presentation'><tr><td id='td1'>Procès verbal</td><td id='td2'>Inspection & contrôle</td><td id='td3'>".$titre."</td></tr></table>");
        }

        function detailsAffaire($societeClient, $equipement, $client, $ficheTechniqueEquipement, $affaire, $pv) {
            $this->ecrireHTML("<table class='details'><tr class='titre'><td colspan='4'>Détails de l'affaire</td></tr>");

            $this->ligneDetails(array("Clients : ", $societeClient['nom'], "Numéro équipement : ", $equipement['Designation'].' '.$equipement['Type']));
            $this->ligneDetails(array("Personne rencontrée : ", $client['nom'], "Diamètre équipement : ", ($ficheTechniqueEquipement['diametre']/1000).' m'));
            $this->ligneDetails(array("Numéro commande client : ", $affaire['commande'], "Hauteur : ", ($ficheTechniqueEquipement['hauteurEquipement']/1000).' m'));
            $this->ligneDetails(array("Lieu : ", $affaire['lieu_intervention'], "Hauteur produit : ", "?"));
            $this->ligneDetails(array("Début du contrôle : ", $pv['date'], "Volume : ", "?"));
            $this->ligneDetails(array("Nbre génératrices : ", $ficheTechniqueEquipement['nbGeneratrice'], "Distance entre 2 points : ", "?"));

            $this->ecrireHTML("</table>");

            $this->ecrireHTML("<table class='separateur'><tr><td></td></tr></table>");
        }

        function detailsDocuments($rapport) {
            $this->ecrireHTML("<table class='details'><tr class='titre'><td colspan='4'>Documents de référence</td></tr>");
            $this->ecrireHTML("<tr><td>Suivant procédure : </td><td class='info'>".$rapport['procedure_controle']."</td><td>Code d'interprétation : </td><td class='info'>".$rapport['code_inter']."</td></tr>");
            $this->ecrireHTML("</table>");
        }

        function situationControle($pv) {
            $this->ecrireHTML("<table class='details'><tr class='titre'><td colspan='4'>Situation de contrôle</td></tr>");
            $this->ecrireHTML("<tr><td>Contrôle interne ? </td><td class='info'>".($pv['controle_interne'] ? "OUI" : "NON")."</td><td>Contrôle externe ? </td><td class='info'>".($pv['controle_externe'] ? "OUI" : "NON")."</td></tr>");
            $this->ecrireHTML("<tr><td>Contrôle périphérique ? </td><td class='info'>".($pv['controle_peripherique'] ? "OUI" : "NON")."</td></tr>");
            $this->ecrireHTML("</table>");
        }

        function constatations($constatations) {
            $this->ecrireHTML("<table><tr class='titre'><td colspan='4'>Constatations</td></tr></table>");

            for ($i = 0; $i < sizeof($constatations); $i++) {
                $this->ecrireHTML("<div class='constatation'>".($i + 1).") ".$constatations[$i]['type_constatation']."</div><div>".$constatations[$i]['constatation']."</div>");
            }
        }

        function conclusions($conclusions) {
            $this->ecrireHTML("<table><tr class='titre'><td colspan='4'>Conclusions</td></tr></table>");

            for ($i = 0; $i < sizeof($conclusions); $i++) {
                $this->ecrireHTML("<div class='conclusion'>".$conclusions[$i]['conclusion']."</div>");
            }

            $this->ecrireHTML("<table class='separateur'><tr><td></td></tr></table>");
        }

        function signatures($pv) {
            $this->ecrireHTML("<table class='signatures'><tr><td>Date : </td><td class='info'>".date("d.m.y")."</td>");
            $this->ecrireHTML("<td colspan='2' rowspan='4' class='visa'>Nom et visa du contrôleur : </td>");
            $this->ecrireHTML("<td colspan='2' rowspan='4' class='visa'>Nom et visa du vérificateur : </td></tr>");
            $this->ecrireHTML("<tr><td>Photos jointes : </td><td class='info'>".($pv['photos_jointes'] == 1 ? "OUI" : "NON")."</td></tr>");
            $this->ecrireHTML("<tr><td>Pièces jointes : </td><td class='info'>".($pv['pieces_jointes'] == 1 ? "OUI" : "NON")."</td></tr>");
            $this->ecrireHTML("<tr><td>Nombre d'annexes : </td><td class='info'>".$pv['nb_annexes']."</td></tr></table>");
        }

        function ligneDetails($infos) {
            $this->ecrireHTML("<tr><td>".$infos[0]."</td><td class='info'>".$infos[1]."</td><td>".$infos[2]."</td><td class='info'>".$infos[3]."</td></tr>");
        }

        function ecrireHTML($html, $couche = 2) {
            $this->WriteHTML($html, $couche);
        }

    }

?>