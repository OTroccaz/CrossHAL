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

if (isset($_FILES['CSV_OCDHAL']['name']) && $_FILES['CSV_OCDHAL']['name'] != "") //File has been submitted
{
	if ($_FILES['CSV_OCDHAL']['error'])
	{
		switch ($_FILES['CSV_OCDHAL']['error'])
		{
			 case 1: // UPLOAD_ERR_INI_SIZE
			 Header("Location: "."CrossIDHALEnr_CSV.php?erreur=1");
			 break;
			 case 2: // UPLOAD_ERR_FORM_SIZE
			 Header("Location: "."CrossIDHALEnr_CSV.php?erreur=2");
			 break;
			 case 3: // UPLOAD_ERR_PARTIAL
			 Header("Location: "."CrossIDHALEnr_CSV.php?erreur=3");
			 break;
			 //case 4: // UPLOAD_ERR_NO_FILE
			 //Header("Location: "."OverHAL.php?erreur=4");
			 //break;
		}
	}
	$extension = strrchr($_FILES['CSV_OCDHAL']['name'], '.');
	if ($extension != ".csv") {
		Header("Location: "."CrossIDHALEnr_CSV.php?erreur=5");
	}
	$temp = $_FILES['CSV_OCDHAL']['tmp_name'];
	
	$Fnm = "./CrossIDHALEnr.php";
	include $Fnm;
	//array_multisort($CrossIDHALEnr);
	
	$inF = fopen($Fnm,"w");
	fseek($inF, 0);
	/*
	$chaine = "";
	$chaine .= '<?php'.chr(13);
	$chaine .= '$CrossIDHALEnr = array('.chr(13);
	fwrite($inF,$chaine);
	
	$ind = 0;
	$chaine = "";
	foreach($CrossIDHALEnr as $elt) {
		$chaine .= $ind.'=>array(';
		$chaine .= '"Nom"=>"'.trim($elt["Nom"]).'", ';
		$chaine .= '"Prenom"=>"'.trim($elt["Prenom"]).'", ';
		$chaine .= '"idHAL"=>"'.trim($elt["idHAL"]).'", ';
		$chaine .= '"Annee"=>"'.trim($elt["Annee"]).'", ';
		$chaine .= '"Affiliation"=>"'.trim(str_replace('"', '\"', $elt["Affiliation"])).'"),';
		$chaine .= chr(13);
		$ind++;
	}
	fwrite($inF,$chaine);
	//echo $chaine;
	//echo in_array_r("titi", $CrossIDHALEnr) ? 'oui' : 'non';
	*/

	$chaine = "";
	$chaine .= '<?php'.chr(13);
	$chaine .= '$CrossIDHALEnr = array('.chr(13);
	fwrite($inF,$chaine);
	
	$ind = 0;
	$chaine = "";
	$handle = utf8_fopen_read($temp);
	if ($handle) {
		$ligne = 0;
		$total = count(file($temp));
		while($tab = fgetcsv($handle, 0, ';')) {
			if ($ligne != 0) {//Exclure les noms des colonnes
				$chaine = $ind.'=>array(';
        $chaine .= '"Nom"=>"'.trim($tab[0]).'", ';
        $chaine .= '"Prenom"=>"'.trim($tab[1]).'", ';
        $chaine .= '"idHAL"=>"'.trim($tab[2]).'", ';
        $chaine .= '"Annee"=>"'.trim($tab[5]).'", ';
				$chaine .= '"Domaine"=>"'.trim($tab[10]).'", ';
        $chaine .= '"Affiliation"=>"'.str_replace('"', '’', trim($tab[6])).' - '.str_replace('"', '’', trim($tab[9])).'")';
				if ($ligne != $total-1) {
					$chaine .= ',';
				}
				$chaine .= chr(13);
				fwrite($inF,$chaine);
				$ind++;
			}
			$ligne++;
		}
		$chaine = ');'.chr(13);
		$chaine .= '?>';
		fwrite($inF,$chaine);
	}else{
		$chaine = ');'.chr(13);
		$chaine .= '?>';
		fwrite($inF,$chaine);
		die("<font color='red'><big><big>Votre fichier source est incorrect.</big></big></font>");
	}
fclose($inF);
fclose($handle);
echo ('<br><strong>Le fichier nécessaire au traitement des idHAL auteurs via OCDHAL enrichi a été créé avec succès.<br><br>');
echo ('<a href="./CrossIDHALEnr_CSV_analyse.php">Vous pouvez lancer l\'analyse de ce fichier</a><br><br>');
}
?>

<?php
include('./Glob_bas.php');
?>
</body>
</html>