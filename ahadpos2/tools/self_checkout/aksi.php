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
elseif ($_GET['refresh']) {
    refreshDetail($link, $clientIP);
}

function tambahBarang($link, $clientIP, $barcode) {
    $qty = 1;
    mysqli_query($link, "insert into self_checkout_temp (barcode, qty, harga_jual, ip4)
                            select barcode, {$qty} as qty, hargaJual, '{$clientIP}' as ip4
                            from barang
                            where barcode = '{$barcode}'") or die('Gagal tambah barang ' . $barcode);
    $return = array(
        'sukses' => true
    );
    echo json_encode($return);
}

function refreshDetail($link, $clientIP) {
    $result = mysqli_query($link, "SELECT b.barcode, b.namaBarang, sct.qty, sct.harga_jual, sct.qty * sct.harga_jual as sub_total
                                    FROM self_checkout_temp sct
                                    JOIN barang b on b.barcode = sct.barcode
                                    WHERE ip4 = '{$clientIP}'
                                    ORDER by sct.id desc") or die("Gagal ambil data detail");
    while ($detail = mysqli_fetch_array($result)):
        ?>
        <tr>
            <td><?php echo $detail['barcode']; ?></td>
            <td><?php echo $detail['namaBarang']; ?></td>
            <td class="kanan"><?php echo number_format($detail['harga_jual'], 0, ',', '.'); ?></td>
            <td class="kanan"><?php echo $detail['qty']; ?></td>
            <td class="kanan"><?php echo number_format($detail['sub_total'], 0, ',', '.'); ?></td>
        </tr>
        <?php
    endwhile;
}
