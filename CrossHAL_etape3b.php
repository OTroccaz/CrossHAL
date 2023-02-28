<?php
//Etape 3b > Manuscrit auteurs (via OverHAL)
include("./CrossHAL_Stats_overhal_mails_UR1.php");
//var_dump($Stats_OH_Mails);
echo "<div id='cpt'></div>";
echo "<table class='table table-responsive table-bordered table-centered table-sm text-center small'>";
echo "<thead class='thead-dark'>";
echo "<tr>";
echo "<th><strong>ID</strong></th>";
echo "<th><strong>Lien DOI</strong></th>";
echo "<th><strong>Lien HAL</strong></th>";
echo "<th><strong>DOI</strong></th>";
echo "<th><strong>Mails</strong></th>";
echo "<th><strong>Quand</strong></th>";
echo "<th><strong>Qui</strong></th>";
echo "<th><strong>OA</strong></th>";
echo "<th><strong>Type</strong></th>";
echo "<th><strong>Fwd</strong></th>";
echo "<th><strong>Action 1 > ADD</strong></th>";
echo "<th><strong>Action 2 > Parcourir</strong></th>";
echo "</tr></thead><tbody>";
for ($i = 0; $i < count($Stats_OH_Mails); $i++) {
	progression($i+1, count($Stats_OH_Mails), $iPro);
	$doi = str_replace(array("https://doi.org/", "https://dx.doi.org/"), "", $Stats_OH_Mails[$i]["Article"]);
	if ($doi != "" && ($Stats_OH_Mails[$i]["Type"] == "P" || $Stats_OH_Mails[$i]["Reponse"] == "MS")) {
		$reqAPI = "https://api.archives-ouvertes.fr/search/univ-rennes1/?fq=producedDateY_i:".$anneedeb."%20AND%20docType_s:(ART OR COUV)NOT%20UNDEFINED%20AND%20submitType_s:notice%20AND%20doiId_s:%22".$doi."%22&fl=halId_s,docid,contributorFullName_s,linkExtId_s,doiId_s,title_s";
		$reqAPI = str_replace('"', '%22', $reqAPI);
		$reqAPI = str_replace(" ", "%20", $reqAPI);
		//echo $reqAPI.'<br>';				
		askCurl($reqAPI, $arrayHAL);
		$numFound = $arrayHAL["response"]["numFound"];
		//echo 'toto : '.$numFound.'<br>';
		if ($numFound != 0) {
			echo "<tr>";
			$notice = $i+1;
			echo "<td style='text-align: center;'>".$notice."</td>";
			echo "<td style='text-align: center;'><a target='_blank' href='".$Stats_OH_Mails[$i]["Article"]."'><img title='DOI' src='./img/doi.jpg'></a></td>";
			$lienHAL = "https://hal-univ-rennes1.archives-ouvertes.fr/".$arrayHAL["response"]["docs"][0]["halId_s"];
			echo "<td style='text-align: center;'><a target='_blank' href='".$lienHAL."'><img title='HAL' src='./img/HAL.jpg'></a></td>";
			echo "<td style='text-align: center;'>".$doi."</td>";
			echo "<td style='text-align: center;'>".$Stats_OH_Mails[$i]["Destinataire"]."</td>";
			echo "<td style='text-align: center;'>".$Stats_OH_Mails[$i]["Quand"]."</td>";
			if (isset($arrayHAL["response"]["docs"][0]["halId_s"])) {$ctb = $arrayHAL["response"]["docs"][0]["contributorFullName_s"];}else{$ctb = "";}
			echo "<td style='text-align: center;'>".$ctb."</td>";
			if (isset($arrayHAL["response"]["docs"][0]["linkExtId_s"])) {$oa = $arrayHAL["response"]["docs"][0]["linkExtId_s"];}else{$oa = "";}
			echo "<td style='text-align: center;'>".$oa."</td>";
			if ($Stats_OH_Mails[$i]["Type"] == "P") {$type = "P";}else{$type = "MS";}
			echo "<td style='text-align: center;'>".$type."</td>";
			echo "<td style='text-align: center;'>".$Stats_OH_Mails[$i]["Forward"]."</td>";
			//Action 1 > ADD
			//Recherche d'une éventuelle notice avec le même DOI ou le même titre dans HAL CRAC > PDF soumis en attente de validation
			if (isset($arrayHAL["response"]["docs"][0]["doiId_s"])) {
				$reqCRAC = "https://api.archives-ouvertes.fr/crac/hal/?q=doiId_s:%22".$arrayHAL["response"]["docs"][0]["doiId_s"]."%22%20AND%20status_i:%220%22&fl=submittedDate_s";
			}else{
				$reqCRAC = "https://api.archives-ouvertes.fr/crac/hal/?q=title_s:%22".$arrayHAL["response"]["docs"][0]["title_s"][0]."%22%20AND%20status_i:%220%22&fl=submittedDate_s";
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
				$actADD = "<a href='#'><img alt='Le PDF a déjà été soumis à HAL' title='Le PDF a déjà été soumis à HAL' data-toggle=\"popover\" data-trigger='hover' data-content='En attente de traitement avant d’être mis en ligne, mais soumis à la validation de HAL' data-original-title='' style='width: 50px;' src='./img/dep_grand.png'></a>";
			}else{
				$actADD = "<a target='_blank' href='https://hal-univ-rennes1.archives-ouvertes.fr/submit/addfile/docid/".$arrayHAL["response"]["docs"][0]["docid"]."'><img alt='Add paper' title='Add paper' style='width: 50px;' src='./img/add_grand.png'></a>";
			}
			echo "<td style='text-align: center;'>".$actADD."</td>";
			//Action 2 > Parcourir
			$textAff = "<td width='20%'>";
			$halID = $arrayHAL["response"]["docs"][0]["halId_s"];
			$getHalID = "";
			if (isset($_GET["halID"])) {$getHalID = $_GET["halID"];}
			if ($action == "3" && $halID == $getHalID) {
				$urlPDF = $urlServeur;
				$compND = "";
				$compSA = "";
				//Utilisation détournée de paramètres de la fonction initiale pour l'inscription de l'embargo dans le TEI
				$evd = "noliene";
				$compNC = "6mois";
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
			}else{
				$lignAff = "ok";
				//$textAff .= "<div id='formFilePDF'></div>";
				$textAff .= "<form enctype='multipart/form-data' action='CrossHAL_PDF.php' method='post' accept-charset='UTF-8'>";
				$textAff .= "<input type='hidden' name='MAX_FILE_SIZE' value='10000000' />";
				$textAff .= "<label for='pdf_file'>Envoyez le fichier PDF (10 Mo max) :</label>";
				$textAff .= "<input class='form-control mb-2' id='pdf_file' name='pdf_file' type='file' />";
				$textAff .= "<input type='hidden' value='".$halID."' name='halID'>";
				$textAff .= "<input type='hidden' value='' name='iMin'>";
				$textAff .= "<input type='hidden' value='' name='iMax'>";
				$textAff .= "<input type='hidden' value='' name='iMinRet'>";
				$textAff .= "<input type='hidden' value='' name='iMaxRet'>";
				$textAff .= "<input type='hidden' value='' name='increment'>";
				$textAff .= "<input type='hidden' value='".$team."' name='team'>";
				$textAff .= "<input type='hidden' value='' name='idhal'>";
				$textAff .= "<input type='hidden' value='".$anneedeb."' name='anneedeb'>";
				$textAff .= "<input type='hidden' value='' name='anneefin'>";
				$textAff .= "<input type='hidden' value='' name='apa'>";
				$textAff .= "<input type='hidden' value='".$manuautOH."' name='manuautOH'>";
				$textAff .= "<input type='hidden' value='' name='lienext'>";
				$textAff .= "<input type='hidden' value='' name='noliene'>";
				$textAff .= "<input type='hidden' value='' name='embargo'>";
				$textAff .= "<input type='hidden' value='' name='urlServeur'>";
				$textAff .= "<input type='hidden' value='' name='cptTab'>";
				$textAff .= "<input class='btn btn-info btn-sm' type='submit' value='Envoyer le fichier'>";
				$textAff .= "</form>";
			}
			$textAff .= "</td>";
			echo $textAff;
			
			
			echo "</tr>";
			ob_flush();
			flush();
			ob_flush();
			flush();
		}
	}
}
echo "</tbody></table>";
echo "<script>";
echo "document.getElementById('cpt').style.display = 'none'";
echo "</script>";
//Fin étape 3b
?>