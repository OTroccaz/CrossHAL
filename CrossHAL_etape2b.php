<?php
//Etape 2b > Recherche des IdHAL des auteurs
$tabIdHAL = array();//Tableau des résultats à afficher
$arrayHALStr = array();//Tableau des résultats obtenus pour le/les docid de la structure via HAL
$arrayHALAut = array();//Tableau des résultats obtenus pour le docid de l'auteur via HAL
$tabIdHALsNC = array();//Tableau d'équivalence 'IdHAL_s <> Nom complet'
$tabStructNC = array();//Tableau d'équivalence 'Nom complet <> Id structure'
$iTIH = 0;//Indice de construction du tableau final des résultats
$docidStr = "~";//docid de la structure
//Recherche du/des docid VALID de la structure
$urlHALStr = "https://api.archives-ouvertes.fr/ref/structure/?q=acronym_s:%22".$team."%22%20AND%20valid_s:%22VALID%22&fl=docid";
askCurl($urlHALStr, $arrayHALStr);
$idoc = 0;
while(isset($arrayHALStr["response"]["docs"][$idoc]["docid"])) {
	$docidStr .= $arrayHALStr["response"]["docs"][$idoc]["docid"]."~";
	$idoc++; 
}
//for($cpt = 0; $cpt < $numFound; $cpt++) {
//for($cpt = 0; $cpt < 20; $cpt++) {
$iMinTab = $iMin - 1;
for($cpt = $iMinTab; $cpt < $iMax; $cpt++) {
	progression($cpt+1, $iMax, $iPro);
	$bapa = false;//Booléen HAL (true/false) précisant si c'est une notice à paraître > inPress_bool
	if (isset($arrayHAL["response"]["docs"][$cpt]["inPress_bool"])) {$bapa = $arrayHAL["response"]["docs"][$cpt]["inPress_bool"];}
	$lienDOI = "";//Lien renvoyant vers la notice via le DOI
	if (isset($arrayHAL["response"]["docs"][$cpt]["doiId_s"])) {
		$doi = $arrayHAL["response"]["docs"][$cpt]["doiId_s"];
		$lienDOI = "<a target='_blank' href='https://doi.org/".$doi."'><img alt='DOI' src='./img/doi.jpg'></a>";
	}
	if (isset($arrayHAL["response"]["docs"][$cpt]["halId_s"])) {
		$lienHAL = "<a target='_blank' href='".$racine.$arrayHAL["response"]["docs"][$cpt]["halId_s"]."'><img alt='HAL' src='./img/HAL.jpg'></a>";
	}
	//Prise en compte de tous les auteurs si nombre total < 50
	$iAut = 0;
	if (count($arrayHAL["response"]["docs"][$cpt]["authLastName_s"]) <= 50) {
		while(isset($arrayHAL["response"]["docs"][$cpt]["authLastName_s"][$iAut])) {
			$tabIdHAL["cpt"][$iTIH] = $cpt;
			$tabIdHAL["lienDOI"][$iTIH] = $lienDOI;
			$tabIdHAL["lienHAL"][$iTIH] = $lienHAL;
			$tabIdHAL["nom"][$iTIH] = ucfirst($arrayHAL["response"]["docs"][$cpt]["authLastName_s"][$iAut]);
			$tabIdHAL["prenom"][$iTIH] = ucfirst($arrayHAL["response"]["docs"][$cpt]["authFirstName_s"][$iAut]);
			$tabIdHAL["aff"][$iTIH] = "oui";//Par défaut, l'IdHAL est à rechercher/afficher
			$tabIdHAL["affiliation"][$iTIH] = "-";

			//Recherche de l'IdHAL
			$tabAI = explode("_FacetSep_", $arrayHAL["response"]["docs"][$cpt]["authIdHalFullName_fs"][$iAut]);
			//$authFuN = $tabAI[1];
			$authFuN = $tabIdHAL["prenom"][$iTIH]." ".nomCompEntier($tabIdHAL["nom"][$iTIH]);//Prénom + nom
			//$authFuN = wd_remove_accents(substr($tabIdHAL["prenom"][$iTIH], 0, 1)).". ".wd_remove_accents($tabIdHAL["nom"][$iTIH]);//Initiale(s) prénom(s) + '.' + nom
			$tabIdHAL["nc"][$iTIH] = $authFuN;
			$idHALAjout = "non";
			if ($tabAI[0] != "") {
				//Vérification que le prénom apparaît dans l'IdHAL
				if (strtolower(wd_remove_accents($tabIdHAL["prenom"][$iTIH])) != "") {
					if (strpos($tabAI[0], strtolower(wd_remove_accents($tabIdHAL["prenom"][$iTIH]))) !== false) {
						$tabIdHAL["idhals"][$iTIH] = $tabAI[0];
						$idHALAjout = "oui";
						if (array_search($authFuN, $tabIdHALsNC) === false) {//On ajoute les équivalences 'IdHAL_s <> Nom complet' seulement si elle est absente du tableau
							$tabIdHALsNC[$tabAI[0]] = $authFuN;
						}
					}
				}
			}else{//Pas d'IdHAL avec authIdHalFullName_fs > recherche via le référentiel auteur
				$urlRefAut = "https://api.archives-ouvertes.fr/ref/author/?q=firstName_s:".$tabIdHAL["prenom"][$iTIH]."%20AND%20lastName_s:".$tabIdHAL["nom"][$iTIH]."&fl=*";
				askCurl($urlRefAut, $arrayRefAut);
				$iref = 0;
				while(isset($arrayRefAut["response"]["docs"][$iref]["docid"])) {
					if ($arrayRefAut["response"]["docs"][$iref]["valid_s"] == "VALID" && isset($arrayRefAut["response"]["docs"][$iref]["idHal_s"]) && $arrayRefAut["response"]["docs"][$iref]["idHal_s"] != "") {
						//Vérification que le prénom apparaît dans l'IdHAL
						if (strtolower(wd_remove_accents($tabIdHAL["prenom"][$iTIH])) != "") {
							if (strpos($arrayRefAut["response"]["docs"][$iref]["idHal_s"], strtolower(wd_remove_accents($tabIdHAL["prenom"][$iTIH]))) !== false) {
								$tabIdHAL["idhals"][$iTIH] = $arrayRefAut["response"]["docs"][$iref]["idHal_s"];
								$idHALAjout = "oui";
								if (array_search($authFuN, $tabIdHALsNC) === false) {//On ajoute les équivalences 'IdHAL_s <> Nom complet' seulement si elle est absente du tableau
									$tabIdHALsNC[$arrayRefAut["response"]["docs"][$iref]["idHal_s"]] = $authFuN;
								}
							}
						}
						break;
					}
					$iref++; 
				}
			}
			
			if($idHALAjout == "non") {//Recherche de l'idHAL avec le tableau généré à partir des CSV OCDHAL
				include("./CrossIDHAL.php");
				$testUniqK = strtolower(normalize($team.$tabIdHAL["nom"][$iTIH].$tabIdHAL["prenom"][$iTIH]));
				//if(array_search($testUniqK, array_column($CrossIDHAL, "UniqK")) !== false) {//Auteur trouvé
				foreach($CrossIDHAL as $elt) {
					if($elt["UniqK"] == $testUniqK) {
						$tabIdHAL["idhals"][$iTIH] = $elt["idHAL"];
						$idHALAjout = "oui";
						if (array_search($authFuN, $tabIdHALsNC) === false) {//On ajoute les équivalences 'IdHAL_s <> Nom complet' seulement si elle est absente du tableau
							$tabIdHALsNC[$elt["idHAL"]] = $authFuN;
						}
						break;
					}
				}
			}
			
		
			//L'idHAL trouvé est-il déjà présent dans la notice > si oui, la ligne ne sera pas à afficher
			$aIH = 0;
			while (isset($arrayHAL["response"]["docs"][$cpt]["authIdHal_s"][$aIH])) {
				if (isset($tabIdHAL["idhals"][$iTIH]) && $tabIdHAL["idhals"][$iTIH] == $arrayHAL["response"]["docs"][$cpt]["authIdHal_s"][$aIH]) {
					$idHALAjout = "non";
					break;
				}
				$aIH++;
			}
			if ($idHALAjout == "non") {
				$tabIdHAL["idhals"][$iTIH] = "-";
				$tabIdHAL["aff"][$iTIH] = "non";
			}
			
			
			//Recherche de(s) l'affiliation(s)
			$iAff = 0;//Indice de parcours des résultats obtenus avec authIdHasStructure_fs
			while(isset($arrayHAL["response"]["docs"][$cpt]["authIdHasStructure_fs"][$iAff])) {
				$tabIS = explode("_FacetSep_", $arrayHAL["response"]["docs"][$cpt]["authIdHasStructure_fs"][$iAff]);
				$tabISP = explode("_JoinSep_", $tabIS[1]);
				$tabNA = explode(" ", $tabISP[0]);
				if (isset($tabISP[0]) && $tabISP[0] != "") {
					$pnAut = $tabISP[0];
					//if ($tabISP[0] == $authFuN) {//Les noms complets de l'auteur correspondent
					if ($pnAut == $authFuN) {//Les noms complets de l'auteur correspondent
						$tabIdHAL["affiliation"][$iTIH] .= "~".$tabISP[1];
						if (!array_key_exists($authFuN, $tabStructNC)) {
							$tabStructNC[$authFuN] = $tabISP[1];
						}else{
							if (strpos($tabStructNC[$authFuN], $tabISP[1]) === false) {$tabStructNC[$authFuN] .= "~".$tabISP[1];}
						}
					}
					$iAff++;
				}
			}
			if (isset($arrayHAL["response"]["docs"][$cpt]["producedDate_s"])) {
				$tabIdHAL["annee"][$iTIH] = $arrayHAL["response"]["docs"][$cpt]["producedDate_s"];
			}else{
				$tabIdHAL["annee"][$iTIH] = "-";
			}
			$iAut++;
			$iTIH++;
		}
		
		//var_dump($tabIdHAL);
		//die();
	}
}

