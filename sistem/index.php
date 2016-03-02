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
require_once($_SERVER["DOCUMENT_ROOT"].'/define.php');
ahp_nojs_header(BRAND_NAME);
?>
<body class='loginpage'>

<div id="login">
<div class="col-lg-6 col-md-6">
<img src="../tacoen/theme/logo-lg.png" />
</div>

<form method="POST" action="cek_login.php" class="col-lg-6 col-md-6">
<div class="form-group">
	<label>Username</labeL>
	<input type="text" class="form-control" id="username" name="username" placeholder="User Name">
</div>
<div class="form-group">
	<label>Password</labeL>
	<input class='form-control' type="password" id="password" name="password" placeholder="Password">
</div>
<div class="form-group">
	<input type="submit" class="btn btn-primary" id="tombol-login" value="Login">
</div>
</form>

<div class="col-lg-12 col-md-12 smallprint">
<p><?php e(BRAND_NAME); ?> &mdash;  Point of Sales &mdash; <?php e(BRAND_OWNER); ?><br>
It's an <a href="http://ahadpos.com/">AhadPOS</a> &mdash; Copyright &copy; 2011 by Rimbalinux.com.</p>
</div>

</div>
</body>
</html>

<?php
/* CHANGELOG -----------------------------------------------------------


1.0.2 : Gregorius Arief		: initial release

------------------------------------------------------------------------ */
?>