<?php

require "lib/functions.php";

/* 'iching_root' is defined in the php.ini file, this way is it always correct 
 * for whatever maching is being used 
 */

try {
    $query = "delete from  xref_structured";
    $sth = $dbh->o->prepare($query);
    $sth->execute();
} catch (PDOException $e) {
    dbug($e->xdebug_message, TRUE);
    die("FATAL ERROR");
}

//require getRootDir() . "/elements/header.php";

DEFINE('YANG', 1);
DEFINE('YIN', 0);


//yang line hold a yang = string holding stroing
//
//yang line hold a yin =  string holding weak
//yin line hold a yang = weak holding strong
//yin line hold a yin  = weak holding weak

$YY_names = array(
    "0" => array(
        "name" => "YIN",
        "quality" => "RECEPTIVITY",
        "direction" => "INWARD",
        "pole" => "MATERIALISTIC",
        "desc" => "RECEIVING EXPRESSION",
        "verb" => "IS ACTED UPON",
        "noun" => "...",
        "graph" => "--  --",
        "ability" => "WEAK"
    ),
    "1" => array(
        "name" => "YANG",
        "quality" => "ACTION",
        "direction" => "OUTWARD",
        "pole" => "IDEALISTIC",
        "desc" => "PENETRATING EXPRESSION",
        "verb" => "INITIATES ACTION",
        "noun" => "...",
        "graph" => "------",
        "ability" => "STRONG"
    )
);

$tri = array(
    "ABSTRACT" => array(
        "1" => "INITIATION",
        "2" => "MANIFESTATION",
        "3" => "RAMIFICATION"
    ),
    "OUTER" => array(
        "POS" => array(
            "1" => "INTEGRATION/(symbiosys)",
            "2" => "APPLICATION/(functionality)",
            "3" => "EFFECT/(sustainability)"
        ),
        "NAT" => array(
            "1" => YIN,
            "2" => YANG,
            "3" => YIN
        )
    ),
    "INNER" => array(
        "POS" => array(
            "1" => "CAUSE/(impulse to create)",
            "2" => "PLANNING/(gathering of resources)",
            "3" => "SKILLS/(limits of implimentation)"
        ),
        "NAT" => array(
            "1" => YANG,
            "2" => YIN,
            "3" => YANG
        )
    )
);

$hex = array(
    "POS" => array(
        "1" => $tri['INNER']['POS']["1"],
        "2" => $tri['INNER']['POS']["2"],
        "3" => $tri['INNER']['POS']["3"],
        "4" => $tri['OUTER']['POS']["1"],
        "5" => $tri['OUTER']['POS']["2"],
        "6" => $tri['OUTER']['POS']["3"]
    ),
    "NAT" => array(
        "1" => $tri['INNER']['NAT']["1"],
        "2" => $tri['INNER']['NAT']["2"],
        "3" => $tri['INNER']['NAT']["3"],
        "4" => $tri['INNER']['NAT']["1"],
        "5" => $tri['INNER']['NAT']["2"],
        "6" => $tri['INNER']['NAT']["3"],
    )
);
/*
 *  APPLY: Assign a YIN/YANG state to each position of the trigram.  
 * Form these definitions, assign a symbol for each trigram.
 */


$tri_states = array(
    "SPACE/(opportunity)", // (yin air)",           The opportunity at hand
    "ENERGY/(initial action)", // (yang fire)",     The first steps 
    "LAND/(arena)", // (yin earth)",                The resources 
    "RAIN/(the journey)", // (yang water)",         The journey 
    
    "OCEAN/(interactivity)", // (yin water)",       The interactivity
    "LIFE/(the game)", // (yang earth)",            The game
    "HEAT/(work)", // (yin fire)",                  The work
    "WIND/(accomplishment)" // (yang air)"          The accomplishment
);
$tri_states_images = array(
    "A VASE", // (yin air)",
    "LIGHTENING", // (yang fire)Y",
    "A CHILD", // (yin earth)",
    "LOVERS", // (yang water)",
    "A MOUNTAIN", // (yin water)",
    "THE SUN", // (yang earth)",
    "A RIVER", // (yin fire)",
    "THE MILKY WAY" // (yang air)"
);
$pair_themes = array(
    "AIR",
    "FIRE",
    "EARTH",
    "WATER"
);

