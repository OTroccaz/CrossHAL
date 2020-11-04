<?php
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
  curl_setopt($ch, CURLOPT_USERAGENT, 'SCD (https://halur1.univ-rennes1.fr)');
  curl_setopt($ch, CURLOPT_USERAGENT, 'PROXY (http://siproxy.univ-rennes1.fr)');
  if (isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_CAINFO, "cacert.pem");
	}
  $json = curl_exec($ch);
  curl_close($ch);
  $parsed_json = json_decode($json);
  $arrayCurl = objectToArray($parsed_json);
}
*/

function rechRevueISSN($doi, &$issn, &$eissn, &$docid, &$rev) {
  $urlCR = "https://api.crossref.org/v1/works/http:/dx.doi.org/".$doi;
  $issn = "";
  $eissn = "";
  $docid = "";
  $title = "";
  askCurl($urlCR, $arrayCR);
  
  //var_dump($arrayCR["message"]["issn-type"]);
  
  if (isset($arrayCR["message"]["issn-type"])) {
		$issnType = $arrayCR["message"]["issn-type"];
  
		foreach ($issnType as $res) {
			if ($res["type"] == "print") {
				$issn = $res["value"];
			}
			if ($res["type"] == "electronic") {
				$eissn = $res["value"];
			}
		}
	}
  
  if ($issn != "") {
    $urlHAL = "https://api.archives-ouvertes.fr/ref/journal/?q=issn_s:".$issn."&fl=title_s,valid_s,label_s,docid,code_s";
    askCurl($urlHAL, $arrayHAL);
    
    //var_dump($arrayHAL["response"]["docs"]);
    
    $docs = $arrayHAL["response"]["docs"];
    
    foreach ($docs as $res) {
      if ($res["valid_s"] == "VALID") {
        $docid = $res["docid"];
        $rev = $res["title_s"];
        break;
      }
    }
  }
  
  if ($eissn != "" && $docid == "") {
    $urlHAL = "https://api.archives-ouvertes.fr/ref/journal/?q=eissn_s:".$eissn."&fl=title_s,valid_s,label_s,docid,code_s";
    askCurl($urlHAL, $arrayHAL);
    
    $docs = $arrayHAL["response"]["docs"];
    
    foreach ($docs as $res) {
      if ($res["valid_s"] == "VALID") {
        $docid = $res["docid"];
        $rev = $res["title_s"];
        break;
      }
    }
  }
}
//$doi = "10.1080/2162402X.2016.1186323";
//$issn = "2212-8271";
//rechRevueISSN($doi, $issn, $eissn, $docid, $rev);
//echo 'toto : '.$issn.' - '.$eissn;
//echo'<br>';
//echo 'titi : '.$docid.' - '.$rev;
?>
