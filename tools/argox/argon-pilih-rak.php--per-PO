<?php
/* argon-pilih-rak.php ----------------------------------------
   	version: 1.01

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
include "../../config/config.php";


// let's print the international format for the en_US locale
$query = 'SELECT idTransaksiBeli FROM transaksibeli ORDER BY idTransaksiBeli DESC';
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

echo "<form action=$_SERVER[PHP_SELF] method=GET> pilih Nota/Invoice:<select name=rak>";
while ($line = mysql_fetch_array($result)) {
echo"<option value=$line[idTransaksiBeli]>$line[idTransaksiBeli]</option>";
}
echo "</select><input type=submit></form>";

if($_GET[rak]!=""){
// Performing SQL query

$po = $_GET[rak];

$queryharga = mysql_query("SELECT b.barcode,b.namaBarang,b.hargaJual  FROM barang AS b, detail_beli AS d WHERE  d.barcode=b.barcode AND d.idTransaksiBeli = '$po'") or DIE ("auoo ".mysql_error());

// Printing results in HTML
echo "<table border=1>\n";
while ($baris = mysql_fetch_array($queryharga)) {


	$kd_barang 	= $baris[barcode];
	$nama_barang	= $baris[namaBarang];
	$harga_jual	= $baris[hargaJual];
	$jml		= "0002";

        echo "
		<tr>
			<form action=argon-cetak-barcode.php method=GET target=Content>
			<td ROWSPAN=2><input type=hidden name=file value=$kd_barang >
				<input type=hidden name=kode value=$kd_barang >$kd_barang</td>
			<td>$nama_barang</td>
			<td><input type=hidden name=harga value=".number_format($harga_jual, 0, '', '.')." 
				>".number_format($harga_jual, 0, '', '.')."</td>
		</tr>
			<td><textarea name=nama rows=1 cols=30 >$nama_barang</textarea> 
			jumlah :<input  type=text size=3 name=jml maxlength=4 value=$jml></td>
			<td><input type=submit></td></form>";

}
echo "</table>\n";

// Free resultset
mysql_free_result($result);

}


/* CHANGELOG -----------------------------------------------------------

 1.0.1	: Harry Sufehmi		: initial release

------------------------------------------------------------------------ */

?> 
