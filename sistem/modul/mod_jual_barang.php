<?php
/* mod_jual_barang.php ------------------------------------------------------
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

check_user_access(basename($_SERVER['SCRIPT_NAME']));


//HS javascript untuk menampilkan popup
?>

<SCRIPT TYPE="text/javascript">
<!--
   function popupform(myform, windowname)
   {
      if (!window.focus)
         return true;
      window.open('', windowname, 'type=fullWindow,fullscreen=yes,scrollbars=yes');
      myform.target = windowname;
      return true;
   }
//-->
</SCRIPT>

<?php
// ambil daftar customer yang bukan member saja
// Untuk member, nanti dientry di tampilan POS
$sql = "SELECT idCustomer, namaCustomer
		FROM customer WHERE member=0 ORDER BY idCustomer ASC";
$namaCustomer = mysql_query($sql);
?>
<h2>Penjualan Barang</h2>
<form method=POST action='modul/js_jual_barang.php?act=caricustomer' onSubmit="popupform(this, 'jual_barang')">
   (i) ID Customer : <select name='idCustomer' accesskey='i'>

      <?php
      /*
        <h2>Penjualan Barang</h2>
        <form method=POST target="_blank" action="modul/js_jual_barang_2.php?act=caricustomer">
        (i) ID Customer : <select name='idCustomer' accesskey='i'>";
       *
       */
      ?>
      <?php
      while ($cust = mysql_fetch_array($namaCustomer)) :
         if ($cust[idCustomer] == 1) {
            echo "<option value='$cust[idCustomer]' selected>$cust[namaCustomer]</option>\n";
         } else {
            echo "<option value='$cust[idCustomer]'>$cust[namaCustomer]</option>\n";
         };
      endwhile;
      ?>
   </select>
   <input type=submit value='(p) Pilih Customer' name='cariCustomer' accesskey='p'/>
</form>
<?php
/** Menampilkan tombol untuk membuka cash drawer
 * Jika Admin
 */
if ($_SESSION['leveluser'] === 'admin') {
   $tampil = mysql_query("SELECT workstation_address,namaWorkstation,keterangan FROM workstation ORDER BY namaWorkstation");
   ?>
   <label>
      Buka <u>C</u>ash Drawer
      <select id="workstation" accesskey="c">
         <?php
         while ($ws = mysql_fetch_array($tampil)):
            ?>
            <option value="<?php echo $ws['workstation_address']; ?>"><?php echo $ws['namaWorkstation']; ?></option>
            <?php
         endwhile;
         ?>
      </select>
      <a href="#" id="tombol-buka" class="tombol" accesskey="b"><u>B</u>uka</a>
   </label>
   <script>
      $("#tombol-buka").click(function () {
         var dataKirim = {
            workstation: $("#workstation").val()
         };
         $.ajax({
            type: "POST",
            data: dataKirim
         });
      });
   </script>
   <?php
} else if ($_SESSION['leveluser'] === 'kasir') {
   /** Jika kasir, ada tombol buka cash drawer
    *
    */
   $result = mysql_query("SELECT workstation.workstation_address ip
                            FROM kasir
                            JOIN workstation on workstation.idWorkstation = kasir.currentWorkstation
                            where kasTutup is null and idUser={$_SESSION['iduser']}");
   $workstation = mysql_fetch_array($result);
   ?>
   <a href="#" id="tombol-buka" class="tombol" accesskey="C">Buka <u>C</u>ash Drawer</a>
   <script>
      $("#tombol-buka").click(function () {
         var dataKirim = {
            workstation: "<?php echo $workstation['ip']; ?>"
         };
         $.ajax({
            type: "POST",
            data: dataKirim
         });
      });
   </script>
   <?php
}
?>
<div>
   <a href="../tools/self_checkout/cekharga.php" class="tombol" accesskey="h"/>Cek <b><u>H</u></b>arga Jual</a>
</div>
<?php
//Cek jika ada post workstation untuk buka cash drawer
if (isset($_POST['workstation'])):
   $ip = $_POST['workstation'];
   $perintahPrinter = "-H $ip -P printer$ip";
   $command = chr(27)."@"; //Init printer

   $command .= chr(27).chr(101).chr(1); //1 reverse lf
   $command .= chr(27).chr(112).chr(48).chr(60).chr(120); // buka cash drawer
   $command .= chr(27).chr(101).chr(1); //1 reverse lf
   $perintah = "echo \"$command\" |lpr $perintahPrinter ";
   exec($perintah, $output);
endif;
?>
<?php
/* CHANGELOG -----------------------------------------------------------

  1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
  0.6.5		    : Gregorius Arief		: initial release

  ------------------------------------------------------------------------ */
?>
