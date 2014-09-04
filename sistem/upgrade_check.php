<?php  
/* upgrade_check.php ------------------------------------------------------
   	version: 1.5.0

	Part of AhadPOS : http://AhadPOS.com
	License: GPL v2
			http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
			http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License v2 (links provided above) for more details.
----------------------------------------------------------------*/

/* -------------------------------------------------------------
This script will automatically upgrade the database 
as required by the current version of AhadPOS  

NO DESTRUCTIVE QUERY ALLOWED HERE
----------------------------------------------------------------*/
// exit;

include "../config/config.php";


// Software Version
// probably a good idea to move these next 3 lines into config.php instead
$major 		= 1;
$minor		= 6;
$revision	= 1;

// serialize this
$current_version	= array($major, $minor, $revision);

// ===============================================================

// get version number from database
$sql	= "SELECT value FROM config WHERE `option` = 'version'";
$hasil	= mysql_query($sql);
$x	= mysql_fetch_array($hasil);

// if no version, means current database structure is from version < 1.5.0
if (mysql_num_rows($hasil) < 1) {

	$dbmajor	= 1;
	$dbminor	= 2;
	$dbrevision	= 0;

} else { // ======= get the major, minor, and revision number 

	$dbversion	= unserialize($x[value]);
	$dbmajor	= $dbversion[0];
	$dbminor	= $dbversion[1];
	$dbrevision	= $dbversion[2];
};


// if up to date, don't do anything at all
if ($major == $dbmajor && $minor == $dbminor && $revision == $dbrevision) { 

	header('location:media.php?module=home');
}


// ---------------------- start upgrading if database version < software version
 
echo "Current database version : $dbmajor.$dbminor.$dbrevision <br />";
echo "Current software version : $major.$minor.$revision <br /><br />";

if ($major >= 1 && $dbmajor <= $major) { 	// ------- eksekusi semua patch versi 1.x
	echo "Checking database version 1.x.x \n <br />";
        check_minor_major1($dbminor, $minor, $dbrevision, $revision);
} else { selesai(); };

if ($major >= 2 && $dbmajor <= $major) { 	// ------- eksekusi semua patch versi 2.x
        echo "Checking database version 2.x.x \n <br />";
        check_minor_major2($dbminor, $minor, $dbrevision, $revision);
} else { selesai(); }

if ($major >= 3 && $dbmajor <= $major) { 	// ------- eksekusi semua patch versi 3.x
        echo "Checking database version 3.x.x \n <br />";
	check_minor_major3($dbminor, $minor, $dbrevision, $revision);
} else { selesai(); }



exit;

	

// =================================== PATCH VERSI 1.x.x ==========================================
function check_minor_major1($dbminor, $minor, $dbrevision, $revision) {

	if ($minor <= 2 && $dbminor <= $minor) {	// ------- eksekusi semua patch versi 1.2.x 
	        echo "Upgrading database to version 1.2.x \n <br />";
		check_revision_minor2_major1($dbminor, $minor, $dbrevision, $revision);
	}

	if ($minor <= 5 && $dbminor <= $minor) { 	// ------- eksekusi semua patch versi 1.5.x
                echo "Upgrading database to version 1.5.x \n <br />";
		check_revision_minor5_major1($dbminor, $minor, $dbrevision, $revision);
	}

	if ($minor <= 6 && $dbminor <= $minor) { 	// ------- eksekusi semua patch versi 1.6.x
		if ($dbrevision < $revision) {
	                echo "Upgrading database to version 1.6.x \n <br />";
			check_revision_minor6_major1($dbminor, $minor, $dbrevision, $revision);
		}
	}
}


function check_revision_minor2_major1($dbminor, $minor, $dbrevision, $revision) {

        echo "Upgrading database to version 1.2.0 \n <br />";
	upgrade_old_to_120();

        echo "Upgrading database from 1.2.0 to version 1.2.5 \n <br />";
	upgrade_120_to_125();
}


