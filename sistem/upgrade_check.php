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
  ---------------------------------------------------------------- */

/* -------------------------------------------------------------
  This script will automatically upgrade the database
  as required by the current version of AhadPOS

  NO DESTRUCTIVE QUERY ALLOWED HERE
  ---------------------------------------------------------------- */
// exit;

include "../config/config.php";


// Software Version
// probably a good idea to move these next 3 lines into config.php instead
$major = 2;
$minor = 0;
$revision = 8;

// serialize this
$current_version = array($major, $minor, $revision);

// ===============================================================
// get version number from database
$sql = "SELECT value FROM config WHERE `option` = 'version'";
$hasil = mysql_query($sql);
$x = mysql_fetch_array($hasil);

// if no version, means current database structure is from version < 1.5.0
if (mysql_num_rows($hasil) < 1) {

    $dbmajor = 1;
    $dbminor = 2;
    $dbrevision = 0;
}
else { // ======= get the major, minor, and revision number
    $dbversion = unserialize($x[value]);
    $dbmajor = $dbversion[0];
    $dbminor = $dbversion[1];
    $dbrevision = $dbversion[2];
};


// if up to date, don't do anything at all
if ($major == $dbmajor && $minor == $dbminor && $revision == $dbrevision) {
    header('location:media.php?module=home');
}
else {


// ---------------------- start upgrading if database version < software version

    echo "Current database version : $dbmajor.$dbminor.$dbrevision <br />";
    echo "Current software version : $major.$minor.$revision <br /><br />";

    if ($dbmajor == 1) {  // ------- eksekusi semua patch versi 1.x
        echo "Checking database version 1.x.x \n <br />";
        check_minor_major1($dbminor, $minor, $dbrevision, $revision);
    }
//    else {
//        selesai();
//    };

    if ($major == 2 && $dbmajor == 1) {  // ------- upgrade dari versi 1.6.1 ke versi 2.0.0
        echo "Upgrading to database version 2.0.0 \n <br />";
        upgrade_161_to_200();
    }
//    else {
//        selesai();
//    }

    if (($major == 2) && ($dbmajor == 2)) {  // ------- eksekusi semua patch versi 2.x
        echo "Checking database version 2.x.x \n <br />";
        check_minor_major2($dbminor, $minor, $dbrevision, $revision);
    }
    else {
        selesai();
    }

    if ($major >= 3 && $dbmajor <= $major) {  // ------- eksekusi semua patch versi 3.x
        echo "Checking database version 3.x.x \n <br />";
        check_minor_major3($dbminor, $minor, $dbrevision, $revision);
    }
    else {
        selesai();
    }



    exit;
}

// =================================== PATCH VERSI 1.x.x ==========================================
function check_minor_major1($dbminor, $minor, $dbrevision, $revision) {

    if ($dbminor == 2) { // ------- eksekusi semua patch versi 1.2.x
        echo "Upgrading database to version 1.2.x \n <br />";
        check_revision_minor2_major1($dbminor, $minor, $dbrevision, $revision);
    }

    if ($dbminor < 5) {  // ------- eksekusi semua patch versi 1.5.x
        echo "Upgrading database to version 1.5.x \n <br />";
        check_revision_minor5_major1($dbminor, $minor, $dbrevision, $revision);
    }

    if ($dbminor < 6) {  // ------- eksekusi semua patch versi 1.6.x
        if ($dbrevision < 1) {
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
    if ($dbrevision < 1) {
        echo "Upgrading database from 1.6.0 to version 1.6.1 \n <br />";
        upgrade_160_to_161();
    };
}

// ------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------

function upgrade_old_to_120() {

    // nothing to do here
}

function upgrade_120_to_125() {


    // database structure upgrade -------------------------------------------------
    $sql = "ALTER TABLE  `kategori_barang` CHANGE  `idKategoriBarang`  `idKategoriBarang` INT( 5 ) NOT NULL AUTO_INCREMENT";
    $hasil = exec_query($sql);

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
    $hasil = exec_query($sql);
    echo mysql_error();

    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(1, 2, 5)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(1, 2, 5)) . "', '')";
    };
    $hasil = mysql_query($sql);
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
    $hasil = exec_query($sql);
    echo mysql_error();

    // optimizations --------------------------------------------------------------
    // no optimizations for 1.2.5 --> 1.5.0
    //$sql = "";
    //$hasil  = exec_query($sql);
    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(1, 5, 0)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(1, 5, 0)) . "', '')";
    };
    $hasil = mysql_query($sql);
}

