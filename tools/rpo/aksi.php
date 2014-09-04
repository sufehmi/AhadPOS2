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

$act = $_GET['act'];

switch ($act):
    case 'getbarang':
        if (isset($_GET['po_id'])):
            getBarang($link, $_GET['po_id'], $_GET['rak_id']);
        endif;
        break;
    case 'getbarangorder':
        if (isset($_GET['po_id'])) {
            getBarangOrder($link, $_GET['po_id']);
        }
        break;
    case 'getinterval':
        if (isset($_POST['supplierId'])) {
            echo json_encode(getInterval($link, $_POST['supplierId']));
        }
        break;
    case 'tambahrpo':
        $rpoId = tambahRpo($link, $_POST['param']);
        tambahRpoDetail($link, $rpoId, $_POST['param']['supplier_id']);
        header('location:input.php?id=' . $rpoId);
        break;
    case 'hitung':
        $poId = $_POST['poId'];
        $barcode = $_POST['barcode'];
        /*
         * Ambil hasil perhitungan untuk avg daily sales dan saran order
         */
        $hasil = hitung($link, $poId, $barcode);
        /*
         * Update po detail dengan nilai hasil perhitungan
         */
        mysqli_query($link, "update purchase_order_detail set avg_daily_sales={$hasil['avg_daily_sales']}, saran_order={$hasil['saran_order']}"
                        . " where purchase_order_id={$poId} and barcode='{$barcode}'") or die('Gagal update po #' . $poId . ', error: ' . mysqli_error($link));
        /*
         * Kembalikan hasil perhitungan + status sukses
         */
        echo json_encode(array_merge(
                        array('sukses' => true), $hasil
        ));
        break;
    case 'order':
        echo json_encode(order($link, $_POST['poId'], $_POST['barcode'], $_POST['qty']));
        break;
    case 'hapus':
        echo json_encode(hapus($link, $_POST['poId'], $_POST['barcode']));
        break;

    case 'simpan':
        echo json_encode(simpan($link, $_POST['poId']));
        break;
    case 'csv':
        csv($link, $_GET['poId']);
        break;
    case 'hapuspo':
        hapusPo($link, $_GET['poid']);
        header('location:index.php');
        break;
endswitch;

/**
 * Fungsi untuk menghapus po (header dan detail)
 * @param mysqli $link
 * @param int $poId
 */
function hapusPo($link, $poId) {
    mysqli_query($link, "DELETE FROM purchase_order_detail WHERE purchase_order_id={$poId}") or die('Gagal hapus po detail, po#' . $poId . ', error: ' . mysqli_error($link));
    mysqli_query($link, "DELETE FROM purchase_order WHERE id={$poId}") or die('Gagal hapus po#' . $poId . ', error: ' . mysqli_error($link));
}

/**
 * fungsi untuk menghasilkan file csv dari PO
 * @param mysqli $link
 * @param int $poId ID Purchase Order
 * @return string csv file siap didownload
 */
function csv($link, $poId) {
    /*
     * Ambil data untuk export ke csv
     */
    $query = "select 
                pod.barcode, 
                barang.namaBarang, 
                pod.harga_beli_terakhir, 
                pod.jumlah_order 
                from purchase_order_detail pod
                join barang on barang.barcode = pod.barcode
                where purchase_order_id = {$poId} and jumlah_order>0 
                order by barang.namaBarang ";
    $result = mysqli_query($link, $query);
    /*
     * Buat string 
     */
    $csv = "\"barcode\","
            . "\"idBarang\","
            . "\"namaBarang\","
            . "\"jumBarang\","
            . "\"hargaBeli\","
            . "\"hargaJual\","
            . "\"RRP\","
            . "\"SatuanBarang\","
            . "\"KategoriBarang\","
            . "\"Supplier\","
            . "\"kasir\"\n";

    $kosong = '';
    while ($barang = mysqli_fetch_array($result)) {
        $csv .=
                "\"{$barang['barcode']}\","
                . "\"{$kosong}\","
                . "\"{$barang['namaBarang']}\","
                . "\"{$barang['jumlah_order']}\","
                . "\"{$barang['harga_beli_terakhir']}\","
                . "\"{$kosong}\","
                . "\"{$kosong}\","
                . "\"{$kosong}\","
                . "\"{$kosong}\","
                . "\"{$kosong}\","
                . "\"{$kosong}\"\n";
    }

    // cari nama toko ini 
    $hasil = mysqli_query($link, "SELECT value FROM config WHERE `option` = 'store_name'");
    $storeName = mysqli_fetch_array($hasil);
    $namaToko = $storeName['value'];
    // masukkan nama toko ini ke nama file csv
    $namaToko = str_replace(' ', '_', $namaToko);
    $namaFile = 'PO-' . $namaToko . "-" . date("Y-m-d--H-i") . ".csv";

    // kirim output CSV ke browser untuk di download
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=\"$namaFile\"");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $csv;
}

