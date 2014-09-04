<?php
        
        include "../config/config.php";

	// GLOBAL VARIABLES
	$temporary_barcode = 2323232323;		// barcode prefix for items with non-unique barcode
	$sdb = 'ahadmart'; 			// source database
	$tdb = 'ahadpos_pdkkacang'; 		// target database

// ====START CODE==================================================================================================================

	// konek ke source & target database
	$sourcedb = mysql_connect($server,$username,$password, true) or die("Koneksi gagal");
	mysql_select_db($sdb, $sourcedb);

	// 65536 = bisa multiple SQL statement di satu mysql_query() !
	$targetdb = mysql_connect($server,$username,$password, true, 65536) or die("Koneksi gagal");
	mysql_select_db($tdb, $targetdb);


	// OPTIMIZATIONS
	$sql ="ALTER TABLE `pembelian_detail` ADD INDEX ( `kd_produk` ) ";
	mysql_query($sql, $sourcedb);


	// ### supplier : dari supplier
	echo " selesai</h1><h1> Sedang memproses: data SUPPLIER .....";
	
	$hasil = mysql_query("select * from supplier", $sourcedb) or die("Error : ".mysql_error());
	while($x = mysql_fetch_array($hasil)) {
		$sql = "INSERT INTO supplier (last_update,idSupplier,namaSupplier,alamatSupplier,telpSupplier,Keterangan) 
				VALUES ('2010-06-17', $x[id_supplier], '$x[nama]', '$x[alamat] :: $x[kd_pos]', 
				'Telp: $x[telp], Fax: $x[fax], TelpCP: $x[telp_cp], MobileCP: $x[mobile_cp],', 'CP: $x[cp], Ket: $x[ket],')
			";
		mysql_query($sql,$targetdb);
	};
/*
last_update='2010-06-17'
get:
# idSupplier		(num)	id_supplier
# namaSupplier 		(text) nama
# alamatSupplier 	(text) alamat + kd_pos
# telpSupplier 		(text) telp 	fax 	telp_cp  mobile_cp 	
# Keterangan 		(text) cp + ket
*/


	//### satuan_barang : dari satuan_ukuran
	echo "selesai</h1><h1>Sedang memproses : data SATUAN .....";	
	
	$hasil = mysql_query("select * from satuan_ukuran", $sourcedb) or die("Error : ".mysql_error());
	while($x = mysql_fetch_array($hasil)) {
		$sql = "INSERT INTO satuan_barang (idSatuanBarang,namaSatuanBarang) 
				VALUES ($x[id_satuan_ukuran], '$x[satuan_ukuran]')
			";
		mysql_query($sql,$targetdb);
	};
/*
get:
# idSatuanBarang	(num)	id_satuan_ukuran
# namaSatuanBarang	(text)	satuan_ukuran
*/


	// ### rak : dari rak
	echo "selesai</h1><h1>Sedang memproses : data RAK .....";	
	
	$hasil = mysql_query("select * from rak", $sourcedb) or die("Error : ".mysql_error());
	while($x = mysql_fetch_array($hasil)) {
		$sql = "INSERT INTO rak (idRak,namaRak) 
				VALUES ($x[id_rak],'$x[no_rak]')
			";
		mysql_query($sql,$targetdb);
	};
/*
get: 
# idRak			(num) id_rak
# namaRak		(text) no_rak
*/


	// ### kategori : dari kategori
	echo "selesai</h1><h1>Sedang memproses : data KATEGORI .....";	
	// hapus dulu isi table kategori_barang di $targetdb
	mysql_query("delete from kategori_barang;",$targetdb);
	// mulai copy data kategori
	$hasil = mysql_query("SELECT * FROM kategori", $sourcedb) or die("Error : ".mysql_error());
	while($x = mysql_fetch_array($hasil)) {
		$sql = "INSERT INTO kategori_barang (idKategoriBarang,namaKategoriBarang) 
				VALUES ($x[id_kategori],'$x[kategori]')
			";
		mysql_query($sql,$targetdb) or die("Error : ".mysql_error());
	};