function upgrade_150_to_160() {

    $sql = "alter table modul add index(idLevelUser);";
    $hasil = exec_query($sql);
    echo mysql_error();

    $sql = "alter table modul add index(publish);";
    $hasil = exec_query($sql);
    echo mysql_error();

    $sql = "alter table leveluser add index (idLevelUser);";
    $hasil = exec_query($sql);
    echo mysql_error();

    $sql = "alter table leveluser add index (levelUser);";
    $hasil = exec_query($sql);
    echo mysql_error();

    $sql = "ALTER TABLE `supplier` ADD `interval` INT NOT NULL DEFAULT '7'";
    $hasil = exec_query($sql);
    echo mysql_error();


    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(1, 6, 0)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(1, 6, 0)) . "', '')";
    };
    $hasil = mysql_query($sql);
}

function upgrade_160_to_161() {

    $sql = "CREATE TABLE IF NOT EXISTS `arsip_barang` (
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
    $hasil = exec_query($sql);
    echo mysql_error();

    $sql = "CREATE TABLE IF NOT EXISTS `tmp_cetak_label_perbarcode` (
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
    $hasil = exec_query($sql);
    echo mysql_error();

    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(1, 6, 1)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(1, 6, 1)) . "', '')";
    };
    $hasil = mysql_query($sql);
}

// =================================== PATCH VERSI 2.x.x ==========================================
function check_minor_major2($dbminor, $minor, $dbrevision, $revision) {

    if ($minor == 0) { // ------- eksekusi semua patch versi 2.0.x
        echo "Upgrading database to version 2.0.x \n <br />";
        check_revision_minor0_major2($dbminor, $minor, $dbrevision, $revision);
    }
}

function check_revision_minor0_major2($dbminor, $minor, $dbrevision, $revision) {
    if ($dbrevision < 1) {
        echo "Upgrading database to version 2.0.1 <br />";
        upgrade_200_to_201();
    }
    if ($dbrevision < 2) {
        echo "Upgrading database to version 2.0.2 <br />";
        upgrade_201_to_202();
    }
    if ($dbrevision < 3) {
        echo "Upgrading database to version 2.0.3 <br />";
        upgrade_202_to_203();
    }
    if ($dbrevision < 4) {
        echo "Upgrading database to version 2.0.4 <br />";
        upgrade_203_to_204();
    }
    if ($dbrevision < 5) {
        echo "Upgrading database to version 2.0.5 <br />";
        upgrade_204_to_205();
    }
    if ($dbrevision < 6) {
        echo "Upgrading database to version 2.0.6 <br />";
        upgrade_205_to_206();
    }
    if ($dbrevision < 7) {
        echo "Upgrading database to version 2.0.7 <br />";
        upgrade_206_to_207();
    }
    if ($dbrevision < 8) {
        echo "Upgrading database to version 2.0.8 <br />";
        upgrade_207_to_208();
    }
}

