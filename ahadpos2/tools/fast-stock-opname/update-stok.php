<?php
$in_index = false;
$root_path = '/ahadmart/mobile/';
require_once("../lib/common.php");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
<title>Update Stok - Ahad Mart : Pdk Kacang</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">
<!--

document.defaultAction = true;

function init() {
	var x = document.getElementById('testForm').getElementsByTagName('input');
	for (var i=0;i<x.length;i++) {
		x[i].onclick = setEvents;
		if (x[i].checked)
			x[i].onclick();
	}

/*	writeroot = document.getElementById('writeroot');

	document.getElementById('emptyWriteroot').onclick = function () {
		writeroot.innerHTML = '';
		return false;
	}
*/

	// set focus ke textbox
	document.getElementById("bacadisini").focus()

}

function setEvents() {
	if (this.id == 'default') {
		document.defaultAction = !this.checked;
		return;
	}
	var eventHandler = (this.checked) ? detectEvent : empty;
	document['on'+this.id] = eventHandler;
}

function detectEvent(e) {
	var evt = e || window.event;
	//writeData(evt.type);
	
/* ----------- 	*/

	// jika # maka submit form
	if (evt.keyCode == '35') { document.getElementById("testForm").submit(); }

	inputan = document.getElementById("tulisdisini").value

	if (evt.keyCode == '46')  { document.getElementById("tulisdisini").value = inputan + '1'; }
	if (evt.keyCode == '97')  { document.getElementById("tulisdisini").value = inputan + '2'; }
	if (evt.keyCode == '100') { document.getElementById("tulisdisini").value = inputan + '3'; }
	if (evt.keyCode == '103') { document.getElementById("tulisdisini").value = inputan + '4'; }
	if (evt.keyCode == '106') { document.getElementById("tulisdisini").value = inputan + '5'; }
	if (evt.keyCode == '109') { document.getElementById("tulisdisini").value = inputan + '6'; }
	if (evt.keyCode == '112') { document.getElementById("tulisdisini").value = inputan + '7'; }
	if (evt.keyCode == '116') { document.getElementById("tulisdisini").value = inputan + '8'; }
	if (evt.keyCode == '119') { document.getElementById("tulisdisini").value = inputan + '9'; }
	if (evt.keyCode == '32')  { document.getElementById("tulisdisini").value = inputan + '0'; }

	
/*----------- */
	
/* ----------- 
	writeData('keyCode is ' + evt.keyCode);
	writeData('charCode is ' + evt.charCode);
	writeData('');
 ----------- */	
	
	return document.defaultAction;
}

function empty() {
	// nothing
}

var writeroot;

function writeData(msg) {
	//writeroot.innerHTML += msg + '<br />';
	
	//document.getElementById("tulisdisini").value = msg
}




window.onunload = function () {

	if (self.exit)
		exit();
}

window.onload = function () {
	
	if (self.init)
		init();

}

// -->
</script>

</head>

<body>

<form id="testForm" method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
<input type="checkbox" checked id="keypress" /> <label for="keypress">silakan input</label><br />


<?php

//phpinfo();

if ($_GET["jmlbarang"]) { // ------------------------------------------------------------------------

	if (empty($_GET["jumlahtercatat"])) { $_GET["jumlahtercatat"] = 0; }

	//$sql = "UPDATE STOCK SET id_rak=".$_GET["nomorrak"].",jumlah=".$_GET["jmlbarang"]." WHERE kd_barang='".$_GET["barcode1"]."'";
	$sql = "INSERT INTO stock_opname (kd_barang, id_rak, jumlah_tercatat, jumlah_ditemukan, tanggal) VALUES ('".$_GET["barcode1"]."',".$_GET["nomorrak"].",".$_GET["jumlahtercatat"].",".$_GET["jmlbarang"].",'".date("Y-m-d")."')";
	$db->sql_query($sql) or message_die(DB_ERROR, 'SQL Error', __LINE__ , __FILE__ , $sql);
	$rows = $db->sql_fetchrowset();

	echo "<h3> Terimakasih, jumlah stok sudah dicatat di komputer</h3>";

}

if ($_GET["barcode"]) {

	$sql = "SELECT * FROM stock WHERE kd_barang='".$_GET["barcode"]."'";
	$db->sql_query($sql) or message_die(DB_ERROR, 'SQL Error', __LINE__ , __FILE__ , $sql);
	$rows = $db->sql_fetchrowset();
	//var_dump($rows);
	if(is_array($rows)) { $jumlah_barang = $rows[0]['jumlah']; //echo "<h1>jumlah barang ".$rows[0]['jumlah']."</h1>";
	} 

//<input type="text" id="bacadisini"	name="inputan" />

?>

<br /><br />

<h2> Masukkan jumlah barang saat ini </h2>

<input type="text" id="bacadisini"	name="jmlbarang" /><br />
<input type="submit" name=submit />


<input type="hidden" name="barcode1" value="<?php echo $_GET["barcode"]; ?>" />
<input type="hidden" name="nomorrak" value="<?php echo $_GET["nomorrak"]; ?>" />
<input type="hidden" name="jumlahtercatat" value="<?php echo $rows[0]['jumlah']; ?>" />


<?php 

} elseif ($_GET["nomorrak"]) {  // ---------------------------------------------------------------

?>


<br /><br />

<h2> Masukkan barcode barang </h2>

<input type="text" id="bacadisini" name="barcode"/><br />
<input type="submit" name=submit />

<input type="hidden" name="nomorrak" value="<?php echo $_GET["nomorrak"]; ?>" />


<?php

} else { //  --------------------------------------------------------------------------------------

?>

<br /><br />

<h2> Masukkan nomor Rak </h2>

<input type="text" id="bacadisini" name="nomorrak" /> <br />
<input type="submit" name=submit />


<?php

}

?>


</form>

<p id="writeroot"></p>

</body></html>
