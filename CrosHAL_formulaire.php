<?php
//Formulaire
//$urlServeur = "";
//if (isset($_POST["verifDOI"])) {
if (!isset($_GET["noliene"])) {$noliene = "";}
if (isset($_POST["valider"]) || isset($_POST["suite"]) || isset($_POST["retour"])) {
  $team = htmlspecialchars($_POST["team"]);
  $idhal = htmlspecialchars($_POST["idhal"]);
  $anneedeb = htmlspecialchars($_POST["anneedeb"]);
  $anneefin = htmlspecialchars($_POST["anneefin"]);
  if (isset($_POST["apa"]) && $_POST["apa"] == "oui") {$apa = "oui";}else{$apa = "non";}
	if (isset($_POST["ordinv"]) && $_POST["ordinv"] == "oui") {$ordinv = "oui";}else{$ordinv = "non";}
  if (!isset($increment)) {$increment = htmlspecialchars($_POST["increment"]);}
  $opt1 = "non";
  $opt2 = "non";
  $opt3 = "non";
  //option 1
  if (isset($_POST["chkall"]) && $_POST["chkall"] == "oui") {$chkall = htmlspecialchars($_POST["chkall"]);$opt1 = "oui";}else{$chkall = "non";}
  if (isset($_POST["doiCrossRef"]) && $_POST["doiCrossRef"] == "oui") {$doiCrossRef = htmlspecialchars($_POST["doiCrossRef"]);$opt1 = "oui";}else{$doiCrossRef = "non";}
  if (isset($_POST["revue"]) && $_POST["revue"] == "oui") {$revue = htmlspecialchars($_POST["revue"]);$opt1 = "oui";}else{$revue = "non";}
  if (isset($_POST["vnp"]) && $_POST["vnp"] == "oui") {$vnp = htmlspecialchars($_POST["vnp"]);$opt1 = "oui";}else{$vnp = "non";}
  if (isset($_POST["lanCrossRef"]) && $_POST["lanCrossRef"] == "oui") {$lanCrossRef = htmlspecialchars($_POST["lanCrossRef"]);$opt1 = "oui";}else{$lanCrossRef = "non";}
  if (isset($_POST["financement"]) && $_POST["financement"] == "oui") {$financement = htmlspecialchars($_POST["financement"]);$opt1 = "oui";}else{$financement = "non";}
  if (isset($_POST["anr"]) && $_POST["anr"] == "oui") {$anr = htmlspecialchars($_POST["anr"]);$opt1 = "oui";}else{$anr = "non";}
  if (isset($_POST["anneepub"]) && $_POST["anneepub"] == "oui") {$anneepub = htmlspecialchars($_POST["anneepub"]);$opt1 = "oui";}else{$anneepub = "non";}
  if (isset($_POST["mel"]) && $_POST["mel"] == "oui") {$mel = htmlspecialchars($_POST["mel"]);$opt1 = "oui";}else{$mel = "non";}
	
  if (isset($_POST["ccTitconf"]) && $_POST["ccTitconf"] == "oui") {$ccTitconf = htmlspecialchars($_POST["ccTitconf"]);$opt1 = "oui";}else{$ccTitconf = "non";}
	if (isset($_POST["ccPays"]) && $_POST["ccPays"] == "oui") {$ccPays = htmlspecialchars($_POST["ccPays"]);$opt1 = "oui";}else{$ccPays = "non";}
	if (isset($_POST["ccDatedeb"]) && $_POST["ccDatedeb"] == "oui") {$ccDatedeb = htmlspecialchars($_POST["ccDatedeb"]);$opt1 = "oui";}else{$ccDatedeb = "non";}
	if (isset($_POST["ccDatefin"]) && $_POST["ccDatefin"] == "oui") {$ccDatefin = htmlspecialchars($_POST["ccDatefin"]);$opt1 = "oui";}else{$ccDatefin = "non";}
	if (isset($_POST["ccISBN"]) && $_POST["ccISBN"] == "oui") {$ccISBN = htmlspecialchars($_POST["ccISBN"]);$opt1 = "oui";}else{$ccISBN = "non";}
	if (isset($_POST["ccTitchap"]) && $_POST["ccTitchap"] == "oui") {$ccTitchap = htmlspecialchars($_POST["ccTitchap"]);$opt1 = "oui";}else{$ccTitchap = "non";}
	if (isset($_POST["ccTitlivr"]) && $_POST["ccTitlivr"] == "oui") {$ccTitlivr = htmlspecialchars($_POST["ccTitlivr"]);$opt1 = "oui";}else{$ccTitlivr = "non";}
	if (isset($_POST["ccEditcom"]) && $_POST["ccEditcom"] == "oui") {$ccEditcom = htmlspecialchars($_POST["ccEditcom"]);$opt1 = "oui";}else{$ccEditcom = "non";}
	
	//if (isset($_POST["mocCrossRef"]) && $_POST["mocCrossRef"] == "oui") {$mocCrossRef = htmlspecialchars($_POST["mocCrossRef"]);$opt1 = "oui";}else{$mocCrossRef = "non";}
  if (isset($_POST["absPubmed"]) && $_POST["absPubmed"] == "oui") {$absPubmed = htmlspecialchars($_POST["absPubmed"]);$opt1 = "oui";}else{$absPubmed = "non";}
  if (isset($_POST["lanPubmed"]) && $_POST["lanPubmed"] == "oui") {$lanPubmed = htmlspecialchars($_POST["lanPubmed"]);$opt1 = "oui";}else{$lanPubmed = "non";}
  if (isset($_POST["mocPubmed"]) && $_POST["mocPubmed"] == "oui") {$mocPubmed = htmlspecialchars($_POST["mocPubmed"]);$opt1 = "oui";}else{$mocPubmed = "non";}
  if (isset($_POST["pmid"]) && $_POST["pmid"] == "oui") {$pmid = htmlspecialchars($_POST["pmid"]);$opt1 = "oui";}else{$pmid = "non";}
  if (isset($_POST["pmcid"]) && $_POST["pmcid"] == "oui") {$pmcid = htmlspecialchars($_POST["pmcid"]);$opt1 = "oui";}else{$pmcid = "non";}
  
	if (isset($_POST["absISTEX"]) && $_POST["absISTEX"] == "oui") {$absISTEX = htmlspecialchars($_POST["absISTEX"]);$opt1 = "oui";}else{$absISTEX = "non";}
  if (isset($_POST["lanISTEX"]) && $_POST["lanISTEX"] == "oui") {$lanISTEX = htmlspecialchars($_POST["lanISTEX"]);$opt1 = "oui";}else{$lanISTEX = "non";}
  if (isset($_POST["mocISTEX"]) && $_POST["mocISTEX"] == "oui") {$mocISTEX = htmlspecialchars($_POST["mocISTEX"]);$opt1 = "oui";}else{$mocISTEX = "non";}
	if (isset($_POST["DOIComm"]) && $_POST["DOIComm"] == "oui") {$DOIComm = htmlspecialchars($_POST["DOIComm"]);$opt1 = "oui";}else{$DOIComm = "non";}
	if (isset($_POST["PoPeer"]) && $_POST["PoPeer"] == "oui") {$PoPeer = htmlspecialchars($_POST["PoPeer"]);$opt1 = "oui";}else{$PoPeer = "non";}

  //option 2
  if (isset($_POST["ordAut"]) && $_POST["ordAut"] == "oui") {$ordAut = htmlspecialchars($_POST["ordAut"]);$opt2 = "oui";}else{$ordAut = "non";}
  if (isset($_POST["iniPre"]) && $_POST["iniPre"] == "oui") {$iniPre = htmlspecialchars($_POST["iniPre"]);$opt2 = "oui";}else{$iniPre = "non";}
  if (isset($_POST["vIdHAL"]) && $_POST["vIdHAL"] == "oui") {$vIdHAL = htmlspecialchars($_POST["vIdHAL"]);$opt2 = "oui";}else{$vIdHAL = "non";}
  if (isset($_POST["rIdHAL"]) && $_POST["rIdHAL"] == "oui") {$rIdHAL = htmlspecialchars($_POST["rIdHAL"]);$opt2 = "oui";}else{$rIdHAL = "non";}
	if (isset($_POST["ctrTrs"]) && $_POST["ctrTrs"] == "oui") {$ctrTrs = htmlspecialchars($_POST["ctrTrs"]);$opt2 = "oui";}else{$ctrTrs = "non";}
  if (isset($_POST["rIdHALArt"]) && $_POST["rIdHALArt"] == "oui") {$rIdHALArt = htmlspecialchars($_POST["rIdHALArt"]);$opt2 = "oui";}else{$rIdHALArt = "non";}
  if (isset($_POST["rIdHALCom"]) && $_POST["rIdHALCom"] == "oui") {$rIdHALCom = htmlspecialchars($_POST["rIdHALCom"]);$opt2 = "oui";}else{$rIdHALCom = "non";}
  if (isset($_POST["rIdHALCou"]) && $_POST["rIdHALCou"] == "oui") {$rIdHALCou = htmlspecialchars($_POST["rIdHALCou"]);$opt2 = "oui";}else{$rIdHALCou = "non";}
  if (isset($_POST["rIdHALOuv"]) && $_POST["rIdHALOuv"] == "oui") {$rIdHALOuv = htmlspecialchars($_POST["rIdHALOuv"]);$opt2 = "oui";}else{$rIdHALOuv = "non";}
  if (isset($_POST["rIdHALDou"]) && $_POST["rIdHALDou"] == "oui") {$rIdHALDou = htmlspecialchars($_POST["rIdHALDou"]);$opt2 = "oui";}else{$rIdHALDou = "non";}
  if (isset($_POST["rIdHALBre"]) && $_POST["rIdHALBre"] == "oui") {$rIdHALBre = htmlspecialchars($_POST["rIdHALBre"]);$opt2 = "oui";}else{$rIdHALBre = "non";}
  if (isset($_POST["rIdHALRap"]) && $_POST["rIdHALRap"] == "oui") {$rIdHALRap = htmlspecialchars($_POST["rIdHALRap"]);$opt2 = "oui";}else{$rIdHALRap = "non";}
  if (isset($_POST["rIdHALThe"]) && $_POST["rIdHALThe"] == "oui") {$rIdHALThe = htmlspecialchars($_POST["rIdHALThe"]);$opt2 = "oui";}else{$rIdHALThe = "non";}
  if (isset($_POST["rIdHALPre"]) && $_POST["rIdHALPre"] == "oui") {$rIdHALPre = htmlspecialchars($_POST["rIdHALPre"]);$opt2 = "oui";}else{$rIdHALPre = "non";}
  if (isset($_POST["rIdHALPub"]) && $_POST["rIdHALPub"] == "oui") {$rIdHALPub = htmlspecialchars($_POST["rIdHALPub"]);$opt2 = "oui";}else{$rIdHALPub = "non";}
  //option 3
  if (isset($_POST["manuaut"]) && $_POST["manuaut"] == "oui") {$manuaut = htmlspecialchars($_POST["manuaut"]);$opt3 = "oui";}else{$manuaut = "non";}
  if (isset($_POST["lienext"]) && $_POST["lienext"] == "oui") {$lienext = htmlspecialchars($_POST["lienext"]);$opt3 = "oui";}else{$lienext = "non";}
  if (isset($_POST["noliene"]) && $_POST["noliene"] == "oui") {$noliene = htmlspecialchars($_POST["noliene"]);$opt3 = "oui";}else{$noliene = "non";}
	if (isset($_POST["manuautOH"]) && $_POST["manuautOH"] == "oui") {$manuautOH = htmlspecialchars($_POST["manuautOH"]);$opt3 = "oui";}else{$manuautOH = "non";}
  if (isset($_POST["manuautNR"]) && $_POST["manuautNR"] == "oui") {$manuautNR = htmlspecialchars($_POST["manuautNR"]);$opt3 = "oui";}else{$manuautNR = "non";}
	$embargo = "";
  if (isset($_POST["embargo"]) && $_POST["embargo"] == "6mois") {$embargo = "6mois";$opt3 = "oui";}
  if (isset($_POST["embargo"]) && $_POST["embargo"] == "12mois") {$embargo = "12mois";$opt3 = "oui";}
  if (isset($_POST["urlServeur"])) {$urlServeur = htmlspecialchars($_POST["urlServeur"]);}
	$iMin = 0;
  if (isset($_POST["valider"]) || isset($_POST["suite"])) {
    if (isset($_POST["iMin"])) {$iMin = htmlspecialchars($_POST["iMin"]);}
    if (isset($_POST["iMax"])) {$iMax = htmlspecialchars($_POST["iMax"]);}
  }
  if (isset($_POST["retour"])) {
    $iMin = htmlspecialchars($_POST["iMinRet"]);
    $iMax = htmlspecialchars($_POST["iMaxRet"]);
  }
}
if (!isset($_POST["valider"]) && !isset($_POST["apa"])) {
	$apa = "oui";
}
if (isset($opt1) && $opt1 == "oui" && $increment >= 10) {$increment = 10;}
if (isset($_POST["valider"])) {
  $iMax = $iMin + $increment - 1;
  $iMinRet = $iMin;
  $iMaxRet = $iMax;
}
if (isset($team) && $team != "") {$team1 = $team; $team2 = $team;}else{$team1 = "Entrez le code de votre collection"; $team2 = "";}
?>
<input type="text" id="team" class="form-control" style="height: 25px; width: 300px;" name="team" value="<?php echo $team1;?>" onClick="this.value='<?php echo $team2;?>';" onkeydown="document.getElementById('idhal').value = '';">
<h2><strong><u>ou</u></strong></h2>
<p class="form-inline"><strong><label for="idhal">Identifiant alphabétique auteur HAL</label></strong> <em>(IdHAL > olivier-troccaz, par exemple)</em> :
<input type="text" id="idhal" name="idhal" class="form-control" style="height: 25px; width: 300px" value="<?php echo $idhal;?>" onkeydown="document.getElementById('team').value = '';">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" rel="noopener noreferrer" href="https://hal.archives-ouvertes.fr/page/mon-idhal">Créer mon IdHAL</a>
<br><br><table aria-describedby="Période">
<tr><th scope="col" valign="top">Période :&nbsp;</th>
<th scope="col" >
<p class="form-inline">
<label for="anneedeb">Depuis</label>
<select id="anneedeb" class="form-control" style="height: 25px; width: 60px; padding: 0px;" name="anneedeb">
<?php
$moisactuel = date('n', time());
if ($moisactuel >= 10) {$i = date('Y', time())+1;}else{$i = date('Y', time());}
while ($i >= date('Y', time()) - 30) {
  if (isset($anneedeb) && $anneedeb == $i) {$txt = "selected";}else{$txt = "";}
  echo '<option value='.$i.' '.$txt.'>'.$i.'</option>' ;
  $i--;
}
?>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label for="anneefin">Jusqu'à</label>
<select id="anneefin" class="form-control" style="height: 25px; width: 60px; padding: 0px;" name="anneefin">
<?php
$moisactuel = date('n', time());
if ($moisactuel >= 10) {$i = date('Y', time())+1;}else{$i = date('Y', time());}
while ($i >= date('Y', time()) - 30) {
  if (isset($anneefin) && $anneefin == $i) {$txt = "selected";}else{$txt = "";}
  echo '<option value='.$i.' '.$txt.'>'.$i.'</option>';
  $i--;
}
?>
</select></th></tr></table>
<?php
if (isset($apa) && $apa == "oui") {$pap = " checked";}else{$pap = "";}
if (isset($ordinv) && $ordinv == "oui") {$ordi = " checked";}else{$ordi = "";}
?>
<p class="form-inline">
<input type="checkbox" id="apa" class="form-control" style="height: 15px;" name="apa" value="oui"<?php echo $pap;?>> <label for="apa">Inclure les articles <em>"A paraître"</em></label><br/>
<input type="checkbox" id="ordinv" class="form-control" style="height: 15px;" name="ordinv" value="oui"<?php echo $ordi;?>> <label for="ordinv">Traiter les notices dans l'ordre inverse de recherche</label><br/>
<br>
<label for="increment">Incrément :</label>
<select class="form-control" id="increment" style="height: 25px; padding: 0px;" name="increment">
<?php
if (isset($increment) && $increment == 1) {$uni = "selected";}else{$uni = "";}
if ((isset($increment) && $increment == 10) || ($team2 == "" && $idhal == "")) {$dix = "selected";}else{$dix = "";}
if (isset($increment) && $increment == 20) {$vgt = "selected";}else{$vgt = "";}
if (isset($increment) && $increment == 50) {$cqt = "selected";}else{$cqt = "";}
if (isset($increment) && $increment == 100) {$cen = "selected";}else{$cen = "";}
if (isset($increment) && $increment == 200) {$dcn = "selected";}else{$dcn = "";}
?>
<option value="1" <?php echo $uni;?>>1</option>
<option value="10" <?php echo $dix;?>>10</option>
<option value="20" <?php echo $vgt;?>>20</option>
<option value="50" <?php echo $cqt;?>>50</option>
<option value="100" <?php echo $cen;?>>100</option>
<option value="200" <?php echo $dcn;?>>200</option>
</select>
<span class='red'>-> Cette valeur correspond au pas des requêtes envoyées vers Crossref. Plus elle sera élevée et plus le risque de blocage de votre poste sera important. Par précaution, elle est volontairement forcée à un maximum de 10 pour l'étape 1.</span>
<br><br>
<?php
if (isset($chkall) && $chkall == "oui") {$cka = " checked";}else{$cka = "";}
?>
<strong>Etape 1 : Compléter et corriger les métadonnées HAL</strong> <input type="checkbox" id="chkall" class="form-control" style="height: 15px;" onclick="chkall1()" name="chkall" value="oui"<?php echo $cka;?>>&nbsp;<label for="chkall">Cocher tout (Articles - Pubmed prioritaire)</label><br>
<?php
if (isset($doiCrossRef) && $doiCrossRef == "oui") {$iod = " checked";}else{$iod = "";}
if (isset($revue) && $revue == "oui") {$rev = " checked";}else{$rev = "";}
if (isset($vnp) && $vnp == "oui") {$pnv = " checked";}else{$pnv = "";}
if (isset($lanCrossRef) && $lanCrossRef == "oui") {$lanC = " checked";}else{$lanC = "";}
if (isset($financement) && $financement == "oui") {$fin = " checked";}else{$fin = "";}
if (isset($anr) && $anr == "oui") {$tan = " checked";}else{$tan = "";}
if (isset($anneepub) && $anneepub == "oui") {$apu = " checked";}else{$apu = "";}
if (isset($mel) && $mel == "oui") {$lem = " checked";}else{$lem = "";}

