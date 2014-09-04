<?php


// DO NOT USE ! unless absolutely necessary
// this tool doesn't create the link from transaksibeli to detail_beli
//
// All records migrated to detail_beli by this tool is assigned to idTransaksiBeli of 0 (zero)


include "../config/config.php";


    $dataBarang = mysql_query("SELECT * from tmp_detail_beli") or die(mysql_error());

    while($simpan = mysql_fetch_array($dataBarang)){

	$sql_simpan = "INSERT INTO detail_beli(idTransaksiBeli,barcode,
                        tglExpire,jumBarang,hargaBeli,username,idBarang)
                    VALUES('0','$simpan[barcode]',
                    '$simpan[tglExpire]','$simpan[jumBarang]','$simpan[hargaBeli]','admin','$simpan[idBarang]')";
	echo $sql_simpan."\n";
	mysql_query($sql_simpan) or die(mysql_error());


        $jumlahAkhir = 0;
        $jumBarang = mysql_query("select jumBarang from barang where barcode = '$simpan[barcode]'") or die(mysql_error());
        $jumlah = mysql_fetch_array($jumBarang);
        $jumlahAkhir = $jumlah[jumBarang] + $simpan[jumBarang];

        mysql_query("UPDATE barang SET jumBarang = '$jumlahAkhir' 
                      WHERE barcode = '$simpan[barcode]'") or die(mysql_error());
    }
    //fixme: waktu delete sebaiknya juga menggunakan username sehingga jika ada user lain yang memasukkan tidak terjadi error
    mysql_query("DELETE FROM tmp_detail_beli ") or die(mysql_error());
?>
