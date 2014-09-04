<?php
        
        include "../config/config.php";

	// GLOBAL VARIABLES
	$temporary_barcode = 2323232323;		// barcode prefix for items with non-unique barcode
	$sdb = 'ahadmart'; 			// source database
	$tdb = 'ahadpos_pdkkacang'; 		// target database

// ====START CODE==================================================================================================================

	// konek ke source & target database
	$sourcedb = mysql_connect($server,$username,$password, true) or die("Koneksi gagal");
	mysql_select_db($sdb, $sourcedb);

	// 65536 = bisa multiple SQL statement di satu mysql_query() !
	$targetdb = mysql_connect($server,$username,$password, true, 65536) or die("Koneksi gagal");
	mysql_select_db($tdb, $targetdb);


	$sql = "SELECT barcode FROM detail_beli  ORDER BY barcode ASC";
	$hasil = mysql_query($sql, $targetdb) or die("Error : ".mysql_error());
	$ctr=0;		
	while($x = mysql_fetch_array($hasil)) {

		$sql = "SELECT harga FROM pembelian_detail WHERE kd_produk = '$x[barcode]'";
		$hasil1 = mysql_query($sql, $sourcedb) or die("Error : ".mysql_error());


		if (mysql_num_rows($hasil1) > 0) {
			$y = mysql_fetch_array($hasil1);
			$sql = "UPDATE detail_beli SET hargaBeli = $y[harga] WHERE barcode = '$x[barcode]'";
			$hasil2 = mysql_query($sql, $targetdb) or die("Error : ".mysql_error());
			$ctr++;
		} else {
			$sql = "UPDATE detail_beli SET hargaBeli = 0 WHERE barcode = '$x[barcode]'";
			$hasil2 = mysql_query($sql, $targetdb) or die("Error : ".mysql_error());
		};
	
	};

	echo "SELESAI - $ctr record di update";

?>
