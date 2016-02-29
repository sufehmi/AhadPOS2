<?php
/* menu.php ------------------------------------------------------
  version: 1.0.0

  Part of AhadPOS : http://AhadPOS.com
  License: GPL v2
  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
  http://vlsm.org/etc/gpl-unofficial.id.html

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License v2 (links provided above) for more details.
  ---------------------------------------------------------------- */

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

  1.0.0  : Abu Muhammad : initial release

  ------------------------------------------------------------------------ */
?>
