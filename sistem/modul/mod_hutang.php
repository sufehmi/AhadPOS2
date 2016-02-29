<?php
/* mod_hutang.php ------------------------------------------------------
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


include "../config/config.php";
check_user_access(basename($_SERVER['SCRIPT_NAME']));


switch($_GET[act]){
    default:
        echo "
        <h2>Daftar Hutang</h2>
        <table class=tableku>
          <tr><th>no</th><th>Id Transaksi</th><th>Nama Supplier</th>
                <th>Tgl Harus Bayar</th><th>Nominal</th><th>No.Invoice</th><th>Aksi</th></tr>";
        $tampil=mysql_query("select h.idTransaksiBeli, namaSupplier, h.nominal, h.tglBayar, NomorInvoice
        from supplier s,hutang h,transaksibeli tb
        where h.idTransaksiBeli = tb.idTransaksiBeli and s.idSupplier = tb.idSupplier");
        $no=1;
        while ($r=mysql_fetch_array($tampil)){
            $tgl = tgl_indo($r[tglBayar]);
            //untuk mewarnai tabel menjadi selang-seling
            if(($no % 2) == 0){
                $warna = "#EAF0F7";
            }
            else{
                $warna = "#FFFFFF";
            }
            echo "<tr bgcolor=$warna>";//end warna
           echo "<td align=right class=td>$no</td>
                 <td class=td align=center>$r[idTransaksiBeli]</td>
                 <td align=center class=td>$r[namaSupplier]</td>
                 <td align=center class=td>$tgl</td>
                 <td align=right class=td>".uang($r[nominal])."</td>
                 <td align=center class=td>$r[NomorInvoice]</td>
                 <td align=center class=td><a href=?module=hutang&act=lihatdetail&id=$r[idTransaksiBeli]>Lihat</a> | <a href=?module=hutang&act=edittrans&id=$r[idTransaksiBeli]>Edit</a></td>
                 </tr>";
          $no++;
        }
        echo "</table>
        <p>&nbsp;</p>
        <a href=javascript:history.go(-1)><< Kembali</a>";

        break;


    case "lihatdetail": // ----------------------------------------------------------------------------
        $transaksiBeli = detailTransaksiBeli($_GET[id]);
        $dataTrans = mysql_fetch_array($transaksiBeli);
        echo "<h2>Detail Hutang</h2>
            <table>
                <tr><td>Nomor Invoice</td><td> : </td><td>$dataTrans[NomorInvoice]</td><td width=10>&nbsp;</td>
                    <td>Tgl Transaksi</td><td> : </td><td>$dataTrans[tglTransaksiBeli]</td></tr>
                <tr><td>No Transaksi Beli</td><td> : </td><td>$dataTrans[idTransaksiBeli]</td><td width=10>&nbsp;</td>
                    <td>Nama Supplier</td><td> : </td><td>$dataTrans[namaSupplier]</td></tr>
            </table><br/>
            ";
        echo "<table>
            <tr><th>barcode</th><th>Nama Barang</th><th>Tgl Expire</th><th>Jumlah</th><th>Harga Beli</th><th>Total</th></tr>
            ";
        $barangTransaksiBeli = detailBarangTransaksiBeli($_GET[id]);
        $no = 1;
        while($dataBarangTrans = mysql_fetch_array($barangTransaksiBeli)){
            $total = $dataBarangTrans[jumBarang]*$dataBarangTrans[hargaBeli];
            //untuk mewarnai tabel menjadi selang-seling
            if(($no % 2) == 0){
                $warna = "#EAF0F7";
            }
            else{
                $warna = "#FFFFFF";
            }
            echo "<tr bgcolor=$warna>";//end warna
            echo "<td class=td>$dataBarangTrans[barcode]</td>
                  <td class=td>$dataBarangTrans[namaBarang]</td>
                  <td class=td>".tgl_indo($dataBarangTrans[tglExpire])."</td>
                  <td class=td align=right>$dataBarangTrans[jumBarang]</td>
                  <td class=td align=right>".uang($dataBarangTrans[hargaBeli])."</td>
                  <td class=td align=right>".uang($total)."</td>                  
                ";
            echo "</tr>";
        }
        echo "<tr><td class=td colspan=4 align=right>Total</td><td class=td colspan=2 align=right>".uang($dataTrans[nominal])."</td></tr>
            
            <tr><td colspan=6 class=td>&nbsp;</td></tr>
            <tr><td colspan=3 class=td><a href=?module=hutang>Kembali</a></td><td colspan=3 class=td><a href=?module=hutang&act=edittrans&id=$_GET[id]>Edit</a></td></tr>
            </table>";

        break;

    case "edittrans":
        if($_GET[action] == 'update'){
            $_GET[id] = $_POST[idTransaksiBeli];
//            echo "$_POST[idTransaksiBeli], $_POST[idBarang], $_POST[jumBarang], $_POST[hargaBeli]";
            editBarangBeli($_POST[idTransaksiBeli], $_POST[idBarang], $_POST[jumBarangLama], $_POST[jumBarang], $_POST[hargaBeli]);
        }
        $transaksiBeli = detailTransaksiBeli($_GET[id]);
        $dataTrans = mysql_fetch_array($transaksiBeli);
        echo "<h2>Edit Transaksi Beli</h2>
            <table>
                <tr><td>Nomor Invoice</td><td> : </td><td>$dataTrans[NomorInvoice]</td><td width=10>&nbsp;</td>
                    <td>Tgl Transaksi</td><td> : </td><td>$dataTrans[tglTransaksiBeli]</td></tr>
                <tr><td>No Transaksi Beli</td><td> : </td><td>$dataTrans[idTransaksiBeli]</td><td width=10>&nbsp;</td>
                    <td>Nama Supplier</td><td> : </td><td>$dataTrans[namaSupplier]</td></tr>
            </table><br/>
            ";
        echo "<table>
            <tr><th>barcode</th><th>Nama Barang</th><th>Tgl Expire</th><th>Jumlah</th><th>Harga Beli</th><th>Total</th><th>AKSI</th></tr>
            ";

        $barangTransaksiBeli = detailBarangTransaksiBeli($dataTrans[idTransaksiBeli]);
        $no = 1;
        while($dataBarangTrans = mysql_fetch_array($barangTransaksiBeli)){
            $total = $dataBarangTrans[jumBarang]*$dataBarangTrans[hargaBeli];
            //untuk mewarnai tabel menjadi selang-seling
            if(($no % 2) == 0){
                $warna = "#EAF0F7";
            }
            else{
                $warna = "#FFFFFF";
            }
            echo "<tr bgcolor=$warna>";//end warna
            echo "<form method=POST action='?module=hutang&act=edittrans&action=update'>
                <input type=hidden name=idBarang value=$dataBarangTrans[idBarang]>
                <input type=hidden name=idTransaksiBeli value=$_GET[id]>
                <input type=hidden name=jumBarangLama value=$dataBarangTrans[jumBarang]>
                <td class=td>$dataBarangTrans[barcode]</td>
                  <td class=td>$dataBarangTrans[namaBarang]</td>
                  <td class=td>".tgl_indo($dataBarangTrans[tglExpire])."</td>
                  <td class=td align=right><input type=text name=jumBarang size=10 value=$dataBarangTrans[jumBarang]></td>
                  <td class=td align=right><input type=text name=hargaBeli value=$dataBarangTrans[hargaBeli]></td>
                  <td class=td align=right>".uang($total)."</td>
                  <td class=td align=center><input type=submit value=Update></td>
                  </form>
                ";
            echo "</tr>";
        }
        $nominal = nominalBeli($_GET[id]);

        echo "<tr><td class=td colspan=4 align=right>Total</td><td class=td colspan=2 align=right>".uang($nominal)."</td></tr>
            </table>";
        echo "<br/><a href=?module=hutang>Kembali</a>";
        break;
}



/* CHANGELOG -----------------------------------------------------------

 1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
 0.9.1		    : Gregorius Arief		: initial release

------------------------------------------------------------------------ */

?>
