<?php
/* mod_kategori_barang.php ------------------------------------------------------
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

check_user_access(basename($_SERVER['SCRIPT_NAME']));


switch ($_GET[act]) {
	// Tampil Kategori Barang
	default:
		?>
		<h2>Tambah Kategori Barang</h2>
		<form method=POST action='./aksi.php?module=kategori_barang&act=input'>
			<table>
				<tr><td>Tambah Kategori</td><td><input type='text' class='form-control' name='namaKategoriBarang' size=30></td></tr>
				<tr><td colspan=2 align=right><input type='submit' class='btn btn-default' value='Simpan'>
						<input type='reset' class='btn btn-default' value='Batal'></td></tr>
			</table>
		</form>
		<br/>
		<h2>Data Kategori Barang</h2>
		<table class="tabel">
			<tr>
				<th>No</th>
				<th>Kategori</th>
				<th>Aksi</th>
			</tr>
			<?php
			$tampil= mysql_query("SELECT * from kategori_barang");
			$no= 1;
			while ($r= mysql_fetch_array($tampil)) {
				//untuk mewarnai tabel menjadi selang-seling
				/*
				if (($no % 2)== 0) {
				$warna= "#EAF0F7";
				} else {
				$warna= "#FFFFFF";
				}
				* 
				*/
				// Mewarnai tabel diganti dengan css agar lebih fleksibel
				?>
				<tr class="<?php echo $no % 2=== 0 ? 'alt' : ''; ?>">
					<td><?php echo $no; ?></td>
					<td><?php echo $r['namaKategoriBarang']; ?></td>
					<td><a href=?module=kategori_barang&act=editkategori&id=<?php echo $r['idKategoriBarang']; ?>><i class='fa fa-pencil-square-o'></i> Edit</a> |
						<a href=./aksi.php?module=kategori_barang&act=hapus&id=<?php echo $r['idKategoriBarang']; ?>><i class='fa  fa-times-circle-o'></i> Hapus</a> 
					</td>
				</tr>
				<?php
				$no++;
			}
			echo "</table>
				<p></p>
				<a class='btn btn-sm btn-default' href='javascript:history.go(-1)'><i class='fa fa-arrow-circle-o-left'></i>Kembali</a>";
			break;

		case "editkategori":
			$edit= mysql_query("select * from kategori_barang where idKategoriBarang= '$_GET[id]'");
			$data= mysql_fetch_array($edit);
			echo "<h2>Edit Kategori Barang</h2>
			<form method=POST action='./aksi.php?module=kategori_barang&act=update' name='editkategori'>
			<input type=hidden name='idKategoriBarang' value='$data[idKategoriBarang]'>
			<table>
				<tr><td>Edit Kategori</td><td><input type='text' class='form-control' name='namaKategoriBarang' size=30 value='$data[namaKategoriBarang]'></td></tr>
				<tr><td colspan=2 align=right><input type='submit' class='btn btn-default' value='Simpan'>
								<input type='reset' class='btn btn-default' value=Batal onclick=self.history.back()></td></tr>
			</table>
			</form>
			<br/>
			<h2>Data Kategori Barang</h2>";
			?>
			<table class="tabel">
				<tr>
					<th>No</th>
					<th>Kategori</th>
					<th>Aksi</th>
				</tr>
				<?php
				$tampil= mysql_query("SELECT * from kategori_barang");
				$no= 1;
				while ($r= mysql_fetch_array($tampil)) :
					?>

					<tr <?php echo $no % 2=== 0 ? 'class="alt"' : ''; ?>>
						<td><?php echo $no; ?></td>
						<td><?php echo $r['namaKategoriBarang']; ?></td>
						<td class=td><a href=?module=kategori_barang&act=editkategori&id=<?php echo $r['idKategoriBarang']; ?>><i class='fa fa-pencil-square-o'></i> Edit</a> |
							<a href=./aksi.php?module=kategori_barang&act=hapus&id=<?php echo $r['idKategoriBarang']; ?>><i class='fa  fa-times-circle-o'></i> Hapus</a> 
						</td>
					</tr>
					<?php
					$no++;
				endwhile;
				?>
			</table>
			<?php
			break;
	}


	/* CHANGELOG -----------------------------------------------------------

	1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
	0.6.5			: Gregorius Arief		: initial release

	------------------------------------------------------------------------ */
	?>
