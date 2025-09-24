<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Etape 1a - Stage 1a
 */
 
//Etape 1a sur les articles

//$urlHAL = "https://api.archives-ouvertes.fr/search/?q=collCode_s:%22".$team."%22".$txtApa."%20AND%20submitType_s:%22file%22&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:%22ART%22&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,anrProjectReference_s,journalTitle_s,journalIssn_s,journalValid_s,docid,journalIssn_s,journalEissn_s,abstract_s,language_s,inPress_bool,label_xml,submittedDate_s&sort=halId_s%20asc";
//$urlHAL = "https://api.archives-ouvertes.fr/search/?q=halId_s:%22hal-01795811%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:%22ART%22&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,anrProjectReference_s,journalTitle_s,journalIssn_s,journalValid_s,docid,journalIssn_s,journalEissn_s,abstract_s,language_s,inPress_bool,label_xml,submittedDate_s,submitType_s,docType_s&sort=halId_s%20asc";
	if($ordinv == "oui") {$sort = "desc";}else{$sort = "asc";}
	$urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(%22ART%22%20OR%20%22COMM%22%20OR%20%22COUV%22)&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,anrProjectReference_s,journalTitle_s,journalIssn_s,journalValid_s,docid,journalIssn_s,journalEissn_s,abstract_s,language_s,inPress_bool,label_xml,submittedDate_s,submitType_s,docType_s,popularLevel_s,peerReviewing_s&sort=contributorFullName_s%20".$sort;
//echo $urlHAL.'<br><br>';
//$contents = file_get_contents($urlHAL);
//$results = json_decode($contents);
//$resHAL = json_decode($contents, true);
askCurl($urlHAL, $arrayHAL);
//$numFound = $results->response->numFound;
$numFound = $arrayHAL["response"]["numFound"];
if ($numFound == 0) {die ('<strong>Aucune référence</strong><br><br>');}
if ($iMax > $numFound) {$iMax = $numFound;}
echo '<strong>Total de '.$numFound.' référence(s)' ;
if ($numFound != 0) {
	 if ($numFound != 0) {echo " : affichage de ".$iMin." à ".$iMax."</strong>&nbsp;<em>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</em><br><br>";}
}
//var_dump($resHAL);
//print_r($results);
//var_dump($resHAL["response"]["docs"][0]);
echo "<div id='cpt'></div>";
//echo "<table class='table table-striped table-bordered table-hover sm-12'><tr>";
//echo "<table style='border-collapse: collapse; width: 100%' border='1px' bordercolor='#999999' cellpadding='5px' cellspacing='5px'><tr>";
echo "<table class='table table-responsive table-bordered table-centered table-sm text-center small'>";
echo "<thead class='thead-dark'>";
echo "<tr>";
echo "<th rowspan='2'><strong>ID</strong></th>";
echo "<th colspan='3'><strong>Liens</strong></th>";
if ($apa == "oui") {
	echo "<th rowspan='2'><strong>AP</strong></th>";
}
echo "<th rowspan='2'><strong>1er auteur</strong></th>";
if ($revue == "oui") {//Revue CR
	echo "<th rowspan='2'><strong>Revue</strong></th>" ;
}
if ($revOA == "oui") {//Revue OA
	echo "<th rowspan='2'><strong>Revue</strong></th>" ;
}
if ($vnp == "oui") {//Vol/num/pag CR
	echo "<th colspan='2'><strong>Vol(n)pp</strong></th>";
}
if ($vnpOA == "oui") {//Vol/num/pag OA
	echo "<th colspan='2'><strong>Vol(n)pp</strong></th>";
}
if ($pmid == "oui") {
	echo "<th colspan='2'><strong>PMID</strong></th>" ;
}
if ($pmcid == "oui") {
	echo "<th colspan='2'><strong>PMCID</strong></th>";
}
if ($anneepub == "oui") {
	echo "<th colspan='2'><strong>Année de publication</strong></th>";
}
if ($mel == "oui") {//Date de mise en ligne CR
	echo "<th colspan='2'><strong>Date de mise en ligne</strong></th>";
}
/*
if ($melOA == "oui") {//Date de mise en ligne OA
	echo "<th colspan='2'><strong>Date de mise en ligne</strong></th>";
}
*/
if ($mocPubmed == "oui") {//Seulement HAL et PM
	echo "<th colspan='2'><strong>Mots-clés</strong></th>";
}else{
	//if ($mocCrossRef == "oui") {
		if ($mocISTEX == "oui") {
			echo "<th colspan='2'><strong>Mots-clés</strong></th>";
		}else{
			echo "<th><strong>Mots-clés</strong></th>" ;
		}
	//}else{
	//  if ($mocISTEX == "oui") {
	//    echo "<th colspan='2'><strong>Mots-clés</strong></th>";
	//  }else{
	//    echo "<th><strong>Mots-clés</strong></th>";
	//  }
	//}
}
if ($absPubmed == "oui") {
	if ($absISTEX == "oui") {
		echo "<th colspan='2'><strong>Résumé</strong></th>";
	}else{
		echo "<th><strong>Résumé</strong></th>";
	}
}else{
	if ($absISTEX == "oui") {
		echo "<th><strong>Résumé</strong></th>";
	}
}
if ($lanPubmed == "oui") {
	echo "<th><strong>Langue</strong></th>";
}
if ($lanISTEX == "oui") {
	echo "<th><strong>Langue</strong></th>";
}
if ($lanCrossRef == "oui") {
	echo "<th><strong>Langue</strong></th>";
}
if ($lanOA == "oui") {
	echo "<th><strong>Langue</strong></th>";
}
if ($financement == "oui") {//Financement CR
	echo "<th colspan='2'><strong>Financement</strong></th>";
}
if ($finOA == "oui") {//Financement OA
	echo "<th colspan='2'><strong>Financement</strong></th>";
}
if ($anr == "oui") {//ANR CR
	echo "<th colspan='2'><strong>ANR</strong></th>";
}
if ($anrOA == "oui") {//ANR OA
	echo "<th colspan='2'><strong>ANR</strong></th>";
}
echo "<th rowspan='2'><strong>Action</strong></th>";
echo "</tr><tr>";
//echo "<th bordercolor='#808080'></th>";
echo "<th><strong>DOI</strong></th>";
echo "<th><strong>HAL</strong></th>";
//OpenAlex
if ($doiOA == "oui" || $revOA == "oui" || $vnpOA == "oui" || $lanOA == "oui" || $finOA == "oui" || $anrOA == "oui" || $melOA == "oui") {
	echo "<th><strong>OA</strong></th>";
}else{
	echo "<th><strong>CR</strong></th>";
}
//echo "<th><strong></strong></th>";
//echo "<th><strong>HAL</strong></th>";
//echo "<th><strong>CR</strong></th>";
if ($vnp == "oui") {//Vol/num/pag CR
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>CR</strong></th>";
}
if ($vnpOA == "oui") {//Vol/num/pag OA
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>OA</strong></th>";
}
if ($pmid == "oui") {
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>Pubmed</strong></th>";
}
//if ($pmcid == "oui") {
	//echo "<th><strong>HAL</strong></th>";
	//echo "<th><strong>Pubmed</strong></th>";
