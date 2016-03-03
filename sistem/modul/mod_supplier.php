<?php
/* mod_supplier.php ------------------------------------------------------
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

require_once($_SERVER["DOCUMENT_ROOT"].'/define.php');

check_user_access(basename($_SERVER['SCRIPT_NAME']));


switch ($_GET[act]) {
	// Tampil supplier -> menampilkan semua daftar supplier tanpa paging
	default:
		?>
		<h2>Data Supplier</h2>
		<form method='post' action='?module=supplier&act=tambahsupplier'>
			<input type='submit' class='btn btn-default' value='Tambah Supplier'></form>
		<br/>
		<table class="tabel">
			<tr>
				<th>No</th>
				<th>Nama Supplier</th>
				<th>Alamat Supplier</th>
				<th>No.Telp Supplier</th>
				<th>aksi</th>
			</tr>
			<?php
			$tampil=mysql_query("select idSupplier, namaSupplier, alamatSupplier, telpSupplier from supplier");
			$no=1;
			while ($r=mysql_fetch_array($tampil)) {
				?>
				<tr <?php echo $no % 2 === 0 ? 'class="alt"' : ''; ?>>
					<td class="right"><?php echo $no; ?></td>
					<td><?php echo $r['namaSupplier']; ?></td>
					<td class="center"><?php echo $r['alamatSupplier']; ?></td>
					<td class="center"><?php echo $r['telpSupplier']; ?></td>
					<td><a href=?module=supplier&act=editsupplier&id=<?php echo $r['idSupplier']; ?>>Edit</a> |
						<a href=./aksi.php?module=supplier&act=hapus&id=<?php echo $r['idSupplier']; ?>>Hapus</a>
					</td></tr>
				<?php
				$no++;
			}
			?>
		</table>
		<p>&nbsp;</p>
		<a href='javascript:history.go(-1)'><i class='fa fa-arrow-left'></i> Kembali</a>
		<?php
		break;

	case "tambahsupplier":
		echo "<h2>Tambah Supplier</h2>
		<form method='post' action='./aksi.php?module=supplier&act=input' name='tambahsupplier'>
		<table>		
		<tr><td>Nama Supplier</td><td><input type='text' class='form-control' class='form-control' name='namaSupplier' size=40></td></tr>
		<tr><td>Alamat Supplier</td><td><textarea name='alamatSupplier' rows='2' cols='35'></textarea></td></tr>
		<tr><td>Telp Supplier</td><td><input type='text' class='form-control' class='form-control' name='telpSupplier' size=15></td></tr>
		<tr><td>Keterangan</td><td><textarea name='Keterangan' rows='4' cols='35'></textarea></td></tr>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr><td colspan=2 align='right'><input type='submit' class='btn btn-default' value=Simpan>&nbsp;
							<input type='reset' class='btn btn-default' value=Batal onclick=self.history.back()></td></tr>
		</table></form>";
		break;

	case "editsupplier":
		$edit=mysql_query("SELECT * FROM supplier WHERE idSupplier='$_GET[id]'");
		$data=mysql_fetch_array($edit);

		echo "<h2>Edit Supplier</h2>
		<form method='post' action='./aksi.php?module=supplier&act=update' name='editsupplier'>
		<input type=hidden name='idSupplier' value='$data[idSupplier]'>
		<table>
		<tr><td>Nama Supplier</td><td><input type='text' class='form-control' class='form-control' name='namaSupplier' size=40 value='$data[namaSupplier]'></td></tr>
		<tr><td>Interval</td><td><input type='text' class='form-control' class='form-control' name='interval' size=5 value='$data[interval]'> hari<br />(selang waktu / periode kunjungan)</td></tr>
		<tr><td>Alamat Supplier</td><td><textarea name='alamatSupplier' rows='2' cols='35'>$data[alamatSupplier]</textarea></td></tr>
		<tr><td>Telp Supplier</td><td><input type='text' class='form-control' class='form-control' name='telpSupplier' size=15 value='$data[telpSupplier]'></td></tr>
		<tr><td>Keterangan</td><td><textarea name='Keterangan' rows='4' cols='35'>$data[Keterangan]</textarea></td></tr>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr><td colspan=2 align='right'><input type='submit' class='btn btn-default' value=Simpan>&nbsp;
							<input type='reset' class='btn btn-default' value=Batal onclick=self.history.back()></td></tr>
		</table></form>";
		break;
}


/* CHANGELOG -----------------------------------------------------------

1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
0.6.5			: Gregorius Arief		: initial release

------------------------------------------------------------------------ */
?>