function upgrade_161_to_200() {

    /* Create Tabel Menu */
    $sql = "CREATE TABLE IF NOT EXISTS `menu` (
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
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1
		";
    $hasil = exec_query($sql);
    echo mysql_error();

    /* Isi tabel menu */
    $sql = "INSERT INTO `menu` (`id`, `nama`, `link`, `icon`, `parent_id`, `label`, `accesskey`, `publish`, `level_user_id`, `urutan`, `level`, `last_update`) VALUES
			(1, 'Home', 'media.php?module=home', 'fa fa-home fa-4x', 0, 'Home', '', 'Y', 1, 1, 0, ''),
			(2, 'Barang', 'media.php?module=barang', 'fa fa-barcode fa-4x', 0, 'Barang', '', 'Y', 3, 2, 0, ''),
			(3, 'Pembelian', 'media.php?module=pembelian_barang', 'fa fa-truck fa-4x', 0, 'Pembelian', '', 'Y', 3, 3, 0, ''),
			(4, 'Kasir', 'media.php?module=kasir', 'fa fa-shopping-cart fa-4x', 0, 'Kasir', '', 'Y', 3, 4, 0, ''),
			(5, 'Laporan', 'media.php?module=laporan', 'fa fa-bar-chart-o fa-4x', 0, 'Laporan', '', 'Y', 2, 5, 0, ''),
			(6, 'Stock Opname', 'media.php?module=barang&act=cetakbarang1', 'fa fa-check-square-o fa-4x', 0, 'Stock Op', '', 'Y', 3, 5, 0, ''),
			(7, 'Settings', 'media.php?module=ganti_password', 'fa fa-wrench fa-4x', 0, 'Settings', '', 'Y', 2, 7, 0, ''),
			(8, 'Logout', 'logout.php', 'fa fa-power-off fa-4x', 0, 'Logout', '', 'Y', 1, 9, 0, ''),
			(9, 'Satuan Barang', 'media.php?module=satuan_barang', '', 2, 'Satuan Barang', '', 'Y', 3, 1, 0, ''),
			(10, 'Menu', 'media.php?module=menu', '', 7, 'Menu', '', 'Y', 2, 4, 0, ''),
			(11, 'Supplier', 'media.php?module=supplier', '', 7, 'Supplier', '', 'Y', 3, 1, 0, ''),
			(12, 'Kategori Barang', 'media.php?module=kategori_barang', '', 2, 'Kategori Barang', '', 'Y', 3, 2, 0, ''),
			(13, 'Rak Barang', 'media.php?module=rak', '', 2, 'Rak Barang', '', 'Y', 3, 3, 0, ''),
			(14, 'Tambah Barang', 'media.php?module=barang&act=tambahbarang', '', 2, 'Tambah Barang', '', 'Y', 3, 4, 0, ''),
			(15, 'Cari Barang', 'media.php?module=barang&act=caribarang1', '', 2, 'Cari Barang', '', 'Y', 3, 5, 0, ''),
			(16, 'Cetak Label per Rak', 'media.php?module=barang&act=cetaklabel1', '', 2, 'Cetak Label per Rak', '', 'Y', 3, 6, 0, ''),
			(17, 'Transfer Barang Antar Ahad', 'media.php?module=barang&act=transfer1', '', 2, 'Transfer Barang Antar Ahad', '', 'Y', 3, 7, 0, ''),
			(18, 'Input Rak Barang', 'media.php?module=barang&act=inputrak', '', 2, 'Input Rak Barang', '', 'Y', 3, 8, 0, ''),
			(19, 'Pembelian Barang', 'media.php?module=pembelian_barang&act=pembelianbarang', '', 3, 'Pembelian Barang', '', 'Y', 3, 1, 0, ''),
			(20, 'Retur Pembelian', 'media.php?module=pembelian_barang&act=returpembelian', '', 3, 'Retur Pembelian', '', 'Y', 3, 2, 0, ''),
			(21, 'Cetak Nota Retur', 'media.php?module=pembelian_barang&act=cetakretur', '', 3, 'Cetak Nota Retur', '', 'Y', 3, 3, 0, ''),
			(22, 'Input pembelian elektronik', 'media.php?module=pembelian_barang&act=inputeprocurement1', '', 3, 'Input Pembelian Elektronik', '', 'Y', 3, 4, 0, ''),
			(23, 'Input RPO per item', 'media.php?module=pembelian_barang&act=buatrpo1', '', 3, 'Input RPO (per Item)', '', 'Y', 3, 5, 0, ''),
			(24, 'Input RPO per Supplier', 'media.php?module=pembelian_barang&act=rposup1', '', 3, 'Input RPO per Supplier', '', 'Y', 3, 6, 0, ''),
			(25, 'Buka kasir', 'media.php?module=kasir&act=bukakasir', '', 4, 'Buka Kasir', '', 'Y', 3, 1, 0, ''),
			(26, 'Tutup kasir', 'media.php?module=kasir&act=tutupkasir', '', 4, 'Tutup Kasir', '', 'Y', 3, 2, 0, ''),
			(27, 'Penambahan Dana', 'media.php?module=kasir&act=tambahdana', '', 4, 'Penambahan Dana', '', 'Y', 3, 3, 0, ''),
			(28, 'Penjualan', 'media.php?module=penjualan_barang', '', 4, 'Penjualan', '', 'Y', 4, 4, 0, ''),
			(29, 'User', 'media.php?module=user', '', 7, 'User', '', 'Y', 2, 3, 0, ''),
			(34, 'Customer', 'media.php?module=customer', '', 7, 'Customer', '', 'Y', 4, 2, 0, ''),
			(35, 'Workstation', 'media.php?module=workstation', '', 7, 'Workstation', '', 'Y', 2, 5, 0, ''),
			(36, 'Personal Info', 'media.php?module=ganti_password', '', 7, 'Personal Info', '', 'Y', 2, 6, 0, ''),
			(37, 'Laporan Pemb Brg / tgl', 'media.php?module=pembelian_barang&act=laporanpembeliantanggal', '', 5, 'Pembelian Barang per Tanggal', '', 'Y', 2, 1, 0, ''),
			(38, 'Laporan Pemb Brg / sup', 'media.php?module=pembelian_barang&act=laporanpembelian', '', 5, 'Pembelian Barang per Supplier', '', 'Y', 2, 2, 0, ''),
			(39, 'Laporan Penjualan', 'media.php?module=laporan&act=penjualan1', '', 5, 'Penjualan', '', 'Y', 2, 3, 0, ''),
			(40, 'Total Stock', 'media.php?module=laporan&act=total1', '', 5, 'Total Stock', '', 'Y', 2, 4, 0, ''),
			(41, 'Top Rank', 'media.php?module=laporan&act=toprank1', '', 5, 'Top Rank', '', 'Y', 2, 5, 0, ''),
			(42, 'Aging', 'media.php?module=laporan&act=aging1', '', 5, 'Aging Stock', '', 'Y', 2, 6, 0, ''),
			(43, 'Cetak Stock Barang', 'media.php?module=barang&act=cetakbarang1', '', 6, 'Cetak Stock Barang', '', 'Y', 3, 1, 0, ''),
			(44, 'Cetak Form Stock Op', 'media.php?module=barang&act=cetakSO', '', 6, 'Cetak Form Stock Opname', '', 'Y', 3, 2, 0, ''),
			(45, 'Input SO Manual', 'media.php?module=barang&act=inputSO', '', 6, 'Input SO Manual', '', 'Y', 3, 3, 0, ''),
			(46, 'Fast SO', '../tools/fast-stock-opname/fast-SO.php', '', 6, 'Input Fast SO', '', 'Y', 3, 4, 0, ''),
			(47, 'Approve Fast SO', 'media.php?module=barang&act=ApproveFastSO1', '', 6, 'Approve Fast SO', '', 'Y', 3, 5, 0, ''),
			(48, 'Input Mobil SO', '../tools/fast-stock-opname/fast-SO-mobile.php', '', 6, 'Input Mobile SO', '', 'Y', 3, 6, 0, ''),
			(49, 'Approve Mobile SO', 'media.php?module=barang&act=ApproveMobileSO1', '', 6, 'Approve Mobile SO', '', 'Y', 3, 7, 0, ''),
			(50, 'System', 'media.php?module=system', 'fa fa-cogs fa-4x', 0, 'System', '', 'Y', 2, 8, 0, ''),
			(51, 'Setting', 'media.php?module=system&act=setting', '', 50, 'Setting', '', 'Y', 2, 1, 0, ''),
			(52, 'Maintenance', 'media.php?module=system&act=maintenance', '', 50, 'Maintenance', '', 'Y', 2, 2, 0, ''),
			(53, 'Diskon', 'media.php?module=barang&act=diskon', '', 2, 'Diskon', '', 'Y', 2, 9, 0, ''),
			(54, 'Diskon', 'media.php?module=laporan&act=diskon1', '', 5, 'Diskon', '', 'Y', 2, 7, 0, ''),
			(55, 'Pindah Supplier', 'media.php?module=barang&act=pindahsupplier', '', 2, 'Pindah Supplier', '', 'Y', 2, 10, 0, ''),
            (56, 'Pindah Rak', 'media.php?module=barang&act=pindahrak', '', 2, 'Pindah Rak', '', 'Y', 2, 11, 0, ''),
            (57, 'Rpo per Supplier Responsive', '../tools/rpo', '', 3, 'Input RPO per Supplier per Rak', '', 'Y', 3, 7, 0, '');
		";
    $hasil = exec_query($sql);
    echo mysql_error();

    /* Update deskripsi untuk memudahkan update config di aplikasi */
    $sql = "update config set description = 'Nama Toko' where `option`='store_name';
            update config set description = 'Struk Footer 1' where `option`='receipt_footer1';
            update config set description = 'Struk Footer 2' where `option`='receipt_footer2';
            update config set description = 'Struk Header 1' where `option`='receipt_header1';
            update config set description = 'Temporary Space' where `option`='temporary_space';
            update config set description = 'Versi' where `option`='version';
		";
    $hasil = exec_query($sql);
    echo mysql_error();

    /* Update struktur database untuk diskon (detail_jual, customer)
     * Create table diskon (diskon_detail, diskon_tipe, diskon_transaksi)
     */
    $sql = "ALTER TABLE  `tmp_detail_jual` ADD  `diskon_persen` INT( 11 ) NOT NULL DEFAULT  '0';
            ALTER TABLE  `tmp_detail_jual` ADD  `diskon_rupiah` DECIMAL( 15, 2 ) NOT NULL DEFAULT  '0';
            ALTER TABLE  `tmp_detail_jual` ADD  `diskon_detail_uids` varchar(255) DEFAULT NULL ;

            ALTER TABLE `detail_jual`
            ADD COLUMN `uid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
            ADD PRIMARY KEY (`uid`);

            ALTER TABLE `customer`
            ADD COLUMN `diskon_persen` INT NULL DEFAULT 0 AFTER `last_update`,
            ADD COLUMN `diskon_rupiah` DECIMAL(15,5) NULL DEFAULT 0 AFTER `diskon_persen`;


            CREATE TABLE IF NOT EXISTS `diskon_detail` (
              `uid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
              `diskon_tipe_id` bigint(20) UNSIGNED NOT NULL,
              `diskon_tipe_nama` varchar(25) NOT NULL,
              `trigger` varchar(25) NOT NULL,
              `barcode` varchar(25) DEFAULT NULL,
              `tanggal_dari` datetime DEFAULT '0000-00-00 00:00:00',
              `tanggal_sampai` datetime DEFAULT '0000-00-00 00:00:00',
              `diskon_rupiah` decimal(15,2) NOT NULL DEFAULT '0.00',
              `diskon_persen` int(11) NOT NULL DEFAULT '0',
              `min_item` int(11) unsigned DEFAULT NULL COMMENT 'if (value >= qty) dapatDiskon;',
              `max_item` int(11) unsigned DEFAULT NULL,
              `status` tinyint(1) DEFAULT '1' COMMENT 'true=aktif; ',
              PRIMARY KEY (`uid`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1000 ;


            CREATE TABLE IF NOT EXISTS `diskon_tipe` (
              `uid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
              `nama` varchar(25) NOT NULL,
              `deskripsi` varchar(250) DEFAULT NULL,
              `trigger_quantity` tinyint(1) NOT NULL DEFAULT '0',
              `trigger_price` tinyint(1) NOT NULL DEFAULT '0',
              `trigger_time` tinyint(1) NOT NULL DEFAULT '0',
              `trigger_total` tinyint(1) NOT NULL DEFAULT '0',
              `trigger_barcode` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`uid`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1000 ;


            INSERT INTO `diskon_tipe` (`uid`, `nama`, `deskripsi`, `trigger_quantity`, `trigger_price`, `trigger_time`, `trigger_total`, `trigger_barcode`) VALUES
            (1, 'Admin', 'Entry Diskon Manual by Admin', 0, 0, 0, 0, 0),
            (2, 'Customer', 'Diskon per Customer/Member', 0, 0, 0, 0, 0),
            (1000, 'Grosir', 'Beli banyak harga turun', 1, 0, 0, 0, 1),
            (1001, 'Waktu', 'Turun Harga selama waktu tertentu', 0, 0, 1, 0, 1);


            CREATE TABLE IF NOT EXISTS `diskon_transaksi` (
              `uid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
              `diskon_detail_uids` varchar(255) NOT NULL COMMENT 'json = {diskon_detail_uid : diskon_rupiah}',
              `barcode` varchar(25) DEFAULT NULL,
              `waktu` datetime NOT NULL,
              `diskon_rupiah` decimal(15,2) NOT NULL DEFAULT '0.00',
              `diskon_persen` int(11) NOT NULL DEFAULT '0',
              `idDetailJual` bigint(20) unsigned NOT NULL,
              PRIMARY KEY (`uid`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

		";
    $hasil = exec_query($sql);
    echo mysql_error();

    /* Create table "universal" tmp, saat ini dibuat, tabel ini hanya digunakan untuk rpo per supplier */
    $sql = "CREATE TABLE IF NOT EXISTS `tmp` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `bigint1` bigint(20) DEFAULT NULL,
              `bigint2` bigint(20) DEFAULT NULL,
              `bigint3` bigint(20) DEFAULT NULL,
              `integer1` int(11) DEFAULT NULL,
              `integer2` int(11) DEFAULT NULL,
              `integer3` int(11) DEFAULT NULL,
              `vc1` varchar(45) DEFAULT NULL,
              `vc2` varchar(45) DEFAULT NULL,
              `vc3` varchar(45) DEFAULT NULL,
              `float1` float DEFAULT NULL,
              `float2` float DEFAULT NULL,
              `float3` float DEFAULT NULL,
              `dt1` datetime DEFAULT NULL,
              `dt2` datetime DEFAULT NULL,
              `dt3` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
		";
    $hasil = exec_query($sql);
    echo mysql_error();

    /* Ubah struktur tabel barang agar bisa menampung status nonAktif */
    $sql = "ALTER TABLE `barang`
            ADD COLUMN `nonAktif` TINYINT(1) NULL COMMENT '1=Tidak Aktif' AFTER `idRak`;
		";
    $hasil = exec_query($sql);
    echo mysql_error();

    /* Create tabel untuk proses self_checkout / mobile cashier
     * Tabel: self_checkout, self_checkout_detail, self_checkout_temp
     */
    $sql = "CREATE TABLE `self_checkout` (
              `uid` int(11) NOT NULL AUTO_INCREMENT,
              `datetime` datetime NOT NULL,
              `ipv4` varchar(15) NOT NULL,
              PRIMARY KEY (`uid`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

            CREATE TABLE `self_checkout_detail` (
              `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `self_checkout_uid` bigint(20) NOT NULL,
              `barcode` varchar(45) NOT NULL,
              `qty` int(11) NOT NULL,
              `harga_jual` bigint(20) NOT NULL,
              `diskon` bigint(20) NOT NULL,
              PRIMARY KEY (`uid`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

            CREATE TABLE `self_checkout_temp` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `barcode` varchar(45) NOT NULL,
              `qty` int(10) unsigned NOT NULL,
              `harga_jual` bigint(20) NOT NULL,
              `diskon` bigint(20) NOT NULL DEFAULT '0',
              `ipv4` varchar(15) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
		";
    $hasil = exec_query($sql);
    echo mysql_error();


    /* Create tabel untuk modul rpo (responsive layout) di /tools/rpo
     * Tabel: purchase_order, purchase_order_detail
     */
    $sql = "CREATE TABLE `purchase_order` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `tanggal_buat` datetime NOT NULL,
              `supplier_id` bigint(20) NOT NULL,
              `range` int(11) NOT NULL COMMENT 'hari',
              `buffer` int(11) NOT NULL COMMENT '%',
              `jumlah_hari_persediaan` int(11) NOT NULL COMMENT 'hari',
              `updated_by` varchar(30) NOT NULL,
              `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=rpo; 1=po',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


            CREATE TABLE `purchase_order_detail` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `purchase_order_id` bigint(20) unsigned NOT NULL,
              `barcode` varchar(25) NOT NULL,
              `harga_beli_terakhir` bigint(20) NOT NULL,
              `stok_saat_ini` int(11) DEFAULT NULL,
              `avg_daily_sales` float DEFAULT NULL,
              `saran_order` int(11) DEFAULT NULL,
              `jumlah_order` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
		";
    $hasil = exec_query($sql);
    echo mysql_error();


    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(2, 0, 0)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(2, 0, 0)) . "', '')";
    };
    $hasil = mysql_query($sql) or die('Gagal update db version, error: ' . mysql_error());
}

function upgrade_200_to_201() {
    // alter tabel self_checkout_temp
    $sql = "ALTER TABLE `self_checkout_temp`
            ADD COLUMN `waktu` TIMESTAMP NOT NULL AFTER `ipv4`;
		";
    $hasil = exec_query($sql);
    echo mysql_error();

    // ubah tabel ke myisam (standar ahadpos)
    $sql = "ALTER TABLE `purchase_order`
            ENGINE = MyISAM";
    $hasil = exec_query($sql);
    echo mysql_error();
    $sql = "ALTER TABLE `purchase_order_detail`
            ENGINE = MyISAM";
    $hasil = exec_query($sql);
    echo mysql_error();

    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(2, 0, 1)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(2, 0, 1)) . "', '')";
    };
    $hasil = mysql_query($sql) or die('Gagal update db version, error: ' . mysql_error());
}

function upgrade_201_to_202() {
    /*
     * Init DB ahadPOS2 dari versi ini
     * Yang belum ada di init script. dipindah ke upgrade_202_to_203
     */
    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(2, 0, 2)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(2, 0, 2)) . "', '')";
    };
    $hasil = mysql_query($sql) or die('Gagal update db version, error: ' . mysql_error());
}

