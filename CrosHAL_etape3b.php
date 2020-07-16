<?php
//Etape 3b > Manuscrit auteurs (via OverHAL)
include("./Stats-overhal-mails-UR1.php");
//var_dump($Stats_OH_Mails);
echo "<div id='cpt'></div>";
echo "<table class='table table-striped table-bordered table-hover;'>";
echo "<tr>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>ID</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Lien DOI</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Lien HAL</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>DOI</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Mails</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Quand</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Qui</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>OA</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Type</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Fwd</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Action 1 > ADD</strong></td>";
echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Action 2 > Parcourir</strong></td>";
echo "</tr>";
for ($i = 0; $i < count($Stats_OH_Mails); $i++) {
	progression($i+1, count($Stats_OH_Mails), $iPro);
	$doi = str_replace(array("https://doi.org/", "https://dx.doi.org/"), "", $Stats_OH_Mails[$i]["Article"]);
	if ($doi != "" && ($Stats_OH_Mails[$i]["Type"] == "P" || $Stats_OH_Mails[$i]["Reponse"] == "MS")) {
		$reqAPI = "https://api.archives-ouvertes.fr/search/univ-rennes1/?fq=producedDateY_i:".$anneedeb."%20AND%20docType_s:(ART OR COUV)%20AND%20submitType_s:notice%20AND%20doiId_s:%22".$doi."%22&fl=halId_s,docid,contributorFullName_s,linkExtId_s";
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
			$actADD = "<a target='_blank' href='https://hal-univ-rennes1.archives-ouvertes.fr/submit/addfile/docid/".$arrayHAL["response"]["docs"][0]["docid"]."'><img alt='Add paper' title='Add paper' src='./img/add.png'></a>";
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
				include "./CrosHAL_actions.php";
				$actMaj = "ok";
				foreach($ACTIONS_LISTE as $tab) {
					if (in_array($halID, $tab) && in_array("MAJ_PDF",$tab)) {$actMaj = "no"; $lignAff = "ok";}
				}
				if ($lienPDF == "noDateEpub") {
					$textAff .= "<center><img alt='Pas de dateEpub' title=\"La date de publication en ligne n'est pas renseignée !\" src='./img/MAJEmbargo.png'></center>";
					$lignAff = "ok";
				}else{
					if ($actMaj == "ok") {
						$textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienPDF."' onclick='$.post(\"CrosHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_PDF\" });majok(\"".$halID."\"); majokVu(\"".$halID."\");'><img alt='MAJ' src='./img/MAJ.png'></a></span></center>";
						$lignAff = "ok";
					}else{
						$textAff .= "<center><img src='./img/MAJOK.png'></center>";
					}
				}
			}else{
				$lignAff = "ok";
				//$textAff .= "<div id='formFilePDF'></div>";
				$textAff .= "<form enctype='multipart/form-data' action='CrosHALPDF.php' method='post' accept-charset='UTF-8'>";
				$textAff .= "<input type='hidden' name='MAX_FILE_SIZE' value='10000000' />";
				$textAff .= "<p class='form-inline'><label for='pdf_file'>Envoyez le fichier PDF (10 Mo max) :</label><br />";
				$textAff .= "<input class='form-control' style='font-size:90%; height:25px; padding: 0px;' id='pdf_file' name='pdf_file' type='file' /><br />";
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
				$textAff .= "<input class='form-control btn btn-md btn-primary' style='height: 25px; padding: 0px; width: 130px;'type='submit' value='Envoyer le fichier'>";
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
echo "</table>";
echo "<script>";
echo "document.getElementById('cpt').style.display = 'none'";
echo "</script>";
//Fin étape 3b
?>