<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

DEFINE('ROOT',dirname(__FILE__)."/");

echo "<pre>";

print_r($_POST);

$css = $_POST['css'];
$out = $_POST['out'];

$l=''; $lz = "\n/* ". date('ymd.hi') ." */";

foreach ($css as $c) {
	$s = file_get_contents(ROOT.$c);
	$sta = "[NA]"; if (file_exists(ROOT.$c)) { $sta = "[OK]"; }
	$l .= compress_css($s);
	echo "\n+ $sta ".ROOT."$c";
}

file_put_contents(ROOT.$out,$l.$lz);

echo "\n= ".ROOT."$out";
echo "</pre>";

exit; 

function compress_css($css) {
	$css = preg_replace('#\/\*(.+?)\*\/#','',$css);	
	$css = preg_replace('#^\s+#','',$css);	
	$css = preg_replace('#\s+$#','',$css);	
	$css = preg_replace("/\n|\r+/","",$css);
	$css = preg_replace("/\s+/"," ",$css);
	$css = preg_replace("/(\s+|)(\:|\;|\}|\{|\,|\(|\)|\>)(\s+|\s)/","\\2",$css);
	$css = preg_replace("/\;\}/","}",$css);
	$css = preg_replace("/\, \./",",.",$css);
	$css = preg_replace("/and\s+\(/","and(",$css);
	$css = preg_replace("/media\s+\(/","media(",$css);	
	$css = preg_replace("/  /"," ",$css);
	return $css;
}