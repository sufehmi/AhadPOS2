<?php

/* function.php ------------------------------------------------------
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

session_start();

function uang($duit) {
   if (!$duit) {
      $duit = 0;
   }
   $duit = "".str_replace(",", ".", number_format($duit)).""; # Melakukan Format bilangan untuk pembagian digit 3 mis: 10000 menjadi 10.000
   return $duit;
}

function getKasAwal($idUser) {
   $tgl = date("Y-m-d");
   $queryKas = mysql_query("SELECT kasAwal FROM kasir WHERE idUser = '$idUser' and tglBukaKasir LIKE '$tgl%'");
   $dataKas = mysql_fetch_array($queryKas);
   $kas = $dataKas[kasAwal];

   return $kas;
}

function getUangKasir($idUser) {
   $tgl = date("Y-m-d");
   $query = mysql_query("SELECT sum(nominal) AS uang FROM transaksijual WHERE tglTransaksiJual LIKE '$tgl%' and idUser = '$idUser'");
   $dataUang = mysql_fetch_array($query);
   $uang = $dataUang[uang];
   if ($uang == null) {
      return $uang = 0;
   } else {
      return $uang;
   }
}

function findSupplier($idSupplier) {
   $sql = "SELECT * from supplier WHERE idSupplier = '$idSupplier'";
   $query = mysql_query($sql);
   $dataSupplier = mysql_fetch_array($query);
   //var_dump($dataSupplier);
   #session_register("idSupplier");
   #session_register("namaSupplier");
   if ($dataSupplier) {
      $_SESSION['idSupplier'] = $dataSupplier['idSupplier'];
      $_SESSION['namaSupplier'] = $dataSupplier['namaSupplier'];
   };
}

function releaseSupplier() {
   #session_unregister("idSupplier");
   #session_unregister("namaSupplier");
}

function findCustomer($idCustomer) {
   $query = mysql_query("SELECT * from customer WHERE idCustomer = '$idCustomer'");
   $dataCustomer = mysql_fetch_array($query);

   #session_register("idCustomer");
   #session_register("namaCustomer");
   $_SESSION[idCustomer] = $dataCustomer[idCustomer];
   $_SESSION[namaCustomer] = $dataCustomer[namaCustomer];
   $_SESSION['customerDiskonP'] = $dataCustomer['diskon_persen'];
   $_SESSION['customerDiskonR'] = $dataCustomer['diskon_rupiah'];
   $_SESSION['isMember'] = $dataCustomer['member']; // 0=bukan member; 1=member
}

function releaseCustomer() {
   #session_unregister("idCustomer");
   #session_unregister("namaCustomer");
   unset($_SESSION['idCustomer']);
   unset($_SESSION['namaCustomer']);
   $_SESSION[tot_pembelian] = 0;
   unset($_SESSION['range']);
   unset($_SESSION['periode']);
   unset($_SESSION['persediaan']);
   unset($_SESSION['hakAdmin']);
}

function cekBarang($barcode) {
   // jika ada banyak barang dengan barcode yang sama, kembalikan record yang terbaru
   $sql = "SELECT b.idBarang, b.namaBarang, b.hargaJual, b.barcode, d.hargaBeli, b.jumBarang FROM barang AS b, detail_beli AS d
        	    WHERE b.barcode = '$barcode' AND d.barcode = '$barcode' ORDER BY d.idBarang DESC LIMIT 1";
   //echo $sql;
   $query = mysql_query($sql);
   $data = mysql_fetch_array($query);

   //HS jika tidak ada data yang ditemukan - mungkin baru ada di tabel barang, tapi belum ada di detail_beli
   if (mysql_num_rows($query) < 1) {
      $sql = "SELECT idBarang, namaBarang, hargaJual, barcode FROM barang WHERE barcode = '$barcode'";
      $query = mysql_query($sql);
      $data = mysql_fetch_array($query);
   };

   return $data;
}

function cekBarangTemp($idSupplier, $barcode) {
   $adaBeli = 0;
   $cek = mysql_query("SELECT * from tmp_detail_beli where idSupplier = '$idSupplier' and barcode = '$barcode'");
   $adaBeli = mysql_num_rows($cek);

   return $adaBeli;
}

/*
  function cekBarangTempJual($idCustomer, $barcode) {
  $adaJual = 0;
  $sql = "SELECT * from tmp_detail_jual where idCustomer = '$idCustomer' and barcode = '$barcode' and username = '$_SESSION[uname]'";
  //echo $sql;
  $cek = mysql_query($sql);
  $adaJual = mysql_num_rows($cek);
  return $adaJual;
  }
 */

function cekBarangTempJual($idCustomer, $barcode) {
   $adaJual = 0;
   $sql = "SELECT sum(jumBarang) as jumBarang from tmp_detail_jual where "
           ."idCustomer = '$idCustomer' "
           ."and barcode = '$barcode' "
           ."and username = '$_SESSION[uname]' "
           ."group by barcode";
   //echo $sql;
   $cek = mysql_query($sql);
   $barang = mysql_fetch_array($cek);
   return $barang;
}

function tambahBarangAda($idSupplier, $barcode, $jumBarang) {
   $ambilJumBarang = mysql_query("SELECT jumBarang FROM tmp_detail_beli WHERE idSupplier = '$idSupplier' and barcode = '$barcode'");
   $dataJum = mysql_fetch_array($ambilJumBarang);
   $jumlah = $dataJum[jumBarang] + $jumBarang;
   mysql_query("UPDATE tmp_detail_beli SET jumBarang = '$jumlah' WHERE idSupplier = '$idSupplier' and barcode = '$barcode'");
}

function tambahBarang($idSupplier, $barcode, $jumBarang, $hargaBeli, $hargaJual, $tglExpire) {
   $tgl = date("Y-m-d");
   mysql_query("INSERT into tmp_detail_beli(idSupplier, tglTransaksi,
                        barcode,tglExpire,jumBarang,hargaBeli,hargaJual,username)
                    VALUES('$idSupplier','$tgl','$barcode','$tglExpire',
                        '$jumBarang','$hargaBeli','$hargaJual','$_SESSION[uname]')");
}

function tambahBarangJualAda($idCustomer, $barcode, $jumBarang) {
   $jumlah = 0;
   $tgl = date("Y-m-d H:i:s");
//  if($jumBarang==0){
//  quantity can not be 0 (zero) or less than that
   if ($jumBarang < 1) {
      $jumlah = 1;
   } else {
      $jumlah = $jumBarang;
   }

   $ambilJumBarang = mysql_query("SELECT uid, jumBarang FROM tmp_detail_jual WHERE idCustomer = '$idCustomer' AND barcode = '$barcode' AND username='$_SESSION[uname]'");
   $dataJum = mysql_fetch_array($ambilJumBarang);

   $jumlah = $jumlah + $dataJum[jumBarang];

   mysql_query("UPDATE tmp_detail_jual SET jumBarang = '$jumlah', tglTransaksi = '$tgl'
		 WHERE idCustomer = '$idCustomer' AND barcode = '$barcode' AND username='$_SESSION[uname]'");

   cekDiskon($dataJum['uid']);
   // Diskon Admin
   // Jika ada diskon admin, akan mengoverride diskon grosir
//	if ($_SESSION['hakAdmin']) {
//		cekDiskonAdmin($dataJum['uid']);
//	}
}

/*
 * ukmMode : Tambahan parameter hargaBarang
 */

function tambahBarangJual($barcode, $jumBarang, $hargaBarang) {
   //cekBarangTempJual($idBarang);
   $ukmMode = is_null($hargaBarang) ? false : true;

   //cek TransferAhad
   $transferAhad = $_POST['transferahad'] ? true : false;
   //cek ReturBeli
   $returBeli = $_POST['returbeli'] ? true : false;

   $dataAda = cekBarang($barcode);
   if ($dataAda != 0) {
      $jumlah = 0;
//      if($jumBarang==0){
      // quantity can not be 0 (zero) or less than that
      if ($jumBarang < 1) {
         $jumlah = 1;
      } else {
         $jumlah = $jumBarang;
      }
      $tgl = date("Y-m-d H:i:s");
      $jualBarang = mysql_query("SELECT * FROM barang WHERE barcode = '$barcode'") or die(mysql_error());
      $jual = mysql_fetch_array($jualBarang);

      // bugfix :
      //	"ORDER BY idDetailBeli" diganti menjadi "ORDER BY idTransaksiBeli"
      //	karena, banyak database di berbagai toko Ahad mart yang isi idDetailBeli nya ngaco
      //	(banyak field idDetailBeli yang isinya 0 [nol])
      // cari hargaBeli & idBarang nya
      $sql = "SELECT * FROM detail_beli
		WHERE isSold = 'N' AND barcode = '$barcode' AND jumBarang > 0
		ORDER BY idTransaksiBeli ASC LIMIT 1";
      //echo $sql;
      $hasil = mysql_query($sql);
      if (mysql_num_rows($hasil) < 1) {  // jika tidak ada / stok sudah habis semua, coba cari lagi dengan menyertakan stok barang = 0
         // tampilkan stok yang terakhir dibeli (ORDER BY idDetailBeli DESC)
         $sql = "SELECT * FROM detail_beli
			WHERE barcode = '$barcode'
			ORDER BY idTransaksiBeli DESC LIMIT 1";
         $hasil = mysql_query($sql);
      }

      $detilBarang = mysql_fetch_array($hasil);
      if (mysql_num_rows($hasil) > 0) {
         $hargaBeli = $detilBarang[hargaBeli];
         $idBarang = $detilBarang[idBarang];
      } else {
         // not supposed to ever happen, but just to be safe....
         //fixme: kalau seluruh stok barang sudah habis (sehingga jadi masuk ke blok ini)
         // -- coba lagi dengan record terakhir utk barang ybs di detail_beli, walaupun isSold=Y
         $hargaBeli = 0;
         $idBarang = 0;
      }

      /*
       * ukmMode: Jika tidak ada $hargaBarang / ukmMode==false maka dipakai harga jual asli
       */
      if (!$ukmMode) {
         $hargaBarang = $jual['hargaJual'];
      }

      $sql = "INSERT into tmp_detail_jual(idCustomer, tglTransaksi,
                            barcode,jumBarang,hargaBeli,hargaJual,username, idBarang)
                        VALUES('$_SESSION[idCustomer]','$tgl','$barcode',
                            '$jumlah','$hargaBeli','$hargaBarang','$_SESSION[uname]', $idBarang)";

      mysql_query($sql) or die(mysql_error());
      $uid = mysql_insert_id();

      // Jika transfer ahad / retur beli, maka diskon dan harga banded diabaikan
      if ($uid && !($transferAhad || $returBeli)) {
         // Cek dan sekaligus tambahkan diskon jika ada
         if ($ukmMode) {
            /*
             * ukmMode: cek diskon admin terlebih dahulu
             */
            cekDiskonAdmin($uid, $barcode, $jumlah);
         } else {
            cekDiskon($uid, $barcode, $jumlah);
         }

         /*
          * Cek dan terapkan harga banded, diskon akan diabaikan (overwrite)
          */
         $paramJual = array(
             'tgl' => $tgl,
             'hargaBeli' => $hargaBeli,
             'hargaBarang' => $hargaBarang,
             'idBarang' => $idBarang,
         );
         cekHargaBanded($uid, $barcode, $jumlah, $paramJual);
      }
   } else {
      echo "Barang tidak ada";
   }
}

