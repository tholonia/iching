
<?php

//require "../lib/functions.php";
include("templates/template.class.php");

$dsn = "mysql:host=localhost;port=3306;dbname=babelbrowser";
$user = "ichingDBuser";
$passwd = "1q2w3e";
$params = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
$dbh = new PDO($dsn, $user, $passwd, $params);

$type = 'pseq';
$ids = getids(array('bseq' => 'bseq', 'pseq' => 'pseq'));
$cols = getcols();



foreach ($ids as $id) {

    $fpseq = sprintf("%02s", $id['pseq']);
    $fbseq = sprintf("%02s", $id['bseq']);

    $hex = xmdgethex($fpseq, $fbseq, $id);




    /**
     * Creates a new template for the user's page.
     * Fills it with mockup data just for testing.
     */
    $page = new Template("templates/page.tpl");

    $page->set("id", htmlize(f($hex[0]['pseq'])));
    $page->set("trans", htmlize($hex[0]['trans']));
    $page->set("title", htmlize($hex[0]['title']));
    $page->set("pseq", htmlize(f($hex[0]['pseq'])));
    $page->set("bseq", htmlize(f($hex[0]['bseq'])));
    $page->set("binary", htmlize($hex[0]['binary']));
    $page->set("tri_upper", htmlize($hex[0]['tri_upper']));
    $page->set("tri_lower", htmlize($hex[0]['tri_lower']));
    $page->set("judge_old", htmlize($hex[0]['judge_old']));
    $page->set("judge_exp", htmlize($hex[0]['judge_exp']));
    $page->set("image_old", htmlize($hex[0]['image_old']));
    $page->set("image_exp", htmlize($hex[0]['image_exp']));
    $page->set("line_1", htmlize($hex[0]['line_1']));
    $page->set("line_1_org", htmlize($hex[0]['line_1_org']));
    $page->set("line_1_exp", htmlize($hex[0]['line_1_exp']));
    $page->set("line_2", htmlize($hex[0]['line_2']));
    $page->set("line_2_org", htmlize($hex[0]['line_2_org']));
    $page->set("line_2_exp", htmlize($hex[0]['line_2_exp']));
    $page->set("line_3", htmlize($hex[0]['line_3']));
    $page->set("line_3_org", htmlize($hex[0]['line_3_org']));
    $page->set("line_3_exp", htmlize($hex[0]['line_3_exp']));
    $page->set("line_4", htmlize($hex[0]['line_4']));
    $page->set("line_4_org", htmlize($hex[0]['line_4_org']));
    $page->set("line_4_exp", htmlize($hex[0]['line_4_exp']));
    $page->set("line_5", htmlize($hex[0]['line_5']));
    $page->set("line_5_org", htmlize($hex[0]['line_5_org']));
    $page->set("line_5_exp", htmlize($hex[0]['line_5_exp']));
    $page->set("line_6", htmlize($hex[0]['line_6']));
    $page->set("line_6_org", htmlize($hex[0]['line_6_org']));
    $page->set("line_6_exp", htmlize($hex[0]['line_6_exp']));
    $page->set("explanation_tho", htmlize($hex[0]['explanation_tho']));
    $page->set("tri_upper_tho", htmlize($hex[0]['tri_upper_tho']));
    $page->set("tri_lower_tho", htmlize($hex[0]['tri_lower_tho']));
    $page->set("tri_upper_tho_ex", htmlize($hex[0]['tri_upper_tho_ex']));
    $page->set("tri_lower_tho_ex", htmlize($hex[0]['tri_lower_tho_ex']));

    /**
     * Loads our layout template, settings its title and content.
     */
    $layout = new Template("templates/layout.tpl");
    $layout->set("content", $page->output());

    /**
     * Outputs the page with the user's page.
     */
    $fpage = $layout->output();
    $f = "ichingbook/hexagrams/" . $fpseq . "-" . $hex[0]['filename'] . ".md";
    echo "writing [${f}]\n";
    file_put_contents($f, $fpage);
}

function getids($ary) {
    $dsn = "mysql:host=localhost;port=3306;dbname=babelbrowser";
    $user = "ichingDBuser";
    $passwd = "1q2w3e";
    $params = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
    $dbh = new PDO($dsn, $user, $passwd, $params);

//$dbh = new PDO('mysql:host=localhost;dbname=iching;charset=utf8mb4', 'ichingDBuser', '1q2w3e');
    $sql = "SELECT " . $ary['bseq'] . "," . $ary['pseq'] . " from hexagrams order by " . $ary['pseq'] . " asc";
    $sth = $dbh->prepare($sql);
    $sth->execute();
    $ids = $sth->fetchAll();
    $c = array();
    foreach ($ids as $id) {
//        var_dump($id);exit;
//        array_push($c, $id[$type]);
    }
    return($ids);
}

function htmlize($s) {
    /*  see ->   https://www.functions-online.com/preg_replace.html */
    $r = preg_replace("/\r/", "", $s);
    $r = preg_replace("/(\n)/", '<br/>$1', $r);
    $r = preg_replace("/(\n\n)/", '$1<p></p>$1', $r);

    return($r);
}

