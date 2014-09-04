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
	$targetdb = mysql_connect('192.168.10.14',$username,'root', true) or die("Koneksi gagal");
	mysql_select_db($tdb, $targetdb);


	$sql = "SELECT barcode FROM barang ORDER BY barcode ASC";
	$hasil = mysql_query($sql, $targetdb) or die("Error : ".mysql_error());
	$ctr=0;		
	while($x = mysql_fetch_array($hasil)) {

		$sql = "SELECT id_supplier FROM stock WHERE kd_barang = '$x[barcode]'";
		$hasil1 = mysql_query($sql, $sourcedb) or die("Error : ".mysql_error());


		if (mysql_num_rows($hasil1) > 0) {
			$y = mysql_fetch_array($hasil1);
			$sql = "UPDATE barang SET idSupplier = $y[id_supplier] WHERE barcode = '$x[barcode]'";
			$hasil2 = mysql_query($sql, $targetdb) or die("Error : ".mysql_error());
			$ctr++;
		} else {
			$sql = "UPDATE barang SET idSupplier = 0 WHERE barcode = '$x[barcode]'";
			$hasil2 = mysql_query($sql, $targetdb) or die("Error : ".mysql_error());
		};
	
	};

	echo "SELESAI - $ctr record di update";

?>
