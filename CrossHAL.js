//document.getElementById("embargo").style.display = "none";

function affich_form() {
  document.getElementById("embargo").style.display = "block";
}

function option1() {
  document.getElementById("chkall").checked = false;
  //document.getElementById("chk9").checked = false;
  document.getElementById("chk10").checked = false;
  document.getElementById("urlpdf").value = "";
  document.getElementById('chk13').onchange = function() {
    //document.getElementById("chk13").checked = true;
    document.getElementById("chk16").checked = false;
  };
  document.getElementById('chk16').onchange = function() {
    //document.getElementById("chk16").checked = true;
    document.getElementById("chk13").checked = false;
  };
  document.getElementById('chk12').onchange = function() {
    document.getElementById("chk15").checked = false;
    document.getElementById("chk24").checked = false;
  };
  document.getElementById('chk15').onchange = function() {
    document.getElementById("chk12").checked = false;
    document.getElementById("chk24").checked = false;
  };
  document.getElementById('chk24').onchange = function() {
    document.getElementById("chk12").checked = false;
    document.getElementById("chk15").checked = false;
  };
  document.getElementById("chk18").checked = false;
  document.getElementById("chk19").checked = false;
  document.getElementById("chk20").checked = false;
  document.getElementById("chk21").checked = false;
  document.getElementById("chk22").checked = false;
  document.getElementById("chk23").checked = false;
  document.getElementById("chk25").checked = false;
  document.getElementById("chk26").checked = false;
  document.getElementById("chk27").checked = false;
  document.getElementById("chk28").checked = false;
  document.getElementById("chk29").checked = false;
  document.getElementById("chk30").checked = false;
  document.getElementById("chk31").checked = false;
  document.getElementById("chk32").checked = false;
  document.getElementById("chk33").checked = false;
  document.getElementById("chk34").checked = false;
  document.getElementById("chk35").checked = false;
  document.getElementById("chk36").checked = false;
	document.getElementById("chk47").checked = false;
	document.getElementById("chk50").checked = false;
	document.getElementById("chk51").checked = false;
  document.getElementById("embargo").style.display = "none";
	//Si un des éléments CrossRef (cc) est coché > en plus de décocher ceux des option 2 et 3, décocher tous les autres de l'option 1
	for (let ichk = 39; ichk < 51; ichk++) {
		if (ichk != 48 && $ichk != 49) {
			document.getElementById('chk'+ichk).onchange = function() {
				document.getElementById("chk0").checked = false;
				document.getElementById("chk1").checked = false;
				document.getElementById("chk2").checked = false;
				document.getElementById("chk3").checked = false;
				document.getElementById("chk4").checked = false;
				document.getElementById("chk5").checked = false;
				//document.getElementById("chk6").checked = false;
				document.getElementById("chk7").checked = false;
				//document.getElementById("chk8").checked = false;
				//document.getElementById("chk9").checked = false;
				for (let jchk = 10; jchk < 25; jchk++) {
					document.getElementById('chk'+jchk).checked = false;
				}
			}
		}
	}
	//Si un des éléments autre que CrossRef (cc) est coché > en plus de décocher ceux des option 2 et 3, décocher tous ceux de CrossRef (cc)
	for (let ichk = 0; ichk < 25; ichk++) {
		//if (ichk != 6 && ichk != 8 && ichk != 9 && ichk != 18 && ichk != 19 && ichk != 20 && ichk != 21 && ichk != 22 && ichk != 23) {
			if (ichk != 6 && ichk != 8 && ichk != 9 && ichk != 18 && ichk != 19) {
			document.getElementById('chk'+ichk).onchange = function() {
				for (let jchk = 39; jchk < 52; jchk++) {
					document.getElementById('chk'+jchk).checked = false;
				}
			}
		}
	}
}

