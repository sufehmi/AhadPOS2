<?php
//include '../../config/config.php';
//mysql_close();
//
//$link = mysqli_connect($server, $username, $password) or die("Koneksi gagal");
//mysqli_select_db($link, $database) or die("Database tidak bisa dibuka");
//$result = mysqli_query($link, "select * from config");
//$namaToko = '';
//while ($config = mysqli_fetch_array($result)) :
//    if ($config['option'] === 'store_name'):
//        $namaToko = $config['value'];
//        break;
//    endif;
//endwhile;
$splash = '';
if (isset($_GET['sum']) && $_GET['sum'] === 'selesai'):
    $splash ="Printing.. <br />Struk No. {$_GET['struk']}";
    // $splashContent = 'Struk #'; maybe later
endif;
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

    <head>
        <meta charset="utf-8">
        <meta http-equiv="refresh" content="7; url=index.php?sum=splash" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ahadmart - Self Checkout</title>

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/foundation.css">
        <link rel="stylesheet" href="css/font-awesome.css">

        <!-- Ahadmart Style -->
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr.js"></script>
    </head>
    <body id="halaman-splash">
        <h4 style="text-align:center"><?php echo $splash; ?></h4>
        <script src="js/vendor/jquery.js"></script>
        <script src="js/foundation.min.js"></script>
    </body>
</html>