function check_revision_minor5_major1($dbminor, $minor, $dbrevision, $revision) {

        echo "Upgrading database from 1.2.5 to version 1.5.0 \n <br />";
	upgrade_125_to_150();
}

function check_revision_minor6_major1($dbminor, $minor, $dbrevision, $revision) {

	// upgrade 1.5.x ke 1.6.0
        if ($dbminor == '5') {
		echo "Upgrading database from 1.5.0 to version 1.6.0 \n <br />";
		upgrade_150_to_160();
	};

	// upgrade 1.6.0 ke 1.6.x
	//if (($dbminor == $minor) && ($dbrevision < $revision)) {
		echo "Upgrading database from 1.6.0 to version 1.6.x \n <br />";
		upgrade_160_to_161();
	//};
}


// ------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------

function upgrade_old_to_120() {

	// nothing to do here
}


function upgrade_120_to_125() {


        // database structure upgrade -------------------------------------------------
	$sql = "ALTER TABLE  `kategori_barang` CHANGE  `idKategoriBarang`  `idKategoriBarang` INT( 5 ) NOT NULL AUTO_INCREMENT";
	$hasil	= exec_query($sql);

        // optimizations --------------------------------------------------------------
        $sql = "ALTER TABLE  `barang` ADD INDEX (`idKategoriBarang`);
		ALTER TABLE  `barang` ADD INDEX (`idSupplier`);
		ALTER TABLE  `barang` ADD FULLTEXT (`namaBarang`);

		ALTER TABLE  `detail_beli` ADD INDEX (`isSold`);
		ALTER TABLE  `detail_beli` ADD INDEX (`jumBarang`);
		ALTER TABLE  `detail_beli` ADD INDEX (`idBarang`);
		ALTER TABLE  `detail_beli` ADD INDEX (`barcode`);

		ALTER TABLE  `detail_jual` ADD INDEX (`username`);
		ALTER TABLE  `detail_jual` ADD INDEX (`nomorStruk`);
		ALTER TABLE  `detail_jual` ADD INDEX (`barcode`);

		ALTER TABLE  `tmp_detail_beli` ADD INDEX (`idSupplier`);
		ALTER TABLE  `tmp_detail_beli` ADD INDEX (`username`);

		ALTER TABLE  `transaksijual` ADD INDEX (`idUser`);
		ALTER TABLE  `transaksijual` ADD INDEX (`tglTransaksiJual`);
		ALTER TABLE  `transaksijual` ADD INDEX (`nominal`);
		";
        $hasil  = exec_query($sql);
	echo mysql_error();

        // update version number ------------------------------------------------------
	$sql 	= "SELECT * FROM config WHERE `option` = 'version'";
	$hasil	= mysql_query($sql);

	if (mysql_num_rows($hasil) > 0) {
	        $sql = "UPDATE `config` SET value = '".serialize(array(1,2,5))."' WHERE `option` = 'version'";
	} else {
		$sql  = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '".serialize(array(1,2,5))."', '')";
	};
        $hasil  = mysql_query($sql);

}