/**
 * 
 * @param mysqli $link
 * @param int $poId
 * @return array Mengembalikan nilai sukses=>true jika berhasil
 */
function simpan($link, $poId) {
    $query = "UPDATE purchase_order SET status=1 WHERE id={$poId}";
    mysqli_query($link, $query) or die('Gagal update status PO #' . $poId . ', error: ' . mysqli_error($link));
    $query = "DELETE FROM purchase_order_detail WHERE purchase_order_id={$poId} AND jumlah_order is null";
    mysqli_query($link, $query) or die('Gagal simpan po detail, error: ' . mysqli_error($link));
    return array(
        'sukses' => true
    );
}

/**
 * 
 * @param mysqli $link
 * @param int $poId
 * @param string $barcode
 * @param int $qty
 * @return array Mengembalikan nilai sukses=> true jika berhasil
 */
function order($link, $poId, $barcode, $qty) {
    if ($qty > 0) {
        $query = "UPDATE purchase_order_detail SET jumlah_order = {$qty} "
                . "WHERE purchase_order_id={$poId} AND "
                . "barcode = '{$barcode}'";
        mysqli_query($link, $query) or die('Gagal order, barang ' . $barcode . ', error: ' . mysqli_error($link));
        return array(
            'sukses' => true
        );
    }
}

/**
 * 
 * @param mysqli $link
 * @param int $poId id Purchase Order
 * @param string $barcode Barcode barang yang akan dihitung
 * @return array Mengembalikan nilai avg_daily_sales dan saran_order
 */
function hitung($link, $poId, $barcode) {
    $query = "SELECT `range`, buffer, jumlah_hari_persediaan FROM purchase_order WHERE id= {$poId}";
    $result = mysqli_query($link, $query) or die('Gagal ambil data po, error: ' . mysqli_error($link));
    $po = mysqli_fetch_array($result);
    $query = "SELECT IFNULL(SUM(jumBarang),0) / {$po['range']} AS avg_daily_sales,
                CEIL((IFNULL(SUM(jumBarang),0) / {$po['range']} * {$po['jumlah_hari_persediaan']}) + 
                    ({$po['buffer']}/100*(IFNULL(SUM(jumBarang),0) / {$po['range']} * {$po['jumlah_hari_persediaan']}))) AS saran_order
                FROM detail_jual dj
                JOIN transaksijual tj ON tj.idTransaksiJual = dj.nomorStruk 
                WHERE barcode = '{$barcode}' AND tj.tglTransaksiJual BETWEEN DATE_SUB(NOW(), INTERVAL {$po['range']} DAY) AND NOW()
                ";
    $result = mysqli_query($link, $query) or die('Gagal ambil data perhitungan barang ' . $barcode . ', error: ' . mysqli_error($link));
    $hasilPerhitungan = mysqli_fetch_array($result);
    return array(
        'avg_daily_sales' => $hasilPerhitungan['avg_daily_sales'],
        'saran_order' => $hasilPerhitungan['saran_order']
    );
}

/**
 * 
 * @param mysqli $link
 * @param int $supplierId
 * @return array interval, status sukses
 */
function getInterval($link, $supplierId) {
    $result = mysqli_query($link, "SELECT `interval` FROM supplier WHERE idSupplier={$supplierId}") or die('Gagal ambil interval :' . mysqli_error($link));
    $supplier = mysqli_fetch_array($result);
    return array(
        'sukses' => true,
        'interval' => $supplier['interval']
    );
}

/**
 * Membuat po baru (status:draft)
 * @param mysqli $link
 * @param array $param
 * @return int id po
 */
