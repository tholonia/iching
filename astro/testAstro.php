<?php


require getRootDir(). "/lib/functions.php";
require_once getRootDir(). "/lib/class/Tosser.class.php";
?>


<?php

//$tosser = new Tosser();
//$r = $tosser->getAstro();
$r = getAstro();

print_r($r);

function getAstro() {


    $astroRoot = getRootDir(). "/astro";
    $astroCalc = getServerPrefix() . "/astro/as.html";
    system(getRootDir(). "/astro/getJson.sh ${astroRoot} ${astroCalc}");

    $astroUrl = getServerPrefix() . "/astro/js/astrodataJson.html";
    $astroPage = file_get_contents($astroUrl);

//        $this->logit("astro debug", $astroPage);

    $search_pattern = "/.*>(\{.*\})<.*/s";
    $clean = "<div>" . preg_replace($search_pattern, "$1", $astroPage) . "</div>";
    $dom = new DOMDocument();
    $dom->loadHTML($clean);

    $myDivs = $dom->getElementsByTagName('div');
    foreach ($myDivs as $key => $value) {
        $result[] = $value->nodeValue;
    }
    $astroJson = $result[0];
    $astroObj = json_decode($astroJson, true);

//    print_r($astroObj);
//exit;
    $chart = makeAstroChart($astroObj);

    exit;
    $anums = array();


    foreach ($astroObj as $planet => $pary) {
        if ($planet != "Sun") {
            if (isset($pary['RA'])) {
                $nodec = str_replace(".", "", $pary['RA']['S']);
                $nary = str_split($nodec);
                $nt = 0;
                foreach ($nary as $n) {
                    $nt += $n;
                }
                $anums[$planet] = (sumnums($nt) % 4) + 6;
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

    //      $this->logit("=> getAstro()", $throw);
    return($throw);
}

function sumnums($n) {
    if ($n > 10) {
        $na = str_split($n);
        $at = 0;
        foreach ($na as $a) {
            $at += $a;
        }
        return(sumnums($at));
    } else {
        return($n);
    }
}

function shortTimeStr($p) {
    $ra = $p['RA']['h'] . "m:" . $p['RA']['m'] . "s";
    $dec = $p['dec']['dec'] . $p['dec']['deg'] . "d" . $p['dec']['min'] . "m" . $p['dec']['sec'] . "s";

//    $degz = sprintf("%02f.02", (($p['RA']['h'] * 30) % 30)  +  ($p['RA']['m']/60));
//    $degz = sprintf("%03f.02", ($p['RA']['h'] * 15)   +  (60/$p['RA']['m']));
//    $degz = sprintf("%dd", (($p['RA']['h'] * 15)  +($p['RA']['m']/60)) / 30);
    $zdeg = $p['RA']['H'] * 15;
    $zmin = $p['RA']['M'] / 60;

    $z1 = $zdeg / 30;
    $z1i = intval($zdeg / 30);
    $sdeg = sprintf("%.2f", ($z1 - $z1i) * 30);
//        print $p['RA']['h']." = ".sprintf("%.2f",$sdeg)."\n";
//        print $p['RA']['m']." = ".$zmin."\n";
//
    $degz = 0;

    return(array('ra' => $ra, 'dec' => $dec, 'degz' => $sdeg));
}

function makeAstroChart($obj) {
    foreach ($obj as $planet => $pary) {
        if ($planet != "Sun") {
            if ($planet != "planet") {
                if ($planet != "localtime") {
                    $shortTime = shortTimeStr($pary);
                    print "$planet in " . $pary['RA']['zodiac'] . "(" . $shortTime['degz'] . ")";
                    if (isset($pary['aspects'])) {
                        print "..."
                                . $pary['aspects']['relation'] . " "
                                . $pary['aspects']['that_planet'] . " in "
                                . $pary['aspects']['that_sign'];
                    }
                    print "\n\n";
                }
            }
        } else {
//            $shortTime = shortTimeStr($pary);
//            print "$planet in " . $pary['RA']['zodiac'];
//            if (isset($pary['aspects'])) {
//                print "..." . $pary['aspects']['relation'] . " " . $pary['aspects']['that_planet'] . " in " . $pary['aspects']['that_sign'];
//            }
//            print "\n";
        }
    }
}
?>
