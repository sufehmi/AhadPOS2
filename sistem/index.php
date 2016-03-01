<?php
/* index.php ------------------------------------------------------
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
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Halaman Login AhadPOS</title>
		<link href="../css/style.css" rel="stylesheet" type="text/css" />
	</head>
	<body>

		<div id="login">
			<div class="header"><img src="../img/logo-login.png" /></div>

			<form method="POST" action="cek_login.php">
				<input type="text" class="form-control" id="username" name="username" placeholder="User Name">
				<input class='form-control' type="password" id="password" name="password" placeholder="Password">
				<input type="submit" class="btn btn-default" class="tombol" id="tombol-login" value="Login">
			</form>
		</div>
	</body>
</html>

<?php
/* CHANGELOG -----------------------------------------------------------


1.0.2 : Gregorius Arief		: initial release

------------------------------------------------------------------------ */
?>