function cekDiskon($uid, $barcode, $jumBarang) {
   // Cek dan tambahkan diskon waktu/promo jika ada
   // Jika ada diskon waktu/promo, maka diskon member tidak ada (untuk barang yang sama)
   if (!cekDiskonWaktu($uid) && $_SESSION['isMember']) {
      cekDiskonWaktuMember($uid);
   }
   // eo diskon waktu
   // Cek dan tambahkan diskon grosir jika ada
   // ctt: Diskon grosir akan menambah diskon waktu/promo jika ada
   $diskonGrosir = cekDiskonGrosir($barcode, $jumBarang);
   if ($diskonGrosir) {
      //echo 'ketemu diskon grosir';
      tambahkanDiskonGrosir($barcode, $diskonGrosir);
   }
   // eo diskon grosir

   $diskonCustomer = cekDiskonCustomer($_SESSION['idCustomer']);
   if ($diskonCustomer) {
      //echo 'ketemu diskon customer';
      tambahkanDiskonCustomer($barcode, $diskonCustomer);
   }
}

function tambahkanDiskonCustomer($barcode, $diskonCustomer) {
   $sql = "select uid, diskon_persen, diskon_rupiah, diskon_detail_uids, b.hargaJual
				from tmp_detail_jual tdj
				join barang b on b.barcode = tdj.barcode
				where tdj.username = '{$_SESSION['uname']}' and idCustomer = {$_SESSION['idCustomer']} and tdj.barcode = '$barcode'";
   $hasil = mysql_query($sql) or die("DC: Gagal ambil detail_jual, error: ".mysql_error());
   while ($tdj = mysql_fetch_array($hasil)):
      $nilaiDiskonCustomer = 0;
      if ($diskonCustomer['diskon_persen'] > 0) {
         $nilaiDiskonCustomer = $diskonCustomer['diskon_persen'] / 100 * $tdj['hargaJual'];
      } else {
         $nilaiDiskonCustomer = $diskonCustomer['diskon_rupiah'];
      }
      $totalDiskon = $nilaiDiskonCustomer;
      $hargaJual = $tdj['hargaJual'] - $totalDiskon;

      $uidsDiskon = array('2' => $totalDiskon); // Diskon Customer idnya adalah 2
      // Jika sebelumnya ada diskon waktu/promo,
      // 1. kurangi lagi hargaJual,
      // 2. tambahkan lagi nilai diskon
      // 3. tambahkan lagi uid diskon detail
      if ($tdj['diskon_rupiah'] > 0) {
         $uidsDiskon = json_decode($tdj['diskon_detail_uids'], true);

         // tambahkan nilai diskonnya
         if ($diskonCustomer['diskon_persen'] > 0) {
            $nilaiDiskonCustomer = $diskonCustomer['diskon_persen'] / 100 * ($tdj['hargaJual'] - $tdj['diskon_rupiah']);
         } else {
            $nilaiDiskonCustomer = $diskonCustomer['diskon_rupiah'];
         }
         $totalDiskon = $tdj['diskon_rupiah'] + $nilaiDiskonCustomer;

         $uidsDiskon['2'] = $nilaiDiskonCustomer;
         // kurangi hargaJual nya
         $hargaJual = $tdj['hargaJual'] - $totalDiskon;
      }
      // simpan lagi
      // simpan hanya nilai diskon rupiahnya,
      // diskon persen di nol kan
      $uidsDiskon = json_encode($uidsDiskon);
      //echo 'diskon Customer:'.$uidsDiskon;
      $sql = "update tmp_detail_jual set hargaJual = {$hargaJual}, diskon_persen = 0, diskon_rupiah = {$totalDiskon}, diskon_detail_uids='{$uidsDiskon}' "
              ." where uid={$tdj['uid']}";
      //echo $sql;
      mysql_query($sql) or die("Gagal menambahkan diskon customer, error: ".mysql_error());
   endwhile;
}

function cekDiskonCustomer($idCustomer) {
   $sql = "select diskon_persen, diskon_rupiah "
           ."from customer "
           ."where idCustomer={$idCustomer}";
   $hasil = mysql_query($sql) or die('Gagal ambil data diskon customer, error: '.mysql_error());
   $diskon = mysql_fetch_array($hasil);
   if ($diskon['diskon_persen'] > 0 || $diskon['diskon_rupiah'] > 0) {
      return $diskon;
   } else {
      return false;
   }
}

function cekDiskonGrosir($barcode, $jumBarang) {
   // Cek tabel diskon_detail, apakah ada skema diskon grosir yang cocok
   $sql = "select dd.uid, dd.diskon_persen, dd.diskon_rupiah
				from diskon_detail dd
				where barcode = '$barcode' and
				dd.tanggal_dari<= now() and
				(dd.tanggal_sampai='0000-00-00 00:00:00' or tanggal_sampai >= now() ) and
				dd.min_item<=$jumBarang and
				dd.diskon_tipe_id=1000 and
				dd.status=1
				order by dd.uid desc";
   $hasil = mysql_query($sql) or die("Gagal cek diskon grosir, error: ".mysql_error());
   return mysql_fetch_array($hasil);
}

function tambahkanDiskonGrosir($barcode, $diskonGrosir) {
   $sql = "select uid, diskon_persen, diskon_rupiah, diskon_detail_uids, b.hargaJual
				from tmp_detail_jual tdj
				join barang b on b.barcode = tdj.barcode
				where tdj.username = '{$_SESSION['uname']}' and idCustomer = {$_SESSION['idCustomer']} and tdj.barcode = '$barcode'";
   $hasil = mysql_query($sql) or die("DG: Gagal ambil detail_jual, error: ".mysql_error());
   while ($tdj = mysql_fetch_array($hasil)):

      // Hitung nilai diskon grosir
      $nilaiDiskonGrosir = 0;
      if ($diskonGrosir['diskon_persen'] > 0) {
         $nilaiDiskonGrosir = $diskonGrosir['diskon_persen'] / 100 * $tdj['diskon_rupiah'];
      } else {
         $nilaiDiskonGrosir = $diskonGrosir['diskon_rupiah'];
      }
      $hargaJual = $tdj['hargaJual'] - $nilaiDiskonGrosir;
      $totalDiskon = $nilaiDiskonGrosir;

      $uidsDiskon = array($diskonGrosir['uid'] => $totalDiskon);

      // Jika sebelumnya ada diskon waktu/promo,
      // 1. kurangi lagi hargaJual,
      // 2. tambahkan lagi nilai diskon
      // 3. tambahkan lagi uid diskon detail

      if ($tdj['diskon_rupiah'] > 0) {
         // ambil uid diskon sebelumnya
         $uidsDiskon = json_decode($tdj['diskon_detail_uids'], true);

         // tambahkan nilai diskonnya
         if ($diskonGrosir['diskon_persen'] > 0) {
            $nilaiDiskonGrosir = $diskonGrosir['diskon_persen'] / 100 * ($tdj['hargaJual'] - $tdj['diskon_rupiah']);
         } else {
            $nilaiDiskonGrosir = $diskonGrosir['diskon_rupiah'];
         }
         $totalDiskon = $tdj['diskon_rupiah'] + $nilaiDiskonGrosir;

         // tambahkan uid diskon grosir:
         $uidsDiskon[$diskonGrosir['uid']] = $nilaiDiskonGrosir;

         // kurangi hargaJual nya
         $hargaJual = $tdj['hargaJual'] - $totalDiskon;
      }
      // simpan lagi
      // simpan hanya nilai diskon rupiahnya,
      // diskon persen di nol kan
      $uidsDiskon = json_encode($uidsDiskon);
      //echo 'diskon grosir: '.$uidsDiskon.'  ::';
      $sql = "update tmp_detail_jual set hargaJual = {$hargaJual}, diskon_persen = 0, diskon_rupiah = {$totalDiskon}, diskon_detail_uids='{$uidsDiskon}' "
              ." where uid={$tdj['uid']}";
      //echo $sql;
      mysql_query($sql) or die("Gagal menambahkan diskon grosir, error: ".mysql_error());
   endwhile;
}

function cekDiskonWaktu($uid) {
   $adaDiskonWaktu = false;
   // diskon_tipe_id = 1001
   $sql = "select dd.uid, dd.diskon_persen, dd.diskon_rupiah, b.hargaJual, tdj.jumBarang, dd.max_item,
				tdj.tglTransaksi, tdj.barcode, tdj.hargaBeli, tdj.idCustomer,tdj.username, tdj.idBarang
				from tmp_detail_jual tdj
				join diskon_detail dd on dd.barcode = tdj.barcode
				join barang b on b.barcode = dd.barcode
				where tdj.uid=$uid and dd.status=1 and
				dd.tanggal_dari<= now() and
				(dd.tanggal_sampai='0000-00-00 00:00:00' or tanggal_sampai >= now() ) and
				diskon_tipe_id=1001
				order by dd.uid desc
				limit 1";
   $result = mysql_query($sql) or die('Gagal cek diskon promo, error: '.mysql_error());
   $dataDiskon = mysql_fetch_array($result);
   if ($dataDiskon) {
      $diskonDetailId = $dataDiskon['uid'];
      $diskonPersen = $dataDiskon['diskon_persen'];
      $diskonRupiah = $dataDiskon['diskon_rupiah'];
      $hargaJual = $dataDiskon['hargaJual'];
      // Jika ada diskon persen, diskon rupiah diabaikan (dianggap kesalahan input)
      if ($diskonPersen > 0) {
         $diskon = $diskonPersen / 100 * $hargaJual;
         // harga jual dibulatkan ke atas jika berkoma.
         $hargaJualNet = ceil($hargaJual - $diskon);
         $diskonNet = $hargaJual - $hargaJualNet;
      } elseif ($diskonRupiah > 0) {
         $diskon = $diskonRupiah;
         $hargaJualNet = $hargaJual - $diskon;
         $diskonNet = $diskon;
      }

      // diskon uid dan value disimpan dalam bentuk json, jika item dikenakan lebih dari 1 diskon
      $diskonUids = json_encode(array($diskonDetailId => $diskonNet));
      $jumbarang = $dataDiskon['jumBarang'];
      $maxItem = $dataDiskon['max_item'];
      if ($jumbarang > $maxItem) {
         $sql = "update tmp_detail_jual set jumBarang = {$maxItem}, hargaJual = '{$hargaJualNet}', diskon_persen = $diskonPersen, diskon_rupiah = '$diskonNet', diskon_detail_uids='{$diskonUids}' "
                 ."where uid=$uid";
         mysql_query($sql) or die('Gagal menambahkan diskon promo1, error: '.mysql_error());
         $sisaBarang = $jumbarang - $maxItem;
         $sql = "INSERT into tmp_detail_jual(idCustomer, tglTransaksi,
                            barcode,jumBarang,hargaBeli,hargaJual,username, idBarang)
                        VALUES('{$dataDiskon['idCustomer']}','{$dataDiskon['tglTransaksi']}','{$dataDiskon['barcode']}',
								{$sisaBarang},{$dataDiskon['hargaBeli']},{$dataDiskon['hargaJual']},'{$dataDiskon['username']}', '{$dataDiskon['idBarang']}')";
         mysql_query($sql) or die('Gagal menambahkan diskon promo2, error: '.mysql_error());
         $uid2 = mysql_insert_id();
         //$return = array($uid => true, $uid2 => false);
      } else {
         $sql = "update tmp_detail_jual set hargaJual = '{$hargaJualNet}', diskon_persen = $diskonPersen, diskon_rupiah = '$diskonNet', diskon_detail_uids='{$diskonUids}' "
                 ."where uid=$uid";
         mysql_query($sql) or die('Gagal menambahkan diskon promo0, error: '.mysql_error());
         //$return = array($uid => true);
      }
      $adaDiskonWaktu = true;
   }
   return $adaDiskonWaktu;
}