if (isset($ccTitconf) && $ccTitconf == "oui") {$tco = " checked";}else{$tco = "";}
if (isset($ccPays) && $ccPays == "oui") {$pay = " checked";}else{$pay = "";}
if (isset($ccDatedeb) && $ccDatedeb == "oui") {$ddb = " checked";}else{$ddb = "";}
if (isset($ccDatefin) && $ccDatefin == "oui") {$dfn = " checked";}else{$dfn = "";}
if (isset($ccISBN) && $ccISBN == "oui") {$isb = " checked";}else{$isb = "";}
if (isset($ccTitchap) && $ccTitchap == "oui") {$tch = " checked";}else{$tch = "";}
if (isset($ccTitlivr) && $ccTitlivr == "oui") {$tli = " checked";}else{$tli = "";}
if (isset($ccEditcom) && $ccEditcom == "oui") {$edc = " checked";}else{$edc = "";}

if (isset($mocCrossRef) && $mocCrossRef == "oui") {$mocC = " checked";}else{$mocC = "";}
if (isset($absPubmed) && $absPubmed == "oui") {$absP = " checked";}else{$absP = "";}
if (isset($lanPubmed) && $lanPubmed == "oui") {$lanP = " checked";}else{$lanP = "";}
if (isset($mocPubmed) && $mocPubmed == "oui") {$mocP = " checked";}else{$mocP = "";}
if (isset($pmid) && $pmid == "oui") {$pmi = " checked";}else{$pmi = "";}

