<?php
/* mod_kasir.php ------------------------------------------------------
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


include "../config/config.php";
check_user_access(basename($_SERVER['SCRIPT_NAME']));

switch ($_GET[act]) { //------------------------------------------------------------------------
	default:
		?>
		<br/>
		<h2>Kasir Aktif</h2>
		<table class="tabel">
			<tr><th>Nama</th>
				<th>Workstation</th>
				<th>Sejak</th>
				<th>Kas Awal</th>
			</tr>
			<?php
			$sql= "SELECT k.tglBukaKasir, k.kasAwal, w.namaWorkstation, u.namaUser FROM kasir AS k, user AS u, workstation AS w 
			WHERE k.tglTutupKasir IS NULL AND k.currentWorkstation= w.idWorkstation AND k.idUser= u.idUser";

			$tampil= mysql_query($sql);
			$no= 1;
			while ($r= mysql_fetch_array($tampil)) {
				?>
				<tr class="<?php echo $no % 2=== 0 ? 'alt' : ''; ?>">
					<td><?php echo $r['namaUser']; ?></td>
					<td><?php echo $r['namaWorkstation']; ?></td>
					<td><?php echo date("l, d-F-Y, H:i", strtotime($r['tglBukaKasir'])); ?></td>
					<td><?php echo $r['kasAwal']; ?></td>
				</tr>
				<?php
				$no++;
			}
			?>
		</table>
		<p></p>
		<a class='btn btn-x btn-default' href='javascript:history.go(-1)'><i class='fa fa-arrow-circle-o-left'></i>Kembali</a>
		<?php
		break;



	case "bukakasir": //===========================================================================================================


		echo "<h2>Buka Kasir</h2>";

		// ambil daftar nama kasir
		// idLevelUser : 4= kasir
		$sql= "SELECT namaUser, idUser 
		FROM user 
		WHERE idLevelUser= 4 ORDER BY namaUser ASC";
		$namaKasir= mysql_query($sql);

		// ambil daftar workstation
		$sql= "SELECT idWorkstation, namaWorkstation 
		FROM workstation ORDER BY namaWorkstation ASC";
		$namaWorkstation= mysql_query($sql);

		echo "
	<form method=POST action='./aksi.php?module=buka_kasir&act=input'>
	
	<table>
		<tr>
		<td>Tanggal / Waktu</td>
		<td><input type='text' class='form-control' readonly='readonly' name='tglBukaKasir' value='" . date("Y-m-d H:i:s") . "'></td>
	</tr>
		<tr>
		<td>(k) Pilih Kasir</td>
		<td><select class='form-control' name='idKasir' accesskey='k'>";
		while ($kasir= mysql_fetch_array($namaKasir)) {
			echo "<option value='$kasir[idUser]'>$kasir[namaUser]</option>\n";
		}

		echo "
		</td>
	</tr>
		<tr>
		<td>Pilih Workstation</td>
		<td><select class='form-control' name='idWorkstation'>";
		while ($wks= mysql_fetch_array($namaWorkstation)) {
			echo "<option value='$wks[idWorkstation]'>$wks[namaWorkstation]</option>\n";
		}

		echo "
		</td>
	</tr>
		<tr><td>Uang Kasir</td><td><input type='text' class='form-control' name=kasAwal></td></tr>
		<tr><td colspan=2></td></tr>
		<tr><td colspan=2><input type='submit' class='btn btn-default' value='Simpan'>
								<input type='reset' class='btn btn-default' value='Batal'></td></tr>
	</table>
</form>
	";

		break;



	case "tutupkasir": //===========================================================================================================

		echo "<h2>Tutup Kasir</h2>";

		// ambil nama kasir
		$sql= "SELECT u.namaUser, k.idUser 
			FROM kasir AS k, user AS u 
			WHERE k.tglTutupKasir IS NULL
			AND k.idUser= u.idUser ORDER BY u.namaUser ASC";
		$namaKasir= mysql_query($sql);

		echo "
	<form method=POST action='./media.php?module=kasir&act=tutupkasir2'>
	
	<table>
		<tr>
		<td>(k) Pilih Kasir</td>
		<td><select class='form-control' name='idKasir' accesskey='k'>";
		while ($kasir= mysql_fetch_array($namaKasir)) {
			echo "<option value='$kasir[idUser]'>$kasir[namaUser]</option>\n";
		}

		echo "
		</td></tr>
		<tr><td colspan=2><input type='submit' class='btn btn-default' value='Pilih'></td></tr>
	</table>
</form>
	";

		break;


	case "tutupkasir2": //===========================================================================================================

		echo "<h2>Tutup Kasir</h2>";

		// cari kasAwal
		$sql= "SELECT k.kasAwal,k.tglBukaKasir,u.uname FROM kasir AS k, user AS u 
			WHERE k.idUser= $_POST[idKasir] AND tglTutupKasir IS NULL AND k.idUser= u.idUser";
		$hasil= mysql_query($sql);
		$x= mysql_fetch_array($hasil);

		$kasAwal= $x[kasAwal];
		$tglBukaKasir= $x[tglBukaKasir];
		$tglTutupKasir= date("Y-m-d H:i:s");
		$username= $x[uname];

		// hitung TotalTransaksi
		$totalTransaksi= 0;
		$sql= "SELECT sum(nominal) AS tot_trans FROM transaksijual 
			WHERE idUser=$_POST[idKasir] AND tglTransaksiJual BETWEEN '$tglBukaKasir' AND '$tglTutupKasir'";
		$hasil= mysql_query($sql);
		if ($x= mysql_fetch_array($hasil)) {
			$totalTransaksi= $x[tot_trans];
		};

		// hitung total profit
		$totalProfit= 0;
//		$sql= "SELECT sum(d.hargaJual - b.hargaBeli) AS tot_profit FROM detail_jual AS d, transaksijual AS t, detail_beli AS b	
//			WHERE d.username='$username' AND t.tglTransaksiJual BETWEEN '$tglBukaKasir' AND '$tglTutupKasir'
//				AND d.nomorStruk= t.idTransaksiJual AND d.barcode= b.barcode";
		// Sepertinya script di atas salah, coba ganti dengan ini: (by bambang abu muhammad)
		$sql= "select sum((hargaJual-hargaBeli) * jumBarang) as tot_profit
				from detail_jual d
				join transaksijual as t on t.idTransaksiJual= d.nomorStruk
				where t.tglTransaksiJual BETWEEN '$tglBukaKasir' AND '$tglTutupKasir'
				and username='{$username}'";

		//echo "(".$sql.")<br />";
		$hasil= mysql_query($sql);
		if ($x= mysql_fetch_array($hasil)) {
			$totalProfit= $x[tot_profit];
		};

		// hitung total Retur
		$totalRetur= 0;
		$sql= "SELECT ifnull(sum(nominal),0) AS tot_retur FROM transaksireturjual 
			WHERE idKasir=$_POST[idKasir] AND tglTransaksi BETWEEN '$tglBukaKasir' AND '$tglTutupKasir'";
		$hasil= mysql_query($sql);
		if ($x= mysql_fetch_array($hasil)) {
			$totalRetur= $x[tot_retur];
		}

		//fixme: hitung total transaksi petty cash
		$totalTransaksiKas= 0;

		//fixme: hitung total transaksi debit / credit
		$totalTransaksiKartu= 0;

		// hitung kasSeharusnya 
		$kasSeharusnya= $kasAwal + $totalTransaksi + $totalTransaksiKas - $totalRetur - $totalTransaksiKartu;

		echo "
	<form method=POST action='./aksi.php?module=tutup_kasir&act=input'>
	<input type=hidden name=idKasir value='$_POST[idKasir]'>

	<table>
	<tr>	<td>Tanggal / Waktu</td>
		<td><input type='text' class='form-control' readonly='readonly' name='tglTutupKasir' value='" . date("Y-m-d H:i:s") . "'></td></tr>
	<tr><td>Kas Awal</td><td><input type='text' class='form-control' readonly='readonly' name='kasAwal' value='$kasAwal'></td><tr>
		<tr><td>Total Transaksi</td><td><input type='text' class='form-control' readonly='readonly' name='totalTransaksi' value='$totalTransaksi'></td><tr>
		<tr><td>Total Profit</td><td><input type='text' class='form-control' readonly='readonly' name='totalProfit' value='$totalProfit'></td><tr>
		<tr><td>Total Retur</td><td><input type='text' class='form-control' readonly='readonly' name='totalRetur' value='$totalRetur'></td><tr>
		<tr><td>Total Transaksi Kas</td><td><input type='text' class='form-control' readonly='readonly' name='totalTransaksiKas' value='$totalTransaksiKas'></td><tr>
		<tr><td>Total Transaksi Kartu Kredit/Debit</td><td><input type='text' class='form-control' readonly='readonly' name='totalTransaksiKartu' value='$totalTransaksiKartu'></td><tr>
		<tr><td>Uang Seharusnya</td><td><input type='text' class='form-control' readonly='readonly' name='kasSeharusnya' value='$kasSeharusnya'></td><tr>
		<tr><td>Uang Kasir</td><td><input type='text' class='form-control' name=kasAkhir></td><tr>
		<tr><td colspan=2></td></tr>
		<tr><td colspan=2><input type='submit' class='btn btn-default' value='Simpan'>
								<input type='reset' class='btn btn-default' value='Batal'></td></tr>
	</table>
</form>
";

		break;



	case "tambahdana": //===========================================================================================================
		//fixme : selesaikan modul
		// transaksi disimpan di tabel transaksikasir

		echo "
		<h2>Tambah Dana Kasir</h2>

		Gunakan form ini untuk dropping dana kepada Kasir yang sedang bertugas.
		";

		break;



	case "pettycash": //===========================================================================================================
		//fixme : selesaikan modul
		// transaksi disimpan di tabel transaksikasir

		echo "
		<h2>Petty Cash</h2>

		Gunakan form ini jika ada pengambilan uang Kas dari Kasir yang sedang bertugas.
		";

		break;











		$edit= mysql_query("select * from rak where idRak= '$_GET[id]'");
		$data= mysql_fetch_array($edit);
		echo "<h2>Edit Rak Barang</h2>
			<form method=POST action='./aksi.php?module=rak&act=update' name='editrak'>
			<input type=hidden name='idRak' value='$data[idRak]'>
			<table>
				<tr><td>Edit Rak</td><td><input type='text' class='form-control' name='namaRak' size=30 value='$data[namaRak]'></td></tr>
				<tr><td colspan=2 align=right><input type='submit' class='btn btn-default' value='Simpan'>
								<input type=button value=Batal onclick=self.history.back()></td></tr>
			</table>
			</form>
			<br/>
			<h2>Data Rak Barang</h2>
			<table class=tableku>
			<tr><th>no</th><th>rak</th><th>aksi</th></tr>";
		$tampil= mysql_query("SELECT * from rak");
		$no= 1;
		while ($r= mysql_fetch_array($tampil)) {
			//untuk mewarnai tabel menjadi selang-seling
			if (($no % 2)== 0) {
				$warna= "#EAF0F7";
			}
			else {
				$warna= "#FFFFFF";
			}
			echo "<tr bgcolor=$warna>"; //end warna
			echo "<td class=td>$no</td>
						<td class=td>$r[namaRak]</td>
						<td class=td><a href=?module=rak&act=editrak&id=$r[idRak]>Edit</a>|
								<a href=./aksi.php?module=rak&act=hapus&id=$r[idRak]>Hapus</a>
						</td></tr>";
			$no++;
		}
		echo "</table>";
		break;
}


/* CHANGELOG -----------------------------------------------------------

1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
0.9.1 / 2010-02-27 : Harry Sufehmi		: initial release

------------------------------------------------------------------------ */
?>