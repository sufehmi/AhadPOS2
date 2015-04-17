<?php
/* pdt-so.php ------------------------------------------------------
  V: 1.0.0
  Part of AhadPOS : http://ahadpos.com
  License: GPL v2
  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
  http://vlsm.org/etc/gpl-unofficial.id.html

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License v2 (links provided above) for more details.
  ---------------------------------------------------------------- */

/*
 * Memakai template mobile SO yang sudah ada :)
 */
session_start();
include "../../config/config.php";

//$username = $_SESSION['uname'];
// $username = 'so';

$_SESSION['nomorraks'] = $_GET['nomorrak'];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>PDT SO - Ahad Mart</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <!-- Bootstrap -->
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/bootstrap-responsive.css" rel="stylesheet">

        <script src="../../js/jquery-1.9.1.min.js" ></script>
    </head>

    <body>

        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand">PDT SO</a>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="well" align="center">
                <?php
                /*
                 * Inputan pertama: Rak
                 */
                if (!isset($_GET['rak'])):
                    ?>
                    <h2>Masukkan Nomor Rak</h2>
                    <h2><small>Semua barang yang diinput akan masuk ke rak ini!</small></h2>
                    <form>
                        <table border="0">
                            <tr>
                                <td>
                                    <?php
                                    $sql = "select idRak, namaRak from rak ORDER BY LPAD(lower(namaRak), 10,0)";
                                    $raks = mysql_query($sql) or die('Gagal ambil data rak');
                                    ?>
                                    <select name="rak">
                                        <?php
                                        while ($rak = mysql_fetch_array($raks)) {
                                            ?>
                                            <option value="<?php echo $rak['idRak']; ?>"><?php echo $rak['namaRak']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div align="right">
                                        <p><input type="submit" class="btn btn-primary" name="submit" value="Submit" /></p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <?php
                elseif (!isset($_POST['barcodes']) && isset($_GET['rak']) && !isset($_POST['dataFinal'])):
                    // Inputan Kedua : list of: barcode, qty <enter>
                    ?>
                    <h2>Upload Data</h2>
                    <form method="POST">
                        <table border="0">
                            <tr>
                                <td>
                                    <textarea name="barcodes" rows="9" ></textarea>
                                    <input type="hidden" name="rak" value="<?php echo $_GET['rak']; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div align="right">
                                        <p><input type="submit" class="btn btn-primary" name="submit" value="Submit" /></p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <?php
                elseif (isset($_POST['barcodes']) && isset($_POST['rak'])) :
                    // Menerima data batch barcode, qty
                    $barcodes = explode("\n", $_POST['barcodes']);
                    $dataSO = array();
                    foreach ($barcodes as $barcode):
                        if (!empty($barcode) || $barcode != ''):
                            $data = explode(',', $barcode);
                            $dataSO[] = array(
                                'barcode' => $data[0],
                                'qty' => $data[1]
                            );
                        endif;
                    endforeach;

                    $rak = mysql_query("select namaRak from rak where idRak={$_POST['rak']}") or die('Gagal ambil data rak');
                    $dataRak = mysql_fetch_array($rak, MYSQL_ASSOC);
                    ?>
                    <h2>Konfirmasi?</h2>
                    <h2><small>Barang akan dimasukkan ke <?php echo $dataRak['namaRak']; ?></small> </h2>
                    <p>
                        <a class="btn btn-success" id="tombol-ok">OK</a> <a class="btn btn-warning" id="tombol-batal">Batal</a>
                    </p>
                    <table class="table table-bordered table-condensed table-striped">
                        <thead>
                            <tr>
                                <th style="text-align: center">#</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th style="text-align: right">Jumlah Asli</th>
                                <th style="text-align: right">Jumlah Tercatat</th>
                                <th style="text-align: right">Selisih</th>
                            </tr>
                        </thead>
                        <tbody>
                        <form id="kirim" method="POST">
                            <?php
                            $barangKirim = '';
                            $no = 1;
                            foreach ($dataSO as $data):
                                $sql = "select namaBarang, jumBarang from barang where barcode = '{$data['barcode']}'";
                                $result = mysql_query($sql) or die("Gagal ambil data barang: {$data['barcode']}");
                                $barang = mysql_fetch_array($result, MYSQL_ASSOC);
                                // if ($barang):
                                    $selisih = $data['qty'] - $barang['jumBarang'];
                                    ?>
                                    <tr>
                                        <td style="text-align: right"><?php echo $no; ?></td>
                                        <td style="font-family: courier"><?php echo $data['barcode']; ?></td>
                                        <td><?php echo $barang['namaBarang']; ?></td>
                                        <td style="text-align: right"><?php echo $data['qty']; ?></td>
                                        <td style="text-align: right"><?php echo $barang['jumBarang']; ?></td>
                                        <td style="text-align: right"><?php echo $selisih; ?></td>
                                    <!--<input type="hidden" name="dataFinal[<?php //echo $no;             ?>][idRak]" value="<?php //echo $_POST['rak'];             ?>" />-->
                                    <input type="hidden" name="dataFinal[<?php echo $no; ?>][barcode]" value="<?php echo $data['barcode']; ?>" />
                                    <input type="hidden" name="dataFinal[<?php echo $no; ?>][jmlTercatat]" value="<?php echo $barang['jumBarang']; ?>" />
                                    <input type="hidden" name="dataFinal[<?php echo $no; ?>][selisih]" value="<?php echo $selisih; ?>" />
                                    </tr>
                                    <?php
                                    $no++;
                                // endif;
                            endforeach;
                            ?>

                        </form>
                        </tbody>
                    </table>
                    <script>
                        $("#tombol-batal").click(function () {
                            window.history.back();
                        });

                        $("#tombol-ok").click(function () {
                            $("#kirim").submit();
                        });
                    </script>
                    <?php
                elseif (isset($_POST['dataFinal'])):
                    $idRak = $_GET['rak'];
                    $dataFinal = $_POST['dataFinal'];
                    $tanggal = date('Y-m-d');
                    foreach ($dataFinal as $data):
                        $jmlTercatat = $data['jmlTercatat'] == '' ? '0' : $data['jmlTercatat'];
                        $barangs = mysql_query("select barcode from fast_stock_opname where username='pdt-so' and barcode = '{$data['barcode']}' and approved=0") or die('Gagal ambil data barang di tabel SO: ' . mysql_error());
                        $barangAda = mysql_num_rows($barangs);
                        //Jika ada, update. Jika tidak ada, insert
                        //Yang dipakai hanya data yang terakhir discan
                        if ($barangAda >= 1) :
                            $sql = "update fast_stock_opname set jmlTercatat= {$jmlTercatat}, selisih={$data['selisih']} where "
                                    . "username = 'pdt-so' and barcode= '{$data['barcode']}'";
                            //echo $sql;
                            //echo '<br />';
                            mysql_query($sql) or die('Gagal update PDT SO ' . mysql_error());
                        else :
                            $sql = "insert into fast_stock_opname (idRak, tanggalSO, username, barcode, jmlTercatat, selisih, approved) VALUES "
                                    . "({$idRak}, '{$tanggal}', 'pdt-so', '{$data['barcode']}', {$jmlTercatat}, {$data['selisih']}, 0) ";
                            //echo $sql;
                            //echo '<br />';
                            mysql_query($sql) or die('Gagal Insert PDT SO' . mysql_error());
                        endif;
                    endforeach;
                    ?>
                    <h2>Data stock sudah tersimpan</h2>
                    <script>

                        $(function () {
                            window.setTimeout(awal, 3000);
                        });

                        function awal() {
                            window.location = "<?php echo $_SERVER['PHP_SELF']; ?>";
                        }
                    </script>
                    <?php
                endif;
                ?>

            </div>
        </div>
    </body>
</html>
<?php
/* CHANGELOG -----------------------------------------------------------

  1.0.0 / 2014-11-05 : Bambang Abu Muhammad		: initial release

  ------------------------------------------------------------------------ */
?>
