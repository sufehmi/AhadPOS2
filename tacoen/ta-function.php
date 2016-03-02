<?php

function e($s) { echo $s; }

function ta_logingagal() {
	e("Login Gagal! <a href='".SITE_URL."'>login ulang</a>"); exit;
}

function check_ahadpos_session() {
	if (empty($_SESSION['namauser']) AND empty($_SESSION['passuser'])){
		e("Login Expire! <a href='".SITE_URL."'>login ulang</a>"); exit;
	}
}

// dari 2b


function ahp_htmlheader($title,$add_to_head='') {
	echo "<!DOCTYPE html>\n";
	echo "<html lang='en'>\n";
	echo "<head>\n";
	echo "<meta charset='UTF-8' />\n";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
	echo "<meta http-equiv='X-UA-Compatible' content='IE=edge' />\n";
	echo "<meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0'>\n";
	echo "<title>AhadPOS: $title</title>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery.min.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/interface.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery.form.min.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery-ui.min-ac.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery.simple-dtpicker.js'></script>\n";
	//echo "<script type='text/javascript' src='".SITE_URL."tacoen/bootstrap/js/bootstrap.min.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."tacoen/js/ahadpos.js'></script>\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."tacoen/font/fa/style.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."tacoen/font/dosis/style.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."css/jquery-ui-ac.min.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."css/jquery.simple-dtpicker.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."tacoen/bootstrap/css/bootstrap.min.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."tacoen/bootstrap/css/bootstrap-theme.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."tacoen/css/ap.css' />\n";
	echo "<link rel='apple-touch-icon' sizes='57x57' href='".SITE_URL."tacoen/icons/apple-touch-icon-57x57.png'>\n";
	echo "<link rel='apple-touch-icon' sizes='60x60' href='".SITE_URL."tacoen/icons/apple-touch-icon-60x60.png'>\n";
	echo "<link rel='icon' type='image/png' href='".SITE_URL."tacoen/icons/favicon-32x32.png' sizes='32x32'>\n";
	echo "<link rel='icon' type='image/png' href='".SITE_URL."tacoen/icons/favicon-16x16.png' sizes='16x16'>\n";
	echo "<link rel='manifest' href='".SITE_URL."tacoen/icons/manifest.json'>\n";
	echo "<link rel='mask-icon' href='".SITE_URL."tacoen/icons/safari-pinned-tab.svg' color='#5bbad5'>\n";
	echo "<meta name='msapplication-TileColor' content='#00aba9'>\n";
	echo "<meta name='theme-color' content='#ffffff'>\n";
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
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery-1.9.1.min.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/interface.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."/js/jquery-ui.min-ac.js'></script>\n";	
	echo "<script type='text/javascript' src='".SITE_URL."js/ahadpos.js'></script>\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."css/ap.css' />\n";
	echo "<link rel='apple-touch-icon' sizes='57x57' href='".SITE_URL."tacoen/icons/apple-touch-icon-57x57.png'>\n";
	echo "<link rel='apple-touch-icon' sizes='60x60' href='".SITE_URL."tacoen/icons/apple-touch-icon-60x60.png'>\n";
	echo "<link rel='icon' type='image/png' href='".SITE_URL."tacoen/icons/favicon-32x32.png' sizes='32x32'>\n";
	echo "<link rel='icon' type='image/png' href='".SITE_URL."tacoen/icons/favicon-16x16.png' sizes='16x16'>\n";
	echo "<link rel='manifest' href='".SITE_URL."tacoen/icons/manifest.json'>\n";
	echo "<link rel='mask-icon' href='".SITE_URL."tacoen/icons/safari-pinned-tab.svg' color='#5bbad5'>\n";
	echo "<meta name='msapplication-TileColor' content='#00aba9'>\n";
	echo "<meta name='theme-color' content='#ffffff'>\n";
	
	if ($add_to_head!='') { echo $add_to_head; }
	echo "</head>";
}

function ahp_kasirheader($title,$add_to_head='') {
	echo "<!DOCTYPE html>\n";
	echo "<html lang='en'>\n";
	echo "<head>\n";
	echo "<meta charset='UTF-8' />\n";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";
	echo "<meta http-equiv='X-UA-Compatible' content='IE=edge' />\n";
	echo "<meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0'>\n";
	echo "<title>AhadPOS: $title</title>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery-1.9.1.min.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery.poshytip.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."js/jquery-editable-poshytip.min.js'></script>\n";
	echo "<script type='text/javascript' src='".SITE_URL."tacoen/js/kasir.js'></script>\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."tacoen/font/fa/style.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."tacoen/font/dosis/style.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."tacoen/bootstrap/css/bootstrap.min.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."tacoen/bootstrap/css/bootstrap-theme.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."css/jquery-editable.css' />\n";
	echo "<link rel='stylesheet' type='text/css' href='".SITE_URL."tacoen/css/ap.css' />\n";
	echo "<link rel='apple-touch-icon' sizes='57x57' href='".SITE_URL."tacoen/icons/apple-touch-icon-57x57.png'>\n";
	echo "<link rel='apple-touch-icon' sizes='60x60' href='".SITE_URL."tacoen/icons/apple-touch-icon-60x60.png'>\n";
	echo "<link rel='icon' type='image/png' href='".SITE_URL."tacoen/icons/favicon-32x32.png' sizes='32x32'>\n";
	echo "<link rel='icon' type='image/png' href='".SITE_URL."tacoen/icons/favicon-16x16.png' sizes='16x16'>\n";
	echo "<link rel='manifest' href='".SITE_URL."tacoen/icons/manifest.json'>\n";
	echo "<link rel='mask-icon' href='".SITE_URL."tacoen/icons/safari-pinned-tab.svg' color='#5bbad5'>\n";
	echo "<meta name='msapplication-TileColor' content='#00aba9'>\n";
	echo "<meta name='theme-color' content='#ffffff'>\n";
	if ($add_to_head!='') { echo $add_to_head; }
	echo "</head>";

}

function ahad_homepage() {

	$kas=getKasAwal($_SESSION[iduser]);
	$uang=getUangKasir($_SESSION[iduser]); ?>
	<div class='row'>
	<div class='col-md-8 col-lg-8'>

	<h1 style='margin-top:0'>Hi! <?php echo $_SESSION[namauser]; ?>.</h1>
	
	<p>Waktu Login Saat ini: <?php echo tgl_indo(date("Y m d")); ?> | <?php echo date("H:i"); ?> </p>
	
	<pre><?php print_r($_SESSION); ?></pre>

	</div><div class='col-md-4 col-lg-4'>

	<div class="panel panel-primary">
	<div class="panel-heading">Uang Transaksi</div>
	<div class="panel-body"><b>Rp. <?php echo $uang; ?></b></div>
	</div>

	<div class="panel panel-success">
	<div class="panel-heading">Kas Awal</div>
	<div class="panel-body"><b>Rp. <?php echo $kas; ?></b></div>
	</div>
	
	<div class="panel panel-danger">
	<div class="panel-heading">Akses ID</div>
	<div class="panel-body"><b><?php echo $_SESSION[leveluser]; ?> / <?php echo $_SESSION[iduser]; ?></b></div>
	</div>
	
	</div>
	</div>

<?php }
?>