<?php
include '../../config/config.php';
mysql_close();

$link = mysqli_connect($server, $username, $password) or die("Koneksi gagal");
mysqli_select_db($link, $database) or die("Database tidak bisa dibuka");

$clientIP = $_SERVER['REMOTE_ADDR'];
?>

<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>AhadPOS - Cek Harga</title>

      <link rel="stylesheet" href="css/normalize.css">
      <link rel="stylesheet" href="css/foundation.css">
      <link rel="stylesheet" href="css/font-awesome.css">

      <!-- Ahadmart Style -->
      <link rel="stylesheet" href="css/main.css">

      <script src="js/vendor/modernizr.js"></script>

   </head>
   <body id="halaman-input">
      <div class="row">
         <div class="medium-7 large-8 columns">
            <div class="row">
               <div class="small-12 columns">
                  <a href="../../sistem/media.php?module=penjualan_barang" class="success large button expand" accesskey="m">KE<u>M</u>BALI KE PENJUALAN</a> 
               </div>
            </div>
            <div class="row">
               <div class="small-12 columns">
                  <div class="panel" style="background-color: rgba(0, 0, 0, 0.5);" >
                     <h4 style="font-weight: 400;color: #fff">&nbsp;
                        <span class="" id="view-nama"></span>
                     </h4>
                     <h4 style="font-weight: 400;color: #fff">&nbsp;
                        <span class="" id="view-barcode"></span>
                     </h4>                     
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="small-12 columns">
                  <div class="panel" style="background-color: rgba(0, 0, 0, 0.5);" >
                     <h1 style="font-weight: 700;color: #fff">&nbsp;<span class="rata-kanan" id="view-harga"></span>
                     </h1>
                  </div>
               </div>
            </div>
         </div>
         <div class="medium-5 large-4 columns"> 
            <div class="row collapse">
               <div class="small-12 columns" style="text-align: center; background-color: rgba(255, 255, 255, 0.875);">
                  <img style=" padding: 10px" src="img/logo.png" />
               </div>
            </div>
            <div class="row" style="margin-top: 20px;margin-bottom: 20px">
               <div class="small-12 columns">
                  <input id="scan" type="text" placeholder="Scan Barcode" style="background-color: rgba(0, 0, 0, 0.9);" autofocus="autofocus"/>
               </div>
            </div>
            <!--<span class="label sc-nomor">Self Checkout # 98374</span>-->
            <div class="panel" style="background-color: rgba(0,0,0,0.4); padding-bottom: 0px">
               <div class="row">
                  <div class="small-3 columns">
                     <a href="#" class="large button expand keynum">7</a>
                  </div>
                  <div class="small-3 columns">
                     <a href="#" class="large button expand keynum">8</a>
                  </div>
                  <div class="small-3 columns">
                     <a href="#" class="large button expand keynum">9</a>
                  </div>
                  <div class="small-3 columns">
                     <a href="#" class="alert large button expand keynum">C</a>
                  </div>
               </div>
               <div class="row">
                  <div class="small-3 columns">
                     <a href="#" class="large button expand keynum">4</a>
                  </div>
                  <div class="small-3 columns">
                     <a href="#" class="large button expand keynum">5</a>
                  </div>
                  <div class="small-3 columns">
                     <a href="#" class="large button expand keynum">6</a>
                  </div>
                  <div class="small-3 columns">
                     <a href="#" class="alert large button expand keynum">DEL</a>
                  </div>
               </div>
               <div class="row">
                  <div class="small-3 columns">
                     <a href="#" class="large button expand keynum">1</a>
                  </div>
                  <div class="small-3 columns">
                     <a href="#" class="large button expand keynum">2</a>
                  </div>
                  <div class="small-3 columns">
                     <a href="#" class="large button expand keynum">3</a>
                  </div>
                  <div class="small-3 columns">
                     <a href="#" class="disabled secondary large button expand">&nbsp;</a>
                  </div>
               </div>
               <div class="row">
                  <div class="small-6 columns">
                     <a href="#" class="large button expand keynum">0</a>
                  </div>
                  <div class="small-6 columns">
                     <a href="#" class="warning large button expand keynum" id="enter">ENTER</a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script src="js/vendor/jquery.js"></script>
      <script src="js/foundation.min.js"></script>
      <script>
         $(document).foundation();

         function isiView(data) {
            $("#view-barcode").html(data.barcode);
            $("#view-nama").html(data.nama);
            $("#view-harga").html(data.harga);
         }

         function kirimBarcode(barcode) {
            var dataKirim = {
               cekharga: true,
               barcode: barcode
            };
            $.ajax({
               type: "POST",
               url: 'aksi.php',
               data: dataKirim,
               dataType: "json",
               success: function (data) {
                  if (data.sukses) {
                     isiView(data);
                     $("#scan").val("");
                     $("#scan").focus();
                  }
                  $("#enter").html('ENTER');
                  $("#enter").removeClass('disable');
               }
            });
         }

         $(document).ready(function () {
            $("#scan").val("");
            $("#scan").focus();
         });

         $("#scan").keyup(function (e) {
            if (e.keyCode === 13) {
               $("#enter").html('Proses..');
               $("#enter").addClass('disable');
               var barcode = $(this).val();
               kirimBarcode(barcode);
            }
            return false;
         });

         $('a.keynum').click(function (e) {
            var nilai = $(e.target).text();
            console.log(nilai);
            var barcode = $("#scan").val();
            if (nilai >= 0) {
               $("#scan").val(barcode + nilai);
            } else if (nilai === 'DEL') {
               $("#scan").val(barcode.substring(0, barcode.length - 1));
            } else if (nilai === 'C') {
               $("#scan").val("");
            } else if (nilai === 'ENTER') {
               $(this).html('Proses..');
               $(this).addClass('disable');
               kirimBarcode(barcode);
            }
            $("#scan").focus();
            return false;
         });

         $(document).on("click", ".button.kecil.hapus", function () {
            var barcode = $(this).attr('id');
            deleteBarcode(barcode);
            return false;
         });

      </script>
   </body>
</html>