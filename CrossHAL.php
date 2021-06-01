<!DOCTYPE html>
<?php
// récupération de l'adresse IP du client (on cherche d'abord à savoir s'il est derrière un proxy)
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
  $ip = $_SERVER['HTTP_CLIENT_IP'];
}else {
  $ip = $_SERVER['REMOTE_ADDR'];
}

/*
//Restriction IP
include("./Glob_IP_list.php");
if (!in_array($ip, $IP_aut)) {
  echo "<br><br><center><font face='Corbel'><strong>";
  echo "Votre poste n'est pas autorisé à accéder à cette application.";
  echo "</strong></font></center>";
  die;
}
*/

header('Content-type: text/html; charset=UTF-8');

register_shutdown_function(function() {
    $error = error_get_last();

    if ($error['type'] === E_ERROR && strpos($error['message'], 'Maximum execution time of') === 0) {
        echo "<br><strong><font color='red'>Le script a été arrêté car son temps d'exécution dépasse la limite maximale autorisée.</font></strong><br>";
    }
});

//CR = CrossRef / PM = Pubmed
$action = "";//Variable pour identifier l'étape 1, 2 ou 3
$urlServeur = "";//URL du PDF qui sera renseignée dans le TEI
$nbjours = 1;//"Embargo" > Interdit de modifier une notice si date "whenSubmitted" < n jours
$racine = "https://hal.archives-ouvertes.fr/";

if (isset($_GET['action']) && ($_GET['action'] == 3)) {
  $action = $_GET["action"];
  $opt3 = $_GET['opt3'];
  $halId = $_GET["halID"];
  $iMin = $_GET["iMin"];
  $iMax = $_GET["iMax"];
  $iMinRet = $_GET["iMinRet"];
  $iMaxRte = $_GET["iMaxRet"];
  $increment = $_GET["increment"];
  $team = $_GET["team"];
  $idhal = $_GET["idhal"];
  $anneedeb = $_GET["anneedeb"];
  $anneefin = $_GET["anneefin"];
  $apa = $_GET["apa"];
  if (isset($_GET["manuaut"])) {$manuaut = $_GET["manuaut"];}
	if (isset($_GET["manuautOH"])) {$manuautOH = $_GET["manuautOH"];}
  $lienext = $_GET["lienext"];
  $noliene = $_GET["noliene"];
  $embargo = $_GET["embargo"];
  $urlServeur = $_GET["urlServeur"];
  $urlPDF3 = $_GET["urlPDF3"];
  $cptTab = $_GET["cptTab"];
  $chkall = "";
  $doiCrossRef = "";
  $revue = "";
  $vnp = "";
  $lanCrossRef = "";
  $financement = "";
  $anr = "";
  $anneepub = "";
  $mel = "";
  //$mocCrossRef = "";
	$ccTitconf = "";
	$ccPays = "";
	$ccDatedeb = "";
	$ccDatefin = "";
	$ccISBN = "";
	$ccTitchap = "";
	$ccTitlivr = "";
	$ccEditcom = "";
  $absPubmed = "";
  $lanPubmed = "";
  $mocPubmed = "";
  $pmid = "";
  $pmcid = "";
  $absISTEX = "";
  $lanISTEX = "";
  $mocISTEX = "";
	$DOIComm = "";
	$PoPeer= "";
	$ordinv = "";
}

if (isset($_GET["erreur"]))
{
	$erreur = $_GET["erreur"];
	if ($erreur == 1) {echo "<script type=\"text/javascript\">alert(\"Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !\")</script>";}
	if ($erreur == 2) {echo "<script type=\"text/javascript\">alert(\"Le fichier dépasse la limite autorisée dans le formulaire HTML !\")</script>";}
	if ($erreur == 3) {echo "<script type=\"text/javascript\">alert(\"L'envoi du fichier a été interrompu pendant le transfert !\")</script>";}
	//if ($erreur == 4) {echo "<script type=\"text/javascript\">alert(\"Aucun fichier envoyé ou bien il a une taille nulle !\")</script>";}
	if ($erreur == 5) {echo "<script type=\"text/javascript\">alert(\"Mauvaise extension de fichier !\")</script>";}
}

