<?php
//Etape 1c sur les auteurs correspondants
include "./CrossDOIAC.php";
if($ordinv == "oui") {$sort = "desc";}else{$sort = "asc";}
$urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:%22ART%22&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,docType_s,label_xml&sort=halId_s%20".$sort;
//echo $urlHAL.'<br>';
askCurl($urlHAL, $arrayHAL);
$numFound = $arrayHAL["response"]["numFound"];
if ($numFound == 0) {die ('<strong>Aucune référence</strong><br><br>');}
if ($iMax > $numFound) {$iMax = $numFound;}
echo '<strong>Total de '.$numFound.' référence(s)' ;
if ($numFound != 0) {
	 if ($numFound != 0) {echo " : affichage de ".$iMin." à ".$iMax."</strong><br><br>";}
}
echo "<div id='cpt'></div>";
//echo "<table class='table table-striped table-bordered table-hover;'><tr>";
//echo "<table style='border-collapse: collapse; width: 100%' border='1px' bordercolor='#999999' cellpadding='5px' cellspacing='5px'><tr>";
echo "<table class='table table-responsive table-bordered table-centered table-sm small'>";
echo "<thead class='thead-dark'>";
echo "<tr>";
echo "<th rowspan='2'><strong>ID</strong></th>";
echo "<th colspan='2'><strong>Liens</strong></th>";
if ($apa == "oui") {
	echo "<th rowspan='2'><strong>AP</strong></th>";
}
echo "<th rowspan='2'><strong>Année</strong></th>";
echo "<th rowspan='2'><strong>Auteur correspondant</strong></th>";
echo "<th rowspan='2'><strong>Email AC</strong></th>";
echo "<th rowspan='2'><strong>Action</strong></th>";
echo "</tr><tr>";
echo "<th><strong>DOI</strong></th>";
echo "<th><strong>HAL</strong></th>";
echo "</tr></thead><tbody>";
$iMinTab = $iMin - 1;
$cptAff = 0;//Compteur de ligne(s) affichée(s)
for($cpt = $iMinTab; $cpt < $iMax; $cpt++) {
	progression($cpt+1, $iMax, $iPro);
	$lignAff = "no";//Test affichage ou non de la ligne du tableau
	$textAff = "";//Texte de la ligne du tableau
	$doi = "";//DOI de la notice
	$halID = "";//HalId de la notice
	$lienHAL = "";//Lien renvoyant vers la notice HAL
	$lienDOI = "";//Lien renvoyant vers la notice via le DOI
	$bapa = false;//Booléen HAL (true/false) précisant si c'est une notice à paraître > inPress_bool
	if (isset($arrayHAL["response"]["docs"][$cpt]["inPress_bool"])) {$bapa = $arrayHAL["response"]["docs"][$cpt]["inPress_bool"];}
	$annee = "";//Année de publication
	$reprintAC = "";//Auteur correspondant
	$emailAC = "";//Email auteur correspondant
	$nomAC = "";//Nom auteur correspondant
	
	if (isset($arrayHAL["response"]["docs"][$cpt]["halId_s"])) {
		$lienHAL = "<a target='_blank' href='".$racine.$arrayHAL["response"]["docs"][$cpt]["halId_s"]."'><img alt='HAL' src='./img/HAL.jpg'></a>";
		$halID = $arrayHAL["response"]["docs"][$cpt]["halId_s"];
	}
	if (isset($arrayHAL["response"]["docs"][$cpt]["doiId_s"])) {
		$doi = $arrayHAL["response"]["docs"][$cpt]["doiId_s"];
		$lienDOI = "<a target='_blank' href='https://doi.org/".$doi."'><img alt='DOI' src='./img/doi.jpg'></a>";
	}
	
	$cptTab = $cpt + 1;
	$textAff .= "<tr>";
	$textAff .= "<td style='text-align: center;'>".$cptTab."</td>";
	$textAff .= "<td style='text-align: center;'>".$lienDOI."</td>";
	$textAff .= "<td style='text-align: center;'>".$lienHAL."</td>";
	if ($apa == "oui") {
		if ($bapa) {
			$textAff .= "<td style='text-align: center;'>AP</td>";
		}else{
			$textAff .= "<td style='text-align: center;'>&nbsp;</td>";
		}
	}
	
	//Recherche de la notice dans le CSV via le DOI
	$trouve = "non";
	foreach($DOIAC as $elt) {
		if ($elt["DOI"] == $doi) {
			$trouve = "oui";
			break;
		}
	}
	
	if ($trouve == "oui") {
		//Actions
		$lienMAJ = "";
		$actsMAJ = "";
		$lienMAJgrp = "";
		$actsMAJgrp = "";
		$actMaj = "ok";
		$raisons = "";
		$tei = $arrayHAL["response"]["docs"][$cpt]["label_xml"];
		//echo $tei;
		$tei = str_replace(array('<p>', '</p>'), '', $tei);
		$tei = str_replace('<p part="N">HAL API platform', '<p part="N">HAL API platform</p>', $tei);
		$teiRes = '<?xml version="1.0" encoding="UTF-8"?>'.$tei;
		//$teiRes = str_replace('<TEI xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', '<TEI xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.tei-c.org/ns/1.0 http://api.archives-ouvertes.fr/documents/aofr-sword.xsd" xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', $teiRes);
		//$Fnm = "./XML/".normalize(wd_remove_accents($titre)).".xml";
		$Fnm = "./XML/".$arrayHAL["response"]["docs"][$cpt]["halId_s"].".xml";
		$xml = new DOMDocument( "1.0", "UTF-8" );
		$xml->formatOutput = true;
		$xml->preserveWhiteSpace = false;
		$colact = "ok";
		if (@$xml->loadXML($teiRes) !== false) {//tester validité teiRes
			$xml->loadXML($teiRes);
		}else{
			$colact = "pasok";
		}
		
		corrXML($xml);
		
		//Recherche du nom de l'auteur correspondant
		$tabAC = explode(",", $elt["ReprintAC"]);
		$nomAC = $tabAC[0];
		
		//Est-ce que cet auteur a bien été désigné comme auteur correspondant dans la notice HAL ?
		$auts = $xml->getElementsByTagName("author");
		foreach($auts as $aut) {
			$quoi = $aut->getAttribute("role");
			foreach($aut->childNodes as $item) {
				if ($item->nodeName == "persName") {
					foreach($item->childNodes as $qui) {
						if ($qui->nodeName == "surname") {
							if (stripos(wd_remove_accents($qui->nodeValue), wd_remove_accents($nomAC)) !== false && $quoi != "crp") {
								//Le TEI est à modifier
								$aut->setAttribute("role", "crp");
								$xml->save($Fnm);
								
								$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
								include "./CrossHAL_actions.php";
								$testMaj = "ok";
								$lignAff = "ok";
								foreach($ACTIONS_LISTE as $tab) {
									if (in_array($halID, $tab) && in_array("MAJ_DAC",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "auteur correspondant, ";}
								}
								if ($testMaj == "ok") {$actsMAJ .= "MAJ_DAC~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"];}
							}
						}
					}
				}
			}
		}

		//Année
		$textAff .= "<td style='text-align: center;'>".$elt["Annee"]."</td>";
		
		//Auteur correspondant
		$textAff .= "<td>".$elt["ReprintAC"]."</td>";
		
		//Email auteur correspondant
		$textAff .= "<td>".$elt["EmailAC"]."</td>";
				
		if ($colact == "ok") {
			if ($lienMAJ != "") {
				$textAff .= "<td style='text-align: center;'>";
				if ($actMaj == "ok") {
					$textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienMAJ."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"".$actsMAJ."\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span></center>";
				}else{
					$textAff .= "<center><img title=\"La(les) modification(s) n'est(ne sont) pas envisageables car une ou plusieurs métadonnées a(ont) été modifiée(s) depuis moins d'une semaine : ".$raisons."\" style='width: 50px;' src='./img/addOK_grand.png'></center>";
				}
				$textAff .= "</td></tr>";
				$lignAff = "ok";
			}else{
				$textAff .= "<td style='text-align: center;'><img alt='Done' title='Ok' src='./img/done.png'></td></tr>";
			}
		}else{
			$textAff .= "<td style='text-align: center;'><img alt='Erreur XML' title='Erreur dans le XML' src='./img/xmlpasok.png'></td></tr>";
			$lignAff = "ok";
		}
	}
		
	if ($lignAff == "ok") {//Il y a au moins une correction à apporter > la ligne est à afficher	
		echo $textAff;
		$cptAff++;
	}
}
echo "</tbody></table><br>";
echo "<script>";
echo "  document.getElementById('cpt').style.display = \"none\";";
echo "</script>";

