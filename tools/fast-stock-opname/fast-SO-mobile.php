<?php
/* fast-SO-mobile.php ------------------------------------------------------
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

session_start();
include "../../config/config.php";

//$username = $_SESSION['uname'];
// $username = 'so';

$_SESSION['nomorraks'] = $_GET['nomorrak'];
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Mobile SO - Ahad Mart</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<!-- Bootstrap -->
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/bootstrap-responsive.css" rel="stylesheet">

		<script src="../../js/jquery-1.9.1.min.js" ></script>
		<script type="text/javascript" >

		</script>
	</head>

	<body>

		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand">Mobile SO</a>
				</div>
			</div>
		</div>
		<?php
// Create connection
//$con=mysql_connect("localhost","root","","ahadpos");
//$db=mysql_select_db('ahadpos');
// Check connection
		?>



		<?php
// simpan data Fast SO
		if ($_GET["jmlbarang"]) { // ================================================================================
			echo "	<form id='testForm' method='get' action='".$_SERVER['PHP_SELF']."'>";

			if (empty($_GET["jumlahtercatat"])) {
				$_GET["jumlahtercatat"] = 0;
			}

			// komplain jika salah input angka barcode ke jumlahTercatat
			if ($_GET["jmlbarang"] > 2000) {
				echo "<div class='container'><div class='well'><h4>Salah : Input Barcode sebagai Jumlah Barang. <br />
			<a href='".$_SERVER["PHP_SELF"]."?nomorrak=".$_GET["nomorrak"]."&username=".$_GET["username"]."'>
			Klik disini untuk mengulang kembali</a><h4></div></div>";
				exit;
			};

			//$selisih 	= ($_GET["jmlbarang"] - $_GET["jumlahtercatat"]);
			$selisih = $_GET["jmlbarang"];

			// cari apakah sudah ada
			$sql = "SELECT sum(selisih) AS total FROM fast_stock_opname WHERE barcode='".$_GET["barcode1"]."' AND approved=0";
			$hasil = mysql_query($sql);
			$x = mysql_fetch_array($hasil);

			if ($x['total'] > 0) {
				$selisih = $selisih + $x['total'];
				$sql = "UPDATE fast_stock_opname SET selisih=$selisih, jmlTercatat=$selisih WHERE barcode='".$_GET["barcode1"]."' AND approved=0";
				$hasil = mysql_query($sql);
			} else {
				// simpan di database
				$sql = "INSERT INTO fast_stock_opname (barcode, idRak, jmlTercatat, selisih, tanggalSO, username, namaBarang)
			VALUES ('".$_GET["barcode1"]."',".$_GET["nomorrak"].",".$_GET["jmlbarang"].",
				".$selisih.",'".date("Y-m-d")."', '".$_GET["username"]."',
				'".$_GET["namaBarang"]."')";
				$hasil = mysql_query($sql);
			};

			$showdiv = $_GET["divAwal"];

			header("refresh:0;url=redirect.php");
		}



// minta jumlah barang
		elseif ($_GET["barcode"]) { // ===================================================================================
			echo "	<form id='testForm' method='get' action='".$_SERVER['PHP_SELF']."'>";

			$sql = "SELECT namaBarang FROM barang WHERE barcode='".$_GET["barcode"]."'";
			$hasil = mysql_query($sql);
			$x = mysql_fetch_array($hasil) or die("<div class='container'><div class='well'><h3>Barang tidak ditemukan<a href='".$_SERVER["PHP_SELF"]."?nomorrak=".$_GET["nomorrak"]."&username=".$_GET["username"]."'>
			Klik disini untuk mengulang kembali</a></h3></div></div>");
			$namaBarang = $x['namaBarang'];

			if (mysql_num_rows($hasil) < 1) {
				echo "Salah input barcode. [<a href='".$_SERVER["PHP_SELF"]."?nomorrak=".$_GET["nomorrak"]."&username=".$_GET["username"]."'> KLIK DISINI </a>]";
				exit;
			}

			$sql = "SELECT sum(selisih) AS total FROM fast_stock_opname WHERE barcode='".$_GET["barcode"]."' AND approved=0";
			$hasil = mysql_query($sql);
			$x = mysql_fetch_array($hasil);

			$total = 0;
			if ($x['total'] > 0) {
				$total = $x['total'];
			}
			?>

			<?php echo "
			<div class='container'>
			<div class='well' align='center'>
			<table>
			<tr>
				<td><b>".$namaBarang."</b></td>
				<td>( <a href='".$_SERVER["PHP_SELF"]."?nomorrak=".$_GET["nomorrak"]."&username=".$_GET["username"]."'>
					klik disini untuk membatalkan</a> karena beda barang)</td>
			</tr>

			<tr>
				<td></td>
				<td>( total jumlah barang tercatat pada SO ini : <b>$total</b> )</td>
			</tr>

			</table>
			";
			?>



			<h2> Masukkan jumlah barang saat ini </h2>
			<table border='0' style='align:center'>
				<tr><td>
						<?php
						if (substr($_SERVER["HTTP_USER_AGENT"], 0, 19) == "Mozilla/5.0 (iPhone") {
							echo '		<input type="text" id="bacadisini"	name="jmlbarang" style="height:30px"/>';
						} else {
							echo '		<input type="number" id="bacadisini" autofocus="autofocus" name="jmlbarang" style="height:30px"/>';
						};
						?><input type="hidden" name="barcode1" value="<?php echo $_GET["barcode"]; ?>" />
						<input type="hidden" name="nomorrak" value="<?php echo $_GET["nomorrak"]; ?>" />
						<input type="hidden" name="username" value="<?php echo $_GET["username"]; ?>" />

						<input type="hidden" name="namaBarang" 		value="<?php echo $namaBarang; ?>" />
					</td></tr>
				<tr><td>
						<div align="right">
							<p><input type="submit" name=submit class="btn btn-primary" /></p>
						</div>
					</td></tr>

				<input type="hidden" name="barcode1" value="<?php echo $_GET["barcode"]; ?>" />
				<input type="hidden" name="nomorrak" value="<?php echo $_GET["nomorrak"]; ?>" />
				<input type="hidden" name="username" value="<?php echo $_GET["username"]; ?>" />

				<input type="hidden" name="namaBarang" 		value="<?php echo $namaBarang; ?>" />
			</div>
		</div>

		<?php
	}

	if ($_GET["caribarang1"]) { // ===================================================================================
		$sql = "SELECT namaBarang, jumBarang, hargaJual, barcode
                        FROM barang AS b
						WHERE namaBarang LIKE '%".$_GET['caribarang']."%'
                        ORDER BY namaBarang ASC";
		$cari = mysql_query($sql);

		echo "
    	<div class='container'>
		<div class='well' align='center'>
    	<table border='0' style='align:center' class='table table-condensed table-hover' >
        <tr>
			<th>No</th>
			<th>Barcode</th>
			<th>Nama Barang</th>
			<th>Harga Jual</th>
		</tr>";

		$no = 1;
		while ($r = mysql_fetch_array($cari)) {
			//untuk mewarnai tabel menjadi selang-seling
			if (($no % 2) == 0) {
				$warna = "#EAF0F7";
			} else {
				$warna = "#FFFFFF";
			}
			echo "<tr bgcolor=$warna>"; //end warna

			$linkurl = "?noscan=1&username=".$_GET["username"]."&nomorrak=".$_GET["nomorrak"]."&barcode=".$r["barcode"]."";

			echo "<td>$no</td>
         	<td>$r[barcode]<br> <a href='$linkurl' class='btn btn-primary btn-small'>PILIH</a></td>
         	<td>$r[namaBarang]</td>
         	<td>$r[hargaJual]</td>

         	</tr>";
			$no++;
		}

		if (mysql_num_rows($cari) < 1) {
			echo "
	<tr><td colspan='4'><h3>Barang tidak ditemukan<a href='".$_SERVER["PHP_SELF"]."?nomorrak=".$_GET["nomorrak"]."&username=".$_GET["username"]."'>
			Klik disini untuk mengulang kembali</a></h3></td></tr>
    </table>
    </div>
    </div>";
		} else {
			echo "</table>
    </div>
    </div>";
		}
	}


// minta barcode

	if ($_GET["nomorrak"]) { // ===================================================================================
		if (!$_GET["noscan"]) {
			// ref: https://code.google.com/p/zxing/wiki/ScanningFromWebPages

			echo "
		<div class='container' name='divAwal'>
		<div class='well' align='center'>

			<a class='btn btn-primary' href ='zxing://scan/?ret=".urlencode("http://".$_SERVER["SERVER_NAME"].$_SERVER['PHP_SELF']."?barcode={CODE}&nomorrak=".$_GET["nomorrak"]."&username=".$_GET["username"]."&noscan=1")."'> Scan Barcode</a>
			<form method=get action='".$_SERVER['PHP_SELF']."'>
				<h2>Masukkan Barcode</h2>
				<table border='0' style='align:center'>
				<tr><td>";

			//if (substr($_SERVER["HTTP_USER_AGENT"], 0, 19) == "Mozilla/5.0 (iPhone") {
			echo "<input type=text name=barcode autofocus='autofocus' style='height:30px'> <div align='right'><input type=submit value='input' class='btn btn-primary'>";
			//} else {
			//echo "<input type=number name=barcode style='height:30px'> <div align='right'><input type=submit value='input' class='btn btn-primary' >";
			//};
			echo "
										<input type=hidden name=noscan value=1>
										<input type=hidden name=nomorrak value='".$_GET["nomorrak"]."'>
										<input type=hidden name=username value='".$_GET["username"]."'>
					</form>
				</td></tr>

				<tr><td>
				<h2>Cari Barang</h2>
					<form method=get action='".$_SERVER['PHP_SELF']."'>
					<input type=text name=caribarang style='height:30px'>
					<div align='right'><input type=submit value='input' class='btn btn-primary' >
										<input type=hidden name=noscan value=1>
										<input type=hidden name=caribarang1 value=1>
										<input type=hidden name=nomorrak value='".$_GET["nomorrak"]."'>
										<input type=hidden name=username value='".$_GET["username"]."'>
					</form>
				</td>
				</tr>

			</table>
			</div>
			</div>

			";
		};
	} elseif (!$_GET["noscan"]) { //  --------------------------------------------------------------------------------------
		echo "	<form id='testForm' method='get' action='".$_SERVER['PHP_SELF']."'>";
		?>

		<div class="container">
			<div class="well" align="center">

				<h2>Masukkan Nomor Rak</h2>
				<table border="0">
					<tr><td>
							<?php
							//if (substr($_SERVER["HTTP_USER_AGENT"], 0, 19) == "Mozilla/5.0 (iPhone") {
							//echo '<p><input type="text" id="bacadisini" name="nomorrak" style="height:30px"/></p>';
							//} else {
							//echo '<p><input type="number" id="bacadisini" name="nomorrak" style="height:30px"/></p>';
							//};
							$sql = "select idRak, namaRak from rak order by namaRak";
							$raks = mysql_query($sql) or die('Gagal ambil data rak');
							?>
							<select name="nomorrak">
								<?php
								while ($rak = mysql_fetch_array($raks)) {
									?>
									<option value="<?php echo $rak['idRak']; ?>"><?php echo $rak['namaRak']; ?></option>
									<?php
								}
								?>
							</select>
						</td></tr>
					<tr><td>
							<div align="right">
								<p><input type="submit" class="btn btn-primary" name="submit"></p>
							</div>
					<tr><td>
				</table>

				<input type="hidden" name="username" value="<?php echo $username; ?>" />
			</div>
		</div>

		<?php
	}
	?>


</form>

<p id="writeroot"></p>

</body></html><?php
/* CHANGELOG -----------------------------------------------------------

  1.0.1 / 2013-07-01 : Harry Sufehmi		: initial release

  ------------------------------------------------------------------------ */
?>
