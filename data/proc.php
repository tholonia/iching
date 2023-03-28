<?php

$f = file_get_contents("x");
$parts = explode("\n", $f);

$t =array(
    "num",
    "title",
    "hebchar",
    "assiah",
    "tarot",
    "des_name",
    "des_pseq",
    "des_bseq",
    "asc_name",
    "asc_pseq",
    "asc_bseq",
);

foreach ($parts as $p) {
    $items = explode(",", $p);
//    print_r($items);
    for ($i=0; $i<11; $i++) {
//        print_r($items[$i]);
        $str = "update iqab set ".$t[$i] . " = '". trim($items[$i])."' where path = ".trim($items[0]).";\n";
        print $str;
    }
    print "-- --------------------------------------\n";
}
