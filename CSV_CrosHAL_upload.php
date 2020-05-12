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
  <script type="text/javascript" language="Javascript" src="./CrosHAL.js"></script>
  <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
  <link rel="icon" type="type/ico" href="HAL_favicon.ico">
  <link rel="stylesheet" href="./CrosHAL.css">
</head>
<body>

<noscript>
<div align='center' id='noscript'><font color='red'><b>ATTENTION !!! JavaScript est désactivé ou non pris en charge par votre navigateur : cette procédure ne fonctionnera pas correctement.</b></font><br>
<b>Pour modifier cette option, voir <a target='_blank' href='https://www.libellules.ch/browser_javascript_activ.php'>ce lien</a>.</b></div><br>
</noscript>

<table width="100%">
<tr>
<td style="text-align: left;"><img alt="CrosHAL" title="CrosHAL" width="250px" src="./img/logo_CrosHAL.png"></td>
<td style="text-align: right;"><img alt="Université de Rennes 1" title="Université de Rennes 1" width="150px" src="./img/logo_UR1_gris_petit.jpg"></td>
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
if (isset($_FILES['CSV_CrosHAL']['name']) && $_FILES['CSV_CrosHAL']['name'] != "") //File has been submitted
{
	if ($_FILES['CSV_CrosHAL']['error'])
	{
		switch ($_FILES['CSV_CrosHAL']['error'])
		{
			 case 1: // UPLOAD_ERR_INI_SIZE
			 Header("Location: "."CSV_CrosHAL.php?erreur=1");
			 break;
			 case 2: // UPLOAD_ERR_FORM_SIZE
			 Header("Location: "."CSV_CrosHAL.php?erreur=2");
			 break;
			 case 3: // UPLOAD_ERR_PARTIAL
			 Header("Location: "."CSV_CrosHAL.php?erreur=3");
			 break;
			 //case 4: // UPLOAD_ERR_NO_FILE
			 //Header("Location: "."OverHAL.php?erreur=4");
			 //break;
		}
	}
	$extension = strrchr($_FILES['CSV_CrosHAL']['name'], '.');
	if ($extension != ".csv") {
		Header("Location: "."CSV_CrosHAL.php?erreur=5");
	}
	$temp = $_FILES['CSV_CrosHAL']['tmp_name'];
	
	$Fnm = "./Stats-overhal-mails-UR1.php";
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
        $chaine .= '"Type"=>"'.$tab[3].'", ';
        $chaine .= '"Fichier"=>"'.$tab[4].'", ';
        $chaine .= '"Langue"=>"'.$tab[5].'", ';
        $chaine .= '"Labo"=>"'.$tab[6].'", ';
        $chaine .= '"Reponse"=>"'.$tab[7].'", ';
        $chaine .= '"Remarques"=>"'.$tab[8].'")';
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
	echo ('<br><b>Le fichier nécessaire au traitement des manuscrits auteurs via OverHAL a été créé avec succès.<br><br>');
	echo ('Vous pouvez fermer cet onglet et lancer votre requête<br><br>');
	}
?>

<?php
include('./bas.php');
?>
</body>
</html>