//}
if ($anneepub == "oui") {
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>CR</strong></th>";
}
if ($mel == "oui") {//Date de mise en ligne CR
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>CR</strong></th>";
}
/*
if ($melOA == "oui") {//Date de mise en ligne OA
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>OA</strong></th>";
}
*/
if ($mocPubmed == "oui") {//Seulement HAL et PM
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>Pubmed</strong></th>";
}else{
	//if ($mocCrossRef == "oui") {
		if ($mocISTEX == "oui") {
			echo "<th><strong>HAL</strong></th>";
			echo "<th><strong>ISTEX</strong></th>";
		}else{
			echo "<th><strong>HAL</strong></th>";
		}
	//}else{
	//  if ($mocISTEX == "oui") {
	//    echo "<th><strong>HAL</strong></th>";
	//    echo "<th><strong>ISTEX</strong></th>";
	//  }else{
	//    echo "<th><strong>HAL</strong></th>";
	//  }
	//}
}
if ($absPubmed == "oui") {
	if ($absISTEX == "oui") {
		echo "<th><strong>Pubmed</strong></th>";
		echo "<th><strong>ISTEX</strong></th>";
	}else{
		echo "<th><strong>Pubmed</strong></th>";
	}
}else{
	if ($absISTEX == "oui") {
		echo "<th><strong>ISTEX</strong></th>";
	}
}
if ($lanPubmed == "oui") {
	echo "<th><strong>Pubmed</strong></th>";
}
if ($lanISTEX == "oui") {
	echo "<th><strong>ISTEX</strong></th>";
}
if ($lanCrossRef == "oui") {
	echo "<th><strong>CR</strong></th>";
}
if ($lanOA == "oui") {
	echo "<th><strong>OA</strong></th>";
}
if ($financement == "oui") {//Financement CR
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>CR</strong></th>";
}
if ($finOA == "oui") {//Financement OA
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>OA</strong></th>";
}
if ($anr == "oui") {//ANR CR
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>CR</strong></th>";
}
if ($anrOA == "oui") {//ANR OA
	echo "<th><strong>HAL</strong></th>";
	echo "<th><strong>OA</strong></th>";
}
//echo "<th><strong></strong></th>";
echo "</tr></thead><tbody>";
$lienMAJgrpTot = "";
$actsMAJgrpTot = "";
//foreach($results->response->docs as $entry){
$iMinTab = $iMin - 1;
$cptAff = 0;//Compteur de ligne(s) affichée(s)
for($cpt = $iMinTab; $cpt < $iMax; $cpt++) {
	progression($cpt+1, $iMax, $iPro);
	//if ($arrayHAL["response"]["docs"][$cpt]["halId_s"] == "hal-01509702") {
		$lignAff = "no";//Test affichage ou non de la ligne du tableau
		$textAff = "";//Texte de la ligne du tableau
		$titre = $arrayHAL["response"]["docs"][$cpt]["title_s"][0];//Titre de la notice
		$doi = "";//DOI de la notice
		$halID = "";//HalId de la notice
		$doiCR = "";//DOI CR
		$lienHAL = "";//Lien renvoyant vers la notice HAL
		$lienDOI = "";//Lien renvoyant vers la notice via le DOI
		$bapa = false;//Booléen HAL (true/false) précisant si c'est une notice à paraître > inPress_bool
		if (isset($arrayHAL["response"]["docs"][$cpt]["inPress_bool"])) {$bapa = $arrayHAL["response"]["docs"][$cpt]["inPress_bool"];}
		$prenomHAL = "";//Prénom du 1er auteur HAL
		$nomHAL = "";//Nom du 1er auteur HAL
		$doiOAR = "";//DOI OpenAlex
		$revOAR = "";//Revue OpenAlex
		$volOAR = "";//Vol OpenAlex
		$numOAR = "";//Num OpenAlex
		$pagOAR = "";//Pag OpenAlex
		$lanOAR = "";//Langue OpenAlex
		$finOAR = "";//Financement OpenAlex
		$anrOAR = "";//ANR OpenAlex
		$melOAR = "";//Date de mise en ligne OpenAlex
		unset($arrayCR);//Tableau de métadonnées CR
		$prenomCR = "";//Prénom du 1er auteur CR
		$nomCR = "";//Nom du 1er auteur CR
		$corr = "";//Contenu de la cellule à afficher pour la correspondance du premier auteur > ok/pas ok
		$pubCR = "";//Date de publication CR
		$lienCR = "";//Lien renvoyant vers la notice CR
		$volCR = "";//Numéro de volume CR
		$numCR = "";//Numéro de fascicule CR
		$pagCR = "";//Pagination CR
		$lanCR = "";//Langue CR
		$finCR = "";//Financement CR
		$annCR = "";//Année de publication CR
		$txtAnnCR = "";//Année de publication CR sous format texte
		$melCR = "";//Date de mise en ligne CR
		$mocCR = "";//Mots-clés CR
		$mocPM = "";//Mots-clés PM
		$absPM = "";//Résumé PM
		$lanPM = "";//Langue PM
		$pmiPM = "";//PMID PM
		$dpbPM = "";//Date publication PM
		$doiHAL = "";//DOI HAL
		$volHAL = "";//Numéro de volume HAL
		$numHAL = "";//Numéro de fascicule HAL
		$pagHAL = "";//Pagination HAL
		$finHAL = "";//Financement HAL
		$annHAL = "";//Année de publication HAL
		$melHAL = "";//Date de mise en ligne HAL
		$mocHAL = "";//Mots-clés HAL
		$absHAL = "";//Résumé HAL
		$lanHAL = "";//Langue HAL
		$pmiHAL = "";//PMID HAL
		$revHAL = "";//Titre de la revue HAL
		$revCRIH = "";//Titre de la revue retrouvé via l'ISSN ou l'EISSN et CR ou HAL > fonction rechRevueISSN de CR_DOI_ISSN_HAL_Rev.php
		$docidHAL = "";//Identifiant document HAL
		$docidCRIH = "";//Identifiant du document retrouvé via l'ISSN ou l'EISSN et CR ou HAL > fonction rechRevueISSN de CR_DOI_ISSN_HAL_Rev.php
		$issnCRIH = "";//ISSN de la revue retrouvé via le DOI et CR > fonction rechRevueISSN de CR_DOI_ISSN_HAL_Rev.php
		$issnHAL = "";//ISSN HAL
		$eissnCRIH = "";//EISSN de la revue retrouvé via le DOI et CR > fonction rechRevueISSN de CR_DOI_ISSN_HAL_Rev.php
		$eissnHAL = "";//EISSN HAL
		//$results = ""; //Ancien tableau des résultats obtenus avec utilisation initiale de file_get_contents > utilisation par la suite de la fonction askCurl
		$Fnm = "";//Chemin + nom du fichier qui va servir à créer le XML pour les modifications
		$pcMocPM = 0;//Indice de similarité des mots-clés entre HAL et PM
		$pcMocIS = 0;//Indice de similarité des mots-clés entre HAL et ISTEX
		$absIS = "";//Résumé ISTEX
		$lanIS = "";//Langue ISTEX
		$dpbIS = "";//Date publication ISTEX
		
		//OpenAlex
		if ($doiOA == "oui" || $revOA == "oui" || $vnpOA == "oui" || $lanOA == "oui" || $finOA == "oui" || $anrOA == "oui" || $melOA == "oui") {
			rechMetadoOA($doi, $titre, $doiOAR, $revOAR, $volOAR, $numOAR, $pagOAR, $lanOAR, $finOAR, $anrOAR, $melOAR);//OAR = OpenAlexResults
		}
		
		$textAff .= "<tr>";
		//if (isset($entry->halId_s)) {
		if (isset($arrayHAL["response"]["docs"][$cpt]["halId_s"])) {
			$lienHAL = "<a target='_blank' href='".$racine.$arrayHAL["response"]["docs"][$cpt]["halId_s"]."'><img alt='HAL' src='./img/HAL.jpg'></a>";
			$halID = $arrayHAL["response"]["docs"][$cpt]["halId_s"];
		}
		if (isset($arrayHAL["response"]["docs"][$cpt]["doiId_s"])) {
			$doi = $arrayHAL["response"]["docs"][$cpt]["doiId_s"];
			$lienDOI = "<a target='_blank' href='https://doi.org/".$doi."'><img alt='DOI' src='./img/doi.jpg'></a>";
			
			//Test DOI Crossref
			$prenomHAL = prenomCompInit($arrayHAL["response"]["docs"][$cpt]["authFirstName_s"][0]);
			$nomHAL = nomCompEntier($arrayHAL["response"]["docs"][$cpt]["authLastName_s"][0]);
			$urlCR = "https://api.crossref.org/v1/works/http://dx.doi.org/".$doi;
			if (@file_get_contents($urlCR)) {
			//if (@file_get_contents(askCurl($urlCR, $arrayCR))) {
				//$contents = file_get_contents($urlCR);
				//$contents = utf8_encode($contents); 
				//$results = json_decode($contents, TRUE);
				//var_dump($results);
				askCurl($urlCR, $arrayCR);
				
				if (isset($arrayCR["message"]["author"][0]["given"])) {
					$prenomCR = prenomCompInit($arrayCR["message"]["author"][0]["given"]);
				}
				if (isset($arrayCR["message"]["author"][0]["family"])) {
					$nomCR = nomCompEntier($arrayCR["message"]["author"][0]["family"]);
				}
				if (isset($arrayCR["message"]["published-print"]["date-parts"][0][0])) {
					$pubCR = $arrayCR["message"]["published-print"]["date-parts"][0][0];
				}
				$lienCR = "";
			}else{//Problème de DOI
				$rechDOI = "";//Recherche du DOI à partir du titre via CR avec la fonction rechTitrDOI de CR_DOI_Levenshtein.php
				rechTitreDOI($titre, 5, $closest, $shortest, $rechDOI);
				if ($rechDOI != "") {
					$doi = $rechDOI;
					$lienDOI = "<a target='_blank' href='https://doi.org/".$rechDOI."'><img alt='DOI' src='./img/doi.jpg'></a>";
					$lienCR = "<a target='_blank' href='http://search.crossref.org/?q=".$rechDOI."'><img alt='CrossRef' src='./img/CR.jpg'></a>";
				}else{
					$lienCR = "DOI inconnu de Crossref";
					$doiCR = "inconnu";
				}
			}
			
			//correspondance du premier auteur
			$why = ""; 
			if ($nomHAL == $nomCR) {
				//echo($doi .' => Ok<br>');
				$corr = "<img alt='OK' src='./img/ok.jpg'>";
			}else{
				$why = $nomHAL." <> ".$nomCR;
				$why = str_replace("'", " ", $why);
				$corr = "<img alt='".$why."' title='".$why."' src='./img/pasok.jpg'>";
			}
			
			if ($lienCR == "") {$lienCR = "<a target='_blank' href='http://search.crossref.org/?q=".$doi."'><img alt='CrossRef' src='./img/CR.jpg'></a>";}
			
		}else{//Pas de DOI trouvé dans HAL > on va essayer de le retrouver grâce au titre et l'API CR si la recherche a bien été demandée initialement
			$doiHAL = "inconnu";
			if (isset($doiCrossRef) && $doiCrossRef == "oui") {
				$titreTest = $arrayHAL["response"]["docs"][$cpt]["title_s"][0];
				$urlCR = "https://api.crossref.org/works?query.title=".urlencode($titreTest);
				//echo urlencode($titreTest);
				if (@file_get_contents($urlCR)) {
					askCurl($urlCR, $arrayCR);
					//if ($arrayCR["message"]["items"][0]["publisher"] != "PERSEE Program") {
						$titreCR = $arrayCR["message"]["items"][0]["title"][0];           
						if ($titreTest != "") {$titreTestRed = strtolower(substr($titreTest, 0, 250));}else{$titreTestRed= "";}
						if ($titreCR != "") {$titreCRRed = strtolower(substr($titreCR, 0, 250));}else{$titreCRRed= "";}
						$pcTitre = 100;//Indice de similarité des titres HAL et CR
						if ($titreTestRed != $titreCRRed) {
							$pcTitre = (250-levenshtein_utf8($titreTestRed, $titreCRRed))*100/250;
						}
						if ($pcTitre < 98) {
							$why = 'Indice de similarité des titres HAL et CR : '.$pcTitre.' %';
							$lienDOI = "<img alt='".$why."' title='".$why."' src='./img/doiCRpasok.png'>";
						}else{
							$doiCR = $arrayCR["message"]["items"][0]["DOI"];
							$doi = $doiCR;
							$lienDOI = "<a target='_blank' href='https://doi.org/".$doiCR."'><img alt='CrossRef' src='./img/doiCR.png'></a>";
							$lienCR = "<a target='_blank' href='http://search.crossref.org/?q=".$doiCR."'><img alt='CrossRef' src='./img/CR.jpg'></a>";
						}
					//}
				}
			}else{
				//echo $doiOA.' > '.$doi.' > '.$doiOAR;
				//die();
				if (isset($doiOA) && $doiOA == "oui") {//DOI via OA
					if ($doiOAR != "") {
						$lienDOI = "<a target='_blank' href='https://doi.org/".$doiOAR."'><img alt='DOI' src='./img/doiCR.png'></a>";
						//$lienOA = "<a target='_blank' href='http://search.crossref.org/?q=".$rechDOI."'><img alt='CrossRef' src='./img/CR.jpg'></a>";
					}else{
						$lienOA = "DOI inconnu de OpenAlex";
						//$doiOA = "inconnu";
					}
				}
			}
		}
		$cptTab = $cpt + 1;
		$textAff .= "<td>".$cptTab."</td>";
		$textAff .= "<td>".$lienDOI."</td>";
		$textAff .= "<td>".$lienHAL."</td>";
		//OpenAlex
		if ($doiOA == "oui" || $revOA == "oui" || $vnpOA == "oui" || $lanOA == "oui" || $finOA == "oui" || $anrOA == "oui" || $melOA == "oui") {
			if ($doi != '') {
				$textAff .= "<td><a target='_blank' href='https://api.openalex.org/works?filter=doi:".$doi."&mailto=laurent.jonchere@univ-rennes.fr'><img alt='OpenAlex' src='./img/OA.jpg'></a></td>";
			}else{
				$titreOA = str_replace(array(',', ';', '.'), '', $titre);
				$titreOA = str_replace(' ', '%20', $titreOA);
				$textAff .= "<td><a target='_blank' href='https://api.openalex.org/works?filter=title.search:%22".$titreOA."%22&mailto=laurent.jonchere@univ-rennes.fr'><img alt='OpenAlex' src='./img/OA.jpg'></a></td>";
			}
		}else{
			$textAff .= "<td>".$lienCR."</td>";
		}
		if ($apa == "oui") {
			if ($bapa) {
				$textAff .= "<td>AP</td>";
			}else{
				$textAff .= "<td>&nbsp;</td>";
			}
		}
		$textAff .= "<td>".$corr."</td>";
		
		//Revue CR
		if ($revue == "oui") {
			if (isset($doi) && $doi != "" && $lienCR != "DOI inconnu de Crossref") {
				if (isset($arrayHAL["response"]["docs"][$cpt]["journalValid_s"]) && $arrayHAL["response"]["docs"][$cpt]["journalValid_s"] != "VALID" ) {
					if (isset($arrayHAL["response"]["docs"][$cpt]["journalTitle_s"]) && $arrayHAL["response"]["docs"][$cpt]["journalTitle_s"] != "" ) {
						$revHAL = $arrayHAL["response"]["docs"][$cpt]["journalTitle_s"];
					}
					if (isset($arrayHAL["response"]["docs"][$cpt]["docid"]) && $arrayHAL["response"]["docs"][$cpt]["docid"] != "" ) {
						$docidHAL = $arrayHAL["response"]["docs"][$cpt]["docid"];
					}
					if (isset($arrayHAL["response"]["docs"][$cpt]["journalIssn_s"]) && $arrayHAL["response"]["docs"][$cpt]["journalIssn_s"] != "" ) {
						$issnHAL = $arrayHAL["response"]["docs"][$cpt]["journalIssn_s"];
					}
					if (isset($arrayHAL["response"]["docs"][$cpt]["journalEissn_s"]) && $arrayHAL["response"]["docs"][$cpt]["journalEissn_s"] != "" ) {
						$eissnHAL = $arrayHAL["response"]["docs"][$cpt]["journalEissn_s"];
					}
					rechRevueISSN($doi, $issnCRIH, $eissnCRIH, $docidCRIH, $revCRIH);
					$why = $arrayHAL["response"]["docs"][$cpt]["journalValid_s"]." <> ".$docidCRIH;
					$why = str_replace("'", " ", $why);
					if ($docidCRIH != "") {
						$textAff .= "<td><img alt='".$why."' title='".$why."' src='./img/pasok.jpg'></td>";
					}else{
						$textAff .= "<td>&nbsp;</td>";
					}
				}else{
					$textAff .= "<td><img alt='OK' src='./img/ok.jpg'></td>";
				}
			}else{
				$textAff .= "<td></td>";
			}
		}
		
		if ($revOA == "oui") {//Revue OA
			if (isset($doi) && $doi != "" && $revOAR != "") {
				if (isset($arrayHAL["response"]["docs"][$cpt]["journalValid_s"]) && $arrayHAL["response"]["docs"][$cpt]["journalValid_s"] != "VALID" ) {
					if (isset($arrayHAL["response"]["docs"][$cpt]["journalTitle_s"]) && $arrayHAL["response"]["docs"][$cpt]["journalTitle_s"] != "" ) {
						$revHAL = $arrayHAL["response"]["docs"][$cpt]["journalTitle_s"];
					}
					$why = $revHAL." <> ".$revOAR;
					$why = str_replace("'", " ", $why);
					if ($revHAL != $revOAR) {
						$textAff .= "<td><img alt='".$why."' title='".$why."' src='./img/pasok.jpg'></td>";
					}else{
						$textAff .= "<td>&nbsp;</td>";
					}
				}else{
					$textAff .= "<td><img alt='OK' src='./img/ok.jpg'></td>";
				}
			}else{
				$textAff .= "<td></td>";
			}
		}
		
		//Vol/num/pag
		if ($vnp == "oui") {
			$volHAL = "";
			$volCR = "";
			$numHAL = "";
			$numCR = "";
			$pagHAL = "";
			$pagCR = "";
			if (isset($arrayHAL["response"]["docs"][$cpt]["volume_s"])) {
				$volHAL = $arrayHAL["response"]["docs"][$cpt]["volume_s"];
			}
			if (isset($arrayHAL["response"]["docs"][$cpt]["issue_s"][0])) {
				$numHAL = $arrayHAL["response"]["docs"][$cpt]["issue_s"][0];
			}
			if (isset($arrayHAL["response"]["docs"][$cpt]["page_s"])) {
				$pagHAL = $arrayHAL["response"]["docs"][$cpt]["page_s"];
			}
			$textAff .= "<td>".$volHAL."(".$numHAL.")".$pagHAL."</td>";
			if (isset($arrayCR["message"]["volume"]) && $doiCR == "" && isset($doi) && $doi != "") {
				$volCR = $arrayCR["message"]["volume"];
			}
			if (isset($arrayCR["message"]["issue"]) && $doiCR == "" && isset($doi) && $doi != "") {
				$numCR = $arrayCR["message"]["issue"];
			}
			if (isset($arrayCR["message"]["page"]) && $doiCR == "" && isset($doi) && $doi != "") {
				$pagCR = $arrayCR["message"]["page"];
			}
			$deb = "";
			$fin = "";
			if ($volCR."(".$numCR.")".$pagCR != "()") {
				if ($volHAL == "" && $volCR != "") {
					$deb = "<strong>";$fin = "</strong>";        }
				if ($numHAL == "" && $numCR != "") {
					$deb = "<strong>";$fin = "</strong>";
				}
				//On complète la pagination HAL par CR sauf si les champs vol et num sont déjà complétés dans HAL
				if ($pagCR != "" && $volHAL == "" && $numHAL == "") {
					$deb = "<strong>";$fin = "</strong>";
				}
				$textAff .= "<td style='text-align: center; background-color: #eeeeee;'>".$deb.$volCR."(".$numCR.")".$pagCR.$fin."</td>";
			}else{
				$textAff .= "<td style='text-align: center; background-color: #eeeeee;'>&nbsp;</td>";
			}
		}
		if ($vnpOA == "oui") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["volume_s"])) {
				$volHAL = $arrayHAL["response"]["docs"][$cpt]["volume_s"];
			}
			if (isset($arrayHAL["response"]["docs"][$cpt]["issue_s"][0])) {
				$numHAL = $arrayHAL["response"]["docs"][$cpt]["issue_s"][0];
			}
			if (isset($arrayHAL["response"]["docs"][$cpt]["page_s"])) {
				$pagHAL = $arrayHAL["response"]["docs"][$cpt]["page_s"];
			}
			$textAff .= "<td>".$volHAL."(".$numHAL.")".$pagHAL."</td>";
			
			$deb = "";
			$fin = "";
			if ($volOAR."(".$numOAR.")".$pagOAR != "()") {
				if ($volHAL == "" && $volOAR != "") {
					$deb = "<strong>";$fin = "</strong>";        }
				if ($numHAL == "" && $numOAR != "") {
					$deb = "<strong>";$fin = "</strong>";
				}
				//On complète la pagination HAL par OA sauf si les champs vol et num sont déjà complétés dans HAL
				if ($pagOAR != "" && $volHAL == "" && $numHAL == "") {
					$deb = "<strong>";$fin = "</strong>";
				}
				$textAff .= "<td style='text-align: center; background-color: #eeeeee;'>".$deb.$volOAR."(".$numOAR.")".$pagOAR.$fin."</td>";
			}else{
				$textAff .= "<td style='text-align: center; background-color: #eeeeee;'>&nbsp;</td>";
			}
		}

		//PMID
		if ($pmid == "oui") {
			if (isset($doi) && $doi != "") {
				if (isset($arrayHAL["response"]["docs"][$cpt]["pubmedId_s"])) {
					$pmiHAL = $arrayHAL["response"]["docs"][$cpt]["pubmedId_s"];
				}
				$urlNCBI = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&retmode=json&term=".$doi."[lid]";
				//$cntNCBI = file_get_contents($urlNCBI);
				//$cntNCBI = utf8_encode($cntNCBI);
				//$resNCBI = json_decode($cntNCBI, true);
				//var_dump($resNCBI);
				askCurl($urlNCBI, $arrayNCBI);
				$numNCBI = $arrayNCBI["esearchresult"]["count"];

				if (isset($arrayNCBI["esearchresult"]["idlist"][0])) {
					$pmiPM = $arrayNCBI["esearchresult"]["idlist"][0];
					rechMetadoPMID($pmiPM, $absPM, $mcMESH, $lanPM, $mocPM, $dpbPM);
				}
				$deb = "";
				$fin = "";
				if ($pmiHAL != $pmiPM) {$deb = "<strong>";$fin = "</strong>";}
				$textAff .= "<td>".$pmiHAL."</td>";
				$textAff .= "<td style='text-align : center; background-color: #eeeeee;'>".$deb.$pmiPM.$fin."</td>";
			}else{
				$textAff .= "<td></td>";
				$textAff .= "<td></td>";
			}
		}

		//Année de publication
		if ($anneepub == "oui") {
			$txtAnnCR = "";
			if (isset($arrayHAL["response"]["docs"][$cpt]["producedDate_s"])) {
				$annHAL = $arrayHAL["response"]["docs"][$cpt]["producedDate_s"];
			}
			if (isset($arrayCR["message"]["published-print"]["date-parts"][0]) && $doiCR == "" && isset($doi) && $doi != "") {
				$annCR = $arrayCR["message"]["published-print"]["date-parts"][0];
				foreach ($annCR as $value) {
					if ($value < 10) {$value = '0'.$value;}
					$txtAnnCR .= $value.'-';
				}
				$txtAnnCR = substr($txtAnnCR, 0, strlen($txtAnnCR)-1);
			}else{//pas de datepub via CR > recherche via Pubmed
				if (isset($doi) && $doi != "") {
					if (isset($arrayHAL["response"]["docs"][$cpt]["pubmedId_s"])) {
						$pmiHAL = $arrayHAL["response"]["docs"][$cpt]["pubmedId_s"];
					}
					$urlNCBI = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&retmode=json&term=".$doi."[lid]";
					askCurl($urlNCBI, $arrayNCBI);
					$numNCBI = $arrayNCBI["esearchresult"]["count"];
					if (isset($arrayNCBI["esearchresult"]["idlist"][0])) {
						$pmiPM = $arrayNCBI["esearchresult"]["idlist"][0];
						rechMetadoPMID($pmiPM, $absPM, $mcMESH, $lanPM, $mocPM, $dpbPM);
					}
				}
				if ($dpbPM != "") {
					$txtAnnCR = $dpbPM;
					$annCR[0] = substr($dpbPM, 0, 4);
				}else{//pas de datepub via Pubmed > recherche via ISTEX
					if (isset($doi) && $doi != "") {
						rechMetadoISTEX($doi, $absIS, $lanIS, $mocIS, $lanmocIS, $dpbIS);
					}
					if ($dpbIS != "") {
						$txtAnnCR = $dpbIS;
						$annCR[0] = substr($dpbIS, 0, 4);
					}
				}
			}						
			$deb = "";
			$fin = "";
			if (isset($annCR[0])) {
				$testAnnCR = $annCR[0];
				//echo $annHAL;
				if ($testAnnCR < substr($annHAL, 0, 4)) {
					//dates différentes mais pas de modification à effectuer
				}else{
					//if (($testAnnCR == substr($annHAL, 0, 4) && (strlen($txtAnnCR) > strlen($annHAL))) || (substr($annHAL, 0, 4) != substr($txtAnnCR, 0, 4) && substr($txtAnnCR, 0, 4) != "" && substr($annHAL, 5, 2) != substr($txtAnnCR, 5, 2) && substr($txtAnnCR, 5, 2) != "" && substr($annHAL, 8, 2) != substr($txtAnnCR, 8, 2) && substr($txtAnnCR, 8, 2) != "" )) {
					//if (($testAnnCR == substr($annHAL, 0, 4) && (strlen($txtAnnCR) > strlen($annHAL))) || (substr($annHAL, 0, 4) != substr($txtAnnCR, 0, 4))) {
					//Modification que si (AAAA-CR > AAAA-HAL) ou si (AAAA-CR = AAAA-HAL et AAAA-CR plus complète que AAAA-HAL)
					/*
					if ((intval(substr($txtAnnCR, 0, 4)) > intval(substr($annHAL, 0, 4))) || (intval(substr($txtAnnCR, 0, 4)) == intval(substr($annHAL, 0, 4)) && strlen($txtAnnCR) > strlen($annHAL))) {
						$deb = "<strong>";$fin = "</strong>";
					}
					*/
					//Modification que si (AAAA-CR > AAAA-HAL)
					if ((intval(substr($txtAnnCR, 0, 4)) > intval(substr($annHAL, 0, 4)))) {
						$deb = "<strong>";$fin = "</strong>";
					}
				}
			}
			$textAff .= "<td>".$annHAL."</td>";
			$textAff .= "<td style='text-align : center; background-color: #eeeeee;'>".$deb.$txtAnnCR.$fin."</td>";
		}

		//Date de mise en ligne CR
		if ($mel == "oui") {
			//var_dump($arrayCR["message"]["created"]);
			$txtMelCR = "";
			if (isset($arrayHAL["response"]["docs"][$cpt]["ePublicationDate_s"])) {
				$melHAL = $arrayHAL["response"]["docs"][$cpt]["ePublicationDate_s"];
			}
			if (isset($arrayCR["message"]["created"]["date-parts"][0]) && $doiCR == "" && isset($doi) && $doi != "") {
				$melCR = $arrayCR["message"]["created"]["date-parts"][0];
				foreach ($melCR as $value) {
					if ($value < 10) {$value = '0'.$value;}
					$txtMelCR .= $value.'-';
				}
				$txtMelCR = substr($txtMelCR, 0, strlen($txtMelCR)-1);
			}
			$deb = "";
			$fin = "";
			if (isset($melCR[0])) {
				$testMelCR = $melCR[0];
				if (($testMelCR == substr($melHAL, 0, 4) && (strlen($txtMelCR) > strlen($melHAL))) || (substr($melHAL, 0, 4) != substr($txtMelCR, 0, 4) && substr($txtMelCR, 0, 4) != "" && substr($melHAL, 5, 2) != substr($txtMelCR, 5, 2) && substr($txtMelCR, 5, 2) != "" && substr($melHAL, 8, 2) != substr($txtMelCR, 8, 2) && substr($txtMelCR, 8, 2) != "" )) {
					$deb = "<strong>";$fin = "</strong>";
				}
			}
			if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] != "COMM") {
				$textAff .= "<td>".$melHAL."</td>";
				$textAff .= "<td style='text-align : center; background-color: #eeeeee;'>".$deb.$txtMelCR.$fin."</td>";
			}else{//pas de date de mise en ligne pour les COMM
				$textAff .= "<td>&nbsp;</td>";
				$textAff .= "<td>&nbsp;</td>";
			}
		}
		
		//Date de mise en ligne OA
		/*
		if ($melOA == "oui") {
			$txtMelOAR = "";
			if (isset($arrayHAL["response"]["docs"][$cpt]["ePublicationDate_s"])) {
				$melHAL = $arrayHAL["response"]["docs"][$cpt]["ePublicationDate_s"];
			}
			$deb = "";
			$fin = "";
			if ((substr($melOAR, 0, 4) == substr($melHAL, 0, 4) && (strlen($melOAR) > strlen($melHAL))) || (substr($melHAL, 0, 4) != substr($melOAR, 0, 4) && substr($melOAR, 0, 4) != "" && substr($melHAL, 5, 2) != substr($melOAR, 5, 2) && substr($melOAR, 5, 2) != "" && substr($melHAL, 8, 2) != substr($melOAR, 8, 2) && substr($melOAR, 8, 2) != "" )) {
				$deb = "<strong>";$fin = "</strong>";
			}
			if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] != "COMM") {
				$textAff .= "<td>".$melHAL."</td>";
				$textAff .= "<td style='text-align : center; background-color: #eeeeee;'>".$deb.$melOAR.$fin."</td>";
			}else{//pas de date de mise en ligne pour les COMM
				$textAff .= "<td>&nbsp;</td>";
				$textAff .= "<td>&nbsp;</td>";
			}
		}
		*/
		
		//ISTEX
		if ($absISTEX == "oui" || $lanISTEX == "oui" || $mocISTEX == "oui") {
			if (isset($doi) && $doi != "") {
				rechMetadoISTEX($doi, $absIS, $lanIS, $mocIS, $lanmocIS, $dpbIS);
			}
		}
		
		//Mots-clés
		$txtMocHAL = "";
		$txtMocHALaff = "";
		$txtMocCRaff = "";
		if (isset($arrayHAL["response"]["docs"][$cpt]["keyword_s"])) {
			$mocHAL = $arrayHAL["response"]["docs"][$cpt]["keyword_s"];
			foreach ($mocHAL as $value) {
				$txtMocHAL .= $value.', ';
			}
			$txtMocHAL = substr($txtMocHAL, 0, strlen($txtMocHAL)-2);
			$txtMocHALred = substr($txtMocHAL, 0, 15)." ...";
			$txtMocHALaff = "<span tabindex='0' class='text-primary' data-toggle='popover' data-trigger='hover' title='' data-content='".$txtMocHAL."' data-original-title=''>".$txtMocHALred."</span>";
		}

		/*
		if ($mocCrossRef == "oui") {
			//var_dump($arrayCR["message"]["subject"]);
			$txtMocCR = "";
			if (isset($arrayCR["message"]["subject"]) && $doiCR == "" && isset($doi) && $doi != "") {
				$mocCR = $arrayCR["message"]["subject"];
				foreach ($mocCR as $value) {
					$txtMocCR .= $value.', ';
				}
				$txtMocCR = substr($txtMocCR, 0, strlen($txtMocCR)-2);
				$txtMocCRred = substr($txtMocCR, 0, 15)." ...";
				$txtMocCRaff = "<span tabindex='0' class='text-primary' data-toggle='popover' data-trigger='hover' title='' data-content='".$txtMocCR."' data-original-title=''>".$txtMocCRred."</span>";
			}
			//echo "<td>".$txtMocHALaff."</td>";
			//echo "<td style='background-color: #eeeeee;'>".$txtMocCRaff."</td>";
		}
		*/
		
		//Mots-clés PM
		$txtMocPMaff = "";
		if (isset($mocPM) && $mocPM != "") {
			if ($txtMocHAL != $mocPM) {
				if ($txtMocHAL != "") {$mocHALred = strtolower(substr($txtMocHAL, 0, 250));}else{$mocHALred= "";}
				if ($mocPM != "") {$mocPMred = strtolower(substr($mocPM, 0, 250));}else{$mocPMred= "";}

				if ($mocHALred != $mocPMred) {
					$pcMocPM = (250-levenshtein_utf8($mocHALred, $mocPMred))*100/250;
					$why = 'Indice de similarité : '.$pcMocPM.' %';
					$txtMocPMaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
				}else{
					$txtMocPMaff = "<img alt='OK' src='./img/ok.png'>";
				}
			}
		}
		
		//Mots-clés ISTEX
		$txtMocISaff = "";
		if (isset($mocIS) && $mocIS != "") {
			if ($txtMocHAL != $mocIS) {
				if ($txtMocHAL != "") {$mocHALred = strtolower(substr($txtMocHAL, 0, 250));}else{$mocHALred= "";}
				if ($mocIS != "") {$mocISred = strtolower(substr($mocIS, 0, 250));}else{$mocISred= "";}

				if ($mocHALred != $mocISred) {
					$pcMocIS = (250-levenshtein_utf8($mocHALred, $mocISred))*100/250;
					$why = 'Indice de similarité : '.$pcMocIS.' %';
					$txtMocISaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
				}else{
					$txtMocISaff = "<img alt='OK' src='./img/ok.png'>";
				}
			}
		}

		//Affichage des mots-clés
		if ($mocPubmed == "oui") {//Only HAL and PM
			$textAff .= "<td>".$txtMocHALaff."</td>";
			$textAff .= "<td style='background-color: #eeeeee; text-align: center;'>".$txtMocPMaff."</td>";
		}else{
			//if ($mocCrossRef == "oui") {
				if ($mocISTEX == "oui") {
					$textAff .= "<td>".$txtMocHALaff."</td>";
					//echo "<td style='background-color: #eeeeee;'>".$txtMocCRaff."</td>";
					$textAff .= "<td style='background-color: #eeeeee; text-align: center;'>".$txtMocISaff."</td>";
				}else{
					$textAff .= "<td>".$txtMocHALaff."</td>";
					//echo "<td style='background-color: #eeeeee;'>".$txtMocCRaff."</td>";
				}
			//}else{
			//  if ($mocISTEX == "oui") {
			//    echo "<td>".$txtMocHALaff."</td>";
			//    echo "<td style='background-color: #eeeeee; text-align: center;'>".$txtMocISaff."</td>";
			//  }else{
			//    echo "<td>".$txtMocHALaff."</td>";
			//  }
			//}
		}
		
		$indLimAbs = 95;
		//Résumé Pubmed
		$txtAbsPMaff = "";
		if ($absPubmed == "oui") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["abstract_s"][0])) {
				$absHAL = $arrayHAL["response"]["docs"][$cpt]["abstract_s"][0];
			}
			if ($absHAL != "") {$absHALred = strtolower(substr($absHAL, 0, 250));}else{$absHALred= "";}
			if ($absPM != "") {$absPM = str_ireplace("<br>", " ", $absPM); $absPMred = strtolower(substr($absPM, 0, 250));}else{$absPMred = "";}
			$why = "";
			$pc = (250-levenshtein_utf8($absHALred, $absPMred))*100/250;
			//if ($absHAL != "" && $absPM == "") {$why = "Résumé HAL présent - Résumé PM absent";}
			if ($absHAL != "" && $absPM != "") {
				//if ($absHALred != $absPMred) {
				if ($pc < $indLimAbs) {
					$why = 'Indice de similarité : '.$pc.' %';
					$txtAbsPMaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
				}else{
					$txtAbsPMaff = "<img alt='OK' src='./img/ok.png'>";
				}
			}
		}
		
		//Résumé ISTEX
		$txtAbsISaff = "";
		if ($absISTEX == "oui") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["abstract_s"][0])) {
				$absHAL = $arrayHAL["response"]["docs"][$cpt]["abstract_s"][0];
			}
			if ($absHAL != "") {$absHALred = strtolower(substr($absHAL, 0, 250));}else{$absHALred= "";}
			if ($absIS != "") {$absISred = strtolower(substr($absIS, 0, 250));}else{$absISred = "";}
			$why = "";
			$pc = (250-levenshtein_utf8($absHALred, $absISred))*100/250;
			if ($absHAL != "" && $absIS != "") {
				//if ($absHALred != $absISred) {
				if ($pc < $indLimAbs) {
					$why = 'Indice de similarité : '.$pc.' %';
					$txtAbsISaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
				}else{
					$txtAbsISaff = "<img alt='OK' src='./img/ok.png'>";
				}
			}
		}
		
		//Affichage des résumés
		if ($absPubmed == "oui") {
			if ($absISTEX == "oui") {
				$textAff .= "<td>".$txtAbsPMaff."</td>";
				$textAff .= "<td>".$txtAbsISaff."</td>";
			}else{
				$textAff .= "<td>".$txtAbsPMaff."</td>";
			}
		}else{
			if ($absISTEX == "oui") {
				$textAff .= "<td>".$txtAbsPMaff."</td>";
			}
		}
		
		
		//Langue Pubmed
		$txtLanPMaff = "";
		if ($lanPubmed == "oui") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["language_s"][0])) {
				$lanHAL = $arrayHAL["response"]["docs"][$cpt]["language_s"][0];
			}
			if ($lanPM != "") {$lanPMred = substr($lanPM, 0, 2);}else{$lanPMred = "";}

			if ($lanHAL != $lanPMred && $lanPMred != "") {
				$why = $lanHAL." <> ".$lanPMred;
				$txtLanPMaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
			}else{
				if ($lanPMred != "") {
					$txtLanPMaff = "<img alt='OK' src='./img/ok.png'>";
				}else{
					$txtLanPMaff = "&nbsp;";
				}
			}
		}

		//Langue ISTEX
		$txtLanISaff = "";
		if ($lanISTEX == "oui") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["language_s"][0])) {
				$lanHAL = $arrayHAL["response"]["docs"][$cpt]["language_s"][0];
			}
			if ($lanIS != "") {$lanISred = substr($lanIS, 0, 2);}else{$lanISred = "";}

			if ($lanHAL != $lanISred && $lanISred != "") {
				$why = $lanHAL." <> ".$lanISred;
				$txtLanISaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
			}else{
				if ($lanISred != "") {
					$txtLanISaff = "<img alt='OK' src='./img/ok.png'>";
				}else{
					$txtLanISaff = "&nbsp;";
				}
			}
		}
		
		//Langue CrossRef
		if (isset($arrayCR["message"]["language"])) {$lanCR = $arrayCR["message"]["language"];}
		$txtLanCRaff = "";
		if ($lanCrossRef == "oui") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["language_s"][0])) {
				$lanHAL = $arrayHAL["response"]["docs"][$cpt]["language_s"][0];
			}
			if ($lanCR != "") {$lanCRred = $lanCR;}else{$lanCRred= "";}
			
			if ($lanHAL != $lanCRred && $lanCRred != "") {
				$why = $lanHAL." <> ".$lanCRred;
				$txtLanCRaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
			}else{
				if ($lanCRred != "") {
					$txtLanCRaff = "<img alt='OK' src='./img/ok.png'>";
				}else{
					$txtLanCRaff = "&nbsp;";
				}
			}
		}
		
		//Langue OA
		$txtLanOARaff = "";
		if ($lanOA == "oui") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["language_s"][0])) {
				$lanHAL = $arrayHAL["response"]["docs"][$cpt]["language_s"][0];
			}
			if ($lanOAR != "") {$lanOARred = $lanOAR;}else{$lanOARred= "";}
			
			if ($lanHAL != $lanOARred && $lanOARred != "") {
				$why = $lanHAL." <> ".$lanOARred;
				$txtLanOARaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
			}else{
				if ($lanOARred != "") {
					$txtLanOARaff = "<img alt='OK' src='./img/ok.png'>";
				}else{
					$txtLanOARaff = "&nbsp;";
				}
			}
		}

		//Affichage de la langue
		if ($lanPubmed == "oui") {
			$textAff .= "<td>".$txtLanPMaff."</td>";
		}
		if ($lanISTEX == "oui") {
			$textAff .= "<td>".$txtLanISaff."</td>";
		}
		if ($lanCrossRef == "oui") {
			$textAff .= "<td>".$txtLanCRaff."</td>";
		}
		if ($lanOA == "oui") {
			$textAff .= "<td>".$txtLanOARaff."</td>";
		}
		
		//Financement CR
		if ($financement == "oui") {
			$txtFinHAL = "";
			$txtFinHALaff = "";
			$txtFinCR = "";
			$txtFinCRaff = "";
			if (isset($arrayHAL["response"]["docs"][$cpt]["funding_s"][0])) {
				$finHAL = $arrayHAL["response"]["docs"][$cpt]["funding_s"];
				foreach ($finHAL as $value) {
					$txtFinHAL .= $value.'; ';
				}
				$txtFinHAL = substr($txtFinHAL, 0, strlen($txtFinHAL)-2);
				$txtFinHALred = substr($txtFinHAL, 0, 15)." ...";
				$txtFinHALaff = "<span tabindex='0' class='text-primary' data-toggle='popover' data-trigger='hover' title='' data-content='".$txtFinHAL."' data-original-title=''>".$txtFinHALred."</span>";
			}
			$textAff .= "<td>".$txtFinHALaff."</td>";
			if (isset($arrayCR["message"]["funder"])) {
				$finCR = $arrayCR["message"]["funder"];
				foreach ($finCR as $value) {
					if (isset($value["award"][0]) && $value["award"][0] != "Not applicable") {
						$txtFinCR .= $value["award"][0].', ';
					}
					if ($value["name"] != "Not applicable") {$txtFinCR .= $value["name"].'; ';}
				}
				$txtFinCR = substr($txtFinCR, 0, strlen($txtFinCR)-2);
				$txtFinCRred = substr($txtFinCR, 0, 15)." ...";
				$txtFinCRaff = "<span tabindex='0' class='text-primary' data-toggle='popover' data-trigger='hover' title='' data-content='".$txtFinCR."' data-original-title=''>".$txtFinCRred."</span>";
			}
			$textAff .= "<td style='background-color: #eeeeee;'>".$txtFinCRaff."</td>";
		}
		
		//Financement OA
		if ($finOA == "oui") {
			$txtFinHAL = "";
			$txtFinHALaff = "";
			$txtFinOAR = "";
			$txtFinOARaff = "";
			if (isset($arrayHAL["response"]["docs"][$cpt]["funding_s"][0])) {
				$finHAL = $arrayHAL["response"]["docs"][$cpt]["funding_s"];
				foreach ($finHAL as $value) {
					$txtFinHAL .= $value.'; ';
				}
				$txtFinHAL = substr($txtFinHAL, 0, strlen($txtFinHAL)-2);
				$txtFinHALred = substr($txtFinHAL, 0, 15)." ...";
				$txtFinHALaff = "<span tabindex='0' class='text-primary' data-toggle='popover' data-trigger='hover' title='' data-content='".$txtFinHAL."' data-original-title=''>".$txtFinHALred."</span>";
			}
			$textAff .= "<td>".$txtFinHALaff."</td>";
			if ($finOAR != '') {
				$txtFinOAR = $finOAR;
				$txtFinOARred = substr($txtFinOAR, 0, 15)." ...";
				$txtFinOARaff = "<span tabindex='0' class='text-primary' data-toggle='popover' data-trigger='hover' title='' data-content='".$txtFinOAR."' data-original-title=''>".$txtFinOARred."</span>";
			}
			$textAff .= "<td style='background-color: #eeeeee;'>".$txtFinOARaff."</td>";
		}

		//ANR CR
		if ($anr == "oui") {
			$txtAnrHAL = "";
			$txtAnrHALAff = "";
			$txtAnrCR = "";
			$txtAnrCRAff = "";
			if (isset($arrayHAL["response"]["docs"][$cpt]["anrProjectReference_s"])){
				$txtAnrHAL = $arrayHAL["response"]["docs"][$cpt]["anrProjectReference_s"];
				foreach ($txtAnrHAL as $t) {
					$txtAnrHALAff .= $t."; ";
				}
			$txtAnrHALAff = substr($txtAnrHALAff, 0, strlen($txtAnrHALAff)-2);
			}
			if (isset($txtFinCR) && $txtFinCR != "") {
				if (strpos($txtFinCR, "ANR-") !== false) {
					$txtAnrCR = explode(";", $txtFinCR);
					foreach ($txtAnrCR as $t) {
						$txtAnrCRtab = explode(",", $t);
						foreach ($txtAnrCRtab as $ta) {
							if (strpos($ta, "ANR-") !== false) {
								$txtAnrCRAff = trim(strtoupper(strstr("ANR", $ta)))."; ";
							}
						}
					}
				}
				$txtAnrCRAff = substr($txtAnrCRAff, 0, strlen($txtAnrCRAff)-2);
			}
			$textAff .= "<td>".$txtAnrHALAff."</td>";
			$textAff .= "<td style='background-color: #eeeeee;'>".$txtAnrCRAff."</td>";
		}
		
		//ANR OA
		if ($anrOA == "oui") {
			$txtAnrHAL = "";
			$txtAnrHALAff = "";
			$anrOARcorr = $anrOAR;
			$txtAnrOARAff = "";
			if (isset($arrayHAL["response"]["docs"][$cpt]["anrProjectReference_s"])){
				$txtAnrHAL = $arrayHAL["response"]["docs"][$cpt]["anrProjectReference_s"];
				foreach ($txtAnrHAL as $t) {
					$txtAnrHALAff .= $t."; ";
					//Tester si présence projet dans HAL et OA
					if (strpos($anrOAR, $t) !== false) {$anrOARcorr = str_replace(array($t.'; ', $t), '', $anrOARcorr);}
				}
			$txtAnrHALAff = substr($txtAnrHALAff, 0, strlen($txtAnrHALAff)-2);
			}
			$txtAnrOARAff = $anrOARcorr;
			if (substr($txtAnrOARAff, -2) == '; ') {$txtAnrOARAff = substr($txtAnrOARAff, 0, -2);}
			$textAff .= "<td>".$txtAnrHALAff."</td>";
			$textAff .= "<td style='background-color: #eeeeee;'>".$txtAnrOARAff."</td>";
		}
		
		//Actions
		$lienMAJ = "";
		$lienMAJgrp = "";
		$actsMAJ = "";
		$actsMAJgrp = "";
		$actMaj = "ok";
		$raisons = "";
		$tei = $arrayHAL["response"]["docs"][$cpt]["label_xml"];
		//echo 'toto'.$tei.'otot';
		//echo $arrayHAL["response"]["docs"][$cpt]["halId_s"];
		//$tei = str_replace(array('<p>', '</p>'), '', $tei);
		//$tei = str_replace('<p part="N">HAL API platform', '<p part="N">HAL API platform</p>', $tei);
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
		
		// Si DOI HAL absent mais trouvé via CrossRef ou OA
		// Si notice de type COMM, la modification du DOI est-elle autorisée ?
		if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "ART" || ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "COMM" && $DOIComm == "oui")) {
			if (isset($doiCrossRef) && $doiCrossRef == "oui" && $doiHAL == "inconnu" && $doiCR != "") {
				$insert = "";
				$elts = $xml->getElementsByTagName("ref");
				foreach ($elts as $elt) {
					if ($elt->hasAttribute("type")) {
						$quoi = $elt->getAttribute("type");
						if ($quoi == "publisher") {
							insertNode($xml, $doiCR, "biblStruct", "ref", "idno", "type", "doi", "", "", "iB");
							$insert = "ok";
						}
					}
				}
				if ($insert == "") {
					insertNode($xml, $doiCR, "biblStruct", "monogr", "idno", "type", "doi", "", "", "aC");
				}
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_DOI",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "DOI, "; }
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_DOI~"; $lienMAJgrp .= "~A_exclure:".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_DOI";}
			}else{
				if (isset($doiOA) && $doiOA == "oui" && $doiHAL == "inconnu" && $doiOAR != "") {//DOI via OA
					$insert = "";
					$elts = $xml->getElementsByTagName("ref");
					foreach ($elts as $elt) {
						if ($elt->hasAttribute("type")) {
							$quoi = $elt->getAttribute("type");
							if ($quoi == "publisher") {
								insertNode($xml, $doiOAR, "biblStruct", "ref", "idno", "type", "doi", "", "", "iB");
								$insert = "ok";
							}
						}
					}
					if ($insert == "") {
						insertNode($xml, $doiOAR, "biblStruct", "monogr", "idno", "type", "doi", "", "", "aC");
					}
					$xml->save($Fnm);
					$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
					include "./CrossHAL_actions.php";
					$testMaj = "ok";
					foreach($ACTIONS_LISTE as $tab) {
						if (in_array($halID, $tab) && in_array("MAJ_DOI",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "DOI, "; }
					}
					if ($testMaj == "ok") {$actsMAJ .= "MAJ_DOI~"; $lienMAJgrp .= "~A_exclure:".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_DOI";}
				}
			}
		}
		
		//Si article et champs popularLevel_s et peerReviewing_s absents > ajout des noeuds note type="popular" et note type="peer"
		if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "ART" && (!isset($arrayHAL["response"]["docs"][$cpt]["popularLevel_s"]) || !isset($arrayHAL["response"]["docs"][$cpt]["peerReviewing_s"]))) {
			insertNode($xml, "No", "notesStmt", "note", "note", "type", "popular", "n", "0", "aC");
			insertNode($xml, "Yes", "notesStmt", "note", "note", "type", "peer", "n", "1", "aC");
			$xml->save($Fnm);
			$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
			include "./CrossHAL_actions.php";
			$testMaj = "ok";
			foreach($ACTIONS_LISTE as $tab) {
				if (in_array($halID, $tab) && in_array("MAJ_POP",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "DOI, "; }
			}
			if ($testMaj == "ok") {$actsMAJ .= "MAJ_POP~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_POP";}
		}

		//Si revue VALID trouvée alors qu'INCOMING à la base dans la notice
		if ($revue == "oui" && ($docidCRIH != $docidHAL && $docidCRIH != "")) {
			//docid
			$docid = $docidCRIH;
			insertNode($xml, $docid, "monogr", "title", "idno", "type", "halJournalId", "status", "VALID", "iB");

			//nom revue
			$rev = "";
			if ($revCRIH != "") {
				$rev = $revCRIH;
			}else{
				if ($revHAL != "") {
					$rev = $revHAL;
				}
			}
			insertNode($xml, $rev, "monogr", "title", "title", "level", "j", "", "", "iB");

			//issn
			$issn = "";
			if ($issnCRIH != "") {
				$issn = $issnCRIH;
			}else{
				if ($issnHAL != "") {
					$issn = $issnHAL;
				}
			}
			insertNode($xml, $issn, "monogr", "title", "idno", "type", "issn", "", "", "iB");
			
			//eissn
			$eissn = "";
			if ($eissnCRIH != "") {
				$eissn = $eissnCRIH;
			}else{
				if ($eissnHAL != "") {
					$eissn = $eissnHAL;
				}
			}
			insertNode($xml, $eissn, "monogr", "title", "idno", "type", "eissn", "", "", "iB");

			$xml->save($Fnm);
			$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
			include "./CrossHAL_actions.php";
			$testMaj = "ok";
			foreach($ACTIONS_LISTE as $tab) {
				if (in_array($halID, $tab) && in_array("MAJ_REV",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "revue, ";}
			}
			if ($testMaj == "ok") {$actsMAJ .= "MAJ_REV~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_REV";}
		}

		
		//Via CR, si vnp différents
		if ($vnp == "oui" && ($volCR."(".$numCR.")".$pagCR != "()" && $volHAL."(".$numHAL.")".$pagHAL != $volCR."(".$numCR.")".$pagCR)) {
		//if ($vnp == "oui" && ($volHAL != $volCR && $volCR != "" || $numHAL != $numCR && $numCR != "" || $pagHAL != $pagCR && $pagCR != "")) {
		//if ($volHAL != $volCR && $volCR != "" || $arrayHAL["response"]["docs"][$cpt]["halId_s"] == "hal-01509702") {
		//if ($volHAL != $volCR) {
			//On complète tous les champs HAL vides par CR
			if ($volHAL == "" && $volCR != "") {
				insertNode($xml, $volCR, "imprint", "date", "biblScope", "unit", "volume", "", "", "iB");
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_VOL",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "volume, ";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_VOL~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_VOL";}
				}
			if ($numHAL == "" && $numCR != "") {
				insertNode($xml, $numCR, "imprint", "date", "biblScope", "unit", "issue", "", "", "iB");
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_NUM",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "numéro, ";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_NUM~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_NUM";}
			}
			//On complète la pagination HAL par CR sauf si les champs vol et num sont déjà complétés dans HAL
			if ($pagCR != "" && $volHAL == "" && $numHAL == "") {
				insertNode($xml, $pagCR, "imprint", "date", "biblScope", "unit", "pp", "", "", "iB");
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_PAG",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "pagination, ";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_PAG~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_PAG";}
			}
		}
		
		//Via OA, si vnp différents
		if ($vnpOA == "oui" && ($volOAR."(".$numCR.")".$pagOAR != "()" && $volHAL."(".$numHAL.")".$pagHAL != $volOAR."(".$numOAR.")".$pagOAR)) {
			//On complète tous les champs HAL vides par OA
			if ($volHAL == "" && $volOAR != "") {
				insertNode($xml, $volOAR, "imprint", "date", "biblScope", "unit", "volume", "", "", "iB");
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_VOL",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "volume, ";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_VOL~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_VOL";}
				}
			if ($numHAL == "" && $numOAR != "") {
				insertNode($xml, $numCR, "imprint", "date", "biblScope", "unit", "issue", "", "", "iB");
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_NUM",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "numéro, ";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_NUM~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_NUM";}
			}
			//On complète la pagination HAL par OA sauf si les champs vol et num sont déjà complétés dans HAL
			if ($pagOAR != "" && $volHAL == "" && $numHAL == "") {
				insertNode($xml, $pagOAR, "imprint", "date", "biblScope", "unit", "pp", "", "", "iB");
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_PAG",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "pagination, ";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_PAG~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_PAG";}
			}
		}
		
		//Via CR, si financements différents
		if ($financement == "oui" && $txtFinCR != $txtFinHAL && $txtFinCR != "" && $txtFinHAL == "") {
			//noeud forcément absent puisque $txtFinHAL = "" > recherche du noeud 'biblFull' pour insérer les nouvelles données au bon emplacement
			$impr = $xml->getElementsByTagName('biblFull');
			foreach ($impr as $elt) {
				foreach($elt->childNodes as $item) { 
					if ($item->nodeName == "titleStmt") {
						$txtFinCRtab = explode(";", $txtFinCR);
						foreach($txtFinCRtab as $f) {
							$bif = $xml->createElement("funder");
							$cTn = $xml->createTextNode(trim($f));
							$bif->appendChild($cTn);
							$item->appendChild($bif);
						}
						break 2;
					}
				}
			}
			
			$xml->save($Fnm);
			$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
			include "./CrossHAL_actions.php";
			$testMaj = "ok";
			foreach($ACTIONS_LISTE as $tab) {
				if (in_array($halID, $tab) && in_array("MAJ_FIN",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "financement, ";}
			}
			if ($testMaj == "ok") {$actsMAJ .= "MAJ_FIN~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_FIN";}
		}
		
		//Via OA, si financements différents
		if ($finOA == "oui" && $txtFinOAR != $txtFinHAL && $txtFinOAR != "" && $txtFinHAL == "") {
			//noeud forcément absent puisque $txtFinHAL = "" > recherche du noeud 'biblFull' pour insérer les nouvelles données au bon emplacement
			$impr = $xml->getElementsByTagName('biblFull');
			foreach ($impr as $elt) {
				foreach($elt->childNodes as $item) { 
					if ($item->nodeName == "titleStmt") {
						$txtFinOARtab = explode(";", $txtFinOAR);
						foreach($txtFinOARtab as $f) {
							$bif = $xml->createElement("funder");
							$cTn = $xml->createTextNode(trim($f));
							$bif->appendChild($cTn);
							$item->appendChild($bif);
						}
						break 2;
					}
				}
			}
			
			$xml->save($Fnm);
			$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
			include "./CrossHAL_actions.php";
			$testMaj = "ok";
			foreach($ACTIONS_LISTE as $tab) {
				if (in_array($halID, $tab) && in_array("MAJ_FIN",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "financement, ";}
			}
			if ($testMaj == "ok") {$actsMAJ .= "MAJ_FIN~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_FIN";}
		}
		
		//ANR via CR
		$idANR = "";
		if ($anr == "oui" && $txtAnrCRAff != $txtAnrHALAff && $txtAnrCRAff != "") {
			$anrTab = explode(";", $txtAnrCRAff);
			foreach ($anrTab as $a) {
				if (substr($a, 0, 4) == "ANR-" && strpos($a, "IDEX") === false) {
					$urlANR = "https://api.archives-ouvertes.fr/ref/anrproject/?q=reference_sci:%22".trim($a)."%22&fl=title_s,valid_s,yearDate_s,docid,callTitle_s,acronym_s,reference_s";
					$contANR = file_get_contents($urlANR);
					$resANR = json_decode($contANR, true);
					$numANR = $resANR["response"]["numFound"];
					//echo 'toto : '.$numANR.' - '.trim($a).'<br>';
					if ($numANR == 1) {//Projet ANR trouvé
						$docid = $resANR["response"]["docs"][0]["docid"];
						$ref = "projanr-".$docid;
						$titre = $resANR["response"]["docs"][0]["title_s"];
						$acron = $resANR["response"]["docs"][0]["acronym_s"];
						$ref_s = $resANR["response"]["docs"][0]["reference_s"];
						$annee = $resANR["response"]["docs"][0]["yearDate_s"];
						$valid = $resANR["response"]["docs"][0]["valid_s"];
						
						//Pour vérifier s'il y un noeud 'funder'
						$nFunder = "non";
						$funs = $xml->getElementsByTagName("funder");
						foreach($funs as $fun) {
							$nFunder = "oui";
						}
						
						//Insertion ANR comme noeud 'funder'
						if ($nFunder == "oui") {//Il y a au moins un noeud 'funder'
							insertNode($xml, $ref_s, "titleStmt", "funder", "funder", "ref", "#".$ref, "", "", "aC");
							$xml->save($Fnm);
						}else{
							$nEditor = "non";//Pour vérfier s'il y un noeud 'editor' pour insérer 'funder' juste après
							$edts = $xml->getElementsByTagName("editor");
							foreach($edts as $edt) {
								$nEditor = "oui";
							}
							if ($nEditor == "oui") {
								insertNode($xml, $ref_s, "titleStmt", "editor", "funder", "ref", "#".$ref, "", "", "aC");
								$xml->save($Fnm);
							}else{
								insertNode($xml, $ref_s, "titleStmt", "author", "funder", "ref", "#".$ref, "", "", "aC");
								$xml->save($Fnm);
							}
						}
						
						//Y-a-t-il déjà un noeud listOrg pour les projets ?
						$nListOrg = "non";
						$orgs = $xml->getElementsByTagName("listOrg");
						foreach($orgs as $org) {
							if ($org->hasAttribute("type") && $org->getAttribute("type") == "projects") {
								$nListOrg = "oui";
							}
						}
						if ($nListOrg == "non") {
							$bacs = $xml->getElementsByTagName("back");
							$bimoc = $xml->createElement("listOrg");
							$bimoc->setAttribute("type", "projects");
							$bacs->item(0)->appendChild($bimoc);
							$xml->save($Fnm);
						}
						
						//Positionnement au noeud <listOrg type="projects"> pour ajout des noeuds enfants
						foreach($orgs as $org) {
							if ($org->hasAttribute("type") && $org->getAttribute("type") == "projects") {
								break;
							}
						}
						$bimoc = $xml->createElement("org");
						$moc = $xml->createTextNode("");
						$bimoc->setAttribute("type", "anrProject");
						$bimoc->setAttribute("xml:id", $ref);
						$bimoc->setAttribute("status", $valid);
						$bimoc->appendChild($moc);
						$org->appendChild($bimoc);
						$xml->save($Fnm);
						
						$orgs = $xml->getElementsByTagName("org");
						foreach($orgs as $org) {
							if ($org->hasAttribute("xml:id") && $org->getAttribute("xml:id") == $ref) {
								break;
							}
						}
						$bimoc = $xml->createElement("idno");
						$moc = $xml->createTextNode($ref_s);
						$bimoc->setAttribute("type", "anr");
						$bimoc->appendChild($moc);
						$org->appendChild($bimoc);
						$xml->save($Fnm);
						
						$bimoc = $xml->createElement("orgName");
						$moc = $xml->createTextNode($acron);
						$bimoc->appendChild($moc);
						$org->appendChild($bimoc);
						$xml->save($Fnm);
						
						$bimoc = $xml->createElement("desc");
						$moc = $xml->createTextNode($titre);
						$bimoc->appendChild($moc);
						$org->appendChild($bimoc);
						$xml->save($Fnm);
						
						$bimoc = $xml->createElement("date");
						$moc = $xml->createTextNode($annee);
						$bimoc->setAttribute("type", "start");
						$bimoc->appendChild($moc);
						$org->appendChild($bimoc);
						$xml->save($Fnm);
						
						
						$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
						include "./CrossHAL_actions.php";
						$testMaj = "ok";
						foreach($ACTIONS_LISTE as $tab) {
							if (in_array($halID, $tab) && in_array("MAJ_APA",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "'projet ANR', ";}
						}
						if ($testMaj == "ok") {$actsMAJ .= "MAJ_ANR~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_ANR";}
					}
				}
			}
		}
		
		//ANR via OA
		$idANR = "";
		if ($anrOA == "oui" && $txtAnrOARAff != $txtAnrHALAff && $txtAnrOARAff != "") {
			$anrTab = explode(";", $txtAnrOARAff);
			foreach ($anrTab as $a) {
				if (substr($a, 0, 4) == "ANR-" && strpos($a, "IDEX") === false) {
					$urlANR = "https://api.archives-ouvertes.fr/ref/anrproject/?q=reference_sci:%22".trim($a)."%22&fl=title_s,valid_s,yearDate_s,docid,callTitle_s,acronym_s,reference_s";
					$contANR = file_get_contents($urlANR);
					$resANR = json_decode($contANR, true);
					$numANR = $resANR["response"]["numFound"];
					//echo 'toto : '.$numANR.' - '.trim($a).'<br>';
					if ($numANR == 1) {//Projet ANR trouvé
						$docid = $resANR["response"]["docs"][0]["docid"];
						$ref = "projanr-".$docid;
						$titre = $resANR["response"]["docs"][0]["title_s"];
						$acron = $resANR["response"]["docs"][0]["acronym_s"];
						$ref_s = $resANR["response"]["docs"][0]["reference_s"];
						$annee = $resANR["response"]["docs"][0]["yearDate_s"];
						$valid = $resANR["response"]["docs"][0]["valid_s"];
						
						//Pour vérifier s'il y un noeud 'funder'
						$nFunder = "non";
						$funs = $xml->getElementsByTagName("funder");
						foreach($funs as $fun) {
							$nFunder = "oui";
						}
						
						//Insertion ANR comme noeud 'funder'
						if ($nFunder == "oui") {//Il y a au moins un noeud 'funder'
							insertNode($xml, $ref_s, "titleStmt", "funder", "funder", "ref", "#".$ref, "", "", "aC");
							$xml->save($Fnm);
						}else{
							$nEditor = "non";//Pour vérfier s'il y un noeud 'editor' pour insérer 'funder' juste après
							$edts = $xml->getElementsByTagName("editor");
							foreach($edts as $edt) {
								$nEditor = "oui";
							}
							if ($nEditor == "oui") {
								insertNode($xml, $ref_s, "titleStmt", "editor", "funder", "ref", "#".$ref, "", "", "aC");
								$xml->save($Fnm);
							}else{
								insertNode($xml, $ref_s, "titleStmt", "author", "funder", "ref", "#".$ref, "", "", "aC");
								$xml->save($Fnm);
							}
						}
						
						//Y-a-t-il déjà un noeud listOrg pour les projets ?
						$nListOrg = "non";
						$orgs = $xml->getElementsByTagName("listOrg");
						foreach($orgs as $org) {
							if ($org->hasAttribute("type") && $org->getAttribute("type") == "projects") {
								$nListOrg = "oui";
							}
						}
						if ($nListOrg == "non") {
							$bacs = $xml->getElementsByTagName("back");
							$bimoc = $xml->createElement("listOrg");
							$bimoc->setAttribute("type", "projects");
							$bacs->item(0)->appendChild($bimoc);
							$xml->save($Fnm);
						}
						
						//Positionnement au noeud <listOrg type="projects"> pour ajout des noeuds enfants
						foreach($orgs as $org) {
							if ($org->hasAttribute("type") && $org->getAttribute("type") == "projects") {
								break;
							}
						}
						$bimoc = $xml->createElement("org");
						$moc = $xml->createTextNode("");
						$bimoc->setAttribute("type", "anrProject");
						$bimoc->setAttribute("xml:id", $ref);
						$bimoc->setAttribute("status", $valid);
						$bimoc->appendChild($moc);
						$org->appendChild($bimoc);
						$xml->save($Fnm);
						
						$orgs = $xml->getElementsByTagName("org");
						foreach($orgs as $org) {
							if ($org->hasAttribute("xml:id") && $org->getAttribute("xml:id") == $ref) {
								break;
							}
						}
						$bimoc = $xml->createElement("idno");
						$moc = $xml->createTextNode($ref_s);
						$bimoc->setAttribute("type", "anr");
						$bimoc->appendChild($moc);
						$org->appendChild($bimoc);
						$xml->save($Fnm);
						
						$bimoc = $xml->createElement("orgName");
						$moc = $xml->createTextNode($acron);
						$bimoc->appendChild($moc);
						$org->appendChild($bimoc);
						$xml->save($Fnm);
						
						$bimoc = $xml->createElement("desc");
						$moc = $xml->createTextNode($titre);
						$bimoc->appendChild($moc);
						$org->appendChild($bimoc);
						$xml->save($Fnm);
						
						$bimoc = $xml->createElement("date");
						$moc = $xml->createTextNode($annee);
						$bimoc->setAttribute("type", "start");
						$bimoc->appendChild($moc);
						$org->appendChild($bimoc);
						$xml->save($Fnm);
						
						
						$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
						include "./CrossHAL_actions.php";
						$testMaj = "ok";
						foreach($ACTIONS_LISTE as $tab) {
							if (in_array($halID, $tab) && in_array("MAJ_APA",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "'projet ANR', ";}
						}
						if ($testMaj == "ok") {$actsMAJ .= "MAJ_ANR~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_ANR";}
					}
				}
			}
		}
		
		
		//Si article "à paraître" mais Vol(n)pp CR non nul > suppression subtype=inPress
		if ($bapa && $txtAnnCR != "") {
			insertNode($xml, $txtAnnCR, "imprint", "date", "date", "type", "datePub", "", "", "iB");
			$xml->save($Fnm);
			$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
			include "./CrossHAL_actions.php";
			$testMaj = "ok";
			foreach($ACTIONS_LISTE as $tab) {
				if (in_array($halID, $tab) && in_array("MAJ_APA",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "'à paraître', ";}
			}
			if ($testMaj == "ok") {$actsMAJ .= "MAJ_APA~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_APA";}
		}
		
		//Si années de publication différentes
		if ($anneepub == "oui") {
			//On vérifie d'abord que, pour l&apos;année en cours uniquement : si la date de publication CrossRef YYYY est < date de publication HAL YYYY (ne pas tenir compte des MM et DD) => ne pas modifier (l&apos;info CrossRef n&apos;est sans doute pas encore à jour)
			if (isset($annCR[0])) {
				$testAnnCR = $annCR[0];
				if ($testAnnCR < substr($annHAL, 0, 4)) {
					//dates différentes mais pas de modification à effectuer
				}else{
					//if (($testAnnCR == substr($annHAL, 0, 4) && (strlen($txtAnnCR) > strlen($annHAL))) || (substr($annHAL, 0, 4) != substr($txtAnnCR, 0, 4) && substr($txtAnnCR, 0, 4) != "" && substr($annHAL, 5, 2) != substr($txtAnnCR, 5, 2) && substr($txtAnnCR, 5, 2) != "" && substr($annHAL, 8, 2) != substr($txtAnnCR, 8, 2) && substr($txtAnnCR, 8, 2) != "" )) {
					//if (($testAnnCR == substr($annHAL, 0, 4) && (strlen($txtAnnCR) > strlen($annHAL))) || (substr($annHAL, 0, 4) != substr($txtAnnCR, 0, 4))) {
					//Modification que si (AAAA-CR > AAAA-HAL) ou si (AAAA-CR = AAAA-HAL et AAAA-CR plus complète que AAAA-HAL)
					//if ((intval(substr($txtAnnCR, 0, 4)) > intval(substr($annHAL, 0, 4))) || (intval(substr($txtAnnCR, 0, 4)) == intval(substr($annHAL, 0, 4)) && strlen($txtAnnCR) > strlen($annHAL))) {
					//Modification que si (AAAA-CR > AAAA-HAL)
					if ((intval(substr($txtAnnCR, 0, 4)) > intval(substr($annHAL, 0, 4)))) {
						insertNode($xml, $txtAnnCR, "imprint", "date", "date", "type", "datePub", "", "", "iB");
						$xml->save($Fnm);
						$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
						include "./CrossHAL_actions.php";
						$testMaj = "ok";
						foreach($ACTIONS_LISTE as $tab) {
							if (in_array($halID, $tab) && in_array("MAJ_ANN",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "année de publication, ";}
						}
						if ($testMaj == "ok") {$actsMAJ .= "MAJ_ANN~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_ANN";}
					}
				}
			}
		}

		//Via CR, si dates de mise en ligne différentes			
		if ($mel == "oui" && $arrayHAL["response"]["docs"][$cpt]["docType_s"] != "COMM") {
			//Modification uniquement si la date de publication est postérieure à la date de mise en ligne
			//echo $annHAL." - ".$melHAL;
			$testApuHAL = "";
			$testMelHAL = "";
			if ($annHAL != "") {
				if (strlen($annHAL) == 4) {$testApuHAL = mktime(0, 0, 0, 12, 31, $annHAL);}
				if (strlen($annHAL) == 7) {$testApuHAL = mktime(0, 0, 0, substr($annHAL, 5, 2), 31, substr($annHAL, 0, 4));}
				if (strlen($annHAL) == 10) {$testApuHAL = mktime(0, 0, 0, substr($annHAL, 5, 2), substr($annHAL, 8, 2), substr($annHAL, 0, 4));}
			}
			if ($melHAL != "") {
				if (strlen($melHAL) == 4) {$testMelHAL = mktime(0, 0, 0, 12, 31, $melHAL);}
				if (strlen($melHAL) == 7) {$testMelHAL = mktime(0, 0, 0, substr($melHAL, 5, 2), 31, substr($melHAL, 0, 4));}
				if (strlen($melHAL) == 10) {$testMelHAL = mktime(0, 0, 0, substr($melHAL, 5, 2), substr($melHAL, 8, 2), substr($melHAL, 0, 4));}
			}
			if ($testApuHAL != "" && $testApuHAL >= $testMelHAL) {
				if (isset($melCR[0])) {
					$testMelCR = $melCR[0];
					if (($testMelCR == substr($melHAL, 0, 4) && (strlen($txtMelCR) > strlen($melHAL))) || (substr($melHAL, 0, 4) != substr($txtMelCR, 0, 4) && substr($txtMelCR, 0, 4) != "" && substr($melHAL, 5, 2) != substr($txtMelCR, 5, 2) && substr($txtMelCR, 5, 2) != "" && substr($melHAL, 8, 2) != substr($txtMelCR, 8, 2) && substr($txtMelCR, 8, 2) != "" )) {
						insertNode($xml, $txtMelCR, "imprint", "date", "date", "type", "dateEpub", "", "", "aC");
						$xml->save($Fnm);
						$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
						include "./CrossHAL_actions.php";
						$testMaj = "ok";
						foreach($ACTIONS_LISTE as $tab) {
							if (in_array($halID, $tab) && in_array("MAJ_MEL",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "date de mise en ligne, ";}
						}
						if ($testMaj == "ok") {$actsMAJ .= "MAJ_MEL~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_MEL";}
					}
				}
			}
		}
		
		//Via OA, si dates de mise en ligne différentes			
		/*
		if ($melOA == "oui" && $arrayHAL["response"]["docs"][$cpt]["docType_s"] != "COMM") {
			//Modification uniquement si la date de publication est postérieure à la date de mise en ligne
			//echo $annHAL." - ".$melHAL;
			$testApuHAL = "";
			$testMelHAL = "";
			if ($annHAL != "") {
				if (strlen($annHAL) == 4) {$testApuHAL = mktime(0, 0, 0, 12, 31, $annHAL);}
				if (strlen($annHAL) == 7) {$testApuHAL = mktime(0, 0, 0, substr($annHAL, 5, 2), 31, substr($annHAL, 0, 4));}
				if (strlen($annHAL) == 10) {$testApuHAL = mktime(0, 0, 0, substr($annHAL, 5, 2), substr($annHAL, 8, 2), substr($annHAL, 0, 4));}
			}
			if ($melHAL != "") {
				if (strlen($melHAL) == 4) {$testMelHAL = mktime(0, 0, 0, 12, 31, $melHAL);}
				if (strlen($melHAL) == 7) {$testMelHAL = mktime(0, 0, 0, substr($melHAL, 5, 2), 31, substr($melHAL, 0, 4));}
				if (strlen($melHAL) == 10) {$testMelHAL = mktime(0, 0, 0, substr($melHAL, 5, 2), substr($melHAL, 8, 2), substr($melHAL, 0, 4));}
			}
			if ($testApuHAL != "" && $testApuHAL >= $testMelHAL) {
				if (isset($melOAR)) {
					if ((substr($melOAR, 0, 4) == substr($melHAL, 0, 4) && (strlen($melOAR) > strlen($melHAL))) || (substr($melHAL, 0, 4) != substr($melOAR, 0, 4) && substr($melOAR, 0, 4) != "" && substr($melHAL, 5, 2) != substr($melOAR, 5, 2) && substr($melOAR, 5, 2) != "" && substr($melHAL, 8, 2) != substr($melOAR, 8, 2) && substr($melOAR, 8, 2) != "" )) {
						insertNode($xml, $melOAR, "imprint", "date", "date", "type", "dateEpub", "", "", "aC");
						$xml->save($Fnm);
						$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
						include "./CrossHAL_actions.php";
						$testMaj = "ok";
						foreach($ACTIONS_LISTE as $tab) {
							if (in_array($halID, $tab) && in_array("MAJ_MEL",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "date de mise en ligne, ";}
						}
						if ($testMaj == "ok") {$actsMAJ .= "MAJ_MEL~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_MEL";}
					}
				}
			}
		}
		*/

		//Ajout de mots-clés
		$indLim = 90;
		
		//PM
		if ($mocPubmed == "oui") {
			//if ($pcMocPM < $indLim && $mocPM != "") {
			if (empty($mocHAL) && $mocPM != "") {
				//si noeud présent
				$mocTab = explode(",", $mocPM);
				$keyw = $xml->getElementsByTagName('keywords')->item(0);
				if (isset($keyw)) {
					foreach($mocTab as $i) {
						if (stripos(",".$txtMocHAL, $i) === false) { //mot-clé PM n'existe pas déjà parmi ceux d'HAL
							$bimoc = $xml->createElement("term");
							$moc = $xml->createTextNode(trim($i));
							$bimoc->setAttribute("xml:lang", "en");
							$bimoc->appendChild($moc);
							$keyw->appendChild($bimoc);
						}
					}
				}else{
					//si noeud absent > recherche du noeud 'textClass' pour insérer les nouvelles données au bon emplacement        
					$textC = $xml->getElementsByTagName('textClass');
					foreach ($textC as $elt) {
						foreach($elt->childNodes as $item) { 
							if ($item->hasChildNodes()) {
								$childs = $item->childNodes;
								foreach($childs as $i) {
									$name = $i->parentNode->nodeName;
									if ($name == "classCode" && stripos(",".$txtMocHAL, $i->parentNode->nodeValue) === false) {//insertion nvx noeuds si mot-clé PM n'existe pas déjà parmi ceux de HAL
										$cE = $xml->createElement("keywords");
										$cE->setAttribute("scheme", "author");
										$xml->appendChild($cE);
										$textC0 = $xml->getElementsByTagName("textClass")->item(0);
										$textC0->insertBefore($cE, $i->parentNode);
									}
									break 2;
								}
							}
						}
					}
					$keyw = $xml->getElementsByTagName('keywords')->item(0);
					foreach ($mocTab as $i) {
						if (stripos(",".$txtMocHAL, $i) === false) { //mot-clé PM n'existe pas déjà parmi ceux d'HAL
							$bimoc = $xml->createElement("term");
							$moc = $xml->createTextNode(trim($i));
							$bimoc->setAttribute("xml:lang", "en");
							$bimoc->appendChild($moc);
							$keyw->appendChild($bimoc);
						}
					}
				}
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_MOC",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "mots-clés ";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_MOC~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_MOC";}
			}
		}
		
		//ISTEX
		if ($mocISTEX == "oui") {
			//if ($pcMocIS < $indLim && $mocIS != "") {
			if (empty($mocHAL) && $mocIS != "") {
				//si noeud présent
				$mocTab = explode(",", $mocIS);
				$keyw = $xml->getElementsByTagName('keywords')->item(0);
				if (isset($keyw)) {
					foreach($mocTab as $i) {
						if (stripos(",".$txtMocHAL, $i) === false) { //mot-clé PM n'existe pas déjà parmi ceux d'HAL
							$bimoc = $xml->createElement("term");
							$moc = $xml->createTextNode(trim($i));
							$bimoc->setAttribute("xml:lang", "en");
							$bimoc->appendChild($moc);
							$keyw->appendChild($bimoc);
						}
					}
				}else{
					//si noeud absent > recherche du noeud 'textClass' pour insérer les nouvelles données au bon emplacement        
					$textC = $xml->getElementsByTagName('textClass');
					foreach ($textC as $elt) {
						foreach($elt->childNodes as $item) { 
							if ($item->hasChildNodes()) {
								$childs = $item->childNodes;
								foreach($childs as $i) {
									$name = $i->parentNode->nodeName;
									if ($name == "classCode" && stripos(",".$txtMocHAL, $i->parentNode->nodeValue) === false) {//insertion nvx noeuds si mot-clé PM n'existe pas déjà parmi ceux de HAL
										$cE = $xml->createElement("keywords");
										$cE->setAttribute("scheme", "author");
										$xml->appendChild($cE);
										$textC0 = $xml->getElementsByTagName("textClass")->item(0);
										$textC0->insertBefore($cE, $i->parentNode);
									}
									break 2;
								}
							}
						}
					}
					$keyw = $xml->getElementsByTagName('keywords')->item(0);
					foreach ($mocTab as $i) {
						if (stripos(",".$txtMocHAL, $i) === false) { //mot-clé PM n'existe pas déjà parmi ceux d'HAL
							$bimoc = $xml->createElement("term");
							$moc = $xml->createTextNode(trim($i));
							$bimoc->setAttribute("xml:lang", "en");
							$bimoc->appendChild($moc);
							$keyw->appendChild($bimoc);
						}
					}
				}
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_MOC",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "mots-clés ";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_MOC~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_MOC";}
			}
		}
		
		//Ajout d'un résumé > Pubmed prioritaire par rapport à ISTEX
		$indLimAbs = 95;
		$pcPM = 100;
		$pcIS = 100;
		if ($absHAL != "") {$absHALred = strtolower(substr($absHAL, 0, 250));}else{$absHALred= "";}
		if ($absPM != "") {$absPM = str_ireplace("<br>", " ", $absPM); $absPMred = strtolower(substr($absPM, 0, 250));}else{$absPMred = "";}
		if ($absIS != "") {$absIS = str_ireplace("<br>", " ", $absIS); $absISred = strtolower(substr($absIS, 0, 250));}else{$absISred = "";}
		if ($absHAL != "" && $absPM != "") {
			if ($absHALred != $absPMred) {
				$pcPM = (250-levenshtein_utf8($absHALred, $absPMred))*100/250;
			}
		}
		if ($absHAL != "" && $absIS != "") {
			if ($absHALred != $absISred) {
				$pcIS = (250-levenshtein_utf8($absHALred, $absISred))*100/250;
			}
		}
		//echo 'HAL : '.$absHAL.'<br><br>'.'PM : '.$absPM.'<br>'.$pcPM.'<br>';
		//echo 'HAL : '.$absHALred.'<br><br>'.'PM : '.$absPMred.'<br>'.$pcPM.'<br>';
		if ($absPubmed == "oui" && $absPM != $absHAL && $absPM != "" && $pcPM < $indLimAbs) {
			insertNode($xml, $absPM, "profileDesc", "", "abstract", "xml:lang", "en", "", "", "aC");
			$xml->save($Fnm);
			$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
			include "./CrossHAL_actions.php";
			$testMaj = "ok";
			foreach($ACTIONS_LISTE as $tab) {
				if (in_array($halID, $tab) && in_array("MAJ_ABS",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "résumé ";}
			}
			if ($testMaj == "ok") {$actsMAJ .= "MAJ_ABS~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_ABS";}
		}else{
			if ($absISTEX == "oui" && $absIS != $absHAL && $absIS != ""  && $pcIS < $indLimAbs) {
				insertNode($xml, $absIS, "profileDesc", "", "abstract", "xml:lang", "en", "", "", "aC");
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_ABS",$tab)) {$actMaj = "no"; $testMaj = "no";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_ABS~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_ABS";}
			}
		}
		
		//Modification de la langue
		$lanTest = "";
		$lanTestred = "";
		if ($lanPubmed == "oui") {$lanTest = $lanPM; $lanTestred = $lanPMred;}
		if ($lanISTEX == "oui") {$lanTest = $lanIS; $lanTestred = $lanISred;}
		if ($lanCrossRef == "oui") {$lanTest = $lanCR; $lanTestred = $lanCRred;}
		if ($lanOA == "oui") {$lanTest = $lanOAR; $lanTestred = $lanOARred;}
		if ($lanTest != "" && $lanTestred != $lanHAL && $lanTestred != "") {
			if ($lanTest == "eng" || $lanTest == "en") {
				insertNode($xml, "English", "langUsage", "", "language", "ident", "en", "", "", "aC");
				insertNode($xml, "international", "notesStmt", "", "note", "type", "audience", "n", "2", "aC");
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_LAN",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "langue ";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_LAN~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_LAN";}
			}else{
				if (strlen($lanTest) == 2) {$lanTest = $countries23[$lanTest];}
				insertNode($xml, $countries[$lanTest], "langUsage", "", "language", "ident", substr($lanTest,0,2), "", "", "aC");
				insertNode($xml, "national", "notesStmt", "", "note", "type", "audience", "n", "3", "aC");
				$xml->save($Fnm);
				$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				include "./CrossHAL_actions.php";
				$testMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_LAN",$tab)) {$actMaj = "no"; $testMaj = "no";}
				}
				if ($testMaj == "ok") {$actsMAJ .= "MAJ_LAN~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_LAN";}
			}
		}
		
		//Modification de l'audience
		

		
		//si PMID différents
		if ($pmid == "oui" && $pmiPM != $pmiHAL && $pmiPM != "") {
			//echo 'toto !';
			//insertNode($xml, $pmiPM, "biblStruct", "monogr", "idno", "type", "pubmed", "", "", "aC");
			//insertNode($xml, $pmiPM, "biblStruct", "ref", "idno", "type", "pubmed", "", "", "iB");
			insertNode($xml, $pmiPM, "biblStruct", "idno", "idno", "type", "pubmed", "", "", "iB");
			//insertNode($xml, $pmiPM, "biblStruct", "idno", "idno", "type", "pubmed", "", "", "aC");

			$xml->save($Fnm);
			$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
			include "./CrossHAL_actions.php";
			$testMaj = "ok";
			foreach($ACTIONS_LISTE as $tab) {
				if (in_array($halID, $tab) && in_array("MAJ_PMI",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "PMID ";}
			}
			if ($testMaj == "ok") {$actsMAJ .= "MAJ_PMI~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"]; $actsMAJgrp .= "~MAJ_PMI";}
		}

		if ($colact == "ok") {
			if ($lienMAJ != "") {
				$textAff .= "<td>";
				//if ($lienMAJ != "") {echo "<span id='maj".$halID."'><a target='_blank' href='".$lienMAJ."' onclick='majok(\"".$doi."\")'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span>";}
				if ($actMaj == "ok") {
					//"Embargo" > Interdit de modifier une notice si date "whenSubmitted" < n jours
					$submDate = "";
					$elts = $xml->getElementsByTagName("date");
					foreach ($elts as $elt) {
						if ($elt->hasAttribute("type")) {
							$quoi = $elt->getAttribute("type");
							if ($quoi == "whenSubmitted") {
								$submDate = $elt->nodeValue;
							}
						}
					}
					//Vérification "whenEndEmbargoed"
					$embgDate = "";
					$embgModi = "ok";
					$elts = $xml->getElementsByTagName("date");
					foreach ($elts as $elt) {
						if ($elt->hasAttribute("type")) {
							$quoi = $elt->getAttribute("type");
							if ($quoi == "whenEndEmbargoed") {
								$embgDate = $elt->nodeValue;
							}
						}
					}
					if ($embgDate != "") {
						$embgDate = mktime(0, 0, 0, substr($embgDate, 5, 2), substr($embgDate, 8, 2), substr($embgDate, 0, 4));
						$limDate = time();
						if ($embgDate > $limDate) {//La date whenEndEmbargoed n'est pas dépassée > suppression des noeuds <ref type="file">
							//$embgModi = "pasok";
							$nomfic = "./XML/".$halID.".xml";
							$elts = $xml->getElementsByTagName("ref");
							$nbelt = $elts->length;
							for ($pos = $nbelt; --$pos >= 0;) {
								$elt = $elts->item($pos);
								if ($elt && $elt->hasAttribute("type")) {
									$quoi = $elt->getAttribute("type");
									if ($quoi == "file") {
										$elt->parentNode->removeChild($elt);
										$xml->save($nomfic);
									}
								}
							}
						}
					}
					if ($embgModi == "ok") {
						$actsMAJ = substr($actsMAJ, 0, (strlen($actsMAJ) - 1));
						$textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienMAJ."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"".$actsMAJ."\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span></center>";
						$lienMAJgrpTot .= $lienMAJgrp;
						$actsMAJgrpTot .= $actsMAJgrp;
					}else{
						$textAff .= "<center><img alt='Embargo' title='Modification impossible : dépôt sous embargo' style='width: 50px;' src='./img/addEmbargo_grand.png'></center>";
					}
				}else{
					$textAff .= "<center><img title=\"La(les) modification(s) n'est(ne sont) pas envisageables car une ou plusieurs métadonnées a(ont) été modifiée(s) depuis moins d'une semaine : ".$raisons."\" style='width: 50px;' src='./img/addOK_grand.png'></center>";
				}
				$textAff .= "</td></tr>";
				$lignAff = "ok";
			}else{
				$textAff .= "<td><img alt='Done' title='Ok' src='./img/done.png'></td></tr>";
			}
		}else{
			$textAff .= "<td><img alt='Erreur XML' title='Erreur dans le XML' src='./img/xmlpasok.png'></td></tr>";
			$lignAff = "ok";
		}
		if ($lignAff == "ok") {//Il y a au moins une correction à apporter > la ligne est à afficher
			echo $textAff;
			$cptAff++;
		}
		//$cpt++;
	//}
}
echo "</tbody></table><br>";
echo "<script>";
echo "  document.getElementById('cpt').style.display = \"none\";";
echo "</script>";

//Modification automatisée
$actionMA = "onclick='";
//if ($lienMAJgrpTot != "" && $increment == 10) {
if ($lienMAJgrpTot != "") {
	if (strpos($lienMAJgrpTot, "A_exclure:") !== false) {//Suppression des IdHAL pour lesquels la modification automatisée ne doit pas être appliquée
		$tabHalId = explode("~", $lienMAJgrpTot);
		$tabActId = explode("~", $actsMAJgrpTot);
		for ($i=0; $i<count($tabHalId); $i++) {
			if (strpos($tabHalId[$i], "A_exclure:") !== false) {
				$halId = str_replace("A_exclure:", "", $tabHalId[$i]);
				$lienMAJgrpTot = str_replace(array("~A_exclure:".$halId, "~".$halId), "", $lienMAJgrpTot);
				$tabActId[$i] = "";
			}
		}
		$actsMAJgrpTot = "";
		for ($i=0; $i<count($tabActId); $i++) {
			$actsMAJgrpTot .= $tabActId[$i];
		}
	}
	$lienMAJgrpTot = substr($lienMAJgrpTot, 1, strlen($lienMAJgrpTot));
	$actsMAJgrpTot = substr($actsMAJgrpTot, 1, strlen($actsMAJgrpTot));
	$tabHalId = explode("~", $lienMAJgrpTot);
	$tabActId = explode("~", $actsMAJgrpTot);
	$lienMAJgrpTot = "";
	$actsMAJgrpTot = "";
	$k = 0;
	for ($i=0; $i<count($tabHalId); $i++) {
		if ($lienMAJgrpTot == "" || strpos($lienMAJgrpTot, $tabHalId[$i]) === false) {
			$lienMAJgrpTot .= "#".$tabHalId[$i];
			$actsMAJgrpTot = substr($actsMAJgrpTot, 0, (strlen($actsMAJgrpTot) - 1));
			$actsMAJgrpTot .= "#".$tabActId[$k]."~";
			$k++;
		}else{
			$actsMAJgrpTot .= $tabActId[$k]."~";
			$k++;
		}
	}
	$lienMAJgrpTot = substr($lienMAJgrpTot, 1, strlen($lienMAJgrpTot));
	$actsMAJgrpTot = substr($actsMAJgrpTot, 1, (strlen($actsMAJgrpTot) - 2));
	echo ('Mettre à jour toutes les notices identifiées : ');
	//echo $lienMAJgrpTot."<br>";
	//echo $actsMAJgrpTot."<br>";
	$tabHalId = explode("#", $lienMAJgrpTot);
	$tabActId = explode("#", $actsMAJgrpTot);
	for ($i=0; $i<count($tabHalId); $i++) {
		$actionMA .= 'window.open("./CrossHAL_Modif.php?action=MAJ&etp=1&Id='.$tabHalId[$i].'"); ';
		$actionMA .= 'majok("'.$tabHalId[$i].'"); ';
	}
	$actionMA .= '$.post("CrossHAL_liste_actions.php", { halID: "'.$lienMAJgrpTot.'", action: "'.$actsMAJgrpTot.'" }); ';
	//$actionMA .= "'";
	$actionMA .= 'document.getElementById("actionMA").innerHTML = "<img src=./img/addOK.png>";';
	$actionMA .= "'";
	//echo $actionMA;
	echo ("<span id='actionMA'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png' style='cursor:hand;' ".$actionMA."></span><br>");
}

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
	echo "<input type='hidden' value='".$doiOA."' name='doiOA'>";
	echo "<input type='hidden' value='".$revOA."' name='revOA'>";
	echo "<input type='hidden' value='".$vnpOA."' name='vnpOA'>";
	echo "<input type='hidden' value='".$lanOA."' name='lanOA'>";
	echo "<input type='hidden' value='".$finOA."' name='finOA'>";
	echo "<input type='hidden' value='".$anrOA."' name='anrOA'>";
	//echo "<input type='hidden' value='".$melOA."' name='melOA'>";
	echo "<input type='hidden' value='".$doiCrossRef."' name='doiCrossRef'>";
	echo "<input type='hidden' value='".$revue."' name='revue'>";
	echo "<input type='hidden' value='".$vnp."' name='vnp'>";
	echo "<input type='hidden' value='".$lanCrossRef."' name='lanCrossRef'>";
	echo "<input type='hidden' value='".$financement."' name='financement'>";
	echo "<input type='hidden' value='".$anr."' name='anr'>";
	echo "<input type='hidden' value='".$anneepub."' name='anneepub'>";
	echo "<input type='hidden' value='".$mel."' name='mel'>";
	//echo "<input type='hidden' value='".$mocCrossRef."' name='mocCrossRef'>";
	echo "<input type='hidden' value='".$ccTitconf."' name='ccTitconf'>";
	echo "<input type='hidden' value='".$ccPays."' name='ccPays'>";
	echo "<input type='hidden' value='".$ccDatedeb."' name='ccDatedeb'>";
	echo "<input type='hidden' value='".$ccDatefin."' name='ccDatefin'>";
	echo "<input type='hidden' value='".$ccISBN."' name='ccISBN'>";
	echo "<input type='hidden' value='".$ccTitchap."' name='ccTitchap'>";
	echo "<input type='hidden' value='".$ccTitlivr."' name='ccTitlivr'>";
	echo "<input type='hidden' value='".$ccEditcom."' name='ccEditcom'>";
	echo "<input type='hidden' value='".$absPubmed."' name='absPubmed'>";
	echo "<input type='hidden' value='".$lanPubmed."' name='lanPubmed'>";
	echo "<input type='hidden' value='".$mocPubmed."' name='mocPubmed'>";
	echo "<input type='hidden' value='".$pmid."' name='pmid'>";
	echo "<input type='hidden' value='".$pmcid."' name='pmcid'>";
	echo "<input type='hidden' value='".$absISTEX."' name='absISTEX'>";
	echo "<input type='hidden' value='".$lanISTEX."' name='lanISTEX'>";
	echo "<input type='hidden' value='".$mocISTEX."' name='mocISTEX'>";
	echo "<input type='hidden' value='".$DOIComm."' name='DOIComm'>";
	echo "<input type='hidden' value='".$PoPeer."' name='PoPeer'>";
	echo "<input type='hidden' value='".$manuaut."' name='manuaut'>";
	echo "<input type='hidden' value='".$manuautOH."' name='manuautOH'>";
	echo "<input type='hidden' value='".$manuautNR."' name='manuautNR'>";
	echo "<input type='hidden' value='".$lienext."' name='lienext'>";
	echo "<input type='hidden' value='".$noliene."' name='noliene'>";
	echo "<input type='hidden' value='".$embargo."' name='embargo'>";
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
	echo "<input type='hidden' value='".$doiOA."' name='doiOA'>";
	echo "<input type='hidden' value='".$revOA."' name='revOA'>";
	echo "<input type='hidden' value='".$vnpOA."' name='vnpOA'>";
	echo "<input type='hidden' value='".$lanOA."' name='lanOA'>";
	echo "<input type='hidden' value='".$finOA."' name='finOA'>";
	echo "<input type='hidden' value='".$anrOA."' name='anrOA'>";
	//echo "<input type='hidden' value='".$melOA."' name='melOA'>";
	echo "<input type='hidden' value='".$doiCrossRef."' name='doiCrossRef'>";
	echo "<input type='hidden' value='".$revue."' name='revue'>";
	echo "<input type='hidden' value='".$vnp."' name='vnp'>";
	echo "<input type='hidden' value='".$lanCrossRef."' name='lanCrossRef'>";
	echo "<input type='hidden' value='".$financement."' name='financement'>";
	echo "<input type='hidden' value='".$anr."' name='anr'>";
	echo "<input type='hidden' value='".$anneepub."' name='anneepub'>";
	echo "<input type='hidden' value='".$mel."' name='mel'>";
	//echo "<input type='hidden' value='".$mocCrossRef."' name='mocCrossRef'>";
	echo "<input type='hidden' value='".$ccTitconf."' name='ccTitconf'>";
	echo "<input type='hidden' value='".$ccPays."' name='ccPays'>";
	echo "<input type='hidden' value='".$ccDatedeb."' name='ccDatedeb'>";
	echo "<input type='hidden' value='".$ccDatefin."' name='ccDatefin'>";
	echo "<input type='hidden' value='".$ccISBN."' name='ccISBN'>";
	echo "<input type='hidden' value='".$ccTitchap."' name='ccTitchap'>";
	echo "<input type='hidden' value='".$ccTitlivr."' name='ccTitlivr'>";
	echo "<input type='hidden' value='".$ccEditcom."' name='ccEditcom'>";
	echo "<input type='hidden' value='".$absPubmed."' name='absPubmed'>";
	echo "<input type='hidden' value='".$lanPubmed."' name='lanPubmed'>";
	echo "<input type='hidden' value='".$mocPubmed."' name='mocPubmed'>";
	echo "<input type='hidden' value='".$pmid."' name='pmid'>";
	echo "<input type='hidden' value='".$pmcid."' name='pmcid'>";
	echo "<input type='hidden' value='".$absISTEX."' name='absISTEX'>";
	echo "<input type='hidden' value='".$lanISTEX."' name='lanISTEX'>";
	echo "<input type='hidden' value='".$mocISTEX."' name='mocISTEX'>";
	echo "<input type='hidden' value='".$DOIComm."' name='DOIComm'>";
	echo "<input type='hidden' value='".$PoPeer."' name='PoPeer'>";
	echo "<input type='hidden' value='".$manuaut."' name='manuaut'>";
	echo "<input type='hidden' value='".$manuautOH."' name='manuautOH'>";
	echo "<input type='hidden' value='".$manuautNR."' name='manuautNR'>";
	echo "<input type='hidden' value='".$lienext."' name='lienext'>";
	echo "<input type='hidden' value='".$noliene."' name='noliene'>";
	echo "<input type='hidden' value='".$embargo."' name='embargo'>";
	echo "<input type='hidden' value='Valider' name='valider'>";
	if ($iMaxRet != 0) {
		echo "<input type='submit' class='btn btn-md btn-primary btn-sm' value='Retour' name='retour'>";
	}
}
if ($cptAff == 0 && $cpt != $iMax) {//Auto-soumission du formulaire si ce n'est pas la dernière notice à avoir été traitée
	echo "<script>";
	echo "  document.getElementById(\"etape1\").submit(); ";
	echo "</script>";
}
//Fin étape 1a
?>