if (isset($pmcid) && $pmcid == "oui") {$pmc = " checked";}else{$pmc = "";}
if (isset($absISTEX) && $absISTEX == "oui") {$absI = " checked";}else{$absI = "";}
if (isset($lanISTEX) && $lanISTEX == "oui") {$lanI = " checked";}else{$lanI = "";}
if (isset($mocISTEX) && $mocISTEX == "oui") {$mocI = " checked";}else{$mocI = "";}
if (isset($DOIComm) && $DOIComm == "non" || !isset($team)) {$DOICn = " checked";}else{$DOICn = "";}
if (isset($DOIComm) && $DOIComm == "oui") {$DOICo = " checked";}else{$DOICo = "";}
if (isset($PoPeer) && $PoPeer == "oui") {$Popo = " checked";}else{$Popo = "";}
if (isset($PoPeer) && $PoPeer == "non" || !isset($team)) {$Popn = " checked";}else{$Popn = "";}

?>
<p class="form-inline">
Via CrossRef (articles): 
<input type="checkbox" id="chk17" class="form-control" style="height: 15px;" onclick="option1()" name="doiCrossRef" value="oui"<?php echo $iod;?>>&nbsp;<label for="chk17">DOI</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk0" class="form-control" style="height: 15px;" onclick="option1()" name="revue" value="oui"<?php echo $rev;?>>&nbsp;<label for="chk0">Revue</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk1" class="form-control" style="height: 15px;" onclick="option1()" name="vnp" value="oui"<?php echo $pnv;?>>&nbsp;<label for="chk1">Vol/Num/Pag</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk24" class="form-control" style="height: 15px;" onclick="option1()" name="lanCrossRef" value="oui"<?php echo $lanC;?>>&nbsp;<label for="chk24">Langue</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk2" class="form-control" style="height: 15px;" onclick="option1()" name="financement" value="oui"<?php echo $fin;?>>&nbsp;<label for="chk2">Financement</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk3" class="form-control" style="height: 15px;" onclick="option1()" name="anr" value="oui"<?php echo $tan;?>>&nbsp;<label for="chk3">ANR</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk4" class="form-control" style="height: 15px;" onclick="option1()" name="anneepub" value="oui"<?php echo $apu;?>>&nbsp;<label for="chk4">Année de publication</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk5" class="form-control" style="height: 15px;" onclick="option1()" name="mel" value="oui"<?php echo $lem;?>>&nbsp;<label for="chk5">Date de mise en ligne</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!--<input type="checkbox" id="chk6" class="form-control" style="height: 15px;" onclick="option1()" name="mocCrossRef" value="oui"<?php echo $mocC;?>>&nbsp;<label for="chk6">Mots-clés généralistes</label>--><br>
Via CrossRef (conférences et chapitres): 
<input type="checkbox" id="chk39" class="form-control" style="height: 15px;" onclick="option1()" name="ccTitconf" value="oui"<?php echo $tco;?>>&nbsp;<label for="chk39">Titre de la conférence</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk40" class="form-control" style="height: 15px;" onclick="option1()" name="ccPays" value="oui"<?php echo $pay;?>>&nbsp;<label for="chk40">Pays</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk41" class="form-control" style="height: 15px;" onclick="option1()" name="ccDatedeb" value="oui"<?php echo $ddb;?>>&nbsp;<label for="chk41">Date début</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk42" class="form-control" style="height: 15px;" onclick="option1()" name="ccDatefin" value="oui"<?php echo $dfn;?>>&nbsp;<label for="chk42">Date fin</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk43" class="form-control" style="height: 15px;" onclick="option1()" name="ccISBN" value="oui"<?php echo $isb;?>>&nbsp;<label for="chk43">ISBN</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk44" class="form-control" style="height: 15px;" onclick="option1()" name="ccTitchap" value="oui"<?php echo $tch;?>>&nbsp;<label for="chk44">Titre chapitre</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk45" class="form-control" style="height: 15px;" onclick="option1()" name="ccTitlivr" value="oui"<?php echo $tli;?>>&nbsp;<label for="chk45">Titre livre</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk46" class="form-control" style="height: 15px;" onclick="option1()" name="ccEditcom" value="oui"<?php echo $edc;?>>&nbsp;<label for="chk46">Editeur commercial</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<br>
Via Pubmed : 
<input type="checkbox" id="chk11" class="form-control" style="height: 15px;" onclick="option1()" name="absPubmed" value="oui"<?php echo $absP;?>>&nbsp;<label for="chk11">Résumé</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk12" class="form-control" style="height: 15px;" onclick="option1()" name="lanPubmed" value="oui"<?php echo $lanP;?>>&nbsp;<label for="chk12">Langue</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk13" class="form-control" style="height: 15px;" onclick="option1()" name="mocPubmed" value="oui"<?php echo $mocP;?>>&nbsp;<label for="chk13">Mots-clés</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk7" class="form-control" style="height: 15px;" onclick="option1()" name="pmid" value="oui"<?php echo $pmi;?>>&nbsp;<label for="chk7">PMID</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!--<input type="checkbox" id="chk8" class="form-control" style="height: 15px;" onclick="option1()" name="pmcid" disabled="disabled" value="oui"<?php echo $pmc;?>>&nbsp;<label for="chk8">PMCID</label>--><br>
Via ISTEX : 
<input type="checkbox" id="chk14" class="form-control" style="height: 15px;" onclick="option1()" name="absISTEX" value="oui"<?php echo $absI;?>>&nbsp;<label for="chk14">Résumé</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk15" class="form-control" style="height: 15px;" onclick="option1()" name="lanISTEX" value="oui"<?php echo $lanI;?>>&nbsp;<label for="chk15">Langue</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk16" class="form-control" style="height: 15px;" onclick="option1()" name="mocISTEX" value="oui"<?php echo $mocI;?>>&nbsp;<label for="chk16">Mots-clés</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
Vérifier la présence des champs popularLevel_s et peerReviewing_s (articles) :
<input type="radio" id="chk48" class="form-control" style="height: 15px;" onclick="option1()" name="PoPeer" value="oui"<?php echo $Popo;?>>&nbsp;<label for="chk48">Oui</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" id="chk49" class="form-control" style="height: 15px;" onclick="option1()" name="PoPeer" value="non"<?php echo $Popn;?>>&nbsp;<label for="chk49">Non</label><br>
Autoriser l'ajout d'un DOI aux dépôts HAL de type communication : 
<input type="radio" id="chk37" class="form-control" style="height: 15px;" onclick="option1()" name="DOIComm" value="oui"<?php echo $DOICo;?>>&nbsp;<label for="chk37">Oui</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" id="chk38" class="form-control" style="height: 15px;" onclick="option1()" name="DOIComm" value="non"<?php echo $DOICn;?>>&nbsp;<label for="chk38">Non</label><br>
<br>
<br><br>
<strong>Etape 2 : Compléter et corriger les auteurs :</strong><br>
<?php
if (isset($ordAut) && $ordAut == "oui") {$tua = " checked";}else{$tua = "";}
if (isset($iniPre) && $iniPre == "oui") {$erp = " checked";}else{$erp = "";}
if (isset($vIdHAL) && $vIdHAL == "oui") {$idv = " checked";}else{$idv = "";}
if (isset($rIdHAL) && $rIdHAL == "oui") {$idh = " checked";}else{$idh = "";}
if (isset($ctrTrs) && $ctrTrs == "oui") {$ctr = " checked";}else{$ctr = "";}
if (isset($rIdHALArt) && $rIdHALArt == "oui") {$idhart = " checked";}else{$idhart = "";}
if (isset($rIdHALCom) && $rIdHALCom == "oui") {$idhcom = " checked";}else{$idhcom = "";}
if (isset($rIdHALCou) && $rIdHALCou == "oui") {$idhcou = " checked";}else{$idhcou = "";}
if (isset($rIdHALOuv) && $rIdHALOuv == "oui") {$idhouv = " checked";}else{$idhouv = "";}
if (isset($rIdHALDou) && $rIdHALDou == "oui") {$idhdou = " checked";}else{$idhdou = "";}
if (isset($rIdHALBre) && $rIdHALBre == "oui") {$idhbre = " checked";}else{$idhbre = "";}
if (isset($rIdHALRap) && $rIdHALRap == "oui") {$idhrap = " checked";}else{$idhrap = "";}
if (isset($rIdHALThe) && $rIdHALThe == "oui") {$idhthe = " checked";}else{$idhthe = "";}
if (isset($rIdHALPre) && $rIdHALPre == "oui") {$idhpre = " checked";}else{$idhpre = "";}
if (isset($rIdHALPub) && $rIdHALPub == "oui") {$idhpub = " checked";}else{$idhpub = "";}
?>
<input type="checkbox" id="chk18" class="form-control" style="height: 15px;" onclick="option2()" name="ordAut" value="oui"<?php echo $tua;?>>&nbsp;<label for="chk18">Corriger l'ordre des auteurs</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk19" class="form-control" style="height: 15px;" onclick="option2()" name="iniPre" value="oui"<?php echo $erp;?>>&nbsp;<label for="chk19">Remplacer l'initiale du premier prénom par son écriture complète</label><br>
<input type="checkbox" id="chk25" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHAL" value="oui"<?php echo $idh;?>>&nbsp;<label for="chk25">IdHAL :</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk26" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALArt" value="oui"<?php echo $idhart;?>>&nbsp;<label for="chk26">Articles</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk27" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALCom" value="oui"<?php echo $idhcom;?>>&nbsp;<label for="chk27">Communications</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk28" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALCou" value="oui"<?php echo $idhcou;?>>&nbsp;<label for="chk28">Chapitres d'ouvrages</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk29" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALOuv" value="oui"<?php echo $idhouv;?>>&nbsp;<label for="chk29">Ouvrages</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk30" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALDou" value="oui"<?php echo $idhdou;?>>&nbsp;<label for="chk30">Directions d'ouvrages</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk31" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALBre" value="oui"<?php echo $idhbre;?>>&nbsp;<label for="chk31">Brevets</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk32" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALRap" value="oui"<?php echo $idhrap;?>>&nbsp;<label for="chk32">Rapports</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk33" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALThe" value="oui"<?php echo $idhthe;?>>&nbsp;<label for="chk33">Thèses</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk34" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALPre" value="oui"<?php echo $idhpre;?>>&nbsp;<label for="chk34">Preprints</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk35" class="form-control" style="height: 15px;" onclick="option2()" name="rIdHALPub" value="oui"<?php echo $idhpub;?>>&nbsp;<label for="chk35">Autres publications</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<br><label style="padding-left:40px; font-weight:normal; font-style: italic">Cette option permet de rechercher d'éventuels IdHAL auteur absents des notices.</label><br><br>
<input type="checkbox" id="chk36" class="form-control" style="height: 15px;" onclick="option2()" name="vIdHAL" value="oui"<?php echo $idv;?>>&nbsp;<label for="chk36">Repérer les formes IdHAL non valides (en rouge)</label>
<br><label style="padding-left:40px; font-weight:normal; font-style: italic">Pour ce test de repérage, choisissez une période de recherche raisonnable pour limiter le nombre total de notices. L'incrément de recherche n'a aucune incidence puisque toutes les notices comportant au moins un auteur de la collection sont traitées.</label>
<br>
<input type="checkbox" id="chk47" class="form-control" style="height: 15px;" onclick="option2()" name="ctrTrs" value="oui"<?php echo $ctr;?>>&nbsp;<label for="chk47">Contrôle des tiers</label>
<br><br>
<strong>Etape 3 : Déposer le texte intégral des articles :</strong><br>
<?php
if (isset($manuautOH) && $manuautOH == "oui") {$manOH = " checked";}else{$manOH = "";}
if (isset($manuautNR) && $manuautNR == "oui") {$manNR = " checked";}else{$manNR = "";}

