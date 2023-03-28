<?php

class DataMapper {

    private $pdo;
    public $o;

    public function __construct($ini) {
        $name = $ini['db.name'];
        $server = $ini['db.server'];
        $user = $ini['db.user'];
        $pass = $ini['db.pass'];

        $dsn = "mysql:host=${server};dbname=${name};charset=utf8mb4";

        try {
        #            echo $dsn,$user,$pass;
            $dbh = new PDO($dsn, $user, $pass);
            $this->pdo = $dbh;
            $this->o = $dbh;
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (Exception $e) {
            echo 'Exception -> ';
            dbug($e->xdebug_message);
            die();
        }
    }

    public function ex($q, $qargs = null, $fargs = array()) {
        $res = null;
        $stmt = null;
//        var_dump($q);
        try {
            $stmt = $this->pdo->prepare($q);
        } catch (Exception $e) {
            echo 'Exception -> ';
            dbug($e->xdebug_message);
        }

        if (!($res = $stmt->execute($qargs))) {
            print_r($stmt->errorInfo());
            die;
        }
        return($res);
    }

    public function getData($param) {
        $bseq = bindec($param);
        $query = <<<EOX
        SELECT 
            fix
            ,proofed
            ,`comment`
                        ,filename
            ,pseq
            ,bseq
            ,`binary`
            ,title
            ,trans
            ,trigrams
                   ,(SELECT distinct concat(
                xref_trigrams.pseq,' (',xref_trigrams.bseq,') ',xref_trigrams.title,' / ',xref_trigrams.trans,' / ',xref_trigrams.t_element
                )   FROM
                hexagrams
                Inner Join xref_trigrams ON hexagrams.tri_upper_bin = xref_trigrams.bseq 
                WHERE hexagrams.bseq = ? limit 1
                ) as tri_upper
            ,(SELECT distinct concat(
                xref_trigrams.pseq,' (',xref_trigrams.bseq,') ',xref_trigrams.title,' / ',xref_trigrams.trans,' / ',xref_trigrams.t_element
                )   FROM
                hexagrams
                Inner Join xref_trigrams ON hexagrams.tri_lower_bin = xref_trigrams.bseq 
                WHERE hexagrams.bseq = ? limit 1
             ) as tri_lower
                
                
                
         
           ,(SELECT distinct concat(
                '(',tholonic_tri.bseq,') ' ,tholonic_tri.name,' / ',tholonic_tri.short,' ( ',tholonic_tri.tags ,')'
                ) FROM
                hexagrams
                Inner Join tholonic_tri ON hexagrams.tri_upper_bin = tholonic_tri.bseq 
                WHERE hexagrams.bseq = ? limit 1
                ) as tri_upper_tho

            ,(SELECT distinct concat(
                '(',tholonic_tri.bseq,') ' ,tholonic_tri.name,' / ',tholonic_tri.short,' ( ',tholonic_tri.tags ,')'
                ) FROM
                hexagrams
                Inner Join tholonic_tri ON hexagrams.tri_lower_bin = tholonic_tri.bseq                 
                WHERE hexagrams.bseq = ? limit 1
             ) as tri_lower_tho        
                 
                
                
           ,(SELECT distinct 
                tholonic_tri.explanation
                FROM
                hexagrams
                Inner Join tholonic_tri ON hexagrams.tri_upper_bin = tholonic_tri.bseq                 
                WHERE hexagrams.bseq = ? limit 1
             ) as tri_upper_tho_ex                

            ,(SELECT distinct 
                tholonic_tri.explanation
                FROM
                hexagrams
                Inner Join tholonic_tri ON hexagrams.tri_lower_bin = tholonic_tri.bseq                 
                WHERE hexagrams.bseq = ? limit 1
             ) as tri_lower_tho_ex                 
                
                
                
                
                
            ,(SELECT distinct 
                tholonic_hex.explanation   
                FROM
                hexagrams
                Inner Join tholonic_hex ON hexagrams.bseq = tholonic_hex.bseq 
                WHERE hexagrams.bseq = ? limit 1
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
        WHERE hexagrams.bseq = ?
EOX;

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(1, $bseq, PDO::PARAM_STR);
            $stmt->bindParam(2, $bseq, PDO::PARAM_STR);
            $stmt->bindParam(3, $bseq, PDO::PARAM_STR);
            $stmt->bindParam(4, $bseq, PDO::PARAM_STR);
            $stmt->bindParam(5, $bseq, PDO::PARAM_STR);
            $stmt->bindParam(6, $bseq, PDO::PARAM_STR);
            $stmt->bindParam(7, $bseq, PDO::PARAM_STR);
            $stmt->bindParam(8, $bseq, PDO::PARAM_STR);


            $stmt->execute();
            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return($r);
        } catch (Exception $e) {
            echo 'Exception -> ';
            dbug($e->xdebug_message);
            die();
        }
    }



    public function getNotesData($param) {
        //dbug($param);

        $query = <<<EOX
        SELECT 
            fix
            ,proofed
            ,`comment`
            ,filename
            ,pseq
            ,bseq
            ,`binary`
            ,title
            ,trans
            ,trigrams
            ,tri_upper
            ,tri_lower
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

    FROM xref_notes
        WHERE xref_notes.`binary` = ?
EOX;

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(1, $param, PDO::PARAM_STR);
            $stmt->bindParam(2, $param, PDO::PARAM_STR);
            $stmt->bindParam(3, $param, PDO::PARAM_STR);

            $stmt->execute();
            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return($r);
        } catch (Exception $e) {
            echo 'Exception -> ';
            //dbug($e->xdebug_message);
            die();
        }
    }
    public function getQabalahData($param) {
        //dbug($param);
        /* convert binary paran to nuymner */
        
        $dec = bindec($param);
        $query = "";
        if ($dec>31) {
            $query = <<<EOX

            SELECT
                        xref_32pairs.pathnum
                ,xref_32pairs.num
                ,xref_32pairs.title
                ,xref_32pairs.path
                ,xref_32pairs.assiah
                ,xref_32pairs.type
                ,xref_32pairs.tarot_num
                ,xref_32pairs.tarot
                ,xref_32pairs.des_name
                ,xref_32pairs.des_pseq
                ,xref_32pairs.des_bseq
                ,xref_32pairs.des_binary
                ,xref_32pairs.des_balance
                ,xref_32pairs.des_balance_desc
                ,xref_32pairs.des_meaning
                ,xref_32pairs.asc_name
                ,xref_32pairs.asc_pseq
                ,xref_32pairs.asc_bseq
                ,xref_32pairs.asc_binary
                ,xref_32pairs.asc_balance
                ,xref_32pairs.asc_balance_desc
                ,xref_32pairs.asc_meaning
                ,xref_32pairs.theme
                ,xref_32pairs.desc
                ,hexagrams.title AS `HEX`
            FROM
                hexagrams
            Inner Join xref_32pairs ON hexagrams.bseq = xref_32pairs.des_bseq
            WHERE hexagrams.`bseq` = ?
EOX;
        } else {
            $query = <<<EOX

            SELECT
                xref_32pairs.pathnum
                        ,xref_32pairs.num
                ,xref_32pairs.title
                ,xref_32pairs.path
                ,xref_32pairs.assiah
                ,xref_32pairs.type
                ,xref_32pairs.tarot_num
                ,xref_32pairs.tarot
                ,xref_32pairs.des_name
                ,xref_32pairs.des_pseq
                ,xref_32pairs.des_bseq
                ,xref_32pairs.des_binary
                ,xref_32pairs.des_balance
                ,xref_32pairs.des_balance_desc
                ,xref_32pairs.des_meaning
                ,xref_32pairs.asc_name
                ,xref_32pairs.asc_pseq
                ,xref_32pairs.asc_bseq
                ,xref_32pairs.asc_binary
                ,xref_32pairs.asc_balance
                ,xref_32pairs.asc_balance_desc
                ,xref_32pairs.asc_meaning
                ,xref_32pairs.theme
                ,xref_32pairs.desc
                ,hexagrams.title AS `HEX`
            FROM
                hexagrams
            Inner Join xref_32pairs ON hexagrams.bseq = xref_32pairs.asc_bseq
            WHERE hexagrams.`bseq` = ?
EOX;
        }

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(1, $dec, PDO::PARAM_STR);
//            $stmt->bindParam(2, $param, PDO::PARAM_STR);
//            $stmt->bindParam(3, $param, PDO::PARAM_STR);

            $stmt->execute();
            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return($r);
        } catch (Exception $e) {
            //echo 'Exception -> ';
            dbug($e);
            
        }
    }

    public function DEL_getShortData($param) {
        //dbug($param);

//        $query = <<<EOX
//        SELECT 
//            fix
//            ,proofed
//            ,`comment`
//            ,filename
//            ,pseq
//            ,bseq
//            ,`binary`
//            ,title
//            ,trans
//            ,trigrams
//            ,(SELECT distinct concat(
//                xref_trigrams.pseq,' (',xref_trigrams.bseq,') ',xref_trigrams.title,' / ',xref_trigrams.trans,' / ',xref_trigrams.t_element
//                )   FROM
//                xref_short
//                Inner Join xref_trigrams ON xref_short.tri_upper_bin = xref_trigrams.bseq 
//                WHERE xref_short.binary = ? limit 1
//                ) as tri_upper
//            ,(SELECT distinct concat(
//                xref_trigrams.pseq,' (',xref_trigrams.bseq,') ',xref_trigrams.title,' / ',xref_trigrams.trans,' / ',xref_trigrams.t_element
//                )   FROM
//                xref_short
//                Inner Join xref_trigrams ON xref_short.tri_lower_bin = xref_trigrams.bseq 
//                WHERE xref_short.binary = ? limit 1
//             ) as tri_lower
//                
//            ,explanation
//            ,judge_old
//            ,judge_exp
//            ,image_old
//            ,image_exp
//            ,line_1
//            ,line_1_org
//            ,line_1_exp
//            ,line_2
//            ,line_2_org
//            ,line_2_exp
//            ,line_3
//            ,line_3_org
//            ,line_3_exp
//            ,line_4
//            ,line_4_org
//            ,line_4_exp
//            ,line_5
//            ,line_5_org
//            ,line_5_exp
//            ,line_6
//            ,line_6_org
//            ,line_6_exp
//
//    FROM xref_short
//        WHERE xref_short.`binary` = ?
//EOX;
//
//        try {
//            $stmt = $this->pdo->prepare($query);
//            $stmt->bindParam(1, $param, PDO::PARAM_STR);
//            $stmt->bindParam(2, $param, PDO::PARAM_STR);
//            $stmt->bindParam(3, $param, PDO::PARAM_STR);
//
//            $stmt->execute();
//            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
//            return($r);
//        } catch (Exception $e) {
//            echo 'Exception -> ';
//            dbug($e->xdebug_message);
//            die();
//        }
    }


    public function DEL_getDataAlt($param) {
//        $query = <<<EOX
//        SELECT 
//        `fix`
//        ,`comment`
//        ,`filename`
//        ,pseq
//        ,bseq
//        ,`binary`
//        ,title
//        ,trans
//        ,trigrams
//        ,(SELECT distinct concat(
//                xref_trigrams.pseq,' (',xref_trigrams.bseq,') ',xref_trigrams.title,' / ',xref_trigrams.trans,' / ',xref_trigrams.t_element
//            )   FROM
//            hexagrams
//            Inner Join xref_trigrams ON hexagrams.tri_upper_bin = xref_trigrams.bseq 
//            WHERE hexagrams.pseq = ? limit 1
//            ) as tri_upper
//        ,(SELECT distinct concat(
//                xref_trigrams.pseq,' (',xref_trigrams.bseq,') ',xref_trigrams.title,' / ',xref_trigrams.trans,' / ',xref_trigrams.t_element
//            )   FROM
//            hexagrams
//            Inner Join xref_trigrams ON hexagrams.tri_lower_bin = xref_trigrams.bseq 
//            WHERE hexagrams.pseq = ? limit 1
//         ) as tri_lower
//        ,explanation
//        ,judge_old
//        ,judge_exp
//        ,image_old
//        ,image_exp
//        ,line_1
//        ,line_1_org
//        ,line_1_exp
//        ,line_2
//        ,line_2_org
//        ,line_2_exp
//        ,line_3
//        ,line_3_org
//        ,line_3_exp
//        ,line_4
//        ,line_4_org
//        ,line_4_exp
//        ,line_5
//        ,line_5_org
//        ,line_5_exp
//        ,line_6
//        ,line_6_org
//        ,line_6_exp
//FROM hexagrams
//    WHERE hexagrams.pseq = ?      
//EOX;
//        try {
//            $stmt = $this->pdo->prepare($query);
//            $stmt->bindParam(1, $param, PDO::PARAM_STR);
//            $stmt->bindParam(2, $param, PDO::PARAM_STR);
//            $stmt->bindParam(3, $param, PDO::PARAM_STR);
//            $stmt->execute();
//            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
//            return($r);
//        } catch (Exception $e) {
//            echo 'Exception -> ';
//            dbug($e->xdebug_message);
//            die();
//        }
    }

    public function BROKEgetData($query) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    
    public function fetchAllHexByPseq($pseq) {
        $query = "SELECT * FROM hexagrams WHERE pseq = :pseq";
        return($this->ex($query, array(':pseq' => $pseq), array())->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchAllHex() {
        $query = "SELECT * FROM `hexagrams`";
        return($this->ex($query, array(), array())->fetchAll(PDO::FETCH_ASSOC));
    }

    public function sql($vars) {
        $query = "UPDATE hexagrams set " . $vars['name'] . " = :val WHERE ID = " . $vars['i'];
        return($this->ex($query, array(":val" => $vars['val']), array())->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getAllHexes() {
        $query = "SELECT * from hexagrams";
        $sth = $this->o->prepare($query);
        $sth->execute();
        return($sth->fetchAll(PDO::FETCH_ASSOC));
//        return($this->ex($query, array(), array('debug' => $d))->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getNotes($h) {
        $query = "SELECT * from xref_notes where pseq=${h}";
        $sth = $this->o->prepare($query);
        $sth->execute();
        return($sth->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getHex($h) {
        $query = "SELECT * from hexagrams where pseq=${h}";
        $sth = $this->o->prepare($query);
        $sth->execute();
        return($sth->fetchAll(PDO::FETCH_ASSOC));

        //return($this->ex($query, array(), array())->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getBin($h) {
        $query = "SELECT * from hexagrams where bseq=${h}";
        $sth = $this->o->prepare($query);
        $sth->execute();
        return($sth->fetchAll(PDO::FETCH_ASSOC));
    }

    public function cbin2hex($b) {
        $query = "SELECT pseq from hexagrams where bseq=${b}";

        $sth = $this->o->prepare($query);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return($res[0]['pseq']);
    }

    public function chex2bin($h) {
        $query = "SELECT bseq from hexagrams where pseq=${h}";
        //var_dump($query);
        $sth = $this->o->prepare($query);
        $sth->execute();
        $bin = $sth->fetch();
        return($bin['bseq']);
    }

    public function getHexnumOppositeByPseq($pseq) {
        $bin = $this->getHexFieldByPseq("hexagrams", "bseq", $pseq);
        $obin = 63 - $bin;
        $ohexnum = $this->cbin2hex($obin);
        return($ohexnum);
    }

    public function getHexnumOppositeByBseq($bseq) {
        $bin = $bseq;//$bin = $this->getHexFieldByPseq("hexagrams", "bseq", $bseq);
        $obin = 63 - $bin;
        $ohexnum = $obin;//$this->cbin2hex($obin);
        return($ohexnum);
    }

    public function getHexFieldByBinary($table, $field, $bin) {
        $query = "SELECT $table.$field from $table where `binary` = '${bin}'";
        $sth = $this->o->prepare($query);
        $sth->execute();
        $bin = $sth->fetch();
        return($bin[$field]);
    }

    public function getHexFieldByPseq($table, $field, $pseq) {
        $query = "SELECT $table.$field from $table where pseq = ${pseq}";
        //var_dump($query);
        $sth = $this->o->prepare($query);
        $sth->execute();
        $bin = $sth->fetch();
        return($bin[$field]);
    }    
    public function getHexFieldByBseq($table, $field, $bseq) {
        $query = "SELECT $table.$field from $table where bseq = ${bseq}";
        //var_dump($query);
        $sth = $this->o->prepare($query);
        $sth->execute();
        $bin = $sth->fetch();
        return($bin[$field]);
    }
    public function getPairsByDesBseq($table, $field, $bseq) {
        $query = "SELECT $table.$field from $table where des_bseq = ${bseq}";
        //var_dump($query);
        $sth = $this->o->prepare($query);
        $sth->execute();
        $bin = $sth->fetch();
        return($bin[$field]);
    }
    public function getPairsPathnumByDesBseq($bseq) {
        $query = "SELECT pathnum from xref_32pairs where des_bseq = ${bseq} OR asc_bseq = ${bseq}";
        //var_dump($query);
        $sth = $this->o->prepare($query);
        $sth->execute();
        $bin = $sth->fetch();
        return($bin['pathnum']);
    }

    public function getAllPositions() {
        $query = "SELECT * from positions";
        $sth = $this->o->prepare($query);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return($res);
    }

    public function getAllDescPositions() {
        $query = "SELECT * from desc_positions";
        $sth = $this->o->prepare($query);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return($res);
    }

    public function getAllAscPositions() {
        $query = "SELECT * from asc_positions";
        $sth = $this->o->prepare($query);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return($res);
    }

    public function site_authuser($u, $p) {
        $query = "SELECT * FROM `site_user` WHERE username='$u' and password='$p'";
        $_SESSION['username'] = $u;
        $sth = $this->o->prepare($query);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($res);
        $count = count($res);
        //var_dump($count);
        return($res);
    }

    public function putSuggestion($sug) {
        try {
            $query = "insert into site_suggestions (suggestion,dtstamp) values (:sug,NOW())";
            $sth = $this->o->prepare($query);
            $sth->bindParam(":sug", $sug,PDO::PARAM_STR);
            $sth->execute();
            return(1);
        } catch (PDOException $e) {
            dbug($e,TRUE);
            die("FATAL ERROR");                   
        }
    }
    public function getSuggestions() {
        try {
            $query = "select * from site_suggestions";
            $sth = $this->o->prepare($query);
            $sth->bindParam(":sug", $sug,PDO::PARAM_STR);
            $sth->execute();
            $r = $sth->fetchAll(PDO::FETCH_ASSOC);
            return($r);            return(1);
        } catch (PDOException $e) {
            dbug($e,TRUE);
            die("FATAL ERROR");                   
        }
    }
    public function subSearch($searchStr) {
        $sr = array();


        $sr["comment"] = $this->subResults($searchStr, "comment");
        $sr["title"] = $this->subResults($searchStr, "title");
        $sr["trans"] = $this->subResults($searchStr, "trans");
        $sr["trigrams"] = $this->subResults($searchStr, "trigrams");
        $sr["tri_upper"] = $this->subResults($searchStr, "tri_upper");
        $sr["tri_lower"] = $this->subResults($searchStr, "tri_lower");
        $sr["explanation"] = $this->subResults($searchStr, "explanation");
        $sr["judge_old"] = $this->subResults($searchStr, "judge_old");
        $sr["judge_exp"] = $this->subResults($searchStr, "judge_exp");
        $sr["image_old"] = $this->subResults($searchStr, "image_old");
        $sr["image_exp"] = $this->subResults($searchStr, "image_exp");
        $sr["line_1_org"] = $this->subResults($searchStr, "line_1_org");
        $sr["line_1_exp"] = $this->subResults($searchStr, "line_1_exp");
        $sr["line_2_org"] = $this->subResults($searchStr, "line_2_org");
        $sr["line_2_exp"] = $this->subResults($searchStr, "line_2_exp");
        $sr["line_3_org"] = $this->subResults($searchStr, "line_3_org");
        $sr["line_3_exp"] = $this->subResults($searchStr, "line_3_exp");
        $sr["line_4_org"] = $this->subResults($searchStr, "line_4_org");
        $sr["line_4_exp"] = $this->subResults($searchStr, "line_4_exp");
        $sr["line_5_org"] = $this->subResults($searchStr, "line_5_org");
        $sr["line_5_exp"] = $this->subResults($searchStr, "line_5_exp");
        $sr["line_6_org"] = $this->subResults($searchStr, "line_6_org");
        $sr["line_6_exp"] = $this->subResults($searchStr, "line_6_exp");




        $final = formatSearch($sr, $searchStr);
        return($final);
    }

    private function subResults($searchStr, $field) {

        //https://dev.mysql.com/doc/refman/5.7/en/fulltext-natural-language.html

        $query = <<<EOX
            SELECT pseq ,${field}
            FROM 
              hexagrams 
            WHERE MATCH 
              (${field})
                 AGAINST
              ('${searchStr}'  IN NATURAL LANGUAGE MODE);
EOX;

        $sth = $this->o->prepare($query);
        $sth->execute();
        $r = $sth->fetchAll(PDO::FETCH_ASSOC);
        return($r);
    }

    public function searchResults($str, $field) {

        //https://dev.mysql.com/doc/refman/5.7/en/fulltext-natural-language.html
        $query = <<<EOX
                
            SELECT pseq ,${field},
            MATCH 
              (${field}) 
                AGAINST
              ('${str}'  IN NATURAL LANGUAGE MODE) AS score
            FROM 
              hexagrams 
            WHERE MATCH 
              (${field})
                 AGAINST
              ('${str}'  IN NATURAL LANGUAGE MODE);
EOX;

        $sth = $this->o->prepare($query);
        $sth->execute();
        $r = $sth->fetchAll(PDO::FETCH_ASSOC);
        $res = var_export($r, TRUE);
        $out = "";
        foreach ($r as $p) {
            $out .= "<div><b><a href='/show.php?pseq=" . $p['pseq'] . "'>hexagram " . $p['pseq'] . " (" . $this->getHexFieldByPseq("hexagrams", "trans", $p['pseq']) . " )</a></b>:</div>";
            $out .= "${field} Commentary<div style='font-size:8pt; border:1px solid grey; margin-left:20px'>" . highlight($str, $p[$field]) . "</div>";
            //$out .= "Image Commentary<div style='font-size:8pt; border:1px solid grey; margin-left:20px'>".highlight($str,$p['image_exp'])."</div>";  
            foreach ($p as $key => $val) {
                
            }
        }

        $final = "<div style='max-width:80%;overflow:scroll; word-break:break-all;height:400px;'>" . count($r) . " hexagrams with " . highlight($str, $str) . " by score $out</div>";

        return($final);
    }
    
    public function getStopWords() {
        $query = "SELECT * FROM INFORMATION_SCHEMA.INNODB_FT_DEFAULT_STOPWORD";
        $sth = $this->o->prepare($query);
        $sth->execute();
        $r = $sth->fetchAll(PDO::FETCH_ASSOC);
        $simpleArray = array();
        foreach ($r as $s) {
            array_push($simpleArray,$s['value']);
        }
        return($simpleArray);
    }
}
