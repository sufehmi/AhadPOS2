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

include "../config/config.php";
check_user_access(basename($_SERVER['SCRIPT_NAME']));

$act= $_GET['act'];

switch ($act) {
	default:
		setting();
		break;
}

function setting() {
	$result= mysql_query('select `option`, value, description from config') or die(mysql_error());
	$config= array();

	while ($configItem= mysql_fetch_array($result)) {
		$config[$configItem['option']]= array(
			'value'=>$configItem['value'],
			'description'=>$configItem['description']
		);
	}
	?>

	<h2>Membership Configuration</h2>
	<form method="POST" action="./aksi.php?module=membership&act=simpan">
		<table>
			<tbody>
				<tr>
					<td><?php echo $config['point_value']['description']; ?></td>
					<td><input type="text" class="form-control" name="config[point_value]" value="<?php echo $config['point_value']['value']; ?>"></td>
				</tr>
				<tr>
					<td colspan="2"></td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<input type='submit' class='btn btn-info' value="Simpan">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<?php
	$bulanIndonesia= array(
		1=>'Januari',
		2=>'Februari',
		3=>'Maret',
		4=>'April',
		5=>'Mei',
		6=>'Juni',
		7=>'Juli',
		8=>'Agustus',
		9=>'September',
		10=>'Oktober',
		11=>'November',
		12=>'Desember'
	);

	?>
	<h4>Periode Poin</h4>
	<form method="POST" action="./aksi.php?module=membership&act=tambahperiode">
		<table>
			<tbody>
				<tr>
					<td>Nama:</td>
					<td><input type="text" class="form-control" name="periode[nama]" placeholder="Deskripsi Periode"></td>
					<td>Awal:</td>
					<td>
						<select class="form-control" name="periode[awal]">
							<?php
							foreach ($bulanIndonesia as $nomor=>$nama) {
								?>
								<option value="<?php echo $nomor; ?>"><?php echo $nama; ?></option>
								<?php
							}
							?>
						</select>
					</td>
					<td>Akhir:</td>
					<td>
						<select class="form-control" name="periode[akhir]">
							<?php
							foreach ($bulanIndonesia as $nomor=>$nama) {
								?>
								<option value="<?php echo $nomor; ?>"><?php echo $nama; ?></option>
								<?php
							}
							?>
						</select>
					</td>
					<td colspan="2" align="right">
						<input type='submit' class='btn btn-info' value="Tambah">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<table class="tabel">
		<thead>
			<tr>
				<th>Nama Periode</th>
				<th>Awal</th>
				<th>Akhir</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sql= "SELECT id, nama, awal, akhir FROM periode_poin ORDER BY nama";
			$query= mysql_query($sql);
			while ($dataPeriode= mysql_fetch_array($query, MYSQL_ASSOC)) {
				?>
				<tr>
					<td><?php echo $dataPeriode['nama']; ?></td>
					<td><?php echo $bulanIndonesia[$dataPeriode['awal']]; ?></td>
					<td><?php echo $bulanIndonesia[$dataPeriode['akhir']]; ?></td>
					<td><a href="./aksi.php?module=membership&act=hapusperiode&periodeId=<?php echo $dataPeriode['id']; ?>"><i class="fa fa-times"></i></a></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php
}
