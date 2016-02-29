<?php
/* ajax-check-dup-barcode.php ------------------------------------------------------
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

	include "../../config/config.php";

$barcode = $_POST['barcode']; // get the barcode
$barcode = trim(htmlentities($barcode)); // strip some crap out of it

echo check_barcode($barcode); // call the check_barcode function and echo the results.

function check_barcode($barcode){

	$sql = "SELECT * FROM barang WHERE barcode='$barcode'";
	$hasil = mysql_query($sql);
	if (mysql_num_rows($hasil) > 0) {
		return '<span style="color:#f00">ERROR: Barcode Sudah Ada !</span>';
	};

	return '<span style="color:#0c0">Barcode baru, OK!</span>';
}

/* CHANGELOG -----------------------------------------------------------

 1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
 0.7.5		    : Harry Sufehmi		: initial release

------------------------------------------------------------------------ */

?>
