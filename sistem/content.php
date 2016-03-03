<?php
/* content.php ------------------------------------------------------
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

include SITE_ROOT."config/library.php";
include SITE_ROOT."config/fungsi_indotgl.php";
include SITE_ROOT."config/fungsi_combobox.php";
include SITE_ROOT."config/class_paging.php";
include_once SITE_ROOT."sistem/modul/function.php";

switch($_GET['module']) {
	case 'home':
		ahad_homepage();
		break;		
	case 'user':
		include SITE_ROOT."sistem/modul/mod_user.php";
		break;		
	case 'modul':
		include SITE_ROOT."sistem/modul/mod_modul.php";
		break;		
	case 'menu':
		include SITE_ROOT."sistem/modul/mod_menu.php";
		break;		
	case 'satuan_barang':
		include SITE_ROOT."sistem/modul/mod_satuan_barang.php";
		break;		
	case 'kategori_barang':
		include SITE_ROOT."sistem/modul/mod_kategori_barang.php";
		break;		
	case 'rak':
		include SITE_ROOT."sistem/modul/mod_rak.php";
		break;		
	case 'barang':
		include SITE_ROOT."sistem/modul/mod_barang.php";
		break;
	case 'supplier':
		include SITE_ROOT."sistem/modul/mod_supplier.php";
		break;		
	case 'customer':
		include SITE_ROOT."sistem/modul/mod_customer.php";
		break;		
	case 'pembelian_barang':
		include SITE_ROOT."sistem/modul/mod_beli_barang.php";
		break;		
	case 'penjualan_barang':
		include SITE_ROOT."sistem/modul/mod_jual_barang.php";
		break;		
	case 'hutang':
		include SITE_ROOT."sistem/modul/mod_hutang.php";
		break;		
	case 'piutang':
		include SITE_ROOT."sistem/modul/mod_piutang.php";
		break;		
	case 'kasir':
		include SITE_ROOT."sistem/modul/mod_kasir.php";
		break;		
	case 'laporan':
		include SITE_ROOT."sistem/modul/mod_laporan.php";
		break;		
	case 'workstation':
		include SITE_ROOT."sistem/modul/mod_manage_workstation.php";
		break;
	case 'ganti_password':
		include SITE_ROOT."sistem/modul/mod_user.php";
		break;		
	case 'system':
		include SITE_ROOT."sistem/modul/mod_system.php";
		break;
}

/* CHANGELOG -----------------------------------------------------------

1.0.2 : Gregorius Arief		: initial release

------------------------------------------------------------------------ */
?>
