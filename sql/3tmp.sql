--
-- Table structure for table `tmp`
--

CREATE TABLE IF NOT EXISTS `tmp` (
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
