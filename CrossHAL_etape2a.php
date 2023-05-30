<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Etape 2a - Stage 2a
 */
 
//Etape 2a > Corriger ordre des auteurs et remplacer l'initiale du premier prénom par son écriture complète
$iMinTab = $iMin - 1;
for($cpt = $iMinTab; $cpt < $iMax; $cpt++) {
	progression($cpt+1, $iMax, $iPro);
	$lignAff = "no";//Test affichage ou non de la ligne du tableau
	$textAff = "";//Texte de la ligne du tableau
	$doi = "";//DOI de la notice
	$halID = "";//halId de la notice
	$lienHAL = "";//Lien renvoyant vers la notice HAL
	$autHAL = "";//$lim premiers auteurs HAL
	$prenomHAL = "";//Prénom du 1er auteur HAL
	$prenomsHAL = "";//Liste des prénoms des auteurs HAL séparés par des virgules
	$nomHAL = "";//Nom du premier auteur HAL
	$totAutHAL = "";//Nombre total d'auteurs HAL
	$autHALTot = "";//Liste des noms des auteurs HAL séparés par des virgules
	$lienDOI = "";//Lien renvoyant vers la notice via le DOI
	$bapa = false;//Booléen HAL (true/false) précisant si c'est une notice à paraître > inPress_bool
	if (isset($arrayHAL["response"]["docs"][$cpt]["inPress_bool"])) {$bapa = $arrayHAL["response"]["docs"][$cpt]["inPress_bool"];}
	$lienCR = "";//Lien renvoyant vers la notice CR
	$autCR = "";//$lim premiers auteurs CR
	$prenomCR = "";//Prénom du 1er auteur CR
	$prenomsCR = "";//Liste des prénoms des auteurs CR séparés par des virgules
	$nomCR = "";//Nom du premier auteur CR
	$nomsCR = "";//Liste des noms des auteurs CR séparés par des virgules
	$totAutCR = "";//Nombre total d'auteurs CR
	$autCRTot = "";//Liste des noms des auteurs CR séparés par des virgules
	$cptTab = $cpt + 1;
	if (isset($arrayHAL["response"]["docs"][$cpt]["title_s"][0])) {
		$titre = $arrayHAL["response"]["docs"][$cpt]["title_s"][0];
	}
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
			askCurl($urlCR, $arrayCR);
			$lienCR = "";
		}else{//Problème de DOI
			$rechDOI = "";
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
	}
	if ($lienCR == "" && $doi != "") {$lienCR = "<a target='_blank' href='http://search.crossref.org/?q=".$doi."'><img alt='CrossRef' src='./img/CR.jpg'></a>";}
	$textAff .= "<tr><td>".$cptTab."</td>";
	$textAff .= "<td>".$lienDOI."</td>";
	$textAff .= "<td>".$lienHAL."</td>";
	$textAff .= "<td>".$lienCR."</td>";
	if ($apa == "oui") {
		if ($bapa) {
			$textAff .= "<td>AP</td>";
		}else{
			$textAff .= "<td>&nbsp;</td>";
		}
	}
	if ($ordAut == "oui") {
		$lim = 10;
		//$lim premiers auteurs HAL
		if (isset($arrayHAL["response"]["docs"][$cpt]["doiId_s"])) {$doi = $arrayHAL["response"]["docs"][$cpt]["doiId_s"];}
		$tabAutHAL = $arrayHAL["response"]["docs"][$cpt]["authLastName_s"];
		$tabPreAutHAL = $arrayHAL["response"]["docs"][$cpt]["authFirstName_s"];
		for ($iaut = 0; $iaut < $lim; $iaut++) {
			if (isset($tabAutHAL[$iaut])) {$autHAL .= $tabAutHAL[$iaut].", ";}
		}
		$autHAL = substr($autHAL, 0, strlen($autHAL) - 2);
		//Tous les auteurs HAL
		$autHALTot = "";
		$iautTot = 0;
		while(isset($tabAutHAL[$iautTot])) {
			$prenomsHAL .= $tabPreAutHAL[$iautTot].", ";
			$autHALTot .= $tabAutHAL[$iautTot].", ";
			$iautTot++;
		}
		$prenomsHAL = substr($prenomsHAL, 0, strlen($prenomsHAL) - 2);
		$totAutHAL = count($arrayHAL["response"]["docs"][$cpt]["authLastName_s"]);
		//echo wd_remove_accents($autHALTot)."<br>";
		//$lim premiers auteurs CrossRef
		if ($doi != "") {
			$urlCR = "https://api.crossref.org/v1/works/http://dx.doi.org/".$doi;
			if (@file_get_contents($urlCR)) {
				askCurl($urlCR, $arrayCR);
				for ($iaut = 0; $iaut < $lim; $iaut++) {
					if (isset($arrayCR["message"]["author"][$iaut]["family"])) {
						$autCR .= $arrayCR["message"]["author"][$iaut]["family"].", ";
					}
				}
				$autCR = substr($autCR, 0, strlen($autCR) - 2);
				//Tous les auteurs CrossRef
				$autCRTot = "";
				$iautTot = 0;
				while(isset($arrayCR["message"]["author"][$iautTot]["sequence"])) {
					if (isset($arrayCR["message"]["author"][$iautTot]["given"])) {
						$prenomsCR .= $arrayCR["message"]["author"][$iautTot]["given"].", ";
						$nomsCR .= $arrayCR["message"]["author"][$iautTot]["family"].", ";
						$autCRTot .= $arrayCR["message"]["author"][$iautTot]["family"].", ";
					}
					$iautTot++;
				}
				//echo wd_remove_accents($autCRTot)."<br>";
				$totAutCR = 0;
				$totAutCRcpt = 0;
				while(isset($arrayCR["message"]["author"][$totAutCRcpt]["sequence"])) {
					//Ne pas tenir compte des "auteurs groupe" qui n'ont pas de clé 'family'
					if (!isset($arrayCR["message"]["author"][$totAutCRcpt]["family"])) {
						$totAutCR--;
					}
					$totAutCR++;
					$totAutCRcpt++;
				}
				$prenomsCR = substr($prenomsCR, 0, strlen($prenomsCR) - 2);
				$nomsCR = substr($nomsCR, 0, strlen($nomsCR) - 2);
			}
		}
		$autHALAff = "";
		$autCRAff = "";
		$tabDimHAL = explode(",", $autHAL);
		$tabDimCR = explode(",", $autCR);
		foreach($tabDimHAL as $i => $c) {
			if (isset($tabDimHAL[$i]) && isset($tabDimCR[$i])) {
				if (normalize(strtolower(wd_remove_accents($tabDimHAL[$i]))) != normalize(strtolower(wd_remove_accents($tabDimCR[$i]))) && $doi != "") {
					$autHALAff .= '<font color="red">'.$tabDimHAL[$i].'</font>,';
					$autCRAff .= '<font color="red">'.$tabDimCR[$i].'</font>,';
				}else{
					$autHALAff .= '<font color="black">'.$tabDimHAL[$i].'</font>,';
					$autCRAff .= '<font color="black">'.$tabDimCR[$i].'</font>,';
				}
			}else{
				if (!isset($tabDimHAL[$i]) && isset($tabDimCR[$i])) {
					if ($doi != "") {
						$autCRAff .= '<font color="red">'.$tabDimCR[$i].'</font>,';
					}else{
						$autCRAff .= '<font color="black">'.$tabDimCR[$i].'</font>,';
					}
				}
				if (!isset($tabDimCR[$i]) && isset($tabDimHAL[$i])) {
					if ($doi != "") {
						$autHALAff .= '<font color="red">'.$tabDimHAL[$i].'</font>,';
					}else{
						$autHALAff .= '<font color="black">'.$tabDimHAL[$i].'</font>,';
					}
				}
			}
		}
		$autHALAff = substr($autHALAff, 0, strlen($autHALAff) - 2);
		$autCRAff = substr($autCRAff, 0, strlen($autCRAff) - 2);
		$textAff .= "<td style='text-align: left;'>".$autHALAff."</td>";
		$textAff .= "<td style='text-align: left;'>".$autCRAff."</td>";
		$textAff .= "<td style='text-align: left;'>".$totAutHAL."</td>";
		$textAff .= "<td style='text-align: left;'>".$totAutCR."</td>";
		
		//Actions
		$lienMAJAut = "";
		$tabDocid = explode("-", $arrayHAL["response"]["docs"][$cpt]["halId_s"]);
		$lienMAJAut = "https://hal.archives-ouvertes.fr/submit/update/docid/".$tabDocid[1];
		
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
		$xml->loadXML($teiRes);
		
		corrXML($xml);

		if ($doi != "") {
			//echo normalize(strtolower(wd_remove_accents($autHALTot))).'<br>'.normalize(strtolower(wd_remove_accents($autCRTot))).'<br>';
			if ((normalize(strtolower(wd_remove_accents($autHALTot))) == normalize(strtolower(wd_remove_accents($autCRTot)))) && ($totAutCR == count($arrayHAL["response"]["docs"][$cpt]["authLastName_s"]))) {
			//if (($autHALTot == $autCRTot) && (count($arrayCR["message"]["author"]) == count($arrayHAL["response"]["docs"][$cpt]["authLastName_s"]))) {
				//Tout correspond > ok
				$textAff .= "<td><img alt='Done' src='./img/done.png'></td>";
			}else{
				$lignAff = "ok";
				$textAff .= "<td>";
				//echo "A modifier";
				//if ($lienMAJAut != "") {echo "<span id='maj".$halID."'><a target='_blank' href='".$lienMAJAut."' onclick='majok(\"".$doi."\")'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span>";}
				include "./CrossHAL_actions.php";
				$actMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_AUT",$tab)) {$actMaj = "no";}
				}
				if ($actMaj == "ok") {
					$textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienMAJAut."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_AUT\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span></center>";
				}else{
					$textAff .= "<center><img style='width: 50px;' src='./img/addOK_grand.png'></center>";
				}
				$xml->save($Fnm);
				$textAff .= "</td>";
			}
		}else{
			$textAff .= "<td>&nbsp;</td>";
		}
	}
	
	if ($iniPre == "oui") {
		//Prénom premier auteur HAL
		if ($prenomsHAL == "") {//recherche via API HAL pas encore effectuée
			if (isset($arrayHAL["response"]["docs"][$cpt]["doiId_s"])) {$doi = $arrayHAL["response"]["docs"][$cpt]["doiId_s"];}
			$iHALMax = count($arrayHAL["response"]["docs"][$cpt]["authFirstName_s"]);
			for ($iaut = 0; $iaut < $iHALMax; $iaut++) {
				if (isset($arrayHAL["response"]["docs"][$cpt]["authFirstName_s"][$iaut])) {$prenomsHAL .= $arrayHAL["response"]["docs"][$cpt]["authFirstName_s"][$iaut].", ";}
			}
			$prenomsHAL = substr($prenomsHAL, 0, strlen($prenomsHAL) - 2);
		}
		$textAff .= "<td style='text-align: left;'>".$prenomsHAL."</td>";
		if ($prenomsCR == "") {//recherche via API CR pas encore effectuée
			if ($doi != "") {
				$urlCR = "https://api.crossref.org/v1/works/http://dx.doi.org/".$doi;
				if (@file_get_contents($urlCR)) {
					askCurl($urlCR, $arrayCR);
					if (isset($arrayCR["message"]["author"])) {$lim = count($arrayCR["message"]["author"]);}
					for ($iaut = 0; $iaut < $lim; $iaut++) {
						if (isset($arrayCR["message"]["author"][$iaut]["given"])) {
							$prenomsCR .= $arrayCR["message"]["author"][$iaut]["given"].", ";
							$nomsCR .= $arrayCR["message"]["author"][$iaut]["family"].", ";
						}
					}
					$prenomsCR = substr($prenomsCR, 0, strlen($prenomsCR) - 2);
					$nomsCR = substr($nomsCR, 0, strlen($nomsCR) - 2);
				}
			}
		}
		$textAff .= "<td style='text-align: left;'>".$prenomsCR."</td>";

		//Actions
		$lienMAJPre = "";
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
		$xml->loadXML($teiRes);
		
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
		
		$nbPreHAL = count(explode(",", $prenomsHAL));
		$nbPreCR = count(explode(",", $prenomsCR));
		//if ($prenomsHAL != $prenomsCR && $prenomsCR != "" && strpos($prenomsCR, ".") === false) {
		//echo(normalize(strtolower(wd_remove_accents($prenomsHAL))).'<br>'.normalize(strtolower(wd_remove_accents($prenomsCR))));
		if ($doi != "") {
			if (normalize(strtolower(wd_remove_accents($prenomsHAL))) != normalize(strtolower(wd_remove_accents($prenomsCR))) && $prenomsCR != "" && preg_match("/^[a-zA-Z]+\.|[a-zA-Z], [a-zA-Z]+\./", $prenomsCR) != 1 && $nbPreHAL == $nbPreCR) {
				//Les prénoms sont différents
				//echo "<td>";
				//echo "A modifier";
				$ind = 0;
				$tabPrenomsCR = explode(", ", $prenomsCR);
				$tabNomsCR = explode(", ", $nomsCR);
				
				$elts = $xml->getElementsByTagName("author");
				foreach ($elts as $elt) {
					$modif = "oui";
					$verif = "oui";
					$numAutHAL = "";
					$docid = "";
					$idhali = "";
					$idhals = "";
					if ($elt->hasAttribute("role")) {
						$quoi = $elt->getAttribute("role");
						if ($quoi == "aut") {
							//vérification qu'il n'y ait pas d'idHAL
							foreach($elt->childNodes as $item) {
								if ($item->nodeName == "idno") {
									if ($item->hasAttribute("type")) {
										$quoi = $item->getAttribute("type");
										if ($quoi == "idhal") {
											$modif .= ",non";
										}
									}
								}
							}
							//echo $modif.' - '.$tabPrenomsCR[$ind].'<br>';
							if (strpos($modif, "non") === false) {//potentielle modification à effectuer
								//recherche d'un docid et d'un/des idhal
								$rechAutHAL = "https://api.archives-ouvertes.fr/ref/author/?q=firstName_s:".str_replace(" ", "%20", $tabPrenomsCR[$ind])."%20AND%20lastName_s:".str_replace(" ", "%20", $tabNomsCR[$ind])."&fl=*";
								//echo $rechAutHAL.'<br>';
								askCurl($rechAutHAL, $arrayAutHAL);
								$numAutHAL = $arrayAutHAL["response"]["numFound"];
								if ($numAutHAL != "") {
									$cmpIdhali = "";
									$cmpIdhals = "";
									$tstIdhali = "oui";
									$tstIdhals = "oui";
									$cmpINC = "";
									$cmpOLD = "";
									$numINC = 0;
									//conditions à respecter pour effectuer la modification
									for ($cnd = 0; $cnd < $numAutHAL; $cnd++) {
										//vérification qu'il n'existe une seule forme IdHAL pour cet auteur
										if (isset($arrayAutHAL["response"]["docs"][$cnd]["idHal_i"]) && $arrayAutHAL["response"]["docs"][$cnd]["idHal_i"] != 0) {
											if ($cmpIdhali == "") {
												$cmpIdhali = $arrayAutHAL["response"]["docs"][$cnd]["idHal_i"];
											}else{
												if ($cmpIdhali != $arrayAutHAL["response"]["docs"][$cnd]["idHal_i"]) {
													$verif = "non";
												}
											}
										}else{
											$tstidhali = "non";
										}
										if (isset($arrayAutHAL["response"]["docs"][$cnd]["idHal_s"]) && $arrayAutHAL["response"]["docs"][$cnd]["idHal_s"] != 0) {
											if ($cmpIdhals == "") {
												$cmpIdhals = $arrayAutHAL["response"]["docs"][$cnd]["idHal_s"];
											}else{
												if ($cmpIdhals != $arrayAutHAL["response"]["docs"][$cnd]["idHal_s"]) {
													$verif = "non";
												}
											}
										}else{
											$tstidhals = "non";
										}
									}
									if ($verif == "oui" && ($tstidhali == "oui" || $tstidhals == "oui")) {//idHAL unique > on recherche la forme VALID
										for ($cnd = 0; $cnd < $numAutHAL; $cnd++) {
											if ($arrayAutHAL["response"]["docs"][$cnd]["valid_s"] == "VALID") {
												if (isset($arrayAutHAL["response"]["docs"][$cnd]["docid"])) {$docid = $arrayAutHAL["response"]["docs"][$cnd]["docid"];}
												if (isset($arrayAutHAL["response"]["docs"][$cnd]["idHal_i"])) {$idhali = $arrayAutHAL["response"]["docs"][$cnd]["idHal_i"];}
												if (isset($arrayAutHAL["response"]["docs"][$cnd]["idHal_s"])) {$idhals = $arrayAutHAL["response"]["docs"][$cnd]["idHal_s"];}
											}
										}
									}else{//vérification qu'il n'existe une seule forme INCOMING ou OLD avec prénom complet pour l'auteur
										$INCmail = array();
										$INCdoc = array();
										$INChali = array();
										$INChals = array();
										for ($cnd = 0; $cnd < $numAutHAL; $cnd++) {
											if ($arrayAutHAL["response"]["docs"][$cnd]["valid_s"] == "INCOMING") {
												if ($cmpINC == "") {
													$cmpINC = "oui";
													$numINC++;
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["docid"])) {$docid = $arrayAutHAL["response"]["docs"][$cnd]["docid"]; $INCdoc[$cnd] = $arrayAutHAL["response"]["docs"][$cnd]["docid"];}
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["idHal_i"])) {$idhali = $arrayAutHAL["response"]["docs"][$cnd]["idHal_i"]; $INChali[$cnd] = $arrayAutHAL["response"]["docs"][$cnd]["idHal_i"];}
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["idHal_s"])) {$idhals = $arrayAutHAL["response"]["docs"][$cnd]["idHal_s"]; $INChals[$cnd] = $arrayAutHAL["response"]["docs"][$cnd]["idHal_s"];}
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["email_s"])) {$INCmail[$cnd] = $arrayAutHAL["response"]["docs"][$cnd]["email_s"];}
													//if (isset($arrayAutHAL["response"]["docs"][$cnd]["docid"])) {$INCdoc[$cnd] = $arrayAutHAL["response"]["docs"][$cnd]["docid"];}
												}else{//plusieurs formes INCOMING
													$numINC++;
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["email_s"])) {$INCmail[$cnd] = $arrayAutHAL["response"]["docs"][$cnd]["email_s"];}
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["docid"])) {$INCdoc[$cnd] = $arrayAutHAL["response"]["docs"][$cnd]["docid"];}
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["idhal_i"])) {$INChali[$cnd] = $arrayAutHAL["response"]["docs"][$cnd]["idHal_i"];}
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["idhal_s"])) {$INChals[$cnd] = $arrayAutHAL["response"]["docs"][$cnd]["idHal_s"];}
												}
											}
											if ($arrayAutHAL["response"]["docs"][$cnd]["valid_s"] == "OLD" && $cmpINC == "") {
												if ($cmpOLD == "") {
													$cmpOLD = "oui";
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["docid"])) {$docid = $arrayAutHAL["response"]["docs"][$cnd]["docid"];}
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["idHal_i"])) {$idhali = $arrayAutHAL["response"]["docs"][$cnd]["idHal_i"];}
													if (isset($arrayAutHAL["response"]["docs"][$cnd]["idHal_s"])) {$idhals = $arrayAutHAL["response"]["docs"][$cnd]["idHal_s"];}
												}else{//plusieurs formes OLD
													$docid = "";
													$idhali = "";
													$idhals = "";
													$verif = "non";
												}
											}
										}
									}
									if ($numINC != 0) {//plusieurs formes INCOMING
										$numMail = count($INCmail);
										if ($numINC - $numMail == 1) {//une seule forme INCOMING n'a pas d'adresse mail
											for ($cnd = 0; $cnd < $numINC; $cnd++) {
												if (isset($INCmail[$cnd]) && $INCmail[$cnd] == "") {
													$docid = $INCdoc[$cnd];
													$idhali = $INChali[$cnd];
													$idhals = $INChals[$cnd];
												}
											}
										}else{//abandon choix forme via adresse mail > on récupère celle avec le docid le plus élevé
											$docimax = 0;
											for ($cnd = 0; $cnd < $numINC; $cnd++) {
												if ($INCdoc[$cnd] > $docimax) {
													$docimax = $INCdoc[$cnd];
												}
											}
											$cnd = array_search($docimax, $INCdoc);
											if (isset($INCdoc[$cnd]) && $INCdoc[$cnd] != 0) {$docid = $INCdoc[$cnd];}
											if (isset($INChali[$cnd]) && $INChali[$cnd] != 0) {$docid = $INChali[$cnd];}
											if (isset($INChals[$cnd]) && $INChals[$cnd] != 0) {$docid = $INChals[$cnd];}
										}
									}

								}
								if ($verif == "oui") {//on fait les modifications
									//echo $docid.' - '.$idhali.' - '.$idhals.'<br>';
									if ($docid != "") {
										foreach($elt->childNodes as $item) {
											if ($item->nodeName == "idno") {
												if ($item->hasAttribute("type")) {
													if ($item->getAttribute("type") == "halauthorid") {
														$item->nodeValue = $docid;
													}
												}
											}
										}
									 //insertNode($xml, $docid, "author", "affiliation", "idno", "type", "halauthorid", "", "", "iB");
									}
									if ($idhali != "") {
										$iou = "";
										foreach($elt->childNodes as $item) {
											if ($item->nodeName == "affiliation") {
												$iou = $item;
											}
										}
										$idh = $xml->createElement("idno");
										$idh->setAttribute("type", "idhal");
										$idh->setAttribute("notation", "numeric");
										$cth = $xml->createTextNode($idhali);
										$idh->appendChild($cth);
										if (isset($iou) && $iou != "") {
											$elt->insertBefore($idh, $iou);
										}else{
											$elt->appendChild($idh);
										}
									}
									if ($idhals != "") {
										$iou = "";
										foreach($elt->childNodes as $item) {
											if ($item->nodeName == "affiliation") {
												$iou = $item;
											}
										}
										$idh = $xml->createElement("idno");
										$idh->setAttribute("type", "idhal");
										$idh->setAttribute("notation", "string");
										$cth = $xml->createTextNode($idhals);
										$idh->appendChild($cth);
										if (isset($iou) && $iou != "") {
											$elt->insertBefore($idh, $iou);
										}else{
											$elt->appendChild($idh);
										}
									}
									foreach($elt->childNodes as $item) {
										if ($item->nodeName == "persName") {
											$item->firstChild->nodeValue = $tabPrenomsCR[$ind];
										}
									}
								}
							}
						$ind ++;
						if ($ind == $nbPreCR) {$ind = 0;}
						}
					}
				}
				$xml->save($Fnm);
				$textAff .= "<td>";
				$lienMAJPre = "./CrossHAL_Modif.php?action=MAJ&etp=2&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
				if ($lienMAJPre != "") {
					//echo "<span id='maj".$halID."'><a target='_blank' href='".$lienMAJPre." 'onclick='majok(\"".$doi."\")'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span>";
					include "./CrossHAL_actions.php";
					$actMaj = "ok";
					foreach($ACTIONS_LISTE as $tab) {
						if (in_array($halID, $tab) && in_array("MAJ_PRE",$tab)) {$actMaj = "no";}
					}
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
							$textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienMAJPre."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_PRE\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span></center>";
						}else{
							$textAff .= "<center><img alt='Embargo' title='Modification impossible : dépôt sous embargo' style='width: 50px;' src='./img/addEmbargo_grand.png'></center>";
						}
					}else{
						$textAff .= "<center><img style='width: 50px;' src='./img/addOK_grand.png'></center>";
					}
				}
				$textAff .= "</td>";
				$lignAff = "ok";
			}else{
				$textAff .= "<td><img alt='Done' src='./img/done.png'></td>";
			}
		}else{
			$textAff .= "<td>&nbsp;</td>";
		}
	}
	$textAff .= "</tr>";
	if ($lignAff == "ok") {//Il y a au moins une correction à apporter > la ligne est à afficher
		echo $textAff;
	}
}
//echo "</tr>";
echo "</tbody></table><br>";
echo "<script>";
echo "  document.getElementById('cpt').style.display = \"none\";";
echo "</script>";

