<?php

/* fast-SO-mobile.php ------------------------------------------------------
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

	session_start();
//	include "../../config/config.php";

//$username = $_SESSION['uname'];
// $username = 'so';



?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Mobile SO - Ahad Mart</title>
	 <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	 <meta name="apple-mobile-web-app-capable" content="yes" />
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <script src="http://code.jquery.com/jquery-1.9.0.min.js" ></script>
    <script type="text/javascript" >

    </script>
</head>

<body>

<div class="navbar navbar-fixed-top">
    		<div class="navbar-inner">
				<div class="container">
					<a class="brand">Mobile SO</img></a>
				</div>
    		</div>
    </div>


<?php
  header( "refresh:2;url=fast-SO-mobile.php?nomorrak=".$_SESSION["nomorraks"]."&submit=Submit&username=" );

  echo '<div class="container"><div class="well" align="center"><b><i><h2> Terimakasih, jumlah stok sudah dicatat di komputer. </h2></i></b></div></div>';
?>



</body>
</html>