function tambahRpo($link, $param) {
    $sql = "INSERT INTO purchase_order (tanggal_buat, supplier_id, `range`, buffer, jumlah_hari_persediaan, updated_by) "
            . "values(now(), {$param['supplier_id']}, {$param['range']}, {$param['buffer']}, {$param['jumlah_hari_persediaan']}, '{$_SESSION['uname']}')";

    mysqli_query($link, $sql)
            or die('Gagal tambah rpo, error:' . mysqli_error($link));
    return mysqli_insert_id($link);
}

/**
 * Memasukkan semua barang yang aktif ke po
 * @param mysqli $link
 * @param int $poId
 * @param int $supplierId
 */
function tambahRpoDetail($link, $poId, $supplierId) {
    /*
     * , harga_beli_terakhir, 
      (select hargaBeli
      from detail_beli
      where barcode = b.barcode
      order by idDetailBeli desc limit 1) hargaBeli
     */
    $query = "
            insert into purchase_order_detail (purchase_order_id, barcode, stok_saat_ini, harga_beli_terakhir)
            (
            select 
            {$poId},
            b.barcode, 
            b.jumBarang,
            (select hargaBeli
            from detail_beli db
            where barcode = b.barcode and 
            db.idDetailBeli=(select max(idDetailBeli) 
                from detail_beli 
                where barcode = b.barcode)
            ) hargaBeli
            from barang b
            where idSupplier = {$supplierId}  and (b.nonAktif!=1 or b.nonAktif is null)
            )";
    echo $query;
    mysqli_query($link, $query) or die('Gagal tambah po detail #' . $poId . '. error: ' . mysqli_error($link));
}

/**
 * 
 * @param mysqli $link
 * @param int $poId
 * @param int $rakId
 * @return string Echoing html tabel, yang berisi daftar barang per rak
 */
function getBarang($link, $poId, $rakId) {
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
                        where purchase_order_id = {$poId} and pod.jumlah_order is null ";
    if ($rakId > 0) {
        $query .= " and barang.idRak = {$rakId} ";
    }
    $query .= 'order by barang.namaBarang';

    $resultBarang = mysqli_query($link, $query)
            or die('Gagal ambil data barang: ' . mysqli_error($link));
    ?>
    <?php
    while ($poDetail = mysqli_fetch_array($resultBarang)):
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
            <td class="rata-tengah"><a class="tiny button tombol-order tombol-tabel" data-barcode="<?php echo $poDetail['barcode']; ?>"><i class="fa fa-check"></i></a></td>
        </tr>
        <?php
    endwhile;
}

/**
 * 
 * @param mysqli $link
 * @param int $poId id Purchase Order
 * @return string Echoing html tabel untuk barang yang diorder
 */
function getBarangOrder($link, $poId) {

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
    while ($poDetail = mysqli_fetch_array($result)):
        ?>
        <tr>
            <td><?php echo $poDetail['barcode']; ?></td>
            <td><?php echo $poDetail['namaBarang']; ?></td>
            <td class="rata-kanan"><?php echo number_format($poDetail['harga_beli_terakhir'], 0, ',', '.'); ?></td>
            <td class="rata-kanan"><?php echo $poDetail['jumlah_order']; ?></td>
            <td class="rata-kanan sub_total"><?php echo $poDetail['jumlah_order'] * $poDetail['harga_beli_terakhir']; ?></td>
            <td class="rata-tengah"><a class="tiny alert radius button tombol-hapus tombol-tabel" data-barcode="<?php echo $poDetail['barcode']; ?>"><i class="fa fa-times"></i></a></td>
        </tr>
        <?php
    endwhile;
}

/**
 * Menghapus item di Purchase Order
 * @param mysqli $link
 * @param int $poId
 * @param string $barcode
 * @return array status sukses jika berhasil
 */
function hapus($link, $poId, $barcode) {
    $query = "UPDATE purchase_order_detail SET jumlah_order = NULL WHERE purchase_order_id={$poId} AND barcode='{$barcode}'";
    mysqli_query($link, $query) or die('Gagal hapus barang ' . $barcode . ', error: ' . mysqli_error($link));
    return array(
        'sukses' => true
    );
}
