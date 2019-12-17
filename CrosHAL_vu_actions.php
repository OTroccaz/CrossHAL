<?php
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
  if ($noeud == "" && $dueon != "") {
    $bibl = $xml->getElementsByTagName($amont);
    foreach ($bibl as $elt) {
			if ($elt->hasChildNodes()) {
				foreach($elt->childNodes as $item) { 
					if ($item->hasChildNodes()) {
						$childs = $item->childNodes;
						foreach($childs as $i) {
							$name = $i->parentNode->nodeName;
							if ($name == $aval) {//insertion nvx noeuds
								$bip = $xml->createElement($tagName);
								$cTn = $xml->createTextNode($dueon);
								if ($typAtt1 != "" && $valAtt1 != "") {$bip->setAttribute($typAtt1, $valAtt1);}
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
			}else{
				//Il y a juste '<notesStmt/>'
				$item0 = $xml->getElementsByTagName("notesStmt")->item(0);
				$note = $xml->createElement($tagName);
				$note->setAttribute($typAtt1, $valAtt1);
				$note->nodeValue = $dueon;
				$item0->appendChild($note);
			}
    }
  }
}

function objectToArray($object) {
  if (!is_object( $object) && !is_array($object)) {
    return $object;
  }
  if (is_object($object)) {
    $object = get_object_vars($object);
  }
  return array_map('objectToArray', $object);
}

$Vu = $_POST["Vu"];
$halID = $_POST["halID"];

$urlVu = "https://api.archives-ouvertes.fr/search/?q=halId_s:%22".$halID."%22&fl=label_xml";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlVu);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'SCD (https://halur1.univ-rennes1.fr)');
curl_setopt($ch, CURLOPT_USERAGENT, 'PROXY (http://siproxy.univ-rennes1.fr)');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$json = curl_exec($ch);
curl_close($ch);
$parsed_json = json_decode($json, true);
$arrayVu = objectToArray($parsed_json);

//Récupération du TEI
$tei = $arrayVu["response"]["docs"][0]["label_xml"];
$tei = str_replace(array('<p>', '</p>'), '', $tei);
$tei = str_replace('<p part="N">HAL API platform', '<p part="N">HAL API platform</p>', $tei);
$teiRes = '<?xml version="1.0" encoding="UTF-8"?>'.$tei;

$Fnm = "./XML/".$halID.".xml";
$xml = new DOMDocument( "1.0", "UTF-8" );
$xml->formatOutput = true;
$xml->preserveWhiteSpace = false;
$xml->loadXML($teiRes);
insertNode($xml, $Vu, "notesStmt", "note", "note", "type", "commentary", "", "", "aC");
$xml->save($Fnm);
?>