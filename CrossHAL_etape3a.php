<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Etape 3a - Stage 3a
 */
 
//Etape 3a > Manuscrit auteurs (fichiers sous la forme doi_normalisé.pdf)
$urlServeur = "";
if (isset($_POST["urlServeur"]) && $_POST["urlServeur"] != "") {$urlServeur = $_POST["urlServeur"];}
if (isset($_GET["urlServeur"]) && $_GET["urlServeur"] != "")  {$urlServeur = $_GET["urlServeur"];}
$rows = 100000;//100000
$racine = "https://hal.archives-ouvertes.fr/";
if ($apa == "oui") {//Notice "A paraître"
	$txtApa = "";
}else{
	$txtApa = "%20AND%20NOT%20inPress_bool:%22true%22";
}
if ($lienext == "oui") {
	$txtExt = "%20AND%20(linkExtId_s:%22openaccess%22%20OR%20linkExtId_s:%22pubmedcentral%22)";
}else{
	$txtExt = "";
}
if ($noliene == "oui") {
	$txtNoe = "%20AND%20NOT%20(linkExtId_s:%22openaccess%22%20OR%20linkExtId_s:%22pubmedcentral%22)";
}else{
	$txtNoe = "";
}
if (isset($idhal) && $idhal != "") {$atester = "authIdHal_s"; $qui = $idhal;}else{$atester = "collCode_s"; $qui = $team;}
//$urlHAL = "https://api.archives-ouvertes.fr/search/?q=collCode_s:%22".$team."%22".$txtApa.$txtExt.$txtNoe."%20AND%20NOT%20submitType_s:%22file%22&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:%22ART%22&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,producedDateY_i,publisher_s,label_xml,submittedDate_s&sort=halId_s%20desc";
	if($ordinv == "oui") {$sort = "desc";}else{$sort = "asc";}
	$urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa.$txtExt.$txtNoe."%20AND%20NOT%20submitType_s:%22file%22&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(%22ART%22%20OR%20%22COMM%22%20OR%20%22COUV%22)&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,producedDateY_i,publisher_s,label_xml,submittedDate_s,docType_s&sort=halId_s%20".$sort;
//$contents = file_get_contents($urlHAL);
//$resHAL = json_decode($contents, true);
//$numFound = $resHAL["response"]["numFound"];
askCurl($urlHAL, $arrayHAL);
//var_dump($arrayCurl);
//var_dump($arrayHAL['response']['docs']);
$numFound = $arrayHAL["response"]["numFound"];
if ($numFound == 0) {die ('<strong>Aucune référence</strong><br><br>');}
if ($iMax > $numFound) {$iMax = $numFound;}
echo '<strong>Total de '.$numFound.' référence(s)';
if ($numFound != 0) {echo " : affichage de ".$iMin." à ".$iMax."</strong>&nbsp;<em>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</em><br><br>";}

echo "<div id='cpt'></div>";
//echo "<table class='table table-striped table-bordered table-hover;'><tr>";
//echo "<table style='border-collapse: collapse; width: 100%' border='1px' bordercolor='#999999' cellpadding='5px' cellspacing='5px'><tr>";
echo "<table class='table table-responsive table-striped table-bordered table-centered table-sm text-center small'>";
echo "<thead class='thead-dark'><tr>";
echo "<th><strong>ID</strong></th>";
echo "<th><strong>DOI</strong></th>";
echo "<th><strong>HAL</strong></th>";
echo "<th><strong>Année pub.</strong></th>";
echo "<th><strong>Titre</strong></th>";
echo "<th><strong>Nom 1er auteur</strong></th>";
echo "<th><strong>Licence</strong></th>";
echo "<th><strong>Type</strong></th>";
echo "<th><strong>PDF</strong></th>";
echo "<th><strong>Editeur</strong></th>";
echo "<th><strong>Action 1 > Déposer</strong></th>";
echo "<th><strong>Action 2 > Parcourir</strong></th>";
//echo "<th><strong>Statut</strong></th>";
echo "</tr></thead><tbody>";

