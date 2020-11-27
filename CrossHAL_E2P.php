<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
// récupération de l'adresse IP du client (on cherche d'abord à savoir s'il est derrière un proxy)
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
  $ip = $_SERVER['HTTP_CLIENT_IP'];
}else {
  $ip = $_SERVER['REMOTE_ADDR'];
}

//Restriction IP
include("./Glob_IP_list.php");
if (!in_array($ip, $IP_aut)) {
  echo "<br><br><center><font face='Corbel'><b>";
  echo "Votre poste n'est pas autorisé à accéder à cette application.";
  echo "</b></font></center>";
  die;
}

header('Content-type: text/html; charset=UTF-8');
//CR = CrossRef / PM = Pubmed
$action = "";//Variable pour identifier l'étape 1, 2 ou 3
$urlPDF = "";//URL du PDF qui sera renseignée dans le TEI
$nbjours = 90;//"Embargo" > Interdit de modifier une notice si date "whenSubmitted" < n jours
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
  $manuaut = $_GET["manuaut"];
  $lienext = $_GET["lienext"];
  $noliene = $_GET["noliene"];
  $embargo = $_GET["embargo"];
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
  $absPubmed = "";
  $lanPubmed = "";
  $mocPubmed = "";
  $pmid = "";
  $pmcid = "";
  $absISTEX = "";
  $lanISTEX = "";
  $mocISTEX = "";
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
$targetPDF = "https://ecobio.univ-rennes1.fr/CrossHAL/PDF/";
$testok = 0;
$idhal = "";

function strposa($haystack, $needles=array(), $offset=0) {
  $chr = array();
  foreach($needles as $needle) {
    $res = strpos($haystack, strval($needle), $offset);
    if ($res !== false) $chr[$needle] = $res;
  }
  if(empty($chr)) return false;
  return min($chr);
}

function insertNode($xml, $dueon, $amont, $aval, $tagName, $typAtt1, $valAtt1, $typAtt2, $valAtt2, $methode) {//$methode = iB (insertBefore) ou aC (appendChild)
  $noeud = "";
  $dueon = htmlspecialchars($dueon);
  //si noeud présent
  $elts = $xml->getElementsByTagName($tagName);
  foreach ($elts as $elt) {
    if ($elt->hasAttribute($typAtt1)) {
      $quoi = $elt->getAttribute($typAtt1);
      if ($amont != "langUsage" && $tagName != "abstract") {
        if ($quoi == $valAtt1) {
          $elt->nodeValue = $dueon;
          if ($elt->hasAttribute("subtype")) {$elt->removeAttribute("subtype");}//suppression inPress
          if ($valAtt2 != "") {$elt->setAttribute($typAtt2, $valAtt2);}
          $noeud = "ok";
        }
      }else{
        $elt->nodeValue = $dueon;
        $elt->setAttribute($typAtt1, $valAtt1);
        if ($elt->hasAttribute("subtype")) {$elt->removeAttribute("subtype");}//suppression inPress
        if ($valAtt2 != "") {$elt->setAttribute($typAtt2, $valAtt2);}
        $noeud = "ok";
      }
    }
  }
  //si noeud absent > recherche du noeud amont pour insérer les nouvelles données au bon emplacement
  if($noeud == "" && $dueon != "") {
    $bibl = $xml->getElementsByTagName($amont);
    foreach ($bibl as $elt) {
      foreach($elt->childNodes as $item) { 
        if ($item->hasChildNodes()) {
          $childs = $item->childNodes;
          foreach($childs as $i) {
            $name = $i->parentNode->nodeName;
            if ($name == $aval) {//insertion nvx noeuds
              $bip = $xml->createElement($tagName);
              $cTn = $xml->createTextNode($dueon);
              $bip->setAttribute($typAtt1, $valAtt1);
              if ($valAtt2 != "") {$bip->setAttribute($typAtt2, $valAtt2);}
              $bip->appendChild($cTn);
              $biblStr = $xml->getElementsByTagName($amont)->item(0);
              if ($methode == "iB") {//insertBefore
                $biblStr->insertBefore($bip, $i->parentNode);
              }else{
                $biblStr->appendChild($bip);
              }
              break 2;
            }
          }
        }
      }
    }
  }
}

function objectToArray($object) {
  if(!is_object( $object) && !is_array($object)) {
    return $object;
  }
  if(is_object($object)) {
    $object = get_object_vars($object);
  }
  return array_map('objectToArray', $object);
}

function askCurl($url, &$arrayCurl) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'SCD (https://halur1.univ-rennes1.fr)');
  curl_setopt($ch, CURLOPT_USERAGENT, 'PROXY (http://siproxy.univ-rennes1.fr)');
  $json = curl_exec($ch);
  curl_close($ch);
  
  $memory = intval(ini_get('memory_limit')) * 1024 * 1024;
  $limite = strlen($json)*10;
  if ($limite > $memory) {
    die ('<b><font color="red">Désolé ! La collection et/ou la période choisie génère(nt) trop de résultats pour être traités correctement.</font></b>');
  }else{
    $parsed_json = json_decode($json, true);
    $arrayCurl = objectToArray($parsed_json);
  }
}

function genXMLPDF($halID, $doi, $targetPDF, $titPDF, $evd, $compNC, $compND, $compSA, &$lienPDF, $urlPDF) {
  //echo 'Bingo ! > '.$halID.'<br>';
  //Y a-t-il toujours une référence dans le document TEI de HAL?
  $lienPDF = "";
  $urlTEI = 'https://api.archives-ouvertes.fr/search/?q=halId_s:'.$halID.'&fl=label_xml';
  //$contents = file_get_contents($urlTEI);
  //$resTEI = json_decode($contents, true);
  askCurl($urlTEI, $arrayTEI);
  $valTEI = $arrayTEI["response"]["docs"][0]["label_xml"];
  $teiPDF = '<?xml version="1.0" encoding="UTF-8"?>'.$valTEI;
  $Fnm = "./XML/".$halID.".xml";
  $xml = new DOMDocument( "1.0", "UTF-8" );
  $xml->formatOutput = true;
  $xml->preserveWhiteSpace = false;
  $xml->loadXML($teiPDF);

  //suppression éventuel noeud <listBibl type="references">
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
  
  if ($evd == "noliene") {//notice sans lien externe
    $elts = $xml->getElementsByTagName('date');//recherche de dateEpub
    $cDate = time();
    $eDate = "";
    foreach ($elts as $elt) {
      if ($elt->hasAttribute("type") && $elt->getAttribute("type") == "dateEpub") {
        $eDate = $elt->nodeValue;
        $cDate = mktime(0, 0, 0, substr($eDate, 5, 2), substr($eDate, 8, 2), substr($eDate, 0, 4));
        if ($compNC == "6mois") {
          $cDate += 60*60*24*184;
        }else{
          $cDate += 60*60*24*366;
        }
      }
    }        
    $edt = $xml->getElementsByTagName('edition');
    $bip = $xml->createElement("date");
    $bip->setAttribute("type", "whenEndEmbargoed");
    $bip->nodeValue = date("Y-m-d", $cDate);
    $edt->item(0)->appendChild($bip);
    
    $bip = $xml->createElement("ref");
    $bip->setAttribute("type", "file");
    $bip->setAttribute("subtype", "author");
    $bip->setAttribute("n", "1");
    //$bip->setAttribute("target", $targetPDF.$titPDF.".pdf");
    $bip->setAttribute("target", $urlPDF);
    $edt->item(0)->appendChild($bip);
    
    $elts = $xml->getElementsByTagName('ref');
    foreach ($elts as $elt) {
      if ($elt->hasAttribute("type") && $elt->getAttribute("type") == "file") {
        $bip = $xml->createElement("date");
        $bip->setAttribute("notBefore", date("Y-m-d", $cDate));
        $elt->appendChild($bip);
      }
      break;
    }
    
    if ($eDate != "") {
      $xml->save($Fnm);
      $lienPDF = "./CrossHAL_Modif.php?action=PDF&Id=".$halID."&DOI=".$doi;
    }else{//pas de date de publication en ligne renseignée
      $lienPDF = "noDateEpub";
    }
  }else{//notice avec lien externe
    $elts = $xml->getElementsByTagName('ref');
    foreach ($elts as $elt) {
      if ($elt->hasAttribute("type") && $elt->getAttribute("type") == "file") {//PDF présent > exit
        $Fnm = "";
      }else{
        $avail = 'http://creativecommons.org/licenses/by';
        if ($compNC != "") {$avail .= '-nc';}
        if ($compND != "") {$avail .= '-nd';}
        if ($compSA != "") {$avail .= '-sa';}
        $avail .= '/';
        
        $edt = $xml->getElementsByTagName('edition');
        $bip = $xml->createElement("ref");
        $bip->setAttribute("type", "file");
        $bip->setAttribute("subtype", $evd);
        $bip->setAttribute("n", "1");
        //$bip->setAttribute("target", $targetPDF.$titPDF.".pdf");
        $bip->setAttribute("target", $urlPDF);
        $edt->item(0)->appendChild($bip);

        $xml->save($Fnm);
        
        /*
        //Où déposer le fichier PDF pour qu'HAL/le CCSD puisse le valider correctement ?
        include('./_connexion.php');
        $conn = ftp_connect("129.20.88.134");
        if (ftp_login($conn, $user, $pass)) {
          ftp_pasv($conn, false);
          //ftp_chdir($conn, "/PDF/");
          $remote_file = $titPDF.".pdf";
          //$file = "C:/wamp/www/CrossHAL/PDF/".$titPDF.".pdf";
          $file = "./PDF/".$titPDF.".pdf";
          if (ftp_put($conn, $remote_file, $file, FTP_BINARY)) {
            //echo "Chargement avec succès du fichier $file\n";
          }else{
            //echo "Il y a eu un problème lors du chargement du fichier $file\n";
          }
          ftp_close($conn);
        }
        if ($Fnm != "") {
          $lienPDF = "./CrossHAL_Modif.php?action=PDF&Id=".$halID."&DOI=".$doi;
        }
        */
        $lienPDF = "./CrossHAL_Modif.php?action=PDF&Id=".$halID."&DOI=".$doi;
        //echo $lienPDF;
      }
      break;
    }
  //return $lienPDF;
  }
}

