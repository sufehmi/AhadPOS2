<?php
/* mod_customer.php ------------------------------------------------------
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


switch ($_GET[act]) {
    // Tampil customer -> menampilkan semua daftar customer tanpa paging
    default:
        ?>
        <h2>Data Customer</h2>
        <form method=POST action='?module=customer&act=tambahcustomer'>
            <input type=submit value='Tambah Customer'></form>
        <br/>
        <table class="tabel">
            <tr>
                <th>No</th>
                <th>Nomor</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No.Telp</th>
                <th>No.HP</th>
                <th>No.KTP</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
                <th>E-mail</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th colspan="2">Diskon</th>
                <th>aksi</th>
            </tr>
            <?php
            $tampil = mysql_query("select idCustomer, nomor_kartu, namaCustomer, alamatCustomer, telpCustomer, diskon_persen, diskon_rupiah, keterangan, "
                    . "nomor_ktp, jenis_kelamin, tanggal_lahir, handphone, email, member "
                    . "from customer");
            $no = 1;
            while ($r = mysql_fetch_array($tampil)) {
                ?>
                <tr <?php echo $no % 2 === 0 ? 'class="alt"' : ''; ?>>
                    <td class="right"><?php echo $no; ?></td>
                    <td><?php echo $r['nomor_kartu']; ?></td>
                    <td><?php echo $r['namaCustomer']; ?></td>
                    <td><?php echo $r['alamatCustomer']; ?></td>
                    <td><?php echo $r['telpCustomer']; ?></td>
                    <td><?php echo $r['handphone']; ?></td>
                    <td><?php echo $r['nomor_ktp']; ?></td>
                    <td><?php echo $r['jenis_kelamin'] == 0 ? 'Laki-laki' : 'Perempuan'; ?></td>
                    <td><?php echo date_format(date_create_from_format('Y-m-d', $r['tanggal_lahir']), 'd-m-Y'); ?></td>
                    <td><?php echo $r['email']; ?></td>
                    <td><?php echo $r['member'] == 0 ? 'Non member' : 'Member'; ?></td>
                    <td><?php echo $r['keterangan']; ?></td>
                    <td class="right"><?php echo $r['diskon_persen']; ?>%</td>
                    <td class="right"><?php echo number_format($r['diskon_rupiah'], 2, ',', '.'); ?></td>
                    <td><a href=?module=customer&act=editcustomer&id=<?php echo $r['idCustomer']; ?>>Edit</a> |
                        <a href=./aksi.php?module=customer&act=hapus&id=<?php echo $r['idCustomer']; ?>>Hapus</a>
                    </td>
                </tr>
                <?php
                $no++;
            }
            ?>
        </table>
        <p>&nbsp;</p>
        <a href=javascript:history.go(-1)><< Kembali</a>
        <?php
        break;

    case "tambahcustomer":
        ?>
        <h2>Tambah Customer</h2>
        <form method=POST action='./aksi.php?module=customer&act=input' name='tambahcustomer'>
            <table style="border-collapse: collapse">
                <tr>
                    <td>Nomor Kartu Member</td>
                    <td> : <input type="text" name="nomor_kartu" size=40 /></td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td> : <input type=text name='namaCustomer' size=40 autofocus="autofocus"></td>
                </tr>
                <tr>
                    <td>Nomor KTP</td>
                    <td> : <input type="text" name="nomor_ktp" size=40/></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td> : <textarea name='alamatCustomer' rows='2' cols='35'></textarea></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td> : <select name="jenis_kelamin">
                            <option value="0">Laki-laki</option>
                            <option value="1">Perempuan</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td> : <input type="text" name="tanggal_lahir" placeholder="dd-mm-yyyy"/></td>
                </tr>
                <tr>
                    <td>Telp</td>
                    <td> : <input type=text name='telpCustomer' size=15 /></td>
                </tr>
                <tr>
                    <td>HP</td>
                    <td> : <input type=text name='handphone' size=15 /></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td> : <input type=text name='email' size=40 /></td>
                </tr>
                <tr>
                    <td>Member</td>
                    <td> : <select name="member">
                            <option value="0">Non Member</option>
                            <option value="1" selected="selected">Member</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Keterangan</td>
                    <td> : <textarea name='keterangan' rows='4' cols='35'></textarea></td>
                </tr>
                <tr>
                    <td colspan=2>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan=2 align='right'>
                        <input type=submit value=Simpan>&nbsp;&nbsp;&nbsp;
                        <input type=button value=Batal onclick=self.history.back()>
                    </td>
                </tr>
            </table>
        </form>
        <?php
        break;

    case "editcustomer":
        $edit = mysql_query("SELECT * FROM customer WHERE idCustomer='$_GET[id]'");
        $data = mysql_fetch_array($edit);
        ?>
        <h2>Edit Customer</h2>
        <form method=POST action=./aksi.php?module=customer&act=update name='editcustomer'>
            <input type=hidden name='idCustomer' value='<?php echo $data['idCustomer']; ?>'>
            <table>
                <tr>
                    <td>Nomor Kartu Member</td>
                    <td> : <input type="text" name="nomor_kartu" size=40 value="<?php echo $data['nomor_kartu']; ?>"/></td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td> : <input type=text name='namaCustomer' size=40 value='<?php echo $data['namaCustomer']; ?>'></td>
                </tr>
                <tr>
                    <td>Nomor KTP</td>
                    <td> : <input type="text" name="nomor_ktp" size=40 value="<?php echo $data['nomor_ktp']; ?>"/></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td> : <textarea name='alamatCustomer' rows='2' cols='35'><?php echo $data['alamatCustomer']; ?></textarea></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td> : <select name="jenis_kelamin">
                            <option value="0" <?php echo $data['jenis_kelamin'] == 0 ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="1" <?php echo $data['jenis_kelamin'] == 1 ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td> : <input type="text" name="tanggal_lahir" placeholder="dd-mm-yyyy" value="<?php echo date_format(date_create_from_format('Y-m-d', $data['tanggal_lahir']), 'd-m-Y')?>"/></td>
                </tr>
                <tr>
                    <td>Telp</td>
                    <td> : <input type=text name='telpCustomer' size=15 value='<?php echo $data['telpCustomer']; ?>'></td>
                </tr>
                <tr>
                    <td>HP</td>
                    <td> : <input type=text name='handphone' size=15 value="<?php echo $data['handphone']; ?>"/></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td> : <input type=text name='email' size=40 value="<?php echo $data['email']; ?>"/></td>
                </tr>
                <tr>
                    <td>Member</td>
                    <td> : <select name="member">
                            <option value="0" <?php echo $data['member'] == 0 ? 'selected' : '' ?>>Non Member</option>
                            <option value="1" <?php echo $data['member'] == 0 ? 'selected' : '' ?>>Member</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Keterangan</td>
                    <td> : <textarea name='keterangan' rows='4' cols='35'><?php echo $data['keterangan']; ?></textarea></td>
                </tr>
                <tr>
                    <td>Diskon (%)</td>
                    <td> : <input type='text' name='diskon_persen' value='<?php echo $data['diskon_persen']; ?>' /></td>
                </tr>
                <tr>
                    <td>Diskon (Rp)</td>
                    <td> : <input type='text' name='diskon_rupiah' value='<?php echo $data['diskon_rupiah']; ?>' /></td>
                </tr>
                <tr>
                    <td colspan=2>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan=2 align='right'><input type=submit value=Simpan>&nbsp;&nbsp;&nbsp;
                        <input type=button value=Batal onclick=self.history.back()></td>
                </tr>
            </table></form>
        <?php
        break;
}


/* CHANGELOG -----------------------------------------------------------

  1.0.1 / 2010-06-03 : Harry Sufehmi		: various enhancements, bugfixes
  0.6.5		    : Gregorius Arief		: initial release

  ------------------------------------------------------------------------ */
?>
