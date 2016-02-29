<?php
$beta = '/2';
DEFINE ('SITE_URL',"http://".$_SERVER['SERVER_NAME'].$beta."/");
DEFINE ('SITE_ROOT',$_SERVER["DOCUMENT_ROOT"].$beta."/");

function e($s) { echo $s; }

function check_ahadpos_session() {
	if (empty($_SESSION['namauser']) AND empty($_SESSION['passuser'])){
		die('Untuk mengakses modul, Anda harus <a href="/">login</a>');
	}
}


function ahp_htmlheader($title,$add_to_head='') {
	echo "<!DOCTYPE html>\n";
	echo "<html lang='en'>\n";
	echo "<head>\n";
	echo "<meta charset='UTF-8' />\n";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
	echo "<meta http-equiv='X-UA-Compatible' content='IE=edge' />\n";
	echo "<meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0'>\n";
	echo "<title>AhadPOS: $title</title>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery-1.9.1.min.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/interface.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery.form.min.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery-ui.min-ac.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery.simple-dtpicker.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."bootstrap/js/bootstrap.min.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/ahadpos.js'></script>\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."css/jquery-ui-ac.min.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."css/jquery.simple-dtpicker.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."css/font/fa/style.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."bootstrap/css/bootstrap.min.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."bootstrap/css/bootstrap-theme.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."css/ap.css' />\n";
	if ($add_to_head!='') { echo $add_to_head; }
	echo "</head>";

}

function ahp_popupheader($title,$add_to_head='') {
	echo "<!DOCTYPE html>\n";
	echo "<html lang='en'>\n";
	echo "<head>\n";
	echo "<meta charset='UTF-8' />\n";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
	echo "<meta http-equiv='X-UA-Compatible' content='IE=edge' />\n";
	echo "<meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0'>\n";
	echo "<title>AhadPOS: $title</title>\n";
	echo "<!--ahp_popupheader-->";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery-1.9.1.min.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/ahadpos.js'></script>\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."css/ap.css' />\n";
	if ($add_to_head!='') { echo $add_to_head; }
	echo "</head>";
}

?>
