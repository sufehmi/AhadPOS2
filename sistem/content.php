<?php
/* content.php ------------------------------------------------------
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

include "../config/config.php";
include "../config/library.php";
include "../config/fungsi_indotgl.php";
include "../config/fungsi_combobox.php";
include "../config/class_paging.php";
include_once "modul/function.php";
//echo $_GET[module];
// Bagian Home
if ($_GET[module] == 'home') {
    $kas = getKasAwal($_SESSION[iduser]);
    $uang = getUangKasir($_SESSION[iduser]);
    ?>

    <h2>Selamat Datang</h2>
    <p>Hai <b><?php echo $_SESSION[namauser]; ?></b>, id Anda = <b><?php echo $_SESSION[iduser]; ?></b>.
        Anda menjabat sebagai <b><?php echo $_SESSION[leveluser]; ?></b>
        di sistem ini. Pergunakanlah dengan bijak jabatan Anda.</p>
    <p>Kas Awal Anda adalah : <b>Rp. <?php echo $kas; ?></b></p>
    <?php
    if ($_SESSION[leveluser] != 'kasir') {
        ?>
        <p>Uang Transaksi : <b>Rp.<?php echo $uang; ?></b></p>
        <?php
    }
    ?>
    <?php
    /* Menampilkan total nilai barang aging, jika level user admin atau semua */
    if ($_SESSION['leveluser'] == 'admin' || $_SESSION['leveluser'] == 'semua') {
        $hasil = mysql_query("SELECT total_nilai_stok, jumlah_hari_terakhir "
                . "FROM tmp_total_aging");
        if ($hasil) {
            $aging = mysql_fetch_array($hasil);
            ?>
            <p>Total nilai stok barang aging, dengan umur lebih lama dari <b><?php echo $aging['jumlah_hari_terakhir']; ?></b> hari adalah : <b>Rp. <?php echo number_format($aging['total_nilai_stok'], 0, ',', '.'); ?></b></p>
            <?php
        }
    }
    ?>
    <p>&nbsp;</p>

    <?php
    //$handle = fopen("http://www.rimbalinux.com/projects/ahadpos/news.html", "r");
    $handle = false;
    if ($handle === false) {
        ?>
        <p>(Berita terbaru seputar AhadPOS bisa didapatkan dari : <br />
            <a href=http://www.rimbalinux.com/projects/ahadpos/news.html>http://www.rimbalinux.com/projects/ahadpos/news.html</a> dan <a href=http://ahadpos.com>AhadPOS.com</a>)
        </p>
        <?php
    } else {

        echo "
		<center>
		<p><IFRAME SRC=http://www.rimbalinux.com/projects/ahadpos/news.html WIDTH=450 HEIGHT=150>
		<p>(Berita terbaru seputar AhadPOS bisa didapatkan dari : <br /><a href=http://www.rimbalinux.com/projects/ahadpos/news.html>http://www.rimbalinux.com/projects/ahadpos/news.html</a> dan <a href=http://ahadpos.com>AhadPOS.com</a>)</p>
		</IFRAME></p>
		</center>
		";
    }
    ?>

    <p>&nbsp;</p>
    <p>Waktu Login Saat ini: <?php echo tgl_indo(date("Y m d")); ?> | <?php echo date("H:i"); ?>
    </p>
    <?php
}

// Bagian User
elseif ($_GET[module] == 'user') {
    include "modul/mod_user.php";
}

// Bagian Modul
elseif ($_GET[module] == 'modul') {
    include "modul/mod_modul.php";
}


// Bagian Menu
elseif ($_GET[module] == 'menu') {
    include "modul/mod_menu.php";
}

// Bagian Satuan Barang
elseif ($_GET[module] == 'satuan_barang') {
    include "modul/mod_satuan_barang.php";
}

// Bagian Kategori Barang
elseif ($_GET[module] == 'kategori_barang') {
    include "modul/mod_kategori_barang.php";
}

// Bagian Rak
elseif ($_GET[module] == 'rak') {
    include "modul/mod_rak.php";
}

// Bagian Barang
elseif ($_GET[module] == 'barang') {
    include "modul/mod_barang.php";
}

// Bagian Supplier
elseif ($_GET[module] == 'supplier') {
    include "modul/mod_supplier.php";
}

// Bagian Customer
elseif ($_GET[module] == 'customer') {
    include "modul/mod_customer.php";
}

// Bagian Beli Barang
elseif ($_GET[module] == 'pembelian_barang') {
    include "modul/mod_beli_barang.php";
}

// Bagian Jual Barang
elseif ($_GET[module] == 'penjualan_barang') {
    include "modul/mod_jual_barang.php";
}

// Bagian Tampil Hutang
elseif ($_GET[module] == 'hutang') {
    include "modul/mod_hutang.php";
}

// Bagian Tampil Piutang
elseif ($_GET[module] == 'piutang') {
    include "modul/mod_piutang.php";
}

//KASIR
elseif ($_GET[module] == 'kasir') {
    include "modul/mod_kasir.php";
}

// Laporan Manajemen
elseif ($_GET[module] == 'laporan') {
    include "modul/mod_laporan.php";
}

// Manajemen Workstation
elseif ($_GET[module] == 'workstation') {
    include "modul/mod_manage_workstation.php";
}

// Ganti Password
elseif ($_GET[module] == 'ganti_password') {
    include "modul/mod_user.php";
}

// System
elseif ($_GET[module] == 'system') {
    include "modul/mod_system.php";
}

// System
elseif ($_GET[module] == 'membership') {
    include "modul/mod_membership.php";
}


/* CHANGELOG -----------------------------------------------------------

  1.0.2  : Gregorius Arief		: initial release

  ------------------------------------------------------------------------ */
?>
