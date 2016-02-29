<?php
/* menu.php ------------------------------------------------------
   	version: 1.0.2

	Part of AhadPOS : http://AhadPOS.com
	License: GPL v2
			http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
			http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License v2 (links provided above) for more details.
----------------------------------------------------------------*/

include "../config/config.php";
include "modul/function.php";
if ($_SESSION[leveluser]=='admin'){
  $sql="select namaModul,link from modul order by urutan ";
}
elseif ($_SESSION[leveluser]=='gudang'){
  $sql="select * from modul m, leveluser lu where m.idLevelUser = lu.idLevelUser and levelUser='gudang' and publish='Y' order by urutan";
}
elseif ($_SESSION[leveluser]=='kasir'){    
  $sql="select * from modul m, leveluser lu where m.idLevelUser = lu.idLevelUser and levelUser='kasir' and publish='Y' order by urutan";
}
else{
  $sql="select * from modul m, leveluser lu where m.idLevelUser = lu.idLevelUser and levelUser='semua' and publish='Y' order by urutan";
} 
//debug 
//echo $_SESSION[leveluser]."-".$sql;
$hasil = mysql_query($sql);

while ($data=mysql_fetch_array($hasil)){  
  echo "<li><a href='$data[link]'>&#187; $data[namaModul]</a></li>";
}

$semua = mysql_query("select * from modul where idLevelUser = 1");
while ($dataSemua=mysql_fetch_array($semua)){
  echo "<li><a href='$dataSemua[link]'>&#187; $dataSemua[namaModul]</a></li>";
}


/* CHANGELOG -----------------------------------------------------------

 1.0.2  : Gregorius Arief		: initial release

------------------------------------------------------------------------ */

?>