function f($n) {
    return(sprintf("%02d", $n));
}

function getcols() {
    $dsn = "mysql:host=localhost;port=3306;dbname=babelbrowser";
    $user = "ichingDBuser";
    $passwd = "1q2w3e";
    $params = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
    $dbh = new PDO($dsn, $user, $passwd, $params);
//$dbh = new PDO('mysql:host=localhost;dbname=iching;charset=utf8mb4', 'ichingDBuser', '1q2w3e');
    $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'iching' AND TABLE_NAME = 'hexagrams'";
    $sth = $dbh->prepare($sql);
    $sth->execute();
    $cols = $sth->fetchAll();
    $c = array();
    foreach ($cols as $col) {
        array_push($c, $col['COLUMN_NAME']);
    }
    return($c);
}

function xmdgethex($pseq, $bseq, $id) {
    //  var_dump($bseq);
//    $dbh = new PDO('mysql:host=localhost;dbname=iching;charset=utf8mb4', 'Spartacus', 'holo3601q2w3e');

    $binary = sprintf("%06s", decbin($bseq));


    $sql = <<<EOX
    SELECT 
        `fix`
        ,`comment`
        ,`filename`
        ,pseq
        ,bseq
        ,`binary`
        ,title
        ,trans
        ,trigrams
            
       ,( 
            SELECT distinct concat(
                xref_trigrams.pseq,' (',xref_trigrams.bseq,') ',xref_trigrams.title,' / ',xref_trigrams.trans,' / ',xref_trigrams.t_element) 
            FROM
                hexagrams
            Inner Join xref_trigrams ON hexagrams.tri_upper_bin = xref_trigrams.bseq 
            WHERE hexagrams.pseq = ${pseq} limit 1
        ) as tri_upper
            
        ,(
            SELECT distinct concat(
                xref_trigrams.pseq,' (',xref_trigrams.bseq,') ',xref_trigrams.title,' / ',xref_trigrams.trans,' / ',xref_trigrams.t_element)   
            FROM
                hexagrams
            Inner Join xref_trigrams ON hexagrams.tri_lower_bin = xref_trigrams.bseq 
            WHERE hexagrams.pseq = ${pseq} limit 1
         ) as tri_lower
            
         
           ,(SELECT distinct concat(
                '(',tholonic_tri.bseq,') ' ,tholonic_tri.name,' / ',tholonic_tri.short,' ( ',tholonic_tri.tags ,')'
                ) FROM
                hexagrams
                Inner Join tholonic_tri ON hexagrams.tri_upper_bin = tholonic_tri.bseq 
                WHERE hexagrams.bseq = ${bseq} limit 1
                ) as tri_upper_tho

            ,(SELECT distinct concat(
                '(',tholonic_tri.bseq,') ' ,tholonic_tri.name,' / ',tholonic_tri.short,' ( ',tholonic_tri.tags ,')'
                ) FROM
                hexagrams
                Inner Join tholonic_tri ON hexagrams.tri_lower_bin = tholonic_tri.bseq                 
                WHERE hexagrams.bseq = ${bseq} limit 1
             ) as tri_lower_tho        
                 
                
           ,(SELECT distinct 
                tholonic_tri.explanation
                FROM
                hexagrams
                Inner Join tholonic_tri ON hexagrams.tri_upper_bin = tholonic_tri.bseq                 
                WHERE hexagrams.bseq = ${bseq} limit 1
             ) as tri_upper_tho_ex                

            ,(SELECT distinct 
                tholonic_tri.explanation
                FROM
                hexagrams
                Inner Join tholonic_tri ON hexagrams.tri_lower_bin = tholonic_tri.bseq                 
                WHERE hexagrams.bseq = ${bseq} limit 1
             ) as tri_lower_tho_ex                


            ,(SELECT distinct 
                tholonic_hex.explanation   
                FROM
                hexagrams
                Inner Join tholonic_hex ON hexagrams.bseq = tholonic_hex.bseq 
                WHERE hexagrams.bseq = ${bseq} limit 1
             ) as explanation_tho                 
                            
            
        ,explanation
        ,judge_old
        ,judge_exp
        ,image_old
        ,image_exp
        ,line_1
        ,line_1_org
        ,line_1_exp
        ,line_2
        ,line_2_org
        ,line_2_exp
        ,line_3
        ,line_3_org
        ,line_3_exp
        ,line_4
        ,line_4_org
        ,line_4_exp
        ,line_5
        ,line_5_org
        ,line_5_exp
        ,line_6
        ,line_6_org
        ,line_6_exp

FROM hexagrams
    WHERE hexagrams.bseq =    ${bseq}
EOX;
    $dsn = "mysql:host=localhost;port=3306;dbname=babelbrowser";
    $user = "ichingDBuser";
    $passwd = "1q2w3e";
    $params = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
    $dbh = new PDO($dsn, $user, $passwd, $params);

//    $sql = $sql . "${pseq}";
    $sth = $dbh->prepare($sql);
    $sth->execute();
    $hex = $sth->fetchAll(PDO::FETCH_ASSOC);
    return($hex);
}