/* 
get:
# idKategoriBarang	(num) 	id_kategori
# namaKategoriBarang	(text) kategori
*/


	echo "selesai</h1><h1>Sedang memproses : data BARANG .....";	

	$ctr= 0;
	$errorctr=0;
	$sql = "SELECT s.id_supplier,s.jumlah_gudang,s.jumlah,s.harga_jual,s.kd_barang, s.nama_barang,s.id_kategori,s.id_satuan_ukuran,
			s.id_rak, 
			b.harga
		FROM stock as s, (SELECT bb.harga FROM pembelian_detail as bb, stock as ss 
					WHERE ss.kd_barang=bb.kd_produk ORDER BY bb.trx_time DESC LIMIT 1) as b  
		LIMIT $ctr, ".($ctr+10000);

	$hasil = mysql_query($sql, $sourcedb) or die("Error : ".mysql_error());
	while (mysql_num_rows($hasil) > 0) {	
		echo "<br> data: ".($ctr+1)." s/d ".($ctr+10000)." ....";
		
	while(($x = mysql_fetch_array($hasil)) !== false) {

		// cari HargaBeli
		//$hasil2 = mysql_query("SELECT harga FROM pembelian_detail WHERE kd_produk='$x[kd_barang]'", $sourcedb) or die("Error : ".mysql_error()." sql: ".$sql);
		//$y = mysql_fetch_array($hasil2);
		$HargaBeli = $x[harga];		
		if (empty($HargaBeli)) { $HargaBeli = 0;};
		
		// ### tmp_detail_beli :: dari stock
		// perlu simpan ke tmp_detail_beli dulu, untuk mendapatkan idBarang yang unique (di generate oleh MySQL)
		$sql = "INSERT INTO tmp_detail_beli (username,tglExpire,tglTransaksi,idSupplier,jumBarang, 
					hargaBeli,hargaJual,barcode) 
				VALUES ('admin','0000-00-00','2010-06-17',$x[id_supplier],".($x[jumlah_gudang]+$x[jumlah]).",
					$HargaBeli,$x[harga_jual],'$x[kd_barang]')
			";
		mysql_query($sql,$targetdb) or die("Error : ".mysql_error()." sql: ".$sql." -- HargaBeli: ".$HargaBeli);
		/*
		username='admin'
		tglExpire='0000-00-00'
		tglTransaksi='2010-06-17'
		get:
		# idSupplier 		(num) id_supplier
		# jumBarang 		(num) jumlah_gudang + jumlah
		# hargaBeli 		(num) select b.harga from pembelian_detail as b where b.kd_produk='barcode' ('barcode' = stock.kd_barang)
		# hargaJual 		(num) harga_jual
		# barcode 		(text) kd_barang
		*/


		// cari Idbarang
		//$Idbarang = mysql_insert_id($targetdb) or die("Error : ".mysql_error());
		$z = mysql_query("SELECT LAST_INSERT_ID() FROM tmp_detail_beli",$targetdb) or die("Error : ".mysql_error());
		$zz = mysql_fetch_array($z);
		$IdBarang = $zz["LAST_INSERT_ID()"];
	
		// ganti ' menjadi `, agar tidak error saat INSERT
		$x[nama_barang] = str_replace("'","`",$x[nama_barang]);

		// ### barang	:: untuk setiap record di tabel tmp_detail_beli, buat juga record di tabel barang
		$sql = "INSERT INTO barang (idSupplier,username,last_update,idBarang,namaBarang,idKategoriBarang,idSatuanBarang,jumBarang,
				hargaJual,barcode,idRak) 
			VALUES ($x[id_supplier],'admin','2010-06-17',$IdBarang,'$x[nama_barang]',$x[id_kategori],$x[id_satuan_ukuran],
				".($x[jumlah_gudang]+$x[jumlah]).",
				$x[harga_jual],'$x[kd_barang]', $x[id_rak])
			";
		$hasil3 = mysql_query($sql,$targetdb); 

		if (!$hasil3) {
			$errorctr++;
			echo "Error : ".mysql_error()." sql: ".$sql;

			$temporary_barcode++;
		};
		/*
		username='admin'
		lastupdate='2010-06-17'
		get: 
		# idBarang 		: dari tmp_detail_beli.idBarang
		# namaBarang		(text) 	stock.nama_barang
		# idKategoriBarang	(num) 	stock.id_kategori
		# idSatuanBarang	(num)	stock.id_satuan_ukuran
		# jumBarang		: dari tmp_detail_beli.jumBarang
		# hargaJual		: dari tmp_detail_beli.hargaJual
		# barcode		: dari tmp_detail_beli.barcode
		# idRak			(num)	id_rak
		*/
	}; // end while(($x = mysql_fetch_array($hasil)) !== false) 
	

		// siap-siap untuk looping berikutnya
		$ctr = $ctr + 10000;
		//$sql = "SELECT * FROM stock LIMIT $ctr, ".($ctr+10000);
		//$sql = "SELECT s.id_supplier,s.jumlah_gudang,s.jumlah,s.harga_jual,s.kd_barang, s.nama_barang,s.id_kategori,s.id_satuan_ukuran,
		//	s.id_rak, 
		//	b.harga
		//FROM stock as s, pembelian_detail as b 
		//WHERE b.kd_produk = s.kd_barang 
		//LIMIT $ctr, ".($ctr+10000);
		$sql = "SELECT s.id_supplier,s.jumlah_gudang,s.jumlah,s.harga_jual,s.kd_barang, s.nama_barang,s.id_kategori,s.id_satuan_ukuran,
			s.id_rak, 
			b.harga
		FROM stock as s, (SELECT bb.harga FROM pembelian_detail as bb, stock as ss 
					WHERE ss.kd_barang=bb.kd_produk ORDER BY bb.trx_time DESC  LIMIT 1) as b  
		LIMIT $ctr, ".($ctr+10000);
		$hasil = mysql_query($sql, $sourcedb) or die("Error : ".mysql_error());
	}; // end while (mysql_num_rows($hasil) > 0) 


