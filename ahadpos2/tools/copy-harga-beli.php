<?php

/* ------------------------------------------------------------------------------
copy-harga-beli.php

Kasus : di sebuah database AhadPOS, ada banyak barang tanpa hargaBeli / hargaBeli = 0

Solusi : copy isi hargaBeli dari database AhadPOS dari toko lainnya

------------------------------------------------------------------------------ */

$server   = "localhost";
$username = "root";
$password = "";

$db_sumber = "test2";
$db_target = "test3";

// Koneksi dan memilih database di server
mysql_connect($server,$username,$password) or die("Koneksi gagal");
mysql_select_db($db_target) or die("Database tidak bisa dibuka");

$sql	= "SELECT * FROM detail_beli WHERE hargaBeli = 0 GROUP BY barcode";
$hasil1	= mysql_query($sql);
echo "Saat ini ada ".mysql_num_rows($hasil1)." barang yang hargaBeli nya nol.";

$current_barcode = "";
while ($x = mysql_fetch_array($hasil1)) {

	if ($current_barcode <> $x["barcode"]) {

		$current_barcode 	= $x["barcode"];
		// cari hargaBeli nya dari $db_sumber
		mysql_select_db($db_sumber);
		//$sql	= "SELECT hargaBeli FROM detail_beli WHERE barcode = '".$x["barcode"]."'";
		$sql	= "SELECT namaBarang, hargaJual FROM barang WHERE barcode = '".$x["barcode"]."'";
		$hasil2	= mysql_query($sql) or die("Error: ".mysql_error());
		//echo "sql: $sql \n";

		// cari average hargaBeli nya
		//$n = 0; $total = 0; $avgHargaBeli = 0;
		//while ($z = mysql_fetch_array($hasil2)) { 
		//	$n++;
		//	$total	= $total + $z["hargaBeli"];
		//};
		//if ($n > 0) {	$avgHargaBeli	= $total / $n;	};

		//if ($avgHargaBeli == 0) { //  jika tidak ada datanya dari $db_sumber -- cari dari record lainnya di $db_target
		//	mysql_select_db($db_target);
		//	$sql	= " SELECT hargaBeli FROM detail_beli WHERE barcode = '".$x["barcode"]."' AND hargaBeli > 0";
		//	$hasil3	= mysql_query($sql);
		//	if ($z = mysql_fetch_array($hasil3)) {
		//		$avgHargaBeli = $z["hargaBeli"];
		//	};
		//};

		//echo "avgHargaBeli ".$x["barcode"]." : 	$avgHargaBeli \n";

		// apakah ada ditemukan ?
		$avgHargaBeli = 0;
		if ($z = mysql_fetch_array($hasil2)) {
			$avgHargaBeli = $z["hargaJual"];
		};

		// simpan hargaBeli ke $db_target
		if ($avgHargaBeli > 0) {
			mysql_select_db($db_target);
			$sql	= "UPDATE detail_beli SET hargaBeli = $avgHargaBeli WHERE barcode = '".$x["barcode"]."'";
			$hasil3	= mysql_query($sql);
			echo "Update hargaBeli untuk ".$x["barcode"]." / ".$z["namaBarang"].": $avgHargaBeli \n\n";
		}; // if ($avgHargaBeli > 0) {


	} else {
		// skip jika masih barcode yang sama

	};// if ($current_barcode <> $x[barcode]) {

}; // while ($x = mysql_fetch_array($hasil)) {

// SELESAI

?>
