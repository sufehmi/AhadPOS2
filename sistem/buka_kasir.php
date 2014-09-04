<?php
/* buka_kasir.php ------------------------------------------------------
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


session_start();
if (empty($_SESSION[namauser]) AND empty($_SESSION[passuser])){
  echo "<link href='../config/adminstyle.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=index.php><b>LOGIN</b></a></center>";
}
else{
?>

<html>
<head>
<title>Halaman Login AhadPOS</title>
<link href="../config/adminstyle.css" rel="stylesheet" type="text/css" />
</head>
<?php
    $tglHariIni = date("Y-m-d");

?>
<body>

  <div id="login">
		<h2>Menu Buka Kasir</h2>


<form method="POST" action="aksi.php?module=transaksi_kas&act=input">
<input type="hidden" name="idUser" value=<?php echo"$_SESSION[iduser]"; ?>>
<table>
<tr><td>User</td><td> : <?php echo "$_SESSION[namauser]"; ?></td></tr>
<tr><td>Tanggal</td><td> : <?php echo"$tglHariIni"; ?></td></tr>
<tr><td>Uang Kas Awal</td><td> : <input type="text" name="kasAwal"></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" align="center"><a href="logout.php">BATAL</a>&nbsp;&nbsp;&nbsp;<input type="submit" value="Masuk"></td></tr>
</table>
</form>

<p>&nbsp;</p>
  </div>


</body>
</html>

<?php
}



/* CHANGELOG -----------------------------------------------------------

 1.0.2  : Gregorius Arief		: initial release

------------------------------------------------------------------------ */

?>
