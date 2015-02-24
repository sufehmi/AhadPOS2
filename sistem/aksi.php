<?php
/* aksi.php ------------------------------------------------------
  version: 1.0.2

  Part of AhadPOS : http://AhadPOS.com
  License: GPL v2
  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
  http://vlsm.org/etc/gpl-unofficial.id.html

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License v2 (links provided above) for more details.
  ---------------------------------------------------------------- */

session_start();
include "../config/config.php";
include "../config/library.php";
include "modul/function.php";

$module = $_GET[module];
$act = $_GET[act];


// Input Kas Awal
if ($module == 'transaksi_kas' AND $act == 'input') {
    $tglHariIni = date("Y-m-d");
//  echo "$_POST[idUser]";
    mysql_query("INSERT INTO transaksikas(tglTransaksiKas,idUser,kasAwal)
        VALUES('$tglHariIni','$_POST[idUser]','$_POST[kasAwal]')");
    header('location:media.php?module=home');
}

// Input user
elseif ($module == 'user' AND $act == 'input') {
    $pass = md5($_POST[pass]);
    $ambilID = mysql_query("select max(idUser)+1 from user");
    $ID = mysql_fetch_array($ambilID);
    $id_user;
    if ($ID[0] == '')
        $id_user = '1';
    else
        $id_user = $ID[0];
    mysql_query("INSERT INTO user(idUser,
                                namaUser,
                                idLevelUser,
                                uname,
                                pass)
	                       VALUES('$id_user',
                                '$_POST[namaUser]',
                                '$_POST[levelUser]',
                                '$_POST[uname]',
                                '$pass')");
    header('location:media.php?module=' . $module);
}//end input user
// Update user
elseif ($module == 'user' AND $act == 'update') {
    // Apabila password tidak diubah
    if (empty($_POST[pass])) {
        mysql_query("UPDATE user SET namaUser       = '$_POST[namaUser]',
                                 idLevelUser    = '$_POST[levelUser]',
                                 uname          = '$_POST[uname]'
                           WHERE idUser         = '$_POST[idUser]'");
    }
    // Apabila password diubah
    else {
        $pass = md5($_POST[pass]);
        mysql_query("UPDATE user SET namaUser       = '$_POST[namaUser]',
                                 idLevelUser    = '$_POST[levelUser]',
                                 uname          = '$_POST[uname]',
                                 pass           = '$pass'
                           WHERE idUser         = '$_POST[idUser]'");
    }

    if ($_GET[home]) {
        header('location:media.php?module=home');
    }
    else {
        header('location:media.php?module=' . $module);
    };
}// end update user
// Hapus User
elseif ($module == 'user' AND $act == 'hapus') {
    mysql_query("DELETE FROM user WHERE idUser = '$_GET[id]'");
    header('location:media.php?module=' . $module);
} // end hapus user
// Input modul ==========================================================
elseif ($module == 'modul' AND $act == 'input') {
    $ambilID = mysql_query("select max(idModul)+1 from modul");
    $ID = mysql_fetch_array($ambilID);
    $id_modul;
    if ($ID[0] == '')
        $id_modul = '1';
    else
        $id_modul = $ID[0];
    $ambilUrut = mysql_query("select max(urutan)+1 from modul where urutan < 100");
    $u = mysql_fetch_array($ambilUrut);
    $urut;
    if ($u[0] == '')
        $urut = '1';
    else
        $urut = $u[0];
    mysql_query("INSERT INTO modul(idModul,
                                 namaModul,
                                 link,
                                 publish,
                                 idLevelUser,
                                 urutan)
	                       VALUES('$id_modul',
                                '$_POST[namaModul]',
                                '$_POST[link]',
                                '$_POST[publish]',
                                '$_POST[levelUser]',
                                '$urut')");
    header('location:media.php?module=' . $module);
}// end input modul
// Update modul
elseif ($module == 'modul' AND $act == 'update') {
    mysql_query("UPDATE modul SET namaModul = '$_POST[namaModul]',
                                link       = '$_POST[link]',
                                publish    = '$_POST[publish]',
                                idLevelUser= '$_POST[levelUser]'
                          WHERE idModul   = '$_POST[idModul]'");
    header('location:media.php?module=' . $module);
}// end update modul
// Hapus Modul
elseif ($module == 'modul' AND $act == 'hapus') {
    mysql_query("DELETE FROM modul WHERE idModul = '$_GET[id]'");
    header('location:media.php?module=' . $module);
} // end hapus modul
// Input Menu
elseif ($module == 'menu' AND $act == 'input') {
    mysql_query("INSERT INTO menu(nama, parent_id, link, icon, label, accesskey, publish, level_user_id, urutan)
						   VALUES('{$_POST['nama']}', '{$_POST['parent_id']}', '{$_POST['link']}', '{$_POST['icon']}',"
            . " '{$_POST['label']}', '{$_POST['accesskey']}', '{$_POST['publish']}', '{$_POST['level_user_id']}', '{$_POST['urutan']}')");
    header('location:media.php?module=' . $module);
}// end input menu
//
// Update menu
elseif ($module == 'menu' AND $act == 'update') {
    mysql_query("UPDATE menu SET nama = '{$_POST['nama']}',
								parent_id  = '{$_POST['parent_id']}',
								link       = '{$_POST['link']}',
								icon       = '{$_POST['icon']}',
								label      = '{$_POST['label']}',
								accesskey  = '{$_POST['accesskey']}',
                                publish    = '{$_POST['publish']}',
								level_user_id= '{$_POST['level_user_id']}',
								urutan= '{$_POST['urutan']}'
                          WHERE id   = {$_POST['id']}");
    header('location:media.php?module=' . $module);
}// end update menu
// Hapus Modul
elseif ($module == 'menu' AND $act == 'hapus') {
    mysql_query("DELETE FROM menu WHERE id = '{$_GET['id']}'");
    header('location:media.php?module=' . $module);
} // end hapus modul
// Workstation Management ============================================================================================================
elseif ($module == 'workstation' AND $act == 'input') {
    mysql_query("INSERT INTO workstation (namaWorkstation, keterangan, workstation_address, printer_type, printer_commands)
		VALUES ('$_POST[namaWorkstation]', '$_POST[keterangan]','$_POST[workstation_address]','$_POST[printer_type]','$_POST[printer_commands]')
	");

    header('location:media.php?module=' . $module);
}
elseif ($module == 'workstation' AND $act == 'update') {
    mysql_query("UPDATE workstation SET namaWorkstation 		= '$_POST[namaWorkstation]',
					keterangan		= '$_POST[keterangan]',
					workstation_address 	= '$_POST[workstation_address]',
					printer_type		= '$_POST[printer_type]',
					printer_commands	= '$_POST[printer_commands]'
		WHERE idWorkstation = '$_POST[idWorkstation]'
	");
    header('location:media.php?module=' . $module);
}
elseif ($module == 'workstation' AND $act == 'hapus') {
    mysql_query("DELETE FROM workstation 	WHERE idWorkstation = '$_POST[idWorkstation]'");
    header('location:media.php?module=' . $module);
}




// Input Satuan Barang
elseif ($module == 'satuan_barang' AND $act == 'input') { // ==============================================================================
    $ambilID = mysql_query("select max(idSatuanBarang)+1 from satuan_barang");
    $ID = mysql_fetch_array($ambilID);
    $id_satuan;
    if ($ID[0] == '')
        $id_satuan = '1';
    else
        $id_satuan = $ID[0];
    mysql_query("INSERT INTO satuan_barang(idSatuanBarang,namaSatuanBarang)
                    VALUES('$id_satuan','$_POST[namaSatuanBarang]')");
    header('location:media.php?module=' . $module);
}// end Input Satuan Barang
// Update Satuan Barang
elseif ($module == 'satuan_barang' AND $act == 'update') {
    mysql_query("UPDATE satuan_barang SET namaSatuanBarang = '$_POST[namaSatuanBarang]'
                    WHERE idSatuanBarang = '$_POST[idSatuanBarang]'");
    header('location:media.php?module=' . $module);
}// end Update Satuan Barang
// Hapus Satuan Barang
elseif ($module == 'satuan_barang' AND $act == 'hapus') {
    $sql = "DELETE FROM satuan_barang WHERE idSatuanBarang = '$_GET[id]'";
    mysql_query($sql) or die(mysql_error());
    header('location:media.php?module=' . $module);
}// end Hapus Satuan Barang
// Input Kategori Barang ==============================================================================================
elseif ($module == 'kategori_barang' AND $act == 'input') {
    $ambilID = mysql_query("SELECT max(idKategoriBarang)+1 FROM kategori_barang");
    $ID = mysql_fetch_array($ambilID);
    $id_rak;
    if ($ID[0] == '')
        $id_rak = '1';
    else
        $id_rak = $ID[0];
    mysql_query("INSERT INTO kategori_barang(idKategoriBarang,namaKategoriBarang)
                    VALUES('$id_rak','$_POST[namaKategoriBarang]')");
    header('location:media.php?module=' . $module);
}// end Input Kategori Barang
// Update Kategori Barang, bugfix credit to: Yono Nox <weyouknow@yahoo.com>
elseif ($module == 'kategori_barang' AND $act == 'update') {
    mysql_query("UPDATE kategori_barang SET namaKategoriBarang = '$_POST[namaKategoriBarang]'
                    WHERE idKategoriBarang = '$_POST[idKategoriBarang]'");
    header('location:media.php?module=' . $module);
}// end Update Kategori Barang
// Hapus Kategori Barang, credit: mianova.net@gmail.com
elseif ($module == 'kategori_barang' AND $act == 'hapus') {
    mysql_query("DELETE FROM kategori_barang
                    WHERE idKategoriBarang = '$_GET[id]'");
    header('location:media.php?module=' . $module);
}// end Hapus Kategori Barang
// Input Rak =============================================================
elseif ($module == 'rak' AND $act == 'input') {
    $ambilID = mysql_query("select max(idRak)+1 from rak");
    $ID = mysql_fetch_array($ambilID);
    $id_rak;
    if ($ID[0] == '')
        $id_rak = '1';
    else
        $id_rak = $ID[0];
    mysql_query("INSERT INTO rak(idRak,namaRak)
                    VALUES('$id_rak','$_POST[namaRak]')");
    header('location:media.php?module=' . $module);
}// end Input Rak
// Update Rak
elseif ($module == 'rak' AND $act == 'update') {
    mysql_query("UPDATE rak SET namaRak = '$_POST[namaRak]'
                    WHERE idRak = '$_POST[idRak]'");
    header('location:media.php?module=' . $module);
}// end Update Rak
// Hapus Rak
elseif ($module == 'rak' AND $act == 'hapus') {
    mysql_query("DELETE FROM rak WHERE idRak = '$_GET[id]'");
    header('location:media.php?module=' . $module);
} // end hapus rak
// Input Barang     =============================================================================================================================
elseif ($module == 'barang' AND $act == 'input') {

    //fixme: bagaimana dengan idBarangnya ? generate dulu di tmp_detail_beli ?
    $tgl = date("Y-m-d");
    mysql_query("INSERT INTO barang(namaBarang,
                    idKategoriBarang,idSatuanBarang,last_update, barcode, username)
                    VALUES('$_POST[namaBarang]',
                    '$_POST[kategori_barang]','$_POST[satuan_barang]',
                    '$tgl', '$_POST[barcode]', '$_SESSION[uname]')");
    header('location:media.php?module=' . $module);
}// end Input Barang
// Hapus Barang
elseif ($module == 'barang' AND $act == 'hapus') {
    // copy data barang ke table arsip_barang
    $sql = "INSERT INTO arsip_barang (idBarang, namaBarang, idKategoriBarang, idSatuanBarang, jumBarang,
			hargaJual, last_update, idSupplier, barcode, username, idRak)
				SELECT idBarang, namaBarang, idKategoriBarang, idSatuanBarang, jumBarang,
					hargaJual, '" . date("Y-m-d") . "', idSupplier, barcode, '" . $_SESSION[uname] . "', idRak
				FROM barang WHERE idBarang = " . $_GET['id'];
    $hasil = mysql_query($sql) or die("Error : " . mysql_error() . " :: $sql");

    // hapus data barang dari table barang
    $sql = "DELETE FROM barang WHERE idBarang = " . $_GET['id'];
    $hasil = mysql_query($sql) or die("Error : " . mysql_error() . " :: $sql");

    header('location:media.php?module=' . $module);
}// end Hapus Barang
// Update Barang
elseif ($module == 'barang' AND $act == 'update') {
    $tgl = date("Y-m-d");

    // jika barcode diubah, maka ubah juga semua di :
    // 	detail_beli
    // 	detail_jual
    // 	detail_retur_beli
    //	detail_retur_barang
    //	detail_stock_opname
    // 	fast_stock_opname
    if ($_POST[barcode] <> $_POST[oldbarcode]) {

        // check apakah barcode baru ini sudah ada di database
        // jika sudah ada, batalkan semua tindakan
        $hasil = mysql_query("SELECT * FROM barang WHERE barcode='$_POST[barcode]'");
        if (mysql_num_rows($hasil) > 0) {
            echo "<h2>Barcode $_POST[barcode] sudah ada di database ! Tidak ada perubahan yang dilakukan.</h2><br />
				[<a href='media.php?module=barang'> Kembali ke Menu </a>]";
            exit;
        };

        $hasil = mysql_query("UPDATE detail_beli 		SET barcode='$_POST[barcode]' WHERE barcode='$_POST[oldbarcode]'");
        $hasil = mysql_query("UPDATE detail_jual 		SET barcode='$_POST[barcode]' WHERE barcode='$_POST[oldbarcode]'");
        $hasil = mysql_query("UPDATE detail_retur_beli 	SET barcode='$_POST[barcode]' WHERE barcode='$_POST[oldbarcode]'");
        $hasil = mysql_query("UPDATE detail_retur_barang 	SET barcode='$_POST[barcode]' WHERE barcode='$_POST[oldbarcode]'");
        $hasil = mysql_query("UPDATE detail_stock_opname 	SET barcode='$_POST[barcode]' WHERE barcode='$_POST[oldbarcode]'");
        $hasil = mysql_query("UPDATE fast_stock_opname 	SET barcode='$_POST[barcode]' WHERE barcode='$_POST[oldbarcode]'");
    }

    /*
     * Cari dulu barang yang akan diupdate untuk mengetahui informasi field yang diupdate
     */
    $sql = "select barcode, namaBarang, idKategoriBarang, idSatuanBarang, idRak, idSupplier, hargaJual, nonAktif
				from barang
				where barcode = '{$_POST['barcode']}'";
    $result = mysql_query($sql) or die("Gagal ambil data barang, error: " . mysql_error());
    $currentBarang = mysql_fetch_array($result);
    $updated = '';
    /*
     * Tandai field yang berbeda (yang diupdate)
     */
    if ($currentBarang['namaBarang'] != $_POST['namaBarang']) {
        $updated .= "&barang=1";
    }
    if ($currentBarang['idKategoriBarang'] != $_POST['kategori_barang']) {
        $updated .= '&kategori=1';
    }
    if ($currentBarang['idSatuanBarang'] != $_POST['satuan_barang']) {
        $updated .= '&satuan=1';
    }
    if ($currentBarang['idSupplier'] != $_POST['supplier']) {
        $updated .= '&supplier=1';
    }
    if ($currentBarang['hargaJual'] != $_POST['hargaJual']) {
        $updated .= '&hargajual=1';
    }
    if ($currentBarang['idRak'] != $_POST['rak']) {
        $updated .= '&rak=1';
    }
    if ($currentBarang['nonAktif'] != $_POST['nonAktif']) {
        $updated .= '&nonAktif=1';
    }

    $sql = "UPDATE barang SET namaBarang = '$_POST[namaBarang]',
			barcode = '$_POST[barcode]',
			idSupplier = $_POST[supplier],
                    	idKategoriBarang = $_POST[kategori_barang],
                    	idSatuanBarang = $_POST[satuan_barang],
                    	hargaJual = $_POST[hargaJual],
                    	last_update = '$tgl',
							username = '$_SESSION[uname]',
							idRak = $_POST[rak],
							nonAKtif = $_POST[nonAktif]
                    WHERE barcode = '$_POST[barcode]'";
    mysql_query($sql) or die('Gagal update data barang, error: ' . mysql_error());
    // header('location:media.php?module=' . $module);
    header('location:media.php?module=barang&act=editbarang&id=' . $_POST['barcode'] . $updated);
}// end Update Barang
elseif ($module == 'barang' and $act == 'diskonupdate') {
    $diskonDetailId = $_POST['id'];
    $status = $_POST['status'];
    $sql = "update diskon_detail set status={$status} where uid={$diskonDetailId}";
    mysql_query($sql) or die(mysql_error());
}
// Input Supplier     =============================================================================================================================
elseif ($module == 'supplier' AND $act == 'input') {
    //HS idSupplier sekarang auto-increment oleh MySQL, untuk menghindari dobel
    /* 	$ambilID = mysql_query("select max(idSupplier)+1 from supplier");
      $ID = mysql_fetch_array($ambilID);
      $id_supplier;
      if($ID[0]=='')
      $id_supplier = '1';
      else
      $id_supplier = $ID[0]; */

    $tgl = date("Y-m-d");
    mysql_query("INSERT INTO supplier(namaSupplier,
                    alamatSupplier,telpSupplier,Keterangan,last_update)
                    VALUES('$_POST[namaSupplier]',
                    '$_POST[alamatSupplier]','$_POST[telpSupplier]',
                    '$_POST[Keterangan]','$tgl')");
    header('location:media.php?module=' . $module);
}// end Input Supplier
// Update Supplier
elseif ($module == 'supplier' AND $act == 'update') {
    $tgl = date("Y-m-d");
    mysql_query("UPDATE supplier SET
			namaSupplier = '$_POST[namaSupplier]',
			`interval` = $_POST[interval],
			alamatSupplier = '$_POST[alamatSupplier]',
			telpSupplier = '$_POST[telpSupplier]',
			Keterangan = '$_POST[Keterangan]',
			last_update = '$tgl'
		WHERE idSupplier = '$_POST[idSupplier]'");
    header('location:media.php?module=' . $module);
}// end Update Supplier
// Hapus Supplier
elseif ($module == 'supplier' AND $act == 'hapus') {
    mysql_query("DELETE FROM supplier WHERE idSupplier = '$_GET[id]'");
    header('location:media.php?module=' . $module);
} // end hapus user
// Input Customer =====================================================================================================
elseif ($module == 'customer' AND $act == 'input') {
    $tgl = date("Y-m-d");
    $tanggalLahir = date_format(date_create_from_format('d-m-Y', $_POST['tanggal_lahir']), 'Y-m-d');
    $nomorKartu = $_POST['nomor_kartu'] == '' ? 'NULL' : "'{$_POST['nomor_kartu']}'";
    mysql_query("INSERT INTO customer(nomor_kartu, namaCustomer, alamatCustomer,telpCustomer,keterangan,last_update,
                    nomor_ktp, jenis_kelamin, tanggal_lahir, handphone, email, member)
                    VALUES({$nomorKartu}, '{$_POST['namaCustomer']}', '{$_POST['alamatCustomer']}', '{$_POST['telpCustomer']}', '{$_POST['keterangan']}','$tgl',
            '{$_POST['nomor_ktp']}', {$_POST['jenis_kelamin']}, '{$tanggalLahir}', '{$_POST['handphone']}', '{$_POST['email']}', {$_POST['member']})") or die(mysql_error());
    header('location:media.php?module=' . $module);
}// end Input Customer
// Update Customer
elseif ($module == 'customer' AND $act == 'update') {
    $tgl = date("Y-m-d");
    $tanggalLahir = date_format(date_create_from_format('d-m-Y', $_POST['tanggal_lahir']), 'Y-m-d');
    mysql_query("UPDATE customer SET namaCustomer = '$_POST[namaCustomer]',
                        nomor_kartu = '{$_POST['nomor_kartu']}',
                        alamatCustomer = '$_POST[alamatCustomer]',
                        telpCustomer = '$_POST[telpCustomer]',
                        keterangan = '$_POST[keterangan]',
                        diskon_persen = $_POST[diskon_persen],
                        diskon_rupiah = $_POST[diskon_rupiah],
                        last_update = '$tgl',
                        nomor_ktp = '{$_POST['nomor_ktp']}',
                        jenis_kelamin = {$_POST['jenis_kelamin']},
                        tanggal_lahir = '{$tanggalLahir}',
                        handphone = '{$_POST['handphone']}',
                        email = '{$_POST['email']}',
                        member = {$_POST['member']}
                    WHERE idCustomer = '$_POST[idCustomer]'");
    header('location:media.php?module=' . $module);
}// end Update Customer
// Hapus Customer
elseif ($module == 'customer' AND $act == 'hapus') {
    mysql_query("DELETE FROM customer WHERE idCustomer = '$_GET[id]'");
    header('location:media.php?module=' . $module);
} // end hapus customer
// Input Transaksi Beli =================================================================================================================
// Ditambahkan pengecekan variabel $_SESSION untuk memastikan input pembelian jika masih ada session
elseif ($module == 'pembelian_barang' AND $act == 'input' AND isset($_SESSION['uname'])) {
    $tgl = $_POST[TanggalInvoice];

    //HS - idTransaksi sekarang di generate MySQL, untuk menghindari duplikat / dobel
    /* $ambilID = mysql_query("select max(idTransaksiBeli)+1 from transaksibeli");
      $ID = mysql_fetch_array($ambilID);
      $id_transaksi;
      if($ID[0]=='')
      $id_transaksi = '1';
      else
      $id_transaksi = $ID[0]; */

    //HS jika keliru input tipe pembayaran, default ke 1 = CASH
    if ($_POST[tipePembayaran] == 0) {
        $_POST[tipePembayaran] = 1;
    };

    $sql_trans = "INSERT INTO transaksibeli(tglTransaksiBeli,
                    idSupplier,nominal,idTipePembayaran,username,last_update,NomorInvoice)
                    VALUES('$tgl','$_POST[idSupplier]',
                           '$_POST[tot_pembayaran]','$_POST[tipePembayaran]',
                            '$_SESSION[uname]','$tgl','$_POST[NomorInvoice]')";
//	echo $sql_trans;

    mysql_query($sql_trans) or die(mysql_error());
    $idTransaksiBeli = mysql_insert_id();

    if ($_POST[tipePembayaran] == '2') {

        mysql_query("INSERT INTO hutang(idTransaksiBeli,nominal,tglBayar,
                        username,last_update)
                        VALUES('$idTransaksiBeli','$_POST[tot_pembayaran]',
                        '$_POST[tglBayar]','$_SESSION[uname]','$tgl')") or die(mysql_error());
    }

    $sql = "SELECT * FROM tmp_detail_beli WHERE idSupplier = '" . $_POST['idSupplier'] . "'
			AND username = '" . $_SESSION['uname'] . "' AND idBarang != 0";
    $dataBarang = mysql_query($sql) or die(mysql_error());
    //echo $sql;

    while ($simpan = mysql_fetch_array($dataBarang)) {

        $sql_simpan = "INSERT INTO detail_beli(idTransaksiBeli,barcode,
                        tglExpire,jumBarang,jumBarangAsli,hargaBeli,username,idBarang)
                    VALUES('$idTransaksiBeli','$simpan[barcode]',
                    '$simpan[tglExpire]',$simpan[jumBarang],$simpan[jumBarang],'$simpan[hargaBeli]','$_SESSION[uname]','$simpan[idBarang]')";
        //echo $sql_simpan;
        mysql_query($sql_simpan) or die(mysql_error());

        $jumlahAkhir = 0;
        $jumBarang = mysql_query("SELECT jumBarang FROM barang WHERE barcode = '" . $simpan['barcode'] . "'") or die(mysql_error());
        $jumlah = mysql_fetch_array($jumBarang);
        $jumlahAkhir = $jumlah[jumBarang] + $simpan[jumBarang];

        mysql_query("UPDATE barang SET jumBarang = '$jumlahAkhir',
                     hargaJual = '$simpan[hargaJual]' WHERE barcode = '$simpan[barcode]'") or die(mysql_error());

        // harga banded
        $hb = mysql_query("SELECT barcode, qty, harga_satuan FROM tmp_harga_banded WHERE barcode = '{$simpan['barcode']}'");
        $tmpHargaBanded = mysql_fetch_array($hb, MYSQL_ASSOC);
        print_r($tmpHargaBanded);
        $sql = "INSERT INTO harga_banded (barcode, qty, harga) "
                . "VALUES('{$simpan['barcode']}',{$tmpHargaBanded['qty']},{$tmpHargaBanded['harga_satuan']}) "
                . "ON DUPLICATE KEY UPDATE qty={$tmpHargaBanded['qty']}, harga={$tmpHargaBanded['harga_satuan']} ";
        if ($tmpHargaBanded) {
            mysql_query($sql) or die(mysql_error());
        }
    }
    mysql_query("DELETE FROM tmp_detail_beli where idSupplier = '$_SESSION[idSupplier]' and username = '$_SESSION[uname]'") or die(mysql_error());

    // hapus harga banded
    mysql_query("DELETE FROM tmp_harga_banded WHERE supplier_id = '{$_SESSION['idSupplier']}' and user_name = '{$_SESSION['uname']}'") or die(mysql_error());

    releaseSupplier();
    header('location:media.php?module=pembelian_barang');
}


//Batal sebuah item di Nota Beli
elseif ($module == 'pembelian_barang' AND $act == 'hapus_detil') {
    mysql_query("DELETE FROM tmp_detail_beli where idSupplier = '" . $_SESSION['idSupplier'] . "' and idBarang = '$_GET[id]'");

    //var_dump($_SESSION);
    header('location:media.php?module=pembelian_barang&act=carisupplier');
}


//Batal Seluruh Invoice / Transaksi Beli
elseif ($module == 'pembelian_barang' AND $act == 'batal') {
    mysql_query("DELETE FROM tmp_detail_beli where idSupplier = '$_SESSION[idSupplier]' and tglTransaksi = '$tgl'");
    releaseSupplier();
    header('location:media.php?module=' . $module);
}


// Input Transaksi Jual ======================================================================================================================
elseif ($module == 'penjualan_barang' AND $act == 'input') {

    //$ambilID = mysql_query("select max(idTransaksiJual)+1 from transaksijual");
    //$ID = mysql_fetch_array($ambilID);
    //$id_transaksi;
    //if($ID[0]=='')
    //	$id_transaksi = '1';
    //else
    //	$id_transaksi = $ID[0];
    // simpan transaksi ke database
    $tgl = date("Y-m-d H:i:s");

    $NomorStruk = 0;
    $jumlahPoin = isset($_POST['jumlah_poin']) ? $_POST['jumlah_poin'] : 0;

    $transferahad = false;
    if (($_POST['transferahad'] == 1) || ($_GET['transferahad'] == 1)) {
        $transferahad = true;
    }
    if (!$transferahad) {
        $sql = "INSERT INTO transaksijual(tglTransaksiJual,
	                    idCustomer,idTipePembayaran,nominal,idUser,last_update,uangDibayar,jumlah_poin)
        	            VALUES('$tgl','$_SESSION[idCustomer]',
        	                   '$_POST[tipePembayaran]','$_POST[tot_pembayaran]',
        	                    '$_SESSION[iduser]','$tgl', $_POST[uangDibayar], $jumlahPoin)";
        $hasil = mysql_query($sql) or die(mysql_error());
        //echo $sql;
        $NomorStruk = mysql_insert_id();
    }
    else if ($transferahad) {
        $sql = "INSERT INTO transaksitransferbarang(tglTransaksi,
	                    idCustomer,idTipePembayaran,nominal,idUser,last_update)
        	            VALUES('$tgl','$_SESSION[idCustomer]',
        	                   '$_POST[tipePembayaran]','$_POST[tot_pembayaran]',
        	                    '$_SESSION[iduser]','$tgl')";
        $hasil = mysql_query($sql);// or die(mysql_error());
        //echo $sql;
        $NomorStruk = mysql_insert_id();
    }

    // cetak struk -------------
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

    // ambil alamat printer
    $sql = "SELECT w.printer_commands, w.printer_type FROM kasir AS k, workstation AS w
		WHERE k.tglTutupKasir IS NULL AND k.idUser = $_SESSION[iduser] AND k.currentWorkstation = w.idWorkstation";
    $hasil = mysql_query($sql) or die(mysql_error());
    $x = mysql_fetch_array($hasil);
    $perintah_printer = $x[printer_commands];
    $jenis_printer = $x[printer_type];

    // ambil transaksi yang akan dicetak
    $sql = "SELECT t.jumBarang,t.hargaJual,b.namaBarang, t.diskon_detail_uids, t.diskon_persen, t.diskon_rupiah FROM barang AS b, tmp_detail_jual AS t
		WHERE t.username='$_SESSION[uname]' and t.idCustomer = {$_SESSION['idCustomer']} AND t.barcode=b.barcode order by t.uid";
    //echo $sql;
    $hasil = mysql_query($sql);

    if ($jenis_printer == 'rlpr') {
        /**
         * Init printer, dan buka cash drawer
         */
        $command = chr(27) . "@"; //Init printer
        //$command .= chr(27) . chr(101) . chr(1); //1 reverse lf
        $command .= chr(27) . chr(112) . chr(48) . chr(60) . chr(120); // buka cash drawer
        $command .= chr(27) . chr(101) . chr(1); //1 reverse lf
        $perintah = "echo \"$command\" |lpr $perintah_printer ";
        exec($perintah, $output);
    }
    /**
     *
     */
    // siapkan string yang akan dicetak
    $struk = ''; //chr(27) . "@"; //Init printer
    $struk .= str_pad($store_name, 40, " ", STR_PAD_BOTH) . "\n" . str_pad($header1, 40, " ", STR_PAD_BOTH) . "\n";
    $struk .= str_pad($_SESSION[uname] . " : " . date("d-m-Y H:i") . " #$NomorStruk", 40, " ", STR_PAD_BOTH) . "\n";
    if ($_SESSION['isMember']) {
        $queryCustomer = mysql_query("SELECT nomor_kartu, namaCustomer FROM customer WHERE idCustomer = {$_SESSION['idCustomer']}");
        //print_r($_SESSION);
        $customer = mysql_fetch_array($queryCustomer, MYSQL_ASSOC);
        $struk .= str_pad("{$customer['namaCustomer']} : {$customer['nomor_kartu']}", 40, " ", STR_PAD_BOTH) . "\n";
    }
    $struk .= "----------------------------------------\n";

    $diskonHargaPerBarangTotal = 0;
    $diskonCustomer = 0;
    while ($x = mysql_fetch_array($hasil)) {
        //$temp = $x[jumBarang] . "x ". $x[namaBarang]. " @".number_format($x[hargaJual],0,',','.').
        //		": ".number_format(($x[hargaJual] * $x[jumBarang]),0,',','.')."\n";
        $tempNamaBarang = $x['namaBarang'];
        $textSubTotal = number_format(($x['hargaJual'] + $x['diskon_rupiah']) * $x['jumBarang'], 0, ',', '.');
        $tempHarga = "@ " . number_format($x['hargaJual'] + $x['diskon_rupiah'], 0, ',', '.') . " x " . $x['jumBarang'] . " : " . str_pad($textSubTotal, 11, ' ', STR_PAD_LEFT);

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
                $textDiskon = "Potongan (" . $diskonPersen . '%) : ' . str_pad('(' . number_format($diskonRupiah, 0, ',', '.') . ')', 12, ' ', STR_PAD_LEFT);
            }
            elseif ($x['diskon_rupiah'] > 0) {
                $diskonRupiah = $x['diskon_rupiah'] * $x['jumBarang'];
                $diskonHargaPerBarangTotal += $diskonRupiah;
                $textDiskon = "Potongan : " . str_pad("(" . number_format($diskonRupiah, 0, ',', '.') . ')', 12, ' ', STR_PAD_LEFT);
            }
            $diskon = str_pad($textDiskon, 40, ' ', STR_PAD_LEFT) . "\n";
        }
        // jika panjang baris > 40 huruf, pecah jadi 2 baris
        //if (strlen($temp) > 40) {
        //	$tmp = substr($temp, 0, 40) . "- \n -" . substr($temp, 40);
        //	$temp = $tmp;
        //};
        $struk .= ' ' . $tempNamaBarang . "\n";
        $struk .= str_pad($tempHarga, 39, ' ', STR_PAD_LEFT) . "\n";
        $struk .= $diskon;
    }

    $diskonHargaPerBarangTotal -= $diskonCustomer;
    $struk .= "----------------------------------------\n";
    $textTotalPotongan = "Total Potongan   : " . str_pad(number_format($diskonHargaPerBarangTotal, 0, ',', '.'), 11, ' ', STR_PAD_LEFT);
    $textDiskonCustomer = 'Potongan Spesial : ' . str_pad(number_format($diskonCustomer, 0, ',', '.'), 11, ' ', STR_PAD_LEFT);
    $textTotal = "TOTAL            : " . str_pad(number_format($_POST[tot_pembayaran], 0, ',', '.'), 11, " ", STR_PAD_LEFT);
    $textDibayar = "Dibayar          : " . str_pad(number_format($_POST[uangDibayar], 0, ',', '.'), 11, " ", STR_PAD_LEFT);
    $textKembali = "Kembali          : " . str_pad(number_format($_POST[uangDibayar] - $_POST[tot_pembayaran], 0, ',', '.'), 11, " ", STR_PAD_LEFT);
    $textAndaHemat = "ANDA HEMAT       : " . str_pad(number_format($diskonHargaPerBarangTotal + $diskonCustomer, 0, ',', '.'), 11, " ", STR_PAD_LEFT);

    $struk .= $diskonHargaPerBarangTotal > 0 && $diskonCustomer > 0 ? str_pad($textTotalPotongan, 39, ' ', STR_PAD_LEFT) . " \n" : '';
    $struk .= $diskonCustomer > 0 ? str_pad($textDiskonCustomer, 39, ' ', STR_PAD_LEFT) . "\n" : '';
    $struk .= str_pad($textTotal, 39, ' ', STR_PAD_LEFT) . "\n";
    $struk .= str_pad($textDibayar, 39, ' ', STR_PAD_LEFT) . " \n";
    $struk .= str_pad($textKembali, 39, ' ', STR_PAD_LEFT) . " \n";
    $struk .= $diskonHargaPerBarangTotal > 0 ? str_pad($textAndaHemat, 39, ' ', STR_PAD_LEFT) . "\n" : '';
    $struk .= "----------------------------------------\n";
    $struk .= str_pad($footer1, 40, " ", STR_PAD_BOTH) . "\n" . str_pad($footer2, 40, " ", STR_PAD_BOTH) . "\n\n";

    if ($_SESSION['isMember']) {
        $struk .= 'Jumlah poin terkumpul: ' . getJumlahPoinPeriodeBerjalan($_SESSION['idCustomer']);
    }
    $struk .= "\n\n\n\n\n\n\n\n\n\n";
// tambahan perintah untuk cutter epson
    if ($jenis_printer == 'rlpr') {
        $struk .= chr(27) . "@" . chr(29) . "V" . chr(1);
    }

    if ($jenis_printer == 'pdf') {
        require('classes/fpdf.php');
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Courier', '', 9);
        $struk_pdf = explode("\n", $struk);
        foreach ($struk_pdf as $baris) {
            $width = 40;
            $length = 1;
            $pdf->Cell($width, $length, $baris);
            $pdf->Ln(3);
        }
        $pdf->Output();
    }
    elseif ($jenis_printer == 'rlpr') {
        include "classes/PrintSend.php";
        include "classes/PrintSendLPR.php";
        $perintah = "echo \"$struk\" |lpr $perintah_printer -l";

        // cara test lpr :
        // export ip=192.168.0.17; echo "Ini AhadPOS \n apakah sukses cetak struk ?" |lpr -H $ip -P printer$ip -l
        //echo $perintah; exit;
        exec($perintah, $output);
    };


    if ($_POST[tipePembayaran] == '2') {
        mysql_query("INSERT INTO piutang(idTransaksiJual,nominal,tglDiBayar,
                        idUser,last_update)
                        VALUES('$id_transaksi','$_POST[tot_pembayaran]',
                        '$_POST[tglBayar]','$_SESSION[iduser]','$tgl')") or die(mysql_error());
    }


    $dataBarang = mysql_query("SELECT * from tmp_detail_jual
            			WHERE idCustomer = '$_SESSION[idCustomer]' AND username = '$_SESSION[uname]' order by uid");
    while ($simpan = mysql_fetch_array($dataBarang)) {
        $jumlahAkhir = 0;
        $jumBarang = mysql_query("SELECT jumBarang FROM barang WHERE barcode = '$simpan[barcode]'");
        $jumlah = mysql_fetch_array($jumBarang);
        $jumlahAkhir = $jumlah[jumBarang] - $simpan[jumBarang];

        /*
         * Biarkan minus seperti apa adanya :)
         */
        /*
          if ($jumlahAkhir < 0) {
          $jumlahAkhir = 0;
          };
         *
         */

        //fixme: kurangi quantity pembelian dengan benar :
        //	(1) cari barang di tabel detail_beli, yang stoknya masih ada, lalu
        //	(2) catat quantity nya, lalu
        $sql = "SELECT * FROM detail_beli
		WHERE isSold='N' AND barcode='$simpan[barcode]' ORDER BY idDetailBeli ASC";
        $hasil = mysql_query($sql);
        $x = mysql_fetch_array($hasil);

        // 	(3) update dengan jumlah yang terjual
        // 		(4) jika stok habis : tandai
        //		(5) jika stok kurang : cari lagi stok lainnya
        //			(6) jika tidak ada lagi - laporkan ke user, bahwa stok kurang
        $Sold = $simpan[jumBarang];
        $StockAvailable = $x[jumBarang];
        $SoldOut = false;
        $Finish = false;
        do { // looping mengurangi jumlah terjual (Sold) dengan stok yg ada (StockAvailable)
            // kurangi stok di record tsb dengan $Sold
            if ($Sold >= $StockAvailable) {
                $newStock = 0;
                $Sold = $Sold - $StockAvailable;
                // catat bahwa record ini sudah habis stok nya
                $sql = "UPDATE detail_beli SET isSold='Y' WHERE idDetailBeli=$x[idDetailBeli]";
                mysql_query($sql);
            }
            else {
                $newStock = $StockAvailable - $Sold;
                $Finish = true;
                $Sold = 0;
            }
            // catat jumlah stok yang baru / sudah dikurangi penjualan
            $sql = "UPDATE detail_beli SET jumBarang=$newStock WHERE idDetailBeli=$x[idDetailBeli]";
            mysql_query($sql);

            // ambil record berikutnya dari database
            $records = $records - 1;
            if (!($x = mysql_fetch_array($hasil))) {
                $SoldOut = true;
            };
            $StockAvailable = $x[jumBarang];
        }
        while (!$SoldOut && !$Finish);

        if (!$SoldOut) { // kurangi sisa item terjual yang masih ada dengan stok yang ada di database
            // kurangi stok di record tsb dengan $Sold
            if ($Sold > $StockAvailable) {
                $newStock = 0;
            }
            else {
                $newStock = $StockAvailable - $Sold;
            }
            $sql = "UPDATE detail_beli SET jumBarang=$newStock WHERE idDetailBeli=$x[idDetailBeli]";
            mysql_query($sql);
        }

        // 	(7) cari barang di tabel barang, lalu
        //	(8) catat quantity nya, lalu
        // 	(9) update dengan jumlah yang terjual
        //
	$sql = "UPDATE barang SET jumBarang = '$jumlahAkhir' WHERE barcode = '$simpan[barcode]'";
        $hasil = mysql_query($sql);

        // Cek jika ini adalah harga banded
        $query = mysql_query("SELECT qty, harga FROM harga_banded WHERE barcode = '{$simpan['barcode']}'");
        $hargaBanded = mysql_fetch_array($query);
        $hargaJualAsli = 'null';
        if ($hargaBanded) {
            $query = mysql_query("SELECT hargaJual FROM barang WHERE barcode = '{$simpan['barcode']}'");
            $hargaJual = mysql_fetch_array($query);
            // Jika jika qty "terkena" harga banded, maka harga_jual_asli diisi
            if (($simpan['jumBarang'] % $hargaBanded['qty']) == 0) {
                $hargaJualAsli = $hargaJual['hargaJual'];
            }
        }


        if (!$transferahad) {
            $sql = "INSERT INTO detail_jual(idBarang, barcode,
	                        jumBarang,hargaJual,harga_jual_asli,username, nomorStruk, hargaBeli)
							  VALUES({$simpan['idBarang']}, '{$simpan['barcode']}',
							  {$simpan['jumBarang']},{$simpan['hargaJual']},{$hargaJualAsli},'{$_SESSION['uname']}', {$NomorStruk}, {$simpan['hargaBeli']})";
            mysql_query($sql) or die('Gagal simpan transaksi detail ' . mysql_error());
            $detailJualId = mysql_insert_id();
            // Diskon
            if (!(is_null($simpan['diskon_detail_uids']) && $simpan['diskon_detail_uids'] == 0)) {
                $sql = "insert into diskon_transaksi (diskon_detail_uids, barcode, waktu, diskon_persen, diskon_rupiah, idDetailJual) "
                        . "values('{$simpan['diskon_detail_uids']}','{$simpan['barcode']}','{$simpan['tglTransaksi']}',"
                        . "{$simpan['diskon_persen']},{$simpan['diskon_rupiah']},{$detailJualId})";
                mysql_query($sql) or die('Gagal simpan diskon_transaksi ' . mysql_error());
            }
            // End of Diskon
        }
        else if ($transferahad) {
            $sql = "INSERT INTO detail_transfer_barang(idBarang, barcode,
	                        jumBarang,hargaJual, username, nomorStruk)
							  VALUES({$simpan['idBarang']}, '{$simpan['barcode']}',
							  {$simpan['jumBarang']},{$simpan['hargaBeli']}, '{$_SESSION['uname']}', {$NomorStruk})";
            mysql_query($sql);// or die('Gagal simpan transaksi detail transfer' . mysql_error());
        }
    }

    // jika transfer antar Ahad,
    // generate file CSV nya
    if ($transferahad) {

        // format isi file CSV :
        // $data[0]  = barcode
        // $data[1]  = idBarang - ignored
        // $data[2]  = namaBarang
        // $data[3]  = jumlah Barang / jumBarang
        // $data[4]  = hargaBeli - ignored
        // $data[5]  = hargaJual (di Gudang)
        // $data[6]  = RRP (Recommended Retail Price)
        // $data[7]  = namaSatuanBarang
        // $data[8]  = namaKategoriBarang
        // $data[9]  = Supplier - ignored
        // $data[10] = username - ignored
        // persiapan membuat output file CSV
        $csv = "\"barcode\",\"idBarang\",\"namaBarang\",\"jumBarang\",\"hargaBeli\",\"hargaJual\",\"RRP\",\"SatuanBarang\",\"KategoriBarang\",\"Supplier\",\"kasir\"\n";

        // cari nama gudang ini
        $hasil = mysql_query("SELECT value FROM config WHERE `option` = 'store_name'");
        $x = mysql_fetch_array($hasil);
        $namaGudang = "";
        $namaGudang = $x[value];

        $hasil1 = mysql_query("SELECT * FROM tmp_detail_jual WHERE idCustomer = '$_SESSION[idCustomer]' AND username = '$_SESSION[uname]'");
        while ($x = mysql_fetch_array($hasil1)) {

            // cari namaBarang
            $hasil2 = mysql_query("SELECT namaBarang, idKategoriBarang, idSatuanBarang, hargaJual FROM barang WHERE barcode='" . $x['barcode'] . "'");
            $y = mysql_fetch_array($hasil2);
            $namaBarang = $y['namaBarang'];
            $idKategoriBarang = $y['idKategoriBarang'];
            $idSatuanBarang = $y['idSatuanBarang'];
				$hargaJual = $y['hargaJual'];

            // cari namaSatuanBarang
            $hasil2 = mysql_query("SELECT namaSatuanBarang FROM satuan_barang WHERE idSatuanBarang=" . $idSatuanBarang);
            $y = mysql_fetch_array($hasil2);
            $namaSatuanBarang = $y[namaSatuanBarang];

            // cari namaKategoriBarang
            $hasil2 = mysql_query("SELECT namaKategoriBarang FROM kategori_barang WHERE idKategoriBarang=" . $idKategoriBarang);
            $y = mysql_fetch_array($hasil2);
            $namaKategoriBarang = $y[namaKategoriBarang];

            $csv .= "\"" . $x['barcode'] . "\",\"" . $x['idBarang'] . "\",\"" . $namaBarang . "\",\"" . $x['jumBarang'] . "\",\"" . $x['hargaBeli'] . "\",\"" . $x['hargaBeli'] . "\",\"" . $hargaJual . "\",\"" . $namaSatuanBarang . "\",\"" . $namaKategoriBarang . "\",\"" . $namaGudang . "\",\"" . $_SESSION['uname'] . "\"\n";
        }; // while ($x = mysql_fetch_array($hasil)) {
        //header('location:media.php?module='.$module);
        //echo "<script>window.close();</script>";
        // kirim output CSV ke browser untuk di download
        // cari nama Customer
        $hasil2 = mysql_query("SELECT namaCustomer FROM customer WHERE idCustomer='" . $_SESSION[idCustomer] . "'");
        $y = mysql_fetch_array($hasil2);
        $namaCustomer = $y[namaCustomer];
        $namaFile = $namaCustomer . "-" . date("Y-m-d--H-i");

        // hapus transaksi jual ini dari table tmp_detail_jual
        mysql_query("DELETE FROM tmp_detail_jual WHERE idCustomer = '$_SESSION[idCustomer]' AND username = '$_SESSION[uname]'");
        $_SESSION[tot_pembelian] = 0;
        releaseCustomer();

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"$namaFile.csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $csv;
    }
    else {
        // hapus transaksi jual ini dari table tmp_detail_jual
        mysql_query("DELETE FROM tmp_detail_jual WHERE idCustomer = '$_SESSION[idCustomer]' AND username = '$_SESSION[uname]'");
        $_SESSION[tot_pembelian] = 0;
        releaseCustomer();
        //header('location:media.php?module='.$module);
        echo "<script>window.close();</script>";
    };
}

//Batal Transaksi Jual
elseif ($module == 'penjualan_barang' AND $act == 'batal') {

    mysql_query("DELETE FROM tmp_detail_jual where idCustomer = '$_SESSION[idCustomer]'  AND username = '$_SESSION[uname]'");
    releaseCustomer();

    header('location:media.php?module=' . $module);
}

//Self Checkout
elseif ($module == 'penjualan_barang' AND $act == 'selfcheckoutinput') {
    if (isset($_POST['sc-id'])) {
        $scId = $_POST['sc-id'];
        $result = mysql_query("select * from self_checkout_detail where self_checkout_uid={$scId}");
        while ($barang = mysql_fetch_array($result)) {
            $trueJual = cekBarangTempJual($_SESSION[idCustomer], $barang['barcode']);
            // Jika barang sudah ada (hanya tambah kuantiti) maka tambahkan kuantitinya;
            if ($trueJual) {
                $jumBarang = $trueJual['jumBarang'];
                mysql_query("delete from tmp_detail_jual where idCustomer='{$_SESSION['idCustomer']}' "
                                . "and barcode = '{$barang['barcode']}' "
                                . "and username='{$_SESSION['uname']}'") or die('Gagal clean ' . mysql_error());
                $jumBarang += $barang['qty'];
            }
            else {
                $jumBarang = $barang['qty'];
            }
            tambahBarangJual($barang['barcode'], $jumBarang);
        }
    }
}

// Nomor Kartu Customer
elseif ($module == 'penjualan_barang' AND $act == 'nomorkartuinput') {
    if (isset($_POST['nomor-kartu'])) {
        $return = array('sukses' => false);
        $nomorKartu = $_POST['nomor-kartu'];
        $result = mysql_query("select idCustomer from customer where nomor_kartu='{$nomorKartu}'");
        $customer = mysql_fetch_array($result);
        if ($customer) {
            findCustomer($customer['idCustomer']);
            mysql_query("UPDATE tmp_detail_jual SET idCustomer = {$customer['idCustomer']} WHERE username = '{$_SESSION['uname']}'");
            $query = mysql_query("SELECT barcode, sum(jumBarang) qty
                                    FROM tmp_detail_jual
                                    WHERE username = '{$_SESSION['uname']}' AND idCustomer = {$customer['idCustomer']}
                                    group by barcode");
            // Hapus semuanya
            // Ulangi proses input
            while ($barang = mysql_fetch_array($query)) {
                mysql_query("delete from tmp_detail_jual where idCustomer={$customer['idCustomer']} "
                                . "and barcode = '{$barang['barcode']}' "
                                . "and username='{$_SESSION['uname']}'") or die('Gagal clean ' . mysql_error());
                tambahBarangJual($barang['barcode'], $barang['qty']);
            }

            $return = array('sukses' => true);
        }
    }
    header('Content-type: application/json');
    echo json_encode($return);
}

//ukmMode: Cek Harga untuk input harga jual manual
elseif ($module == 'penjualan_barang' AND $act == 'get_harga_jual') {
    if (isset($_POST['barcode'])) {
        $result = mysql_query("select hargaJual from barang where barcode = '{$_POST['barcode']}'") or die('Gagal ambil harga jual, barang#' . $_POST['barcode'] . ', error: ' . mysql_error());
        $barang = mysql_fetch_array($result);
        $return = array(
            'sukses' => true,
            'hargaJual' => $barang['hargaJual']
        );
        echo json_encode($return);
    }
}
// BUKA KASIR  // -----------------------------------------------------------------------------------------------------------------------------------
elseif ($module == 'buka_kasir' AND $act == 'input') {

    // cari apakah kasir ini sedang aktif - jika ya, maka tolak
    $sql = "SELECT * FROM kasir WHERE idUser=$_POST[idKasir] AND tglTutupKasir IS NULL";
    $hasil = mysql_query($sql);

    if (mysql_num_rows($hasil) > 0) {
        echo "Kasir ini sedang Aktif ! Silakan ditutup dulu.
			<p>&nbsp;</p>
			    <a href=javascript:history.go(-1)><< Kembali</a>";
    }
    else {
        $sql = "INSERT INTO kasir(tglBukaKasir,idUser,kasAwal,currentWorkstation)
			VALUES ('$_POST[tglBukaKasir]',$_POST[idKasir],$_POST[kasAwal],$_POST[idWorkstation])";
        mysql_query($sql);
        header('location:media.php?module=kasir');
    };
}

//TUTUP KASIR
elseif ($module == 'tutup_kasir' AND $act == 'input') {

    if (empty($_POST[kasAkhir])) {
        $_POST[kasAkhir] = 0;
    };
    if (empty($_POST[totalTransaksi])) {
        $_POST[totalTransaksi] = 0;
    };
    if (empty($_POST[totalProfit])) {
        $_POST[totalProfit] = 0;
    };

    $sql = "UPDATE kasir SET kasTutup 	= $_POST[kasAkhir],
         		kasSeharusnya 		= $_POST[kasSeharusnya],
			tglTutupKasir 		= '$_POST[tglTutupKasir]',
			totalTransaksi 		= $_POST[totalTransaksi],
			totalProfit 		= $_POST[totalProfit],
			totalRetur 		= $_POST[totalRetur],
			totalTransaksiKas 	= $_POST[totalTransaksiKas],
			totalTransaksiKartu 	= $_POST[totalTransaksiKartu]
        WHERE idUser = $_POST[idKasir] AND tglTutupKasir IS NULL";
    //echo $sql;

    mysql_query($sql);
    header('location:media.php?module=kasir');
}
elseif ($module == 'retur_barang' AND $act == 'input') { // ====================================================================================
    $tgl = date("Y-m-d H:i:s");

    /* 	fixme : simpan ke table 'retur', dan dapatkan nomor nota retur nya

      mysql_query("INSERT INTO transaksijual(tglTransaksiJual,
      idCustomer,idTipePembayaran,nominal,idUser,last_update,uangDibayar)
      VALUES('$tgl','$_SESSION[idCustomer]',
      '$_POST[tipePembayaran]','$_POST[tot_pembayaran]',
      '$_SESSION[iduser]','$tgl', $_POST[uangDibayar])") or die(mysql_error());
      $NomorStruk = mysql_insert_id();
     */


    // cetak struk -------------
    // ambil transaksi yang akan dicetak
    $sql = "SELECT t.jumBarang,t.hargaJual,t.hargaBeli,b.namaBarang,t.barcode FROM barang AS b, tmp_detail_retur_barang AS t
		WHERE t.username='$_SESSION[uname]' AND t.barcode=b.barcode";
    //echo $sql;
    $hasil = mysql_query($sql);

    echo "namaPrinter : " . $_POST[namaPrinter];


    // cetak struk
    //cetakStruk ("$_POST[namaPrinter]", 1, "$_SESSION[uname]", $_POST[tot_retur], 0, $hasil, true);
    // mulai simpan data ke detail_retur_barang
    $dataBarang = mysql_query("SELECT * from tmp_detail_retur_barang
            			WHERE username = '$_SESSION[uname]'");

    while ($simpan = mysql_fetch_array($dataBarang)) {

        echo "1 <br>";

        $jumlahAkhir = 0;
        $jumBarang = mysql_query("SELECT jumBarang FROM barang WHERE barcode = '$simpan[barcode]'");
        $jumlah = mysql_fetch_array($jumBarang);
        $jumlahAkhir = $jumlah[jumBarang] + $simpan[jumBarang];

        //fixme: kurangi quantity pembelian dengan benar :
        //	(1) cari barang di tabel detail_beli, yang stoknya masih ada, lalu
        //	(2) catat quantity nya, lalu
        $sql = "SELECT * FROM detail_beli
		WHERE isSold='N' AND barcode='$simpan[barcode]' ORDER BY idDetailBeli ASC";
        $hasil = mysql_query($sql);
        $x = mysql_fetch_array($hasil);

        // 	(3) update dengan jumlah yang di retur
        $retur = $simpan[jumBarang];
        $StockAvailable = $x[jumBarang];

        $newStock = $StockAvailable + $retur;

        mysql_query("UPDATE detail_beli SET jumBarang=$newStock WHERE idDetailBeli=$x[idDetailBeli]");


        // 	(4) cari barang di tabel barang, lalu
        //	(5) catat quantity nya, lalu
        // 	(6) update dengan jumlah yang di retur
        //
	$sql = "UPDATE barang SET jumBarang = '$jumlahAkhir' WHERE barcode = '$simpan[barcode]'";
        $hasil = mysql_query($sql);


        $sql = "INSERT INTO detail_retur_barang (tglTransaksi, idBarang, barcode,
                        jumBarang,hargaJual,username, hargaBeli)
                    VALUES('$tgl', '$simpan[idBarang]', '$simpan[barcode]',
                    '$simpan[jumBarang]',$simpan[hargaJual],'$_SESSION[uname]', $simpan[hargaBeli])";
        echo $sql;
        mysql_query($sql) or die(mysql_error());
    }

    mysql_query("DELETE FROM tmp_detail_retur_barang WHERE username = '$_SESSION[uname]'");

    $_SESSION[tot_retur] = 0;
    //header('location:media.php?module='.$module);
    echo "<script>window.close();</script>";
}

//Batal Transaksi Retur Barang
elseif ($module == 'retur_barang' AND $act == 'batal') {

    mysql_query("DELETE FROM tmp_detail_retur_barang WHERE username = '$_SESSION[uname]'");
    $_SESSION[tot_retur] = 0;

    header('location:media.php?module=barang');
}
elseif ($module == 'inputreturbeli' AND $act == 'inputtemp') { // ====================================================================================
    $sql = "INSERT INTO tmp_edit_detail_retur_beli (idDetailBeli,idTransaksiBeli,idBarang,tglExpire,jumBarang,hargaBeli,barcode)
                    SELECT d.idDetailBeli,d.idTransaksiBeli,d.idBarang,d.tglExpire,d.jumBarangAsli,d.hargaBeli,d.barcode
			FROM detail_beli AS d, barang AS b
			WHERE b.barcode = d.barcode AND d.idTransaksiBeli = '$_POST[idNota]' AND d.idTransaksiBeli != 0";
    mysql_query($sql) or die('Gagal input temporary detail return beli, error: ' . mysql_error());
    //echo $sql; exit;
    header('location:media.php?module=pembelian_barang&act=inputreturbeli&idnota=' . $_POST[idNota]);
}
elseif ($module == 'inputreturbeli' AND $act == 'simpanretur') { // -----------------------------------------------------------------------------------
    // baca detail nota ybs dari transaksibeli
    $sql = "SELECT * FROM transaksibeli WHERE idTransaksiBeli = $_POST[idNota]" or die(mysql_error());
    $hasil = mysql_query($sql);
    $x = mysql_fetch_array($hasil);
    $idSupplier = $x[idSupplier];
    $idTipePembayaran = $x[idTipePembayaran];
    $NomorInvoice = $x[NomorInvoice];

    // hitung nominal retur
    $sql = "SELECT SUM(jumRetur * hargaBeli) AS totalRetur, SUM(jumBarang * hargaBeli) AS totalCurrent FROM tmp_edit_detail_retur_beli WHERE idTransaksiBeli = '$_POST[idNota]'";
    $hasil = mysql_query($sql) or die(mysql_error());
    $x = mysql_fetch_array($hasil);
    $nominal = $x[totalCurrent] - $x[totalRetur];
    $totalRetur = $x[totalRetur];
    $username = $_SESSION[uname];
    $last_update = date("Y-m-d");

    // mulai baca data perubahan dari tmp_edit_detail_retur_beli
    $query = mysql_query("SELECT idTransaksiBeli, idBarang,tglExpire,jumBarang,hargaBeli,barcode,jumRetur, idDetailBeli
			FROM tmp_edit_detail_retur_beli WHERE idTransaksiBeli = '$_POST[idNota]'") or die(mysql_error());

    while ($tmpEdit = mysql_fetch_array($query)) {
        $jumBarang = getJumBarangDiBarang($tmpEdit[idDetailBeli], $tmpEdit[barcode]);
        $jumBarangDetail = getJumBarangDetailPembelian($tmpEdit[idDetailBeli]);
        $jumBarangBaru = $jumBarang - $tmpEdit[jumRetur];
        $jumBarangDetailBaru = $jumBarangDetail - $tmpEdit[jumRetur];

        // update nota pembelian
        if ($jumBarangDetail > 0) { // jika stok sudah nol, jangan dikurangi (jadi minus)
            mysql_query("UPDATE detail_beli SET jumBarang = '$jumBarangDetailBaru'
	            WHERE idDetailBeli = '$tmpEdit[idDetailBeli]'") or die(mysql_error());
        }

        // update stok barang
        if ($jumBarang > 0) {  // jika stok sudah nol, jangan dikurangi (jadi minus)
            mysql_query("UPDATE barang SET jumBarang = $jumBarangBaru
	                WHERE barcode = '$tmpEdit[barcode]'") or die(mysql_error());
        }

        // input transaksi retur ke database
        if ($tmpEdit[jumRetur] > 0) { // yang jumRetur 0 (nol) tidak usah dicatat
            $z = $tmpEdit;
            $sql = "INSERT INTO detail_retur_beli (idTransaksiBeli,idBarang,tglExpire,jumRetur,hargaBeli,barcode,
				username,idSupplier,nominal,idTipePembayaran,NomorInvoice,tglRetur)
			VALUES ($z[idTransaksiBeli],$z[idBarang],'$z[tglExpire]',$z[jumRetur],$z[hargaBeli],'$z[barcode]',
				'$username','$idSupplier', $totalRetur, $idTipePembayaran, '$NomorInvoice','$last_update')";
            mysql_query($sql) or die(mysql_error());
        }
    }

    // update transaksibeli
    mysql_query("UPDATE transaksibeli SET last_update = '$last_update', nominal = $nominal
                WHERE idTransaksiBeli = '$_POST[idNota]'") or die(mysql_error());

    // hapus data temporary
    mysql_query("DELETE FROM tmp_edit_detail_retur_beli WHERE idTransaksiBeli = '$_POST[idNota]'") or die(mysql_error());
    header('location:media.php?module=pembelian_barang');
}
elseif ($module == 'editlaporanpembelian' AND $act == 'inputtemp') { // ====================================================================================
    mysql_query("INSERT INTO tmp_edit_detail_beli(idDetailBeli,idTransaksiBeli,idBarang,tglExpire,jumBarang,hargaBeli)
                    SELECT detail_beli.idDetailBeli,detail_beli.idTransaksiBeli,detail_beli.idBarang,detail_beli.tglExpire,
                            detail_beli.jumBarang,detail_beli.hargaBeli
                            from detail_beli,barang where barang.idBarang = detail_beli.idBarang AND detail_beli.idTransaksiBeli = '$_POST[idNota]' AND detail_beli.idTransaksiBeli != 0") or die(mysql_error());
    header('location:media.php?module=pembelian_barang&act=editlaporan&idnota=' . $_POST[idNota]);
}
elseif ($module == 'editlaporanpembelian' AND $act == 'simpanedit') { // -----------------------------------------------------------------------------------
//    echo "Edit nota $_POST[idNota]";
    $query = mysql_query("SELECT idDetailBeli, idBarang,tglExpire,jumBarang,hargaBeli FROM tmp_edit_detail_beli WHERE idTransaksiBeli = '$_POST[idNota]'") or die(mysql_error());
    while ($tmpEdit = mysql_fetch_array($query)) {
        $jumBarang = getJumBarangDiBarang($tmpEdit[idDetailBeli]);
        $jumBarangDetail = getJumBarangDetailPembelian($tmpEdit[idDetailBeli]);
        $jumBarangEdit = $jumBarangDetail - $tmpEdit[jumBarang];
        $jumBarangBaru = $jumBarang + $jumBarangEdit;

        mysql_query("UPDATE detail_beli SET tglExpire = '$tmpEdit[tglExpire]', jumBarang = '$tmpEdit[jumBarang]', hargaBeli = '$tmpEdit[hargaBeli]'
            WHERE idDetailBeli = '$tmpEdit[idDetailBeli]'") or die(mysql_error());
        mysql_query("UPDATE barang SET jumBarang = '$jumBarangBaru'
                WHERE idBarang = '$tmpEdit[idBarang]'") or die(mysql_error());
    }
    mysql_query("DELETE FROM tmp_edit_detail_beli WHERE idTransaksiBeli = '$_POST[idNota]'") or die(mysql_error());
    header('location:media.php?module=pembelian_barang&act=detaillaporan&idnota=' . $_POST[idNota]);
}
elseif ($module == 'laporanpenjualan' AND $act == 'hapuslaporan') {
//    echo "Kasir : $_POST[kasir], No Nota : $_POST[idNota]";
    $query = mysql_query("SELECT idBarang, jumBarang FROM detail_jual WHERE idTransaksiJual = '$_POST[idNota]'") or die(mysql_error());

    while ($penjualan = mysql_fetch_array($query)) {
        $queryBarang = mysql_query("SELECT jumBarang FROM barang WHERE idBarang = '$penjualan[idBarang]'") or die(mysql_error());
        $jum = mysql_fetch_array($queryBarang);
        $jumBarangBaru = $jum[jumBarang] + $penjualan[jumBarang];
        mysql_query("UPDATE barang SET jumBarang = $jumBarangBaru WHERE idBarang = '$penjualan[idBarang]'") or die(mysql_error());
    }
    mysql_query("DELETE FROM detail_jual WHERE idTransaksiJual = '$_POST[idNota]'") or die(mysql_error());
    mysql_query("DELETE FROM transaksijual WHERE idTransaksiJual = '$_POST[idNota]'") or die(mysql_error());
}


// Hapus tmp cetak perbarcode
elseif ($module == 'labelperbarcode' AND $act == 'hapus') {
    mysql_query("DELETE FROM tmp_cetak_label_perbarcode WHERE id = '$_GET[id]'");
    header('location:media.php?module=barang&act=cetakperbarcode');
} // end
// simpan RPO
elseif ($module == 'buat_rpo' AND $act == 'input') {

    $tgl = date("Y-m-d H:i:s");

    $NomorStruk = 0;
    // cetak struk -------------
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

    // ambil alamat printer
    $sql = "SELECT w.printer_commands, w.printer_type FROM kasir AS k, workstation AS w
		WHERE k.tglTutupKasir IS NULL AND k.idUser = $_SESSION[iduser] AND k.currentWorkstation = w.idWorkstation";
    $hasil = mysql_query($sql) or die(mysql_error());
    $x = mysql_fetch_array($hasil);
    $perintah_printer = $x['printer_commands'];
    $jenis_printer = $x['printer_type'];

    // ambil transaksi yang akan dicetak
    $sql = "SELECT t.jumBarang,t.hargaJual,b.namaBarang FROM barang AS b, tmp_detail_jual AS t
		WHERE t.username='$_SESSION[uname]' AND t.barcode=b.barcode";
    $hasil = mysql_query($sql);

    // siapkan string yang akan dicetak
    $struk = str_pad($store_name, 40, " ", STR_PAD_BOTH) . "\n" . str_pad($header1, 40, " ", STR_PAD_BOTH) . "\n" . str_pad($_SESSION[uname] . " : " . date("d-m-Y H:i") . " #$NomorStruk", 40, " ", STR_PAD_BOTH) . " \n";

    $struk .= "-------------------------------------\n";
    while ($x = mysql_fetch_array($hasil)) {
        //$temp = $x[jumBarang] . "x ". $x[namaBarang]. " @".number_format($x[hargaJual],0,',','.').
        //		": ".number_format(($x[hargaJual] * $x[jumBarang]),0,',','.')."\n";
        $temp = $x[namaBarang] . "\n        @ " . number_format($x[hargaJual], 0, ',', '.') . " x " . $x[jumBarang] .
                " = " . number_format(($x[hargaJual] * $x[jumBarang]), 0, ',', '.') . "\n";
        // jika panjang baris > 40 huruf, pecah jadi 2 baris
        //if (strlen($temp) > 40) {
        //	$tmp = substr($temp, 0, 40) . "- \n -" . substr($temp, 40);
        //	$temp = $tmp;
        //};
        $struk .= $temp;
    }
    $struk .= "-------------------------------------\n";
    $struk .= " TOTAL   : " . number_format($_POST['tot_pembelian'], 0, ',', '.') . " \n";
    $struk .= "-------------------------------------\n";
    $struk .= str_pad($footer1, 40, " ", STR_PAD_BOTH) . "\n" . str_pad($footer2, 40, " ", STR_PAD_BOTH) . "\n\n\n\n\n\n\n\n\n\n\n\n\n";

    if ($jenis_printer == 'pdf') {
        require('classes/fpdf.php');
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 9);
        $struk_pdf = explode("\n", $struk);
        foreach ($struk_pdf as $baris) {
            $width = 40;
            $length = 1;
            $pdf->Cell($width, $length, $baris);
            $pdf->Ln(3);
        }
        $pdf->Output();
    }
    elseif ($jenis_printer == 'rlpr') {
        include "classes/PrintSend.php";
        include "classes/PrintSendLPR.php";
        $perintah = "echo \"$struk\" |lpr $perintah_printer -l";
        exec($perintah, $output);
    }



    // generate file CSV nya
    // format isi file CSV untuk RPO :
    // $data[0]  = barcode
    // $data[1]  = idBarang 			- ignored
    // $data[2]  = namaBarang
    // $data[3]  = jumlah Barang / jumBarang
    // $data[4]  = hargaBeli 			- ignored
    // $data[5]  = hargaJual 			- ignored
    // $data[6]  = RRP (Recommended Retail Price) 	- ignored
    // $data[7]  = namaSatuanBarang
    // $data[8]  = namaKategoriBarang
    // $data[9]  = Supplier
    // $data[10] = username 			- ignored
    // persiapan membuat output file CSV
    $csv = "\"barcode\",\"idBarang\",\"namaBarang\",\"jumBarang\",\"hargaBeli\",\"hargaJual\",\"RRP\",\"SatuanBarang\",\"KategoriBarang\",\"Supplier\",\"kasir\"\n";

    // cari nama gudang ini
    $hasil = mysql_query("SELECT value FROM config WHERE `option` = 'store_name'");
    $x = mysql_fetch_array($hasil);
    $namaGudang = "";
    $namaGudang = $x['value'];

    $hasil1 = mysql_query("SELECT * FROM tmp_detail_jual WHERE idCustomer = '$_SESSION[idCustomer]' AND username = '$_SESSION[uname]'");
    while ($x = mysql_fetch_array($hasil1)) {

        // cari namaBarang
        $hasil2 = mysql_query("SELECT namaBarang, idKategoriBarang, idSatuanBarang FROM barang WHERE barcode='" . $x['barcode'] . "'");
        $y = mysql_fetch_array($hasil2);
        $namaBarang = $y['namaBarang'];
        $idKategoriBarang = $y['idKategoriBarang'];
        $idSatuanBarang = $y['idSatuanBarang'];

        // cari namaSatuanBarang
        $hasil2 = mysql_query("SELECT namaSatuanBarang FROM satuan_barang WHERE idSatuanBarang=" . $idSatuanBarang);
        $y = mysql_fetch_array($hasil2);
        $namaSatuanBarang = $y[namaSatuanBarang];

        // cari namaKategoriBarang
        $hasil2 = mysql_query("SELECT namaKategoriBarang FROM kategori_barang WHERE idKategoriBarang=" . $idKategoriBarang);
        $y = mysql_fetch_array($hasil2);
        $namaKategoriBarang = $y[namaKategoriBarang];

        $csv .= "\"" . $x['barcode'] . "\",\"" . $x['idBarang'] . "\",\"" . $namaBarang . "\",\"" . $x['jumBarang'] . "\",\"" . $x['hargaBeli'] . "\",\"" . $x['hargaJual'] . "\",\"" . $x['hargaJual'] . "\",\"" . $namaSatuanBarang . "\",\"" . $namaKategoriBarang . "\",\"" . $namaGudang . "\",\"" . $_SESSION['uname'] . "\"\n";
    }; // while ($x = mysql_fetch_array($hasil)) {
    // kirim output CSV ke browser untuk di download
    // cari nama Customer
    $hasil2 = mysql_query("SELECT namaSupplier FROM supplier WHERE idSupplier='" . $_SESSION['idCustomer'] . "'");
    $y = mysql_fetch_array($hasil2);
    $namaSupplier = $y['namaSupplier'];
    //$namaFile = $namaSupplier . "-" . date("Y-m-d--H-i");
    // masukkan nama toko ini ke nama file csv
    $namaToko = $store_name;
    $namaToko = str_replace(' ', '_', $namaToko);
    $namaFile = 'PO-' . $namaToko . "-" . date("Y-m-d--H-i") . ".csv";

    // hapus transaksi jual ini dari table tmp_detail_jual
    mysql_query("DELETE FROM tmp_detail_jual WHERE idCustomer = '$_SESSION[idCustomer]' AND username = '$_SESSION[uname]'");
    $_SESSION['tot_pembelian'] = 0;
    releaseCustomer();

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=\"$namaFile\"");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $csv;

    // hapus transaksi jual ini dari table tmp_detail_jual
    mysql_query("DELETE FROM tmp_detail_jual WHERE idCustomer = '$_SESSION[idCustomer]' AND username = '$_SESSION[uname]'");
    $_SESSION['tot_pembelian'] = 0;

    unset($_SESSION['idCustomer']);
    unset($_SESSION['periode']);
    unset($_SESSION['range']);
    unset($_SESSION['persediaan']);
    releaseCustomer();
}

// simpan Config
elseif ($module == 'system' AND $act == 'setting-simpan') {
    if (isset($_POST['config'])) {
        $config = $_POST['config'];
        foreach ($config as $option => $value) {
            mysql_query("update config set value = '{$value}' where `option` = '{$option}'") or die(mysql_error());
        }
        header("Refresh:1; url=media.php?module={$module}&act=setting", true, 303);
        echo 'Setting sudah disimpan..';
    }
}

// Ambil data barang bermasalah dengan idKategori dan idSatuan
elseif ($module == 'system' && $act == 'maintenance-barang') {
    $result = mysql_query('
		  select barcode, namaBarang, barang.idSatuanBarang, barang.idKategoriBarang
                from barang
                left join kategori_barang kb on barang.idKategoriBarang = kb.idKategoriBarang
                left join satuan_barang sb on barang.idSatuanBarang = sb.idSatuanBarang
                where kb.idKategoriBarang is null or sb.idSatuanBarang is null
		  ') or die('Gagal cari data barang error, error: ' . mysql_error());
    ?>
    <table class='tabel'>
        <thead>
            <?php
            if (mysql_num_rows($result) > 0):
                ?>
                <tr>
                    <td colspan="4" style="text-align: right"><a id="tombol-auto-update" href="#"><button>Auto Update</button></a></td>
                </tr>
                <?php
            endif;
            ?>
            <tr>
                <th>Barcode</th>
                <th>Nama Barang</th>
                <th>Kategori Barang</th>
                <th>Satuan Barang</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            if (mysql_num_rows($result) > 0) {
                while ($barang = mysql_fetch_array($result)) {
                    ?>
                    <tr <?php echo $i % 2 == 0 ? 'class="alt"' : ''; ?>>
                        <td><?php echo $barang['barcode']; ?></td>
                        <td><?php echo $barang['namaBarang']; ?></td>
                        <td <?php echo $barang['idKategoriBarang'] == 0 ? 'class="error"' : ''; ?>><?php echo $barang['idKategoriBarang']; ?></td>
                        <td <?php //echo $barang['idSatuanBarang'] == 0 ? 'class="error"' : '';              ?>><?php echo $barang['idSatuanBarang']; ?></td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            else {
                ?>
                <tr>
                    <td colspan="4">Data tidak ditemukan</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <script>
        $("#tombol-auto-update").click(function () {
            var aksiurl = 'aksi.php?module=system&act=maintenance-upd-barang';
            $.ajax({
                url: aksiurl,
                type: "GET",
                success: function (data) {
                    if (data == 'update-selesai') {
                        ambilHasil();
                    }
                }
            });
        });

    </script>
    <?php
}

// Update kategori dan satuan barang menjadi 1 (id kategori), 3 (id satuan)
elseif ($module == 'system' && $act == 'maintenance-upd-barang') {
    mysql_query('
        update barang
            left join kategori_barang kb on barang.idKategoriBarang = kb.idKategoriBarang
            set barang.idKategoriBarang = 1
            where kb.idKategoriBarang is null
        ') or die('Gagal update kategori, error: ' . mysql_error());
    mysql_query('update barang
            left join satuan_barang kb on barang.idSatuanBarang = kb.idSatuanBarang
            set barang.idSatuanBarang = 3
            where kb.idSatuanBarang is null
        ') or die('Gagal update satuan, error: ' . mysql_error());
    echo 'update-selesai';
}

// Cek login admin
// Untuk ability diskon manual di jual barang
// Diturunkan level aksesnya ke level gudang
elseif ($module == 'diskon' && $act == 'loginadmin') {
    if (isset($_POST['username'])) {
        $username = htmlspecialchars($_POST['username']);
        $pass = md5($_POST['pass']);
        $sql = "select idUser
					from user
					join leveluser lu on lu.idLevelUser = user.idLevelUser
					where uname='{$username}' and pass='{$pass}' and lu.levelUser in ('admin','gudang')";
        $result = mysql_query($sql) or die(mysql_error());
        $ketemu = mysql_num_rows($result);
        if ($ketemu) {
            $_SESSION['hakAdmin'] = true;
            echo 'ketemu';
        }
        else {
            echo 'tidak ketemu';
        }
    }
    elseif (isset($_POST['logout'])) {
        unset($_SESSION['hakAdmin']);
    }
}

// Update harga satuan manually
// Untuk ability diskon manual di jual barang
elseif ($module === 'diskon' && $act === 'updatehj') {
    if (isset($_POST['pk'])) {
        $uid = $_POST['pk'];
        $hj = $_POST['value'];
        $sql = "update tmp_detail_jual set hargaJual=$hj where uid=$uid";
        $result = mysql_query($sql) or die(mysql_error());

        if (mysql_affected_rows() > 0) {
            if (!cekDiskonAdmin($uid)) {

            }
            $response = array('sukses' => true);
            echo json_encode($response);
            //echo '{sukses: true}';
        }
        elseif (mysql_affected_rows() < 0) {
            echo json_encode(array('sukses' => false));
        }
    }
}

// Ambil nama barang dan harga jual, info ketika entry diskon
elseif ($module === 'diskon' && $act === "getbarcodeinfo") {
    if (isset($_GET['barcode'])) {
        $barcode = $_GET['barcode'];
        $hasil = cekBarang($barcode);
        echo $hasil['namaBarang'] . ' :: Rp. ' . number_format($hasil['hargaJual'], 0, ',', '.');
    }
}
elseif ($module === 'hargabanded' && $act === 'getnamabarang') {
    if (isset($_GET['term'])) {
        $namaBarang = $_GET['term'];
        echo $term;
        $sql = "SELECT barcode, namaBarang FROM barang where namaBarang like '%{$namaBarang}%'";
        $hasil = mysql_query($sql);
        $barangs = array();
        while ($barang = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
            $barangs[] = array(
                'id' => $barang['barcode'],
                'label' => $barang['namaBarang'],
                'value' => $barang['namaBarang'],
            );
        }

        echo json_encode($barangs);
    }
}
elseif ($module === 'membership' && $act === 'simpan') {
    if (isset($_POST['config'])) {
        $config = $_POST['config'];
        foreach ($config as $option => $value) {
            mysql_query("update config set value = '{$value}' where `option` = '{$option}'") or die(mysql_error());
        }
        header("Refresh:1; url=media.php?module={$module}&act=setting", true, 303);
        echo 'Setting membership sudah disimpan..';
    }
}
elseif ($module === 'membership' && $act === 'tambahperiode') {
    if (isset($_POST['periode'])) {
        $periode = $_POST['periode'];
        //insert ke tabel
        mysql_query("INSERT INTO periode_poin (nama, awal, akhir) VALUES('{$periode['nama']}',{$periode['awal']},{$periode['akhir']})") or die('Gagal Insert Periode Poin');

        header('location:media.php?module=membership');
    }
}
elseif ($module === 'membership' && $act === 'hapusperiode') {
    if (isset($_GET['periodeId'])) {
        $periodeId = $_GET['periodeId'];
        //hapus periode
        mysql_query("DELETE FROM periode_poin WHERE id = {$periodeId}") or die('Gagal Hapus Periode Poin: ' . mysql_error());
    }
    header('location:media.php?module=membership');
}
elseif ($module === 'laporan' && $act === 'jumlahpoin') {
    if (isset($_POST['laporan'])) {
        $param = $_POST['laporan'];
        $sql = "SELECT awal, akhir FROM periode_poin WHERE id= {$param['periode']}";
        $query = mysql_query($sql);
        $periode = mysql_fetch_array($query, MYSQL_ASSOC);
        $sort = $param['sort'] == 1 ? 'DESC' : 'ASC';

        $sql = "SELECT poin.*, customer.nomor_kartu, customer.namaCustomer, customer.alamatCustomer,
                customer.telpCustomer, customer.email, customer.handphone, customer.nomor_ktp, customer.tanggal_lahir
                FROM
                (
                SELECT SUM(jumlah_poin) jumlah_poin, idCustomer
                            FROM transaksijual
                            WHERE YEAR(tglTransaksiJual)= {$param['tahun']} AND
                            MONTH(tglTransaksiJual) BETWEEN {$periode['awal']} AND {$periode['akhir']}
                GROUP BY idCustomer
                HAVING SUM(jumlah_poin) between {$param['jumlahDari']} AND {$param['jumlahSampai']}
                ) AS poin
                JOIN customer ON poin.idCustomer = customer.idCustomer AND customer.member=1
                ORDER BY poin.jumlah_poin {$sort}, customer.namaCustomer";
        $query = mysql_query($sql);
        ?>
        <html>
            <head>
                <link rel="stylesheet" type="text/css" href="../css/style.css" />
            </head>
            <body>
                <h2>Laporan Jumlah Poin</h2>
                <h4>Periode <?php echo bulanIndonesia($periode['awal']); ?> - <?php echo bulanIndonesia($periode['akhir']); ?> <?php echo $param['tahun']; ?></h4>
                <table class="tabel">
                    <thead>
                        <tr style="border-bottom: 1px solid gray">
                            <th>No Kartu</th>
                            <th>Nama</th>
                            <th>Jumlah Poin</th>
                            <th>Alamat</th>
                            <th>Telp</th>
                            <th>Handphone</th>
                            <th>Email</th>
                            <th>No KTP</th>
                            <th>Tgl Lahir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($data = mysql_fetch_array($query, MYSQL_ASSOC)) {
                            //print_r($data);
                            ?>
                            <tr>
                                <td><?php echo $data['nomor_kartu']; ?></td>
                                <td><?php echo $data['namaCustomer']; ?></td>
                                <td class="right"><?php echo $data['jumlah_poin']; ?></td>
                                <td><?php echo $data['alamatCustomer']; ?></td>
                                <td><?php echo $data['telpCustomer']; ?></td>
                                <td><?php echo $data['handphone']; ?></td>
                                <td><?php echo $data['email']; ?></td>
                                <td><?php echo $data['nomor_ktp']; ?></td>
                                <td><?php echo date_format(date_create_from_format('Y-m-d', $data['tanggal_lahir']), 'd-m-Y'); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </body>
        </html>

        <?php
    }
    else {
        echo 'Error';
    }
}
// else
else { // =======================================================================================================================================
    echo "Tidak Ada Aksi untuk modul ini";
}


/* CHANGELOG -----------------------------------------------------------

  1.6.0 / 2013-05-01 : Herwono		: fitur : cetak label harga perbarcode
  1.6.0 / 2013-02-24 : Harry Sufehmi	: fitur : transfer barang antar sesama pengguna AhadPOS
  1.6.0 / 2013-02-21 : Harry Sufehmi	: revisi: cetak struk : kini nama barang & harga dipisah menjadi 2 baris
  1.6.0 / 2013-02-07 : Harry Sufehmi	: bugfix: hapus barang kini sudah bisa
  1.2.5 / 2012-04-17 : Harry Sufehmi 	: bugfix: hapus satuan barang tidak berfungsi
  1.2.5 / 2012-03-16 : Harry Sufehmi 	: bugfix: kini perubahan barang (dari Barang - Cari Barang - Ubah) disimpan dengan benar
  (branch "($module=='barang' AND $act=='update')")
  1.2.5 / 2012-02-14 : Harry Sufehmi	: bugfix: kini Retur Pembelian ($act=='simpanretur') akan mengurangi jumlah stok (jumBarang) di table barang dengan benar
  1.0.3 / 2011-07-14 : Harry Sufehmi	: jika ganti / edit barcode, maka otomatis barcode ybs di table-table lainnya juga di update
  1.0.2 / 2011-03-04 : Harry Sufehmi	: jika user biasa ganti password, kembali ke Home
  1.0.1 / 2010-06-03 : Harry Sufehmi	: penambahan fasilitas workstation management, print ke PDF

  0.9.1		    : Gregorius Arief		: initial release

  ------------------------------------------------------------------------ */
?>
