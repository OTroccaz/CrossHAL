    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
<?php
/*
 * CrossHAL - Enrichissez vos dépôts HAL - Enrich your HAL repositories
 *
 * Copyright (C) 2023 Olivier Troccaz (olivier.troccaz@cnrs.fr) and Laurent Jonchère (laurent.jonchere@univ-rennes.fr)
 * Released under the terms and conditions of the GNU General Public License (https://www.gnu.org/licenses/gpl-3.0.txt)
 *
 * Chargement de PDF - Loading PDFs
 */
 
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
  <link rel="stylesheet" href="<?php echo $css ;?>" type="text/css">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="icon" type="type/ico" href="HAL_favicon.ico">
  <link rel="stylesheet" href="./CrossHAL.css">
</head>
<body>

<noscript>
<div class='text-primary' id='noscript'><strong>ATTENTION !!! JavaScript est désactivé ou non pris en charge par votre navigateur : cette procédure ne fonctionnera pas correctement.</strong><br>
<strong>Pour modifier cette option, voir <a target='_blank' rel='noopener noreferrer' href='http://www.libellules.ch/browser_javascript_activ.php'>ce lien</a>.</strong></div><br>
</noscript>

<?php
$pdf_file = 0;
if ($_FILES['pdf_file']['error'] != 4)//Is there a pdf file ?
{
  if ($_FILES['pdf_file']['error'])
  {
    switch ($_FILES['pdf_file']['error'])
    {
      case 1: // UPLOAD_ERR_INI_SIZE
        Header("Location: "."CrossHAL.php?erreur=1");
        break;
      case 2: // UPLOAD_ERR_FORM_SIZE
        Header("Location: "."CrossHAL.php?erreur=2");
        break;
      case 3: // UPLOAD_ERR_PARTIAL
        Header("Location: "."CrossHAL.php?erreur=3");
        break;
      default:
        error();
        break;
    }
  }
  $extension = strrchr($_FILES['pdf_file']['name'], '.');
	if ($extension != ".pdf") {
    Header("Location: "."CrossHAL.php?erreur=5");
  }
  $halID = $_POST["halID"];
  if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], "./PDF/".$halID.".pdf")) {
    $pdf_file = 1;
  }else{
    //erreur de chargement du PDF
  }
}

if ($pdf_file == 1) {
$urlPDF = "";
if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false || strpos($_SERVER['HTTP_HOST'], 'ecobio') !== false) {
  $urlPDF = "https://ecobio.univ-rennes1.fr/CrossHAL/PDF/".$halID.".pdf";
}
if (strpos($_SERVER['HTTP_HOST'], 'halur1') !== false) {
  $urlPDF = "https://halur1.univ-rennes1.fr/PDF/".$halID.".pdf";
}

  $url = "&opt3=oui";
  $url .= "&halID=".$_POST["halID"];
	$url .= "&idhal=".$_POST["idhal"];
  $url .= "&iMin=".$_POST["iMin"];
  $url .= "&iMax=".$_POST["iMax"];
  $url .= "&iMinRet=".$_POST["iMinRet"];
  $url .= "&iMaxRet=".$_POST["iMaxRet"];
  $url .= "&increment=".$_POST["increment"];
  $url .= "&team=".$_POST["team"];
  $url .= "&anneedeb=".$_POST["anneedeb"];
  $url .= "&anneefin=".$_POST["anneefin"];
  $url .= "&apa=".$_POST["apa"];
  //$url .= "&pdfedit=".$_POST["pdfedit"];
  if (isset($_POST["manuaut"])) {$url .= "&manuaut=".$_POST["manuaut"];}
	if (isset($_POST["manuautOH"])) {$url .= "&manuautOH=".$_POST["manuautOH"];}
  $url .= "&lienext=".$_POST["lienext"];
  $url .= "&noliene=".$_POST["noliene"];
  $url .= "&embargo=".$_POST["embargo"];
	$url .= "&urlServeur=".$_POST["urlServeur"];
  $url .= "&urlPDF3=".$urlPDF;
  $url .= "&cptTab=".$_POST["cptTab"];
  $url .= "&action=3";
  header('Location: '.'./CrossHAL.php?'.$url);
}
?>

</body>
</html>