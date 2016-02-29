<?php

/* cek_login.php ------------------------------------------------------
  version: 1.0.2

  Part of AhadPOS : http://AhadPOS.com
  License: GPL v2
  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
  http://vlsm.org/etc/gpl-unofficial.id.html

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License v2 (links provided above) for more details.
  ---------------------------------------------------------------- */

include "../config/config.php";
$pass = md5($_POST[password]);

$user = str_replace("'", "0", $_POST[username]);
$user = str_replace("'", "0", $user);
$sql = "SELECT idUser,namaUser,levelUser,uname FROM user u, leveluser lu WHERE u.idLevelUser = lu.idLevelUser and uname='$user' AND pass='$pass'";
$login = mysql_query($sql);

$ketemu = mysql_num_rows($login);
//$ketemu = 1;
$r = mysql_fetch_array($login);

// Apabila username dan password ditemukan
if ($ketemu > 0) {
    session_start();
    #session_register("idUser");
    #session_register("namauser");
    #session_register("passuser");
    #session_register("leveluser");
    #session_register("uname");

    $_SESSION[namauser] = $r[namaUser];
    $_SESSION[iduser] = $r[idUser];
    $_SESSION[leveluser] = $r[levelUser];
    $_SESSION[uname] = $r[uname];


    // cek jika ada upgrade database yang perlu dilakukan
    include "upgrade_check.php";
}
else {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<center>Login gagal! username & password tidak benar<br>";
    echo "<a href=index.php><b>ULANGI LAGI</b></a></center>";
}



/* CHANGELOG -----------------------------------------------------------

  1.5.0 / 2012-11-25 : Harry Sufehmi  	: clean up + upgrade_check.php

  1.0.2  : Gregorius Arief		: initial release

  ------------------------------------------------------------------------ */
?>
