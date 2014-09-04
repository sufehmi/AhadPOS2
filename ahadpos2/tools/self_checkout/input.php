<?php
include '../../config/config.php';
mysql_close();

$link = mysqli_connect($server, $username, $password) or die("Koneksi gagal");
mysqli_select_db($link, $database) or die("Database tidak bisa dibuka");

$clientIP = $_SERVER['REMOTE_ADDR'];

$selfCheckOutTemp = mysqli_query($link, "SELECT b.barcode, b.namaBarang, sct.qty, sct.harga_jual, sct.qty * sct.harga_jual as sub_total
                                            FROM self_checkout_temp sct
                                            JOIN barang b on b.barcode = sct.barcode
                                            WHERE ip4 = '{$clientIP}'
                                            ORDER by sct.id desc");
?>

<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ahadmart - Self Checkout</title>

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/foundation.css">

        <!-- Ahadmart Style -->
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr.js"></script>

    </head>
    <body id="halaman-input">
        <div class="row">
            <div class="medium-8 columns">
                <div class="row">
                    <div class="small-2 columns">
                        <a href="#" id="tombol-batal" class="secondary large button expand">BATAL</a>
                    </div>
                    <div class="small-2 columns">
                        <a href="#" class="success large button expand">SELESAI</a> 
                    </div>
                    <div class="small-8 columns">
                        <div class="panel" style="background-color: rgba(0, 0, 0, 0.5);" ><h4 style="font-weight: 400;color: #fff">Total <span class="rata-kanan">123.456.789</span></h4></div>
                    </div>
                </div>
                <table width="100%" style="background-color: rgba(255,255,255,0.9)">
                    <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Nama Barang</th>
                            <th class="kanan">Harga</th>
                            <th class="kanan">Qty</th>
                            <th class="kanan">Sub Total</th>
                            <th class="tengah">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="detail">
                        <?php
                        while ($detail = mysqli_fetch_array($selfCheckOutTemp)):
                            ?>
                            <tr>
                                <td><?php echo $detail['barcode']; ?></td>
                                <td><?php echo $detail['namaBarang']; ?></td>
                                <td class="kanan"><?php echo number_format($detail['harga_jual'], 0, ',', '.'); ?></td>
                                <td class="kanan"><?php echo $detail['qty']; ?></td>
                                <td class="kanan"><?php echo number_format($detail['sub_total'], 0, ',', '.'); ?></td>
                                <td class="tengah"><a href="" class="tiny alert button kecil">X</a></td>
                            </tr>
                            <?php
                        endwhile;
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="medium-4 columns"> 
                <div class="row collapse">
                    <div class="small-12 columns" style="text-align: center; background-color: rgba(255, 255, 255, 0.875);">
                        <img style=" padding: 10px" src="img/ahadmart-logo.png" />
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                    <div class="small-12 columns">
                        <input id="scan" type="text" placeholder="Scan Barcode" style="background-color: rgba(0, 0, 0, 0.9);"/>
                    </div>
                </div>
                <!--<span class="label sc-nomor">Self Checkout # 98374</span>-->
                <div class="row">
                    <div class="small-3 columns">
                        <a href="#" class="large button expand">7</a>
                    </div>
                    <div class="small-3 columns">
                        <a href="#" class="large button expand">8</a>
                    </div>
                    <div class="small-3 columns">
                        <a href="#" class="large button expand">9</a>
                    </div>
                    <div class="small-3 columns">
                        <a href="#" class="alert large button expand">C</a>
                    </div>
                </div>
                <div class="row">
                    <div class="small-3 columns">
                        <a href="#" class="large button expand">4</a>
                    </div>
                    <div class="small-3 columns">
                        <a href="#" class="large button expand">5</a>
                    </div>
                    <div class="small-3 columns">
                        <a href="#" class="large button expand">6</a>
                    </div>
                    <div class="small-3 columns">
                        <a href="#" class="alert large button expand">DEL</a>
                    </div>
                </div>
                <div class="row">
                    <div class="small-3 columns">
                        <a href="#" class="large button expand">1</a>
                    </div>
                    <div class="small-3 columns">
                        <a href="#" class="large button expand">2</a>
                    </div>
                    <div class="small-3 columns">
                        <a href="#" class="large button expand">3</a>
                    </div>
                    <div class="small-3 columns">
                        <a href="#" class="disabled secondary large button expand">&nbsp;</a>
                    </div>
                </div>
                <div class="row">
                    <div class="small-6 columns">
                        <a href="#" class="large button expand">0</a>
                    </div>
                    <div class="small-6 columns">
                        <a href="#" class="warning large button expand">ENTER</a>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/vendor/jquery.js"></script>
        <script src="js/foundation.min.js"></script>
        <script>
            $(document).foundation();

            $("#tombol-batal").click(function() {
                window.location = "index.php?sum=batal";
            });

            $(document).ready(function() {
                $("#scan").val("");
                $("#scan").focus();
            });

            function updateTabelDetail() {
                $("#detail").load("aksi.php?refresh=1");
            }

            $("#scan").keyup(function(e) {
                if (e.keyCode === 13) {
                    var barcode = $(this).val();
                    var dataKirim = {
                        tambah: true,
                        barcode: barcode,
                    }
                    $.ajax({
                        type: "POST",
                        url: 'aksi.php',
                        data: dataKirim,
                        dataType: "json",
                        success: function(data) {
                            if (data.sukses) {
                                updateTabelDetail();
                                $("#scan").val("");
                                $("#scan").focus();
                            }
                        }
                    });
                }
                return false;
            });
        </script>


<!--        <table>
            <tr id="row1">
                <td>A</td>
                <td>One</td>
                <td>Red</td>
                <td>Apple</td>
                <td>$0.99</td>
                <td><a href="">Show row 2</a></td>
            </tr>
        </table>-->
        <script type="text/javascript">
            $(document).ready(function() {
                // content to add after the current row
                var $row2 = $('<tr id="row2">' +
                        '<td>B</td>' +
                        '<td>Two</td>' +
                        '<td>Yellow</td>' +
                        '<td>Banana</td>' +
                        '<td>$1.23</td>' +
                        '<td> </td>' +
                        '</tr>');

                // add hidden divs around the content of all the TD tags
                $row2.find('td').wrapInner('<div style="display:none" />');

                // add this row after the first row
                $('#row1').after($row2);

                $('#row1 a').click(function() {
                    if ($('#row2 div').is(":visible")) {
                        // hide the div
                        $('#row2 div').slideUp(700);

                        // update link text
                        $('#row1 a').text('show row 2');

                    } else {
                        // slide the div into view
                        $('#row2 div').slideDown(700);

                        // update link text
                        $('#row1 a').text('hide row 2');
                    }

                    // prevent the click on the link from propagating
                    return false;
                });
            });
        </script>
    </body>
</html>