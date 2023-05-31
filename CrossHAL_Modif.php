<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Enregistrement CURL des modifications réalisées - CURL record of changes made
 */
 
header('Content-type: text/html; charset=UTF-8');
?>

<?php
//require_once('./CAS_connect.php')//authentification CAS ou autre ?
if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
  include('./_connexion.php');
  $HAL_USER = $user;
  $HAL_PASSWD = $pass;
}else{
  require_once('./CAS_connect.php');
  
  session_start();
  $HAL_USER = phpCAS::getUser();
  $_SESSION['HAL_USER'] = $HAL_USER;
  $HAL_PASSWD = "";
  if (isset($_POST['password']) && $_POST['password'] != "") {$_SESSION['HAL_PASSWD'] = htmlspecialchars($_POST['password']);}

  if (isset($_SESSION['HAL_PASSWD']) && $_SESSION['HAL_PASSWD'] != "") {
    $HAL_PASSWD = $_SESSION['HAL_PASSWD'];
  }else{
    include('./CrossHAL_Form.php');
    die();
  }
}
?>
<html lang="fr">
<body>
<?php
if (isset($_GET['Id']) && ($_GET['Id'] != ""))
{
  //$halid = "hal-01179051";
  $halid = $_GET['Id'];
}else{
  if (isset($_GET['DOI']) && ($_GET['DOI'] != ""))
  {
    //$halid = "hal-01179051";
    $doi = $_GET['DOI'];
  }else{
    header('Location: CrossHAL.php');
    exit;
  }
}

//Pour visualiser résultat preprod > https://univ-rennes1.halpreprod.archives-ouvertes.fr/halid

/*
$url = "https://api-preprod.archives-ouvertes.fr/sword/hal/";
$urlStamp = "https://api-preprod.archives-ouvertes.fr/";
*/

$url = "https://api.archives-ouvertes.fr/sword/";
$urlStamp = "https://api.archives-ouvertes.fr/";

/*
if ($_GET['action'] == "MAJ") {
  $nomfic = "./XML/".$halid.".xml";
  $nomficFin = "./XML/".$halid."-Fin.xml";
  copy($nomfic, $nomficFin);
}
if ($_GET['action'] == "PDF") {
  $nomfic = "./XML/".$halid."_PDF.xml";
  $nomficFin = "./XML/".$halid."_PDF-Fin.xml";
  copy($nomfic, $nomficFin);
}
*/

$nomfic = "./XML/".$halid.".xml";
$nomficFin = "./XML/".$halid."-Fin.xml";
copy($nomfic, $nomficFin);
  
//suppression éventuel noeud <listBibl type="references">
$xml = new DOMDocument( "1.0", "UTF-8" );
$xml->formatOutput = true;
$xml->preserveWhiteSpace = false;
$xml->load($nomfic);

$gpElts = $xml->documentElement;
$elts = $xml->getElementsByTagName("listBibl");

foreach($elts as $elt) {
  if ($elt->hasAttribute("type")) {
    $quoi = $elt->getAttribute("type");
    if ($quoi == "references") {
      $parent = $elt->parentNode; 
      $newXml = $parent->removeChild($elt);
      $xml->save($nomficFin);
    }
  }
}

//vérification validité des collections renseignées
$eltASup = array();
$elts = $xml->getElementsByTagName("idno");
foreach($elts as $elt) {
  if ($elt->hasAttribute("type")) {
    $quoi = $elt->getAttribute("type");
    if ($quoi == "stamp") {
      $coll = $elt->getAttribute("n");
      $contents = file_get_contents($urlStamp.'search/?q=collCode_s:"'.$coll.'"');
      $contents = mb_convert_encoding($contents, 'UTF-8', 'ISO-8859-1');
      $results = json_decode($contents);
      $numFound = $results->response->numFound;
      //echo $coll." - ".$numFound.'<br>';
      if ($numFound == 0) {
        $eltASup[] = $elt;
      }
    }
  }
}
foreach($eltASup as $elt) {
  $elt->parentNode->removeChild($elt); 
}
$xml->save($nomficFin);

