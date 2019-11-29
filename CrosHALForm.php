<?php
header('Content-type: text/html; charset=UTF-8');
require_once('./CAS_connect.php');
if (isset($_GET['css']) && ($_GET['css'] != ""))
{
  $css = $_GET['css'];
}else{
  $css = "https://ecobio.univ-rennes1.fr/HAL_SCD.css";
}
$action = $_GET['action'];
$id = $_GET['Id'];
$form = "CrosHALModif.php?action=".$action."&amp;Id=".$id;
?>
<html>
<head>
  <title>CrosHAL</title>
  <meta name="Description" content="CrosHAL">
  <link rel="stylesheet" href="<?php echo $css ;?>" type="text/css">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="icon" type="type/ico" href="HAL_favicon.ico">
  <link rel="stylesheet" href="./CrosHAL.css">
</head>
<body>
Bonjour <b><?php echo phpCAS::getUser();?></b>,<br>
La procédure de modification CrosHAL des notices n'étant pas pour l'instant complètement liée à l'authentification CAS du CCSD, nous avons besoin que vous resaisissiez le mot de passe de votre compte administrateur HAL.
<form action="<?php echo $form; ?>" method="post">
<input type ="password" name="password">
<input type="submit" value="Envoyer">
</form>
</body>
</html>