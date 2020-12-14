																				<?php
																				//Formulaire
																				//$urlServeur = "";
																				//if (isset($_POST["verifDOI"])) {
																				if (!isset($_GET["noliene"])) {$noliene = "";}
																				if (isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) {
																					$team = htmlspecialchars($_POST["team"]);
																					$idhal = htmlspecialchars($_POST["idhal"]);
																					$anneedeb = htmlspecialchars($_POST["anneedeb"]);
																					$anneefin = htmlspecialchars($_POST["anneefin"]);
																					if (isset($_POST["apa"]) && $_POST["apa"] == "oui") {$apa = "oui";}else{$apa = "non";}
																					if (isset($_POST["ordinv"]) && $_POST["ordinv"] == "oui") {$ordinv = "oui";}else{$ordinv = "non";}
																					if (!isset($increment)) {$increment = htmlspecialchars($_POST["increment"]);}
																					$opt1 = "non";
																					$opt2 = "non";
																					$opt3 = "non";
																					//option 1
																					if (isset($_POST["chkall"]) && $_POST["chkall"] == "oui") {$chkall = htmlspecialchars($_POST["chkall"]);$opt1 = "oui";}else{$chkall = "non";}
																					if (isset($_POST["doiCrossRef"]) && $_POST["doiCrossRef"] == "oui") {$doiCrossRef = htmlspecialchars($_POST["doiCrossRef"]);$opt1 = "oui";}else{$doiCrossRef = "non";}
																					if (isset($_POST["revue"]) && $_POST["revue"] == "oui") {$revue = htmlspecialchars($_POST["revue"]);$opt1 = "oui";}else{$revue = "non";}
																					if (isset($_POST["vnp"]) && $_POST["vnp"] == "oui") {$vnp = htmlspecialchars($_POST["vnp"]);$opt1 = "oui";}else{$vnp = "non";}
																					if (isset($_POST["lanCrossRef"]) && $_POST["lanCrossRef"] == "oui") {$lanCrossRef = htmlspecialchars($_POST["lanCrossRef"]);$opt1 = "oui";}else{$lanCrossRef = "non";}
																					if (isset($_POST["financement"]) && $_POST["financement"] == "oui") {$financement = htmlspecialchars($_POST["financement"]);$opt1 = "oui";}else{$financement = "non";}
																					if (isset($_POST["anr"]) && $_POST["anr"] == "oui") {$anr = htmlspecialchars($_POST["anr"]);$opt1 = "oui";}else{$anr = "non";}
																					if (isset($_POST["anneepub"]) && $_POST["anneepub"] == "oui") {$anneepub = htmlspecialchars($_POST["anneepub"]);$opt1 = "oui";}else{$anneepub = "non";}
																					if (isset($_POST["mel"]) && $_POST["mel"] == "oui") {$mel = htmlspecialchars($_POST["mel"]);$opt1 = "oui";}else{$mel = "non";}
																					
																					if (isset($_POST["ccTitconf"]) && $_POST["ccTitconf"] == "oui") {$ccTitconf = htmlspecialchars($_POST["ccTitconf"]);$opt1 = "oui";}else{$ccTitconf = "non";}
																					if (isset($_POST["ccPays"]) && $_POST["ccPays"] == "oui") {$ccPays = htmlspecialchars($_POST["ccPays"]);$opt1 = "oui";}else{$ccPays = "non";}
																					if (isset($_POST["ccDatedeb"]) && $_POST["ccDatedeb"] == "oui") {$ccDatedeb = htmlspecialchars($_POST["ccDatedeb"]);$opt1 = "oui";}else{$ccDatedeb = "non";}
																					if (isset($_POST["ccDatefin"]) && $_POST["ccDatefin"] == "oui") {$ccDatefin = htmlspecialchars($_POST["ccDatefin"]);$opt1 = "oui";}else{$ccDatefin = "non";}
																					if (isset($_POST["ccISBN"]) && $_POST["ccISBN"] == "oui") {$ccISBN = htmlspecialchars($_POST["ccISBN"]);$opt1 = "oui";}else{$ccISBN = "non";}
																					if (isset($_POST["ccTitchap"]) && $_POST["ccTitchap"] == "oui") {$ccTitchap = htmlspecialchars($_POST["ccTitchap"]);$opt1 = "oui";}else{$ccTitchap = "non";}
																					if (isset($_POST["ccTitlivr"]) && $_POST["ccTitlivr"] == "oui") {$ccTitlivr = htmlspecialchars($_POST["ccTitlivr"]);$opt1 = "oui";}else{$ccTitlivr = "non";}
																					if (isset($_POST["ccEditcom"]) && $_POST["ccEditcom"] == "oui") {$ccEditcom = htmlspecialchars($_POST["ccEditcom"]);$opt1 = "oui";}else{$ccEditcom = "non";}
																					
																					//if (isset($_POST["mocCrossRef"]) && $_POST["mocCrossRef"] == "oui") {$mocCrossRef = htmlspecialchars($_POST["mocCrossRef"]);$opt1 = "oui";}else{$mocCrossRef = "non";}
																					if (isset($_POST["absPubmed"]) && $_POST["absPubmed"] == "oui") {$absPubmed = htmlspecialchars($_POST["absPubmed"]);$opt1 = "oui";}else{$absPubmed = "non";}
																					if (isset($_POST["lanPubmed"]) && $_POST["lanPubmed"] == "oui") {$lanPubmed = htmlspecialchars($_POST["lanPubmed"]);$opt1 = "oui";}else{$lanPubmed = "non";}
																					if (isset($_POST["mocPubmed"]) && $_POST["mocPubmed"] == "oui") {$mocPubmed = htmlspecialchars($_POST["mocPubmed"]);$opt1 = "oui";}else{$mocPubmed = "non";}
																					if (isset($_POST["pmid"]) && $_POST["pmid"] == "oui") {$pmid = htmlspecialchars($_POST["pmid"]);$opt1 = "oui";}else{$pmid = "non";}
																					if (isset($_POST["pmcid"]) && $_POST["pmcid"] == "oui") {$pmcid = htmlspecialchars($_POST["pmcid"]);$opt1 = "oui";}else{$pmcid = "non";}
																					
																					if (isset($_POST["absISTEX"]) && $_POST["absISTEX"] == "oui") {$absISTEX = htmlspecialchars($_POST["absISTEX"]);$opt1 = "oui";}else{$absISTEX = "non";}
																					if (isset($_POST["lanISTEX"]) && $_POST["lanISTEX"] == "oui") {$lanISTEX = htmlspecialchars($_POST["lanISTEX"]);$opt1 = "oui";}else{$lanISTEX = "non";}
																					if (isset($_POST["mocISTEX"]) && $_POST["mocISTEX"] == "oui") {$mocISTEX = htmlspecialchars($_POST["mocISTEX"]);$opt1 = "oui";}else{$mocISTEX = "non";}
																					if (isset($_POST["DOIComm"]) && $_POST["DOIComm"] == "oui") {$DOIComm = htmlspecialchars($_POST["DOIComm"]);$opt1 = "oui";}else{$DOIComm = "non";}
																					if (isset($_POST["PoPeer"]) && $_POST["PoPeer"] == "oui") {$PoPeer = htmlspecialchars($_POST["PoPeer"]);$opt1 = "oui";}else{$PoPeer = "non";}

																					//option 2
																					if (isset($_POST["ordAut"]) && $_POST["ordAut"] == "oui") {$ordAut = htmlspecialchars($_POST["ordAut"]);$opt2 = "oui";}else{$ordAut = "non";}
																					if (isset($_POST["iniPre"]) && $_POST["iniPre"] == "oui") {$iniPre = htmlspecialchars($_POST["iniPre"]);$opt2 = "oui";}else{$iniPre = "non";}
																					if (isset($_POST["vIdHAL"]) && $_POST["vIdHAL"] == "oui") {$vIdHAL = htmlspecialchars($_POST["vIdHAL"]);$opt2 = "oui";}else{$vIdHAL = "non";}
																					if (isset($_POST["rIdHAL"]) && $_POST["rIdHAL"] == "oui") {$rIdHAL = htmlspecialchars($_POST["rIdHAL"]);$opt2 = "oui";}else{$rIdHAL = "non";}
																					if (isset($_POST["ctrTrs"]) && $_POST["ctrTrs"] == "oui") {$ctrTrs = htmlspecialchars($_POST["ctrTrs"]);$opt2 = "oui";}else{$ctrTrs = "non";}
																					if (isset($_POST["rIdHALArt"]) && $_POST["rIdHALArt"] == "oui") {$rIdHALArt = htmlspecialchars($_POST["rIdHALArt"]);$opt2 = "oui";}else{$rIdHALArt = "non";}
																					if (isset($_POST["rIdHALCom"]) && $_POST["rIdHALCom"] == "oui") {$rIdHALCom = htmlspecialchars($_POST["rIdHALCom"]);$opt2 = "oui";}else{$rIdHALCom = "non";}
																					if (isset($_POST["rIdHALCou"]) && $_POST["rIdHALCou"] == "oui") {$rIdHALCou = htmlspecialchars($_POST["rIdHALCou"]);$opt2 = "oui";}else{$rIdHALCou = "non";}
																					if (isset($_POST["rIdHALOuv"]) && $_POST["rIdHALOuv"] == "oui") {$rIdHALOuv = htmlspecialchars($_POST["rIdHALOuv"]);$opt2 = "oui";}else{$rIdHALOuv = "non";}
																					if (isset($_POST["rIdHALDou"]) && $_POST["rIdHALDou"] == "oui") {$rIdHALDou = htmlspecialchars($_POST["rIdHALDou"]);$opt2 = "oui";}else{$rIdHALDou = "non";}
																					if (isset($_POST["rIdHALBre"]) && $_POST["rIdHALBre"] == "oui") {$rIdHALBre = htmlspecialchars($_POST["rIdHALBre"]);$opt2 = "oui";}else{$rIdHALBre = "non";}
																					if (isset($_POST["rIdHALRap"]) && $_POST["rIdHALRap"] == "oui") {$rIdHALRap = htmlspecialchars($_POST["rIdHALRap"]);$opt2 = "oui";}else{$rIdHALRap = "non";}
																					if (isset($_POST["rIdHALThe"]) && $_POST["rIdHALThe"] == "oui") {$rIdHALThe = htmlspecialchars($_POST["rIdHALThe"]);$opt2 = "oui";}else{$rIdHALThe = "non";}
																					if (isset($_POST["rIdHALPre"]) && $_POST["rIdHALPre"] == "oui") {$rIdHALPre = htmlspecialchars($_POST["rIdHALPre"]);$opt2 = "oui";}else{$rIdHALPre = "non";}
																					if (isset($_POST["rIdHALPub"]) && $_POST["rIdHALPub"] == "oui") {$rIdHALPub = htmlspecialchars($_POST["rIdHALPub"]);$opt2 = "oui";}else{$rIdHALPub = "non";}
																					//option 3
																					if (isset($_POST["manuaut"]) && $_POST["manuaut"] == "oui") {$manuaut = htmlspecialchars($_POST["manuaut"]);$opt3 = "oui";}else{$manuaut = "non";}
																					if (isset($_POST["lienext"]) && $_POST["lienext"] == "oui") {$lienext = htmlspecialchars($_POST["lienext"]);$opt3 = "oui";}else{$lienext = "non";}
																					if (isset($_POST["noliene"]) && $_POST["noliene"] == "oui") {$noliene = htmlspecialchars($_POST["noliene"]);$opt3 = "oui";}else{$noliene = "non";}
																					if (isset($_POST["manuautOH"]) && $_POST["manuautOH"] == "oui") {$manuautOH = htmlspecialchars($_POST["manuautOH"]);$opt3 = "oui";}else{$manuautOH = "non";}
																					if (isset($_POST["manuautNR"]) && $_POST["manuautNR"] == "oui") {$manuautNR = htmlspecialchars($_POST["manuautNR"]);$opt3 = "oui";}else{$manuautNR = "non";}
																					$embargo = "";
																					if (isset($_POST["embargo"]) && $_POST["embargo"] == "6mois") {$embargo = "6mois";$opt3 = "oui";}
																					if (isset($_POST["embargo"]) && $_POST["embargo"] == "12mois") {$embargo = "12mois";$opt3 = "oui";}
																					if (isset($_POST["urlServeur"])) {$urlServeur = htmlspecialchars($_POST["urlServeur"]);}
																					$iMin = 0;
																					if (isset($_POST["valider"]) || isset($_POST["suite"])) {
																						if (isset($_POST["iMin"])) {$iMin = htmlspecialchars($_POST["iMin"]);}
																						if (isset($_POST["iMax"])) {$iMax = htmlspecialchars($_POST["iMax"]);}
																					}
																					if (isset($_POST["retour"])) {
																						$iMin = htmlspecialchars($_POST["iMinRet"]);
																						$iMax = htmlspecialchars($_POST["iMaxRet"]);
																					}
																				}
																				if (!isset($_POST["valider"]) && !isset($_POST["apa"])) {
																					$apa = "oui";
																				}
																				if (isset($opt1) && $opt1 == "oui" && $increment >= 10) {$increment = 10;}
																				if (isset($_POST["valider"])) {
																					$iMax = $iMin + $increment - 1;
																					$iMinRet = $iMin;
																					$iMaxRet = $iMax;
																				}
																				if (isset($team) && $team != "") {$team1 = $team; $team2 = $team;}else{$team1 = "Entrez le code de votre collection"; $team2 = "";}
																				?>
																				<form method="POST" accept-charset="utf-8" name="crosshal" action="CrossHAL.php" class="form-horizontal" onsubmit="return verif();">
                                            <div class="form-group row mb-1">
                                                <label for="team" class="col-12 col-md-3 col-form-label font-weight-bold">
                                                Code collection HAL
                                                </label>
                                                
                                                <div class="col-12 col-md-9">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <button type="button" tabindex="0" class="btn btn-info" data-html="true" data-toggle="popover" data-trigger="focus" title="" data-content='Code visible dans l&apos;URL d&apos;une collection.
                                            Exemple IPR-MOL est le code de la collection http://hal.archives-ouvertes.fr/ <span class="font-weight-bold">IPR-PMOL</span> de l&apos;équipe Physique moléculaire de l&apos;unité IPR UMR CNRS 6251' data-original-title="">
                                                            <i class="mdi mdi-comment-question text-white"></i>
                                                            </button>
                                                        </div>

																												<?php
																												//Formulaire
																												//$urlServeur = "";
																												//if (isset($_POST["verifDOI"])) {
																												if (!isset($_GET["noliene"])) {$noliene = "";}
																												if (isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) {
																													$team = htmlspecialchars($_POST["team"]);
																													$idhal = htmlspecialchars($_POST["idhal"]);
																													$anneedeb = htmlspecialchars($_POST["anneedeb"]);
																													$anneefin = htmlspecialchars($_POST["anneefin"]);
																													if (isset($_POST["apa"]) && $_POST["apa"] == "oui") {$apa = "oui";}else{$apa = "non";}
																													if (isset($_POST["ordinv"]) && $_POST["ordinv"] == "oui") {$ordinv = "oui";}else{$ordinv = "non";}
																													if (!isset($increment)) {$increment = htmlspecialchars($_POST["increment"]);}
																													$opt1 = "non";
																													$opt2 = "non";
																													$opt3 = "non";
																													//option 1
																													if (isset($_POST["chkall"]) && $_POST["chkall"] == "oui") {$chkall = htmlspecialchars($_POST["chkall"]);$opt1 = "oui";}else{$chkall = "non";}
																													if (isset($_POST["doiCrossRef"]) && $_POST["doiCrossRef"] == "oui") {$doiCrossRef = htmlspecialchars($_POST["doiCrossRef"]);$opt1 = "oui";}else{$doiCrossRef = "non";}
																													if (isset($_POST["revue"]) && $_POST["revue"] == "oui") {$revue = htmlspecialchars($_POST["revue"]);$opt1 = "oui";}else{$revue = "non";}
																													if (isset($_POST["vnp"]) && $_POST["vnp"] == "oui") {$vnp = htmlspecialchars($_POST["vnp"]);$opt1 = "oui";}else{$vnp = "non";}
																													if (isset($_POST["lanCrossRef"]) && $_POST["lanCrossRef"] == "oui") {$lanCrossRef = htmlspecialchars($_POST["lanCrossRef"]);$opt1 = "oui";}else{$lanCrossRef = "non";}
																													if (isset($_POST["financement"]) && $_POST["financement"] == "oui") {$financement = htmlspecialchars($_POST["financement"]);$opt1 = "oui";}else{$financement = "non";}
																													if (isset($_POST["anr"]) && $_POST["anr"] == "oui") {$anr = htmlspecialchars($_POST["anr"]);$opt1 = "oui";}else{$anr = "non";}
																													if (isset($_POST["anneepub"]) && $_POST["anneepub"] == "oui") {$anneepub = htmlspecialchars($_POST["anneepub"]);$opt1 = "oui";}else{$anneepub = "non";}
																													if (isset($_POST["mel"]) && $_POST["mel"] == "oui") {$mel = htmlspecialchars($_POST["mel"]);$opt1 = "oui";}else{$mel = "non";}
																													
																													if (isset($_POST["ccTitconf"]) && $_POST["ccTitconf"] == "oui") {$ccTitconf = htmlspecialchars($_POST["ccTitconf"]);$opt1 = "oui";}else{$ccTitconf = "non";}
																													if (isset($_POST["ccPays"]) && $_POST["ccPays"] == "oui") {$ccPays = htmlspecialchars($_POST["ccPays"]);$opt1 = "oui";}else{$ccPays = "non";}
																													if (isset($_POST["ccDatedeb"]) && $_POST["ccDatedeb"] == "oui") {$ccDatedeb = htmlspecialchars($_POST["ccDatedeb"]);$opt1 = "oui";}else{$ccDatedeb = "non";}
																													if (isset($_POST["ccDatefin"]) && $_POST["ccDatefin"] == "oui") {$ccDatefin = htmlspecialchars($_POST["ccDatefin"]);$opt1 = "oui";}else{$ccDatefin = "non";}
																													if (isset($_POST["ccISBN"]) && $_POST["ccISBN"] == "oui") {$ccISBN = htmlspecialchars($_POST["ccISBN"]);$opt1 = "oui";}else{$ccISBN = "non";}
																													if (isset($_POST["ccTitchap"]) && $_POST["ccTitchap"] == "oui") {$ccTitchap = htmlspecialchars($_POST["ccTitchap"]);$opt1 = "oui";}else{$ccTitchap = "non";}
																													if (isset($_POST["ccTitlivr"]) && $_POST["ccTitlivr"] == "oui") {$ccTitlivr = htmlspecialchars($_POST["ccTitlivr"]);$opt1 = "oui";}else{$ccTitlivr = "non";}
																													if (isset($_POST["ccEditcom"]) && $_POST["ccEditcom"] == "oui") {$ccEditcom = htmlspecialchars($_POST["ccEditcom"]);$opt1 = "oui";}else{$ccEditcom = "non";}
																													
																													//if (isset($_POST["mocCrossRef"]) && $_POST["mocCrossRef"] == "oui") {$mocCrossRef = htmlspecialchars($_POST["mocCrossRef"]);$opt1 = "oui";}else{$mocCrossRef = "non";}
																													if (isset($_POST["absPubmed"]) && $_POST["absPubmed"] == "oui") {$absPubmed = htmlspecialchars($_POST["absPubmed"]);$opt1 = "oui";}else{$absPubmed = "non";}
																													if (isset($_POST["lanPubmed"]) && $_POST["lanPubmed"] == "oui") {$lanPubmed = htmlspecialchars($_POST["lanPubmed"]);$opt1 = "oui";}else{$lanPubmed = "non";}
																													if (isset($_POST["mocPubmed"]) && $_POST["mocPubmed"] == "oui") {$mocPubmed = htmlspecialchars($_POST["mocPubmed"]);$opt1 = "oui";}else{$mocPubmed = "non";}
																													if (isset($_POST["pmid"]) && $_POST["pmid"] == "oui") {$pmid = htmlspecialchars($_POST["pmid"]);$opt1 = "oui";}else{$pmid = "non";}
																													if (isset($_POST["pmcid"]) && $_POST["pmcid"] == "oui") {$pmcid = htmlspecialchars($_POST["pmcid"]);$opt1 = "oui";}else{$pmcid = "non";}
																													
																													if (isset($_POST["absISTEX"]) && $_POST["absISTEX"] == "oui") {$absISTEX = htmlspecialchars($_POST["absISTEX"]);$opt1 = "oui";}else{$absISTEX = "non";}
																													if (isset($_POST["lanISTEX"]) && $_POST["lanISTEX"] == "oui") {$lanISTEX = htmlspecialchars($_POST["lanISTEX"]);$opt1 = "oui";}else{$lanISTEX = "non";}
																													if (isset($_POST["mocISTEX"]) && $_POST["mocISTEX"] == "oui") {$mocISTEX = htmlspecialchars($_POST["mocISTEX"]);$opt1 = "oui";}else{$mocISTEX = "non";}
																													if (isset($_POST["DOIComm"]) && $_POST["DOIComm"] == "oui") {$DOIComm = htmlspecialchars($_POST["DOIComm"]);$opt1 = "oui";}else{$DOIComm = "non";}
																													if (isset($_POST["PoPeer"]) && $_POST["PoPeer"] == "oui") {$PoPeer = htmlspecialchars($_POST["PoPeer"]);$opt1 = "oui";}else{$PoPeer = "non";}

																													//option 2
																													if (isset($_POST["ordAut"]) && $_POST["ordAut"] == "oui") {$ordAut = htmlspecialchars($_POST["ordAut"]);$opt2 = "oui";}else{$ordAut = "non";}
																													if (isset($_POST["iniPre"]) && $_POST["iniPre"] == "oui") {$iniPre = htmlspecialchars($_POST["iniPre"]);$opt2 = "oui";}else{$iniPre = "non";}
																													if (isset($_POST["vIdHAL"]) && $_POST["vIdHAL"] == "oui") {$vIdHAL = htmlspecialchars($_POST["vIdHAL"]);$opt2 = "oui";}else{$vIdHAL = "non";}
																													if (isset($_POST["rIdHAL"]) && $_POST["rIdHAL"] == "oui") {$rIdHAL = htmlspecialchars($_POST["rIdHAL"]);$opt2 = "oui";}else{$rIdHAL = "non";}
																													if (isset($_POST["ctrTrs"]) && $_POST["ctrTrs"] == "oui") {$ctrTrs = htmlspecialchars($_POST["ctrTrs"]);$opt2 = "oui";}else{$ctrTrs = "non";}
																													if (isset($_POST["rIdHALArt"]) && $_POST["rIdHALArt"] == "oui") {$rIdHALArt = htmlspecialchars($_POST["rIdHALArt"]);$opt2 = "oui";}else{$rIdHALArt = "non";}
																													if (isset($_POST["rIdHALCom"]) && $_POST["rIdHALCom"] == "oui") {$rIdHALCom = htmlspecialchars($_POST["rIdHALCom"]);$opt2 = "oui";}else{$rIdHALCom = "non";}
																													if (isset($_POST["rIdHALCou"]) && $_POST["rIdHALCou"] == "oui") {$rIdHALCou = htmlspecialchars($_POST["rIdHALCou"]);$opt2 = "oui";}else{$rIdHALCou = "non";}
																													if (isset($_POST["rIdHALOuv"]) && $_POST["rIdHALOuv"] == "oui") {$rIdHALOuv = htmlspecialchars($_POST["rIdHALOuv"]);$opt2 = "oui";}else{$rIdHALOuv = "non";}
																													if (isset($_POST["rIdHALDou"]) && $_POST["rIdHALDou"] == "oui") {$rIdHALDou = htmlspecialchars($_POST["rIdHALDou"]);$opt2 = "oui";}else{$rIdHALDou = "non";}
																													if (isset($_POST["rIdHALBre"]) && $_POST["rIdHALBre"] == "oui") {$rIdHALBre = htmlspecialchars($_POST["rIdHALBre"]);$opt2 = "oui";}else{$rIdHALBre = "non";}
																													if (isset($_POST["rIdHALRap"]) && $_POST["rIdHALRap"] == "oui") {$rIdHALRap = htmlspecialchars($_POST["rIdHALRap"]);$opt2 = "oui";}else{$rIdHALRap = "non";}
																													if (isset($_POST["rIdHALThe"]) && $_POST["rIdHALThe"] == "oui") {$rIdHALThe = htmlspecialchars($_POST["rIdHALThe"]);$opt2 = "oui";}else{$rIdHALThe = "non";}
																													if (isset($_POST["rIdHALPre"]) && $_POST["rIdHALPre"] == "oui") {$rIdHALPre = htmlspecialchars($_POST["rIdHALPre"]);$opt2 = "oui";}else{$rIdHALPre = "non";}
																													if (isset($_POST["rIdHALPub"]) && $_POST["rIdHALPub"] == "oui") {$rIdHALPub = htmlspecialchars($_POST["rIdHALPub"]);$opt2 = "oui";}else{$rIdHALPub = "non";}
																													//option 3
																													if (isset($_POST["manuaut"]) && $_POST["manuaut"] == "oui") {$manuaut = htmlspecialchars($_POST["manuaut"]);$opt3 = "oui";}else{$manuaut = "non";}
																													if (isset($_POST["lienext"]) && $_POST["lienext"] == "oui") {$lienext = htmlspecialchars($_POST["lienext"]);$opt3 = "oui";}else{$lienext = "non";}
																													if (isset($_POST["noliene"]) && $_POST["noliene"] == "oui") {$noliene = htmlspecialchars($_POST["noliene"]);$opt3 = "oui";}else{$noliene = "non";}
																													if (isset($_POST["manuautOH"]) && $_POST["manuautOH"] == "oui") {$manuautOH = htmlspecialchars($_POST["manuautOH"]);$opt3 = "oui";}else{$manuautOH = "non";}
																													if (isset($_POST["manuautNR"]) && $_POST["manuautNR"] == "oui") {$manuautNR = htmlspecialchars($_POST["manuautNR"]);$opt3 = "oui";}else{$manuautNR = "non";}
																													$embargo = "";
																													if (isset($_POST["embargo"]) && $_POST["embargo"] == "6mois") {$embargo = "6mois";$opt3 = "oui";}
																													if (isset($_POST["embargo"]) && $_POST["embargo"] == "12mois") {$embargo = "12mois";$opt3 = "oui";}
																													if (isset($_POST["urlServeur"])) {$urlServeur = htmlspecialchars($_POST["urlServeur"]);}
																													$iMin = 0;
																													if (isset($_POST["valider"]) || isset($_POST["suite"])) {
																														if (isset($_POST["iMin"])) {$iMin = htmlspecialchars($_POST["iMin"]);}
																														if (isset($_POST["iMax"])) {$iMax = htmlspecialchars($_POST["iMax"]);}
																													}
																													if (isset($_POST["retour"])) {
																														$iMin = htmlspecialchars($_POST["iMinRet"]);
																														$iMax = htmlspecialchars($_POST["iMaxRet"]);
																													}
																												}
																												if (!isset($_POST["valider"]) && !isset($_POST["apa"])) {
																													$apa = "oui";
																												}
																												if (isset($opt1) && $opt1 == "oui" && $increment >= 10) {$increment = 10;}
																												if (isset($_POST["valider"])) {
																													$iMax = $iMin + $increment - 1;
																													$iMinRet = $iMin;
																													$iMaxRet = $iMax;
																												}
																												if (isset($team) && $team != "") {$team1 = $team; $team2 = $team;}else{$team1 = "Entrez le code de votre collection"; $team2 = "";}
																												?>
																												
																												<input type="text" id="team" name="team" class="form-control"  value="<?php echo $team1;?>" onclick="this.value='<?php echo $team2;?>';" onkeydown="document.getElementById('idhal').value = '';">
                                                    <a class="ml-2 small" target="_blank" rel="noopener noreferrer" href="https://hal-univ-rennes1.archives-ouvertes.fr/page/codes-collections">Trouver le code<br>de mon équipe / labo</a>
                                                    </div>

                                                    
                                                </div>
                                            </div> <!-- .form-group -->
																						
																						<div class="form-group row mb-1">
                                                <div class="col-12">
                                                    <h3 class="d-inline-block border-bottom border-primary text-primary">OU</h3>
                                                </div>
                                            </div> <!-- .form-group -->

                                            <div class="form-group row mb-1">
                                                <label for="idhal" class="col-12 col-md-3 col-form-label font-weight-bold">
                                                Identifiant alphabétique auteur HAL
                                                </label>
																								
																								<div class="col-12 col-md-9">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <!-- <button type="button" tabindex="0" class="btn btn-info" data-html="true" data-toggle="popover" data-trigger="focus" title="Pour une requête sur plusieurs IdHAL" data-content="Mettre entre parenthèses, et remplacer les guillemets par %22 et les espaces par %20. Exemple <strong>(%22laurent-jonchere%22%20OR%20%22olivier-troccaz%22)" data-original-title="">
                                                            <i class="mdi mdi-comment-question text-white"></i>
                                                            </button> -->
                                                        </div>
																												<input type="text" id="idhal" name="idhal" class="form-control" value="<?php echo $idhal;?>" onkeydown="document.getElementById('team').value = '';">
																												<a class="ml-2 small" target="_blank" rel="noopener noreferrer" href="https://hal.archives-ouvertes.fr/page/mon-idhal">Créer mon IdHAL</a>
																												<p class="small mt-2 w-100">(IdHAL > olivier-troccaz, par exemple)</p>
                                                   
																										</div>
																								
																								</div>
                                            </div><!-- .form-group -->
																						
																						<div class="form-group row mb-1">
																							<div class="form-group col-sm-2">
																								<label for="anneedeb">Période : Depuis</label>
																								<select id="anneedeb" class="custom-select" name="anneedeb">
																								<?php
																								$moisactuel = date('n', time());
																								if ($moisactuel >= 10) {$i = date('Y', time())+1;}else{$i = date('Y', time());}
																								while ($i >= date('Y', time()) - 30) {
																									if (isset($anneedeb) && $anneedeb == $i) {$txt = "selected";}else{$txt = "";}
																									echo '<option value='.$i.' '.$txt.'>'.$i.'</option>' ;
																									$i--;
																								}
																								?>
																								</select>
																							</div>
																							<div class="form-group col-sm-2">
																								<label for="anneefin">Jusqu'à</label>
																								<select id="anneefin" class="custom-select" name="anneefin">
																								<?php
																								$moisactuel = date('n', time());
																								if ($moisactuel >= 10) {$i = date('Y', time())+1;}else{$i = date('Y', time());}
																								while ($i >= date('Y', time()) - 30) {
																									if (isset($anneefin) && $anneefin == $i) {$txt = "selected";}else{$txt = "";}
																									echo '<option value='.$i.' '.$txt.'>'.$i.'</option>';
																									$i--;
																								}
																								?>
																								</select>
																							</div>
                                            </div><!-- .form-group -->
																						
																						<div class="form-group row mb-1">
																							<br>
																						</div>
																						
																						<div class="form-group row mb-1">
																							<?php
																							if (isset($apa) && $apa == "oui") {$pap = " checked";}else{$pap = "";}
																							?>
																							<div class="form-group col-sm-12">
																								<div class="custom-control custom-checkbox">
																										<input type="checkbox" id="apa" class="custom-control-input" name="apa" value="oui"<?php echo $pap;?>>
																										<label for="apa" class="custom-control-label">
																										Inclure les articles <em>"A paraître"</em>
																										</label>
																								</div>
																							</div>
																						</div><!-- .form-group -->
																						
																						<div class="form-group row mb-1">
																							<?php
																							if (isset($ordinv) && $ordinv == "oui") {$ordi = " checked";}else{$ordi = "";}
																							?>
																							<div class="form-group col-sm-12">
																								<div class="custom-control custom-checkbox">
																										<input type="checkbox" id="ordinv" class="custom-control-input" name="ordinv" value="oui"<?php echo $ordi;?>>
																										<label for="ordinv" class="custom-control-label">
																										Traiter les notices dans l'ordre inverse de recherche
																										</label>
																								</div>
																							</div>
																						</div><!-- .form-group -->
																						
																						<div class="form-group row mb-1">
																							<label for="increment" class="col-2 col-form-label">
																							Incrément
																							</label>
																							<div class="col-1">
																									<select class="form-control" id="increment" name="increment">
																									<?php
																									if (isset($increment) && $increment == 1) {$uni = "selected";}else{$uni = "";}
																									if ((isset($increment) && $increment == 10) || ($team2 == "" && $idhal == "")) {$dix = "selected";}else{$dix = "";}
																									if (isset($increment) && $increment == 20) {$vgt = "selected";}else{$vgt = "";}
																									if (isset($increment) && $increment == 50) {$cqt = "selected";}else{$cqt = "";}
																									if (isset($increment) && $increment == 100) {$cen = "selected";}else{$cen = "";}
																									if (isset($increment) && $increment == 200) {$dcn = "selected";}else{$dcn = "";}
																									?>
																									<option value="1" <?php echo $uni;?>>1</option>
																									<option value="10" <?php echo $dix;?>>10</option>
																									<option value="20" <?php echo $vgt;?>>20</option>
																									<option value="50" <?php echo $cqt;?>>50</option>
																									<option value="100" <?php echo $cen;?>>100</option>
																									<option value="200" <?php echo $dcn;?>>200</option>
																									</select>
																							</div>
																							<div class="col-9">
																									<div class="border border-primary rounded p-2 small d-inline-block">
																											<span class='text-primary'>-> Cette valeur correspond au pas des requêtes envoyées vers Crossref. Plus elle sera élevée et plus le risque de blocage de votre poste sera important. Par précaution, elle est volontairement forcée à un maximum de 10 pour l'étape 1.</span>
																									</div>
																							</div>
																						</div><!-- .form-group -->
																						
																						<div class="form-group row mb-1">
																							<br>
																						</div>
																						
																						<div class="form-group row mb-1">
                                                <div class="col-12">
                                                    <div class="accordion" id="accordionChoix">
                                                        <div class="card mb-0">
                                                            <div class="card-header" id="headingOne">
                                                                <h5 class="m-0">
                                                                    <a class="custom-accordion-title collapsed d-block"
                                                                        data-toggle="collapse" href="#collapseOne"
                                                                        aria-expanded="true" aria-controls="collapseOne">
                                                                        Compléter et corriger les métadonnées HAL
                                                                    </a>
                                                                </h5>
                                                            </div>

                                                            <div id="collapseOne" class="collapse p-2" aria-labelledby="headingOne" data-parent="#accordionChoix">
																																<div class="card-body">
																																		<div class="border border-grey rounded p-2 mb-2">
																																		
																																				<?php
																																				if (isset($doiCrossRef) && $doiCrossRef == "oui") {$iod = " checked";}else{$iod = "";}
																																				if (isset($revue) && $revue == "oui") {$rev = " checked";}else{$rev = "";}
																																				if (isset($vnp) && $vnp == "oui") {$pnv = " checked";}else{$pnv = "";}
																																				if (isset($lanCrossRef) && $lanCrossRef == "oui") {$lanC = " checked";}else{$lanC = "";}
																																				if (isset($financement) && $financement == "oui") {$fin = " checked";}else{$fin = "";}
																																				if (isset($anr) && $anr == "oui") {$tan = " checked";}else{$tan = "";}
																																				if (isset($anneepub) && $anneepub == "oui") {$apu = " checked";}else{$apu = "";}
																																				if (isset($mel) && $mel == "oui") {$lem = " checked";}else{$lem = "";}

																																				if (isset($ccTitconf) && $ccTitconf == "oui") {$tco = " checked";}else{$tco = "";}
																																				if (isset($ccPays) && $ccPays == "oui") {$pay = " checked";}else{$pay = "";}
																																				if (isset($ccDatedeb) && $ccDatedeb == "oui") {$ddb = " checked";}else{$ddb = "";}
																																				if (isset($ccDatefin) && $ccDatefin == "oui") {$dfn = " checked";}else{$dfn = "";}
																																				if (isset($ccISBN) && $ccISBN == "oui") {$isb = " checked";}else{$isb = "";}
																																				if (isset($ccTitchap) && $ccTitchap == "oui") {$tch = " checked";}else{$tch = "";}
																																				if (isset($ccTitlivr) && $ccTitlivr == "oui") {$tli = " checked";}else{$tli = "";}
																																				if (isset($ccEditcom) && $ccEditcom == "oui") {$edc = " checked";}else{$edc = "";}

																																				if (isset($mocCrossRef) && $mocCrossRef == "oui") {$mocC = " checked";}else{$mocC = "";}
																																				if (isset($absPubmed) && $absPubmed == "oui") {$absP = " checked";}else{$absP = "";}
																																				if (isset($lanPubmed) && $lanPubmed == "oui") {$lanP = " checked";}else{$lanP = "";}
																																				if (isset($mocPubmed) && $mocPubmed == "oui") {$mocP = " checked";}else{$mocP = "";}
																																				if (isset($pmid) && $pmid == "oui") {$pmi = " checked";}else{$pmi = "";}

																																				if (isset($pmcid) && $pmcid == "oui") {$pmc = " checked";}else{$pmc = "";}
																																				if (isset($absISTEX) && $absISTEX == "oui") {$absI = " checked";}else{$absI = "";}
																																				if (isset($lanISTEX) && $lanISTEX == "oui") {$lanI = " checked";}else{$lanI = "";}
																																				if (isset($mocISTEX) && $mocISTEX == "oui") {$mocI = " checked";}else{$mocI = "";}
																																				if (isset($DOIComm) && $DOIComm == "non" || !isset($team)) {$DOICn = " checked";}else{$DOICn = "";}
																																				if (isset($DOIComm) && $DOIComm == "oui") {$DOICo = " checked";}else{$DOICo = "";}
																																				if (isset($PoPeer) && $PoPeer == "oui") {$Popo = " checked";}else{$Popo = "";}
																																				if (isset($PoPeer) && $PoPeer == "non" || !isset($team)) {$Popn = " checked";}else{$Popn = "";}
																																				?>
																																			
																																				<div class="form-group row mb-1">
																																					<?php
																																					if (isset($chkall) && $chkall == "oui") {$cka = " checked";}else{$cka = "";}
																																					?>
																																					<div class="form-group col-sm-12">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chkall" class="custom-control-input" onclick="chkall1()" name="chkall" value="oui"<?php echo $cka;?>>
																																							<label for="chkall" class="custom-control-label">
																																							Cocher tout (Articles - Pubmed prioritaire)
																																							</label>
																																						</div>
																																					</div>
																																				</div>
																																				
																																				<h4><span class='badge badge-secondary-lighten'>Via CrossRef (articles) :</span></h4>
																																				<div class="form-group row mb-1">
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk17" class="custom-control-input" onclick="option1();" name="doiCrossRef" value="oui"<?php echo $iod;?>>
																																							<label for="chk17" class="custom-control-label">
																																							DOI
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk0" class="custom-control-input" onclick="option1();" name="revue" value="oui"<?php echo $rev;?>>
																																							<label for="chk0" class="custom-control-label">
																																							Revue
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk1" class="custom-control-input" onclick="option1();" name="vnp" value="oui"<?php echo $pnv;?>>
																																							<label for="chk1" class="custom-control-label">
																																							Vol/num/Pag
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk24" class="custom-control-input" onclick="option1();" name="lanCrossRef" value="oui"<?php echo $lanC;?>>
																																							<label for="chk24" class="custom-control-label">
																																							Langue
																																							</label>
																																						</div>
																																					</div>

																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk2" class="custom-control-input" onclick="option1();" name="financement" value="oui"<?php echo $fin;?>>
																																							<label for="chk2" class="custom-control-label">
																																							Financement
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk3" class="custom-control-input" onclick="option1();" name="anr" value="oui"<?php echo $tan;?>>
																																							<label for="chk3" class="custom-control-label">
																																							ANR
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk4" class="custom-control-input" onclick="option1();" name="anneepub" value="oui"<?php echo $apu;?>>
																																							<label for="chk4" class="custom-control-label">
																																							Année de publication
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk5" class="custom-control-input" onclick="option1();" name="mel" value="oui"<?php echo $lem;?>>
																																							<label for="chk5" class="custom-control-label">
																																							Date de mise en ligne
																																							</label>
																																						</div>
																																					</div>
																																				</div><!-- .form-group -->
																																				
																																				<h4><span class='badge badge-secondary-lighten'>Via CrossRef (conférences et chapitres) :</span></h4>
																																				<!--<input type="checkbox" id="chk6" class="form-control" onclick="option1();" name="mocCrossRef" value="oui"<?php echo $mocC;?>>&nbsp;<label for="chk6">Mots-clés généralistes</label>-->
																																				<div class="form-group row mb-1">
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk39" class="custom-control-input" onclick="option1();" name="ccTitconf" value="oui"<?php echo $tco;?>>
																																							<label for="chk39" class="custom-control-label">
																																							Titre de la conférence
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk40" class="custom-control-input" onclick="option1();" name="ccPays" value="oui"<?php echo $pay;?>>
																																							<label for="chk40" class="custom-control-label">
																																							Pays
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk41" class="custom-control-input" onclick="option1();" name="ccDatedeb" value="oui"<?php echo $ddb;?>>
																																							<label for="chk41" class="custom-control-label">
																																							Date de début
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk42" class="custom-control-input" onclick="option1();" name="ccDatefin" value="oui"<?php echo $dfn;?>>
																																							<label for="chk42" class="custom-control-label">
																																							Date de fin
																																							</label>
																																						</div>
																																					</div>

																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk43" class="custom-control-input" onclick="option1();" name="ccISBN" value="oui"<?php echo $isb;?>>
																																							<label for="chk43" class="custom-control-label">
																																							ISBN
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk44" class="custom-control-input" onclick="option1();" name="ccTitchap" value="oui"<?php echo $tch;?>>
																																							<label for="chk44" class="custom-control-label">
																																							Titre du chapitre
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk45" class="custom-control-input" onclick="option1();" name="ccTitlivr" value="oui"<?php echo $tli;?>>
																																							<label for="chk45" class="custom-control-label">
																																							Titre du livre
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk46" class="custom-control-input" onclick="option1();" name="ccEditcom" value="oui"<?php echo $edc;?>>
																																							<label for="chk46" class="custom-control-label">
																																							Editeur commercial
																																							</label>
																																						</div>
																																					</div>
																																				</div><!-- .form-group -->
																																				
																																				<h4><span class='badge badge-secondary-lighten'>Via PubMed :</span></h4>
																																				<div class="form-group row mb-1">
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk11" class="custom-control-input" onclick="option1();" name="absPubmed" value="oui"<?php echo $absP;?>>
																																							<label for="chk11" class="custom-control-label">
																																							Résumé
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk12" class="custom-control-input" onclick="option1();" name="lanPubmed" value="oui"<?php echo $lanP;?>>
																																							<label for="chk12" class="custom-control-label">
																																							Langue
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk13" class="custom-control-input" onclick="option1();" name="mocPubmed" value="oui"<?php echo $mocP;?>>
																																							<label for="chk13" class="custom-control-label">
																																							Mots-clés
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk7" class="custom-control-input" onclick="option1();" name="pmid" value="oui"<?php echo $pmi;?>>
																																							<label for="chk7" class="custom-control-label">
																																							PMID
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<!--<input type="checkbox" id="chk8" class="form-control" onclick="option1();" name="pmcid" disabled="disabled" value="oui"<?php echo $pmc;?>>&nbsp;<label for="chk8">PMCID</label>-->
																																				</div><!-- .form-group -->
																																				
																																				<h4><span class='badge badge-secondary-lighten'>Via ISTEX :</span></h4>
																																				<div class="form-group row mb-1">
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk14" class="custom-control-input" onclick="option1();" name="absISTEX" value="oui"<?php echo $absI;?>>
																																							<label for="chk14" class="custom-control-label">
																																							Résumé
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk15" class="custom-control-input" onclick="option1();" name="lanISTEX" value="oui"<?php echo $lanI;?>>
																																							<label for="chk15" class="custom-control-label">
																																							Langue
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk16" class="custom-control-input" onclick="option1();" name="mocISTEX" value="oui"<?php echo $mocI;?>>
																																							<label for="chk16" class="custom-control-label">
																																							Mots-clés
																																							</label>
																																						</div>
																																					</div>
																																				</div><!-- .form-group -->
																																					
																																				<div class="form-group row mb-1">	
																																					<div class="form-group col-sm-12">
																																						<span>
																																						Vérifier la présence des champs popularLevel_s et peerReviewing_s (articles) :
																																						</span>
																																						<div class="custom-control custom-radio custom-control-inline">
																																							<input type="radio" id="chk48" class="custom-control-input" onclick="option1();" name="PoPeer" value="oui"<?php echo $Popo;?>>
																																							<label for="chk48" class="custom-control-label">
																																							oui
																																							</label>
																																						</div>
																																						<div class="custom-control custom-radio custom-control-inline">
																																							<input type="radio" id="chk49" class="custom-control-input" onclick="option1();" name="PoPeer" value="non"<?php echo $Popn;?>>
																																							<label for="chk49" class="custom-control-label">
																																							non
																																							</label>
																																						</div>
																																					</div>
																																				</div><!-- .form-group -->
																																				
																																				<div class="form-group row mb-1">	
																																					<div class="form-group col-sm-12">
																																						<span>
																																						Autoriser l'ajout d'un DOI aux dépôts HAL de type communication :
																																						</span>
																																						<div class="custom-control custom-radio custom-control-inline">
																																							<input type="radio" id="chk37" class="custom-control-input" onclick="option1();" name="DOIComm" value="oui"<?php echo $DOICo;?>>
																																							<label for="chk37" class="custom-control-label">
																																							oui
																																							</label>
																																						</div>
																																						<div class="custom-control custom-radio custom-control-inline">
																																							<input type="radio" id="chk38" class="custom-control-input" onclick="option1();" name="DOIComm" value="non"<?php echo $DOICn;?>>
																																							<label for="chk38" class="custom-control-label">
																																							non
																																							</label>
																																						</div>
																																					</div>
																																				</div><!-- .form-group -->
																																			</div>
																																	</div>
																															</div>
                                                        </div>

                                                        <div class="card mb-0">
                                                            <div class="card-header" id="headingTwo">
                                                                <h5 class="m-0">
                                                                    <a class="custom-accordion-title d-block collapsed"
                                                                        data-toggle="collapse" href="#collapseTwo"
                                                                        aria-expanded="true" aria-controls="collapseTwo">
                                                                        Compléter et corriger les auteurs
                                                                    </a>
                                                                </h5>
                                                            </div>
                                                            <div id="collapseTwo" class="collapse p-2" aria-labelledby="headingTwo" data-parent="#accordionChoix">
																																<div class="card-body">
																																		<div class="border border-grey rounded p-2 mb-2">

																																				<?php
																																				if (isset($ordAut) && $ordAut == "oui") {$tua = " checked";}else{$tua = "";}
																																				if (isset($iniPre) && $iniPre == "oui") {$erp = " checked";}else{$erp = "";}
																																				if (isset($vIdHAL) && $vIdHAL == "oui") {$idv = " checked";}else{$idv = "";}
																																				if (isset($rIdHAL) && $rIdHAL == "oui") {$idh = " checked";}else{$idh = "";}
																																				if (isset($ctrTrs) && $ctrTrs == "oui") {$ctr = " checked";}else{$ctr = "";}
																																				if (isset($rIdHALArt) && $rIdHALArt == "oui") {$idhart = " checked";}else{$idhart = "";}
																																				if (isset($rIdHALCom) && $rIdHALCom == "oui") {$idhcom = " checked";}else{$idhcom = "";}
																																				if (isset($rIdHALCou) && $rIdHALCou == "oui") {$idhcou = " checked";}else{$idhcou = "";}
																																				if (isset($rIdHALOuv) && $rIdHALOuv == "oui") {$idhouv = " checked";}else{$idhouv = "";}
																																				if (isset($rIdHALDou) && $rIdHALDou == "oui") {$idhdou = " checked";}else{$idhdou = "";}
																																				if (isset($rIdHALBre) && $rIdHALBre == "oui") {$idhbre = " checked";}else{$idhbre = "";}
																																				if (isset($rIdHALRap) && $rIdHALRap == "oui") {$idhrap = " checked";}else{$idhrap = "";}
																																				if (isset($rIdHALThe) && $rIdHALThe == "oui") {$idhthe = " checked";}else{$idhthe = "";}
																																				if (isset($rIdHALPre) && $rIdHALPre == "oui") {$idhpre = " checked";}else{$idhpre = "";}
																																				if (isset($rIdHALPub) && $rIdHALPub == "oui") {$idhpub = " checked";}else{$idhpub = "";}
																																				?>
																																			
																																				<div class="form-group row mb-1">
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk18" class="custom-control-input" onclick="option2();" name="ordAut" value="oui"<?php echo $tua;?>>
																																							<label for="chk18" class="custom-control-label">
																																							Corriger l'ordre des auteurs
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-6">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk19" class="custom-control-input" onclick="option2();" name="iniPre" value="oui"<?php echo $erp;?>>
																																							<label for="chk19" class="custom-control-label">
																																							Remplacer l'initiale du premier prénom par son écriture complète
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<a target="_blank" rel="noopener noreferrer" href="./CrossIDHAL_CSV.php">Procédure CSV OCDHAL</a>
																																					</div>
																																				
																																				</div>
																																				
																																				<div class="form-group row mb-2">
																																					<br>
																																				</div>
																																				
																																				<div class="form-group row mb-1">
																																					<div class="form-group col-sm-12">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk25" class="custom-control-input" onclick="option2();" name="rIdHAL" value="oui"<?php echo $idh;?>>
																																							<label for="chk25" class="custom-control-label">
																																							IdHAL :
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk26" class="custom-control-input" onclick="option2();" name="rIdHALArt" value="oui"<?php echo $idhart;?>>
																																							<label for="chk26" class="custom-control-label">
																																							Articles
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk27" class="custom-control-input" onclick="option2();" name="rIdHALCom" value="oui"<?php echo $idhcom;?>>
																																							<label for="chk27" class="custom-control-label">
																																							Communications
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk28" class="custom-control-input" onclick="option2();" name="rIdHALCou" value="oui"<?php echo $idhcou;?>>
																																							<label for="chk28" class="custom-control-label">
																																							Chapitres d'ouvrages
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk29" class="custom-control-input" onclick="option2();" name="rIdHALOuv" value="oui"<?php echo $idhouv;?>>
																																							<label for="chk29" class="custom-control-label">
																																							Ouvrages
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk30" class="custom-control-input" onclick="option2();" name="rIdHALDou" value="oui"<?php echo $idhdou;?>>
																																							<label for="chk30" class="custom-control-label">
																																							Directions d'ouvrages
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk31" class="custom-control-input" onclick="option2();" name="rIdHALBre" value="oui"<?php echo $idhbre;?>>
																																							<label for="chk31" class="custom-control-label">
																																							Brevets
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk32" class="custom-control-input" onclick="option2();" name="rIdHALRap" value="oui"<?php echo $idhrap;?>>
																																							<label for="chk32" class="custom-control-label">
																																							Rapports
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk33" class="custom-control-input" onclick="option2();" name="rIdHALThe" value="oui"<?php echo $idhthe;?>>
																																							<label for="chk33" class="custom-control-label">
																																							Thèses
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk34" class="custom-control-input" onclick="option2();" name="rIdHALPre" value="oui"<?php echo $idhpre;?>>
																																							<label for="chk34" class="custom-control-label">
																																							Preprints
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-3">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk35" class="custom-control-input" onclick="option2();" name="rIdHALPub" value="oui"<?php echo $idhpub;?>>
																																							<label for="chk35" class="custom-control-label">
																																							Autres publications
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="col-sm-12 font-italic">
																																					Cette option permet de rechercher d'éventuels IdHAL auteur absents des notices.
																																					</div>
																																				</div>
																																				
																																				<div class="form-group row mb-2">
																																					&nbsp;<br>
																																				</div>
																																				
																																				<div class="form-group row mb-1">
																																					<div class="form-group col-sm-12">
																																						<div class="custom-control custom-checkbox">
																																							<input type="checkbox" id="chk36" class="custom-control-input" onclick="option2();" name="vIdHAL" value="oui"<?php echo $idv;?>>
																																							<label for="chk36" class="custom-control-label">
																																							Repérer les formes IdHAL non valides (en rouge)
																																							</label>
																																						</div>
																																					</div>
																																					<div class="col-sm-12 font-italic">
																																					Pour ce test de repérage, choisissez une période de recherche raisonnable pour limiter le nombre total de notices. L'incrément de recherche n'a aucune incidence puisque toutes les notices comportant au moins un auteur de la collection sont traitées.
																																					</div>
																																				</div>
																																			
																																				<div class="form-group row mb-2">
																																					&nbsp;<br>
																																				</div>
																																				
																																				<?php
																																				//Restriction IP pour le contrôle des tiers
																																				include("./Glob_IP_list.php");
																																				if (in_array($ip, $IP_aut)) {
																																					echo "<div class=\"form-group row mb-1\">";
																																					echo "<div class=\"form-group col-sm-12\">";
																																					echo "	<div class=\"custom-control custom-checkbox\">";
																																					echo "		<input type=\"checkbox\" id=\"chk47\" class=\"custom-control-input\" onclick=\"option2();\" name=\"ctrTrs\" value=\"oui\"".$ctr.">";
																																					echo "		<label for=\"chk47\" class=\"custom-control-label\">";
																																					echo "		Contrôle des tiers";
																																					echo "		</label>";
																																					echo "	</div>";
																																					echo "</div>";
																																					echo "</div>";
																																				}
																																				?>
																																				
																																			</div>
																																	</div>
																															</div>
																													</div>
																															
																												<div class="card mb-0">
                                                            <div class="card-header" id="headingThree">
                                                                <h5 class="m-0">
                                                                    <a class="custom-accordion-title d-block collapsed"
                                                                        data-toggle="collapse" href="#collapseThree"
                                                                        aria-expanded="true" aria-controls="collapseThree">
                                                                        Déposer le texte intégral des articles
                                                                    </a>
                                                                </h5>
                                                            </div>
                                                            <div id="collapseThree" class="collapse p-2" aria-labelledby="headingThree" data-parent="#accordionChoix">
																																<div class="card-body">
																																		<div class="border border-grey rounded p-2 mb-2">

																																				<?php
																																				if (isset($manuautOH) && $manuautOH == "oui") {$manOH = " checked";}else{$manOH = "";}
																																				if (isset($manuautNR) && $manuautNR == "oui") {$manNR = " checked";}else{$manNR = "";}

																																				if ((isset($lienext) && $lienext == "oui" || !isset($_POST["valider"])) && $noliene != "oui" && $manOH != " checked") {$ext = " checked";}else{$ext = "";}
																																				if (isset($manuaut) && $manuaut == "oui") {$man = " checked";}else{$man = "";}
																																				if (isset($noliene) && $noliene == "oui") {$noe = " checked";}else{$noe = "";}
																																				if (isset($embargo) && $embargo == "6mois") {$m6 = " checked";}else{$m6 = "";}
																																				if (isset($embargo) && $embargo == "12mois") {$m12 = " checked";}else{$m12 = "";}
																																				?>
																																				
																																				<div class="form-group row mb-1">
																																					<div class="form-group col-sm-12">
																																						<span class="col-sm-3">Restreindre l'affichage aux notices</span>
																																						<div class="custom-control custom-checkbox custom-control-inline">
																																							<input type="checkbox" id="chk20" class="custom-control-input" onclick="option3();" name="lienext" value="oui"<?php echo $ext;?>>
																																							<label for="chk20" class="custom-control-label">
																																							ayant un lien externe
																																							</label>
																																						</div>
																																						<div class="custom-control custom-checkbox custom-control-inline">
																																							<input type="checkbox" id="chk21" class="custom-control-input" onclick="option3();affich_form();" name="noliene" value="oui"<?php echo $noe;?>>
																																							<label for="chk21" class="custom-control-label">
																																							sans lien externe
																																							</label>
																																						</div>
																																					</div>
																																					
																																					<div class="form-group col-sm-12" id="embargo" style="display: block;">
																																						<span class="col-sm-2">Embargo</span>
																																						<div class="custom-control custom-radio custom-control-inline">
																																							<input type="radio" id="chk22" class="custom-control-input" onclick="option3();" name="embargo" value="6mois"<?php echo $m6;?>>
																																							<label for="chk22" class="custom-control-label">
																																							6 mois
																																							</label>
																																						</div>
																																						<div class="custom-control custom-radio custom-control-inline">
																																							<input type="radio" id="chk23" class="custom-control-input" onclick="option3();" name="embargo" value="12mois"<?php echo $m12;?>>
																																							<label for="chk23" class="custom-control-label">
																																							12 mois&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;←──────────────┘
																																							</label>
																																						</div>
																																					</div>
																																				</div>
																																				
																																				<div class="form-group row mb-1">
																																					<div class="form-group col-sm-12">
																																						<div class="form-group row ml-1 mr-1">
																																							<div class="custom-control custom-checkbox col-sm-6">
																																								<input type="checkbox" id="chk10" class="custom-control-input" onclick="option3();" name="manuaut" value="oui"<?php echo $man;?>>
																																								<label for="chk10" class="custom-control-label">
																																								Manuscrit auteur (fichiers sous la forme doi_normalisé.pdf)
																																								</label>
																																							</div>
																																							<div class="input-group col-sm-6">
																																								<span> > URL du serveur :&nbsp;</span>
																																								<input id="urlpdf" class="form-control" type="text" name="urlServeur" value="<?php echo $urlServeur;?>">
																																								<span id="urlserveur" class="text-primary"></span>
																																							</div>
																																						</div>
																																					</div>

																																					<div class="form-group col-sm-12">
																																						<div class="form-group row ml-1 mr-1">
																																							<div class="custom-control custom-checkbox col-sm-6">
																																								<input type="checkbox" id="chk50" class="custom-control-input" onclick="option3();" name="manuautOH" value="oui"<?php echo $manOH;?>>
																																								<label for="chk50" class="custom-control-label">
																																								Manuscrit auteurs (via OverHAL)
																																								</label>
																																							</div>
																																							<div class="col-sm-6">
																																								<span> > Au préalable, vous devez procéder au <a target="_blank" rel="noopener noreferrer" href="./CrossHAL_CSV.php">chargement du fichier CSV des statistiques</a></span>
																																							</div>
																																						</div>
																																					</div>

																																					<div class="form-group col-sm-12">
																																						<div class="form-group row ml-1 mr-1">
																																							<div class="custom-control custom-checkbox col-sm-6">
																																								<input type="checkbox" id="chk51" class="custom-control-input" onclick="option3();" name="manuautNR" value="oui"<?php echo $manNR;?>>
																																								<label for="chk51" class="custom-control-label">
																																								Manuscrit auteurs (via OverHAL) <u>non référencés dans HAL</u>
																																								</label>
																																							</div>
																																							<div class="col-sm-6">
																																								<span> > Au préalable, vous devez procéder au <a target="_blank" rel="noopener noreferrer" href="./CrossHAL_CSV.php">chargement du fichier CSV des statistiques</a></span>
																																							</div>
																																						</div>
																																					</div>
																																				</div>
																																		</div>
																																</div>
																																		
																														</div>
																												</div>
																										</div> <!-- .form-group -->
																								</div>
																						</div><!-- .form-group -->
																						
																						<div class="form-group row mt-4">
																								<div class="col-12 justify-content-center d-flex">
																									<!--<input type="submit" value="Vérifier les DOI" name="verifDOI">-->
																									<input type="hidden" value="1" name="iMin">
																									<input type="hidden" value="" name="iMax">
																									<input type="hidden" value="1" name="iMinRet">
																									<input type="hidden" value="" name="iMaxRet">
																									<input type="submit" class="btn btn-md btn-primary btn-lg" value="Valider" name="valider">
																								</div>
																						</div>
																						</form>
																						<script>
																						if (document.getElementById("chk21").checked == false) {
																							document.getElementById("embargo").style.display = "none";
																						}else{
																							document.getElementById("embargo").style.display = "block";
																						}
																						</script>
																						<!--Fin formulaire-->
