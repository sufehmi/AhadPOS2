<?php
/* mod_modul.php ------------------------------------------------------
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

include "../config/config.php";
check_user_access(basename($_SERVER['SCRIPT_NAME']));


$ambilLevelUser = mysql_query("select * from leveluser");

switch ($_GET[act]) {
	// Tampil Menu
	default:
		?>
		<h2>Data Menu</h2>
		<form method=POST action='?module=menu&act=tambahmenu'>
			<input type=submit value='Tambah Menu'>
		</form>
		<form method=POST action='?module=menu&act=listicon'>
			<input type=submit value='List Icon'>
		</form>

		<br/>
		<table class="tabel">
			<tr>
				<th>No</th>
				<th>Nama Menu</th>
				<th>Parent</th>
				<th>Link</th>
				<th>Icon</th>
				<th>Label</th>
				<th>Access<br/>Key</th>
				<th>Publish</th>
				<th>Pejabat</th>
				<th>Aksi</th>
			</tr>
			<?php
			$tampil = mysql_query("SELECT m.id, m.nama, m.link, m.icon, m.label, m.accesskey, parent.nama as nama_parent , m.publish, lu.levelUser, m.urutan
						from menu m 
						join leveluser lu on lu.idLevelUser = m.level_user_id
						left join menu parent on parent.id = m.parent_id
						order by parent.nama, m.urutan");
			$no = 1;
			while ($r = mysql_fetch_array($tampil)) {
				?>
				<tr class="<?php echo $no % 2 === 0 ? 'alt' : ''; ?>" >
					<td><?php echo $no; ?></td>
					<td><?php echo $r['nama']; ?></td>
					<td><?php echo $r['nama_parent']; ?></td>
					<td><a href="<?php echo $r['link']; ?>"><?php echo $r['link']; ?></a></td>
					<td class="center icon-small"><i class="<?php echo $r['icon']; ?>"></i></td>
					<td><?php echo $r['label']; ?></td>
					<td class="center"><?php echo $r['accesskey']; ?></td>
					<td class="center"><?php echo $r['publish']; ?></td>
					<td><?php echo $r['levelUser']; ?></td>
					<td><a href=?module=menu&act=editmenu&id=<?php echo $r['id']; ?>>Edit</a> | <a href=./aksi.php?module=menu&act=hapus&id=<?php echo $r['id']; ?>>Hapus</a>
					</td>
				</tr>
				<?php
				$no++;
			}
			?>
		</table>
		<br />
		<a href=javascript:history.go(-1)><< Kembali</a>
		<?php
		break;

	case "tambahmenu":
		?>
		<h2>Tambah Menu</h2>
		<form method=POST action='./aksi.php?module=menu&act=input'>
			<table>
				<tr>
					<td>Nama Menu</td> 
					<td> : <input type=text name='nama' size=30></td>
				</tr>
				<tr>
					<td>Parent</td>
					<td> : <select name="parent_id">
							<option value="0">NULL</option>
							<?php
							$sql = 'select id, nama from menu where parent_id=0';
							$hasil = mysql_query($sql) or die(mysql_error());
							while ($parent = mysql_fetch_array($hasil)) :
								?>
								<option value="<?php echo $parent['id']; ?>"><?php echo $parent['nama']; ?></option>
								<?php
							endwhile;
							?>
						</select>
				</tr>
				<tr>
					<td>Link</td>       
					<td> : <input type="text" name="link" size=30 value="?module="></td>
				</tr>
				<tr>
					<td>Icon</td>       
					<td> : <input type="text" name="icon" size=30></td>
				</tr>
				<tr>
					<td>Label</td>       
					<td> : <input type="text" name="label" size=30></td>
				</tr>
				<tr>
					<td>Access Key</td>       
					<td> : <input type="text" name="accesskey" size=30></td>
				</tr>
				<tr>
					<td>Publish</td>    
					<td> : <input type="radio" name="publish" value="Y" checked>Y
						<input type="radio" name="publish" value="N">N  </td>
				</tr>
				<tr>
					<td>Pejabat</td>
					<td> : 
						<select name="level_user_id">
							<option value="0">- Jabatan User-</option>
							<?php
							while ($level = mysql_fetch_array($ambilLevelUser)) :
								?>
								<option value="<?php echo $level['idLevelUser']; ?>"><?php echo $level['levelUser']; ?></option>
								<?php
							endwhile;
							?>
						</select>
					</td>
				</tr>

				<tr>
					<td>Urutan</td>    
					<td> : <input type="text" name="urutan" value="1" /></td>
				</tr>
				<tr>
					<td colspan=2>&nbsp;</td>
				</tr>
				<tr>
					<td colspan=2 align=right>
						<input type=submit value=Simpan>&nbsp;&nbsp;&nbsp;
						<input type=button value=Batal onclick=self.history.back()></td>
				</tr>
			</table>
		</form>
		<?php
		break;

	case "editmenu":
		$edit = mysql_query("SELECT * FROM menu WHERE id = '$_GET[id]'");
		$data = mysql_fetch_array($edit);
		?>
		<h2>Edit Menu</h2>
		<form method="POST" action="./aksi.php?module=menu&act=update">
			<input type=hidden name="id" value="<?php echo $data['id']; ?>">
			<table>
				<tr><td>Nama Menu</td>     
					<td> : <input type="text" name="nama" value="<?php echo $data['nama']; ?>"></td>
				</tr>
				<tr>
					<td>Parent</td>
					<td> : <select name="parent_id">
							<option value="0">NULL</option>
							<?php
							$sql = 'select id, nama from menu where parent_id=0';
							$hasil = mysql_query($sql) or die(mysql_error());
							while ($parent = mysql_fetch_array($hasil)) :
								?>
								<option value="<?php echo $parent['id']; ?>" <?php echo $parent['id'] === $data['parent_id'] ? 'selected' : ''; ?>><?php echo $parent['nama']; ?></option>
								<?php
							endwhile;
							?>
						</select>
				</tr>
				<tr>
					<td>Link</td>     
					<td> : <input type="text" name="link" size=30 value="<?php echo htmlentities($data['link']); ?>"></td>
				</tr>
				<tr>
					<td>Icon</td>       
					<td> : <input type="text" name="icon" size=30 value="<?php echo $data['icon']; ?>"></td>
				</tr>
				<tr>
					<td>Label</td>       
					<td> : <input type="text" name="label" size=30 value="<?php echo htmlentities($data['label']); ?>"></td>
				</tr>
				<tr>
					<td>Access Key</td>       
					<td> : <input type="text" name="accesskey" size=30 value="<?php echo $data['accesskey']; ?>"></td>
				</tr>
				<tr>
					<td>Publish</td> 
					<td> : <input type=radio name='publish' value='Y' <?php echo $data['publish'] == 'Y' ? 'checked' : ''; ?>> Y
						<input type=radio name='publish' value='N' <?php echo $data['publish'] == 'N' ? 'checked' : ''; ?>> N</td>
				</tr>
				<tr>
					<td>Jabatan User</td>
					<td> : <select name="level_user_id">
							<?php
							while ($level = mysql_fetch_array($ambilLevelUser)) :
								?>
								<option value="<?php echo $level['idLevelUser']; ?>" <?php echo $level['idLevelUser'] === $data['level_user_id'] ? 'selected' : '' ?>><?php echo $level['levelUser']; ?></option>
								<?php
							endwhile;
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Urutan</td>    
					<td> : <input type="text" name="urutan" value="<?php echo $data['urutan']; ?>" /></td>
				</tr>
				<tr><td colspan=2>&nbsp;</td></tr>
				<tr><td colspan=2><input type="submit" value="update">&nbsp;&nbsp;&nbsp;
						<input type=button value=Batal onclick=self.history.back()></td>
				</tr>
			</table>
		</form>
		<?php
		break;

	case 'listicon':
		?>

		<div id="new">
			<h2 class="page-header">Font Awesome 4.0</h2>


			<div class="row fontawesome-icon-list">

				<div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-rub"></i> fa-rub</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/rub"><i class="fa fa-ruble"></i> fa-ruble <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/rub"><i class="fa fa-rouble"></i> fa-rouble <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/pagelines"><i class="fa fa-pagelines"></i> fa-pagelines</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/stack-exchange"><i class="fa fa-stack-exchange"></i> fa-stack-exchange</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-circle-o-right"><i class="fa fa-arrow-circle-o-right"></i> fa-arrow-circle-o-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-circle-o-left"><i class="fa fa-arrow-circle-o-left"></i> fa-arrow-circle-o-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-left"><i class="fa fa-caret-square-o-left"></i> fa-caret-square-o-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-left"><i class="fa fa-toggle-left"></i> fa-toggle-left <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/dot-circle-o"><i class="fa fa-dot-circle-o"></i> fa-dot-circle-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/wheelchair"><i class="fa fa-wheelchair"></i> fa-wheelchair</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/vimeo-square"><i class="fa fa-vimeo-square"></i> fa-vimeo-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/try"><i class="fa fa-try"></i> fa-try</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/try"><i class="fa fa-turkish-lira"></i> fa-turkish-lira <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/plus-square-o"><i class="fa fa-plus-square-o"></i> fa-plus-square-o</a></div>

			</div>

		</div>

		<section id="web-application">
			<h2 class="page-header">Web Application Icons</h2>

			<div class="row fontawesome-icon-list">



				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/adjust"><i class="fa fa-adjust"></i> fa-adjust</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/anchor"><i class="fa fa-anchor"></i> fa-anchor</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/archive"><i class="fa fa-archive"></i> fa-archive</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrows"><i class="fa fa-arrows"></i> fa-arrows</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrows-h"><i class="fa fa-arrows-h"></i> fa-arrows-h</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrows-v"><i class="fa fa-arrows-v"></i> fa-arrows-v</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/asterisk"><i class="fa fa-asterisk"></i> fa-asterisk</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/ban"><i class="fa fa-ban"></i> fa-ban</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bar-chart-o"><i class="fa fa-bar-chart-o"></i> fa-bar-chart-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/barcode"><i class="fa fa-barcode"></i> fa-barcode</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bars"><i class="fa fa-bars"></i> fa-bars</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/beer"><i class="fa fa-beer"></i> fa-beer</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bell"><i class="fa fa-bell"></i> fa-bell</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bell-o"><i class="fa fa-bell-o"></i> fa-bell-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bolt"><i class="fa fa-bolt"></i> fa-bolt</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/book"><i class="fa fa-book"></i> fa-book</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bookmark"><i class="fa fa-bookmark"></i> fa-bookmark</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bookmark-o"><i class="fa fa-bookmark-o"></i> fa-bookmark-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/briefcase"><i class="fa fa-briefcase"></i> fa-briefcase</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bug"><i class="fa fa-bug"></i> fa-bug</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/building-o"><i class="fa fa-building-o"></i> fa-building-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bullhorn"><i class="fa fa-bullhorn"></i> fa-bullhorn</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bullseye"><i class="fa fa-bullseye"></i> fa-bullseye</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/calendar"><i class="fa fa-calendar"></i> fa-calendar</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/calendar-o"><i class="fa fa-calendar-o"></i> fa-calendar-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/camera"><i class="fa fa-camera"></i> fa-camera</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/camera-retro"><i class="fa fa-camera-retro"></i> fa-camera-retro</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-down"><i class="fa fa-caret-square-o-down"></i> fa-caret-square-o-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-left"><i class="fa fa-caret-square-o-left"></i> fa-caret-square-o-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-right"><i class="fa fa-caret-square-o-right"></i> fa-caret-square-o-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-up"><i class="fa fa-caret-square-o-up"></i> fa-caret-square-o-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/certificate"><i class="fa fa-certificate"></i> fa-certificate</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/check"><i class="fa fa-check"></i> fa-check</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/check-circle"><i class="fa fa-check-circle"></i> fa-check-circle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/check-circle-o"><i class="fa fa-check-circle-o"></i> fa-check-circle-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/check-square"><i class="fa fa-check-square"></i> fa-check-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/check-square-o"><i class="fa fa-check-square-o"></i> fa-check-square-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/circle"><i class="fa fa-circle"></i> fa-circle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/circle-o"><i class="fa fa-circle-o"></i> fa-circle-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/clock-o"><i class="fa fa-clock-o"></i> fa-clock-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/cloud"><i class="fa fa-cloud"></i> fa-cloud</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/cloud-download"><i class="fa fa-cloud-download"></i> fa-cloud-download</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/cloud-upload"><i class="fa fa-cloud-upload"></i> fa-cloud-upload</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/code"><i class="fa fa-code"></i> fa-code</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/code-fork"><i class="fa fa-code-fork"></i> fa-code-fork</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/coffee"><i class="fa fa-coffee"></i> fa-coffee</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/cog"><i class="fa fa-cog"></i> fa-cog</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/cogs"><i class="fa fa-cogs"></i> fa-cogs</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/comment"><i class="fa fa-comment"></i> fa-comment</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/comment-o"><i class="fa fa-comment-o"></i> fa-comment-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/comments"><i class="fa fa-comments"></i> fa-comments</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/comments-o"><i class="fa fa-comments-o"></i> fa-comments-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/compass"><i class="fa fa-compass"></i> fa-compass</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/credit-card"><i class="fa fa-credit-card"></i> fa-credit-card</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/crop"><i class="fa fa-crop"></i> fa-crop</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/crosshairs"><i class="fa fa-crosshairs"></i> fa-crosshairs</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/cutlery"><i class="fa fa-cutlery"></i> fa-cutlery</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/tachometer"><i class="fa fa-dashboard"></i> fa-dashboard <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/desktop"><i class="fa fa-desktop"></i> fa-desktop</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/dot-circle-o"><i class="fa fa-dot-circle-o"></i> fa-dot-circle-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/download"><i class="fa fa-download"></i> fa-download</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/pencil-square-o"><i class="fa fa-edit"></i> fa-edit <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/ellipsis-h"><i class="fa fa-ellipsis-h"></i> fa-ellipsis-h</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/ellipsis-v"><i class="fa fa-ellipsis-v"></i> fa-ellipsis-v</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/envelope"><i class="fa fa-envelope"></i> fa-envelope</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/envelope-o"><i class="fa fa-envelope-o"></i> fa-envelope-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/eraser"><i class="fa fa-eraser"></i> fa-eraser</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/exchange"><i class="fa fa-exchange"></i> fa-exchange</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/exclamation"><i class="fa fa-exclamation"></i> fa-exclamation</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/exclamation-circle"><i class="fa fa-exclamation-circle"></i> fa-exclamation-circle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/exclamation-triangle"><i class="fa fa-exclamation-triangle"></i> fa-exclamation-triangle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/external-link"><i class="fa fa-external-link"></i> fa-external-link</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/external-link-square"><i class="fa fa-external-link-square"></i> fa-external-link-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/eye"><i class="fa fa-eye"></i> fa-eye</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/eye-slash"><i class="fa fa-eye-slash"></i> fa-eye-slash</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/female"><i class="fa fa-female"></i> fa-female</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/fighter-jet"><i class="fa fa-fighter-jet"></i> fa-fighter-jet</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/film"><i class="fa fa-film"></i> fa-film</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/filter"><i class="fa fa-filter"></i> fa-filter</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/fire"><i class="fa fa-fire"></i> fa-fire</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/fire-extinguisher"><i class="fa fa-fire-extinguisher"></i> fa-fire-extinguisher</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/flag"><i class="fa fa-flag"></i> fa-flag</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/flag-checkered"><i class="fa fa-flag-checkered"></i> fa-flag-checkered</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/flag-o"><i class="fa fa-flag-o"></i> fa-flag-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bolt"><i class="fa fa-flash"></i> fa-flash <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/flask"><i class="fa fa-flask"></i> fa-flask</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/folder"><i class="fa fa-folder"></i> fa-folder</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/folder-o"><i class="fa fa-folder-o"></i> fa-folder-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/folder-open"><i class="fa fa-folder-open"></i> fa-folder-open</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/folder-open-o"><i class="fa fa-folder-open-o"></i> fa-folder-open-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/frown-o"><i class="fa fa-frown-o"></i> fa-frown-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/gamepad"><i class="fa fa-gamepad"></i> fa-gamepad</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/gavel"><i class="fa fa-gavel"></i> fa-gavel</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/cog"><i class="fa fa-gear"></i> fa-gear <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/cogs"><i class="fa fa-gears"></i> fa-gears <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/gift"><i class="fa fa-gift"></i> fa-gift</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/glass"><i class="fa fa-glass"></i> fa-glass</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/globe"><i class="fa fa-globe"></i> fa-globe</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/users"><i class="fa fa-group"></i> fa-group <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/hdd-o"><i class="fa fa-hdd-o"></i> fa-hdd-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/headphones"><i class="fa fa-headphones"></i> fa-headphones</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/heart"><i class="fa fa-heart"></i> fa-heart</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/heart-o"><i class="fa fa-heart-o"></i> fa-heart-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/home"><i class="fa fa-home"></i> fa-home</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/inbox"><i class="fa fa-inbox"></i> fa-inbox</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/info"><i class="fa fa-info"></i> fa-info</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/info-circle"><i class="fa fa-info-circle"></i> fa-info-circle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/key"><i class="fa fa-key"></i> fa-key</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/keyboard-o"><i class="fa fa-keyboard-o"></i> fa-keyboard-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/laptop"><i class="fa fa-laptop"></i> fa-laptop</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/leaf"><i class="fa fa-leaf"></i> fa-leaf</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/gavel"><i class="fa fa-legal"></i> fa-legal <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/lemon-o"><i class="fa fa-lemon-o"></i> fa-lemon-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/level-down"><i class="fa fa-level-down"></i> fa-level-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/level-up"><i class="fa fa-level-up"></i> fa-level-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/lightbulb-o"><i class="fa fa-lightbulb-o"></i> fa-lightbulb-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/location-arrow"><i class="fa fa-location-arrow"></i> fa-location-arrow</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/lock"><i class="fa fa-lock"></i> fa-lock</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/magic"><i class="fa fa-magic"></i> fa-magic</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/magnet"><i class="fa fa-magnet"></i> fa-magnet</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/share"><i class="fa fa-mail-forward"></i> fa-mail-forward <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/reply"><i class="fa fa-mail-reply"></i> fa-mail-reply <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/mail-reply-all"><i class="fa fa-mail-reply-all"></i> fa-mail-reply-all</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/male"><i class="fa fa-male"></i> fa-male</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/map-marker"><i class="fa fa-map-marker"></i> fa-map-marker</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/meh-o"><i class="fa fa-meh-o"></i> fa-meh-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/microphone"><i class="fa fa-microphone"></i> fa-microphone</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/microphone-slash"><i class="fa fa-microphone-slash"></i> fa-microphone-slash</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/minus"><i class="fa fa-minus"></i> fa-minus</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/minus-circle"><i class="fa fa-minus-circle"></i> fa-minus-circle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/minus-square"><i class="fa fa-minus-square"></i> fa-minus-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/minus-square-o"><i class="fa fa-minus-square-o"></i> fa-minus-square-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/mobile"><i class="fa fa-mobile"></i> fa-mobile</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/mobile"><i class="fa fa-mobile-phone"></i> fa-mobile-phone <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/money"><i class="fa fa-money"></i> fa-money</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/moon-o"><i class="fa fa-moon-o"></i> fa-moon-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/music"><i class="fa fa-music"></i> fa-music</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/pencil"><i class="fa fa-pencil"></i> fa-pencil</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/pencil-square"><i class="fa fa-pencil-square"></i> fa-pencil-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/pencil-square-o"><i class="fa fa-pencil-square-o"></i> fa-pencil-square-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/phone"><i class="fa fa-phone"></i> fa-phone</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/phone-square"><i class="fa fa-phone-square"></i> fa-phone-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/picture-o"><i class="fa fa-picture-o"></i> fa-picture-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/plane"><i class="fa fa-plane"></i> fa-plane</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/plus"><i class="fa fa-plus"></i> fa-plus</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/plus-circle"><i class="fa fa-plus-circle"></i> fa-plus-circle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/plus-square"><i class="fa fa-plus-square"></i> fa-plus-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/plus-square-o"><i class="fa fa-plus-square-o"></i> fa-plus-square-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/power-off"><i class="fa fa-power-off"></i> fa-power-off</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/print"><i class="fa fa-print"></i> fa-print</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/puzzle-piece"><i class="fa fa-puzzle-piece"></i> fa-puzzle-piece</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/qrcode"><i class="fa fa-qrcode"></i> fa-qrcode</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/question"><i class="fa fa-question"></i> fa-question</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/question-circle"><i class="fa fa-question-circle"></i> fa-question-circle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/quote-left"><i class="fa fa-quote-left"></i> fa-quote-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/quote-right"><i class="fa fa-quote-right"></i> fa-quote-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/random"><i class="fa fa-random"></i> fa-random</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/refresh"><i class="fa fa-refresh"></i> fa-refresh</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/reply"><i class="fa fa-reply"></i> fa-reply</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/reply-all"><i class="fa fa-reply-all"></i> fa-reply-all</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/retweet"><i class="fa fa-retweet"></i> fa-retweet</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/road"><i class="fa fa-road"></i> fa-road</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/rocket"><i class="fa fa-rocket"></i> fa-rocket</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/rss"><i class="fa fa-rss"></i> fa-rss</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/rss-square"><i class="fa fa-rss-square"></i> fa-rss-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/search"><i class="fa fa-search"></i> fa-search</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/search-minus"><i class="fa fa-search-minus"></i> fa-search-minus</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/search-plus"><i class="fa fa-search-plus"></i> fa-search-plus</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/share"><i class="fa fa-share"></i> fa-share</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/share-square"><i class="fa fa-share-square"></i> fa-share-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/share-square-o"><i class="fa fa-share-square-o"></i> fa-share-square-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/shield"><i class="fa fa-shield"></i> fa-shield</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/shopping-cart"><i class="fa fa-shopping-cart"></i> fa-shopping-cart</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sign-in"><i class="fa fa-sign-in"></i> fa-sign-in</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sign-out"><i class="fa fa-sign-out"></i> fa-sign-out</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/signal"><i class="fa fa-signal"></i> fa-signal</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sitemap"><i class="fa fa-sitemap"></i> fa-sitemap</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/smile-o"><i class="fa fa-smile-o"></i> fa-smile-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort"><i class="fa fa-sort"></i> fa-sort</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort-alpha-asc"><i class="fa fa-sort-alpha-asc"></i> fa-sort-alpha-asc</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort-alpha-desc"><i class="fa fa-sort-alpha-desc"></i> fa-sort-alpha-desc</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort-amount-asc"><i class="fa fa-sort-amount-asc"></i> fa-sort-amount-asc</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort-amount-desc"><i class="fa fa-sort-amount-desc"></i> fa-sort-amount-desc</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort-asc"><i class="fa fa-sort-asc"></i> fa-sort-asc</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort-desc"><i class="fa fa-sort-desc"></i> fa-sort-desc</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort-asc"><i class="fa fa-sort-down"></i> fa-sort-down <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort-numeric-asc"><i class="fa fa-sort-numeric-asc"></i> fa-sort-numeric-asc</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort-numeric-desc"><i class="fa fa-sort-numeric-desc"></i> fa-sort-numeric-desc</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort-desc"><i class="fa fa-sort-up"></i> fa-sort-up <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/spinner"><i class="fa fa-spinner"></i> fa-spinner</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/square"><i class="fa fa-square"></i> fa-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/square-o"><i class="fa fa-square-o"></i> fa-square-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/star"><i class="fa fa-star"></i> fa-star</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/star-half"><i class="fa fa-star-half"></i> fa-star-half</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/star-half-o"><i class="fa fa-star-half-empty"></i> fa-star-half-empty <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/star-half-o"><i class="fa fa-star-half-full"></i> fa-star-half-full <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/star-half-o"><i class="fa fa-star-half-o"></i> fa-star-half-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/star-o"><i class="fa fa-star-o"></i> fa-star-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/subscript"><i class="fa fa-subscript"></i> fa-subscript</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/suitcase"><i class="fa fa-suitcase"></i> fa-suitcase</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sun-o"><i class="fa fa-sun-o"></i> fa-sun-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/superscript"><i class="fa fa-superscript"></i> fa-superscript</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/tablet"><i class="fa fa-tablet"></i> fa-tablet</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/tachometer"><i class="fa fa-tachometer"></i> fa-tachometer</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/tag"><i class="fa fa-tag"></i> fa-tag</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/tags"><i class="fa fa-tags"></i> fa-tags</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/tasks"><i class="fa fa-tasks"></i> fa-tasks</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/terminal"><i class="fa fa-terminal"></i> fa-terminal</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/thumb-tack"><i class="fa fa-thumb-tack"></i> fa-thumb-tack</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/thumbs-down"><i class="fa fa-thumbs-down"></i> fa-thumbs-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/thumbs-o-down"><i class="fa fa-thumbs-o-down"></i> fa-thumbs-o-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/thumbs-o-up"><i class="fa fa-thumbs-o-up"></i> fa-thumbs-o-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/thumbs-up"><i class="fa fa-thumbs-up"></i> fa-thumbs-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/ticket"><i class="fa fa-ticket"></i> fa-ticket</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/times"><i class="fa fa-times"></i> fa-times</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/times-circle"><i class="fa fa-times-circle"></i> fa-times-circle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/times-circle-o"><i class="fa fa-times-circle-o"></i> fa-times-circle-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/tint"><i class="fa fa-tint"></i> fa-tint</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-down"><i class="fa fa-toggle-down"></i> fa-toggle-down <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-left"><i class="fa fa-toggle-left"></i> fa-toggle-left <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-right"><i class="fa fa-toggle-right"></i> fa-toggle-right <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-up"><i class="fa fa-toggle-up"></i> fa-toggle-up <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/trash-o"><i class="fa fa-trash-o"></i> fa-trash-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/trophy"><i class="fa fa-trophy"></i> fa-trophy</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/truck"><i class="fa fa-truck"></i> fa-truck</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/umbrella"><i class="fa fa-umbrella"></i> fa-umbrella</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/unlock"><i class="fa fa-unlock"></i> fa-unlock</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/unlock-alt"><i class="fa fa-unlock-alt"></i> fa-unlock-alt</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/sort"><i class="fa fa-unsorted"></i> fa-unsorted <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/upload"><i class="fa fa-upload"></i> fa-upload</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/user"><i class="fa fa-user"></i> fa-user</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/users"><i class="fa fa-users"></i> fa-users</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/video-camera"><i class="fa fa-video-camera"></i> fa-video-camera</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/volume-down"><i class="fa fa-volume-down"></i> fa-volume-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/volume-off"><i class="fa fa-volume-off"></i> fa-volume-off</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/volume-up"><i class="fa fa-volume-up"></i> fa-volume-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/exclamation-triangle"><i class="fa fa-warning"></i> fa-warning <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/wheelchair"><i class="fa fa-wheelchair"></i> fa-wheelchair</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/wrench"><i class="fa fa-wrench"></i> fa-wrench</a></div>

			</div>

		</section>

		<section id="form-control">
			<h2 class="page-header">Form Control Icons</h2>

			<div class="row fontawesome-icon-list">



				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/check-square"><i class="fa fa-check-square"></i> fa-check-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/check-square-o"><i class="fa fa-check-square-o"></i> fa-check-square-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/circle"><i class="fa fa-circle"></i> fa-circle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/circle-o"><i class="fa fa-circle-o"></i> fa-circle-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/dot-circle-o"><i class="fa fa-dot-circle-o"></i> fa-dot-circle-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/minus-square"><i class="fa fa-minus-square"></i> fa-minus-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/minus-square-o"><i class="fa fa-minus-square-o"></i> fa-minus-square-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/plus-square"><i class="fa fa-plus-square"></i> fa-plus-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/plus-square-o"><i class="fa fa-plus-square-o"></i> fa-plus-square-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/square"><i class="fa fa-square"></i> fa-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/square-o"><i class="fa fa-square-o"></i> fa-square-o</a></div>

			</div>
		</section>

		<section id="currency">
			<h2 class="page-header">Currency Icons</h2>

			<div class="row fontawesome-icon-list">



				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/btc"><i class="fa fa-bitcoin"></i> fa-bitcoin <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/btc"><i class="fa fa-btc"></i> fa-btc</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/jpy"><i class="fa fa-cny"></i> fa-cny <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/usd"><i class="fa fa-dollar"></i> fa-dollar <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/eur"><i class="fa fa-eur"></i> fa-eur</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/eur"><i class="fa fa-euro"></i> fa-euro <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/gbp"><i class="fa fa-gbp"></i> fa-gbp</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/inr"><i class="fa fa-inr"></i> fa-inr</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/jpy"><i class="fa fa-jpy"></i> fa-jpy</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/krw"><i class="fa fa-krw"></i> fa-krw</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/money"><i class="fa fa-money"></i> fa-money</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/jpy"><i class="fa fa-rmb"></i> fa-rmb <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/rub"><i class="fa fa-rouble"></i> fa-rouble <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/rub"><i class="fa fa-rub"></i> fa-rub</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/rub"><i class="fa fa-ruble"></i> fa-ruble <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/inr"><i class="fa fa-rupee"></i> fa-rupee <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/try"><i class="fa fa-try"></i> fa-try</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/try"><i class="fa fa-turkish-lira"></i> fa-turkish-lira <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/usd"><i class="fa fa-usd"></i> fa-usd</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/krw"><i class="fa fa-won"></i> fa-won <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/jpy"><i class="fa fa-yen"></i> fa-yen <span class="text-muted">(alias)</span></a></div>

			</div>

		</section>

		<section id="text-editor">
			<h2 class="page-header">Text Editor Icons</h2>

			<div class="row fontawesome-icon-list">



				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/align-center"><i class="fa fa-align-center"></i> fa-align-center</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/align-justify"><i class="fa fa-align-justify"></i> fa-align-justify</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/align-left"><i class="fa fa-align-left"></i> fa-align-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/align-right"><i class="fa fa-align-right"></i> fa-align-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bold"><i class="fa fa-bold"></i> fa-bold</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/link"><i class="fa fa-chain"></i> fa-chain <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/chain-broken"><i class="fa fa-chain-broken"></i> fa-chain-broken</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/clipboard"><i class="fa fa-clipboard"></i> fa-clipboard</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/columns"><i class="fa fa-columns"></i> fa-columns</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/files-o"><i class="fa fa-copy"></i> fa-copy <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/scissors"><i class="fa fa-cut"></i> fa-cut <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/outdent"><i class="fa fa-dedent"></i> fa-dedent <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/eraser"><i class="fa fa-eraser"></i> fa-eraser</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/file"><i class="fa fa-file"></i> fa-file</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/file-o"><i class="fa fa-file-o"></i> fa-file-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/file-text"><i class="fa fa-file-text"></i> fa-file-text</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/file-text-o"><i class="fa fa-file-text-o"></i> fa-file-text-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/files-o"><i class="fa fa-files-o"></i> fa-files-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/floppy-o"><i class="fa fa-floppy-o"></i> fa-floppy-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/font"><i class="fa fa-font"></i> fa-font</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/indent"><i class="fa fa-indent"></i> fa-indent</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/italic"><i class="fa fa-italic"></i> fa-italic</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/link"><i class="fa fa-link"></i> fa-link</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/list"><i class="fa fa-list"></i> fa-list</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/list-alt"><i class="fa fa-list-alt"></i> fa-list-alt</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/list-ol"><i class="fa fa-list-ol"></i> fa-list-ol</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/list-ul"><i class="fa fa-list-ul"></i> fa-list-ul</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/outdent"><i class="fa fa-outdent"></i> fa-outdent</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/paperclip"><i class="fa fa-paperclip"></i> fa-paperclip</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/clipboard"><i class="fa fa-paste"></i> fa-paste <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/repeat"><i class="fa fa-repeat"></i> fa-repeat</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/undo"><i class="fa fa-rotate-left"></i> fa-rotate-left <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/repeat"><i class="fa fa-rotate-right"></i> fa-rotate-right <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/floppy-o"><i class="fa fa-save"></i> fa-save <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/scissors"><i class="fa fa-scissors"></i> fa-scissors</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/strikethrough"><i class="fa fa-strikethrough"></i> fa-strikethrough</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/table"><i class="fa fa-table"></i> fa-table</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/text-height"><i class="fa fa-text-height"></i> fa-text-height</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/text-width"><i class="fa fa-text-width"></i> fa-text-width</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/th"><i class="fa fa-th"></i> fa-th</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/th-large"><i class="fa fa-th-large"></i> fa-th-large</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/th-list"><i class="fa fa-th-list"></i> fa-th-list</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/underline"><i class="fa fa-underline"></i> fa-underline</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/undo"><i class="fa fa-undo"></i> fa-undo</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/chain-broken"><i class="fa fa-unlink"></i> fa-unlink <span class="text-muted">(alias)</span></a></div>

			</div>

		</section>

		<section id="directional">
			<h2 class="page-header">Directional Icons</h2>

			<div class="row fontawesome-icon-list">



				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/angle-double-down"><i class="fa fa-angle-double-down"></i> fa-angle-double-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/angle-double-left"><i class="fa fa-angle-double-left"></i> fa-angle-double-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/angle-double-right"><i class="fa fa-angle-double-right"></i> fa-angle-double-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/angle-double-up"><i class="fa fa-angle-double-up"></i> fa-angle-double-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/angle-down"><i class="fa fa-angle-down"></i> fa-angle-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/angle-left"><i class="fa fa-angle-left"></i> fa-angle-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/angle-right"><i class="fa fa-angle-right"></i> fa-angle-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/angle-up"><i class="fa fa-angle-up"></i> fa-angle-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-circle-down"><i class="fa fa-arrow-circle-down"></i> fa-arrow-circle-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-circle-left"><i class="fa fa-arrow-circle-left"></i> fa-arrow-circle-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-circle-o-down"><i class="fa fa-arrow-circle-o-down"></i> fa-arrow-circle-o-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-circle-o-left"><i class="fa fa-arrow-circle-o-left"></i> fa-arrow-circle-o-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-circle-o-right"><i class="fa fa-arrow-circle-o-right"></i> fa-arrow-circle-o-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-circle-o-up"><i class="fa fa-arrow-circle-o-up"></i> fa-arrow-circle-o-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-circle-right"><i class="fa fa-arrow-circle-right"></i> fa-arrow-circle-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-circle-up"><i class="fa fa-arrow-circle-up"></i> fa-arrow-circle-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-down"><i class="fa fa-arrow-down"></i> fa-arrow-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-left"><i class="fa fa-arrow-left"></i> fa-arrow-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-right"><i class="fa fa-arrow-right"></i> fa-arrow-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrow-up"><i class="fa fa-arrow-up"></i> fa-arrow-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrows"><i class="fa fa-arrows"></i> fa-arrows</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrows-alt"><i class="fa fa-arrows-alt"></i> fa-arrows-alt</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrows-h"><i class="fa fa-arrows-h"></i> fa-arrows-h</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrows-v"><i class="fa fa-arrows-v"></i> fa-arrows-v</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-down"><i class="fa fa-caret-down"></i> fa-caret-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-left"><i class="fa fa-caret-left"></i> fa-caret-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-right"><i class="fa fa-caret-right"></i> fa-caret-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-down"><i class="fa fa-caret-square-o-down"></i> fa-caret-square-o-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-left"><i class="fa fa-caret-square-o-left"></i> fa-caret-square-o-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-right"><i class="fa fa-caret-square-o-right"></i> fa-caret-square-o-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-up"><i class="fa fa-caret-square-o-up"></i> fa-caret-square-o-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-up"><i class="fa fa-caret-up"></i> fa-caret-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/chevron-circle-down"><i class="fa fa-chevron-circle-down"></i> fa-chevron-circle-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/chevron-circle-left"><i class="fa fa-chevron-circle-left"></i> fa-chevron-circle-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/chevron-circle-right"><i class="fa fa-chevron-circle-right"></i> fa-chevron-circle-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/chevron-circle-up"><i class="fa fa-chevron-circle-up"></i> fa-chevron-circle-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/chevron-down"><i class="fa fa-chevron-down"></i> fa-chevron-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/chevron-left"><i class="fa fa-chevron-left"></i> fa-chevron-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/chevron-right"><i class="fa fa-chevron-right"></i> fa-chevron-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/chevron-up"><i class="fa fa-chevron-up"></i> fa-chevron-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/hand-o-down"><i class="fa fa-hand-o-down"></i> fa-hand-o-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/hand-o-left"><i class="fa fa-hand-o-left"></i> fa-hand-o-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/hand-o-right"><i class="fa fa-hand-o-right"></i> fa-hand-o-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/hand-o-up"><i class="fa fa-hand-o-up"></i> fa-hand-o-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/long-arrow-down"><i class="fa fa-long-arrow-down"></i> fa-long-arrow-down</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/long-arrow-left"><i class="fa fa-long-arrow-left"></i> fa-long-arrow-left</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/long-arrow-right"><i class="fa fa-long-arrow-right"></i> fa-long-arrow-right</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/long-arrow-up"><i class="fa fa-long-arrow-up"></i> fa-long-arrow-up</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-down"><i class="fa fa-toggle-down"></i> fa-toggle-down <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-left"><i class="fa fa-toggle-left"></i> fa-toggle-left <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-right"><i class="fa fa-toggle-right"></i> fa-toggle-right <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/caret-square-o-up"><i class="fa fa-toggle-up"></i> fa-toggle-up <span class="text-muted">(alias)</span></a></div>

			</div>

		</section>

		<section id="video-player">
			<h2 class="page-header">Video Player Icons</h2>

			<div class="row fontawesome-icon-list">



				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/arrows-alt"><i class="fa fa-arrows-alt"></i> fa-arrows-alt</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/backward"><i class="fa fa-backward"></i> fa-backward</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/compress"><i class="fa fa-compress"></i> fa-compress</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/eject"><i class="fa fa-eject"></i> fa-eject</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/expand"><i class="fa fa-expand"></i> fa-expand</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/fast-backward"><i class="fa fa-fast-backward"></i> fa-fast-backward</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/fast-forward"><i class="fa fa-fast-forward"></i> fa-fast-forward</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/forward"><i class="fa fa-forward"></i> fa-forward</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/pause"><i class="fa fa-pause"></i> fa-pause</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/play"><i class="fa fa-play"></i> fa-play</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/play-circle"><i class="fa fa-play-circle"></i> fa-play-circle</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/play-circle-o"><i class="fa fa-play-circle-o"></i> fa-play-circle-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/step-backward"><i class="fa fa-step-backward"></i> fa-step-backward</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/step-forward"><i class="fa fa-step-forward"></i> fa-step-forward</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/stop"><i class="fa fa-stop"></i> fa-stop</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/youtube-play"><i class="fa fa-youtube-play"></i> fa-youtube-play</a></div>

			</div>

		</section>

		<section id="brand">
			<h2 class="page-header">Brand Icons</h2>

			<div class="alert alert-success">
				<ul class="margin-bottom-none padding-left-lg">
					<li>All brand icons are trademarks of their respective owners.</li>
					<li>The use of these trademarks does not indicate endorsement of the trademark holder by Font Awesome, nor vice versa.</li>
				</ul>

			</div>

			<div class="row fontawesome-icon-list">



				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/adn"><i class="fa fa-adn"></i> fa-adn</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/android"><i class="fa fa-android"></i> fa-android</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/apple"><i class="fa fa-apple"></i> fa-apple</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bitbucket"><i class="fa fa-bitbucket"></i> fa-bitbucket</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/bitbucket-square"><i class="fa fa-bitbucket-square"></i> fa-bitbucket-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/btc"><i class="fa fa-bitcoin"></i> fa-bitcoin <span class="text-muted">(alias)</span></a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/btc"><i class="fa fa-btc"></i> fa-btc</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/css3"><i class="fa fa-css3"></i> fa-css3</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/dribbble"><i class="fa fa-dribbble"></i> fa-dribbble</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/dropbox"><i class="fa fa-dropbox"></i> fa-dropbox</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/facebook"><i class="fa fa-facebook"></i> fa-facebook</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/facebook-square"><i class="fa fa-facebook-square"></i> fa-facebook-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/flickr"><i class="fa fa-flickr"></i> fa-flickr</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/foursquare"><i class="fa fa-foursquare"></i> fa-foursquare</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/github"><i class="fa fa-github"></i> fa-github</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/github-alt"><i class="fa fa-github-alt"></i> fa-github-alt</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/github-square"><i class="fa fa-github-square"></i> fa-github-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/gittip"><i class="fa fa-gittip"></i> fa-gittip</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/google-plus"><i class="fa fa-google-plus"></i> fa-google-plus</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/google-plus-square"><i class="fa fa-google-plus-square"></i> fa-google-plus-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/html5"><i class="fa fa-html5"></i> fa-html5</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/instagram"><i class="fa fa-instagram"></i> fa-instagram</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/linkedin"><i class="fa fa-linkedin"></i> fa-linkedin</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/linkedin-square"><i class="fa fa-linkedin-square"></i> fa-linkedin-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/linux"><i class="fa fa-linux"></i> fa-linux</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/maxcdn"><i class="fa fa-maxcdn"></i> fa-maxcdn</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/pagelines"><i class="fa fa-pagelines"></i> fa-pagelines</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/pinterest"><i class="fa fa-pinterest"></i> fa-pinterest</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/pinterest-square"><i class="fa fa-pinterest-square"></i> fa-pinterest-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/renren"><i class="fa fa-renren"></i> fa-renren</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/skype"><i class="fa fa-skype"></i> fa-skype</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/stack-exchange"><i class="fa fa-stack-exchange"></i> fa-stack-exchange</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/stack-overflow"><i class="fa fa-stack-overflow"></i> fa-stack-overflow</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/trello"><i class="fa fa-trello"></i> fa-trello</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/tumblr"><i class="fa fa-tumblr"></i> fa-tumblr</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/tumblr-square"><i class="fa fa-tumblr-square"></i> fa-tumblr-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/twitter"><i class="fa fa-twitter"></i> fa-twitter</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/twitter-square"><i class="fa fa-twitter-square"></i> fa-twitter-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/vimeo-square"><i class="fa fa-vimeo-square"></i> fa-vimeo-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/vk"><i class="fa fa-vk"></i> fa-vk</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/weibo"><i class="fa fa-weibo"></i> fa-weibo</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/windows"><i class="fa fa-windows"></i> fa-windows</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/xing"><i class="fa fa-xing"></i> fa-xing</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/xing-square"><i class="fa fa-xing-square"></i> fa-xing-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/youtube"><i class="fa fa-youtube"></i> fa-youtube</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/youtube-play"><i class="fa fa-youtube-play"></i> fa-youtube-play</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/youtube-square"><i class="fa fa-youtube-square"></i> fa-youtube-square</a></div>

			</div>
		</section>

		<section id="medical">
			<h2 class="page-header">Medical Icons</h2>

			<div class="row fontawesome-icon-list">

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/ambulance"><i class="fa fa-ambulance"></i> fa-ambulance</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/h-square"><i class="fa fa-h-square"></i> fa-h-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/hospital-o"><i class="fa fa-hospital-o"></i> fa-hospital-o</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/medkit"><i class="fa fa-medkit"></i> fa-medkit</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/plus-square"><i class="fa fa-plus-square"></i> fa-plus-square</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/stethoscope"><i class="fa fa-stethoscope"></i> fa-stethoscope</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/user-md"><i class="fa fa-user-md"></i> fa-user-md</a></div>

				<div class="fa-hover col-md-3 col-sm-4"><a href="../icon/wheelchair"><i class="fa fa-wheelchair"></i> fa-wheelchair</a></div>

			</div>

		</section>

		<?php
		break;
}

/* CHANGELOG -----------------------------------------------------------

  1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
  0.6.5		    : Gregorius Arief		: initial release

  ------------------------------------------------------------------------ */
?>
