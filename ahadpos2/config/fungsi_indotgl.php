<?php
/* fungsi_indotgl.php ------------------------------------------------------
   	version: 1.01

	Part of AhadPOS : http://rimbalinux.com/projects/ahadpos/
	License: GPL v2
			http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
			http://vlsm.org/etc/gpl-unofficial.id.html

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License v2 (links provided above) for more details.
----------------------------------------------------------------*/

	function tgl_indo($tgl){
			$tanggal = substr($tgl,8,2);
			$bulan = getBulan2(substr($tgl,5,2));
			$tahun = substr($tgl,0,4);
			return $tanggal.' '.$bulan.' '.$tahun;		 
	}

        function getTahun($tgl){
            $tahun = substr($tgl,0,4);

            return $tahun;
        }

	function getBulan($bln){
				switch ($bln){
					case 1: 
						return "Januari";
						break;
					case 2:
						return "Februari";
						break;
					case 3:
						return "Maret";
						break;
					case 4:
						return "April";
						break;
					case 5:
						return "Mei";
						break;
					case 6:
						return "Juni";
						break;
					case 7:
						return "Juli";
						break;
					case 8:
						return "Agustus";
						break;
					case 9:
						return "September";
						break;
					case 10:
						return "Oktober";
						break;
					case 11:
						return "November";
						break;
					case 12:
						return "Desember";
						break;
				}
			}

       function getBulan2($bln){
				switch ($bln){
					case 1:
						return "Jan";
						break;
					case 2:
						return "Feb";
						break;
					case 3:
						return "Mar";
						break;
					case 4:
						return "Apr";
						break;
					case 5:
						return "Mei";
						break;
					case 6:
						return "Juni";
						break;
					case 7:
						return "Juli";
						break;
					case 8:
						return "Agus";
						break;
					case 9:
						return "Sept";
						break;
					case 10:
						return "Okt";
						break;
					case 11:
						return "Nov";
						break;
					case 12:
						return "Des";
						break;
				}
			}
     function getMonth($table="", $date_column=""){
		
	if ($table=="") {
		$table = "transaksibeli";	$date_column = "tglTransaksiBeli";
	};
         $query = mysql_query("select distinct(month($date_column)) as bulan from $table 
                group by bulan
                order by bulan desc") or die(mysql_error());
         return $query;
     }

     function getYear(){
         $query = mysql_query("select distinct(year(tglTransaksiBeli)) as tahun from transaksibeli
                group by tahun
                order by tahun desc") or die(mysql_error());
         return $query;
     }

     function getBulanku($bln){
        switch ($bln){
            case 1:
                return "Jan";
		break;
            case 2:
		return "Feb";
		break;
            case 3:
		return "Mar";
		break;
            case 4:
		return "Apr";
		break;
            case 5:
		return "Mei";
		break;
            case 6:
		return "Juni";
		break;
            case 7:
		return "Juli";
		break;
            case 8:
		return "Agus";
		break;
            case 9:
		return "Sept";
		break;
            case 10:
		return "Okt";
		break;
            case 11:
		return "Nov";
		break;
            case 12:
		return "Des";
                break;
        }
    }


/* CHANGELOG -----------------------------------------------------------

 1.0.1 / 2010-07-11 : Harry Sufehmi		: various enhancements, bugfixes
 0.9.1		    : Gregorius Arief		: initial release

------------------------------------------------------------------------ */
?>

