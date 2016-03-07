<?php
/* mod_laporan.php ------------------------------------------------------
  version: 1.01

  Part of AhadPOS : http://ahadpos.com
  License: GPL v2
  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
  http://vlsm.org/etc/gpl-unofficial.id.html

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License v2 (links provided above) for more details.
  ---------------------------------------------------------------- */


include "../../config/config.php";
include_once 'function.php';
check_user_access();

echo "
	<link href='../../css/style.css' rel='stylesheet' type='text/css' />

        <SCRIPT TYPE='text/javascript'>
        <!--
        function popupform(myform, windowname)
        {
                if (! window.focus)return true;
                window.open('', windowname, 'type=fullWindow,fullscreen,scrollbars=yes');
                myform.target=windowname;
                return true;
        }
        //-->
        </SCRIPT>
	";
?>

<?php // JqueryUI untuk autocomplete cari nama barang ?>
<script type="text/javascript" src="../js/jquery-ui.min-ac.js"></script>
<?php
switch ($_GET[act]) { //------------------------------------------------------------------------
    default:

        echo "<h2>Laporan Manajemen</h2>

		<table>
		<tr>

		<td>
			<form method=POST action='?module=laporan&act=penjualan1'>
			<input type=submit value='(j) Laporan Penjualan' accesskey='j'>
			</form>
		</td>

		<td>
			<form method=POST action='?module=pembelian_barang&act=laporanpembeliantanggal'>
			<input type=submit value='(b) Laporan Pembelian per tanggal' accesskey='b'>
			</form>
		</td>

		<td>
			<form method=POST action='?module=pembelian_barang&act=laporanpembelian'>
			<input type=submit value='(c) Laporan Pembelian per supplier' accesskey='c'>
			</form>
		</td>

		<td>
			<form method=POST action='?module=laporan&act=total1'>
			<input type=submit value='(t) Total Stok' accesskey='t'>
			</form>
		</td>

		</tr>

		<tr>

			<td>
			<form method=POST action='?module=laporan&act=toprank1'>
			<input type=submit value='(r) Top Rank' accesskey='r'>
			</form>
			</td>


			<td>
			<form method=POST action='?module=laporan&act=aging1'>
			<input type=submit value='(a) Aging' accesskey='a'>
			</form>
			</td>

		</tr>

		<tr>

			<td>
			<form method=POST action='?module=laporan&act=po'>
			<input type=submit value='(p) Purchase Order' accesskey='r'>
			</form>
			</td>
		</tr>


		</table>

	";

        break;



    case "penjualan1":  // ===========================================================================================================


        echo "<h2>Laporan Penjualan</h2>";

// ambil daftar nama kasir
// idLevelUser : 4 = kasir
        $sql = "SELECT namaUser, idUser
		FROM user
		WHERE idLevelUser = 4 ORDER BY namaUser ASC";
        $namaKasir = mysql_query($sql);
        ?>
        <form method=GET action="?module=laporan">

            <input type=hidden name=module value=laporan>
            <input type=hidden name=act value=penjualan2>

            <table>
                <tr>
                    <td>(d) Dari Tanggal </td>
                    <td>: <input type="text" class="tanggalan" name="DariTanggal" value="<?php echo date("d-m-Y 00:00:00"); ?>" accesskey='d'></td>
                </tr>
                <tr>
                    <td>Sampai Tanggal </td>
                    <td>: <input type="text" class="tanggalan" name="SampaiTanggal" value="<?php echo date("d-m-Y 23:59:59"); ?>"></td>
                </tr>
                <tr>
                    <td>Pilih Kasir </td>
                    <td>: <select name='idKasir'>
                            <option value='SEMUA' selected>SEMUA</option>";
                            <?php
                            while ($kasir = mysql_fetch_array($namaKasir)) :
                                ?>
                                <option value="<?php echo $kasir['idUser']; ?>"><?php echo $kasir['namaUser']; ?></option>
                                <?php
                            endwhile;
                            ?>
                    </td>
                </tr>

                <tr><td colspan=2>&nbsp;</td></tr>
                <tr><td colspan=2>
                        <input type=submit value='Buat Laporan'>&nbsp;&nbsp;&nbsp;
                        <input type=reset value='Batal'>
                    </td>
                </tr>
            </table>
        </form>
        <script>
            $(function () {
                $('.tanggalan').appendDtpicker({
                    "closeOnSelected": true,
                    'locale': 'id',
                    'dateFormat': 'DD-MM-YYYY hh:mm'
                });
            });
        </script>
        <?php
        break;



    case "penjualan2":  // ===========================================================================================================
// ambil daftar nama kasir
        if ($_GET[idKasir] == 'SEMUA') {
            $x[namaUser] = 'SEMUA';
        } else {
            $sql = "SELECT namaUser FROM user WHERE idUser = $_GET[idKasir]";
            $hasil = mysql_query($sql);
            $x = mysql_fetch_array($hasil);
        }

        echo "
              <br/>
              <h2>Laporan Penjualan</h2>

		<h3>Kasir: $x[namaUser], Dari: $_GET[DariTanggal], Sampai: $_GET[SampaiTanggal]</h3>";
        ?>
        <table class="tabel">
            <tr>
                <th>No.Struk</th>
                <th>Waktu</th>
                <th>Total Transaksi</th>
                <th>Aksi</th>
                <!--<th>Hapus?</th>-->
            </tr>
            <?php
            $dariTanggal = date_format(date_create_from_format('d-m-Y H:i', $_GET['DariTanggal']), 'Y-m-d H:i');
            $sampaiTanggal = date_format(date_create_from_format('d-m-Y H:i', $_GET['SampaiTanggal']), 'Y-m-d H:i');

            if ($_GET[idKasir] == 'SEMUA') {
                $sql = "SELECT t.idTransaksiJual, t.tglTransaksiJual, t.nominal
				FROM transaksijual AS t
				WHERE t.tglTransaksiJual BETWEEN '$dariTanggal' AND '$sampaiTanggal'
 					ORDER BY t.idTransaksiJual ASC";
            } else {
                $sql = "SELECT t.idTransaksiJual, t.tglTransaksiJual, t.nominal
				FROM transaksijual AS t
				WHERE t.idUser = $_GET[idKasir]
					AND t.tglTransaksiJual BETWEEN '$dariTanggal' AND '$sampaiTanggal' ORDER BY t.idTransaksiJual ASC";
            }
            $tampil = mysql_query($sql);

            $no = 1;
            $total_transaksi = 0;
            while ($r = mysql_fetch_array($tampil)) {
                ?>
                <tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>">
                    <td class="center"><?php echo $r['idTransaksiJual']; ?></td>
                    <td class="center"><?php echo date("H:i:s", strtotime($r['tglTransaksiJual'])); ?></td>
                    <td class="right"><?php echo number_format($r['nominal'], 0, ',', '.'); ?></td>
                    <td>	
                        <a href='?module=laporan&act=aksi&action=cetakjual1&id=<?php echo $r['idTransaksiJual']; ?>&kasir=<?php echo $x['namaUser']; ?>'>Cetak</a> |
                        <a href='?module=laporan&act=aksi&action=lihatjual&id=<?php echo $r['idTransaksiJual']; ?>&kasir=<?php echo $x['namaUser']; ?>'>Lihat</a>
                        <?php /* <td class="right"> Ha<a href='?module=laporan&act=aksi&action=hapusjual&id=<?php echo $r['idTransaksiJual']; ?>&idKasir=<?php echo $_GET['idKasir']; ?>&DariTanggal=<?php echo $_GET['DariTanggal']; ?>&SampaiTanggal=<?php echo $_GET['SampaiTanggal']; ?>'>p</a>us</td> */ ?>
                </tr>
                <?php
                //fixme: tampilkan juga profit dari setiap invoice

                $total_transaksi = $total_transaksi + $r[nominal];
                $no++;
            }


            // hitung profit
            if ($_GET[idKasir] == 'SEMUA') {
                $sql = "SELECT SUM((d.hargaJual - d.hargaBeli) * jumBarang) AS profit
				FROM transaksijual AS t, detail_jual AS d
				WHERE t.tglTransaksiJual BETWEEN '$dariTanggal' AND '$sampaiTanggal' AND t.idTransaksiJual=d.nomorStruk
 					ORDER BY t.idTransaksiJual ASC";
            } else {
                $sql = "SELECT SUM((d.hargaJual - d.hargaBeli) * jumBarang) AS profit
				FROM transaksijual AS t, detail_jual AS d
				WHERE t.idUser = $_GET[idKasir] AND t.idTransaksiJual=d.nomorStruk
				AND t.tglTransaksiJual BETWEEN '$dariTanggal' AND '$sampaiTanggal' ORDER BY t.idTransaksiJual ASC";
            };
            $tampil = mysql_query($sql);
            $r = mysql_fetch_array($tampil);
            $total_profit = 0;
            $total_profit = $total_profit + $r[profit];

            echo "</table>";

            if ($_GET[idKasir] == 'SEMUA') {
                $sql = "SELECT t.id, t.tglTransaksi, t.nominal
				FROM transaksireturjual AS t
				WHERE t.tglTransaksi BETWEEN '$dariTanggal' AND '$sampaiTanggal'
 					ORDER BY t.tglTransaksi ASC";
            } else {
                $sql = "SELECT t.id, t.tglTransaksi, t.nominal
				FROM transaksireturjual AS t
				WHERE t.idKasir = {$_GET['idKasir']}
					AND t.tglTransaksi BETWEEN '$dariTanggal' AND '$sampaiTanggal' ORDER BY t.tglTransaksi ASC";
            }
            $tampil = mysql_query($sql);
            ?>
            <h2>Retur Penjualan</h2>
            <table class="tabel">
                <tr>
                    <th>No.Struk</th>
                    <th>Waktu</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
                <?php
                $totalReturJual = 0;
                while ($r = mysql_fetch_array($tampil, MYSQL_ASSOC)) {
                    ?>
                    <tr>
                        <td class="right"><?php echo $r['id']; ?></td>
                        <td class="center"><?php echo date("H:i:s", strtotime($r['tglTransaksi'])); ?></td>
                        <td class="right"><?php echo number_format($r['nominal'], 0, ',', '.'); ?></td>
                        <td class="center"><a href='?module=laporan&act=aksi&action=lihatreturjual&id=<?php echo $r['id']; ?>'>Lihat</a></td>
                    </tr>
                    <?php
                    $totalReturJual+=$r['nominal'];
                }

                // hitung profit retur jual (minus)
                if ($_GET[idKasir] == 'SEMUA') {
                    $sql = "SELECT SUM((d.hargaJual - d.hargaBeli) * jumBarang) AS profit
				FROM transaksireturjual AS t, detail_retur_barang AS d
				WHERE t.tglTransaksi BETWEEN '$dariTanggal' AND '$sampaiTanggal' AND t.id=d.idTransaksiRetur
 					ORDER BY t.id ASC";
                } else {
                    $sql = "SELECT SUM((d.hargaJual - d.hargaBeli) * jumBarang) AS profit
				FROM transaksireturjual AS t, detail_retur_barang AS d
				WHERE t.idKasir = $_GET[idKasir] AND t.id=d.idTransaksiRetur
				AND t.tglTransaksi BETWEEN '$dariTanggal' AND '$sampaiTanggal' ORDER BY t.id ASC";
                }
                $tampil = mysql_query($sql);
                $r = mysql_fetch_array($tampil);
                $total_profit_retur = $r['profit'];
                ?>
            </table>

            <h2>Total Transaksi: <?php echo number_format($total_transaksi, 0, ',', '.'); ?></h2>
            <h2>Total Profit: <?php echo number_format($total_profit, 0, ',', '.'); ?></h2>
            <h2>Total Retur Jual: <span  style="color:red">(<?php echo number_format($totalReturJual, 0, ',', '.'); ?>)</span></h2>
            <h2>Total Retur Profit: <span  style="color:red">(<?php echo number_format($total_profit_retur, 0, ',', '.'); ?>)</span></h2>

            <p>&nbsp;</p>
            <a href=javascript:history.go(-1)><< Kembali</a>

            <?php
            break;

        case 'diskon1':
            ?>
            <h2>Laporan Diskon</h2>
            <form method=GET action='?module=laporan'>

                <input type=hidden name=module value=laporan>
                <input type=hidden name=act value=diskon2>
                <table>
                    <tr>
                        <td>(d) Dari Tanggal </td>
                        <td>: <input type=text class="tanggalan" name='DariTanggal' value='<?php echo date("d-m-Y 00:00"); ?>' accesskey='d'></td>
                    </tr>
                    <tr>
                        <td>Sampai Tanggal </td>
                        <td>: <input type=text class="tanggalan" name='SampaiTanggal' value='<?php echo date("d-m-Y 23:59"); ?>'></td>
                    </tr>
                    <tr>
                        <td>Pilih Kasir </td>
                        <td>: <select name='idKasir'> <option value='SEMUA' selected>SEMUA</option>
                                <?php
                                // ambil daftar nama kasir
                                // idLevelUser : 4 = kasir
                                $sql = "SELECT namaUser, idUser
											FROM user
											WHERE idLevelUser = 4 ORDER BY namaUser ASC";
                                $namaKasir = mysql_query($sql);
                                while ($kasir = mysql_fetch_array($namaKasir)) :
                                    ?>
                                    <option value='<?php echo $kasir['idUser']; ?>'><?php echo $kasir['namaUser']; ?></option>
                                    <?php
                                endwhile;
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Pilih Jenis Diskon </td>
                        <td>: <select name="tipeDiskon">
                                <option value="SEMUA" selected>SEMUA</option>
                                <?php
                                $sql = "select uid, nama, deskripsi from diskon_tipe order by uid";
                                $hasil = mysql_query($sql) or die('Gagal ambil tipe diskon, error: ' . mysql_error());
                                while ($tipeDiskon = mysql_fetch_array($hasil)):
                                    ?>
                                    <option value="<?php echo $tipeDiskon['uid']; ?>"><?php echo "{$tipeDiskon['nama']} | {$tipeDiskon['deskripsi']}"; ?></option>
                                    <?php
                                endwhile;
                                ?>
                            </select></td>
                    </tr>
                    <tr><td colspan=2>&nbsp;</td></tr>
                    <tr><td colspan=2><input type=submit value='Buat Laporan'>&nbsp;&nbsp;&nbsp;
                            <input type=reset value='Batal'></td></tr>
                </table>
            </form>

            <script>
                $(function () {
                    $('.tanggalan').appendDtpicker({
                        "closeOnSelected": true,
                        'locale': 'id',
                        'dateFormat': 'DD-MM-YYYY hh:mm'
                    });
                });
            </script>
            <?php
            break;

        case 'diskon2':
            // ambil daftar nama kasir
            if ($_GET['idKasir'] == 'SEMUA') {
                $x['namaUser'] = 'SEMUA';
            } else {
                $sql = "SELECT namaUser FROM user WHERE idUser = {$_GET['idKasir']}";
                $hasil = mysql_query($sql);
                $x = mysql_fetch_array($hasil);
            }
            $tipeDiskon = $_GET['tipeDiskon'];
            ?>
            <h2>Laporan Diskon</h2>
            <h3>Kasir: <?php echo $x['namaUser']; ?>, Dari: <?php echo $_GET['DariTanggal']; ?>, Sampai: <?php echo $_GET['SampaiTanggal']; ?></h3>
            <table class="tabel">
                <tr>
                    <th>Nomor Struk</th>
                    <th>Waktu Transaksi</th>
                    <th>Barcode</th>
                    <th>Harga Jual</th>
                    <th>Harga Beli</th>
                    <th>Banyaknya</th>
                    <th>Total<br />Harga (Net)</th>
                    <th>Total<br />Diskon</th>
                    <th>Diskon @</th>
                    <th>Detail<br />Diskon @</th>
                    <?php
                    if ($tipeDiskon != 'SEMUA'):
                        ?>
                        <th>Sub Total</th>
                        <?php
                    endif;
                    ?>
                </tr>
                <?php
                $dariTanggal = date_format(date_create_from_format('d-m-Y H:i', $_GET['DariTanggal']), 'Y-m-d H:i');
                $sampaiTanggal = date_format(date_create_from_format('d-m-Y H:i', $_GET['SampaiTanggal']), 'Y-m-d H:i');
                $sql = "select
						dj.nomorStruk,
						tj.tglTransaksiJual,
						dt.barcode,
						dj.hargaJual,
						dj.hargaBeli,
						dj.jumBarang,
						(dj.hargaJual * dj.jumBarang) net,
						(dt.diskon_rupiah * dj.jumBarang) totalDiskon,
						dt.diskon_rupiah as diskon,
						dt.diskon_detail_uids
						from diskon_transaksi dt
						join detail_jual dj on dt.idDetailJual = dj.uid
						join transaksijual tj on tj.idTransaksiJual = dj.nomorStruk
						where tj.tglTransaksiJual between '{$dariTanggal}' and '{$sampaiTanggal}' ";
                if ($_GET['idKasir'] != 'SEMUA') {
                    $sql.= "and tj.idUser = {$_GET['idKasir']} ";
                }
                $sql.= "order by nomorStruk";

                $result = mysql_query($sql) or die('Gagal ambil data diskon, error: ' . mysql_error());

                $no = 1;
                $totalDiskon = 0;
                while ($data = mysql_fetch_array($result)):
                    //$totalDiskon += $data['totalDiskon'];
                    /*
                     * Cek Tipe Diskon dulu !!
                     */
                    $diskonDetail = '';
                    $dariTanggal = date_format(date_create_from_format('d-m-Y H:i', $_GET['DariTanggal']), 'Y-m-d H:i');
                    $sampaiTanggal = date_format(date_create_from_format('d-m-Y H:i', $_GET['SampaiTanggal']), 'Y-m-d H:i');
                    $diskonDetail = json_decode($data['diskon_detail_uids'], true);
                    $adaDiskon = false;
                    foreach ($diskonDetail as $key => $value) {
                        /*
                         * Jika tipe diskon adalah admin / customer
                         */
                        if ($key < 3 && $key == $tipeDiskon) {
                            $adaDiskon = true;
                            break;
                        } else {
                            $dataDiskon = null;
                            $hasil = mysql_query("select * from diskon_detail where uid=$key") or die("Gagal ambil detail diskon $key, error:" . mysql_error());
                            $dataDiskon = mysql_fetch_array($hasil);
                            if ($tipeDiskon == $dataDiskon['diskon_tipe_id']) {
                                $adaDiskon = true;
                                break;
                            }
                        }
                    }
                    if ($tipeDiskon === 'SEMUA' || $adaDiskon):
                        ?>
                        <tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>">
                            <td><?php echo $data['nomorStruk']; ?></td>
                            <td><?php echo $data['tglTransaksiJual']; ?></td>
                            <td><?php echo $data['barcode']; ?></td>
                            <td class="right"><?php echo number_format($data['hargaJual'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($data['hargaBeli'], 0, ',', '.'); ?></td>
                            <td class="center"><?php echo $data['jumBarang']; ?></td>
                            <td class="right"><?php echo number_format($data['net'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($data['totalDiskon'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($data['diskon'], 0, ',', '.'); ?></td>
                            <td class="right"><?php
                                // var_dump($diskonDetail);
                                // mysql_query("select * from diskon_detail where uid=")
                                $subTotal = 0;
                                foreach ($diskonDetail as $key => $value) {
                                    if ($key < 3) {
                                        /*
                                         * Untuk diskon admin dan diskon per customer
                                         */
                                        switch ($key):
                                            case 1:
                                                if ($tipeDiskon === 'SEMUA' || $tipeDiskon == 1) {
                                                    $s = '(Admin) ' . number_format($value, 0, ',', '.');
                                                    $subTotal = $value * $data['jumBarang'];
                                                    $totalDiskon += $subTotal;
                                                }
                                                break;
                                            case 2:
                                                if ($tipeDiskon === 'SEMUA' || $tipeDiskon == 2) {
                                                    $s = '(Customer) ' . number_format($value, 0, ',', '.');
                                                    $subTotal = $value * $data['jumBarang'];
                                                    $totalDiskon += $subTotal;
                                                }
                                                break;
                                        endswitch;
                                        echo $s;
                                    } else {
                                        /*
                                         * Untuk diskon grosir dan diskon waktu
                                         */
                                        $dataDiskon = null;
                                        $hasil = mysql_query("select * from diskon_detail where uid=$key") or die("Gagal ambil detail diskon $key, error:" . mysql_error());
                                        $dataDiskon = mysql_fetch_array($hasil);
                                        //echo "<br />dataDiskonTipeId: {$dataDiskon['diskon_tipe_id']}<br />tipeDiskon: {$tipeDiskon}<br />";
                                        $s = '';
                                        if ($tipeDiskon === 'SEMUA' || $tipeDiskon == $dataDiskon['diskon_tipe_id']) {
                                            $s = "({$dataDiskon['diskon_tipe_nama']}) ";
                                            if ($dataDiskon['diskon_persen'] > 0) {
                                                $s.=$dataDiskon['diskon_persen'] . '%';
                                            } else {
                                                $s.=number_format($dataDiskon['diskon_rupiah'], 0, ',', '.');
                                            }
                                            $subTotal = $dataDiskon['diskon_rupiah'] * $data['jumBarang'];
                                            $totalDiskon += $subTotal;
                                            echo $s . '<br />';
                                        }
                                    }
                                }
                                ?>
                            </td>
                            <?php
                            /*
                             * Jika diskon tidak semua, maka tampilkan sub total diskon
                             */
                            if ($tipeDiskon != 'SEMUA'):
                                ?>
                                <td><?php echo number_format($subTotal, 0, ',', '.'); ?></td>
                                <?php
                            endif;
                            ?>
                        </tr>
                        <?php
                        $no++;
                    endif;
                endwhile;
                ?>
            </table>
            <h2>Total Diskon: <?php echo number_format($totalDiskon, 0, ',', '.'); ?></h2>
            <p>&nbsp;</p>
            <a href=javascript:history.go(-1)><< Kembali</a>
            <?php
            break;

        case "aksi":  // ===========================================================================================================


            if ($_GET[action] == 'hapusjual') { // ---------------------------------------------------------------------------------
                // cek transaksi jualnya
                /*
                  $sql = "SELECT idBarang, barcode, jumBarang, hargaJual FROM detail_jual WHERE nomorStruk = $_GET[id]";
                  $hasil = mysql_query($sql);

                  $grandtotal = 0;
                  while ($x = mysql_fetch_array($hasil)) {

                  $grandtotal = $grandtotal + ($x[jumBarang] * $x[hargaJual]);

                  // kembalikan jumlah stok sebelumnya di tabel barang
                  // cari jumlah saat ini di table barang
                  $sql = "SELECT jumBarang FROM barang WHERE barcode = '".$x[barcode]."'";
                  $hasil1 = mysql_query($sql);
                  $x1 = mysql_fetch_array($hasil1);

                  $jumlahbaru = $x1[jumBarang] + $x[jumBarang];
                  // simpan jumlah yang baru
                  $sql = "UPDATE barang SET jumBarang = $jumlahbaru WHERE barcode = '".$x[barcode]."'";
                  $hasil1 = mysql_query($sql);

                  // kembalikan jumlah stok sebelumnya di tabel detail_beli
                  // cari jumlah saat ini di table barang
                  $BarangHabis = false;
                  $sql = "SELECT jumBarang FROM detail_beli WHERE barcode = '".$x[barcode]."'
                  AND isSold = 'N' AND idBarang = ".$x[idBarang]."
                  ORDER BY idDetailBeli ASC";
                  $hasil2 = mysql_query($sql);

                  // kalau tidak ada yang ketemu, cari lagi - kali ini isSold = 'Y'
                  if (mysql_num_rows($hasil2) < 1) {
                  $sql = "SELECT jumBarang FROM detail_beli WHERE barcode = '".$x[barcode]."'
                  AND isSold = 'Y' AND idBarang = ".$x[idBarang]."
                  ORDER BY idDetailBeli DESC";
                  $hasil2 = mysql_query($sql);
                  $BarangHabis = true;
                  };
                  $x2 = mysql_fetch_array($hasil2);

                  $jumlahbaru = $x2[jumBarang] + $x[jumBarang];
                  // simpan jumlah yang baru
                  if ($BarangHabis) {
                  $sql = "UPDATE detail_beli SET jumBarang = $jumlahbaru, isSold = 'N' WHERE idBarang = '".$x[idBarang]."'";
                  } else {
                  $sql = "UPDATE detail_beli SET jumBarang = $jumlahbaru WHERE idBarang = '".$x[idBarang]."'";
                  };
                  $hasil1 = mysql_query($sql);
                  }; // while ($x = mysql_fetch_array($hasil))
                  // simpan audit trail nya
                  $sql = "INSERT INTO audit (jenisTransaksi, username, tglTransaksi,
                  nomorStruk, nominalStruk)
                  VALUES ('returnotajual', '$_SESSION[uname]', '".date("Y-m-d H:i:s")."',
                  $_GET[id], $grandtotal)";
                  $hasil = mysql_query($sql) or die(mysql_error());


                  // hapus di transaksi_jual
                  $sql = "DELETE FROM transaksijual WHERE idTransaksiJual = $_GET[id]";
                  $hasil = mysql_query($sql) or die(mysql_error());

                  // hapus juga seluruh transaksinya di detail_jual
                  $sql = "DELETE FROM detail_jual WHERE nomorStruk = $_GET[id]";
                  $hasil = mysql_query($sql) or die(mysql_error());

                  // module=laporan&act=penjualan2&DariTanggal=2010-08-02+00%3A00%3A00&SampaiTanggal=2010-08-02+23%3A59%3A59&idKasir=SEMUA
                  header("location:media.php?module = laporan&act = penjualan2&DariTanggal = $_GET[DariTanggal]&SampaiTanggal = $_GET[SampaiTanggal]&idKasir = $_GET[idKasir]");
                 * 
                 */
            }

            if ($_GET[action] == 'cetakjual1') { // ---------------------------------------------------------------------------------
                // pilih printer
                $sql = "SELECT idWorkStation, namaWorkstation, printer_commands, workstation_address FROM workstation ";
                $hasil = mysql_query($sql) or die(mysql_error());
//			<form method = POST action = '?module=laporan&act=aksi&action=cetakjual2'>
                ?>
                <form method = POST action = 'aksi.php?module=laporan&act=cetakjual'>

                    <table>
                        <tr>
                            <td>Pilih Printer </td>
                            <td>: <select name = 'idWorkStation'>";
                                    <?php
                                    while ($printer = mysql_fetch_array($hasil)) {
                                        ?>
                                        <option value = '<?php echo "{$printer['idWorkStation']}"; ?>'><?php echo "{$printer['namaWorkstation']}"; ?></option>
                                        <?php
                                    }
                                    ?>
                            </td>
                            <td>Pilih layout struk</td>
                            <td>
                                <select name="layoutStruk">
                                    <option value="struk">Struk Kasir</option>
                                    <option value="invoice">Invoice (A4)</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td colspan = 2>&nbsp;	</td>
                        </tr>
                        <tr>
                            <td colspan = 2><input type = submit value = 'Submit'>&nbsp;&nbsp;&nbsp;<input type = reset value = 'Batal'>
                            </td>
                        </tr>
                    </table>

                    <input type = hidden name = idTransaksi value = '<?php echo $_GET['id']; ?>'>
                    <input type = hidden name = namaKasir value = '<?php echo $_GET['kasir']; ?>'>

                </form>

                <br /><br /> <a href = javascript:history.go(-1)><< Kembali</a>
                <?php
            } // if ($_GET[action] == 'cetakjual1')



            if ($_GET[action] == 'cetakjual2') { // ---------------------------------------------------------------------------------
                // Pindah ke aksi.php !! Kemungkinan tidak dipakai
                // tampilkan link untuk kembali
                echo "<br /><br /> <a href = javascript:history.go(-1)><< Kembali</a>";
            }


            if ($_GET[action] == 'lihatjual') { // ---------------------------------------------------------------------------------
                if ($_GET[kasir] == 'SEMUA') {
                    $namaKasir = 'SEMUA';
                } else {
                    $namaKasir = $_GET[kasir];
                }

                echo "
						<br/>
						<h2>Detail Penjualan</h2>

						<h3>Kasir: $namaKasir, No.Struk: $_GET[id]</h3>";
                ?>
                <table class="tabel">
                    <tr>
                        <th>Barcode</th>
                        <th>Nama Barang</th>
                        <th>Harga Jual</th>
                        <th>Harga Beli</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Total Diskon</th>
                        <th>Harga Jual Asli<br />(Selisih)</th>
                    </tr>
                    <?php
                    // $sql = "SELECT d.barcode, b.namaBarang, d.hargaJual, d.hargaBeli, d.jumBarang
                    // 			FROM detail_jual AS d, barang AS b
                    // 			WHERE d.nomorStruk = $_GET[id] AND d.barcode = b.barcode";
                    $sql = "select dj.barcode, b.namaBarang, dj.hargaBeli, dj.hargaJual, dj.harga_jual_asli,
								dj.jumBarang, dt.diskon_persen, dt.diskon_rupiah
								from detail_jual dj
								join barang b on b.barcode = dj.barcode
								left join diskon_transaksi dt on dt.idDetailJual = dj.uid
								where dj.nomorStruk = {$_GET['id']}
								order by dj.uid";
                    $tampil = mysql_query($sql) or die('Gagal ambil detail penjualan, error:' . mysql_error());

                    $no = 1;
                    $total_transaksi = 0;
                    $total_profit = 0;
                    $total_diskon = 0;
                    while ($r = mysql_fetch_array($tampil)) {
                        ?>
                        <tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>">
                            <td><?php echo $r['barcode']; ?></td>
                            <td><?php echo $r['namaBarang']; ?>	</td>
                            <td class="right"><?php echo number_format($r['hargaJual'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($r['hargaBeli'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($r['jumBarang'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format(($r['hargaJual'] * $r['jumBarang']), 0, ',', '.'); ?></td>
                            <td class="right">
                                <?php
                                if ($r['diskon_persen'] > 0) {
                                    echo $r['diskon_persen'] . '%' . ' = ';
                                    $total_diskon += $r['diskon_persen'] / 100 * $r['hargaJual'] * $r['jumBarang'];
                                }
                                if ($r['diskon_rupiah'] > 0) {
                                    echo number_format($r['diskon_rupiah'] * $r['jumBarang'], 0, ',', '.');
                                    $total_diskon += $r['diskon_rupiah'];
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo is_null($r['harga_jual_asli']) ? '' : number_format($r['harga_jual_asli'], 0, ',', '.') . ' (' . number_format($r['harga_jual_asli'] - $r['hargaJual'], 0, ',', '.') . ')'; ?>
                            </td>
                        </tr>
                        <?php
                        $total_transaksi = $total_transaksi + ($r[hargaJual] * $r[jumBarang]);
                        $total_profit = $total_profit + (($r[hargaJual] - $r[hargaBeli]) * $r[jumBarang]);
                        $no++;
                    }
                    ?>
                </table>

                <h3>Total Transaksi : <?php echo number_format($total_transaksi, 0, ',', '.'); ?></h3>
                <h3>Total Profit : <?php echo number_format($total_profit, 0, ',', '.'); ?></h3>

                <p>&nbsp;</p>
                <a href=javascript:history.go(-1)><< Kembali</a>
                <?php
            }
            if ($_GET[action] == 'lihatreturjual') { // ---------------------------------------------------------------------------------
                if ($_GET[kasir] == 'SEMUA') {
                    $namaKasir = 'SEMUA';
                } else {
                    $namaKasir = $_GET[kasir];
                }

                echo "
						<br/>
						<h2>Detail Retur Penjualan</h2>

						<h3>No.Struk: $_GET[id]</h3>";
                ?>
                <table class="tabel">
                    <tr>
                        <th>Barcode</th>
                        <th>Nama Barang</th>
                        <th>Harga Jual</th>
                        <th>Harga Beli</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                    <?php
                    $sql = " SELECT detail.barcode, barang.namaBarang, detail.hargaBeli, detail.hargaJual, detail.jumBarang 
                        FROM detail_retur_barang detail
                        JOIN barang on detail.barcode = barang.barcode
                        where idTransaksiRetur =  {$_GET['id']}
                        order by detail.uid";
                    $tampil = mysql_query($sql) or die('Gagal ambil detail retur penjualan, error:' . mysql_error());

                    $no = 1;
                    $total_transaksi = 0;
                    $total_profit = 0;
                    while ($r = mysql_fetch_array($tampil)) {
                        ?>
                        <tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>">
                            <td><?php echo $r['barcode']; ?></td>
                            <td><?php echo $r['namaBarang']; ?>	</td>
                            <td class="right"><?php echo number_format($r['hargaJual'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($r['hargaBeli'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($r['jumBarang'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format(($r['hargaJual'] * $r['jumBarang']), 0, ',', '.'); ?></td>

                        </tr>
                        <?php
                        $total_transaksi = $total_transaksi + ($r['hargaJual'] * $r['jumBarang']);
                        $total_profit = $total_profit + (($r['hargaJual'] - $r['hargaBeli']) * $r['jumBarang']);
                        $no++;
                    }
                    ?>
                </table>

                <h3>Total Retur Jual : <?php echo number_format($total_transaksi, 0, ',', '.'); ?></h3>
                <h3>Total Retur Profit : <?php echo number_format($total_profit, 0, ',', '.'); ?></h3>

                <p>&nbsp;</p>
                <a href=javascript:history.go(-1)><< Kembali</a>
                <?php
            }
            break;

        case 'total1': { // ---------------------------------------------------------------------------------
                echo "
              <br/>
              <h2>Laporan Total Stok</h2>

		";

//	Ini salah, karena kalau ada 1 saja record barang yang sudah sold-out / isSold='Y'
// 	-- maka seluruh barang tersebut jadi tidak dihitung lagi stoknya
//	Biarkan penentu keberadaan stok adalah field jumBarang di table barang
//		$sql	= "SELECT SUM(b.jumBarang * d.hargaBeli) AS TotalStok
//			FROM barang AS b, (SELECT barcode, hargaBeli FROM detail_beli WHERE isSold='N' AND hargaBeli > 0 GROUP BY barcode) AS d
//			WHERE b.jumBarang > 0 AND b.barcode = d.barcode";

                /* 		// query ini mengambil hargaBeli yang paling pertama / lama
                  $sql	= "SELECT SUM(b.jumBarang * d.hargaBeli) AS TotalStok
                  FROM barang AS b, (SELECT barcode, hargaBeli FROM detail_beli WHERE hargaBeli > 0 GROUP BY barcode) AS d
                  WHERE b.jumBarang > 0 AND b.barcode = d.barcode";
                 */

                // query ini mengambil hargaBeli yang terbaru
                $sql = "SELECT SUM(b.jumBarang * d.hargaBeli) AS TotalStok
			FROM barang AS b,
			(SELECT barcode, hargaBeli FROM
				(SELECT barcode, hargaBeli, idTransaksiBeli
				 FROM detail_beli WHERE hargaBeli > 0 ORDER BY idTransaksiBeli DESC) AS d1
			GROUP BY barcode) AS d
			WHERE b.jumBarang > 0 AND b.barcode = d.barcode";

                $tampil = mysql_query($sql);
                $x = mysql_fetch_array($tampil);

                echo "Total Stok Saat Ini = Rp " . number_format($x[TotalStok], 0, ',', '.') . "

                	<p>&nbsp;</p>
	                <a href=javascript:history.go(-1)><< Kembali</a>

		";

                exit;
            }


        case 'toprank1': { // ---------------------------------------------------------------------------------
                $tanggal = date('Y-m-d');
                echo "
              <br/>
              <h2>Laporan Top Rank</h2>

			<form method=POST action='modul/mod_laporan.php?act=toprank2' onSubmit=\"popupform(this, 'top-rank')\">

		<table>
        	<tr>
			<td>Dari Tanggal </td>
			<td>: <input type=text name=dari value='$tanggal'>
			</td>
		</tr>

        	<tr>
			<td>Sampai Tanggal </td>
			<td>: <input type=text name=sampai value='$tanggal'>
			</td>
		</tr>

        	<tr>
			<td>Kategori </td>
			<td>: 	<select name='kategori'>
				<option value='0' selected>--pilih--</option>
				<option value='SEMUA'>SEMUA</option>";
                $hasil = mysql_query("SELECT idKategoriBarang, namaKategoriBarang FROM kategori_barang");
                while ($x = mysql_fetch_array($hasil)) {
                    echo "<option value='" . $x['idKategoriBarang'] . "'>" . $x['namaKategoriBarang'] . "</option>";
                };

                echo "		</select>
			</td>
		</tr>

        	<tr>
			<td>Rack </td>
			<td>: 	<select name='rak'>
				<option value='0' selected>--pilih--</option>
				<option value='SEMUA'>SEMUA</option>";
                $hasil = mysql_query("SELECT idRak, namaRak FROM rak");
                while ($x = mysql_fetch_array($hasil)) {
                    echo "<option value='" . $x['idRak'] . "'>" . $x['namaRak'] . "</option>";
                };

                echo "		</select>
			</td>
		</tr>

        	<tr>
			<td>Jumlah Item </td>
			<td>: <input type=text name=jumlah value='200'>
			</td>
		</tr>

        	<tr>
			<td>Sortir berdasarkan</td>
			<td>: 	<select name='sortir'>
				<option value='jumlah' selected>jumlah</option>
				<option value='omset' >		omset</option>
				<option value='profit'>		profit</option>
				</select>
			</td>
		</tr>


        		<tr><td colspan=2><input type=submit value='Buat Laporan'></td></tr>
		</table>

		</form>

		";
                exit;
            }

        case 'toprank2': { // ---------------------------------------------------------------------------------
                if ($_POST['kategori'] == 'SEMUA') {
                    $kategori = 'SEMUA';
                } else {
                    $hasil = mysql_query("SELECT namaKategoriBarang FROM kategori_barang WHERE idKategoriBarang=" . $_POST['kategori']);
                    $x = mysql_fetch_array($hasil);
                    $kategori = $x['namaKategoriBarang'];
                };

                if ($_POST['rak'] == 'SEMUA') {
                    $rak = 'SEMUA';
                } else {
                    $sql = "SELECT namaRak FROM rak WHERE idRak=" . $_POST['rak'];
                    $hasil = mysql_query($sql);
                    $x = mysql_fetch_array($hasil);
                    $rak = $x['namaRak'];
                };

                if ($_POST['kategori'] == 'SEMUA') {
                    $idKategoriBarang = '';
                } else {
                    $idKategoriBarang = 'AND b.idKategoriBarang = ' . $_POST['kategori'];
                };

                if ($_POST['rak'] == 'SEMUA') {
                    $idRak = '';
                } else {
                    $idRak = 'AND b.idRak = ' . $_POST['rak'];
                };

                $sortir = $_POST['sortir'];
                $sql = "SELECT lb.barcode, lb.namaBarang, COUNT(lb.barcode) AS jumlah, SUM(lb.hargaJual) AS omset,
			SUM(lb.hargaJual - lb.hargaBeli) AS profit, lb.jumBarang
		FROM 	(SELECT dj.barcode AS barcode, b.namaBarang AS namaBarang, b.jumBarang, dj.hargaJual, dj.hargaBeli, b.idKategoriBarang
      			FROM barang AS b,
             			(SELECT barcode, hargaJual, hargaBeli FROM detail_jual AS j,
                    			(SELECT idTransaksiJual AS nomorStruk FROM transaksijual
                    			WHERE tglTransaksiJual BETWEEN '" . $_POST['dari'] . " 00:00:01' AND '" . $_POST['sampai'] . " 23:59:59') AS t
             			WHERE j.nomorStruk = t.nomorStruk) AS dj
			WHERE dj.barcode = b.barcode  $idKategoriBarang ORDER BY dj.barcode) AS lb
		GROUP BY lb.barcode
		ORDER BY $sortir DESC
		LIMIT " . $_POST['jumlah'] . ";
		";

                if ($_POST['rak'] <> 0) {
                    $sql = "SELECT lb.barcode, lb.namaBarang, COUNT(lb.barcode) AS jumlah, SUM(lb.hargaJual) AS omset,
			SUM(lb.hargaJual - lb.hargaBeli) AS profit, lb.jumBarang
		FROM 	(SELECT dj.barcode AS barcode, b.namaBarang AS namaBarang, b.jumBarang, dj.hargaJual, dj.hargaBeli, b.idKategoriBarang
      			FROM barang AS b,
             			(SELECT barcode, hargaJual, hargaBeli FROM detail_jual AS j,
                    			(SELECT idTransaksiJual AS nomorStruk FROM transaksijual
                    			WHERE tglTransaksiJual BETWEEN '" . $_POST['dari'] . " 00:00:01' AND '" . $_POST['sampai'] . " 23:59:59') AS t
             			WHERE j.nomorStruk = t.nomorStruk) AS dj
			WHERE dj.barcode = b.barcode  $idRak ORDER BY dj.barcode) AS lb
		GROUP BY lb.barcode
		ORDER BY $sortir DESC
		LIMIT " . $_POST['jumlah'] . ";
		";
                };
                $hasil = mysql_query($sql) or die("Error : " . mysql_error());
                //echo $sql;

                echo "
		<br/>
		<h2>Laporan Top Rank</h2>
		Tanggal:" . $_POST['dari'] . " s/d " . $_POST['sampai'];

                if ($_POST['rak'] <> 0) {
                    echo " Rak: $rak";
                } else {
                    echo " Kategori: $kategori";
                };
                ?>
                <table class="tabel">
                    <tr>
                        <th>No</th>
                        <th>Barcode</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Omset</th>
                        <th>Profit</th>
                        <th>Avg / day</th>
                        <th>Total Stok</th>
                    </tr>
                    <?php
                    $start = strtotime($_POST['dari']);
                    $end = strtotime($_POST['sampai']);
                    $jmlhari = abs($end - $start) / 86400;

                    $no = 0;
                    while ($x = mysql_fetch_array($hasil)) {
                        //untuk mewarnai tabel menjadi selang-seling
                        $no++;
                        ?>
                        <tr <?php echo $no % 2 === 0 ? 'class="alt"' : ''; ?>>
                            <td class="center"><?php echo $no; ?></td>
                            <td><?php echo $x['barcode']; ?></td>
                            <td><?php echo $x['namaBarang']; ?></td>
                            <td class="right"><?php echo number_format($x['jumlah'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($x['omset'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($x['profit'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format(($x['jumlah'] / $jmlhari), 2, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($x['jumBarang'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php
                    };
                    ?>
                </table>
                <?php
                exit;
            }


        case 'aging1': { // ---------------------------------------------------------------------------------
                $tanggalAwal = '2000-01-01';                
                // Tanggal akhir > 180 hari dari hari ini
                $tanggalAkhir = $day_before = date( 'Y-m-d', strtotime( date('Y-m-d') . ' -181 day' ) );
                echo "
              <br/>
              <h2>Laporan Aging / Barang Mati</h2>

			<form method=POST action='modul/mod_laporan.php?act=aging2' onSubmit=\"popupform(this, 'aging')\">

		<table>
        	<tr>
			<td>Dari Tanggal </td>
			<td>: <input type=text name=dari value='$tanggalAwal'>
			</td>
		</tr>

        	<tr>
			<td>Sampai Tanggal </td>
			<td>: <input type=text name=sampai value='$tanggalAkhir'>
			</td>
		</tr>

        	<tr>
			<td>Kategori </td>
			<td>: 	<select name='kategori'>
				<option value='SEMUA' selected>SEMUA</option>";
                $hasil = mysql_query("SELECT idKategoriBarang, namaKategoriBarang FROM kategori_barang");
                while ($x = mysql_fetch_array($hasil)) {
                    echo "<option value='" . $x['idKategoriBarang'] . "'>" . $x['namaKategoriBarang'] . "</option>";
                };

                echo "		</select>
			</td>
		</tr>

        	<tr>
			<td>Jumlah Item </td>
			<td>: <input type=text name=jumlah value='200'>
			</td>
		</tr>

        	<tr>
			<td>Sortir berdasarkan</td>
			<td>: 	<select name='sortir1'>
				<option value='avgSales1'>	Average Daily Sales (a-z)</option>
				<option value='avgSales2'>	Average Daily Sales (z-a)</option>
				<option value='jmlStokIni1' >		Jumlah Sisa Stok (a-z)</option>
				<option value='jmlStokIni2' >		Jumlah Sisa Stok (z-a)</option>
				<option value='umurStok1' >		Umur Stok (a-z)</option>
				<option value='umurStok2' selected >		Umur Stok (z-a)</option>
				<option value='nilaiStok1'>		Nilai Stok (a-z)</option>
				<option value='nilaiStok2'>		Nilai Stok (z-a)</option>
				</select>
			</td>
		</tr>
        	<tr>
			<td></td>
			<td>: 	<select name='sortir2'>
				<option value='avgSales1'>	Average Daily Sales (a-z)</option>
				<option value='avgSales2'>	Average Daily Sales (z-a)</option>
				<option value='jmlStokIni1' >		Jumlah Sisa Stok (a-z)</option>
				<option value='jmlStokIni2' >		Jumlah Sisa Stok (z-a)</option>
				<option value='umurStok1' >		Umur Stok (a-z)</option>
				<option value='umurStok2' >		Umur Stok (z-a)</option>
				<option value='nilaiStok1'>		Nilai Stok (a-z)</option>
				<option value='nilaiStok2' selected>		Nilai Stok (z-a)</option>
				</select>
			</td>
		</tr>


        		<tr><td colspan=2><input type=submit value='Buat Laporan'></td></tr>
		</table>

		</form>

		";
                exit;
            }

        case 'aging2': { // ---------------------------------------------------------------------------------
                if ($_POST['kategori'] == 'SEMUA') {
                    $kategori = 'SEMUA';
                } else {
                    $hasil = mysql_query("SELECT namaKategoriBarang FROM kategori_barang WHERE idKategoriBarang=" . $_POST['kategori']);
                    $x = mysql_fetch_array($hasil);
                    $kategori = $x['namaKategoriBarang'];
                };

                if ($_POST['kategori'] == 'SEMUA') {
                    $idKategoriBarang = '';
                } else {
                    $idKategoriBarang = 'AND b.idKategoriBarang = ' . $_POST['kategori'];
                };

                // buat temporary table untuk simpan hasil
                $sql = "
		CREATE TABLE IF NOT EXISTS `tmp_lap_aging` (
		  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
		  `barcode` varchar(25) DEFAULT NULL,
		  `namaBarang` varchar(30) DEFAULT ' ',
		  `nilaiStok` bigint(20) DEFAULT '0',
		  `umurStok` int(10) DEFAULT '0',
		  `jmlStokIni` int(10) DEFAULT '0',
		  `jmlStokSemua` int(10) DEFAULT '0',
		  `avgSales` DECIMAL (6,6) DEFAULT '0',

		  PRIMARY KEY `uid` (`uid`),
		  KEY `avgSales` (`avgSales`)
		) ENGINE=MEMORY DEFAULT CHARSET=latin1;
		";
                $hasil = mysql_query($sql) or die("Error : " . mysql_error());

                /*
                  $sql = "SELECT lb.barcode, lb.namaBarang, SUM(lb.jumBarang) AS sisastok,
                  SUM(lb.hargaBeli * lb.jumBarang) AS nilaistok,
                  (TIMESTAMPDIFF(DAY, lb.tglTransaksiBeli, NOW())) AS umurstok,
                  lb.tglTransaksiBeli, lb.TotalJumlah

                  FROM (
                  SELECT dj.barcode AS barcode, b.namaBarang AS namaBarang, dj.jumBarang,
                  dj.jumBarangAsli, dj.hargaBeli, b.idKategoriBarang, dj.tglTransaksiBeli,
                  b.jumBarang AS TotalJumlah
                  FROM barang AS b, (
                  SELECT b.barcode, b.hargaBeli, b.jumBarang, b.jumBarangAsli, t.tglTransaksiBeli
                  FROM detail_beli AS b, (
                  SELECT idTransaksiBeli, tglTransaksiBeli
                  FROM transaksibeli
                  WHERE tglTransaksiBeli BETWEEN '".$_POST['dari']."' AND '".$_POST['sampai']."'
                  ) AS t
                  WHERE isSold = 'N' AND t.idTransaksiBeli = b.idTransaksiBeli AND b.jumBarang > 0
                  ) AS dj
                  WHERE dj.barcode = b.barcode ORDER BY dj.barcode
                  ) AS lb

                  GROUP BY lb.barcode
                  LIMIT ".$_POST['jumlah'].";
                  ";

                  $hasil = mysql_query($sql) or die("Error : ".mysql_error());

                  // masukkan ke temporary table
                  mysql_query("truncate table tmp_lap_aging"); // Memastikan isi tabel kosong sebelum diinsert
                  $sqltmp = "INSERT INTO tmp_lap_aging (barcode,namaBarang,nilaiStok,umurStok,jmlStokIni,jmlStokSemua,avgSales) VALUES ";
                  while ($x = mysql_fetch_array($hasil)) {
                  // hitung Average Sales / Day
                  $sql = "
                  SELECT SUM(jumBarang) AS total
                  FROM detail_jual AS dj,
                  (
                  SELECT idTransaksiJual
                  FROM transaksijual
                  WHERE tglTransaksiJual BETWEEN '".$_POST['dari']."' AND '".$_POST['sampai']."') AS tj
                  WHERE barcode='".$x['barcode']."' AND dj.nomorStruk = tj.idTransaksiJual";
                  $hasil3 = mysql_query($sql);
                  $y = mysql_fetch_array($hasil3);
                  $avgSales = ($y['total'] / $jmlhari);

                  // buat statement SQL
                  $sqltmp .= "('".$x['barcode']."','".$x['namaBarang']."','".$x['nilaistok']."','".$x['umurstok']."',
                  '".$x['sisastok']."','".$x['TotalJumlah']."','$avgSales'),";
                  };
                  // hapus koma di akhir string
                  $sqltmp = substr($sqltmp, 0, -1);
                  // simpan ke temporary table
                  $hasil = mysql_query($sqltmp) or die("Error : ".mysql_error());
                 */

                mysql_query("truncate table tmp_lap_aging"); // Memastikan isi tabel kosong sebelum diinsert

                $kondisi = 'WHERE (barang.nonAktif!=1 or barang.nonAktif is null) AND barang.jumBarang > 0';

                if ($_POST['kategori'] != 'SEMUA') {
                    $kondisi .= " AND barang.idKategoriBarang = {$_POST['kategori']}";
                }

                $sqltmp = "INSERT INTO tmp_lap_aging (barcode,namaBarang,nilaiStok,umurStok,jmlStokIni,jmlStokSemua,avgSales) 
                SELECT 
                    barang.barcode,
                    barang.namaBarang,
                    dbAgregat.nilaistok,
                    TIMESTAMPDIFF(DAY,
                        dbAgregat.maxTglTransaksiBeli,
                        NOW()) umurstok,
                    #dbAgregat.maxTglTransaksiBeli tglTransaksiBeli,
                    dbAgregat.sisastok,
                    barang.jumBarang totalJumlah,
                    penjualan.avg_daily_sales
                FROM
                    barang
                        JOIN
                    (SELECT 
                        barcode,
                            SUM(jumBarang) sisastok,
                            SUM(jumBarang * hargaBeli) nilaistok,
                            MAX(tb.tglTransaksiBeli) maxTglTransaksiBeli
                    FROM
                        detail_beli db
                    JOIN transaksibeli tb ON db.idTransaksiBeli = tb.idTransaksiBeli
                        AND tb.tglTransaksiBeli BETWEEN '{$_POST['dari']}' AND '{$_POST['sampai']}'
                    WHERE
                        db.isSold = 'N'
                    GROUP BY db.barcode) AS dbAgregat ON barang.barcode = dbAgregat.barcode
                        LEFT JOIN
                    (SELECT 
                        barcode,
                            SUM(jumBarang) / TIMESTAMPDIFF(DAY, '{$_POST['dari']}', '{$_POST['sampai']}') avg_daily_sales
                    FROM
                        detail_jual dj
                    JOIN transaksijual tj ON dj.nomorStruk = tj.idTransaksiJual
                        AND tj.tglTransaksiJual BETWEEN '{$_POST['dari']}' AND '{$_POST['sampai']}'
                    GROUP BY barcode) penjualan ON barang.barcode = penjualan.barcode
                {$kondisi}
                ORDER BY barang.namaBarang";



                $hasil = mysql_query($sqltmp) or die("Error : " . mysql_error());


                echo "
		<br/>
		<h2>Laporan Aging</h2>
		Tanggal:" . $_POST['dari'] . " s/d " . $_POST['sampai'] . " Kategori: $kategori

		<table>
		<tr>
			<td class=td><b><center>No.</center></b></td>
			<td class=td><b><center>Barcode</center></b></td>
			<td class=td><b><center>Nama Barang</center></b></td>
			<td class=td><b><center>Nilai Stok</center></b></td>
			<td class=td><b><center>Umur Stok</center></b></td>
			<td class=td><b><center>Sisa Stok<br />(periode ini)</center></b></td>
			<td class=td><b><center>Sisa Stok<br />(semua / saat ini)</center></b></td>
			<td class=td><b><center>Avg<br />Daily<br />Sales</center></b></td>
		</tr>
		";

                // ambil data dari temporary table
                /*
                  if ($sortir == 'avgSales') {
                  $sortir = 'avgSales,nilaiStok';
                  };
                 */
                $sortir1 = $_POST['sortir1'];
                $urut1 = '';
                switch ($sortir1) {
                    case 'avgSales1':
                        $urut1 = 'avgSales';
                        break;
                    case 'avgSales2':
                        $urut1 = 'avgSales desc';
                        break;
                    case 'jmlStokIni1':
                        $urut1 = 'jmlStokIni';
                        break;
                    case 'jmlStokIni2':
                        $urut1 = 'jmlStokIni desc';
                        break;
                    case 'umurStok1':
                        $urut1 = 'umurStok';
                        break;
                    case 'umurStok2':
                        $urut1 = 'umurStok desc';
                        break;
                    case 'nilaiStok1':
                        $urut1 = 'nilaiStok';
                        break;
                    case 'nilaiStok2':
                        $urut1 = 'nilaiStok desc';
                        break;
                }
                $sortir2 = $_POST['sortir2'];
                $urut2 = '';
                switch ($sortir2) {
                    case 'avgSales1':
                        $urut2 = 'avgSales';
                        break;
                    case 'avgSales2':
                        $urut2 = 'avgSales desc';
                        break;
                    case 'jmlStokIni1':
                        $urut2 = 'jmlStokIni';
                        break;
                    case 'jmlStokIni2':
                        $urut2 = 'jmlStokIni desc';
                        break;
                    case 'umurStok1':
                        $urut2 = 'umurStok';
                        break;
                    case 'umurStok2':
                        $urut2 = 'umurStok desc';
                        break;
                    case 'nilaiStok1':
                        $urut2 = 'nilaiStok';
                        break;
                    case 'nilaiStok2':
                        $urut2 = 'nilaiStok desc';
                        break;
                }
                $sql = "SELECT * FROM tmp_lap_aging ORDER BY {$urut1}, {$urut2} LIMIT {$_POST['jumlah']}";
                $hasil = mysql_query($sql) or die("Error : " . mysql_error());


                $start = strtotime($_POST['dari']);
                $end = strtotime(time());
                $jmlhari = abs($end - $start) / 86400;

                $no = 0;
                $nilai = 0;
                while ($x = mysql_fetch_array($hasil)) {


                    //untuk mewarnai tabel menjadi selang-seling
                    $no++;
                    if (($no % 2) == 0) {
                        $warna = "#EAF0F7";
                    } else {
                        $warna = "#FFFFFF";
                    }

                    echo "<tr bgcolor=$warna>";
                    echo "
			<td class=td align=center> $no </td>
			<td class=td> " . $x['barcode'] . " </td>
			<td class=td> " . $x['namaBarang'] . " </td>
			<td class=td align=right> " . number_format($x['nilaiStok'], 0, ',', '.') . " </td>
			<td class=td align=right> " . number_format($x['umurStok'], 0, ',', '.') . " </td>
			<td class=td align=right> <center>" . number_format($x['jmlStokIni'], 0, ',', '.') . " </td>
			<td class=td align=right> <center>" . number_format($x['jmlStokSemua'], 0, ',', '.') . " </td>
			<td class=td align=right> " . number_format($x['avgSales'], 6, ',', '.') . " </td>
			</tr>";
                    $nilai = $nilai + ($x['nilaiStok'] / $x['jmlStokIni'] * $x['jmlStokSemua']);
                };
                echo "</table> Nilai Stok : Rp " . number_format($nilai, 0, ',', '.');

                // bersihkan temporary table
                $hasil = mysql_query("DELETE FROM tmp_lap_aging");

                exit;
            }

        case "po": // =======================================================================================================================
            echo "<h2>Purchase Order</h2>
            <form method=POST action='?module=laporan&act=po&action=pesanbarang'>
                Supplier :
                <select name=supplierId>";
            $supplier = getSupplier();
            while ($dataSupplier = mysql_fetch_array($supplier)) {
                echo "<option value=$dataSupplier[idSupplier]>$dataSupplier[namaSupplier]::$dataSupplier[alamatSupplier]</option>";
            }
            echo "</select>
		<br />
		Tampilkan hanya barang dengan jumlah lebih kecil dari : <input type=text name=jumlahmin value=0 size=3>
		<br />
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type=submit value=Pilih>
            </form>";

            if ($_GET[action] == 'pesanbarang') {

                $supplier = getDetailSupplier($_POST[supplierId]);
                $detailSupplier = mysql_fetch_array($supplier);
                echo "<h2>Pesan Barang di Supplier $detailSupplier[namaSupplier]</h2>
            <br/>Alamat Supplier : $detailSupplier[alamatSupplier]<br/><br/>
            <form method=POST action='modul/js_cetak_PO.php'   onSubmit=\"popupform(this, 'Purchase_Order')\">
            <table width=500>
                <tr><th>#</th><th>No</th><th>Barcode</th><th>Nama Barang</th><th>Stok<br />Saat Ini</th><th>Harga<br />Beli</th></tr>";
                $no = 0;
                $queryBarang = getDaftarBarangSupplier($_POST[supplierId], $_POST[jumlahmin]);
                while ($barangSupplier = mysql_fetch_array($queryBarang)) {
                    if (($no % 2) == 0) {
                        $warna = "#EAF0F7";
                    } else {
                        $warna = "#FFFFFF";
                    }
                    echo "<tr bgcolor=$warna>"; //end warna
                    echo "<td class=td align=center><input type=checkbox name=cek[] value=$barangSupplier[barcode] id=id$no checked=true></td>";
                    $no++;
                    echo "<td class=td>$no</td>
                        <td class=td>$barangSupplier[barcode]</td>
                        <td class=td>$barangSupplier[namaBarang]</td>
                        <td class=td align=right><center>$barangSupplier[jumBarang]</center></td>
                        <td class=td align=right>$barangSupplier[hargaBeli]</td>
                        </tr>";
                }

                echo "<input type=hidden name=idSupplier value=$_POST[supplierId]>";
                echo "<tr><td colspan=5 align=center class=td>
            <input type=radio name=pilih onClick='for (i=0;i<$no;i++){document.getElementById(\"id\"+i).checked=true;}'>Check All
            <input type=radio name=pilih onClick='for (i=0;i<$no;i++){document.getElementById(\"id\"+i).checked=false;}'>Uncheck All
            </td></tr>
            <tr>
		<td colspan=3 class=td> 		<input type=checkbox name=cetakcsv> Cetak Excel / CSV</td>
		<td colspan=2 align=right class=td>	<input type=submit value=Cetak></form></td></tr>";
                echo "</table>";
            }
            exit;

        case 'jumlahpoin':
            $bulanIndonesia = array(
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            );
            $sql = "SELECT id, nama, awal, akhir FROM periode_poin ORDER BY nama";
            $query = mysql_query($sql);
            $periode = array();
            while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
                $periode[] = $row;
            }
            ?>
            <h2>Laporan Jumlah Poin Member</h2>
            <form method="POST" target="_blank" action="./aksi.php?module=laporan&act=jumlahpoin">
                <table>
                    <tbody>
                        <tr>
                            <td>Tahun:</td>
                            <td><input type="text"  name="laporan[tahun]" placeholder="yyyy" size="4" value="<?php echo date('Y'); ?>" autofocus="autofocus"/></td>
                            <td>Periode:</td>
                            <td>
                                <select name="laporan[periode]">
                                    <?php
                                    foreach ($periode as $period) {
                                        ?>
                                        <option value="<?php echo $period['id']; ?>"><?php echo $period['nama']; ?> (<?php echo $bulanIndonesia[$period['awal']]; ?> - <?php echo $bulanIndonesia[$period['akhir']]; ?>)</option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>Sort by:</td>
                            <td>
                                <select name="laporan[sort]">
                                    <option value="1">Jumlah Poin (dari tertinggi)</option>
                                    <option value="2">Jumlah Poin (dari terendah)</option>
                                </select>
                            </td>
                            <td>Jumlah Poin dari</td>
                            <td>
                                <input type="text" name="laporan[jumlahDari]" value="0" size="1"/>
                            </td>
                            <td>sampai</td>
                            <td>
                                <input type="text" name="laporan[jumlahSampai]" value="9999" size="1"/>
                            </td>
                            <td colspan="7"></td>
                            <td><input type="submit" value="Submit" /></td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <?php
            break;

        case 'transferbarang':
            $sql = "SELECT idCustomer, namaCustomer FROM customer ORDER BY namaCustomer";
            $queryCustomer = mysql_query($sql);
            ?>
            <h2>Laporan Transfer Barang</h2>
            <form method=POST action='?module=laporan&act=transferbarang2'>
                <table>
                    <tr>
                        <td><b>C</b>ustomer: </td>
                        <td>
                            <select name="customer" accesskey="c" autofocus="autofocus">
                                <option value="0">Semua</option>
                                <?php
                                while ($customer = mysql_fetch_array($queryCustomer, MYSQL_ASSOC)) {
                                    ?>
                                    <option value="<?php echo $customer['idCustomer']; ?>"><?php echo $customer['namaCustomer']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Periode</td>
                        <td>
                            <input type="text" id="tanggal_dari" name="tanggal[dari]" value="" size="4" placeholder="Optional">
                            -
                            <input type="text" id="tanggal_sampai" name="tanggal[sampai]" value="" size="4">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right"><input type=submit name="transfer" value='Submit'></td>
                    </tr>
                </table>
            </form>
            <script>
                $(function () {
                    $('#tanggal_dari').appendDtpicker({
                        "closeOnSelected": true,
                        'locale': 'id',
                        'dateFormat': 'DD-MM-YYYY',
                        "dateOnly": true
                    });
                });
                $(function () {
                    $('#tanggal_sampai').appendDtpicker({
                        "closeOnSelected": true,
                        'locale': 'id',
                        'dateFormat': 'DD-MM-YYYY',
                        "dateOnly": true
                    });
                });
            </script>
            <?php
            break;

        case 'transferbarang2':
            if (isset($_POST['transfer'])) {
                $customerId = $_POST['customer'];
                $namaCustomer = 'SEMUA';

                if ($customerId > 0) {
                    $queryCustomer = mysql_query("SELECT namaCustomer FROM customer WHERE idCustomer = {$customerId}");
                    $cust = mysql_fetch_array($queryCustomer, MYSQL_ASSOC);
                    $namaCustomer = $cust['namaCustomer'];
                }
                ?>
                <h2>Laporan Transfer Barang</h2>
                <h3>Customer: <?php echo $namaCustomer; ?>, Periode <?php echo $_POST['tanggal']['dari'] . ' s.d ' . $_POST['tanggal']['sampai']; ?></h3>
                <?php
                $periode = $_POST['tanggal'];
                $dariTanggal = date_format(date_create_from_format('d-m-Y', $periode['dari']), 'Y-m-d');
                $sampaiTanggal = date_format(date_create_from_format('d-m-Y', $periode['sampai']), 'Y-m-d');

                $sql = "SELECT trx.idTransaksi, trx.tglTransaksi, customer.namaCustomer, trx.nominal
									FROM transaksitransferbarang trx
									JOIN customer ON trx.idCustomer = customer.idCustomer ";
                $sql .= $customerId > 0 ? "AND customer.idCustomer = {$customerId} " : '';
                $sql .= "WHERE date(trx.tglTransaksi) between '{$dariTanggal}' AND '{$sampaiTanggal}'
									ORDER BY trx.tglTransaksi";
                $queryHeader = mysql_query($sql);
                ?>
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Tgl Transaksi</th>
                            <th>Customer</th>
                            <th>Nominal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $alt = false;
                        while ($trx = mysql_fetch_array($queryHeader, MYSQL_ASSOC)) {
                            ?>
                            <tr<?php echo $alt ? ' class="alt"' : ''; ?>>
                                <td><?php echo $trx['tglTransaksi']; ?></td>
                                <td><?php echo $trx['namaCustomer']; ?></td>
                                <td class="right"><?php echo number_format($trx['nominal'], 0, ',', '.'); ?></td>
                                <td><a href="media.php?module=laporan&act=transferbarang3&id=<?php echo $trx['idTransaksi']; ?>">Lihat</a></td>
                            </tr>
                            <?php
                            $alt = !$alt;
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
            break;

        case 'transferbarang3':
            $idTransaksi = $_GET['id'];
            $sql = "SELECT trx.idTransaksi, trx.tglTransaksi, customer.namaCustomer, trx.nominal
						FROM transaksitransferbarang trx
						JOIN customer ON trx.idCustomer = customer.idCustomer
						WHERE trx.idTransaksi = {$idTransaksi}";
            $queryHeader = mysql_query($sql);
            $header = mysql_fetch_array($queryHeader, MYSQL_ASSOC);
            ?>
            <h2>Transfer Barang <small><?php echo $header['tglTransaksi']; ?></small></h2>
            <h3>Customer: <?php echo $header['namaCustomer']; ?>, Total: <?php echo number_format($header['nominal'], 0, ',', '.'); ?></h3>
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Barcode</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT detail.barcode, barang.namaBarang, detail.jumBarang, detail.hargaJual, (detail.jumBarang * detail.hargaJual) subTotal
								FROM detail_transfer_barang detail
								JOIN barang on detail.barcode = barang.barcode
								where detail.nomorStruk = {$idTransaksi}";
                    $queryDetail = mysql_query($sql);
                    $alt = false;
                    while ($detail = mysql_fetch_array($queryDetail, MYSQL_ASSOC)) {
                        ?>
                        <tr<?php echo $alt ? ' class="alt"' : ''; ?>>
                            <td><?php echo $detail['barcode']; ?></td>
                            <td><?php echo $detail['namaBarang']; ?></td>
                            <td class="right"><?php echo $detail['jumBarang']; ?></td>
                            <td class="right"><?php echo number_format($detail['hargaJual'], 0, ',', '.'); ?></td>
                            <td class="right"><?php echo number_format($detail['subTotal'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php
                        $alt = !$alt;
                    }
                    ?>
                </tbody>
            </table>
            <?php
            break;
    }

	/* CHANGELOG -----------------------------------------------------------

	  1.5.5 / 2013-01-22 : Harry Sufehmi	: Penambahan Laporan : Top Rank
	  1.5.0 / 2013-01-04 : Harry Sufehmi	: bugfix : perbaikan rumus perhitungan Total Stok
	  1.2.5 / 2012-05-14 : Harry Sufehmi	: fitur : audit trail untuk "hapusjual"
	  1.2.5 / 2012-04-17 : Harry Sufehmi	: bugfix : perbaikan rumus perhitungan Total Stok
	  1.2.5 / 2012-03-04 : Harry Sufehmi	: bugfix : perhitungan Total Stok / total nilai stok kini sudah dari hargaBeli
	  (tadinya dari hargaJual)
	  1.2.5 / 2012-02-14 : Harry Sufehmi	: Hapus transaksi jual : kini otomatis mengembalikan jumlah stok barang ke
	  table barang & detail_beli, sejumlah banyak barang yang dibatalkan transaksinya
	  1.2.5 / 2012-02-01 : Harry Sufehmi	: Laporan Total Stok
	  1.0.1 / 2010-06-03 : Harry Sufehmi	: various enhancements, bugfixes
	  0.9.2 / 2010-03-08 : Harry Sufehmi	: initial release

	  ------------------------------------------------------------------------ */
	