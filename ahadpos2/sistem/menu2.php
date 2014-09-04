<?php
/* menu.php ------------------------------------------------------
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
//include "modul/function.php";
if (mysql_query("DESCRIBE `menu`")) {
	// table menu Exists, nothing to do
} else {
	// sql untuk update ke design baru
	$sql = "

			CREATE TABLE IF NOT EXISTS `menu` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `nama` varchar(100) NOT NULL,
			  `link` varchar(1000) NOT NULL,
			  `icon` varchar(45) DEFAULT NULL,
			  `parent_id` int(11) DEFAULT NULL,
			  `label` varchar(100) NOT NULL,
			  `accesskey` varchar(1) DEFAULT NULL,
			  `publish` enum('Y','N') NOT NULL,
			  `level_user_id` int(11) NOT NULL,
			  `urutan` int(11) NOT NULL DEFAULT '1',
			  `level` int(11) NOT NULL DEFAULT '0',
			  `last_update` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=50";
	$result = mysql_query($sql) or die(mysql_error());
	if ($result) {
		$sql = "
			INSERT INTO `menu` (`id`, `nama`, `link`, `icon`, `parent_id`, `label`, `accesskey`, `publish`, `level_user_id`, `urutan`, `level`, `last_update`) VALUES
			(1, 'Home', 'media.php?module=home', 'fa fa-home fa-4x', 0, 'Home', '', 'Y', 1, 1, 0, NULL),
			(2, 'Barang', 'media.php?module=barang', 'fa fa-barcode fa-4x', 0, 'Barang', '', 'Y', 3, 2, 0, NULL),
			(3, 'Pembelian', 'media.php?module=pembelian_barang', 'fa fa-truck fa-4x', 0, 'Pembelian', '', 'Y', 3, 3, 0, NULL),
			(4, 'Kasir', 'media.php?module=kasir', 'fa fa-shopping-cart fa-4x', 0, 'Kasir', '', 'Y', 3, 4, 0, NULL),
			(5, 'Laporan', 'media.php?module=laporan', 'fa fa-bar-chart-o fa-4x', 0, 'Laporan', '', 'Y', 2, 5, 0, NULL),
			(6, 'Stock Opname', 'media.php?module=barang&act=cetakbarang1', 'fa fa-check-square-o fa-4x', 0, 'Stock Op', '', 'Y', 3, 5, 0, NULL),
			(7, 'Settings', 'media.php?module=ganti_password', 'fa fa-wrench fa-4x', 0, 'Settings', '', 'Y', 2, 7, 0, NULL),
			(8, 'Logout', 'logout.php', 'fa fa-power-off fa-4x', 0, 'Logout', '', 'Y', 1, 9, 0, NULL),
			(9, 'Satuan Barang', 'media.php?module=satuan_barang', '', 2, 'Satuan Barang', '', 'Y', 3, 1, 0, NULL),
			(10, 'Menu', 'media.php?module=menu', '', 7, 'Menu', '', 'Y', 2, 4, 0, NULL),
			(11, 'Supplier', 'media.php?module=supplier', '', 7, 'Supplier', '', 'Y', 3, 1, 0, NULL),
			(12, 'Kategori Barang', 'media.php?module=kategori_barang', '', 2, 'Kategori Barang', '', 'Y', 3, 2, 0, NULL),
			(13, 'Rak Barang', 'media.php?module=rak', '', 2, 'Rak Barang', '', 'Y', 3, 3, 0, NULL),
			(14, 'Tambah Barang', 'media.php?module=barang&act=tambahbarang', '', 2, 'Tambah Barang', '', 'Y', 3, 4, 0, NULL),
			(15, 'Cari Barang', 'media.php?module=barang&act=caribarang1', '', 2, 'Cari Barang', '', 'Y', 3, 5, 0, NULL),
			(16, 'Cetak Label per Rak', 'media.php?module=barang&act=cetaklabel1', '', 2, 'Cetak Label per Rak', '', 'Y', 3, 6, 0, NULL),
			(17, 'Transfer Barang Antar Ahad', 'media.php?module=barang&act=transfer1', '', 2, 'Transfer Barang Antar Ahad', '', 'Y', 3, 7, 0, NULL),
			(18, 'Input Rak Barang', 'media.php?module=barang&act=inputrak', '', 2, 'Input Rak Barang', '', 'Y', 3, 8, 0, NULL),
			(19, 'Pembelian Barang', 'media.php?module=pembelian_barang&act=pembelianbarang', '', 3, 'Pembelian Barang', '', 'Y', 3, 1, 0, NULL),
			(20, 'Retur Pembelian', 'media.php?module=pembelian_barang&act=returpembelian', '', 3, 'Retur Pembelian', '', 'Y', 3, 2, 0, NULL),
			(21, 'Cetak Nota Retur', 'media.php?module=pembelian_barang&act=cetakretur', '', 3, 'Cetak Nota Retur', '', 'Y', 3, 3, 0, NULL),
			(22, 'Input pembelian elektronik', 'media.php?module=pembelian_barang&act=inputeprocurement1', '', 3, 'Input Pembelian Elektronik', '', 'Y', 3, 4, 0, NULL),
			(23, 'Input RPO per item', 'media.php?module=pembelian_barang&act=buatrpo1', '', 3, 'Input RPO (per Item)', '', 'Y', 3, 5, 0, NULL),
			(24, 'Input RPO per Supplier', 'media.php?module=pembelian_barang&act=rposup1', '', 3, 'Input RPO per Supplier', '', 'Y', 3, 6, 0, NULL),
			(25, 'Buka kasir', 'media.php?module=kasir&act=bukakasir', '', 4, 'Buka Kasir', '', 'Y', 3, 1, 0, NULL),
			(26, 'Tutup kasir', 'media.php?module=kasir&act=tutupkasir', '', 4, 'Tutup Kasir', '', 'Y', 3, 2, 0, NULL),
			(27, 'Penambahan Dana', 'media.php?module=kasir&act=tambahdana', '', 4, 'Penambahan Dana', '', 'Y', 3, 3, 0, NULL),
			(28, 'Penjualan', 'media.php?module=penjualan_barang', '', 4, 'Penjualan', '', 'Y', 4, 4, 0, NULL),
			(29, 'User', 'media.php?module=user', '', 7, 'User', '', 'Y', 2, 3, 0, NULL),
			(34, 'Customer', 'media.php?module=customer', '', 7, 'Customer', '', 'Y', 4, 2, 0, NULL),
			(35, 'Workstation', 'media.php?module=workstation', '', 7, 'Workstation', '', 'Y', 2, 5, 0, NULL),
			(36, 'Personal Info', 'media.php?module=ganti_password', '', 7, 'Personal Info', '', 'Y', 2, 6, 0, NULL),
			(37, 'Laporan Pemb Brg / tgl', 'media.php?module=pembelian_barang&act=laporanpembeliantanggal', '', 5, 'Pembelian Barang per Tanggal', '', 'Y', 2, 1, 0, NULL),
			(38, 'Laporan Pemb Brg / sup', 'media.php?module=pembelian_barang&act=laporanpembelian', '', 5, 'Pembelian Barang per Supplier', '', 'Y', 2, 2, 0, NULL),
			(39, 'Laporan Penjualan', 'media.php?module=laporan&act=penjualan1', '', 5, 'Penjualan', '', 'Y', 2, 3, 0, NULL),
			(40, 'Total Stock', 'media.php?module=laporan&act=total1', '', 5, 'Total Stock', '', 'Y', 2, 4, 0, NULL),
			(41, 'Top Rank', 'media.php?module=laporan&act=toprank1', '', 5, 'Top Rank', '', 'Y', 2, 5, 0, NULL),
			(42, 'Aging', 'media.php?module=laporan&act=aging1', '', 5, 'Aging Stock', '', 'Y', 2, 6, 0, NULL),
			(43, 'Cetak Stock Barang', 'media.php?module=barang&act=cetakbarang1', '', 6, 'Cetak Stock Barang', '', 'Y', 3, 1, 0, NULL),
			(44, 'Cetak Form Stock Op', 'media.php?module=barang&act=cetakSO', '', 6, 'Cetak Form Stock Opname', '', 'Y', 3, 2, 0, NULL),
			(45, 'Input SO Manual', 'media.php?module=barang&act=inputSO', '', 6, 'Input SO Manual', '', 'Y', 3, 3, 0, NULL),
			(46, 'Fast SO', '../tools/fast-stock-opname/fast-SO.php', '', 6, 'Input Fast SO', '', 'Y', 3, 4, 0, NULL),
			(47, 'Approve Fast SO', 'media.php?module=barang&act=ApproveFastSO1', '', 6, 'Approve Fast SO', '', 'Y', 3, 5, 0, NULL),
			(48, 'Input Mobil SO', '../tools/fast-stock-opname/fast-SO-mobile.php', '', 6, 'Input Mobile SO', '', 'Y', 3, 6, 0, NULL),
			(49, 'Approve Mobile SO', 'media.php?module=barang&act=ApproveMobileSO1', '', 6, 'Approve Mobile SO', '', 'Y', 3, 7, 0, NULL),
			(50, 'System', 'media.php?module=system', 'fa fa-cogs fa-4x', 0, 'System', '', 'Y', 2, 8, 0, NULL),
			(51, 'Setting', 'media.php?module=system&act=setting', '', 50, 'Setting', '', 'Y', 2, 1, 0, NULL),
			(52, 'Maintenance', 'media.php?module=system&act=maintenance', '', 50, 'Maintenance', '', 'Y', 2, 2, 0, NULL),
			(53, 'Pindah Supplier', 'media.php?module=barang&act=pindahsupplier', '', 2, 'Pindah Supplier', '', 'Y', 2, 10, 0, NULL),
            (56, 'Pindah Rak', 'media.php?module=barang&act=pindahrak', '', 2, 'Pindah Rak', '', 'Y', 2, 11, 0, NULL);
			";
		mysql_query($sql) or die(mysql_error());
	}
}


// Query menu yang tampil sesuai level user
$sqlLv1 = "select distinct m.id, m.nama, m.link, m.icon, m.label, m.accesskey "
		. "from menu m "
		. "join leveluser lu on lu.idLevelUser = m.level_user_id "
		. "left join menu child on child.parent_id = m.id "
		. "left join leveluser clu on clu.idLevelUser = child.level_user_id "
		. "where m.parent_id = 0 and m.publish = 'Y' ";

if ($_SESSION['leveluser'] === 'admin') {
	// nothing to do;
} elseif ($_SESSION['leveluser'] === 'gudang') {
	$sqlLv1.="and (m.level_user_id=1 or lu.levelUser='gudang') or clu.levelUser='gudang' ";
} elseif ($_SESSION['leveluser'] === 'kasir') {
	$sqlLv1.="and (m.level_user_id=1 or lu.levelUser='kasir') or clu.levelUser='kasir' ";
}

$sqlLv1.='order by m.urutan';

// Cek link yang sedang aktif
$link = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "/") + 1);

// Cari id menu dan/atau parent nya jika ada
$sql = "select id,parent_id from menu where '{$link}' like concat(link,'%') order by parent_id desc limit 0,1";
$h = mysql_query($sql) or die(mysql_error());
$idMenu = mysql_fetch_array($h);
$hasil = mysql_query($sqlLv1);
?>
<ul>
	<?php
	while ($dataLv1 = mysql_fetch_array($hasil)) :
		// Cek apakah menu level 1 sedang aktif
		$menuLv1Active = $idMenu['id'] === $dataLv1['id'] || $idMenu['parent_id'] === $dataLv1['id'] ? true : false;
		?>
		<li <?php echo $menuLv1Active ? 'class="active"' : ''; ?>><a href="<?php echo $dataLv1['link']; ?>" accesskey="<?php echo $dataLv1['accesskey']; ?>"><i class="<?php echo $dataLv1['icon']; ?>"></i><?php echo $dataLv1['label']; ?></a>
			<?php
			// Jika menu level 1 aktif maka render menu level 2 nya 
			if ($menuLv1Active) :
				?>
				<ul id="menu-level-2">

					<?php
					// Query menu yang tampil sesuai level user
					$sqlLv2 = "select m.id, m.nama, m.link, m.icon, m.label, m.accesskey "
							. "from menu m "
							. "join leveluser lu on lu.idLevelUser = m.level_user_id "
							. "where parent_id = {$dataLv1['id']} and publish = 'Y' ";
					if ($_SESSION['leveluser'] === 'admin') {
						// nothing to do;
					} elseif ($_SESSION['leveluser'] === 'gudang') {
						$sqlLv2.="and  (m.level_user_id=1 or lu.levelUser='gudang')  ";
					} elseif ($_SESSION['leveluser'] === 'kasir') {
						$sqlLv2.="and  (m.level_user_id=1 or lu.levelUser='kasir')  ";
					}
					$sqlLv2.= "order by m.urutan ";
					//echo $sqlLv2;
					$hasil2 = mysql_query($sqlLv2);
					$count2 = mysql_num_rows($hasil2);
					if ($count2 == 0) {
						echo '<li><span>&nbsp;</span></li>';
					}
					while ($dataLv2 = mysql_fetch_array($hasil2)):
						?>
						<?php //echo 'idLink = '.$idMenu['id'].' , iddata='.$dataLv2['id']; ?>
						<li <?php echo $idMenu['id'] === $dataLv2['id'] ? 'class="active"' : ''; ?>><a href="<?php echo $dataLv2['link']; ?>" accesskey="<?php echo $dataLv2['accesskey']; ?>"><?php echo $dataLv2['icon'] != '' ? '<i class="' . $dataLv2['icon'] . '"></i>' : ''; ?><?php echo $dataLv2['label']; ?></a></li>
						<?php
					endwhile;
					?>
				</ul>
				<?php
			endif;
			?>
		</li>
		<?php
	endwhile;
	?>
</ul>
<?php
/* CHANGELOG -----------------------------------------------------------

  0.0.1  : Abu Fathir : initial release

  ------------------------------------------------------------------------ */
?>
