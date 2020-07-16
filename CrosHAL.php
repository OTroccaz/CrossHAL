<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
include("./IP_list.php");
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

if (isset($_GET['css']) && ($_GET['css'] != ""))
{
  $css = $_GET['css'];
}else{
  $css = "https://ecobio.univ-rennes1.fr/HAL_SCD.css";
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

include "./CrosHAL_oaDOI.php";
include "./CR_DOI_Levenshtein.php";
include "./CR_DOI_ISSN_HAL_Rev.php";
include "./PMID_Metado.php";
include "./ISTEX_Metado.php";
include "./CrosHAL_codes_pays.php";
//authentification CAS ou autre ?
if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false || strpos($_SERVER['HTTP_HOST'], 'ecobio') !== false) {
  include('./_connexion.php');
}else{
  require_once('./CAS_connect.php');
  //echo 'toto : '.phpCAS::getUser();
  /*
  foreach (phpCAS::getAttributes() as $key => $value) {
    if (is_array($value)) {
    echo '<li>', $key, ':<ol>';
    foreach($value as $item) {
          echo '<li><strong>', $item, '</strong></li>';
    }
    echo '</ol></li>';
    } else {
        echo '<li>', $key, ': <strong>', $value, '</strong></li>';
    }
  }
  */
}

$root = 'http';
if ( isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
  $root.= "s";
}
$targetPDF = "https://ecobio.univ-rennes1.fr/CrosHAL/PDF/";
$testok = 0;
$idhal = "";

include "./CrosHAL_fonctions.php";

suppression("./XML", 3600);//Suppression des fichiers du dossier XML créés il y a plus d'une heure

include("./normalize.php");

?>
<html lang="fr">
<head>
  <title>CrosHAL</title>
  <meta name="Description" content="CrosHAL">
  <link href="bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $css;?>" type="text/css">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <script type="text/javascript" language="Javascript" src="./CrosHAL.js"></script>
  <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
  <link rel="icon" type="type/ico" href="HAL_favicon.ico">
  <link rel="stylesheet" href="./CrosHAL.css">
</head>
<body style="font-family: Corbel, sans-serif;">

<noscript>
<div class='center, red' id='noscript'><strong>ATTENTION !!! JavaScript est désactivé ou non pris en charge par votre navigateur : cette procédure ne fonctionnera pas correctement.</strong><br>
<strong>Pour modifier cette option, voir <a target='_blank' rel='noopener noreferrer' href='http://www.libellules.ch/browser_javascript_activ.php'>ce lien</a>.</strong></div><br>
</noscript>

<table class="table100" aria-describedby="Entêtes">
<tr>
<th scope="col" style="text-align: left;"><img alt="CrosHAL" title="CrosHAL" width="250px" src="./img/logo_Croshal.png"><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Enrichissez vos dépôts HAL</th>
<th scope="col" style="text-align: right;"><img alt="Université de Rennes 1" title="Université de Rennes 1" width="150px" src="./img/logo_UR1_gris_petit.jpg"></th>
</tr>
</table>
<hr style="color: #467666; height: 1px; border-width: 1px; border-top-color: #467666; border-style: inset;">

<p>CrosHAL permet de vérifier la validité des métadonnées des notices saisies dans HAL avec celles présentes dans CrossRef, Pubmed et ISTEX, de compléter et corriger les auteurs et de déposer le texte intégral des articles.</p>

<form name="troli" action="CrosHAL.php" method="post" onsubmit="return verif ();">
<p class="form-inline"><label for="team">Code collection HAL</label> <a class='info' onclick='return false' href="#">(qu'est-ce que c’est ?)<span>Code visible dans l’URL d’une collection.
Exemple : IPR-MOL est le code de la collection http://hal.archives-ouvertes.fr/<strong>IPR-PMOL</strong> de l’équipe Physique moléculaire
de l’unité IPR UMR CNRS 6251</span></a> :

<?php
//Formulaire
include "./CrosHAL_formulaire.php";

//Etape 1
if ((isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) && $opt1 == "oui") {
  //authentification CAS ou autre ?
  if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false || strpos($_SERVER['HTTP_HOST'], 'ecobio') !== false) {
    include "./_connexion.php";
  }else{
    require_once "./CAS_connect.php";
  }
  $rows = 100000;//100000
	if ($increment >= 10) {$increment = 10;}//Pour éviter d'être blacklisté par Crossref
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
	if ($ccTitconf == "non" && $ccPays == "non" && $ccDatedeb == "non" && $ccDatefin == "non" && $ccISBN == "non" && $ccTitchap == "non" && $ccTitlivr == "non" && $ccEditcom == "non") {
		//Etape 1a sur les articles
		include "./CrosHAL_etape1a.php";
	}else{
		//Etape 1b sur les conférences et chapitres
		include "./CrosHAL_etape1b.php";
	}
}