$xmlContenu = $xml->saveXML();//Nécessaire pour le mime-type soit bien considéré comme du text/xml

$fp = fopen($nomficFin, "r");
$ENDPOINTS_RESPONDER["TIMOUT"] = 20;

$ch = curl_init($url.$halid);
curl_setopt($ch, CURLOPT_PUT, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
if (isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLOPT_CAINFO, "cacert.pem");
}else{
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
}
curl_setopt($ch, CURLOPT_VERBOSE, 1);
$headers=array();
$headers[] = "Packaging: http://purl.org/net/sword-types/AOfr";
$headers[] = "Content-Type: text/xml";
//$headers[] = "Authorization: Basic";
if (isset($doi)) {
  $headers[] = "X-Allow-Completion[".$doi."]";
}
curl_setopt($ch, CURLOPT_USERPWD, ''.$HAL_USER.':'.$HAL_PASSWD.'');
//var_dump($headers);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_INFILE, $fp);
curl_setopt($ch, CURLOPT_INFILESIZE, filesize($nomficFin));
curl_setopt($ch, CURLOPT_UPLOAD, TRUE);

$return = curl_exec($ch);
//var_dump($return);

$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Code retour http : ".$httpcode."<br>";
if($return == FALSE)
{
  if ($httpcode == 401) {
    $errStr="Problème d'authentification, mot de passe incorrect.\n";
  }else{
    $errStr="Problème avec l'API sword, contactez le support technique (erreur http=$httpcode)";
  }
  //exit ("ERREUR : ".$art->getCle()." : ".$errStr);
  exit ("ERREUR : ".$errStr);;
}
try {
  //$entry = new SimpleXMLElement($return);
	$entry = simplexml_load_string($return,'SimpleXMLElement', LIBXML_NOCDATA);
  $entry->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
  $entry->registerXPathNamespace('sword', 'http://purl.org/net/sword/terms');
  $entry->registerXPathNamespace('hal', 'http://hal.archives-ouvertes.fr/');
  if (in_array($httpcode, array(200, 201, 202))) {
    $id = $entry->id;
    $passwdRes=$entry->xpath('hal:password');
    if (!empty($passwdRes) and is_array($passwdRes) and !empty($passwdRes[0][0]))
    {
      $passwd=$passwdRes[0][0];
    }
    else {
      $passwd='Unknown';
    }
    $link="unknown";
    $linkAttribute=$entry->link->attributes();
    if (isset($linkAttribute) && @count($linkAttribute) > 0) {
      if (!empty($linkAttribute) && isset($linkAttribute['href']) && !empty($linkAttribute['href'])) {
        $link = "<a target='_blank' href='".$linkAttribute['href']."'>prod</a>";
        $linkpreprod = "<a target='_blank' href='https://univ-rennes1.halpreprod.archives-ouvertes.fr/".$halid."'>preprod</a>";
      }
    }
    //exit ("<b>OK, modification effectuée :</b> id=>$id,passwd=>$passwd,link=> $link ou $linkpreprod \n");
    //exit ("<b>OK, modification effectuée :</b> id=>$id,passwd=>$passwd,link=> $link \n");
		if (isset($_GET['etp']) && ($_GET['etp'] == 1))
		{
			echo('<script type="text/javascript">');
			echo('setTimeout(window.close,1000);');
			echo('</script>');
		} else {
			header("Location: ".$linkAttribute['href']);
		}
  } else {
    //var_dump($return);
    $err = $entry->xpath('/sword:error/sword:verboseDescription');
    $summaries = $entry->xpath('/atom:summary/sword:error');
    //var_dump($summaries[0]);
    exit ("ERREUR : Pb sword : ".$err[0][0]."\n");
  }
} catch (Exception $e) {
  return ("ERREUR : Erreur Web service  : ".$e->getMessage()."\n");
}

curl_close($ch); 
?>
</body>
</html>
