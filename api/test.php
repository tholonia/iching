<?php
require "../lib/functions.php";

$url = "http://slider.com/api/func.php?";



$args = array(
    'func' =>"getHexnumOppositeByPseq",
    'pseq' => 11
    );
$arglist = http_build_query($args, 'myvar_');

echo "<pre>";
$testurl = $url.$arglist;
//echo "[$testurl]\n";
        
$res = file_get_contents($testurl);
echo "$res";
//echo "\n\n";

