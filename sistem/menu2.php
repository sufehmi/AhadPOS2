<?php
/* menu.php ------------------------------------------------------
version: 1.0.0

Part of AhadPOS : http://AhadPOS.com
License: GPL v2
http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License v2 (links provided above) for more details.
---------------------------------------------------------------- */

require_once($_SERVER["DOCUMENT_ROOT"].'/define.php');

// Query menu yang tampil sesuai level user
$sqlLv1= "select distinct m.id, m.nama, m.link, m.icon, m.label, m.accesskey "
		. "from menu m "
		. "join leveluser lu on lu.idLevelUser= m.level_user_id "
		. "left join menu child on child.parent_id= m.id "
		. "left join leveluser clu on clu.idLevelUser= child.level_user_id "
		. "where m.parent_id= 0 and m.publish= 'Y' ";

if ($_SESSION['leveluser']=== 'admin') {
	// nothing to do;
} elseif ($_SESSION['leveluser']=== 'gudang') {
	// Gudang-an
	$sqlLv1.="and (m.level_user_id=1 or lu.levelUser='gudang') or clu.levelUser='gudang' ";
} elseif ($_SESSION['leveluser']=== 'kasir') {
	// Kasir-an
	$sqlLv1.="and (m.level_user_id=1 or lu.levelUser='kasir') or clu.levelUser='kasir' ";
}

$sqlLv1.='order by m.urutan';

// Cek link yang sedang aktif
$link= substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "/") + 1);

// Cari id menu dan/atau parent nya jika ada
$sql= "select id,parent_id from menu where '{$link}' like concat(link,'%') order by parent_id desc limit 0,1";
$h= mysql_query($sql) or die(mysql_error());
$idMenu= mysql_fetch_array($h);
$hasil= mysql_query($sqlLv1);
?>

<header class="navbar navbar-default" id="header">
<button onclick='ta_toggle($(this))' data-menu='drawer' id='drawer-btn' data-active=0><i class='fa fa-bars'></i></button>
<h1><?php e(BRAND_NAME); ?></h1>
</header>

<nav id='drawer' class='sidemenu' style='display:none'><ul class="nav" id="mainmenu">
<?php
	while ($dataLv1= mysql_fetch_array($hasil)) :
		// Cek apakah menu level 1 sedang aktif
		$menuLv1Active= $idMenu['id']=== $dataLv1['id'] || $idMenu['parent_id']=== $dataLv1['id'] ? true : false;
		
		e('<li'); if ($menuLv1Active) { e(' class="active"'); } e(">");
		e('<a href="'.$dataLv1['link'].'" accesskey="'.$dataLv1['accesskey'].'">');
		e('<i class="'.$dataLv1['icon'].'"></i>'); e($dataLv1['label']);
		e('</a>');
		
		if ($menuLv1Active) :
					// Query menu yang tampil sesuai level user
					$sqlLv2= "select m.id, m.nama, m.link, m.icon, m.label, m.accesskey "
							. "from menu m "
							. "join leveluser lu on lu.idLevelUser= m.level_user_id "
							. "where parent_id= {$dataLv1['id']} and publish= 'Y' ";
					if ($_SESSION['leveluser']=== 'admin') {
						// nothing to do;
					} elseif ($_SESSION['leveluser']=== 'gudang') {
						$sqlLv2.="and (m.level_user_id=1 or lu.levelUser='gudang') ";
					} elseif ($_SESSION['leveluser']=== 'kasir') {
						$sqlLv2.="and (m.level_user_id=1 or lu.levelUser='kasir') ";
					}
					$sqlLv2.= "order by m.urutan ";
					//echo $sqlLv2;
					$hasil2= mysql_query($sqlLv2); $count2= mysql_num_rows($hasil2);
					
					if ($count2 >0) {
					e("\n<ul class='nav ah-menu-lv2' role='menu'>");
					while ($dataLv2= mysql_fetch_array($hasil2)):
						if ($idMenu['id']=== $dataLv2['id'] ) { $active2='active'; } else { $active2=''; }
						e("\n<li class='".$active2."'><a href='".$dataLv2['link']."' accesskey='".$dataLv2['accesskey']."'>");
						e("<i class='".$dataLv2['icon']."'></i>");
						e($dataLv2['label']);
						e("</a></li>");
						
					endwhile;
					e("</ul>\n");
					
					}

		endif;
		e("</li>\n");
	endwhile;
?>
</ul>

<div class="nav-option">
<p class='expand' onclick='ta_toggle_navExpand()'>Always Expand <i class='fa fa-square-o'></i></p>
</div>

</nav>

<?php
/* CHANGELOG -----------------------------------------------------------

1.0.0 : Abu Muhammad : initial release

------------------------------------------------------------------------ */
?>