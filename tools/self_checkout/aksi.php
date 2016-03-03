<?php
include '../../config/config.php';
mysql_close();

$link = mysqli_connect($server, $username, $password) or die("Koneksi gagal");
mysqli_select_db($link, $database) or die("Database tidak bisa dibuka");

$clientIP = $_SERVER['REMOTE_ADDR'];

if ($_POST['tambah']) {
    $barcode = $_POST['barcode'];
    tambahBarang($link, $clientIP, $barcode);
}
elseif ($_POST['hapus']) {
    $barcode = $_POST['barcode'];
    hapusBarang($link, $clientIP, $barcode);
}
elseif ($_GET['refresh']) {
    refreshDetail($link, $clientIP);
}
elseif ($_GET['gettotal']) {
    getTotal($link, $clientIP);
}
elseif ($_POST['selesai']) {
    selesai($link, $clientIP);
}

/**
 * Tambah barang self checkout, qty selalu 1, karena tidak ada input qty di user interface :)
 * @param mysqli $link myqli link
 * @param string $clientIP ip address v4 dari client ybs
 * @param string $barcode barcode barang
 */
function tambahBarang($link, $clientIP, $barcode) {
    if ($barcode != '') {
        $qty = 1;
        /**
         * Cek apa barang sudah ada
         */
        $resultBarang = mysqli_query($link, "SELECT sum(qty) qty from self_checkout_temp "
                . "WHERE ipv4 = '{$clientIP}' "
                . "AND barcode = '{$barcode}'") or die('Gagal ambil data barang ' . $barcode);

        /**
         * Jika barang sudah ada tambahkan qty nya, hapus yang sudah ada
         */
        if ($resultBarang) {
            $barang = mysqli_fetch_array($resultBarang);
            $qty += $barang['qty'];
            mysqli_query($link, "DELETE FROM self_checkout_temp WHERE ipv4 = '{$clientIP}' AND barcode = '{$barcode}'") or die('Gagal hapus data barang ' . $barcode);
        }

        /**
         * Simpan barcode dan qty nya
         */
        mysqli_query($link, "insert into self_checkout_temp (barcode, qty, harga_jual, ipv4)
                            select barcode, {$qty} as qty, hargaJual, '{$clientIP}' as ipv4
                            from barang
                            where barcode = '{$barcode}'") or die('Gagal tambah barang ' . $barcode);
        $id = mysqli_insert_id($link);
        /**
         * cek diskon jika ada
         */
        cekDiskon($link, $clientIP, $id, $barcode, $qty);
    }
    $return = array(
        'sukses' => true
    );
    echo json_encode($return);
}

/**
 *
 * @param mysqli $link
 * @param int $uid
 * @param string $barcode
 * @param int $jumBarang
 */
function cekDiskon($link, $clientIP, $id, $barcode, $jumBarang) {
    // Cek dan tambahkan diskon waktu/promo jika ada
    cekDiskonWaktu($link, $clientIP, $id);
    // eo diskon waktu
    // Cek dan tambahkan diskon grosir jika ada
    // ctt: Diskon grosir akan menambah diskon waktu/promo jika ada
    $diskonGrosir = cekDiskonGrosir($link, $barcode, $jumBarang);
    if ($diskonGrosir) {
        //echo 'ketemu diskon grosir';
        tambahkanDiskonGrosir($link, $clientIP, $barcode, $diskonGrosir);
    }
    // eo diskon grosir
}

/**
 * Cek dan sekaligus menambahkan diskon waktu/promo pada id row self_checkout_temp
 * @param mysqli $link link mysqli
 * @param string $clientIP ip v 4 client yang connect
 * @param int $id id row dari tabel detail self_checkout_temp
 * @return boolean true jika berhasil, false jika tidak berhasil
 */
function cekDiskonWaktu($link, $clientIP, $id) {
    $sql = "select dd.uid, dd.diskon_persen, dd.diskon_rupiah, b.hargaJual, sct.qty, dd.max_item,
				sct.waktu, sct.barcode
				from self_checkout_temp sct
				join diskon_detail dd on dd.barcode = sct.barcode
				join barang b on b.barcode = dd.barcode
				where sct.id=$id and dd.status=1 and
				dd.tanggal_dari<= now() and
				(dd.tanggal_sampai='0000-00-00 00:00:00' or tanggal_sampai >= now() ) and
				diskon_tipe_id=1001
				order by dd.uid desc
				limit 1";
    $result = mysqli_query($link, $sql) or die('Gagal cek diskon promo, error: ' . mysqli_error($link));
    $dataDiskon = mysqli_fetch_array($result);
    if ($dataDiskon) {
//		$diskonDetailId = $dataDiskon['uid'];
        $diskonPersen = $dataDiskon['diskon_persen'];
        $diskonRupiah = $dataDiskon['diskon_rupiah'];
        $hargaJual = $dataDiskon['hargaJual'];
        // Jika ada diskon persen, diskon rupiah diabaikan (dianggap kesalahan input)
        if ($diskonPersen > 0) {
            $diskon = $diskonPersen / 100 * $hargaJual;
            // harga jual dibulatkan ke atas jika berkoma.
            $hargaJualNet = ceil($hargaJual - $diskon);
            $diskonNet = $hargaJual - $hargaJualNet;
        }
        elseif ($diskonRupiah > 0) {
            $diskon = $diskonRupiah;
            $hargaJualNet = $hargaJual - $diskon;
            $diskonNet = $diskon;
        }

        $jumbarang = $dataDiskon['qty'];
        $maxItem = $dataDiskon['max_item'];
        if ($jumbarang > $maxItem) {
            $sql = "update self_checkout_temp set qty = {$maxItem}, harga_jual = '{$hargaJualNet}', diskon = '$diskonNet' "
                    . "where id=$id";
            mysqli_query($link, $sql) or die('Gagal menambahkan diskon promo1, error: ' . mysqli_error($link));
            $sisaBarang = $jumbarang - $maxItem;
            $sql = "INSERT into self_checkout_temp(ipv4, waktu,
                            barcode,qty,harga_jual)
                        VALUES('{$clientIP}','{$dataDiskon['waktu']}','{$dataDiskon['barcode']}',
								{$sisaBarang},{$dataDiskon['hargaJual']})";
            mysqli_query($link, $sql) or die('Gagal menambahkan diskon promo2, error: ' . mysqli_error($link));
            $id2 = mysqli_insert_id($link);
            $return = array($id => true, $id2 => false);
        }
        else {
            $sql = "update self_checkout_temp set harga_jual = '{$hargaJualNet}', diskon = '$diskonNet' "
                    . "where id=$id";
            mysqli_query($link, $sql) or die('Gagal menambahkan diskon promo0, error: ' . mysqli_error($link));
            $return = array($id => true);
        }
        return $return;
    }
    else {
        return false;
    }
}

