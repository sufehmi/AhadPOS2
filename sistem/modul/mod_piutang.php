<?php
/* mod_piutang.php ------------------------------------------------------
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
        <h2>Daftar Piutang</h2>
        <table class=tableku>
          <tr><th>no</th><th>Id Transaksi</th><th>Nama Customer</th>
                <th>Tgl Harus Bayar</th><th>Nominal</th></tr>";
        $tampil=mysql_query("select p.idTransaksiJual, namaCustomer, p.tglDiBayar, p.nominal
            from piutang p, transaksijual tj, customer c
            where p.idTransaksiJual = tj.idTransaksiJual and tj.idCustomer = c.idCustomer");
        $no=1;
        while ($r=mysql_fetch_array($tampil)){
            $tgl = tgl_indo($r[tglDiBayar]);
            //untuk mewarnai tabel menjadi selang-seling
            if(($no % 2) == 0){
                $warna = "#EAF0F7";
            }
            else{
                $warna = "#FFFFFF";
            }
            echo "<tr bgcolor=$warna>";//end warna
           echo "<td align=right class=td>$no</td>
                 <td class=td align=center>$r[idTransaksiJual]</td>
                 <td align=center class=td width=40%>$r[namaCustomer]</td>
                 <td align=center class=td>$tgl</td>
                 <td align=center class=td>$r[nominal]</td>
                 </tr>";
          $no++;
        }
        echo "</table>
        <p>&nbsp;</p>
        <a href=javascript:history.go(-1)><< Kembali</a>";

        break;
}


/* CHANGELOG -----------------------------------------------------------

 1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
 0.6.5		    : Gregorius Arief		: initial release

------------------------------------------------------------------------ */

?>