function upgrade_125_to_150() {

        // database structure upgrade -------------------------------------------------
        $sql = "
		ALTER TABLE `modul` ADD `script_name` VARCHAR( 50 ) NOT NULL;
		ALTER TABLE `modul` ADD INDEX (`script_name`);

                UPDATE `modul` SET `script_name` = 'mod_user.php' WHERE `modul`.`link` = '?module=user' ;
                UPDATE `modul` SET `script_name` = 'mod_supplier.php' WHERE `modul`.`link` = '?module=supplier' ;
                UPDATE `modul` SET `script_name` = 'mod_customer.php' WHERE `modul`.`link` = '?module=customer' ;
                UPDATE `modul` SET `script_name` = 'mod_barang.php' WHERE `modul`.`link` = '?module=barang' ;
                UPDATE `modul` SET `script_name` = 'mod_rak.php' WHERE `modul`.`link` = '?module=rak' ;
                UPDATE `modul` SET `script_name` = 'mod_satuan_barang.php' WHERE `modul`.`link` = '?module=satuan_barang' ;
                UPDATE `modul` SET `script_name` = 'mod_kategori_barang.php' WHERE `modul`.`link` = '?module=kategori_barang' ;
                UPDATE `modul` SET `script_name` = 'mod_beli_barang.php' WHERE `modul`.`link` = '?module=pembelian_barang' ;
                UPDATE `modul` SET `script_name` = 'mod_jual_barang.php' WHERE `modul`.`link` = '?module=penjualan_barang' ;
                UPDATE `modul` SET `script_name` = 'mod_hutang.php' WHERE `modul`.`link` = '?module=hutang' ;
                UPDATE `modul` SET `script_name` = 'mod_piutang.php' WHERE `modul`.`link` = '?module=piutang' ;
                UPDATE `modul` SET `script_name` = 'mod_modul.php' WHERE `modul`.`link` = '?module=modul' ;
                UPDATE `modul` SET `script_name` = 'mod_kasir.php' WHERE `modul`.`link` = '?module=kasir' ;
                UPDATE `modul` SET `script_name` = 'mod_laporan.php' WHERE `modul`.`link` = '?module=laporan' ;
                UPDATE `modul` SET `script_name` = 'mod_manage_workstation.php' WHERE `modul`.`link` = '?module=workstation' ;

		";
        $hasil  = exec_query($sql);
	echo mysql_error();

        // optimizations --------------------------------------------------------------
        // no optimizations for 1.2.5 --> 1.5.0
	//$sql = "";
        //$hasil  = exec_query($sql);

        // update version number ------------------------------------------------------
        $sql    = "SELECT * FROM config WHERE `option` = 'version'";
        $hasil  = mysql_query($sql);

        if (mysql_num_rows($hasil) > 0) {
                $sql = "UPDATE `config` SET value = '".serialize(array(1,5,0))."' WHERE `option` = 'version'";
        } else {
                $sql  = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '".serialize(array(1,5,0))."', '')";
        };
        $hasil  = mysql_query($sql);

}


function upgrade_150_to_160() {

	$sql	= "alter table modul add index(idLevelUser);";
        $hasil  = exec_query($sql);
	echo mysql_error();

	$sql	= "alter table modul add index(publish);";
        $hasil  = exec_query($sql);
	echo mysql_error();

	$sql	= "alter table leveluser add index (idLevelUser);";
        $hasil  = exec_query($sql);
	echo mysql_error();

	$sql	= "alter table leveluser add index (levelUser);";
        $hasil  = exec_query($sql);
	echo mysql_error();

	$sql	= "ALTER TABLE `supplier` ADD `interval` INT NOT NULL DEFAULT '7'";
        $hasil  = exec_query($sql);
	echo mysql_error();


        // update version number ------------------------------------------------------
        $sql    = "SELECT * FROM config WHERE `option` = 'version'";
        $hasil  = mysql_query($sql);

        if (mysql_num_rows($hasil) > 0) {
                $sql = "UPDATE `config` SET value = '".serialize(array(1,6,0))."' WHERE `option` = 'version'";
        } else {
                $sql  = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '".serialize(array(1,6,0))."', '')";
        };
        $hasil  = mysql_query($sql);

}