/* THE GROUP is based on the odd or even=ness of the tri value */

$group_type = array(
    "0" => array("name" => $tri_states[0], "group" => YIN, "theme" => $pair_themes[0]),
    "1" => array("name" => $tri_states[1], "group" => YANG, "theme" => $pair_themes[1]),
    "2" => array("name" => $tri_states[2], "group" => YIN, "theme" => $pair_themes[2]),
    "3" => array("name" => $tri_states[3], "group" => YANG, "theme" => $pair_themes[3]),
    "4" => array("name" => $tri_states[4], "group" => YIN, "theme" => $pair_themes[3]),
    "5" => array("name" => $tri_states[5], "group" => YANG, "theme" => $pair_themes[2]),
    "6" => array("name" => $tri_states[6], "group" => YIN, "theme" => $pair_themes[1]),
    "7" => array("name" => $tri_states[7], "group" => YANG, "theme" => $pair_themes[0])
);

/* this is for a trigram */

$tri_data = array(
    array(0, 0, 0),
    array(0, 0, 1),
    array(0, 1, 0),
    array(0, 1, 1),
    array(1, 0, 0),
    array(1, 0, 1),
    array(1, 1, 0),
    array(1, 1, 1)
);

$rel = array(
    "line" => array(
        "1" => array(
            "is" => array(
                "1" => array(
                    "has" => array(
                        "0" => "WEAKENED: " . $hex['POS']["1"],
                         "1" => "STABLE: " . $hex['POS']["1"],
                        "supportedby" => array(
                            // 1 has 1 (1-1) [0] 
                            //                      0 has 1 (1-0) [1]
                            // [01]
                            "01" => "[".$hex['POS']["1"]."] IS STABLE YANG w/ HYPER YIN PARTNER OUTER " . $hex['POS']["4"],
                            // 1 has 0 (0-1) [-1] 
                            //                       0 has 1 (1-0) [1]
                            // [-11]
                            "-11" => "[".$hex['POS']["1"]."] IS HYPO YANG w/ HYPER YIN PARTNER OUTER " . $hex['POS']["4"],
                            // 1 has 1 (1-1) [0] 
                            //                       0 has 0 (0-0) [0]
                            // [00]
                            "00" => "[".$hex['POS']["1"]."] IS STABLE YANG w/ STABLE YIN PARTNER OUTER " . $hex['POS']["4"],
                            // 1 has 0 (0-1) [-1] 
                            //                       0 has 0 (0-0) [0]
                            // [-10]
                            "-10" => "[".$hex['POS']["1"]."] IS HYPO YANG w/ STABLE YIN PARTNER OUTER " . $hex['POS']["4"],
                            
                            "0-1" => " [0-1] not here",
                            "10" => " [10] not here",
                            "1-1" => " [1-1] not here",
                        ),
                    )
                ),
            )
        ),
       "2" => array(
            "is" => array(
                "0" => array(
                    "has" => array(
                        "0" => "COMPATIBLE: " . $hex['POS']["2"],
                        "1" => "STRONG: " . $hex['POS']["2"],
                        "supportedby" => array(
                            // 0 has 1 (1-0) [1] 
                            //                      1 has 1 (1-1) [0]
                            // [10]
                            "10" => "[".$hex['POS']["2"]."] IS HYPER YIN w/ STABLE YANG PARTNER OUTER " . $hex['POS']["5"],
                            // 0 has 0 (0-0) [0] 
                            //                      1 has 1 (1-1) [0]
                            // [00]
                            "00" => "[".$hex['POS']["2"]."] IS STABLE YIN w/ STABLE YANG PARTNER OUTER " . $hex['POS']["5"],
                            // 0 has 1 (1-0) [1] 
                            //                      1 has 0 (0-1) [-1]
                            // [1-1]
                            "1-1" => "[".$hex['POS']["2"]."] IS HYPER YIN w/ HYPO YANG PARTNER OUTER " . $hex['POS']["5"],
                            // 0 has 0 (0-0) [0] 
                            //                      1 has 0 (0-1) [-1]
                            // [1-1]
                            "0-1" => "[".$hex['POS']["2"]."] IS STABLE YIN w/ HYPER YANG PARTNER OUTER " . $hex['POS']["5"],
                            
                            "01" =>"[01]not here",
                            "-10"=>"[-10]not here",
                            "-11"=>"[-11]not here",
                        ),
                    )
                ),
            )
        ),
        "3" => array(
            "is" => array(
                "1" => array(
                    "has" => array(
                        "0" => "WEAK: " . $hex['POS']["3"],
                        "1" => "COMPATIBLE: " . $hex['POS']["3"],
                        "supportedby" => array(
                            // 1 has 1 (1-1) [0] 
                            //                      0 has 1 (1-0) [1]
                            // [01]
                            "01" => "[".$hex['POS']["3"]."] IS STABLE YANG w/ HYPER YIN PARTNER OUTER " . $hex['POS']["6"],
                            // 1 has 0 (0-1) [-1] 
                            //                       0 has 1 (1-0) [1]
                            // [-11]
                            "-11" => "[".$hex['POS']["3"]."] IS HYPO YANG w/ HYPER YIN PARTNER OUTER " . $hex['POS']["6"],
                            // 1 has 1 (1-1) [0] 
                            //                       0 has 0 (0-0) [0]
                            // [00]
                            "00" => "[".$hex['POS']["3"]."] IS STABLE YANG w/ STABLE YIN PARTNER OUTER " . $hex['POS']["6"],
                            // 1 has 0 (0-1) [-1] 
                            //                       0 has 0 (0-0) [0]
                            // [-10]
                            "-10" => "[".$hex['POS']["3"]."] IS HYPO YANG w/ STABLE YIN PARTNER OUTER " . $hex['POS']["6"],
                            
                            "0-1" => " [0-1] not here",
                            "10" => " [10] not here",
                            "1-1" => " [1-1] not here",
                        ),
                    )
                ),
            )
        ),
        "4" => array(
            "is" => array(
                "0" => array(
                    "has" => array(
                        "0" => "COMPATIBLE: " . $hex['POS']["4"],
                        "1" => "STRONG: " . $hex['POS']["4"],
                        "supportedby" => array(
                            // 0 has 1 (1-0) [1] 
                            //                      1 has 1 (1-1) [0]
                            // [10]
                            "10" => "[".$hex['POS']["4"]."] IS HYPER YIN w/ STABLE YANG PARTNER INNER " . $hex['POS']["1"],
                            // 0 has 0 (0-0) [0] 
                            //                      1 has 1 (1-1) [0]
                            // [00]
                            "00" => "[".$hex['POS']["4"]."] IS STABLE YIN w/ STABLE YANG PARTNER INNER " . $hex['POS']["1"],
                            // 0 has 1 (1-0) [1] 
                            //                      1 has 0 (0-1) [-1]
                            // [1-1]
                            "1-1" => "[".$hex['POS']["4"]."] IS HYPER YIN w/ HYPO YANG PARTNER INNER " . $hex['POS']["1"],
                            // 0 has 0 (0-0) [0] 
                            //                      1 has 0 (0-1) [-1]
                            // [1-1]
                            "0-1" => "[".$hex['POS']["4"]."] IS STABLE YIN w/ HYPER YANG PARTNER INNER " . $hex['POS']["1"],
                            
                            "01" =>"[01]not here",
                            "-10"=>"[-10]not here",
                            "-11"=>"[-11]not here",
                        ),
                    )
                ),
            )
        ),
        "5" => array(
            "is" => array(
                "1" => array(
                    "has" => array(
                        "0" => "WEAK: " . $hex['POS']["5"],
                        "1" => "COMPATIBLE: " . $hex['POS']["5"],
                        "supportedby" => array(
                            // 1 has 1 (1-1) [0] 
                            //                      0 has 1 (1-0) [1]
                            // [01]
                            "01" => "[".$hex['POS']["5"]."] IS STABLE YANG w/ HYPER YIN PARTNER INNER " . $hex['POS']["2"],
                            // 1 has 0 (0-1) [-1] 
                            //                       0 has 1 (1-0) [1]
                            // [-11]
                            "-11" => "[".$hex['POS']["5"]."] IS HYPO YANG w/ HYPER YIN PARTNER INNER " . $hex['POS']["2"],
                            // 1 has 1 (1-1) [0] 
                            //                       0 has 0 (0-0) [0]
                            // [00]
                            "00" => "[".$hex['POS']["5"]."] IS STABLE YANG w/ STABLE YIN PARTNER INNER " . $hex['POS']["2"],
                            // 1 has 0 (0-1) [-1] 
                            //                       0 has 0 (0-0) [0]
                            // [-10]
                            "-10" => "[".$hex['POS']["5"]."] IS HYPO YANG w/ STABLE YIN PARTNER INNER " . $hex['POS']["2"],
                            
                            "0-1" => " [0-1] not here",
                            "10" => " [10] not here",
                            "1-1" => " [1-1] not here",
                        ),
                    )
                ),
            )
        ),
        "6" => array(
            "is" => array(
                "0" => array(
                    "has" => array(
                        "0" => "COMPATIBLE: " . $hex['POS']["6"],
                        "1" => "STRONG: " . $hex['POS']["6"],
                        "supportedby" => array(
                            // 0 has 1 (1-0) [1] 
                            //                      1 has 1 (1-1) [0]
                            // [10]
                            "10" => "[".$hex['POS']["6"]."] IS HYPER YIN w/ STABLE YANG PARTNER INNER " . $hex['POS']["3"],
                            // 0 has 0 (0-0) [0] 
                            //                      1 has 1 (1-1) [0]
                            // [00]
                            "00" => "[".$hex['POS']["6"]."] IS STABLE YIN w/ STABLE YANG PARTNER INNER " . $hex['POS']["3"],
                            // 0 has 1 (1-0) [1] 
                            //                      1 has 0 (0-1) [-1]
                            // [1-1]
                            "1-1" => "[".$hex['POS']["6"]."] IS HYPER YIN w/ HYPO YANG PARTNER INNER " . $hex['POS']["3"],
                            // 0 has 0 (0-0) [0] 
                            //                      1 has 0 (0-1) [-1]
                            // [1-1]
                            "0-1" => "[".$hex['POS']["6"]."] IS STABLE YIN w/ HYPER YANG PARTNER INNER " . $hex['POS']["3"],
                            
                            "01" =>"[01]not here",
                            "-10"=>"[-10]not here",
                            "-11"=>"[-11]not here",
                        ),
                    )
                ),
            )
        ),
    )
);

