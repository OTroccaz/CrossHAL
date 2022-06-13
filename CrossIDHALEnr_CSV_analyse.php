<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<?php
header('Content-type: text/html; charset=UTF-8');

if (isset($_GET['css']) && ($_GET['css'] != ""))
{
  $css = $_GET['css'];
}else{
  $css = "https://ecobio.univ-rennes1.fr/HAL_SCD.css";
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
	
	<!-- App css -->
	<link href="./assets/css/icons.css" rel="stylesheet" type="text/css" />
	
	<!-- Datatables css -->
	<link href="./assets/css/vendor/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
	<link href="./assets/css/vendor/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
	
	<style>
	tr:nth-child(even) {background-color: #dedede;}
	</style>

</head>
<body>

<noscript>
<div class='text-primary' id='noscript'><strong>ATTENTION !!! JavaScript est désactivé ou non pris en charge par votre navigateur : cette procédure ne fonctionnera pas correctement.</strong><br>
<strong>Pour modifier cette option, voir <a target='_blank' rel='noopener noreferrer' href='https://www.libellules.ch/browser_javascript_activ.php'>ce lien</a>.</strong></div><br>
</noscript>

<table class="table100" aria-describedby="Entêtes">
<tr>
<th scope="col" style="text-align: left;"><img alt="CrossHAL" title="CrossHAL" width="250px" src="./img/logo_CrossHAL.png"></th>
<th scope="col" style="text-align: right;"><img alt="Université de Rennes 1" title="Université de Rennes 1" width="150px" src="./img/logo_UR1_gris_petit.jpg"></th>
</tr>
</table>
<hr style="color: #467666; height: 1px; border-width: 1px; border-top-color: #467666; border-style: inset;">

<a href="./CrossIDHALEnr_CSV.php">Recharger votre fichier CSV enrichi.</a>
<br><br>

CrossIDHALEnr - Traitement de votre fichier CSV enrichi :
<br><br>

<?php
include "./CrossHAL_fonctions.php";
function utf8_fopen_read($fileName) {
    //$fc = iconv('windows-1250', 'utf-8', file_get_contents($fileName));
    $fc = file_get_contents($fileName);
    $handle=fopen("php://memory", "rw");
    fwrite($handle, $fc);
    fseek($handle, 0);
    return $handle;
}

function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}
include ("./Glob_normalize.php");

$Fnm = "./CrossIDHALEnr.php";
include $Fnm;

$avecIdHAL = array();
$sansIdHAL = array();
$quelIdhAL = array();

//Comptage des idHAL
$Nom = "";
$Prenom = "";
$uniqK = "";

foreach($CrossIDHALEnr as $elt) {
	//if ($elt["Nom"] == $Nom && substr($elt["Prenom"], 0, 1) == substr($Prenom, 0, 1) && $uniqK != "") {
	if ($elt["Nom"] == $Nom && $elt["Prenom"] == $Prenom && $uniqK != "") {
		if ($elt["idHAL"] != "") {
			$avecIdHAL[$uniqK]++;
			$quelIdHAL[$uniqK] = $elt["idHAL"];
		}else{
			$sansIdHAL[$uniqK]++;
		}
	}else{
		$Nom = $elt["Nom"];
		//$Prenom = substr($elt["Prenom"], 0, 1);
		$Prenom = $elt["Prenom"];
		$uniqK = strtolower(normalize($Nom.$Prenom));
		if (!isset($avecIdHAL[$uniqK])) {$avecIdHAL[$uniqK] = 0;}
		if (!isset($sansIdHAL[$uniqK])) {$sansIdHAL[$uniqK] = 0;}
		if (!isset($quelIdHAL[$uniqK])) {$quelIdHAL[$uniqK] = "";}
		if ($elt["idHAL"] != "") {
			$avecIdHAL[$uniqK]++;
			$quelIdHAL[$uniqK] = $elt["idHAL"];
		}else{
			$sansIdHAL[$uniqK]++;
		}
	}
}

//var_dump($avecIdHAL);
//var_dump($sansIdHAL);
//var_dump($quelIdHAL);

?>

<!--<table class="table100" aria-describedby="Entêtes">-->
<table id="basic-datatable" class="table table-responsive table-bordered table-striped table-centered table-sm text-center small">
<thead class='thead-dark'>
<tr>
<th scope="col" style="text-align: center;">Indice</th>
<th scope="col" style="text-align: center;">Nom</th>
<th scope="col" style="text-align: center;">Prénom</th>
<th scope="col" style="text-align: center;">idHAL isolés</th>
<th scope="col" style="text-align: center;">idHAL suggéré</th>
<th scope="col" style="text-align: center;"># ocurrences (idHAL)</th>
<th scope="col" style="text-align: center;"># sans idHAL</th>
<th scope="col" style="text-align: center;">Lien AUréHAL</th>
<th scope="col" style="text-align: center;">Année</th>
<th scope="col" style="text-align: center;">Domaine</th>
<th scope="col" style="text-align: center;">Affiliation</th>
</tr></thead><tbody>


<?php
$ind = 1;
foreach($CrossIDHALEnr as $elt) {
	$Nom = $elt["Nom"];
	$Prenom = $elt["Prenom"];
	$uniqK = strtolower(normalize($Nom.$Prenom));
	if ($elt["idHAL"] == "") {
		if (isset($quelIdHAL[$uniqK]) && $quelIdHAL[$uniqK] != "") {
			echo '<tr>';
			echo '<td>'.$ind.'</td>';
			echo '<td>'.$elt["Nom"].'</td>';
			echo '<td>'.$elt["Prenom"].'</td>';
			echo '<td>&nbsp;</td>';
			if (isset($quelIdHAL[$uniqK])) {echo '<td>'.$quelIdHAL[$uniqK].'</td>';}else{echo '<td>&nbsp;</td>';}
			if (isset($avecIdHAL[$uniqK])) {echo '<td>'.$avecIdHAL[$uniqK].'</td>';}else{echo '<td>&nbsp;</td>';}
			if (isset($sansIdHAL[$uniqK])) {echo '<td>'.$sansIdHAL[$uniqK].'</td>';}else{echo '<td>&nbsp;</td>';}
			echo '<td><a target="_blank" href="https://aurehal.archives-ouvertes.fr/person/browse?critere='.$elt["Nom"].'+'.$elt["Prenom"].'">Lien</a></td>';
			echo '<td>'.$elt["Annee"].'</td>';
			echo '<td>'.$elt["Domaine"].'</td>';
			echo '<td>'.$elt["Affiliation"].'</td>';
			echo '</tr>';
			$ind++;
		}
	}else{//IdHAL isolés
		if ($avecIdHAL[$uniqK] == 1) {
			if (strlen(str_replace('.', '', $elt["Prenom"])) == 1) {
				echo '<tr>';
				echo '<td>'.$ind.'</td>';
				echo '<td>'.$elt["Nom"].'</td>';
				echo '<td>'.$elt["Prenom"].'</td>';
				echo '<td>'.$quelIdHAL[$uniqK].' ???</td>';
				echo '<td>&nbsp;</td>';
				if (isset($avecIdHAL[$uniqK])) {echo '<td>'.$avecIdHAL[$uniqK].'</td>';}else{echo '<td>&nbsp;</td>';}
				if (isset($sansIdHAL[$uniqK])) {echo '<td>'.$sansIdHAL[$uniqK].'</td>';}else{echo '<td>&nbsp;</td>';}
				echo '<td><a target="_blank" href="https://aurehal.archives-ouvertes.fr/author/browse?critere='.$elt["Nom"].'+'.$elt["Prenom"].'">Lien</a></td>';
				echo '<td>'.$elt["Annee"].'</td>';
				echo '<td>'.$elt["Domaine"].'</td>';
				echo '<td>'.$elt["Affiliation"].'</td>';
				echo '</tr>';
				$ind++;
			}
		}
	}
}

?>
</tbody>
</table>
<br><br>
<?php
include('./Glob_bas.php');
?>
</body>

<!-- Datatables js -->
<script src="assets/js/vendor/jquery.dataTables.min.js"></script>
<script src="assets/js/vendor/dataTables.bootstrap4.js"></script>
<script src="assets/js/vendor/dataTables.responsive.min.js"></script>
<script src="assets/js/vendor/responsive.bootstrap4.min.js"></script>

<!-- Datatable Init js -->
<script src="assets/js/pages/demo.datatable-init.js"></script>
                                                
</html>