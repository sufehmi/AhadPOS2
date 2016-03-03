<?php

/* Default: hitung untuk 180 hari terakhir */
$jumlahHari = 180;

/*
 * ambil argument pertama, argument setelahnya diabaikan 
 * scriptnya bisa dipanggil seperti ini:
 * php hitung_total_barang_aging.php 180 hari terakhir
 */
if (isset($argv[1]) && $argv[1] > 0) {
    $jumlahHari = $argv[1];
}

// Connecting, selecting database
include __DIR__ . "/../config/config.php";
mysql_close();

$host = $server;
$dbName = $database;
define('MYSQL_USER', $username);
define('MYSQL_PASS', $password);

$dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";


try {
    $dbh = new PDO($dsn, MYSQL_USER, MYSQL_PASS);
    /*     * * echo a message saying we have connected ** */
    echo "Connected to database \n";

    $namaTabel = 'tmp_total_aging';
    /* drop table */
    $dropTable = $dbh->prepare("DROP TABLE IF EXISTS `{$namaTabel}`");
    $dropTable->execute();
    echo "Drop table \n";

    /* create table */
    $createTable = $dbh->prepare("CREATE TABLE IF NOT EXISTS `{$namaTabel}` (
                 `total_nilai_stok` decimal(18,0) NOT NULL DEFAULT '0',
                 `jumlah_hari_terakhir` int(11) NOT NULL DEFAULT '180',
                 `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
               ) ENGINE=MyISAM DEFAULT CHARSET=latin1");
    $createTable->execute();
    echo "Create table \n";

    echo "Hitung Total Barang Aging {$jumlahHari} hari terakhir.. \n";
    /* Insert hasil perhitungan */
    $sql = "INSERT INTO `{$namaTabel}` (total_nilai_stok, jumlah_hari_terakhir)
            SELECT 
                SUM(dbAgregat.nilaistok / dbAgregat.sisastok * barang.jumBarang)  nilai, :jumlahHari
            FROM
                barang
                    JOIN
                (SELECT 
                    barcode,
                        SUM(jumBarang) sisastok,
                        SUM(jumBarang * hargaBeli) nilaistok,
                        MAX(tb.tglTransaksiBeli) maxTglTransaksiBeli
                FROM
                    detail_beli db
                JOIN transaksibeli tb ON db.idTransaksiBeli = tb.idTransaksiBeli
                    AND TIMESTAMPDIFF(DAY, tb.tglTransaksiBeli, NOW()) > :jumlahHari
                WHERE
                    db.isSold = 'N'
                GROUP BY db.barcode
                ) AS dbAgregat ON barang.barcode = dbAgregat.barcode";
                
    $insert = $dbh->prepare($sql)->execute(array(':jumlahHari' => $jumlahHari));
    echo "Selesai \n";

    /*     * * close the database connection ** */
    $dbh = null;
} catch (PDOException $e) {
    echo $e->getMessage() . "\n";
}