function option2() {
  document.getElementById("chkall").checked = false;
  document.getElementById("chk0").checked = false;
  document.getElementById("chk1").checked = false;
  document.getElementById("chk2").checked = false;
  document.getElementById("chk3").checked = false;
  document.getElementById("chk4").checked = false;
  document.getElementById("chk5").checked = false;
  //document.getElementById("chk6").checked = false;
  document.getElementById("chk7").checked = false;
  //document.getElementById("chk8").checked = false;
  //document.getElementById("chk9").checked = false;
  document.getElementById("chk10").checked = false;
  document.getElementById("chk11").checked = false;
  document.getElementById("chk12").checked = false;
  document.getElementById("chk13").checked = false;
  document.getElementById("chk14").checked = false;
  document.getElementById("chk15").checked = false;
  document.getElementById("chk16").checked = false;
  document.getElementById("chk17").checked = false;
  document.getElementById("chk20").checked = false;
  document.getElementById("chk21").checked = false;
  document.getElementById("chk22").checked = false;
  document.getElementById("chk23").checked = false;
  document.getElementById("chk24").checked = false;
  document.getElementById("embargo").style.display = "none";
	document.getElementById("chk39").checked = false;
	document.getElementById("chk40").checked = false;
	document.getElementById("chk41").checked = false;
	document.getElementById("chk42").checked = false;
	document.getElementById("chk43").checked = false;
	document.getElementById("chk44").checked = false;
	document.getElementById("chk45").checked = false;
	document.getElementById("chk46").checked = false;
	document.getElementById("chk48").checked = false;
	document.getElementById("chk49").checked = false;
	document.getElementById("chk50").checked = false;
	document.getElementById("chk51").checked = false;
  document.getElementById('chk18').onchange = function() {
    document.getElementById("chk36").checked = false;
    for (let ichk = 25; ichk < 51; ichk++) {
      document.getElementById('chk'+ichk).checked = false;
    }
  };
  document.getElementById('chk19').onchange = function() {
    document.getElementById("chk36").checked = false;
    for (let ichk = 25; ichk < 51; ichk++) {
      document.getElementById('chk'+ichk).checked = false;
    }
  };
  document.getElementById('chk25').onchange = function() {
    document.getElementById("chk18").checked = false;
    document.getElementById("chk19").checked = false;
    document.getElementById("chk36").checked = false;
    if (document.getElementById("chk25").checked == true) {
      for (let ichk = 26; ichk < 36; ichk++) {
        document.getElementById('chk'+ichk).checked = true;
      }
    }else{
      for (let ichk = 26; ichk < 36; ichk++) {
        document.getElementById('chk'+ichk).checked = false;
      }
    }
  };
  document.getElementById('chk36').onchange = function() {
    document.getElementById("chk18").checked = false;
    document.getElementById("chk19").checked = false;
    for (let ichk = 25; ichk < 52; ichk++) {
      document.getElementById('chk'+ichk).checked = false;
    }
  };
	document.getElementById('chk47').onchange = function() {
    document.getElementById("chk18").checked = false;
    document.getElementById("chk19").checked = false;
    for (let ichk = 25; ichk < 52; ichk++) {
      document.getElementById('chk'+ichk).checked = false;
    }
  };
  
  for (let ichk = 26; ichk < 52; ichk++) {
    document.getElementById('chk'+ichk).onchange = function() {
      document.getElementById("chk18").checked = false;
      document.getElementById("chk19").checked = false;
      if (document.getElementById("chk26").checked == true || document.getElementById("chk27").checked == true || document.getElementById("chk28").checked == true || document.getElementById("chk29").checked == true || document.getElementById("chk30").checked == true || document.getElementById("chk31").checked == true || document.getElementById("chk32").checked == true || document.getElementById("chk33").checked == true || document.getElementById("chk34").checked == true || document.getElementById("chk35").checked == true) {
        document.getElementById("chk25").checked = true;
      }else{
        document.getElementById("chk25").checked = false;
      }
    };
  }
}

function option3() {
  document.getElementById("chkall").checked = false;
  document.getElementById("chk0").checked = false;
  document.getElementById("chk1").checked = false;
  document.getElementById("chk2").checked = false;
  document.getElementById("chk3").checked = false;
  document.getElementById("chk4").checked = false;
  document.getElementById("chk5").checked = false;
  //document.getElementById("chk6").checked = false;
  document.getElementById("chk11").checked = false;
  document.getElementById("chk7").checked = false;
  //document.getElementById("chk8").checked = false;
  document.getElementById("chk12").checked = false;
  document.getElementById("chk13").checked = false;
  document.getElementById("chk14").checked = false;
  document.getElementById("chk15").checked = false;
  document.getElementById("chk16").checked = false;
  document.getElementById("chk17").checked = false;
  document.getElementById("chk18").checked = false;
  document.getElementById("chk19").checked = false;
  document.getElementById("chk24").checked = false;
  document.getElementById("chk25").checked = false;
  document.getElementById("chk26").checked = false;
  document.getElementById("chk27").checked = false;
  document.getElementById("chk28").checked = false;
  document.getElementById("chk36").checked = false;
	document.getElementById("chk39").checked = false;
	document.getElementById("chk40").checked = false;
	document.getElementById("chk41").checked = false;
	document.getElementById("chk42").checked = false;
	document.getElementById("chk43").checked = false;
	document.getElementById("chk44").checked = false;
	document.getElementById("chk45").checked = false;
	document.getElementById("chk46").checked = false;
	document.getElementById("chk47").checked = false;
	document.getElementById("chk48").checked = false;
	document.getElementById("chk49").checked = false;
  document.getElementById('chk20').onchange = function() {
    document.getElementById("chk21").checked = false;
		document.getElementById("chk50").checked = false;
		document.getElementById("chk51").checked = false;
    if (document.getElementById("chk21").checked == false) {
      document.getElementById("embargo").style.display = "none";
    }
  };
  document.getElementById('chk21').onchange = function() {
    document.getElementById("chk20").checked = false;
		document.getElementById("chk50").checked = false;
		document.getElementById("chk51").checked = false;
    if (document.getElementById("chk21").checked == false) {
      document.getElementById("embargo").style.display = "none";
    }
  };
	document.getElementById('chk50').onchange = function() {
		if (document.getElementById("chk50").checked == true) {
			document.getElementById("chk20").checked = false;
			document.getElementById("chk21").checked = false;
			document.getElementById("chk10").checked = false;
			document.getElementById("chk51").checked = false;
			document.getElementById("embargo").style.display = "none";
		}
	}
	document.getElementById('chk51').onchange = function() {
		if (document.getElementById("chk51").checked == true) {
			document.getElementById("chk20").checked = false;
			document.getElementById("chk21").checked = false;
			document.getElementById("chk10").checked = false;
			document.getElementById("chk50").checked = false;
			document.getElementById("embargo").style.display = "none";
		}
	}
	document.getElementById('chk10').onchange = function() {
		if (document.getElementById("chk10").checked == true) {
			document.getElementById("chk50").checked = false;
			document.getElementById("chk51").checked = false;
		}
	}
}