if ((isset($lienext) && $lienext == "oui" || !isset($_POST["valider"])) && $noliene != "oui" && $manOH != " checked") {$ext = " checked";}else{$ext = "";}
if (isset($manuaut) && $manuaut == "oui") {$man = " checked";}else{$man = "";}
if (isset($noliene) && $noliene == "oui") {$noe = " checked";}else{$noe = "";}
if (isset($embargo) && $embargo == "6mois") {$m6 = " checked";}else{$m6 = "";}
if (isset($embargo) && $embargo == "12mois") {$m12 = " checked";}else{$m12 = "";}
?>
<strong>Restreindre l'affichage aux notices&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
<input type="checkbox" id="chk20" class="form-control" style="height: 15px;" onclick="option3()" name="lienext" value="oui"<?php echo $ext;?>>&nbsp;<label for="chk20">ayant un lien externe</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="chk21" class="form-control" style="height: 15px;" onclick="option3();affich_form();" name="noliene" value="oui"<?php echo $noe;?>>&nbsp;<label for="chk21">sans lien externe</label><br>
<p class="form-inline" id="embargo" style="display: block;">
<strong>Embargo :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
<input type="radio" id="chk22" class="form-control" style="height: 15px;" onclick="option3()" name="embargo" value="6mois"<?php echo $m6;?>>&nbsp;<label for="chk22">6 mois</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" id="chk23" class="form-control" style="height: 15px;" onclick="option3()" name="embargo" value="12mois"<?php echo $m12;?>>&nbsp;<label for="chk22">12 mois</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;←──────────────┘
</p><p class="form-inline"><br>
<input type="checkbox" id="chk10" class="form-control" style="height: 15px;" onclick="option3()" name="manuaut" value="oui"<?php echo $man;?>>&nbsp;<label for="chk10">Manuscrit auteurs (fichiers sous la forme doi_normalisé.pdf)</label> -> <label for="urlserveur">URL du serveur :</label>
<input type="text" id="urlpdf" class="form-control" style="height: 25px; width: 300px;" name="urlServeur" value="<?php echo $urlServeur;?>" size="30"><span id="urlserveur" style="color:red;"></span><br>
<input type="checkbox" id="chk50" class="form-control" style="height: 15px;" onclick="option3()" name="manuautOH" value="oui"<?php echo $manOH;?>>&nbsp;<label for="chk50">Manuscrit auteurs (via OverHAL)</label> > Au préalable, vous devez procéder au <a target="_blank" href="./CSV_CrosHAL.php">chargement du fichier CSV des statistiques</a><br>
<input type="checkbox" id="chk51" class="form-control" style="height: 15px;" onclick="option3()" name="manuautNR" value="oui"<?php echo $manNR;?>>&nbsp;<label for="chk51">Manuscrit auteurs (via OverHAL) <u>non référencés dans HAL</u></label> > Au préalable, vous devez procéder au <a target="_blank" href="./CSV_CrosHAL.php">chargement du fichier CSV des statistiques</a><br>
<br><br>
<!--<input type="submit" value="Vérifier les DOI" name="verifDOI">-->
<input type="hidden" value="1" name="iMin">
<input type="hidden" value="" name="iMax">
<input type="hidden" value="1" name="iMinRet">
<input type="hidden" value="" name="iMaxRet">
<input type="submit" class="form-control btn btn-md btn-primary" value="Valider" name="valider">
</form>
<script>
if (document.getElementById("chk21").checked == false) {
  document.getElementById("embargo").style.display = "none";
}else{
  document.getElementById("embargo").style.display = "block";
}
</script>
<br>

<!--Fin formulaire-->
