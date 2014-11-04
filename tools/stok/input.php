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
    $stokStatId = $_GET['id'];
    $query = "select keterangan, status
            from stok_stat
            where id={$stokStatId}";
    $result = mysqli_query($link, $query) or die('Gagal ambil data StokStat #' . $stokStatId . '. error: ' . mysqli_error($link));
    $stokStat = mysqli_fetch_array($result);
}
else {
    die('Wrong Request!!');
}
$result = mysqli_query($link, "select * from config");
$namaToko = '';
while ($config = mysqli_fetch_array($result)) :
    if ($config['option'] === 'store_name'):
        $namaToko = $config['value'];
        break;
    endif;
endwhile;

$hal = 0;
$mulai = 0;
$idRak = null;
$jumBaris = 10;
if (isset($_GET['jumBaris'])) {
    $jumBaris = $_GET['jumBaris'];
}

if (isset($_GET['hal'])) {
    $hal = $_GET['hal'];
    $mulai = $jumBaris * $hal;
}
if (isset($_GET['idRak']) && $_GET['idRak'] != '' && $_GET['idRak'] > 0) {
    $idRak = $_GET['idRak'];
}

$barcodeCari = null;
if (isset($_GET['barcode']) && ($_GET['barcode'] != '')) {
    $barcodeCari = $_GET['barcode'];
}

function getItemSelisih($link, $ssId) {
    $sql = "select
            (select count(*)
            from stok_stat_detail
            where stok_stat_id={$ssId} and (stok_tercatat - stok_sebenarnya != 0)
            ) selisih,
            (select count(*)
            from stok_stat_detail
            where stok_stat_id={$ssId}) total,
            (SELECT ifnull(count(*),0)
            FROM stok_stat_detail
            WHERE stok_stat_id = {$ssId} AND stok_sebenarnya - stok_tercatat < 0) selisih_minus_item,
            (SELECT ifnull(sum(stok_sebenarnya - stok_tercatat),0)
            FROM stok_stat_detail
            WHERE stok_stat_id = {$ssId} AND stok_sebenarnya - stok_tercatat < 0) selisih_minus,
            (SELECT  ifnull(count(*),0)
            FROM stok_stat_detail
            WHERE stok_stat_id = {$ssId} AND stok_sebenarnya - stok_tercatat > 0) selisih_plus_item,
            (SELECT ifnull(sum(stok_sebenarnya - stok_tercatat),0)
            FROM stok_stat_detail
            WHERE stok_stat_id = {$ssId} AND stok_sebenarnya - stok_tercatat > 0) selisih_plus,
            (SELECT ifnull(sum(stok_sebenarnya),0)
            FROM stok_stat_detail
            WHERE stok_stat_id = {$ssId}) stok_sebenarnya,
            (SELECT ifnull(sum(stok_tercatat),0)
            FROM stok_stat_detail
            WHERE stok_stat_id = {$ssId}) stok_tercatat ";
    $result = mysqli_query($link, $sql) or die('Gagal ambil selisih, error: ' . mysqli_error($link));
    $selisih = mysqli_fetch_array($result);
    return array(
        'selisih' => $selisih['selisih'],
        'total' => $selisih['total'],
        'selisih_minus_item' => $selisih['selisih_minus_item'],
        'selisih_minus' => $selisih['selisih_minus'],
        'selisih_plus_item' => $selisih['selisih_plus_item'],
        'selisih_plus' => $selisih['selisih_plus'],
        'stok_sebenarnya' => $selisih['stok_sebenarnya'],
        'stok_tercatat' => $selisih['stok_tercatat']
    );
}
?>

