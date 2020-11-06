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

Chargement de votre fichier CSV :
<br><br>
<?php
if (isset($_GET["erreur"]))
{
	$erreur = $_GET["erreur"];
	if ($erreur == 1) {echo("<script type=\"text/javascript\">alert(\"Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !\")</script>");}
	if ($erreur == 2) {echo("<script type=\"text/javascript\">alert(\"Le fichier dépasse la limite autorisée dans le formulaire HTML !\")</script>");}
	if ($erreur == 3) {echo("<script type=\"text/javascript\">alert(\"L'envoi du fichier a été interrompu pendant le transfert !\")</script>");}
	//if ($erreur == 4) {echo("<script type=\"text/javascript\">alert(\"Aucun fichier envoyé ou bien il a une taille nulle !\")</script>");}
	if ($erreur == 5) {echo("<script type=\"text/javascript\">alert(\"Mauvaise extension de fichier !\")</script>");}
	if ($erreur == 6) {echo("<script type=\"text/javascript\">alert(\"Vous devez au préalable fournir votre extraction TEI d'OverHAL !\")</script>");}
	if ($erreur == 7) {echo("<script type=\"text/javascript\">alert(\"Le répertoire de dépôt de fichier est automatiquement nettoyé chaque heure et votre fichier ZIP des extractions TEI d'OverHAL n'existe plus : vous devez procéder de nouveau à son chargement !\")</script>");}
	if ($erreur == 8) {echo("<script type=\"text/javascript\">alert(\"Archive ZIP incorrecte !\")</script>");}
	if ($erreur == 9) {echo("<script type=\"text/javascript\">alert(\"Au moins un des fichiers de votre archive ZIP n'a pas l'extension XML !\")</script>");}
}
?>

<form enctype="multipart/form-data" action="CrossHAL_CSV_upload.php" method="post" accept-charset="UTF-8">
<p class="form-inline">
<label for="CSV_CrossHAL">CSV CrossHAL</label> : <input class="form-control" id="CSV_CrossHAL" style="height: 25px; font-size: 90%; padding: 0px;" name="CSV_CrossHAL" type="file" /><br/>
<input type="submit" class="form-control btn btn-md btn-primary" value="Envoyer">
</form>

<?php
include('./Glob_bas.php');
?>
</body>
</html>