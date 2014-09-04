ALTER TABLE  `tmp_detail_jual` ADD  `diskon_persen` INT( 11 ) NOT NULL DEFAULT  '0';
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
