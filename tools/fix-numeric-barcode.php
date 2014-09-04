<?php
/* fix-numeric-barcode.php ------------------------------------------------------
   	version: 1.01

	Part of AhadPOS : http://rimbalinux.com/projects/ahadpos/
	License: GPL v2
			http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
			http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License v2 (links provided above) for more details.
----------------------------------------------------------------*/


/* ---------------------------------
This script will fix old barcode fields in detail_beli table, 
which are still of bigint type. It should be varchar(25)

It can't be bigint, because there are barcode prefixed by 0 (zeros), so it will be stored without those zero prefix.

Example:
barcode: 089686592016
Stored by varchar(25) field	: 089686592016
Stored by bigint field		: 89686592016

For those using old version of AhadPOS, thus still have some of its data stored incorrectly as above, 
this script will fix it, using following logic :

1. SELECT * FROM barang, then start looping
2. Find the corresponding idBarang in detail_beli
3. See if the barcode is different. If yes:
	3a. copy the barcode from barang
		UPDATE detail_beli SET barcode=barang.barcode WHERE idBarang=barang.barcode;
	3b. find similar wrong barcodes in detail_beli, and change them as well
		UPDATE detail_beli SET barcode=barang.barcode WHERE barcode=$wrong_barcode;
4. End loop


==== BEFORE ==== running this script, execute the following SQL commands frist,
to fix the detail_beli table :

ALTER TABLE `detail_beli` CHANGE `barcode` `barcode` VARCHAR( 25 ) NULL DEFAULT NULL ;
ALTER TABLE `tmp_detail_beli` CHANGE `barcode` `barcode` VARCHAR( 25 ) NULL DEFAULT NULL ,
CHANGE `username` `username` VARCHAR( 30 ) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL ;
ALTER TABLE `tmp_detail_beli` CHANGE `barcode` `barcode` VARCHAR( 25 ) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL ;

------------------------------------ */

include "../config/config.php";



$sql = "SELECT * FROM barang";
$hasil1 = mysql_query($sql) or die(mysql_error());
$ctr=0;
while ($x = mysql_fetch_array($hasil1)) {

	// cari idBarang ybs di detail_beli
	$sql = "SELECT * FROM detail_beli WHERE idBarang=$x[idBarang]";
	$hasil2 = mysql_query($sql) or die(mysql_error());
	$y = mysql_fetch_array($hasil2);

	// cek apakah barcode-nya sama
	if ($y[barcode] !== $x[barcode]) {
		// jika tidak - copy barcode dari table barang
		$sql = "UPDATE detail_beli SET barcode='$x[barcode]' WHERE barcode='$y[barcode]'";
		$hasil3 = mysql_query($sql) or die(mysql_error());
		if ($x[idBarang] == 21097) { echo $sql;};
	};

	$ctr++;
	if ($ctr % 100 == 0) {
		echo "Sedang proses record : $ctr \n<br>";
	};
}


?>
