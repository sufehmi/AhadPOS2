<?php

error_reporting(E_ALL);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

$beta = '';
if (!defined('SITE_URL')) { DEFINE ('SITE_URL',"http://".$_SERVER['SERVER_NAME'].$beta."/"); }
if (!defined('SITE_ROOT')) { DEFINE ('SITE_ROOT',$_SERVER["DOCUMENT_ROOT"].$beta."/"); }
if (!defined('BRAND_NAME')) { DEFINE ('BRAND_NAME','Ahad POS'); }

include_once(SITE_ROOT."config/config.php");
include_once(SITE_ROOT."tacoen/ta-function.php");

?>