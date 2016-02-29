<?php
/* js_buat_rpo.php ----------------------------------------
version: 1.01

Part of AhadPOS : http://ahadpos.com
License: GPL v2
http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License v2 (links provided above) for more details.
---------------------------------------------------------------- */


include "../../config/config.php";
include "function.php";


session_start();
if (empty($_SESSION['namauser'])) {
	echo "<link href='../config/style.css' rel='stylesheet' type='text/css'>
<center>Untuk mengakses modul, Anda harus login<br>";
	echo "<a href=index.php><b>LOGIN</b></a></center>";
} else {

	if (!isset($_SESSION['idCustomer'])) {
		findSupplier($_POST['supplierid']);
		$_SESSION['idCustomer']= $_SESSION['idSupplier'];
	};

	if (!isset($_SESSION['periode'])) {
		$_SESSION['periode']= $_POST['periode'];
	};
	if (!isset($_SESSION['range'])) {
		$_SESSION['range']= $_POST['range'];
	};
	if (!isset($_SESSION['persediaan'])) {
		$_SESSION['persediaan']= $_POST['persediaan'];
	};

	//var_dump($_SESSION);
	//HS javascript untuk menampilkan popup
	ahp_popupheader('retur barang');
?>
		<body class="kasir" id="dokumen">
			<div id="content" >
				<?php
				if ($_GET[doit]== 'hapus') {
					$sql= "DELETE FROM tmp_detail_jual WHERE uid= $_GET[uid]";
					$hasil= mysql_query($sql);
				}



				switch ($_GET[act]) { //============================================================================================================
					case "caricustomer":
					case "mulairpo": //========================================================================================================
						?>
						<h2>Buat RPO</h2>
						<div style='float:right' id='tot_pembelian'>
							<span style='font-size:48pt'><?php echo number_format($_SESSION[tot_pembelian], 0, ',', '.'); ?>
							</span>
						</div>

						Supplier :<?php echo $_SESSION['namaSupplier']; ?><br />Untuk Persediaan :<?php echo $_SESSION['persediaan']; ?>hari

						<h3>Barang yang dipesan</h3>
						<table>
							<tr>
								<td>

									<form id="entry-barang" method=POST action='js_buat_rpo.php?act=mulairpo&action=tambah'>
										<div class="input-group">
											<label for="jumBarang">Qty</label>
											<input type="text" class="form-control" id="jumBarang" name='jumBarang' value='1' size=5 accesskey="q">
										</div>
										<div class="input-group">
											<label for="barcode">Barcode</label>
											<input type="text" class="form-control" name="barcode" accesskey="b" id="barcode">
										</div>
										<button type="submit">Tambah</button>
			<!--							(b) Barcode</td><td><input type='text' class='form-control' name='barcode' accesskey='b' id='barcode'></td>

								<td>(q) Qty</td><td><input type='text' class='form-control' name='jumBarang' value='0' size=5 accesskey='q'></td>
								<td align=right><input type='submit' class='btn btn-default' name='btnTambah' value='(t) Tambah' accesskey='t'></td>-->
									</form>



								<td>
									<FORM METHOD=POST ACTION="js_cari_barang.php?caller=js_buat_rpo" onSubmit="popupform(this, 'cari1')">
										<div class="input-group">
											<label for="namaBarang">Cari Barang</label>
											<input type="text" class="form-control" id="namaBarang" name='namabarang' accesskey='c'>
										</div>
			<!--										<input type='text' class='form-control' name='namabarang' accesskey='c'>
										<input type='submit' class='btn btn-default' name='btnCari' id='btnCari' value='(c) Cari Barang'>-->
									</form>
								</td>

							</tr>
						</table>
					</form>


					<?php
					if ($_GET['action']== 'tambah') {
						if ($_GET['barcode']) {
							$_POST['barcode']= $_GET['barcode'];
						};

						$sudahAda= cekBarangTempRPO($_SESSION['idCustomer'], $_POST['barcode']);
						if ($sudahAda != 0) {
							tambahBarangRPOAda($_SESSION['idCustomer'], $_POST['barcode'], $_POST['jumBarang']);
						} else {
							// tambahBarangRPO($_POST['barcode'], $_POST['jumBarang'], $_SESSION['range'], $_SESSION['periode'], $_SESSION['persediaan']);
					tambahBarangRPOperItem($_POST['barcode'], $_POST['jumBarang']);
						}
					}

					$sql= "SELECT * FROM tmp_detail_jual tdj, barang b
			WHERE tdj.barcode= b.barcode AND tdj.idCustomer= '" . $_SESSION['idCustomer'] . "' 
				AND tdj.username= '$_SESSION[uname]'";
					$query= mysql_query($sql);
					$r= mysql_fetch_row($query);
					echo "<hr/>";

					if ($r) {
						?>	
						<table class="tabel daftar-pembelian">
							<tr>
								<th>Barcode</th>
								<th>Nama Barang</th>
						<th>Rata2 Penjualan<br />Mingguan</th>
								<th>Saran Order</th>
								<th>Stok Saat Ini</th>
								<th>Jumlah Pesanan</th>
								<th>Harga</th>
								<th>Total</th>
								<th>Hapus</th>
							</tr>
							<?php
							$no= 1;
							$tot_pembelian= 0;

							$query2= mysql_query("SELECT tdj.uid, tdj.barcode, tdj.idBarang, tdj.hargaBeli, b.namaBarang, 
						tdj.jumBarang, tdj.hargaJual, tdj.tglTransaksi, dp.rata_rata_mingguan
										FROM tmp_detail_jual tdj, barang b
										LEFT JOIN data_penjualan dp ON b.barcode= dp.barcode
										WHERE tdj.barcode= b.barcode AND tdj.idCustomer= '$_SESSION[idCustomer]' 
						AND tdj.username= '$_SESSION[uname]' ORDER BY tglTransaksi DESC");

							while ($data= mysql_fetch_array($query2)) {
								$total= $data['hargaJual'] * $data['jumBarang'];
						$saran= round($data['rata_rata_mingguan'] / 7 * $_SESSION['persediaan']) - $data['hargaBeli']; // $data['harga_beli']== stok saat ini
						$saranOrder= $saran >= 0 ? $saran : 0;
								?>
								<?php /*
								<tr class="<?php echo $no % 2=== 0 ? 'alt' : ''; ?>">
								<td><?php echo $data['barcode']; ?></td>
								<td><?php echo $data['namaBarang']; ?></td>
								<td class="right"><?php echo $data['jumBarang']; ?></td>
								<td class="right"><?php echo $data['hargaJual']; ?></td>
								<td class="right"><?php echo number_format($total, 0, ',', '.'); ?></td>
								<td class="center"><a class="pilih" href='js_jual_barang.php?act=caricustomer&doit=hapus&uid=<?php echo $data['uid']; ?>'><i class="fa fa-times"></i></a></td>
								</tr>
								*/ ?>

								<tr class="<?php echo $no % 2=== 0 ? 'alt' : ''; ?>">
									<td><?php echo $data['barcode']; ?></td>
									<td><?php echo $data['namaBarang']; ?></td>
									<td class="right"><?php echo number_format($data['rata_rata_mingguan'],2,',','.'); ?></td>
									<td class="right"><?php echo $saranOrder; //saran order	?></td>
									<td class="right"><?php echo $data['hargaBeli']; //cur stock	?></td>
									<td class="right"><?php echo $data['jumBarang']; // jumlah pesanan	?></td>
									<td class="right"><?php echo $data['hargaJual']; // harga	?></td>
									<td class="right"><?php echo number_format($total, 0, ',', '.'); ?></td>
									<td class="right"><a href='js_buat_rpo.php?act=caricustomer&doit=hapus&uid=<?php echo $data['uid']; ?>'><i class='fa  fa-times-circle-o'></i> Hapus</a> 
									</td>
								</tr>

								<?php
								$tot_pembelian += $total;
								$no++;
							}
							?></table>
						<?php
						$pmbyrn= mysql_query("SELECT * from pembayaran");
						?>
						<div style="clear:both"></div>
						<form method=POST action='../aksi.php?module=buat_rpo&act=input'>
							<input type=hidden name='tot_pembayaran' value='<?php echo $tot_pembelian; ?>'>

							<div id='Kembalian' style='float:right'></div>

							<table class="daftar-pembelian" style="border:1px solid rgb(221, 221, 221)">
								<tr>
									<td width=65% align=right>Total Pembelian</td>
									<td align=right>
										<div id='TotalBeli'><?php echo number_format($tot_pembelian, 0, ',', '.'); ?></div>
									</td>
								</tr>
								<script>
									document.getElementById('tot_pembelian').innerHTML= '<span style="font-size:48pt"><?php echo number_format($tot_pembelian, 0, ',', '.'); ?></span>';
								</script>
								<?php
								$_SESSION['tot_pembelian']= $tot_pembelian;
								?>
								<tr>
									<td>[<a href='../aksi.php?module=penjualan_barang&act=batal'>BATAL</a>]</td>
									<td align=right><input type='submit' class='btn btn-default' value='Simpan' onclick='this.disabled= true;'></td>
								</tr>
							</table>
						</form>
						<?php
					} else {
						echo "Belum ada barang yang dipilih<br />
						<a href='../aksi.php?module=penjualan_barang&act=batal'>BATAL</a>
						";
					}

					//echo "		<div style='float:right'><img src='../../image/logo-ahadpos-1.gif'></div>";

					break;
			}
		} // if (empty($_SESSION[namauser])
		?>
	</div>
</body>
</html>

<?php
/* CHANGELOG -----------------------------------------------------------

1.6.0 / 2013-05-06 : Harry Sufehmi	: initial release
------------------------------------------------------------------------ */
?>
