<?php
include '../../config/config.php';
mysql_close();

session_start();
if (empty($_SESSION['namauser'])) {
    ?>
    <link href='../../css/style.css' rel='stylesheet' type='text/css'>
    <center>Untuk mengakses tools ini, Anda harus login <br>
        <a href="../../index.php"><b>LOGIN</b></a></center>
    <?php
    die();
}

$link = mysqli_connect($server, $username, $password) or die("Koneksi gagal");
mysqli_select_db($link, $database) or die("Database tidak bisa dibuka");
$result = mysqli_query($link, "select * from config");
$namaToko = '';
while ($config = mysqli_fetch_array($result)) :
    if ($config['option'] === 'store_name'):
        $namaToko = $config['value'];
        break;
    endif;
endwhile;

$resultSupplier = mysqli_query($link, "select * from supplier order by namaSupplier") or die('Gagal ambil data supplier' . mysqli_error($link));
?>

<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ahadmart - buat RPO</title>

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/foundation.css">

        <!-- Ahadmart RPO Style -->
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr.js"></script>

    </head>
    <body id="halaman-depan">
        <form method="POST" action="aksi.php?act=tambahrpo"> 
            <div class="row">
                <div class="small-12 columns">
                    <h4>Buat RPO (Rencana Purchase Order) Per SUPPLIER Per Rak</h4>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <select id="pilih-supplier" name="param[supplier_id]">
                        <option>Pilih Supplier ..</option>
                        <?php
                        while ($supplier = mysqli_fetch_array($resultSupplier)):
                            ?>
                            <option value="<?php echo $supplier['idSupplier']; ?>"><?php echo $supplier['namaSupplier']; ?></option>
                            <?php
                        endwhile;
                        ?>
                    </select>
                </div>
            </div>
            <div class="row" id="parameter-rpo">
                <div class="large-6 columns"> 
                    <fieldset>
                        <legend>Parameter perhitungan </legend>
                        <div class="row"> 
                            <div class="small-6 columns">
                                <label for="input-range" class="right inline" accesskey="r"><u>R</u>ange analisa penjualan</label>
                            </div>
                            <div class="small-6 columns">
                                <div class="row collapse">
                                    <div class="small-9 columns">
                                        <input id="input-range" name="param[range]" type="text" value="120"/>
                                    </div>
                                    <div class="small-3 columns">
                                        <span class="postfix">hari</span>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row"> 
                            <div class="small-6 columns">
                                <label for="input-buffer" class="right inline" accesskey="b"><u>B</u>uffer Stock</label>
                            </div>
                            <div class="small-6 columns">
                                <div class="row collapse">
                                    <div class="small-9 columns">
                                        <input id="input-buffer" name="param[buffer]" type="text" value="30"/>
                                    </div>
                                    <div class="small-3 columns">
                                        <span class="postfix">%</span>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row"> 
                            <div class="small-6 columns">
                                <label for="input-interval" class="right inline" >Periode delivery Supplier</label>
                            </div>
                            <div class="small-6 columns">
                                <div class="row collapse">
                                    <div class="small-9 columns">
                                        <input id="input-interval" type="text" disabled/>
                                    </div>
                                    <div class="small-3 columns">
                                        <span class="postfix">hari</span>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row"> 
                            <div class="small-6 columns">
                                <label for="input-tiba-gudang" class="right inline" accesskey="g">Pesananan tiba di <u>g</u>udang</label>
                            </div>
                            <div class="small-6 columns">
                                <div class="row collapse">
                                    <div class="small-9 columns">
                                        <input id="input-tiba-gudang" type="text" value="2"/>
                                    </div>
                                    <div class="small-3 columns">
                                        <span class="postfix">hari</span>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row"> 
                            <div class="small-6 columns">
                                <label for="input-tiba-toko" class="right inline" accesskey="t">Pesanan tiba di <u>t</u>oko</label>
                            </div>
                            <div class="small-6 columns">
                                <div class="row collapse">
                                    <div class="small-9 columns">
                                        <input id="input-tiba-toko" type="text" value="3"/>
                                    </div>
                                    <div class="small-3 columns">
                                        <span class="postfix">hari</span>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row"> 
                            <div class="small-6 columns">
                                <label for="input-jml-hari" class="right inline" accesskey="j"><u>J</u>umlah pemesanan untuk persediaan</label>
                            </div>
                            <div class="small-6 columns">
                                <div class="row collapse">
                                    <div class="small-9 columns">
                                        <input id="input-jml-hari" name='param[jumlah_hari_persediaan]' type="text" value="12"/>
                                    </div>
                                    <div class="small-3 columns">
                                        <span class="postfix">hari</span>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </fieldset>
                    <input type="submit" value="Submit" class="button radius" />
                    </form>
                </div> 
            </div>
            <div id="footer">
                <span></span>
            </div>
            <script src="js/vendor/jquery.js"></script>
            <script src="js/foundation.min.js"></script>
            <script>
                $(document).foundation();

                function hitungJumlahHariPersediaan() {
                    var jmlHari;
                    jmlHari = parseInt($("#input-interval").val()) + parseInt($("#input-tiba-gudang").val()) + parseInt($("#input-tiba-toko").val())
                    console.log('hitung hari');
                    $("#input-jml-hari").val(jmlHari);
                }

                $("#input-interval").change(hitungJumlahHariPersediaan);
                $("#input-tiba-gudang").change(hitungJumlahHariPersediaan);
                $("#input-tiba-toko").change(hitungJumlahHariPersediaan);

                $("#pilih-supplier").change(function() {
                    var dataKirim = {
                        supplierId: $("#pilih-supplier").val(),
                    };
                    $.ajax({
                        type: "POST",
                        url: 'aksi.php?act=getinterval',
                        data: dataKirim,
                        dataType: "json",
                        success: function(data) {
                            if (data.sukses) {
                                $("#input-interval").val(data.interval);
                            }
                        }
                    });
                });

            </script>
    </body>
</html>