if ($iMax != $numFound) {
	echo "<form name='troli' id='etape2' action='CrossHAL.php' method='post'>";
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
	echo "<input type='hidden' value='".$ordAut."' name='ordAut'>";
	echo "<input type='hidden' value='".$iniPre."' name='iniPre'>";
	echo "<input type='hidden' value='".$vIdHAL."' name='vIdHAL'>";
	echo "<input type='hidden' value='".$rIdHAL."' name='rIdHAL'>";
	echo "<input type='hidden' value='".$ctrTrs."' name='ctrTrs'>";
	echo "<input type='hidden' value='".$rIdHALArt."' name='rIdHALArt'>";
	echo "<input type='hidden' value='".$rIdHALCom."' name='rIdHALCom'>";
	echo "<input type='hidden' value='".$rIdHALCou."' name='rIdHALCou'>";
	echo "<input type='hidden' value='".$rIdHALOuv."' name='rIdHALOuv'>";
	echo "<input type='hidden' value='".$rIdHALDou."' name='rIdHALDou'>";
	echo "<input type='hidden' value='".$rIdHALBre."' name='rIdHALBre'>";
	echo "<input type='hidden' value='".$rIdHALRap."' name='rIdHALRap'>";
	echo "<input type='hidden' value='".$rIdHALThe."' name='rIdHALThe'>";
	echo "<input type='hidden' value='".$rIdHALPre."' name='rIdHALPre'>";
	echo "<input type='hidden' value='".$rIdHALPub."' name='rIdHALPub'>";
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
	echo "<form name='troli' id='etape2' action='CrossHAL.php' method='post'>";
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
	echo "<input type='hidden' value='".$ordAut."' name='ordAut'>";
	echo "<input type='hidden' value='".$iniPre."' name='iniPre'>";
	echo "<input type='hidden' value='".$vIdHAL."' name='vIdHAL'>";
	echo "<input type='hidden' value='".$rIdHAL."' name='rIdHAL'>";
	echo "<input type='hidden' value='".$ctrTrs."' name='ctrTrs'>";
	echo "<input type='hidden' value='".$rIdHALArt."' name='rIdHALArt'>";
	echo "<input type='hidden' value='".$rIdHALCom."' name='rIdHALCom'>";
	echo "<input type='hidden' value='".$rIdHALCou."' name='rIdHALCou'>";
	echo "<input type='hidden' value='".$rIdHALOuv."' name='rIdHALOuv'>";
	echo "<input type='hidden' value='".$rIdHALDou."' name='rIdHALDou'>";
	echo "<input type='hidden' value='".$rIdHALBre."' name='rIdHALBre'>";
	echo "<input type='hidden' value='".$rIdHALRap."' name='rIdHALRap'>";
	echo "<input type='hidden' value='".$rIdHALThe."' name='rIdHALThe'>";
	echo "<input type='hidden' value='".$rIdHALPre."' name='rIdHALPre'>";
	echo "<input type='hidden' value='".$rIdHALPub."' name='rIdHALPub'>";
	echo "<input type='hidden' value='".$lienext."' name='lienext'>";
	echo "<input type='hidden' value='".$noliene."' name='noliene'>";
	echo "<input type='hidden' value='".$embargo."' name='embargo'>";
	echo "<input type='hidden' value='Valider' name='valider'>";
	if ($iMaxRet != 0) {
		echo "<input type='submit' class='btn btn-md btn-primary btn-sm' value='Retour' name='retour'>";
	}
}
//Fin étape 2a
?>