<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Basée sur la distance de Levenshtein, fonction de recherche du titre à partir de CrossRef et du DOI - Based on Levenshtein distance, title search function using CrossRef and DOI
 */
 
function utf8_to_extended_ascii($str, &$map) {
  // find all multibyte characters (cf. utf-8 encoding specs)
  $matches = array();
  if (!preg_match_all('/[\xC0-\xF7][\x80-\xBF]+/', $str, $matches)) {
    return $str; // plain ascii string
  }
 
  // update the encoding map with the characters not already met
  foreach ($matches[0] as $mbc) {
    if (!isset($map[$mbc])) {
      $map[$mbc] = chr(128 + count($map));
    }
  }
 
  // finally remap non-ascii characters
  return strtr($str, $map);
}

function levenshtein_utf8($s1, $s2) {
  $charMap = array();
  $s1 = utf8_to_extended_ascii(strtolower($s1), $charMap);
  $s2 = utf8_to_extended_ascii(strtolower($s2), $charMap);
 
  return levenshtein($s1, $s2);
}
/*
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
  curl_setopt($ch, CURLOPT_USERAGENT, 'SCD (https://halur.univ-rennes.fr)');
  if (isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_CAINFO, "cacert.pem");
	}
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
*/
function rechTitreDOI ($titreI, $nbTest, &$closest, &$shortest, &$rechDOI) {
  $shortest = -1;// aucune distance trouvée pour le moment
  $titreR = str_replace(" ", "+", $titreI);
  $url = "https://api.crossref.org/works?rows=".$nbTest."&query.title=".$titreR;
  
  askCurl($url, $arrayTest);

  //var_dump($arrayTest["message"]["items"][0]["DOI"]);
  //var_dump($arrayTest["message"]["items"][0]["title"][0]);

  for($i = 0; $i < $nbTest; $i++) {
		$titreC = "";
    if (isset( $arrayTest["message"]["items"][$i]["title"][0])) {$titreC = $arrayTest["message"]["items"][$i]["title"][0];}
    //echo $titreC;
    $lev = levenshtein($titreI, $titreC);
    //echo $lev.'<br>';
    if ($lev == 0) {
        // le titre le plus près est celui-ci (correspondance exacte)
        $closest = $titreC;
        $shortest = 0;
        $iest = $i;
        // on sort de la boucle car nous avons trouvé une correspondance exacte
        break;
    }
    // Si la distance est plus petite que la prochaine distance trouvée
    // OU, si le prochain titre le plus près n'a pas encore été trouvé
    if ($lev <= $shortest || $shortest < 0) {
        // définition du titre le plus près ainsi que la distance
        $closest  = $titreC;
        $shortest = $lev;
        $iest = $i;
    }
  }
  if ($shortest < 10) {//les titres doivent être identiques à 90%
    $rechDOI = $arrayTest["message"]["items"][$iest]["DOI"];
  }
}

/*
$titreI = "Landscape level processes driving carabid crop assemblage in dynamic farmlands";
$titreI = "Integrating Biobank Data into a Clinical Data Research Network The IBCB Project";
$nbTest = 5;

rechTitreDOI ($titreI, $nbTest, $closest, $shortest, $rechDOI);
echo $shortest.' - '.$closest.' - '.$rechDOI;
*/
?>
