<?php
	
	include "../config/config.php";

	$tampil=mysql_query("SELECT DISTINCT *   
                        FROM tmp_detail_beli");


	while ($r=mysql_fetch_array($tampil)) {

	if ($r[hargaJual] > 0) {

		$sql = "UPDATE barang SET hargaJual=$r[hargaJual] WHERE barcode=$r[barcode]";
		$hasil = mysql_query($sql);
		echo $sql."<br />";

	}

	}	

?>
