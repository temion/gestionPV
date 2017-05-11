<?php

    require_once 'C:\wamp64\www\gestionPV\lib\vendor\mpdf\mpdf\mpdf.php';

    class PDFWriter extends mPDF {

        /**
         * Écrit sur l'en-tête du document.
         *
         * array @param $infos Informations à faire apparaître.
         */
        function enTete($infos) {
            for ($i = 0; $i < sizeof($infos); $i++)
                $this->ecrireHTML("<div id='enTete'>".$infos[$i]."</div>");
        }

        /**
         * Écrit le titre du document.
         *
         * @param string $titre Titre.
         */
        function ecrireTitre($titre) {
            $this->ecrireHTML("<table id='presentation'><tr><td id='td1'>Procès verbal</td><td id='td2'>Inspection & contrôle</td><td id='td3'>".$titre."</td></tr></table>");
        }

        /**
         * Écrit sous forme de tableau les détails de l'affaire.
         *
         * @param array $societeClient Société cliente du contrôle.
         * @param array $equipement Équipement inspecté.
         * @param array $client Client rencontré.
         * @param array $ficheTechniqueEquipement Détails techniques de l'équipement inspecté.
         * @param array $affaire Affaire concernée.
         * @param array $pv PV concerné.
         */
        function detailsAffaire($societeClient, $equipement, $client, $ficheTechniqueEquipement, $affaire, $pv) {
            $this->ecrireHTML("<table class='details'><tr class='titre'><td colspan='4'>Détails de l'affaire</td></tr>");

            $this->ligneDetails(array("Clients : ", $societeClient['nom_societe'], "Numéro équipement : ", $equipement['Designation'].' '.$equipement['Type']));
            $this->ligneDetails(array("Personne rencontrée : ", $client['nom'], "Diamètre équipement : ", ($ficheTechniqueEquipement['diametre']/1000).' m'));
            $this->ligneDetails(array("Numéro commande client : ", $affaire['commande'], "Hauteur : ", ($ficheTechniqueEquipement['hauteurEquipement']/1000).' m'));
            $this->ligneDetails(array("Lieu : ", $affaire['lieu_intervention'], "Hauteur produit : ", "?"));
            $this->ligneDetails(array("Début du contrôle : ", $pv['date_debut'], "Volume : ", "?"));
            $this->ligneDetails(array("Nbre génératrices : ", $ficheTechniqueEquipement['nbGeneratrice'], "Distance entre 2 points : ", "?"));

            $this->ecrireHTML("</table>");

            $this->ecrireHTML("<table class='separateur'><tr><td></td></tr></table>");
        }

        /**
         * Écrit les détails des documents utilisés pour le contrôle.
         *
         * @param array $rapport Rapport concerné.
         */
        function detailsDocuments($rapport) {
            $this->ecrireHTML("<table class='details'><tr class='titre'><td colspan='4'>Documents de référence</td></tr>");
            $this->ecrireHTML("<tr><td>Suivant procédure : </td><td class='info'>".$rapport['procedure_controle']."</td><td>Code d'interprétation : </td><td class='info'>".$rapport['code_inter']."</td></tr>");
            $this->ecrireHTML("</table>");
        }

        /**
         * Indique les contrôles effectués (Interne, externe, périphérique).
         *
         * @param array $pv PV concerné.
         */
        function situationControle($pv) {
            $this->ecrireHTML("<table class='details'><tr class='titre'><td colspan='4'>Situation de contrôle</td></tr>");
            $this->ecrireHTML("<tr><td>Contrôle interne ? </td><td class='info'>".($pv['controle_interne'] ? "OUI" : "NON")."</td><td>Contrôle externe ? </td><td class='info'>".($pv['controle_externe'] ? "OUI" : "NON")."</td></tr>");
            $this->ecrireHTML("<tr><td>Contrôle périphérique ? </td><td class='info'>".($pv['controle_peripherique'] ? "OUI" : "NON")."</td></tr>");
            $this->ecrireHTML("</table>");
        }

        /**
         * Crée la section constatations dans le document, comportant les différentes constatations effectuées par le contrôleur.
         *
         * @param array $constatations Constatations lors du contrôle.
         */
        function constatations($constatations) {
            $this->ecrireHTML("<table><tr class='titre'><td colspan='4'>Constatations</td></tr></table>");

            for ($i = 0; $i < sizeof($constatations); $i++) {
                $this->ecrireHTML("<div class='constatation'>".($i + 1).") ".$constatations[$i]['type_constatation']."</div><div>".$constatations[$i]['constatation']."</div>");
            }
        }

        /**
         * Crée la section conclusions dans le document, comportant les différentes conclusions effectuées par le contrôleur.
         *
         * @param array $conclusions Conclusions du contrôle.
         */
        function conclusions($conclusions) {
            $this->ecrireHTML("<table><tr class='titre'><td colspan='4'>Conclusions</td></tr></table>");

            for ($i = 0; $i < sizeof($conclusions); $i++) {
                $this->ecrireHTML("<div class='conclusion'>".$conclusions[$i]['conclusion']."</div>");
            }

            $this->ecrireHTML("<table class='separateur'><tr><td></td></tr></table>");
        }

        /**
         * Crée la section comportant les derniers détails du PV (Nombre d'annexes, date de génération...) ainsi que les zones de signatures.
         *
         * @param array $pv PV concerné.
         */
        function signatures($pv) {
            $this->ecrireHTML("<table class='signatures'><tr><td>Date : </td><td class='info'>".date("d.m.y")."</td>");
            $this->ecrireHTML("<td colspan='2' rowspan='4' class='visa'>Nom et visa du contrôleur : </td>");
            $this->ecrireHTML("<td colspan='2' rowspan='4' class='visa'>Nom et visa du vérificateur : </td></tr>");
            $this->ecrireHTML("<tr><td>Photos jointes : </td><td class='info'>".($pv['photos_jointes'] == 1 ? "OUI" : "NON")."</td></tr>");
            $this->ecrireHTML("<tr><td>Pièces jointes : </td><td class='info'>".($pv['pieces_jointes'] == 1 ? "OUI" : "NON")."</td></tr>");
            $this->ecrireHTML("<tr><td>Nombre d'annexes : </td><td class='info'>".$pv['nb_annexes']."</td></tr></table>");
        }

        /**
         * Crée une ligne de tableau comportant les informations passées en paramètre.
         *
         * @param array $infos Informations à faire apparaître.
         */
        function ligneDetails($infos) {
            $this->ecrireHTML("<tr><td>".$infos[0]."</td><td class='info'>".$infos[1]."</td><td>".$infos[2]."</td><td class='info'>".$infos[3]."</td></tr>");
        }

        /**
         * Méthode permettant l'écriture et le formatage de code HTML en PDF.
         *
         * @param string $html Texte à écrire sur le document, acceptant les balises HTML.
         * @param int $couche "Ordre d'écriture" du texte passé en paramètre. Les feuilles de style doivent être écrites
         *                     en premières ($couche = 1). Par défaut, ce paramètre est défini à 2.
         */
        function ecrireHTML($html, $couche = 2) {
            $this->WriteHTML($html, $couche);
        }

    }

?>