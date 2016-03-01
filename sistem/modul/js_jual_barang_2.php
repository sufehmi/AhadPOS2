<?php
/* js_jual_barang_2.php ----------------------------------------
version: 0.01

Part of AhadPOS : http://ahadpos.com
License: GPL v2
http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License v2 (links provided above) for more details.
---------------------------------------------------------------- */


require_once($_SERVER["DOCUMENT_ROOT"].'/define.php');

include SITE_ROOT."sistem/modul/function.php";


session_start();
if (empty($_SESSION[namauser]) AND empty($_SESSION[passuser])) {
	echo "<link href='../css/pos_style.css' rel='stylesheet' type='text/css'>
<center>Untuk mengakses modul, Anda harus login <br>";
	echo "<a href=index.php><b>LOGIN</b></a></center>";
} else {

	if (!isset($_SESSION[idCustomer])) {
		findCustomer($_POST[idCustomer]);
	}


	//HS javascript untuk menampilkan popup
	?>	
	<!DOCTYPE html>
	<html>
		<head>


			<script>

				function addComma(angka) {
					nStr += '';
					x=nStr.split('.');
					x1=x[0];
					x2=x.length > 1 ? '.' + x[1] : '';
					var rgx=/(\d+)(\d{3})/;
					while (rgx.test(x1)) {
						x1=x1.replace(rgx, '$1' + ',' + '$2');
					}
					return x1 + x2;
				}

				function RecalcTotal(tot_pembelian) {
					var totalBeli=0;
					var Kembali=0;
					var uangDibayar=parseInt(document.getElementById("uangDibayar").value);
					var surcharge=parseInt(document.getElementById("surcharge").value);

					totalSurcharge=((tot_pembelian / 100) * surcharge);
					totalBeli=tot_pembelian + totalSurcharge;
					Kembali=uangDibayar - totalBeli;

					document.getElementById("uangKembali").value=Kembali;
					document.getElementById("kembalian").innerHTML='<span>' + addComma(Kembali) + '</span>';
					document.getElementById("TotalSurcharge").value=addComma(totalSurcharge);
	//					document.getElementById("tot_pembelian").innerHTML='<span>' + number_format(totalBeli, 0, ',', '.') + '</span>';
					document.getElementById("tot_pembelian").innerHTML='<span>' + addComma(totalBeli) + '</span>';
				}

				-- ></SCRIPT>



			<!--<link href='../../css/adminstyle.css' rel='stylesheet' type='text/css' />-->

			<link rel="stylesheet" type="text/css" href="../../css/style.css" />

		</head>
		<body class="kasir">
			<div id="content" >
				<?php
				if ($_GET[doit] == 'hapus') {
					$sql="DELETE FROM tmp_detail_jual WHERE uid=$_GET[uid]";
					$hasil=mysql_query($sql);
				}


				//fixme: hargaBeli TIDAK tersimpan di detail_jual !!!



				switch ($_GET[act]) { // ============================================================================================================
					case "caricustomer": // ========================================================================================================

						if (($_POST['transferahad'] == 1) || ($_GET['transferahad'] == 1)) {
							?>
							<h2>Transfer antar Ahad</h2>
							<?php
						} else {
							?>
							<!--<h2>Penjualan Barang</h2>-->
							<?php
						};
						?>
						<div style="float:right" id="tot_pembelian">
							<span><?php echo number_format($_SESSION['tot_pembelian'], 0, ',', '.'); ?></span>
						</div>
						<?php
						if (($_POST['transferahad'] == 1) || ($_GET['transferahad'] == 1)) {
							?>
							Transfer Barang ke Ahad : <?php echo $_SESSION['namaCustomer']; ?>
							<h3>Barang yang ditransfer</h3>
							<?php
						} else {
							?>
							<div class="top">
								Customer : <?php echo $_SESSION['namaCustomer']; ?>
							</div>
							<!--<h3>Barang yang dijual</h3>-->
							<?php
						};
						?>
						<form id="entry-barang" method='post' action='js_jual_barang.php?act=caricustomer&action=tambah'>
							<div class="input-group">
								<label for="barcode"><span class="u">B</span>arcode</label>
								<input type="text" class="form-control" name="barcode" accesskey="b" id="barcode">
							</div>
							<?php
							// ----- TERLALU LAMBAT ! ----- jangan gunakan dropbox terlampir untuk memilih barcode
							// ambil daftar barang
							//$sql="SELECT namaBarang,barcode,hargaJual
							//	FROM barang FORCE INDEX (barcode) ORDER BY barcode ASC";
							//$namaBarang=mysql_query($sql);
							//while($brg=mysql_fetch_array($namaBarang)){
							//	echo "<option value='$brg[barcode]'>$brg[barcode] - $brg[namaBarang] - Rp ".number_format($brg[hargaJual],0,',','.')."</option>\n";
							//}	
							//var_dump($_POST);
							//var_dump($_GET);	
							if (($_POST['transferahad'] == 1) || ($_GET['transferahad'] == 1)) {
								?>
								<input type=hidden name='transferahad' value='1'>
								<?php
							};
							?>
							<div class="input-group">
								<label for="jumBarang"><span class="u">Q</span>ty</label>
								<input type="text" class="form-control" id="jumBarang" name='jumBarang' value='1' size=5 accesskey="q">
							</div>
						<!--<input type="submit" class="btn btn-default" name="btnTambah" value="Tambah" accesskey="t">-->
							<button type="submit" class="btn btn-default"><span class="u">T</span>ambah</button>
						</form>

						<form method="POST" action="js_cari_barang.php?caller=js_jual_barang" onSubmit="popupform(this, 'cari1')">
							<div class="input-group">
								<label for="namaBarang"><span class="u">C</span>ari Barang</label>
								<input type="text" class="form-control" id="namaBarang" name='namabarang' accesskey='c'>
							</div>
							<?php
							if (($_POST['transferahad'] == 1) || ($_GET['transferahad'] == 1)) {
								?>
								<input type=hidden name='transferahad' value='1'>
								<?php
							};
							?>
							<!--<button type="submit" class="btn btn-default" name="btnCari" id="btnCari">GO</button>-->
							<!--<input type='submit' class='btn btn-default' name='btnCari' id='btnCari' value='Cari'>-->
						</form>


						<script>
									var dropBox=document.getElementById("barcode");
							if (dropBox != null)
								dropBox.focus();
						</script>

						<?php
						if ($_GET[action] == 'tambah') {

							if ($_GET[barcode]) {
								$_POST[barcode]=$_GET[barcode];
							};
							$trueJual=cekBarangTempJual($_SESSION[idCustomer], $_POST[barcode]);
//			echo "$trueJual";
							if ($trueJual != 0) {

								tambahBarangJualAda($_SESSION[idCustomer], $_POST[barcode], $_POST[jumBarang]);
							} else {

								tambahBarangJual($_POST[barcode], $_POST[jumBarang]);
							}
						}
						$sql="SELECT *
								FROM tmp_detail_jual tdj, barang b
								WHERE tdj.barcode=b.barcode AND tdj.idCustomer='$_SESSION[idCustomer]' AND tdj.username='$_SESSION[uname]'";
						//echo $sql;
						$query=mysql_query($sql);
						$r=mysql_fetch_row($query);
						?>
						<hr />
						<?php
						if ($r) {
							//echo "Ada $r[0] data";
							?>
							<table class="tabel daftar-pembelian">
								<tr>
									<th>Barcode</th>
									<th>Nama Barang</th>
									<th>Jumlah</th>
									<th>Harga</th>
									<th>Total</th>
									<th>Hapus</th>
								</tr>
								<?php
								$no=1;
								$tot_pembelian=0;

								$query2=mysql_query("SELECT tdj.uid, tdj.barcode, b.namaBarang, tdj.jumBarang, tdj.hargaJual, tdj.tglTransaksi
										FROM tmp_detail_jual tdj, barang b
										WHERE tdj.barcode=b.barcode AND tdj.idCustomer='{$_SESSION['idCustomer']}'
										AND tdj.username='{$_SESSION['uname']}' ORDER BY tglTransaksi DESC");

								while ($data=mysql_fetch_array($query2)) {
									//untuk mewarnai tabel menjadi selang-seling
									if (($no % 2) == 0) {
										$warna="#EAF0F7";
									} else {
										$warna="#FFFFFF";
									}

									// jika ini barang yang akan di transfer,
									// maka berikan hargaBeli (modal) sebagai hargaJual
									if (($_POST['transferahad'] == 1) || ($_GET['transferahad'] == 1)) {
										$sql="SELECT hargaBeli FROM detail_beli
										WHERE isSold='N' AND barcode='$data[barcode]' ORDER BY idDetailBeli ASC";
										$hasil=mysql_query($sql);
										$x=mysql_fetch_array($hasil);

										// jika tidak ada / semua stok barang ini sudah terjual=catatan stok ngaco
										// maka ambil hargaBeli yang terakhir saja
										if (mysql_num_rows($hasil) < 1) {
											$sql="SELECT hargaBeli FROM detail_beli
											WHERE barcode='$data[barcode]' ORDER BY idDetailBeli ASC";
											$hasil=mysql_query($sql);
											$x=mysql_fetch_array($hasil);
										};

										$data['hargaJual']=$x['hargaBeli'];
									};

									$total=$data[hargaJual] * $data[jumBarang];
									?>

									<tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>">
										<td><?php echo $data[barcode]; ?></td>
										<td><?php echo $data[namaBarang]; ?></td>
										<td class="right"><?php echo $data['jumBarang']; ?></td>
										<td class="right"><?php echo $data['hargaJual']; ?></td>
										<td class="right"><?php echo number_format($total, 0, ',', '.'); ?></td>
										<td class="center"> <a class="pilih" href='js_jual_barang.php?act=caricustomer&doit=hapus&uid=<?php echo $data['uid']; ?>'><i class="fa fa-times"></i></a></td>
									</tr>
									<?php
									$tot_pembelian += $total;
									$no++;
								}
								?>
							</table>
							<?php
							$pmbyrn=mysql_query("SELECT * from pembayaran");
							?>

							<form method='post' action='../aksi.php?module=penjualan_barang&act=input'>
								<input type=hidden name='tot_pembayaran' value="<?php echo $tot_pembelian; ?>" >
								<div class="kasir-kanan">
									<div id='kembalian'></div>
									<div class="pembayaran">
										<table class="pembayaran">
											<tr>
												<td class="right">Total Pembelian :</td>
												<td><div id='TotalBeli'><?php echo number_format($tot_pembelian, 0, ',', '.'); ?></div></td>
											</tr>

											<script>
												document.getElementById('tot_pembelian').innerHTML='<span><?php echo number_format($tot_pembelian, 0, ', ', '.'); ?></span>';
											</script>

											<?php
											if (($_POST['transferahad'] == 1) || ($_GET['transferahad'] == 1)) {
												?>
												<input type=hidden name=transferahad value=1>
												<?php
											};

											$_SESSION['tot_pembelian']=$tot_pembelian;
											?>
											<tr>
												<td class="right">Tipe Pembayar<span class="u">a</span>n :</td>
												<td class="">
													<select class='form-control' name='tipePembayaran' accesskey='a' tabindex=1>
														<option value='0'>-Tipe Pembayaran-</option>
														<?php
														while ($pembayaran=mysql_fetch_array($pmbyrn)) {
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
														<input type='text' class='form-control' class='form-control' name='surcharge' id='surcharge' value=0 size=2 tabindex=2>
													</div>
													<div class="input-group">
														<label for="TotalSurcharge">Rp</label>
														<input type='text' class='form-control' class='form-control' name='TotalSurcharge' id='TotalSurcharge' value=0 size=6 tabindex=100 readonly></td>
													</div>
											</tr>
											<tr>
												<td class="right"><span class="u">U</span>ang Dibayar :</td>
												<td class=""><input type="text" class="form-control" accesskey="u" name="uangDibayar" id="uangDibayar" value="0" onBlur="RecalcTotal(<?php echo $tot_pembelian; ?>)" tabindex=3></td>
											</tr>
											<tr>
												<td class="right">Kembali :</td>
												<td class=""><input type='text' class='form-control' class='form-control' name='uangKembali' id='uangKembali' value=0></td>
											</tr>
											<tr>
												<td><a href='../aksi.php?module=penjualan_barang&act=batal'><button>Batal</button></a></td>
												<td class="right">&nbsp;<input type='submit' class='btn btn-default' value='Simpan' onclick='this.disabled=true;'></td>
											</tr>
										</table>
									</div>
								</div>
							</form>
							<?php
						} else {
							?>
							Belum ada barang yang dibeli<br />
							<a href='../aksi.php?module=penjualan_barang&act=batal'><button>BATAL</button></a>
							<?php
						}
						?>
						<div class="logo-kasir">
							<!--<img src='../../image/logo-ahadpos-1.gif'>-->
							<img src="../../img/logo-glow.png">
						</div>
						<?php
						break;
				}
			} // if (empty($_SESSION[namauser])



			/* CHANGELOG -----------------------------------------------------------

			1.6.0 / 2013-02-24 : Harry Sufehmi	: fitur : transfer barang antar sesama pengguna AhadPOS
			1.0.1 / 2010-06-03 : Harry Sufehmi	: perhitungan Surcharge dibetulkan
			0.9.2 / 2010-03-03 : Harry Sufehmi 	: initial release
			------------------------------------------------------------------------ */
			?>
		</div>
	</body>
</html>