<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $namaToko; ?> - Stok Stat Input</title>

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/foundation.css">
        <link rel="stylesheet" href="css/font-awesome.css">

        <!-- Ahadmart StokStat Style -->
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr.js"></script>

    </head>
    <body id="halaman-input">
        <div class="row">
            <div class="small-12 columns">
                <?php
                if ($stokStat['status'] == 0) {
                    ?>
                    <h4>Stok Stat (draft) #<?php echo $stokStatId; ?></h4>
                    <?php
                }
                else {
                    ?>
                    <h4>Stok Stat #<?php echo $stokStatId; ?></h4>
                    <?php
                }
                ?>
                <h6><small>keterangan</small> <?php echo $stokStat['keterangan']; ?></h6>

                <?php
                $selisih = getItemSelisih($link, $stokStatId)
                ?>

                <a href="index.php" class="small button" ><i class="fa fa-list"></i> Index</a>
                <?php
                if ($stokStat['status'] == 0) {
                    ?>
                    <a href="" class="small button" id="tombol-input"><i class="fa fa-arrows-v"></i> Input</a>
                    <a href="" class="small button" id="tombol-simpan"><i class="fa fa-save"></i> Simpan</a>
                    <?php
                }
                ?>
            </div>
            <div class="medium-6 columns">
                <table width="100%">
                    <tr>
                        <td>Total Item</td>
                        <td><?php echo $selisih['total']; ?> item</td>
                    </tr>
                    <tr>
                        <td>Item yang selisih</td>
                        <td>
                            <?php
                            echo $selisih['selisih'];
                            ?>
                            (<?php
                            echo number_format($selisih['selisih'] / $selisih['total'] * 100, 2, ',', '.') . '%';
                            ?>)
                        </td>
                    </tr>
                    <tr>
                        <td>Selisih Plus</td>
                        <td>
                            <?php
                            echo $selisih['selisih_plus_item'] . ' item ';
                            echo "({$selisih['selisih_plus']})";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Selisih Minus</td>
                        <td>
                            <?php
                            echo $selisih['selisih_minus_item'] . ' item ';
                            echo "({$selisih['selisih_minus']})";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Total Stok Tercatat (qty)</td>
                        <td>
                            <?php
                            echo $selisih['stok_tercatat'];
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Total Stok Sebenarnya (qty)</td>
                        <td>
                            <?php
                            echo $selisih['stok_sebenarnya'];
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
            $query = "select *
                        from rak
                        order by namaRak";
            $result = mysqli_query($link, $query) or die('Gagal ambil data rak, error: ' . mysql_error());
            ?>
            <?php
            if ($stokStat['status'] == 0):
                ?>
                <div id="input">
                    <div class="medium-6 columns">
                        <select id="pilih-rak">
                            <option value="-1">Semua Rak</option>
                            <?php
                            while ($rak = mysqli_fetch_array($result)):
                                ?>
                                <option value="<?php echo $rak['idRak']; ?>" <?php echo $idRak == $rak['idRak'] ? 'selected' : ''; ?>><?php echo $rak['namaRak']; ?></option>
                                <?php
                            endwhile;
                            ?>
                        </select>
                    </div>
                    <div class="medium-6 columns">
                        <label for="barcode">Pindahkan barang ke rak ini</label>
                        <div class="row collapse">
                            <div class="small-10 columns">
                                <input type="text" placeholder="barcode" id="barcode">
                            </div>
                            <div class="small-2 columns">
                                <a href="#" class="button postfix" id="tombol-go">Go</a>
                            </div>
                        </div>
                    </div>
                    <div class="small-6 medium-3 columns">
                        <label for="barcode-cari">Cari barang di rak ini</label>
                        <div class="row collapse">
                            <div class="small-10 columns">
                                <input type="text" placeholder="barcode" id="barcode-cari">
                            </div>
                            <div class="small-2 columns">
                                <a href="#" class="button postfix" id="tombol-cari">Cari</a>
                            </div>
                        </div>
                    </div>
                    <div class="small-6 medium-3 columns">
                        <label>Tampilkan
                            <select id="jumBaris">
                                <option value="0" <?php echo $jumBaris == '0' ? 'selected' : ''; ?>>Semua</option>
                                <option value="50" <?php echo $jumBaris == '50' ? 'selected' : ''; ?>>50 baris</option>
                                <option value="30" <?php echo $jumBaris == '30' ? 'selected' : ''; ?>>30 baris</option>
                                <option value="10" <?php echo $jumBaris == '10' ? 'selected' : ''; ?>>10 baris</option>
                            </select>
                        </label>
                    </div>
                    <?php
                    /*
                     * Menampilkan barang yang belum dicek
                     */
                    $query = "select barang.barcode, namaBarang, hargaJual, jumBarang
                                from barang
                                left join stok_stat_detail on stok_stat_detail.barcode = barang.barcode
                                where (stok_stat_detail.id is null or stok_stat_detail.id={$stokStatId}) ";
                    
                    if (!is_null($idRak)) {
                        $query .= "and barang.idRak={$idRak} ";
                    }
                    if (!is_null($barcodeCari)) {
                        $query .= "and barang.barcode={$barcodeCari} ";
                    }

                    $resultTemp = mysqli_query($link, $query) or die('Gagal ambil StokStat detail temp #' . $stokStatId . '. error: ' . mysqli_error($link));
                    $totalBaris = mysqli_num_rows($resultTemp);

                    $query.= "order by namaBarang ";

                    if ($jumBaris != '0') {
                        $query.="limit {$mulai},{$jumBaris} ";
                    }
                    // echo $query;
                    $result = mysqli_query($link, $query) or die('Gagal ambil StokStat detail #' . $stokStatId . '. error: ' . mysqli_error($link));
                    ?>
                    <div class="small-12 columns">
                        <table style="width: 100%" class="tabel-data">
                            <thead>
                                <tr>
                                    <th>Barcode</th>
                                    <th>Nama Barang</th>
                                    <th class="rata-kanan">Harga</th>
                                    <th class="rata-kanan">Stok T</th>
                                    <th class="rata-tengah">Stok S</th>
                                    <th class="rata-tengah">Simpan</th>
                                </tr>
                            </thead>
                            <tbody id="data-barang">
                                <?php
                                while ($barang = mysqli_fetch_array($result)):
                                    ?>
                                    <tr>
                                        <td><?php echo $barang['barcode']; ?></td>
                                        <td><?php echo $barang['namaBarang']; ?></td>
                                        <td class="rata-kanan"><?php echo number_format($barang['hargaJual'], 0, ',', '.'); ?></td>
                                        <td class="rata-kanan"><?php echo $barang['jumBarang']; ?></td>
                                        <td class="rata-kanan">
                                            <input class="stok_sebenarnya" type="text" name="stok_sebenarnya" data-barcode="<?php echo $barang['barcode']; ?>" />
                                        </td>
                                        <td class="rata-tengah"><a class="tiny success button tombol-cek tombol-tabel" data-barcode="<?php echo $barang['barcode']; ?>" data-stok="<?php echo $barang['jumBarang']; ?>"><i class="fa fa-check"></i></a></td>
                                    </tr>
                                    <?php
                                endwhile;
                                ?>
                            </tbody>
                        </table>

                    </div>
                    <?php
                    if ($jumBaris != 0):
