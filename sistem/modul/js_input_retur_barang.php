<?php
/* js_input_retur_barang.php ----------------------------------------
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

include_once('../../tacoen/function.php');
include_once('../tacoen/function.php');
session_start();
check_ahadpos_session();



// catat nama printer yang sudah dipilih
if (!empty($_POST[namaPrinter])) {
	$_SESSION[namaPrinter]= $_POST[namaPrinter];
};


//HS javascript untuk menampilkan popup

ahp_popupheader('retur barang');


?>	

<body class="kasir" id="dokumen">
		<div id="content" >
			<?php
			if ($_GET[doit]== 'hapus') {
			$sql= "DELETE FROM tmp_detail_retur_barang WHERE uid= $_GET[uid]";
			//echo $sql;
			$hasil= mysql_query($sql);
			}


			//fixme: hargaBeli TIDAK tersimpan di detail_jual !!!



			switch ($_GET[act]) { //============================================================================================================
			case "caricustomer": //========================================================================================================
				// catat nama printer yang sudah dipilih
				//$_SESSION[namaPrinter]= $_POST[namaPrinter];
				?>
				<div style="float:right" id="tot_pembelian">
					<span><?php echo number_format($_SESSION['tot_retur'], 0, ',', '.'); ?></span>
				</div>
				<div class="top">
					Retur Jual :<?php echo $_SESSION['namaCustomer']; ?><br /><?php echo date('d-m-Y'); ?>
				</div>

				<form id="entry-barang" method=POST action='js_input_retur_barang.php?act=caricustomer&action=tambah'>
					<div class="input-group">
						<label for="barcode"><span class="u">B</span>arcode</label>
						<input type="text" class="form-control" name="barcode" accesskey="b" id="barcode" autofocus="autofocus" autocomplete="off">

					</div>
					<div class="input-group">
						<label for="jumBarang"><span class="u">Q</span>ty</label>
						<input type="text" class="form-control" id="jumBarang" name='jumBarang' value='1' size=5 accesskey="q" autocomplete="off">
					</div>
				<!--<input type='submit' class='btn btn-info' name="btnTambah" value="Tambah" accesskey="t">-->
					<button type="submit"><span class="u">T</span>ambah</button>
				</form>

				<form method="POST" action="js_cari_barang.php?caller=js_input_retur_barang" onSubmit="popupform(this, 'cari1')">
					<div class="input-group">
						<label for="namaBarang"><span class="u">C</span>ari Barang</label>
						<input type="text" class="form-control" id="namaBarang" name='namabarang' accesskey='c'>
						<input type="hidden" id="jumBarang-cariBarang" name="jumBarang" value="1"/>
					</div>
					<!--<button type="submit" name="btnCari" id="btnCari">GO</button>-->
					<input type='submit' class='btn btn-default' name='btnCari' id='btnCari' value='Cari' style="margin-bottom: 0">
				</form>


				<?php
				if ($_GET[action]== 'tambah') {

					if ($_GET[barcode]) {
						$_POST[barcode]= $_GET[barcode];
					};
					$trueJual= cekBarangTempRetur($_POST[barcode]);
					//			echo "$trueJual";
					if ($trueJual != 0) {

						tambahBarangReturAda($_POST[barcode], $_POST[jumBarang]);
					} else {

						tambahBarangRetur($_POST[barcode], $_POST[jumBarang]);
					}
				}


				// mulai cetak daftar transaksi retur sejauh ini
				$sql= "SELECT *
					FROM tmp_detail_retur_barang tdj, barang b
					WHERE tdj.barcode= b.barcode AND tdj.username= '$_SESSION[uname]'";

				//echo $sql;
				$query= mysql_query($sql);
				$r= mysql_fetch_row($query);
				?>
				<hr />
				<?php
				if ($r) {
					?>
					<table class="tabel daftar-pembelian">
						<tr>
						<th>Barcode</th>
						<th>Nama Barang</th>
						<th>Jumlah</th>
						<th>Harga</th>
						<th>Total</th>
						<th>Aksi</th>
						</tr>
						<?php
						$no= 1;
						$tot_retur= 0;

						$query2= mysql_query("SELECT tdj.uid, tdj.barcode, b.namaBarang, tdj.jumBarang, tdj.hargaJual, tdj.tglTransaksi
						FROM tmp_detail_retur_barang tdj, barang b
						WHERE tdj.barcode= b.barcode AND tdj.username= '$_SESSION[uname]' 
						ORDER BY tglTransaksi DESC");

						while ($data= mysql_fetch_array($query2)) {
						$total= $data[hargaJual] * $data[jumBarang];
						?>
						<tr class="<?php echo $no % 2=== 0 ? 'alt' : ''; ?>">
							<td><?php echo $data['barcode']; ?></td>
							<td><?php echo $data['namaBarang']; ?></td>
							<td align=right><?php echo $data['jumBarang']; ?></td>
							<td align=right><?php echo $data['hargaJual']; ?></td>
							<td align=right><?php echo number_format($total, 0, ',', '.'); ?></td>
							<td align=right><a href='js_input_retur_barang.php?act=caricustomer&doit=hapus&uid=<?php echo $data['uid']; ?>'>Hapus</a></td>
						</tr>
						<?php
						$tot_retur += $total;
						$no++;
						}
						?>
					</table>
					<form method=POST action='../aksi.php?module=retur_barang&act=input'>
						<input type=hidden name='tot_retur' value='<?php echo $tot_retur; ?>'>
						<input type=hidden name='namaPrinter' value='<?php echo $_SESSION['namaPrinter']; ?>'>
						<div class="kasir-kanan">
						<div id='kembalian'></div>
						<div class="pembayaran">
							<table class="pembayaran">
								<tr>
									<td>Uang diambil dari kasir aktif:</td>
									<td>
									<select class='form-control' name="idKasir">
										<?php
										$sql= "SELECT k.tglBukaKasir, k.kasAwal, w.namaWorkstation,u.idUser, u.namaUser FROM kasir AS k, user AS u, workstation AS w 
										WHERE k.tglTutupKasir IS NULL 
										AND k.currentWorkstation= w.idWorkstation AND k.idUser= u.idUser";
										$tampil= mysql_query($sql);
										while ($r= mysql_fetch_array($tampil)) {
											?>
											<option value="<?php echo $r['idUser']; ?>"><?php echo $r['namaUser']; ?>di<?php echo $r['namaWorkstation']; ?></option>
											<?php
										}
										?>
									</select>
									</td>
								</tr>
								<script>
									document.getElementById('tot_pembelian').innerHTML= '<span><?php echo number_format($tot_retur, 0, ', ', '.'); ?></span>';
								</script>
								<?php
								$_SESSION['tot_retur']= $tot_retur;
								?>
								<tr>
									<td><a href='../aksi.php?module=retur_barang&act=batal' class="tombol">Batal</a></td>
									<td class="right">&nbsp;&nbsp;&nbsp;<input type='submit' class='btn btn-default' value='Simpan' onclick='this.form.submit();
													this.disabled= true;'></td>
								</tr>
							</table>
						</div>
						</div>
					</form>
					<?php
					/*
					<form method=POST action='../aksi.php?module=retur_barang&act=input'>
					<input type=hidden name='tot_retur' value='<?php echo $tot_retur; ?>'>
					<input type=hidden name='namaPrinter' value='<?php echo $_SESSION['namaPrinter']; ?>'>


					<table class=tableku width=600>
					<tr>
					<td width=65% align=right>Total Retur</td>
					<td align=right><div id='TotalBeli'><?php echo number_format($tot_retur, 0, ',', '.'); ?></div>
					</td>
					</tr>

					<script>
					document.getElementById('tot_pembelian').innerHTML= '<span style="font-size:48pt"><?php echo number_format($tot_retur, 0, ', ', '.'); ?></span>';
					</script>
					<?php
					$_SESSION[tot_retur]= $tot_retur;
					?>
					<tr>
					<td>[<a href='../aksi.php?module=retur_barang&act=batal'>BATAL</a>]</td>
					<td align=right>&nbsp;&nbsp;&nbsp;<input type='submit' class='btn btn-default' value='Simpan'></td>
					</tr>
					</table>
					</form>
					* 
					*/
					?>
					<?php
				} else {
					?>
					Belum ada barang yang di Retur<br />
					<a href='../aksi.php?module=retur_barang&act=batal'>BATAL</a>
					<?php
				}
				?>

				<?php
				break;
			}
		?>
		<div id="ganti-customer" style="
			display: none;
			position: fixed;
			border: 1px solid #a8cf45;
			bottom: 50px;
			margin-left: 15px;
			background-color: #eef4d2;
			box-shadow: 0px 0px 4px 0px #d2e28b;
			padding: 15px;">
			<form id="form-nomor-kartu">
			<input type="text" class="form-control" id="nomor-kartu-id" name="nomor_kartu" placeholder="Nomor Kartu" autocomplete="off"/><br />
			<input style="float: right" type="submit" id="tombol-login-submit" value="Submit" />
			</form>
		</div>
		<div id="footer" >
			<?php
			?>
			<a class="tombol" href="js_input_retur_barang.php?act=caricustomer" accesskey="r" ><u>R</u>eload</a>
			<a class="tombol" href="#" id="tombol-nomor-kartu" accesskey="k" ><u>K</u>artu Member</a>
		</div>
	</div>
	<script>
		$("#tombol-nomor-kartu").click(function () {
			$("#ganti-customer").toggle(500, function () {
			if ($("#ganti-customer").css('display')=== 'none') {
				console.log('hidden');
			} else {
				console.log("show");
				$("#nomor-kartu-id").val("");
				$("#nomor-kartu-id").focus();
			}
			});

			return false;
		});
		$("#form-nomor-kartu").submit(function () {
			console.log($("#nomor-kartu-id").val());
			var datakirim= {
			'nomor-kartu': $("#nomor-kartu-id").val()
			};
			dataurl= "../aksi.php?module=penjualan_barang&act=nomorkartuinput";
			$.ajax({
			type: "POST",
			url: dataurl,
			data: datakirim,
			success: function (data) {
				console.log(data);
				if (data.sukses) {
					window.location= "js_input_retur_barang.php?act=caricustomer";
				}
			}

			});
			$("#ganti-customer").hide(500);
			return false;
		})
	</script>
</body>
</html>

<?php
		/* CHANGELOG -----------------------------------------------------------

		1.0.1 / 2010-11-22 : Harry Sufehmi		: initial release
		------------------------------------------------------------------------ */
		