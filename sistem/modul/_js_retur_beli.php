<?php
/* _js_retur_beli.php ----------------------------------------
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
?>
<?php
//print_r($_SESSION);
if (isset($_POST['supplierId'])) {
	findSupplier($_POST['supplierId']);
}

if (isset($_GET['barcode'])) {
	$barcodeGet = $_GET['barcode'];
	?>
	<script>
		$(document).ready(function () {
			$("#barcode").val("<?php echo $barcodeGet; ?>");
			$("#jumBarang").focus();
			$("#jumBarang").select();
		});
	</script>
	<?php
}

if ($_GET['action'] === 'tambah') {
	$barcode = isset($_GET['barcode']) ? $_GET['barcode'] : $_POST['barcode'];

	$tambahBarang = 1;
	if (isset($_POST['jumBarang'])) {
		$tambahBarang = $_POST['jumBarang'];
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
?>
<div style="float:right" id="tot_pembelian">
	<span><?php echo number_format($_SESSION['tot_pembelian'], 0, ',', '.'); ?></span>
</div>
<div class="top">
	RETUR BELI : <?php echo $_SESSION['namaSupplier']; ?> <br />
	<?php echo date('d-m-Y'); ?>
</div>
<form id="entry-barang" method=POST action='js_jual_barang.php?act=carisupplier&action=tambah'>
	<div class="input-group">
		<label for="barcode"><span class="u">B</span>arcode</label>
		<input type="text" name="barcode" accesskey="b" id="barcode" autocomplete="off">
	</div>
	<input type=hidden name='returbeli' value='1'>

	<div class="input-group">
		<label for="jumBarang"><span class="u">Q</span>ty</label>
		<input type="text" id="jumBarang" name='jumBarang' value='1' size=5 accesskey="q" autocomplete="off">
	</div>
	<button type="submit"><span class="u">T</span>ambah</button>
</form>

<form method="POST" action="js_cari_barang_2.php?caller=js_jual_barang.php?act=carisupplier" onSubmit="popupform(this, 'cari1')">
	<div class="input-group">
		<label for="namaBarang"><span class="u">C</span>ari Barang</label>
		<input type="text" id="namaBarang" name='namabarang' accesskey='c'>
	</div>
	<button type="submit" style="margin-bottom: 0px;"><span class="u">C</span>ari</button>	
</form>


<script>
	var dropBox = document.getElementById("barcode");
	if (dropBox != null)
		dropBox.focus();
</script>

<?php
$sql = "SELECT *
                                FROM tmp_detail_jual tdj, barang b
                                WHERE tdj.barcode = b.barcode AND tdj.idCustomer = '$_SESSION[idCustomer]' AND tdj.username = '$_SESSION[uname]'";
//echo $sql;
$query = mysql_query($sql);
$r = mysql_fetch_row($query);
?>
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
			<th>Total</th>
			<th>Hapus</th>
		</tr>
		<?php
		$no = 1;
		$tot_pembelian = 0;

		$query2 = mysql_query("SELECT tdj.uid, tdj.barcode, b.namaBarang, tdj.jumBarang, tdj.hargaJual, tdj.tglTransaksi, tdj.diskon_persen, tdj.diskon_rupiah
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
				<td class="right"><?php
					if ($data['diskon_persen'] > 0) {
						echo $data['diskon_persen'].'%';
					} elseif ($data['diskon_rupiah'] > 0) {
						echo number_format($data['diskon_rupiah'], 0, ',', '.');
						$totalDiskon += $data['diskon_rupiah'] * $data['jumBarang'];
					}
					?>
				</td>
				<td class="right"><?php echo number_format($total, 0, ',', '.'); ?></td>

				<td class="center"> <a class="pilih" href='js_jual_barang.php?act=caricustomer&doit=hapus&uid=<?php echo $data['uid']; ?><?php echo $transferahad ? '&transferahad=1' : ''; ?>'><i class="fa fa-times"></i></a></td>
			</tr>
			<?php
			$tot_pembelian += $total;
			$no++;
		}
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

					<script>
						document.getElementById('tot_pembelian').innerHTML = '<span><small><?php echo $diskonCustomer > 0 ? ' '.number_format($diskonCustomer + $tot_pembelian, 0, ', ', '.') : ''; ?></small> <?php echo number_format($tot_pembelian, 0, ', ', '.'); ?> </span>';
					</script>

					<?php
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
					<tr>
						<td><a href='../aksi.php?module=penjualan_barang&act=batal' class="tombol">Batal</a></td>
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
	Belum ada barang yang diretur<br />
	<a href='../aksi.php?module=penjualan_barang&act=batal'><button>BATAL</button></a>
	<?php
}
?>
<div class="logo-kasir">
	<img src="../../img/logo-glow.png">
</div>