<?php

/* js_cetak_retur_beli.php ------------------------------------------------------
version: 1.01

Part of AhadPOS : http://ahadpos.com
License: GPL v2
http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License v2 (links provided above) for more details.
---------------------------------------------------------------- */


include "../../config/config.php";
include "function.php";
include_once('../tacoen/function.php');
session_start();
check_ahadpos_session();

	if (isset($_POST['cetak'])) {

		echo "<link href='../../config/adminstyle.css' rel='stylesheet' type='text/css'>";

		echo "<h2>Note Retur Beli</h2>";
		$supplier= getDetailSupplier($_POST[idSupplier]);
		$detailSupplier= mysql_fetch_array($supplier);
		echo "Nama Supplier : $detailSupplier[namaSupplier]
			<br/>Tanggal Retur : $_POST[tglRetur] 
		<br/>Nota : $_POST[idTransaksiBeli]<br /><br />";


		echo "<table>
		<tr><th>No</th><th>Id Barang</th><th>Barcode</th><th>Nama Barang</th>
		<th>Jumlah<br />Retur</th><th>Harga Beli</th></tr>";

		$sql= "SELECT d.idBarang, d.barcode, d.jumRetur, d.hargaBeli, d.nominal, d.username, b.namaBarang 
			FROM detail_retur_beli AS d, barang AS b 
			WHERE idTransaksiBeli= $_POST[idTransaksiBeli] AND tglRetur= '$_POST[tglRetur]' AND d.barcode= b.barcode AND d.idSupplier={$_POST['idSupplier']}";
		$hasil= mysql_query($sql);

		$currentTotal= 0;
		$oldTotal= 0;
		$ctr= 1;
		$totalRecord= mysql_num_rows($hasil);
		while ($x= mysql_fetch_array($hasil)) {

			if (($ctr % 2)== 0) {
				$warna= "#EAF0F7";
			} else {
				$warna= "#FFFFFF";
			}
			echo "<tr bgcolor=$warna>"; //end warna
			echo "<td class=td>$ctr</td>
							<td class=td>		$x[idBarang]</td>
							<td class=td>		$x[barcode]</td>
							<td class=td>		$x[namaBarang]</td>
							<td class=td align=center>	$x[jumRetur]</td>
							<td class=td align=right>" . uang($x[hargaBeli]) . "</td>";
			echo "</tr>";

			$ctr++;
			$nominal= $x[nominal];
			$username= $x[username];
		} // while ($x= mysql_fetch_array($hasil))

		echo "</table><br />
		TOTAL : " . uang($nominal) . "<br />(username: $username)
	";
	} elseif (isset($_POST['download'])) {
//		echo 'Download CSV';
		// format isi file CSV :
		// $data[0]= barcode
		// $data[1]= idBarang - ignored
		// $data[2]= namaBarang - JOIN ke tabel barang
		// $data[3]= jumlah Barang / jumBarang - jumRetur
		// $data[4]= hargaBeli 
		// $data[5]= hargaJual (di Gudang) - ignored 
		// $data[6]= RRP (Recommended Retail Price) - ignored
		// $data[7]= namaSatuanBarang - ignored
		// $data[8]= namaKategoriBarang - ignored
		// $data[9]= Supplier - ignored
		// $data[10]= username - ignored
		// persiapan membuat output file CSV
		$csv= "\"barcode\",\"idBarang\",\"namaBarang\",\"jumBarang\",\"hargaBeli\",\"hargaJual\",\"RRP\",\"SatuanBarang\",\"KategoriBarang\",\"Supplier\",\"kasir\"\n";

		// cari nama toko ini 
		$hasil= mysql_query("SELECT value FROM config WHERE `option`= 'store_name'");
		$x= mysql_fetch_array($hasil);
		$namaToko= $x[value];

		$sql= "SELECT d.barcode, d.jumRetur, d.hargaBeli, d.username, b.namaBarang 
				FROM detail_retur_beli AS d
				join barang as b on b.barcode= d.barcode
				WHERE idTransaksiBeli= {$_POST['idTransaksiBeli']} AND tglRetur= '{$_POST['tglRetur']}'";
		$hasil= mysql_query($sql);
		$kosong= '';

		while ($x= mysql_fetch_array($hasil)) {
			$csv.= "\"" . $x['barcode'] . "\",\"" . $kosong . "\",\"" . $x['namaBarang'] . "\",\"" .
					$x['jumRetur'] . "\",\"" . $x['hargaBeli'] . "\",\"" .
					$kosong . "\",\"" . $kosong . "\",\"" . $kosong . "\",\"" . $kosong . "\",\"" .
					$kosong . "\",\"" . $x['username'] . "\",\"\"\n";
		}
		// masukkan nama toko ini ke nama file csv
		$namaToko= str_replace(' ', '_', $namaToko);
		$namaFile= 'RETUR-' . $namaToko . "-" . date("Y-m-d--H-i") . ".csv";

		// kirim output CSV ke browser untuk di download
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=\"$namaFile\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $csv;
	}
};



/* CHANGELOG -----------------------------------------------------------

1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
0.9.3 / 2010-04-16 : Harry Sufehmi		: initial release

------------------------------------------------------------------------ */
?>
