<?php

$nums = getN();


foreach ($nums as $num) {

    $newbin = decbin($num['bseq']);
    //echo "pseq:".sprintf("%02d",$num['pseq']).":  bseq:".sprintf("%02d",$num['bseq'])."    was   ".$num['binary']."   new   ".sprintf("%06d",$newbin)."\n";
    $sql = "update hexagrams set `binary` = '".sprintf("%06d",$newbin)."' where bseq = ".$num['bseq'].";\n";
    echo $sql;
    
}









function getN() {
    $dbh = new PDO('mysql:host=localhost;dbname=iching;charset=utf8mb4', 'root', '1q2w3e');
    $sql = "SELECT hexagrams.pseq, hexagrams.bseq, hexagrams.`binary` FROM hexagrams";
    $sth = $dbh->prepare($sql);
    $sth->execute();
    return $sth->fetchAll();
}
function checkBin($sql) {
    $dbh = new PDO('mysql:host=localhost;dbname=iching;charset=utf8mb4', 'root', '1q2w3e');
    $sth = $dbh->prepare($sql);
    $sth->execute();
}