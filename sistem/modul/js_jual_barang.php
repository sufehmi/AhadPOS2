<?php
/* js_jual_barang.php ----------------------------------------
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
include "function.php";


session_start();
if (empty($_SESSION[namauser]) AND empty($_SESSION[passuser])) {
   echo "<link href='../../css/style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
   echo "<a href=index.php><b>LOGIN</b></a></center>";
} else {

   if (isset($_POST['idCustomer'])) {
      findCustomer($_POST['idCustomer']);
   }

   $result = mysql_query("select `value` from config where `option`='ukm_mode'") or die(mysql_error());
   $hasil = mysql_fetch_array($result);
   $ukmMode = $hasil['value'];

   $transferahad = false;
   if (($_POST['transferahad'] == 1) || ($_GET['transferahad'] == 1)) {
      $transferahad = true;
   }

   $batalGagal = false;
   if (isset($_GET['bg']) && $_GET['bg'] && !$transferahad) {
      $batalGagal = true;
   }

//HS javascript untuk menampilkan popup
   ?>
   <!DOCTYPE html>
   <html>
      <head>


         <script>
            function popupform(myform, windowname)
            {
               if (!window.focus)
                  return true;
               popWindo = window.open('', windowname, 'height=400,width=700,scrollbars=yes');
               myform.target = windowname;
               popWindo.focus();
               return true;
            }

            function number_format(a, b, c, d) {
               // credit: http://www.krisnanda.web.id/2009/06/09/javascript-number-format/

               a = Math.round(a * Math.pow(10, b)) / Math.pow(10, b);

               e = a + '';
               f = e.split('.');
               if (!f[0]) {
                  f[0] = '0';
               }
               if (!f[1]) {
                  f[1] = '';
               }

               if (f[1].length < b) {
                  g = f[1];
                  for (i = f[1].length + 1; i <= b; i++) {
                     g += '0';
                  }
                  f[1] = g;
               }

               if (d != '' && f[0].length > 3) {
                  h = f[0];
                  f[0] = '';
                  for (j = 3; j < h.length; j += 3) {
                     i = h.slice(h.length - j, h.length - j + 3);
                     f[0] = d + i + f[0] + '';
                  }
                  j = h.substr(0, (h.length % 3 == 0) ? 3 : (h.length % 3));
                  f[0] = j + f[0];
               }

               c = (b <= 0) ? '' : c;
               return f[0] + c + f[1];
            }


            function RecalcTotal(tot_pembelian) {
               var totalBeli = 0;
               var Kembali = 0;
               var uangDibayar = parseInt(document.getElementById("uangDibayar").value);
               var surcharge = parseInt(document.getElementById("surcharge").value);

               totalSurcharge = ((tot_pembelian / 100) * surcharge);
               totalBeli = tot_pembelian + totalSurcharge;
               Kembali = uangDibayar - totalBeli;

               document.getElementById("uangKembali").value = Kembali;
               document.getElementById("kembalian").innerHTML = '<span>' + number_format(Kembali, 0, ',', '.') + '</span>';

               document.getElementById("TotalSurcharge").value = number_format(totalSurcharge, 0, ',', '.');
               document.getElementById("tot_pembelian").innerHTML = '<span>' + number_format(totalBeli, 0, ',', '.') + '</span>';
            }

         </script>

         <link rel="stylesheet" type="text/css" href="../../css/animate.min.css" />
         <link rel="stylesheet" type="text/css" href="../../css/jquery-ui-ac.min.css" />
         <link rel="stylesheet" type="text/css" href="../../css/style.css" />
         <link rel="stylesheet" type="text/css" href="../../css/jquery-editable.css" />

         <script src="../../js/jquery-1.9.1.min.js"></script>
         <script src="../../js/jquery.poshytip.js"></script>
         <script src="../../js/jquery-editable-poshytip.min.js"></script>
         <?php // JqueryUI untuk autocomplete cari barang pada cek harga ?>
         <script type="text/javascript" src="../../js/jquery-ui.min-ac.js"></script>

      </head>
      <body class="kasir" id="dokumen">
         <div id="content" >
            <?php
            //fixme: hargaBeli TIDAK tersimpan di detail_jual !!!
            switch ($_GET[act]) { // ============================================================================================================
               case "caricustomer": // ========================================================================================================
                  $hapusGagal = false;
                  if ($_GET[doit] == 'hapus') {
                     if ($_SESSION['hakAdmin'] || $transferahad) {
                        $hasil = mysql_query("select barcode from tmp_detail_jual where uid = {$_GET['uid']}") or die('Gagal hapus (ambil data), error: '.mysql_error());
                        $r = mysql_fetch_array($hasil);

                        $sql = "DELETE FROM tmp_detail_jual WHERE barcode = '{$r['barcode']}' and username='{$_SESSION['uname']}'";
                        if (isset($_SESSION['idCustomer'])) {
                           $sql.= " and idCustomer={$_SESSION['idCustomer']}";
                        }
                        // echo $sql;
                        $hasil = mysql_query($sql) or die('Gagal hapus data, error: '.mysql_error());
                     } else {
                        $hapusGagal = true;
                     }
                  }
                  ?>
                  <div style="float:right" id="tot_pembelian">
                     <span><?php echo number_format($_SESSION['tot_pembelian'], 0, ',', '.'); ?></span>
                  </div>
                  <?php
                  if ($transferahad) {
                     ?>
                     <div class="top">
                        Transfer Barang antar Ahad : <?php echo $_SESSION['namaCustomer']; ?> <br />
                        <?php echo date('d-m-Y'); ?>
                     </div>
                     <?php
                  } else {
                     $sql = "SELECT nomor_kartu, member FROM customer WHERE idCustomer = '{$_SESSION['idCustomer']}'";
                     $hasil = mysql_query($sql);
                     $customer = mysql_fetch_array($hasil, MYSQL_ASSOC) or die('Gagal ambil data customer');
                     $isMember = false;
                     if ($customer && $customer['member'] == 1) {
                        $isMember = true;
                     }
                     //$_SESSION['isMember'] = $isMember;
                     ?>
                     <div class="top">
                        <?php echo $isMember ? 'Member' : 'Customer'; ?><?php echo empty($customer['nomor_kartu']) ? '' : " #{$customer['nomor_kartu']}"; ?> : <?php echo $_SESSION['namaCustomer']; ?>
                        <?php
                        if ($_SESSION['customerDiskonP'] > 0) {
                           echo " ({$_SESSION['customerDiskonP']}%)";
                        } elseif ($_SESSION['customerDiskonR'] > 0) {
                           echo ' ('.number_format($_SESSION['customerDiskonR'], 0, ',', '.').')';
                        }
                        ?>
                        <?php
                        if ($isMember) {
                           ?>
                           <br />Jumlah Poin Periode Berjalan = <?php echo getJumlahPoinPeriodeBerjalan($_SESSION['idCustomer']); ?>
                           <?php
                        }
                        ?>
                     </div>
                     <!--<h3>Barang yang dijual</h3>-->
                     <?php
                  };
                  ?>
                  <form id="entry-barang" method=POST action='js_jual_barang.php?act=caricustomer&action=tambah'>
                     <div class="input-group">
                        <label for="barcode"><span class="u">B</span>arcode</label>
                        <input type="text" name="barcode" accesskey="b" id="barcode" autofocus="autofocus" autocomplete="off">

                     </div>
                     <?php
                     // ----- TERLALU LAMBAT ! ----- jangan gunakan dropbox terlampir untuk memilih barcode
                     // ambil daftar barang
                     //$sql="SELECT namaBarang,barcode,hargaJual
                     //	FROM barang FORCE INDEX (barcode) ORDER BY barcode ASC";
                     //$namaBarang=mysql_query($sql);
                     //while($brg = mysql_fetch_array($namaBarang)){
                     //	echo "<option value='$brg[barcode]'>$brg[barcode] - $brg[namaBarang] - Rp ".number_format($brg[hargaJual],0,',','.')."</option>\n";
                     //}
                     //var_dump($_POST);
                     //var_dump($_GET);
                     if ($transferahad) {
                        ?>
                        <input type=hidden name='transferahad' value='1'>
                        <?php
                     }
                     ?>
                     <?php
                     /*  ukmMode: menampilkan hargaBarang */
                     if ($ukmMode) {
                        ?>
                        <div class="input-group">
                           <label for="hargaBarang"><span class="u">H</span>arga</label>
                           <input type="text" id="hargaBarang" name='hargaBarang' value='1' size=5 accesskey="h" autocomplete="off">
                        </div>
                        <?php
                     }
                     ?>

                     <div class="input-group">
                        <label for="jumBarang"><span class="u">Q</span>ty</label>
                        <input type="text" id="jumBarang" name='jumBarang' value='1' size=5 accesskey="q" autocomplete="off">
                     </div>
                 <!--<input type="submit" name="btnTambah" value="Tambah" accesskey="t">-->
                     <button type="submit"><span class="u">T</span>ambah</button>
                  </form>

                  <form method="POST" action="js_cari_barang.php?caller=js_jual_barang" onSubmit="popupform(this, 'cari1')">
                     <div class="input-group">
                        <label for="namaBarang"><span class="u">C</span>ari Barang</label>
                        <input type="text" id="namaBarang" name='namabarang' accesskey='c'>
                        <input type="hidden" id="jumBarang-cariBarang" name="jumBarang" value="1"/>
                     </div>
                     <?php
                     if ($transferahad) {
                        ?>
                        <input type=hidden name='transferahad' value='1'>
                        <?php
                     };
                     ?>
                     <!--<button type="submit" name="btnCari" id="btnCari">GO</button>-->
                     <!--<input type=submit name='btnCari' id='btnCari' value='Cari'>-->
                  </form>


                  <script>
                     var dropBox = document.getElementById("barcode");
                     if (dropBox != null)
                        dropBox.focus();
                  </script>

                  <?php
                  if ($_GET[action] == 'tambah') {

                     if ($_GET[barcode]) {
                        $_POST[barcode] = $_GET[barcode];
                     };
                     /*
                      * ukmMode: akan ada hargaBarang
                      */
                     $hargaBarang = isset($_POST['hargaBarang']) ? $_POST['hargaBarang'] : NULL;
                     /*
                       $trueJual = cekBarangTempJual($_SESSION[idCustomer], $_POST[barcode]);
                       //            echo "$trueJual";
                       if ($trueJual != 0) {

                       tambahBarangJualAda($_SESSION[idCustomer], $_POST[barcode], $_POST[jumBarang]);
                       } else {

                       tambahBarangJual($_POST[barcode], $_POST[jumBarang]);
                       }
                      *
                      */
                     $tambahBarang = 1;
                     if (isset($_POST['jumBarang']) || isset($_GET['jumBarang'])) {
                        $tambahBarang = isset($_POST['jumBarang']) ? $_POST['jumBarang'] : $_GET['jumBarang'];
                     }
                     $trueJual = cekBarangTempJual($_SESSION[idCustomer], $_POST[barcode]);
                     // Jika barang sudah ada (hanya tambah kuantiti) maka tambahkan kuantitinya;
                     if ($trueJual) {
                        $jumBarang = $trueJual['jumBarang'];
                        mysql_query("delete from tmp_detail_jual where idCustomer='{$_SESSION['idCustomer']}' "
                                        ."and barcode = '{$_POST['barcode']}' "
                                        ."and username='{$_SESSION['uname']}'") or die('Gagal clean '.mysql_error());
                        $jumBarang += $tambahBarang;
                     } else {
                        $jumBarang = $tambahBarang;
                     }
                     tambahBarangJual($_POST['barcode'], $jumBarang, $hargaBarang);
                  }
                  $sql = "SELECT *
                                FROM tmp_detail_jual tdj, barang b
                                WHERE tdj.barcode = b.barcode AND tdj.idCustomer = '$_SESSION[idCustomer]' AND tdj.username = '$_SESSION[uname]'";
                  //echo $sql;
                  $query = mysql_query($sql);
                  $r = mysql_fetch_row($query);
                  ?>
                  <span style="color:red">
                     <?php
                     echo $hapusGagal ? "Gagal hapus item, aktifkan Admin Mode terlebih dahulu!" : '';
                     echo $batalGagal ? "Gagal batal nota, aktifkan Admin Mode terlebih dahulu!" : '';
                     ?>
                  </span>
                  <hr />
                  <?php
                  if ($r) {
                     //echo "Ada $r[0] data";
                     ?>
                     <table class="tabel daftar-pembelian">
                        <tr>
                           <th>No</th>
                           <th>Barcode</th>
                           <th>Nama Barang</th>
                           <th>Jumlah</th>
                           <th>Harga</th>
                           <th>Diskon</th>
                           <th>Net</th>
                           <th>Total</th>
                           <th>Hapus</th>
                        </tr>
                        <?php
                        $no = 1;
                        $tot_pembelian = 0;

                        $query2 = mysql_query("SELECT tdj.uid, tdj.barcode, b.namaBarang, tdj.jumBarang, b.hargaJual hargaNormal, tdj.hargaJual, tdj.tglTransaksi, tdj.diskon_persen, tdj.diskon_rupiah
                                        FROM tmp_detail_jual tdj, barang b
										WHERE tdj.barcode = b.barcode AND tdj.idCustomer = '{$_SESSION['idCustomer']}'
										AND tdj.username = '{$_SESSION['uname']}' ORDER BY tdj.uid DESC");
                        $banyakItem = mysql_num_rows($query2);
                        while ($data = mysql_fetch_array($query2)) {

                           // jika ini barang yang akan di transfer,
                           // maka berikan hargaBeli (modal) sebagai hargaJual
                           if (($_POST['transferahad'] == 1) || ($_GET['transferahad'] == 1)) {
                              $sql = "SELECT hargaBeli FROM detail_beli
										WHERE isSold='N' AND barcode='$data[barcode]' ORDER BY idDetailBeli ASC";
                              $hasil = mysql_query($sql);
                              $x = mysql_fetch_array($hasil);

                              // jika tidak ada / semua stok barang ini sudah terjual = catatan stok ngaco
                              // maka ambil hargaBeli yang terakhir saja
                              if (mysql_num_rows($hasil) < 1) {
                                 $sql = "SELECT hargaBeli FROM detail_beli
											WHERE barcode='$data[barcode]' ORDER BY idDetailBeli ASC";
                                 $hasil = mysql_query($sql);
                                 $x = mysql_fetch_array($hasil);
                              };

                              $data['hargaJual'] = $x['hargaBeli'];
                           };

                           $total = $data['hargaJual'] * $data['jumBarang'];
                           $totalDiskon = 0;
                           ?>

                           <tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>">
                              <td class="right"><?php echo $banyakItem - $no + 1; ?></td>
                              <td><?php echo $data[barcode]; ?></td>
                              <td><?php echo $data[namaBarang]; ?></td>
                              <td class="right"><?php echo $data['jumBarang']; ?></td>
                              <td class="right"><?php echo $data['hargaNormal']; ?></td>
                              <td class="right"><?php
                                 if ($data['diskon_persen'] > 0) {
                                    echo $data['diskon_persen'].'%';
                                 } elseif ($data['diskon_rupiah'] > 0) {
                                    echo number_format($data['diskon_rupiah'], 0, ',', '.');
                                    $totalDiskon += $data['diskon_rupiah'] * $data['jumBarang'];
                                 }
                                 ?>
                              </td>
                              <td class="right">
                                 <?php
                                 // Jika punya hak admin, maka muncul link untuk manually update harga jual
                                 // Jika tidak, maka hanya muncul text statis harga jual
                                 if ($_SESSION['hakAdmin']) {
                                    ?>
                                    <a href="#" class="harga-jual pilih" data-type="text" data-pk="<?php echo $data['uid']; ?>" data-url="../aksi.php?module=diskon&act=updatehj" ><?php echo $data['hargaJual']; ?></a>
                                    <?php
                                 } else {
                                    echo $data['hargaJual'];
                                 }
                                 ?>
                              </td>
                              <td class="right"><?php echo number_format($total, 0, ',', '.'); ?></td>
                              <?php /*
                                <td class="right">
                                <?php
                                if ($data['diskon_persen'] > 0) {
                                $totalPlusDiskon = $total - ($total * $data['diskon_persen'] / 100);
                                echo number_format($totalPlusDiskon, 0, ',', '.');
                                } elseif ($data['diskon_rupiah'] > 0) {
                                $totalPlusDiskon = $total - $data['diskon_rupiah'];
                                echo number_format($totalPlusDiskon, 0, ',', '.');
                                } else {
                                $totalPlusDiskon = $total;
                                echo number_format($totalPlusDiskon, 0, ',', '.');
                                }
                                ?>
                                </td>
                               *
                               */ ?>
                              <td class="center"> <a class="pilih" href='js_jual_barang.php?act=caricustomer&doit=hapus&uid=<?php echo $data['uid']; ?><?php echo $transferahad ? '&transferahad=1' : ''; ?>'><i class="fa fa-times"></i></a></td>
                           </tr>
                           <?php
                           $tot_pembelian += $total;
                           $no++;
                        }
                        // Cek Diskon per Customer
                        // Jika ada masukkan ke variabel $totalDiskon
                        // $diskonCustomer = 0;
                        // $customerDiskonData = cekCustomerDiskon($_SESSION['idCustomer']);
                        //echo $customerDiskonData['diskon_persen'];
                        // PENTING!! Jika ada diskon persen, maka diskon harga (Rp) diabaikan
//								if ($customerDiskonData['diskon_persen'] > 0) {
//									$diskonCustomer = $tot_pembelian * $customerDiskonData['diskon_persen'] / 100;
//								} elseif ($customerDiskonData['diskon_rupiah'] > 0) {
//									$diskonCustomer = $customerDiskonData['diskon_rupiah'];
//								}
                        // Total Pembelian di kurangi $diskonCustomer
                        // $tot_pembelian -= $diskonCustomer;
                        // $_SESSION['diskonCustomer'] = $diskonCustomer;
                        // end Diskon per Customer
                        ?>
                     </table>
                     <?php
                     $pmbyrn = mysql_query("SELECT * from pembayaran");
                     ?>

                     <form method=POST action='../aksi.php?module=penjualan_barang&act=input'>
                        <input type=hidden name='tot_pembayaran' value="<?php echo $tot_pembelian; ?>" >
                        <div class="kasir-kanan">
                           <div id='kembalian'></div>
                           <div class="pembayaran">
                              <table class="pembayaran">
                                 <tr>
                                    <td class="right">Total Pembelian :</td>
                                    <td><div id='TotalBeli'><?php echo number_format($tot_pembelian, 0, ',', '.'); ?></div></td>
                                 </tr>
                                 <?php
                                 if ($isMember) {
                                    $sql = "SELECT `value` FROM `config` where `option`='point_value'";
                                    //echo $sql;
                                    $query = mysql_query($sql);
                                    $nilaiPoin = mysql_fetch_array($query, MYSQL_ASSOC);
                                    $jumlahPoin = floor($tot_pembelian / $nilaiPoin['value']);
                                    ?>
                                    <tr>
                                       <td class="right">Jumlah Poin :</td>
                                       <td><div id='jumlahPoin'><?php echo number_format($jumlahPoin, 0, ',', '.'); ?></div></td>
                                    <input type=hidden name='jumlah_poin' value="<?php echo $jumlahPoin; ?>" >
                                    </tr>
                                    <?php
                                 }
                                 ?>

                                 <script>
                                    document.getElementById('tot_pembelian').innerHTML = '<span><small><?php echo $diskonCustomer > 0 ? ' '.number_format($diskonCustomer + $tot_pembelian, 0, ', ', '.') : ''; ?></small> <?php echo number_format($tot_pembelian, 0, ', ', '.'); ?> </span>';
                                 </script>

                                 <?php
                                 if ($transferahad) {
                                    ?>
                                    <input type=hidden name=transferahad value=1>
                                    <?php
                                 }

                                 $_SESSION['tot_pembelian'] = $tot_pembelian;
                                 ?>
                                 <tr>
                                    <td class="right">Tipe Pembayar<span class="u">a</span>n :</td>
                                    <td class="">
                                       <select name='tipePembayaran' accesskey='a' tabindex=1>
                                          <option value='0'>-Tipe Pembayaran-</option>
                                          <?php
                                          while ($pembayaran = mysql_fetch_array($pmbyrn)) {
                                             if ($pembayaran[tipePembayaran] == 'CASH') {
                                                ?>
                                                <option value="<?php echo $pembayaran['idTipePembayaran']; ?>" selected><?php echo $pembayaran['tipePembayaran']; ?></option>
                                                <?php
                                             } else {
                                                ?>
                                                <option value="<?php echo $pembayaran['idTipePembayaran']; ?>" ><?php echo $pembayaran['tipePembayaran']; ?></option>
                                                <?php
                                             }
                                          }
                                          ?>
                                       </select>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="right">Surcharge :</td>
                                    <td class="">
                                       <div class="input-group">
                                          <label for="surcharge">%</label>
                                          <input type=text name='surcharge' id='surcharge' value=0 size=2 tabindex=2>
                                       </div>
                                       <div class="input-group">
                                          <label for="TotalSurcharge">Rp</label>
                                          <input type=text name='TotalSurcharge' id='TotalSurcharge' value=0 size=6  tabindex=100 readonly>
                                       </div>
                                    </td>
                                 </tr>
                                 <?php
                                 if (!$transferahad) {
                                    ?>
                                    <tr>
                                       <td class="right"><span class="u">U</span>ang Dibayar :</td>
                                       <td class=""><input type="text" accesskey="u" name="uangDibayar" id="uangDibayar" value="0" onBlur="RecalcTotal(<?php echo $tot_pembelian; ?>)"  tabindex=3></td>
                                    </tr>
                                    <tr>
                                       <td class="right">Kembali :</td>
                                       <td class=""><input type=text name='uangKembali' id='uangKembali' value=0></td>
                                    </tr>
                                    <?php
                                 }
                                 ?>
                                 <tr>
                                    <td><a href='../aksi.php?module=penjualan_barang&act=batal<?php echo $transferahad ? '&transferahad=1' : ''; ?>' class="tombol">Batal</a></td>
                                    <td class="right">&nbsp;&nbsp;&nbsp;<input type=submit value='Simpan' onclick='this.form.submit();
                                          this.disabled = true;'></td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </form>
                     <?php
                  } else {
                     ?>
                     Belum ada barang yang dibeli<br />
                     <a href='../aksi.php?module=penjualan_barang&act=batal<?php echo $transferahad ? '&transferahad=1' : ''; ?>'><button>BATAL</button></a>
                     <?php
                  }
                  ?>
                  <div class="logo-kasir">
                      <!--<img src='../../image/logo-ahadpos-1.gif'>-->
                     <img src="../../img/logo-glow.png">
                  </div>
                  <?php
                  break;
               case 'carisupplier':
                  $returBeli = true;
                  require '_js_retur_beli.php';
                  break;
            }
         } // if (empty($_SESSION[namauser])
         ?>
         <div id="login-admin" style="
              display: none;
              position: fixed;
              border: 1px solid #a8cf45;
              bottom: 50px;
              background-color: #eef4d2;
              box-shadow: 0px 0px 4px 0px #d2e28b;
              padding: 15px;">
            <form>
               <input type="text" id="nama-user" name="nama-user" placeholder="Nama User Admin" /><br />
               <input type="password" id="password" name="password" placeholder="Password" /><br />
               <a href="js_jual_barang.php?act=caricustomer<?php echo $transferahad ? '&transferahad=1' : ''; ?>" class="tombol" id="tombol-batal-login" accesskey="l">Bata<u>l</u></a>
               <input style="float: right" type="submit" id="tombol-login-submit" value="Submit" />
            </form>
         </div>
         <div id="self-checkout" style="
              display: none;
              position: fixed;
              border: 1px solid #a8cf45;
              bottom: 50px;
              background-color: #eef4d2;
              box-shadow: 0px 0px 4px 0px #d2e28b;
              padding: 15px;">
            <form id="form-sc">
               <input type="text" id="self-checkout-id" name="self-checkout-id" placeholder="Nomor Self Checkout" autocomplete="off"/><br />
               <!--<input type="password" id="password" name="password" placeholder="Password" /><br />-->
               <!--<a href="js_jual_barang.php?act=caricustomer" class="tombol" id="tombol-batal-sc" accesskey="l">Bata<u>l</u></a>-->
               <input style="float: right" type="submit" id="tombol-login-submit" value="Submit" />
            </form>
         </div>
         <div id="ganti-customer" style="
              display: none;
              position: fixed;
              border: 1px solid #a8cf45;
              bottom: 50px;
              margin-left: 200px;
              background-color: #eef4d2;
              box-shadow: 0px 0px 4px 0px #d2e28b;
              padding: 15px;">
            <form id="form-nomor-kartu">
               <input type="text" id="nomor-kartu-id" name="nomor_kartu" placeholder="Nomor Kartu" autocomplete="off"/><br />
               <input style="float: right" type="submit" id="tombol-login-submit" value="Submit" />
            </form>
         </div>
         <div id="cek-harga" style="
              display: none;
              position: fixed;
              border: 1px solid #a8cf45;
              bottom: 50px;
              margin-left: 300px;
              background-color: #eef4d2;
              box-shadow: 0px 0px 4px 0px #d2e28b;
              padding: 15px;">
            <table style="">      
               <form id="form-cek-harga">
                  <tr>
                     <td rowspan="3">               
                        <input type="text" id="cek-harga-barcode" name="cek-harga[barcode]" placeholder="Scan Barcode" autocomplete="off"/><br />
                        <input type="text" id="cek-harga-nama" name="cek-harga[nama]" placeholder="Nama Barang (min 3 huruf)"/><br />
                     </td>
                     <td rowspan="3" style="font-size: 16pt" id="cek-harga-view-harga" class="cek-harga-view">123.456</td>
                     <td style="vertical-align: middle" id="cek-harga-view-nama" class="cek-harga-view">Nama</td>                  
                  </tr>
                  <tr>
                     <td style="vertical-align: middle" id="cek-harga-view-barcode" class="cek-harga-view">
                        Barcode
                     </td>
                  </tr>
                  <tr>
                     <td style="vertical-align: middle" id="cek-harga-view-stok" class="cek-harga-view">
                        Stok
                     </td>
                  </tr>
                  <tr style="border-top: 1px solid gray;">
                     <td style="padding-top: 5px">
                        <input style="float: right" type="submit" id="tombol-login-submit" value="Submit" />
                     </td>
                     <td class="cek-harga-view"></td>
                     <td class="cek-harga-view"></td>
                  </tr>  
               </form>
            </table>
         </div>
         <div id="footer" >
            <?php
            if ($returBeli) {
               ?>	
               <a class="tombol" href="js_jual_barang.php?act=carisupplier" accesskey="r" ><b><u>R</u></b>eload</a>
               <?php
            } else {
               ?>
               <a class="tombol" href="js_jual_barang.php?act=caricustomer<?php echo $transferahad ? '&transferahad=1' : ''; ?>" accesskey="r" ><b><u>R</u></b>eload</a>
               <a class="tombol<?php echo $hapusGagal || $batalGagal ? " getar" : ''; ?>" href="" accesskey="d" id="admin-mode" <?php echo $_SESSION['hakAdmin'] ? 'style="background-color:#a8cf45;color:#fff"' : ''; ?>>
                  <?php echo $_SESSION['hakAdmin'] ? '<i class="fa fa-power-off" style="color:green;"></i>' : '<i class="fa fa-power-off" ></i>'; ?> A<u><b>d</b></u>min Mode
               </a>
               <a class="tombol" href="#" id="tombol-self-checkout" accesskey="f" >Sel<b><u>f</u></b> Checkout</a>
               <a class="tombol" href="#" id="tombol-nomor-kartu" accesskey="k" ><b><u>K</u></b>artu Member</a>
               <a class="tombol" href="#" id="tombol-cek-harga" accesskey="g" >Cek Har<b><u>g</u></b>a</a>
                     <?php }
                     ?>
         </div>
         <script>
            $(document).ready(function () {
               $('.harga-jual').editable({
                  success: function (response, newValue) {
                     var respon = JSON && JSON.parse(response) || $.parseJSON(response);
                     if (respon.sukses) {
                        window.location = "js_jual_barang.php?act=caricustomer<?php echo $transferahad ? '&transferahad=1' : ''; ?>";
                     } else {
                        alert('hmm, error!')
                     }
                  }
               });
            });

            $("#cek-harga-nama").autocomplete({
               source: "../aksi.php?module=penjualan&act=getnamabarangstok",
               minLength: 3,
               select: function (event, ui) {
                  console.log(ui.item ?
                          "Nama: " + ui.item.value + "; Barcode " + ui.item.id :
                          "Nothing selected, input was " + this.value);
                  if (ui.item) {
                     $("#cek-harga-barcode").val(ui.item.id);
                     $("#form-cek-harga").submit();
                  }
               }
            });

            $("#tombol-nomor-kartu").click(function () {
               //$("#self-checkout").show(500);
               $("#ganti-customer").toggle(500, function () {
                  if ($("#ganti-customer").css('display') === 'none') {
                     console.log('hidden');
                  } else {
                     console.log("show");
                     $("#nomor-kartu-id").val("");
                     $("#nomor-kartu-id").focus();
                  }
               });

               return false;
            });

            $("#tombol-self-checkout").click(function () {
               //$("#self-checkout").show(500);
               $("#self-checkout").toggle(500, function () {
                  if ($("#self-checkout").css('display') === 'none') {
                     console.log('hidden');
                  } else {
                     console.log("show");
                     $("#self-checkout-id").val("");
                     $("#self-checkout-id").focus();
                  }
               });

               return false;
            });

            $("#tombol-cek-harga").click(function () {
               $(".cek-harga-view").hide();
               $("#cek-harga").toggle(500, function () {
                  if ($("#cek-harga").css('display') === 'none') {
                     console.log('hidden');
                  } else {
                     console.log("show");
                     $("#cek-harga-barcode").val("");
                     $("#cek-harga-barcode").focus();
                  }
               });

               return false;
            });

            $("#form-nomor-kartu").submit(function () {
               console.log($("#nomor-kartu-id").val());
               var datakirim = {
                  'nomor-kartu': $("#nomor-kartu-id").val()
               };
               dataurl = "../aksi.php?module=penjualan_barang&act=nomorkartuinput";
               $.ajax({
                  type: "POST",
                  url: dataurl,
                  data: datakirim,
                  success: function (data) {
                     console.log(data);
                     if (data.sukses) {
                        window.location = "js_jual_barang.php?act=caricustomer<?php echo $transferahad ? '&transferahad=1' : ''; ?>";
                     }
                  }

               });
               $("#ganti-customer").hide(500);
               return false;
            })

            $("#form-sc").submit(function () {
               console.log($("#self-checkout-id").val());
               var datakirim = {
                  'sc-id': $("#self-checkout-id").val()
               };
               dataurl = "../aksi.php?module=penjualan_barang&act=selfcheckoutinput";
               $.ajax({
                  type: "POST",
                  url: dataurl,
                  data: datakirim,
                  success: window.location = "js_jual_barang.php?act=caricustomer<?php echo $transferahad ? '&transferahad=1' : ''; ?>"
               });
               $("#self-checkout").hide(500);
               return false;
            });

            $("#form-cek-harga").submit(function () {
               console.log($("#cek-harga-barcode").val());
               var datakirim = {
                  'cek-harga-barcode': $("#cek-harga-barcode").val()
               };
               dataurl = "../aksi.php?module=penjualan_barang&act=cek_harga";
               $.ajax({
                  type: "POST",
                  url: dataurl,
                  data: datakirim,
                  success: function (data) {
                     console.log(data);
                     if (data.sukses) {
                        $("#cek-harga-view-harga").html(data.harga);
                        $("#cek-harga-view-nama").html(data.nama);
                        $("#cek-harga-view-barcode").html(data.barcode);
                        $("#cek-harga-view-stok").html('Stok: ' + data.stok);
                        $(".cek-harga-view").show();
                        $("#cek-harga-barcode").val("");
                        $("#cek-harga-nama").val("");
                        $("#cek-harga-barcode").focus();
                     }
                  }

               });
               return false;
            })

            $("#admin-mode").click(function () {
<?php
if ($_SESSION['hakAdmin']) {
   ?>
                  var datakirim = {
                     'logout': 'iya'
                  };
                  dataurl = "../aksi.php?module=diskon&act=loginadmin";
                  $.ajax({
                     type: "POST",
                     url: dataurl,
                     data: datakirim,
                     success: function (data) {
                        if (data == 'logout admin') {
                           window.location = "js_jual_barang.php?act=caricustomer<?php echo $transferahad ? '&transferahad=1' : ''; ?>";
                        }
                     }
                  });
   <?php
} else {
   ?>
                  $("#login-admin").show(500);
                  $("#nama-user").focus();
   <?php
}
?>
               return false;
            });
            $("#tombol-batal-login").click(function () {
               $("#login-admin").hide(500);
               return false;
            });
            $("#tombol-login-submit").click(function () {
               var datakirim = {
                  'username': $("#nama-user").val(),
                  'pass': $("#password").val()
               }
               console.log(datakirim);
               dataurl = "../aksi.php?module=diskon&act=loginadmin";
               $.ajax({
                  type: "POST",
                  url: dataurl,
                  data: datakirim,
                  success: function (data) {
                     if (data === 'ketemu') {
                        window.location = "js_jual_barang.php?act=caricustomer<?php echo $transferahad ? '&transferahad=1' : ''; ?>";
                     } else {
                        alert('Login ditolak!');
                     }
                  },
               });
               return false;
            });
