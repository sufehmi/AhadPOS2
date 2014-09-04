<?php   
/* js_tambah_barang.php ------------------------------------------------------
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

include "../../config/config.php";

	// simpan di tmp_detail_beli
    $tgl = date("Y-m-d");


	//fixme: potensi masalah: 
	// jika barcode dimasukkan sudah ada - dan suppliernya berbeda
	// solusi: validasi sebelum INSERT bahwa suppliernya juga sama.
	mysql_query("INSERT into tmp_detail_beli(idSupplier, tglTransaksi,
                        barcode,jumBarang,hargaBeli,hargaJual,username)
                    VALUES('$_POST[supplier]','$tgl','$_POST[barcode]',
                        '$_POST[jumBarang]','$_POST[hargaBeli]','$_POST[hargaJual]','$_POST[username]')");

	// catat nomor idBarang yang di generate oleh MySQL
	$idBarang = mysql_insert_id();
	echo "idBarang = $idBarang";

	// buat record nya di tabel barang
    	$tgl = date("Y-m-d");
    	mysql_query("INSERT INTO barang(namaBarang, idKategoriBarang, idSatuanBarang, jumBarang, hargaJual, last_update, idSupplier, barcode, idBarang, idRak)
                    VALUES('$_POST[namaBarang]', '$_POST[kategori_barang]','$_POST[satuan_barang]',0,'$_POST[hargaJual]', 
                    '$tgl','$_POST[supplier]', '$_POST[barcode]', $idBarang, $_POST[rak])");
	// jumBarang = 0  karena ini nanti akan ditambahkan isinya dari function.php, 
	// kalau diisi disini, maka akan ditambah lagi oleh function.php - dan jadi dobel



	// jika barang sudah ada, INSERT diatas akan gagal (karena barcode tidak bisa dobel di tabel barang), 
	// dan mysql_insert_id() akan menghasilkan 0 = idBarang = 0,
	// maka kita cari idBarang nya di tabel barang
	if ($idBarang == 0) {
		$sql = "SELECT idBarang FROM barang WHERE barcode='$_POST[barcode]'";
		$hasil = mysql_query($sql); 
		if ($x = mysql_fetch_array($hasil)) {
			$idBarang = $x[idBarang];
		}
	}


/*
	echo "            <form method=POST action='?module=pembelian_barang&act=carisupplier&action=tambah'>
                <table>
                    <tr>
                        <td>Barcode</td><td> : <input type=text name='barcode' value='$_POST[barcode]' readonly='readonly' />
				<input type=hidden name='idBarang' value=".$idBarang." /></td>
			<td></td>
                    </tr>
                    <tr>
                        <td>Nama Barang</td><td> : <input type=text name='namaBarang' value='$_POST[namaBarang]' readonly='readonly' /></td>
                        <td><u>j</u>umlah yang dibeli</td><td> : <input type=text name='jumBarang' value='$_POST[jumBarang]' accesskey='j' tabindex=1/></td>                        
                    </tr>
			<tr>
				<td>Harga Beli Sekarang</td><td> : <input type=text name='hargaBeliLama' value='$_POST[hargaBeli]' readonly='readonly' /></td>
	                        <td>Harga Beli Barang</td><td> : <input type=text name='hargaBeliBaru' value='$_POST[hargaBeli]' tabindex=2/></td>
			</tr>
                    <tr>
                        <td>Harga Jual Sekarang</td><td> : <input type=text name='hargaJualLama' value='$_POST[hargaJual]' readonly='readonly' /></td>
                        <td>Harga Jual Barang</td><td> : <input type=text name='hargaJualBaru' value='$_POST[hargaJual]' tabindex=3/></td>
                    </tr>                    
                    <tr>
                        <td colspan=2>&nbsp</td>
                        <td>Tanggal Expire</td><td> : <input type=text name='tglExpire' size=10 tabindex=4/>(yyyy-mm-dd)</td>
                    </tr>
                    <tr>
                        <td align=right colspan=4><input type=submit value='(t) Tambah' name=btTambah accesskey='t' tabindex=5></td>
                    </tr>
                </table>
            </form>";
*/


	echo "<script>window.location.reload()</script>";


/* CHANGELOG -----------------------------------------------------------

 1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
 0.9.3 / 2010-04-16 : Harry Sufehmi		: initial release

------------------------------------------------------------------------ */

?>
