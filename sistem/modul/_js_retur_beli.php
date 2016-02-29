<?php
/* _js_retur_beli.php ----------------------------------------
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
?>
<?php
//print_r($_SESSION);
if (isset($_POST['supplierId'])) {
	findSupplier($_POST['supplierId']);
}

if ($_GET[doit]== 'hapus') {
	$sql= "DELETE FROM tmp_edit_detail_retur_beli WHERE idBarang= {$_GET['idBarang']}";
	// echo $sql;
	$hasil= mysql_query($sql) or die('Gagal hapus data, error: '.mysql_error());
}

if (isset($_GET['barcode'])) {
	$barcodeGet= $_GET['barcode'];
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

if ($_GET['action']=== 'tambah') {
	$barcode= isset($_GET['barcode']) ? $_GET['barcode'] : $_POST['barcode'];

	$tambahBarang= 1;
	if (isset($_POST['jumBarang'])) {
		$tambahBarang= $_POST['jumBarang'];
	}
	tambahBarangReturBeli($barcode, $tambahBarang);
}
?>
<div style="float:right" id="tot_pembelian">
	<span><?php echo number_format($_SESSION['tot_pembelian'], 0, ',', '.'); ?></span>
</div>
<div class="top">
	RETUR BELI :<?php echo $_SESSION['namaSupplier']; ?><br />
	<?php echo date('d-m-Y'); ?>
</div>
<form id="entry-barang" method=POST action='js_jual_barang.php?act=carisupplier&action=tambah'>
	<div class="input-group">
		<label for="barcode">Barcode</label>
		<input type="text" class="form-control" name="barcode" accesskey="b" id="barcode" autocomplete="off">
	</div>
	<input type=hidden name='returbeli' value='1'>

	<div class="input-group">
		<label for="jumBarang">Qty</label>
		<input type="text" class="form-control" id="jumBarang" name='jumBarang' value='1' size=5 accesskey="q" autocomplete="off">
	</div>
	<button type="submit">Tambah</button>
</form>

<form method="POST" action="js_cari_barang_2.php?caller=js_jual_barang.php?act=carisupplier" onSubmit="popupform(this, 'cari1')">
	<div class="input-group">
		<label for="namaBarang">Cari Barang</label>
		<input type="text" class="form-control" id="namaBarang" name='namabarang' accesskey='c'>
	</div>
	<button type="submit" style="margin-bottom: 0px;">Cari</button>	
</form>




<?php
$sql= "SELECT *
		FROM tmp_edit_detail_retur_beli trb 
		JOIN barang b ON trb.barcode= b.barcode";
//echo $sql;
$query= mysql_query($sql);
$r= mysql_fetch_row($query);
?>
<hr />
<?php
if ($r) {
	//echo "Ada $r[0] data";
	?>
	<table class="tabel daftar-pembelian">
		<tr>
			<th>No</th>
			<th>Nota Beli</th>
			<th>Barcode</th>
			<th>Nama Barang</th>
			<th>Jumlah Asli</th>
			<th>Jumlah Retur</th>
			<th>Harga</th>
			<th>Total</th>
			<th>Hapus</th>
		</tr>
		<?php
		$no= 1;
		$tot_pembelian= 0;

		$query2= mysql_query("SELECT @rownum:=@rownum+1 urut, trb.idTransaksiBeli, trb.barcode, b.namaBarang, trb.jumBarang, trb.hargaBeli, trb.jumRetur, trb.idBarang
										FROM tmp_edit_detail_retur_beli trb, barang b, (SELECT @rownum:=0) r
										WHERE trb.barcode= b.barcode ORDER BY @rownum desc");
		$banyakItem= mysql_num_rows($query2);
		while ($data= mysql_fetch_array($query2)) {
			$total= $data['hargaBeli'] * $data['jumRetur'];
			?>

			<tr class="<?php echo $no % 2=== 0 ? 'alt' : ''; ?>">
				<td class="right"><?php echo $banyakItem - $no + 1; ?></td>
				<td><?php echo $data['idTransaksiBeli']; ?></td>
				<td><?php echo $data['barcode']; ?></td>
				<td><?php echo $data['namaBarang']; ?></td>
				<td class="right"><?php echo $data['jumBarang']; ?></td>
				<td class="right"><?php echo $data['jumRetur']; ?></td>		
				<td class="right"><?php echo number_format($data['hargaBeli'], 0, ',', '.'); ?></td>	
				<td class="right"><?php echo number_format($total, 0, ',', '.'); ?></td>
				<td class="center"><a class="pilih" href='js_jual_barang.php?act=carisupplier&doit=hapus&idBarang=<?php echo $data['idBarang']; ?>'><i class="fa fa-times"></i></a></td>
			</tr>
			<?php
			$tot_pembelian += $total;
			$no++;
		}
		?>
	</table>
	<?php
	$pmbyrn= mysql_query("SELECT * from pembayaran");
	?>

	<form method=POST action='../aksi.php?module=inputreturbeli2&act=simpan'>
		<input type=hidden name='tot_pembayaran' value="<?php echo $tot_pembelian; ?>" >
		<div class="kasir-kanan">
			<div id='Kembalian'></div>
			<div class="pembayaran">
				<table class="pembayaran">
					<tr>
						<td class="right">Total Retur Beli :</td>
						<td><div id='TotalBeli'><?php echo number_format($tot_pembelian, 0, ',', '.'); ?></div></td>
					</tr>

					<script>
						document.getElementById('tot_pembelian').innerHTML= '<span><small><?php echo $diskonCustomer >0 ? ' '.number_format($diskonCustomer + $tot_pembelian, 0, ', ', '.') : ''; ?></small><?php echo number_format($tot_pembelian, 0, ', ', '.'); ?></span>';
					</script>
					<input type="hidden" name="returbeli" value="1">
					<?php
					$_SESSION['tot_pembelian']= $tot_pembelian;
					?>
					<tr>
						<td class="right">Tipe Pembayaran :</td>
						<td class="">
							<select class='form-control' name='tipePembayaran' accesskey='a' tabindex=1>
								<option value='0'>-Tipe Pembayaran-</option>
								<?php
								while ($pembayaran= mysql_fetch_array($pmbyrn)) {
									if ($pembayaran[tipePembayaran]== 'CASH') {
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
								<input type='text' class='form-control' name='surcharge' id='surcharge' value=0 size=2 tabindex=2>
							</div>
							<div class="input-group">
								<label for="TotalSurcharge">Rp</label>
								<input type='text' class='form-control' name='TotalSurcharge' id='TotalSurcharge' value=0 size=6 tabindex=100 readonly>
							</div>
						</td>
					</tr>
					<tr>
						<td><a href='../aksi.php?module=inputreturbeli2&act=batal' class="tombol">Batal</a></td>
						<td class="right"><input type='submit' class='btn btn-default' value='Simpan' onclick='this.form.submit();
									this.disabled= true;'></td>
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