// selesai input data ke tmp_detail_beli,
// mulai pindahkan ke transaksi_beli


	// ### transaksi_beli :: baca dari tmp_detail_beli, grouped per supplier
	echo "selesai</h1><h1>Sedang memproses : data TRANSAKSI BELI ..... </h1>";	
	
	$hasil = mysql_query("SELECT DISTINCT idSupplier FROM tmp_detail_beli", $targetdb) or die("Error : ".mysql_error());
	while($x = mysql_fetch_array($hasil)) {

		echo "<br> Supplier: $x[idSupplier]";
		
		// simpan transaksi beli nya
		$sql = "INSERT INTO transaksibeli (username,idUser,last_update,idTipePembayaran,tglTransaksibeli,
					NomorInvoice,nominal,idSupplier) 
			VALUES ('admin',1,'2010-06-17',1,'2010-06-17',0,0,$x[idSupplier])
			";
		mysql_query($sql,$targetdb) or die("Error : ".mysql_error());
		/*
		username='admin'
		idUser=1
		last_update='2010-06-17'
		idTipePembayaran=1
		tglTransaksiBeli='2010-06-17'
		NomorInvoice=0
		nominal=0
		get:
		# idSupplier 		: dari tmp_detail_beli.idSupplier
		*/

		// cari IdTransaksiBeli
		//$IdTransaksiBeli = mysql_insert_id($targetdb) or die("Error : ".mysql_error());
		$z = mysql_query("SELECT LAST_INSERT_ID() FROM transaksibeli",$targetdb) or die("Error : ".mysql_error());
		$zz = mysql_fetch_array($z);
		$IdTransaksiBeli = $zz["LAST_INSERT_ID()"];

		// kumpulkan semua Detail transaksi beli supplier ybs
		$hasil2 = mysql_query("SELECT * FROM tmp_detail_beli WHERE idSupplier = $x[idSupplier]", $targetdb) or die("Error : ".mysql_error());
		while($y = mysql_fetch_array($hasil2)) {

			$sql = "INSERT INTO detail_beli (username,isSold,IdTransaksiBeli,tglExpire,idBarang,jumBarang,jumBarangAsli,hargaBeli,barcode) 
					VALUES ('admin','N', $IdTransaksiBeli,'0000-00-00',$y[idBarang],$y[jumBarang],$y[jumBarang],$y[hargaBeli],
						'$y[barcode]')
				";
			
			mysql_query($sql,$targetdb) or die("Error : ".mysql_error()." sql: ".$sql);
			/*
			username='admin'
			isSold='N'
			tglExpire='0000-00-00'
			get :
			# idTransaksiBeli 	: dari transaksi_beli.idTransaksiBeli
			# idBarang 		: dari tmp_detail_beli.idBarang
			# jumBarang		: dari tmp_detail_beli.jumBarang 	
			# jumBarangAsli 	: dari tmp_detail_beli.jumBarang
			# hargaBeli 	 	: dari tmp_detail_beli.hargaBeli
			# barcode 		: dari tmp_detail_beli.barcode
			*/
		}; // while($y = mysql_fetch_array($hasil2))

	}; // while($x = mysql_fetch_array($hasil))



/*
users:
admin
awan
hamdani
harry
aie
ayu
lela
*/


	// ### finish : hapus semua isi tmp_detail_beli

	echo "s<h1>Sedang memproses : hapus data sementara .....";	
	
	mysql_query("DELETE FROM tmp_detail_beli", $targetdb) or die("Error : ".mysql_error());	
	

	// hapus duplikasi di detail_beli
	$sql = "CREATE TABLE sementara AS SELECT * FROM detail_beli WHERE 1 GROUP BY barcode";
	mysql_query($sql,$targetdb);
	$sql = "DROP TABLE detail_beli";
	mysql_query($sql,$targetdb);
	$sql = "RENAME TABLE sementara TO detail_beli";
	mysql_query($sql,$targetdb);

	
	echo " SELESAI !</h1> \n\n Error : $errorctr \n\n";
	exit;




