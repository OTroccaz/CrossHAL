<?php
//Etape 3c > Manuscrit auteurs (via OverHAL) non référencés dans HAL
include("./Stats-overhal-mails-UR1.php");
//var_dump($Stats_OH_Mails);
echo "<div id='cpt'></div>";
echo "<table class='table table-striped table-bordered table-hover;'>";
echo "<tr>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>ID</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Lien DOI</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>DOI</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Mails</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Quand</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Type</strong></td>";
echo "</tr>";
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
	if ($doi != "" && ($Stats_OH_Mails[$i]["Type"] == "P" || $Stats_OH_Mails[$i]["Reponse"] == "MS") && ((time() - $quand) > $limite) && strpos($doiCpt, "https://doi.org/") !== false) {
		$reqAPI = "https://api.archives-ouvertes.fr/search/univ-rennes1/?fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(ART%20OR%20COUV)%20AND%20submitType_s:*%20AND%20doiId_s:%22".$doi."%22&rows=10000&fl=halId_s,docid,contributorFullName_s,linkExtId_s";
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
echo "</table>";
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