function cekDiskonWaktuMember($uid) {
   $adaDiskonWaktu = false;
   // diskon_tipe_id = 1002
   $sql = "select dd.uid, dd.diskon_persen, dd.diskon_rupiah, b.hargaJual, tdj.jumBarang, dd.max_item,
				tdj.tglTransaksi, tdj.barcode, tdj.hargaBeli, tdj.idCustomer,tdj.username, tdj.idBarang
				from tmp_detail_jual tdj
				join diskon_detail dd on dd.barcode = tdj.barcode
				join barang b on b.barcode = dd.barcode
				where tdj.uid=$uid and dd.status=1 and
				dd.tanggal_dari<= now() and
				(dd.tanggal_sampai='0000-00-00 00:00:00' or tanggal_sampai >= now() ) and
				diskon_tipe_id=1002
				order by dd.uid desc
				limit 1";
   $result = mysql_query($sql) or die('Gagal cek diskon promo untuk member, error: '.mysql_error());
   $dataDiskon = mysql_fetch_array($result);
   if ($dataDiskon) {
      $diskonDetailId = $dataDiskon['uid'];
      $diskonPersen = $dataDiskon['diskon_persen'];
      $diskonRupiah = $dataDiskon['diskon_rupiah'];
      $hargaJual = $dataDiskon['hargaJual'];
      // Jika ada diskon persen, diskon rupiah diabaikan (dianggap kesalahan input)
      if ($diskonPersen > 0) {
         $diskon = $diskonPersen / 100 * $hargaJual;
         // harga jual dibulatkan ke atas jika berkoma.
         $hargaJualNet = ceil($hargaJual - $diskon);
         $diskonNet = $hargaJual - $hargaJualNet;
      } elseif ($diskonRupiah > 0) {
         $diskon = $diskonRupiah;
         $hargaJualNet = $hargaJual - $diskon;
         $diskonNet = $diskon;
      }

      // diskon uid dan value disimpan dalam bentuk json, jika item dikenakan lebih dari 1 diskon
      $diskonUids = json_encode(array($diskonDetailId => $diskonNet));
      $jumbarang = $dataDiskon['jumBarang'];
      $maxItem = $dataDiskon['max_item'];
      if ($jumbarang > $maxItem) {
         $sql = "update tmp_detail_jual set jumBarang = {$maxItem}, hargaJual = '{$hargaJualNet}', diskon_persen = $diskonPersen, diskon_rupiah = '$diskonNet', diskon_detail_uids='{$diskonUids}' "
                 ."where uid=$uid";
         mysql_query($sql) or die('Gagal menambahkan diskon member promo1, error: '.mysql_error());
         $sisaBarang = $jumbarang - $maxItem;
         $sql = "INSERT into tmp_detail_jual(idCustomer, tglTransaksi,
                            barcode,jumBarang,hargaBeli,hargaJual,username, idBarang)
                        VALUES('{$dataDiskon['idCustomer']}','{$dataDiskon['tglTransaksi']}','{$dataDiskon['barcode']}',
								{$sisaBarang},{$dataDiskon['hargaBeli']},{$dataDiskon['hargaJual']},'{$dataDiskon['username']}', '{$dataDiskon['idBarang']}')";
         mysql_query($sql) or die('Gagal menambahkan diskon member promo2, error: '.mysql_error());
         $uid2 = mysql_insert_id();
         //$return = array($uid => true, $uid2 => false);
      } else {
         $sql = "update tmp_detail_jual set hargaJual = '{$hargaJualNet}', diskon_persen = $diskonPersen, diskon_rupiah = '$diskonNet', diskon_detail_uids='{$diskonUids}' "
                 ."where uid=$uid";
         mysql_query($sql) or die('Gagal menambahkan diskon member promo0, error: '.mysql_error());
         //$return = array($uid => true);
      }
      $adaDiskonWaktu = true;
   }
   return $adaDiskonWaktu;
}

function cekDiskonAdmin($uid, $barcode, $jumlah) {
   // Periksa apakah ada perubahan dari hargaJual asli
   // Jika ada berarti ada diskon manual dari admin
   $sql2 = "select b.hargaJual, tdj.hargaJual as hargaJualTdj, b.hargaJual-tdj.hargaJual as selisih "
           ."from barang b "
           ."join tmp_detail_jual tdj on tdj.barcode=b.barcode "
           ."where tdj.uid=$uid";
   $hasil = mysql_query($sql2) or die(mysql_error());
   $selisih = mysql_fetch_array($hasil);
   if ($selisih['selisih'] != 0) {
      // PENTING!! Update diskon_detail_uids dengan uid dan value nya;
      mysql_query("update tmp_detail_jual set hargaJual= {$selisih['hargaJualTdj']}, diskon_detail_uids='{\"1\":{$selisih['selisih']}}', diskon_rupiah={$selisih['selisih']}, diskon_persen=0 where uid=$uid") or die(mysql_error());
//		mysql_query("update tmp_detail_jual set diskon_detail_uid=1 where uid=$uid") or die(mysql_error());
      return true;
   } else {
      // Jika tidak ada selisih / tidak ada update dari hak Admin
      // Maka cekDiskon lagi
      mysql_query("update tmp_detail_jual set diskon_detail_uids=null, diskon_rupiah=0 where uid=$uid") or die(mysql_error());
      cekDiskon($uid, $barcode, $jumlah);
      return false;
   }
}

function cekCustomerDiskon($customerId) {
   $sql = "select diskon_persen, diskon_rupiah from customer where idCustomer = $customerId";
   $result = mysql_query($sql) or die(mysql_error());
   return mysql_fetch_array($result);
}

// ======= HARGA BANDED =======
function cekHargaBanded($uid, $barcode, $jumlah, $paramJual) {
   $adaHargaBanded = false;
   $sql = "SELECT qty, harga "
           ."FROM harga_banded "
           ."WHERE barcode = '{$barcode}'";
   $query = mysql_query($sql) or die(mysql_error());
   $hargaBanded = mysql_fetch_array($query, MYSQL_ASSOC);
   // print_r($hargaBanded);
   if ($hargaBanded && ($hargaBanded['qty'] <= $jumlah)) {
      $sisa = $jumlah % $hargaBanded['qty'];
      // echo 'sisa = ' . $sisa;
      $qtyBanded = $jumlah - $sisa;
      // echo 'qtyBanded=' . $qtyBanded;
      mysql_query("UPDATE tmp_detail_jual set jumBarang = {$qtyBanded}, hargaJual = {$hargaBanded['harga']} "
                      ."WHERE uid={$uid}") or die(mysql_error());

      if ($sisa > 0) {
         $sql = "INSERT INTO tmp_detail_jual(idCustomer, tglTransaksi,
                            barcode,jumBarang,hargaBeli,hargaJual,username, idBarang)
                        VALUES('$_SESSION[idCustomer]','{$paramJual['tgl']}','$barcode',
                            '$sisa',{$paramJual['hargaBeli']},{$paramJual['hargaBarang']},'$_SESSION[uname]', {$paramJual['idBarang']})";
         mysql_query($sql) or die(mysql_error());
      }
      $adaHargaBanded = true;
   }
   return $adaHargaBanded;
}

// =========================================== RPO ===========================================
function cekBarangTempRPO($idCustomer, $barcode) {

   $adaJual = 0;
   $sql = "SELECT * from tmp_detail_jual where idCustomer = '$idCustomer' and barcode = '$barcode' and username = '$_SESSION[uname]'";
   $cek = mysql_query($sql);
   $adaJual = mysql_num_rows($cek);

   return $adaJual;
}

function tambahBarangRPOAda($idCustomer, $barcode, $jumBarang) {
   $jumlah = 0;

   // jumBarang bisa < 1, yaitu untuk mengurangi jumlah
   $jumlah = $jumBarang;

   $sql = "SELECT jumBarang FROM tmp_detail_jual
			WHERE idCustomer = '$idCustomer' AND barcode = '$barcode' AND username='$_SESSION[uname]'";
   $ambilJumBarang = mysql_query($sql);
   $dataJum = mysql_fetch_array($ambilJumBarang);

   $jumlah = $jumlah + $dataJum['jumBarang'];

   $tgl = date("Y-m-d H:i:s");

   $sql = "UPDATE tmp_detail_jual SET jumBarang = '$jumlah', tglTransaksi = '$tgl'
		 WHERE idCustomer = '$idCustomer' AND barcode = '$barcode' AND username='$_SESSION[uname]'";
   mysql_query($sql);
}

function tambahBarangRPO($barcode, $jumBarang, $range, $periode, $persediaan) {

   $dataAda = cekBarang($barcode);
   if ($dataAda != 0) {

      $tgl = date("Y-m-d H:i:s");
      $tglrange = date("Y-m-d H:i:s", (time() - ($range * 24 * 60 * 60)));
      $jualBarang = mysql_query("SELECT * FROM barang WHERE barcode = '$barcode'") or die(mysql_error());
      $x = mysql_fetch_array($jualBarang);

      $StokSaatIni = $x['jumBarang'];

      // cari harga modal nya
      $sql = "SELECT * FROM detail_beli
			WHERE barcode = '$barcode'
			ORDER BY idTransaksiBeli DESC LIMIT 1";
      $hasil = mysql_query($sql);

      $detilBarang = mysql_fetch_array($hasil);
      if (mysql_num_rows($hasil) > 0) {
         $hargaBeli = $detilBarang['hargaBeli'];
      } else {
         $hargaBeli = 0;
      }

      // hitung $SaranOrder
      // SaranOrder = (TotalPenjualan[$range] / $range) x $persediaan
//		$sql = "SELECT SUM(jumBarang) AS total FROM detail_jual AS dj,
//				(SELECT idTransaksiJual FROM transaksijual
//				WHERE tglTransaksiJual BETWEEN '$tglrange' AND '$tgl') AS tj
//			WHERE barcode='$barcode' AND dj.nomorStruk = tj.idTransaksiJual";
      $sql = "select sum(jumBarang) as total
					from detail_jual dj
					join transaksijual tj on tj.idTransaksiJual = dj.nomorStruk
					where tj.tglTransaksiJual between DATE_SUB(NOW(), INTERVAL {$range} DAY) AND NOW() and
					dj.barcode = '{$barcode}'";
      $hasil = mysql_query($sql) or die(mysql_error());
      $z = mysql_fetch_array($hasil);

      $SaranOrder = round(($z['total'] / $range) * $persediaan) - $StokSaatIni;

      //echo $z['total']." - ".$range." - ".$persediaan." - ".$SaranOrder;
      // simpan transaksi di tmp_detail_jual
      $sql = "INSERT into tmp_detail_jual(idCustomer, tglTransaksi,
                            barcode,jumBarang,hargaBeli,hargaJual,username, idBarang)
                        VALUES('$_SESSION[idCustomer]','$tgl','$barcode',
                            '$jumBarang','$StokSaatIni',$hargaBeli,'$_SESSION[uname]', $SaranOrder)";
      //echo $sql;
      mysql_query($sql) or die(mysql_error());
   } else {
      echo "Barang tidak ada";
   }
}