<?php
// ukmMode: Barcode -> enter. Muncul Harga Jual
// dan bukan retur beli
if ($ukmMode && !$returBeli) {
   ?>
               $("#barcode").keydown(function (e) {
                  var datakirim = {
                     barcode: $(this).val()
                  };
                  var dataurl = '../aksi.php?module=penjualan_barang&act=get_harga_jual';
                  if (e.keyCode === 13) {
                     $.ajax({
                        data: datakirim,
                        url: dataurl,
                        type: "POST",
                        dataType: "json",
                        success: function (data) {
                           if (data.sukses) {
                              $("#hargaBarang").val(data.hargaJual);
                           } else {

                           }
                           $("#hargaBarang").focus();
                           $("#hargaBarang").select();
                        }
                     });
                     return false;
                  }
               });
   <?php
}
?>
            $("#jumBarang").change(function () {
               $("#jumBarang-cariBarang").val($(this).val());
            });
         </script>
      </div>
   </body>
</html>

<?php

            /* CHANGELOG -----------------------------------------------------------

              1.6.0 / 2013-02-24 : Harry Sufehmi	: fitur : transfer barang antar sesama pengguna AhadPOS
              1.0.1 / 2010-06-03 : Harry Sufehmi	: perhitungan Surcharge dibetulkan
              0.9.2 / 2010-03-03 : Harry Sufehmi 	: initial release
              ------------------------------------------------------------------------ */