<?php
/* mod_customer.php ------------------------------------------------------
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
	// Tampil customer -> menampilkan semua daftar customer tanpa paging
	default:
		?>
		<h2>Data Customer</h2>
		<form method='post' action='?module=customer&act=tambahcustomer'>
			<input type='submit' class='btn btn-default' value='Tambah Customer'></form>
		<br/>
		<table class="tabel">
			<tr>
				<th>No</th>
				<th>Nama Customer</th>
				<th>Alamat Customer</th>
				<th>No.Telp Customer</th>
				<th colspan="2">Diskon</th>
				<th>aksi</th>
			</tr>
			<?php
			$tampil=mysql_query("select idCustomer, namaCustomer, alamatCustomer, telpCustomer, diskon_persen, diskon_rupiah from customer");
			$no=1;
			while ($r=mysql_fetch_array($tampil)) {
				?>
				<tr <?php echo $no % 2 === 0 ? 'class="alt"' : ''; ?>>
					<td class="right"><?php echo $no; ?></td>
					<td><?php echo $r['namaCustomer']; ?></td>
					<td class="center"><?php echo $r['alamatCustomer']; ?></td>
					<td class="center"><?php echo $r['telpCustomer']; ?></td>
					<td class="right"><?php echo $r['diskon_persen']; ?>%</td>
					<td class="right"><?php echo number_format($r['diskon_rupiah'], 2, ',', '.'); ?></td>
					<td><a href=?module=customer&act=editcustomer&id=<?php echo $r['idCustomer']; ?>>Edit</a> |
						<a href=./aksi.php?module=customer&act=hapus&id=<?php echo $r['idCustomer']; ?>>Hapus</a>
					</td>
				</tr>
				<?php
				$no++;
			}
			?>
		</table>
		<p>&nbsp;</p>
		<a href='javascript:history.go(-1)'><i class='fa fa-arrow-left'></i> Kembali</a>
		<?php
		break;

	case "tambahcustomer":
		echo "<h2>Tambah Customer</h2>
		<form method='post' action='./aksi.php?module=customer&act=input' name='tambahcustomer'>
		<table>
		<tr><td>Nama Customer</td><td> <input type='text' class='form-control' class='form-control' name='namaCustomer' size=40></td></tr>
		<tr><td>Alamat Customer</td><td> <textarea name='alamatCustomer' rows='2' cols='35'></textarea></td></tr>
		<tr><td>Telp Customer</td><td> <input type='text' class='form-control' class='form-control' name='telpCustomer' size=15></td></tr>
		<tr><td>Keterangan</td><td> <textarea name='keterangan' rows='4' cols='35'></textarea></td></tr>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr><td colspan=2 align='right'><input type='submit' class='btn btn-default' value=Simpan>&nbsp;
							<input type='reset' class='btn btn-default' value=Batal onclick=self.history.back()></td></tr>
		</table></form>";
		break;

	case "editcustomer":
		$edit=mysql_query("SELECT * FROM customer WHERE idCustomer='$_GET[id]'");
		$data=mysql_fetch_array($edit);

		echo "<h2>Edit Customer</h2>
		<form method='post' action=./aksi.php?module=customer&act=update name='editcustomer'>
		<input type=hidden name='idCustomer' value='$data[idCustomer]'>
		<table>
		<tr><td>Nama Customer</td><td> <input type='text' class='form-control' class='form-control' name='namaCustomer' size=40 value='$data[namaCustomer]'></td></tr>
		<tr><td>Alamat Customer</td><td> <textarea name='alamatCustomer' rows='2' cols='35'>$data[alamatCustomer]</textarea></td></tr>
		<tr><td>Telp Customer</td><td> <input type='text' class='form-control' class='form-control' name='telpCustomer' size=15 value='$data[telpCustomer]'></td></tr>
		<tr><td>Keterangan</td><td> <textarea name='keterangan' rows='4' cols='35'>$data[keterangan]</textarea></td></tr>
			<tr><td>Diskon (%)</td><td> <input type='text' class='form-control' class='form-control' name='diskon_persen' value='$data[diskon_persen]' /></td></tr>
		<tr><td>Diskon (Rp)</td><td> <input type='text' class='form-control' class='form-control' name='diskon_rupiah' value='$data[diskon_rupiah]' /></td></tr>
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