function upgrade_202_to_203() {
    // Menambahkan UKM Mode: default Off
    $sql = "INSERT INTO `config` (`option`, `value`, `description`) VALUES ('ukm_mode', '0', 'UKM Mode')";
    $hasil = exec_query($sql);
    echo mysql_error();

    // Menambahkan Stok (SO dengan tambahan summary )
    $sql = "INSERT INTO `menu` (`nama`, `link`, `icon`, `parent_id`, `label`, `accesskey`, `publish`, `level_user_id`, `urutan`, `level`, `last_update`) VALUES
			('SO dengan Summary', '../tools/stok', '', 6, 'Stok', '', 'Y', 3, 8, 0, '')";
    $hasil = exec_query($sql);
    echo mysql_error();

    $sql = "CREATE TABLE `stok_stat` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `keterangan` varchar(1000) NOT NULL,
              `updated_by` varchar(30) NOT NULL,
              `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `status` tinyint(4) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
    $hasil = exec_query($sql);
    echo mysql_error();

    $sql = "CREATE TABLE `stok_stat_detail` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `stok_stat_id` int(10) unsigned NOT NULL,
              `barcode` varchar(25) NOT NULL,
              `harga_jual` bigint(20) NOT NULL,
              `stok_tercatat` int(11) NOT NULL,
              `stok_sebenarnya` int(11) DEFAULT NULL,
              `updated_by` varchar(30) NOT NULL,
              `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
    $hasil = exec_query($sql);
    echo mysql_error();

    // alter tabel stok_stat_detail
    $sql = "ALTER TABLE `stok_stat_detail`
            ADD UNIQUE INDEX `stok_stat_id_UNIQUE` (`stok_stat_id` ASC, `barcode` ASC)
		";
    $hasil = exec_query($sql);
    echo mysql_error();

    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(2, 0, 3)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(2, 0, 3)) . "', '')";
    };
    $hasil = mysql_query($sql) or die('Gagal update db version, error: ' . mysql_error());
}

