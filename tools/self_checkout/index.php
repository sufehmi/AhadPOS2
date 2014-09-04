<?php
include '../../config/config.php';
mysql_close();

$link = mysqli_connect($server, $username, $password) or die("Koneksi gagal");
mysqli_select_db($link, $database) or die("Database tidak bisa dibuka");
$result = mysqli_query($link, "select * from config");
$namaToko = '';
while ($config = mysqli_fetch_array($result)) :
    if ($config['option'] === 'store_name'):
        $namaToko = $config['value'];
        break;
    endif;
endwhile;

if (isset($_GET['sum']) && $_GET['sum'] === 'batal'):
    $clientIP = $_SERVER['REMOTE_ADDR'];
    mysqli_query($link, "DELETE FROM self_checkout_temp WHERE ip4 = '{$clientIP}'") or die('Gagal membersihkan data');
endif;
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ahadmart - Self Checkout</title>

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/foundation.css">

        <!-- Ahadmart Style -->
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr.js"></script>

    </head>
    <body id="halaman-depan">
        <h4>This lane is</h4>
        <h1>Open</h1>
        <h5>Touch to start</h5>
        <?php
        ?>
        <div id="footer">
            <span><?php echo $namaToko; ?></span>
        </div>
        <script src="js/vendor/jquery.js"></script>
        <script src="js/foundation.min.js"></script>
        <script>
            $(document).foundation();
            $("body").click(function() {
                window.location.replace("input.php?sum=index");
            });
        </script>
    </body>
</html>