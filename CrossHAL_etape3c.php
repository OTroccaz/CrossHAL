<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Etape 3c - Stage 3c
 */
 
//Etape 3c > Manuscrit auteurs (via OverHAL) non référencés dans HAL
include("./CrossHAL_Stats_overhal_mails_UR1.php");
//var_dump($Stats_OH_Mails);
echo "<div id='cpt'></div>";
echo "<table class='table table-responsive table-bordered table-centered table-sm text-center small'>";
echo "<thead class='thead-dark'>";
echo "<tr>";
echo "<th><strong>ID</strong></th>";
echo "<th><strong>Lien DOI</strong></th>";
echo "<th><strong>DOI</strong></th>";
echo "<th><strong>Mails</strong></th>";
echo "<th><strong>Quand</strong></th>";
echo "<th><strong>Type</strong></th>";
echo "</tr></thead><tbody>";
$ajout = "";
$listDOIWos = "DO=(";
$listDOIPubmed = "";
$listDOIScopus = "";
$listDOICrossRef = "";
for ($i = 0; $i < count($Stats_OH_Mails); $i++) {
	progression($i+1, count($Stats_OH_Mails), $iPro);
	$doiCpt = $Stats_OH_Mails[$i]["Article"];
	$doi = str_replace("https://doi.org/", "", $Stats_OH_Mails[$i]["Article"]);
	$quand = $Stats_OH_Mails[$i]["Quand"];
	$tabQuand = explode("/", $quand);
	$quand = mktime(0, 0, 0, $tabQuand[1], $tabQuand[0], $tabQuand[2]);
	$limite = 60 * 60 * 24 * 30;//30 jours
	//if ($doi != "" && ($Stats_OH_Mails[$i]["Type"] == "P" || $Stats_OH_Mails[$i]["Reponse"] == "MS") && ((time() - $quand) > $limite) && strpos($doiCpt, "https://doi.org/") !== false) {
	if ($doi != "" && ((time() - $quand) > $limite) && strpos($doiCpt, "https://doi.org/") !== false) {
		$reqAPI = "https://api.archives-ouvertes.fr/search/univ-rennes/?fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(ART%20OR%20COUV)%20AND%20submitType_s:*%20AND%20doiId_s:%22".$doi."%22&rows=10000&fl=halId_s,docid,contributorFullName_s,linkExtId_s";
		$reqAPI = str_replace('"', '%22', $reqAPI);
		$reqAPI = str_replace(" ", "%20", $reqAPI);
		//echo $reqAPI.'<br>';
		askCurl($reqAPI, $arrayHAL);
		$numFound = 0;
		$numFound = $arrayHAL["response"]["numFound"];
		//echo 'toto : '.$numFound.'<br>';
		if ($numFound == 0) {
			echo "<tr>";
			$notice = $i+1;
			echo "<td style='text-align: center;'>".$notice."</td>";
			echo "<td style='text-align: center;'><a target='_blank' href='".$Stats_OH_Mails[$i]["Article"]."'><img title='DOI' src='./img/doi.jpg'></a></td>";
			echo "<td style='text-align: center;'>".$doi."</td>";
			echo "<td style='text-align: center;'>".$Stats_OH_Mails[$i]["Destinataire"]."</td>";
			echo "<td style='text-align: center;'>".$Stats_OH_Mails[$i]["Quand"]."</td>";
			if ($Stats_OH_Mails[$i]["Type"] == "P") {$type = "P";}else{$type = "MS";}
			echo "<td style='text-align: center;'>".$type."</td>";
			
			$listDOIWos .= $ajout.$doi;
			$listDOIPubmed .= $ajout.$doi."[Location ID]";
			$listDOIScopus .= $ajout."DOI(".$doi.")";
			$listDOICrossRef .= $ajout.$doi;
			$ajout = " OR ";
			
			echo "</tr>";
			ob_flush();
			flush();
			ob_flush();
			flush();
		}
	}
}
$listDOIWos .= ")";		
echo "</tbody></table>";
echo "<script>";
echo "document.getElementById('cpt').style.display = 'none'";
echo "</script>";
echo "Requêtes DOI :<br><br>";
echo "<strong>Wos</strong> > ".$listDOIWos."<br><br>";
echo "<strong>Pubmed</strong> > ".$listDOIPubmed."<br><br>";
echo "<strong>Scopus</strong> > ".$listDOIScopus."<br><br>";
echo "<strong>CrossRef</strong> > ".$listDOICrossRef."<br><br>";
//Fin étape 3c
?>