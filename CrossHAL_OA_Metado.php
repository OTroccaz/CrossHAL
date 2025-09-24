<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Fonction de recherche de métadonnées via OpenAlex - OpenAlex metadata search function
 */
 
function rechMetadoOA($doi, $titre, &$doiOAR, &$revue, &$vol, &$num, &$pag, &$langue, &$financement, &$anr, &$datemel) {
	//Via DOI > https://api.openalex.org/works?filter=doi:10.1002/chem.202403385&mailto=laurent.jonchere@univ-rennes.fr&api_key=R7TkFjPwSMVEFT7d6t5Dt4
	//Via titre > https://api.openalex.org/works?filter=title.search:%22le%20titre%20en%20question%22&mailto=laurent.jonchere@univ-rennes.fr&api_key=R7TkFjPwSMVEFT7d6t5Dt4
	
	//Le DOI est connu et a été renseigné par le script d'appel à la fonction
	if ($doi != '') {
		$doiOAR = $doi;
		$urlOA = 'https://api.openalex.org/works?filter=doi:'.$doi.'&mailto=laurent.jonchere@univ-rennes.fr&api_key=R7TkFjPwSMVEFT7d6t5Dt4';
	}else{//Recherche via le titre
		//$titre = urlencode($titre);
		$titre = str_replace(array(',', ';', '.'), '', $titre);
		$titre = str_replace(' ', '%20', $titre);
		$urlOA = 'https://api.openalex.org/works?filter=title.search:%22'.$titre.'%22&mailto=laurent.jonchere@univ-rennes.fr&api_key=R7TkFjPwSMVEFT7d6t5Dt4';
	}
	//echo $urlOA.'<br>';
	//$contents = simplexml_load_file($urlOA);
	$headers = @get_headers($urlOA);
	if (preg_match("|200|", $headers[0])) {
		$contents = file_get_contents($urlOA);
		$resOA = json_decode($contents, true);
		//var_dump($resOA);
		//var_dump($resOA["results"][0]);
		
		if (isset($resOA["results"][0])) {
			//DOI si non renseigné intialement
			if ($doi == '') {
				$doiOAR = (isset($resOA["results"][0]["doi"]) && $resOA["results"][0]["doi"] != NULL) ? str_replace('https://doi.org/', '', $resOA["results"][0]["doi"]) : '';
			}
			
			//Revue
			$revue = (isset($resOA["results"][0]["primary_location"]["source"]["display_name"]) && $resOA["results"][0]["primary_location"]["source"]["display_name"] != NULL) ? $resOA["results"][0]["primary_location"]["source"]["display_name"] : '';
			
			//Vol/num/pag
			$vnp = '';
			$vol = (isset($resOA["results"][0]["biblio"]["volume"]) && $resOA["results"][0]["biblio"]["volume"] != NULL) ? $resOA["results"][0]["biblio"]["volume"] : '';
			$num = (isset($resOA["results"][0]["biblio"]["issue"]) && $resOA["results"][0]["biblio"]["issue"] != NULL) ? $resOA["results"][0]["biblio"]["issue"] : '';
			$pag = (isset($resOA["results"][0]["biblio"]["first_page"]) && $resOA["results"][0]["biblio"]["first_page"] != NULL && isset($resOA["results"][0]["biblio"]["last_page"]) && $resOA["results"][0]["biblio"]["last_page"] != NULL) ? $resOA["results"][0]["biblio"]["first_page"] . '-' . $resOA["results"][0]["biblio"]["last_page"] : '';
			$vnp = $vol . '(' . $num .')' . $pag;//Si différents éléments NULL, retournera ()
			$vnp = ($vnp != '()') ? $vnp : '';
			
			//Langue
			$langue = (isset($resOA["results"][0]["language"]) && $resOA["results"][0]["language"] != NULL) ? $resOA["results"][0]["language"] : '';
			
			//Financement + ANR
			$finOA = '';
			$anrOA = '';
			foreach ($resOA["results"][0]["grants"] as $funder) {
				$finOA .= $funder["funder_display_name"] . '; ';
				if ($funder["funder_display_name"] == 'Agence Nationale de la Recherche') {
					if (strpos($funder["award_id"] ?? '', '-') !== false) {
						//Parfois, plusieurs projets ANR sont renseignés, séparés par une virgule
						$awatab = explode(',', $funder["award_id"]);
						$a = 0;
						while (isset($awatab[$a])) {
							//Tests correctifs
							$anr = str_replace(array(' ', ', ANR', ',ANR'), '', $awatab[$a]);
							$anrtab = explode('-', $anr);
							$i = 0;
							$j = 0;//Compteur pour respecter la forme ANR-00-XXXX-0000
							$tst = 'non';
							while (isset($anrtab[$i])) {
								if ($anrtab[$i] == 'ANR') {
									$anrOA .= 'ANR-';
									$tst = 'oui';
									$j++;
								}else{
									if ($tst == 'oui' && $j < 4) {
										//Si dernier groupe de caractères pas encodé sur 4 chiffres, faire précéder de 0 (exemple : ANR-10-EQPX-05 au lieu de ANR-10-EQPX-0005 (manque 2 zeros))
										if ($j == 3 && strlen($anrtab[$i]) != 4) {
											if (strlen($anrtab[$i]) == 1) {$anrtab[$i] = '000'.$anrtab[$i];}
											if (strlen($anrtab[$i]) == 2) {$anrtab[$i] = '00'.$anrtab[$i];}
											if (strlen($anrtab[$i]) == 3) {$anrtab[$i] = '0'.$anrtab[$i];}
										}
										$anrOA .= $anrtab[$i].'-';
										$j++;
									}
								}
								$i++;
							}
							$anrOA = substr($anrOA, 0, -1).'; ';
							$a++;
						}
					}
				}
			}
			$financement = ($finOA != '') ? substr($finOA, 0, -2) : '';
			$anr = ($anrOA != '') ? substr($anrOA, 0, -2) : '';
			
			//Date de mise en ligne
			$datemel = (isset($resOA["results"][0]["publication_date"]) && $resOA["results"][0]["publication_date"] != NULL) ? $resOA["results"][0]["publication_date"] : '';
		}
	}
}
//$doi = "10.1038/s41467-025-60401-4";
//$doi = '';
//$titre = 'A Scoping Review on Air Quality Monitoring, Policy and Health in West African Cities';
//rechMetadoOA($doi, $titre, $doiOAR, $revue, $vol, $num, $pag, $langue, $financement, $anr, $datemel);
//echo 'toto : '.$doiOAR;
?>
