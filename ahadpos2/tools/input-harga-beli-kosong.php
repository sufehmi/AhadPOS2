<?php

/* ------------------------------------------------------------------------------
input-harga-beli-kosong.php
	version: 1.2.5

Kasus : di sebuah database AhadPOS, ada banyak barang tanpa hargaBeli / hargaBeli = 0

Solusi : tampilkan interface untuk input hargaBeli yang kosong ini, per Rak


	Part of AhadPOS : http://ahadpos.com
	License: GPL v2
			http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
			http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License v2 (links provided above) for more details.
----------------------------------------------------------------*/

// Connecting, selecting database
include "../config/config.php";


if ($_GET["rak"]) {

	$sql	= "SELECT b.namaBarang, d.barcode, b.hargaJual, d.hargaBeli 
			FROM detail_beli AS d, barang AS b 
			WHERE b.barcode = d.barcode AND b.idRak = ".$_GET["rak"]." AND d.hargaBeli = 0 AND isSold = 'N' 
			ORDER BY b.namaBarang ASC LIMIT 10";
	$hasil	= mysql_query($sql);

	echo "
		<h2>Daftar Barang HPP = 0 (nol)</h2>

		<form action=$_SERVER[PHP_SELF] method=POST>

		<table border=1>
		<tr>
			<td>Barcode
			</td>
			<td>Nama Barang
			</td>
			<td>Harga Jual
			</td>
			<td>Harga Beli
			</td>
			<td>Hapus ?
			</td>
		</tr>
		";

	$n = 1;
	while ($x = mysql_fetch_array($hasil)) {

		echo "
		<tr>
		<td>".$x["barcode"]." 	<input type=hidden name=barcode$n value=".$x["barcode"].">
		</td>
		<td>".$x["namaBarang"]."
		</td>
		<td>".$x["hargaJual"]."
		</td>
		<td><input type=text name=hpp$n value=0 size=5>
		</td>
		<td><input type=checkbox name=hapus$n>
		</td>
		</tr>
		";
		$n++;

	}; // 	while ($x = mysql_fetch_array($hasil)) {

	echo "
	</table>

	<input type=hidden name=count value=$n>
	<input type=submit name=simpan value=Simpan>
	</form>
	";

}; // if ($_GET["rak"]) {


if ($_POST["simpan"]) {


	for ($i; $i < $_POST["count"]; $i++) {

		$barcode	= $_POST["barcode".$i];
		$hpp		= $_POST["hpp".$i];
		$hapus		= $_POST["hapus".$i];


		if (($hpp > 0) OR ($hapus == "on")) {

		// tampilkan informasi proses
		echo " <br />
		Sedang memproses data barcode ".$barcode.": HPP = ".$hpp;

		// simpan perubahan HPP
		$sql 	= "UPDATE detail_beli SET hargaBeli = ".$hpp." 
				WHERE barcode = '".$barcode."'";
		$hasil	= mysql_query($sql);
	
		// proses record yang ingin dihapus
		if ($hapus == "on") {
			echo " - DIHAPUS";
			$sql 	= "DELETE FROM barang WHERE barcode = '".$barcode."'";
			$hasil	= mysql_query($sql) or die(mysql_error());

			$sql 	= "UPDATE detail_beli SET isSold = 'Y'  
					WHERE barcode = '".$barcode."'";
			$hasil	= mysql_query($sql) or die(mysql_error());
		};

		}; // if (($hpp > 0) OR ($hapus == "on")) {

	}; // for ($i; $i < $_GET["count"]; $i++) {

}; // if ($_GET["simpan"]) {


// ==================================================================================
// tampilkan daftar Rak

$sql	= "SELECT idRak, namaRak FROM rak ORDER BY namaRak";
$hasil	= mysql_query($sql) or die(mysql_error());

echo "
	<br /><br />
	<hr />
	<form action=$_SERVER[PHP_SELF] method=GET>

	Pilih Rak : 
	<select name=rak>
	";

while ($x = mysql_fetch_array($hasil)) {

	echo "
	<option value=".$x["idRak"].">".$x["namaRak"]."</option> \n";
}; // while ($x = mysql_fetch_array($hasil)) {

echo "
	</select>

	<br /><br />
	<input type=submit value=Pilih>
	</form>
	";

?>

