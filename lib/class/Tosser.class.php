<?php

class Tosser {

    public $code;
    public $script;

    public function __construct() {
        
    }

    public function getAstro() {


        $astroRoot = getRootDir(). "/astro";
        $astroCalc = getServerPrefix() . "/astro/as.html";
        system(getRootDir(). "/astro/getJson.sh ${astroRoot} ${astroCalc}");

#        echo "<PRE>";
#        echo getServerPrefix()."</br>";
        $astroUrl = getServerPrefix() . "/astro/js/astrodataJson.html";
#        echo $astroUrl;
        $astroPage = file_get_contents($astroUrl);
#        echo "</PRE>";

        $this->logit("astro debug", $astroPage);

        libxml_use_internal_errors(false);
        
        $search_pattern = "/.*>(\{.*\})<.*/s";
        $clean = "<div>" . preg_replace($search_pattern, "$1", $astroPage) . "</div>";
//        $clean = "<div></div>";
        
        
        $dom = new DOMDocument();
        $dom->loadHTML($clean);

        $myDivs = $dom->getElementsByTagName('div');
        foreach ($myDivs as $key => $value) {
            $result[] = $value->nodeValue;
        }
        $astroJson = $result[0];
        $astroObj = json_decode($astroJson, true);

        $anums = array();

        foreach ($astroObj as $planet => $pary) {
            if ($planet != "Sun") {
                if ($planet != "planet") {
                    if ($planet != "localtime") {
                        if (isset($pary['RA'])) {
                            $nodec = str_replace(".", "", $pary['RA']['S']);
                            $nary = str_split($nodec);
                            $nt = 0;
                            foreach ($nary as $n) {
                                $nt += $n;
                            }
                            $anums[$planet] = ($this->sumnums($nt) % 4) + 6;
                        }
                    }
                }
            }
        }

        $throw = array(
            $anums['Moon'],
            $anums['Mercury'],
            $anums['Venus'],
            $anums['Mars'],
            $anums['Jupiter'],
            $anums['Saturn'],
        );

        $this->logit("=> getAstro()", $throw);
        return($throw);
    }

    private function sumnums($n) {
        if ($n > 10) {
            $na = str_split($n);
            $at = 0;
            foreach ($na as $a) {
                $at += $a;
            }
            return($this->sumnums($at));
        } else {
            return($n);
        }
    }

    public function getRandomOrg() {
        $throw = array(null, null, null, null, null, null);
        for ($i = 0; $i < 6; $i++) {
            $uid = session_id(); //uniqid();
            $f = getRootDir(). "/throw.sh ${uid} ${i}";
            $run = trim(system($f));
            $flip = file_get_contents("id/${uid}");

            switch ($flip) {
                case 0:
                    $throw[$i] = 6;
                    break;
                case 1:
                    $throw[$i] = 7;
                    break;
                case 2:
                    $throw[$i] = 8;
                    break;
                case 3:
                    $throw[$i] = 9;
                    break;
            }
        }

        $this->logit("=> getRandomOrg()", $throw);
        return($throw);
    }

    private function logit($name, $data) {
        $f = fopen(getRootDir(). "/log/toss.log", "a");
        fwrite($f, $name . "\n");
        $tstamp = date("F d, Y h:i:s A");
        fwrite($f, $tstamp . "\n");
        $t = var_export($data, TRUE);
        fwrite($f, $t);
        fwrite($f, "\n====================================================\n");
        fclose($f);
    }

    public function getHotBits() {
        $lines = array();
        $uid = "hotbits_" . session_id(); //uniqid("hb_"); /* used to save and inspect values */
        for ($i = 0; $i < 6; $i++) {
            $hotbits = $this->getCleanHotBits($uid);
            $line = null;
            foreach ($hotbits as $tb) {
                $c = ($tb % 2) + 2;
                $line += ($tb % 2) + 2;
            }
            array_push($lines, $line);
        }
        $this->logit("=> getHotBits()", $lines);
        return($lines);
    }

    private function getCleanHotBits($id) {
        $intAry = array();
        # retired :(
        #$hotbitsURL = "http://www.fourmilab.ch/cgi-bin/uncgi/Hotbits?nbytes=3&fmt=c&apikey=HB1P93mBRUA23F7HUF5MCpyZ2PS";
        #  Intel CPU built-in RDSEED
        $hotbitsURL = "http://www.fourmilab.ch/cgi-bin/uncgi/Hotbits?nbytes=3&fmt=c&apikey=RB1P93mBRUA23F7HUF5MCpyZ2PS";
 //       $hotbitsURL = "http://www.fourmilab.ch/cgi-bin/uncgi/Hotbits?nbytes=3&fmt=c&pseudo=pseudo";

        $start = microtime(true);

        $str = file_get_contents($hotbitsURL);
        $str = str_replace("\n", "", $str);
        $str = str_replace("\r", "", $str);

        $re = '/^.*{\s*([\d]*),\s*([\d]*),\s*([\d]*)}.*$/sU';
        $subst = '$1,$2,$3';
        $result = preg_replace($re, $subst, $str);
        
        $str = $result;
        $hotbits = explode(",", $str);

        $end = microtime(true);

        $tdelta = $end - $start;
        $f = fopen("id/${id}", "w");
        $fb = var_export($hotbits, TRUE);
        fwrite($f, $fb);
        fwrite($f, $tdelta . "\n");
        fclose($f);

        foreach ($hotbits as $h) {
            array_push($intAry, intval(trim($h)));
        }
        //var_dump($intAry);
        return($intAry);
    }