$linestates = array(
    "0" => "OK",
    "-1" => "THIS RECEPTIVE LINE HAS PETENTRATING POWERS",
    "1" => "THIS STRENGTH OF THIS LINE IS WEAKENED"
);






echo "\n\n\n";

$tri_final = array();  //save these results here

foreach ($tri_data as $sub_tri_data) {  // N elements of tri arrays 
    // check the val
    print "(x2)[".bindec((implode($sub_tri_data)))."]\n";
    
    $idx = binary2dec($sub_tri_data);
    $tri_name = $tri_states[$idx];                              // $idx = 1-7
    $tri_bin = sprintf("%03d", implode($sub_tri_data));
    $tri_dec = binary2dec($sub_tri_data);
    $yy_name = $YY_names[$group_type[$idx]['group']]['desc'];
    $p_theme = $group_type[$idx]['theme'];
//    $p_theme_yy = $pair_themes_duality[$idx];

    $tri_str = "${tri_name}, the ${yy_name} of ${p_theme}\r\n";

    echo "(x)$tri_str";

    $tri_final[$tri_dec] = $tri_str;

    for ($i = 0; $i < count($sub_tri_data); $i++) { // loops throug 3 vals of 1||0
        $line = $i + 1;
        $pos_state = $sub_tri_data[$i]; // pos_state = 0||1

        $tri_pos_line = $tri["ABSTRACT"][$line]; // $tri["ABSTRACT"][1-3] = INITIATION
    }
    echo "\n";
}


