<?php

/* Default: hitung untuk 3 bulan terakhir */
$jumlahBulan = 3;

/*
 * ambil argument pertama, argument setelahnya diabaikan 
 * scriptnya bisa dipanggil seperti ini:
 * php hitung_penjualan.php 3 bulan pertama
 */
if (isset($argv[1]) && $argv[1] > 0) {
   $jumlahBulan = $argv[1];
}

$host = 'localhost';
$dbName = 'ahad_ceger';
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');

$dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";


try {
   $dbh = new PDO($dsn, MYSQL_USER, MYSQL_PASS);
   /*    * * echo a message saying we have connected ** */
   echo "Connected to database \n";

   /* drop table */
   $dropTable = $dbh->prepare('DROP TABLE IF EXISTS `data_penjualan`');
   $dropTable->execute();
   echo "Drop table \n";

   /* create table */
   $createTable = $dbh->prepare("CREATE TABLE IF NOT EXISTS `data_penjualan` (
                 `int` int(11) NOT NULL AUTO_INCREMENT,
                 `barcode` varchar(25) NOT NULL,
                 `penjualan` int(11) NOT NULL DEFAULT '0',
                 `rata_rata_mingguan` float NOT NULL,
                 `jumlah_bulan_terakhir` int(11) NOT NULL DEFAULT '3',
                 PRIMARY KEY (`int`),
                 KEY `barcode` (`barcode`)
               ) ENGINE=MyISAM DEFAULT CHARSET=latin1");
   $createTable->execute();
   echo "Create table \n";

   echo "Hitung penjualan {$jumlahBulan} bulan terakhir.. \n";
   /* Insert hasil perhitungan */
   $sql = " insert into `data_penjualan` (barcode, penjualan, rata_rata_mingguan, jumlah_bulan_terakhir)
            (select barcode, sum(jumBarang) penjualan, 
            sum(jumBarang ) / (:jumlahBulan * 4) as rata2_mingguan, 
            :jumlahBulan as jumlah_bulan_terakhir
            from detail_jual dj
            join transaksijual trx on dj.nomorStruk = trx.idTransaksiJual
            where trx.tglTransaksiJual between DATE_SUB(NOW(), INTERVAL :jumlahBulan MONTH) and now() 
            and barcode <> ''
            group by barcode)";

   $insert = $dbh->prepare($sql)->execute(array(':jumlahBulan' => $jumlahBulan));
   echo "Selesai \n";

   /*    * * close the database connection ** */
   $dbh = null;
} catch (PDOException $e) {
   echo $e->getMessage()."\n";
}
