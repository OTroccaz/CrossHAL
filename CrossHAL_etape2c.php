<?php
//Etape 2c > Test validité IdHAL
//var_dump($arrayHAL["response"]["docs"]);
$arrayHALAut = array();
$aNom = "~";//Liste test noms pour un affichage unique
for($cpt = 0; $cpt < $numFound; $cpt++) {
//for($cpt = 0; $cpt < 20; $cpt++) {
	progression($cpt+1, $numFound, $iPro);
	$iAHS = 0;
	$tabdocid = explode("~", $docidStr);
	while (isset($arrayHAL["response"]["docs"][$cpt]["authIdHasStructure_fs"][$iAHS])) {
		$tabIS = explode("_FacetSep_", $arrayHAL["response"]["docs"][$cpt]["authIdHasStructure_fs"][$iAHS]);
		if (strposa($tabIS[1], $tabdocid, 1) !== false) {
			$tabISP = explode("_JoinSep_", $tabIS[1]);
			//echo $tabISP[0]."<br>";
			$urlHALAut = "https://api.archives-ouvertes.fr/ref/author/?q=fullName_s:%22".$tabISP[0]."%22&fl=*";
			//$urlHALAut = "https://api.archives-ouvertes.fr/ref/author/?q=fullName_s:%22".$tabISP[0]."%22%20AND%20NOT%20valid_s:%22VALID%22&fl=*";
			$urlHALAut = str_replace(" ", "%20", $urlHALAut);
			askCurl($urlHALAut, $arrayHALAut);
			//Existe-t-il une forme VALID ?
			$ivalTest = 0;
			$testVal = "no";
			while (isset($arrayHALAut["response"]["docs"][$ivalTest]["docid"])) {
				if ($arrayHALAut["response"]["docs"][$ivalTest]["valid_s"] == "VALID"){
					$aNom .= $tabISP[0]."~";
					$testVal = "ok";
					break;
				}
			$ivalTest++;
			}
			//Si pas de forme VALID, existe-t-il une forme OLD ?
			$ivalTest = 0;
			while (isset($arrayHALAut["response"]["docs"][$ivalTest]["docid"])) {
				if ($arrayHALAut["response"]["docs"][$ivalTest]["valid_s"] == "OLD"){
					$aNom .= $tabISP[0]."~";
					$testVal = "ok";
					break;
				}
			$ivalTest++;
			}
			if ($testVal == "no") {//Pas de forme VALID
				$iVal = 0;//Indice pour parcourir le tableau des résultats des formes d'auteurs trouvés
				while (isset($arrayHALAut["response"]["docs"][$iVal]["docid"])) {
					$preHal = "-";
					$nomHal = "-";
					if (isset($arrayHALAut["response"]["docs"][$iVal]["firstName_s"])) {$preHAL = $arrayHALAut["response"]["docs"][$iVal]["firstName_s"];}
					if (isset($arrayHALAut["response"]["docs"][$iVal]["lastName_s"])) {$nomHAL = $arrayHALAut["response"]["docs"][$iVal]["lastName_s"];}
					
					if ($arrayHALAut["response"]["docs"][$iVal]["valid_s"] != "VALID" && stripos($aNom, $tabISP[0]) === false && isset($arrayHALAut["response"]["docs"][$iVal]["idHal_s"])) {
						$aNom .= $tabISP[0]."~";
						echo("<tr>");
						$cptID = $cpt + 1;
						echo("<td style='text-align: center;'>".$cptID."</td>");
						//echo("<td style='text-align: center;'>".$preHAL."</td>");
						//echo("<td style='text-align: center;'>".$nomHAL."</td>");
						echo("<td style='text-align: center;'>".$tabISP[0]."</td>");
						$idhals = "-";
						if (isset($arrayHALAut["response"]["docs"][$iVal]["idHal_s"])) {$idhals = $arrayHALAut["response"]["docs"][$iVal]["idHal_s"];}
						echo("<td style='text-align: center;'>".$idhals."</td>");
						$lienAureHAL = "-";
						if ($preHal != "-" && $nomHal != "-") {$lienAureHAL = "https://aurehal.archives-ouvertes.fr/author/browse/critere/".$nomHAL."+".$preHAL."/solR/1/page/1/nbResultPerPage/50/tri/current_bool/filter/all";}
						echo("<td style='text-align: center;'><a target='_blank' href='".$lienAureHAL."'><img src='./img/HAL.jpg'></a></td>");
						echo("<td style='text-align: center;'><a target='_blank' href='".$urlHALAut."'><img src='./img/HAL.jpg'></a></td>");
						echo("</tr>");
						//echo $arrayHALAut["response"]["docs"][$iVal]["fullName_s"]."<br>";
					}
					$iVal++;
				}
			}
		}
		$iAHS++;
	}
}
echo "</table><br>";
echo "<script>";
echo "  document.getElementById('cpt').style.display = \"none\";";
echo "</script>";
//Fin étape 2c
?>