<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Traitement des manuscrits auteurs (via OverHAL) - Processing author manuscripts (via OverHAL)
 */
 
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
function utf8_fopen_read($fileName) {
    //$fc = iconv('windows-1250', 'utf-8', file_get_contents($fileName));
    $fc = file_get_contents($fileName);
    $handle=fopen("php://memory", "rw");
    fwrite($handle, $fc);
    fseek($handle, 0);
    return $handle;
}
if (isset($_FILES['CSV_CrossHAL']['name']) && $_FILES['CSV_CrossHAL']['name'] != "") //File has been submitted
{
	if ($_FILES['CSV_CrossHAL']['error'])
	{
		switch ($_FILES['CSV_CrossHAL']['error'])
		{
			 case 1: // UPLOAD_ERR_INI_SIZE
			 Header("Location: "."CrossHAL_CSV.php?erreur=1");
			 break;
			 case 2: // UPLOAD_ERR_FORM_SIZE
			 Header("Location: "."CrossHAL_CSV.php?erreur=2");
			 break;
			 case 3: // UPLOAD_ERR_PARTIAL
			 Header("Location: "."CrossHAL_CSV.php?erreur=3");
			 break;
			 //case 4: // UPLOAD_ERR_NO_FILE
			 //Header("Location: "."OverHAL.php?erreur=4");
			 //break;
		}
	}
	$extension = strrchr($_FILES['CSV_CrossHAL']['name'], '.');
	if ($extension != ".csv") {
		Header("Location: "."CrossHAL_CSV.php?erreur=5");
	}
	$temp = $_FILES['CSV_CrossHAL']['tmp_name'];
	
	$Fnm = "./CrossHAL_Stats_overhal_mails_UR1.php";
	$inF = fopen($Fnm,"w");
	fseek($inF, 0);
	$chaine = "";
	$chaine .= '<?php'.chr(13);
	$chaine .= '$Stats_OH_Mails = array('.chr(13);
	fwrite($inF,$chaine);
	$handle = utf8_fopen_read($temp);
	if ($handle) {
		$ligne = 0;
		$total = count(file($temp));
		while($tab = fgetcsv($handle, 0, ';')) {
			if ($ligne != 0) {//Exclure les noms des colonnes
				$ind = $ligne - 1;
				$chaine = $ind.'=>array("Quand"=>"'.$tab[0].'", ';
        $chaine .= '"Destinataire"=>"'.$tab[1].'", ';
        $chaine .= '"Article"=>"'.$tab[2].'", ';
				$chaine .= '"Titre"=>"'.$tab[7].'", ';
        $chaine .= '"Type"=>"'.$tab[3].'", ';
        $chaine .= '"Fichier"=>"'.$tab[4].'", ';
        $chaine .= '"Langue"=>"'.$tab[5].'", ';
        $chaine .= '"Labo"=>"'.$tab[6].'", ';
        $chaine .= '"Reponse"=>"'.$tab[8].'", ';
				$chaine .= '"Forward"=>"'.$tab[9].'", ';
        $chaine .= '"Remarques"=>"'.$tab[10].'")';
				if ($ligne != $total-1) {$chaine .= ',';}
				$chaine .= chr(13);
				fwrite($inF,$chaine);
			}
			$ligne++;
		}
		$chaine = ');'.chr(13);
		$chaine .= '?>';
		fwrite($inF,$chaine);
		fclose($inF);
		fclose($handle);
	}else{
		die("<font color='red'><big><big>Votre fichier source est incorrect.</big></big></font>");
	}
	echo ('<br><strong>Le fichier nécessaire au traitement des manuscrits auteurs via OverHAL a été créé avec succès.<br><br>');
	echo ('Vous pouvez fermer cet onglet et lancer votre requête<br><br>');
	}
?>

<?php
include('./Glob_bas.php');
?>
</body>
</html>