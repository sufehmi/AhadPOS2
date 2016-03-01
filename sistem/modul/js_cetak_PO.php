<?php
/* js_cetak_PO.php ------------------------------------------------------
	version: 1.01

	Part of AhadPOS : http://ahadpos.com
	License: GPL v2
			http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
			http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License v2 (links provided above) for more details.
----------------------------------------------------------------*/


require_once($_SERVER["DOCUMENT_ROOT"].'/define.php');

include SITE_ROOT."sistem/modul/function.php";



session_start();
if (empty($_SESSION[namauser]) AND empty($_SESSION[passuser])){
echo "<link href='../../../css/adminstyle.css' rel='stylesheet' type='text/css'>
<center>Untuk mengakses modul, Anda harus login <br>";
echo "<a href=index.php><b>LOGIN</b></a></center>";
}
else{

	if ($_POST['cetakcsv']) {

		// persiapan membuat output file CSV
		$csv="\"Nomor\",\"Barcode\",\"Nama Barang\",\"Stok Saat Ini\",\"Harga Beli\",\"Pesan\"\n";

			$cek=$_POST['cek'];
			$jumlah=count($cek);
			$no=1;
			for($i=0;$i<$jumlah;$i++){
				$data=getBarangPesan($cek[$i]);
				$barangPesan=mysql_fetch_array($data);
			$csv .= "\"".$no."\",\"".$barangPesan['barcode']."\",\"".$barangPesan['namaBarang']."\",\"".$barangPesan['jumBarang']."\",\"".$barangPesan['hargaBeli']."\",\"\"\n";
				$no++;
			};

			$supplier 	= getDetailSupplier($_POST['idSupplier']);
			$detailSupplier=mysql_fetch_array($supplier);
		$namaFile	= $detailSupplier['namaSupplier']."-".date("Y-m-d--H-i").".csv";

		// kirim output CSV ke browser untuk di download
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=\"$namaFile\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $csv;
		
	} else {
		echo "<link href='../../css/adminstyle.css' rel='stylesheet' type='text/css'>";

			echo "<h2>Purchase Order</h2>";
			$supplier=getDetailSupplier($_POST[idSupplier]);
			$detailSupplier=mysql_fetch_array($supplier);
			echo "Nama Supplier : $detailSupplier[namaSupplier]
				<br/>Tanggal PO : ".date("d-m-Y")."<br/><br/>";
			$cek=$_POST['cek'];
			$jumlah=count($cek);
			$no=1;
			echo "<table width=500><tr><th>No</th><th>Barcode</th><th>Nama Barang</th><th>Stok<br />Saat Ini</th><th>Pesan</th></tr>";
			for($i=0;$i<$jumlah;$i++){
				$data=getBarangPesan($cek[$i]);
				$barangPesan=mysql_fetch_array($data);
				echo "<tr><td class=td>$no</td>
					<td class=td>$barangPesan[barcode]</td>
					<td class=td>$barangPesan[namaBarang]</td>
					<td class=td><center>$barangPesan[jumBarang]</center></td>
			<td class=td>_____</td></tr>";
				$no++;
			}
			echo "</table>";
	}
};


/* CHANGELOG -----------------------------------------------------------

1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
0.9.3 / 2010-04-16 : Harry Sufehmi		: initial release

------------------------------------------------------------------------ */

?>
