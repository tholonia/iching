<?php

require "vendor/autoload.php";

use PHPHtmlParser\Dom;

$sels = array(
    'title' => 'body > div.intro > div.title',
    'trigrams' => 'body > div.intro > div.trigrams',
    'explanation' => 'body > div.intro > div.explanation',
    'judge_old' => 'body > div.judgement > div.ancient',
    'judge_exp' => 'body > div.judgement > div.explanation',
    'image_old' => 'body > div.image > div.ancient',
    'image_exp' => 'body > div.image > div.explanation',
    'line_1' => '#L1',
//    'line_1_org' => '#1 > p:nth-child(1)',
//    'line_1_org' => '#L1 > p',
//    'line_1_exp' => '#L1:nth-child(2)',
    'line_2' => '#L2',
//    'line_2_org' => '#L2 > p',
//    'line_2_exp' => '#L2 > p:nth-child(2)',
    'line_3' => '#L3',
//    'line_3_org' => '#L3 > p',
//    'line_3_exp' => '#L3 > p:nth-child(2)',
    'line_4' => '#L4',
//    'line_4_org' => '#L4 > p',
//    'line_4_exp' => '#L4 > p:nth-child(2)',
    'line_5' => '#L5',
//    'line_5_org' => '#L5 > p',
//    'line_5_exp' => '#L5 > p:nth-child(2)',
    'line_6' => '#L6',
//    'line_6_org' => '#L6 > p',
//    'line_6_exp' => '#L6 > p:nth-child(2)',
);
// Create connection

$files = glob('ik/ik*.txt');
$i = 1;


foreach ($files as $file) {
    $dbh = new PDO('mysql:host=localhost;dbname=iching;charset=utf8mb4', 'root', '1q2w3e');
    $sql = "INSERT into hexagrams (id) values (:i)";
    $sth = $dbh->prepare($sql);
    $sth->execute(array(":i" => $i));
    echo "\n----------------------------\n".$i . "[$file] \n------------------------------\n";
    foreach ($sels as $name => $sel) {
        echo " -> ${name}  ";
        $val = rd($file, $name, $sel);
//        if ($name == "line_1_org") {
//            echo "                                            $val\n";
//        }
//        if ($name == "line_1_exp") {
//            echo "                                            $val\n";
//        }
        if ($name == 'title') {
            $parts = preg_split("/[\.\/]/", $val);
//var_dump($parts);exit;            
            $vars = array('name' => "num", 'val' => $parts[0],'i'=>$i);
            sql($vars);
            $vars = array('name' => "title", 'val' => $parts[1],'i'=>$i);
            sql($vars);
            $vars = array('name' => "trans", 'val' => $parts[2],'i'=>$i);
            sql($vars);
        } else {
            $vars = array('name' => $name, 'val' => $val, 'i' => $i);
            sql($vars);
        }
    }
    $i++;
}

function clean($str) {
    $s = trim($str);
    return($s);
}

function rd($file, $str, $sel) {
    $dom = new Dom;
    $dom->load(file_get_contents($file));
    
//    if ($sel == "#xxxx") {
//        $cs = $dom->find($sel);
//        var_dump($cs->innerHtml());
//        var_dump($cs->innerHtml());
//        return(clean($c1));
//    }
//    if ($sel == "#1:nth-child(2)") {
//        $c = $dom->find($sel);
//        $c1 = $c->nextSibling();
//        return(clean($c1->text));
//    }
    $c = $dom->find($sel);
    return(clean($c->text));
}

function sql($vars) {
    $dbh = new PDO('mysql:host=localhost;dbname=iching;charset=utf8mb4', 'root', '1q2w3e');
    $sql = "UPDATE hexagrams set " . $vars['name'] . " = :val WHERE ID = " . $vars['i'];
    $sth = $dbh->prepare($sql);
    $sth->execute(array(":val" => $vars['val']));
}