function chkall1() {
  document.getElementById("chk0").checked = true;
  document.getElementById("chk1").checked = true;
  document.getElementById("chk2").checked = true;
  document.getElementById("chk3").checked = true;
  document.getElementById("chk4").checked = true;
  document.getElementById("chk5").checked = true;
  //document.getElementById("chk6").checked = true;
  document.getElementById("chk7").checked = true;
  //document.getElementById("chk8").checked = false;
  //document.getElementById("chk9").checked = false;
  document.getElementById("chk10").checked = false;
  document.getElementById("chk11").checked = true;
  document.getElementById("chk12").checked = true;
  document.getElementById("chk13").checked = true;
  document.getElementById("chk14").checked = true;
  document.getElementById("chk15").checked = false;
  document.getElementById("chk16").checked = false;
  document.getElementById("chk17").checked = true;
  document.getElementById("chk18").checked = false;
  document.getElementById("chk19").checked = false;
  document.getElementById("chk20").checked = false;
  document.getElementById("chk21").checked = false;
  document.getElementById("chk22").checked = false;
  document.getElementById("chk23").checked = false;
  document.getElementById("chk24").checked = true;
	document.getElementById("chk36").checked = false;
	document.getElementById("chk39").checked = false;
	document.getElementById("chk40").checked = false;
	document.getElementById("chk41").checked = false;
	document.getElementById("chk42").checked = false;
	document.getElementById("chk43").checked = false;
	document.getElementById("chk44").checked = false;
	document.getElementById("chk45").checked = false;
	document.getElementById("chk46").checked = false;
	document.getElementById("chk47").checked = false;
	document.getElementById("chk50").checked = false;
	document.getElementById("chk51").checked = false;
}

function verif() {
  if (document.getElementById("chk10").checked == true && document.getElementById("urlpdf").value == "") {
    //alert("Vous devez spécifier l'URL du serveur");
    document.getElementById("urlserveur").innerHTML = " <b>Vous devez spécifier l'URL du serveur</b>";
    document.getElementById("urlpdf").focus();
    return false;
  }
}

function formFilePDF() {
  var cont = '<form enctype="multipart/form-data" action="CrosHALPDF.php" method="post" accept-charset="UTF-8">';
  cont += '<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />';
  cont += 'Envoyez le fichier PDF (10 Mo maximum) :<br />';
  cont += '<input name="pdf_file" type="file" /><br />';
  cont += '<input type="hidden" name="halID" value="<?php echo $halID; ?>">';
  cont += '<input type="submit" value="Envoyer le fichier" />';
  cont += '</form>';
  document.getElementById("formFilePDF").innerHTML = cont;
}

function majok(halID) {
  document.getElementById("maj"+halID).innerHTML = "<img src='./img/MAJOK.png'>";
}

function majokVu(halID) {
  document.getElementById("Vu"+halID).innerHTML = "<img src='./img/MAJOK.png'>";
}

function condActOk(halID, lienPDF, action) {
  document.getElementById("maj"+halID).innerHTML = "<a target='_blank' href='"+lienPDF+"' onclick='$.post(\"CrosHAL_liste_actions.php\", { halID: \""+halID+"\", action: \""+action+"\" });majok(\""+halID+"\");'><img alt='MAJ' src='./img/MAJ.png'></a>";
}
