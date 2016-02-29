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

include_once('../tacoen/function.php');
session_start();
check_ahadpos_session();
ahp_htmlheader('Halaman Awal');
echo "<body class=''>\n";
require_once("menu2.php");?>

<main id="content">
<?php include "content.php"; ?>
<div class="clearfix"></div>

<footer>
<div><small><a href="http://ahadpos.com/">AhadPOS</a>Copyright &copy; 2011 by Rimbalinux.com ::Tim Support IT::</small></div>
</footer>

</main>
</body>
</html>

<?php


/* CHANGELOG -----------------------------------------------------------
: Abu Muhammad : Penggantian Menu dengan desain baru

1.0.2 : Gregorius Arief		: initial release

------------------------------------------------------------------------ */
?>
