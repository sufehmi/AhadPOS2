<?php

/*
 * Script untuk mengupdate Stock (jumBarang) di tabel detail_beli
 * agar sama dengan Stock (jumBarang) di tabel barang
 * Untuk permasalahan di Gandul2
 * Maret 2014
 * 
 * Pemanggilan script hanya simulasi (tidak update ke database): h++p://..updatestock.php?simulasi=1
 * Pemanggilan script langsung update ke database: h++p://..updatestock.php
 */
session_start();
include "../config/config.php";
$simulasi = isset($_GET['simulasi']) && $_GET['simulasi'] == '1' ? true : false;
echo 'Simulasi: ';
echo $simulasi ? 'true' : 'false';
echo '<br />';

/*
 * Inisialisasi tabel detail_beli
 * dianggap semuanya sudah terjual, stock = 0, isSold='Y'
 * Untuk kemudian diupdate yang masih ada stocknya saja
 * 
 * Jadi.. Script ini bisa dijalankan berulang-ulang
 */
echo "init detail_beli.. ";
$sql = "update detail_beli
	set jumBarang=0, isSold='Y'";
mysql_query($sql) or die('Gagal init detail_beli, error: '.mysql_error());
echo "selesai <br />";

echo "Ambil data barang.. ";
$sql = "select * 
		from barang where jumBarang>0";
$result = mysql_query($sql) or die('Gagal ambil data barang, error: '.mysql_error());
echo "selesai <br /><br />";

while ($barang = mysql_fetch_array($result)):
	echo $barang['barcode'].' ['.$barang['namaBarang'].'] <b>'.$barang['jumBarang'].'</b>';
	echo '<br />';
	$sql = "select *
				from detail_beli db
				join transaksibeli tb on tb.idTransaksiBeli = db.idTransaksiBeli
				where barcode = '{$barang['barcode']}' 
			   order by db.idTransaksiBeli desc";
	$resultDetailBeli = mysql_query($sql) or die('Gagal Ambil Detail Beli, error: '.mysql_error());
	$jumBarang = $barang['jumBarang'];
	$i = 1;
	while (($detailBeli = mysql_fetch_array($resultDetailBeli)) && $jumBarang > 0):

		/*
		 * Jika pembelian (detail_beli.jumlahBarangAsli) lebih besar dari stock (barang.jumBarang)
		 * langsung update detail_beli.jumBarang  dengan barang.jumBarang
		 * Jika lebih kecil
		 * update detail_beli.jumBarang dengan jumlah pembelian (detail_beli.jumBarangAsli)
		 * yang kemudian mencari lagi di row selanjutnya
		 */
		if ($detailBeli['jumBarangAsli'] >= $jumBarang) {
			if (!$simulasi) {
				mysql_query("update detail_beli set jumBarang = {$jumBarang}, isSold='N' where idDetailBeli={$detailBeli['idDetailBeli']}") or die('Gagal update detailbeli script 1, error: '.mysql_error());
			}
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;detail beli {$detailBeli['idDetailBeli']} {$detailBeli['tglTransaksiBeli']} jumlahBarangAsli={$detailBeli['jumBarangAsli']}: UPDATE jumBarang=<b>{$jumBarang}</b> ";
			$jumBarang = 0;
		} else {
			if (!$simulasi) {
				mysql_query("update detail_beli set jumBarang = jumBarangAsli, isSold='N' 
					  where idDetailBeli={$detailBeli['idDetailBeli']}") or die('Gagal update detailbeli script 2, error: '.mysql_error());
			}
			$jumBarang -= $detailBeli['jumBarangAsli'];

			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;detail beli {$detailBeli['idDetailBeli']} {$detailBeli['tglTransaksiBeli']} jumlahBarangAsli={$detailBeli['jumBarangAsli']}: UPDATE jumBarang=<b>{$detailBeli['jumBarangAsli']}</b>, Sisa={$jumBarang}";
		}
		echo '<br />';
	endwhile;
	echo '<br />';
endwhile;
