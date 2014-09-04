<?php
/* tutup_kas.php ------------------------------------------------------
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

 $kas = getKasAwal($_SESSION[iduser]);
 $uang = getUangKasir($_SESSION[iduser]);
 $jum = $kas+$uang;

 echo "<form method=POST action='./aksi.php?module=tutup_kas&act=input'>
    <input type=hidden name=kasSeharusnya value=$jum>
    <table>
        <tr><td>Kas Awal</td><td> : $kas</td><tr>
        <tr><td>Uang Transaksi</td><td> : $uang</td><tr>
        <tr><td>Uang Seharusnya</td><td> : $jum</td><tr>
        <tr><td>Uang Kasir</td><td> : <input type=text name=kasAkhir></td><tr>
        <tr><td colspan=2>&nbsp;</td></tr>
        <tr><td colspan=2><input type=submit value='Simpan'>&nbsp;&nbsp;&nbsp;
                                <input type=reset value='Batal'></td></tr>
    </table>
 </form>
";


/* CHANGELOG -----------------------------------------------------------

 1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
 0.6.5		    : Gregorius Arief		: initial release

------------------------------------------------------------------------ */

?>
