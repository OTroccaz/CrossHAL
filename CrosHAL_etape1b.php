<?php
//Etape 1b sur les conférences et chapitres
if($ordinv == "oui") {$sort = "desc";}else{$sort = "asc";}
$urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(%22COMM%22%20OR%20%22COUV%22)&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,conferenceTitle_s,city_s,conferenceStartDateY_i,conferenceEndDateY_i,isbn_s,bookTitle_s,publisher_s,docType_s,label_xml&sort=halId_s%20".$sort;
//echo $urlHAL.'<br>';
askCurl($urlHAL, $arrayHAL);
$numFound = $arrayHAL["response"]["numFound"];
if ($numFound == 0) {die ('<strong>Aucune référence</strong><br><br>');}
if ($iMax > $numFound) {$iMax = $numFound;}
echo '<strong>Total de '.$numFound.' référence(s)' ;
if ($numFound != 0) {
	 if ($numFound != 0) {echo " : affichage de ".$iMin." à ".$iMax."</strong>&nbsp;<em>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</em><br><br>";}
}
echo "<div id='cpt'></div>";
echo "<table class='table table-striped table-bordered table-hover;'><tr>";
//echo "<table style='border-collapse: collapse; width: 100%' border='1px' bordercolor='#999999' cellpadding='5px' cellspacing='5px'><tr>";
echo "<td rowspan='2' bordercolor='#808080' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>ID</strong></td>";
echo "<td colspan='3' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Liens</strong></td>";
if ($apa == "oui") {
	echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>AP</strong></td>";
}
if ($ccTitconf == "oui") {
	echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Titre de la conférence</strong></td>" ;
}
if ($ccPays == "oui") {
	echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Pays</strong></td>" ;
}
if ($ccDatedeb == "oui") {
	echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Date début</strong></td>" ;
}
if ($ccDatefin == "oui") {
	echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Date fin</strong></td>" ;
}
if ($ccISBN == "oui") {
	echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>ISBN</strong></td>" ;
}
if ($ccTitchap == "oui") {
	echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Titre chapitre</strong></td>" ;
}
if ($ccTitlivr == "oui") {
	echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Titre livre</strong></td>" ;
}
if ($ccEditcom == "oui") {
	echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Editeur commercial</strong></td>" ;
}
echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Action</strong></td>";
echo "</tr><tr>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>DOI</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
if ($ccTitconf == "oui") {
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
}
if ($ccPays == "oui") {
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
}
if ($ccDatedeb == "oui") {
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
}
if ($ccDatefin == "oui") {
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
}
if ($ccISBN == "oui") {
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
}
if ($ccTitchap == "oui") {
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
}
if ($ccTitlivr == "oui") {
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
}
if ($ccEditcom == "oui") {
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
	echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
}
echo "</tr>";
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
	$lienCR = "";//Lien renvoyant vers la notice CR
	$bapa = false;//Booléen HAL (true/false) précisant si c'est une notice à paraître > inPress_bool
	if (isset($arrayHAL["response"]["docs"][$cpt]["inPress_bool"])) {$bapa = $arrayHAL["response"]["docs"][$cpt]["inPress_bool"];}
	unset($arrayCR);//Tableau de métadonnées CR
	$titConfCR = "";//Titre de la conférence CR
	$paysCR = "";//Pays CR
	$dateDebCR = "";//Date début CR
	$dateFinCR = "";//Date fin CR
	$ISBNCR = "";//ISBN CR
	$titChapCR = "";//Titre chapitre CR
	$titLivrCR = "";//Titre livre CR
	$editComCR = "";//Editeur commercial CR
	$titConfHAL = "";//Titre de la conférence HAL
	$paysHAL = "";//Pays HAL
	$dateDebHAL = "";//Date début HAL
	$dateFinHAL = "";//Date fin HAL
	$ISBNHAL = "";//ISBN HAL
	$titChapHAL = "";//Titre chapitre HAL
	$titLivrHAL = "";//Titre livre HAL
	$editComHAL = "";//Editeur commercial HAL
	if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "COMM") {
		$titre = $arrayHAL["response"]["docs"][$cpt]["conferenceTitle_s"];
	}else{
		$titre = $arrayHAL["response"]["docs"][$cpt]["title_s"];
	}
	if (isset($arrayHAL["response"]["docs"][$cpt]["halId_s"])) {
		$lienHAL = "<a target='_blank' href='".$racine.$arrayHAL["response"]["docs"][$cpt]["halId_s"]."'><img alt='HAL' src='./img/HAL.jpg'></a>";
		$halID = $arrayHAL["response"]["docs"][$cpt]["halId_s"];
	}
	if (isset($arrayHAL["response"]["docs"][$cpt]["doiId_s"])) {
		$doi = $arrayHAL["response"]["docs"][$cpt]["doiId_s"];
		$lienDOI = "<a target='_blank' href='https://doi.org/".$doi."'><img alt='DOI' src='./img/doi.jpg'></a>";
		
		//Test DOI Crossref
		$prenomCR = "";
		$nomCR = "";
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
		
	}else{//Pas de DOI trouvé dans HAL > on va essayer de le retrouver grâce au titre et l'API CR
		$doiHAL = "inconnu";
		if (isset($doiCrossRef) && $doiCrossRef == "oui") {
			$titreTest = $arrayHAL["response"]["docs"][$cpt]["title_s"][0];
			$urlCR = "https://api.crossref.org/works?query.title=".urlencode($titreTest);
			//echo urlencode($titreTest);
			if (@file_get_contents($urlCR)) {
				askCurl($urlCR, $arrayCR);
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
			}
		}
	}
	$cptTab = $cpt + 1;
	$textAff .= "<td style='text-align: center;'>".$cptTab."</td>";
	$textAff .= "<td style='text-align: center;'>".$lienDOI."</td>";
	$textAff .= "<td style='text-align: center;'>".$lienHAL."</td>";
	$textAff .= "<td style='text-align: center;'>".$lienCR."</td>";
	if ($apa == "oui") {
		if ($bapa) {
			$textAff .= "<td style='text-align: center;'>AP</td>";
		}else{
			$textAff .= "<td style='text-align: center;'>&nbsp;</td>";
		}
	}

	//Titre de la conférence
	if ($ccTitconf == "oui") {
		if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "COMM") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["conferenceTitle_s"]) && $arrayHAL["response"]["docs"][$cpt]["conferenceTitle_s"] != "" ) {
				$titConfHAL = $arrayHAL["response"]["docs"][$cpt]["conferenceTitle_s"];
			}
			if (isset($arrayCR["message"]["name"]) && isset($doi) && $doi != "") {
				$titConfCR = $arrayCR["message"]["name"];
			}
		}
		$textAff .= "<td style='text-align: center;'>".$titConfHAL."</td><td style='text-align: center; background-color: #eeeeee;'>".$titConfCR."</td>";
	}
	
	//Pays
	if ($ccPays == "oui") {
		if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "COMM") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["city_s"]) && $arrayHAL["response"]["docs"][$cpt]["city_s"] != "" ) {
				$paysHAL = $arrayHAL["response"]["docs"][$cpt]["city_s"];
			}
			if (isset($arrayCR["message"]["location"]) && isset($doi) && $doi != "") {
				$paysCR = $arrayCR["message"]["location"];
			}
		}
		$textAff .= "<td style='text-align: center;'>".$paysHAL."</td><td style='text-align: center; background-color: #eeeeee;'>".$paysCR."</td>";
	}
	
	//Date début
	if ($ccDatedeb == "oui") {
		if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "COMM") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["conferenceStartDateY_i"]) && $arrayHAL["response"]["docs"][$cpt]["conferenceStartDateY_i"] != "" ) {
				$dateDebHAL = $arrayHAL["response"]["docs"][$cpt]["conferenceStartDateY_i"];
			}
			if (isset($arrayCR["message"]["start"]) && isset($doi) && $doi != "") {
				$dateDebCR = $arrayCR["message"]["start"];
			}
		}
		$textAff .= "<td style='text-align: center;'>".$dateDebHAL."</td><td style='text-align: center; background-color: #eeeeee;'>".$dateDebCR."</td>";
	}
	
	//Date fin
	if ($ccDatefin == "oui") {
		if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "COMM") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["conferenceEndDateY_i"]) && $arrayHAL["response"]["docs"][$cpt]["conferenceEndDateY_i"] != "" ) {
				$dateFinHAL = $arrayHAL["response"]["docs"][$cpt]["conferenceEndDateY_i"];
			}
			if (isset($arrayCR["message"]["end"]) && isset($doi) && $doi != "") {
				$dateFinCR = $arrayCR["message"]["end"];
			}
		}
		$textAff .= "<td style='text-align: center;'>".$dateFinHAL."</td><td style='text-align: center; background-color: #eeeeee;'>".$dateFinCR."</td>";
	}
	
	//ISBN
	if ($ccISBN == "oui") {
		if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "COMM") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["isbn_s"]) && $arrayHAL["response"]["docs"][$cpt]["isbn_s"] != "" ) {
				$ISBNHAL = $arrayHAL["response"]["docs"][$cpt]["isbn_s"];
			}
			if (isset($arrayCR["message"]["ISBN"][0]) && isset($doi) && $doi != "") {
				$ISBNCR = $arrayCR["message"]["ISBN"][0];
			}
		}
		$textAff .= "<td style='text-align: center;'>".$ISBNHAL."</td><td style='text-align: center; background-color: #eeeeee;'>".$ISBNCR."</td>";
	}
	
	//Titre chapitre
	if ($ccTitchap == "oui") {
		if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "COUV") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["title_s"][0]) && $arrayHAL["response"]["docs"][$cpt]["title_s"][0] != "" ) {
				$titChapHAL = $arrayHAL["response"]["docs"][$cpt]["title_s"][0];
			}
			if (isset($arrayCR["message"]["title"][0]) && isset($doi) && $doi != "") {
				$titChapCR = $arrayCR["message"]["title"][0];
			}
		}
		$textAff .= "<td style='text-align: center;'>".$titChapHAL."</td><td style='text-align: center; background-color: #eeeeee;'>".$titChapCR."</td>";
	}
	
	//Titre livre
	if ($ccTitlivr == "oui") {
		if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "COUV") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["bookTitle_s"]) && $arrayHAL["response"]["docs"][$cpt]["bookTitle_s"] != "" ) {
				$titLivrHAL = $arrayHAL["response"]["docs"][$cpt]["bookTitle_s"];
			}
			if (isset($arrayCR["message"]["container-title"][0]) && isset($doi) && $doi != "") {
				$titLivrCR = $arrayCR["message"]["container-title"][0];
			}
		}
		$textAff .= "<td style='text-align: center;'>".$titLivrHAL."</td><td style='text-align: center; background-color: #eeeeee;'>".$titLivrCR."</td>";
	}
	
	//Editeur commercial
	if ($ccEditcom == "oui") {
		if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] == "COUV") {
			if (isset($arrayHAL["response"]["docs"][$cpt]["publisher_s"][0]) && $arrayHAL["response"]["docs"][$cpt]["publisher_s"][0] != "" ) {
				$editComHAL = $arrayHAL["response"]["docs"][$cpt]["publisher_s"][0];
			}
			if (isset($arrayCR["message"]["publisher"]) && isset($doi) && $doi != "") {
				//var_dump($arrayCR["message"]);
				$editComCR = $arrayCR["message"]["publisher"];
			}
		}
		$textAff .= "<td style='text-align: center;'>".$editComHAL."</td><td style='text-align: center; background-color: #eeeeee;'>".$editComCR."</td>";;
	}
	
	//Actions
	$lienMAJ = "";
	$actsMAJ = "";
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
	
	//suppression noeud <teiHeader>
	$elts = $xml->documentElement;
	if (is_object($elts->getElementsByTagName("teiHeader")->item(0))) {
		$elt = $elts->getElementsByTagName("teiHeader")->item(0);
		$newXml = $elts->removeChild($elt);
	}
	
	//suppression éventuel attribut 'corresp' pour le noeud <idno type="stamp" n="xxx" corresp="yyy">
	if (is_object($xml->getElementsByTagName("idno"))) {
		$elts = $xml->getElementsByTagName("idno");
		$nbelt = $elts->length;
		for ($pos = $nbelt; --$pos >= 0;) {
			$elt = $elts->item($pos);
			if ($elt && $elt->hasAttribute("type")) {
				$quoi = $elt->getAttribute("type");
				if ($quoi == "stamp") {
					if ($elt->hasAttribute("corresp")) {$elt->removeAttribute("corresp");}
					//$xml->save($nomfic);
				}
			}
		}
	}
	
	//suppression éventuel noeud <listBibl type="references">
	if (is_object($xml->getElementsByTagName("listBibl"))) {
		$elts = $xml->getElementsByTagName("listBibl");
		foreach($elts as $elt) {
			if ($elt->hasAttribute("type")) {
				$quoi = $elt->getAttribute("type");
				if ($quoi == "references") {
					$parent = $elt->parentNode; 
					$newXml = $parent->removeChild($elt);
				}
			}
		}
	}
	
	//Correction éventuelle de l'ordre des noeuds idno/orgName et affiliation pour les auteurs
	$auts = $xml->getElementsByTagName("author");
	foreach($auts as $aut) {
		$tabAffil = array();
		$tabOrg = array();
		foreach($aut->childNodes as $elt) {
			if($elt->nodeName == "orgName") {
				//Enregistrement de l'organisme
				$tabOrg[] = $elt;
			}
			if($elt->nodeName == "affiliation") {
				//Enregistrement de l'affiliation
				$tabAffil[] = $elt;
			}
		}
		//Suppression des organismes
		foreach($tabOrg as $org){ 
			$aut->removeChild($org);
		}
		//Suppression des affiliations
		foreach($tabAffil as $aff){ 
			$aut->removeChild($aff);
		}
		//Ajout des organismes à la fin des noeuds
		foreach($tabOrg as $org){ 
			$aut->appendChild($org);
		}
		//Ajout des affiliations à la fin des noeuds
		foreach($tabAffil as $aff) {
			$aut->appendChild($aff);																		
		}
	}
	
	//Transformation des classCode VOCINRA en mots-clés
	$tabClas = array();
	$tabKeyw = array();
	$keys = array();
	$clas = $xml->getElementsByTagName("classCode");
	//Enregistrement des classCode
	foreach($clas as $cla) {
		if($cla->hasAttribute("scheme") && $cla->getAttribute("scheme") == "VOCINRA") {
			$tabKeyw[] = $cla->getAttribute("n");
			$tabClas[] = $cla;
		}
	}
	
	//Suppression des classCode
	foreach($tabClas as $cla) {
		$cla->parentNode->removeChild($cla);
	}
	//Ajout des classCode aux mots-clés
	$keys = $xml->getElementsByTagName("keywords");
	$langKeyw = "en";//Anglais par défaut
	//Récupération de la langue par défaut déjà présente pour les autres mots-clés
	foreach($keys as $key) {
		foreach($key->childNodes as $elt) {
			if($elt->hasAttribute("xml:lang")) {$langKeyw = $elt->getAttribute("xml:lang");}
		}
	}
	//Si présence d'une lettre accentuée pour un des mots-clés, c'est certainement du français
	foreach($tabKeyw as $keyw) {
		if (!ctype_alnum($keyw)) {
			$langKeyw = "fr";
			break;
		}
	}
	
	//Y-a-t-il déjà des mots-clés ?
	if ($keys->length != 0) {//Oui > on ajoute les nouveaux à la suite
		foreach($tabKeyw as $keyw){
			$bimoc = $xml->createElement("term");
			$moc = $xml->createTextNode($keyw);
			$bimoc->setAttribute("xml:lang", $langKeyw);
			$bimoc->appendChild($moc);
			$key->appendChild($bimoc);																		
		}
	}else{//Non > il faut créer le noeud 'keywords' s'il y a réellement de nouveaux mots-clés à ajouter
		if (!empty($tabKeyw)) {
			$tabClasN = array();
			$clas = $xml->getElementsByTagName("classCode");
			$txtC = $xml->getElementsByTagName("textClass");
			foreach($clas as $cla) {
				$tabClasN[] = $cla;
			}
			//Suppression des classCode
			foreach($tabClasN as $cla) {
				$cla->parentNode->removeChild($cla);
			}
			//Création du noeud 'keywords'
			$bimoc = $xml->createElement("keywords");
			$bimoc->setAttribute("scheme", "author");
			$txtC->item(0)->appendChild($bimoc);
			//Ajout des mots-clés
			$keys = $xml->getElementsByTagName("keywords");
			foreach($tabKeyw as $keyw){
				$bimoc = $xml->createElement("term");
				$moc = $xml->createTextNode($keyw);
				$bimoc->setAttribute("xml:lang", $langKeyw);
				$bimoc->appendChild($moc);
				$keys->item(0)->appendChild($bimoc);																		
			}
			
			//Rajout des classCode
			foreach($tabClasN as $cla) {
				$txtC->item(0)->appendChild($cla);
			}
		}
	}
	
	//MAJ titre de la conférence
	if ($ccTitconf == "oui" && $titConfCR != "" && $titConfCR != $titConfHAL) {
		insertNode($xml, $titConfCR, "meeting", "date", "title", "", "", "", "", "iB");
		$xml->save($Fnm);
		$lienMAJ = "./CrosHALModif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
		include "./CrosHAL_actions.php";
		$testMaj = "ok";
		$lignAff = "ok";
		foreach($ACTIONS_LISTE as $tab) {
			if (in_array($halID, $tab) && in_array("MAJ_TCO",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "titre conf, ";}
		}
		if ($testMaj == "ok") {$actsMAJ .= "MAJ_TCO~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"];}
	}

	//MAJ date début
	if ($ccDatedeb == "oui" && $dateDebCR != "" && $dateDebCR != $dateDebHAL) {
		insertNode($xml, $dateDebCR, "monogr", "meeting", "date", "type", "start", "", "", "aC");
		$xml->save($Fnm);
		$lienMAJ = "./CrosHALModif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
		include "./CrosHAL_actions.php";
		$testMaj = "ok";
		$lignAff = "ok";
		foreach($ACTIONS_LISTE as $tab) {
			if (in_array($halID, $tab) && in_array("MAJ_DDB",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "date début, ";}
		}
		if ($testMaj == "ok") {$actsMAJ .= "MAJ_DDB~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"];}
	}
	
	//MAJ date fin
	if ($ccDatefin == "oui" && $dateFinCR != "" && $dateFinCR != $dateFinHAL) {
		insertNode($xml, $dateFinCR, "monogr", "meeting", "date", "type", "start", "", "", "aC");
		$xml->save($Fnm);
		$lienMAJ = "./CrosHALModif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
		include "./CrosHAL_actions.php";
		$testMaj = "ok";
		$lignAff = "ok";
		foreach($ACTIONS_LISTE as $tab) {
			if (in_array($halID, $tab) && in_array("MAJ_DFN",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "date fin, ";}
		}
		if ($testMaj == "ok") {$actsMAJ .= "MAJ_DFN~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"];}
	}
	
	//MAJ ISBN
	if ($ccISBN == "oui" && $ISBNCR != "" && $ISBNCR != $ISBNHAL) {
		insertNode($xml, $ISBNCR, "monogr", "meeting", "idno", "type", "isbn", "", "", "iB");
		$xml->save($Fnm);
		$lienMAJ = "./CrosHALModif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
		include "./CrosHAL_actions.php";
		$testMaj = "ok";
		$lignAff = "ok";
		foreach($ACTIONS_LISTE as $tab) {
			if (in_array($halID, $tab) && in_array("MAJ_ISB",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "ISBN, ";}
		}
		if ($testMaj == "ok") {$actsMAJ .= "MAJ_ISB~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"];}
	}

	//MAJ titre du chapitre
	if ($ccTitchap == "oui" && $titChapCR != "" && $titChapCR != $titChapHAL) {
		insertNode($xml, $titChapCR, "titleStmt", "author", "title", "xml:lang", "en", "", "", "iB");
		$xml->save($Fnm);
		$lienMAJ = "./CrosHALModif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
		include "./CrosHAL_actions.php";
		$testMaj = "ok";
		$lignAff = "ok";
		foreach($ACTIONS_LISTE as $tab) {
			if (in_array($halID, $tab) && in_array("MAJ_TCH",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "titre chapitre, ";}
		}
		if ($testMaj == "ok") {$actsMAJ .= "MAJ_TCH~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"];}
	}

	//MAJ titre du livre
	if ($ccTitlivr == "oui" && $titLivrCR != "" && $titLivrCR != $titLivrHAL) {
		insertNode($xml, $titLivrCR, "monogr", "imprint", "title", "level", "m", "", "", "iB");
		$xml->save($Fnm);
		$lienMAJ = "./CrosHALModif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
		include "./CrosHAL_actions.php";
		$testMaj = "ok";
		$lignAff = "ok";
		foreach($ACTIONS_LISTE as $tab) {
			if (in_array($halID, $tab) && in_array("MAJ_TLI",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "titre livre, ";}
		}
		if ($testMaj == "ok") {$actsMAJ .= "MAJ_TLI~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"];}
	}

	//MAJ éditeur commercial
	if ($ccEditcom == "oui" && $editComCR != "" && $editComCR != $editComHAL) {
		insertNode($xml, $editComCR, "imprint", "date", "publisher", "", "", "", "", "aC");
		$xml->save($Fnm);
		$lienMAJ = "./CrosHALModif.php?action=MAJ&etp=1&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
		include "./CrosHAL_actions.php";
		$testMaj = "ok";
		$lignAff = "ok";
		foreach($ACTIONS_LISTE as $tab) {
			if (in_array($halID, $tab) && in_array("MAJ_ECO",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "éditeur commercial, ";}
		}
		if ($testMaj == "ok") {$actsMAJ .= "MAJ_ECO~"; $lienMAJgrp .= "~".$arrayHAL["response"]["docs"][$cpt]["halId_s"];}
	}
	
	if ($colact == "ok") {
		if ($lienMAJ != "") {
			$textAff .= "<td style='text-align: center;'>";
			if ($actMaj == "ok") {
				$textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienMAJ."' onclick='$.post(\"CrosHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"".$actsMAJ."\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' src='./img/MAJ.png'></a></span></center>";
			}else{
				$textAff .= "<center><img title=\"La(les) modification(s) n'est(ne sont) pas envisageables car une ou plusieurs métadonnées a(ont) été modifiée(s) depuis moins d'une semaine : ".$raisons."\" src='./img/MAJOK.png'></center>";
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
		
	if ($lignAff == "ok") {//Il y a au moins une correction à apporter > la ligne est à afficher	
		echo $textAff;
		$cptAff++;
	}
}
echo "</table><br>";
echo "<script>";
echo "  document.getElementById('cpt').style.display = \"none\";";
echo "</script>";

if ($iMax != $numFound) {
	echo "<form name='troli' id='etape1' action='CrosHAL.php' method='post'>";
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
		echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>&nbsp;&nbsp;&nbsp;";
	}
	echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Suite' style='width: 70px;' name='suite'>";
	echo "</form><br>";
}else{
	echo "<form name='troli' id='etape1' action='CrosHAL.php' method='post'>";
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
		echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>";
	}
}
/*
if ($cptAff == 0) {//Auto-soumission du formulaire
	echo "<script>";
	echo "  document.getElementById(\"etape1\").submit(); ";
	echo "</script>";
}
*/
//Fin étape 1b
?>