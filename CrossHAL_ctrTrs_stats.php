<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Contrôle des tiers - Control of third parties
 */
 
//Récupération de l'adresse IP du client (on cherche d'abord à savoir s'il est derrière un proxy)
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
  $ip = $_SERVER['HTTP_CLIENT_IP'];
}else {
  $ip = $_SERVER['REMOTE_ADDR'];
}

//Restriction IP
include("./Glob_IP_list.php");
if (!in_array($ip, $IP_aut)) {
  echo "<br><br><center><font face='Corbel'><strong>";
  echo "Votre poste n'est pas autorisé à accéder à cette application.";
  echo "</strong></font></center>";
  die;
}

header('Content-type: text/html; charset=UTF-8');

if (isset($_GET['css']) && ($_GET['css'] != ""))
{
  $css = $_GET['css'];
}else{
  $css = "https://halur1.univ-rennes1.fr/HAL_SCD.css";
}
?>
<html lang="fr">
<head>
  <title>CrossHAL</title>
  <meta name="Description" content="CrossHAL">
  <link href="bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $css;?>" type="text/css">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <script type="text/javascript" language="Javascript" src="./CrossHAL.js"></script>
  <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
  <link rel="icon" type="type/ico" href="HAL_favicon.ico">
  <link rel="stylesheet" href="./CrossHAL.css">
</head>
<body>

<noscript>
<div class='text-primary' id='noscript'><strong>ATTENTION !!! JavaScript est désactivé ou non pris en charge par votre navigateur : cette procédure ne fonctionnera pas correctement.</strong><br>
<strong>Pour modifier cette option, voir <a target='_blank' rel='noopener noreferrer' href='http://www.libellules.ch/browser_javascript_activ.php'>ce lien</a>.</strong></div><br>
</noscript>

<table class="table100" aria-describedby="Entêtes">
<tr>
<th scope="col" style="text-align: left;"><img alt="CrossHAL" title="CrossHAL" width="250px" src="./img/logo_CrossHAL.png"></th>
<th scope="col" style="text-align: right;"><img alt="Université de Rennes 1" title="Université de Rennes 1" width="150px" src="./img/logo_UR1_gris_petit.jpg"></th>
</tr>
</table>
<hr style="color: #467666; height: 1px; border-width: 1px; border-top-color: #467666; border-style: inset;">

<p>Tableau des actions enregistrées pour le contrôle des tiers.</p>
<br>

<?php
//Suppression de ligne demandée
if (isset($_GET["id"])) {
	$id = $_GET["id"] - 1;
	include "./CrossHAL_ctrTrs.php";
	unset($CTRTRS_LISTE[$id]);
	//var_dump($CTRTRS_LISTE);
	array_multisort($CTRTRS_LISTE);
	$Fnm = "./CrossHAL_ctrTrs.php";
	$total = count($CTRTRS_LISTE);
	$inF = fopen($Fnm,"w");
	fseek($inF, 0);
	$chaine = "";
	$chaine .= '<?php'.chr(13);
	$chaine .= '$CTRTRS_LISTE = array('.chr(13);
	fwrite($inF,$chaine);
	foreach($CTRTRS_LISTE AS $i => $valeur) {
		$chaine = $i.' => array("halID"=>"'.$CTRTRS_LISTE[$i]["halID"].'", ';
		$chaine .= '"proDate"=>"'.$CTRTRS_LISTE[$i]["proDate"].'", ';
		$chaine .= '"depDate"=>"'.$CTRTRS_LISTE[$i]["depDate"].'", ';
		$chaine .= '"ctb"=>"'.$CTRTRS_LISTE[$i]["ctb"].'", ';
		$chaine .= '"domMel"=>"'.$CTRTRS_LISTE[$i]["domMel"].'", ';
		$chaine .= '"team"=>"'.$CTRTRS_LISTE[$i]["team"].'", ';
		$chaine .= '"quand"=>"'.$CTRTRS_LISTE[$i]["quand"].'")';
		if ($i != $total-1) {$chaine .= ',';}
		$chaine .= chr(13);
		fwrite($inF,$chaine);
	}
	$chaine = ');'.chr(13);
	$chaine .= '?>';
	fwrite($inF,$chaine);
	fclose($inF);
	header("Location:"."./CrossHAL_ctrTrs_stats.php");
}