//Etape 2
if ((isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) && $opt2 == "oui") {
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
      echo " : affichage de ".$iMin." à ".$iMax."</strong>&nbsp;<em>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</em><br><br>";
    }else{
      echo " : affichage de ".$iMin." à ".$numFound."</strong>&nbsp;<em>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</em><br><br>";
    }
  }
  echo "<div id='cpt'></div>";
  echo "<table class='table table-striped table-bordered table-hover;'>";
  //echo "<table style='border-collapse: collapse; width: 100%' border='1px' bordercolor='#999999' cellpadding='5px' cellspacing='5px'><tr>";
  echo "<tr><td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>ID</strong></td>";
  if ($rIdHAL == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Liens</strong></td>";
  }else{
    if ($rIdHAL != "oui") {
      if ($vIdHAL != "oui") {
				if ($ctrTrs != "oui") {
					echo "<td colspan='3' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Liens</strong></td>";
				}else{
					echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Liens</strong></td>";
				}
      }else{
        //echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Prénom</strong></td>";
        //echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Nom</strong></td>";
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Nom complet</strong></td>";
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>IdHAL</strong></td>";
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>AuréHAL</strong></td>";
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Lien auteur HAL</strong></td>";
      }
    }
  }
  if ($apa == "oui") {
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>AP</strong></td>";
  }
  if ($ordAut == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>10 premiers auteurs</strong></td>";
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Nb auteurs</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Action auteurs</strong></td>";
  }
  if ($iniPre == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Premier prénom auteurs</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Action prénoms</strong></td>";
  }
  if ($rIdHAL == "oui") {
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Formulaire HAL</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Nom</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Prénom</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>IdHAL suggéré</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>AuréHAL</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Nom de domaine</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>DocID</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>IdHAL</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Affiliation</strong></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Année (de publication)</strong></td>";
  }
	if ($ctrTrs == "oui") {
		echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Contributeur</strong></td>";
		echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Co-auteurs affiliés au laboratoire</strong></td>";
		echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Titre de la publication</strong></td>";
		echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Domaine email</strong></td>";
		/*Désactivation temporaire
		echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Domaine(s) disciplinaire(s)</strong></td>";
		*/
		echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Affiliations de type INCOMING ou OLD</strong></td>";
		/*Désactivation temporaire
		echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Pubmed</strong></td>";
		*/
		echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Vu</strong></td>";
		echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Actions</strong></td>";
	}
  echo "</tr>";
  echo "</tr><tr>";
  if ($vIdHAL != "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>DOI</strong></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
  }
  if ($rIdHAL != "oui" && $vIdHAL != "oui" && $ctrTrs != "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
  }
  if ($ordAut == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
  }
  if ($iniPre == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>HAL</strong></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>CR</strong></td>";
  }
  echo "</tr>";
  if ($rIdHAL != "oui" && $vIdHAL != "oui" && $ctrTrs != "oui") {//Etape 2a > Corriger ordre des auteurs et remplacer l'initiale du premier prénom par son écriture complète
    include "./CrosHAL_etape2a.php";
  }else{
    if ($rIdHAL == "oui") {//Etape 2b > Recherche des IdHAL des auteurs
			include "./CrosHAL_etape2b.php";
    }else{
			if ($vIdHAL == "oui") {//Etape 2c > Test validité IdHAL
				include "./CrosHAL_etape2c.php";
			}else{//Etape 2d > Contrôle des tiers
				include "./CrosHAL_etape2d.php";
			}
    }
  }
}

//Etape 3
if (((isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) && $opt3 == "oui") || $action == 3) {
	if (isset($manuaut) && $manuaut == "oui" || $lienext == "oui" || $noliene == "oui") {//Etape 3a > Manuscrit auteurs (fichiers sous la forme doi_normalisé.pdf)
		include "./CrosHAL_etape3a.php";
	}else{
		if (isset($manuautOH) && $manuautOH == "oui") {//Etape 3b > Manuscrit auteurs (via OverHAL)
			include "./CrosHAL_etape3b.php";
		}else{//Etape 3c > Manuscrit auteurs (via OverHAL) non référencés dans HAL
			include "./CrosHAL_etape3c.php";
		}
	}
}
?>
<br>
<?php
include('./bas.php');
?>
</body>
</html>