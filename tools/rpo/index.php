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
?>

<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ahadmart - RPO</title>

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/foundation.css">
        <link rel="stylesheet" href="css/font-awesome.css">

        <!-- Ahadmart RPO Style -->
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr.js"></script>

    </head>
    <body id="halaman-depan">
        <form method="POST" action="aksi.php?act=tambahrpo"> 
            <div class="row" style="margin-top: 20px;">
                <div class="small-6 columns">
                    <h4>Purchase Order</h4>
                </div>
                <div class="small-6 columns rata-kanan">                    
                    <a class="success button radius" href="../../sistem/media.php?module=pembelian_barang"><i class="fa fa-home"></i> Ahadpos</a>
                    <a class="button radius" href="buat.php"><i class="fa fa-plus"></i> Buat RPO</a>
                </div>
            </div>            
            <div class="row">
                <div class="small-12 columns"> 
                    <table style="width: 100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th class="rata-kanan">Total</th>
                                <th class="rata-tengah">Status</th>
                                <th class="rata-tengah">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "select po.id, po.tanggal_buat, supplier.namaSupplier, `status`,
                                            (select ifnull(sum(jumlah_order * harga_beli_terakhir),0)
                                            from purchase_order_detail pod
                                            where pod.purchase_order_id = po.id
                                            ) total
                                        from purchase_order po
                                        join supplier on supplier.idSupplier = po.supplier_id 
                                        where po.updated_by = '{$_SESSION['uname']}' 
                                        order by `status`, id desc 
                                        limit 50";
                            $result = mysqli_query($link, $query) or die('Gagal ambil list po, error: ' . mysqli_error($link));
                            while ($po = mysqli_fetch_array($result)):
                                ?>
                                <tr>
                                    <td><a href="input.php?id=<?php echo $po['id']; ?>"><?php echo $po['tanggal_buat']; ?></a></td>
                                    <td><?php echo $po['namaSupplier']; ?></td>
                                    <td class="rata-kanan"><?php echo number_format($po['total'], 0, ',', '.'); ?></td>
                                    <td class="rata-tengah"><?php echo $po['status'] == 0 ? 'RPO' : 'PO'; ?></td>
                                    <td class="rata-tengah"><a href="aksi.php?act=hapuspo&poid=<?php echo $po['id']; ?>" class="tiny radius alert button tombol-tabel"><i class="fa fa-times"></i></a></td>
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