function tambahBarangRPOperItem($barcode, $jumBarang) {

   $dataAda = cekBarang($barcode);
   if ($dataAda != 0) {

      $jualBarang = mysql_query("SELECT * FROM barang WHERE barcode = '$barcode'") or die(mysql_error());
      $x = mysql_fetch_array($jualBarang);

      $StokSaatIni = $x['jumBarang'];

      // cari harga modal nya
      $sql = "SELECT * FROM detail_beli
			WHERE idDetailBeli= (select max(idDetailBeli) FROM detail_beli WHERE barcode = '{$barcode}')";
      //ORDER BY idTransaksiBeli DESC LIMIT 1";
      $hasil = mysql_query($sql);

      $detilBarang = mysql_fetch_array($hasil);
      if (mysql_num_rows($hasil) > 0) {
         $hargaBeli = $detilBarang['hargaBeli'];
      } else {
         $hargaBeli = 0;
      }
      // simpan transaksi di tmp_detail_jual
      $sql = "INSERT into tmp_detail_jual(idCustomer, tglTransaksi,
                            barcode,jumBarang,hargaBeli,hargaJual,username, idBarang)
                        VALUES('$_SESSION[idCustomer]', now(),'$barcode',
                            '$jumBarang','$StokSaatIni',$hargaBeli,'$_SESSION[uname]', {$x['idBarang']})";
      //echo $sql;
      mysql_query($sql) or die(mysql_error());
   } else {
      echo "Barang tidak ada";
   }
}

// Proses di atas diganti dengan proses di bawah ini, jauh lebih cepat
// by abu fathir;
function SimpanRPOawalOld1($supplierid, $range, $persediaan, $buffer) {

   // ambil daftar barang supplier ybs
   $sql = "SELECT b.barcode, b.namaBarang, b.jumBarang FROM barang AS b
			  WHERE b.idSupplier = ".$supplierid;
   $hasil1 = mysql_query($sql);

   while ($x = mysql_fetch_array($hasil1)) {

      // cari harga beli nya
      $sql = "SELECT db.hargaBeli
				  FROM detail_beli AS db
				  WHERE db.barcode = '".$x['barcode']."'
				  ORDER BY db.idTransaksiBeli DESC LIMIT 1
				  ";
      $hasil2 = mysql_query($sql);
      if ($z = mysql_fetch_array($hasil2)) {
         $hargaBeli = $z['hargaBeli'];
      } else {
         $hargaBeli = 0;
      };

      // cari SO (Saran Order)
      $tglakhir = date("Y-m-d H:i:s");
      $tglawal = date("Y-m-d H:i:s", (time() - ($range * 24 * 60 * 60)));

      $sql = "SELECT SUM(jumBarang) AS total FROM detail_jual AS dj,
				  (SELECT idTransaksiJual FROM transaksijual
				  WHERE tglTransaksiJual BETWEEN '$tglawal' AND '$tglakhir') AS tj
				  WHERE barcode='".$x['barcode']."' AND dj.nomorStruk = tj.idTransaksiJual";
      //echo $sql;
      $hasil3 = mysql_query($sql);
      $y = mysql_fetch_array($hasil3);

      $AvgDaily = ($y['total'] / $range);
      //$BufferStock	= 0 + (($SaranOrder * $buffer) / 100);
      // SaranOrder = ((Avg Daily x Periode Persediaan) + Buffer Stock) - JumlahStokSaatIni
      $SaranOrder = round($AvgDaily * $persediaan);
      $BufferStock = 0 + (($SaranOrder * $buffer) / 100);
      $SaranOrder = round($SaranOrder + $BufferStock);
      $SaranOrder = $SaranOrder - $x['jumBarang'];
      if ($SaranOrder < 0) {
         $SaranOrder = 0;
      };

      // Dikali 100 untuk menyimpan 2 digit pecahan,
      // karena idBarang itu integer / tidak bisa menyimpan pecahan
      $AvgDaily = $AvgDaily * 100;

      // simpan RPO awal di tmp_detail_jual
      $sql = "INSERT INTO tmp_detail_jual(idCustomer, tglTransaksi,
				  barcode,jumBarang,hargaBeli,hargaJual,username, idBarang)
				  VALUES('$_SESSION[idCustomer]','".date("Y-m-d H:i:s", $SaranOrder)."','".$x['barcode']."',
				  $SaranOrder, $hargaBeli, ".$x['jumBarang'].", '$_SESSION[uname]', $AvgDaily)";
      mysql_query($sql) or die(mysql_error()." :: SQL = ".$sql);
   }; // while ($x = mysql_fetch_array($hasil1))
}

// Penyimpanan diganti tablenya, agar tidak konflik dengan proses penjualan kasir
function SimpanRPOawal($supplierid, $range, $persediaan, $buffer) {
   /* bigint1 = idSupplier,
    * dt1 = tanggal_sekarang,
    * vc1 = barcode,
    * integer1 = saran,
    * float1 = harga_beli,
    * integer2 = stok,
    * vc2 = username,
    * float2 = avgPerHari
    */
   $sql = "INSERT INTO tmp(bigint1, dt1, vc1, integer1, float1, integer2, vc2, float2)
			  SELECT
				  {$_SESSION['idCustomer']},
				  NOW(),
				  barcode,
				  CASE
				  WHEN ROUND(ROUND(total/{$range} * {$persediaan}) + ROUND(total/{$range} * {$persediaan}) * {$buffer}/100) < 0 THEN 0
				  ELSE ROUND(ROUND(total/{$range} * {$persediaan}) + ROUND(total/{$range} * {$persediaan}) * {$buffer}/100) ".
           //WHEN ROUND(ROUND(total/{$range} * {$persediaan}) + ROUND(total/{$range} * {$persediaan}) * {$buffer}/100) - stok < 0 THEN 0
           //ELSE ROUND(ROUND(total/{$range} * {$persediaan}) + ROUND(total/{$range} * {$persediaan}) * {$buffer}/100) - stok
           "END AS saran,
				  hargaBeli,
				  stok,
				  '{$_SESSION[uname]}',
				  total/{$range} AS rata
			  FROM(

				  SELECT b.barcode, b.namaBarang, b.jumBarang as stok, t2.hargaBeli, (SELECT  IFNULL(SUM(jumBarang),0)
																	  FROM detail_jual AS dj
																	  JOIN transaksijual AS tj ON tj.idTransaksiJual = dj.nomorStruk
																	  WHERE barcode=b.barcode AND
																		  tj.tglTransaksiJual BETWEEN DATE_SUB(NOW(), INTERVAL {$range} DAY) AND NOW()) AS total
				  FROM barang b
				  LEFT JOIN (

				  SELECT db . *
				  FROM detail_beli AS db
				  JOIN (

					  SELECT barcode, MAX( idTransaksiBeli ) AS idTransaksiBeli
					  FROM detail_beli
					  GROUP BY barcode
				  )
				  AS t1 ON t1.barcode = db.barcode AND t1.idTransaksiBeli = db.idTransaksiBeli
			  )
			  AS t2 ON t2.barcode = b.barcode
			  WHERE b.idSupplier ={$supplierid}
		  ) AS t3";
   mysql_query($sql) or die(mysql_error());
}

function SimpanRPOawalOld2($supplierid, $range, $persediaan, $buffer) {
   $sql = "INSERT INTO tmp_detail_jual(idCustomer, tglTransaksi,
									  barcode,jumBarang,hargaBeli,hargaJual,username, idBarang)
			  SELECT
				  {$_SESSION['idCustomer']},
				  NOW(),
				  barcode,
				  CASE
				  WHEN ROUND(ROUND(total/{$range} * {$persediaan}) + ROUND(total/{$range} * {$persediaan}) * {$buffer}/100) - stok < 0 THEN 0
				  ELSE ROUND(ROUND(total/{$range} * {$persediaan}) + ROUND(total/{$range} * {$persediaan}) * {$buffer}/100) - stok
				  END AS saran,
				  hargaBeli,
				  stok,
				  '{$_SESSION[uname]}',
				  ROUND(total/{$range}*100) AS rata
			  FROM(

				  SELECT b.barcode, b.namaBarang, b.jumBarang as stok, t2.hargaBeli, (SELECT  IFNULL(SUM(jumBarang),0)
																	  FROM detail_jual AS dj
																	  JOIN transaksijual AS tj ON tj.idTransaksiJual = dj.nomorStruk
																	  WHERE barcode=b.barcode AND
																		  tj.tglTransaksiJual BETWEEN DATE_SUB(NOW(), INTERVAL {$range} DAY) AND NOW()) AS total
				  FROM barang b
				  LEFT JOIN (

				  SELECT db . *
				  FROM detail_beli AS db
				  JOIN (

					  SELECT barcode, MAX( idTransaksiBeli ) AS idTransaksiBeli
					  FROM detail_beli
					  GROUP BY barcode
				  )
				  AS t1 ON t1.barcode = db.barcode AND t1.idTransaksiBeli = db.idTransaksiBeli
			  )
			  AS t2 ON t2.barcode = b.barcode
			  WHERE b.idSupplier ={$supplierid}
		  ) AS t3";
   mysql_query($sql) or die(mysql_error());
}

// ========================================= END RPO =========================================