include "./CrossHAL_oaDOI.php";
include "./CrossHAL_CR_DOI_Levenshtein.php";
include "./CrossHAL_CR_DOI_ISSN_HAL_Rev.php";
include "./CrossHAL_PMID_Metado.php";
include "./CrossHAL_ISTEX_Metado.php";
include "./CrossHAL_codes_pays.php";
//authentification CAS ou autre ?
if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false || strpos($_SERVER['HTTP_HOST'], 'ecobio') !== false) {
  include('./_connexion.php');
}else{
  require_once('./CAS_connect.php');
	$HAL_USER = phpCAS::getUser();
	$HAL_QUOI = "CrossHAL";
	if($HAL_USER != "jonchere" && $HAL_USER != "otroccaz") {include('./Stats_listes_HALUR1.php');}
}

$root = 'http';
if ( isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
  $root.= "s";
}
$targetPDF = "https://ecobio.univ-rennes1.fr/CrossHAL/PDF/";
$testok = 0;
$idhal = "";

include "./CrossHAL_fonctions.php";

suppression("./XML", 86400);//Suppression des fichiers et dossiers du dossier XML créés il y a plus d'un jour

include("./Glob_normalize.php");

?>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title>CrossHAL - HAL - UR1</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="CrossHAL permet de vérifier les métadonnées des notices saisies dans HAL, de les compléter et corriger, et de déposer le texte intégral des articles" name="description" />
	<meta content="Coderthemes + Lizuka + OTroccaz + LJonchere" name="author" />
	<!-- App favicon -->
	<link rel="shortcut icon" href="favicon.ico">

	<!-- third party css -->
	<!-- <link href="./assets/css/vendor/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" /> -->
	<!-- third party css end -->

	<!-- App css -->
	<link href="./assets/css/icons.min.css" rel="stylesheet" type="text/css" />
	<link href="./assets/css/app-hal-ur1.min.css" rel="stylesheet" type="text/css" id="light-style" />
	<!-- <link href="./assets/css/app-creative-dark.min.css" rel="stylesheet" type="text/css" id="dark-style" /> -->
	
	<!-- third party js -->
	<script src="./CrossHAL.js"></script>
	<!-- third party js end -->
	
	<!-- bundle -->
	<script src="./assets/js/vendor.min.js"></script>
	<script src="./assets/js/app.min.js"></script>

	<!-- third party js -->
	<!-- <script src="./assets/js/vendor/Chart.bundle.min.js"></script> -->
	<!-- third party js ends -->
	<script src="./assets/js/pages/hal-ur1.chartjs.js"></script>
	
</head>

<body class="loading" data-layout="topnav" >