$iMinTab = $iMin - 1;
$cptAff = 0;//Compteur de ligne(s) affichée(s) 
for($cpt = $iMinTab; $cpt < $iMax; $cpt++) {
	progression($cpt+1, $iMax, $iPro);
	$cptTab = $cpt + 1;
	//Si le TEI contient un noeud avec licence 'Copyright', la notice ne doit pas remonter
	$stop = "non";
	$tei = $arrayHAL["response"]["docs"][$cpt]["label_xml"];
	$tei = str_replace(array('<p>', '</p>'), '', $tei);
	$tei = str_replace('<p part="N">HAL API platform', '<p part="N">HAL API platform</p>', $tei);
	$teiRes = '<?xml version="1.0" encoding="UTF-8"?>'.$tei;
	$xml = new DOMDocument( "1.0", "UTF-8" );
	$xml->formatOutput = true;
	$xml->preserveWhiteSpace = false;
	$xml->loadXML($teiRes);
	$copyr = array();
	$copyr = $xml->getElementsByTagName('licence');
	foreach ($copyr as $elt) {
		if ($elt->nodeValue == 'Copyright') {
			$stop = "oui";
			break;
		}
	}
	if ($stop == "non") {		
		$lignAff = "no";//Test affichage ou non de la ligne du tableau
		$textAff = "";//Texte de la ligne du tableau
		$condAct = "no";// Condition à remplir pour activer le bouton Action : Si type doc "auteur", le bouton Action n'est activé que si on a cliqué sur "lien", i.e., on s'est assuré que le PDF était bien un manuscrit auteur
		$doi = "";//DOI de la notice
		$halID = "";//halId de la notice
		$evd = "";//Noeud "evidence" (OA detection process) récupéré via https://api.unpaywall.org/v2/
		$lienPDF = "";//2 fonctions : initialement, valeur du noeud "fileMain_s" HAL, puis lien Action permettant la modification
		$urlPDF = "";//URL du PDF qui sera renseignée dans le TEI
		if (isset($arrayHAL["response"]["docs"][$cpt]["doiId_s"])) {
			$doi = $arrayHAL["response"]["docs"][$cpt]["doiId_s"];
			//echo normalize($doi);
		}else{
			$titre = $arrayHAL["response"]["docs"][$cpt]["title_s"][0];
			$rechDOI = "";
			rechTitreDOI($titre, 5, $closest, $shortest, $rechDOI);
			if ($rechDOI != "") {
				$doi = $rechDOI;
			}
		}
		$nodoi = "";
		//Le DOI doit-il être ignoré (et donc la notice) ?
		include "./CrossHAL_DOIS_a_exclure.php";
		foreach ($EXCLDOIS_LISTE as $value) {
			if ($doi == $value) {
				$nodoi = "DOI to be ignored";
				break;
			}
		}
		//Rechercher initialement si un nom de fichier a été renseigné dans la notice HAL
		if (isset($arrayHAL["response"]["docs"][$cpt]["halId_s"])) {
			$halID = $arrayHAL["response"]["docs"][$cpt]["halId_s"];
			$lienHAL = "<a target='_blank' href='".$racine.$arrayHAL["response"]["docs"][$cpt]["halId_s"]."'><img alt='HAL' src='./img/HAL.jpg'></a>";
		}
		$urlFIL = "https://api.archives-ouvertes.fr/search/?q=halId_s:%22".$halID."%22&fl=fileMain_s,linkExtUrl_s";
		//$urlFIL = "https://api.archives-ouvertes.fr/search/?q=halId_s:%22hal-01649568%22&fl=fileMain_s";
		//$contFIL = file_get_contents($urlFIL);
		//$resFIL = json_decode($contFIL, true);
		askCurl($urlFIL, $arrayFIL);

		if (isset($arrayFIL["response"]["docs"][0]["fileMain_s"]) && $arrayFIL["response"]["docs"][0]["fileMain_s"] != "")  {
			$lienPDF = $arrayFIL["response"]["docs"][0]["fileMain_s"];
		}
		if ($lienPDF == "" && $nodoi == "") {//Rien actuellement dans la notice et le DOI n'est pas à exclure ...
			//... mais il y a peut-être un lien OA externe
			if (isset($arrayFIL["response"]["docs"][0]["linkExtUrl_s"]) && $arrayFIL["response"]["docs"][0]["linkExtUrl_s"] != "")  {
				$urlPDF = "";
				//$urlPDF = htmlspecialchars($lienPDF);
				$halID = $arrayHAL["response"]["docs"][$cpt]["halId_s"];
				$urlT = "https://api.unpaywall.org/v2/".$doi;
				$volT = "";
				$issT = "";
				$pagT = "";
				$datT = "";
				$pdfCR = "";
				$orig = "licextlink";
				$testDOI = "";
				$ipc = 0;
				//$evd = "greenPublisher";
				while ($testDOI == "") {
					testOALic($urlT, $volT, $issT, $pagT, $datT, $pdfCR, $halID, $evd, $testDOI, $typLic, $compCC, $compNC, $compND, $compSA, $urlPDF, $orig);
					if ($testDOI == "") {
						$ipc++;
						proxyCURL($cpt+1, 2000000, $ipc, $iMax, $iPro);
					}
				}
				//Ne pas afficher le bouton Action si le lien ne contient pas la chaîne 'pdf'
				//if (stripos($lienPDF, ".pdf") === false) {$evd = "noaction";}
				
				//Si on a un point après le dernier slash du lien PDF, il doit être obligatoirement suivi de 'pdf', autrement, ne pas afficher le bouton Action
				//Recherche du dernier slash et extraction de la sous-chaîne
				$extUrlPDF = strrchr ($urlPDF, "/");
				//Tester la présence d'un point dans cette sous-chaîne, et, si c'est le cas, de la présence de '.pdf'
				if (strpos($extUrlPDF, ".") !== false && stripos($extUrlPDF, ".pdf") === false) {
					$evd = "noaction";
				}
				
				if ($evd != "noaction" && stripos($urlPDF, "https://hal") === false) {//Le fichier PDF n'est pas un fichier auteur
					genXMLPDF($halID, $doi, $targetPDF, $halID, $evd, $compNC, $compND, $compSA, $lienPDF, $urlPDF);
				}
			}else{
				//Si le DOI existe, il faut rechercher un fichier PDF OA si aucun PDF n'a été envoyé
				if (isset($doi) && $doi != "") {
					$pubCR = "";
					$volCR = "";
					$numCR = "";
					$pagCR = "";
					$pdfCR = "";
					$urlCR = "https://api.crossref.org/v1/works/http://dx.doi.org/".$doi;
					//echo $urlCR;

					if (@file_get_contents($urlCR)) {
					//if (@file_get_contents(askCurl($urlCR, $arrayCR))) {
						//$contents = file_get_contents($urlCR);
						//$contents = utf8_encode($contents); 
						//$results = json_decode($contents, TRUE);
						askCurl($urlCR, $arrayCR);
						if (isset($arrayCR["message"]["volume"])) {
							$volCR = $arrayCR["message"]["volume"];
						}
						if (isset($arrayCR["message"]["issue"])) {
							$numCR = $arrayCR["message"]["issue"];
						}
						if (isset($arrayCR["message"]["page"])) {
							$pagCR = $arrayCR["message"]["page"];
						}
						if (isset($arrayCR["message"]["published-print"]["date-parts"][0][0])) {
							$pubCR = $arrayCR["message"]["published-print"]["date-parts"][0][0];
						}
						if (isset($arrayCR["message"]["link"][0]["URL"])) {
							$pdfCR = $arrayCR["message"]["link"][0]["URL"];
						}
					}

					$urlT = "https://api.unpaywall.org/v2/".$doi;
					$volT = $volCR;
					$issT = $numCR;
					$pagTab = explode("-", $pagCR);
					$pagT = $pagTab[0];
					$datT = $pubCR;
					$Fnm = "";
					$titPDF = "";
					$lienPDF = "";
					$orig = "searchpdf";
					$testDOI = "";
					$ipc = 0;
					
					while ($testDOI == "") {
						testOALic($urlT, $volT, $issT, $pagT, $datT, $pdfCR, $arrayHAL["response"]["docs"][$cpt]["halId_s"], $evd, $testDOI, $typLic, $compCC, $compNC, $compND, $compSA, $urlPDF, $orig);
						if ($testDOI == "") {
							$ipc++;
							proxyCURL($cpt+1, 2000000, $ipc, $iMax, $iPro);
						}
					}
					
					//Si on a un point après le dernier slash du lien PDF, il doit être obligatoirement suivi de 'pdf', autrement, ne pas afficher le bouton Action
					//Recherche du dernier slash et extraction de la sous-chaîne
					$extUrlPDF = strrchr ($urlPDF, "/");
					//Tester la présence d'un point dans cette sous-chaîne, et, si c'est le cas, de la présence de '.pdf'
					if (strpos($extUrlPDF, ".") !== false && stripos($extUrlPDF, ".pdf") === false) {
						$evd = "noaction";
					}

					if ($urlPDF != "" && $arrayHAL["response"]["docs"][$cpt]["halId_s"] != "" && $evd != "noaction")//Un fichier PDF OA a été trouvé, le DOI est défini et la revue est OA
					{
						$urlPDF = htmlspecialchars($urlPDF);
						$halID = $arrayHAL["response"]["docs"][$cpt]["halId_s"];
						//$targetPDF = "./PDF/".$halID.".pdf";
						if (stripos($urlPDF, "https://hal") === false) {//Le fichier PDF n'est pas un fichier auteur
							genXMLPDF($halID, $doi, $targetPDF, $halID, $evd, $compNC, $compND, $compSA, $lienPDF, $urlPDF);
						}
					}
				}
			}

			$licEvd = "";
			$compCC = "";
			if ($evd == "greenPublisher") {$licEvd = "OA";}
			if ($evd == "publisherPaid") {$licEvd = "hybride";}
			if ($evd == "author") {$licEvd = "auteur";}
			if ($evd == "noaction") {$licEvd = "non OA";}
			$lienDOI = "";
			if ($doi != "") {
				$lienDOI = "<a target='_blank' href='https://doi.org/".$doi."'><img alt='DOI' src='./img/doi.jpg'></a>";
			}
			$textAff .= "<tr><td>".$cptTab."</td>";
			$textAff .= "<td>".$lienDOI."</td>";
			$textAff .= "<td>".$lienHAL."</td>";
			$textAff .= "<td>".$arrayHAL["response"]["docs"][$cpt]["producedDateY_i"]."</td>";
			$textAff .= "<td>".$arrayHAL["response"]["docs"][$cpt]["title_s"][0]."</td>";
			$textAff .= "<td>".$arrayHAL["response"]["docs"][$cpt]["authLastName_s"][0]."</td>";
			$textAff .= "<td>".$licEvd."</td>";
			$textAff .= "<td>".$compCC."</td>";
			if ($lienPDF == "" || $urlPDF == "") {
				$textAff .= "<td></td>";
			}else{
				if ($licEvd == "auteur") {//Si type doc "auteur", le bouton Action n'est activé que si on a cliqué sur "lien", i.e., on s'est assuré que le PDF était bien un manuscrit auteur
					$textAff .= "<td><a target='_blank' href='".$urlPDF."' onclick='condActOk(\"".$halID."\",\"".$lienPDF."\", \"MAJ_PDF\");'><img style='width: 50px;' src='./img/pdf_grand.png'></a></td>";
					$condAct = "ok";
				}else{
					$textAff .= "<td><a target='_blank' href='".$urlPDF."'><img style='width: 50px;' src='./img/pdf_grand.png'></a></td>";
				}
			}
			if (isset($arrayHAL["response"]["docs"][$cpt]["publisher_s"])) {
				$textAff .= "<td>".$arrayHAL["response"]["docs"][$cpt]["publisher_s"][0]."</td>";
			}else{
				$textAff .= "<td>&nbsp;</td>";
			}

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
			
			corrXML($xml);
								
			//Action 1 > Déposer
			//PDF trouvé avec oaDOI ?
			if ($lienPDF != "" && $evd != "noaction" && $urlPDF != "") {
				include "./CrossHAL_actions.php";
				$actMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_PDF",$tab)) {$actMaj = "no"; $lignAff = "ok";}
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
						//Recherche d'une éventuelle notice avec le même DOI ou le même titre dans HAL CRAC > PDF soumis en attente de validation
						if (isset($arrayHAL["response"]["docs"][$cpt]["doiId_s"])) {
							$reqCRAC = "https://api.archives-ouvertes.fr/crac/hal/?q=doiId_s:%22".$arrayHAL["response"]["docs"][$cpt]["doiId_s"]."%22%20AND%20status_i:%220%22&fl=submittedDate_s";
						}else{
							$reqCRAC = "https://api.archives-ouvertes.fr/crac/hal/?q=title_s:%22".$arrayHAL["response"]["docs"][$cpt]["title_s"][0]."%22%20AND%20status_i:%220%22&fl=submittedDate_s";
						}
						$reqCRAC = str_replace('"', '%22', $reqCRAC);
						$reqCRAC = str_replace(" ", "%20", $reqCRAC);
						//echo $reqCRAC;
						
						$contCRAC = file_get_contents($reqCRAC);
						//$contCRAC = utf8_encode($contCRAC);
						$resCRAC = json_decode($contCRAC);
						$numFCRAC = 0;
						if (isset($resCRAC->response->numFound)) {$numFCRAC = $resCRAC->response->numFound;}
						if ($numFCRAC != 0) {
							$textAff .= "<td><center><span id='maj".$halID."'><a href='#'><img alt='Le PDF a déjà été soumis à HAL' title='Le PDF a déjà été soumis à HAL' data-toggle=\"popover\" data-trigger='hover' data-content='En attente de traitement avant d’être mis en ligne, mais soumis à la validation de HAL' data-original-title='' style='width: 50px;' src='./img/dep_grand.png'></a></span></center></td>";
						}else{
							if ($condAct == "ok") {//Il y a une condition préalable au lancement de l'action
								$textAff .= "<td><center><span id='maj".$halID."'><img alt='Vérification nécessaire' title='Vérification nécessaire' data-toggle=\"popover\" data-trigger='hover' data-content='Par précaution, ce bouton Action ne sera activé que lorsque vous aurez vérifié via le lien ci-avant que le PDF est bien un manuscrit auteur' data-original-title='' style='width: 50px;' src='./img/addOK_grand.png'></span></center></td>";
							}else{
								$textAff .= "<td><center><span id='maj".$halID."'><a target='_blank' href='".$lienPDF."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_PDF\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span></center></td>";
							}
						}
					}else{
						$textAff .= "<center><a href='#'><img alt='Modification impossible' title='Modification impossible' data-toggle=\"popover\" data-trigger='hover' data-content='Dépôt sous embargo' data-original-title='' style='width: 50px;' src='./img/addEmbargo_grand.png'></a></center>";
					}
					$lignAff = "ok";
				}else{
					$textAff .= "<td><center><img style='width: 50px;' src='./img/addOK_grand.png'></center></td>";
				}
			}else{
				$textAff .= "<td>&nbsp;</td>";
			}
			//Action 2 > Parcourir
			$textAff .= "<td width='20%'>";
			if (($lienPDF == "" || $urlPDF == "") && $licEvd != "non OA") {
				$halID = $arrayHAL["response"]["docs"][$cpt]["halId_s"];
				$getHalID = "";
				if (isset($_GET["halID"])) {$getHalID = $_GET["halID"];}
				$iMinRet = $iMin - $increment;
				$iMaxRet = $iMax - $increment;
				if ($action == "3" && $halID == $getHalID) {
					$urlPDF = $urlServeur;
					$evd = "";
					if ($licEvd == "auteur") {$evd = "auteur";}
					if ($licEvd == "OA") {$evd = "greenPublisher";}
					if ($licEvd == "hybride") {$evd = "publisherPaid";}
					$compNC = "";
					$compND = "";
					$compSA = "";
					//echo $halID;
					if ($lienext == "oui") {//notice avec lien externe
						genXMLPDF($halID, $doi, $targetPDF, $halID, $evd, $compNC, $compND, $compSA, $lienPDF, $urlPDF3);
						include "./CrossHAL_actions.php";
						$actMaj = "ok";
						foreach($ACTIONS_LISTE as $tab) {
							if (in_array($halID, $tab) && in_array("MAJ_PDF",$tab)) {$actMaj = "no"; $lignAff = "ok";}
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
								//Recherche d'une éventuelle notice avec le même DOI ou le même titre dans HAL CRAC > PDF soumis en attente de validation
								if (isset($arrayHAL["response"]["docs"][$cpt]["doiId_s"])) {
									$reqCRAC = "https://api.archives-ouvertes.fr/crac/hal/?q=doiId_s:%22".$arrayHAL["response"]["docs"][$cpt]["doiId_s"]."%22%20AND%20status_i:%220%22&fl=submittedDate_s";
								}else{
									$reqCRAC = "https://api.archives-ouvertes.fr/crac/hal/?q=title_s:%22".$arrayHAL["response"]["docs"][$cpt]["title_s"][0]."%22%20AND%20status_i:%220%22&fl=submittedDate_s";
								}
								$reqCRAC = str_replace('"', '%22', $reqCRAC);
								$reqCRAC = str_replace(" ", "%20", $reqCRAC);
								//echo $reqCRAC;
								
								$contCRAC = file_get_contents($reqCRAC);
								//$contCRAC = utf8_encode($contCRAC);
								$resCRAC = json_decode($contCRAC);
								$numFCRAC = 0;
								if (isset($resCRAC->response->numFound)) {$numFCRAC = $resCRAC->response->numFound;}
								if ($numFCRAC != 0) {
									$textAff .= "<td><center><span id='maj".$halID."'><a href='#'><img alt='Le PDF a déjà été soumis à HAL' title='Le PDF a déjà été soumis à HAL' data-toggle=\"popover\" data-trigger='hover' data-content='En attente de traitement avant d’être mis en ligne, mais soumis à la validation de HAL' data-original-title='' style='width: 50px;' src='./img/dep_grand.png'></a></span></center></td>";
								}else{
									$textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienPDF."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_PDF\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span></center>";
								}
							}else{
								$textAff .= "<center><a href='#'><img alt='Modification impossible' title='Modification impossible' data-toggle=\"popover\" data-trigger='hover' data-content='Dépôt sous embargo' data-original-title='' style='width: 50px;' src='./img/addEmbargo_grand.png'></a></center>";
							}
							$lignAff = "ok";
						}else{
							$textAff .= "<center><img style='width: 50px;' src='./img/addOK_grand.png'></center>";
						}
					}else{//Notice sans lien externe > embargo à mettre en place
						//Utilisation détournée de paramètres de la fonction initiale pour l'inscription de l'embargo dans le TEI
						$evd = "noliene";
						$compNC = $embargo;
						genXMLPDF($halID, $doi, $targetPDF, $halID, $evd, $compNC, $compND, $compSA, $lienPDF, $urlPDF3);
						include "./CrossHAL_actions.php";
						$actMaj = "ok";
						foreach($ACTIONS_LISTE as $tab) {
							if (in_array($halID, $tab) && in_array("MAJ_PDF",$tab)) {$actMaj = "no"; $lignAff = "ok";}
						}
						if ($lienPDF == "noDateEpub") {
							$textAff .= "<center><a href='#'><img alt='Pas de dateEpub' title='Pas de dateEpub' data-toggle=\"popover\" data-trigger='hover' data-content='La date de publication en ligne n’est pas renseignée !' data-original-title='' style='width: 50px;' src='./img/addEmbargo_grand.png'></a></center>";
							$lignAff = "ok";
						}else{
							if ($actMaj == "ok") {
								//Recherche d'une éventuelle notice avec le même DOI ou le même titre dans HAL CRAC > PDF soumis en attente de validation
								if (isset($arrayHAL["response"]["docs"][$cpt]["doiId_s"])) {
									$reqCRAC = "https://api.archives-ouvertes.fr/crac/hal/?q=doiId_s:%22".$arrayHAL["response"]["docs"][$cpt]["doiId_s"]."%22%20AND%20status_i:%220%22&fl=submittedDate_s";
								}else{
									$reqCRAC = "https://api.archives-ouvertes.fr/crac/hal/?q=title_s:%22".$arrayHAL["response"]["docs"][$cpt]["title_s"][0]."%22%20AND%20status_i:%220%22&fl=submittedDate_s";
								}
								$reqCRAC = str_replace('"', '%22', $reqCRAC);
								$reqCRAC = str_replace(" ", "%20", $reqCRAC);
								//echo $reqCRAC;
								
								$contCRAC = file_get_contents($reqCRAC);
								//$contCRAC = utf8_encode($contCRAC);
								$resCRAC = json_decode($contCRAC);
								$numFCRAC = 0;
								if (isset($resCRAC->response->numFound)) {$numFCRAC = $resCRAC->response->numFound;}
								if ($numFCRAC != 0) {
									$textAff .= "<td><center><span id='maj".$halID."'><a href='#'><img alt='Le PDF a déjà été soumis à HAL' title='Le PDF a déjà été soumis à HAL' data-toggle=\"popover\" data-trigger='hover' data-content='En attente de traitement avant d’être mis en ligne, mais soumis à la validation de HAL' data-original-title='' style='width: 50px;' src='./img/dep_grand.png'></a></span></center></td>";
								}else{
									$textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienPDF."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_PDF\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' style='width: 50px;' src='./img/add_grand.png'></a></span></center>";
								}
								$lignAff = "ok";
							}else{
								$textAff .= "<center><img style='width: 50px;' src='./img/addOK_grand.png'></center>";
							}
						}
					}
				}else{
					if (stripos($urlPDF, "https://hal") === false) {//Le fichier PDF n'est pas un fichier auteur
						$lignAff = "ok";
						//$textAff .= "<div id='formFilePDF'></div>";
						$textAff .= "<form enctype='multipart/form-data' action='CrossHAL_PDF.php' method='post' accept-charset='UTF-8'>";
						$textAff .= "<input type='hidden' name='MAX_FILE_SIZE' value='10000000' />";
						$textAff .= "<label for='pdf_file'>Envoyez le fichier PDF (10 Mo max) :</label>";
						$textAff .= "<input class='form-control mb-2' id='pdf_file' name='pdf_file' type='file' />";
						$textAff .= "<input type='hidden' value='".$halID."' name='halID'>";
						$textAff .= "<input type='hidden' value='".$iMin."' name='iMin'>";
						$textAff .= "<input type='hidden' value='".$iMax."' name='iMax'>";
						$textAff .= "<input type='hidden' value='".$iMinRet."' name='iMinRet'>";
						$textAff .= "<input type='hidden' value='".$iMaxRet."' name='iMaxRet'>";
						$textAff .= "<input type='hidden' value='".$increment."' name='increment'>";
						$textAff .= "<input type='hidden' value='".$team."' name='team'>";
						$textAff .= "<input type='hidden' value='".$idhal."' name='idhal'>";
						$textAff .= "<input type='hidden' value='".$anneedeb."' name='anneedeb'>";
						$textAff .= "<input type='hidden' value='".$anneefin."' name='anneefin'>";
						$textAff .= "<input type='hidden' value='".$apa."' name='apa'>";
						$textAff .= "<input type='hidden' value='".$manuaut."' name='manuaut'>";
						$textAff .= "<input type='hidden' value='".$lienext."' name='lienext'>";
						$textAff .= "<input type='hidden' value='".$noliene."' name='noliene'>";
						$textAff .= "<input type='hidden' value='".$embargo."' name='embargo'>";
						$textAff .= "<input type='hidden' value='".$urlServeur."' name='urlServeur'>";
						$textAff .= "<input type='hidden' value='".$cptTab."' name='cptTab'>";
						$textAff .= "<input type='hidden' value='".$ordinv."' name='ordinv'>";
						$textAff .= "<input class='btn btn-info btn-sm' type='submit' value='Envoyer le fichier'>";
						$textAff .= "</form>";
					}
				}
			}
			$textAff .= "</td></tr>";
			//echo "<td></td></tr>";
		}else{//Présence d'un nom de fichier dans la notice ou DOI à exclure
			$lignAff = "ok";
			$textAff .= "<tr><td>".$cptTab."</td>";
			$lienDOI = "<a target='_blank' href='https://doi.org/".$doi."'><img alt='DOI' src='./img/doi.jpg'></a>";
			$textAff .= "<td>".$lienDOI."</td>";
			$textAff .= "<td>".$lienHAL."</td>";
			$textAff .= "<td>".$arrayHAL["response"]["docs"][$cpt]["producedDateY_i"]."</td>";
			$textAff .= "<td></td>";
			$textAff .= "<td></td>";
			$textAff .= "<td></td>";
			$textAff .= "<td></td>";
			if ($nodoi != "") {
				$textAff .= "<td>DOI à exclure</td>";
			}else{
				$textAff .= "<td>URL de fichier déjà mentionnée dans la notice : <a target='_blank' href='".$lienPDF."'><img style='width: 50px;' src='./img/pdf_grand.png'></a></td>";
			}
			if (isset($arrayHAL["response"]["docs"][$cpt]["publisher_s"])) {
				$textAff .= "<td>".$arrayHAL["response"]["docs"][$cpt]["publisher_s"][0]."</td>";
			}else{
				$textAff .= "<td>&nbsp;</td>";
			}
			$textAff .= "<td></td>";
			$textAff .= "<td></td></tr>";
		}
		if ($lignAff == "ok") {//Il y a au moins une correction à apporter > la ligne est à afficher
			echo $textAff;
			$cptAff++;
		}
	}
}
echo "</tbody></table><br>";
echo "<script>";
echo "  document.getElementById('cpt').style.display = \"none\";";
echo "</script>";

