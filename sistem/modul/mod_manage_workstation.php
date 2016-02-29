<?php
/* mod_manage_workstation.php ----------------------------------------
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



include "../config/config.php";
check_user_access(basename($_SERVER['SCRIPT_NAME']));

session_start();
if (empty($_SESSION[namauser]) AND empty($_SESSION[passuser])) {
	echo "<link href='../css/style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
	echo "<a href=index.php><b>LOGIN</b></a></center>";
} else {

	if (!isset($_SESSION[idCustomer])) {
		findCustomer($_POST[idCustomer]);
	}


	//HS javascript untuk menampilkan popup
	?>	


	<SCRIPT TYPE="text/javascript">


		function CalculatePrinterCommands() {
			var ip_address = document.getElementById("workstation_address").value;
			var printer_type = document.getElementById("printer_type").value;

			if (printer_type == 'rlpr') {

				printer_commands = '-H ' + ip_address + ' -P printer' + ip_address;
				document.getElementById("printer_commands").value = printer_commands;
			}
		}

	</SCRIPT>


	<?php
	switch ($_GET[act]) {

		default:
			?>
			<h2>Data Workstation</h2>            
			<form method=POST action='?module=workstation&act=tambahworkstation'>
				<input type=submit value='Tambah Workstation'></form>
			<br/>
			<table class="tabel">
				<tr>
					<th>No</th>
					<th>ID Wks</th>
					<th>Nama Workstation</th>
					<th>Keterangan</th>
					<th>aksi</th>
				</tr>
				<?php
				$tampil = mysql_query("SELECT idWorkstation,namaWorkstation,keterangan FROM workstation ORDER BY namaWorkstation");

				$no = 1;
				while ($r = mysql_fetch_array($tampil)) {
					?>
					<tr <?php echo $no % 2 === 0 ? 'class="alt"' : ''; ?>>
						<td class="center"><?php echo $no; ?></td>
						<td class="center"><?php echo $r['idWorkstation']; ?></td>
						<td><?php echo $r['namaWorkstation']; ?></td>
						<td class="center"><?php echo $r['keterangan']; ?></td>
						<td><a href=?module=workstation&act=editworkstation&id=<?php echo $r['idWorkstation']; ?>>Edit</a> |
							Ha<a href=./aksi.php?module=workstation&act=hapus&id=<?php echo $r['idWorkstation']; ?>>pus</a>
						</td>
					</tr>
					<?php
					$no++;
				}
				?>
			</table>
			<p>&nbsp;</p>
			<a href=javascript:history.go(-1)><< Kembali</a>
			<?php
			break;



		case "tambahworkstation": // =============================================================================================================
			echo "<h2>Tambah Workstation</h2>
          <form method=POST action='./aksi.php?module=workstation&act=input' name='tambahwks'>
          <table>
          <tr><td>Nama Workstation</td><td> : <input type=text name='namaWorkstation' size=30></td></tr>
          <tr><td>Keterangan</td><td> : <input type=text name='keterangan' size=30></td></tr>
          <tr><td>IP address</td><td> : <input type=text name='workstation_address' id='workstation_address' size=30 value='10.1.1.1'></td></tr>

	<tr><td>Jenis Printer</td><td> : <select name='printer_type' id='printer_type' onBlur='CalculatePrinterCommands()'>
		<option value='pdf' selected>PDF : paling kompatibel</option>
		<option value='rlpr'>Remote LPR : khusus untuk komputer Unix / Linux</option>
		</select>
	</td></tr>

          <tr><td>Printer Commands<br />(auto-generated)</td><td> : <input type=text name='printer_commands' id='printer_commands' size=30 readonly></td></tr>
	";

			echo "
          <tr><td colspan=2>&nbsp;</td></tr>
          <tr><td colspan=2 align='right'><input type=submit value=Simpan>&nbsp;&nbsp;&nbsp;
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
          </table></form>
		";
			break;



		case "editworkstation": // ======================================================================================================================
			$edit = mysql_query("SELECT * FROM workstation WHERE idWorkstation='$_GET[id]'");
			$data = mysql_fetch_array($edit);
			?>
			<h2>Edit Workstation</h2>
			<form method=POST action=./aksi.php?module=workstation&act=update name='editworkstation'>
				<input type=hidden name='idWorkstation' value='<?php echo $data['idWorkstation']; ?>'>

				<table>
					<tr><td>Nama Workstation</td><td> : <input type=text name='namaWorkstation' value='<?php echo $data['namaWorkstation']; ?>' size=30></td></tr>
					<tr><td>Keterangan</td><td> : <input type=text name='keterangan' value='<?php echo $data['keterangan']; ?>' size=30></td></tr>
					<tr><td>IP address</td><td> : <input type=text name='workstation_address' id='workstation_address' value='<?php echo $data['workstation_address']; ?>' size=30></td></tr>
					<tr><td>Jenis Printer</td><td> : <select name='printer_type' id='printer_type' onBlur='CalculatePrinterCommands()'>
								<option value='pdf' <?php echo $data['printer_type'] === 'pdf' ? 'selected' : ''; ?>>PDF : paling kompatibel</option>
								<option value='rlpr' <?php echo $data['printer_type'] === 'rlpr' ? 'selected' : ''; ?>>Remote LPR : khusus untuk komputer Unix / Linux</option>
								<option value='text' <?php echo $data['printer_type'] === 'text' ? 'selected' : ''; ?>>Text/Plain</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Otomatis Buka<br />CashDrawer (*rlpr)</td>
						<td> : <select name="cashdrawer_command">
								<option value="1" <?php echo $data['send_cdopen_commands'] === '1' ? 'selected' : ''; ?>>Ya</option>
								<option value="0" <?php echo $data['send_cdopen_commands'] === '0' ? 'selected' : ''; ?>>Tidak</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Otomatis potong<br />kertas (*rlpr)</td>
						<td> : <select name="autocut_command">
								<option value="1" <?php echo $data['send_autocut_commands'] === '1' ? 'selected' : ''; ?>>Ya</option>
								<option value="0" <?php echo $data['send_autocut_commands'] === '0' ? 'selected' : ''; ?>>Tidak</option>
							</select>
						</td>
					</tr>
					<tr><td>Printer Commands<br />(auto-generated)</td><td> : <input type=text name='printer_commands' id='printer_commands' value='<?php echo $data['printer_commands']; ?>' size=30 readonly></td></tr>

					<tr><td colspan=2>&nbsp;</td></tr>
					<tr><td colspan=2 align='right'><input type=submit value=Simpan>&nbsp;&nbsp;&nbsp;
							<input type=button value=Batal onclick=self.history.back()></td></tr>
				</table></form>
			<?php
			break;
	}
} // if (empty($_SESSION[namauser]) AND empty($_SESSION[passuser]))



/* CHANGELOG -----------------------------------------------------------

  1.0.1 / 2010-06-03 : Harry Sufehmi		: initial release

  ------------------------------------------------------------------------ */
?>
