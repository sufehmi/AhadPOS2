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


function getPersenSelisih($link, $ssId) {
    $sql = "select 
            (select count(*)
            from stok_stat_detail
            where stok_stat_id={$ssId} and (stok_tercatat - stok_sebenarnya != 0)
            ) selisih,
            (select count(*)
            from stok_stat_detail
            where stok_stat_id={$ssId}) total";
    $result = mysqli_query($link, $sql) or die('Gagal ambil selisih, error: ' . mysqli_error($link));
    $selisih = mysqli_fetch_array($result);
    return $selisih['selisih'] / $selisih['total'] * 100;
}
?>

<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $namaToko; ?> - Stok Stat</title>

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/foundation.css">
        <link rel="stylesheet" href="css/font-awesome.css">

        <!-- Ahadmart Stock Stat Style -->
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr.js"></script>

    </head>
    <body id="halaman-depan">
        <form method="POST" action="aksi.php?act=tambahrpo"> 
            <div class="row" style="margin-top: 20px;">
                <div class="small-6 columns">
                    <h4>Stok Stat</h4>
                </div>
                <div class="small-6 columns rata-kanan">                    
                    <a class="success button radius" href="../../sistem/media.php?module=barang&act=cetakbarang1"><i class="fa fa-home"></i> Ahadpos</a>
                    <a class="button radius" href="buat.php"><i class="fa fa-plus"></i> Buat</a>
                </div>
            </div>            
            <div class="row">
                <div class="small-12 columns"> 
                    <table style="width: 100%">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Keterangan</th>
                                <th class="rata-kanan">Persentase</th>
                                <th class="rata-tengah">Status</th>
                                <th class="rata-tengah">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT id, keterangan, updated_by, last_update, status "
                                    . "FROM stok_stat "
                                    . "ORDER BY status, last_update desc";
                            $result = mysqli_query($link, $query) or die('Gagal ambil list stok stat, error: ' . mysqli_error($link));
                            while ($ss = mysqli_fetch_array($result)):
                                ?>
                                <tr>
                                    <td><a href="input.php?id=<?php echo $ss['id']; ?>"><?php echo $ss['last_update']; ?></a></td>
                                    <td><?php echo $ss['keterangan']; ?></td>
                                    <td class="rata-kanan"><?php echo number_format(getPersenSelisih($link, $ss['id']), 2, ',', '.'); ?>%</td>
                                    <td class="rata-tengah"><?php echo $ss['status'] == 0 ? 'DRAFT' : 'OK'; ?></td>
                                    <td class="rata-tengah"><a href="aksi.php?act=hapusss&id=<?php echo $ss['id']; ?>" class="tiny radius alert button tombol-tabel"><i class="fa fa-times"></i></a></td>
                                </tr>
                                <?php
                            endwhile;
                            ?>
                        </tbody>
                    </table>
                </div> 
            </div>
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

