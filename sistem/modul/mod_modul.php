<?php
/* mod_modul.php ------------------------------------------------------
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


$ambilLevelUser = mysql_query("select * from leveluser");

switch($_GET[act]){
  // Tampil Modul
  default:
    echo "<h2>Data Modul</h2>
          <form method=POST action='?module=modul&act=tambahmodul'>
          <input type=submit value='Tambah Modul'></form>
          <br/>
          <table class=tableku>
          <tr><th>No</th><th>nama modul</th><th>link</th><th>publish</th><th>pejabat</th><th>aksi</th></tr>";
    $tampil=mysql_query("SELECT idModul,namaModul,link,publish,levelUser,urutan
                            FROM modul m, leveluser lu
                            WHERE m.idLevelUser = lu.idLevelUser
                            ORDER BY urutan");
    $no=1;
    while ($r=mysql_fetch_array($tampil)){
        //untuk mewarnai tabel menjadi selang-seling
        if(($no % 2) == 0){
            $warna = "#EAF0F7";
	}
	else{
            $warna = "#FFFFFF";
	}
        echo "<tr bgcolor=$warna>";//end warna
      echo "<td class=td>$no</td>
            <td class=td>$r[namaModul]</td>
            <td class=td><a href=$r[link]>$r[link]</a></td>
            <td align=center class=td>$r[publish]</td>
            <td align=center class=td>$r[levelUser]</td>
            <td class=td><a href=?module=modul&act=editmodul&id=$r[idModul]>Edit</a> |
	              <a href=./aksi.php?module=modul&act=hapus&id=$r[idModul]>Hapus</a>
            </td></tr>";

       $no++;
    }
    echo "</table><p>&nbsp;</p>
    <a href=javascript:history.go(-1)><< Kembali</a>";
    break;

  case "tambahmodul":
    echo "<h2>Tambah Modul</h2>
          <form method=POST action='./aksi.php?module=modul&act=input'>
          <table>
          <tr><td>Nama Modul</td> <td> : <input type=text name='namaModul' size=30></td></tr>
          <tr><td>Link</td>       <td> : <input type=text name='link' size=30 value='?module='></td></tr>
          <tr><td>Publish</td>    <td> : <input type=radio name='publish' value='Y' checked>Y
                                         <input type=radio name='publish' value='N'>N  </td></tr>
          <tr><td>Pejabat</td>
                <td> : <select name='levelUser'>
                            <option value='0'>- Jabatan User-</option>";
                            while($level = mysql_fetch_array($ambilLevelUser)){
                                echo "<option value='$level[idLevelUser]'>$level[levelUser]</option>";
                            }
            echo "</select></td></tr>
          <tr><td colspan=2>&nbsp;</td></tr>
          <tr><td colspan=2 align=right><input type=submit value=Simpan>&nbsp;&nbsp;&nbsp;
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
          </table></form>";
     break;

  case "editmodul":
    $edit = mysql_query("SELECT * FROM modul WHERE idModul = '$_GET[id]'");
    $data    = mysql_fetch_array($edit);

    echo "<h2>Edit Modul</h2>
          <form method=POST action=./aksi.php?module=modul&act=update>
          <input type=hidden name='idModul' value='$data[idModul]'>
          <table>
          <tr><td>Nama Modul</td>     <td> : <input type=text name='namaModul' value='$data[namaModul]'></td></tr>
          <tr><td>Link</td>     <td> : <input type=text name='link' size=30 value='$data[link]'></td></tr>";
    if ($data[publish]=='Y'){
      echo "<tr><td>Publish</td> <td> : <input type=radio name='publish' value='Y' checked>Y
                                        <input type=radio name='publish' value='N'> N</td></tr>";
    }
    else{
      echo "<tr><td>Publish</td> <td> : <input type=radio name='publish' value='Y'>Y
                                        <input type=radio name='publish' value='N' checked>N</td></tr>";
    }
    echo "<tr><td>Jabatan User</td>
                <td> : <select name='levelUser'>
                            ";
                            while($level = mysql_fetch_array($ambilLevelUser)){
                                if($level[idLevelUser] == $data[idLevelUser]){
                                    echo "<option value='$level[idLevelUser]' selected>$level[levelUser]</option>";
                                }
                                else{
                                    echo "<option value='$level[idLevelUser]'>$level[levelUser]</option>";
                                }
                            }
            echo "</select></td></tr>
    <tr><td colspan=2>&nbsp;</td></tr>
          <tr><td colspan=2><input type=submit value=Update>&nbsp;&nbsp;&nbsp;
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
          </table></form>";
    break;
}

/* CHANGELOG -----------------------------------------------------------

 1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
 0.6.5		    : Gregorius Arief		: initial release

------------------------------------------------------------------------ */
?>
