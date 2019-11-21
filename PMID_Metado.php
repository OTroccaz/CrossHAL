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
*/
function rechMetadoPMID($pmid, &$abstract, &$mcMESH, &$langue, &$keywords, &$datepub) {
  $urlPM = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&retmode=xml&id=".$pmid;
	if (testURL($urlPM)) {
		$contents = simplexml_load_file($urlPM);
		$resPM = objectToArray($contents);
		$abstract = "";
		$abstract_init = "";
		$keywords = "";
		$keywords_init = "";
		$mcMESH = "";
		//echo $pmid.'<br>';
		//Abstract
		if (isset($resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Abstract"]["AbstractText"])) {
			$abstract_init = $resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Abstract"]["AbstractText"];
		}
		if (is_array($abstract_init)) {
			$doc = new DomDocument();
			$doc->load("https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&retmode=xml&id=".$pmid); 
			$elts = $doc->getElementsByTagName('AbstractText');
			$res = array();
			$i = 0;
			$testTab = "1";
			foreach ($elts as $elt) {
				if ($elt->hasAttribute("Label")) {
					$quoi = $elt->getAttribute("Label");
					//echo ucfirst(strtolower($quoi)).'<br>';
					$res[$i] = ucfirst(strtolower($quoi))." - ";
				}else{
					$testTab = "0";
					$res[$i] = "";
				}
				$i += 2;
			}
			if ($testTab == "1") {
				//var_dump($res);
				$i = 1;
				foreach($abstract_init as $abs) {
					if (!is_array($abs)) {$res[$i] = $abs."<br><br>";}
					$i += 2;
				}
				$imax = $i - 2;;
				for($i = 0; $i <= $imax; $i++ ) {
					if (isset($res[$i])) {$abstract .= $res[$i];}
				}
				//echo($abstract);
			}else{
				//$abstract = $elts[0]->textContent;
			}
		}else{
			$abstract = $abstract_init;
		}
		//Mesh
		if (isset($resPM["PubmedArticle"]["MedlineCitation"]["MeshHeadingList"]["MeshHeading"])) {
			$mcMESH = $resPM["PubmedArticle"]["MedlineCitation"]["MeshHeadingList"]["MeshHeading"];
		}
		//Langue
		if (isset($resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Language"])) {
			$langue = $resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Language"];
		}
		//Keywords
		if (isset($resPM["PubmedArticle"]["MedlineCitation"]["KeywordList"]["Keyword"])) {
			$keywords_init = $resPM["PubmedArticle"]["MedlineCitation"]["KeywordList"]["Keyword"];
		}
		if (is_array($keywords_init)) {
			foreach($keywords_init as $key) {
				if (!is_array($key)) {
					$keywords .= $key.", ";
				}
			}
			$keywords = substr($keywords, 0, (strlen($keywords) - 2));
		}else{
			$keywords = $keywords_init;
		}
		//echo($keywords);
		//var_dump($resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Abstract"]);
		//var_dump($resPM["PubmedArticle"]["MedlineCitation"]["MeshHeadingList"]["MeshHeading"]);
		//var_dump($resPM["PubmedArticle"]["MedlineCitation"]["KeywordList"]["Keyword"]);
		
		//Datepub
		$datepub = "";
		if (isset($resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Journal"]["JournalIssue"]["PubDate"]["Year"])) {
			$datepub .= $resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Journal"]["JournalIssue"]["PubDate"]["Year"];
		}
		
		if (isset($resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Journal"]["JournalIssue"]["PubDate"]["Month"])) {
			$datetmp = $resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Journal"]["JournalIssue"]["PubDate"]["Month"];
			$datepub .= "-".str_replace(array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"), array("01","02","03","04","05","06","07","08","09","10","11","12"), $datetmp);
		}
		
		if (isset($resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Journal"]["JournalIssue"]["PubDate"]["Day"])) {
			$datepub .= "-".$resPM["PubmedArticle"]["MedlineCitation"]["Article"]["Journal"]["JournalIssue"]["PubDate"]["Day"];
		}
	}
}
//$pmid = "28879042";
//rechMetadoPMID($pmid, $abstract, $mcMESH, $langue, $keywords);
//echo 'toto : '.$abstract;
//var_dump($mcMESH);
?>
