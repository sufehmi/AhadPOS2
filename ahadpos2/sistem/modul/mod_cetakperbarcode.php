<?php

include "../../config/config.php";

switch ($_GET[act]) {
	default:

		echo "Data tidak ditemukan";

		break;

	case "printperbarcode":

		$cari = mysql_query("SELECT * FROM tmp_cetak_label_perbarcode");

		if ($_POST[idTmpBarang] == '') {
			echo '<center>Data tidak ditemukan</center>';
		} else {


			$lebar_label = 200;
			$tinggi_label = 112;
			$label_per_baris = 3;
			$baris_per_halaman = 7;

			// Layout 
			// 0 = 3 mm (default) / 112px;
			// 1 = 3,3 mm
			if ($_POST['layout'] == '1') {
				$tinggi_label = 120;
			}


			$total = $_POST[total];
			$baris = 1;
			$kolom = 1;
			echo "<div style=\"float:none\">";

			for ($i = 1; $i <= $total; $i++) {

				$r = mysql_fetch_array($cari);

				$clear = "";
				// cek posisi saat ini
				if ($kolom > $label_per_baris) {
					$kolom = 1;
					$baris++;
					$clear = " clear:left; "; //echo "</div><div style=\"float:none\">"; // ganti baris
				};
				if ($baris > $baris_per_halaman) {
					$baris = 1;
					echo '<p style="page-break-after: always" />';
				};

				$namaBarang = $r[tmpNama];
				// jika terlalu panjang nama barangnya 
				if (strlen($namaBarang) > 15) {
					// bikin menjadi 2 baris
					$namaBarang = substr($namaBarang, 0, 15) .
							"</p><p style=\"line-height:0px; letter-spacing:-2px; text-align:center; font-family:Arial; font-size:12pt; font-weight:normal; text-transform:uppercase;  \">" . substr($namaBarang, 15);
				};

				// cetak label
				echo "\n 

				<div style=\"border: thin solid #000000; $clear float:left; margin-right:10px; margin-bottom:10px; width:" . $lebar_label . "px; height:" . $tinggi_label . "px\">
				<p style=\"line-height:0px; letter-spacing:-2px; text-align:center; font-family:Arial; font-size:12pt; font-weight:normal; text-transform:uppercase;  \">
					$namaBarang	
				</p>
				<p style=\"line-height:0px; letter-spacing:+2px; text-align:center; font-family:Arial; font-size:26pt; \">
					" . number_format($r[tmpHargaJual], 0, ',', '.') . "	</p>
				<p style=\"line-height:0px; text-align:left; font-family:Arial; font-size:6pt; \">
					$r[tmpBarcode] - $r[tmpIdBarang]
				</div>
			";

				$kolom++;
			}
		}
		echo "</div>";

		break;
}
?>