function upgrade_160_to_161() {

	$sql 	= "CREATE TABLE IF NOT EXISTS `arsip_barang` (
			`idBarang` bigint(20) NOT NULL DEFAULT '0',
			`namaBarang` varchar(30) DEFAULT ' ',
			`idKategoriBarang` int(5) DEFAULT '0',
  			`idSatuanBarang` int(5) DEFAULT '0',
			`jumBarang` int(10) DEFAULT '0',
			`hargaJual` bigint(20) DEFAULT '0',
			`last_update` date DEFAULT '2000-01-01',
			`idSupplier` bigint(20) DEFAULT '0',
			`barcode` varchar(25) DEFAULT NULL,
			`username` varchar(30) DEFAULT NULL,
			`idRak` bigint(5) DEFAULT NULL,
			  UNIQUE KEY `barcode` (`barcode`),
			  KEY `idKategoriBarang` (`idKategoriBarang`),
			  KEY `namaBarang` (`namaBarang`),
			  KEY `idSupplier` (`idSupplier`),
			  KEY `idKategoriBarang_2` (`idKategoriBarang`),
			  KEY `idSupplier_2` (`idSupplier`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
        $hasil  = exec_query($sql);
	echo mysql_error();

	$sql	= "CREATE TABLE IF NOT EXISTS `tmp_cetak_label_perbarcode` (
			`id` int(12) NOT NULL AUTO_INCREMENT,
  			`tmpBarcode` varchar(50) DEFAULT NULL,
			`tmpNama` varchar(100) DEFAULT NULL,
			`tmpKategori` varchar(50) DEFAULT NULL,
			`tmpSatuan` varchar(50) DEFAULT NULL,
			`tmpJumlah` varchar(100) DEFAULT NULL,
			`tmpHargaJual` varchar(100) DEFAULT NULL,
			`tmpIdBarang` int(12) DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9;";
        $hasil  = exec_query($sql);
	echo mysql_error();

        // update version number ------------------------------------------------------
        $sql    = "SELECT * FROM config WHERE `option` = 'version'";
        $hasil  = mysql_query($sql);

        if (mysql_num_rows($hasil) > 0) {
                $sql = "UPDATE `config` SET value = '".serialize(array(1,6,1))."' WHERE `option` = 'version'";
        } else {
                $sql  = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '".serialize(array(1,6,1))."', '')";
        };
        $hasil  = mysql_query($sql);

}



// =================================== PATCH VERSI 2.x.x ==========================================
function check_minor_major2($dbminor, $minor, $dbrevision, $revision) {

	// nothing here yet
}



// =================================== PATCH VERSI 3.x.x ==========================================
function check_minor_major3($dbminor, $minor, $dbrevision, $revision) {

        // nothing here yet
}

// ==================== general functions ==============================

function exec_query($sql) {
// able to loop through & execute MULTIPLE query lines

	$queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sql); 
	foreach ($queries as $query){ 
		if (strlen(trim($query)) > 0) mysql_query($query); 
	} 
}



function selesai() {

	echo "Database upgrade finished, thank you. <br /> Silakan <a href=index.php> <b>LOGIN</b> </a> lagi.";
	exit;
}




