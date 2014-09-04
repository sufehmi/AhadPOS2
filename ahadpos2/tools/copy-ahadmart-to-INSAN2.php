<?php
        
        include "../config/config.php";

	// GLOBAL VARIABLES
	$temporary_barcode = 2323232323;		// barcode prefix for items with non-unique barcode
	$sdb 		= 'delme'; 		// source database
	$tdb 		= 'insan'; 		// target database
	$idSupplier	= 368;


// ====START CODE==================================================================================================================

	// konek ke source & target database
	$sourcedb = mysql_connect($server,$username,$password, true) or die("Koneksi gagal");
	mysql_select_db($sdb, $sourcedb);

	// 65536 = bisa multiple SQL statement di satu mysql_query() !
	$targetdb = mysql_connect($server,$username,$password, true, 65536) or die("Koneksi gagal");
	mysql_select_db($tdb, $targetdb);


	// OPTIMIZATIONS
	$sql = "ALTER TABLE `barang` ADD INDEX ( `namaBarang` ) ";
	$x1 = mysql_query($sql, $targetdb);



	echo "<h1>Sedang memproses : data ATK .....";	

	$ctr= 0;
	$errorctr=0;
	$sql = "SELECT * FROM barang WHERE idKategoriBarang = 5  
		LIMIT $ctr, ".($ctr+10000);
	//echo $sql;
	$hasil = mysql_query($sql, $sourcedb) or die("Error : ".mysql_error());
	while (mysql_num_rows($hasil) > 0) {	
		echo "<br> data: ".($ctr+1)." s/d ".($ctr+10000)." ....";
		
	while(($x = mysql_fetch_array($hasil)) !== false) {


		// cari apakah Nama barang ATAU barcode ini sudah ada di $targetdb
		$sql = "SELECT * FROM barang WHERE barcode = '$x[barcode]' OR namaBarang = '$x[namaBarang]'";
		$x1  = mysql_query($sql, $targetdb);

		// jika belum ada, INSERT
		if (mysql_num_rows($x1) == 0) {

			$sql = "INSERT INTO barang (namaBarang,idKategoriBarang,idSatuanBarang,jumBarang,
					hargaJual,last_update,idSupplier,barcode,username,idRak) 
				VALUES ('$x[namaBarang]',14,3,0,$x[hargaJual],'".date("Y-m-d")."',$idSupplier,'$x[barcode]','susan',0);";
			$x2  = mysql_query($sql, $targetdb);

		
		};
	}; // while(($x = mysql_fetch_array($hasil)) !== false) 


		// siap-siap untuk looping berikutnya
		$ctr = $ctr + 10000;
		$sql = "SELECT * FROM barang WHERE idKategoriBarang = 5  
			LIMIT $ctr, ".($ctr+10000);
		$hasil = mysql_query($sql, $sourcedb) or die("Error : ".mysql_error());
	}; // end while (mysql_num_rows($hasil) > 0) 


	
	echo " SELESAI !</h1> \n\n Error : $errorctr \n\n";
	exit;



?>



