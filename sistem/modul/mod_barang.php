<?php
/* mod_barang.php ------------------------------------------------------
  version: 1.5.0

  Part of AhadPOS : http://ahadpos.com
  License: GPL v2
  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
  http://vlsm.org/etc/gpl-unofficial.id.html

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License v2 (links provided above) for more details.
  ---------------------------------------------------------------- */

if (basename($_SERVER['SCRIPT_NAME']) <> 'media.php') {
    // when this module is called directly, we'll need to initialize the session properly
    session_start();
    include "../../config/config.php";
    include "function.php";
};

check_user_access(basename($_SERVER['SCRIPT_NAME']));




//HS javascript untuk menampilkan popup
?>

<SCRIPT TYPE="text/javascript">
    <!--
function popupform(myform, windowname)
    {
        if (!window.focus)
            return true;
        window.open('', windowname, 'type=fullWindow,fullscreen,scrollbars=yes');
        myform.target = windowname;
        return true;
    }
//-->
</SCRIPT>

<?php
$ambilSupplier = mysql_query("select * from supplier order by namaSupplier");
$ambilRak = mysql_query("select * from rak");
$ambilKategoriBarang = mysql_query("select * from kategori_barang");
$ambilSatuanBarang = mysql_query("select * from satuan_barang");
?>
<SCRIPT TYPE="text/javascript">

    function popupform(myform, windowname)
    {
        if (!window.focus)
            return true;
        window.open('', windowname, 'type=fullWindow,fullscreen,scrollbars=yes,menubar=yes');
        myform.target = windowname;
        return true;
    }

