<?php
include_once '../../config/config.php';
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
    case 'tambahstokstat':
        $stokStatId = tambahStokStat($link, $_POST['keterangan']);
        header('location:input.php?id=' . $stokStatId);
        break;
    case 'cek':
        if (cek($link, $_POST['stokStatId'], $_POST['barcode'], $_POST['qty'])) {
            echo json_encode(
                    array('sukses' => true)
            );
        }
        break;
    case 'hapus':
        $id = $_POST['id'];
        if (hapus($link, $id)) {
            echo json_encode(
                    array('sukses' => true)
            );
        }
        break;
    case 'simpan':
        $id = $_POST['id'];
        echo json_encode(array(
            'sukses' => simpan($link, $id)
        ));
        break;
    case 'hapusss': //hapus SS
        $id = $_GET['id'];
        hapusSs($link, $id);
        header('location:index.php');
        break;
    case 'ubahrak':
        $barcode = $_POST['barcode'];
        $idRak = $_POST['rakId'];
        echo json_encode(array(
            'sukses' => ubahRak($link, $barcode, $idRak)
        ));
        break;
endswitch;

/**
 * Membuat stok_stat baru (status:draft)
 * @param mysqli $link
 * @param array $param
 * @return int id stok_stat
 */
function tambahStokStat($link, $keterangan) {
    $sql = "INSERT INTO stok_stat (keterangan, updated_by) "
            . "values('$keterangan', '{$_SESSION['uname']}')";

    mysqli_query($link, $sql)
            or die('Gagal tambah StokStat, error:' . mysqli_error($link));
    return mysqli_insert_id($link);
}

/**
 * Barang sudah di cek (sudah diisi stok sebenarnya)
 * @param type $link
 * @param type $barcode
 * @param type $qty Stok Sebenarnya
 */
function cek($link, $stokStatId, $barcode, $qty) {
    $sql = "
            insert into stok_stat_detail 
            (stok_stat_id, barcode, harga_jual, stok_tercatat, stok_sebenarnya, updated_by)
            select {$stokStatId}, '{$barcode}', hargaJual, jumBarang, {$qty},'{$_SESSION['uname']}'
            from barang where barcode = '{$barcode}' ";
    $result = mysqli_query($link, $sql) or die('Gagal Insert, error: ' . mysqli_error($link));
    return $result;
}

function hapus($link, $id) {
    $sql = "DELETE FROM stok_stat_detail WHERE id={$id}";
    return mysqli_query($link, $sql) or die('Gagal Hapus detail #' . $id . ', error: ' . mysqli_error($link));
}

function simpan($link, $id) {
    $berhasil = mysqli_query($link, "UPDATE stok_stat SET status=1 WHERE id={$id}") or die("Gagal simpan, error: " . mysqli_error($link));
    if ($berhasil) {
        // Update qty barang di tabel barang dengan penambahan selisih dari cek stok
        $query = "update barang
                join stok_stat_detail ssd on ssd.barcode = barang.barcode
                set barang.jumBarang = barang.jumBarang + (ssd.stok_sebenarnya - ssd.stok_tercatat)
                where ssd.stok_stat_id = {$id}";
        return mysqli_query($link, $query) or die('Gagal update jumlah barang, error: ' . mysqli_error($link));
    }
    else {
        return false;
    }
}

function hapusSs($link, $id) {
    $result = mysqli_query($link, "select status from stok_stat where id={$id}");
    $stokStat = mysqli_fetch_array($result);
    // Bisa dihapus jika dan hanya jika status masih DRAFT  
    if ($stokStat['status'] == 0) {
        mysqli_query($link, "DELETE FROM stok_stat_detail WHERE stok_stat_id={$id}") or die('Gagal hapus details, error: ' . mysqli_error($link));
        mysqli_query($link, "DELETE FROM stok_stat WHERE id={$id}") or die('Gagal hapus SS, error: ' . mysqli_error($link));
        return true;
    }
    else {
        return false;
    }
}

function ubahRak($link, $barcode, $idRak) {
    $query = "UPDATE barang SET idRak = {$idRak} WHERE barcode='{$barcode}' ";
    return mysqli_query($link, $query) or die('Gagal ubah id Rak, error: ' . mysqli_error($link).'; sql='.$query);
}
