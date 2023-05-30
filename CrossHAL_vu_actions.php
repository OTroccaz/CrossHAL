<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Auto-alimentation des halid concernés par une action des utilisateurs - Self-feeding of halids affected by user action
 */
 
$halID = $_POST["halID"];

$Fnm = "./CrossHAL_vu_halID.php";
include $Fnm;

$inF = fopen($Fnm,"w");
fseek($inF, 0);
$i = 0;
$chaine = "";
$chaine .= '<?php'.chr(13);
$chaine .= '$HALID_VU = array('.chr(13);
foreach ($HALID_VU AS $i => $valeur) {
  $chaine .= '"'.$HALID_VU[$i].'"';
  $chaine .= ',';
  $chaine .= chr(13);
}
$chaine .= '"'.$halID.'"';
$chaine .= chr(13);

$chaine .= ');'.chr(13);
$chaine .= '?>';
fwrite($inF,$chaine);
fclose($inF);
?>