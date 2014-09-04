<?php

/* fast-SO.php ------------------------------------------------------
   	version: 1.01

	Part of AhadPOS : http://ahadpos.com
	License: GPL v2
			http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
			http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License v2 (links provided above) for more details.
----------------------------------------------------------------*/
	
include "../../config/config.php";

$username = 'test';


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
<title>Fast Stock Opname - Ahad Mart</title>
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

// simpan data Fast SO
if ($_GET["jmlbarang"]) { // ================================================================================

	if (empty($_GET["jumlahtercatat"])) { $_GET["jumlahtercatat"] = 0; }

	// komplain jika salah input angka barcode ke jumlahTercatat
	if ($_GET["jmlbarang"] > 2000) {
		echo "Salah : Input Barcode sebagai Jumlah Barang. <br />
			<a href='".$_SERVER["PHP_SELF"]."?nomorrak=".$_GET["nomorrak"]."&username=".$_GET["username"]."'>
			Klik disini untuk mengulang kembali</a>";
		exit;
	};
	
	$selisih 	= ($_GET["jmlbarang"] - $_GET["jumlahtercatat"]);

	// apakah sudah ada barcode yang sama di tanggal yang sama ? 
	// jika ya, berarti ini adalah barang di gudang
	// hitung selisihnya, dg memperhitungkan selisih yg sebelumnya
	$sql 	= "SELECT f.uid, b.jumBarang, f.jmlTercatat, f.selisih FROM fast_stock_opname AS f, barang AS b 
			WHERE f.barcode='".$_GET["barcode1"]."' AND f.tanggalSO='".date("Y-m-d")."' AND f.barcode=b.barcode 
			ORDER BY f.uid DESC LIMIT 1";
	//echo $sql;
	$hasil 	= mysql_query($sql);
	if (mysql_num_rows($hasil) > 0) { 
		$x	= mysql_fetch_array($hasil);
		//var_dump($x);
		$jumBarangLama 	= $x[jumBarang] + $x[selisih]; 
		$selisih =  $_GET["jmlbarang"];
		$_GET["jumlahtercatat"] = $jumBarangLama;
		//echo "selisih: $selisih";
	};
	


	// cari nama barang ybs
	// simpan di database
	$sql = "INSERT INTO fast_stock_opname (barcode, idRak, jmlTercatat, selisih, tanggalSO, username, namaBarang) 
		VALUES ('".$_GET["barcode1"]."',".$_GET["nomorrak"].",".$_GET["jumlahtercatat"].",
			".$selisih.",'".date("Y-m-d")."', '".$_GET["username"]."',
			'".$_GET["namaBarang"]."')";
	$hasil	= mysql_query($sql);

	echo "<h3> Terimakasih, jumlah stok sudah dicatat di komputer</h3>";

}

// minta jumlah barang
if ($_GET["barcode"]) { // ===================================================================================

	$sql 	= "SELECT jumBarang, namaBarang FROM barang WHERE barcode='".$_GET["barcode"]."'";
	$hasil 	= mysql_query($sql);
	$x	= mysql_fetch_array($hasil);

	if(is_array($x)) { 
		$jumlahtercatat = $x[jumBarang];
		$namaBarang	= $x[namaBarang]; 
	} 

?>

<br /><br />
<?php echo "<b>".$namaBarang."</b> ( <a href='".$_SERVER["PHP_SELF"]."?nomorrak=".$_GET["nomorrak"]."&username=".$_GET["username"]."'>
			klik disini untuk membatalkan</a> karena beda barang)"; ?> 
<br /><br />

<h2> Masukkan jumlah barang saat ini </h2>

<input type="text" id="bacadisini"	name="jmlbarang" /><br />
<input type="submit" name=submit />


<input type="hidden" name="barcode1" value="<?php echo $_GET["barcode"]; ?>" />
<input type="hidden" name="nomorrak" value="<?php echo $_GET["nomorrak"]; ?>" />
<input type="hidden" name="username" value="<?php echo $_GET["username"]; ?>" />

<input type="hidden" name="jumlahtercatat" 	value="<?php echo $jumlahtercatat; ?>" />
<input type="hidden" name="namaBarang" 		value="<?php echo $namaBarang; ?>" />


<?php 

// minta barcode barang
} elseif ($_GET["nomorrak"]) {  // ===================================================================

?>


<br /><br />

<h2> Masukkan barcode barang </h2>

<input type="text" id="bacadisini" name="barcode"/><br />
<input type="submit" name=submit />

<input type="hidden" name="nomorrak" value="<?php echo $_GET["nomorrak"]; ?>" />
<input type="hidden" name="username" value="<?php echo $_GET["username"]; ?>" />

<?php

} else { //  --------------------------------------------------------------------------------------

?>

<br /><br />

<h2> Masukkan nomor Rak </h2>

<input type="text" id="bacadisini" name="nomorrak" /> <br />
<input type="submit" name=submit />

<input type="hidden" name="username" value="<?php echo $username; ?>" />


<?php

}

?>


</form>

<p id="writeroot"></p>

</body></html><?php







/* CHANGELOG -----------------------------------------------------------

 1.0.1 / 2011-01-06 : Harry Sufehmi		: initial release

------------------------------------------------------------------------ */


?>