function testURL($url) {
  $resURL = curl_init();
  curl_setopt($resURL, CURLOPT_URL, $url);
  curl_setopt($resURL, CURLOPT_BINARYTRANSFER, 1);
  //curl_setopt($resURL, CURLOPT_HEADERFUNCTION, 'curlHeaderCallback');
  curl_setopt($resURL, CURLOPT_FAILONERROR, 1);
  curl_setopt($resURL, CURLOPT_HEADER, false);
  curl_setopt($resURL, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($resURL, CURLINFO_HEADER_OUT, true);
  curl_setopt($resURL, CURLOPT_TIMEOUT, 15);
  curl_setopt($resURL, CURLOPT_CONNECTTIMEOUT, 10);
  curl_exec ($resURL);
  $intReturnCode = curl_getinfo($resURL, CURLINFO_HTTP_CODE);
  //echo $intReturnCode;
  curl_close ($resURL);
  //if ($intReturnCode != 200 && $intReturnCode != 302 && $intReturnCode != 304) {
  if ($intReturnCode != 200 && $intReturnCode != 304) {
    return false;
  }else{
    return true;
  }
}

//Nettoyage des dossiers de création de fichiers
function suppression($dossier, $age) {
  $repertoire = opendir($dossier);
    while(false !== ($fichier = readdir($repertoire)))
    {
      $chemin = $dossier."/".$fichier;
      $infos = pathinfo($chemin);
      $age_fichier = time() - filemtime($chemin);
      if($fichier != "." && $fichier != ".." && !is_dir($fichier) && $age_fichier > $age)
      {
      unlink($chemin);
      //echo $chemin." - ".date ("F d Y H:i:s.", filemtime($chemin))."<br>";
      }
    }
  closedir($repertoire);
}
suppression("./XML", 3600);//Suppression des fichiers du dossier XML créés il y a plus d'une heure

function normalize($st) {
    //return preg_replace('/\W+/', '', $st);
    $st = strtr($st,' ()"-!?[]{}:,;./*+$^=\'\\','                       ');
    $st = preg_replace('/\s+/', '', $st);
		return str_replace(array('à','á','â','ã','ä','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ','À','Á','Â','Ã','Ä','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý'), array('a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','u','y','y','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','U','Y'), $st);
    //return strtr($st,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

//Suppresion des accents
function wd_remove_accents($str, $charset='utf-8')
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

    return $str;
}

function mb_ucwords($str) {
  $str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
  return ($str);
}

function prenomCompInit($prenom) {
  $prenom = str_replace("  ", " ",$prenom);
  if (strpos(trim($prenom),"-") !== false) {//Le prénom comporte un tiret
    $postiret = mb_strpos(trim($prenom),'-', 0, 'UTF-8');
    $prenomg = trim(mb_substr($prenom,0,($postiret-1),'UTF-8'));
    $prenomd = trim(mb_substr($prenom,($postiret+1),strlen($prenom),'UTF-8'));
    $autg = mb_substr($prenomg,0,1,'UTF-8');
    $autd = mb_substr($prenomd,0,1,'UTF-8');
    $prenom = mb_ucwords($autg).".-".mb_ucwords($autd).".";
  }else{
    if (strpos(trim($prenom)," ") !== false) {//plusieurs prénoms
      $posespace = strpos(trim($prenom)," ");
      $tabprenom = explode(" ", trim($prenom));
      $p = 0;
      $prenom = "";
      while (isset($tabprenom[$p])) {
        if ($p == 0) {
          $prenom .= mb_ucwords(mb_substr($tabprenom[$p], 0, 1, 'UTF-8')).".";
        }else{
          $prenom .= " ".mb_ucwords(mb_substr($tabprenom[$p], 0, 1, 'UTF-8')).".";
        }
        $p++;
      }
    }else{
      $prenom = mb_ucwords(mb_substr($prenom, 0, 1, 'UTF-8')).".";
    }
  }
  return wd_remove_accents($prenom);
}

function nomCompEntier($nom) {
  $nom = trim(mb_strtolower($nom,'UTF-8'));
  if (strpos($nom,"-") !== false) {//Le nom comporte un tiret
    $postiret = strpos($nom,"-");
    $autg = substr($nom,0,$postiret);
    $autd = substr($nom,($postiret+1),strlen($nom));
    $nom = mb_ucwords($autg)."-".mb_ucwords($autd);
  }else{
    $nom = mb_ucwords($nom);
  }
  return wd_remove_accents($nom);
}

function progression($indice, $iMax) {	
	echo "<script>";
  echo "var txt = 'Traitement référence $indice sur $iMax<br>';";
	echo "document.getElementById('cpt').innerHTML = txt";
	echo "</script>";
	ob_flush();
	flush();
	ob_flush();
	flush();
}
?>
<html lang="fr">
<head>
  <title>CrossHAL</title>
  <meta name="Description" content="CrossHAL">
  <link href="bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $css;?>" type="text/css">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <script type="text/javascript" language="Javascript" src="./CrossHAL.js"></script>
  <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
  <link rel="icon" type="type/ico" href="HAL_favicon.ico">
  <link rel="stylesheet" href="./CrossHAL.css">
</head>
<body>

<noscript>
<div align='center' id='noscript'><font color='red'><b>ATTENTION !!! JavaScript est désactivé ou non pris en charge par votre navigateur : cette procédure ne fonctionnera pas correctement.</b></font><br>
<b>Pour modifier cette option, voir <a target='_blank' href='http://www.libellules.ch/browser_javascript_activ.php'>ce lien</a>.</b></div><br>
</noscript>

<table width="100%">
<tr>
<td style="text-align: left;"><img alt="CrossHAL" title="CrossHAL" width="250px" src="./img/logo_CrossHAL.png"></td>
<td style="text-align: right;"><img alt="Université de Rennes 1" title="Université de Rennes 1" width="150px" src="./img/logo_UR1_gris_petit.jpg"></td>
</tr>
</table>
<hr style="color: #467666; height: 1px; border-width: 1px; border-top-color: #467666; border-style: inset;">

<p>CrossHAL permet de vérifier la validité des métadonnées des notices saisies dans HAL avec celles présentes dans CrossRef, Pubmed et ISTEX, de compléter et corriger les auteurs et de déposer le texte intégral des articles.</p>

<form name="troli" action="CrossHAL_E2P.php" method="post" onsubmit="return verif();">
<p class="form-inline"><label for="team">Code collection HAL</label> <a class='info' onclick='return false' href="#">(qu'est-ce que c&apos;est ?)<span>Code visible dans l&apos;URL d&apos;une collection.
Exemple : IPR-MOL est le code de la collection http://hal.archives-ouvertes.fr/<b>IPR-PMOL</b> de l&apos;équipe Physique moléculaire
de l&apos;unité IPR UMR CNRS 6251</span></a> :

<?php
//$urlPDF = "";
//if (isset($_POST["verifDOI"])) {
if (!isset($_GET["noliene"])) {$noliene = "";}
if (isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) {
  $team = htmlspecialchars($_POST["team"]);
  $idhal = htmlspecialchars($_POST["idhal"]);
  $anneedeb = htmlspecialchars($_POST["anneedeb"]);
  $anneefin = htmlspecialchars($_POST["anneefin"]);
  if(isset($_POST["apa"]) && $_POST["apa"] == "oui") {$apa = "oui";}else{$apa = "non";}
  if (!isset($increment)) {$increment = htmlspecialchars($_POST["increment"]);}
  $opt1 = "non";
  $opt2 = "non";
  $opt3 = "non";
  //option 1
  if(isset($_POST["chkall"]) && $_POST["chkall"] == "oui") {$chkall = htmlspecialchars($_POST["chkall"]);$opt1 = "oui";}else{$chkall = "non";}
  if(isset($_POST["doiCrossRef"]) && $_POST["doiCrossRef"] == "oui") {$doiCrossRef = htmlspecialchars($_POST["doiCrossRef"]);$opt1 = "oui";}else{$doiCrossRef = "non";}
  if(isset($_POST["revue"]) && $_POST["revue"] == "oui") {$revue = htmlspecialchars($_POST["revue"]);$opt1 = "oui";}else{$revue = "non";}
  if(isset($_POST["vnp"]) && $_POST["vnp"] == "oui") {$vnp = htmlspecialchars($_POST["vnp"]);$opt1 = "oui";}else{$vnp = "non";}
  if(isset($_POST["lanCrossRef"]) && $_POST["lanCrossRef"] == "oui") {$lanCrossRef = htmlspecialchars($_POST["lanCrossRef"]);$opt1 = "oui";}else{$lanCrossRef = "non";}
  if(isset($_POST["financement"]) && $_POST["financement"] == "oui") {$financement = htmlspecialchars($_POST["financement"]);$opt1 = "oui";}else{$financement = "non";}
  if(isset($_POST["anr"]) && $_POST["anr"] == "oui") {$anr = htmlspecialchars($_POST["anr"]);$opt1 = "oui";}else{$anr = "non";}
  if(isset($_POST["anneepub"]) && $_POST["anneepub"] == "oui") {$anneepub = htmlspecialchars($_POST["anneepub"]);$opt1 = "oui";}else{$anneepub = "non";}
  if(isset($_POST["mel"]) && $_POST["mel"] == "oui") {$mel = htmlspecialchars($_POST["mel"]);$opt1 = "oui";}else{$mel = "non";}
  //if(isset($_POST["mocCrossRef"]) && $_POST["mocCrossRef"] == "oui") {$mocCrossRef = htmlspecialchars($_POST["mocCrossRef"]);$opt1 = "oui";}else{$mocCrossRef = "non";}
  if(isset($_POST["absPubmed"]) && $_POST["absPubmed"] == "oui") {$absPubmed = htmlspecialchars($_POST["absPubmed"]);$opt1 = "oui";}else{$absPubmed = "non";}
  if(isset($_POST["lanPubmed"]) && $_POST["lanPubmed"] == "oui") {$lanPubmed = htmlspecialchars($_POST["lanPubmed"]);$opt1 = "oui";}else{$lanPubmed = "non";}
  if(isset($_POST["mocPubmed"]) && $_POST["mocPubmed"] == "oui") {$mocPubmed = htmlspecialchars($_POST["mocPubmed"]);$opt1 = "oui";}else{$mocPubmed = "non";}
  if(isset($_POST["pmid"]) && $_POST["pmid"] == "oui") {$pmid = htmlspecialchars($_POST["pmid"]);$opt1 = "oui";}else{$pmid = "non";}
  if(isset($_POST["pmcid"]) && $_POST["pmcid"] == "oui") {$pmcid = htmlspecialchars($_POST["pmcid"]);$opt1 = "oui";}else{$pmcid = "non";}
  if(isset($_POST["absISTEX"]) && $_POST["absISTEX"] == "oui") {$absISTEX = htmlspecialchars($_POST["absISTEX"]);$opt1 = "oui";}else{$absISTEX = "non";}
  if(isset($_POST["lanISTEX"]) && $_POST["lanISTEX"] == "oui") {$lanISTEX = htmlspecialchars($_POST["lanISTEX"]);$opt1 = "oui";}else{$lanISTEX = "non";}
  if(isset($_POST["mocISTEX"]) && $_POST["mocISTEX"] == "oui") {$mocISTEX = htmlspecialchars($_POST["mocISTEX"]);$opt1 = "oui";}else{$mocISTEX = "non";}
  //option 2
  if(isset($_POST["ordAut"]) && $_POST["ordAut"] == "oui") {$ordAut = htmlspecialchars($_POST["ordAut"]);$opt2 = "oui";}else{$ordAut = "non";}
  if(isset($_POST["iniPre"]) && $_POST["iniPre"] == "oui") {$iniPre = htmlspecialchars($_POST["iniPre"]);$opt2 = "oui";}else{$iniPre = "non";}
  if(isset($_POST["vIdHAL"]) && $_POST["vIdHAL"] == "oui") {$vIdHAL = htmlspecialchars($_POST["vIdHAL"]);$opt2 = "oui";}else{$vIdHAL = "non";}
  if(isset($_POST["rIdHAL"]) && $_POST["rIdHAL"] == "oui") {$rIdHAL = htmlspecialchars($_POST["rIdHAL"]);$opt2 = "oui";}else{$rIdHAL = "non";}
  if(isset($_POST["rIdHALArt"]) && $_POST["rIdHALArt"] == "oui") {$rIdHALArt = htmlspecialchars($_POST["rIdHALArt"]);$opt2 = "oui";}else{$rIdHALArt = "non";}
  if(isset($_POST["rIdHALCom"]) && $_POST["rIdHALCom"] == "oui") {$rIdHALCom = htmlspecialchars($_POST["rIdHALCom"]);$opt2 = "oui";}else{$rIdHALCom = "non";}
  if(isset($_POST["rIdHALCou"]) && $_POST["rIdHALCou"] == "oui") {$rIdHALCou = htmlspecialchars($_POST["rIdHALCou"]);$opt2 = "oui";}else{$rIdHALCou = "non";}
  if(isset($_POST["rIdHALOuv"]) && $_POST["rIdHALOuv"] == "oui") {$rIdHALOuv = htmlspecialchars($_POST["rIdHALOuv"]);$opt2 = "oui";}else{$rIdHALOuv = "non";}
  if(isset($_POST["rIdHALDou"]) && $_POST["rIdHALDou"] == "oui") {$rIdHALDou = htmlspecialchars($_POST["rIdHALDou"]);$opt2 = "oui";}else{$rIdHALDou = "non";}
  if(isset($_POST["rIdHALBre"]) && $_POST["rIdHALBre"] == "oui") {$rIdHALBre = htmlspecialchars($_POST["rIdHALBre"]);$opt2 = "oui";}else{$rIdHALBre = "non";}
  if(isset($_POST["rIdHALRap"]) && $_POST["rIdHALRap"] == "oui") {$rIdHALRap = htmlspecialchars($_POST["rIdHALRap"]);$opt2 = "oui";}else{$rIdHALRap = "non";}
  if(isset($_POST["rIdHALThe"]) && $_POST["rIdHALThe"] == "oui") {$rIdHALThe = htmlspecialchars($_POST["rIdHALThe"]);$opt2 = "oui";}else{$rIdHALThe = "non";}
  if(isset($_POST["rIdHALPre"]) && $_POST["rIdHALPre"] == "oui") {$rIdHALPre = htmlspecialchars($_POST["rIdHALPre"]);$opt2 = "oui";}else{$rIdHALPre = "non";}
  if(isset($_POST["rIdHALPub"]) && $_POST["rIdHALPub"] == "oui") {$rIdHALPub = htmlspecialchars($_POST["rIdHALPub"]);$opt2 = "oui";}else{$rIdHALPub = "non";}
  //option 3
  if(isset($_POST["manuaut"]) && $_POST["manuaut"] == "oui") {$manuaut = htmlspecialchars($_POST["manuaut"]);$opt3 = "oui";}else{$manuaut = "non";}
  if(isset($_POST["lienext"]) && $_POST["lienext"] == "oui") {$lienext = htmlspecialchars($_POST["lienext"]);$opt3 = "oui";}else{$lienext = "non";}
  if(isset($_POST["noliene"]) && $_POST["noliene"] == "oui") {$noliene = htmlspecialchars($_POST["noliene"]);$opt3 = "oui";}else{$noliene = "non";}
  $embargo = "";
  if(isset($_POST["embargo"]) && $_POST["embargo"] == "6mois") {$embargo = "6mois";$opt3 = "oui";}
  if(isset($_POST["embargo"]) && $_POST["embargo"] == "12mois") {$embargo = "12mois";$opt3 = "oui";}
  if(isset($_POST["urlPDF"])) {$urlPDF = htmlspecialchars($_POST["urlPDF"]);}
  if (isset($_POST["valider"]) || isset($_POST["suite"])) {
    $iMin = htmlspecialchars($_POST["iMin"]);
    $iMax = htmlspecialchars($_POST["iMax"]);
  }
  if (isset($_POST["retour"])) {
    $iMin = htmlspecialchars($_POST["iMinRet"]);
    $iMax = htmlspecialchars($_POST["iMaxRet"]);
  }
}
if (isset($_POST["valider"])) {
  $iMax = $iMin + $increment - 1;
  $iMinRet = $iMin;
  $iMaxRet = $iMax;
}
if (isset($team) && $team != "") {$team1 = $team; $team2 = $team;}else{$team1 = "Entrez le code de votre collection"; $team2 = "";}
?>
<input type="text" id="team" class="form-control" style="height: 25px; width: 300px;" name="team" value="<?php echo $team1;?>" onClick="this.value='<?php echo $team2;?>';" onkeydown="document.getElementById('idhal').value = '';">
<h2><b><u>ou</u></b></h2>
<p class="form-inline"><b><label for="idhal">Identifiant alphabétique auteur HAL</label></b> <i>(IdHAL > olivier-troccaz, par exemple)</i> :
<input type="text" id="idhal" name="idhal" class="form-control" style="height: 25px; width: 300px" value="<?php echo $idhal;?>" onkeydown="document.getElementById('team').value = '';">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="https://hal.archives-ouvertes.fr/page/mon-idhal">Créer mon IdHAL</a>
<br><br><table>
<tr><td valign="top">Période :&nbsp;</td>
<td>
<p class="form-inline">
<label for="anneedeb">Depuis</label>
<select id="anneedeb" class="form-control" style="height: 25px; width: 60px; padding: 0px;" name="anneedeb">
<?php
$moisactuel = date('n', time());
if ($moisactuel >= 10) {$i = date('Y', time())+1;}else{$i = date('Y', time());}
while ($i >= date('Y', time()) - 30) {
  if(isset($anneedeb) && $anneedeb == $i) {$txt = "selected";}else{$txt = "";}
  echo '<option value='.$i.' '.$txt.'>'.$i.'</option>' ;
  $i--;
}
?>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label for="anneefin">Jusqu'à</label>
<select id="anneefin" class="form-control" style="height: 25px; width: 60px; padding: 0px;" name="anneefin">
<?php
$moisactuel = date('n', time());
if ($moisactuel >= 10) {$i = date('Y', time())+1;}else{$i = date('Y', time());}
while ($i >= date('Y', time()) - 30) {
  if(isset($anneefin) && $anneefin == $i) {$txt = "selected";}else{$txt = "";}
  echo '<option value='.$i.' '.$txt.'>'.$i.'</option>';
  $i--;
}
?>
</select></td></tr></table>
<?php
if(isset($apa) && $apa == "oui") {$pap = " checked";}else{$pap = "";}
?>
<p class="form-inline">
<input type="checkbox" id="apa" class="form-control" style="height: 15px;" name="apa" value="oui"<?php echo $pap;?>> <label for="apa">Inclure les articles <i>"A paraître"</i></label><br/>
<br>
<label for="increment">Incrément :</label>
<select class="form-control" id="increment" style="height: 25px; padding: 0px;" name="increment">
<?php
if(isset($increment) && $increment == 1) {$uni = "selected";}else{$uni = "";}
if((isset($increment) && $increment == 10) || ($team2 == "" && $idhal == "")) {$dix = "selected";}else{$dix = "";}
if(isset($increment) && $increment == 20) {$vgt = "selected";}else{$vgt = "";}
if(isset($increment) && $increment == 50) {$cqt = "selected";}else{$cqt = "";}
if(isset($increment) && $increment == 100) {$cen = "selected";}else{$cen = "";}
if(isset($increment) && $increment == 200) {$dcn = "selected";}else{$dcn = "";}
?>
<option value="1" <?php echo $uni;?>>1</option>
<option value="10" <?php echo $dix;?>>10</option>
<option value="20" <?php echo $vgt;?>>20</option>
<option value="50" <?php echo $cqt;?>>50</option>
<option value="100" <?php echo $cen;?>>100</option>
<option value="200" <?php echo $dcn;?>>200</option>
</select>
-> Cette valeur correspond au pas des requêtes envoyées vers Crossref. Plus elle sera élevée et plus le risque de blocage de votre poste sera important.
<br><br>
<?php
if(isset($chkall) && $chkall == "oui") {$cka = " checked";}else{$cka = "";}
?>
<b>Etape 1 : Compléter et corriger les métadonnées HAL</b> <input type="checkbox" id="chkall" class="form-control" style="height: 15px;" onclick="chkall1()" name="chkall" value="oui"<?php echo $cka;?>>&nbsp;<label for="chkall">Cocher tout (Pubmed prioritaire)</label><br>
<?php
if(isset($doiCrossRef) && $doiCrossRef == "oui") {$iod = " checked";}else{$iod = "";}
if(isset($revue) && $revue == "oui") {$rev = " checked";}else{$rev = "";}
if(isset($vnp) && $vnp == "oui") {$pnv = " checked";}else{$pnv = "";}
if(isset($lanCrossRef) && $lanCrossRef == "oui") {$lanC = " checked";}else{$lanC = "";}
if(isset($financement) && $financement == "oui") {$fin = " checked";}else{$fin = "";}
if(isset($anr) && $anr == "oui") {$tan = " checked";}else{$tan = "";}
if(isset($anneepub) && $anneepub == "oui") {$apu = " checked";}else{$apu = "";}
if(isset($mel) && $mel == "oui") {$lem = " checked";}else{$lem = "";}
if(isset($mocCrossRef) && $mocCrossRef == "oui") {$mocC = " checked";}else{$mocC = "";}
if(isset($absPubmed) && $absPubmed == "oui") {$absP = " checked";}else{$absP = "";}
if(isset($lanPubmed) && $lanPubmed == "oui") {$lanP = " checked";}else{$lanP = "";}
if(isset($mocPubmed) && $mocPubmed == "oui") {$mocP = " checked";}else{$mocP = "";}
if(isset($pmid) && $pmid == "oui") {$pmi = " checked";}else{$pmi = "";}
if(isset($pmcid) && $pmcid == "oui") {$pmc = " checked";}else{$pmc = "";}
if(isset($absISTEX) && $absISTEX == "oui") {$absI = " checked";}else{$absI = "";}
if(isset($lanISTEX) && $lanISTEX == "oui") {$lanI = " checked";}else{$lanI = "";}
if(isset($mocISTEX) && $mocISTEX == "oui") {$mocI = " checked";}else{$mocI = "";}

?>
<p class="form-inline">
Via CrossRef : 
<input type="checkbox" id="chk17" class="form-control" style="height: 15px;" onclick="option1()" name="doiCrossRef" value="oui"<?php echo $iod;?>>&nbsp;<label for="chk17">DOI</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk0" class="form-control" style="height: 15px;" onclick="option1()" name="revue" value="oui"<?php echo $rev;?>>&nbsp;<label for="chk0">Revue</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk1" class="form-control" style="height: 15px;" onclick="option1()" name="vnp" value="oui"<?php echo $pnv;?>>&nbsp;<label for="chk1">Vol/Num/Pag</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk24" class="form-control" style="height: 15px;" onclick="option1()" name="lanCrossRef" value="oui"<?php echo $lanC;?>>&nbsp;<label for="chk24">Langue</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk2" class="form-control" style="height: 15px;" onclick="option1()" name="financement" value="oui"<?php echo $fin;?>>&nbsp;<label for="chk2">Financement</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk3" class="form-control" style="height: 15px;" onclick="option1()" name="anr" value="oui"<?php echo $tan;?>>&nbsp;<label for="chk3">ANR</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk4" class="form-control" style="height: 15px;" onclick="option1()" name="anneepub" value="oui"<?php echo $apu;?>>&nbsp;<label for="chk4">Année de publication</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk5" class="form-control" style="height: 15px;" onclick="option1()" name="mel" value="oui"<?php echo $lem;?>>&nbsp;<label for="chk5">Date de mise en ligne</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!--<input type="checkbox" id="chk6" class="form-control" style="height: 15px;" onclick="option1()" name="mocCrossRef" value="oui"<?php echo $mocC;?>>&nbsp;<label for="chk6">Mots-clés généralistes</label>--><br>
Via Pubmed : 
<input type="checkbox" id="chk11" class="form-control" style="height: 15px;" onclick="option1()" name="absPubmed" value="oui"<?php echo $absP;?>>&nbsp;<label for="chk11">Résumé</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk12" class="form-control" style="height: 15px;" onclick="option1()" name="lanPubmed" value="oui"<?php echo $lanP;?>>&nbsp;<label for="chk12">Langue</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk13" class="form-control" style="height: 15px;" onclick="option1()" name="mocPubmed" value="oui"<?php echo $mocP;?>>&nbsp;<label for="chk13">Mots-clés</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk7" class="form-control" style="height: 15px;" onclick="option1()" name="pmid" value="oui"<?php echo $pmi;?>>&nbsp;<label for="chk7">PMID</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!--<input type="checkbox" id="chk8" class="form-control" style="height: 15px;" onclick="option1()" name="pmcid" disabled="disabled" value="oui"<?php echo $pmc;?>>&nbsp;<label for="chk8">PMCID</label>--><br>
Via ISTEX : 
<input type="checkbox" id="chk14" class="form-control" style="height: 15px;" onclick="option1()" name="absISTEX" value="oui"<?php echo $absI;?>>&nbsp;<label for="chk14">Résumé</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk15" class="form-control" style="height: 15px;" onclick="option1()" name="lanISTEX" value="oui"<?php echo $lanI;?>>&nbsp;<label for="chk15">Langue</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk16" class="form-control" style="height: 15px;" onclick="option1()" name="mocISTEX" value="oui"<?php echo $mocI;?>>&nbsp;<label for="chk16">Mots-clés</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<br><br>
<b>Etape 2 : Compléter et corriger les auteurs :</b><br>
<?php
if(isset($ordAut) && $ordAut == "oui") {$tua = " checked";}else{$tua = "";}
if(isset($iniPre) && $iniPre == "oui") {$erp = " checked";}else{$erp = "";}
if(isset($vIdHAL) && $vIdHAL == "oui") {$idv = " checked";}else{$idv = "";}
if(isset($rIdHAL) && $rIdHAL == "oui") {$idh = " checked";}else{$idh = "";}
if(isset($rIdHALArt) && $rIdHALArt == "oui") {$idhart = " checked";}else{$idhart = "";}
if(isset($rIdHALCom) && $rIdHALCom == "oui") {$idhcom = " checked";}else{$idhcom = "";}
if(isset($rIdHALCou) && $rIdHALCou == "oui") {$idhcou = " checked";}else{$idhcou = "";}
if(isset($rIdHALOuv) && $rIdHALOuv == "oui") {$idhouv = " checked";}else{$idhouv = "";}
if(isset($rIdHALDou) && $rIdHALDou == "oui") {$idhdou = " checked";}else{$idhdou = "";}
if(isset($rIdHALBre) && $rIdHALBre == "oui") {$idhbre = " checked";}else{$idhbre = "";}
if(isset($rIdHALRap) && $rIdHALRap == "oui") {$idhrap = " checked";}else{$idhrap = "";}
if(isset($rIdHALThe) && $rIdHALThe == "oui") {$idhthe = " checked";}else{$idhthe = "";}
if(isset($rIdHALPre) && $rIdHALPre == "oui") {$idhpre = " checked";}else{$idhpre = "";}
if(isset($rIdHALPub) && $rIdHALPub == "oui") {$idhpub = " checked";}else{$idhpub = "";}
?>
<input type="checkbox" id="chk18" class="form-control" style="height: 15px;" onclick="option2()" name="ordAut" value="oui"<?php echo $tua;?>>&nbsp;<label for="chk18">Corriger l'ordre des auteurs</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk19" class="form-control" style="height: 15px;" onclick="option2()" name="iniPre" value="oui"<?php echo $erp;?>>&nbsp;<label for="chk19">Remplacer l'initiale du premier prénom par son écriture complète</label><br>
<input type="checkbox" id="chk36" class="form-control" style="height: 15px;" onclick="option2()" name="vIdHAL" value="oui"<?php echo $idv;?>>&nbsp;<label for="chk36">Repérer les IdHAL des auteurs d'une collection dont la forme de validité est autre que VALID ou OLD</label>
<br><label style="padding-left:40px; font-weight:normal; font-style: italic">Pour ce test de repérage, choisissez une période de recherche raisonnable pour limiter le nombre total de notices. L'incrément de recherche n'a aucune incidence puisque toutes les notices comportant au moins un auteur de la collection sont traitées.</label><br><br>
<input type="checkbox" id="chk25" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHAL" value="oui"<?php echo $idh;?>>&nbsp;<label for="chk25">IdHAL :</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk26" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALArt" value="oui"<?php echo $idhart;?>>&nbsp;<label for="chk26">Articles</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk27" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALCom" value="oui"<?php echo $idhcom;?>>&nbsp;<label for="chk27">Communications</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk28" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALCou" value="oui"<?php echo $idhcou;?>>&nbsp;<label for="chk28">Chapitres d'ouvrages</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk29" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALOuv" value="oui"<?php echo $idhouv;?>>&nbsp;<label for="chk29">Ouvrages</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk30" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALDou" value="oui"<?php echo $idhdou;?>>&nbsp;<label for="chk30">Directions d'ouvrages</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk31" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALBre" value="oui"<?php echo $idhbre;?>>&nbsp;<label for="chk31">Brevets</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk32" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALRap" value="oui"<?php echo $idhrap;?>>&nbsp;<label for="chk32">Rapports</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk33" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALThe" value="oui"<?php echo $idhthe;?>>&nbsp;<label for="chk33">Thèses</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk34" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALPre" value="oui"<?php echo $idhpre;?>>&nbsp;<label for="chk34">Preprints</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk35" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALPub" value="oui"<?php echo $idhpub;?>>&nbsp;<label for="chk35">Autres publications</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<br><label style="padding-left:40px; font-weight:normal; font-style: italic">Cette option permet de rechercher d'éventuels IdHAL auteur absents des notices; l'incrément de recherche n'a aucune incidence puisque toutes les notices sont traitées.</label>
<br><br>
<b>Etape 3 : Déposer le texte intégral des articles :</b><br>
<?php
if((isset($lienext) && $lienext == "oui" || !isset($_POST["valider"])) && $noliene != "oui") {$ext = " checked";}else{$ext = "";}
if(isset($manuaut) && $manuaut == "oui") {$man = " checked";}else{$man = "";}
if(isset($noliene) && $noliene == "oui") {$noe = " checked";}else{$noe = "";}
if(isset($embargo) && $embargo == "6mois") {$m6 = " checked";}else{$m6 = "";}
if(isset($embargo) && $embargo == "12mois") {$m12 = " checked";}else{$m12 = "";}
?>
<b>Restreindre l'affichage aux notices&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
<input type="checkbox" id="chk20" class="form-control" style="height: 15px;" onclick="option3()" name="lienext" value="oui"<?php echo $ext;?>>&nbsp;<label for="chk20">ayant un lien externe</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk21" class="form-control" style="height: 15px;" onclick="option3();affich_form();" name="noliene" value="oui"<?php echo $noe;?>>&nbsp;<label for="chk21">sans lien externe</label><br>
<p class="form-inline" id="embargo" style="display: block;">
<b>Embargo :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
<input type="radio" id="chk22" class="form-control" style="height: 15px;" onclick="option3()" name="embargo" value="6mois"<?php echo $m6;?>>&nbsp;<label for="chk22">6 mois</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" id="chk23" class="form-control" style="height: 15px;" onclick="option3()" name="embargo" value="12mois"<?php echo $m12;?>>&nbsp;<label for="chk22">12 mois</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;←──────────────┘
</p><p class="form-inline"><br>
<input type="checkbox" id="chk10" class="form-control" style="height: 15px;" onclick="option3()" name="manuaut" value="oui"<?php echo $man;?>>&nbsp;<label for="chk10">Manuscrit auteurs (fichiers sous la forme doi_normalisé.pdf)</label> -> <label for="urlpdf">URL du serveur :</label>
<input type="text" id="urlpdf" class="form-control" style="height: 25px; width: 300px;" name="urlPDF" value="<?php echo $urlPDF;?>" size="30"><span id="urlserveur" style="color:red;"></span><br>
<br><br>
<!--<input type="submit" value="Vérifier les DOI" name="verifDOI">-->
<input type="hidden" value="1" name="iMin">
<input type="hidden" value="" name="iMax">
<input type="hidden" value="1" name="iMinRet">
<input type="hidden" value="" name="iMaxRet">
<input type="submit" class="form-control btn btn-md btn-primary" value="Valider" name="valider">
</form>
<script>
if (document.getElementById("chk21").checked == false) {
  document.getElementById("embargo").style.display = "none";
}else{
  document.getElementById("embargo").style.display = "block";
}
</script>
<br>
<?php
//Etape 1
if ((isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) && $opt1 == "oui") {
  //authentification CAS ou autre ?
  if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false || strpos($_SERVER['HTTP_HOST'], 'ecobio') !== false) {
    include('./_connexion.php');
  }else{
    require_once('./CAS_connect.php');
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
  //$urlHAL = "https://api.archives-ouvertes.fr/search/?q=collCode_s:%22".$team."%22".$txtApa."%20AND%20submitType_s:%22file%22&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:%22ART%22&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,anrProjectReference_s,journalTitle_s,journalIssn_s,journalValid_s,docid,journalIssn_s,journalEissn_s,abstract_s,language_s,inPress_bool,label_xml,submittedDate_s&sort=halId_s%20asc";
  //$urlHAL = "https://api.archives-ouvertes.fr/search/?q=halId_s:%22hal-01795811%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:%22ART%22&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,anrProjectReference_s,journalTitle_s,journalIssn_s,journalValid_s,docid,journalIssn_s,journalEissn_s,abstract_s,language_s,inPress_bool,label_xml,submittedDate_s,submitType_s,docType_s&sort=halId_s%20asc";
  $urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(%22ART%22%20OR%20%22COMM%22%20OR%20%22COUV%22)&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,anrProjectReference_s,journalTitle_s,journalIssn_s,journalValid_s,docid,journalIssn_s,journalEissn_s,abstract_s,language_s,inPress_bool,label_xml,submittedDate_s,submitType_s,docType_s&sort=halId_s%20asc";
  //echo $urlHAL.'<br>';
  //$contents = file_get_contents($urlHAL);
  //$results = json_decode($contents);
  //$resHAL = json_decode($contents, true);
  askCurl($urlHAL, $arrayHAL);
  //$numFound = $results->response->numFound;
  $numFound = $arrayHAL["response"]["numFound"];
  if ($iMax > $numFound) {$iMax = $numFound;}
  echo '<b>Total de '.$numFound.' référence(s)' ;
  if ($numFound != 0) {
     if ($numFound != 0) {echo " : affichage de ".$iMin." à ".$iMax."</b>&nbsp;<i>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</i><br><br>";}
  }
  //var_dump($resHAL);
  //print_r($results);
  //var_dump($resHAL["response"]["docs"][0]);
  echo "<div id='cpt'></div>";
  echo "<table class='table table-striped table-bordered table-hover;'><tr>";
  //echo "<table style='border-collapse: collapse; width: 100%' border='1px' bordercolor='#999999' cellpadding='5px' cellspacing='5px'><tr>";
  echo "<td rowspan='2' bordercolor='#808080' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>ID</b></td>";
  echo "<td colspan='3' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Liens</b></td>";
  if($apa == "oui") {
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>AP</b></td>";
  }
  echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>1er auteur</b></td>";
  if($revue == "oui") {
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Revue</b></td>" ;
  }
  if($vnp == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Vol(n)pp</b></td>";
  }
  if($pmid == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>PMID</b></td>" ;
  }
  if($pmcid == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>PMCID</b></td>";
  }
  if($anneepub == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Année de publication</b></td>";
  }
  if($mel == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Date de mise en ligne</b></td>";
  }
  if($mocPubmed == "oui") {//Seulement HAL et PM
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Mots-clés</b></td>";
  }else{
    //if($mocCrossRef == "oui") {
      if($mocISTEX == "oui") {
        echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Mots-clés</b></td>";
      }else{
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Mots-clés</b></td>" ;
      }
    //}else{
    //  if($mocISTEX == "oui") {
    //    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Mots-clés</b></td>";
    //  }else{
    //    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Mots-clés</b></td>";
    //  }
    //}
  }
  if($absPubmed == "oui") {
    if($absISTEX == "oui") {
      echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Résumé</b></td>";
    }else{
      echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Résumé</b></td>";
    }
  }else{
    if($absISTEX == "oui") {
      echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Résumé</b></td>";
    }
  }
  if($lanPubmed == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Langue</b></td>";
  }
  if($lanISTEX == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Langue</b></td>";
  }
  if($lanCrossRef == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Langue</b></td>";
  }
  if($financement == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Financement</b></td>";
  }
  if($anr == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>ANR</b></td>";
  }
  echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Action</b></td>";
  echo "</tr><tr>";
  //echo "<td bordercolor='#808080'></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>DOI</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  //echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b></b></td>";
  //echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
  //echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  if($vnp == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  }
  if($pmid == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Pubmed</b></td>";
  }
  //if($pmcid == "oui") {
    //echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    //echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Pubmed</b></td>";
  //}
  if($anneepub == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  }
  if($mel == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  }
  if($mocPubmed == "oui") {//Seulement HAL et PM
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Pubmed</b></td>";
  }else{
    //if($mocCrossRef == "oui") {
      if($mocISTEX == "oui") {
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>ISTEX</b></td>";
      }else{
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
      }
    //}else{
    //  if($mocISTEX == "oui") {
    //    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    //    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>ISTEX</b></td>";
    //  }else{
    //    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    //  }
    //}
  }
  if($absPubmed == "oui") {
    if($absISTEX == "oui") {
      echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Pubmed</b></td>";
      echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>ISTEX</b></td>";
    }else{
      echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Pubmed</b></td>";
    }
  }else{
    if($absISTEX == "oui") {
      echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>ISTEX</b></td>";
    }
  }
  if($lanPubmed == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Pubmed</b></td>";
  }
  if($lanISTEX == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>ISTEX</b></td>";
  }
  if($lanCrossRef == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  }
  if($financement == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  }
  if($anr == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  }
  //echo "<td style='text-align: center;'><b></b></td>";
  echo "</tr>";
  //foreach($results->response->docs as $entry){
  $iMinTab = $iMin - 1;
  for($cpt = $iMinTab; $cpt < $iMax; $cpt++) {
    progression($cpt+1, $iMax);
    //if ($arrayHAL["response"]["docs"][$cpt]["halId_s"] == "hal-01509702") {
      $lignAff = "no";//Test affichage ou non de la ligne du tableau
      $textAff = "";//Texte de la ligne du tableau
      $titre = $arrayHAL["response"]["docs"][$cpt]["title_s"][0];//Titre de la notice
      $doi = "";//DOI de la notice
      $halID = "";//HalId de la notice
      $doiCR = "";//DOI CR
      $lienHAL = "";//Lien renvoyant vers la notice HAL
      $lienDOI = "";//Lien renvoyant vers la notice via le DOI
      $bapa = false;//Booléen HAL (true/false) précisant si c'est une notice à paraître > inPress_bool
      if (isset($arrayHAL["response"]["docs"][$cpt]["inPress_bool"])) {$bapa = $arrayHAL["response"]["docs"][$cpt]["inPress_bool"];}
      $prenomHAL = "";//Prénom du 1er auteur HAL
      $nomHAL = "";//Nom du 1er auteur HAL
      unset($arrayCR);//Tableau de métadonnées CR
      $prenomCR = "";//Prénom du 1er auteur CR
      $nomCR = "";//Nom du 1er auteur CR
      $corr = "";//Contenu de la cellule à afficher pour la correspondance du premier auteur > ok/pas ok
      $pubCR = "";//Date de publication CR
      $lienCR = "";//Lien renvoyant vers la notice CR
      $volCR = "";//Numéro de volume CR
      $numCR = "";//Numéro de fascicule CR
      $pagCR = "";//Pagination CR
      $lanCR = "";//Langue CR
      $finCR = "";//Financement CR
      $annCR = "";//Année de publication CR
      $melCR = "";//Date de mise en ligne CR
      $mocCR = "";//Mots-clés CR
      $mocPM = "";//Mots-clés PM
      $absPM = "";//Résumé PM
      $lanPM = "";//Langue PM
      $pmiPM = "";//PMID PM
      $doiHAL = "";//DOI HAL
      $volHAL = "";//Numéro de volume HAL
      $numHAL = "";//Numéro de fascicule HAL
      $pagHAL = "";//Pagination HAL
      $finHAL = "";//Financement HAL
      $annHAL = "";//Année de publication HAL
      $melHAL = "";//Date de mise en ligne HAL
      $mocHAL = "";//Mots-clés HAL
      $absHAL = "";//Résumé HAL
      $lanHAL = "";//Langue HAL
      $pmiHAL = "";//PMID HAL
      $revHAL = "";//Titre de la revue HAL
      $revCRIH = "";//Titre de la revue retrouvé via l'ISSN ou l'EISSN et CR ou HAL > fonction rechRevueISSN de CR_DOI_ISSN_HAL_Rev.php
      $docidHAL = "";//Identifiant document HAL
      $docidCRIH = "";//Identifiant du document retrouvé via l'ISSN ou l'EISSN et CR ou HAL > fonction rechRevueISSN de CR_DOI_ISSN_HAL_Rev.php
      $issnCRIH = "";//ISSN de la revue retrouvé via le DOI et CR > fonction rechRevueISSN de CR_DOI_ISSN_HAL_Rev.php
      $issnHAL = "";//ISSN HAL
      $eissnCRIH = "";//EISSN de la revue retrouvé via le DOI et CR > fonction rechRevueISSN de CR_DOI_ISSN_HAL_Rev.php
      $eissnHAL = "";//EISSN HAL
      //$results = ""; //Ancien tableau des résultats obtenus avec utilisation initiale de file_get_contents > utilisation par la suite de la fonction askCurl
      $Fnm = "";//Chemin + nom du fichier qui va servir à créer le XML pour les modifications
      $pcMocPM = 0;//Indice de similarité des mots-clés entre HAL et PM
      $pcMocIS = 0;//Indice de similarité des mots-clés entre HAL et ISTEX
      $absIS = "";//Résumé ISTEX
      $lanIS = "";//Langue ISTEX
      $textAff .= "<tr>";
      //if (isset($entry->halId_s)) {
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
        //if (@file_get_contents(askCurl($urlCR, $arrayCR))) {
          //$contents = file_get_contents($urlCR);
          //$contents = utf8_encode($contents); 
          //$results = json_decode($contents, TRUE);
          //var_dump($results);
          askCurl($urlCR, $arrayCR);
          
          if(isset($arrayCR["message"]["author"][0]["given"])) {
            $prenomCR = prenomCompInit($arrayCR["message"]["author"][0]["given"]);
          }
          if(isset($arrayCR["message"]["author"][0]["family"])) {
            $nomCR = nomCompEntier($arrayCR["message"]["author"][0]["family"]);
          }
          if(isset($arrayCR["message"]["published-print"]["date-parts"][0][0])) {
            $pubCR = $arrayCR["message"]["published-print"]["date-parts"][0][0];
          }
          $lienCR = "";
        }else{//Problème de DOI
          $rechDOI = "";//Recherche du DOI à partir du titre viar CR avec la fonction rechTitrDOI de CR_DOI_Levenshtein.php
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
        
      }else{//Pas de DOI trouvé dans HAL > on va essayer de le retrouver grâce au titre et l'API CR si la recherche a bien été demandée initialement
        $doiHAL = "inconnu";
        if(isset($doiCrossRef) && $doiCrossRef == "oui") {
          $titreTest = $arrayHAL["response"]["docs"][$cpt]["title_s"][0];
          $urlCR = "https://api.crossref.org/works?query.title=".urlencode($titreTest);
          //echo urlencode($titreTest);
          if (@file_get_contents($urlCR)) {
            askCurl($urlCR, $arrayCR);
            //if ($arrayCR["message"]["items"][0]["publisher"] != "PERSEE Program") {
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
            //}
          }
        }
      }
      $cptTab = $cpt + 1;
      $textAff .= "<td style='text-align: center;'>".$cptTab."</td>";
      $textAff .= "<td style='text-align: center;'>".$lienDOI."</td>";
      $textAff .= "<td style='text-align: center;'>".$lienHAL."</td>";
      $textAff .= "<td style='text-align: center;'>".$lienCR."</td>";
      if($apa == "oui") {
        if ($bapa) {
          $textAff .= "<td style='text-align: center;'>AP</td>";
        }else{
          $textAff .= "<td style='text-align: center;'>&nbsp;</td>";
        }
      }
      $textAff .= "<td style='text-align: center;'>".$corr."</td>";

      //Revue
      if($revue == "oui") {
        if (isset($doi) && $doi != "" && $lienCR != "DOI inconnu de Crossref") {
          if (isset($arrayHAL["response"]["docs"][$cpt]["journalValid_s"]) && $arrayHAL["response"]["docs"][$cpt]["journalValid_s"] != "VALID" ) {
            if (isset($arrayHAL["response"]["docs"][$cpt]["journalTitle_s"]) && $arrayHAL["response"]["docs"][$cpt]["journalTitle_s"] != "" ) {
              $revHAL = $arrayHAL["response"]["docs"][$cpt]["journalTitle_s"];
            }
            if (isset($arrayHAL["response"]["docs"][$cpt]["docid"]) && $arrayHAL["response"]["docs"][$cpt]["docid"] != "" ) {
              $docidHAL = $arrayHAL["response"]["docs"][$cpt]["docid"];
            }
            if (isset($arrayHAL["response"]["docs"][$cpt]["journalIssn_s"]) && $arrayHAL["response"]["docs"][$cpt]["journalIssn_s"] != "" ) {
              $issnHAL = $arrayHAL["response"]["docs"][$cpt]["journalIssn_s"];
            }
            if (isset($arrayHAL["response"]["docs"][$cpt]["journalEissn_s"]) && $arrayHAL["response"]["docs"][$cpt]["journalEissn_s"] != "" ) {
              $eissnHAL = $arrayHAL["response"]["docs"][$cpt]["journalEissn_s"];
            }
            rechRevueISSN($doi, $issnCRIH, $eissnCRIH, $docidCRIH, $revCRIH);
            $why = $arrayHAL["response"]["docs"][$cpt]["journalValid_s"]." <> ".$docidCRIH;
            $why = str_replace("'", " ", $why);
            if ($docidCRIH != "") {
              $textAff .= "<td style='text-align: center;'><img alt='".$why."' title='".$why."' src='./img/pasok.jpg'></td>";
            }else{
              $textAff .= "<td style='text-align: center;'>&nbsp;</td>";
            }
          }else{
            $textAff .= "<td style='text-align: center;'><img alt='OK' src='./img/ok.jpg'></td>";
          }
        }else{
          $textAff .= "<td></td>";
        }
      } 
      //Vol/num/pag
      if($vnp == "oui") {
        $volHAL = "";
        $volCR = "";
        $numHAL = "";
        $numCR = "";
        $pagHAL = "";
        $pagCR = "";
        if(isset($arrayHAL["response"]["docs"][$cpt]["volume_s"])) {
          $volHAL = $arrayHAL["response"]["docs"][$cpt]["volume_s"];
        }
        if(isset($arrayHAL["response"]["docs"][$cpt]["issue_s"][0])) {
          $numHAL = $arrayHAL["response"]["docs"][$cpt]["issue_s"][0];
        }
        if(isset($arrayHAL["response"]["docs"][$cpt]["page_s"])) {
          $pagHAL = $arrayHAL["response"]["docs"][$cpt]["page_s"];
        }
        $textAff .= "<td style='text-align: center;'>".$volHAL."(".$numHAL.")".$pagHAL."</td>";
        if(isset($arrayCR["message"]["volume"]) && $doiCR == "" && isset($doi) && $doi != "") {
          $volCR = $arrayCR["message"]["volume"];
        }
        if(isset($arrayCR["message"]["issue"]) && $doiCR == "" && isset($doi) && $doi != "") {
          $numCR = $arrayCR["message"]["issue"];
        }
        if(isset($arrayCR["message"]["page"]) && $doiCR == "" && isset($doi) && $doi != "") {
          $pagCR = $arrayCR["message"]["page"];
        }
        $deb = "";
        $fin = "";
        if ($volCR."(".$numCR.")".$pagCR != "()") {
          if ($volHAL == "" && $volCR != "") {
            $deb = "<b>";$fin = "</b>";        }
          if ($numHAL == "" && $numCR != "") {
            $deb = "<b>";$fin = "</b>";
          }
          //On complète la pagination HAL par CR sauf si les champs vol et num sont déjà complétés dans HAL
          if ($pagCR != "" && $volHAL == "" && $numHAL == "") {
            $deb = "<b>";$fin = "</b>";
          }
          $textAff .= "<td style='text-align: center; background-color: #eeeeee;'>".$deb.$volCR."(".$numCR.")".$pagCR.$fin."</td>";
        }else{
          $textAff .= "<td style='text-align: center; background-color: #eeeeee;'>&nbsp;</td>";
        }
      }

      //PMID
      if($pmid == "oui") {
        if (isset($doi) && $doi != "") {
          if(isset($arrayHAL["response"]["docs"][$cpt]["pubmedId_s"])) {
            $pmiHAL = $arrayHAL["response"]["docs"][$cpt]["pubmedId_s"];
          }
          $urlNCBI = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&retmode=json&term=".$doi."[lid]";
          //$cntNCBI = file_get_contents($urlNCBI);
          //$cntNCBI = utf8_encode($cntNCBI);
          //$resNCBI = json_decode($cntNCBI, true);
          //var_dump($resNCBI);
          askCurl($urlNCBI, $arrayNCBI);
          $numNCBI = $arrayNCBI["esearchresult"]["count"];

          if(isset($arrayNCBI["esearchresult"]["idlist"][0])) {
            $pmiPM = $arrayNCBI["esearchresult"]["idlist"][0];
            rechMetadoPMID($pmiPM, $absPM, $mcMESH, $lanPM, $mocPM);
          }
          $deb = "";
          $fin = "";
          if ($pmiHAL != $pmiPM) {$deb = "<b>";$fin = "</b>";}
          $textAff .= "<td style='text-align: center;'>".$pmiHAL."</td>";
          $textAff .= "<td style='text-align : center; background-color: #eeeeee;'>".$deb.$pmiPM.$fin."</td>";
        }else{
          $textAff .= "<td></td>";
          $textAff .= "<td></td>";
        }
      }

      //Année de publication
      if($anneepub == "oui") {
        $txtAnnCR = "";
        if(isset($arrayHAL["response"]["docs"][$cpt]["producedDate_s"])) {
          $annHAL = $arrayHAL["response"]["docs"][$cpt]["producedDate_s"];
        }
        if(isset($arrayCR["message"]["issued"]["date-parts"][0]) && $doiCR == "" && isset($doi) && $doi != "") {
          $annCR = $arrayCR["message"]["issued"]["date-parts"][0];
          foreach ($annCR as $value) {
            if ($value < 10) {$value = '0'.$value;}
            $txtAnnCR .= $value.'-';
          }
          $txtAnnCR = substr($txtAnnCR, 0, strlen($txtAnnCR)-1);
        }
        $deb = "";
        $fin = "";
        if (isset($annCR[0])) {
          $testAnnCR = $annCR[0];
          //echo $annHAL;
          if ($testAnnCR < substr($annHAL, 0, 4)) {
            //dates différentes mais pas de modification à effectuer
          }else{
            if (($testAnnCR == substr($annHAL, 0, 4) && (strlen($txtAnnCR) > strlen($annHAL))) || (substr($annHAL, 0, 4) != substr($txtAnnCR, 0, 4) && substr($txtAnnCR, 0, 4) != "" && substr($annHAL, 5, 2) != substr($txtAnnCR, 5, 2) && substr($txtAnnCR, 5, 2) != "" && substr($annHAL, 8, 2) != substr($txtAnnCR, 8, 2) && substr($txtAnnCR, 8, 2) != "" )) {
              $deb = "<b>";$fin = "</b>";
            }
          }
        }
        $textAff .= "<td style='text-align: center;'>".$annHAL."</td>";
        $textAff .= "<td style='text-align : center; background-color: #eeeeee;'>".$deb.$txtAnnCR.$fin."</td>";
      }

      //Date de mise en ligne
      if($mel == "oui") {
        //var_dump($arrayCR["message"]["created"]);
        $txtMelCR = "";
        if(isset($arrayHAL["response"]["docs"][$cpt]["ePublicationDate_s"])) {
          $melHAL = $arrayHAL["response"]["docs"][$cpt]["ePublicationDate_s"];
        }
        if(isset($arrayCR["message"]["created"]["date-parts"][0]) && $doiCR == "" && isset($doi) && $doi != "") {
          $melCR = $arrayCR["message"]["created"]["date-parts"][0];
          foreach ($melCR as $value) {
            if ($value < 10) {$value = '0'.$value;}
            $txtMelCR .= $value.'-';
          }
          $txtMelCR = substr($txtMelCR, 0, strlen($txtMelCR)-1);
        }
        $deb = "";
        $fin = "";
        if (isset($melCR[0])) {
          $testMelCR = $melCR[0];
          if (($testMelCR == substr($melHAL, 0, 4) && (strlen($txtMelCR) > strlen($melHAL))) || (substr($melHAL, 0, 4) != substr($txtMelCR, 0, 4) && substr($txtMelCR, 0, 4) != "" && substr($melHAL, 5, 2) != substr($txtMelCR, 5, 2) && substr($txtMelCR, 5, 2) != "" && substr($melHAL, 8, 2) != substr($txtMelCR, 8, 2) && substr($txtMelCR, 8, 2) != "" )) {
            $deb = "<b>";$fin = "</b>";
          }
        }
        if ($arrayHAL["response"]["docs"][$cpt]["docType_s"] != "COMM") {
          $textAff .= "<td style='text-align: center;'>".$melHAL."</td>";
          $textAff .= "<td style='text-align : center; background-color: #eeeeee;'>".$deb.$txtMelCR.$fin."</td>";
        }else{//pas de date de mise en ligne pour les COMM
          $textAff .= "<td style='text-align: center;'>&nbsp;</td>";
          $textAff .= "<td style='text-align: center;'>&nbsp;</td>";
        }
      }
      
      //ISTEX
      if($absISTEX == "oui" || $lanISTEX == "oui" || $mocISTEX == "oui") {
        if (isset($doi) && $doi != "") {
          rechMetadoISTEX($doi, $absIS, $lanIS, $mocIS, $lanmocIS);
        }
      }

      //Mots-clés
      $txtMocHAL = "";
      $txtMocHALaff = "";
      $txtMocCRaff = "";
      if(isset($arrayHAL["response"]["docs"][$cpt]["keyword_s"])) {
        $mocHAL = $arrayHAL["response"]["docs"][$cpt]["keyword_s"];
        foreach ($mocHAL as $value) {
          $txtMocHAL .= $value.', ';
        }
        $txtMocHAL = substr($txtMocHAL, 0, strlen($txtMocHAL)-2);
        $txtMocHALred = substr($txtMocHAL, 0, 15)." ...";
        $txtMocHALaff = "<a class=info2 onclick='return false' href='#'>".$txtMocHALred.")<span>".$txtMocHAL."</span></a>";
      }

      /*
      if($mocCrossRef == "oui") {
        //var_dump($arrayCR["message"]["subject"]);
        $txtMocCR = "";
        if(isset($arrayCR["message"]["subject"]) && $doiCR == "" && isset($doi) && $doi != "") {
          $mocCR = $arrayCR["message"]["subject"];
          foreach ($mocCR as $value) {
            $txtMocCR .= $value.', ';
          }
          $txtMocCR = substr($txtMocCR, 0, strlen($txtMocCR)-2);
          $txtMocCRred = substr($txtMocCR, 0, 15)." ...";
          $txtMocCRaff = "<a class=info2 onclick='return false' href='#'>".$txtMocCRred.")<span>".$txtMocCR."</span></a>";
        }
        //echo "<td>".$txtMocHALaff."</td>";
        //echo "<td style='background-color: #eeeeee;'>".$txtMocCRaff."</td>";
      }
      */
      
      //Mots-clés PM
      $txtMocPMaff = "";
      if(isset($mocPM) && $mocPM != "") {
        if ($txtMocHAL != $mocPM) {
          if ($txtMocHAL != "") {$mocHALred = strtolower(substr($txtMocHAL, 0, 250));}else{$mocHALred= "";}
          if ($mocPM != "") {$mocPMred = strtolower(substr($mocPM, 0, 250));}else{$mocPMred= "";}

          if ($mocHALred != $mocPMred) {
            $pcMocPM = (250-levenshtein_utf8($mocHALred, $mocPMred))*100/250;
            $why = 'Indice de similarité : '.$pcMocPM.' %';
            $txtMocPMaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
          }else{
            $txtMocPMaff = "<img alt='OK' src='./img/ok.png'>";
          }
        }
      }
      
      //Mots-clés ISTEX
      $txtMocISaff = "";
      if(isset($mocIS) && $mocIS != "") {
        if ($txtMocHAL != $mocIS) {
          if ($txtMocHAL != "") {$mocHALred = strtolower(substr($txtMocHAL, 0, 250));}else{$mocHALred= "";}
          if ($mocIS != "") {$mocISred = strtolower(substr($mocIS, 0, 250));}else{$mocISred= "";}

          if ($mocHALred != $mocISred) {
            $pcMocIS = (250-levenshtein_utf8($mocHALred, $mocISred))*100/250;
            $why = 'Indice de similarité : '.$pcMocIS.' %';
            $txtMocISaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
          }else{
            $txtMocISaff = "<img alt='OK' src='./img/ok.png'>";
          }
        }
      }

      //Affichage des mots-clés
      if($mocPubmed == "oui") {//Only HAL and PM
        $textAff .= "<td>".$txtMocHALaff."</td>";
        $textAff .= "<td style='background-color: #eeeeee; text-align: center;'>".$txtMocPMaff."</td>";
      }else{
        //if($mocCrossRef == "oui") {
          if($mocISTEX == "oui") {
            $textAff .= "<td>".$txtMocHALaff."</td>";
            //echo "<td style='background-color: #eeeeee;'>".$txtMocCRaff."</td>";
            $textAff .= "<td style='background-color: #eeeeee; text-align: center;'>".$txtMocISaff."</td>";
          }else{
            $textAff .= "<td>".$txtMocHALaff."</td>";
            //echo "<td style='background-color: #eeeeee;'>".$txtMocCRaff."</td>";
          }
        //}else{
        //  if($mocISTEX == "oui") {
        //    echo "<td>".$txtMocHALaff."</td>";
        //    echo "<td style='background-color: #eeeeee; text-align: center;'>".$txtMocISaff."</td>";
        //  }else{
        //    echo "<td>".$txtMocHALaff."</td>";
        //  }
        //}
      }
      
      $indLimAbs = 95;
      //Résumé Pubmed
      $txtAbsPMaff = "";
      if($absPubmed == "oui") {
        if(isset($arrayHAL["response"]["docs"][$cpt]["abstract_s"][0])) {
          $absHAL = $arrayHAL["response"]["docs"][$cpt]["abstract_s"][0];
        }
        if ($absHAL != "") {$absHALred = strtolower(substr($absHAL, 0, 250));}else{$absHALred= "";}
        if ($absPM != "") {$absPM = str_ireplace("<br>", " ", $absPM); $absPMred = strtolower(substr($absPM, 0, 250));}else{$absPMred = "";}
        $why = "";
        $pc = (250-levenshtein_utf8($absHALred, $absPMred))*100/250;
        //if ($absHAL != "" && $absPM == "") {$why = "Résumé HAL présent - Résumé PM absent";}
        if ($absHAL != "" && $absPM != "") {
          //if ($absHALred != $absPMred) {
          if ($pc < $indLimAbs) {
            $why = 'Indice de similarité : '.$pc.' %';
            $txtAbsPMaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
          }else{
            $txtAbsPMaff = "<img alt='OK' src='./img/ok.png'>";
          }
        }
      }
      
      //Résumé ISTEX
      $txtAbsISaff = "";
      if($absISTEX == "oui") {
        if(isset($arrayHAL["response"]["docs"][$cpt]["abstract_s"][0])) {
          $absHAL = $arrayHAL["response"]["docs"][$cpt]["abstract_s"][0];
        }
        if ($absHAL != "") {$absHALred = strtolower(substr($absHAL, 0, 250));}else{$absHALred= "";}
        if ($absIS != "") {$absISred = strtolower(substr($absIS, 0, 250));}else{$absISred = "";}
        $why = "";
        $pc = (250-levenshtein_utf8($absHALred, $absISred))*100/250;
        if ($absHAL != "" && $absIS != "") {
          //if ($absHALred != $absISred) {
          if ($pc < $indLimAbs) {
            $why = 'Indice de similarité : '.$pc.' %';
            $txtAbsISaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
          }else{
            $txtAbsISaff = "<img alt='OK' src='./img/ok.png'>";
          }
        }
      }
      
      //Affichage des résumés
      if($absPubmed == "oui") {
        if($absISTEX == "oui") {
          $textAff .= "<td style='text-align: center; background-color: #eeeeee; color: #999999;'>".$txtAbsPMaff."</td>";
          $textAff .= "<td style='text-align: center; background-color: #eeeeee; color: #999999;'>".$txtAbsISaff."</td>";
        }else{
          $textAff .= "<td style='text-align: center; background-color: #eeeeee; color: #999999;'>".$txtAbsPMaff."</td>";
        }
      }else{
        if($absISTEX == "oui") {
          $textAff .= "<td style='text-align: center; background-color: #eeeeee; color: #999999;'>".$txtAbsPMaff."</td>";
        }
      }
      
      
      //Langue Pubmed
      $txtLanPMaff = "";
      if($lanPubmed == "oui") {
        if(isset($arrayHAL["response"]["docs"][$cpt]["language_s"][0])) {
          $lanHAL = $arrayHAL["response"]["docs"][$cpt]["language_s"][0];
        }
        if ($lanPM != "") {$lanPMred = substr($lanPM, 0, 2);}else{$lanPMred = "";}

        if ($lanHAL != $lanPMred && $lanPMred != "") {
          $why = $lanHAL." <> ".$lanPMred;
          $txtLanPMaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
        }else{
          if ($lanPMred != "") {
            $txtLanPMaff = "<img alt='OK' src='./img/ok.png'>";
          }else{
            $txtLanPMaff = "&nbsp;";
          }
        }
      }

      //Langue ISTEX
      $txtLanISaff = "";
      if($lanISTEX == "oui") {
        if(isset($arrayHAL["response"]["docs"][$cpt]["language_s"][0])) {
          $lanHAL = $arrayHAL["response"]["docs"][$cpt]["language_s"][0];
        }
        if ($lanIS != "") {$lanISred = substr($lanIS, 0, 2);}else{$lanISred = "";}

        if ($lanHAL != $lanISred && $lanISred != "") {
          $why = $lanHAL." <> ".$lanISred;
          $txtLanISaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
        }else{
          if ($lanISred != "") {
            $txtLanISaff = "<img alt='OK' src='./img/ok.png'>";
          }else{
            $txtLanISaff = "&nbsp;";
          }
        }
      }
      
      //Langue CrossRef
      if(isset($arrayCR["message"]["language"])) {$lanCR = $arrayCR["message"]["language"];}
      $txtLanCRaff = "";
      if($lanCrossRef == "oui") {
        if(isset($arrayHAL["response"]["docs"][$cpt]["language_s"][0])) {
          $lanHAL = $arrayHAL["response"]["docs"][$cpt]["language_s"][0];
        }
        if ($lanCR != "") {$lanCRred = $lanCR;}else{$lanCRred= "";}
        
        if ($lanHAL != $lanCRred && $lanCRred != "") {
          $why = $lanHAL." <> ".$lanCRred;
          $txtLanCRaff = "<img alt='".$why."' title='".$why."' src='./img/pasok.png'>";
        }else{
          if ($lanCRred != "") {
            $txtLanCRaff = "<img alt='OK' src='./img/ok.png'>";
          }else{
            $txtLanCRaff = "&nbsp;";
          }
        }
      }

      //Affichage de la langue
      if($lanPubmed == "oui") {
        $textAff .= "<td style='text-align: center; background-color: #eeeeee; color: #999999;'>".$txtLanPMaff."</td>";
      }
      if($lanISTEX == "oui") {
        $textAff .= "<td style='text-align: center; background-color: #eeeeee; color: #999999;'>".$txtLanISaff."</td>";
      }
      if($lanCrossRef == "oui") {
        $textAff .= "<td style='text-align: center; background-color: #eeeeee; color: #999999;'>".$txtLanCRaff."</td>";
      }
      
      //Financement
      if($financement == "oui") {
        $txtFinHAL = "";
        $txtFinHALaff = "";
        $txtFinCR = "";
        $txtFinCRaff = "";
        if(isset($arrayHAL["response"]["docs"][$cpt]["funding_s"][0])) {
          $finHAL = $arrayHAL["response"]["docs"][$cpt]["funding_s"];
          foreach ($finHAL as $value) {
            $txtFinHAL .= $value.'; ';
          }
          $txtFinHAL = substr($txtFinHAL, 0, strlen($txtFinHAL)-2);
          $txtFinHALred = substr($txtFinHAL, 0, 15)." ...";
          $txtFinHALaff = "<a class=info2 onclick='return false' href='#'>".$txtFinHALred.")<span>".$txtFinHAL."</span></a>";
        }
        $textAff .= "<td>".$txtFinHALaff."</td>";
        if(isset($arrayCR["message"]["funder"])) {
          $finCR = $arrayCR["message"]["funder"];
          foreach ($finCR as $value) {
            if(isset($value["award"][0]) && $value["award"][0] != "Not applicable") {
              $txtFinCR .= $value["award"][0].', ';
            }
            if ($value["name"] != "Not applicable") {$txtFinCR .= $value["name"].'; ';}
          }
          $txtFinCR = substr($txtFinCR, 0, strlen($txtFinCR)-2);
          $txtFinCRred = substr($txtFinCR, 0, 15)." ...";
          $txtFinCRaff = "<a class=info2 onclick='return false' href='#'>".$txtFinCRred.")<span>".$txtFinCR."</span></a>";
        }
        $textAff .= "<td style='background-color: #eeeeee;'>".$txtFinCRaff."</td>";
      }

      //ANR
      if($anr == "oui") {
        $txtAnrHAL = "";
        $txtAnrHALAff = "";
        $txtAnrCR = "";
        $txtAnrCRAff = "";
        if (isset($arrayHAL["response"]["docs"][$cpt]["anrProjectReference_s"])){
          $txtAnrHAL = $arrayHAL["response"]["docs"][$cpt]["anrProjectReference_s"];
          foreach ($txtAnrHAL as $t) {
            $txtAnrHALAff .= $t."; ";
          }
        $txtAnrHALAff = substr($txtAnrHALAff, 0, strlen($txtAnrHALAff)-2);
        }
        if (isset($txtFinCR) && $txtFinCR != "") {
          if (strpos($txtFinCR, "ANR-") !== false) {
            $txtAnrCR = explode(";", $txtFinCR);
            foreach ($txtAnrCR as $t) {
              $txtAnrCRtab = explode(",", $t);
              foreach ($txtAnrCRtab as $ta) {
                if (strpos($ta, "ANR-") !== false) {
                  $txtAnrCRAff = trim($ta)."; ";
                }
              }
            }
          }
          $txtAnrCRAff = substr($txtAnrCRAff, 0, strlen($txtAnrCRAff)-2);
        }
        $textAff .= "<td>".$txtAnrHALAff."</td>";
        $textAff .= "<td style='background-color: #eeeeee;'>".$txtAnrCRAff."</td>";
      }
      
      //Actions
      $lienMAJ = "";
      $actsMAJ = "";
      $actMaj = "ok";
      $raisons = "";
      $tei = $arrayHAL["response"]["docs"][$cpt]["label_xml"];
      //echo $tei;
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
      
      //suppression éventuel noeud <listBibl type="references">
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
      
      // Si DOI HAL absent mais trouvé via CrossRef
      if(isset($doiCrossRef) && $doiCrossRef == "oui"  && $doiHAL == "inconnu" && $doiCR != "") {
        $insert = "";
        $elts = $xml->getElementsByTagName("ref");
        foreach ($elts as $elt) {
          if ($elt->hasAttribute("type")) {
            $quoi = $elt->getAttribute("type");
            if ($quoi == "publisher") {
              insertNode($xml, $doiCR, "biblStruct", "ref", "idno", "type", "doi", "", "", "iB");
              $insert = "ok";
            }
          }
        }
        if ($insert == "") {
          insertNode($xml, $doiCR, "biblStruct", "monogr", "idno", "type", "doi", "", "", "aC");
        }
        $xml->save($Fnm);
        $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
        include "./CrossHAL_actions.php";
        $testMaj = "ok";
        foreach($ACTIONS_LISTE as $tab) {
          if (in_array($halID, $tab) && in_array("MAJ_DOI",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "DOI, ";}
        }
        if ($testMaj == "ok") {$actsMAJ .= "MAJ_DOI~";}
      }

      //Si revue VALID trouvée alors qu'INCOMING à la base dans la notice
      if($revue == "oui" && ($docidCRIH != $docidHAL && $docidCRIH != "")) {
        //docid
        $docid = $docidCRIH;
        insertNode($xml, $docid, "monogr", "title", "idno", "type", "halJournalId", "status", "VALID", "iB");

        //nom revue
        $rev = "";
        if ($revCRIH != "") {
          $rev = $revCRIH;
        }else{
          if ($revHAL != "") {
            $rev = $revHAL;
          }
        }
        insertNode($xml, $rev, "monogr", "title", "title", "level", "j", "", "", "iB");

        //issn
        $issn = "";
        if ($issnCRIH != "") {
          $issn = $issnCRIH;
        }else{
          if ($issnHAL != "") {
            $issn = $issnHAL;
          }
        }
        insertNode($xml, $issn, "monogr", "title", "idno", "type", "issn", "", "", "iB");
        
        //eissn
        $eissn = "";
        if ($eissnCRIH != "") {
          $eissn = $eissnCRIH;
        }else{
          if ($eissnHAL != "") {
            $eissn = $eissnHAL;
          }
        }
        insertNode($xml, $eissn, "monogr", "title", "idno", "type", "eissn", "", "", "iB");

        $xml->save($Fnm);
        $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
        include "./CrossHAL_actions.php";
        $testMaj = "ok";
        foreach($ACTIONS_LISTE as $tab) {
          if (in_array($halID, $tab) && in_array("MAJ_REV",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "revue, ";}
        }
        if ($testMaj == "ok") {$actsMAJ .= "MAJ_REV~";}
      }

      
      //Si vnp différents
      if($vnp == "oui" && ($volCR."(".$numCR.")".$pagCR != "()" && $volHAL."(".$numHAL.")".$pagHAL != $volCR."(".$numCR.")".$pagCR)) {
      //if($vnp == "oui" && ($volHAL != $volCR && $volCR != "" || $numHAL != $numCR && $numCR != "" || $pagHAL != $pagCR && $pagCR != "")) {
      //if($volHAL != $volCR && $volCR != "" || $arrayHAL["response"]["docs"][$cpt]["halId_s"] == "hal-01509702") {
      //if($volHAL != $volCR) {
        //On complète tous les champs HAL vides par CR
        if ($volHAL == "" && $volCR != "") {
          insertNode($xml, $volCR, "imprint", "date", "biblScope", "unit", "volume", "", "", "iB");
          $xml->save($Fnm);
          $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
          include "./CrossHAL_actions.php";
          $testMaj = "ok";
          foreach($ACTIONS_LISTE as $tab) {
            if (in_array($halID, $tab) && in_array("MAJ_VOL",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "volume, ";}
          }
          if ($testMaj == "ok") {$actsMAJ .= "MAJ_VOL~";}
          }
        if ($numHAL == "" && $numCR != "") {
          insertNode($xml, $numCR, "imprint", "date", "biblScope", "unit", "issue", "", "", "iB");
          $xml->save($Fnm);
          $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
          include "./CrossHAL_actions.php";
          $testMaj = "ok";
          foreach($ACTIONS_LISTE as $tab) {
            if (in_array($halID, $tab) && in_array("MAJ_NUM",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "numéro, ";}
          }
          if ($testMaj == "ok") {$actsMAJ .= "MAJ_NUM~";}
        }
        //On complète la pagination HAL par CR sauf si les champs vol et num sont déjà complétés dans HAL
        if ($pagCR != "" && $volHAL == "" && $numHAL == "") {
          insertNode($xml, $pagCR, "imprint", "date", "biblScope", "unit", "pp", "", "", "iB");
          $xml->save($Fnm);
          $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
          include "./CrossHAL_actions.php";
          $testMaj = "ok";
          foreach($ACTIONS_LISTE as $tab) {
            if (in_array($halID, $tab) && in_array("MAJ_PAG",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "pagination, ";}
          }
          if ($testMaj == "ok") {$actsMAJ .= "MAJ_PAG~";}
        }
      }
      
      //Si financements différents
      if($financement == "oui" && $txtFinCR != $txtFinHAL && $txtFinCR != "" && $txtFinHAL == "") {
        //noeud forcément absent puisque $txtFinHAL = "" > recherche du noeud 'biblFull' pour insérer les nouvelles données au bon emplacement
        $impr = $xml->getElementsByTagName('biblFull');
        foreach ($impr as $elt) {
          foreach($elt->childNodes as $item) { 
            if ($item->nodeName == "titleStmt") {
              $txtFinCRtab = explode(";", $txtFinCR);
              foreach($txtFinCRtab as $f) {
                $bif = $xml->createElement("funder");
                $cTn = $xml->createTextNode(trim($f));
                $bif->appendChild($cTn);
                $item->appendChild($bif);
              }
              break 2;
            }
          }
        }
        
        $xml->save($Fnm);
        $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
        include "./CrossHAL_actions.php";
        $testMaj = "ok";
        foreach($ACTIONS_LISTE as $tab) {
          if (in_array($halID, $tab) && in_array("MAJ_FIN",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "financement, ";}
        }
        if ($testMaj == "ok") {$actsMAJ .= "MAJ_FIN~";}
      }
      
      //ANR
      
      if($anr == "oui" && $txtAnrCRAff != $txtAnrHALAff && $txtAnrCRAff != "") {
        $anrTab = explode(";", $txtAnrCRAff);
        foreach ($anrTab as $a) {
          if (substr($a, 0, 4) == "ANR-") {
            $urlANR = "https://api.archives-ouvertes.fr/ref/anrproject/?q=reference_s:%22".trim($a)."%22&fl=title_s,valid_s,yearDate_s,docid,callTitle_s,acronym_s";
            $contANR = file_get_contents($urlANR);
            $resANR = json_decode($contANR, true);
            $numANR = $resANR["response"]["numFound"];
            //echo 'toto : '.$numANR.' - '.trim($a).'<br>';
            if ($numANR == 1) {
              $idANR = $resANR["response"]["docs"][0]["docid"];
            }
          }
        }
      }
      
      
      //Si article "à paraître" mais Vol(n)pp CR non nul > suppression subtype=inPress
      if($bapa && $txtAnnCR != "") {
        insertNode($xml, $txtAnnCR, "imprint", "date", "date", "type", "datePub", "", "", "iB");
        $xml->save($Fnm);
        $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
        include "./CrossHAL_actions.php";
        $testMaj = "ok";
        foreach($ACTIONS_LISTE as $tab) {
          if (in_array($halID, $tab) && in_array("MAJ_APA",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "'à paraître', ";}
        }
        if ($testMaj == "ok") {$actsMAJ .= "MAJ_APA~";}
      }
      
      //Si années de publication différentes
      if ($anneepub == "oui") {
        //On vérifie d'abord que, pour l&apos;année en cours uniquement : si la date de publication CrossRef YYYY est < date de publication HAL YYYY (ne pas tenir compte des MM et DD) => ne pas modifier (l&apos;info CrossRef n&apos;est sans doute pas encore à jour)
        if (isset($annCR[0])) {
          $testAnnCR = $annCR[0];
          if ($testAnnCR < substr($annHAL, 0, 4)) {
            //dates différentes mais pas de modification à effectuer
          }else{
            if (($testAnnCR == substr($annHAL, 0, 4) && (strlen($txtAnnCR) > strlen($annHAL))) || (substr($annHAL, 0, 4) != substr($txtAnnCR, 0, 4) && substr($txtAnnCR, 0, 4) != "" && substr($annHAL, 5, 2) != substr($txtAnnCR, 5, 2) && substr($txtAnnCR, 5, 2) != "" && substr($annHAL, 8, 2) != substr($txtAnnCR, 8, 2) && substr($txtAnnCR, 8, 2) != "" )) {
              insertNode($xml, $txtAnnCR, "imprint", "date", "date", "type", "datePub", "", "", "iB");
              $xml->save($Fnm);
              $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
              include "./CrossHAL_actions.php";
              $testMaj = "ok";
              foreach($ACTIONS_LISTE as $tab) {
                if (in_array($halID, $tab) && in_array("MAJ_ANN",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "année de publication, ";}
              }
              if ($testMaj == "ok") {$actsMAJ .= "MAJ_ANN~";}
            }
          }
        }
      }

      //Si dates de mise en ligne différentes
      if($mel == "oui" && $arrayHAL["response"]["docs"][$cpt]["docType_s"] != "COMM") {
        if (isset($melCR[0])) {
          $testMelCR = $melCR[0];
          if (($testMelCR == substr($melHAL, 0, 4) && (strlen($txtMelCR) > strlen($melHAL))) || (substr($melHAL, 0, 4) != substr($txtMelCR, 0, 4) && substr($txtMelCR, 0, 4) != "" && substr($melHAL, 5, 2) != substr($txtMelCR, 5, 2) && substr($txtMelCR, 5, 2) != "" && substr($melHAL, 8, 2) != substr($txtMelCR, 8, 2) && substr($txtMelCR, 8, 2) != "" )) {
            insertNode($xml, $txtMelCR, "imprint", "date", "date", "type", "dateEpub", "", "", "aC");
            $xml->save($Fnm);
            $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
            include "./CrossHAL_actions.php";
            $testMaj = "ok";
            foreach($ACTIONS_LISTE as $tab) {
              if (in_array($halID, $tab) && in_array("MAJ_MEL",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "date de mise en ligne, ";}
            }
            if ($testMaj == "ok") {$actsMAJ .= "MAJ_MEL~";}
          }
        }
      }

      //Ajout de mots-clés
      $indLim = 90;
      
      //PM
      if($mocPubmed == "oui") {
        //if ($pcMocPM < $indLim && $mocPM != "") {
        if (empty($mocHAL) && $mocPM != "") {
          //si noeud présent
          $mocTab = explode(",", $mocPM);
          $keyw = $xml->getElementsByTagName('keywords')->item(0);
          if(isset($keyw)) {
            foreach($mocTab as $i) {
              if (stripos(",".$txtMocHAL, $i) === false) { //mot-clé PM n'existe pas déjà parmi ceux d'HAL
                $bimoc = $xml->createElement("term");
                $moc = $xml->createTextNode(trim($i));
                $bimoc->setAttribute("xml:lang", "en");
                $bimoc->appendChild($moc);
                $keyw->appendChild($bimoc);
              }
            }
          }else{
            //si noeud absent > recherche du noeud 'textClass' pour insérer les nouvelles données au bon emplacement        
            $textC = $xml->getElementsByTagName('textClass');
            foreach ($textC as $elt) {
              foreach($elt->childNodes as $item) { 
                if ($item->hasChildNodes()) {
                  $childs = $item->childNodes;
                  foreach($childs as $i) {
                    $name = $i->parentNode->nodeName;
                    if ($name == "classCode" && stripos(",".$txtMocHAL, $i->parentNode->nodeValue) === false) {//insertion nvx noeuds si mot-clé PM n'existe pas déjà parmi ceux de HAL
                      $cE = $xml->createElement("keywords");
                      $cE->setAttribute("scheme", "author");
                      $xml->appendChild($cE);
                      $textC0 = $xml->getElementsByTagName("textClass")->item(0);
                      $textC0->insertBefore($cE, $i->parentNode);
                    }
                    break 2;
                  }
                }
              }
            }
            $keyw = $xml->getElementsByTagName('keywords')->item(0);
            foreach ($mocTab as $i) {
              if (stripos(",".$txtMocHAL, $i) === false) { //mot-clé PM n'existe pas déjà parmi ceux d'HAL
                $bimoc = $xml->createElement("term");
                $moc = $xml->createTextNode(trim($i));
                $bimoc->setAttribute("xml:lang", "en");
                $bimoc->appendChild($moc);
                $keyw->appendChild($bimoc);
              }
            }
          }
          $xml->save($Fnm);
          $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
          include "./CrossHAL_actions.php";
          $testMaj = "ok";
          foreach($ACTIONS_LISTE as $tab) {
            if (in_array($halID, $tab) && in_array("MAJ_MOC",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "mots-clés ";}
          }
          if ($testMaj == "ok") {$actsMAJ .= "MAJ_MOC~";}
        }
      }
      
      //ISTEX
      if($mocISTEX == "oui") {
        //if ($pcMocIS < $indLim && $mocIS != "") {
        if (empty($mocHAL) && $mocIS != "") {
          //si noeud présent
          $mocTab = explode(",", $mocIS);
          $keyw = $xml->getElementsByTagName('keywords')->item(0);
          if(isset($keyw)) {
            foreach($mocTab as $i) {
              if (stripos(",".$txtMocHAL, $i) === false) { //mot-clé PM n'existe pas déjà parmi ceux d'HAL
                $bimoc = $xml->createElement("term");
                $moc = $xml->createTextNode(trim($i));
                $bimoc->setAttribute("xml:lang", "en");
                $bimoc->appendChild($moc);
                $keyw->appendChild($bimoc);
              }
            }
          }else{
            //si noeud absent > recherche du noeud 'textClass' pour insérer les nouvelles données au bon emplacement        
            $textC = $xml->getElementsByTagName('textClass');
            foreach ($textC as $elt) {
              foreach($elt->childNodes as $item) { 
                if ($item->hasChildNodes()) {
                  $childs = $item->childNodes;
                  foreach($childs as $i) {
                    $name = $i->parentNode->nodeName;
                    if ($name == "classCode" && stripos(",".$txtMocHAL, $i->parentNode->nodeValue) === false) {//insertion nvx noeuds si mot-clé PM n'existe pas déjà parmi ceux de HAL
                      $cE = $xml->createElement("keywords");
                      $cE->setAttribute("scheme", "author");
                      $xml->appendChild($cE);
                      $textC0 = $xml->getElementsByTagName("textClass")->item(0);
                      $textC0->insertBefore($cE, $i->parentNode);
                    }
                    break 2;
                  }
                }
              }
            }
            $keyw = $xml->getElementsByTagName('keywords')->item(0);
            foreach ($mocTab as $i) {
              if (stripos(",".$txtMocHAL, $i) === false) { //mot-clé PM n'existe pas déjà parmi ceux d'HAL
                $bimoc = $xml->createElement("term");
                $moc = $xml->createTextNode(trim($i));
                $bimoc->setAttribute("xml:lang", "en");
                $bimoc->appendChild($moc);
                $keyw->appendChild($bimoc);
              }
            }
          }
          $xml->save($Fnm);
          $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
          include "./CrossHAL_actions.php";
          $testMaj = "ok";
          foreach($ACTIONS_LISTE as $tab) {
            if (in_array($halID, $tab) && in_array("MAJ_MOC",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "mots-clés ";}
          }
          if ($testMaj == "ok") {$actsMAJ .= "MAJ_MOC~";}
        }
      }
      
      //Ajout d'un résumé > Pubmed prioritaire par rapport à ISTEX
      $indLimAbs = 95;
      $pcPM = 100;
      $psIS = 100;
      if ($absHAL != "") {$absHALred = strtolower(substr($absHAL, 0, 250));}else{$absHALred= "";}
      if ($absPM != "") {$absPM = str_ireplace("<br>", " ", $absPM); $absPMred = strtolower(substr($absPM, 0, 250));}else{$absPMred = "";}
      if ($absIS != "") {$absIS = str_ireplace("<br>", " ", $absIS); $absISred = strtolower(substr($absIS, 0, 250));}else{$absISred = "";}
      if ($absHAL != "" && $absPM != "") {
        if ($absHALred != $absPMred) {
          $pcPM = (250-levenshtein_utf8($absHALred, $absPMred))*100/250;
        }
      }
      if ($absHAL != "" && $absIS != "") {
        if ($absHALred != $absISred) {
          $pcIS = (250-levenshtein_utf8($absHALred, $absISred))*100/250;
        }
      }
      //echo 'HAL : '.$absHAL.'<br><br>'.'PM : '.$absPM.'<br>'.$pcPM.'<br>';
      //echo 'HAL : '.$absHALred.'<br><br>'.'PM : '.$absPMred.'<br>'.$pcPM.'<br>';
      if($absPubmed == "oui" && $absPM != $absHAL && $absPM != "" && $pcPM < $indLimAbs) {
        insertNode($xml, $absPM, "profileDesc", "", "abstract", "xml:lang", "en", "", "", "aC");
        $xml->save($Fnm);
        $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
        include "./CrossHAL_actions.php";
        $testMaj = "ok";
        foreach($ACTIONS_LISTE as $tab) {
          if (in_array($halID, $tab) && in_array("MAJ_ABS",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "résumé ";}
        }
        if ($testMaj == "ok") {$actsMAJ .= "MAJ_ABS~";}
      }else{
        if($absISTEX == "oui" && $absIS != $absHAL && $absIS != ""  && $pcIS < $indLimAbs) {
          insertNode($xml, $absIS, "profileDesc", "", "abstract", "xml:lang", "en", "", "", "aC");
          $xml->save($Fnm);
          $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
          include "./CrossHAL_actions.php";
          $testMaj = "ok";
          foreach($ACTIONS_LISTE as $tab) {
            if (in_array($halID, $tab) && in_array("MAJ_ABS",$tab)) {$actMaj = "no"; $testMaj = "no";}
          }
          if ($testMaj == "ok") {$actsMAJ .= "MAJ_ABS~";}
        }
      }
      
      //Modification de la langue
      $lanTest = "";
      $lanTestred = "";
      if($lanPubmed == "oui") {$lanTest = $lanPM; $lanTestred = $lanPMred;}
      if($lanISTEX == "oui") {$lanTest = $lanIS; $lanTestred = $lanISred;}
      if($lanCrossRef == "oui") {$lanTest = $lanCR; $lanTestred = $lanCRred;}
      if($lanTest != "" && $lanTestred != $lanHAL && $lanTestred != "") {
        if ($lanTest == "eng" || $lanTest == "en") {
          insertNode($xml, "English", "langUsage", "", "language", "ident", "en", "", "", "aC");
          insertNode($xml, "international", "notesStmt", "", "note", "type", "audience", "n", "2", "aC");
          $xml->save($Fnm);
          $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
          include "./CrossHAL_actions.php";
          $testMaj = "ok";
          foreach($ACTIONS_LISTE as $tab) {
            if (in_array($halID, $tab) && in_array("MAJ_LAN",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "langue ";}
          }
          if ($testMaj == "ok") {$actsMAJ .= "MAJ_LAN~";}
        }else{
          insertNode($xml, $countries[$lanPM], "langUsage", "", "language", "ident", substr($lanPM,0,2), "", "", "aC");
          insertNode($xml, "national", "notesStmt", "", "note", "type", "audience", "n", "3", "aC");
          $xml->save($Fnm);
          $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
          include "./CrossHAL_actions.php";
          $testMaj = "ok";
          foreach($ACTIONS_LISTE as $tab) {
            if (in_array($halID, $tab) && in_array("MAJ_LAN",$tab)) {$actMaj = "no"; $testMaj = "no";}
          }
          if ($testMaj == "ok") {$actsMAJ .= "MAJ_LAN~";}
        }
      }
      
      //Modification de l'audience
      

      
      //si PMID différents
      if($pmid == "oui" && $pmiPM != $pmiHAL && $pmiPM != "") {
        //echo 'toto !';
        //insertNode($xml, $pmiPM, "biblStruct", "monogr", "idno", "type", "pubmed", "", "", "aC");
        //insertNode($xml, $pmiPM, "biblStruct", "ref", "idno", "type", "pubmed", "", "", "iB");
        insertNode($xml, $pmiPM, "biblStruct", "idno", "idno", "type", "pubmed", "", "", "iB");
        //insertNode($xml, $pmiPM, "biblStruct", "idno", "idno", "type", "pubmed", "", "", "aC");

        $xml->save($Fnm);
        $lienMAJ = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
        include "./CrossHAL_actions.php";
        $testMaj = "ok";
        foreach($ACTIONS_LISTE as $tab) {
          if (in_array($halID, $tab) && in_array("MAJ_PMI",$tab)) {$actMaj = "no"; $testMaj = "no"; $raisons .= "PMID ";}
        }
        if ($testMaj == "ok") {$actsMAJ .= "MAJ_PMI~";}
      }

      if ($colact == "ok") {
        if ($lienMAJ != "") {
          $textAff .= "<td style='text-align: center;'>";
          //if ($lienMAJ != "") {echo "<span id='maj".$halID."'><a target='_blank' href='".$lienMAJ."' onclick='majok(\"".$doi."\")'><img alt='MAJ' src='./img/add.png'></a></span>";}
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
            $submDate = mktime(0, 0, 0, substr($submDate, 5, 2), substr($submDate, 8, 2), substr($submDate, 0, 4));
            $limDate = time() - $nbjours*86400;
            if ($submDate <= $limDate) {//Il est possible de faire les modifications car la date limite est dépassée
              $textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienMAJ."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"".$actsMAJ."\" });majok(\"".$halID."\");'><img alt='MAJ' src='./img/add.png'></a></span></center>";
            }else{
              $textAff .= "<center><img alt='Embargo' title='Embargo: interdit de modifier cette notice car la date \"whenSubmitted\" < 90 jours' src='./img/addEmbargo.png'></center>";
            }
          }else{
            $textAff .= "<center><img title=\"La(les) modification(s) n'est(ne sont) pas envisageables car une ou plusieurs métadonnées a(ont) été modifiée(s) depuis moins d'une semaine : ".$raisons."\" src='./img/addOK.png'></center>";
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
      }
      //$cpt++;
    //}
  }
  echo "</table><br>";
  echo "<script>";
  echo "  document.getElementById('cpt').style.display = \"none\";";
  echo "</script>";
  
  if ($iMax != $numFound) {
    echo "<form name='troli' action='CrossHAL_E2P.php' method='post'>";
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
    echo "<input type='hidden' value='".$absPubmed."' name='absPubmed'>";
    echo "<input type='hidden' value='".$lanPubmed."' name='lanPubmed'>";
    echo "<input type='hidden' value='".$mocPubmed."' name='mocPubmed'>";
    echo "<input type='hidden' value='".$pmid."' name='pmid'>";
    echo "<input type='hidden' value='".$pmcid."' name='pmcid'>";
    echo "<input type='hidden' value='".$absISTEX."' name='absISTEX'>";
    echo "<input type='hidden' value='".$lanISTEX."' name='lanISTEX'>";
    echo "<input type='hidden' value='".$mocISTEX."' name='mocISTEX'>";
    echo "<input type='hidden' value='".$manuaut."' name='manuaut'>";
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
    echo "<form name='troli' action='CrossHAL_E2P.php' method='post'>";
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
    echo "<input type='hidden' value='".$absPubmed."' name='absPubmed'>";
    echo "<input type='hidden' value='".$lanPubmed."' name='lanPubmed'>";
    echo "<input type='hidden' value='".$mocPubmed."' name='mocPubmed'>";
    echo "<input type='hidden' value='".$pmid."' name='pmid'>";
    echo "<input type='hidden' value='".$pmcid."' name='pmcid'>";
    echo "<input type='hidden' value='".$absISTEX."' name='absISTEX'>";
    echo "<input type='hidden' value='".$lanISTEX."' name='lanISTEX'>";
    echo "<input type='hidden' value='".$mocISTEX."' name='mocISTEX'>";
    echo "<input type='hidden' value='".$manuaut."' name='manuaut'>";
    echo "<input type='hidden' value='".$lienext."' name='lienext'>";
    echo "<input type='hidden' value='".$noliene."' name='noliene'>";
    echo "<input type='hidden' value='".$embargo."' name='embargo'>";
    echo "<input type='hidden' value='Valider' name='valider'>";
    echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>";
  }
}

//Etape 2
if ((isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) && $opt2 == "oui") {
  //authentification CAS ou autre ?
  if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false || strpos($_SERVER['HTTP_HOST'], 'ecobio') !== false) {
    include('./_connexion.php');
  }else{
    require_once('./CAS_connect.php');
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
  if (isset($rIdHAL) && $rIdHAL == "oui") {//Etape 2 > recherche des IdHAL des auteurs
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
    $urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(".$rechIdHAL.")&fl=authFirstName_s,authLastName_s,doiId_s,halId_s,producedDate_s,docid,label_xml,authIdHalFullName_fs,authIdHasStructure_fs,authIdHal_s,&sort=halId_s%20desc";
    //$increment = 10000;
  }else{
    if($vIdHAL != "oui") {
      $urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa."&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(%22ART%22%20OR%20%22COMM%22%20OR%20%22COUV%22)&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,anrProjectReference_s,journalTitle_s,journalIssn_s,journalValid_s,docid,journalIssn_s,journalEissn_s,abstract_s,language_s,label_xml,submittedDate_s,submitType_s,docType_s&sort=halId_s%20desc";
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
    die ('<b><font color="red">Désolé ! Le code collection '.$team.' ne permet pas de récupérer un docid HAL valide.</font></b><br><br>');
  }
  if ($iMax > $numFound) {$iMax = $numFound;}
  echo '<b>Total de '.$numFound.' référence(s)';
  if ($numFound != 0) {
    //if($rIdHAL != "oui" && $vIdHAL != "oui") {
    if($vIdHAL != "oui") {
      echo " : affichage de ".$iMin." à ".$iMax."</b>&nbsp;<i>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</i><br><br>";
    }else{
      echo " : affichage de ".$iMin." à ".$numFound."</b>&nbsp;<i>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</i><br><br>";
    }
  }
  echo "<div id='cpt'></div>";
  echo "<table class='table table-striped table-bordered table-hover;'>";
  //echo "<table style='border-collapse: collapse; width: 100%' border='1px' bordercolor='#999999' cellpadding='5px' cellspacing='5px'><tr>";
  echo "<tr><td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>ID</b></td>";
  if($rIdHAL == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Liens</b></td>";
  }else{
    if($rIdHAL != "oui") {
      if ($vIdHAL != "oui") {
        echo "<td colspan='3' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Liens</b></td>";
      }else{
        //echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Prénom</b></td>";
        //echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Nom</b></td>";
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Nom complet</b></td>";
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>IdHAL</b></td>";
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>AuréHAL</b></td>";
        echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Lien auteur HAL</b></td>";
      }
    }
  }
  if($apa == "oui") {
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>AP</b></td>";
  }
  if($ordAut == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>10 premiers auteurs</b></td>";
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Nb auteurs</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Action auteurs</b></td>";
  }
  if($iniPre == "oui") {
    echo "<td colspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Premier prénom auteurs</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Action prénoms</b></td>";
  }
  if($rIdHAL == "oui") {
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Formulaire HAL</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Nom</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Prénom</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>IdHAL suggéré</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>AuréHAL</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Nom de domaine</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>DocID</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>IdHAL</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Affiliation</b></td>";
    echo "<td rowspan='2' style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Année (de publication)</b></td>";
  }
  echo "</tr>";
  echo "</tr><tr>";
  if($vIdHAL != "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>DOI</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
  }
  if($rIdHAL != "oui" && $vIdHAL != "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  }
  if($ordAut == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  }
  if($iniPre == "oui") {
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
    echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>CR</b></td>";
  }
  echo "</tr>";
  if($rIdHAL != "oui" && $vIdHAL != "oui") {
    $iMinTab = $iMin - 1;
    for($cpt = $iMinTab; $cpt < $iMax; $cpt++) {
      progression($cpt+1, $iMax);
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
      $textAff .= "<tr style='text-align: center;'><td>".$cptTab."</td>";
      $textAff .= "<td style='text-align: center;'>".$lienDOI."</td>";
      $textAff .= "<td style='text-align: center;'>".$lienHAL."</td>";
      $textAff .= "<td style='text-align: center;'>".$lienCR."</td>";
      if($apa == "oui") {
        if ($bapa) {
          $textAff .= "<td style='text-align: center;'>AP</td>";
        }else{
          $textAff .= "<td style='text-align: center;'>&nbsp;</td>";
        }
      }
      if($ordAut == "oui") {
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
        $teiRes = '<?xml version="1.0" encoding="UTF-8"?>'.$tei;
        //$teiRes = str_replace('<TEI xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', '<TEI xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.tei-c.org/ns/1.0 http://api.archives-ouvertes.fr/documents/aofr-sword.xsd" xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', $teiRes);
        //$Fnm = "./XML/".normalize(wd_remove_accents($titre)).".xml";
        $Fnm = "./XML/".$arrayHAL["response"]["docs"][$cpt]["halId_s"].".xml";
        $xml = new DOMDocument( "1.0", "UTF-8" );
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;
        $xml->loadXML($teiRes);
        if ($doi != "") {
          //echo normalize(strtolower(wd_remove_accents($autHALTot))).'<br>'.normalize(strtolower(wd_remove_accents($autCRTot))).'<br>';
          if ((normalize(strtolower(wd_remove_accents($autHALTot))) == normalize(strtolower(wd_remove_accents($autCRTot)))) && ($totAutCR == count($arrayHAL["response"]["docs"][$cpt]["authLastName_s"]))) {
          //if (($autHALTot == $autCRTot) && (count($arrayCR["message"]["author"]) == count($arrayHAL["response"]["docs"][$cpt]["authLastName_s"]))) {
            //Tout correspond > ok
            $textAff .= "<td style='text-align: center;'><img alt='Done' src='./img/done.png'></td>";
          }else{
            $lignAff = "ok";
            $textAff .= "<td style='text-align: center;'>";
            //echo "A modifier";
            //if ($lienMAJAut != "") {echo "<span id='maj".$halID."'><a target='_blank' href='".$lienMAJAut."' onclick='majok(\"".$doi."\")'><img alt='MAJ' src='./img/add.png'></a></span>";}
            include "./CrossHAL_actions.php";
            $actMaj = "ok";
            foreach($ACTIONS_LISTE as $tab) {
              if (in_array($halID, $tab) && in_array("MAJ_AUT",$tab)) {$actMaj = "no";}
            }
            if ($actMaj == "ok") {
              $textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienMAJAut."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_AUT\" });majok(\"".$halID."\");'><img alt='MAJ' src='./img/add.png'></a></span></center>";
            }else{
              $textAff .= "<center><img src='./img/addOK.png'></center>";
            }
            $xml->save($Fnm);
            $textAff .= "</td>";
          }
        }else{
          $textAff .= "<td style='text-align: center;'>&nbsp;</td>";
        }
      }
      
      if($iniPre == "oui") {
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
        $teiRes = '<?xml version="1.0" encoding="UTF-8"?>'.$tei;
        //$teiRes = str_replace('<TEI xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', '<TEI xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.tei-c.org/ns/1.0 http://api.archives-ouvertes.fr/documents/aofr-sword.xsd" xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', $teiRes);
        //$Fnm = "./XML/".normalize(wd_remove_accents($titre)).".xml";
        $Fnm = "./XML/".$arrayHAL["response"]["docs"][$cpt]["halId_s"].".xml";
        $xml = new DOMDocument( "1.0", "UTF-8" );
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;
        $xml->loadXML($teiRes);
        $nbPreHAL = count(explode(",", $prenomsHAL));
        $nbPreCR = count(explode(",", $prenomsCR));
        //if ($prenomsHAL != $prenomsCR && $prenomsCR != "" && strpos($prenomsCR, ".") === false) {
        //echo(normalize(strtolower(wd_remove_accents($prenomsHAL))).'<br>'.normalize(strtolower(wd_remove_accents($prenomsCR))));
        if ($doi != "") {
          if (normalize(strtolower(wd_remove_accents($prenomsHAL))) != normalize(strtolower(wd_remove_accents($prenomsCR))) && $prenomsCR != "" && preg_match("/^[a-zA-Z]+\.|[a-zA-Z], [a-zA-Z]+\./", $prenomsCR) != 1 && $nbPreHAL == $nbPreCR) {
            //Les prénoms sont différents
            //echo "<td style='text-align: center;'>";
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
                          if($cmpIdhali == "") {
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
                          if($cmpIdhals == "") {
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
                            if($cmpINC == "") {
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
                            if($cmpOLD == "") {
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
            $textAff .= "<td style='text-align: center;'>";
            $lienMAJPre = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$cpt]["halId_s"];
            if ($lienMAJPre != "") {
              //echo "<span id='maj".$halID."'><a target='_blank' href='".$lienMAJPre." 'onclick='majok(\"".$doi."\")'><img alt='MAJ' src='./img/add.png'></a></span>";
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
                $submDate = mktime(0, 0, 0, substr($submDate, 5, 2), substr($submDate, 8, 2), substr($submDate, 0, 4));
                $limDate = time() - $nbjours*86400;
                if ($submDate <= $limDate) {//Il est possible de faire les modifications car la date limite est dépassée
                  $textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienMAJPre."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_PRE\" });majok(\"".$halID."\");'><img alt='MAJ' src='./img/add.png'></a></span></center>";
                }else{
                  $textAff .= "<center><img alt='Embargo' title='Embargo: interdit de modifier cette notice car la date \"whenSubmitted\" < 90 jours' src='./img/addEmbargo.png'></center>";
                }
              }else{
                $textAff .= "<center><img src='./img/addOK.png'></center>";
              }
            }
            $textAff .= "</td>";
            $lignAff = "ok";
          }else{
            $textAff .= "<td style='text-align: center;'><img alt='Done' src='./img/done.png'></td>";
          }
        }else{
          $textAff .= "<td style='text-align: center;'>&nbsp;</td>";
        }
      }
      $textAff .= "</tr>";
      if ($lignAff == "ok") {//Il y a au moins une correction à apporter > la ligne est à afficher
        echo $textAff;
      }
    }
    //echo "</tr>";
    echo "</table><br>";
    echo "<script>";
    echo "  document.getElementById('cpt').style.display = \"none\";";
    echo "</script>";
    
    if ($iMax != $numFound) {
      echo "<form name='troli' action='CrossHAL_E2P.php' method='post'>";
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
      echo "<input type='hidden' value='".$ordAut."' name='ordAut'>";
      echo "<input type='hidden' value='".$iniPre."' name='iniPre'>";
      echo "<input type='hidden' value='".$vIdHAL."' name='vIdHAL'>";
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
      echo "<form name='troli' action='CrossHAL_E2P.php' method='post'>";
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
      echo "<input type='hidden' value='".$ordAut."' name='ordAut'>";
      echo "<input type='hidden' value='".$iniPre."' name='iniPre'>";
      echo "<input type='hidden' value='".$vIdHAL."' name='vIdHAL'>";
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
      echo "<input type='hidden' value='".$lienext."' name='lienext'>";
      echo "<input type='hidden' value='".$noliene."' name='noliene'>";
      echo "<input type='hidden' value='".$embargo."' name='embargo'>";
      echo "<input type='hidden' value='Valider' name='valider'>";
      echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>";
    }
  }else{
    if ($rIdHAL == "oui") {//Etape 2 > Recherche IdHAL
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
      $iMinTab = $iMin - 1;
      for($cpt = $iMinTab; $cpt < $iMax; $cpt++) {
      //for($cpt = 0; $cpt < $numFound; $cpt++) {
      //for($cpt = 0; $cpt < 20; $cpt++) {
        //progression($cpt+1, $numFound);
        progression($cpt+1, $iMax);
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
        //Prise en compte de tous les auteurs
        $iAut = 0;
        while(isset($arrayHAL["response"]["docs"][$cpt]["authLastName_s"][$iAut])) {
          $tabIdHAL["cpt"][$iTIH] = $cpt;
          $tabIdHAL["lienDOI"][$iTIH] = $lienDOI;
          $tabIdHAL["lienHAL"][$iTIH] = $lienHAL;
          $tabIdHAL["nom"][$iTIH] = ucfirst($arrayHAL["response"]["docs"][$cpt]["authLastName_s"][$iAut]);
          $tabIdHAL["prenom"][$iTIH] = ucfirst($arrayHAL["response"]["docs"][$cpt]["authFirstName_s"][$iAut]);

          //Recherche de l'IdHAL
          $autIdHAL = "";
          $tabAI = explode("_FacetSep_", $arrayHAL["response"]["docs"][$cpt]["authIdHalFullName_fs"][$iAut]);
          if ($tabAI[0] != "") {
            $autIdHAL = $tabAI[0];
          }else{
            $IdHALtab = array();
            $urlIdHAL = "https://api.archives-ouvertes.fr/ref/author/?q=firstName_s:".str_replace(" ", "%20", $arrayHAL["response"]["docs"][$cpt]["authFirstName_s"][$iAut])."%20AND%20lastName_s:".str_replace(" ", "%20", $arrayHAL["response"]["docs"][$cpt]["authLastName_s"][$iAut])."%20AND%20valid_s:%22VALID%22&fl=*";
            askCurl($urlIdHAL, $IdHALtab);
            if (isset($IdHALtab["response"]["docs"][0]["idHal_s"])) {$autIdHAL = $IdHALtab["response"]["docs"][0]["idHal_s"];}
          }
          $authFuN = wd_remove_accents(substr($tabIdHAL["prenom"][$iTIH], 0, 1)).". ".wd_remove_accents($tabIdHAL["nom"][$iTIH]);//Initiale(s) prénom(s) + '.' + nom
          $tabIdHAL["nc"][$iTIH] = $authFuN;
          if ($autIdHAL != "") {
            $tabIdHAL["idhals"][$iTIH] = $autIdHAL;
            if (array_search($authFuN, $tabIdHALsNC) === false) {//On ajoute les équivalences 'IdHAL_s <> Nom complet' seulement si elle est absente du tableau
              $tabIdHALsNC[$autIdHAL] = $authFuN;
            }
          }else{
            $tabIdHAL["idhals"][$iTIH] = "-";
          }
          
          //Recherche de l'affiliation
          $iAff = 0;//Indice de parcours des résultats obtenus avec authIdHasStructure_fs
          while(isset($arrayHAL["response"]["docs"][$cpt]["authIdHasStructure_fs"][$iAff])) {
            $tabIS = explode("_FacetSep_", $arrayHAL["response"]["docs"][$cpt]["authIdHasStructure_fs"][$iAff]);
            $tabISP = explode("_JoinSep_", $tabIS[1]);
            $tabNA = explode(" ", $tabISP[0]);
            //$pnAut = prenomCompInit($tabNA[0])." ".nomCompEntier($tabNA[1]);
            if (isset($tabNA[0]) && isset($tabNA[1])) {
              $pnAut = substr($tabNA[0], 0, 1).". ".wd_remove_accents($tabNA[1]);
              //if ($tabISP[0] == $authFuN) {//Les noms complets de l'auteur correspondent
              if ($pnAut == $authFuN) {//Les noms complets de l'auteur correspondent
                $tabIdHAL["affiliation"][$iTIH] = $tabISP[1];
                if (!array_key_exists($authFuN, $tabStructNC)) {$tabStructNC[$authFuN] = $tabISP[1];}
                $iAff++;
                break;
              }else{
                $tabIdHAL["affiliation"][$iTIH] = "-";
                $iAff++;
              }
            }else{
              break;
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
      }
      //var_dump($tabStructNC);
      //var_dump($tabdocidNC);
      //print_r($tabIdHALsNC);
      //print_r($tabIdHAL);
      if (!empty($tabIdHAL)) {array_multisort($tabIdHAL["nom"], SORT_ASC, SORT_STRING, $tabIdHAL["prenom"], $tabIdHAL["nc"], $tabIdHAL["cpt"], $tabIdHAL["lienHAL"], $tabIdHAL["lienDOI"], $tabIdHAL["idhals"], $tabIdHAL["affiliation"], $tabIdHAL["annee"]);}

      $cpt = 0;
      while(isset($tabIdHAL["lienHAL"][$cpt])) {
        $lignAff = "no";//Test affichage ou non de la ligne du tableau
        $textAff = "";//Texte de la ligne du tableau
        $iCpt = $cpt + 1;
        if ($docidStr != "" && strpos($docidStr, $tabIdHAL["affiliation"][$cpt]) !== false) {//N'afficher que les auteurs de la collection recherchée
          $textAff .= "<tr style='text-align: center;'>";
          $textAff .= "<td>".$iCpt."</td>";
          if (isset($tabIdHAL["lienDOI"][$cpt])) {
            $textAff .= "<td>".$tabIdHAL["lienDOI"][$cpt]."</td>";
          }else{
            $textAff .= "<td>&nbsp;</td>";
          }
          $textAff .= "<td>".$tabIdHAL["lienHAL"][$cpt]."</td>";
          if($apa == "oui") {
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
          if ($tabIdHAL["idhals"][$cpt] == "-") {//pas d'IdHAL trouvé pour cet auteur et cette notice ...
            if (array_search($tabIdHAL["nc"][$cpt], $tabIdHALsNC) !== false) {//...mais peut-être qu'un IdHAL a déjà été trouvé pour cet auteur dans une autre notice
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
              
              if ($idhals != "") {//On va récupérer les informations sur l'auteur
                $tabdocidNC[$tabIS[0]] = $authFuN;
                //$urlHALAut = "https://api.archives-ouvertes.fr/ref/author/?q=idHal_s:%22".$idhals."%22%20AND%20valid_s:%22VALID%22&fl=idHal_s,idHal_i,emailDomain_s,email_s,researcherid_s,docid,arxiv_s,idref_s,isni_s,orcid_s,viaf_s";
                $urlHALAut = "https://api.archives-ouvertes.fr/ref/author/?q=idHal_s:%22".$idhals."%22&fl=idHal_s,idHal_i,emailDomain_s,email_s,researcherid_s,docid,arxiv_s,idref_s,isni_s,orcid_s,viaf_s,valid_s";
                askCurl($urlHALAut, $arrayHALAut);
                $iHALAut = 0;
                $nodocid = "";
                $iHALtst = "no";//Test pour vérifier si un IdHal valide a été trouvé
                $aureDoc = "";//Test pour vérifier si les informations complémentaires sur l'auteur ont été ajoutées
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
                $teiRes = '<?xml version="1.0" encoding="UTF-8"?>'.$tei;
                //$teiRes = str_replace('<TEI xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', '<TEI xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.tei-c.org/ns/1.0 http://api.archives-ouvertes.fr/documents/aofr-sword.xsd" xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', $teiRes);
                //$Fnm = "./XML/".normalize(wd_remove_accents($titre)).".xml";
                $Fnm = "./XML/".$arrayHAL["response"]["docs"][$tabIdHAL["cpt"][$cpt]]["halId_s"].".xml";
                $xml = new DOMDocument( "1.0", "UTF-8" );
                $xml->formatOutput = true;
                $xml->preserveWhiteSpace = false;
                $xml->loadXML($teiRes);
                
                //Modification noeud auteur avec ajout idhal
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
                              //suppression noeuds idno, affiliation et email
                              if($elt->getElementsByTagName("idno")->length > 0) {
                                while($newXml = $elt->getElementsByTagName("idno")->item(0)) {
                                  $newXml->parentNode->removeChild($newXml);
                                  //$newXml = $elt->removeChild($elt->getElementsByTagName("idno")->item(0));
                                }
                              }
                              if($elt->getElementsByTagName("affiliation")->length > 0) {
                                while($newXml = $elt->getElementsByTagName("affiliation")->item(0)) {
                                  $newXml->parentNode->removeChild($newXml);
                                  //$newXml = $elt->removeChild($elt->getElementsByTagName("affiliation")->item(0));
                                }
                              }
                              if($elt->getElementsByTagName("email")->length > 0) {
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
                                $node = $xml->createElement("affiliation");
                                $node->setAttribute("ref", "#struct-".$struct);
                                $newXml = $elt->appendChild($node);
                              }
                              
                              break 2;
                            }
                          }
                        }
                      }
                    }
                  }
                }
                $xml->save($Fnm);
                $lienIDH = "./CrossHAL_Modif.php?action=MAJ&Id=".$arrayHAL["response"]["docs"][$tabIdHAL["cpt"][$cpt]]["halId_s"];
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
                  $submDate = mktime(0, 0, 0, substr($submDate, 5, 2), substr($submDate, 8, 2), substr($submDate, 0, 4));
                  $limDate = time() - $nbjours*86400;
                  if ($submDate <= $limDate) {//Il est possible de faire les modifications car la date limite est dépassée
                    $lignAff = "ok";
                    $textAff .= "<td><center><span id='maj".$halID."'><a target='_blank' href='".$lienIDH."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_IDH\" });majok(\"".$halID."\");'><img alt='MAJ' src='./img/add.png'></a></span></center></td>";
                  }else{
                    $lignAff = "ok";
                    $textAff .= "<td><center><img alt='Embargo' title='Embargo: interdit de modifier cette notice car la date \"whenSubmitted\" < 90 jours' src='./img/addEmbargo.png'></center></td>";
                  }
                }else{
                  $lignAff = "ok";
                  $textAff .= "<td><center><img src='./img/addOK.png'></center></td>";
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
                  $textAff .= "<td><center><img alt='Invalide' title='IdHal non valide' src='./img/addEmbargo.png'></center></td>";
                }else{
                  $textAff .= "<td><center><img alt='Invalide' title='DocID à ignorer' src='./img/addEmbargo.png'></center></td>";
                }
              }
              //echo("<td style='background-color:#FFFF00'>&nbsp;</td>");
            }else{
              $textAff .= "<td>".$tabIdHAL["idhals"][$cpt]."</td>";
            }
          }else{
            $textAff .= "<td>".$tabIdHAL["idhals"][$cpt]."</td>";
          }
          $textAff .= "<td>".$team."</td>";
          //echo("<td>".$tabIdHAL["domaine"][$cpt]."</td>");
          $textAff .= "<td>".substr($tabIdHAL["annee"][$cpt], 0, 4)."</td>";
          $textAff .= "</tr>";
        }
        if ($lignAff == "ok") {//Il y a des corrections à apporter > la ligne est à afficher
          echo $textAff;
        }else{//Pas de correction à apporter > inutile d'afficher la ligne
        }
        $cpt++;
      }
      echo "</table><br>";
      echo "<script>";
      echo "  document.getElementById('cpt').style.display = \"none\";";
      echo "</script>";
      
      if ($iMax != $numFound) {
        echo "<form name='troli' action='CrossHAL_E2P.php' method='post'>";
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
        echo "<input type='hidden' value='".$ordAut."' name='ordAut'>";
        echo "<input type='hidden' value='".$iniPre."' name='iniPre'>";
        echo "<input type='hidden' value='".$vIdHAL."' name='vIdHAL'>";
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
        echo "<form name='troli' action='CrossHAL_E2P.php' method='post'>";
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
        echo "<input type='hidden' value='".$ordAut."' name='ordAut'>";
        echo "<input type='hidden' value='".$iniPre."' name='iniPre'>";
        echo "<input type='hidden' value='".$vIdHAL."' name='vIdHAL'>";
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
        echo "<input type='hidden' value='".$lienext."' name='lienext'>";
        echo "<input type='hidden' value='".$noliene."' name='noliene'>";
        echo "<input type='hidden' value='".$embargo."' name='embargo'>";
        echo "<input type='hidden' value='Valider' name='valider'>";
        echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>";
      }
    }else{//Etape 2 > Test validité IdHAL
      //var_dump($arrayHAL["response"]["docs"]);
      $arrayHALAut = array();
      $aNom = "~";//Liste test noms pour un affichage unique
      for($cpt = 0; $cpt < $numFound; $cpt++) {
      //for($cpt = 0; $cpt < 20; $cpt++) {
        progression($cpt+1, $numFound);
        $iAHS = 0;
        $tabdocid = explode("~", $docidStr);
        while (isset($arrayHAL["response"]["docs"][$cpt]["authIdHasStructure_fs"][$iAHS])) {
          $tabIS = explode("_FacetSep_", $arrayHAL["response"]["docs"][$cpt]["authIdHasStructure_fs"][$iAHS]);
          if (strposa($tabIS[1], $tabdocid, 1) !== false) {
            $tabISP = explode("_JoinSep_", $tabIS[1]);
            //echo $tabISP[0]."<br>";
            $urlHALAut = "https://api.archives-ouvertes.fr/ref/author/?q=fullName_s:%22".$tabISP[0]."%22&fl=*";
            //$urlHALAut = "https://api.archives-ouvertes.fr/ref/author/?q=fullName_s:%22".$tabISP[0]."%22%20AND%20NOT%20valid_s:%22VALID%22&fl=*";
            $urlHALAut = str_replace(" ", "%20", $urlHALAut);
            askCurl($urlHALAut, $arrayHALAut);
            //Existe-t-il une forme VALID ?
            $ivalTest = 0;
            $testVal = "no";
            while (isset($arrayHALAut["response"]["docs"][$ivalTest]["docid"])) {
              if ($arrayHALAut["response"]["docs"][$ivalTest]["valid_s"] == "VALID"){
                $aNom .= $tabISP[0]."~";
                $testVal = "ok";
                break;
              }
            $ivalTest++;
            }
            //Si pas de forme VALID, existe-t-il une forme OLD ?
            $ivalTest = 0;
            while (isset($arrayHALAut["response"]["docs"][$ivalTest]["docid"])) {
              if ($arrayHALAut["response"]["docs"][$ivalTest]["valid_s"] == "OLD"){
                $aNom .= $tabISP[0]."~";
                $testVal = "ok";
                break;
              }
            $ivalTest++;
            }
            if ($testVal == "no") {//Pas de forme VALID
              $iVal = 0;//Indice pour parcourir le tableau des résultats des formes d'auteurs trouvés
              while (isset($arrayHALAut["response"]["docs"][$iVal]["docid"])) {
                $preHal = "-";
                $nomHal = "-";
                if (isset($arrayHALAut["response"]["docs"][$iVal]["firstName_s"])) {$preHAL = $arrayHALAut["response"]["docs"][$iVal]["firstName_s"];}
                if (isset($arrayHALAut["response"]["docs"][$iVal]["lastName_s"])) {$nomHAL = $arrayHALAut["response"]["docs"][$iVal]["lastName_s"];}
                
                if ($arrayHALAut["response"]["docs"][$iVal]["valid_s"] != "VALID" && stripos($aNom, $tabISP[0]) === false && isset($arrayHALAut["response"]["docs"][$iVal]["idHal_s"])) {
                  $aNom .= $tabISP[0]."~";
                  echo("<tr>");
                  $cptID = $cpt + 1;
                  echo("<td style='text-align: center;'>".$cptID."</td>");
                  //echo("<td style='text-align: center;'>".$preHAL."</td>");
                  //echo("<td style='text-align: center;'>".$nomHAL."</td>");
                  echo("<td style='text-align: center;'>".$tabISP[0]."</td>");
                  $idhals = "-";
                  if (isset($arrayHALAut["response"]["docs"][$iVal]["idHal_s"])) {$idhals = $arrayHALAut["response"]["docs"][$iVal]["idHal_s"];}
                  echo("<td style='text-align: center;'>".$idhals."</td>");
                  $lienAureHAL = "-";
                  if ($preHal != "-" && $nomHal != "-") {$lienAureHAL = "https://aurehal.archives-ouvertes.fr/author/browse/critere/".$nomHAL."+".$preHAL."/solR/1/page/1/nbResultPerPage/50/tri/current_bool/filter/all";}
                  echo("<td style='text-align: center;'><a target='_blank' href='".$lienAureHAL."'><img src='./img/HAL.jpg'></a></td>");
                  echo("<td style='text-align: center;'><a target='_blank' href='".$urlHALAut."'><img src='./img/HAL.jpg'></a></td>");
                  echo("</tr>");
                  //echo $arrayHALAut["response"]["docs"][$iVal]["fullName_s"]."<br>";
                }
                $iVal++;
              }
            }
          }
          $iAHS++;
        }
      }
      echo "</table><br>";
      echo "<script>";
      echo "  document.getElementById('cpt').style.display = \"none\";";
      echo "</script>";
      
      if ($iMax != $numFound) {
        echo "<form name='troli' action='CrossHAL_E2P.php' method='post'>";
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
        echo "<input type='hidden' value='".$ordAut."' name='ordAut'>";
        echo "<input type='hidden' value='".$iniPre."' name='iniPre'>";
        echo "<input type='hidden' value='".$vIdHAL."' name='vIdHAL'>";
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
        echo "<form name='troli' action='CrossHAL_E2P.php' method='post'>";
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
        echo "<input type='hidden' value='".$ordAut."' name='ordAut'>";
        echo "<input type='hidden' value='".$iniPre."' name='iniPre'>";
        echo "<input type='hidden' value='".$vIdHAL."' name='vIdHAL'>";
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
        echo "<input type='hidden' value='".$lienext."' name='lienext'>";
        echo "<input type='hidden' value='".$noliene."' name='noliene'>";
        echo "<input type='hidden' value='".$embargo."' name='embargo'>";
        echo "<input type='hidden' value='Valider' name='valider'>";
        echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>";
      }
    }
  }
}

//Etape 3
if (((isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) && $opt3 == "oui") || $action == 3) {
  $urlPDF = "";
  if (isset($_POST["urlPDF"]) && $_POST["urlPDF"] != "") {$urlPDF = $_POST["urlPDF"];}
  if (isset($_GET["urlPDF"]) && $_GET["urlPDF"] != "")  {$urlPDF = $_GET["urlPDF"];}
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
  $urlHAL = "https://api.archives-ouvertes.fr/search/?q=".$atester.":%22".$qui."%22".$txtApa.$txtExt.$txtNoe."%20AND%20NOT%20submitType_s:%22file%22&rows=".$rows."&fq=producedDateY_i:[".$anneedeb."%20TO%20".$anneefin."]%20AND%20docType_s:(%22ART%22%20OR%20%22COMM%22%20OR%20%22COUV%22)&fl=title_s,authFirstName_s,authLastName_s,doiId_s,halId_s,volume_s,issue_s,page_s,funding_s,producedDate_s,ePublicationDate_s,keyword_s,pubmedId_s,producedDateY_i,publisher_s,label_xml,submittedDate_s,docType_s&sort=halId_s%20desc";
  //$contents = file_get_contents($urlHAL);
  //$resHAL = json_decode($contents, true);
  //$numFound = $resHAL["response"]["numFound"];
  askCurl($urlHAL, $arrayHAL);
  //var_dump($arrayCurl);
  $numFound = $arrayHAL["response"]["numFound"];
  if ($iMax > $numFound) {$iMax = $numFound;}
  echo '<b>Total de '.$numFound.' référence(s)';
  if ($numFound != 0) {echo " : affichage de ".$iMin." à ".$iMax."</b>&nbsp;<i>(Dans le cas où aucune action corrective n'est à apporter, la ligne n'est pas affichée.)</i><br><br>";}
  
  echo "<div id='cpt'></div>";
  echo "<table class='table table-striped table-bordered table-hover;'><tr>";
  //echo "<table style='border-collapse: collapse; width: 100%' border='1px' bordercolor='#999999' cellpadding='5px' cellspacing='5px'><tr>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>ID</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>DOI</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>HAL</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Année pub.</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Titre</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Nom 1er auteur</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Licence</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Type</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>PDF</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Editeur</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Action 1 > Déposer</b></td>";
  echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Action 2 > Parcourir</b></td>";
  //echo "<td style='text-align: center; background-color: #eeeeee; color: #999999;'><b>Statut</b></td>";
  echo "</tr>";

  $iMinTab = $iMin - 1;
  for($cpt = $iMinTab; $cpt < $iMax; $cpt++) {
    progression($cpt+1, $iMax);
    $cptTab = $cpt + 1;
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
        //$evd = "greenPublisher";
        testOALic($urlT, $volT, $issT, $pagT, $datT, $pdfCR, $halID, $evd, $titLic, $typLic, $compCC, $compNC, $compND, $compSA, $urlPDF, $orig);
        //Ne pas afficher le bouton Action si le lien ne contient pas la chaîne "pdf"
        //if (stripos($lienPDF, ".pdf") === false) {$evd = "noaction";}
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
            if(isset($arrayCR["message"]["volume"])) {
              $volCR = $arrayCR["message"]["volume"];
            }
            if(isset($arrayCR["message"]["issue"])) {
              $numCR = $arrayCR["message"]["issue"];
            }
            if(isset($arrayCR["message"]["page"])) {
              $pagCR = $arrayCR["message"]["page"];
            }
            if(isset($arrayCR["message"]["published-print"]["date-parts"][0][0])) {
              $pubCR = $arrayCR["message"]["published-print"]["date-parts"][0][0];
            }
            if(isset($arrayCR["message"]["link"][0]["URL"])) {
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
          
          testOALic($urlT, $volT, $issT, $pagT, $datT, $pdfCR, $arrayHAL["response"]["docs"][$cpt]["halId_s"], $evd, $titLic, $typLic, $compCC, $compNC, $compND, $compSA, $urlPDF, $orig);

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
      $textAff .= "<tr style='text-align: center;'><td>".$cptTab."</td>";
      $textAff .= "<td style='text-align: center;'>".$lienDOI."</td>";
      $textAff .= "<td style='text-align: center;'>".$lienHAL."</td>";
      $textAff .= "<td style='text-align: center;'>".$arrayHAL["response"]["docs"][$cpt]["producedDateY_i"]."</td>";
      $textAff .= "<td style='text-align: center;'>".$arrayHAL["response"]["docs"][$cpt]["title_s"][0]."</td>";
      $textAff .= "<td style='text-align: center;'>".$arrayHAL["response"]["docs"][$cpt]["authLastName_s"][0]."</td>";
      $textAff .= "<td style='text-align: center;'>".$licEvd."</td>";
      $textAff .= "<td style='text-align: center;'>".$compCC."</td>";
      if ($lienPDF == "" || $urlPDF == "") {
        $textAff .= "<td style='text-align: center;'></td>";
      }else{
        if ($licEvd == "auteur") {//Si type doc "auteur", le bouton Action n'est activé que si on a cliqué sur "lien", i.e., on s'est assuré que le PDF était bien un manuscrit auteur
          $textAff .= "<td><a target='_blank' href='".$urlPDF."' onclick='condActOk(\"".$halID."\",\"".$lienPDF."\", \"MAJ_PDF\");'>lien</a></td>";
          $condAct = "ok";
        }else{
          $textAff .= "<td><a target='_blank' href='".$urlPDF."'>lien</a></td>";
        }
      }
      if (isset($arrayHAL["response"]["docs"][$cpt]["publisher_s"])) {
        $textAff .= "<td style='text-align: center;'>".$arrayHAL["response"]["docs"][$cpt]["publisher_s"][0]."</td>";
      }else{
        $textAff .= "<td style='text-align: center;'>&nbsp;</td>";
      }

      //Actions
      $lienMAJPre = "";
      $tei = $arrayHAL["response"]["docs"][$cpt]["label_xml"];
      //echo $tei;
      $teiRes = '<?xml version="1.0" encoding="UTF-8"?>'.$tei;
      //$teiRes = str_replace('<TEI xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', '<TEI xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.tei-c.org/ns/1.0 http://api.archives-ouvertes.fr/documents/aofr-sword.xsd" xmlns="http://www.tei-c.org/ns/1.0" xmlns:hal="http://hal.archives-ouvertes.fr/">', $teiRes);
      //$Fnm = "./XML/".normalize(wd_remove_accents($titre)).".xml";
      $Fnm = "./XML/".$arrayHAL["response"]["docs"][$cpt]["halId_s"].".xml";
      $xml = new DOMDocument( "1.0", "UTF-8" );
      $xml->formatOutput = true;
      $xml->preserveWhiteSpace = false;
      $xml->loadXML($teiRes);
      
      //Action 1 > Déposer
      //PDF trouvé avec oaDOI ?
      if ($lienPDF != "" && $evd != "noaction" && $urlPDF != "") {
        include "./CrossHAL_actions.php";
        $actMaj = "ok";
        foreach($ACTIONS_LISTE as $tab) {
          if (in_array($halID, $tab) && in_array("MAJ_PDF",$tab)) {$actMaj = "no";}
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
          $submDate = mktime(0, 0, 0, substr($submDate, 5, 2), substr($submDate, 8, 2), substr($submDate, 0, 4));
          $limDate = time() - $nbjours*86400;
          if ($submDate <= $limDate) {//Il est possible de faire les modifications car la date limite est dépassée
            if ($condAct == "ok") {//Il y a une condition préalable au lancement de l'action
              $textAff .= "<td><center><span id='maj".$halID."'><img alt='MAJ' title='Par précaution, ce bouton Action ne sera activé que lorsque vous aurez vérifié via le lien ci-avant que le PDF est bien un manuscrit auteur' src='./img/addOK.png'></span></center></td>";
            }else{
              $textAff .= "<td><center><span id='maj".$halID."'><a target='_blank' href='".$lienPDF."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_PDF\" });majok(\"".$halID."\");'><img alt='MAJ' src='./img/add.png'></a></span></center></td>";
            }
          }else{
            $textAff .= "<td><center><img alt='Embargo' title='Embargo: interdit de modifier cette notice car la date \"whenSubmitted\" < 90 jours' src='./img/addEmbargo.png'></center></td>";
          }
          $lignAff = "ok";
        }else{
          $textAff .= "<td><center><img src='./img/addOK.png'></center></td>";
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
          $urlPDF = $urlPDF3;
          $evd = "";
          $compNC = "";
          $compND = "";
          $compSA = "";
          //echo $halID;
          if ($lienext == "oui") {//notice avec lien externe
            genXMLPDF($halID, $doi, $targetPDF, $halID, $evd, $compNC, $compND, $compSA, $lienPDF, $urlPDF);
            include "./CrossHAL_actions.php";
            $actMaj = "ok";
            foreach($ACTIONS_LISTE as $tab) {
              if (in_array($halID, $tab) && in_array("MAJ_PDF",$tab)) {$actMaj = "no";}
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
              $submDate = mktime(0, 0, 0, substr($submDate, 5, 2), substr($submDate, 8, 2), substr($submDate, 0, 4));
              $limDate = time() - $nbjours*86400;
              if ($submDate <= $limDate) {//Il est possible de faire les modifications car la date limite est dépassée
                $textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienPDF."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_PDF\" });majok(\"".$halID."\");'><img alt='MAJ' src='./img/add.png'></a></span></center>";
              }else{
                $textAff .= "<center><img alt='Embargo' title='Embargo: interdit de modifier cette notice car la date \"whenSubmitted\" < 90 jours' src='./img/addEmbargo.png'></center>";
              }
              $txtAff = "ok";
            }else{
              $textAff .= "<center><img src='./img/addOK.png'></center>";
            }
          }else{//Notice sans lien externe > embargo à mettre en place
            //Utilisation détournée de paramètres de la fonction initiale pour l'inscription de l'embargo dans le TEI
            $evd = "noliene";
            $compNC = $embargo;
            genXMLPDF($halID, $doi, $targetPDF, $halID, $evd, $compNC, $compND, $compSA, $lienPDF, $urlPDF);
            include "./CrossHAL_actions.php";
            $actMaj = "ok";
            foreach($ACTIONS_LISTE as $tab) {
              if (in_array($halID, $tab) && in_array("MAJ_PDF",$tab)) {$actMaj = "no";}
            }
            if ($lienPDF == "noDateEpub") {
              $textAff .= "<center><img alt='Pas de dateEpub' title=\"La date de publication en ligne n'est pas renseignée !\" src='./img/addEmbargo.png'></center>";
              $lignAff = "ok";
            }else{
              if ($actMaj == "ok") {
                $textAff .= "<center><span id='maj".$halID."'><a target='_blank' href='".$lienPDF."' onclick='$.post(\"CrossHAL_liste_actions.php\", { halID: \"".$halID."\", action: \"MAJ_PDF\" });majok(\"".$halID."\");'><img alt='MAJ' src='./img/add.png'></a></span></center>";
                $lignAff = "ok";
              }else{
                $textAff .= "<center><img src='./img/addOK.png'></center>";
              }
            }
          }
        }else{
          if (stripos($urlPDF, "https://hal") === false) {//Le fichier PDF n'est pas un fichier auteur
            $lignAff = "ok";
            //$textAff .= "<div id='formFilePDF'></div>";
            $textAff .= "<form enctype='multipart/form-data' action='CrossHAL_PDF.php' method='post' accept-charset='UTF-8'>";
            $textAff .= "<input type='hidden' name='MAX_FILE_SIZE' value='10000000' />";
            $textAff .= "<p class='form-inline'><label for='pdf_file'>Envoyez le fichier PDF (10 Mo max) :</label><br />";
            $textAff .= "<input class='form-control' style='font-size:90%; height:25px; padding: 0px;' id='pdf_file' name='pdf_file' type='file' /><br />";
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
            $textAff .= "<input type='hidden' value='".$urlPDF."' name='urlPDF'>";
            $textAff .= "<input type='hidden' value='".$cptTab."' name='cptTab'>";
            $textAff .= "<input class='form-control btn btn-md btn-primary' style='height: 25px; padding: 0px; width: 130px;'type='submit' value='Envoyer le fichier'>";
            $textAff .= "</form>";
          }
        }
      }
      $textAff .= "</td></tr>";
      //echo "<td></td></tr>";
    }else{//Présence d'un nom de fichier dans la notice ou DOI à exclure
      $lignAff = "ok";
      $textAff .= "<tr><td style='text-align: center;'>".$cptTab."</td>";
      $lienDOI = "<a target='_blank' href='https://doi.org/".$doi."'><img alt='DOI' src='./img/doi.jpg'></a>";
      $textAff .= "<td style='text-align: center;'>".$lienDOI."</td>";
      $textAff .= "<td style='text-align: center;'>".$lienHAL."</td>";
      $textAff .= "<td style='text-align: center;'>".$arrayHAL["response"]["docs"][$cpt]["producedDateY_i"]."</td>";
      $textAff .= "<td style='text-align: center;'></td>";
      $textAff .= "<td style='text-align: center;'></td>";
      $textAff .= "<td style='text-align: center;'></td>";
      $textAff .= "<td style='text-align: center;'></td>";
      if ($nodoi != "") {
        $textAff .= "<td>DOI à exclure</td>";
      }else{
        $textAff .= "<td>URL de fichier déjà mentionnée dans la notice : <a target='_blank' href='".$lienPDF."'>lien</a></td>";
      }
      if (isset($arrayHAL["response"]["docs"][$cpt]["publisher_s"])) {
        $textAff .= "<td style='text-align: center;'>".$arrayHAL["response"]["docs"][$cpt]["publisher_s"][0]."</td>";
      }else{
        $textAff .= "<td style='text-align: center;'>&nbsp;</td>";
      }
      $textAff .= "<td style='text-align: center;'></td>";
      $textAff .= "<td style='text-align: center;'></td></tr>";
    }
    if ($lignAff == "ok") {//Il y a au moins une correction à apporter > la ligne est à afficher
      echo $textAff;
    }
  }
  echo "</table><br>";
  echo "<script>";
  echo "  document.getElementById('cpt').style.display = \"none\";";
  echo "</script>";
  
  if ($iMax != $numFound) {
    echo "<form name='troli' action='CrossHAL_E2P.php' method='post'>";
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
    echo "<input type='hidden' value='".$absPubmed."' name='absPubmed'>";
    echo "<input type='hidden' value='".$lanPubmed."' name='lanPubmed'>";
    echo "<input type='hidden' value='".$mocPubmed."' name='mocPubmed'>";
    echo "<input type='hidden' value='".$pmid."' name='pmid'>";
    echo "<input type='hidden' value='".$pmcid."' name='pmcid'>";
    echo "<input type='hidden' value='".$absISTEX."' name='absISTEX'>";
    echo "<input type='hidden' value='".$lanISTEX."' name='lanISTEX'>";
    echo "<input type='hidden' value='".$mocISTEX."' name='mocISTEX'>";
    echo "<input type='hidden' value='".$manuaut."' name='manuaut'>";
    echo "<input type='hidden' value='".$lienext."' name='lienext'>";
    echo "<input type='hidden' value='".$noliene."' name='noliene'>";
    echo "<input type='hidden' value='".$embargo."' name='embargo'>";
    echo "<input type='hidden' value='".$urlPDF."' name='urlPDF'>";
    echo "<input type='hidden' value='Valider' name='valider'>";
    if ($iMinInit != 1) {
      echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>&nbsp;&nbsp;&nbsp;";
    }
    echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Suite' style='width: 70px;' name='suite'>";
    echo "</form><br>";
    //echo "<script>formFilePDF();</script>";
  }else{
    echo "<form name='troli' action='CrossHAL_E2P.php' method='post'>";
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
    echo "<input type='hidden' value='".$absPubmed."' name='absPubmed'>";
    echo "<input type='hidden' value='".$lanPubmed."' name='lanPubmed'>";
    echo "<input type='hidden' value='".$mocPubmed."' name='mocPubmed'>";
    echo "<input type='hidden' value='".$pmid."' name='pmid'>";
    echo "<input type='hidden' value='".$pmcid."' name='pmcid'>";
    echo "<input type='hidden' value='".$absISTEX."' name='absISTEX'>";
    echo "<input type='hidden' value='".$lanISTEX."' name='lanISTEX'>";
    echo "<input type='hidden' value='".$mocISTEX."' name='mocISTEX'>";
    echo "<input type='hidden' value='".$manuaut."' name='manuaut'>";
    echo "<input type='hidden' value='".$lienext."' name='lienext'>";
    echo "<input type='hidden' value='".$noliene."' name='noliene'>";
    echo "<input type='hidden' value='".$embargo."' name='embargo'>";
    echo "<input type='hidden' value='".$urlPDF."' name='urlPDF'>";
    echo "<input type='hidden' value='Valider' name='valider'>";
    echo "<input type='submit' class='form-control btn btn-md btn-primary' value='Retour' style='width: 70px;' name='retour'>";
  }
}
/*
  $url = "https://api.crossref.org/v1/works/http://dx.doi.org/10.1016/j.scitotenv.2017.07.206";
  if (@file_get_contents($url)) {
    $contents = file_get_contents($url);
    $contents = utf8_encode($contents); 
    $results = json_decode($contents, TRUE);
    //var_dump($results);
    //var_dump($results["message"]["author"]);

    $jsonIterator = new RecursiveIteratorIterator(
        new RecursiveArrayIterator(json_decode($contents, TRUE)),
        RecursiveIteratorIterator::SELF_FIRST);

    //foreach ($jsonIterator as $key => $val) {
      //if($key === "volume" || $key === "numero" || $key === "page") {
        //if(is_array($val)) {
            //echo "$key:<br>";
        //} else {
            //echo "$key => $val<br>";
        //}
      //}
    //}
  }else{
    echo "DOI inconnu de Crossref";
  }
*/

?>
</body>
</html>