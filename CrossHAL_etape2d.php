<?php
//Etape 2d > Contrôle des tiers
//var_dump($arrayHAL["response"]["docs"]);
include("./CrossHAL_contrib_surs.php");
include("./CrossHAL_dom_coll.php");
include("./CrossHAL_labo_affil_struct.php");
include("./CrossHAL_suppr_tampons.php");
include("./pvt/ExtractionHAL-auteurs.php");

$iMinTab = $iMin - 1;
$cptAff = 0;//Compteur de ligne(s) affichée(s)
include("./CrossHAL_vu_halID.php");
$totCpt = 0;
for($cpt = $iMinTab; $cpt < $iMax; $cpt++) {
	if (in_array($arrayHAL["response"]["docs"][$cpt]["halId_s"], $HALID_VU) || $arrayHAL["response"]["docs"][$cpt]["docType_s"] == "THESE") {//Ne pas prendre en compte les halId déjà VU ou les thèses
	}else{
		progression($cpt+1, $iMax, $iPro);
		$lignAff = "ok";//Test affichage ou non de la ligne du tableau
		$textAff = "";//Texte de la ligne du tableau
		$doi = "";//DOI de la notice
		$halID = "";//halId de la notice
		$lienHAL = "";//Lien renvoyant vers la notice HAL
		$lienDOI = "";//Lien renvoyant vers la notice via le DOI
		$bapa = false;//Booléen HAL (true/false) précisant si c'est une notice à paraître > inPress_bool
		$premAut = "";//Premier auteur
		$listAut = "";//Liste des auteurs incrémentée pour éviter les doublons dans l'extraction TEI des co-auteurs affiliés au laboratoire
		$coAutAffil = "";//Liste des co-auteurs affiliés au laboratoire
		$domMel = "-";//Domaine email
		$verifCtb = "non";//Test pour savoir s'il faut vérifier que le contributeur est "sûr"
		$ctb = "";//Prénom nom du contributeur
		$pcentAffil = "";//Affiliations de type INCOMING ou OLD
		$pubmedAff = "";//Résultat interrogation FCGI si PMID
		$actions = "";
		$actMaj = "";
		
		//Récupération du TEI
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
		$elts = $xml->documentElement;
		
		//$xml->save($Fnm);
		
		//Assignation des variables
		if (isset($arrayHAL["response"]["docs"][$cpt]["inPress_bool"])) {$bapa = $arrayHAL["response"]["docs"][$cpt]["inPress_bool"];}
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
		$textAff .= "<tr style='text-align: center;'><td>".$cptTab."</td>";
		
		//Affichage des liens
		$textAff .= "<td style='text-align: center;'>".$lienDOI."</td>";
		$textAff .= "<td style='text-align: center;'>".$lienHAL."</td>";
		
		//Affichage AP si demandé
		if ($apa == "oui") {
			if ($bapa) {
				$textAff .= "<td style='text-align: center;'>AP</td>";
			}else{
				$textAff .= "<td style='text-align: center;'>&nbsp;</td>";
			}
		}
		
		//Co-auteurs affiliés au laboratoire
		$cptAut = 0;
		$cptRed = 0;
		$listecoAut = "~";
		for($i=0; $i < $xml->getElementsByTagName("author")->length; $i++) {
			$affil = "";
			$aut = "";
			$affilTest = "";
			$collHAL = "";
			$actAut = "";
			$elts = $xml->getElementsByTagName("author")->item($i);
			//Vérification que l'auteur a bien un rôle 'aut'
			if ($elts->hasAttribute("role") && $elts->getAttribute("role") == "aut") {
				if (strpos($listAut, $xml->getElementsByTagName("author")->item($i)->nodeValue) === false) {//Auteur non encore rencontré et donc à considérer
					$listAut .= "~".$xml->getElementsByTagName("author")->item($i)->nodeValue;
					for($j=0; $j < $elts->childNodes->length; $j++) {
						//Récupération du prénom nom de l'auteur
						if ($elts->childNodes->item($j)->nodeName == "persName") {
							$preAut = prenomCompEntier($elts->childNodes->item($j)->getElementsByTagName("forename")->item(0)->nodeValue);
							$nomAut = nomCompEntier($elts->childNodes->item($j)->getElementsByTagName("surname")->item(0)->nodeValue);
							$aut = $preAut." ".$nomAut;
						}
						//Récupération de l'affiliation
						if ($elts->childNodes->item($j)->nodeName == "affiliation") {
							if ($elts->childNodes->item($j)->hasAttribute("ref")) {
								$affil = $elts->childNodes->item($j)->getAttribute("ref");
								if (strpos($listecoAut, $aut) === false) {//Auteur non présent encore dans la liste
									if (array_key_exists($affil, $LABAFFSTR_LISTE)) {//Affiliation présente dans la liste
										$affilTest = $LABAFFSTR_LISTE[$affil];
										//Co-auteurs à mettre en évidence
										if ($LABAFFSTR_LISTE[$affil] == $team && $aut != $premAut) {
											$listecoAut .= $aut."~";
											if ($cptAut == 0) {$coAutAffil .= "<font color='red'>".$aut."</font>"; $cptAut++; $cptRed++;}else{$coAutAffil .= ", <font color='red'>".$aut."</font>"; $cptRed++;}
										}
									}else{
										if ($aut != $premAut) {
											$listecoAut .= $aut."~";
											if ($cptAut == 0) {$coAutAffil .= $aut; $cptAut++;}else{$coAutAffil .= ", ".$aut;}
										}
									}
								}
								//Vérification avec le listing ExtrHAL
								if (array_search($nomAut, array_column($AUTEURS_LISTE, 'nom')) && ($AUTEURS_LISTE[array_search($nomAut, array_column($AUTEURS_LISTE, 'nom'))]['prenom'] == $preAut || substr($AUTEURS_LISTE[array_search($nomAut, array_column($AUTEURS_LISTE, 'nom'))]['prenom'], 0, 1) == $preAut || prenomCompInit($AUTEURS_LISTE[array_search($nomAut, array_column($AUTEURS_LISTE, 'nom'))]['prenom'], 0, 1) == $preAut)) {
										$collHAL = $AUTEURS_LISTE[array_search($nomAut, array_column($AUTEURS_LISTE, 'nom'))]["collhal"];
								}
								if ($affilTest != $collHAL && $collHAL != "" && $actAut != "no") {$actAut = "ok";}else{$actAut = "no";}
							}
						}
					}
				}
				if ($actAut == "ok") {$actions .= "<font color='red'>".$aut." > ".$collHAL."</font><br>"; }
			}
		}
		if ($cptRed > 1) {$lignAff = "no"; $domCont = "ok";}//Ne pas afficher la ligne s'il y a au moins 2 auteurs identifiés dans le listing ExtrHAL
		
		//Domaine email + contributeur
		$domCont = "no";//Quelle que soit la suite, bloquer l'affichage de la ligne si domaine email du contributeur contient "rennes" ou "irisa.fr" ou si c'est un contributeur sûr
		for($i=0; $i < $xml->getElementsByTagName("respStmt")->length; $i++) {
			$elts = $xml->getElementsByTagName("respStmt")->item($i);
			for($j=0; $j < $elts->childNodes->length; $j++) {
				if ($elts->childNodes->item($j)->nodeName == "resp" && $elts->childNodes->item($j)->nodeValue == "contributor") {
					$name = $elts->getElementsByTagName("name")->item(0);
					for($k=0; $k < $name->childNodes->length; $k++) {
						//Récupération prénom nom du contributeur
						if ($name->childNodes->item($k)->nodeName == "persName") {
							$preCtb = prenomCompEntier($name->getElementsByTagName("forename")->item(0)->nodeValue);
							$nomCtb = ucfirst(mb_strtolower($name->getElementsByTagName("surname")->item(0)->nodeValue, 'UTF-8'));
							$ctb = $preCtb." ".$nomCtb;
						}
						//Récupération domaine email du contributeur
						if ($name->childNodes->item($k)->nodeName == "email") {
							if ($name->childNodes->item($k)->hasAttribute("type") && $name->childNodes->item($k)->getAttribute("type") == "domain") {
								$domMel = $name->childNodes->item($k)->nodeValue;
								if (stripos($domMel, "rennes") !== false || stripos($domMel, "irisa.fr") !== false) {
									$lignAff = "no";//Ne pas afficher les publications dont le domaine email du contributeur contient "rennes"
									$domCont = "ok";
								}else{
									if (in_array($ctb, $CTBSURS_LISTE)) {$lignAff = "no"; $domCont = "ok";}//Ne pas afficher les publications dont le contributeur est "sûr" + in_array sensible à la casse
								}
							}
						}
					}
				}
			}
		}
		
		/*Désactivation temporaire du contrôle du domaine disciplinaire
		//Domaine disciplinaire
		$domDis = "-";//Domaine disciplinaire
		$elts = $xml->getElementsByTagName("classCode");
		$nbelt = $elts->length;
		for ($pos = $nbelt; --$pos >= 0;) {
			$elt = $elts->item($pos);
			if ($elt && $elt->hasAttribute("scheme") && $elt->getAttribute("scheme") == "halDomain") {
				if ($elt->hasAttribute("n")) {//Isoler le premier élément avant le point dans l'attribut "n"
					$tabDom = explode(".", $elt->getAttribute("n"));
					$domDis .= $tabDom[0]." - ";
				}
			}
		}
		if ($domDis != "-") {$domDis = substr($domDis, 1, (strlen($domDis) - 3));}
		//Vérification que le domaine trouvé dans le TEI est bien celui qui devrait être trouvé à partir de la liste $DOMCOLL_LISTE
		$actDom = "<font color='red'>Domaines différents</font><br>";
		for ($i=0; $i<count($DOMCOLL_LISTE); $i++) {
			if ($DOMCOLL_LISTE[$i]['collhal'] == $team) {
				if (strpos($domDis, $DOMCOLL_LISTE[$i]['domaine']) !== false) {$actDom = "";}
			}
		}
		$actions .= $actDom;
		*/
		
		//Vérification présence affiliation code collection recherché parmi les auteurs et les organismes pour suppression selon supervision utilisateur
		$affilAut = "";
		$affilOrg = "";
		$actAffil = "";
		$actMaj = "";
		
		$tabAffil = array_keys($LABAFFSTR_LISTE, $team);
		
		for($ta=0; $ta < count($tabAffil); $ta++) {
			$affilAut = $tabAffil[$ta];
			$affilOrg = str_replace("#", "", $affilAut);
								
			if ($affilAut != "") {
				for($i=0; $i < $xml->getElementsByTagName("affiliation")->length; $i++) {
					$elt = $xml->getElementsByTagName("affiliation")->item($i);
					if ($elt->hasAttribute("ref") && $elt->getAttribute("ref") == $affilAut) {
						$elt->parentNode->removeChild($elt);
						$i--;
						$xml->save($Fnm);
						$actMaj = "ok";
						$actAffilInit = "ok";
					}
				}
				
				for($i=0; $i < $xml->getElementsByTagName("org")->length; $i++) {
					$elt = $xml->getElementsByTagName("org")->item($i);
					if ($elt->hasAttribute("xml:id") && $elt->getAttribute("xml:id") == $affilOrg) {
						$elt->parentNode->removeChild($elt);
						$i--;
						$xml->save($Fnm);
						$actMaj = "ok";
						$actAffilInit = "ok";
					}
				}
				
				for($i=0; $i < $xml->getElementsByTagName("relation")->length; $i++) {
					$elt = $xml->getElementsByTagName("relation")->item($i);
					if ($elt->hasAttribute("active") && $elt->getAttribute("active") == $affilAut) {
						$del = $elt->parentNode->parentNode;
						$del->parentNode->removeChild($del);
						$i--;
						$xml->save($Fnm);
						$actMaj = "ok";
						$actAffilInit = "ok";
					}
				}
			}
			//echo $ctb.' - '.$affilAut.' - '.$affilOrg.'<br>';
		}

		if ($actMaj == "ok") {
			$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=2&Id=".$halID;
			$proDate = $arrayHAL["response"]["docs"][$cpt]["producedDate_s"];
			$depDate = $arrayHAL["response"]["docs"][$cpt]["submittedDate_s"];
			$actAffil .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienMAJ."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_AFFIL\", ctb: \"".$ctb."\", domMel: \"".$domMel."\", proDate: \"".$proDate."\", depDate: \"".$depDate."\", team: \"".$team."\" });majok(\"".$halID."\"); majokVu(\"".$halID."\"); '><img alt='MAJ' src='./img/MAJ.png'></a></span></center>";
		}else{
			$actAffil .= "";
		}
		$actions .= $actAffil;

		
		//Affichages initiaux
		//Contributeur
		$textAff .= "<td style='text-align: center;'>".$ctb."</td>";
		//Co-auteurs
		$textAff .= "<td style='text-align: center;'>".$coAutAffil."</td>";
		//Titre de la publication
		$textAff .= "<td style='text-align: center;'>".$titre."</td>";
		//Domaine email
		$textAff .= "<td style='text-align: center;'>".$domMel."</td>";
		/*Désactivations temporaires
		//Domaine disciplinaire
		$textAff .= "<td style='text-align: center;'>".$domDis."</td>";
		*/
		
		//Affiliations de type INCOMING ou OLD
		for($i=0; $i < $xml->getElementsByTagName("org")->length; $i++) {
			$elts = $xml->getElementsByTagName("org")->item($i);
			if ($elts->hasAttribute("type") && ($elts->getAttribute("type") == "laboratory" ||$elts->getAttribute("type") == "researchteam")) {
				if ($elts->hasAttribute("status") && ($elts->getAttribute("status") == "OLD" || $elts->getAttribute("status") == "INCOMING")) {
					$pcentAffil = "<img src='./img/pasok.png'>";
					break;
				}
			}
		}
		$textAff .= "<td style='text-align: center;'>".$pcentAffil."</td>";
		
		/*Désactivation temporaire du contrôle Pubmed
		//FCGI et PMID
		if (isset($arrayHAL["response"]["docs"][$cpt]["pubmedId_s"])) {
			$testAffiMC = "no";
			$pubmed = $arrayHAL["response"]["docs"][$cpt]["pubmedId_s"];
			$urlPM = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=".$pubmed;
			$fp = fopen("./PubMed.fcgi", "w");
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $urlPM);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'SCD (https://halur1.univ-rennes1.fr)');
			curl_setopt($ch, CURLOPT_USERAGENT, 'PROXY (http://siproxy.univ-rennes1.fr)');
			if (isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
				curl_setopt($ch, CURLOPT_CAINFO, "cacert.pem");
			}
			$resultat = curl_exec($ch);
			fwrite($fp, $resultat);
			
			//Traitemant du fichier FCGI
			include('./CrossHAL_FCGI_import.php');
			
			//Recherche des affiliations
			$affili = "";
			$affiMC = array();
			for ($k=0; $k<count($fcgiRes[0]['tabAff']); $k++) {
				$affili .= $fcgiRes[0]['tabAff'][$k].';';
				$motcle = explode(",", strtolower(wd_remove_accents(str_replace(array(';', '.'), array(',', ''), $fcgiRes[0]['tabAff'][$k]))));
				for ($mc=0; $mc<count($motcle); $mc++) {
					array_push($affiMC, trim($motcle[$mc]));
				}
			}
			include('./CrossHAL_collections_motscles.php');
			$collMC = array();
			if (isset($collectionsMC[$team])) {$collMC = explode(";", strtolower(wd_remove_accents($collectionsMC[$team])));}
			
			$pubmedAff = "<img src='./img/pasok.png'>";
			for ($mc=0; $mc<count($collMC); $mc++) {
				for ($cm=0; $cm<count($affiMC); $cm++) {
					//if (array_search($collMC[$mc], $affiMC) !== false) {// Au moins une correspondance affiliation fcgi/"mot-clé HAL"
					if (stripos($affiMC[$cm], $collMC[$mc]) !== false) {// Au moins une correspondance affiliation fcgi/"mot-clé HAL"
						$pubmedAff = "";
						$testAffiMC = "ok";
						break 2;
					}
				}
			}
			//if ($testAffiMC == "no") {var_dump($collMC); var_dump($affiMC); $lignAff = "ok";}
			if ($testAffiMC == "no" && $domCont == "no") {$lignAff = "ok";}
			
		}
		$textAff .= "<td style='text-align: center;'>".$pubmedAff."</td>";
		*/
		
		//Vu > Conforme
		$textAff .= "<td style='text-align: center;'><span id='Vu".$halID."'><a style=\"cursor:pointer\" onclick='$.post(\"CrossHAL_vu_actions.php\", { halID: \"".$halID."\" }); majokVu(\"".$halID."\"); $.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_VU\" }); majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' src='./img/MAJ.png'></a></span></td>";

		//Tampons
		$actStp = "";
		$elts = $xml->getElementsByTagName("idno");
		for($i=0; $i < $xml->getElementsByTagName("idno")->length; $i++) {
			$elt = $xml->getElementsByTagName("idno")->item($i);
			//On ne s'intéresse qu'aux idno de type stamp
			if ($elt->hasAttribute("type") && $elt->getAttribute("type") == "stamp") {
				if (array_search($elt->getAttribute("n") , $TAMPERR_LISTE)) {
					$elt->parentNode->removeChild($elt);
					$i--;
					$xml->save($Fnm);
					$actMaj = "ok";
				}
			}
		}
		/*//En fait, il n'est pas possible (pour l'instant ?) de modifier les tampons via Sword
		if ($actMaj == "ok") {
			$lienMAJ = "./CrossHAL_Modif.php?action=MAJ&etp=2&Id=".$halID;
			$actStp .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienMAJ."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_STAMP\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' src='./img/MAJ.png'></a></span></center>";
		}else{
			$actStp .= "<center><img src='./img/MAJOK.png'></center>";
		}
		$actions .= $actStp;
		*/
		
		//Action
		$textAff .= "<td style='text-align: center; width: 20%;'>".$actions."</td>";

		if ($lignAff == "ok") {//Il y a des corrections à apporter > la ligne est à afficher
			echo $textAff;
			$cptAff++;
			$totCpt++;
		}
	}
}

echo "</table>";
echo "<script>";
echo "  document.getElementById('cpt').style.display = \"none\";";
echo "</script>";
echo "<strong>".$totCpt." notice(s) remontée(s)</strong><br><br>";

if ($iMax != $numFound) {
	echo "<form name='troli' id='etape2c' action='CrossHAL.php' method='post'>";
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
		echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>&nbsp;&nbsp;&nbsp;";
	}
	echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Suite' style='width: 70px;' name='suite'>";
	echo "</form><br>";
}else{
	echo "<form name='troli' id='etape2c' action='CrossHAL.php' method='post'>";
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
		echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>";
	}
}

if ($cptAff == 0) {//Auto-soumission du formulaire
	echo "<script>";
	echo "  document.getElementById(\"etape2c\").submit(); ";
	echo "</script>";
}
//Fin étape 2d
?>