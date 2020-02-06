<?php
$halID = $_POST["halID"];
$action = $_POST["action"];

//Stats contrôle des tiers
$ctb = $_POST["ctb"];
$domMel = $_POST["domMel"];
$proDate = $_POST["proDate"];
$Fnm1 = "./CrosHAL_ctrTrs.php";
include $Fnm1;
array_multisort($CTRTRS_LISTE);

$Fnm = "./CrosHAL_actions.php";
include $Fnm;
array_multisort($ACTIONS_LISTE);

if (strpos($halID, "#") !== false) {
	$tabID = explode("#", $halID);
	$actID = explode("#", $action);
	for ($id=0; $id<count($tabID); $id++) {
		$halIDact = $tabID[$id];
		$tabAct = explode("~", $actID[$id]);
		foreach ($tabAct as $act) {
			if ($act != "") {
				$ajout = count($ACTIONS_LISTE);
				$ACTIONS_LISTE[$ajout]["halID"] = $halIDact;
				$ACTIONS_LISTE[$ajout]["action"] = $act;
				$ACTIONS_LISTE[$ajout]["quand"] = time();
			}
		}
	}
}else{
	$tabAct = explode("~", $action);
	foreach ($tabAct as $act) {
		if ($act != "") {
			$ajout = count($ACTIONS_LISTE);
			$ACTIONS_LISTE[$ajout]["halID"] = $halID;
			$ACTIONS_LISTE[$ajout]["action"] = $act;
			$ACTIONS_LISTE[$ajout]["quand"] = time();
		}
	}
	//Stats contrôle des tiers
	$ajout = count($CTRTRS_LISTE);
	$CTRTRS_LISTE[$ajout]["halID"] = $halID;
	$CTRTRS_LISTE[$ajout]["proDate"] = $proDate;
	$CTRTRS_LISTE[$ajout]["ctb"] = $ctb;
	$CTRTRS_LISTE[$ajout]["domMel"] = $domMel;
	$CTRTRS_LISTE[$ajout]["quand"] = time();
}
$total = count($ACTIONS_LISTE);

$inF = fopen($Fnm,"w");
fseek($inF, 0);
$chaine = "";
$chaine .= '<?php'.chr(13);
$chaine .= '$ACTIONS_LISTE = array('.chr(13);
fwrite($inF,$chaine);
foreach($ACTIONS_LISTE AS $i => $valeur) {
  $chaine = $i.' => array("halID"=>"'.$ACTIONS_LISTE[$i]["halID"].'", ';
  $chaine .= '"action"=>"'.$ACTIONS_LISTE[$i]["action"].'", ';
  $chaine .= '"quand"=>"'.$ACTIONS_LISTE[$i]["quand"].'")';
  if ($i != $total-1) {$chaine .= ',';}
  $chaine .= chr(13);
  //session 1 day test
  //$hier = time() - 86400;
  //session 7 days test
  $hier = time() - 604800;
  if ($ACTIONS_LISTE[$i]["quand"] > $hier) {
    fwrite($inF,$chaine);
  }else{
    $i -= 1;
  }
}
$chaine = ');'.chr(13);
$chaine .= '?>';
fwrite($inF,$chaine);
fclose($inF);
array_multisort($ACTIONS_LISTE);

//Stats contrôle des tiers
$total = count($CTRTRS_LISTE);
$inF = fopen($Fnm1,"w");
fseek($inF, 0);
$chaine = "";
$chaine .= '<?php'.chr(13);
$chaine .= '$CTRTRS_LISTE = array('.chr(13);
fwrite($inF,$chaine);
foreach($CTRTRS_LISTE AS $i => $valeur) {
  $chaine = $i.' => array("halID"=>"'.$CTRTRS_LISTE[$i]["halID"].'", ';
  $chaine .= '"proDate"=>"'.$CTRTRS_LISTE[$i]["proDate"].'", ';
  $chaine .= '"ctb"=>"'.$CTRTRS_LISTE[$i]["ctb"].'", ';
	$chaine .= '"domMel"=>"'.$CTRTRS_LISTE[$i]["domMel"].'", ';
	$chaine .= '"quand"=>"'.$CTRTRS_LISTE[$i]["quand"].'")';
  if ($i != $total-1) {$chaine .= ',';}
  $chaine .= chr(13);
  fwrite($inF,$chaine);
}
$chaine = ');'.chr(13);
$chaine .= '?>';
fwrite($inF,$chaine);
fclose($inF);
array_multisort($CTRTRS_LISTE);
?>