function upgrade_203_to_204() {
    // ubah urutan menu Stok ke belakang
    $sql = "UPDATE `menu` SET `urutan`='10' WHERE `label`='Stok'";
    $hasil = exec_query($sql);
    echo mysql_error();

    // Tambahkan menu Fast PDT SO (input SO dengan menggunakan Portable Data Terminal atau alat lain yang bisa batch scan barcode)
    // dan "Approve" nya
    $sql = "INSERT INTO `menu` (`nama`, `link`, `icon`, `parent_id`, `label`, `accesskey`, `publish`, `level_user_id`, `urutan`, `level`, `last_update`) VALUES
			('Input SO dengan Portable Data Terminal', '../tools/fast-stock-opname/pdt-so.php', '', 6, 'Input PDT SO', '', 'Y', 3, 8, 0, ''),
            ('Approve SO dengan Portable Data Terminal', 'media.php?module=barang&act=ApprovePdtSO1', '', 6, 'Approve PDT SO', '', 'Y', 3, 9, 0, '');";
    $hasil = exec_query($sql);
    echo mysql_error();

    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(2, 0, 4)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(2, 0, 4)) . "', '')";
    };
    $hasil = mysql_query($sql) or die('Gagal update db version, error: ' . mysql_error());
}

function upgrade_204_to_205() {
    // Create Tabel harga_banded
    $sql = "CREATE TABLE `harga_banded` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `barcode` varchar(25) NOT NULL,
          `qty` int(10) unsigned NOT NULL,
          `harga` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `barcode_UNIQUE` (`barcode`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
    $hasil = exec_query($sql);
    echo mysql_error();

    // Tambahkan menu Harga banded
    $sql = "INSERT INTO `menu` (`nama`, `link`, `icon`, `parent_id`, `label`, `accesskey`, `publish`, `level_user_id`, `urutan`, `level`, `last_update`) VALUES
			('Harga Banded', 'media.php?module=barang&act=hargabanded', '', 2, 'Harga Banded', '', 'Y', 2, 12, 0, '')";
    $hasil = exec_query($sql);
    echo mysql_error();

    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(2, 0, 5)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(2, 0, 5)) . "', '')";
    };
    $hasil = mysql_query($sql) or die('Gagal update db version, error: ' . mysql_error());
}