/**
 * Cek Diskon Grosir untuk barcode tertentu
 * @param mysqli $link link mysqli
 * @param string $barcode barcode barang
 * @param int $jumBarang qty barang
 * @return mixed array of string dari data diskon grosir, NULL jika tidak ada diskon grosir untuk barcode di atas
 */
function cekDiskonGrosir($link, $barcode, $jumBarang) {
    // Cek tabel diskon_detail, apakah ada skema diskon grosir yang cocok
    $sql = "select dd.uid, dd.diskon_persen, dd.diskon_rupiah
				from diskon_detail dd
				where barcode = '$barcode' and
				dd.tanggal_dari<= now() and
				(dd.tanggal_sampai='0000-00-00 00:00:00' or tanggal_sampai >= now() ) and
				dd.min_item<=$jumBarang and
				dd.diskon_tipe_id=1000 and
				dd.status=1
				order by dd.uid desc";
    $hasil = mysqli_query($link, $sql) or die("Gagal cek diskon grosir, error: " . mysqli_error($link));
    return mysqli_fetch_array($hasil);
}

function tambahkanDiskonGrosir($link, $clientIP, $barcode, $diskonGrosir) {
    $sql = "select id, diskon, b.hargaJual
				from self_checkout_temp sct
				join barang b on b.barcode = sct.barcode
				where sct.ipv4 = '{$clientIP}' and sct.barcode = '$barcode'";
    $hasil = mysqli_query($link, $sql) or die("DG: Gagal ambil detail_jual, error: " . mysqli_error());
    while ($sct = mysqli_fetch_array($hasil)):

        // Hitung nilai diskon grosir
        $nilaiDiskonGrosir = 0;
        if ($diskonGrosir['diskon_persen'] > 0) {
            $nilaiDiskonGrosir = $diskonGrosir['diskon_persen'] / 100 * $tdj['diskon_rupiah'];
        }
        else {
            $nilaiDiskonGrosir = $diskonGrosir['diskon_rupiah'];
        }
        $hargaJual = $sct['hargaJual'] - $nilaiDiskonGrosir;
        $totalDiskon = $nilaiDiskonGrosir;

        // Jika sebelumnya ada diskon waktu/promo,
        // 1. kurangi lagi hargaJual,
        // 2. tambahkan lagi nilai diskon

        if ($sct['diskon'] > 0) {
            // tambahkan nilai diskonnya
            if ($diskonGrosir['diskon_persen'] > 0) {
                $nilaiDiskonGrosir = $diskonGrosir['diskon_persen'] / 100 * ($sct['hargaJual'] - $sct['diskon']);
            }
            else {
                $nilaiDiskonGrosir = $diskonGrosir['diskon_rupiah'];
            }
            $totalDiskon = $sct['diskon'] + $nilaiDiskonGrosir;

            // kurangi hargaJual nya
            $hargaJual = $sct['hargaJual'] - $totalDiskon;
        }
        // simpan lagi
        // simpan hanya nilai diskon rupiahnya
        $uidsDiskon = json_encode($uidsDiskon);
        $sql = "update self_checkout_temp set harga_jual = {$hargaJual}, diskon = {$totalDiskon} "
                . " where id={$sct['id']}";
        //echo $sql;
        mysqli_query($link, $sql) or die("Gagal menambahkan diskon grosir, error: " . mysqli_error($link));
    endwhile;
}