//var_dump($tabStructNC);
//var_dump($tabdocidNC);
//var_dump($tabIdHALsNC);
//var_dump($tabIdHAL);
if (!empty($tabIdHAL)) {array_multisort($tabIdHAL["nom"], SORT_ASC, SORT_STRING, $tabIdHAL["prenom"], $tabIdHAL["nc"], $tabIdHAL["cpt"], $tabIdHAL["lienHAL"], $tabIdHAL["lienDOI"], $tabIdHAL["idhals"], $tabIdHAL["affiliation"], $tabIdHAL["annee"], $tabIdHAL["aff"]);}

$cpt = 0;
$cptAff = 0;//Compteur de ligne(s) affichée(s)
while(isset($tabIdHAL["lienHAL"][$cpt])) {
	$lignAff = "no";//Test affichage ou non de la ligne du tableau
	$textAff = "";//Texte de la ligne du tableau
	$iCpt = $cpt + 1;
	//N'afficher que les auteurs de la collection recherchée
	$yestu = "non";
	$tabYet = explode("~", $tabIdHAL["affiliation"][$cpt]);
	foreach($tabYet as $yet) {
		if ($docidStr != "" && strpos($docidStr, $yet) !== false) {
			$yestu = "oui";
			break;
		}
	}
	if ($yestu == "oui") {//Auteur de la collection recherchée
		$textAff .= "<tr style='text-align: center;'>";
		$textAff .= "<td>".$iCpt."</td>";
		if (isset($tabIdHAL["lienDOI"][$cpt])) {
			$textAff .= "<td>".$tabIdHAL["lienDOI"][$cpt]."</td>";
		}else{
			$textAff .= "<td>&nbsp;</td>";
		}
		$textAff .= "<td>".$tabIdHAL["lienHAL"][$cpt]."</td>";
		if ($apa == "oui") {
			if ($bapa) {
				$textAff .= "<td style='text-align: center;'>AP</td>";
			}else{
				$textAff .= "<td style='text-align: center;'>&nbsp;</td>";
			}
		}
		$lienMAJNot = str_replace(array("<a target='_blank' href='https://hal.archives-ouvertes.fr/", "'><img alt='HAL' src='./img/HAL.jpg'></a>"), "", $tabIdHAL["lienHAL"][$cpt]);
		$tabDocid = explode("-", $lienMAJNot);
		$lienMAJNot = "https://hal.archives-ouvertes.fr/submit/update/docid/".$tabDocid[1];
		$textAff .= "<td><a target='_blank' href='".$lienMAJNot."'><img alt='HAL'src='./img/HAL.jpg'></a></td>";

		$textAff .= "<td>".$tabIdHAL["nom"][$cpt]."</td>";
		$textAff .= "<td>".$tabIdHAL["prenom"][$cpt]."</td>";
		if ($tabIdHAL["aff"][$cpt] == "oui") {//IdHAL trouvé à afficher
			//Action
			$lienIDH = "";
			$nomIDH = $tabIdHAL["nom"][$cpt];
			$prenomIDH = $tabIdHAL["prenom"][$cpt];
			$nc = $tabIdHAL["nc"][$cpt];
			$idhals = "";//IdHAL texte
			$struct = "";//Structure d'appartenance de l'auteur
			$idhali = "";//IdHAL numérique
			$emails = "";//email md5
			$emdoms = "";//domaine email
			$arxivs = "";//arxiv_s
			$idrefs = "";//idref_s
			$isnisa = "";//isni_s
			$orcids = "";//orcid_s
			$resids = "";//researcheId_s
			$viafsa = "";//viaf_s
			$docida = "";//docid auteur
			$idhals = array_search($nc, $tabIdHALsNC);
			if (array_key_exists($nc, $tabStructNC)) {$struct = $tabStructNC[$nc];}
			$iHALAut = 0;
			$nodocid = "";
			$iHALtst = "no";//Test pour vérifier si un IdHal valide a été trouvé
			$aureDoc = "";//Test pour vérifier si les informations complémentaires sur l'auteur ont été ajoutées
			if ($idhals != "") {//On va récupérer les informations sur l'auteur
				$tabdocidNC[$tabIS[0]] = $authFuN;
				//$urlHALAut = "https://api.archives-ouvertes.fr/ref/author/?q=idHal_s:%22".$idhals."%22%20AND%20valid_s:%22VALID%22&fl=idHal_s,idHal_i,emailDomain_s,email_s,researcherid_s,docid,arxiv_s,idref_s,isni_s,orcid_s,viaf_s";
				$urlHALAut = "https://api.archives-ouvertes.fr/ref/author/?q=idHal_s:%22".$idhals."%22&fl=idHal_s,idHal_i,emailDomain_s,email_s,researcherid_s,docid,arxiv_s,idref_s,isni_s,orcid_s,viaf_s,valid_s";
				askCurl($urlHALAut, $arrayHALAut);
				while(isset($arrayHALAut["response"]["docs"][$iHALAut]["docid"])) {
					if (isset($arrayHALAut["response"]["docs"][$iHALAut]["valid_s"]) && $arrayHALAut["response"]["docs"][$iHALAut]["valid_s"] == "VALID" ) {//IdHAL auteur valide
						if (isset($arrayHALAut["response"]["docs"][0]["idHal_i"])) {
							$idhali = $arrayHALAut["response"]["docs"][0]["idHal_i"];
						}
						if (isset($arrayHALAut["response"]["docs"][0]["email_s"])) {
							$emails = $arrayHALAut["response"]["docs"][0]["email_s"];
						}
						if (isset($arrayHALAut["response"]["docs"][0]["emailDomain_s"])) {
							$emdoms = $arrayHALAut["response"]["docs"][0]["emailDomain_s"];
						}
						if (isset($arrayHALAut["response"]["docs"][0]["arxiv_s"])) {
							$arxivs = $arrayHALAut["response"]["docs"][0]["arxiv_s"];
						}
						if (isset($arrayHALAut["response"]["docs"][0]["idref_s"])) {
							$idrefs = $arrayHALAut["response"]["docs"][0]["idref_s"];
						}
						if (isset($arrayHALAut["response"]["docs"][0]["isni_s"])) {
							$isnisa = $arrayHALAut["response"]["docs"][0]["isni_s"];
						}
						if (isset($arrayHALAut["response"]["docs"][0]["orcid_s"])) {
							$orcids = $arrayHALAut["response"]["docs"][0]["orcid_s"];
						}
						if (isset($arrayHALAut["response"]["docs"][0]["researcherid_s"])) {
							$resids = $arrayHALAut["response"]["docs"][0]["researcherid_s"];
						}
						if (isset($arrayHALAut["response"]["docs"][0]["viaf_s"])) {
							$viafsa = $arrayHALAut["response"]["docs"][0]["viaf_s"];
						}
						if (isset($arrayHALAut["response"]["docs"][0]["docid"])) {
							$docida = $arrayHALAut["response"]["docs"][0]["docid"];
						}
						$textAff .= "<td>".$idhals."</td>";
						$lienAureHAL = "https://aurehal.archives-ouvertes.fr/author/browse/critere/".$tabIdHAL["nom"][$cpt]."+".$tabIdHAL["prenom"][$cpt]."/solR/1/page/1/nbResultPerPage/50/tri/current_bool/filter/all";
						$textAff .= "<td style='text-align: center;'><a target='_blank' href='".$lienAureHAL."'><img src='./img/HAL.jpg'></a></td>";
						$textAff .= "<td style='text-align: center;'>".$emdoms."</td>";
						$lienDocID = "https://hal.archives-ouvertes.fr/search/index/q/*/authId_i/".$arrayHALAut["response"]["docs"][$iHALAut]["docid"];
						$textAff .= "<td style='text-align: center;'><a target='_blank' href='".$lienDocID."'><img src='./img/HAL.jpg'></a></td>";
						$aureDoc = "ok";
						$iHALtst = "ok";
						//Le DocID doit-il être ignoré ?
						include "./CrossHAL_DocID_a_exclure.php";
						foreach ($EXCLDOCID_LISTE as $value) {
							if ($arrayHALAut["response"]["docs"][$iHALAut]["docid"] == $value) {
								$nodocid = "DocID to be ignored";
								$iHALtst = "";
							}
						}
					}
					$iHALAut++;
				}
			}
			
			if ($iHALtst == "ok") {
				$tei = $arrayHAL["response"]["docs"][$tabIdHAL["cpt"][$cpt]]["label_xml"];
				if (isset($arrayHAL["response"]["docs"][$tabIdHAL["cpt"][$cpt]]["doiId_s"])) {
					$doi = $arrayHAL["response"]["docs"][$tabIdHAL["cpt"][$cpt]]["doiId_s"];
				}
				if (isset($arrayHAL["response"]["docs"][$tabIdHAL["cpt"][$cpt]]["halId_s"])) {
					$halID = $arrayHAL["response"]["docs"][$tabIdHAL["cpt"][$cpt]]["halId_s"];
				}
				//echo $tei;
				$tei = str_replace(array('<p>', '</p>'), '', $tei);
				$tei = str_replace('<p part="N">HAL API platform', '<p part="N">HAL API platform</p>', $tei);
				$teiRes = '<?xml version="1.0" encoding="UTF-8"?>'.$tei;
				//$teiRes = str_replace('<TEI xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', '<TEI xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.tei-c.org/ns/1.0 http://api.archives-ouvertes.fr/documents/aofr-sword.xsd" xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', $teiRes);
				//$Fnm = "./XML/".normalize(wd_remove_accents($titre)).".xml";
				$Fnm = "./XML/".$arrayHAL["response"]["docs"][$tabIdHAL["cpt"][$cpt]]["halId_s"].".xml";
				$xml = new DOMDocument( "1.0", "UTF-8" );
				$xml->formatOutput = true;
				$xml->preserveWhiteSpace = false;
				$xml->loadXML($teiRes);
				
				corrXML($xml);
				
				//Modification noeud auteur avec ajout idhal
				if (is_object($xml->getElementsByTagName("author"))) {
					$elts = $xml->getElementsByTagName("author");
					foreach ($elts as $elt) {
						if ($elt->hasAttribute("role")) {
							$quoi = $elt->getAttribute("role");
							if ($quoi == "aut") {
								foreach($elt->childNodes as $item) {
									if ($item->nodeName == "persName") {
									$trouve = "";
										foreach($item->childNodes as $qui) {
											if ($qui->nodeName == "forename") {
												if ($qui->hasAttribute("type")) {
													if ($qui->getAttribute("type") == "first") {
														if ($qui->nodeValue == $prenomIDH) {
															$trouve .= "oui";
														}
													}
												}
											}
											if ($qui->nodeName == "surname") {
												if ($qui->nodeValue == $nomIDH) {
													$trouve .= "oui";
												}
											}
											if ($trouve == "ouioui") {
												//suppression noeuds idno, affiliation, orgname et email
												if ($elt->getElementsByTagName("idno")->length > 0) {
													while($newXml = $elt->getElementsByTagName("idno")->item(0)) {
														$newXml->parentNode->removeChild($newXml);
														//$newXml = $elt->removeChild($elt->getElementsByTagName("idno")->item(0));
													}
												}
												if ($elt->getElementsByTagName("affiliation")->length > 0) {
													while($newXml = $elt->getElementsByTagName("affiliation")->item(0)) {
														$newXml->parentNode->removeChild($newXml);
														//$newXml = $elt->removeChild($elt->getElementsByTagName("affiliation")->item(0));
													}
												}
												if ($elt->getElementsByTagName("orgName")->length > 0) {
													while($newXml = $elt->getElementsByTagName("orgName")->item(0)) {
														$newXml->parentNode->removeChild($newXml);
														//$newXml = $elt->removeChild($elt->getElementsByTagName("affiliation")->item(0));
													}
												}
												if ($elt->getElementsByTagName("email")->length > 0) {
													while($newXml = $elt->getElementsByTagName("email")->item(0)) {
														$newXml->parentNode->removeChild($newXml);
														//$newXml = $elt->removeChild($elt->getElementsByTagName("email")->item(0));
													}
												}
												//insertion noeuds "corrects"
												
												if ($emails != "") {
													$node = $xml->createElement("email");
													$node->setAttribute("type", "md5");
													$node->nodeValue = $emails;
													$newXml = $elt->appendChild($node);
												}
												if ($emdoms != "") {
													$node = $xml->createElement("email");
													$node->setAttribute("type", "domain");
													$node->nodeValue = $emdoms;
													$newXml = $elt->appendChild($node);
												}
												if ($idhals != "") {
													$node = $xml->createElement("idno");
													$node->setAttribute("type", "idhal");
													$node->setAttribute("notation", "string");
													$node->nodeValue = $idhals;
													$newXml = $elt->appendChild($node);
												}
												if ($idhali != "") {
													$node = $xml->createElement("idno");
													$node->setAttribute("type", "idhal");
													$node->setAttribute("notation", "numeric");
													$node->nodeValue = $idhali;
													$newXml = $elt->appendChild($node);
												}
												if ($docida != "") {
													$node = $xml->createElement("idno");
													$node->setAttribute("type", "halauthorid");
													$node->nodeValue = $docida;
													$newXml = $elt->appendChild($node);
												}
												if ($arxivs != "") {
													$node = $xml->createElement("idno");
													$node->setAttribute("type", "ArxivId");
													$node->nodeValue = $arxivs;
													$newXml = $elt->appendChild($node);
												}
												if ($idrefs != "") {
													$node = $xml->createElement("idno");
													$node->setAttribute("type", "IdrefId");
													$node->nodeValue = $idrefs;
													$newXml = $elt->appendChild($node);
												}
												if ($isnisa != "") {
													$node = $xml->createElement("idno");
													$node->setAttribute("type", "IsniId");
													$node->nodeValue = $isnisa;
													$newXml = $elt->appendChild($node);
												}
												if ($orcids != "") {
													$node = $xml->createElement("idno");
													$node->setAttribute("type", "ORCHID");
													$node->nodeValue = $orcids;
													$newXml = $elt->appendChild($node);
												}
												if ($resids != "") {
													$node = $xml->createElement("idno");
													$node->setAttribute("type", "ResearcherId");
													$node->nodeValue = $resids;
													$newXml = $elt->appendChild($node);
												}
												if ($viafsa != "") {
													$node = $xml->createElement("idno");
													$node->setAttribute("type", "ViafId");
													$node->nodeValue = $viafsa;
													$newXml = $elt->appendChild($node);
												}
												if ($struct != "") {
													//Il faut différencier les 'orgName' des 'affiliation' et les placer dans cet ordre
													$tabStr = explode("~", $struct);
													/*
													foreach($tabStr as $str) {
														$eltstr = $xml->getElementsByTagName("org");
														foreach ($eltstr as $eltr) {
															if ($eltr->hasAttribute("xml:id") && $eltr->getAttribute("xml:id") == "struct-".$str) {
																if ($eltr->getAttribute("type") == "institution") {
																	$node = $xml->createElement("orgName");
																	$node->setAttribute("ref", "#struct-".$str);
																	$newXml = $elt->appendChild($node);
																}
															}
														}
													}
													*/
													foreach($tabStr as $str) {
														$eltstr = $xml->getElementsByTagName("org");
														foreach ($eltstr as $eltr) {
															if ($eltr->hasAttribute("xml:id") && $eltr->getAttribute("xml:id") == "struct-".$str) {
																if ($eltr->getAttribute("type") == "laboratory") {
																	$node = $xml->createElement("affiliation");
																	$node->setAttribute("ref", "#struct-".$str);
																	$newXml = $elt->appendChild($node);
																}
															}
														}
													}
												}
												
												break 2;
											}
										}
									}
								}
							}
						}
					}
				}
				$xml->save($Fnm);
				$lienIDH = "./CrossHAL_Modif.php?action=MAJ&etp=2&Id=".$arrayHAL["response"]["docs"][$tabIdHAL["cpt"][$cpt]]["halId_s"];
				include "./CrossHAL_actions.php";
				$actMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_IDH",$tab)) {$actMaj = "no";}
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
						if ($embgDate > $limDate) {//Il n'est pas possible de faire les modifications car la date whenEndEmbargoed n'est pas dépassée
							$embgModi = "pasok";
						}
					}
					if ($embgModi == "ok") {
						$lignAff = "ok";
						$textAff .= "<td><center><span id='maj".$halID."'><a target='_blank' href='".$lienIDH."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_IDH\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span></center></td>";
					}else{
						$textAff .= "<center><img alt='Embargo' title='Modification impossible : dépôt sous embargo' style='width: 50px;' src='./img/addEmbargo_grand.png'></center>";
					}
				}else{
					$lignAff = "ok";
					$textAff .= "<td><center><img style='width: 50px;' src='./img/addOK_grand.png'></center></td>";
				}
			}else{
				$lignAff = "ok";
				if ($aureDoc == "") {
					$textAff .= "<td>".$tabIdHAL["idhals"][$cpt]."</td>";
					$lienAureHAL = "https://aurehal.archives-ouvertes.fr/author/browse/critere/".$tabIdHAL["nom"][$cpt]."+".$tabIdHAL["prenom"][$cpt]."/solR/1/page/1/nbResultPerPage/50/tri/current_bool/filter/all";
					$textAff .= "<td style='text-align: center;'><a target='_blank' href='".$lienAureHAL."'><img src='./img/HAL.jpg'></a></td>";
					$textAff .= "<td style='text-align: center;'>&nbsp;</td>";//Nom de domaine
					$textAff .= "<td style='text-align: center;'>&nbsp;</td>";//DocID
				}
				if ($nodocid == "") {
					$textAff .= "<td><center><img alt='Invalide' title='IdHal non valide' style='width: 50px;' src='./img/addEmbargo_grand.png'></center></td>";
				}else{
					$textAff .= "<td><center><img alt='Invalide' title='DocID à ignorer' style='width: 50px;' src='./img/addEmbargo_grand.png'></center></td>";
				}
			}
		}
		$textAff .= "<td>".$team."</td>";
		//echo("<td>".$tabIdHAL["domaine"][$cpt]."</td>");
		$textAff .= "<td>".substr($tabIdHAL["annee"][$cpt], 0, 4)."</td>";
		$textAff .= "</tr>";
	}
	if ($lignAff == "ok") {//Il y a des corrections à apporter > la ligne est à afficher
		echo $textAff;
		$cptAff++;
	}else{//Pas de correction à apporter > inutile d'afficher la ligne
	}
	$cpt++;
}
echo "</table><br>";
echo "<script>";
echo "  document.getElementById('cpt').style.display = \"none\";";
echo "</script>";

if ($iMax != $numFound) {
	echo "<form name='troli' id='etape2b' action='CrossHAL.php' method='post'>";
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
	echo "<input type='hidden' value='".$rIdHAL."' name='rIdHAL'>";
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
	echo "<input type='hidden' value='Valider' name='valider'>";
	if ($iMinInit != 1) {
		echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>&nbsp;&nbsp;&nbsp;";
	}
	echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Suite' style='width: 70px;' name='suite'>";
	echo "</form><br>";
	//echo "<script>formFilePDF();</script>";
}else{
	echo "<form name='troli' id='etape2b' action='CrossHAL.php' method='post'>";
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
	echo "<input type='hidden' value='".$rIdHAL."' name='rIdHAL'>";
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
	echo "<input type='hidden' value='Valider' name='valider'>";
	if ($iMaxRet != 0) {
		echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>";
	}
}
if ($cptAff == 0 && $iPro < $numFound) {//Auto-soumission du formulaire
	echo "<script>";
	echo "  document.getElementById(\"etape2b\").submit(); ";
	echo "</script>";
}
//Fin étape 2b
?>