//                        $sql1 = "SELECT DISTINCT COUNT(barcode) DIV {$jumBaris} FROM barang ";
//                        if (!is_null($idRak)) {
//                            $sql1.="where idRak = {$idRak} ";
//                        }
//                        if (!is_null($barcodeCari)) {
//                            $query .= "and barcode={$barcodeCari} ";
//                        }
//                        $proses1 = mysqli_query($link, $sql1);
//                        $output1 = mysqli_fetch_array($proses1);
                        $jumlah_barang = $totalBaris / $jumBaris; //$output1[0];
                        ?>
                        <div class="small-12 columns">
                            <div class="pagination-centered">
                                <ul class="pagination">
                                    <li class="arrow unavailable">
                                        <a href="">&laquo;</a>
                                    </li>
                                    <?php
                                    for ($i = 0; $i <= $jumlah_barang; $i++) {
                                        ?>
                                        <li<?php echo $hal == $i ? ' class="current"' : ''; ?>>
                                            <a href="<?php echo "{$_SERVER['PHP_SELF']}?id={$stokStatId}&hal={$i}&idRak={$idRak}&jumBaris={$jumBaris}"; ?>"><?php echo $i; ?></a>
                                        </li>
                                        <?php
                                        //echo "[<a href='{$_SERVER['PHP_SELF']}?id={$stokStatId}&hal={$i}&idRak={$idRak}'> {$i} </a>] ";
                                    };
                                    ?>
                                    <li class="arrow">
                                        <a href="">&raquo;</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php
                    endif;
                    ?>
                </div>
                <?php
            endif;

            /*
             * Menampilkan barang yang sudah dicek
             */
            $query = "SELECT stok_stat_detail.id, stok_stat_detail.barcode, barang.namaBarang, harga_jual, stok_tercatat, stok_sebenarnya
                        FROM stok_stat_detail
                        JOIN barang on barang.barcode = stok_stat_detail.barcode
                        WHERE stok_stat_id = {$stokStatId}";
            $result = mysqli_query($link, $query) or die('Gagal ambil stok stat detail #' . $stokStatId . '. error: ' . mysqli_error($link));
            ?>
            <div class="small-12 columns">
                <table style="width: 100%" class="tabel-data-order">
                    <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Nama Barang</th>
                            <th class="rata-kanan">Harga</th>
                            <th class="rata-kanan">Stok T</th>
                            <th class="rata-kanan">Stok S</th>
                            <th class="rata-kanan">Sls</th>
                            <?php
                            /*
                             * Jika status masih rpo, tampilkan tombol hapus
                             */
                            if ($stokStat['status'] == 0):
                                ?>
                                <th class="rata-tengah">Hapus</th>
                                <?php
                            endif;
                            ?>
                        </tr>
                    </thead>
                    <tbody id="data-barang-stokstat">
                        <?php
                        while ($stockStatDetail = mysqli_fetch_array($result)):
                            ?>
                            <tr>
                                <td><?php echo $stockStatDetail['barcode']; ?></td>
                                <td><?php echo $stockStatDetail['namaBarang']; ?></td>
                                <td class="rata-kanan"><?php echo number_format($stockStatDetail['harga_jual'], 0, ',', '.'); ?></td>
                                <td class="rata-kanan"><?php echo number_format($stockStatDetail['stok_tercatat'], 0, ',', '.'); ?></td>
                                <td class="rata-kanan"><?php echo number_format($stockStatDetail['stok_sebenarnya'], 0, ',', '.'); ?></td>
                                <td class="rata-kanan sub_total"><?php echo $stockStatDetail['stok_sebenarnya'] - $stockStatDetail['stok_tercatat']; ?></td>
                                <?php
                                /*
                                 * Jika status masih rpo, tampilkan tombol hapus
                                 */
                                if ($stokStat['status'] == 0):
                                    ?>
                                    <td class="rata-tengah"><a class="tiny alert radius button tombol-hapus tombol-tabel" data-detail_id="<?php echo $stockStatDetail['id']; ?>"><i class="fa fa-times"></i></a></td>
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
                    id: <?php echo $stokStatId; ?>,
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

            $("#tombol-go").click(function() {
                var barcodeInput = $("#barcode").val();
                if (barcodeInput != '') {
                    var dataKirim = {
                        barcode: barcodeInput,
                        rakId: <?php echo is_null($idRak) ? 'null' : $idRak; ?>
                    };
                    $.ajax({
                        type: "POST",
                        url: 'aksi.php?act=ubahrak',
                        data: dataKirim,
                        dataType: "json",
                        success: function(data) {
                            if (data.sukses) {
                                window.location.reload();
                            }
                        }
                    });
                }
                return false;
            });

            $("#tombol-cari").click(function() {
                var barcode = $("#barcode-cari").val();
                window.location.href = "<?php echo $_SERVER['PHP_SELF'] . "?id=$stokStatId&idRak={$idRak}"; ?>&barcode=" + barcode;
            });

            $("#pilih-rak").change(function() {
                window.location.href = "<?php echo $_SERVER['PHP_SELF'] . "?id=$stokStatId&idRak="; ?>" + $(this).val() + "&jumBaris=" +<?php echo $jumBaris; ?>;
            });

            $("#jumBaris").change(function() {
                window.location.href = "<?php echo $_SERVER['PHP_SELF'] . "?id=$stokStatId&idRak={$idRak}"; ?>&jumBaris=" + $(this).val();

            });

            $(document).on("click", ".tombol-cek", function() {
                var stokSebenarnya = $(this).parents('tr').find(".stok_sebenarnya");
                var qty = stokSebenarnya.val();
                var barcode = stokSebenarnya.data("barcode");
                var stokTercatat = stokSebenarnya.data("stok");
                //console.log(barcode + " -- " + qty);
                var dataKirim = {
                    stokStatId: <?php echo $stokStatId; ?>,
                    barcode: barcode,
                    stok: stokTercatat,
                    qty: qty
                };
                $.ajax({
                    type: "POST",
                    url: 'aksi.php?act=cek',
                    data: dataKirim,
                    dataType: "json",
                    success: function(data) {
                        if (data.sukses) {
                            location.reload();
                        }
                    }
                });
            });

            $(document).on("click", ".tombol-hapus", function() {
                var detail_id = $(this).data("detail_id");
                var dataKirim = {
                    id: detail_id
                };
                $.ajax({
                    type: "POST",
                    url: 'aksi.php?act=hapus',
                    data: dataKirim,
                    dataType: "json",
                    success: function(data) {
                        if (data.sukses) {
                            location.reload();
                        }
                    }
                });
            });

        </script>
    </body>
</html>