<?php
/* argon-cetak-barcode.php ----------------------------------------
   	version: 1.01

	Part of AhadPOS : http://ahadpos.com
	License: GPL v2
			http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
			http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License v2 (links provided above) for more details.
----------------------------------------------------------------*/

include "../../config/config.php";


if($_GET[file]==""){
	echo "nama file belum didefinisikan";
}elseif($_GET[kode]==""){
	echo "field kode belum didefinisikan";
}elseif($_GET[nama]==""){
	echo "field nama belum didefinisikan";
}elseif($_GET[harga]==""){
	echo "field harga belum didefinisikan/diisi";
}elseif($_GET[jml]==""){
	echo "field jumlah belum didefinisikan/diisi";
}elseif(strlen($_GET[nama])>30) {
echo "field nama \"$_GET[nama]\" lebih dari 30 karakter";
}elseif(strlen($_GET[jml])!=4) {
echo "field jumlah \"$_GET[jml]\" harus 4 karakter";


}else{

//$handle = fopen("tmp\\output.prn", "w");
$handle = fopen("/dev/lp0", "w");

fwrite($handle, "n\nM0500\nO0220\nV0\nSE\nD\nL\nD11\nPE\nA2\n1e2101900210003C");
fwrite($handle,$_GET[kode]."\n1911A0600090002".$_GET[kode]."\n1911A0600490004".$_GET[nama]."\n1911A1000240079Rp ".$_GET[harga]."\n1e4202500210184C");
fwrite($handle,$_GET[kode]."\n1911A0800060196".$_GET[kode]."\n1911A0600650185".$_GET[nama]."\n1911A1200460185Rp ".$_GET[harga]."\n1911A1000640004Ahad Mart"."\nQ".$_GET[jml]."\nE");
fclose($handle);
echo "Silahkan jalankan file <font color=red><b>pb.bat</b></font> di Command Prompt";
}
//".$_GET[file].". 


/* CHANGELOG -----------------------------------------------------------

 1.0.1	: Harry Sufehmi		: initial release

------------------------------------------------------------------------ */
?> 
