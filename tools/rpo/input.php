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
if (isset($_GET['id'])) {
    $poId = $_GET['id'];
    $query = "select po.tanggal_buat, po.`range`, po.`status`, po.buffer, po.jumlah_hari_persediaan, supplier.namaSupplier
            from purchase_order po
            join supplier on supplier.idSupplier = po.supplier_id
            where id={$poId}";
    $result = mysqli_query($link, $query) or die('Gagal ambil data PO #' . $poId . '. error: ' . mysqli_error($link));
    $po = mysqli_fetch_array($result);
}
else {
    die('Wrong Request!!');
}
?>

<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ahadmart - PO Input</title>

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/foundation.css">
        <link rel="stylesheet" href="css/font-awesome.css">

        <!-- Ahadmart RPO Style -->
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr.js"></script>

    </head>
    <body id="halaman-depan">
        <div class="row">
            <div class="small-12 columns">
                <?php
                if ($po['status'] == 0) {
                    ?>
                    <h4>RPO (Rencana Purchase Order) #<?php echo $poId; ?></h4>
                    <?php
                }
                else {
                    ?>
                    <h4>PO (Purchase Order) #<?php echo $poId; ?></h4>
                    <?php
                }
                ?>
                <h5><?php echo $po['namaSupplier']; ?></h5>
                <?php
                if ($po['status'] == 0):
                    ?>
                    <h6><small>Range</small><?php echo $po['range']; ?>hari <small>Buffer</small><?php echo $po['buffer']; ?>% <small>Persediaan</small><?php echo $po['jumlah_hari_persediaan']; ?>hari</h6>
                    <?php
                endif;
                ?>
                <a href="index.php" class="small button" ><i class="fa fa-list"></i> Index</a>
                <?php
                if ($po['status'] == 0) {
                    ?>
                    <a href="" class="small button" id="tombol-input"><i class="fa fa-arrows-v"></i> Input</a>
                    <a href="" class="small button" id="tombol-simpan"><i class="fa fa-save"></i> Simpan</a>
                    <?php
                }
                else {
                    ?>
                    <!--<a href="" class="small button" id="tombol-print"><i class="fa fa-print"></i> Print</a>-->
                    <a href="aksi.php?act=csv&poId=<?php echo $poId; ?>" class="small button" id="tombol-csv"><i class="fa fa-download"></i> CSV</a>
                    <?php
                }
                ?>
            </div>
            <?php
            $query = "select distinct barang.idRak, rak.namaRak
                        from purchase_order_detail pod
                        join barang on barang.barcode = pod.barcode
                        join rak on rak.idRak = barang.idRak
                        where purchase_order_id = {$poId} and pod.jumlah_order is null 
                        order by rak.idrak";
            $result = mysqli_query($link, $query) or die('Gagal ambil data rak, error: ' . mysql_error());
            ?>
            <?php
            if ($po['status'] == 0):
                ?>
                <div id="input" >
                    <div class="small-4 medium-6 columns">
                        <select id="pilih-rak">
                            <option value="-1">Semua Rak</option>
                            <?php
                            while ($rak = mysqli_fetch_array($result)):
                                ?>
                                <option value="<?php echo $rak['idRak']; ?>"><?php echo $rak['namaRak']; ?></option>
                                <?php
                            endwhile;
                            ?>
                        </select>
                    </div>
                    <?php
                    /*
                     * Menampilkan barang yang belum di order
                     */
                    $query = "select 
                        pod.barcode, 
                        barang.namaBarang, 
                        pod.harga_beli_terakhir, 
                        pod.stok_saat_ini, 
                        pod.avg_daily_sales, 
                        pod.saran_order, 
                        pod.jumlah_order 
                        from purchase_order_detail pod
                        join barang on barang.barcode = pod.barcode
                        where purchase_order_id = {$poId} and pod.jumlah_order is null order by barang.namaBarang";
                    $result = mysqli_query($link, $query) or die('Gagal ambil po detail #' . $poId . '. error: ' . mysqli_error($link));
                    ?>
                    <div class="small-12 columns">
                        <table style="width: 100%" class="tabel-data">
                            <thead>
                                <tr>
                                    <th>Barcode</th>
                                    <th>Nama Barang</th>
                                    <th class="rata-kanan">Harga</th>
                                    <th class="rata-kanan">Stok</th>
                                    <th class="rata-tengah">Hitung</th>
                                    <th class="rata-kanan">Avg Daily Sales</th>
                                    <th class="rata-kanan">Saran Order</th>
                                    <th class="rata-kanan">Jumlah Order</th>
                                    <th class="rata-kanan">Sub Total</th>
                                    <th class="rata-tengah">Order</th>
                                </tr>
                            </thead>
                            <tbody id="data-barang">
                                <?php
                                while ($poDetail = mysqli_fetch_array($result)):
                                    ?>
                                    <tr>
                                        <td><?php echo $poDetail['barcode']; ?></td>
                                        <td><?php echo $poDetail['namaBarang']; ?></td>
                                        <td class="rata-kanan"><?php echo number_format($poDetail['harga_beli_terakhir'], 0, ',', '.'); ?></td>
                                        <td class="rata-kanan"><?php echo $poDetail['stok_saat_ini']; ?></td>
                                        <td class="rata-tengah"><a class="tiny button tombol-hitung tombol-tabel" data-barcode="<?php echo $poDetail['barcode']; ?>"><i class="fa fa-refresh"></i></a></td>
                                        <td class="rata-kanan avg_daily_sales"><?php echo $poDetail['avg_daily_sales']; ?></td>
                                        <td class="rata-kanan saran_order"><?php echo $poDetail['saran_order']; ?></td>
                                        <td class="rata-kanan">
                                            <?php
                                            /*
                                             * Jika rata-rata penjualan harian belum dihitung
                                             * input jumlah order belum ditampilkan
                                             */
                                            if (!is_null($poDetail['avg_daily_sales'])):
                                                ?>
                                                <input class="jumlah_order" type="text" name="jumlah_order" data-barcode="<?php echo $poDetail['barcode']; ?>" data-harga="<?php echo $poDetail['harga_beli_terakhir']; ?>"/>
                                                <?php
                                            endif;
                                            ?>
                                        </td>
                                        <td class="rata-kanan sub_total"></td>
                                        <td><a class="rata-tengah tiny button tombol-order tombol-tabel" data-barcode="<?php echo $poDetail['barcode']; ?>"><i class="fa fa-check"></i></a></td>
                                    </tr>
                                    <?php
                                endwhile;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
            endif;

            /*
             * Menampilkan barang yang sudah di order
             */
            $query = "select 
                        pod.barcode, 
                        barang.namaBarang, 
                        pod.harga_beli_terakhir, 
                        pod.stok_saat_ini, 
                        pod.avg_daily_sales, 
                        pod.saran_order, 
                        pod.jumlah_order 
                        from purchase_order_detail pod
                        join barang on barang.barcode = pod.barcode
                        where purchase_order_id = {$poId} and pod.jumlah_order > 0 order by barang.namaBarang";
            $result = mysqli_query($link, $query) or die('Gagal ambil po detail #' . $poId . '. error: ' . mysqli_error($link));
            ?>
            <div class="small-12 columns">
                <table style="width: 100%" class="tabel-data-order">
                    <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Nama Barang</th>
                            <th class="rata-kanan">Harga</th>
                            <!--<th class="rata-kanan">Stok</th>-->
                            <!--<th class="rata-kanan">Avg Daily Sales</th>-->
                            <!--<th class="rata-kanan">Saran Order</th>-->
                            <th class="rata-kanan">Jumlah Order</th>
                            <th class="rata-kanan">Sub Total</th>
                            <?php
                            /*
                             * Jika status masih rpo, tampilkan tombol hapus
                             */
                            if ($po['status'] == 0):
                                ?>
                                <th class="rata-tengah">Hapus</th>
                                <?php
                            endif;
                            ?>
                        </tr>
                    </thead>
                    <tbody id="data-barang-order">
                        <?php
                        while ($poDetail = mysqli_fetch_array($result)):
                            ?>
                            <tr>
                                <td><?php echo $poDetail['barcode']; ?></td>
                                <td><?php echo $poDetail['namaBarang']; ?></td>
                                <td class="rata-kanan"><?php echo number_format($poDetail['harga_beli_terakhir'], 0, ',', '.'); ?></td>
                                <!--<td class="rata-kanan"><?php echo $poDetail['stok_saat_ini']; ?></td>-->
                                <!--<td class="rata-kanan avg_daily_sales"><?php echo $poDetail['avg_daily_sales']; ?></td>-->
                                <!--<td class="rata-kanan saran_order"><?php echo $poDetail['saran_order']; ?></td>-->
                                <td class="rata-kanan"><?php echo $poDetail['jumlah_order']; ?></td>
                                <td class="rata-kanan sub_total"><?php echo $poDetail['jumlah_order'] * $poDetail['harga_beli_terakhir']; ?></td>
                                <?php
                                /*
                                 * Jika status masih rpo, tampilkan tombol hapus
                                 */
                                if ($po['status'] == 0):
                                    ?>
                                    <td class="rata-tengah"><a class="tiny alert radius button tombol-hapus tombol-tabel" data-barcode="<?php echo $poDetail['barcode']; ?>"><i class="fa fa-times"></i></a></td>
                                    <?php
                                endif;
                                ?>
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

            $("#tombol-input").click(function() {
                $("#input").slideToggle();
                return false;
            });

            $("#tombol-simpan").click(function() {
                var dataKirim = {
                    poId: <?php echo $poId; ?>,
                };
                $.ajax({
                    type: "POST",
                    url: 'aksi.php?act=simpan',
                    data: dataKirim,
                    dataType: "json",
                    success: function(data) {
                        if (data.sukses) {
                            window.location.reload();
                        }
                    }
                });
                return false;
            });

            $("#pilih-rak").change(function() {
                $("#data-barang").load('aksi.php?act=getbarang&po_id=<?php echo $poId; ?>&rak_id=' + $(this).val());
            });

            $(document).on("click", ".tombol-hitung", function() {
                /*
                 * Ambil & kirim data barcode yang akan dihitung
                 */
                var curRow = $(this);
                curRow.html('<i class="fa fa-refresh fa-spin"></i>')
                var barcode = curRow.data("barcode");
                console.log(barcode);
                var dataKirim = {
                    poId: <?php echo $poId; ?>,
                    barcode: barcode
                };
                $.ajax({
                    type: "POST",
                    url: 'aksi.php?act=hitung',
                    data: dataKirim,
                    dataType: "json",
                    success: function(data) {
                        if (data.sukses) {
                            curRow.parents('tr').children(".avg_daily_sales").text(data.avg_daily_sales);
                            curRow.parents('tr').children(".saran_order").text(data.saran_order);
                            refreshTabel();
                        }
                        curRow.html('<i class="fa fa-refresh"></i>');
                    }
                });
            });

            $(document).on("click", ".tombol-order", function() {
                var jumlahOrder = $(this).parents('tr').find(".jumlah_order");
                var qty = jumlahOrder.val();
                var barcode = jumlahOrder.data("barcode");
                //console.log(barcode + " -- " + qty);
                var dataKirim = {
                    poId: <?php echo $poId; ?>,
                    barcode: barcode,
                    qty: qty
                };
                $.ajax({
                    type: "POST",
                    url: 'aksi.php?act=order',
                    data: dataKirim,
                    dataType: "json",
                    success: function(data) {
                        if (data.sukses) {
                            refreshTabel();
                        }
                    }
                });
            });

            $(document).on("click", ".tombol-hapus", function() {
                var barcode = $(this).data("barcode");
                var dataKirim = {
                    poId: <?php echo $poId; ?>,
                    barcode: barcode
                };
                $.ajax({
                    type: "POST",
                    url: 'aksi.php?act=hapus',
                    data: dataKirim,
                    dataType: "json",
                    success: function(data) {
                        if (data.sukses) {
                            refreshTabel();
                        }
                    }
                });
            });

            $(document).on("change", ".jumlah_order", function() {
                var jumlahOrder = $(this);
                var qty = jumlahOrder.val();
                var harga = jumlahOrder.data("harga");
                var subTotal = harga * qty;
                jumlahOrder.parents('tr').children(".sub_total").text(subTotal);

            });

            function refreshTabel() {
                var rakId = $("#pilih-rak").val();
                $("#data-barang").load('aksi.php?act=getbarang&po_id=<?php echo $poId; ?>&rak_id=' + rakId);
                $("#data-barang-order").load('aksi.php?act=getbarangorder&po_id=<?php echo $poId; ?>');
            }
        </script>
    </body>
</html>