function PopulateDatabase($targetdb) {

	mysql_query("drop database ahadpos_pdkkacang; create database ahadpos_pdkkacang; commit;", $targetdb) or die("Error : ".mysql_error());
	mysql_select_db('ahadpos_pdkkacang', $targetdb);

	$sql = "
SET NAMES utf8;

SET SQL_MODE='';
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `bank` */

DROP TABLE IF EXISTS `bank`;

CREATE TABLE `bank` (
  `idBank` int(3) NOT NULL,
  `namaBank` varchar(20) default NULL,
  `noRekening` varchar(30) default NULL,
  PRIMARY KEY  (`idBank`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `bank` */

/*Table structure for table `barang` */

DROP TABLE IF EXISTS `barang`;

CREATE TABLE `barang` (
  `idBarang` bigint(20) NOT NULL default '0',
  `namaBarang` varchar(30) default ' ',
  `idKategoriBarang` int(5) default '0',
  `idSatuanBarang` int(5) default '0',
  `jumBarang` int(10) default '0',
  `hargaJual` bigint(20) default '0',
  `last_update` date default '2000-01-01',
  `idSupplier` bigint(20) default '0',
  `barcode` varchar(25) default NULL,
  `username` varchar(30) default NULL,
  `idRak` bigint(5) default NULL,
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `barang` */

insert into `barang` (`idBarang`,`namaBarang`,`idKategoriBarang`,`idSatuanBarang`,`jumBarang`,`hargaJual`,`last_update`,`idSupplier`,`barcode`,`username`,`idRak`) values (1,'Mie Sedap Goreng Kari Spesial',4,3,160,1250,'2010-03-26',5,'80808080',NULL,NULL);
insert into `barang` (`idBarang`,`namaBarang`,`idKategoriBarang`,`idSatuanBarang`,`jumBarang`,`hargaJual`,`last_update`,`idSupplier`,`barcode`,`username`,`idRak`) values (2,'Mie Sedap Rebus Kari Ayam',4,3,160,1094,'2010-03-26',5,'82828282',NULL,NULL);
insert into `barang` (`idBarang`,`namaBarang`,`idKategoriBarang`,`idSatuanBarang`,`jumBarang`,`hargaJual`,`last_update`,`idSupplier`,`barcode`,`username`,`idRak`) values (3,'Sirup ABC Lemon',3,3,20,9375,'2010-03-26',3,'90909090',NULL,NULL);
insert into `barang` (`idBarang`,`namaBarang`,`idKategoriBarang`,`idSatuanBarang`,`jumBarang`,`hargaJual`,`last_update`,`idSupplier`,`barcode`,`username`,`idRak`) values (4,'Sirup ABC Strawberry',3,3,20,10000,'2010-03-26',3,'91919191',NULL,NULL);

/*Table structure for table `config` */

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `idConfig` bigint(20) NOT NULL auto_increment,
  `option` varchar(30) NOT NULL,
  `value` varchar(50) NOT NULL,
  `description` varchar(50) default NULL,
  PRIMARY KEY  (`idConfig`),
  KEY `option` (`option`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `config` */

insert into `config` (`idConfig`,`option`,`value`,`description`) values (1,'store_name','Ahad mart Parungserab','name of the shop');
insert into `config` (`idConfig`,`option`,`value`,`description`) values (2,'receipt_footer1','Terimakasih telah berbelanja di Ahad mart',NULL);
insert into `config` (`idConfig`,`option`,`value`,`description`) values (3,'receipt_footer2','Murah, Lengkap, dan Islami',NULL);
insert into `config` (`idConfig`,`option`,`value`,`description`) values (4,'receipt_header1','---------------------',NULL);
insert into `config` (`idConfig`,`option`,`value`,`description`) values (5,'temporary_space','/tmp/',NULL);

/*Table structure for table `customer` */

DROP TABLE IF EXISTS `customer`;

CREATE TABLE `customer` (
  `idCustomer` varchar(10) NOT NULL,
  `namaCustomer` varchar(30) default NULL,
  `alamatCustomer` varchar(50) default NULL,
  `telpCustomer` varchar(15) default NULL,
  `keterangan` text,
  `uname` varchar(8) default NULL,
  `pwd` varchar(35) default NULL,
  `last_update` date default NULL,
  PRIMARY KEY  (`idCustomer`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `customer` */

insert into `customer` (`idCustomer`,`namaCustomer`,`alamatCustomer`,`telpCustomer`,`keterangan`,`uname`,`pwd`,`last_update`) values ('1','Umum','Customer Umum / Non Member','','Customer Umum / Non Member',NULL,NULL,'2009-12-01');
insert into `customer` (`idCustomer`,`namaCustomer`,`alamatCustomer`,`telpCustomer`,`keterangan`,`uname`,`pwd`,`last_update`) values ('2','Rosari Prima','JL. GelarSena 1','0272325540','rosa',NULL,NULL,'2009-11-21');
insert into `customer` (`idCustomer`,`namaCustomer`,`alamatCustomer`,`telpCustomer`,`keterangan`,`uname`,`pwd`,`last_update`) values ('3','Priska','STM Pembangunan','08562969601','kantin',NULL,NULL,'2009-12-06');

/*Table structure for table `detail_beli` */

DROP TABLE IF EXISTS `detail_beli`;

CREATE TABLE `detail_beli` (
  `idDetailBeli` bigint(20) NOT NULL auto_increment,
  `idTransaksiBeli` bigint(20) NOT NULL,
  `idBarang` bigint(20) NOT NULL,
  `tglExpire` date NOT NULL,
  `jumBarang` int(10) NOT NULL,
  `hargaBeli` bigint(20) NOT NULL,
  `isSold` varchar(1) character set latin1 default 'N',
  `barcode` bigint(15) default NULL,
  `username` varchar(30) character set latin1 default NULL,
  `jumBarangAsli` int(11) default NULL COMMENT 'Jumlah Barang pada saat Pembelian',
  PRIMARY KEY  (`idDetailBeli`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='latin1_general_ci';

/*Data for the table `detail_beli` */

insert into `detail_beli` (`idDetailBeli`,`idTransaksiBeli`,`idBarang`,`tglExpire`,`jumBarang`,`hargaBeli`,`isSold`,`barcode`,`username`,`jumBarangAsli`) values (1,1,1,'0000-00-00',160,1000,'N',80808080,'ariefadmin',160);
insert into `detail_beli` (`idDetailBeli`,`idTransaksiBeli`,`idBarang`,`tglExpire`,`jumBarang`,`hargaBeli`,`isSold`,`barcode`,`username`,`jumBarangAsli`) values (2,1,2,'0000-00-00',160,875,'N',82828282,'ariefadmin',160);
insert into `detail_beli` (`idDetailBeli`,`idTransaksiBeli`,`idBarang`,`tglExpire`,`jumBarang`,`hargaBeli`,`isSold`,`barcode`,`username`,`jumBarangAsli`) values (3,2,4,'0000-00-00',20,8000,'N',91919191,'ariefadmin',20);
insert into `detail_beli` (`idDetailBeli`,`idTransaksiBeli`,`idBarang`,`tglExpire`,`jumBarang`,`hargaBeli`,`isSold`,`barcode`,`username`,`jumBarangAsli`) values (4,2,3,'0000-00-00',20,7500,'N',90909090,'ariefadmin',20);

/*Table structure for table `detail_jual` */

DROP TABLE IF EXISTS `detail_jual`;

CREATE TABLE `detail_jual` (
  `idTransaksiJual` bigint(20) NOT NULL,
  `idBarang` bigint(20) NOT NULL,
  `jumBarang` int(10) NOT NULL,
  `hargaBeli` bigint(20) default NULL,
  `hargaJual` bigint(20) NOT NULL,
  `username` varchar(30) character set latin1 default NULL,
  `diskon` bigint(20) NOT NULL,
  `barcode` varchar(25) character set latin1 default NULL,
  `nomorStruk` bigint(20) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `detail_jual` */

insert into `detail_jual` (`idTransaksiJual`,`idBarang`,`jumBarang`,`hargaBeli`,`hargaJual`,`username`,`diskon`,`barcode`,`nomorStruk`) values (1,2,2,875,1094,'ariefadmin',0,'82828282',1);
insert into `detail_jual` (`idTransaksiJual`,`idBarang`,`jumBarang`,`hargaBeli`,`hargaJual`,`username`,`diskon`,`barcode`,`nomorStruk`) values (1,1,2,1000,1250,'ariefadmin',0,'80808080',1);
insert into `detail_jual` (`idTransaksiJual`,`idBarang`,`jumBarang`,`hargaBeli`,`hargaJual`,`username`,`diskon`,`barcode`,`nomorStruk`) values (2,4,1,8000,10000,'kasir1',0,'91919191',2);
insert into `detail_jual` (`idTransaksiJual`,`idBarang`,`jumBarang`,`hargaBeli`,`hargaJual`,`username`,`diskon`,`barcode`,`nomorStruk`) values (2,3,1,7500,9375,'kasir1',0,'90909090',2);

/*Table structure for table `hutang` */

DROP TABLE IF EXISTS `hutang`;

CREATE TABLE `hutang` (
  `idTransaksiBeli` bigint(20) NOT NULL,
  `nominal` bigint(20) NOT NULL,
  `tglBayar` date NOT NULL,
  `last_update` date NOT NULL,
  `username` varchar(30) default NULL,
  PRIMARY KEY  (`idTransaksiBeli`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `hutang` */

insert into `hutang` (`idTransaksiBeli`,`nominal`,`tglBayar`,`last_update`,`username`) values (1,300000,'2010-04-19','2010-03-26','ariefadmin');

/*Table structure for table `kasir` */

DROP TABLE IF EXISTS `kasir`;

CREATE TABLE `kasir` (
  `idTransKasir` int(15) NOT NULL auto_increment,
  `tglBukaKasir` datetime default NULL,
  `idUser` int(3) default NULL,
  `kasAwal` float default NULL,
  `kasSeharusnya` float default NULL,
  `kasTutup` float default NULL,
  `currentWorkstation` bigint(20) default NULL,
  `tglTutupKasir` datetime default NULL,
  `totalTransaksi` bigint(20) default NULL,
  `totalProfit` bigint(20) default NULL,
  `totalRetur` bigint(20) default NULL,
  `totalTransaksiKas` bigint(20) default NULL,
  `totalTransaksiKartu` bigint(20) default NULL,
  PRIMARY KEY  (`idTransKasir`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `kasir` */

insert into `kasir` (`idTransKasir`,`tglBukaKasir`,`idUser`,`kasAwal`,`kasSeharusnya`,`kasTutup`,`currentWorkstation`,`tglTutupKasir`,`totalTransaksi`,`totalProfit`,`totalRetur`,`totalTransaksiKas`,`totalTransaksiKartu`) values (1,'2010-03-26 20:37:52',10,100000,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `kategori_barang` */

DROP TABLE IF EXISTS `kategori_barang`;

CREATE TABLE `kategori_barang` (
  `idKategoriBarang` int(5) NOT NULL,
  `namaKategoriBarang` varchar(30) default NULL,
  PRIMARY KEY  (`idKategoriBarang`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `kategori_barang` */

insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (1,'wafer');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (2,'biskuit');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (3,'sirup');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (4,'mie');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (5,'kopi');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (6,'isotonik drink');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (7,'makanan');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (8,'Gulaku');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (9,'kosmetik');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (10,'Perlengkapan');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (11,'sabun cuci');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (12,'minuman');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (13,'Susu');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (14,'ATK');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (15,'Elektronik');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (16,'Bayi');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (17,'Detergent/Obat Nyamuk');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (18,'Pecah Belah');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (19,'Muslim');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (20,'Sabun/Shampo');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (21,'Mainan');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (22,'Pakaian');
insert into `kategori_barang` (`idKategoriBarang`,`namaKategoriBarang`) values (23,'Obat');

/*Table structure for table `leveluser` */

DROP TABLE IF EXISTS `leveluser`;

CREATE TABLE `leveluser` (
  `idLevelUser` int(2) NOT NULL,
  `levelUser` varchar(20) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`idLevelUser`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `leveluser` */

insert into `leveluser` (`idLevelUser`,`levelUser`) values (1,'semua');
insert into `leveluser` (`idLevelUser`,`levelUser`) values (2,'admin');
insert into `leveluser` (`idLevelUser`,`levelUser`) values (3,'gudang');
insert into `leveluser` (`idLevelUser`,`levelUser`) values (4,'kasir');

/*Table structure for table `modul` */

DROP TABLE IF EXISTS `modul`;

CREATE TABLE `modul` (
  `idModul` int(3) NOT NULL,
  `namaModul` varchar(50) collate latin1_general_ci default NULL,
  `link` varchar(50) collate latin1_general_ci default NULL,
  `publish` enum('Y','N') collate latin1_general_ci default NULL,
  `idLevelUser` int(2) default NULL,
  `urutan` int(3) default NULL,
  `last_update` date default NULL,
  PRIMARY KEY  (`idModul`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `modul` */

insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (1,'Manajemen User','?module=user','N',2,101,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (2,'Supplier','?module=supplier','Y',3,5,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (3,'Customer','?module=customer','Y',4,6,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (4,'Barang','?module=barang','Y',3,3,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (5,'Rak','?module=rak','Y',3,4,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (6,'Satuan Barang','?module=satuan_barang','Y',3,1,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (7,'Kategori Barang','?module=kategori_barang','Y',3,2,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (8,'Pembelian','?module=pembelian_barang','Y',3,7,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (9,'Penjualan','?module=penjualan_barang','Y',4,8,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (10,'Ganti Password','?module=ganti_password','N',1,100,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (11,'Hutang','?module=hutang','Y',3,9,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (12,'Piutang','?module=piutang','Y',4,10,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (13,'Manajemen Modul','?module=modul','N',2,102,'2009-10-19');
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (14,'Kasir','?module=kasir','Y',2,11,NULL);
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (15,'Laporan Manajemen','?module=laporan','Y',2,12,NULL);
insert into `modul` (`idModul`,`namaModul`,`link`,`publish`,`idLevelUser`,`urutan`,`last_update`) values (16,'Manajemen Workstation','?module=workstation','Y',2,13,NULL);

/*Table structure for table `pembayaran` */

DROP TABLE IF EXISTS `pembayaran`;

CREATE TABLE `pembayaran` (
  `idTipePembayaran` int(3) NOT NULL auto_increment,
  `tipePembayaran` varchar(30) default NULL,
  PRIMARY KEY  (`idTipePembayaran`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `pembayaran` */

insert into `pembayaran` (`idTipePembayaran`,`tipePembayaran`) values (1,'CASH');
insert into `pembayaran` (`idTipePembayaran`,`tipePembayaran`) values (2,'Tempo');
insert into `pembayaran` (`idTipePembayaran`,`tipePembayaran`) values (3,'Voucher');
insert into `pembayaran` (`idTipePembayaran`,`tipePembayaran`) values (4,'Debit');
insert into `pembayaran` (`idTipePembayaran`,`tipePembayaran`) values (5,'Kredit');

/*Table structure for table `piutang` */

DROP TABLE IF EXISTS `piutang`;

CREATE TABLE `piutang` (
  `idTransaksiJual` bigint(20) NOT NULL,
  `nominal` bigint(20) unsigned default NULL,
  `tglDiBayar` date default NULL,
  `idUser` int(3) default NULL,
  `last_update` date default NULL,
  PRIMARY KEY  (`idTransaksiJual`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `piutang` */

/*Table structure for table `rak` */

DROP TABLE IF EXISTS `rak`;

CREATE TABLE `rak` (
  `idRak` bigint(5) NOT NULL auto_increment,
  `namaRak` varchar(30) default NULL,
  PRIMARY KEY  (`idRak`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;

/*Data for the table `rak` */

insert into `rak` (`idRak`,`namaRak`) values (1,'Rak Depan Kasir');
insert into `rak` (`idRak`,`namaRak`) values (2,'Susu 1');
insert into `rak` (`idRak`,`namaRak`) values (3,'Susu 2');
insert into `rak` (`idRak`,`namaRak`) values (4,'Susu 3');
insert into `rak` (`idRak`,`namaRak`) values (5,'Susu 4');
insert into `rak` (`idRak`,`namaRak`) values (6,'Susu 5');
insert into `rak` (`idRak`,`namaRak`) values (7,'Susu 6');
insert into `rak` (`idRak`,`namaRak`) values (8,'Rak 8');
insert into `rak` (`idRak`,`namaRak`) values (9,'Rak 9');
insert into `rak` (`idRak`,`namaRak`) values (10,'Rak 10');
insert into `rak` (`idRak`,`namaRak`) values (11,'Rak 11');
insert into `rak` (`idRak`,`namaRak`) values (12,'Rak 12');
insert into `rak` (`idRak`,`namaRak`) values (13,'Rak 13');
insert into `rak` (`idRak`,`namaRak`) values (14,'Rak 14');
insert into `rak` (`idRak`,`namaRak`) values (15,'Rak 15');
insert into `rak` (`idRak`,`namaRak`) values (16,'Rak 16');
insert into `rak` (`idRak`,`namaRak`) values (17,'Rak 17');
insert into `rak` (`idRak`,`namaRak`) values (18,'Rak 18');
insert into `rak` (`idRak`,`namaRak`) values (19,'Rak 19');
insert into `rak` (`idRak`,`namaRak`) values (20,'Rak 20');

/*Table structure for table `retur` */

DROP TABLE IF EXISTS `retur`;

CREATE TABLE `retur` (
  `idRetur` int(10) NOT NULL auto_increment,
  `idCustomer` varchar(10) collate latin1_general_ci default NULL,
  `idJenisRetur` int(2) default NULL,
  `idTransaksi` bigint(20) default NULL,
  `idAksiRetur` int(2) default NULL,
  PRIMARY KEY  (`idRetur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `retur` */

/*Table structure for table `satuan_barang` */

DROP TABLE IF EXISTS `satuan_barang`;

CREATE TABLE `satuan_barang` (
  `idSatuanBarang` int(5) NOT NULL,
  `namaSatuanBarang` varchar(30) default NULL,
  PRIMARY KEY  (`idSatuanBarang`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `satuan_barang` */

insert into `satuan_barang` (`idSatuanBarang`,`namaSatuanBarang`) values (1,'Kg');
insert into `satuan_barang` (`idSatuanBarang`,`namaSatuanBarang`) values (2,'Ons');
insert into `satuan_barang` (`idSatuanBarang`,`namaSatuanBarang`) values (3,'Pcs');
insert into `satuan_barang` (`idSatuanBarang`,`namaSatuanBarang`) values (4,'Kardus');

/*Table structure for table `supplier` */

DROP TABLE IF EXISTS `supplier`;

CREATE TABLE `supplier` (
  `idSupplier` bigint(20) NOT NULL auto_increment,
  `namaSupplier` varchar(30) default NULL,
  `alamatSupplier` varchar(100) default NULL,
  `telpSupplier` varchar(15) default NULL,
  `Keterangan` text,
  `last_update` date default NULL,
  PRIMARY KEY  (`idSupplier`)
) ENGINE=MyISAM AUTO_INCREMENT=368 DEFAULT CHARSET=latin1;

/*Data for the table `supplier` */

insert into `supplier` (`idSupplier`,`namaSupplier`,`alamatSupplier`,`telpSupplier`,`Keterangan`,`last_update`) values (1,'Catur Edi','Jl. Wuluh 5, Papringan, Sleman','0274567876','Thanx','2009-11-30');
insert into `supplier` (`idSupplier`,`namaSupplier`,`alamatSupplier`,`telpSupplier`,`Keterangan`,`last_update`) values (2,'Albertus Supriyadi','Jl. Jonggrangan 1 No.3, Jonggrangan Baru\r\nKlaten','0282435009','Tenda LUV','2009-10-22');
insert into `supplier` (`idSupplier`,`namaSupplier`,`alamatSupplier`,`telpSupplier`,`Keterangan`,`last_update`) values (3,'MAKRO','Ciputat','','sembako','2010-02-05');
insert into `supplier` (`idSupplier`,`namaSupplier`,`alamatSupplier`,`telpSupplier`,`Keterangan`,`last_update`) values (4,'Jepara','Jembatan Lima','','','2010-02-06');

/*Table structure for table `tmp_detail_beli` */

DROP TABLE IF EXISTS `tmp_detail_beli`;

CREATE TABLE `tmp_detail_beli` (
  `idSupplier` int(10) NOT NULL,
  `tglTransaksi` date NOT NULL,
  `idBarang` bigint(20) NOT NULL auto_increment,
  `tglExpire` date NOT NULL,
  `jumBarang` int(10) NOT NULL,
  `hargaBeli` float NOT NULL,
  `hargaJual` float NOT NULL,
  `barcode` bigint(15) default NULL,
  `username` varchar(30) default NULL,
  KEY `idBarang` (`idBarang`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `tmp_detail_beli` */

/*Table structure for table `tmp_detail_jual` */

DROP TABLE IF EXISTS `tmp_detail_jual`;

CREATE TABLE `tmp_detail_jual` (
  `idCustomer` bigint(20) NOT NULL,
  `tglTransaksi` datetime NOT NULL,
  `barcode` varchar(25) character set latin1 NOT NULL,
  `jumBarang` int(10) NOT NULL,
  `hargaBeli` float NOT NULL,
  `hargaJual` float NOT NULL,
  `username` varchar(30) character set latin1 default NULL,
  `uid` bigint(20) NOT NULL auto_increment,
  `idBarang` bigint(20) default NULL,
  PRIMARY KEY  (`uid`),
  KEY `barcode` (`barcode`),
  KEY `username` (`username`),
  KEY `idCustomer` (`idCustomer`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `tmp_detail_jual` */

/*Table structure for table `tmp_edit_detail_beli` */

DROP TABLE IF EXISTS `tmp_edit_detail_beli`;

CREATE TABLE `tmp_edit_detail_beli` (
  `idDetailBeli` bigint(20) NOT NULL auto_increment,
  `idTransaksiBeli` bigint(20) NOT NULL,
  `idBarang` bigint(20) NOT NULL,
  `tglExpire` date NOT NULL,
  `jumBarang` int(10) NOT NULL,
  `hargaBeli` bigint(20) NOT NULL,
  PRIMARY KEY  (`idDetailBeli`)
) ENGINE=MyISAM AUTO_INCREMENT=112276 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `tmp_edit_detail_beli` */

/*Table structure for table `tmp_pesan_barang` */

DROP TABLE IF EXISTS `tmp_pesan_barang`;

CREATE TABLE `tmp_pesan_barang` (
  `username` varchar(30) collate latin1_general_ci NOT NULL,
  `idSupplier` int(3) NOT NULL,
  `idBarang` bigint(20) NOT NULL,
  `barcode` varchar(25) collate latin1_general_ci NOT NULL,
  `jumBarang` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `tmp_pesan_barang` */

/*Table structure for table `transaksibeli` */

DROP TABLE IF EXISTS `transaksibeli`;

CREATE TABLE `transaksibeli` (
  `idTransaksiBeli` bigint(20) NOT NULL auto_increment,
  `tglTransaksiBeli` date default NULL,
  `idSupplier` varchar(10) default NULL,
  `nominal` bigint(20) default '0',
  `idTipePembayaran` int(3) default NULL,
  `idUser` int(3) default NULL,
  `last_update` date default NULL,
  `NomorInvoice` varchar(15) NOT NULL default '0',
  `username` varchar(30) default NULL,
  PRIMARY KEY  (`idTransaksiBeli`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='latin1_swedish_ci';

/*Data for the table `transaksibeli` */

insert into `transaksibeli` (`idTransaksiBeli`,`tglTransaksiBeli`,`idSupplier`,`nominal`,`idTipePembayaran`,`idUser`,`last_update`,`NomorInvoice`,`username`) values (1,'2010-03-26','5',300000,2,NULL,'2010-03-26','001','ariefadmin');
insert into `transaksibeli` (`idTransaksiBeli`,`tglTransaksiBeli`,`idSupplier`,`nominal`,`idTipePembayaran`,`idUser`,`last_update`,`NomorInvoice`,`username`) values (2,'2010-03-26','3',310000,1,NULL,'2010-03-26','0','ariefadmin');

/*Table structure for table `transaksijual` */

DROP TABLE IF EXISTS `transaksijual`;

CREATE TABLE `transaksijual` (
  `idTransaksiJual` bigint(20) NOT NULL auto_increment,
  `tglTransaksiJual` datetime default NULL,
  `idCustomer` varchar(10) default NULL,
  `tglKirimBarang` date default NULL,
  `idTipePembayaran` int(3) default NULL,
  `nominal` bigint(20) default '0',
  `idUser` int(3) default NULL,
  `last_update` date default NULL,
  `uangDibayar` bigint(20) default NULL,
  PRIMARY KEY  (`idTransaksiJual`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `transaksijual` */

insert into `transaksijual` (`idTransaksiJual`,`tglTransaksiJual`,`idCustomer`,`tglKirimBarang`,`idTipePembayaran`,`nominal`,`idUser`,`last_update`,`uangDibayar`) values (1,'2010-03-26 20:32:45','1',NULL,1,4688,1,'2010-03-26',5000);
insert into `transaksijual` (`idTransaksiJual`,`tglTransaksiJual`,`idCustomer`,`tglKirimBarang`,`idTipePembayaran`,`nominal`,`idUser`,`last_update`,`uangDibayar`) values (2,'2010-03-26 20:36:14','1',NULL,1,19375,10,'2010-03-26',20000);

/*Table structure for table `transaksikas` */

DROP TABLE IF EXISTS `transaksikas`;

CREATE TABLE `transaksikas` (
  `idTransaksiKas` bigint(20) NOT NULL auto_increment,
  `tglTransaksiKas` date default NULL,
  `idUser` int(3) default NULL,
  `kasAwal` bigint(20) default NULL,
  `kasAkhir` bigint(20) default '0',
  `kasSeharusnya` bigint(20) default '0',
  PRIMARY KEY  (`idTransaksiKas`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `transaksikas` */

/*Table structure for table `transaksikasir` */

DROP TABLE IF EXISTS `transaksikasir`;

CREATE TABLE `transaksikasir` (
  `idTransKasir` bigint(20) NOT NULL auto_increment,
  `idUser` int(11) NOT NULL COMMENT 'idUser of the Cashier',
  `jumlahTransaksi` bigint(20) NOT NULL,
  `description` varchar(100) default NULL,
  `approvedBy` int(11) NOT NULL COMMENT 'idUser of the Approver',
  `tglTransaksi` datetime NOT NULL,
  PRIMARY KEY  (`idTransKasir`),
  KEY `idUser` (`idUser`,`tglTransaksi`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `transaksikasir` */

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `idUser` int(3) NOT NULL,
  `namaUser` varchar(30) collate latin1_general_ci default NULL,
  `idLevelUser` int(2) default NULL,
  `uname` varchar(30) collate latin1_general_ci default NULL,
  `pass` varchar(35) collate latin1_general_ci default NULL,
  `currentWorkstation` bigint(20) default NULL,
  PRIMARY KEY  (`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `user` */

insert into `user` (`idUser`,`namaUser`,`idLevelUser`,`uname`,`pass`,`currentWorkstation`) values (7,'admin',2,'admin','21232f297a57a5a743894a0e4a801fc3',NULL);
insert into `user` (`idUser`,`namaUser`,`idLevelUser`,`uname`,`pass`,`currentWorkstation`) values (8,'input',3,'input','a43c1b0aa53a0c908810c06ab1ff3967',NULL);
insert into `user` (`idUser`,`namaUser`,`idLevelUser`,`uname`,`pass`,`currentWorkstation`) values (10,'kasir1',4,'kasir1','29c748d4d8f4bd5cbc0f3f60cb7ed3d0',NULL);

/*Table structure for table `workstation` */

DROP TABLE IF EXISTS `workstation`;

CREATE TABLE `workstation` (
  `idWorkstation` bigint(20) NOT NULL auto_increment,
  `namaWorkstation` varchar(30) NOT NULL,
  `workstation_address` varchar(30) NOT NULL,
  `keterangan` varchar(50) default NULL,
  PRIMARY KEY  (`idWorkstation`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `workstation` */

insert into `workstation` (`idWorkstation`,`namaWorkstation`,`workstation_address`,`keterangan`) values (1,'kasir1','192.168.1.1',NULL);
insert into `workstation` (`idWorkstation`,`namaWorkstation`,`workstation_address`,`keterangan`) values (2,'server','192.168.1.250',NULL);

SET SQL_MODE=@OLD_SQL_MODE;
		";

	$hasil = mysql_query($sql, $targetdb) or die("Error: ".mysql_error());

};

?>



