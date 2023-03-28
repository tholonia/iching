<?php

function getids() {
    $dbh = new PDO('mysql:host=localhost;dbname=babelbrowser;charset=utf8mb4', 'ichingDBuser', '1q2w3e');
    $sql = "SELECT * from hexagrams";
    $sth = $dbh->prepare($sql);
    $sth->execute();
    $ids = $sth->fetchAll();
    return($ids);
}


var_dump(getids())

?>