/*
 * this is for a hexagram
 */

/* loop[ thru all the  hexagrams */

//for ($h = 0; $h < 64; $h++) {
for ($h = 50; $h < 51; $h++) {
    //if ($h == 33) {exit; }

    $_binary = sprintf("%06d", decbin($h));
    $_bseq = $h;
    $_pseq = $dbh->cbin2hex($h);

//    print_r($_binary);

//    $hexary = array_reverse(str_split($_binary));
    $hexary = str_split($_binary);

    $inner = array($hexary[0], $hexary[1], $hexary[2]);
    $outer = array($hexary[3], $hexary[4], $hexary[5]);
    $_trans = $dbh->getHexFieldByBseq("hexagrams", "trans", $_bseq);
    $_tri_lower = "";
    $_tri_upper = "";
    $_tri_lower_bin = "";
    $_tri_upper_bin = "";
    $_judge_exp = "";
    print "##########################################################\n";
    print "###  processing  [$_trans - ] bin:$h \n";
    print "##########################################################\n";

    $tmp = array(null, null);
    $tmp2 = array(null, null);

    $tri_data = array(
        "INNER" => $inner,
        "OUTER" => $outer
    );
    /* create an array to streo the line resuklts in */
    $hexline = 0;
    $hexlines = array();

    /* initialise here to access outsde of loop scope */
    $tri_part = "";
    $_image_old = "";
    $_trigrams = "";
    $linestates = array();

    /* we need to do this twicem, once to get the $linestate and again to use it */
    foreach ($tri_data as $key => $sub_tri_data) {  // 2 elemenmts called  "INNER" and "OUTER" 
        $tidx = binary2dec($sub_tri_data);

        for ($i = 0; $i < count($sub_tri_data); $i++) { // loops throug 3 vals of 1||0
            $line = $i + 1;
            if ($key == "INNER") {
                $hexline = $line;
                $_tri_lower_bin = $tidx;
            } else {
                $hexline = $line + 3;
                $_tri_upper_bin = $tidx;
            }
            $pos_state = $sub_tri_data[$i]; // pos_state = 0||1
            $tri_pos_line = $tri[$key]["POS"][$line]; // $tri["INNER"]["POS"][1-3]
            $tri_pos_nature = $tri[$key]["NAT"][$line]; // $tri["INNER"]["POS"][1-3]
            $linesym = $YY_names[$sub_tri_data[$i]]["graph"];

            $has = $sub_tri_data[$i];
            $is = $tri_pos_nature;

            $linestates[$hexline] = $has - $is;
        }
    }
    foreach ($tri_data as $key => $sub_tri_data) {  // 2 elemenmts called  "INNER" and "OUTER" 
        $tidx = binary2dec($sub_tri_data);
        $tri_name = $tri_states[$tidx]; 
        
        $tri_part = $tri_final[$tidx];
        if ($key == "OUTER") {
            $_tri_upper = $tri_part;
            $tmp[1] .= "INSIDE: $_tri_upper\r\n";
            $tmp2[1] .= "Inside or below is $tri_states_images[$tidx] ";
        } else {
            $_tri_lower = $tri_part;
            $tmp[0] .= "OUTSIDE: $_tri_lower\r\n";
            $tmp2[0] .= "Outside, or high above there is $tri_states_images[$tidx]\r\n";
        }

        //print_r($linestates);
        print "${key} Trigram: " . $tri_part . "\n\n";
        for ($i = 0; $i < count($sub_tri_data); $i++) { // loops throug 3 vals of 1||0
            $line = $i + 1;
            $optriline = $line;
            $triline = $line;
            if ($key == "INNER") {
                $hexline = $line;
                $_tri_lower_bin = $tidx;
                $optriline = $line + 3;
            } else {
                $hexline = $line + 3;
                $_tri_upper_bin = $tidx;
                $optriline = $line;
            }



            $pos_state = $sub_tri_data[$i]; // pos_state = 0||1
            $tri_pos_line = $tri[$key]["POS"][$line]; // $tri["INNER"]["POS"][1-3]
            $tri_pos_nature = $tri[$key]["NAT"][$line]; // $tri["INNER"]["POS"][1-3]
            $linesym = $YY_names[$sub_tri_data[$i]]["graph"];

            $has = $sub_tri_data[$i];
            $is = $tri_pos_nature;
            /*
              this line can be   -1, 0, 1
              sister line can me -1, 0 ,1
             */
            $s = $linestates[$hexline].$linestates[$optriline];
            
            
            
            $supporting = "$tri_pos_line of $tri_name :".$rel["line"][$hexline]["is"][$is]["has"]["supportedby"][$s];
            // [NEEDS] IS STABLE YANG w/ STABLE YIN PARTNER OUTER INTEGRATION 
            

// print $s."\n";           
//            print_r($rel["line"][$hexline]["is"][$is]["has"]);
//            print_r($supporting);
//exit;
            $rtline = $rel["line"][$hexline]["is"][$is]["has"][$has];

//            $support = $rel["line"][$hexline]["is"][$is]["has"]["supportedby"][$supporting];

//            print "line ${hexline} ${is} holding a ${has} :  $supporting\n";
            $hexlines[$hexline] = 
                "Line ${hexline}: [${linesym}] "
                //Line 1: [------] 
                . "The ". $YY_names[$tri_pos_nature]["direction"] 
                //The OUTWARD 
                . " moving place of " . $tri_pos_line . " holding " . $YY_names[$sub_tri_data[$i]]["quality"] 
                ////moving place of NEEDS holding ACTION 
                . " / (".$YY_names[$is]["ability"]. " holding " . $YY_names[$has]["ability"].")"
                /// (STRONG holding STRONG) [00]  
                        
                . " [$s] $supporting \r\n";
                //[00] [NEEDS] IS STABLE YANG w/ STABLE YIN PARTNER OUTER INTEGRATION 
            
            
            $_judge_exp .= $hexlines[$hexline];
            print($hexlines[$hexline]."\n");
            //echo $hexlines[$hexline];
        }
        print "\n\n";
    }


    $_image_old .= $tmp2[0] . $tmp2[1];
    $_trigrams .= $tmp[0] . $tmp[1];
    print "IMAGE: $_image_old\n";
    try {
        $query = <<<EYX
        insert into xref_structured 
            (   `binary`
                ,bseq
                ,pseq
                ,trans
                ,tri_lower
                ,tri_upper
                ,judge_exp
                ,line_1_org
                ,line_2_org
                ,line_3_org
                ,line_4_org
                ,line_5_org
                ,line_6_org
                ,tri_lower_bin
                ,tri_upper_bin
                ,image_old
                ,trigrams
              ) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
EYX;
        $sth = $dbh->o->prepare($query);
        $sth->bindParam(1, $_binary);
        $sth->bindParam(2, $_bseq, PDO::PARAM_STR);
        $sth->bindParam(3, $_pseq);
        $sth->bindParam(4, $_trans, PDO::PARAM_STR);
        $sth->bindParam(5, $_tri_lower, PDO::PARAM_STR);
        $sth->bindParam(6, $_tri_upper, PDO::PARAM_STR);
        $sth->bindParam(7, $_judge_exp, PDO::PARAM_STR);
        $sth->bindParam(8, $hexlines[1]);
        $sth->bindParam(9, $hexlines[2]);
        $sth->bindParam(10, $hexlines[3]);
        $sth->bindParam(11, $hexlines[4]);
        $sth->bindParam(12, $hexlines[5]);
        $sth->bindParam(13, $hexlines[6]);
        $sth->bindParam(14, $_tri_lower_bin);
        $sth->bindParam(15, $_tri_upper_bin);
        $sth->bindParam(16, $_image_old, PDO::PARAM_STR);
        $sth->bindParam(17, $_trigrams, PDO::PARAM_STR);
        $sth->execute();
    } catch (PDOException $e) {
        dbug($e->xdebug_message, TRUE);
        die("FATAL ERROR");
    }
}

function binary2dec($ary) {
//    print_r($ary);
    $bs = implode($ary);
    $bd = bindec($bs);
    return($bd);
}

/*
  require getRootDir(). "/elements/footer.php";
  /* clean up anythign laying around
  $del = "rm ".getRootDir()."/id/*".session_id()."*";
  system($del); */




$X = <<<XXX
000 (0) YIN AIR, SPACE
001 (1) YANG FIRE, ENERGY
010 (2) YIN EARTH, FERTILE LAND
011 (3) YANG WATER, RAIN
100 (4) YIN WATER,OCEAN
101 (5) YANG EARTH,LIFE
110 (6) YIN FIRE,HEAT   `
111 (7) YANG AIR, THE WIND
        
   
   pair_themes     
        
   111 (7) YANG AIR, THE WIND
   000 (0) YIN AIR, SPACE

   110 (6) YIN FIRE,HEAT   
   001 (1) YANG FIRE, ENERGY
        
   101 (5) YANG EARTH,LIFE    
   010 (2) YIN EARTH, FERTILE LAND

   011 (3) YANG WATER, RAIN
   100 (4) YIN WATER,OCEAN
        
    
        
        
        
XXX;
?>
