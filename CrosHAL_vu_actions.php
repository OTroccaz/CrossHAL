<?php
$halID = $_POST["halID"];

$Fnm = "./CrosHAL_vu_halID.php";
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