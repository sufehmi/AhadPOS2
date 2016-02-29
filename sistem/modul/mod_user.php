<?php
/* mod_user.php ------------------------------------------------------
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


$ambilLevelUser= mysql_query("select * from leveluser");


if ($_GET[module]== 'ganti_password') {

$edit= mysql_query("SELECT * FROM user WHERE idUser='$_SESSION[iduser]'");
$data= mysql_fetch_array($edit);
?>
<form method=POST action=./aksi.php?module=user&act=update&home=1 name='edituser'>
	<input type=hidden name='idUser' value='<?php echo $data['idUser']; ?>'>
	<table>
		<tr><td>Nama User</td><td><input type='text' class='form-control' name='namaUser' size=30 value='<?php echo $data['namaUser']; ?>'></td></tr>
		<?php
		if ($data['idLevelUser']<= 2) {
			/* Edit jabatan muncul jika dan hanya jika level user lebih tinggi/sama dengan admin */
			?>
			<tr>
			<td>Jabatan User</td>
			<td><select class="form-control" name='levelUser'>
					<?php
					while ($level= mysql_fetch_array($ambilLevelUser)) {
						if ($level[idLevelUser]== $data[idLevelUser]) {
						echo "<option value='$level[idLevelUser]' selected>$level[levelUser]</option>";
						} else {
						echo "<option value='$level[idLevelUser]'>$level[levelUser]</option>";
						}
					}
					?>
				</select>
			</td>
			</tr>
			<?php
		}
		?>
		<tr><td>Username</td><td><input type='text' class='form-control' name='uname' size=15 value='<?php echo $data['uname']; ?>'></td></tr>
		<tr><td>Password</td><td><input type='password' class='form-control' name='pass' size=15></td></tr>
		<!--<tr><td>Re-Password</td><td><input type='password' class='form-control' name='repass' size=15></td></tr>-->
		<tr><td colspan=2>*) Apabila password tidak diubah, dikosongkan saja.</td></tr>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr><td colspan=2 align='right'><input type='submit' class='btn btn-default' value=Simpan>&nbsp;&nbsp;&nbsp;
			<input type=button value=Batal onclick=self.history.back()></td></tr>
	</table></form>
<?php
} else {

switch ($_GET[act]) {
	// Tampil User ->menampilkan semua daftar user tanpa paging
	default:
		?>
		<h2>Data User</h2>			
		<form method=POST action='?module=user&act=tambahuser'>
			<input type='submit' class='btn btn-default' value='Tambah User'></form>
		<br/>
		<table class="tabel">
			<tr>
			<th>No</th>
			<th>ID User</th>
			<th>Nama User</th>
			<th>Jabatan User</th>
			<th>aksi</th>
			</tr>
			<?php
			$tampil= mysql_query("SELECT idUser,namaUser,levelUser FROM user u, leveluser lu
						where u.idLevelUser= lu.idLevelUser ORDER BY namaUser");
			$no= 1;
			while ($r= mysql_fetch_array($tampil)) {
			?>
			<tr<?php echo $no % 2=== 0 ? 'class="alt"' : ''; ?>>
				<td class="center"><?php echo $no; ?></td>
				<td class="center"><?php echo $r['idUser']; ?></td>
				<td><?php echo $r['namaUser']; ?></td>
				<td class="center"><?php echo $r['levelUser']; ?></td>
				<td><a href=?module=user&act=edituser&id=<?php echo $r['idUser']; ?>>Edit</a>|
					<a href=./aksi.php?module=user&act=hapus&id=<?php echo $r['idUser']; ?>>Hapus</a>
				</td>
			</tr>
			<?php
			$no++;
			}
			?>
		</table>
		<p>&nbsp;</p>
		<a class='btn btn-x btn-default' href='javascript:history.go(-1)'><i class='fa fa-arrow-circle-o-left'></i>Kembali</a>
		<?php
		break;

	case "tambahuser":
		echo "<h2>Tambah User</h2>
		<form method=POST action='./aksi.php?module=user&act=input' name='tambahuser'>
		<table>
		<tr><td>Nama User</td><td><input type='text' class='form-control' name='namaUser' size=30></td></tr>
		<tr><td>Jabatan User</td>
				<td><select class='form-control' name='levelUser'>
							<option value='0'>- Jabatan User-</option>";
		while ($level= mysql_fetch_array($ambilLevelUser)) {
			echo "<option value='$level[idLevelUser]'>$level[levelUser]</option>";
		}
		echo "</select></td></tr>
		<tr><td>Username</td><td><input type='text' class='form-control' name='uname' size=15></td></tr>
		<tr><td>Password</td><td><input type='password' class='form-control' name='pass' size=15></td></tr>
		<!--<tr><td>Re-Password</td><td><input type='password' class='form-control' name='repass' size=15></td></tr>-->
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr><td colspan=2 align='right'><input type='submit' class='btn btn-default' value=Simpan>&nbsp;&nbsp;&nbsp;
							<input type=button value=Batal onclick=self.history.back()></td></tr>
		</table></form>";
		break;

	case "edituser":
		$edit= mysql_query("SELECT * FROM user WHERE idUser='$_GET[id]'");
		$data= mysql_fetch_array($edit);

		echo "<h2>Edit User</h2>
		<form method=POST action=./aksi.php?module=user&act=update name='edituser'>
		<input type=hidden name='idUser' value='$data[idUser]'>
		<table>
		<tr><td>Nama User</td><td><input type='text' class='form-control' name='namaUser' size=30 value='$data[namaUser]'></td></tr>
		<tr><td>Jabatan User</td>
				<td><select class='form-control' name='levelUser'>
							";
		while ($level= mysql_fetch_array($ambilLevelUser)) {
			if ($level[idLevelUser]== $data[idLevelUser]) {
			echo "<option value='$level[idLevelUser]' selected>$level[levelUser]</option>";
			} else {
			echo "<option value='$level[idLevelUser]'>$level[levelUser]</option>";
			}
		}
		echo "</select></td></tr>
		<tr><td>Username</td><td><input type='text' class='form-control' name='uname' size=15 value='$data[uname]'></td></tr>
		<tr><td>Password</td><td><input type='password' class='form-control' name='pass' size=15></td></tr>
		<!--<tr><td>Re-Password</td><td><input type='password' class='form-control' name='repass' size=15></td></tr>-->
		<tr><td colspan=2>*) Apabila password tidak diubah, dikosongkan saja.</td></tr>
		<tr><td colspan=2>&nbsp;</td></tr>
		<tr><td colspan=2 align='right'><input type='submit' class='btn btn-default' value=Simpan>&nbsp;&nbsp;&nbsp;
							<input type=button value=Batal onclick=self.history.back()></td></tr>
		</table></form>";
		break;
};
}; //if ($_GET[module]== 'ganti_password') {


/* CHANGELOG -----------------------------------------------------------

1.0.2 / 2011-03-04 : Harry Sufehmi		: ganti password untuk semua user
1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
0.6.5			: Gregorius Arief		: initial release

------------------------------------------------------------------------ */
?>
