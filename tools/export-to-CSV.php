<?php 

include("../config/config.php");

$fp = fopen('/tmp/ahadpos-'.date("Ymd").'.csv', 'w');
fwrite($fp, '"Product Name", "Price", "SKU", "weight unit", "stock quantity"'."\n");

$sql = "SELECT * FROM barang LIMIT 100";
$hasil = mysql_query($sql);

while ($r=mysql_fetch_array($hasil)) {

	$tulis = '"'.$r[namaBarang].'","'.$r[hargaJual].'","'.$r[barcode].'","gram","1000000"'."\n";

	fwrite($fp, $tulis);
}

fclose($fp);


?>
