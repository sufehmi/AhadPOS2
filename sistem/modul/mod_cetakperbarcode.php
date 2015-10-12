<?php
include "../../config/config.php";
// check_user_access();

switch ($_GET[act]) {
    default:

        echo "Data tidak ditemukan";

        break;

    case "printperbarcode":

        $cari = mysql_query("SELECT * FROM tmp_cetak_label_perbarcode");

        if ($_POST[idTmpBarang] == '') {
            echo '<center>Data tidak ditemukan</center>';
        }
        elseif ($_POST['layout'] == '2') {
            // Layout 2 = Layout harga banded
            ?>
            <style>

                @font-face {
                    font-family: 'Questrial';
                    font-style: normal;
                    font-weight: 400;
                    src: url('../../font/Questrial-Regular.ttf');
                }
            </style>
            <?php
            $lebar_label = 229;
            $tinggi_label = 150;
            $label_per_baris = 4;
            $baris_per_halaman = 4;

            $jumlahKarakterNamaBarang = 25;

            $tanggal = date('dmY');
            $total = $_POST[total];
            $baris = 1;
            $kolom = 1;
            ?>
            <div style="float:none">
                <?php
                for ($i = 1; $i <= $total; $i++) {

                    $r = mysql_fetch_array($cari);

                    $clear = "";
                    // cek posisi saat ini
                    if ($kolom > $label_per_baris) {
                        $kolom = 1;
                        $baris++;
                        $clear = " clear:left; "; //echo "</div><div style=\"float:none\">"; // ganti baris
                    }
                    if ($baris > $baris_per_halaman) {
                        $baris = 1;
                        ?>
                        <p style="page-break-after: always; margin-top: -10px" />
                        <?php
                    }
                    // Harga Banded
                    $sql = "SELECT qty, harga FROM harga_banded WHERE barcode='{$r['tmpBarcode']}'";
                    $hasil = mysql_query($sql);
                    $hargaBanded = mysql_fetch_array($hasil, MYSQL_ASSOC);

                    $namaBarang1 = $r['tmpNama'];
                    $namaBarang2 = '&nbsp;';

                    $namaBarangLengkap = $r['tmpNama'];
                    // jika terlalu panjang nama barangnya
                    if (strlen($namaBarangLengkap) > $jumlahKarakterNamaBarang) {
                        $namaBarangArr = explode(' ', $namaBarangLengkap);
                        $len = 0;
                        $namaBarang1 = '';
                        $namaBarang2 = '';
                        foreach ($namaBarangArr as $namBar) {
                            $len += strlen($namBar);
                            if ($len <= $jumlahKarakterNamaBarang) {
                                $namaBarang1 .= $namBar . ' ';
                                $len++;
                            }
                            else {
                                $namaBarang2 .= $namBar . ' ';
                            }
                        }
                    }

                    // cetak label
                    echo "\n";
                    ?>
                    <div style="border: 1px solid #000; <?php echo $clear; ?> float:left; margin-right:5px; margin-bottom:0px; width:<?php echo $lebar_label - 10; ?>px; height:<?php echo $tinggi_label; ?>px; padding: 0 5px;">
                        <p style="line-height:0px; text-align:left; font-family:'Questrial'; font-size:11pt; font-weight:normal; text-transform:capitalize;">
                            <?php echo $namaBarang1; ?>
                        </p>
                        <p style="line-height:0px; text-align:left; font-family:'Questrial'; font-size:11pt; font-weight:normal; text-transform:capitalize;">
                            <?php echo $namaBarang2; ?>
                        </p>
                        <table style="font-family:'Times New Roman';width: 100%; margin-bottom: 10px; margin-top: -6px;border-top: 1px solid #000;">
                            <tr>
                                <td style="width: 20%;">Rp.</td>
                                <td style="width: 55%;font-size: 27pt;text-align: right; vertical-align: bottom"><?php echo number_format($r['tmpHargaJual'], 0, ',', '.'); ?></td>
                                <td style="font-size: 10pt;">/ <?php echo $r['tmpSatuan']; ?></td>
                            </tr>
                            <tr>
                                <td>Rp.</td>
                                <td style="font-size: 27pt;text-align: right; vertical-align: bottom"><?php echo number_format($hargaBanded['harga'] * $hargaBanded['qty'], 0, ',', '.'); ?></td>
                                <td style="font-size: 10pt;">/ <?php echo $hargaBanded['qty'] . $r['tmpSatuan']; ?></td>
                            </tr>
                        </table>
                        <span style="line-height:0px; text-align:left; font-family:'Questrial'; font-size:8pt; font-style: italic">
                            <?php echo $r['tmpBarcode']; ?>
                        </span>
                    </div>
                    <?php
                    $kolom++;
                }
                ?>
            </div>
            <?php
        }
        elseif ($_POST['layout'] == '3') {
            // Layout 3 = Layout harga banded dengan ukuran lebih kecil
            ?>
            <style>
                @font-face {
                    font-family: 'Questrial';
                    font-style: normal;
                    font-weight: 400;
                    src: url('../../font/Questrial-Regular.ttf');
                }
            </style>
            <?php
            $lebar_label = 200;
            $tinggi_label = 112;
            $label_per_baris = 3;
            $baris_per_halaman = 7;

            $jumlahKarakterNamaBarang = 30;

            $tanggal = date('dmY');
            $total = $_POST[total];
            $baris = 1;
            $kolom = 1;
            ?>
            <div style="float:none">
                <?php
                for ($i = 1; $i <= $total; $i++) {

                    $r = mysql_fetch_array($cari);

                    $clear = "";
                    // cek posisi saat ini
                    if ($kolom > $label_per_baris) {
                        $kolom = 1;
                        $baris++;
                        $clear = " clear:left; "; //echo "</div><div style=\"float:none\">"; // ganti baris
                    }
                    if ($baris > $baris_per_halaman) {
                        $baris = 1;
                        ?>
                        <p style="page-break-after: always; margin-top: -10px" />
                        <?php
                    }
                    // Harga Banded
                    $sql = "SELECT qty, harga FROM harga_banded WHERE barcode='{$r['tmpBarcode']}'";
                    $hasil = mysql_query($sql);
                    $hargaBanded = mysql_fetch_array($hasil, MYSQL_ASSOC);

                    $namaBarang1 = $r['tmpNama'];

                    // cetak label
                    echo "\n";
                    ?>
                    <div style="border: 1px solid #000; <?php echo $clear; ?> float:left; margin-right:5px; margin-bottom:10px; width:<?php echo $lebar_label - 10; ?>px; height:<?php echo $tinggi_label; ?>px; padding: 0 5px;">
                        <p style="margin-top: 3px; text-align:left; font-family:'Questrial'; font-size:8pt; font-weight:normal; text-transform:capitalize; border-bottom: 1px solid #000">
                            <?php echo $namaBarang1; ?>
                        </p>
                        <table style="border-collapse: collapse;font-family:'Times New Roman';width: 100%; margin-bottom: 15px; margin-top: -10px;">
                            <tr style="padding: 0">
                                <td style="width: 15%; vertical-align: bottom; padding-bottom: 7px; white-space: nowrap">Rp.</td>
                                <td style="font-size: 27pt;text-align: right; vertical-align: bottom; white-space: nowrap"><?php echo number_format($r['tmpHargaJual'], 0, ',', '.'); ?></td>
                                <td style="font-size: 8pt; width: 20%; vertical-align: bottom; padding-bottom: 7px; white-space: nowrap">/<?php echo $r['tmpSatuan']; ?></td>
                            </tr>
                        </table>
                        <table style="border-collapse: collapse;font-family:'Times New Roman';width: 100%; margin-bottom: 10px; margin-top: -25px;">
                            <tr>
                                <td style="width: 15%; vertical-align: bottom; padding-bottom: 7px; white-space: nowrap">Rp.</td>
                                <td style="font-size: 27pt;text-align: right; vertical-align: bottom; white-space: nowrap"><?php echo number_format($hargaBanded['harga'] * $hargaBanded['qty'], 0, ',', '.'); ?></td>
                                <td style="font-size: 8pt; width: 20%; vertical-align: bottom; padding-bottom: 7px; white-space: nowrap">/<?php echo $hargaBanded['qty'] . $r['tmpSatuan']; ?></td>
                            </tr>
                        </table>
                        <span style="line-height:0px; text-align:left; font-family:'Questrial'; font-size:7pt; font-style: italic">
                            <?php echo $r['tmpBarcode']; ?>
                        </span>
                    </div>
                    <?php
                    $kolom++;
                }
                ?>
            </div>
            <?php
        }
        else {

            $lebar_label = 200;
            $tinggi_label = 112;
            $label_per_baris = 3;
            $baris_per_halaman = 7;

            $jumlahKarakterNamaBarang = 19;

            // Layout
            // 0 = 3 mm (default) / 112px;
            // 1 = 3,3 mm
            if ($_POST['layout'] == '1') {
                $tinggi_label = 120;
            }

            $tanggal = date('dmY');
            $total = $_POST[total];
            $baris = 1;
            $kolom = 1;
            echo "<div style=\"float:none\">";

            for ($i = 1; $i <= $total; $i++) {

                $r = mysql_fetch_array($cari);

                $clear = "";
                // cek posisi saat ini
                if ($kolom > $label_per_baris) {
                    $kolom = 1;
                    $baris++;
                    $clear = " clear:left; "; //echo "</div><div style=\"float:none\">"; // ganti baris
                };
                if ($baris > $baris_per_halaman) {
                    $baris = 1;
                    echo '<p style="page-break-after: always" />';
                };

                $namaBarang1 = $r['tmpNama'];
                $namaBarang2 = '&nbsp;';

                $namaBarangLengkap = $r['tmpNama'];
                // jika terlalu panjang nama barangnya
                if (strlen($namaBarangLengkap) > $jumlahKarakterNamaBarang) {
                    $namaBarangArr = explode(' ', $namaBarangLengkap);
                    $len = 0;
                    $namaBarang1 = '';
                    $namaBarang2 = '';
                    foreach ($namaBarangArr as $namBar) {
                        $len += strlen($namBar);
                        if ($len <= $jumlahKarakterNamaBarang) {
                            $namaBarang1 .= $namBar . ' ';
                            $len++;
                        }
                        else {
                            $namaBarang2 .= $namBar . ' ';
                        }
                    }
                }

                // cetak label
                echo "\n

				<div style=\"border: thin solid #000000; $clear float:left; margin-right:10px; margin-bottom:10px; width:" . ($lebar_label - 10) . "px; height:" . $tinggi_label . "px; padding: 0 5px;\">
				<p style=\"line-height:0px; text-align:center; font-family:Arial; font-size:11pt; font-weight:normal; text-transform:uppercase;  \">
                {$namaBarang1}</p>
                <p style=\"line-height:0px; text-align:center; font-family:Arial; font-size:11pt; font-weight:normal; text-transform:uppercase;  \">
                {$namaBarang2}
				</p>
				<p style=\"line-height:0px; letter-spacing:+2px; text-align:center; font-family:Arial; font-size:26pt; \">
					" . number_format($r[tmpHargaJual], 0, ',', '.') . "	</p>
				<span style=\"line-height:0px; text-align:left; font-family:Arial; font-size:6pt; \">
					$r[tmpBarcode] - $r[tmpIdBarang]
                </span>
                <span style=\"line-height:0px; text-align:right; float:right; font-family:Arial; font-size:6pt; \">
					{$tanggal}
                </span>
				</div>
			";
                $kolom++;
            }
            echo "</div>";
        }

        break;
}
?>