/* CHANGELOG -----------------------------------------------------------

 1.6.0 / 2013-02-07  : Harry Sufehmi	: table arsip_barang

 1.5.0 / 2012-11-25  : Harry Sufehmi	: initial release

------------------------------------------------------------------------ */
// sql untuk update ke design baru
$sql = "

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `link` varchar(1000) NOT NULL,
  `icon` varchar(45) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `label` varchar(100) NOT NULL,
  `accesskey` varchar(1) DEFAULT NULL,
  `publish` enum('Y','N') NOT NULL,
  `level_user_id` int(11) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT '1',
  `level` int(11) NOT NULL DEFAULT '0',
  `last_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;


INSERT INTO `menu` (`id`, `nama`, `link`, `icon`, `parent_id`, `label`, `accesskey`, `publish`, `level_user_id`, `urutan`, `level`, `last_update`) VALUES
(1, 'Home', 'media.php?module=home', 'fa fa-home fa-4x', 0, '<span class=\"u\">H</span>ome', 'h', 'Y', 1, 1, 0, NULL),
(2, 'Barang', 'media.php?module=barang', 'fa fa-barcode fa-4x', 0, '<span class=\"u\">B</span>arang', 'b', 'Y', 3, 2, 0, NULL),
(3, 'Pembelian', 'media.php?module=pembelian_barang', 'fa fa-truck fa-4x', 0, '<span class=\"u\">P</span>embelian', 'p', 'Y', 3, 3, 0, NULL),
(4, 'Kasir', 'media.php?module=kasir', 'fa fa-shopping-cart fa-4x', 0, '<span class=\"u\">K</span>asir', 'k', 'Y', 3, 4, 0, NULL),
(5, 'Laporan', 'media.php?module=laporan', 'fa fa-bar-chart-o fa-4x', 0, '<span class=\"u\">L</span>aporan', 'l', 'Y', 2, 5, 0, NULL),
(6, 'Stock Opname', 'media.php?module=stockop', 'fa fa-check-square-o fa-4x', 0, 'Stock <span class=\"u\">O</span>p', 'o', 'Y', 3, 5, 0, NULL),
(7, 'Settings', 'media.php?module=setting', 'fa fa-wrench fa-4x', 0, 'Se<span class=\"u\">t</span>tings', 't', 'Y', 2, 7, 0, NULL),
(8, 'Logout', 'logout.php', 'fa fa-power-off fa-4x', 0, 'Lo<span class=\"u\">g</span>out', 'g', 'Y', 1, 8, 0, NULL),
(9, 'Satuan Barang', 'media.php?module=satuan_barang', '', 2, '<span class=\"u\">S</span>atuan Barang', 's', 'Y', 3, 1, 0, NULL),
(10, 'Menu', 'media.php?module=menu', '', 7, 'Menu', '', 'Y', 2, 4, 0, NULL),
(11, 'Supplier', 'media.php?module=supplier', '', 7, 'Supplier', '', 'Y', 3, 1, 0, NULL),
(12, 'Kategori Barang', 'media.php?module=kategori_barang', '', 2, '<span class=\"u\">K</span>ategori Barang', 'k', 'Y', 3, 2, 0, NULL),
(13, 'Rak Barang', 'media.php?module=rak', '', 2, '<span class=\"u\">R</span>ak Barang', 'r', 'Y', 3, 3, 0, NULL),
(14, 'Tambah Barang', 'media.php?module=barang&act=tambahbarang', '', 2, '<span class=\"u\">T</span>ambah Barang', 't', 'Y', 3, 4, 0, NULL),
(15, 'Cari Barang', 'media.php?module=barang&act=caribarang1', '', 2, '<span class=\"u\">C</span>ari Barang', 'c', 'Y', 3, 5, 0, NULL),
(16, 'Cetak Label per Rak', 'media.php?module=barang&act=cetaklabel1', '', 2, 'Cetak <span class=\"u\">L</span>abel per Rak', 'l', 'Y', 3, 6, 0, NULL),
(17, 'Transfer Barang Antar Ahad', 'media.php?module=barang&act=transfer1', '', 2, 'Trans<span class=\"u\">f</span>er Barang Antar Ahad', 'f', 'Y', 3, 7, 0, NULL),
(18, 'Input Rak Barang', 'media.php?module=barang&act=inputrak', '', 2, 'Input <span class=\"u\">R</span>ak Barang', 'r', 'Y', 3, 8, 0, NULL),
(19, 'Pembelian Barang', 'media.php?module=pembelian_barang&act=pembelianbarang', '', 3, 'P<span class=\"u\">e</span>mbelian Barang', 'e', 'Y', 3, 1, 0, NULL),
(20, 'Retur Pembelian', 'media.php?module=pembelian_barang&act=returpembelian', '', 3, '<span class=\"u\">R</span>etur Pembelian', 'r', 'Y', 3, 2, 0, NULL),
(21, 'Cetak Nota Retur', 'media.php?module=pembelian_barang&act=cetakretur', '', 3, '<span class=\"u\">C</span>etak Nota Retur', 'c', 'Y', 3, 3, 0, NULL),
(22, 'Input pembelian elektronik', 'media.php?module=pembelian_barang&act=inputeprocurement1', '', 3, '<span class=\"u\">I</span>nput Pembelian Elektronik', 'i', 'Y', 3, 4, 0, NULL),
(23, 'Input RPO per item', 'media.php?module=pembelian_barang&act=buatrpo1', '', 3, 'Input RPO (per I<span class=\"u\">t</span>em)', 't', 'Y', 3, 5, 0, NULL),
(24, 'Input RPO per Supplier', 'media.php?module=pembelian_barang&act=rposup1', '', 3, 'Input <span class=\"u\">R</span>PO per Supplier', 'r', 'Y', 3, 6, 0, NULL),
(25, 'Buka kasir', 'media.php?module=kasir&act=bukakasir', '', 4, 'B<span class=\"u\">u</span>ka Kasir', 'u', 'Y', 3, 1, 0, NULL),
(26, 'Tutup kasir', 'media.php?module=kasir&act=tutupkasir', '', 4, 'Tutu<span class=\"u\">p</span> Kasir', 'p', 'Y', 3, 2, 0, NULL),
(27, 'Penambahan Dana', 'media.php?module=kasir&act=tambahdana', '', 4, 'Penambahan <span class=\"u\">D</span>ana', 'd', 'Y', 3, 3, 0, NULL),
(28, 'Penjualan', 'media.php?module=penjualan_barang', '', 4, 'Pen<span class=\"u\">j</span>ualan', 'j', 'Y', 4, 4, 0, NULL),
(29, 'User', 'media.php?module=user', '', 7, 'User', '', 'Y', 2, 3, 0, NULL),
(34, 'Customer', 'media.php?module=customer', '', 7, 'Customer', '', 'Y', 4, 2, 0, NULL),
(35, 'Workstation', 'media.php?module=workstation', '', 7, 'Workstation', '', 'Y', 2, 5, 0, NULL),
(36, 'Personal Info', 'media.php?module=ganti_password', '', 7, 'Personal Info', '', 'Y', 2, 6, 0, NULL),
(37, 'Laporan Pemb Brg / tgl', 'media.php?module=pembelian_barang&act=laporanpembeliantanggal', '', 5, 'Pembelian Barang per Tanggal', '', 'Y', 2, 1, 0, NULL),
(38, 'Laporan Pemb Brg / sup', 'media.php?module=pembelian_barang&act=laporanpembelian', '', 5, 'Pembelian Barang per Supplier', '', 'Y', 2, 2, 0, NULL),
(39, 'Laporan Penjualan', 'media.php?module=laporan&act=penjualan1', '', 5, 'Penjualan', '', 'Y', 2, 3, 0, NULL),
(40, 'Total Stock', 'media.php?module=laporan&act=total1', '', 5, 'Total Stock', '', 'Y', 2, 4, 0, NULL),
(41, 'Top Rank', 'media.php?module=laporan&act=toprank1', '', 5, 'Top Rank', '', 'Y', 2, 5, 0, NULL),
(42, 'Aging', 'media.php?module=laporan&act=aging1', '', 5, 'Aging Stock', '', 'Y', 2, 6, 0, NULL),
(43, 'Cetak Stock Barang', 'media.php?module=barang&act=cetakbarang1', '', 6, 'Cetak Stock Barang', '', 'Y', 3, 1, 0, NULL),
(44, 'Cetak Form Stock Op', 'media.php?module=barang&act=cetakSO', '', 6, 'Cetak Form Stock Opname', '', 'Y', 3, 2, 0, NULL),
(45, 'Input SO Manual', 'media.php?module=barang&act=inputSO', '', 6, 'Input SO Manual', '', 'Y', 3, 3, 0, NULL),
(46, 'Fast SO', '../tools/fast-stock-opname/fast-SO.php', '', 6, 'Input Fast SO', '', 'Y', 3, 4, 0, NULL),
(47, 'Approve Fast SO', 'media.php?module=barang&act=ApproveFastSO1', '', 6, 'Approve Fast SO', '', 'Y', 3, 5, 0, NULL),
(48, 'Input Mobil SO', '../tools/fast-stock-opname/fast-SO-mobile.php', '', 6, 'Input Mobile SO', '', 'Y', 3, 6, 0, NULL),
(49, 'Approve Mobile SO', 'media.php?module=barang&act=ApproveMobileSO1', '', 6, 'Approve Mobile SO', '', 'Y', 3, 7, 0, NULL);

";



?>
