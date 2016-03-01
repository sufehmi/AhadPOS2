<?php
/* media.php ------------------------------------------------------
version: 1.0.2

Part of AhadPOS : http://AhadPOS.com
License: GPL v2
http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License v2 (links provided above) for more details.
---------------------------------------------------------------- */

session_start();

require_once($_SERVER["DOCUMENT_ROOT"].'/define.php');

if (empty($_SESSION[namauser]) AND empty($_SESSION[passuser])) {
	echo "<link href='../css/style.css' rel='stylesheet' type='text/css'>
		<center>Untuk mengakses modul, Anda harus login <br>";
	echo "<a href=index.php><b>LOGIN</b></a></center>";
} else {
	?>
	<!DOCTYPE html>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Halaman AhadPOS</title>

			<link rel="stylesheet" type="text/css" href="../css/jquery-ui-ac.min.css" />
			<link rel="stylesheet" type="text/css" href="../css/style.css" />
			<link rel="stylesheet" type="text/css" href="../css/jquery.simple-dtpicker.css" />

			<script type="text/javascript" src="../js/jquery.min.js"></script>
			<script type="text/javascript" src="../js/interface.js"></script>
			<script type="text/javascript" src="../js/jquery.form.min.js"></script>
			<script type="text/javascript" src="../js/jquery.simple-dtpicker.js"></script>

		</head>
		<body>
			<div class="container" id="container">
				<div id="header">
					<div id="mainmenu">
						<?php include SITE_ROOT."sistem/menu2.php"; ?>
						<span id="logo"><img src="../img/logo.png" /></span>
					</div>
				</div>

				<div id="content">
					<?php include SITE_ROOT."sistem/content.php"; ?>
				</div>

				<div class="clear"></div>

				<div id="footer">
					<span><a href="http://ahadpos.com/">AhadPOS</a> Copyright &copy; 2011 by Rimbalinux.com ::Tim Support IT::</span>
				</div>
				<?php // Mengubah margin container menyesuaikan dengan tinggi menu level 2 (jika kepanjangan maka menu level 2 akan turun) ?>
				<script>
					var menu=document.getElementById("menu-level-2");
					console.log(menu.clientHeight);
					margin=parseInt(menu.clientHeight) + 95;
					var container=document.getElementById("container");
					container.setAttribute("style", "margin-top: " + margin + "px");
				</script>
			</div>

		</body>
	</html>

	<?php
}



/* CHANGELOG -----------------------------------------------------------
: Abu Muhammad : Penggantian Menu dengan desain baru

1.0.2 : Gregorius Arief		: initial release

------------------------------------------------------------------------ */
?>
