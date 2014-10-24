<?php
check_user_access(basename($_SERVER['SCRIPT_NAME']));
session_start();

$act = $_GET['act'];

switch ($act) {
    case 'setting':
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
        <h2>Setting Configuration</h2>
        <form method="POST" action="./aksi.php?module=system&act=setting-simpan">
            <table>
                <tbody>
                    <tr>
                        <td><?php echo $config['store_name']['description']; ?></td>
                        <td> : <input type="text" name="config[store_name]" size="40" value="<?php echo $config['store_name']['value']; ?>"></td>
                    </tr>
                    <tr>
                        <td><?php echo $config['receipt_header1']['description']; ?></td>
                        <td> : <input type="text" name="config[receipt_header1]" size="40" value="<?php echo $config['receipt_header1']['value']; ?>"></td>
                    </tr>
                    <tr>
                        <td><?php echo $config['receipt_footer1']['description']; ?></td>
                        <td> : <input type="text" name="config[receipt_footer1]" size="40" value="<?php echo $config['receipt_footer1']['value']; ?>"></td>
                    </tr>
                    <tr>
                        <td><?php echo $config['receipt_footer2']['description']; ?></td>
                        <td> : <input type="text" name="config[receipt_footer2]" size="40" value="<?php echo $config['receipt_footer2']['value']; ?>"></td>
                    </tr>
                    <tr>
                        <td><?php echo $config['temporary_space']['description']; ?></td>
                        <td> : <input type="text" name="temporary_space" size="40" value="<?php echo $config['temporary_space']['value']; ?>" disabled="disabled"></td>
                    </tr>
                    <tr>
                        <td><?php echo $config['version']['description']; ?></td>
                        <td> : <input type="text" name="version" size="40" value="<?php
                            $first = true;
                            foreach (unserialize($config['version']['value']) as $ver) {
                                echo $first ? '' : '.';
                                echo $ver;
                                $first = false;
                            }
                            ?>" disabled="disabled">
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $config['ukm_mode']['description']; ?></td>
                        <td> :
                            <select name="config[ukm_mode]" >
                                <?php $ukmMode = $config['ukm_mode']['value']; ?>
                                <option value="0" <?php echo $ukmMode ? '' : 'selected'; ?>>OFF</option>
                                <option value="1" <?php echo $ukmMode ? 'selected' : ''; ?>>ON</option>
                            </select>
                        </td>
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
        <?php
        break;

    case 'maintenance':
        ?>
        <h2>System Maintenance</h2>
        <select>
            <!--<option>Pilih satu..</option>-->
            <option>Barang</option>
        </select>
        <a id="cari-barang" href="#"><button>Cari</button></a>
        <div id="tabel-hasil">

        </div>
        <script>
            $("#cari-barang").click(function () {
                ambilHasil();
            });

            function ambilHasil() {
                $("#tabel-hasil").load('aksi.php?module=system&act=maintenance-barang');
            }
        </script>
        <?php
        break;

    default:
        ?>
        <p>
            Pilih Sub Menu <i class="fa fa-arrow-circle-up"></i>
        </p>

        <?php
        break;
}
//eof