<noscript>
<div class='text-primary' id='noscript'><strong>ATTENTION !!! JavaScript est désactivé ou non pris en charge par votre navigateur : cette procédure ne fonctionnera pas correctement.</strong><br>
<strong>Pour modifier cette option, voir <a target='_blank' rel='noopener noreferrer' href='http://www.libellules.ch/browser_javascript_activ.php'>ce lien</a>.</strong></div><br>
</noscript>

        <!-- Begin page -->
        <div class="wrapper">

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
								
								<?php
								include "./Glob_haut.php";
								?>

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb bg-light-lighten p-2">
																								<li><a href="https://halur1.univ-rennes1.fr/CrossHAL.php?logout="><i class="uil-power"></i> Déconnexion CAS CCSD</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
                                                <li class="breadcrumb-item"><a href="index.php"><i class="uil-home-alt"></i> Accueil HALUR1</a></li>
                                                <li class="breadcrumb-item active" aria-current="page">Cross<span class="font-weight-bold">HAL</span></li>
                                            </ol>
                                        </nav>
                                    </div>
                                    <h4 class="page-title">Enrichissez vos dépôts HAL</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-xl-8 col-lg-6 d-flex">
                                <!-- project card -->
                                <div class="card d-block w-100 shadow-lg">
                                    <div class="card-body">
                                        
                                        <!-- project title-->
                                        <h2 class="h1 mt-0">
                                            <i class="mdi mdi-label-multiple mdi-24px text-primary"></i>
                                            <span class="font-weight-light">Cross</span><span class="text-primary">HAL</span>
                                        </h2>
                                        <h5 class="badge badge-primary badge-pill">Présentation</h5>
																				
																				<img src="./img/victor-grabarczyk-marbella-spain-unsplash.jpg" alt="Accueil CrossHAL" class="img-fluid"><br>
																				<p class="font-italic">Photo : Marbella Spain by Victor Grabarczyk on Unsplash (détail)</p>
																				
                                        <p class="mb-2 text-justify">
                                            CrossHAL permet de vérifier la qualité des métadonnées des notices saisies dans HAL, de compléter et corriger les auteurs, et de déposer le texte intégral des articles. Ce script a été créé par Olivier Troccaz (conception-développement) et Laurent Jonchère (conception).
                                        </p>
																				
																				<p class="mb-4">
                                            Contacts : <a target='_blank' rel='noopener noreferrer' href="https://openaccess.univ-rennes1.fr/interlocuteurs/laurent-jonchere">Laurent Jonchère</a> (Université de Rennes 1) / <a target='_blank' rel='noopener noreferrer' href="https://openaccess.univ-rennes1.fr/interlocuteurs/olivier-troccaz">Olivier Troccaz</a> (CNRS ECOBIO/OSUR).
                                        </p>


                                    </div> <!-- end card-body-->
                                    
                                </div> <!-- end card-->

                            </div> <!-- end col -->
                            <div class="col-lg-6 col-xl-4 d-flex">
                                <div class="card shadow-lg w-100">
                                    <div class="card-body">
                                        <h5 class="badge badge-primary badge-pill">Mode d'emploi</h5>
																						<div class="mb-2">
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    En préparation
                                                </li>
                                            </ul> 
                                        </div>
                                    </div>
                                </div>
                                <!-- end card-->
                            </div>
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-12 d-flex">
                                <!-- project card -->
                                <div class="card w-100 d-block shadow-lg">
                                    <div class="card-body">
                                        
                                        <h5 class="badge badge-primary badge-pill">Paramétrage</h5>

																				<?php
																				//Formulaire
																				include "./CrossHAL_formulaire.php";
																				?>
																				
																		</div> <!-- end card-body-->
																
														</div> <!-- end card-->

												</div> <!-- end col -->
										</div>
										<!-- end row -->

																				<?php
																				//Etape 1
																				if ((isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) && $opt1 == "oui") {
																						echo '<div class="row">';
																								echo '<div class="col-12 d-flex">';
																										echo '<div class="card shadow-lg w-100">';
																												echo '<div class="card-body">';

																					//authentification CAS ou autre ?
																					if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false || strpos($_SERVER['HTTP_HOST'], 'ecobio') !== false) {
																						include "./_connexion.php";
																					}else{
																						require_once "./CAS_connect.php";
																					}
																					$rows = 100000;//100000
																					if ($increment >= 10 && $csvDOIAC == "non") {$increment = 10;}//Pour éviter d'être blacklisté par Crossref, sauf pour l'étape 1c
																					//$entete = "Authorization: Basic ".$pass."\r\n".
																					//          "On-Behalf-Of: ".$user."\r\n".
																					//          "Content-Type: text/xml"."\r\n".
																					//          "Packaging: http://purl.org/net/sword-types/AOfr"."\r\n"."\r\n";
																					if ($apa == "oui") {//Notice "A paraître"
																						$txtApa = "";
																					}else{
																						$txtApa = "%20AND%20NOT%20inPress_bool:%22true%22";
																					}
																					if (isset($idhal) && $idhal != "") {$atester = "authIdHal_s"; $qui = $idhal;}else{$atester = "collCode_s"; $qui = $team;}
																					//Etape 1 sur les articles ou sur les conférences et chapitres ?
																					if ($ccTitconf == "non" && $ccPays == "non" && $ccDatedeb == "non" && $ccDatefin == "non" && $ccISBN == "non" && $ccTitchap == "non" && $ccTitlivr == "non" && $ccEditcom == "non" && $csvDOIAC == "non") {
																						//Etape 1a sur les articles
																						include "./CrossHAL_etape1a.php";
																					}else{
																						if ($csvDOIAC == "non") {
																							//Etape 1b sur les conférences et chapitres
																							include "./CrossHAL_etape1b.php";
																						}else{
																							//Etape 1c sur les auteurs correspondants
																							include "./CrossHAL_etape1c.php";
																						}
																					}
																					echo '						</div> <!-- end card-body-->';
																					echo '				</div> <!-- end card-->';
																					echo '		</div> <!-- end col -->';
																					echo '</div> <!-- end row -->';
																				}

																				//Etape 2
																				if ((isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) && $opt2 == "oui") {
																						echo '<div class="row">';
																								echo '<div class="col-12 d-flex">';
																										echo '<div class="card shadow-lg w-100">';
																												echo '<div class="card-body">';
																					//authentification CAS ou autre ?
																					if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false || strpos($_SERVER['HTTP_HOST'], 'ecobio') !== false) {
																						include "./_connexion.php";
																					}else{
																						require_once "./CAS_connect.php";
																					}
																					$rows = 100000;//100000
																					//$entete = "Authorization: Basic ".$pass."\r\n".
																					//          "On-Behalf-Of: ".$user."\r\n".
																					//          "Content-Type: text/xml"."\r\n".
																					//          "Packaging: http://purl.org/net/sword-types/AOfr"."\r\n"."\r\n";
																					if ($apa == "oui") {//Notice "A paraître"
																						$txtApa = "";
																					}else{
																						$txtApa = "%20AND%20NOT%20inPress_bool:%22true%22";
																					}
																					if (isset($idhal) && $idhal != "") {$atester = "authIdHal_s"; $qui = $idhal;}else{$atester = "collCode_s"; $qui = $team;}
																					//$urlHAL = "https://api.archives-ouvertes.fr/search/?q=collCode_s:%22".$team."%22".$txtApa."%20AND%20submitType_s:%22file%22&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:%22ART%22&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,anrProjectReference_s,journalTitle_s,journalIssn_s,journalValid_s,docid,journalIssn_s,journalEissn_s,abstract_s,language_s,label_xml,submittedDate_s&sort=halId_s%20desc";
																					//$urlHAL = "https://api.archives-ouvertes.fr/search/?q=halId_s:%22hal-01686774%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:%22ART%22&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,anrProjectReference_s,journalTitle_s,journalIssn_s,journalValid_s,docid,journalIssn_s,journalEissn_s,abstract_s,language_s,inPress_bool,label_xml,submittedDate_s,submitType_s,docType_s&sort=halId_s%20asc";
																					if (isset($rIdHAL) && $rIdHAL == "oui") {//Etape 2b > Recherche des IdHAL des auteurs
																						$rechIdHAL = "";
																						if (isset($rIdHALArt) && $rIdHALArt == "oui") {
																							$rechIdHAL .= ($rechIdHAL == "") ? "%22ART%22":"%20OR%20%22ART%22";
																						}
																						if (isset($rIdHALCom) && $rIdHALCom == "oui") {
																							$rechIdHAL .= ($rechIdHAL == "") ? "%22COMM%22":"%20OR%20%22COMM%22";
																						}
																						if (isset($rIdHALCou) && $rIdHALCou == "oui") {
																							$rechIdHAL .= ($rechIdHAL == "") ? "%22COUV%22":"%20OR%20%22COUV%22";
																						}
																						if (isset($rIdHALOuv) && $rIdHALOuv == "oui") {
																							$rechIdHAL .= ($rechIdHAL == "") ? "%22OUV%22":"%20OR%20%22OUV%22";
																						}
																						if (isset($rIdHALDou) && $rIdHALDou == "oui") {
																							$rechIdHAL .= ($rechIdHAL == "") ? "%22DOUV%22":"%20OR%20%22DOUV%22";
																						}
																						if (isset($rIdHALBre) && $rIdHALBre == "oui") {
																							$rechIdHAL .= ($rechIdHAL == "") ? "%22PATENT%22":"%20OR%20%22PATENT%22";
																						}
																						if (isset($rIdHALRap) && $rIdHALRap == "oui") {
																							$rechIdHAL .= ($rechIdHAL == "") ? "%22REPORT%22":"%20OR%20%22REPORT%22";
																						}
																						if (isset($rIdHALThe) && $rIdHALThe == "oui") {
																							$rechIdHAL .= ($rechIdHAL == "") ? "%22THESE%22":"%20OR%20%22THESE%22";
																						}
																						if (isset($rIdHALPre) && $rIdHALPre == "oui") {
																							$rechIdHAL .= ($rechIdHAL == "") ? "%22UNDEF%22":"%20OR%20%22UNDEF%22";
																						}
																						if (isset($rIdHALPub) && $rIdHALPub == "oui") {
																							$rechIdHAL .= ($rechIdHAL == "") ? "%22OTHER%22":"%20OR%20%22OTHER%22";
																						}
																						//$urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(%22ART%22%20OR%20%22COMM%22%20OR%20%22COUV%22)&fl=authFirstName_s,authLastName_s,doiId_s,halId_s,producedDate_s,docid,label_xml,authIdHalFullName_fs,authIdHasStructure_fs,authIdHal_s&sort=halId_s%20desc";
																						if($ordinv == "oui") {$sort = "desc";}else{$sort = "asc";}
																						$urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(".$rechIdHAL.")&fl=authFirstName_s,authLastName_s,doiId_s,halId_s,producedDate_s,docid,label_xml,authIdHalFullName_fs,authIdHasStructure_fs,authIdHal_s&sort=halId_s%20".$sort;
																						//$increment = 10000;
																					}else{
																						if($ordinv == "oui") {$sort = "desc";}else{$sort = "asc";}
																						if ($vIdHAL != "oui") {
																							if ($ctrTrs == "oui") {//Contrôle des tiers
																								$urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,producedDate_s,submittedDate_s,docid,label_xml,authIdHalFullName_fs,authIdHasStructure_fs,authIdHal_s,label_xml,pubmedId_s,comment_s,docType_s&sort=halId_s%20".$sort;
																							}else{//Repérer les formes IdHAL non valides
																								$urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(%22ART%22%20OR%20%22COMM%22%20OR%20%22COUV%22)&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,anrProjectReference_s,journalTitle_s,journalIssn_s,journalValid_s,docid,journalIssn_s,journalEissn_s,abstract_s,language_s,label_xml,submittedDate_s,submitType_s,docType_s&sort=halId_s%20".$sort;
																							}
																						}else{
																							//Recherche du/des docid VALID de la structure
																							$docidStr = "";
																							$urlHALStr = "https://api.archives-ouvertes.fr/ref/structure/?q=(acronym_s:%22".strtoupper($team)."%22%20OR%20acronym_s:%22".ucfirst(strtolower($team))."%22%20OR%20acronym_s:%22".strtolower($team)."%22)%20AND%20valid_s:%22VALID%22&fl=docid";
																							//echo $urlHALStr;
																							askCurl($urlHALStr, $arrayHALStr);
																							$idoc = 0;
																							$test = "(";
																							while(isset($arrayHALStr["response"]["docs"][$idoc]["docid"])) {
																								$docidStr .= $arrayHALStr["response"]["docs"][$idoc]["docid"]."~";
																								//$test .= "authIdHasStructure_fs:*_".$arrayHALStr["response"]["docs"][$idoc]["docid"]."_*";
																								$test .= "structHasAuthIdHal_fs:".$arrayHALStr["response"]["docs"][$idoc]["docid"]."_FacetSep*";
																								$test .= "%20OR%20";
																								$idoc++;
																							}
																							$docidStr = substr($docidStr, 0, (strlen($docidStr)-1));
																							$test = substr($test, 0, (strlen($test)-8));
																							$test.= ")";
																							$urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$test."&rows=1000&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]&fl=authIdHal_s,authIdHasStructure_fs,authFirstName_s,authLastName_s,structHasAuthIdHal_fs";
																							//$urlHAL = "https://api.archives-ouvertes.fr/search/?q=authIdHasStructure_fs:*_928_*&rows=1000&fq=producedDateY_i:[2018%20TO%202018]&fl=authIdHal_s,authIdHasStructure_fs,authFirstName_s,authLastName_s";
																						}
																					}
																					//echo $urlHAL.'<br>';
																					askCurl($urlHAL, $arrayHAL);
																					//var_dump($arrayHAL);
																					if (isset($arrayHAL["response"]["numFound"])) {
																						$numFound = $arrayHAL["response"]["numFound"];
																					}else{
																						die ('<strong><font color="red">Désolé ! Le code collection '.$team.' ne permet pas de récupérer un docid HAL valide.</font></strong><br><br>');
																					}
																					if ($iMax > $numFound) {$iMax = $numFound;}
																					echo '<strong>Total de '.$numFound.' référence(s)';
																					if ($numFound != 0) {
																						if ($vIdHAL != "oui") {
																							if ($rIdHAL != "oui" && $ctrTrs != "oui") {//Etape 2a
																								echo " : affichage de ".$iMin." à ".$iMax."</strong>&nbsp;<br><br>";
																							}else{
																								echo " : affichage de ".$iMin." à ".$iMax."</strong>&nbsp;<em>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</em><br><br>";
																							}
																						}else{
																							echo " : affichage de ".$iMin." à ".$numFound."</strong>&nbsp;<em>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</em><br><br>";
																						}
																					}
																					echo "<div id='cpt'></div>";
																					echo "<table class='table table-responsive table-bordered table-centered table-sm text-center'>";
																					echo "<thead class='thead-dark'>";
																					//echo "<table style='border-collapse: collapse; width: 100%' border='1px' bordercolor='#999999' cellpadding='5px' cellspacing='5px'><tr>";
																					echo "<tr><th rowspan='2'><strong>ID</strong></th>";
																					if ($rIdHAL == "oui") {
																						echo "<th colspan='2'><strong>Liens</strong></th>";
																					}else{
																						if ($rIdHAL != "oui") {
																							if ($vIdHAL != "oui") {
																								if ($ctrTrs != "oui") {
																									echo "<th colspan='3'><strong>Liens</strong></th>";
																								}else{
																									echo "<th colspan='2'><strong>Liens</strong></th>";
																								}
																							}else{
																								//echo "<th><strong>Prénom</strong></th>";
																								//echo "<th><strong>Nom</strong></th>";
																								echo "<th><strong>Nom complet</strong></th>";
																								echo "<th><strong>IdHAL</strong></th>";
																								echo "<th><strong>AuréHAL</strong></th>";
																								echo "<th><strong>Lien auteur HAL</strong></th>";
																							}
																						}
																					}
																					if ($apa == "oui") {
																						echo "<th rowspan='2'><strong>AP</strong></th>";
																					}
																					if ($ordAut == "oui") {
																						echo "<th colspan='2'><strong>10 premiers auteurs</strong></th>";
																						echo "<th colspan='2'><strong>Nb auteurs</strong></th>";
																						echo "<th rowspan='2'><strong>Action auteurs</strong></th>";
																					}
																					if ($iniPre == "oui") {
																						echo "<th colspan='2'><strong>Premier prénom auteurs</strong></th>";
																						echo "<th rowspan='2'><strong>Action prénoms</strong></th>";
																					}
																					if ($rIdHAL == "oui") {
																						echo "<th rowspan='2'><strong>Formulaire HAL</strong></th>";
																						echo "<th rowspan='2'><strong>Nom</strong></th>";
																						echo "<th rowspan='2'><strong>Prénom</strong></th>";
																						echo "<th rowspan='2'><strong>IdHAL suggéré</strong></th>";
																						echo "<th rowspan='2'><strong>AuréHAL</strong></th>";
																						echo "<th rowspan='2'><strong>Nom de domaine</strong></th>";
																						echo "<th rowspan='2'><strong>DocID</strong></th>";
																						echo "<th rowspan='2'><strong>IdHAL</strong></th>";
																						echo "<th rowspan='2'><strong>Affiliation</strong></th>";
																						echo "<th rowspan='2'><strong>Année (de publication)</strong></th>";
																					}
																					if ($ctrTrs == "oui") {
																						echo "<th rowspan='2'><strong>Contributeur</strong></th>";
																						echo "<th rowspan='2'><strong>Co-auteurs affiliés au laboratoire</strong></th>";
																						echo "<th rowspan='2'><strong>Titre de la publication</strong></th>";
																						echo "<th rowspan='2'><strong>Domaine email</strong></th>";
																						/*Désactivation temporaire
																						echo "<th rowspan='2'><strong>Domaine(s) disciplinaire(s)</strong></th>";
																						*/
																						echo "<th rowspan='2'><strong>Affiliations de type INCOMING ou OLD</strong></th>";
																						/*Désactivation temporaire
																						echo "<th rowspan='2'><strong>Pubmed</strong></th>";
																						*/
																						echo "<th rowspan='2'><strong>Vu</strong></th>";
																						echo "<th rowspan='2'><strong>Actions</strong></th>";
																					}
																					echo "</tr>";
																					echo "</tr><tr>";
																					if ($vIdHAL != "oui") {
																						echo "<th><strong>DOI</strong></th>";
																						echo "<th><strong>HAL</strong></th>";
																					}
																					if ($rIdHAL != "oui" && $vIdHAL != "oui" && $ctrTrs != "oui") {
																						echo "<th><strong>CR</strong></th>";
																					}
																					if ($ordAut == "oui") {
																						echo "<th><strong>HAL</strong></th>";
																						echo "<th><strong>CR</strong></th>";
																						echo "<th><strong>HAL</strong></th>";
																						echo "<th><strong>CR</strong></th>";
																					}
																					if ($iniPre == "oui") {
																						echo "<th><strong>HAL</strong></th>";
																						echo "<th><strong>CR</strong></th>";
																					}
																					echo "</tr><thead><tbody>";
																					if ($rIdHAL != "oui" && $vIdHAL != "oui" && $ctrTrs != "oui") {//Etape 2a > Corriger ordre des auteurs et remplacer l'initiale du premier prénom par son écriture complète
																						include "./CrossHAL_etape2a.php";
																					}else{
																						if ($rIdHAL == "oui") {//Etape 2b > Recherche des IdHAL des auteurs
																							include "./CrossHAL_etape2b.php";
																						}else{
																							if ($vIdHAL == "oui") {//Etape 2c > Test validité IdHAL
																								include "./CrossHAL_etape2c.php";
																							}else{//Etape 2d > Contrôle des tiers
																								include "./CrossHAL_etape2d.php";
																							}
																						}
																					}
																					echo '						</div> <!-- end card-body-->';
																					echo '				</div> <!-- end card-->';
																					echo '		</div> <!-- end col -->';
																					echo '</div> <!-- end row -->';
																				}

																				//Etape 3
																				if (((isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) && $opt3 == "oui") || $action == 3) {
																					echo '<div class="row">';
																								echo '<div class="col-12 d-flex">';
																										echo '<div class="card shadow-lg w-100">';
																												echo '<div class="card-body">';
																					if (isset($manuaut) && $manuaut == "oui" || $lienext == "oui" || $noliene == "oui") {//Etape 3a > Manuscrit auteurs (fichiers sous la forme doi_normalisé.pdf)
																						include "./CrossHAL_etape3a.php";
																					}else{
																						if (isset($manuautOH) && $manuautOH == "oui") {//Etape 3b > Manuscrit auteurs (via OverHAL)
																							include "./CrossHAL_etape3b.php";
																						}else{//Etape 3c > Manuscrit auteurs (via OverHAL) non référencés dans HAL
																							include "./CrossHAL_etape3c.php";
																						}
																					}
																					echo '						</div> <!-- end card-body-->';
																					echo '				</div> <!-- end card-->';
																					echo '		</div> <!-- end col -->';
																					echo '</div> <!-- end row -->';
																				}
																				?>
																				<br>

                    </div> <!-- container -->

                </div>
                <!-- content -->
								
								<?php
								include "./Glob_bas.php";
								?>

						</div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->


        </div>
				
				<button id="scrollBackToTop" class="btn btn-primary"><i class="mdi mdi-24px text-white mdi-chevron-double-up"></i></button>
        <!-- END wrapper -->
				
				<!-- bundle -->
				<!-- <script src="./assets/js/vendor.min.js"></script> -->
				<script src="./assets/js/app.min.js"></script>

				<!-- third party js -->
				<!-- <script src="./assets/js/vendor/Chart.bundle.min.js"></script> -->
				<!-- third party js ends -->
				<script src="./assets/js/pages/hal-ur1.chartjs.js"></script>
				
				<script>
            (function($) {
                'use strict';
                $('#warning-alert-modal').modal(
                    {'show': true, 'backdrop': 'static'}    
                    
                        );
                $(document).scroll(function() {
                  var y = $(this).scrollTop();
                  if (y > 200) {
                    $('#scrollBackToTop').fadeIn();
                  } else {
                    $('#scrollBackToTop').fadeOut();
                  }
                });
                $('#scrollBackToTop').each(function(){
                    $(this).click(function(){ 
                        $('html,body').animate({ scrollTop: 0 }, 'slow');
                        return false; 
                    });
                });
            })(window.jQuery)
        </script>
				
    </body>
	
</html>