function ubahJumlahBarangBeliTemp($idSupplier, $idBarang, $jumlah) {
   mysql_query("UPDATE tmp_detail_beli SET jumBarang = '$jumlah'
            WHERE idSupplier = '$idSupplier' and idBarang = '$idBarang' and username = '$_SESSION[uname]'") or die(mysql_error());
}

function detailTransaksiBeli($idTransaksiBeli) {
   $query = mysql_query("SELECT idTransaksiBeli, tglTransaksiBeli, namaSupplier, nominal, idTipePembayaran, NomorInvoice, namaUser
            FROM transaksibeli AS t, user AS u, supplier AS s
            WHERE t.idSupplier = s.idSupplier AND t.username = u.uname
            AND t.idTransaksiBeli = '$idTransaksiBeli'") or die(mysql_error());
   return $query;
}

function detailBarangTransaksiBeli($idTransaksiBeli) {
   $query = mysql_query("SELECT detail_beli.idBarang, barang.barcode, namaBarang, detail_beli.jumBarang, hargaBeli, tglExpire FROM detail_beli, barang
        WHERE barang.idBarang = detail_beli.idBarang and detail_beli.idTransaksiBeli = '$idTransaksiBeli'") or die(mysql_error());
   return $query;
}

function nominalBeli($idTransaksiBeli) {
   $query = mysql_query("select sum(jumBarang*hargaBeli) as nominal from detail_beli
        where idTransaksiBeli = '$idTransaksiBeli'") or die(mysql_error());
   $dataQuery = mysql_fetch_array($query);
   $nominal = $dataQuery[nominal];
   mysql_query("UPDATE transaksibeli SET nominal = '$nominal' WHERE idTransaksiBeli = '$idTransaksiBeli'") or die(mysql_error());
   mysql_query("UPDATE hutang SET nominal = '$nominal' WHERE idTransaksiBeli = '$idTransaksiBeli'") or die(mysql_error());
   return $nominal;
}

function editBarangBeli($idTransaksiBeli, $idBarang, $jumBarangLama, $jumBarang, $hargaBeli) {
   $queryJumBarang = mysql_query("SELECT jumBarang FROM barang WHERE idBarang = '$idBarang'");
   $jumBarangku = mysql_fetch_array($queryJumBarang);
   $jumBarangBaru = ($jumBarangku[jumBarang] - $jumBarangLama);
   $jumBarangBaru2 = $jumBarangBaru + $jumBarang;
   mysql_query("UPDATE barang SET jumBarang = '$jumBarangBaru2' WHERE idBarang = '$idBarang'") or die(mysql_error());
   mysql_query("UPDATE detail_beli SET jumBarang = '$jumBarang', hargaBeli = '$hargaBeli'
            WHERE idTransaksiBeli = '$idTransaksiBeli' and idBarang = '$idBarang'") or die(mysql_error());
}

function cetakStruk($nomorStruk, $strukRetur = false) {


   $totalRetur = 0;

   // ambil footer & header struk
   $sql = "SELECT `option`,`value` FROM config";
   $hasil = mysql_query($sql) or die(mysql_error());
   while ($x = mysql_fetch_array($hasil)) {
      if ($x[option] == 'store_name') {
         $store_name = $x[value];
      };
      if ($x[option] == 'receipt_header1') {
         $header1 = $x[value];
      };
      if ($x[option] == 'receipt_footer1') {
         $footer1 = $x[value];
      };
      if ($x[option] == 'receipt_footer2') {
         $footer2 = $x[value];
      };
   };

   $sql = "select date_format(tglTransaksiJual, '%d-%m-%Y %H:%i') tglTransaksiJual, nominal, uangDibayar, user.namaUser
		from transaksijual tj
		join user on tj.idUser = user.idUser
		where tj.idTransaksiJual = {$nomorStruk}";
   $query = mysql_query($sql) or die('Gagal ambil header transaksi jual, error:'.mysql_error());
   $transaksiJual = mysql_fetch_array($query, MYSQL_ASSOC);
   $tglTransaksi = $transaksiJual['tglTransaksiJual'];
   $totalTransaksi = $transaksiJual['nominal'];
   $uangDibayar = $transaksiJual['uangDibayar'];
   $namaKasir = $transaksiJual['namaUser'];

   $sql = "select dj.jumBarang, b.namaBarang, dj.hargaJual, dt.diskon_detail_uids, dt.diskon_persen, dt.diskon_rupiah
					  from detail_jual dj
					  join barang b on b.barcode = dj.barcode
					  left join diskon_transaksi dt on dt.idDetailJual = dj.uid
					  where dj.nomorStruk = {$nomorStruk}";
   //echo $sql;
   $detailJual = mysql_query($sql) or die(mysql_error());

   // siapkan string yang akan dicetak
   $struk = str_pad($store_name, 40, " ", STR_PAD_BOTH)."\n".str_pad($header1, 40, " ", STR_PAD_BOTH)."\n"
           .str_pad($namaKasir." : ".$tglTransaksi." #$nomorStruk", 40, " ", STR_PAD_BOTH)." \n";

   $struk .= "-------------------------------------\n";
   $diskonHargaPerBarangTotal = 0;
   $diskonCustomer = 0;
   while ($x = mysql_fetch_array($detailJual)) {

      if ($strukRetur) {
         $struk .= $x[namaBarang]." \n".$x[barcode].":"
                 ." ".$x[jumBarang]."x".number_format($x[hargaBeli], 0, ',', '.')."="
                 .number_format(($x[hargaBeli] * $x[jumBarang]), 0, ',', '.')."\n";
         $totalRetur = $totalRetur + ($x[hargaBeli] * $x[jumBarang]);
      } else {
         //$struk .= $x[jumBarang] . "x ". $x[namaBarang]. " @".number_format($x[hargaJual],0,',','.').
         //		": ".number_format(($x[hargaJual] * $x[jumBarang]),0,',','.')."\n";
         //
//			$struk .= $x[namaBarang] . "\n        " .
//					  $x[jumBarang] . " x " . number_format($x[hargaJual], 0, ',', '.') .
//					  " = " . number_format(($x[hargaJual] * $x[jumBarang]), 0, ',', '.') . "\n";

         $temp = $x[namaBarang]."\n        @ ".number_format($x['hargaJual'] + $x['diskon_rupiah'], 0, ',', '.')." x ".$x['jumBarang'].
                 " = ".number_format(($x['hargaJual'] + $x['diskon_rupiah']) * $x['jumBarang'], 0, ',', '.')."\n";

         $diskon = '';
         // Bilamana ada diskon per barang
         if (!is_null($x['diskon_detail_uids'])) {
            $detailDiskon = json_decode($x['diskon_detail_uids'], true);
            // Jika ada diskon customer dipisah tampilannya di struk
            if (isset($detailDiskon['2'])) {
               $diskonCustomer+=$detailDiskon['2'];
            }
            if ($x['diskon_persen'] > 0) {
               $diskonPersen = $x['diskon_persen'];
               $diskonRupiah = $x['diskon_rupiah'] * $x['jumBarang'];
               $diskonHargaPerBarangTotal += $diskonRupiah;
               $diskon = "        Potongan (".$diskonPersen.'%) = ('.number_format($diskonRupiah, 0, ',', '.').')'."\n";
            } elseif ($x['diskon_rupiah'] > 0) {
               $diskonRupiah = $x['diskon_rupiah'] * $x['jumBarang'];
               $diskonHargaPerBarangTotal += $diskonRupiah;
               $diskon = "        Potongan (".number_format($diskonRupiah, 0, ',', '.').')'."\n";
            }
         }
         // jika panjang baris > 40 huruf, pecah jadi 2 baris
         //if (strlen($temp) > 40) {
         //	$tmp = substr($temp, 0, 40) . "- \n -" . substr($temp, 40);
         //	$temp = $tmp;
         //};
         $struk .= $temp.$diskon;
      };
   }
   $struk .= "-------------------------------------\n";

   $diskonHargaTotal = $diskonHargaPerBarangTotal;

   // Total Diskon per barang di kurangi $diskonCustomer
   $diskonHargaPerBarangTotal -= $diskonCustomer;
   if ($strukRetur) {

      $struk .= "                TOTAL   : ".number_format($totalRetur, 0, ',', '.')." \n";
   } else {
      $struk .= $diskonHargaPerBarangTotal > 0 ?
              " Total Potongan   : ".str_pad(number_format($diskonHargaPerBarangTotal, 0, ',', '.'), 11, ' ', STR_PAD_LEFT)." \n" : '';
      $struk .= $diskonCustomer > 0 ? ' Potongan Spesial : '.str_pad(number_format($diskonCustomer, 0, ',', '.'), 11, ' ', STR_PAD_LEFT)." \n" : '';
      $struk .= " TOTAL            : ".str_pad(number_format($totalTransaksi, 0, ',', '.'), 11, " ", STR_PAD_LEFT)." \n";
      $struk .= " Dibayar          : ".str_pad(number_format($uangDibayar, 0, ',', '.'), 11, " ", STR_PAD_LEFT)." \n";
      $struk .= " Kembali          : ".str_pad(number_format(($uangDibayar - $totalTransaksi), 0, ',', '.'), 11, " ", STR_PAD_LEFT)." \n";
      $struk .= $diskonHargaTotal > 0 ? " ANDA HEMAT       : ".str_pad(number_format($diskonHargaTotal, 0, ',', '.'), 11, " ", STR_PAD_LEFT)." \n" : '';
   };

   $struk .= "-------------------------------------\n";
   $struk .= str_pad($footer1, 40, " ", STR_PAD_BOTH)."\n".str_pad($footer2, 40, " ", STR_PAD_BOTH)."\n\n\n\n\n\n\n\n\n\n\n\n\n";
   return $struk;
}

//======================================//
function getSupplier() {
   $query = mysql_query("SELECT idSupplier, namaSupplier, alamatSupplier FROM supplier ORDER BY namaSupplier") or die(mysql_error());

   return $query;
}

function getDetailSupplier($id) {
   $query = mysql_query("SELECT idSupplier, namaSupplier, alamatSupplier from supplier
            WHERE idSupplier = '$id'") or die(mysql_error());

   return $query;
}

function getDetailTmpEditReturPembelian($idNota) { // =================================================================================================
   $query = mysql_query("SELECT t.idDetailBeli, t.idBarang, t.tglExpire, t.jumBarang, t.hargaBeli, t.jumRetur,
                    barang.namaBarang
                    FROM tmp_edit_detail_retur_beli AS t, barang
                    WHERE barang.barcode = t.barcode AND t.idTransaksiBeli = '$idNota';") or die(mysql_error());
   return $query;
}

function ubahTempEditDetailReturPembelian($idDetailBeli, $tglExpire, $jumBarangAsli, $hargaBeli, $jumRetur) {

   // sanity checks
   if ($jumRetur > $jumBarangAsli) {
      $jumRetur = $jumBarangAsli;
   };
   if ($jumRetur < 0) {
      $jumRetur = 0;
   };

   mysql_query("UPDATE tmp_edit_detail_retur_beli
			SET tglExpire = '$tglExpire', jumBarang = '$jumBarangAsli', hargaBeli = '$hargaBeli', jumRetur = $jumRetur
			WHERE idDetailBeli = '$idDetailBeli'") or die(mysql_error());
}

function getDataPembelian($supplierId, $bulanLaporan, $tahunLaporan) { // ============================================================================
   if ($bulanLaporan < 10) {
      $periode = $tahunLaporan."-0".$bulanLaporan;
   } else {
      $periode = $tahunLaporan."-".$bulanLaporan;
   }
   $query = mysql_query("SELECT transaksibeli.idTransaksiBeli as noNota, transaksibeli.tglTransaksiBeli as tglNota, transaksibeli.NomorInvoice, transaksibeli.nominal as nominal
            FROM transaksibeli
            WHERE transaksibeli.idSupplier = '$supplierId' AND tglTransaksiBeli like '$periode%'") or die(mysql_error());
   return $query;
}

function getDataNotaPembelian($idNota) {

   $sql = "SELECT supplier.namaSupplier, supplier.alamatSupplier, transaksibeli.tglTransaksiBeli as tglNota, transaksibeli.nominal,
	transaksibeli.NomorInvoice
        FROM transaksibeli, supplier
        WHERE transaksibeli.idSupplier = supplier.idSupplier AND transaksibeli.idTransaksiBeli = '$idNota'";

   $query = mysql_query($sql) or die(mysql_error());

   return $query;
}

function getDetailNotaPembelian($idNota) {
   $sql = "SELECT detail_beli.idBarang, detail_beli.tglExpire, detail_beli.jumBarang, detail_beli.hargaBeli, barang.hargaJual, detail_beli.barcode,
                    barang.namaBarang, detail_beli.jumBarangAsli
                    FROM detail_beli, barang
                    WHERE barang.barcode = detail_beli.barcode AND detail_beli.idTransaksiBeli = '$idNota'
			ORDER BY detail_beli.idBarang;";
   //echo $sql;
   $query = mysql_query($sql) or die('Gagal ambil data detail nota pembelian, error: '.mysql_error());
   return $query;
}

function inputDataEditPembelianKeTemp($idNota) {
   mysql_query("INSERT INTO tmp_edit_detail_beli(idDetailBeli,idTransaksiBeli,idBarang,tglExpire,jumBarang,hargaBeli)
                    SELECT detail_beli.idDetailBeli,detail_beli.idTransaksiBeli,detail_beli.idBarang,detail_beli.tglExpire,
                            detail_beli.jumBarang,detail_beli.hargaBeli
                            from detail_beli,barang where barang.idBarang = detail_beli.idBarang AND detail_beli.idTransaksiBeli = '$idNota' AND detail_beli.idTransaksiBeli != 0") or die(mysql_error());
}

function getDetailTmpEditPembelian($idNota) {
   $query = mysql_query("SELECT tmp_edit_detail_beli.idDetailBeli, tmp_edit_detail_beli.idBarang, tmp_edit_detail_beli.tglExpire, tmp_edit_detail_beli.jumBarang, tmp_edit_detail_beli.hargaBeli,
                    barang.namaBarang
                    FROM tmp_edit_detail_beli, barang
                    WHERE barang.idBarang = tmp_edit_detail_beli.idBarang AND tmp_edit_detail_beli.idTransaksiBeli = '$idNota';") or die(mysql_error());
   return $query;
}

function ubahTempEditDetailPembelian($idDetailBeli, $tglExpire, $jumBarang, $hargaBeli) {
   mysql_query("UPDATE tmp_edit_detail_beli SET tglExpire = '$tglExpire', jumBarang = '$jumBarang', hargaBeli = '$hargaBeli' WHERE idDetailBeli = '$idDetailBeli'") or die(mysql_error());
}

function getJumBarangDiBarang($idDetailBeli, $barcode = '') {

   if ($barcode == '') {
      $sql = "SELECT barang.jumBarang FROM barang, tmp_edit_detail_retur_beli AS t WHERE t.idBarang = barang.idBarang AND t.idDetailBeli = '$idDetailBeli'";
   } else {
      $sql = "SELECT jumBarang FROM barang WHERE barcode = '$barcode'";
   };
   $query = mysql_query($sql) or die(mysql_error());
   $jum = mysql_fetch_array($query);

   return $jum['jumBarang'];
}

function getJumBarangDetailPembelian($idDetailBeli) {
   $query = mysql_query("SELECT jumBarang FROM detail_beli WHERE idDetailBeli = '$idDetailBeli'") or die(mysql_error());
   $jum = mysql_fetch_array($query);
   return $jum[jumBarang];
}

function getDataPenjualan($idNota) {
   $query = mysql_query("") or die(mysql_error());
}

function getDetailPenjualan($idNota) {
   $query = mysql_query("SELECT detail_jual.idBarang, barang.namaBarang, detail_jual.jumBarang, detail_jual.hargaBeli
            FROM detail_jual, barang, transaksijual
            WHERE detail_jual.idBarang = barang.idBarang
            AND detail_jual.idTransaksiJual = transaksijual.idTransaksiJual
            AND transaksijual.idTransaksiJual = '$idNota'") or die(mysql_error());

   return $query;
}

function getDaftarBarangSupplier($idSupplier, $jumlahMin) {

//    $query = mysql_query("select idBarang, barcode, namaBarang, jumBarang from barang where idSupplier = '$idSupplier' AND jumBarang < $jumlahMin ORDER BY namaBarang") or die(mysql_error());

   $sql = "SELECT b.idBarang, b.barcode, b.namaBarang, b.jumBarang, d.hargaBeli
                FROM barang AS b,
                        (SELECT * FROM detail_beli
                        GROUP BY barcode ORDER BY idTransaksiBeli) AS d
                WHERE b.idSupplier = '$idSupplier' AND b.jumBarang < $jumlahMin AND b.barcode = d.barcode
                ORDER BY b.namaBarang ASC";
   $query = mysql_query($sql) or die(mysql_error());

   return $query;
}

function getBarangPesan($barcode) {

   //$sql = "SELECT idBarang, barcode, namaBarang, jumBarang FROM barang WHERE idBarang = '$idBarang'";
   $sql = "SELECT b.idBarang, b.barcode, b.namaBarang, b.jumBarang, d.hargaBeli
		FROM barang AS b,
			(SELECT * FROM detail_beli
			WHERE barcode = '$barcode'
			GROUP BY barcode ORDER BY idTransaksiBeli) AS d
		WHERE b.barcode = '$barcode' AND b.barcode = d.barcode
		ORDER BY b.namaBarang ASC;	";

   $query = mysql_query($sql) or die(mysql_error());

   return $query;
}

// ==============================================================================================================================

function tambahBarangReturAda($barcode, $jumBarang) {
   $jumlah = 0;
   if ($jumBarang == 0) {
      $jumlah = 1;
   } else {
      $jumlah = $jumBarang;
   }

   $ambilJumBarang = mysql_query("SELECT jumBarang FROM tmp_detail_retur_barang WHERE barcode = '$barcode'");
   $dataJum = mysql_fetch_array($ambilJumBarang);

   $jumlah = $jumlah + $dataJum[jumBarang];

   $tgl = date("Y-m-d H:i:s");
   mysql_query("UPDATE tmp_detail_retur_barang SET jumBarang = '$jumlah', tglTransaksi = '$tgl'
		 WHERE barcode = '$barcode'");
}

function tambahBarangRetur($barcode, $jumBarang) {
   //cekBarangTempJual($idBarang);
   $dataAda = cekBarang($barcode);
   if ($dataAda != 0) {
      $jumlah = 0;
      if ($jumBarang == 0) {
         $jumlah = 1;
      } else {
         $jumlah = $jumBarang;
      }
      $tgl = date("Y-m-d H:i:s");
      $jualBarang = mysql_query("SELECT * FROM barang WHERE barcode = '$barcode'") or die(mysql_error());
      $jual = mysql_fetch_array($jualBarang);

      // cari hargaBeli & idBarang nya
      $sql = "SELECT * FROM detail_beli WHERE isSold = 'N' AND barcode = '$barcode' ORDER BY idTransaksiBeli ASC LIMIT 1";
      //echo $sql;
      $hasil = mysql_query($sql);
      $detilBarang = mysql_fetch_array($hasil);
      if (mysql_num_rows($hasil) > 0) {
         $hargaBeli = $detilBarang[hargaBeli];
         $idBarang = $detilBarang[idBarang];
      } else {
         // not supposed to ever happen, but just to be safe....
         // -- coba lagi dengan record terakhir utk barang ybs di detail_beli, walaupun isSold=Y
         // cari hargaBeli & idBarang nya
         $sql = "SELECT * FROM detail_beli WHERE barcode = '$barcode' ORDER BY idTransaksiBeli DESC LIMIT 1";
         //echo $sql;
         $hasil = mysql_query($sql);
         $detilBarang = mysql_fetch_array($hasil);
         $hargaBeli = $detilBarang['hargaBeli'];
         $idBarang = $detilBarang['idBarang'];
      }

      // simpan transaksi di tmp_detail_jual
      $sql = "INSERT into tmp_detail_retur_barang (tglTransaksi,
                            barcode,jumBarang,hargaBeli,hargaJual,username, idBarang)
                        VALUES('$tgl','$barcode',
                            '$jumlah','$hargaBeli','$jual[hargaJual]','$_SESSION[uname]', $idBarang)";
      //echo $sql;
      mysql_query($sql) or die(mysql_error());
   } else {
      echo "Barang tidak ada";
   }
}

function cekBarangTempRetur($barcode) {
   $adaJual = 0;
   $sql = "SELECT * from tmp_detail_retur_barang where barcode = '$barcode' and username = '$_SESSION[uname]'";
   //echo $sql;
   $cek = mysql_query($sql);
   $adaJual = mysql_num_rows($cek);

   return $adaJual;
}

//function check_user_access($module_name) {
//
//   $userid = (int) $_SESSION['iduser'];
//   //var_dump($_SESSION);
//   ahp_user_can_access_module($module_name, $userid);
//}

/* Fungsi di atas diganti dengan ini, karena perubahan-perubahan yang terjadi
 * Abu Muhammad */
function check_user_access($module_name = null) {
   // print_r($_SESSION);
   // echo '<br />';
   $queryUser = mysql_query("SELECT * FROM user WHERE idUser = {$_SESSION['iduser']}");
   $user = mysql_fetch_array($queryUser, MYSQL_ASSOC);
   $currUserLevel = $user['idLevelUser'];
   // echo 'curUsrLev:'.$currUserLevel.'<br />';

   parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY));
   $currentScriptName = basename($_SERVER['SCRIPT_NAME']).' ';
   $currentModule = $module;
   $currentAct = $act;

   $sql = "SELECT link, level_user_id FROM menu";
   $menus = mysql_query($sql);
   $scriptMatch = false;
   $scriptUserOK = false;
   $moduleMatch = false;
   $moduleUserOK = false;
   $actMatch = false;
   $actUserOK = false;
   //echo 'curr: S:'.$currentScriptName.' - M:'.$currentModule.' - A:'.$currentAct.'<br />';
   while ($menu = mysql_fetch_array($menus, MYSQL_ASSOC)) {
      $module = '';
      $act = '';
      parse_str(parse_url($menu['link'], PHP_URL_QUERY));
      $dataScriptName = basename(parse_url($menu['link'], PHP_URL_PATH));
      $dataModule = $module;
      $dataAct = $act;
      //echo 'data: S:'.$dataScriptName.' - M:'.$dataModule.' - A:'.$dataAct.'<br />';
      if (trim($currentScriptName) == trim($dataScriptName)) {
         $scriptMatch = true;
         //echo 'Script MATCH!!<br />';
         if ($currUserLevel <= $menu['level_user_id'] || $menu['level_user_id'] == 1) {
            $scriptUserOK = true;
            //echo 'Script User OK <br /><br />';
         }
         if (trim($currentModule) == trim($dataModule)) {
            $moduleMatch = true;
            //echo 'Module MATCH!!<br />';
            if ($currUserLevel <= $menu['level_user_id'] || $menu['level_user_id'] == 1) {
               $moduleUserOK = true;
               //echo 'Module User OK <br /><br />';
            }
            /* Cek jika act nya sama, cek lagi untuk user level akses nya */
            if (trim($currentAct) === trim($dataAct)) {
               $actMatch = true;
               //echo 'act Match <br />';
               if ($currUserLevel <= $menu['level_user_id'] || $menu['level_user_id'] == 1) {
                  $actUserOK = true;
                  //echo 'Act User OK <br /><br />';
                  break;
               }
            }
         }
      }
   }
   if ($actMatch && !$actUserOK) {
      die('Act Access forbidden, please <a href="../index.php"><b>LOGIN</b></a>');
   }
   if ($moduleMatch && !$moduleUserOK) {
      die('Module Access forbidden, please <a href="../index.php"><b>LOGIN</b></a>');
   }
   if ($scriptMatch && !$scriptUserOK) {
      die('Script Access forbidden, please <a href="../index.php"><b>LOGIN</b></a>');
   }
   /* Jika tidak terdaftar dalam menu, berarti dianggap public (semua bisa akses) */
}

// credit : Insan Fajar
function ahp_user_can_access_module($module_name, $userid) {
   $userlevel = ahp_get_user_credentials($userid);
   $query = "SELECT `idLevelUser` FROM `modul` WHERE `script_name` = '$module_name' LIMIT 1;";
   //echo "nih : ".$query;
   //echo "mod : ".$module_name;
   $data = mysql_query($query);
   if ($module_name == "media.php") {
      return;
   };
   if (mysql_num_rows($data) < 1)
      die('No such user');
   $dung = mysql_fetch_array($data);
   $module_ulevel = $dung['idLevelUser'];
   if ($module_ulevel == 1)
      return;
   if ($userlevel > $module_ulevel)
      die('Access forbidden, please <a href="../index.php"><b>LOGIN</b></a>');
}

// credit : Insan Fajar
function ahp_get_user_credentials($userid) {
   $query = "SELECT `idLevelUser` FROM `user` WHERE `idUser` = '$userid' LIMIT 1;";
   $data = mysql_query($query);
   if (mysql_num_rows($data) < 1)
      die('Access forbidden, please <a href="../index.php"><b>LOGIN</b></a>');
   return 0;
   $utmp = mysql_fetch_array($data);
   $userlevel = $utmp['idLevelUser'];
   return $userlevel;
}

// cetak label barang per-barcode
function insertTempLabel($cekBarcode) {
   if (!$cekBarcode) {
      $cekBarcode = "0";
   }
   $tampil = mysql_query("SELECT
				`barang`.`idBarang`,
				`barang`.`namaBarang`,
				`barang`.`idKategoriBarang`,
				`kategori_barang`.`namaKategoriBarang`,
				`barang`.`idSatuanBarang`,
				`satuan_barang`.`namaSatuanBarang`,
				`barang`.`jumBarang`,
				`barang`.`hargaJual`,
				`barang`.`barcode`
			FROM `barang`
				LEFT JOIN `kategori_barang`
					ON `barang`.`idKategoriBarang` = `kategori_barang`.`idKategoriBarang`
				LEFT JOIN `satuan_barang`
					ON `barang`.`idSatuanBarang` = `satuan_barang`.`idSatuanBarang`
			WHERE `barang`.`barcode` = '$cekBarcode' ");


   while ($r = mysql_fetch_array($tampil)) {
      $tmpId = $r['idBarang'];
      $tmpBarcode = $r['barcode'];
      $tmpNama = $r['namaBarang'];
      $tmpKategori = $r['namaKategoriBarang'];
      $tmpSatuan = $r['namaSatuanBarang'];
      $tmpJumlah = $r['jumBarang'];
      $tmpHargaJual = $r['hargaJual'];

      $query = "INSERT INTO tmp_cetak_label_perbarcode (tmpBarcode, tmpNama, tmpKategori, tmpSatuan, tmpJumlah, tmpHargaJual, tmpIdBarang) VALUE ('$tmpBarcode','$tmpNama','$tmpKategori','$tmpSatuan','$tmpJumlah','$tmpHargaJual','$tmpId')";
      $sql = mysql_query($query);
   }
}

function getJumlahPoinPeriodeBerjalan($customerId) {
   $bulanSekarang = date('n');
   $sql = "SELECT awal, akhir FROM periode_poin WHERE {$bulanSekarang}>=awal AND {$bulanSekarang}<=akhir";
   $query = mysql_query($sql);
   $periode = mysql_fetch_array($query, MYSQL_ASSOC);
   //echo $sql.'<br />';
   if ($periode) {
      $tahunSekarang = date('Y');
      $sql = "SELECT SUM(jumlah_poin) jumlah_poin
            FROM transaksijual
            WHERE YEAR(tglTransaksiJual)={$tahunSekarang} AND
            MONTH(tglTransaksiJual) BETWEEN {$periode['awal']} AND {$periode['akhir']} AND
            idCustomer = {$customerId}";
      $query = mysql_query($sql);
      $jumlahPoin = mysql_fetch_array($query);
      //echo $sql;
      return isset($jumlahPoin['jumlah_poin']) ? $jumlahPoin['jumlah_poin'] : 0;
   } else {
      return '0';
   }
}

function bulanIndonesia($nomor) {
   $bulanIndonesia = array(
       1 => 'Januari',
       2 => 'Februari',
       3 => 'Maret',
       4 => 'April',
       5 => 'Mei',
       6 => 'Juni',
       7 => 'Juli',
       8 => 'Agustus',
       9 => 'September',
       10 => 'Oktober',
       11 => 'November',
       12 => 'Desember'
   );
   return $bulanIndonesia[$nomor];
}

function kartuStok($barcode, $tanggal) {
   $dariTanggal = date_format(date_create_from_format('d-m-Y', $tanggal['dari']), 'Y-m-d');
   $sampaiTanggal = date_format(date_create_from_format('d-m-Y', $tanggal['sampai']), 'Y-m-d');

   // Saldo Awal Barang
   $sql = "select
					sum(
					case
					when posisi=1 then qty   #beli
					when posisi=2 then -qty  #jual
					when posisi=3 then qty  #so
					when posisi=4 then -qty  #returbeli
                    when posisi=5 then qty #fso
                    when posisi=6 then qty #returjual
					else 0 end
					) as saldo
					from(
					(select db.username, tb.idTransaksiBeli as nota, tb.tglTransaksiBeli as tgl, jumBarangAsli as qty, '1' as posisi
					from detail_beli db
					join transaksibeli as tb on db.idTransaksiBeli = tb.idTransaksiBeli
					where db.barcode = '{$barcode}' and date(tb.tglTransaksiBeli) < '{$dariTanggal}'
					order by tb.tglTransaksiBeli)
					union
					(select dj.username, tj.idTransaksiJual,  tj.tglTransaksiJual, dj.jumBarang, '2' as posisi
					from detail_jual dj
					join transaksijual as tj on tj.idTransaksiJual = dj.nomorStruk
					where dj.barcode = '{$barcode}' and date(tj.tglTransaksiJual) < '{$dariTanggal}'
					order by tj.tglTransaksiJual)
					union
					(select so.username, so.idStockOpname, so.tanggalSO, dso.selisih, '3' as posisi
					from detail_stock_opname as dso
					join stock_opname as so on so.idStockOpname = dso.idStockOpname
					where dso.barcode = '{$barcode}' and date(so.tanggalSO) < '{$dariTanggal}')
					union
					(select username, NomorInvoice, tglRetur, jumRetur, '4' as posisi
					from detail_retur_beli
					where barcode = '{$barcode}' and date(tglRetur) < '{$dariTanggal}')
					union
					(select username, '', tanggalSO, selisih, '5' as posisi
					from fast_stock_opname
					where barcode = '{$barcode}' and date(tanggalSO) < '{$dariTanggal}')
					union
					(select username, '', tglTransaksi, jumBarang, '6' as posisi
					from detail_retur_barang
					where barcode = '{$barcode}' and date(tglTransaksi) < '{$dariTanggal}')
					) as t1
					";
   $result = mysql_query($sql) or die(mysql_error());
   $dataSaldo = mysql_fetch_array($result);
   $saldo = $dataSaldo['saldo'];

   // Mutasi Transaksi Stock Barang
   $sql = "select tgl, nota, username,
            case posisi
            when 1 then qty else '' end as 'beli',
            case posisi
            when 4 then qty else '' end as 'rbeli',
            case posisi
            when 2 then qty else '' end as 'jual',
            case posisi
            when 6 then qty else '' end as 'rjual',
            case posisi
            when 3 then qty else '' end as 'so',
            case posisi
            when 5 then qty else '' end as 'fso'
            from(
            (select db.username, concat(tb.idTransaksiBeli,' ',tb.NomorInvoice)  as nota, tb.tglTransaksiBeli as tgl, jumBarangAsli as qty, '1' as posisi
            from detail_beli db
            join transaksibeli as tb on db.idTransaksiBeli = tb.idTransaksiBeli
            where db.barcode = '{$barcode}' and date(tb.tglTransaksiBeli) between '{$dariTanggal}' and '{$sampaiTanggal}'
            order by tb.tglTransaksiBeli)
            union all
            (select dj.username, tj.idTransaksiJual,  tj.tglTransaksiJual, dj.jumBarang, '2' as posisi
            from detail_jual dj
            join transaksijual as tj on tj.idTransaksiJual = dj.nomorStruk
            where dj.barcode = '{$barcode}' and date(tj.tglTransaksiJual) between '{$dariTanggal}' and '{$sampaiTanggal}'
            order by tj.tglTransaksiJual)
            union all
            (select so.username, so.idStockOpname, so.tanggalSO, dso.selisih, '3' as posisi
            from detail_stock_opname as dso
            join stock_opname as so on so.idStockOpname = dso.idStockOpname
            where dso.barcode = '{$barcode}' and date(so.tanggalSO) between '{$dariTanggal}' and '{$sampaiTanggal}')
            union all
            (select username, '', tanggalSO, selisih, '5' as posisi
            from fast_stock_opname
            where barcode = '{$barcode}' and date(tanggalSO) between '{$dariTanggal}' and '{$sampaiTanggal}')
            union all
            (select username, '', tglTransaksi, jumBarang, '6' as posisi
            from detail_retur_barang
            where barcode = '{$barcode}' and date(tglTransaksi) between '{$dariTanggal}' and '{$sampaiTanggal}')
            union all
            (select username, concat(NomorInvoice,' ',idTransaksiBeli), tglRetur, jumRetur, '4' as posisi
            from detail_retur_beli
            where barcode = '{$barcode}' and date(tglRetur) between '{$dariTanggal}' and '{$sampaiTanggal}')
            ) as t1
            order by tgl";
   $result = mysql_query($sql) or die(mysql_error());
   return array('saldo' => $saldo, 'mutasi' => $result);
}

function tambahBarangReturBeli($barcode, $qty) {
   $tambahBarang = $qty;
   $query = mysql_query("select * from tmp_edit_detail_retur_beli where barcode = '{$barcode}'");
   /* Jika barang sudah ada tambahkan qty nya, hapus row nya */
   if ($adaBarang = mysql_fetch_array($query, MYSQL_ASSOC)) {
      $tambahBarang += $adaBarang['jumRetur'];
      mysql_query("delete from tmp_edit_detail_retur_beli where barcode = '{$barcode}'");
   }

   /* Cari data detail_beli paling awal yang belum habis stok nya 
    * fixme: sisa stok yang ada seharusnya diperhitungkan 
    */
   $sql = "select * from detail_beli where barcode = '{$barcode}' and isSold='N' order by idDetailBeli";
   $query = mysql_query($sql);
   $count = mysql_num_rows($query);

   if ($count > 0) {
      $detailBeli = mysql_fetch_array($query);
   } else {
      /* Jika tidak ada, maka ambil detail_beli terakhir */
      $sql = "select * from detail_beli where barcode = '{$barcode}' order by idDetailBeli desc limit 1";
      $query = mysql_query($sql);
      $detailBeli = mysql_fetch_array($query);
   }

   /* Insert row ke temp */
   mysql_query("insert into tmp_edit_detail_retur_beli (idDetailBeli, idTransaksiBeli, idBarang, tglExpire, jumBarang, hargaBeli, barcode, jumRetur) "
           ."values ({$detailBeli['idDetailBeli']}, "
           ."{$detailBeli['idTransaksiBeli']}, "
           ."{$detailBeli['idBarang']}, "
           ."'{$detailBeli['tglExpire']}', "
           ."{$detailBeli['jumBarangAsli']}, "
           ."{$detailBeli['hargaBeli']}, "
           ."'{$detailBeli['barcode']}', "
           ."{$tambahBarang})");
}

function cekHargaJualBerubah($barcode, $hargaJualBaru) {
   $query = mysql_query("SELECT hargaJual FROM barang WHERE barcode = '{$barcode}'");
   $barang = mysql_fetch_array($query, MYSQL_ASSOC);
   return !($barang['hargaJual'] == $hargaJualBaru);
}

function hargaJualBerubah($barcode) {
   mysql_query("UPDATE barang SET hargaJualLastUpdate=now() WHERE barcode='{$barcode}'") or die('Gagal update perubahan harga jual, error:'.mysql_error());
}

function textStrukA4($nomorStruk, $cpi = 15) {
   $lebarKertas = 8; //inchi
   $jumlahKolom = $cpi * $lebarKertas;

   // ambil footer & header struk
   $sql = "SELECT `option`,`value` FROM config";
   $hasil = mysql_query($sql) or die(mysql_error());
   while ($x = mysql_fetch_array($hasil)) {
      if ($x[option] == 'store_name') {
         $store_name = $x[value];
      }
      if ($x[option] == 'receipt_header1') {
         $header1 = $x[value];
      }
      if ($x[option] == 'receipt_footer1') {
         $footer1 = $x[value];
      }
      if ($x[option] == 'receipt_footer2') {
         $footer2 = $x[value];
      }
      if ($x[option] == 'footer_nota_a4') {
         $footer3 = $x[value];
      }
   }

   $sql = "select date_format(tglTransaksiJual, '%d-%m-%Y %H:%i') tglTransaksiJual, nominal, uangDibayar, user.namaUser
		from transaksijual tj
		join user on tj.idUser = user.idUser
		where tj.idTransaksiJual = {$nomorStruk}";
   $query = mysql_query($sql) or die('Gagal ambil header transaksi jual, error:'.mysql_error());
   $transaksiJual = mysql_fetch_array($query, MYSQL_ASSOC);
   $tglTransaksi = $transaksiJual['tglTransaksiJual'];
   $totalTransaksi = $transaksiJual['nominal'];
   $uangDibayar = $transaksiJual['uangDibayar'];
   $namaKasir = $transaksiJual['namaUser'];

   $sql = "select dj.jumBarang, b.namaBarang, dj.hargaJual, dt.diskon_detail_uids, dt.diskon_persen, dt.diskon_rupiah
					  from detail_jual dj
					  join barang b on b.barcode = dj.barcode
					  left join diskon_transaksi dt on dt.idDetailJual = dj.uid
					  where dj.nomorStruk = {$nomorStruk}";
   //echo $sql;
   $detailJual = mysql_query($sql) or die(mysql_error());

   // siapkan string yang akan dicetak
   $strNomor = 'Nomor   : '.$nomorStruk;
   $strTgl = 'Tanggal : '.$tglTransaksi;
   $strKasir = 'Kasir   : '.ucwords($namaKasir);
   $kananMaxLength = strlen($strNomor) > strlen($strTgl) ? strlen($strNomor) : strlen($strTgl);
   /* Jika Nama kasir terlalu panjang, akan di truncate */
   $strKasir = strlen($strKasir) > $kananMaxLength ? substr($strKasir, 0, $kananMaxLength - 2).'..' : $strKasir;

   $strInvoice = 'INVOICE '; //Jumlah karakter harus genap!
   // $garisBawahHeader1 = str_pad('', strlen($header1), '-');
   $struk = str_pad($store_name, $jumlahKolom / 2 - strlen($strInvoice) / 2, ' ')
           .$strInvoice
           .str_pad(str_pad($strNomor, $kananMaxLength, ' '), $jumlahKolom / 2 - strlen($strInvoice) / 2, ' ', STR_PAD_LEFT)
           .PHP_EOL;
   $struk .= str_pad($header1, $jumlahKolom - $kananMaxLength, ' ')
           .str_pad($strTgl, $kananMaxLength, ' ')
           .PHP_EOL;
   $struk .= str_pad(str_pad($strKasir, $kananMaxLength, ' '), $jumlahKolom, ' ', STR_PAD_LEFT).PHP_EOL;
   $struk .= PHP_EOL;
   $struk .= str_pad('', $jumlahKolom, "-").PHP_EOL;
   $textHeader1 = ' No  Barang';
   $textHeader2 = 'Jumlah      Harga     Diskon  Harga Net  Sub Total ';
   $textHeader = $textHeader1.str_pad($textHeader2, $jumlahKolom - strlen($textHeader1), ' ', STR_PAD_LEFT).PHP_EOL;
   $struk .= $textHeader;
   //$struk .= ' No  Barang                                  Jumlah      Harga     Diskon  Harga Net  Sub Total'.PHP_EOL;
   $struk .= str_pad('', $jumlahKolom, "-").PHP_EOL;
   $diskonHargaPerBarangTotal = 0;
   $diskonCustomer = 0;
   $no = 1;
   while ($x = mysql_fetch_array($detailJual)) {

      $strNomor = str_pad($no, 3, ' ', STR_PAD_LEFT).'.';
      $strBarang = str_pad(trim($x['namaBarang']), 39, ' ');
      $strQty = str_pad($x['jumBarang'], 6, ' ', STR_PAD_LEFT);
      $strHarga = str_pad(number_format($x['hargaJual'] + $x['diskon_rupiah'], 0, ',', '.'), 9, ' ', STR_PAD_LEFT);
      $strDiskon = str_pad(number_format($x['diskon_rupiah'], 0, ',', '.'), 9, ' ', STR_PAD_LEFT); // : '         ';
      $strHargaNet = str_pad(number_format($x['hargaJual'], 0, ',', '.'), 9, ' ', STR_PAD_LEFT);
      $strSubTotal = str_pad(number_format($x['hargaJual'] * $x['jumBarang'], 0, ',', '.'), 9, ' ', STR_PAD_LEFT);
      $row1 = $strNomor.' '.$strBarang.' ';
      $row2 = $strQty.'  '.$strHarga.'  '.$strDiskon.'  '.$strHargaNet.'  '.$strSubTotal;
      $row = $row1.str_pad($row2.' ', $jumlahKolom - strlen($row1), ' ', STR_PAD_LEFT).PHP_EOL;

      $struk .= $row;
      $no++;

      // Bilamana ada diskon per barang
      if (!is_null($x['diskon_detail_uids'])) {
         $detailDiskon = json_decode($x['diskon_detail_uids'], true);
         // Jika ada diskon customer dipisah tampilannya di struk
         if (isset($detailDiskon['2'])) {
            $diskonCustomer+=$detailDiskon['2'];
         }
         if ($x['diskon_persen'] > 0 || $x['diskon_rupiah'] > 0) {
            $diskonRupiah = $x['diskon_rupiah'] * $x['jumBarang'];
            $diskonHargaPerBarangTotal += $diskonRupiah;
         }
      }
   }
   $struk .= str_pad('', $jumlahKolom, "-").PHP_EOL;

   $diskonHargaTotal = $diskonHargaPerBarangTotal;

   // Total Diskon per barang di kurangi $diskonCustomer
   $diskonHargaPerBarangTotal -= $diskonCustomer;

   $textTotalPotongan = "Total Potongan    ".str_pad(number_format($diskonHargaPerBarangTotal, 0, ',', '.'), 11, ' ', STR_PAD_LEFT);
   $textDiskonCustomer = 'Potongan Spesial  '.str_pad(number_format($diskonCustomer, 0, ',', '.'), 11, ' ', STR_PAD_LEFT);
   $textTotal = "TOTAL             ".str_pad(number_format($totalTransaksi, 0, ',', '.'), 11, " ", STR_PAD_LEFT);
   $textDibayar = "Dibayar           ".str_pad(number_format($uangDibayar, 0, ',', '.'), 11, " ", STR_PAD_LEFT);
   $textKembali = "Kembali           ".str_pad(number_format($uangDibayar - $totalTransaksi, 0, ',', '.'), 11, " ", STR_PAD_LEFT);
   $textAndaHemat = "ANDA HEMAT        ".str_pad(number_format($diskonHargaTotal, 0, ',', '.'), 11, " ", STR_PAD_LEFT);

   $struk .= $diskonHargaPerBarangTotal > 0 && $diskonCustomer > 0 ? str_pad($textTotalPotongan, $jumlahKolom - 1, ' ', STR_PAD_LEFT).PHP_EOL : '';
   $struk .= $diskonCustomer > 0 ? str_pad($textDiskonCustomer, $jumlahKolom - 1, ' ', STR_PAD_LEFT).PHP_EOL : '';
   $struk .= ' '.$footer1.str_pad($textTotal, $jumlahKolom - strlen($footer1) - 2, ' ', STR_PAD_LEFT).PHP_EOL;
   $struk .= ' '.$footer2.str_pad($textDibayar, $jumlahKolom - strlen($footer2) - 2, ' ', STR_PAD_LEFT).PHP_EOL;
   $struk .= str_pad($textKembali, $jumlahKolom - 1, ' ', STR_PAD_LEFT).PHP_EOL;
   $struk .= $diskonHargaPerBarangTotal > 0 ? str_pad($textAndaHemat, $jumlahKolom - 1, ' ', STR_PAD_LEFT).PHP_EOL : '';
   $struk .= str_pad('', $jumlahKolom, "-").PHP_EOL;

   $signatureHead = '        Hormat Kami   	                Pelanggan';

   $struk .= $signatureHead.str_pad($footer3, $jumlahKolom - strlen($signatureHead) - 1, ' ', STR_PAD_LEFT).PHP_EOL;
   $struk .= PHP_EOL.PHP_EOL.PHP_EOL;
   $struk .= '     (                )             (                )'.PHP_EOL;
   $struk .= "\n\n\n\n\n\n\n\n\n\n\n\n\n"; // Tambahan spasi ke bawah, agar pas di posisi robek kertas di lx 300
   return $struk;
}

/* CHANGELOG -----------------------------------------------------------

  1.6.0 / 2013-05-01 : Herwono			: fitur : cetak label harga perbarcode
  1.6.0 / 2013-03-06 : Harry Sufehmi		: bugfix: fungsi findSupplier() tidak lagi menghapus variabel $_SESSION[idSupplier]
  1.5.0 / 2013-01-01 : Harry Sufehmi		: bugfix: fungsi tambahBarangJual() kini tidak lagi mau menerima jumBarang < 1
  (jika quantity penjualan bisa nol / minus, maka uang kas jadi bisa dirampok kasir)

  1.5.0 / 2013-01-01 : Harry Sufehmi		: bugfix: fungsi tambahBarangJual() kadang mendapatkan harga beli yang salah.
  "ORDER BY idDetailBeli" diganti menjadi "ORDER BY idTransaksiBeli"
  karena, banyak database di berbagai toko Ahad mart yang isi idDetailBeli nya ngaco
  (banyak field idDetailBeli yang isinya 0 [nol])

  1.5.0 / 2012-11-25 : Harry Sufehmi		: fungsi-fungsi untuk mengamankan modul-modul (dari akses langsung / bypass login).
  Credit : Insan Fajar

  1.2.5 / 2012-03-05 : Harry Sufehmi		: fungsi tambahBarangJual() kini akan selalu mendapatkan hargaBeli dengan benar.
  (bugfix: hargaBeli tersimpan sebagai 0 di tmp_detail_jual & detail_jual jika
  suatu item jumBarang = 0 dan isSold = 'Y' / tidak ada yang isSold = 'N')

  1.2.5 / 2012-02-14 : Harry Sufehmi		: fungsi getJumBarangDiBarang() kini bisa retrieve jumBarang dari parameter barcode yang diberikan
  (bugfix: jumBarang di table barang tidak berkurang setelah Retur Pembelian)

  1.2.5 / 2012-02-01 : Harry Sufehmi		: fungsi getDaftarBarangSupplier() tidak lagi memotong output barang.namaBarang

  1.2.5 / 2012-01-30 : Harry Sufehmi		: fungsi getDaftarBarangSupplier() : menampilkan hanya yang barang.jumBarang < $jumlahMin

  1.0.1 / 2010-11-22 : Harry Sufehmi		: fungsi-fungsi untuk Retur Barang

  1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes

  0.9.1		    : Gregorius Arief		: initial release

  ------------------------------------------------------------------------ */
?>