    public function getPlum() {


        $hex = array();
        $m = 0;
        for ($k = 0; $k < 6; $k++) {
            $now = microtime_float() * 10000;
            $nowAry = str_split("" . $now);

            $combo = array();

            $ci1 = $this->rand_seq(0, 5, 5);
            $ci2 = $this->rand_seq(6, 11, 5);

            array_push($combo, ($nowAry[$ci1[0]] + $nowAry[$ci2[5]]));
            array_push($combo, ($nowAry[$ci1[1]] + $nowAry[$ci2[4]]));
            array_push($combo, ($nowAry[$ci1[2]] + $nowAry[$ci2[3]]));
            array_push($combo, ($nowAry[$ci1[3]] + $nowAry[$ci2[2]]));
            array_push($combo, ($nowAry[$ci1[4]] + $nowAry[$ci2[1]]));
            array_push($combo, ($nowAry[$ci1[5]] + $nowAry[$ci2[0]]));

            $recombo = array();

            $rci1 = $this->rand_seq(0, 2, 2);
            $rci2 = $this->rand_seq(3, 5, 2);

            array_push($recombo, ($combo[$rci1[0]] + $combo[$rci2[2]]) % 4);
            array_push($recombo, ($combo[$rci1[1]] + $combo[$rci2[1]]) % 4);
            array_push($recombo, ($combo[$rci1[2]] + $combo[$rci2[0]]) % 4);

            for ($l = 0; $l < 3; $l++) {
                //if ($recombo[$l] == 0) {
                $recombo[$l] = ($recombo[$l] % 2) + 2;
                //}
            }
            $ts = $recombo[0] . "," . $recombo[1] . "," . $recombo[2];
            $t = $recombo[0] + $recombo[1] + $recombo[2];
//        print "[$t]  ";


            usleep(rand(rand(1000, 10000), rand(10000, 100000)));
            array_push($hex, $t);
            $m++;
        }
        //var_dump($hex);
        $this->logit("=> getPlum()", $hex);

        return($hex);
    }

    private function rand_seq($fromto, $to = null, $limit = null) {

        if (is_null($to)) {
            $to = $fromto;
            $fromto = 0;
        }

        if (is_null($limit)) {
            $limit = $to - $fromto + 1;
        }
        $randArr = array();

        for ($i = $fromto; $i <= $to; $i++) {
            $randArr[] = $i;
        }
        $result = array();

        for ($i = 0; $i < $limit || sizeof($randArr) > 0; $i++) {
            $index = mt_rand(0, sizeof($randArr) - 1); // select rand index / выбираем случайный индекс массива 
            $result[] = $randArr[$index]; // add random element / добавляем случайный элемент массива 
            array_splice($randArr, $index, 1); // remove it=) / удаляем его =)
        }

        return $result;
    }

}

/*
  //   Find a quiet place, and take a few moments to relax and meditate on your query. Concentrate on your question or the situation for which you seek guidance.
  //Taking the 50 sticks in your hand, remove one stick and set it aside.

  $sticks = 50 -1;
  //With the 49 sticks remaining, divide them into two (right side and left side) and place them down side by side.
  $left_floor = rand (1,48);
  $right_floor = $sticks - $left;

  // 4 Take the bunch on the right side (with your right hand), and remove one stick, placing it on your left hand,
  //between your ring (fourth) and little finger (pinkie).
  $right_hand - $right_floor -1;
  // 5 From the left-side bunch, remove groups of four sticks at a time, until four or less sticks are left. Set this aside.
  $left_floor_remaining = ($left_floor % 4);
  $left_floor_remaining = ($left_floor_remaining == 0 ? 4 : $left_floor_remaining);
  // 6 Taking back the right-side bunch, remove four sticks at a time again, until four of less remain. Set this aside.
  $right_floor_remaining = ($rightfloor_ % 4);
  $right_floor_remaining = ($right_floor_remaining == 0 ? 4 : $right_floor_remaining);
  // 7 Place the remainder from the left-hand bunch between the ring finger and the middle finger and the remainder from the
  $left_hand = $left_floor_remaining;

  //right-hand bunch between the middle finger and index finger of the left hand.
  $left_hand += $right_floor_remaining;
  //Take all the sticks from your left hand and set them aside. Gather the remaining sticks and divide them into two bunches.
  $remaining = $sticks - $left_hand;
 */