if ($iMax != $numFound) {
	echo "<form name='troli' id='etape3' action='CrossHAL.php' method='post'>";
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
	echo "<input type='hidden' value='".$lienext."' name='lienext'>";
	echo "<input type='hidden' value='".$noliene."' name='noliene'>";
	echo "<input type='hidden' value='".$embargo."' name='embargo'>";
	echo "<input type='hidden' value='".$urlServeur."' name='urlServeur'>";
	echo "<input type='hidden' value='Valider' name='valider'>";
	if ($iMinInit != 1) {
		echo "<input type='submit' class='btn btn-md btn-primary btn-sm' value='Retour' name='retour'>&nbsp;&nbsp;&nbsp;";
	}
	echo "<input type='submit' class='btn btn-md btn-primary btn-sm' value='Suite' name='suite'>";
	echo "</form><br>";
	//echo "<script>formFilePDF();</script>";
}else{
	echo "<form name='troli' id='etape3' action='CrossHAL.php' method='post'>";
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
	echo "<input type='hidden' value='".$urlServeur."' name='urlServeur'>";
	echo "<input type='hidden' value='Valider' name='valider'>";
	if ($iMaxRet != 0) {
		echo "<input type='submit' class='btn btn-md btn-primary btn-sm' value='Retour' name='retour'>";
	}
}
if ($cptAff == 0 && $iMax != $numFound) {//Auto-soumission du formulaire
	echo "<script>";
	echo "  document.getElementById(\"etape3\").submit(); ";
	echo "</script>";
}
//Fin étape 3a
?>