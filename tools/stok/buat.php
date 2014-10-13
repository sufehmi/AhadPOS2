<?php
include '../../config/config.php';
mysql_close();

session_start();
if (empty($_SESSION['namauser'])) {
    ?>
    <link href='../../css/style.css' rel='stylesheet' type='text/css'>
    <center>Untuk mengakses tools ini, Anda harus login <br>
        <a href="../../index.php"><b>LOGIN</b></a></center>
    <?php
    die();
}

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
?>

<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $namaToko; ?> - Buat Stok Stat</title>

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/foundation.css">

        <!-- Ahadmart Stok Style -->
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr.js"></script>

    </head>
    <body id="halaman-buat">
        <form method="POST" action="aksi.php?act=tambahstokstat"> 
            <div class="row">
                <div class="small-12 columns">
                    <h4>Buat Stok Stat</h4>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" />
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <input type="submit" value="Submit" class="button radius" />
                </div>
            </div>
        </form>
        <div id="footer">
            <span></span>
        </div>
        <script src="js/vendor/jquery.js"></script>
        <script src="js/foundation.min.js"></script>
        <script>
            $(document).foundation();
        </script>
    </body>
</html>

