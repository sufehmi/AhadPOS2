<?php
function combotgl($awal, $akhir, $var, $terpilih){
  echo "<select name=$var>";
  for ($i=$awal; $i<=$akhir; $i++){
	$p=strlen($i);
	if($p==1)
		$i="0".$i;
	else
		$i=$i;
	$t=strlen($terpilih);
	if($t==1)
		$terpilih="0".$terpilih;   
	else
		$terpilih=$terpilih;
	if ($i==$terpilih)
    {
      	
	    echo "<option value=$i selected>$i</option>";
	}
    else
      echo "<option value=$i>$i</option>";
  }
  echo "</select> ";
}

function combobln($awal, $akhir, $var, $terpilih){
  $nama_bln=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
                      "Juni", "Juli", "Agustus", "September", 
                      "Oktober", "November", "Desember");
  echo "<select name=$var>";
  for ($bln=$awal; $bln<=$akhir; $bln++){
	$bln2=$bln;
	$b=strlen($bln);
	if($b==1)
		$bln="0".$bln;
	else
		$bln=$bln;
	$b2=strlen($terpilih);
	if($b2==1)
		$terpilih="0".$terpilih;
	else
		$terpilih=$terpilih;

      if ($bln==$terpilih)
         echo "<option value=$bln selected>$nama_bln[$bln2]</option>";
      else
        echo "<option value=$bln >$nama_bln[$bln2]</option>";
  }
  echo "</select> ";
}

function agama($var,$terpilih){
	$nama_agama=array(1=> "Islam","Katolik","Kristen","Hindu","Budha");
	echo "<select name=$var>";
	for($i=1;$i<=5;$i++){
		if($nama_agama[$i]==$terpilih)
			echo "<option value=$terpilih selected>$terpilih</option>";
		else
			echo "<option value=$nama_agama[$i]>$nama_agama[$i]</option>";
	}
	echo "</select>";
}
?>