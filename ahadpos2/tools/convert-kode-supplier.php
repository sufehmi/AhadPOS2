<?php 

include("../config/config.php");


echo_dbf('/home/ubuntu/Documents/_workshop/devel/indopos/tools/SUPLIER.DBF');
exit;

function echo_dbf($dbfname) { 
    $fdbf = fopen($dbfname,'r'); 
    $fields = array(); 
    $buf = fread($fdbf,32); 
    $header=unpack( "VRecordCount/vFirstRecord/vRecordLength", substr($buf,4,8)); 
    ////echo 'Header: '.json_encode($header).'<br/>'; 
    $goon = true; 
    $unpackString=''; 
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
	$sql1 = "SELECT * FROM supplier WHERE namaSupplier = '".$record[KODE]."'";
	echo "Mencari: ".$sql1."<br>";
	$hasil1 = mysql_query($sql1);
	// jika ketemu, maka ubah ke nama lengkap supplier ybs :
	if (mysql_num_rows($hasil1) < 1) {
	
		// cari supplier
		$sql2 = "UPDATE supplier SET namaSupplier='".$record[NAMA]."' WHERE namaSupplier='".$record[KODE]."'";
		echo "<br>Eksekusi : ".$sql2;
		$hasil2 = mysql_query($sql2);
		
	};

	} 
    fclose($fdbf); } 
?>