function upgrade_205_to_206() {
    // Create Tabel tmp_harga_banded
    $sql = "CREATE TABLE `tmp_harga_banded` (
              `barcode` varchar(25) NOT NULL,
              `supplier_id` int(11) NOT NULL,
              `user_name` varchar(30) NOT NULL,
              `qty` int(11) NOT NULL,
              `harga_satuan` float NOT NULL,
              PRIMARY KEY (`barcode`,`user_name`,`supplier_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
    $hasil = exec_query($sql);
    echo mysql_error();

    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(2, 0, 6)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(2, 0, 6)) . "', '')";
    };
    $hasil = mysql_query($sql) or die('Gagal update db version, error: ' . mysql_error());
}

function upgrade_206_to_207() {
    // Create Tabel tmp_harga_banded
    $sql = "ALTER TABLE `detail_jual`
            ADD COLUMN `harga_jual_asli` BIGINT NULL AFTER `hargaJual`
            ";
    $hasil = exec_query($sql);
    echo mysql_error();

    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(2, 0, 7)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(2, 0, 7)) . "', '')";
    };
    $hasil = mysql_query($sql) or die('Gagal update db version, error: ' . mysql_error());
}

function upgrade_207_to_208() {

    // Pembuatan tabel untuk menyimpan transaksi transfer antar ahad
    $sql = "CREATE TABLE `transaksitransferbarang` (
                  `idTransaksi` bigint(20) NOT NULL AUTO_INCREMENT,
                  `tglTransaksi` datetime DEFAULT NULL,
                  `idCustomer` varchar(10) DEFAULT NULL,
                  `tglKirimBarang` date DEFAULT NULL,
                  `idTipePembayaran` int(3) DEFAULT NULL,
                  `nominal` bigint(20) DEFAULT '0',
                  `idUser` int(3) DEFAULT NULL,
                  `last_update` date DEFAULT NULL,
                  PRIMARY KEY (`idTransaksi`),
                  KEY `idUser` (`idUser`),
                  KEY `tglTransaksi` (`tglTransaksi`),
                  KEY `nominal` (`nominal`)
                ) ENGINE=MyISAM
            ";
    $hasil = exec_query($sql);
    echo mysql_error();

    $sql = "CREATE TABLE `detail_transfer_barang` (
                  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `idBarang` bigint(20) NOT NULL,
                  `jumBarang` int(10) NOT NULL,
                  `hargaJual` bigint(20) NOT NULL,
                  `username` varchar(30) DEFAULT NULL,
                  `barcode` varchar(25) DEFAULT NULL,
                  `nomorStruk` bigint(20) DEFAULT NULL,
                  PRIMARY KEY (`uid`),
                  KEY `username` (`username`),
                  KEY `nomorStruk` (`nomorStruk`),
                  KEY `barcode` (`barcode`)
                ) ENGINE=MyISAM
            ";
    $hasil = exec_query($sql);
    echo mysql_error();
	 
    // Tambahkan menu Laporan Transfer Barang
    $sql = "INSERT INTO `menu` (`nama`, `link`, `icon`, `parent_id`, `label`, `accesskey`, `publish`, `level_user_id`, `urutan`, `level`, `last_update`) VALUES
			('Laporan Transfer Barang', 'media.php?module=laporan&act=transferbarang', '', 5, 'Transfer Barang', '', 'Y', 3, 8, 0, '')";
    $hasil = exec_query($sql);
    echo mysql_error();
	 
    // update version number ------------------------------------------------------
    $sql = "SELECT * FROM config WHERE `option` = 'version'";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        $sql = "UPDATE `config` SET value = '" . serialize(array(2, 0, 8)) . "' WHERE `option` = 'version'";
    }
    else {
        $sql = "INSERT INTO `config` (`option`, value, description) VALUES ('version', '" . serialize(array(2, 0, 8)) . "', '')";
    };
    $hasil = mysql_query($sql) or die('Gagal update db version, error: ' . mysql_error());
}
// =================================== PATCH VERSI 3.x.x ==========================================
function check_minor_major3($dbminor, $minor, $dbrevision, $revision) {

    // nothing here yet
}

// ==================== general functions ==============================

function exec_query($sql) {
// able to loop through & execute MULTIPLE query lines

    $queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sql);
    foreach ($queries as $query) {
        if (strlen(trim($query)) > 0)
            mysql_query($query);
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