</SCRIPT>
<?php
switch ($_GET['act']) {
    // Tampil barang
    default:  // ========================================================================================================================
        ?>
        <h2>Data barang</h2>

        <?php
//	<div style=\"float:left\">
//          <form method=POST action='?module=barang&act=tambahbarang'>
//          <input type=submit accesskey='t' value='(t) Tambah Barang'></form>
//	</div>
//	<div style=\"float:left\">
//          <form method=POST action='?module=barang&act=caribarang1'>
//          <input type=submit accesskey='c' value='(c) Cari Barang'></form>
//	</div>
//	<div style=\"float:left\">
//          <form method=POST action='?module=barang&act=cetaklabel1'>
//          <input type=submit accesskey='l' value='(l) Cetak Label'></form>
//	</div>
        ?>

        <form class="inline" method=POST action='?module=barang&act=cetakperbarcode'>
            <input type=submit value='Cetak Label / barcode' >
        </form>

        <?php
//	<div style=\"float:left\">
//          <form method=POST action='?module=barang&act=cetakbarang1'>
//          <input type=submit accesskey='b' value='(b) Cetak Stock Barang'></form>
//	</div>
//	<br /><br />
//
//	<div style=\"float:left\">
//          <form method=POST action='?module=barang&act=cetakSO'>
//          <input type=submit accesskey='s' value='(s) Cetak Stock Opname' ></form>
//	</div>
//	<div style=\"float:left\">
//          <form method=POST action='?module=barang&act=inputSO'>
//          <input type=submit accesskey='o' value='(o) Input Stock Opname' ></form>
//	</div>
//	<div style=\"float:left\">
//          <form method=POST action='?module=barang&act=inputrak' onSubmit=\"popupform(this, 'inputrak')\" >
//          <input type=submit accesskey='i' value='(i) Input Cepat Rak' ></form>
//	</div>
//			<br /><br />
//
//	<div style=\"float:left\">
//          <form method=POST action='../tools/fast-stock-opname/fast-SO.php'>
//          <input type=submit value='Input Fast SO' ></form>
//	</div>
//	<div style=\"float:left\">
//          <form method=POST action='../tools/fast-stock-opname/fast-SO-mobile.php'>
//          <input type=submit value='Input Mobile SO' ></form>
//	</div>
//
//	<div style=\"float:left\">
//          <form method=POST action='?module=barang&act=ApproveFastSO1'>
//          || <input type=submit value='Approve Fast SO' ></form>
//	</div>
//
//	<div style=\"float:left\">
//          <form method=POST action='?module=barang&act=ApproveMobileSO1'>
//          || <input type=submit value='Approve Mobile SO' ></form>
//	</div>
//			<br /><br />
        //          <form method=POST action='?module=barang&act=returbarang1' onSubmit=\"popupform(this, 'inputrak')\" >
        ?>

        <form class="inline" method=POST action='modul/js_input_retur_barang.php?act=caricustomer' onSubmit="popupform(this, 'INPUT_RETUR_BARANG')">
            <input type=submit accesskey='r' value='(r) Input Retur'>
        </form>


        <form class="inline"  method=POST action='?module=barang&act=hargajualsync'>
            <input type=submit value='Sinkronisasi Harga Jual' >
        </form>

        <?php
//	<div style=\"float:left\">
//          <form method=POST action='?module=barang&act=transfer1'>
//          || <input type=submit value='Transfer Barang antar Ahad' ></form>
//	</div>
        ?>
        <br/>
        <table class="tabel">
            <thead>
                <tr>
                    <th>no</th>
                    <th>Barcode</th>
                    <th>Nama Barang</th>
                    <th>Kategori Barang</th>
                    <th>Satuan Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Jual</th>
                    <th>Non Aktif</th>
                    <th>aksi</th>
                </tr>
            </thead>
            <?php
            if ($_GET[p]) {
                $mulai = $_GET[p] * 100;
            }
            else {
                $mulai = 0;
            };

//HS query terlampir buggy !! dan juga amat lambat
//    $tampil=mysql_query("SELECT idBarang,namaBarang,namaKategoriBarang,
//                        namaSatuanBarang,jumBarang,hargaJual, barcode
//                        FROM barang b, kategori_barang kb, satuan_barang sb
//                        ORDER BY b.namaBarang ASC LIMIT $mulai,100 ");
//    $tampil=mysql_query("SELECT b.idBarang,b.namaBarang,b.jumBarang,b.hargaJual,b.barcode, k.namaKategoriBarang, s.namaSatuanBarang
//                        FROM barang AS b, kategori_barang AS k, satuan_barang AS s
//			WHERE b.idKategoriBarang = k.idKategoriBarang AND b.idSatuanBarang = s.idSatuanBarang
//                        ORDER BY namaBarang ASC LIMIT $mulai,100 ");
            // query ini lebih cepat & rapi
            // credit : Insan Fajar
            $tampil = mysql_query("SELECT
				`barang`.`idBarang`,
				`barang`.`namaBarang`,
				`barang`.`idKategoriBarang`,
				`kategori_barang`.`namaKategoriBarang`,
				`barang`.`idSatuanBarang`,
				`satuan_barang`.`namaSatuanBarang`,
				`barang`.`jumBarang`,
				`barang`.`hargaJual`,
				`barang`.`barcode`,
				`barang`.`nonAktif`
			FROM `barang`
				LEFT JOIN `kategori_barang`
					ON `barang`.`idKategoriBarang` = `kategori_barang`.`idKategoriBarang`
				LEFT JOIN `satuan_barang`
					ON `barang`.`idSatuanBarang` = `satuan_barang`.`idSatuanBarang`
				ORDER BY `namaBarang` ASC LIMIT $mulai,100");


            $no = 1;
            $ctr = 1;
            ?>
            <tbody>
                <?php
                while (($r = mysql_fetch_array($tampil)) or ( $ctr < 100)) {
                    //untuk mewarnai tabel menjadi selang-seling
                    /*
                      if (($no % 2) == 0) {
                      $warna = "#EAF0F7";
                      } else {
                      $warna = "#FFFFFF";
                      }
                     *
                     */
                    // Mewarnai tabel diganti dengan css agar lebih fleksibel
                    ?>
                    <tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>">
                        <td class="right"><?php echo $no; ?></td>
                        <td><?php echo $r['barcode']; ?></td>
                        <td><?php echo $r['namaBarang']; ?></td>
                        <td><?php echo $r['namaKategoriBarang']; ?></td>
                        <td class="center"><?php echo $r['namaSatuanBarang']; ?></td>
                        <td class="right"><?php echo $r['jumBarang']; ?></td>
                        <td class="right"><?php echo $r['hargaJual']; ?></td>
                        <td class="center"><?php echo $r['nonAktif'] == '1' ? '<i class="fa fa-times"></i>' : ''; ?></td>
                        <td><a href=?module=barang&act=editbarang&id=<?php echo $r['barcode']; ?>>Ubah</a><?php //|Ha<a href=./aksi.php?module=barang&act=hapus&id=<?php echo $r['barcode']; >pus</a>                                                                                       ?>
                        </td>
                    </tr>
                    <?php
                    $no++;
                    $ctr++;
                }
                ?>
            </tbody>
        </table>
        <br />
        <?php
        $sql1 = "SELECT DISTINCT COUNT(barcode) FROM barang";
        $proses1 = mysql_query($sql1);
        $output1 = mysql_fetch_array($proses1);
        $jumlah_barang = $output1[0] / 100;

        for ($i = 0; $i <= $jumlah_barang; $i++) {
            echo "[<a href='media.php?module=barang&p=$i'> $i </a>] ";
        };

        echo "
    <p>&nbsp;</p>
    <a href=javascript:history.go(-1)><< Kembali</a>";
        break;



    case "returbarang1": // ========================================================================================================================
        // pilih printer
        $sql = "SELECT namaWorkstation,printer_commands,workstation_address FROM workstation ";
        $hasil = mysql_query($sql) or die(mysql_error());

        echo "
			<form method=POST action='modul/js_input_retur_barang.php?act=caricustomer' onSubmit=\"popupform(this, 'INPUT_RETUR_BARANG')\">
		<table>
        	<tr>
			<td>Pilih Printer </td>
			<td>: <select name='namaPrinter'>";

        while ($printer = mysql_fetch_array($hasil)) {
            echo "<option value='$printer[printer_commands]'>$printer[namaWorkstation]</option>\n";
        }

        echo "
			</td>
			</tr>

        		<tr><td colspan=2>&nbsp;</td></tr>
        		<tr><td colspan=2><input type=submit value='Pilih Printer'>&nbsp;&nbsp;&nbsp;
        	                        <input type=reset value='Batal'></td></tr>
			</table>
			</form>
		";

        break;



    case "tambahbarang": // ========================================================================================================================
        echo "<h2>Tambah Barang</h2>
          <form method=POST action='./aksi.php?module=barang&act=input' name='tambahbarang'>
          <table>
          <tr><td>Barcode</td><td> : <input type=text name='barcode' size=30 value=$_GET[id]></td></tr>
          <tr><td>Nama Barang</td><td> : <input type=text name='namaBarang' size=30></td></tr>
          <tr><td>Supplier</td>
                <td> : <select name='supplier'>
                            <option value='0'>- Supplier -</option>";
        while ($supplier = mysql_fetch_array($ambilSupplier)) {
            echo "<option value='$supplier[idSupplier]'>$supplier[namaSupplier]</option>";
        }
        echo "</select></td></tr>
          <tr><td>Kategori Barang</td>
                <td> : <select name='kategori_barang'>
                            <option value='0'>- Kategori Barang-</option>";
        while ($kategori = mysql_fetch_array($ambilKategoriBarang)) {
            echo "<option value='$kategori[idKategoriBarang]'>$kategori[namaKategoriBarang]</option>";
        }
        echo "</select></td></tr>
          <tr><td>Satuan Barang</td>
                <td> : <select name='satuan_barang'>
                            <option value='0'>- Satuan Barang-</option>";
        while ($satuan = mysql_fetch_array($ambilSatuanBarang)) {
            echo "<option value='$satuan[idSatuanBarang]'>$satuan[namaSatuanBarang]</option>";
        }
        echo "</select></td></tr>
          <tr><td colspan=2>&nbsp;</td></tr>
          <tr><td colspan=2 align='right'><input type=submit value=Simpan>&nbsp;&nbsp;&nbsp;
                            <input type=button value=Batal onclick=self.history.back()></td></tr>
          </table></form>";
        break;




    case "caribarang1": // ========================================================================================================================

        echo "

		<h2>Cari Barang</h2>
		<form method=POST action='?module=barang&act=caribarang2'>

          <table>
		<tr><td> (r) Pilih Rak ? </td>
                <td> : <select name='rak' accesskey='r'>
			<option value='0'>-- Tidak Usah --</option>";

        while ($rak = mysql_fetch_array($ambilRak)) {
            echo "<option value='$rak[idRak]'>$rak[namaRak]</option>";
        }
        echo "</select></td></tr>

          <tr><td>(b) Barcode</td><td> : <input accesskey='b' type=text name='barcode' size=30 value='0'></td></tr>
          <tr><td>(n) Nama Barang</td><td> : <input accesskey='n' type=text name='namaBarang' size=30 value=''></td></tr>
		<tr><td><input type=submit accesskey='c' value='(c) Cari Barang'></td></tr>

		</table></form>";

        break;


    case "caribarang2":


        if ($_POST[rak] == '0') {
            $q_rak = "";
        }
        else {
            $q_rak = " idRak=$_POST[rak] AND ";
        };

        if ($_POST[barcode] == '0') {
            $sql = "SELECT b.idBarang,b.namaBarang,b.jumBarang,b.hargaJual,b.barcode, k.namaKategoriBarang, s.namaSatuanBarang, b.nonAktif
                        FROM barang AS b, kategori_barang AS k, satuan_barang AS s
			WHERE $q_rak namaBarang LIKE '%$_POST[namaBarang]%' AND
				b.idKategoriBarang = k.idKategoriBarang AND b.idSatuanBarang = s.idSatuanBarang
                        ORDER BY namaBarang ASC";
        }
        else {
            $sql = "SELECT b.idBarang,b.namaBarang,b.jumBarang,b.hargaJual,b.barcode, k.namaKategoriBarang, s.namaSatuanBarang, b.nonAktif
                        FROM barang AS b, kategori_barang AS k, satuan_barang AS s
			WHERE $q_rak barcode LIKE '$_POST[barcode]%' AND
				b.idKategoriBarang = k.idKategoriBarang AND b.idSatuanBarang = s.idSatuanBarang
                        ORDER BY namaBarang ASC";
        };
        $cari = mysql_query($sql);
        //echo $sql;
        ?>
        <table class="tabel">
            <tr>
                <th>no</th>
                <th>Barcode</th>
                <th>Nama Barang</th>
                <th>Kategori Barang</th>
                <th>Satuan Barang</th>
                <th>Jumlah</th>
                <th>Harga Jual</th>
                <th>Non Aktif</th>
                <th>aksi</th>
            </tr>
            <?php
            $no = 1;
            while ($r = mysql_fetch_array($cari)) {
                ?>

                <tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>">
                    <td class="right"><?php echo $no; ?></td>
                    <td><?php echo $r['barcode']; ?></td>
                    <td><?php echo $r['namaBarang']; ?></td>
                    <td class="center"><?php echo $r['namaKategoriBarang']; ?></td>
                    <td class="center"><?php echo $r['namaSatuanBarang']; ?></td>
                    <td class="right"><?php echo $r['jumBarang']; ?></td>
                    <td class="right"><?php echo $r['hargaJual']; ?></td>
                    <td class="center"><?php echo $r['nonAktif'] == '1' ? '<i class="fa fa-times"></i>' : ''; ?></td>
                    <td><a href=?module=barang&act=editbarang&id=<?php echo $r[barcode]; ?>>Ubah</a><?php //|Ha<a href=./aksi.php?module=barang&act=hapus&id=<?php echo $r['idBarang']; >pus</a>                                                                                      ?>
                    </td>
                </tr>
                <?php
                $no++;
            }
            ?>
        </table>
        <br />
        <?php
        break;




    case "cetaklabel1": // ========================================================================================================================

        echo "

		<h2>Cetak Label Barang</h2>
		<form method=POST action='?module=barang&act=cetaklabel2'>

          <table>
          <tr><td>(s) Supplier</td>
                <td> : <select name='supplier' accesskey='s'>
			<option value='0'>-- Pilih Supplier --</option>";
        while ($supplier = mysql_fetch_array($ambilSupplier)) {
            echo "<option value='$supplier[idSupplier]'>$supplier[namaSupplier]</option>";
        }
        echo "</select></td></tr>
          <tr><td>(r) Rak</td>
                <td> : <select name='rak' accesskey='r'>
			<option value='0'>-- Pilih Rak --</option>";
        while ($rak = mysql_fetch_array($ambilRak)) {
            echo "<option value='$rak[idRak]'>$rak[namaRak]</option>";
        }
        echo "</select></td></tr>

		<tr><td><input type=submit accesskey='l' value='(l) Cetak Label Barang'></td></tr>

		</table></form>";

        break;


    case "cetaklabel2":


        if ($_POST[rak] == '0') {
            $sql = "SELECT b.idBarang,b.namaBarang,b.jumBarang,b.hargaJual,b.barcode, k.namaKategoriBarang, s.namaSatuanBarang
                        FROM barang AS b, kategori_barang AS k, satuan_barang AS s
			WHERE idSupplier=$_POST[supplier] AND
				b.idKategoriBarang = k.idKategoriBarang AND b.idSatuanBarang = s.idSatuanBarang AND (nonAktif!=1 or nonAktif is null)
                        ORDER BY namaBarang ASC";
            $cari = mysql_query($sql);
            $q = 'sup';
            $sql = $_POST[supplier];
        }
        else {
            $sql = "SELECT b.idBarang,b.namaBarang,b.jumBarang,b.hargaJual,b.barcode, k.namaKategoriBarang, s.namaSatuanBarang
                        FROM barang AS b, kategori_barang AS k, satuan_barang AS s
			WHERE idRak=$_POST[rak] AND
				b.idKategoriBarang = k.idKategoriBarang AND b.idSatuanBarang = s.idSatuanBarang AND (nonAktif!=1 or nonAktif is null)
                        ORDER BY namaBarang ASC";
            $cari = mysql_query($sql);
            $q = 'rak';
            $sql = $_POST[rak];
        };

        $jumlah_pilihan = mysql_num_rows($cari);
        ?>
        <h2>Cetak Label Barang - Pilih Barang</h2>
        <form method=POST action='modul/mod_barang.php?act=cetaklabel3' onSubmit="popupform(this, 'cetaklabel')">
            <input type=hidden name=total value=<?php echo $jumlah_pilihan; ?>>
            <input type=hidden name=q value=<?php echo $q; ?>>
            <input type=hidden name=sql value=<?php echo $sql; ?>>


            <table class="tabel">
                <tr>
                    <th>No</th>
                    <th>Barcode</th>
                    <th>Nama Barang</th>
                    <th>Kategori Barang</th>
                    <th>Satuan Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Jual</th>
                    <th>Cetak ?</th>
                </tr>
                <?php
                $no = 1;
                $ctr = 1;
                while ($r = mysql_fetch_array($cari)) :
                    ?>
                    <tr <?php echo $no % 2 === 0 ? 'class="alt"' : ''; ?>>
                        <td class="right"><?php echo $no; ?></td>
                        <td><?php echo $r['barcode']; ?></td>
                        <td><?php echo $r['namaBarang']; ?></td>
                        <td class="center"><?php echo $r['namaKategoriBarang']; ?></td>
                        <td class="center"><?php echo $r['namaSatuanBarang']; ?></td>
                        <td class="right"><?php echo $r['jumBarang']; ?></td>
                        <td class="right"><?php echo $r['hargaJual']; ?></td>
                        <td class="center"><input type="checkbox" name="cl<?php echo $ctr; ?>" checked="yes"></td>
                    </tr>
                    <?php
                    $no++;
                    $ctr++;
                endwhile;
                ?>

            </table> <br />
            <label for="layout-tinggi">Pilih Layout : </label>
            <select id="layout-tinggi" name="layout">
                <option value="0">Tinggi: 3 mm</option>
                <option value="1">Tinggi: 3,3 mm</option>
            </select>
            <input type=submit accesskey='l' value='(l) Cetak Label Barang'>
        </form>
        <?php
        break;


    case "cetaklabel3":

        include "../../config/config.php";

        if ($_POST[q] == 'sup') {
            $cari = mysql_query("SELECT * FROM barang WHERE idSupplier=$_POST[sql] ORDER BY namaBarang ASC");
        }
        else {
            $cari = mysql_query("SELECT * FROM barang WHERE idRak=$_POST[sql] ORDER BY namaBarang ASC");
        };

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
            if ($_POST["cl$i"] == 'on') {

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

                $namaBarang = $r[namaBarang];
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
					" . number_format($r[hargaJual], 0, ',', '.') . "	</p>
				<p style=\"line-height:0px; text-align:left; font-family:Arial; font-size:6pt; \">
					$r[barcode] - $r[idRak]
				</div>
			";

                $kolom++;
            };
        } // for

        echo "</div>";

        break;



    case "cetakperbarcode": // =============================================================================================

        $tampil = mysql_query("SELECT * FROM tmp_cetak_label_perbarcode");
        $jumlah_pilihan = mysql_num_rows($tampil);
        ?>
        <div>
            <form action="?module=barang&act=cetakperbarcode&cek=barcode" method="POST">
                <input type="text" name="lBarcode" size="25" placeholder="Input barcode" id="barcode" />
                <input type="submit" name="cekBarcode" value="Get Barang" />
            </form>

            <script>
                var txtBox = document.getElementById("barcode");
                if (txtBox != null)
                    txtBox.focus();</script>

        </div>
        <?php
        if ($_GET[cek] == "barcode") {
            if ($_POST[lBarcode] != "") {
                $cekBarcode = $_POST[lBarcode];
                insertTempLabel($cekBarcode);
                header('location:?module=barang&act=cetakperbarcode');
            }
        }
        ?>
        <form action='modul/mod_cetakperbarcode.php?act=printperbarcode' method='POST' onSubmit="popupform(this, 'printperbarcode')">
            <table class="tabel">
                <tr>
                    <th>No</th>
                    <th>Barcode</th>
                    <th>Nama Barang</th>
                    <th>Kategori Barang</th>
                    <th>Satuan Barang</th>
                    <th>Harga Jual</th>
                    <th>Aksi</th>
                </tr>
                <?php
                $no = 1;
                while (($r = mysql_fetch_array($tampil))) {
                    ?>
                    <tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>">
                        <td class='center'><?php echo $no; ?></td>
                        <td class='center'><?php echo $r['tmpBarcode']; ?></td>
                        <td class='center'><?php echo $r['tmpNama']; ?></td>
                        <td class='center'><?php echo $r['tmpKategori']; ?></td>
                        <td class='center'><?php echo $r['tmpSatuan']; ?></td>
                        <td class='right'><?php echo $r['tmpHargaJual']; ?></td>
                        <td><a href='./aksi.php?module=labelperbarcode&act=hapus&id=<?php echo $r['id']; ?>'>Batal</a>
                        </td></tr>
                    <input type='hidden' name='idTmpBarang' value='<?php echo $r['id']; ?>' />
                    <input type='hidden' name='total' value='<?php echo $jumlah_pilihan; ?>' />
                    <?php
                    $no++;
                }
                ?>
            </table>
            <div>
                <label for="layout-tinggi">Pilih Layout : </label>
                <select id="layout-tinggi" name="layout">
                    <option value="0">Tinggi: 3 mm</option>
                    <option value="1">Tinggi: 3,3 mm</option>
                </select>
                <input type='submit' name='printBarcode' value='Print' />
            </div>
        </form>
        <?php
        break;



    case "inputrak": // ========================================================================================================================

        include "../../config/config.php";

        if ($_POST[masuk]) {
            $tgl = date("Y-m-d");
            $sql = "UPDATE barang SET idRak = '$_POST[rak]'
                    WHERE barcode = '$_POST[barcode]'";
            //echo $sql;
            mysql_query($sql);
        };

        echo "
	  <h2>Input Cepat Rak</h2>
          <form method=POST action='?module=barang&act=inputrak' name='inputrakbarang'>

          <table>
          <tr><td>(r) Rak</td>
                <td> : <select name='rak' accesskey='r'>
			<option value='0'>-- Pilih Rak --</option>";
        while ($rak = mysql_fetch_array($ambilRak)) {
            if ($rak[idRak] == $_POST[rak]) {
                echo "<option value='$rak[idRak]' selected>$rak[namaRak]</option>";
            }
            else {
                echo "<option value='$rak[idRak]'>$rak[namaRak]</option>";
            }
        }
        echo "</select></td></tr>
          <tr><td>(b) Barcode</td><td> : <input type=text name='barcode' id='barcode' accesskey='b' size=30 value=''>	</td></tr>
	  </table>
	<input type=submit accesskey='i' name='masuk' value='(i) Input'></td></tr>

	";

        echo "
		<script>
			var txtBox=document.getElementById(\"barcode\");
			if (txtBox!=null ) txtBox.focus();
		</script>";

        break;




    case "cetakSO": // ========================================================================================================================

        echo "

		<h2>Cetak Stock Opname</h2>
		<form method=POST action='modul/mod_barang.php?act=cetakSO2' onSubmit=\"popupform(this, 'Cetak Stock Opname')\">

          <table>

          <tr><td>(r) Rak</td>
                <td> : <select name='rak' accesskey='r'>
			<option value='0'>-- Pilih Rak --</option>";
        while ($rak = mysql_fetch_array($ambilRak)) {
            echo "<option value='$rak[idRak]'>$rak[namaRak]</option>";
        }
        echo "</select></td></tr>

		<tr><td colspan=2><input type=submit accesskey='c' value='(c) Cetak Stock Opname' ></td></tr>

		</table></form>";

        break;




    case "cetakSO2":

        include "../../config/config.php";

        $cari = mysql_query("SELECT * FROM barang WHERE idRak=$_POST[rak] AND (nonAktif!=1 or nonAktif is null) ORDER BY namaBarang ASC");

        $hasilRak = mysql_query("select namaRak from rak where idRak={$_POST['rak']}");
        $rak = mysql_fetch_array($hasilRak);
        ?>
        <style>
            th{
                font-size: 14px;
            }
            td{
                font-size: 12px;
            }
        </style>
        <?php
        echo "
	<h3>Rak {$rak['namaRak']}</h3>
	  <table class='tabel'>
          <tr><th>no</th><th>Barcode</th><th>Nama Barang</th><th>Harga <br />Jual</th>
                <th>Jml <br />Tercatat</th><th>Selisih</th></tr>";

        $no = 1;
        $ctr = 1;
        while ($r = mysql_fetch_array($cari)) {
            //untuk mewarnai tabel menjadi selang-seling
            if (($no % 2) == 0) {
                $warna = "#EAF0F7";
            }
            else {
                $warna = "#FFFFFF";
            }
            echo "<tr bgcolor=$warna>"; //end warna
            echo "<td align=left class=td>$no</td>
             <td class=td>$r[barcode]</td>
             <td class=td>$r[namaBarang]</td>
             <td align=right class=td>" . number_format($r[hargaJual], 0, ',', '.') . "	</td>
             <td align=right class=td><center>$r[jumBarang]</center>			</td>
             <td align=right class=td>  						</td>
		</tr>";
            $no++;
            $ctr++;
        }
        echo "

	</table> <br />
	";

        break;



    case "inputSO": // ========================================================================================================================

        echo "

		<h2>Input Stock Opname - Pilih Rak</h2>
		<form method=POST action='?module=barang&act=inputSO2'>

          <table>

          <tr><td>(r) Rak</td>
                <td> : <select name='rak' accesskey='r'>
			<option value='0'>-- Pilih Rak --</option>";
        while ($rak = mysql_fetch_array($ambilRak)) {
            echo "<option value='$rak[idRak]'>$rak[namaRak]</option>";
        }
        echo "</select></td></tr>

		<tr><td colspan=2><input type=submit accesskey='i' value='(i) Mulai Input Stock Opname' ></td></tr>

		</table></form>";

        break;




    case "inputSO2":  // mulai input hasil Stock Opname

        include "../config/config.php";

        $sql = "SELECT * FROM barang WHERE idRak=$_POST[rak] ORDER BY namaBarang ASC";
        $cari = mysql_query($sql);
        //echo $sql;
        ?>
        <h2>Input Stock Opname (ID Rak: <?php echo $_POST['rak']; ?>)</h2>
        <form method=POST action='?module=barang&act=inputSO3'>


            <table class="tabel">
                <tr>
                    <th>No</th>
                    <th>Barcode</th>
                    <th>Nama Barang</th>
                    <th>Harga <br />Jual</th>
                    <th>Jml <br />Tercatat</th>
                    <th>Selisih</th>
                </tr>
                <?php
                $no = 1;
                $ctr = 1;
                while ($r = mysql_fetch_array($cari)) :
                    ?>
                    <tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>">

                        <td class="right"><?php echo $no; ?></td>
                        <td><?php echo $r['barcode']; ?>
                            <input type=hidden name='barcode<?php echo $ctr; ?>' value='<?php echo $r['barcode']; ?>' /></td>
                        <td><?php echo $r['namaBarang']; ?>
                            <input type=hidden name='namaBarang<?php echo $ctr; ?>' value='<?php echo $r['namaBarang']; ?>'></td>
                        <td class="right"><?php echo number_format($r['hargaJual'], 0, ',', '.'); ?></td>
                        <td class="center"><?php echo $r['jumBarang']; ?>
                            <input type=hidden name='jmlTercatat<?php echo $ctr; ?>' value='<?php echo $r['jumBarang']; ?>'>	</td>
                        <td class="right">	<input type=text name='selisih<?php echo $ctr; ?>' size=2 value='0'>	</td>
                    </tr>
                    <?php
                    $no++;
                    $ctr++;
                endwhile;
                ?>

                <tr>
                    <td colspan=2><input type=submit value='Input Stock Opname' ></td>
                </tr>
            </table>
            <br />
            <input type=hidden name=rak value='<?php echo $_POST['rak']; ?>'>
            <input type=hidden name=username value='<?php echo $_SESSION['uname']; ?>'>
            <input type=hidden name=ctr value='<?php echo $ctr; ?>'>

            <?php
            break;


        case "inputSO3":  // simpan di database

            include "../../config/config.php";

            // default max_input_vars hanya 1000, ini sangat tidak mencukupi pada rak yang ada banyak jenis barangnya
            // fixme: ini sudah tidak bisa sejak php 5.3, harus lewat .htaccess, httpd.conf atau .user.ini per directory
            ini_set('max_input_vars', '20000');
            ini_set('suhosin.post.max_vars', '20000');
            ini_set('suhosin.request.max_vars', '20000');
            // :end

            $sql = "INSERT INTO stock_opname (username, tanggalSO, idRak) VALUES
		('$_POST[username]','" . date("Y-m-d") . "', $_POST[rak])";
            $hasil = mysql_query($sql) or die("Gagal simpan hasil Stock Opname: " . mysql_error() . " SQL: $sql -- tekan tombol BACK !");
            $idStockOpname = mysql_insert_id();
            $ctr = $_POST[ctr];

            echo "Stock Opname sudah disimpan di database, nota SO nomor: " . $idStockOpname . " <br /><br /> Mulai menyimpan transaksi Stock Opname : <br /><br />";

            for ($i = 1; $i <= $ctr; $i++) {

                if ($_POST["selisih$i"] <> 0) { // simpan hanya yang ada selisihnya
                    $sql = "INSERT INTO detail_stock_opname (idStockOpname,barcode,namaBarang,jmlTercatat,selisih)
				VALUES ($idStockOpname,'" . $_POST["barcode$i"] . "','" . $_POST["namaBarang$i"] . "'," . $_POST["jmlTercatat$i"] . ",
					" . $_POST["selisih$i"] . ") ";
                    $hasil = mysql_query($sql);

                    //fixme: ubah jumlah barang - komprehensif
                    //	# cari seluruh stok dari barang ybs di detail_beli
                    //	# pilih yang paling awal
                    //	# apply selisih di salah satunya
                    //		# jika jadi minus = jadikan nol, lalu pilih record barang tsb yang berikutnya
                    //	# sesuaikan jmlBarang di tabel barang
                    ////////////// update jumlah stok di tabel barang
                    // StokSekarang = jmlTercatat + Selisih
                    $StokSekarang = $_POST["jmlTercatat$i"] + $_POST["selisih$i"];
                    $sql = "UPDATE barang SET jumBarang = '" . $StokSekarang . "' WHERE barcode = '" . $_POST["barcode$i"] . "'";
                    $hasil = mysql_query($sql);

                    echo "
			Transaksi SO : Nama Barang: " . $_POST["namaBarang$i"] . ", Selisih: " . $_POST["selisih$i"] . " - sudah disimpan<br />
			";
                }; // if ($_POST[selisih$i] !== 0)
            }; // for ($i = 1; $i <= $_POST[ctr]; $i++)


            echo "Selesai !";

            break;





        case "editbarang": // ========================================================================================================================
            $edit = mysql_query("SELECT * FROM barang WHERE barcode='$_GET[id]'");
            $data = mysql_fetch_array($edit);
            ?>
            <h2>Edit Barang</h2>
            <form method=POST action=./aksi.php?module=barang&act=update name='editbarang'>
                <input type=hidden name='idBarang' value='<?php echo $data['idBarang']; ?>'>
                <table>
                    <tr><td>Barcode</td><td> : <input type="text" name='barcode' size=30 value='<?php echo $data['barcode']; ?>' /></td></tr>
                    <tr><td>Nama Barang</td><td> : <input type="text" name='namaBarang' size=30 value='<?php echo $data['namaBarang']; ?>' /></td><td style="color:red"><?php echo isset($_GET['barang']) ? 'Nama barang sudah diperbarui' : '' ?></td></tr>
                    <tr><td>Kategori Barang</td>
                        <td> : <select name='kategori_barang'>
                                <?php
                                while ($kategori = mysql_fetch_array($ambilKategoriBarang)) {
                                    if ($kategori[idKategoriBarang] == $data[idKategoriBarang]) {
                                        echo "<option value='$kategori[idKategoriBarang]' selected>$kategori[namaKategoriBarang]</option>";
                                    }
                                    else {
                                        echo "<option value='$kategori[idKategoriBarang]'>$kategori[namaKategoriBarang]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="color:red"><?php echo isset($_GET['kategori']) ? 'Kategori sudah diperbarui' : '' ?></td>
                    </tr>
                    <tr><td>Satuan Barang</td>
                        <td> : <select name='satuan_barang'>
                                <?php
                                while ($satuan = mysql_fetch_array($ambilSatuanBarang)) {
                                    if ($satuan[idSatuanBarang] == $data[idSatuanBarang]) {
                                        echo "<option value='$satuan[idSatuanBarang]' selected>$satuan[namaSatuanBarang]</option>";
                                    }
                                    else {
                                        echo "<option value='$satuan[idSatuanBarang]'>$satuan[namaSatuanBarang]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="color:red"><?php echo isset($_GET['satuan']) ? 'Satuan sudah diperbarui' : '' ?></td>
                    </tr>
                    <tr><td>Supplier</td>
                        <td> : <select name='supplier'>
                                <?php
                                while ($supplier = mysql_fetch_array($ambilSupplier)) {
                                    if ($supplier[idSupplier] == $data[idSupplier]) {
                                        echo "<option value='$supplier[idSupplier]' selected>$supplier[namaSupplier]</option>";
                                    }
                                    else {
                                        echo "<option value='$supplier[idSupplier]'>$supplier[namaSupplier]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="color:red"><?php echo isset($_GET['supplier']) ? 'Supplier sudah diperbarui' : '' ?></td>
                    </tr>
                    <tr><td>Rak</td>
                        <td> : <select name='rak'>
                                <?php
                                while ($rak = mysql_fetch_array($ambilRak)) {
                                    if ($rak[idRak] == $data[idRak]) {
                                        echo "<option value='$rak[idRak]' selected>$rak[namaRak]</option>";
                                    }
                                    else {
                                        echo "<option value='$rak[idRak]'>$rak[namaRak]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="color:red"><?php echo isset($_GET['rak']) ? 'Rak sudah diperbarui' : '' ?></td>
                    </tr>
                    <tr>
                        <td>Harga Jual</td>
                        <td> : <input type=text name='hargaJual' size=20 value='<?php echo $data['hargaJual']; ?>'></td>
                        <td style="color:red"><?php echo isset($_GET['hargajual']) ? 'Harga jual sudah diperbarui' : '' ?></td>
                    </tr>
                    <tr>
                        <td>Non Aktif</td>
                        <td> :
                            <select name="nonAktif">
                                <option value="0" <?php echo $data['nonAktif'] != '1' ? 'selected' : ''; ?>>Tidak</option>
                                <option value="1" <?php echo $data['nonAktif'] == '1' ? 'selected' : ''; ?>>Ya</option>
                            </select>
                        </td>
                        <td style="color:red"><?php echo isset($_GET['nonAktif']) ? 'Status barang sudah diperbarui' : '' ?></td>
                    </tr>
                    <tr><td colspan=2>&nbsp;</td></tr>
                    <tr><td colspan=2 align='right'><input type=submit value=(S)impan accesskey=s>&nbsp;&nbsp;&nbsp;
                            <input type=button value=Batal onclick=self.history.back()></td></tr>

                    <input type=hidden name='oldbarcode' value='<?php echo $data['barcode']; ?>'>
                </table>
            </form> <br /><br />
            <?php
            // tampilkan seluruh stok ybs yang masih ada di toko / belum laku terjual
            $sql = "SELECT t.tglTransaksiBeli,d.hargaBeli, d.jumBarang
		FROM detail_beli AS d, transaksibeli AS t
		WHERE d.barcode = '$data[barcode]' AND d.idTransaksiBeli = t.idTransaksiBeli AND d.isSold='N' ORDER BY d.idTransaksiBeli DESC";
            $hasil = mysql_query($sql);
            $jumlah = mysql_num_rows($hasil);
            while ($x = mysql_fetch_array($hasil)) {
                echo "Tgl.Beli : $x[tglTransaksiBeli], Harga Beli : Rp " . number_format($x[hargaBeli], 0, ',', '.') . " (jumlah: $x[jumBarang])<br />";
            }

            // jika stok nya sudah laku semua,
            // cetak 2 stok yang terakhir (sekedar untuk informasi harga)
            if ($jumlah < 1) {
                $sql = "SELECT d.idTransaksiBeli, d.hargaBeli, d.isSold, d.jumBarang FROM detail_beli AS d
			WHERE d.barcode = '$data[barcode]' ORDER BY d.idTransaksiBeli DESC LIMIT 2";
                $hasil = mysql_query($sql);
                $jumlah = mysql_num_rows($hasil);
                while ($x = mysql_fetch_array($hasil)) {
                    echo "ID: " . $x[idTransaksiBeli] . ", Harga Beli : Rp " . number_format($x[hargaBeli], 0, ',', '.') . ", Status: ";
                    if ($x[isSold] == 'Y') {
                        echo " Habis";
                    }
                    else {
                        echo " Ada";
                    };
                    echo " (jumlah: $x[jumBarang]) <br />";
                }
            }


            break;



        case "cetakbarang1": // ========================================================================================================================
            // cari tahu jumlah rak yang ada di toko ini
            $cari = mysql_query("SELECT idRak FROM rak");
            $jumlah_rak = mysql_num_rows($cari);

            // cari daftar workstation kasir yang ada
            $daftarKasir = mysql_query("SELECT idWorkstation,namaWorkstation FROM workstation");


            echo "
		<h2>Cetak Stock Barang</h2>
		<form method=GET action='modul/mod_barang.php'  onSubmit=\"popupform(this, 'CETAK_STOCK_BARANG')\">

	Disini Anda bisa mencetak daftar stok Barang yang masih ada / jumlahnya tidak nol.
	Biasanya digunakan pada saat Tutup Buku, untuk secara acak memeriksa stok barang yang sebenarnya.

	<br /><br />

	<table>
	<tr>
		<td>(d) Dari Rak</td>
		<td> : <input type=text name=darirak value=1 accesskey='d' size=4></td>
	</tr>
	<tr>
		<td>Sampai Rak</td>
		<td> : <input type=text name=sampairak value=$jumlah_rak size=4></td>
	</tr>
		<td><br /> (p) Cetak ke </td>
                <td><br /> : <select name='printer' accesskey='p'>
			<option value='0'>-- Cetak Ke Browser --</option>";
            while ($printer = mysql_fetch_array($daftarKasir)) {
                echo "<option value='$printer[idWorkstation]'>$printer[namaWorkstation]</option>";
            }
            echo "</select></td></tr>


		<tr><td colspan=2><br /><input type=submit accesskey='b' value='(b) Cetak Stock Barang'></td></tr>
					<input type=hidden name=act value=cetakbarang2>
		</table></form>";

            break;


        case "cetakbarang2":


            echo "
	<form method='post'>
		<input type=button value='Tutup Window Ini' onclick='window.close()'>
	</form>
	";

            // ambil data barang yang akan dicetak
            $sql = "SELECT idRak, namaBarang, hargaJual, jumBarang
		FROM barang WHERE jumBarang <> 0 AND idRak BETWEEN " . $_GET['darirak'] . " AND " . $_GET['sampairak'] . "
		ORDER BY idRak,namaBarang ASC";
            $daftarBarang = mysql_query($sql);
            $jumlahBarang = mysql_num_rows($daftarBarang);
            //echo $sql;
            // mulai mencetak
            if ($_GET[printer] == '0') {

                $rakSebelum = 0;
                $rakSekarang = 0;
                $gantiBaris = 0;
                for ($i = 1; $i <= $jumlahBarang; $i++) {

                    // ambil 1 record
                    $x = mysql_fetch_array($daftarBarang);
                    $rakSekarang = $x[idRak];

                    if ($rakSebelum <> $rakSekarang) {
                        // cetak header
                        $hasil = mysql_query("SELECT namaRak FROM rak WHERE idRak = $x[idRak]");
                        $r = mysql_fetch_array($hasil);

                        echo "
					</table>

					<h2>
						Rak #$x[idRak] : $r[namaRak]
					</h2>

					<table border=1>

					<tr>
						<td><center><b>	Nama Barang	</b></center>
						</td>
						<td><center><b>	Harga	</b></center>
						</td>
						<td><center><b>	Jml	</b></center>
						</td>
						<td><center><b>	Nama Barang	</b></center>
						</td>
						<td><center><b>	Harga	</b></center>
						</td>
						<td><center><b>	Jml	</b></center>
						</td>
					</tr>
					<tr>
				";
                        $rakSebelum = $rakSekarang;
                    }; // if ($rakSebelum <> $rakSekarang)

                    if ($gantiBaris > 1) {
                        // ganti baris
                        echo "</tr><tr>";
                        $gantiBaris = 0;
                    }; // if ($gantiBaris > 1)
                    // cetak data barang
                    echo "
			<td>		$x[namaBarang]
			</td>
			<td><center>	$x[hargaJual]	</center>
			</td>
			<td><center>	$x[jumBarang]	</center>
			</td>
			";

                    $gantiBaris++;
                }; // for ($i = 1; $i <= $jumlahBarang; $i++)
            }
            else {

                // ambil daftar printer_command untuk idWorkstation ybs
                $hasil = mysql_query("SELECT printer_commands FROM workstation WHERE idWorkstation = $_GET[printer]");
                $r = mysql_fetch_array($hasil);
                $perintahPrinter = $r[printer_commands];

                $rakSebelum = 0;
                $rakSekarang = 0;
                $struk = "";
                for ($i = 1; $i <= $jumlahBarang; $i++) {

                    // ambil 1 record
                    $x = mysql_fetch_array($daftarBarang);
                    $rakSekarang = $x[idRak];

                    // kalau ganti rak, cetak dulu $struk
                    if ($rakSebelum <> $rakSekarang) {
                        // kirim ke printer
                        $perintah = "echo \"$struk\" |lpr $perintahPrinter -l";
                        exec($perintah, $output);
                        //echo $struk;

                        $struk = "";
                        $rakSebelum = $rakSekarang;

                        // cetak header
                        $hasil = mysql_query("SELECT namaRak FROM rak WHERE idRak = $x[idRak]");
                        $r = mysql_fetch_array($hasil);
                        $struk .= "\n\nRak #$x[idRak] : $r[namaRak] \n ===============";
                    }; // if ($rakSebelum <> $rakSekarang)
                    // cetak data barang
                    $struk .= "\n $x[namaBarang] \n Harga: $x[hargaJual], Jumlah: $x[jumBarang]";
                }; // for ($i = 1; $i <= $jumlahBarang; $i++)
                // cetak baris terakhir
                $perintah = "echo \"$struk\" |lpr $perintahPrinter -l";
                exec($perintah, $output);
                //echo $struk;
            }; // if ($_GET[printer] == '0')


            break;




        case "ApproveFastSO1": // ========================================================================================================================
            // cari SO yang belum di approve
            $sql = "SELECT DISTINCT tanggalSO FROM fast_stock_opname WHERE approved=0 ORDER BY tanggalSO ASC";
            $hasil = mysql_query($sql);

            echo "
		<h2>Approve Fast Stock Opname</h2>
		<form method=GET action='media.php'>

	<br /><br />

	<table>
	<tr>
		<td><br /> (t) Pilih Tanggal SO </td>
                <td><br /> : <select name='tanggalSO' accesskey='t'>";

            while ($x = mysql_fetch_array($hasil)) {
                echo "<option value='$x[tanggalSO]'>$x[tanggalSO]</option>";
            }

            echo "</select></td>
	</tr>

	<tr>
		<td colspan=2><br /><input type=submit accesskey='s' value='(s) Submit'></td>
	</tr>

		<input type=hidden name=module value=barang>
		<input type=hidden name=act value=ApproveFastSO2>
		</table></form>";

            break;



        case "ApproveFastSO2":  // ----------------------------------------------------------------------------
            // cari SO yang belum di approve di tanggalSO
            $sql = "SELECT * FROM fast_stock_opname WHERE tanggalSO='$_GET[tanggalSO]' AND approved=0 ORDER BY idRak,jmlTercatat DESC";
            $hasil1 = mysql_query($sql);

            echo "
		<h2>Approve Fast Stock Opname</h2>
		<form method=POST action='?module=barang&act=ApproveFastSO3'>

	<br /><br />

	<table border=1>
	<tr>
		<td><center>
			Rak
		</center></td>
		<td>Barcode
		</td>
		<td>Nama Barang
		</td>
		<td><center>
			Jumlah<br />Tercatat
		</center></td>
		<td><center>
			Selisih
		</center></td>
		<td><center>
			Approve
		</center></td>
		<td><center>#</center>
		</td>
		<td><center>
			Salah<br />Rak
		</center></td>
		<td><center>
			Hapus<br />Barang
		</center></td>
	</tr>
	";

            $x = mysql_fetch_array($hasil1);
            $rakSekarang = $x[idRak];
            $rakSebelum = $x[idRak];
            $ctr = 1;
            $ctrRec = 1;
            $jumlahRecord = mysql_num_rows($hasil1);

            do {

                $rakSekarang = $x[idRak];

                if (strlen($x[namaBarang]) > 0) {
                    echo "
			<tr>
			<td><center>
				$x[idRak]
			</center></td>
			<td>$x[barcode] 	<input type=hidden name=barcode$ctr value=$x[barcode]>
			</td>
			<td>$x[namaBarang]
			</td>
			<td><center>
				$x[jmlTercatat]
			</center></td>
			<td><center>
				$x[selisih]	<input type=hidden name=selisih$ctr value=$x[selisih]>
			</center></td>
			<td><center>
				<input type=checkbox name=appr$ctr checked=yes>
			</center></td>
			<td><center>#</center>
			</td>
			<td><center>
				<input type=checkbox name=salahrak$ctr>
			</center></td>
			<td><center>
				<input type=checkbox name=hapus$ctr>
			</center></td>
			</tr>
		";
                }; // if (strlen($x[namaBarang]) > 0) {

                if (($rakSebelum <> $rakSekarang) || ($ctrRec == $jumlahRecord)) {
                    // cari barang di rak yang sama, namun tidak masuk di dalam SO = sebetulnya berada di rak yang lain / sudah tidak ada lagi
                    $sql = "SELECT b.*
					FROM barang AS b LEFT JOIN fast_stock_opname AS f ON b.barcode = f.barcode
					WHERE b.idRak=$rakSekarang AND f.idRak IS NULL ORDER BY b.namaBarang ASC";
                    //echo $sql;
                    $hasil2 = mysql_query($sql);
                    while ($z = mysql_fetch_array($hasil2)) {
                        $ctr++;
                        echo "
					<tr>
					<td><center>
						$z[idRak]
					</center></td>
					<td>$z[barcode] <input type=hidden name=barcode$ctr value=$z[barcode]>
					</td>
					<td>$z[namaBarang]
					</td>
					<td><center>
						$z[jumBarang]
					</center></td>
					<td><center>
					</center></td>
					<td><center>
					</center></td>
					<td><center>#</center>
					</td>
					<td><center>
						<input type=checkbox name=salahrak$ctr checked=yes>
					</center></td>
					<td><center>
						<input type=checkbox name=hapus$ctr>
					</center></td>
					</tr>
				";
                    }; // while($z = mysql_fetch_array($hasil)){
                }; // if ($rakSebelum <> $rakSekarang) {

                $rakSebelum = $rakSekarang;
                $ctr++;
                $ctrRec++; // tidak menghitung record yang didapat dari barang (ketika mencari barang yg salah rak)
            }
            while ($x = mysql_fetch_array($hasil1));

            echo "</table>

		<input type=submit accesskey='s' value='(s) Submit'>
		<input type=hidden name=ctr value=$ctr>

		</form>";

            break;


        case "ApproveFastSO3":  // ----------------------------------------------------------------------------


            echo "
		<h2>Proses Fast Stock Opname</h2>
	<br /><br />
	";

            for ($i = 1; $i <= $_POST[ctr]; $i++) {

                // cek barang dihapus
                if ($_POST["hapus$i"] == 'on') {
                    // ....still having thoughts about it, for now just ignore.
                    // cek barang yang salah rak (tercatat di barang.idRak di rak ybs - tapi, tidak ketemu di rak tsb pada saat SO)
                }
                elseif ($_POST["salahrak$i"] == 'on') {
                    // ganti barang.idRak ybs menjadi 999999
                    $sql = "UPDATE barang SET idRak=999999 WHERE barcode='" . $_POST["barcode$i"] . "'";
                    $hasil1 = mysql_query($sql);
                    echo "Salah Rak : " . $_POST["barcode$i"] . ", sudah diganti raknya menjadi 999999 <br />";

                    // cek barang yang di approve SO nya
                }
                elseif ($_POST["appr$i"] == 'on') {
                    // cari barang.jumBarang ybs
                    $sql = "SELECT jumBarang FROM barang WHERE barcode='" . $_POST["barcode$i"] . "'";
                    $hasil1 = mysql_query($sql);
                    $x = mysql_fetch_array($hasil1);

                    // hitung jumlah barang yang seharusnya
                    $jumBarang = $x[jumBarang] + $_POST["selisih$i"];

                    // update barang.jumBarang untuk barcode ybs
                    $sql = "UPDATE barang SET jumBarang=$jumBarang WHERE barcode='" . $_POST["barcode$i"] . "'";
                    $hasil1 = mysql_query($sql);

                    // ganti fast_stock_opname.approved menjadi 1 / true
                    $sql = "UPDATE fast_stock_opname SET approved=1 WHERE barcode='" . $_POST["barcode$i"] . "'";
                    $hasil1 = mysql_query($sql);
                    echo "Approved : " . $_POST["barcode$i"] . ", stok tercatat: $x[jumBarang], selisih: " . $_POST["selisih$i"] . ", total: $jumBarang <br />";
                    //var_dump($_POST);
                };
            }; // for ($i = 0; $i <= $_POST[ctr]; $i++) {

            break;



        case "ApproveMobileSO1":  // ----------------------------------------------------------------------------
            // cari SO yang belum di approve
            $sql = "SELECT fast_stock_opname.*, rak.namaRak FROM fast_stock_opname JOIN rak on fast_stock_opname.idRak = rak.idRak WHERE approved=0 LIMIT 100";
            $hasil1 = mysql_query($sql);
            ?>
            <h2>Approve Mobile Stock Opname</h2>
            <form method=POST action='?module=barang&act=ApproveMobileSO2'>

                <br /><br />

                <table class="tabel">
                    <tr>
                        <th>Rak</th>
                        <th>Barcode</th>
                        <th>Nama Barang</th>
                        <th>Jumlah<br />Tercatat</th>
                        <th>Ditemukan</th>
                        <th>Approve</th>
                        <th>#</th>
                        <th>Hapus<br />Barang</td>
                    </tr>
                    <?php
                    $x = mysql_fetch_array($hasil1);
                    $ctr = 1;
                    $jumlahRecord = mysql_num_rows($hasil1);

                    do {

                        if (strlen($x[namaBarang]) > 0) {

                            $sql = "SELECT jumBarang FROM barang WHERE barcode='" . $x[barcode] . "'";
                            $hasil2 = mysql_query($sql);
                            $z = mysql_fetch_array($hasil2);
                            ?>
                            <tr class="<?php echo $ctr % 2 === 0 ? 'alt' : ''; ?>">
                                <td class="center"><?php echo $x['namaRak']; ?></td><input type="hidden" name="idRak<?php echo $ctr; ?>" value="<?php echo $x['idRak']; ?>" />
                            <td><?php echo $x['barcode']; ?><input type=hidden name=barcode<?php echo $ctr; ?> value=<?php echo $x['barcode']; ?>></td>
                            <td><?php echo $x['namaBarang']; ?></td>
                            <td class="center"><?php echo $z['jumBarang']; ?></td>
                            <td class="center"><?php echo $x['selisih']; ?>	<input type=hidden name=selisih<?php echo $ctr; ?> value=<?php echo $x['selisih']; ?>></td>
                            <td class="center"><input type=checkbox name=appr<?php echo $ctr; ?> checked=yes></td>
                            <td class="center">#</td>
                            <td class="center"><input type=checkbox name=hapus<?php echo $ctr; ?>></td>
                            </tr>
                            <?php
                        }; // if (strlen($x[namaBarang]) > 0) {

                        $ctr++;
                    }
                    while ($x = mysql_fetch_array($hasil1));
                    ?>
                </table>

                <input type=submit accesskey='s' value='(s) Submit'>
                <input type=hidden name=ctr value=<?php echo $ctr; ?>>

            </form>
            <?php
            break;


        case "ApproveMobileSO2":  // ----------------------------------------------------------------------------


            echo "
		<h2>Proses Mobile Stock Opname</h2>
	<br /><br />
	";

            for ($i = 1; $i <= $_POST[ctr]; $i++) {

                // cek barang dihapus
                if ($_POST["hapus$i"] == 'on') {
                    // ....still having thoughts about it, for now just ignore.
                    // cek barang yang di approve SO nya
                }
                elseif ($_POST["appr$i"] == 'on') {
                    // cari barang.jumBarang ybs
                    $sql = "SELECT jumBarang FROM barang WHERE barcode='" . $_POST["barcode$i"] . "'";
                    $hasil1 = mysql_query($sql);
                    $x = mysql_fetch_array($hasil1);

                    $jumBarang = $_POST["selisih$i"];

                    // update barang.jumBarang untuk barcode ybs
                    $sql = "UPDATE barang SET jumBarang=$jumBarang, idRak = " . $_POST["idRak$i"] . " WHERE barcode='" . $_POST["barcode$i"] . "'";
                    $hasil1 = mysql_query($sql);

                    // ganti fast_stock_opname.approved menjadi 1 / true
                    $sql = "UPDATE fast_stock_opname SET approved=1 WHERE barcode='" . $_POST["barcode$i"] . "'";
                    $hasil1 = mysql_query($sql);
                    echo "Approved : " . $_POST["barcode$i"] . ", stok tercatat: $x[jumBarang], ditemukan = <b>" . $_POST["selisih$i"] . "</b><br />";
                    //var_dump($_POST);
                };
            }; // for ($i = 0; $i <= $_POST[ctr]; $i++) {

            break;

        case "transfer1":  // ----------------------------------------------------------------------------
            // ambil daftar customer
            $sql = "SELECT idCustomer, namaCustomer
		FROM customer ORDER BY namaCustomer ASC";
            $namaCustomer = mysql_query($sql);

            echo "<h2>Transfer Barang</h2>
              <form method=POST action='modul/js_jual_barang.php?act=caricustomer' onSubmit=\"popupform(this, 'jual_barang')\">
              (i) ID Customer : <select name='idCustomer' accesskey='i'>";

            while ($cust = mysql_fetch_array($namaCustomer)) {
                if ($cust[idCustomer] == 1) {
                    echo "<option value='$cust[idCustomer]' selected>$cust[namaCustomer] :: $cust[idCustomer]</option>\n";
                }
                else {
                    echo "<option value='$cust[idCustomer]'>$cust[namaCustomer] :: $cust[idCustomer]</option>\n";
                };
            }

            echo "
              </select><p>
		<input type=hidden name='transferahad' value='1'>

		<input type=submit value='(p) Pilih Customer' name='cariCustomer' accesskey='p'/>
              </form>";


            break;


        case 'hargajualsync':
            ?>
            <h2>Sinkronisasi Harga Jual - Pilih Rak</h2>
            <form method=POST action='?module=barang&act=hargajualsync2'>

                <table>
                    <tr>
                        <td>(r) Rak</td>
                        <td> : <select name="rak" accesskey="r">
                                <option value="0">-- Pilih Rak --</option>
                                <?php
                                while ($rak = mysql_fetch_array($ambilRak)) {
                                    echo "<option value='$rak[idRak]'>$rak[namaRak]</option>";
                                }
                                ?>
                            </select></td></tr>

                    <tr><td><button type="submit" accesskey="O"><u>O</u>K</button></td></tr>

                </table>
            </form>
            <?php
            break;

        case 'hargajualsync2':

            $idRak = $_POST['rak'];
            $result = mysql_query("select * from rak where idRak={$idRak}") or die(mysql_error());
            $rak = mysql_fetch_array($result);
            if (isset($_POST['sinkronisasi_harga'])) {
                echo 'Proses Sinkronisasi..<br />';

                $sql = "CREATE TABLE IF NOT EXISTS `audit_ubah_harga_jual` (
								  `uid` int(11) NOT NULL AUTO_INCREMENT,
								  `barcode` varchar(30) NOT NULL,
								  `harga_jual_awal` bigint(20) DEFAULT NULL,
								  `harga_jual_baru` bigint(20) DEFAULT NULL,
								  `nama_barang` varchar(30) DEFAULT NULL,
								  `user_name` varchar(30) DEFAULT NULL,
								  `lastupdate` datetime DEFAULT NULL,
								  PRIMARY KEY (`uid`)
								) ENGINE=MyISAM";
                $result = mysql_query($sql) or die(mysql_error());
                if ($result) {
                    echo 'Pembuatan Tabel Transaksi.. Selesai<br />';
                }
                $lastupdate = date('Y-m-d H:i:s');
                // Masukkan transaksi sinkronisasi ke tabel audit untuk pencatatan
                $sql = "
									insert into audit_ubah_harga_jual (barcode, nama_barang, harga_jual_awal, harga_jual_baru, user_name, lastupdate)
									select b.barcode, b.namaBarang, b.hargaJual as hargaJualAwal, rhj.hargaJual as hargaJualBaru, '{$_SESSION['uname']}','{$lastupdate}'
									from barang b
									join kategori_barang kb on kb.idKategoriBarang = b.idKategoriBarang
									join satuan_barang sb on sb.idSatuanBarang = b.idSatuanBarang
									join rujukan_harga_jual rhj on rhj.barcode = b.barcode
									where idRak = {$idRak} and rhj.hargaJual > b.hargaJual
									order by namaBarang";
                $result = mysql_query($sql) or die(mysql_error());
                if ($result) {
                    echo 'Pencatatan Transaksi.. Selesai<br />';
                }
                // Setelah dicatat, update tabel barang sesuai dengan yang ada di tabel audit
                $sql = "update barang b
										join audit_ubah_harga_jual audit on audit.barcode = b.barcode
										set b.hargaJual = audit.harga_jual_baru
										where b. idRak = {$idRak}";
                $result = mysql_query($sql) or die(mysql_error());
                if ($result) {
                    ?>
                    Sinkronisasi Harga.. Selesai<br />
                    Proses Sinkronisasi Selesai<br /><br />
                    <form method="POST" action="modul/mod_barang.php?act=cetakhargajualsync" onSubmit="popupform(this, 'cetaklabel')">
                        <input type="hidden" name="lastupdate" value="<?php echo $lastupdate; ?>" />
                        <input type="submit" name="cetak" value="Cetak label harga yang disinkronisasi" />
                    </form>
                    <?php
                }
            }

            // Tampilkan barang yang perlu disinkronisasi harga jual
            // yaitu yang harga jual < dari harga jual baru

            $sql = "select b.barcode, b.namaBarang,  kb.namaKategoriBarang, sb.namaSatuanBarang, b.hargaJual as hargaJualAwal, rhj.hargaJual as hargaJualBaru
								from barang b
								join kategori_barang kb on kb.idKategoriBarang = b.idKategoriBarang
								join satuan_barang sb on sb.idSatuanBarang = b.idSatuanBarang
								join rujukan_harga_jual rhj on rhj.barcode = b.barcode
								where idRak = {$idRak} and rhj.hargaJual > b.hargaJual
								order by namaBarang";
            $result = mysql_query($sql) or die(mysql_error());
            ?>
            <h2>Sinkronisasi Harga Jual - <?php echo $rak['namaRak']; ?></h2>
            <?php
            if (mysql_num_rows($result) > 0) {
                ?>
                <form method="POST">
                    <input type="hidden" name="rak" value="<?php echo $idRak; ?>" />
                    <button type="submit" name="sinkronisasi_harga" accesskey="u"><u>U</u>pdate Harga ke Harga Jual Baru</button>
                </form>
                <br />
                <table class="tabel">
                    <tr>
                        <th>No</th>
                        <th>Barcode</th>
                        <th>Nama Barang</th>
                        <th>Kategori Barang</th>
                        <th>Satuan Barang</th>
                        <th>Harga Jual Awal</th>
                        <th>Harga Jual Baru</th>
                    </tr>
                    <?php
                    $no = 1;
                    while ($data_audit = mysql_fetch_array($result)):
                        ?>
                        <tr <?php echo $no % 2 == 0 ? 'class="alt"' : ''; ?>>
                            <td class="center"><?php echo $no; ?></td>
                            <td><?php echo $data_audit['barcode']; ?></td>
                            <td><?php echo $data_audit['namaBarang']; ?></td>
                            <td class="center"><?php echo $data_audit['namaKategoriBarang']; ?></td>
                            <td class="center"><?php echo $data_audit['namaSatuanBarang']; ?></td>
                            <td class="right"><?php echo $data_audit['hargaJualAwal']; ?></td>
                            <td class="right"><?php echo $data_audit['hargaJualBaru']; ?></td>
                        </tr>
                        <?php
                        $no++;
                    endwhile;
                    ?>
                </table>
                <br />
                <form method="POST">
                    <input type="hidden" name="rak" value="<?php echo $idRak; ?>" />
                    <button type="submit" name="sinkronisasi_harga" accesskey="u"><u>U</u>pdate Harga ke Harga Jual Baru</button>
                </form
                <?php
            } else {
                ?>
                <p>Harga Sudah OK, tidak ada yang perlu disinkronisasi</p>
                <?php
            }
            ?>
            <br />
            <?php
            break;

        case 'cetakhargajualsync':
            include "../../config/config.php";
            if ($_POST['cetak']) {
                $lastupdate = $_POST['lastupdate'];
                $sql = "select *
					from barang
					join audit_ubah_harga_jual audit on audit.barcode = barang.barcode
					where audit.lastupdate = '{$lastupdate}'";
                $result = mysql_query($sql) or die(mysql_error());

                $lebar_label = 200;
                $tinggi_label = 112;
                $label_per_baris = 3;
                $baris_per_halaman = 7;

                $total = mysql_num_rows($result);
                $baris = 1;
                $kolom = 1;
                echo "<div style=\"float:none\">";

                for ($i = 1; $i <= $total; $i++) {

                    $r = mysql_fetch_array($result);

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

                    $namaBarang = $r['namaBarang'];
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
					" . number_format($r['hargaJual'], 0, ',', '.') . "	</p>
				<p style=\"line-height:0px; text-align:left; font-family:Arial; font-size:6pt; \">
				{$r['barcode']} - {$r['idRak']}
				</div>
			";

                    $kolom++;
                } // for

                echo "</div>";
            }




            break;
        case 'diskon':
            $sql = "select uid, nama, deskripsi from diskon_tipe where uid >= 1000 order by uid";
            $rDiskonTipe = mysql_query($sql) or die(mysql_error());
            if (isset($_POST['submit'])) {
                $diskonDetail = $_POST['diskon_detail'];
                //echo '<pre>';
                //print_r($diskonDetail);
                //echo '</pre>';
                $ketemuError = false;
                if (!($diskonDetail['diskon_tipe_id'] > 0)) {
                    $errorDiskonTipeId = 'Tipe Diskon Harus Dipilih!';
                    $ketemuError = true;
                }
                if ($diskonDetail['barcode'] == '') {
                    $errorBarcode = 'Barcode Harus Diisi!';
                    $ketemuError = true;
                }
                $min_item = 1;
                if ($diskonDetail['min_item'] > 1) {
                    $min_item = $diskonDetail['min_item'];
                }

                if (!$ketemuError) {
                    $tanggalDari = date_format(date_create_from_format('d-m-Y H:i', $diskonDetail['tanggal_dari']), 'Y-m-d H:i');
                    $tanggalSampai = date_format(date_create_from_format('d-m-Y H:i', $diskonDetail['tanggal_sampai']), 'Y-m-d H:i');
                    $rDiskon = mysql_query("select uid, nama from diskon_tipe where uid={$diskonDetail['diskon_tipe_id']}") or die(mysql_error());
                    $diskonTipe = mysql_fetch_array($rDiskon);
                    $sqlInsert = "insert into diskon_detail (diskon_tipe_id, diskon_tipe_nama, barcode, tanggal_dari, tanggal_sampai, diskon_persen, diskon_rupiah, min_item, max_item) "
                            . "values({$diskonDetail['diskon_tipe_id']},"
                            . "'{$diskonTipe['nama']}',"
                            . "'{$diskonDetail['barcode']}',"
                            . "'{$tanggalDari}',"
                            . "'{$tanggalSampai}',"
                            . "{$diskonDetail['diskon_persen']},"
                            . "{$diskonDetail['diskon_rupiah']},"
                            . "{$min_item},"
                            . "{$diskonDetail['max_item']})";
                    //echo $sqlInsert;
                    mysql_query($sqlInsert) or die(mysql_error());
                }
            }
            ?>
            <h2>Diskon</h2>
            <form method="POST">
                <table>
                    <tr>
                        <td>Tipe Diskon</td>
                        <td>
                            <select name="diskon_detail[diskon_tipe_id]" id="diskonTipeId">
                                <option>Pilih satu..</option>
                                <?php
                                while ($diskonTipe = mysql_fetch_array($rDiskonTipe)):
                                    ?>
                                    <option value="<?php echo $diskonTipe['uid']; ?>"><?php echo $diskonTipe['nama'] . ' :: ' . $diskonTipe['deskripsi']; ?></option>
                                    <?php
                                endwhile;
                                ?>
                            </select>
                            <?php echo isset($errorDiskonTipeId) ? $errorDiskonTipeId : ''; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Barcode</td>
                        <td><input type="text" id="barcode" name="diskon_detail[barcode]" style="margin-right:5px;" /><?php echo isset($errorBarcode) ? $errorBarcode : ''; ?><span id="barcode-info"></span></td>
                    </tr>
                    <tr>
                        <td>Periode</td>
                        <td>
                            <input type="text" id="tanggal_dari" name="diskon_detail[tanggal_dari]" value="">
                            -
                            <input type="text" id="tanggal_sampai" name="diskon_detail[tanggal_sampai]" value="">
                            <script type="text/javascript">
                                $("#diskonTipeId").change(function () {
                                    var diskonId = $(this).val();
                                    // 1000:gudang; 10001:waktu
                                    if (diskonId == 1000) {
                                        $(".show-on-grosir-only").show();
                                        $(".show-on-waktu-only").hide();
                                    } else if (diskonId == 1001) {
                                        $(".show-on-grosir-only").hide();
                                        $(".show-on-waktu-only").show();
                                    }
                                });

                                $(function () {
                                    $('#tanggal_dari').appendDtpicker({
                                        "closeOnSelected": true,
                                        'locale': 'id',
                                        'dateFormat': 'DD-MM-YYYY hh:mm'
                                    });
                                });
                                $(function () {
                                    $('#tanggal_sampai').appendDtpicker({
                                        "closeOnSelected": true,
                                        'locale': 'id',
                                        'dateFormat': 'DD-MM-YYYY hh:mm'
                                    });
                                });
                                $("#barcode").blur(function () {
                                    $("#barcode-info").load("aksi.php?module=diskon&act=getbarcodeinfo&barcode=" + $(this).val());
                                })
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <td>Diskon (%)</td>
                        <td><input type="text" name="diskon_detail[diskon_persen]" value="0"/></td>
                    </tr>
                    <tr>
                        <td>Diskon @ (Rp)</td>
                        <td><input type="text" name="diskon_detail[diskon_rupiah]" value="0"/></td>
                    </tr>
                    <tr class="show-on-grosir-only">
                        <td>Jumlah Barang Minimum</td>
                        <td><input type="text" name="diskon_detail[min_item]" value="0"/></td>
                    </tr>
                    <tr class="show-on-waktu-only">
                        <td>Jumlah Barang Maksimum</td>
                        <td><input type="text" name="diskon_detail[max_item]" value="0" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="submit" value="submit" /></td>
                    </tr>
                </table>
            </form>
            <hr />
            <table class="tabel">
                <tr>
                    <th>Tipe Diskon</th>
                    <th>Barcode</th>
                    <th>Nama Barang</th>
                    <th>Dari</th>
                    <th>Sampai</th>
                    <th>Diskon(%)</th>
                    <th>Diskon@(Rp)</th>
                    <th>Qty Min</th>
                    <th>Qty Max</th>
                    <th>Aktif</th>
                </tr>
                <?php
                $sql = "select dd.uid, diskon_tipe_nama, dd.barcode, barang.namaBarang, tanggal_dari, tanggal_sampai, diskon_persen, diskon_rupiah, min_item, max_item, dd.status
							from diskon_detail dd
							join barang on barang.barcode = dd.barcode
                            where dd.status=1
							order by dd.status desc, dd.uid desc";
                $result = mysql_query($sql) or die(mysql_error());
                while ($diskonDetail = mysql_fetch_array($result)):
                    ?>
                    <tr>
                        <td><?php echo $diskonDetail['diskon_tipe_nama']; ?></td>
                        <td><?php echo $diskonDetail['barcode']; ?></td>
                        <td><?php echo $diskonDetail['namaBarang']; ?></td>
                        <td><?php echo date_format(date_create_from_format('Y-m-d H:i:s', $diskonDetail['tanggal_dari']), 'd-m-Y H:i'); ?></td>
                        <td><?php echo date_format(date_create_from_format('Y-m-d H:i:s', $diskonDetail['tanggal_sampai']), 'd-m-Y H:i'); ?></td>
                        <td><?php echo $diskonDetail['diskon_persen']; ?></td>
                        <td><?php echo $diskonDetail['diskon_rupiah']; ?></td>
                        <td><?php echo $diskonDetail['min_item']; ?></td>
                        <td><?php echo $diskonDetail['max_item']; ?></td>
                        <td>
                            <select class="status-diskon" id="<?php echo $diskonDetail['uid']; ?>" >
                                <option value="1" <?php echo $diskonDetail['status'] ? 'selected' : ''; ?>>Ya</option>
                                <option value="0" <?php echo $diskonDetail['status'] ? '' : 'selected'; ?>>Tidak</option>
                            </select>
                        </td>
                    </tr>
                    <?php
                endwhile;
                ?>
            </table>
            <script>
                $(".status-diskon").change(function () {
                    var diskonId = $(this).attr("id");
                    var status = $(this).val();
                    var data = "id=" + diskonId + "&status=" + status;
                    var url = "aksi.php?module=barang&act=diskonupdate";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        success: function () {
                            //location.reload()
                        },
                    });
                });

            </script>
            <?php
            break;
        case 'pindahsupplier':
            $sqlSupplier = getSupplier();
            ?>
            <h2>Pindah Supplier</h2>
            <form method="POST">
                <label for="supplierasal">Pilih Supplier Asal</label><br />
                <select name="idSupplier" id="supplierasal" style="width:30%">
                    <?php
                    while ($data = mysql_fetch_array($sqlSupplier)) :
                        ?>
                        <option value="<?php echo $data['idSupplier']; ?>" <?php echo $data['idSupplier'] == $_POST['idSupplier'] ? 'selected' : ''; ?>>
                            <?php
                            echo $data['namaSupplier'];
                            echo trim($data['alamatSupplier']) === '' ? '' : ' | ' . $data['alamatSupplier'];
                            ?>
                        </option>
                        <?php
                    endwhile;
                    ?>
                </select>
                <input type=submit value='(D)isplay Barang' accesskey='d' name='displayBarang' />
            </form>
            <br />
            <?php
            /*
             * Jika ada post displayBarang, tampilkan barang supplier tersebut
             */
            if (isset($_POST['displayBarang'])):
                $sql = "select barcode, namaBarang, kb.namaKategoriBarang, sb.namaSatuanBarang, jumBarang, hargaJual
							from barang b
							left join kategori_barang kb on kb.idKategoriBarang = b.idKategoriBarang
							left join satuan_barang sb on sb.idSatuanBarang = b.idSatuanBarang
							where b.idSupplier={$_POST['idSupplier']} and (b.nonAktif <> 1 or b.nonAktif is null)";
                $result = mysql_query($sql) or die("Gagal ambil dari barang, supplier id#{$_POST['idSupplier']}, error:" . mysql_error());
                ?>
                <form method="POST" action="?module=barang&act=pindahsupplier2">
                    <input type="hidden" name="idSupplierAsal" value="<?php echo $_POST['idSupplier']; ?>"/>
                    <label for="suppliertujuan">Pilih Supplier Tujuan</label><br />
                    <select name="idSupplierTujuan" id="raktujuan" style="width:30%">
                        <?php
                        mysql_data_seek($sqlSupplier, 0);
                        while ($data = mysql_fetch_array($sqlSupplier)) :
                            ?>
                            <option value="<?php echo $data['idSupplier']; ?>" <?php echo $data['idSupplier'] == $_POST['idSupplier'] ? 'selected' : ''; ?>>
                                <?php
                                echo $data['namaSupplier'];
                                echo trim($data['alamatSupplier']) === '' ? '' : ' | ' . $data['alamatSupplier'];
                                ?>
                            </option>
                            <?php
                        endwhile;
                        ?>
                    </select>
                    <br />
                    <table class="tabel">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Jumlah</th>
                                <th>Harga Jual</th>
                                <th>Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($barang = mysql_fetch_array($result)):
                                ?>
                                <tr <?php echo ($i % 2 === 0) ? 'class="alt"' : ''; ?>>
                                    <td><?php echo $barang['barcode']; ?></td>
                                    <td><?php echo $barang['namaBarang']; ?></td>
                                    <td><?php echo $barang['namaKategoriBarang']; ?></td>
                                    <td><?php echo $barang['namaSatuanBarang']; ?></td>
                                    <td class="center"><?php echo $barang['jumBarang']; ?></td>
                                    <td class="right"><?php echo number_format($barang['hargaJual'], 0, ',', '.'); ?></td>
                                    <td class="center"><input type="checkbox" name="barcode[]" value="<?php echo $barang['barcode']; ?>"/></td>
                                </tr>
                                <?php
                                $i++;
                            endwhile;
                            ?>
                        </tbody>
                    </table>
                    <input type="submit" name="pilihBarang" value="Pindahkan Barang" />
                </form>
                <?php
            endif;
            break;
        case 'pindahsupplier2':
            if (isset($_POST['pilihBarang'])):
                foreach ($_POST['barcode'] as $barcode):
                    mysql_query("update barang set idSupplier={$_POST['idSupplierTujuan']} where barcode='{$barcode}'") or die('Gagal update supplier barang, error: ' . mysql_error());
                endforeach;

                $result = mysql_query("select * from supplier where idSupplier={$_POST['idSupplierAsal']}") or die(mysql_error());
                $supplierAsal = mysql_fetch_array($result);
                $result = mysql_query("select * from supplier where idSupplier={$_POST['idSupplierTujuan']}") or die(mysql_error());
                $supplierTujuan = mysql_fetch_array($result);
                ?>
                <h2>Pindah Supplier</h2>
                <p>
                    Barang sudah dipindahkan dari <?php echo $supplierAsal['namaSupplier']; ?> ke <?php echo $supplierTujuan['namaSupplier']; ?>
                </p>
                <p>
                    Daftar Barang:
                </p>
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Jumlah</th>
                            <th>Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($_POST['barcode'] as $barcode):
                            $sql = "select barcode, namaBarang, kb.namaKategoriBarang, sb.namaSatuanBarang, jumBarang, hargaJual
							from barang b
							left join kategori_barang kb on kb.idKategoriBarang = b.idKategoriBarang
							left join satuan_barang sb on sb.idSatuanBarang = b.idSatuanBarang
							where b.barcode='{$barcode}'";
                            $result = mysql_query($sql) or die("Gagal ambil data barang, barcode#{$barcode}, error:" . mysql_error());
                            $barang = mysql_fetch_array($result);
                            ?>
                            <tr <?php echo ($i % 2 === 0) ? 'class="alt"' : ''; ?>>
                                <td><?php echo $barang['barcode']; ?></td>
                                <td><?php echo $barang['namaBarang']; ?></td>
                                <td><?php echo $barang['namaKategoriBarang']; ?></td>
                                <td><?php echo $barang['namaSatuanBarang']; ?></td>
                                <td class="center"><?php echo $barang['jumBarang']; ?></td>
                                <td class="right"><?php echo number_format($barang['hargaJual'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php
                            $i++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
                <?php
            endif;
            break;
        case 'pindahrak':
            ?>
            <h2>Pindah Rak</h2>
            <form method="POST">
                <label for="rakasal">Pilih Rak Asal</label><br />
                <select name="idRak" id="rakasal" style="width:30%">
                    <?php
                    $sqlRak = "select * from rak";
                    $hasilSqlRak = mysql_query($sqlRak) or die('Gagal ambil data rak, error:' . mysql_error());
                    while ($data = mysql_fetch_array($hasilSqlRak)) :
                        ?>
                        <option value="<?php echo $data['idRak']; ?>" <?php echo $data['idRak'] == $_POST['idRak'] ? 'selected' : ''; ?>>
                            <?php
                            echo $data['namaRak'];
                            ?>
                        </option>
                        <?php
                    endwhile;
                    ?>
                </select>
                <input type=submit value='(D)isplay Barang' accesskey='d' name='displayBarang' />
            </form>
            <br />
            <?php
            /*
             * Jika ada post displayBarang, tampilkan barang rak tersebut
             */
            if (isset($_POST['displayBarang'])):
                $sql = "select barcode, namaBarang, kb.namaKategoriBarang, supplier.namaSupplier, sb.namaSatuanBarang, jumBarang, hargaJual
							from barang b
							left join kategori_barang kb on kb.idKategoriBarang = b.idKategoriBarang
							left join satuan_barang sb on sb.idSatuanBarang = b.idSatuanBarang
							left join supplier on supplier.idSupplier = b.idSupplier
							left join rak on rak.idRak = b.idRak
                            where b.idRak = {$_POST['idRak']} and (b.nonAktif <> 1 or b.nonAktif is null)
                            order by namaSupplier, namaBarang";
                $result = mysql_query($sql) or die("Gagal ambil dari barang, supplier id#{$_POST['idSupplier']}, error:" . mysql_error());
                ?>
                <form method="POST" action="?module=barang&act=pindahrak2">
                    <input type="hidden" name="idRakAsal" value="<?php echo $_POST['idRak']; ?>"/>
                    <label for="raktujuan">Pilih Rak Tujuan</label><br />
                    <select name="idRakTujuan" id="raktujuan" style="width:30%">
                        <?php
                        mysql_data_seek($hasilSqlRak, 0);
                        while ($data = mysql_fetch_array($hasilSqlRak)) :
                            ?>
                            <option value="<?php echo $data['idRak']; ?>">
                                <?php
                                echo $data['namaRak'];
                                ?>
                            </option>
                            <?php
                        endwhile;
                        ?>
                    </select>
                    <br />
                    <table class="tabel">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Supplier</th>
                                <th>Satuan</th>
                                <th>Jumlah</th>
                                <th>Harga Jual</th>
                                <th>Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($barang = mysql_fetch_array($result)):
                                ?>
                                <tr <?php echo ($i % 2 === 0) ? 'class="alt"' : ''; ?>>
                                    <td><?php echo $barang['barcode']; ?></td>
                                    <td><?php echo $barang['namaBarang']; ?></td>
                                    <td><?php echo $barang['namaKategoriBarang']; ?></td>
                                    <td><?php echo $barang['namaSupplier']; ?></td>
                                    <td><?php echo $barang['namaSatuanBarang']; ?></td>
                                    <td class="center"><?php echo $barang['jumBarang']; ?></td>
                                    <td class="right"><?php echo number_format($barang['hargaJual'], 0, ',', '.'); ?></td>
                                    <td class="center"><input type="checkbox" name="barcode[]" value="<?php echo $barang['barcode']; ?>"/></td>
                                </tr>
                                <?php
                                $i++;
                            endwhile;
                            ?>
                        </tbody>
                    </table>
                    <input type="submit" name="pilihBarang" value="Pindahkan Barang" />
                </form>
                <?php
            endif;
            break;
        case 'pindahrak2':
            if (isset($_POST['pilihBarang'])):
                foreach ($_POST['barcode'] as $barcode):
                    mysql_query("update barang set idRak={$_POST['idRakTujuan']} where barcode='{$barcode}'") or die('Gagal update rak barang, error: ' . mysql_error());
                endforeach;

                $result = mysql_query("select * from rak where idRak={$_POST['idRakAsal']}") or die(mysql_error());
                $rakAsal = mysql_fetch_array($result);
                $result = mysql_query("select * from rak where idRak={$_POST['idRakTujuan']}") or die(mysql_error());
                $rakTujuan = mysql_fetch_array($result);
                ?>
                <h2>Pindah Rak</h2>
                <p>
                    Barang sudah dipindahkan dari <?php echo $rakAsal['namaRak']; ?> ke <?php echo $rakTujuan['namaRak']; ?>
                </p>
                <p>
                    Daftar Barang:
                </p>
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Supplier</th>
                            <th>Satuan</th>
                            <th>Jumlah</th>
                            <th>Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($_POST['barcode'] as $barcode):
                            $sql = "select barcode, namaBarang, kb.namaKategoriBarang, supplier.namaSupplier, sb.namaSatuanBarang, jumBarang, hargaJual
							from barang b
							left join kategori_barang kb on kb.idKategoriBarang = b.idKategoriBarang
							left join satuan_barang sb on sb.idSatuanBarang = b.idSatuanBarang
                            left join supplier on supplier.idSupplier = b.idSupplier
							where b.barcode='{$barcode}'";
                            $result = mysql_query($sql) or die("Gagal ambil data barang, barcode#{$barcode}, error:" . mysql_error());
                            $barang = mysql_fetch_array($result);
                            ?>
                            <tr <?php echo ($i % 2 === 0) ? 'class="alt"' : ''; ?>>
                                <td><?php echo $barang['barcode']; ?></td>
                                <td><?php echo $barang['namaBarang']; ?></td>
                                <td><?php echo $barang['namaKategoriBarang']; ?></td>
                                <td><?php echo $barang['namaSupplier']; ?></td>
                                <td><?php echo $barang['namaSatuanBarang']; ?></td>
                                <td class="center"><?php echo $barang['jumBarang']; ?></td>
                                <td class="right"><?php echo number_format($barang['hargaJual'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php
                            $i++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
                <?php
            endif;
            break;

        case "ApprovePdtSO1":  // ----------------------------------------------------------------------------
            // cari SO yang belum di approve
            $sql = "SELECT fast_stock_opname.*, rak.namaRak FROM fast_stock_opname JOIN rak on fast_stock_opname.idRak = rak.idRak WHERE approved=0 and username='pdt-so' order by fast_stock_opname.uid";
            $hasil1 = mysql_query($sql);
            ?>
            <h2>Approve Stock Opname dengan PDT (Portable Data Terminal)</h2>
            <form method=POST action='?module=barang&act=ApprovePdtSO2'>

                <br /><br />

                <table class="tabel">
                    <tr>
                        <th>Rak</th>
                        <th>Barcode</th>
                        <th>Nama Barang</th>
                        <th>Jumlah<br />Tercatat</th>
                        <th>Ditemukan</th>
                        <th>Selisih</th>
                        <th>Approve</th>
                        <th>#</th>
                        <th>Batal</td>
                    </tr>
                    <?php
                    $ctr = 1;
                    while ($x = mysql_fetch_array($hasil1)) {

                        $sql = "SELECT namaBarang FROM barang WHERE barcode='" . $x[barcode] . "'";
                        $hasil2 = mysql_query($sql);
                        $z = mysql_fetch_array($hasil2);
                        ?>
                        <tr class="<?php echo $ctr % 2 === 0 ? 'alt' : ''; ?>">
                            <td class="center"><?php echo $x['namaRak']; ?></td><input type="hidden" name="dataApproval[<?php echo $ctr; ?>][idRak]" value="<?php echo $x['idRak']; ?>" />
                        <td><?php echo $x['barcode']; ?><input type=hidden name="dataApproval[<?php echo $ctr; ?>][barcode]" value=<?php echo $x['barcode']; ?>></td>
                        <td><?php echo $z['namaBarang']; ?></td>
                        <td class="center"><?php echo $x['jmlTercatat']; ?></td>
                        <td class="center"><?php echo $x['jmlTercatat'] + $x['selisih']; ?></td>
                        <td class="center"><?php echo $x['selisih']; ?>	<input type=hidden name="dataApproval[<?php echo $ctr; ?>][selisih]" value=<?php echo $x['selisih']; ?>></td>
                        <td class="center"><input type=checkbox name="dataApproval[<?php echo $ctr; ?>][appr]" checked=yes></td>
                        <td class="center">#</td>
                        <td class="center"><input type=checkbox name="dataApproval[<?php echo $ctr; ?>][batal]"></td>
                        </tr>
                        <?php
                        $ctr++;
                    }
                    ?>
                </table>

                <input type=submit accesskey='s' value='(s) Submit'>

            </form>
            <?php
            break;

        case "ApprovePdtSO2":  // ----------------------------------------------------------------------------
            ?>
            <h2>Proses PDT Stock Opname</h2>
            <?php
            if (isset($_POST['dataApproval'])):
                $dataApproval = $_POST['dataApproval'];
                //echo '<pre>';
                //print_r($dataApproval);
                //echo '</pre>';
                ?>
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barcode</th>
                            <th>Nama Barang</th>
                            <th>Jumlah Barang<br /></th>
                            <th>Selisih</th>
                            <th>Jumlah Barang<br />Saat ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($dataApproval as $data):
                            // Cek barang dihapus
                            if ($data['batal'] == 'on'):
                                // Berarti barang tidak jadi di SO
                                // Hapus dari tabel fast SO
                                $sql = "delete from fast_stock_opname where barcode = '{$data['barcode']}' and username='pdt-so'";
                                mysql_query($sql) or die('Gagal hapus data so: ' . mysql_error());

                            // Jika diapprove, ubah jumlah barang di tabel barang dan detail_beli, ubah status approve di tabel so
                            // Dan tampilkan layar
                            elseif ($data['appr'] == 'on') :
                                // data barang
                                $sql = "update barang set jumBarang = jumBarang+{$data['selisih']}, idRak = {$data['idRak']} where barcode='{$data['barcode']}'";
                                mysql_query($sql) or die('Gagal update jumBarang: ' . mysql_error());

                                // Update detail beli juga
                                // Init detail beli (dinol kan)
                                $sql = "update detail_beli set jumBarang=0, isSold='Y' where barcode = '{$data['barcode']}' ";
                                mysql_query($sql) or die('Gagal init detail_beli, error: ' . mysql_error());

                                // Ambil jumlah barang setelah diupdate (SO)
                                $sql = "select jumBarang, namaBarang from barang where barcode = '{$data['barcode']}' ";
                                $hasil = mysql_query($sql) or die('Gagal ambil data barang: ' . mysql_error());
                                $barang = mysql_fetch_row($hasil, MYSQL_ASSOC);
                                $sql = "select *
                                from detail_beli db
                                join transaksibeli tb on tb.idTransaksiBeli = db.idTransaksiBeli
                                where barcode = '{$data['barcode']}'
                                order by db.idTransaksiBeli desc";
                                $resultDetailBeli = mysql_query($sql) or die('Gagal Ambil Detail Beli, error: ' . mysql_error());

                                $jumBarang = $barang['jumBarang'];

                                $simulasi = false; // Variabel untuk testing.. (just for programmers)
                                // Sesuaikan jumlah barang di tabel detail_beli
                                while (($detailBeli = mysql_fetch_array($resultDetailBeli)) && $jumBarang > 0):

                                    /*
                                     * Jika pembelian (detail_beli.jumlahBarangAsli) lebih besar dari stock (barang.jumBarang)
                                     * langsung update detail_beli.jumBarang  dengan barang.jumBarang
                                     * Jika lebih kecil
                                     * update detail_beli.jumBarang dengan jumlah pembelian (detail_beli.jumBarangAsli)
                                     * yang kemudian mencari lagi di row selanjutnya
                                     */
                                    if ($detailBeli['jumBarangAsli'] >= $jumBarang) {
                                        if (!$simulasi) {
                                            mysql_query("update detail_beli set jumBarang = {$jumBarang}, isSold='N' where idDetailBeli={$detailBeli['idDetailBeli']}") or die('Gagal update detailbeli script 1, error: ' . mysql_error());
                                        }
                                        //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;detail beli {$detailBeli['idDetailBeli']} {$detailBeli['tglTransaksiBeli']} jumlahBarangAsli={$detailBeli['jumBarangAsli']}: UPDATE jumBarang=<b>{$jumBarang}</b> ";
                                        $jumBarang = 0;
                                    }
                                    else {
                                        if (!$simulasi) {
                                            mysql_query("update detail_beli set jumBarang = jumBarangAsli, isSold='N'
                                              where idDetailBeli={$detailBeli['idDetailBeli']}") or die('Gagal update detailbeli script 2, error: ' . mysql_error());
                                        }
                                        $jumBarang -= $detailBeli['jumBarangAsli'];

                                        //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;detail beli {$detailBeli['idDetailBeli']} {$detailBeli['tglTransaksiBeli']} jumlahBarangAsli={$detailBeli['jumBarangAsli']}: UPDATE jumBarang=<b>{$detailBeli['jumBarangAsli']}</b>, Sisa={$jumBarang}";
                                    }
                                    echo '<br />';
                                endwhile;

                                // Approve SO
                                $sql = "update fast_stock_opname set approved = 1 where barcode = '{$data['barcode']}' and username='pdt-so' ";
                                mysql_query($sql) or die('Gagal approved pdt SO: ' . mysql_error());
                                ?>
                                <tr class="<?php echo $i % 2 === 0 ? 'alt' : ''; ?>">
                                    <td class="right"><?php echo $i; ?></td>
                                    <td><?php echo $data['barcode']; ?></td>
                                    <td><?php echo $barang['namaBarang'];; ?></td>
                                    <td class="right"><?php echo $barang['jumBarang'] - $data['selisih']; ?></td>
                                    <td class="right"><?php echo $data['selisih']; ?></td>
                                    <td class="right"><?php echo $barang['jumBarang']; ?></td>
                                </tr>
                                <?php
                                $i++;
                            endif;
                        endforeach;
                        ?>
                    </tbody>
                </table>
                <?php
            endif;


//            for ($i = 1; $i <= $_POST[ctr]; $i++) {
//
//                // cek barang dihapus
//                if ($_POST["batal$i"] == 'on') {
//                }
//                elseif ($_POST["appr$i"] == 'on') {
//                    // cari barang.jumBarang ybs
//                    $sql = "SELECT jumBarang FROM barang WHERE barcode='" . $_POST["barcode$i"] . "'";
//                    $hasil1 = mysql_query($sql);
//                    $x = mysql_fetch_array($hasil1);
//
//                    $jumBarang = $_POST["selisih$i"];
//
//                    // update barang.jumBarang untuk barcode ybs
//                    $sql = "UPDATE barang SET jumBarang=$jumBarang, idRak = " . $_POST["idRak$i"] . " WHERE barcode='" . $_POST["barcode$i"] . "'";
//                    $hasil1 = mysql_query($sql);
//
//                    // ganti fast_stock_opname.approved menjadi 1 / true
//                    $sql = "UPDATE fast_stock_opname SET approved=1 WHERE barcode='" . $_POST["barcode$i"] . "'";
//                    $hasil1 = mysql_query($sql);
//                    echo "Approved : " . $_POST["barcode$i"] . ", stok tercatat: $x[jumBarang], ditemukan = <b>" . $_POST["selisih$i"] . "</b><br />";
//                    //var_dump($_POST);
//                };
//            }; // for ($i = 0; $i <= $_POST[ctr]; $i++) {

            break;
    }
    ?>
    <?php
    /* CHANGELOG -----------------------------------------------------------

      1.6.0 / 2013-05-01 : Herwono		: fitur : cetak label harga perbarcode
      1.6.0 / 2013-02-24 : Harry Sufehmi	: fitur : transfer barang antar sesama pengguna AhadPOS
      1.5.5 / 2013-01-25 : Harry Sufehmi 	: bugfix: https://github.com/sufehmi/AhadPOS/issues/1 ,
      terimakasih http://www.facebook.com/civo.pras untuk laporannya.

      1.5.0 / 2012-11-25 : Harry Sufehmi 	: optimisasi : query yang menampilkan seluruh data barang.
      Credit : Insan Fajar
      1.5.0 / 2012-09-09 : Harry Sufehmi	: bugfix: form inputSO3 gagal jika ada > 250 item di suatu rak.
      Ternyata... default setting max_input_vars = 1000, sedangkan setiap item menyimpan 4 jenis informasi = lebih besar dari batas max_input_vars
      Solusi: set max_input_vars di php.ini menjadi 20000 atau lebih

      1.2.5 / 2012-03-02 : Harry Sufehmi 	: bugfix: editbarang kini menemukan item berdasarkan barcode - bukan idBarang
      1.0.3 / 2011-10-21 : Harry Sufehmi	: Kategori Barang & Satuan Barang kini muncul pada tabel daftar barang (tadinya kosong)
      Juga pada sub-modul :
      # caribarang2
      # cetaklabel2

      Thanks kepada Alexander (mr.s4scha@gmail.com) untuk laporannya.

      1.0.2 / 2011-07-14 : Harry Sufehmi	: menu "editbarang", bisa mendeteksi perubahan barcode.
      (sehingga di aksi.php bisa update juga field barcode di table-table lainnya)

      1.0.1 / 2010-06-03 : Harry Sufehmi	: various enhancements, bugfixes
      # fitur Stock Opname
      # Cari Barang : bisa per Rak
      2010-12-16  : Harry Sufehmi	: Cetak Stock Barang

      2011-01-07  : Harry Sufehmi	: Input Fast Stock Opname & Approve Fast Stock Opname


      0.6.5		    : Gregorius Arief	: initial release

      ------------------------------------------------------------------------ */
    ?>