if ($iMax != $numFound) {
	echo "<form name='troli' id='etape1' action='CrossHAL.php' method='post'>";
	$iMinInit = $iMin;
	$iMinRet = $iMin - $increment;
	$iMin = $iMax + 1;
	$iMaxRet = $iMax - $increment;
	$iMax += $increment;
	if ($iMax > $numFound) {$iMax = $numFound;}
	echo "<input type='hidden' value='".$iMin."' name='iMin'>";
	echo "<input type='hidden' value='".$iMax."' name='iMax'>";
	echo "<input type='hidden' value='".$iMinRet."' name='iMinRet'>";
	echo "<input type='hidden' value='".$iMaxRet."' name='iMaxRet'>";
	echo "<input type='hidden' value='".$increment."' name='increment'>";
	echo "<input type='hidden' value='".$team."' name='team'>";
	echo "<input type='hidden' value='".$idhal."' name='idhal'>";
	echo "<input type='hidden' value='".$anneedeb."' name='anneedeb'>";
	echo "<input type='hidden' value='".$anneefin."' name='anneefin'>";
	echo "<input type='hidden' value='".$apa."' name='apa'>";
	echo "<input type='hidden' value='".$ordinv."' name='ordinv'>";
	echo "<input type='hidden' value='".$chkall."' name='chkall'>";
	echo "<input type='hidden' value='".$csvDOIAC."' name='csvDOIAC'>";
	echo "<input type='hidden' value='Valider' name='valider'>";
	if ($iMinInit != 1) {
		echo "<input type='submit' class='btn btn-md btn-primary btn-sm' value='Retour' name='retour'>&nbsp;&nbsp;&nbsp;";
	}
	echo "<input type='submit' class='btn btn-md btn-primary btn-sm' value='Suite' name='suite'>";
	echo "</form><br>";
}else{
	echo "<form name='troli' id='etape1' action='CrossHAL.php' method='post'>";
	$iMinInit = $iMin;
	$iMinRet = $iMin - $increment;
	$iMaxRet = $iMinRet + $increment - 1;
	echo "<input type='hidden' value='".$iMinRet."' name='iMinRet'>";
	echo "<input type='hidden' value='".$iMaxRet."' name='iMaxRet'>";
	echo "<input type='hidden' value='".$increment."' name='increment'>";
	echo "<input type='hidden' value='".$team."' name='team'>";
	echo "<input type='hidden' value='".$idhal."' name='idhal'>";
	echo "<input type='hidden' value='".$anneedeb."' name='anneedeb'>";
	echo "<input type='hidden' value='".$anneefin."' name='anneefin'>";
	echo "<input type='hidden' value='".$apa."' name='apa'>";
	echo "<input type='hidden' value='".$ordinv."' name='ordinv'>";
	echo "<input type='hidden' value='".$chkall."' name='chkall'>";
	echo "<input type='hidden' value='".$csvDOIAC."' name='csvDOIAC'>";
	echo "<input type='hidden' value='Valider' name='valider'>";
	if ($iMaxRet != 0) {
		echo "<input type='submit' class='btn btn-md btn-primary btn-sm' value='Retour' name='retour'>";
	}
}
/*
if ($cptAff == 0 && $cpt != $iMax) {//Auto-soumission du formulaire si ce n'est pas la dernière notice à avoir été traitée
	echo "<script>";
	echo "  document.getElementById(\"etape1\").submit(); ";
	echo "</script>";
}
*/
//Fin étape 1b
?>