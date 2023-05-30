<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Regroupement des fonctions définies - Grouping of defined functions
 */
 
if(!function_exists("array_column")) {
  function array_column($array,$column_name) {
    return array_map(function($element) use($column_name){return $element[$column_name];}, $array);
  }
}

function strposa($haystack, $needles=array(), $offset=0) {
  $chr = array();
  foreach($needles as $needle) {
    $res = strpos($haystack, strval($needle), $offset);
    if ($res !== false) $chr[$needle] = $res;
  }
  if (empty($chr)) return false;
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
  if ($noeud == "" && $dueon != "") {
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
	}else{
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}
  $json = curl_exec($ch);
  curl_close($ch);

  $memory = intval(ini_get('memory_limit')) * 1024 * 1024;
  $limite = strlen($json)*1;
  if ($limite > $memory) {
    die ('<strong><font color="red">Désolé ! La collection et/ou la période choisie génère(nt) trop de résultats pour être traités correctement.</font></strong>');
  }else{
    $parsed_json = json_decode($json, true);
    $arrayCurl = objectToArray($parsed_json);
  }
}

function corrXML($xml) {
	//suppression noeud <teiHeader>
	$elts = $xml->documentElement;
	if (is_object($elts)) {
		if (is_object($elts->getElementsByTagName("teiHeader")->item(0))) {
			$elt = $elts->getElementsByTagName("teiHeader")->item(0);
			$newXml = $elts->removeChild($elt);
		}
		
		//suppression éventuel attribut 'corresp' pour le noeud <idno type="stamp" n="xxx" corresp="yyy">
		if (is_object($xml->getElementsByTagName("idno"))) {
			$elts = $xml->getElementsByTagName("idno");
			$nbelt = $elts->length;
			for ($pos = $nbelt; --$pos >= 0;) {
				$elt = $elts->item($pos);
				if ($elt && $elt->hasAttribute("type")) {
					$quoi = $elt->getAttribute("type");
					if ($quoi == "stamp") {
						if ($elt->hasAttribute("corresp")) {$elt->removeAttribute("corresp");}
						//$xml->save($nomfic);
					}
				}
			}
		}
		
		//suppression éventuel noeud <listBibl type="references">
		if (is_object($xml->getElementsByTagName("listBibl"))) {
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
		}
		
		//Correction éventuelle de l'ordre des noeuds idno/orgName et affiliation pour les auteurs
		$auts = $xml->getElementsByTagName("author");
		foreach($auts as $aut) {
			$tabAffil = array();
			$tabOrg = array();
			foreach($aut->childNodes as $elt) {
				if($elt->nodeName == "orgName") {
					//Enregistrement de l'organisme
					$tabOrg[] = $elt;
				}
				if($elt->nodeName == "affiliation") {
					//Enregistrement de l'affiliation
					$tabAffil[] = $elt;
				}
			}
			//Suppression des organismes
			foreach($tabOrg as $org){ 
				$aut->removeChild($org);
			}
			//Suppression des affiliations
			foreach($tabAffil as $aff){ 
				$aut->removeChild($aff);
			}
			//Ajout des organismes à la fin des noeuds
			foreach($tabOrg as $org){ 
				$aut->appendChild($org);
			}
			//Ajout des affiliations à la fin des noeuds
			foreach($tabAffil as $aff) {
				$aut->appendChild($aff);																		
			}
		}
		
		//Correction éventuelle de l'ordre des noeuds ref et fs pour les types de documents
		$edts = $xml->getElementsByTagName("edition");
		foreach($edts as $edt) {
			$tabRf = array();
			$tabFs = array();
			foreach($edt->childNodes as $elt) {
				if($elt->nodeName == "ref") {
					//Enregistrement de la référence
					$tabRf[] = $elt;
				}
				if($elt->nodeName == "fs") {
					//Enregistrement du 'fs'
					$tabFs[] = $elt;
				}
			}
			//Suppression des références
			foreach($tabRf as $ref){ 
				$edt->removeChild($ref);
			}
			//Suppression des 'fs'
			foreach($tabFs as $fes){ 
				$edt->removeChild($fes);
			}
			//Ajout des références à la fin des noeuds
			foreach($tabRf as $ref){ 
				$edt->appendChild($ref);
			}
			//Ajout des 'fs' à la fin des noeuds
			foreach($tabFs as $fes) {
				$edt->appendChild($fes);																		
			}
		}
		
		
		//Transformation des classCode VOCINRA en mots-clés
		$tabClas = array();
		$tabKeyw = array();
		$keys = array();
		$clas = $xml->getElementsByTagName("classCode");
		//Enregistrement des classCode
		foreach($clas as $cla) {
			if($cla->hasAttribute("scheme") && $cla->getAttribute("scheme") == "VOCINRA") {
				$tabKeyw[] = $cla->getAttribute("n");
				$tabClas[] = $cla;
			}
		}
		
		//Suppression des classCode
		foreach($tabClas as $cla) {
			$cla->parentNode->removeChild($cla);
		}
		//Ajout des classCode aux mots-clés
		$keys = $xml->getElementsByTagName("keywords");
		$langKeyw = "en";//Anglais par défaut
		//Récupération de la langue par défaut déjà présente pour les autres mots-clés
		foreach($keys as $key) {
			foreach($key->childNodes as $elt) {
				if($elt->hasAttribute("xml:lang")) {$langKeyw = $elt->getAttribute("xml:lang");}
			}
		}
		//Si présence d'une lettre accentuée pour un des mots-clés, c'est certainement du français
		foreach($tabKeyw as $keyw) {
			if (!ctype_alnum($keyw)) {
				$langKeyw = "fr";
				break;
			}
		}
		
		//Y-a-t-il déjà des mots-clés ?
		if ($keys->length != 0) {//Oui > on ajoute les nouveaux à la suite
			foreach($tabKeyw as $keyw){
				$bimoc = $xml->createElement("term");
				$moc = $xml->createTextNode($keyw);
				$bimoc->setAttribute("xml:lang", $langKeyw);
				$bimoc->appendChild($moc);
				$key->appendChild($bimoc);																		
			}
		}else{//Non > il faut créer le noeud 'keywords' s'il y a réellement de nouveaux mots-clés à ajouter
			if (!empty($tabKeyw)) {
				$tabClasN = array();
				$clas = $xml->getElementsByTagName("classCode");
				$txtC = $xml->getElementsByTagName("textClass");
				foreach($clas as $cla) {
					$tabClasN[] = $cla;
				}
				//Suppression des classCode
				foreach($tabClasN as $cla) {
					$cla->parentNode->removeChild($cla);
				}
				//Création du noeud 'keywords'
				$bimoc = $xml->createElement("keywords");
				$bimoc->setAttribute("scheme", "author");
				$txtC->item(0)->appendChild($bimoc);
				//Ajout des mots-clés
				$keys = $xml->getElementsByTagName("keywords");
				foreach($tabKeyw as $keyw){
					$bimoc = $xml->createElement("term");
					$moc = $xml->createTextNode($keyw);
					$bimoc->setAttribute("xml:lang", $langKeyw);
					$bimoc->appendChild($moc);
					$keys->item(0)->appendChild($bimoc);																		
				}
				
				//Rajout des classCode
				foreach($tabClasN as $cla) {
					$txtC->item(0)->appendChild($cla);
				}
			}
		}
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
	$valTEI = str_replace(array('<p>', '</p>'), '', $valTEI);
	$valTEI = str_replace('<p part="N">HAL API platform', '<p part="N">HAL API platform</p>', $valTEI);
  $teiPDF = '<?xml version="1.0" encoding="UTF-8"?>'.$valTEI;
  $Fnm = "./XML/".$halID.".xml";
  $xml = new DOMDocument( "1.0", "UTF-8" );
  $xml->formatOutput = true;
  $xml->preserveWhiteSpace = false;
  $xml->loadXML($teiPDF);
	
	//suppression éventuel noeud <ref type="externalLink"
	if (is_object($xml->getElementsByTagName("ref"))) {
		$elts = $xml->getElementsByTagName("ref");
		foreach($elts as $elt) {
			if ($elt->hasAttribute("type")) {
				$quoi = $elt->getAttribute("type");
				if ($quoi == "externalLink") {
					$parent = $elt->parentNode; 
					$newXml = $parent->removeChild($elt);
				}
			}
		}
	}
	
	//Suppression (temporaire ?) des stamps
	$stas = $xml->getElementsByTagName("idno");
	$tabSta = array();
	//Enregistrement des stamps dans un tableau
	foreach($stas as $sta) {
		if ($sta->hasAttribute("type") && $sta->getAttribute("type") == "stamp") {
			$tabSta[] = $sta;
		}
	}
	//Suppression des stamps
	foreach($tabSta as $elt){ 
		$elt->parentNode->removeChild($elt);
	}
	
	corrXML($xml);
  
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
      $lienPDF = "./CrossHAL_Modif.php?action=PDF&etp=3&Id=".$halID."&DOI=".$doi;
    }else{//pas de date de publication en ligne renseignée
      $lienPDF = "noDateEpub";
    }
  }else{//notice avec lien externe
    $elts = $xml->getElementsByTagName('ref');
    foreach ($elts as $elt) {
      if ($elt->hasAttribute("type") && $elt->getAttribute("type") == "file") {//PDF présent > exit
        $Fnm = "";
      }else{
        $avail = 'https://creativecommons.org/licenses/by';
				$label = "Attribution";
        if ($compNC != "") {$avail .= '-nc'; $label .= " - NonCommercial";}
        if ($compND != "") {$avail .= '-nd'; $label .= " - NoDerivatives";}
        if ($compSA != "") {$avail .= '-sa'; $label .= " - ShareAlike";}
        $avail .= '/';
        
        $edt = $xml->getElementsByTagName('edition');
        $bip = $xml->createElement("ref");
        $bip->setAttribute("type", "file");
        $bip->setAttribute("subtype", $evd);
        $bip->setAttribute("n", "1");
        //$bip->setAttribute("target", $targetPDF.$titPDF.".pdf");
        $bip->setAttribute("target", $urlPDF);
        $edt->item(0)->appendChild($bip);
				
				corrXML($xml);

        $xml->save($Fnm);
				
				//Availabilty
				if ($evd == "greenPublisher" || $evd == "publisherPaid") {
					//N'ajouter le noeud que s'il est absent !
					$tests = array();
					$tests = $xml->getElementsByTagName('availability');
					//$testsObj = objectToArray($tests);
					if (empty($tests)) {
						$stm = $xml->getElementsByTagName('publicationStmt');
						$bip = $xml->createElement("availability");
						$bip->setAttribute("status", "restricted");
						$stm->item(0)->appendChild($bip);
						$xml->save($Fnm);
						
						$avl = $xml->getElementsByTagName('availability');
						$bip = $xml->createElement("licence");
						$bip->setAttribute("target", $avail);
						$bip->nodeValue = $label;
						$avl->item(0)->appendChild($bip);
						$xml->save($Fnm);
					}
				}
        
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
          $lienPDF = "./CrossHAL_Modif.php?action=PDF&etp=3&Id=".$halID."&DOI=".$doi;
        }
        */
        $lienPDF = "./CrossHAL_Modif.php?action=PDF&etp=3&Id=".$halID."&DOI=".$doi;
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
	if (isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")	{
		curl_setopt($resURL, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($resURL, CURLOPT_CAINFO, "cacert.pem");
	}
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
function suppression($dir, $age) {
	
	$handle = opendir($dir);
	while($elem = readdir($handle)) {//ce while vide tous les répertoires et sous répertoires
		$ageElem = time() - filemtime($dir.'/'.$elem);
		if ($ageElem > $age) {
			if(is_dir($dir.'/'.$elem) && substr($elem, -2, 2) !== '..' && substr($elem, -1, 1) !== '.') {//si c'est un répertoire
				suppression($dir.'/'.$elem, $age);
			}else{
				if(substr($elem, -2, 2) !== '..' && substr($elem, -1, 1) !== '.')	{
					unlink($dir.'/'.$elem);
				}
			}
		}			
	}
	
	$handle = opendir($dir);
	while($elem = readdir($handle)) {//ce while efface tous les dossiers
		$ageElem = time() - filemtime($dir.'/'.$elem);
		if ($ageElem > $age) {
			if(is_dir($dir.'/'.$elem) && substr($elem, -2, 2) !== '..' && substr($elem, -1, 1) !== '.') {//si c'est un repertoire
				suppression($dir.'/'.$elem, $age);
				rmdir($dir.'/'.$elem);
			}    
		}
	}
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

function prenomCompEntier($prenom) {
  $prenom = trim($prenom);
  if (strpos($prenom,"-") !== false) {//Le prénom comporte un tiret
    $postiret = strpos($prenom,"-");
    $autg = substr($prenom,0,$postiret);
    $autd = substr($prenom,($postiret+1),strlen($prenom));
    $prenom = mb_ucwords($autg)."-".mb_ucwords($autd);
  }else{
    $prenom = mb_ucwords($prenom);
  }
  return $prenom;
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

function progression($indice, $iMax, &$iPro) {
	$iPro = $indice;
	echo "<script>";
  echo "var txt = 'Traitement référence $indice sur $iMax<br>';";
	echo "document.getElementById('cpt').innerHTML = txt";
	echo "</script>";
	ob_flush();
	flush();
	ob_flush();
	flush();
}

function proxyCURL($indice, $pause, $ipc, $iMax, &$iPro) {
	echo "<script>";
	echo "var txtinit = 'Traitement référence $indice sur $iMax<br>';";
	echo "var txtplus = '<strong><font color=\"red\">Référence $indice - Blocage proxy et CURL - Essai n°$ipc</font></strong><br>';";
	echo "document.getElementById('cpt').innerHTML = txtinit + txtplus";
	echo "</script>";
	ob_flush();
	flush();
	ob_flush();
	flush();
	usleep($pause);
}

?>