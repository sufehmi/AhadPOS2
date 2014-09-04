<?php 

$server = "localhost";
$username = "root";
$password = "";
$database = "test2";

// Koneksi dan memilih database di server
mysql_connect($server,$username,$password) or die("Koneksi gagal");
mysql_select_db($database) or die("Database tidak bisa dibuka");


echo_dbf('/home/sufehmi/Documents/_workshop/devel/old-Ahad-POS/GDG_12.DBF');
exit;

function echo_dbf($dbfname) { 
    $fdbf = fopen($dbfname,'r'); 
    $fields = array(); 
    $buf = fread($fdbf,32); 
    $header=unpack( "VRecordCount/vFirstRecord/vRecordLength", substr($buf,4,8)); 
    ////echo 'Header: '.json_encode($header).'<br/>'; 
    $goon = true; 
    $unpackString=''; 

    echo "Total record: ".$header['RecordCount']." \n";
    while ($goon && !feof($fdbf)) { // read fields: 
        $buf = fread($fdbf,32); 
        if (substr($buf,0,1)==chr(13)) {$goon=false;} // end of field list 
        else { 
            $field=unpack( "a11fieldname/A1fieldtype/Voffset/Cfieldlen/Cfielddec", substr($buf,0,18)); 
            ////echo 'Field: '.json_encode($field).'<br/>'; 
            $unpackString.="A$field[fieldlen]$field[fieldname]/"; 
            array_push($fields, $field);}} 
    fseek($fdbf, $header['FirstRecord']+1); // move back to the start of the first record (after the field definitions) 
    for ($i=1; $i<=$header['RecordCount']; $i++) { 
        $buf = fread($fdbf,$header['RecordLength']); 
        $record=unpack($unpackString,$buf); 
        //// output format JSON :: 
	//echo 'record: '.json_encode($record).'<br/>'; 
        //// output format Array
	//echo 'record: '.var_dump($record).'<br/>'; 
        //// output format RAW
	// echo $i.$buf.'<br/>'; 
	// cari apakah barcode ini sudah ada di database ?
	$sql1 = "SELECT * FROM barang WHERE barcode = '".$record["KODE"]."'";
	//echo "Mencari: ".$sql1."<br>";
	$hasil1 = mysql_query($sql1) or die("Error : ".mysql_error()." SQL: ".$sql1);
	// jika belum ada, maka simpan :
	if (mysql_num_rows($hasil1) < 1) {
	

		// kalau SUPL_KD kosong, ganti menjadi "umum"
		if (($record["SUPL_KD"] == '') || (empty($record["SUPL_KD"]))) {
			$record["SUPL_KD"] = "umum";
		};

		// cari supplier
		$sql2 = "SELECT * FROM supplier WHERE namaSupplier='".$record["SUPL_KD"]."'";
		$hasil2 = mysql_query($sql2);
		// jika belum ada, maka simpan kode supplier ybs ke database supplier
		if (mysql_num_rows($hasil2) < 1) {
			mysql_query("INSERT INTO supplier(namaSupplier) VALUES ('".$record["SUPL_KD"]."')");
			$kode_supplier = mysql_insert_id();
		} else { // catat nomor suppliernya
			$data=mysql_fetch_array($hasil2);
			$kode_supplier = $data["idSupplier"];
		};
		//echo "<br>kode supplier : ".$kode_supplier."\n"; 

		// apakah ada karakter ' (single quote) di nama barangnya ? 
		// jika ya, ganti menjadi "
		while (strpos($record["NAMA"], "'") !== false) {
			$record["NAMA"] = str_replace("'", '"', $record["NAMA"]);
			echo "Ada single quote : ".$record["KODE"]." ".$record["NAMA"]."\n";
		};

		// simpan di tabel barang
		$sql3 = "INSERT INTO barang(idBarang,namaBarang, idSupplier, idKategoriBarang, idSatuanBarang, last_update, barcode, hargaJual)
                    VALUES($i, '$record[NAMA]', '$kode_supplier', '9999','3', '2010-02-01', '$record[KODE]', $record[HRG_JUAL])";
		//echo $sql3." <br>";
	    	mysql_query($sql3);

		// simpan di tabel detail_beli
		$sql3 = "INSERT INTO detail_beli (idTransaksiBeli, idBarang, jumBarang, hargaBeli, barcode, username, jumBarangAsli)
                    VALUES(1, $i, 1, $record[HRG_BELI], '$record[KODE]', 'admin', 0)";
		//echo $sql3." <br>";
	    	//mysql_query($sql3);

	};

	} 
    fclose($fdbf); } 
?>
