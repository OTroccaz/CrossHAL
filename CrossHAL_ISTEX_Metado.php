<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Fonction de recherche de métadonnées via ISTEX - ISTEX metadata search function
 */
 
function rechMetadoISTEX($doi, &$abstract, &$langue, &$keywords, &$langmoc, &$datepub) {
  $urlISTEX = "https://api.istex.fr/document/?q=rft_id=info:doi/".$doi."&size=1&output=*";
  //$contents = simplexml_load_file($urlISTEX);
  $headers = @get_headers($urlISTEX);
  if (preg_match("|200|", $headers[0])) {
    $contents = file_get_contents($urlISTEX);
    $resISTEX = json_decode($contents, true);
    //var_dump($resISTEX);
    //var_dump($resISTEX["hits"][0]);

    $doiISTEX = "";
    if (isset($resISTEX["hits"][0]["doi"][0])) {$doiISTEX = $resISTEX["hits"][0]["doi"][0];}
    $abstract = "";
    $langue = "";
    $keywords = "";
    $keywords_init = "";
    $langmoc = "";
    //echo $doi.' - '.$doiISTEX;
    //var_dump($resISTEX["hits"][0]);
    
    if ($doi == $doiISTEX) {//pour être sûr qu'il s'agit du bon article du fait du classement par score d'ISTEX
      //abstract
      if (isset($resISTEX["hits"][0]["abstract"])) {
        $abstract = $resISTEX["hits"][0]["abstract"];
        $abstract = str_replace("Abstract: ", "", $abstract);
      }
      //langue
      if (isset($resISTEX["hits"][0]["language"][0])) {$langue = $resISTEX["hits"][0]["language"][0];}
      //keywords
      if (isset($resISTEX["hits"][0]["subject"])) {
        $keywords_init = $resISTEX["hits"][0]["subject"];
        if (is_array($keywords_init)) {
          foreach($keywords_init as $key) {
            $langmoc .= $key["lang"][0].", ";
            $keywords .= $key["value"].", ";
          }
          $keywords = substr($keywords, 0, (strlen($keywords) - 2));
          $langmoc = substr($langmoc, 0, (strlen($langmoc) - 2));
        }else{
          $keywords = $keywords_init;
        }
      }else{
        if (isset($resISTEX["hits"][0]["keywords"]["teeft"]) && count($resISTEX["hits"][0]["keywords"]["teeft"]) < 5) {
          $keywords_init = $resISTEX["hits"][0]["keywords"]["teeft"];
        }
        if (is_array($keywords_init)) {
          foreach($keywords_init as $key) {
            $keywords .= $key.", ";
          }
          $keywords = substr($keywords, 0, (strlen($keywords) - 2));
        }else{
          $keywords = $keywords_init;
        }
      }
			//datepub
			if (isset($resISTEX["hits"][0]["publicationDate"])) {
        $datepub = $resISTEX["hits"][0]["publicationDate"];
			}
    }
  }
}
//$doi = "10.1016/0167-5273(94)90281-X";//noeud abstract présent
//$doi = "10.1007/s00232-011-9406-2";//noeud subject présent
//$doi = "10.1007/s00259-008-0735-z";
//rechMetadoISTEX($doi, $abstract, $langue, $keywords, $langmoc);
//echo 'toto : '.$langue;
?>
