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

$result = mysql_query('select `option`, value, description from config') or die(mysql_error());
$config = array();

while ($configItem = mysql_fetch_array($result)) {
    $config[$configItem['option']] = array(
        'value' => $configItem['value'],
        'description' => $configItem['description']
    );
}
?>
<style>
    input[type='text']{
        font-family: "Courier New", Courier, monospace;
        font-size: 1.2em;
    }
</style>
<h2>Membership Configuration</h2>
<form method="POST" action="./aksi.php?module=membership&act=simpan">
    <table>
        <tbody>
            <tr>
                <td><?php echo $config['point_value']['description']; ?></td>
                <td> : <input type="text" name="config[point_value]" value="<?php echo $config['point_value']['value']; ?>"></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" align="right">
                    <input type="submit" value="Simpan">
                </td>
            </tr>
        </tbody>
    </table>
</form>