include "./CrossHAL_ctrTrs.php";
//$quand  = array_column($CTRTRS_LISTE, 'quand');//Uniquement si > PHP 5.5.0 >>> pb UR1
//$ctb = array_column($CTRTRS_LISTE, 'ctb');//Uniquement si > PHP 5.5.0 >>> pb UR1
//Si < PHP 5.5.0
foreach ($CTRTRS_LISTE as $key => $row) {
    $quand[$key]  = date("d/m/y", $row['quand']);
    $ctb[$key] = $row['ctb'];
}
array_multisort($quand, SORT_DESC, $ctb, SORT_ASC, $CTRTRS_LISTE);

//export results in a CSV file
$Fnm = "./HAL/ctrTrs.csv"; 
$inF = fopen($Fnm,"w"); 
fseek($inF, 0);
$chaine = "\xEF\xBB\xBF";
fwrite($inF,$chaine);

$inF = fopen($Fnm,"a+"); 
fseek($inF, 0);
fwrite($inF, "ID;halID;Année de publication;Date de dépôt;Contributeur;Domaine email;Code collection;Modifié le".chr(13).chr(10));

echo ("<table class='table table-striped table-bordered table-hover;'><tr>");
echo ("<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>ID</strong></td>");
echo ("<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>halID</strong></td>");
echo ("<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Année de publication</strong></td>");
echo ("<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Date de dépôt</strong></td>");
echo ("<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Contributeur</strong></td>");
echo ("<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Domaine email</strong></td>");
echo ("<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Code collection</strong></td>");
echo ("<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Modifié le</strong></td>");
echo ("<td style='text-align: center; background-color: #eeeeee; color: #999999;'><strong>Edition</strong></td>");
echo ("</tr>");

foreach($CTRTRS_LISTE AS $i => $valeur) {
	$chaine = "";
	$j = $i + 1;
	echo ("<tr style='text-align: center;'><td>".$j."</td>");
	$chaine .= $j.';';
	echo ("<td style='text-align: center;'><a target='_blank' href='https://hal.science/".$CTRTRS_LISTE[$i]["halID"]."'>".$CTRTRS_LISTE[$i]["halID"]."</a></td>");
	$chaine .= $CTRTRS_LISTE[$i]["halID"].';';
	echo ("<td style='text-align: center;'>".$CTRTRS_LISTE[$i]["proDate"]."</td>");
	$chaine .= $CTRTRS_LISTE[$i]["proDate"].';';
	echo ("<td style='text-align: center;'>".$CTRTRS_LISTE[$i]["depDate"]."</td>");
	$chaine .= $CTRTRS_LISTE[$i]["depDate"].';';
	echo ("<td style='text-align: center;'>".$CTRTRS_LISTE[$i]["ctb"]."</td>");
	$chaine .= $CTRTRS_LISTE[$i]["ctb"].';';
	echo ("<td style='text-align: center;'>".$CTRTRS_LISTE[$i]["domMel"]."</td>");
	$chaine .= $CTRTRS_LISTE[$i]["domMel"].';';
	echo ("<td style='text-align: center;'>".$CTRTRS_LISTE[$i]["team"]."</td>");
	$chaine .= $CTRTRS_LISTE[$i]["team"].';';
	echo ("<td style='text-align: center;'>".date("d/m/y", $CTRTRS_LISTE[$i]["quand"])."</td>");
	$chaine .= date("d/m/y", $CTRTRS_LISTE[$i]["quand"]).';';
	echo ("<td style='text-align: center;'><a href='?id=".$j."'>Supprimer</a></td>");
	//Ajout au CSV
  fwrite($inF, $chaine.chr(13).chr(10));
}

echo ("</tr>");
echo ("</table>");
fclose($inF);
echo ("<br><a href='./HAL/ctrTrs.csv'>Exporter les résultats au format CSV</a>");
?>
<br>
<?php
include('./Glob_bas.php');
?>
</body>
</html>