/**
 * Hapus barang per barcode
 * @param mysqli $link mysqli link
 * @param string $clientIP ipv4 client yang connect
 * @param string $barcode barcode barang
 */
function hapusBarang($link, $clientIP, $barcode) {
    mysqli_query($link, "DELETE FROM self_checkout_temp WHERE ipv4 = '{$clientIP}' AND barcode = '{$barcode}'") or die('Gagal hapus data barang ' . $barcode);
    $return = array(
        'sukses' => true
    );
    echo json_encode($return);
}

function refreshDetail($link, $clientIP) {
    $result = mysqli_query($link, "SELECT b.barcode, b.namaBarang, sct.qty, sct.harga_jual, sct.diskon, sct.qty * sct.harga_jual as sub_total
                                    FROM self_checkout_temp sct
                                    JOIN barang b on b.barcode = sct.barcode
                                    WHERE ipv4 = '{$clientIP}'
                                    ORDER by sct.id desc") or die("Gagal ambil data detail");
    while ($detail = mysqli_fetch_array($result)):
        ?>
        <tr>
            <td><?php echo $detail['barcode']; ?></td>
            <td class="tengah"><a id="<?php echo $detail['barcode']; ?>" href="" class="tiny alert button kecil hapus"><i class="fa fa-times fa-2x"></i></a></td>
            <td><?php echo $detail['namaBarang']; ?></td>
            <td class="kanan"><?php echo number_format($detail['harga_jual'], 0, ',', '.'); ?></td>
            <td class="kanan"><?php echo number_format($detail['diskon'], 0, ',', '.'); ?></td>
            <td class="kanan"><?php echo $detail['qty']; ?></td>
            <td class="kanan"><?php echo number_format($detail['sub_total'], 0, ',', '.'); ?></td>
        </tr>
        <?php
    endwhile;
}

function _ambilTotal($link, $clientIP) {
    $result = mysqli_query($link, "select sum(qty * harga_jual) total
                                    from self_checkout_temp
                                    where ipv4='{$clientIP}'") or die('Gagal ambil total!');
    $total = mysqli_fetch_array($result);
    return $total['total'];
}

function getTotal($link, $clientIP) {

    echo number_format(_ambilTotal($link, $clientIP), 0, ',', '.');
}

function selesai($link, $clientIP) {
    $datetime = date("Y-m-d H:i:s");
    mysqli_query($link, "INSERT INTO self_checkout (datetime, ipv4) VALUES('{$datetime}','{$clientIP}')") or die('Gagal tambah data');
    $uid = mysqli_insert_id($link);

    $query = "insert into self_checkout_detail (self_checkout_uid, barcode, qty, harga_jual, diskon)
                (
                select {$uid} as self_checkout_uid, barcode, qty, harga_jual, diskon
                from self_checkout_temp
                where ipv4='{$clientIP}'
                order by id
                )
                ";
    mysqli_query($link, $query) or die('Gagal tambah data detail');

    $return = array(
        'sukses' => true,
        'strukId' => $uid,
    );
    echo json_encode($return);
    cetak($link, $clientIP, $uid);
}

function cetak($link, $clientIP, $strukId) {

    // cetak struk -------------
    // ambil footer & header struk
    $sql = "SELECT `option`,`value` FROM config";
    $hasil = mysqli_query($link, $sql) or die('Gagal ambil config, error: ' . mysqli_error($link));
    while ($x = mysqli_fetch_array($hasil)) {
        if ($x[option] == 'store_name') {
            $namaToko = $x[value];
        };
    };
    $ip = $clientIP;
    $perintahPrinter = "-H $ip -P printerstruk -l";
    $struk = chr(27) . "@"; //Init Printer
    //$struk .= chr(27) . chr(101) . chr(2); //2 reverse lf
    $struk .= chr(27) . "!" . chr(1); //font B / normal
    //$struk .= chr(27) . chr(101) . chr(2); //1 reverse lf
    $struk .= chr(27) . "a" . chr(48); //0 left
    //$struk .= chr(27) . chr(101) . chr(2); //2 reverse lf
    //$struk .= chr(27) . chr(101) . chr(2); //2 reverse lf
    $struk .= strtoupper($namaToko) . "\n";
    $struk .= "Self Check Out\n";


    $total = number_format(_ambilTotal($link, $clientIP), 0, ',', '.');
    $struk .= chr(27) . chr(101) . chr(2); //2 reverse lf
    $struk .= chr(27) . "!" . chr(16); //font double width
    $struk .= chr(27) . "a" . chr(2); //2 right
    $struk .= "Rp. {$total}\n\n";

    $struk .= chr(27) . "!" . chr(48); //font besar
    $struk .= chr(27) . "a" . chr(1); //0 center
    $struk .="{$strukId}\n\n";
    $struk .= chr(27) . "!" . chr(1); //font Normal
    $struk .= chr(27) . "a" . chr(48); //0 left

    $struk .= "Ketentuan:\n";
    $struk .= "Struk ini ";

//    $struk .= chr(27) . "!" . chr(8); //font tebal
    $struk .= "BUKAN bukti pembayaran\n";
    $struk .= chr(27) . "!" . chr(1); //font normal
    $struk .= "Silahkan melakukan pembayaran di kasir\n"
            . "Jika ada perbedaan perhitungan,\n"
            . "Yang benar adalah ";
//    $struk .= chr(27) . "!" . chr(8); //font tebal
    $struk .= "perhitungan kasir\n";
    $struk .= chr(27) . "!" . chr(1); //font normal
    $struk .= chr(29) . "V" . chr(66) . chr(48); //Feed paper & cut
    $perintah = "echo \"$struk\" |lpr $perintahPrinter -l";
    echo $perintah;
    exec($perintah, $output);
}
