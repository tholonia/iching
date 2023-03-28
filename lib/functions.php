<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "/storage/sites/babelbrowser/lib/class/LoadIni.class.php";
require_once "/storage/sites/babelbrowser/lib/class/DataMapper.class.php";
require_once "/storage/sites/babelbrowser/lib/class/CssHex.class.php";
require_once "/storage/sites/babelbrowser/lib/class/Tosser.class.php";
require_once '/storage/sites/babelbrowser/dompdf/autoload.inc.php';

$ini = LoadIni::init("iching");
//var_dump($ini);
$dbh = new DataMapper($ini);

require getRootDir() . "/vendor/autoload.php";
require getRootDir() . "/lib/md2pdf/vendor/autoload.php";

function getRootDir() {
    $runtime = "dev";
    if (isset($_SERVER['runtime'])) {
        $runtime = $_SERVER['runtime'];
    }

    $dir = get_cfg_var("iching.${runtime}.root");
    return($dir);
}

function getTestServer() {
    $runtime = $_SERVER['runtime'];
    $srv = get_cfg_var("iching.${runtime}.testServer");
    return($srv);
}

function getUser() {
    $runtime = $_SERVER['runtime'];
    $un = get_cfg_var("iching.${runtime}.user");
    return($un);
}

function getNotes($pseq) {
    $hex = $GLOBALS['dbh']->getNotes($pseq);
    $out = "";
    $hex[0]['pseq'] = null;
    $hex[0]['bseq'] = null;
    $hex[0]['oseq'] = null;
    $hex[0]['binary'] = null;
    $hex[0]['balance'] = null;
    $hex[0]['tri_upper_bin'] = null;
    $hex[0]['tri_lower_bin'] = null;

    /* JWFIX notes not appearing... see notes for 50 (cauldron) judge_exp */
    foreach ($hex[0] as $key => $val) {
        if ($val) {
            $out .= "<b>$key: </b> $val<br>\n";
        }
    }

    if (!$out) {
        $out = "There are no notes yet.";
    }
    return($out);
}

function highlight($part, $whole) {
    /* if the string is in quotes, we know it is a phrase */
    $isPhrase = 0;

    $isPhrase = preg_match("/\"/", $part);

    if (strpos("\"", $part)) {
        $isPhrase = 1;
    }
    /* remove quotes used in strings by mysql */
    $part = str_replace("\"", "", $part);

    /* make all lowercase for array_diif */
    $partAry = array(strtolower($part));
    if ($isPhrase == 0) {
        $partAry = explode(" ", $part);
    }
    foreach ($partAry as $word) {
        $search_pattern = "/" . $word . "/si";
        $replace_with = "<span style='color:red;font-weight:bold'>" . $word . "</span>";
        $whole = preg_replace($search_pattern, $replace_with, $whole);
    }

    return($whole);
}

function getEdStatus($t) {
    if (!$t['proofed']) {
        return "<div class='notice'>status: UNPROOFED</div>";
    }
}

function showComment($t) {
    if (isset($t['comment'])) {
        $out = "<div class='label'>Comments</div>\n";
        $out .= "<div class='content comment' id='comment'>${t['comment']}</div>\n";
        return($out);
    }
}

function tryFopen($fileName, $mode) {
    try {
        $fp = fopen($fileName, $mode);
        if (!$fp) {
            throw new Exception('File open failed.');
        }
    } catch (Exception $e) {
        print "<div style='width:1000px'>";

        var_dump($fileName);
        print "<hr>";
        var_dump($e);
        print "</div>";
    }
    return($fp);
}

function pvar_dump($x) {
    print "<div style='width:1000px'>";
    var_dump($x);
    print "</div>";
}

function formatSearch($s, $searchStr) {
    $sary = array();
    $psary = array(64);
    for ($i = 0; $i < 64; $i++) {
        $psary[$i] = array();
    }

    $out = "";
    foreach ($s as $field => $topcat) { /* $field = 'judge_exp',$topcat = array of search results */
        foreach ($topcat as $p) { /* $p = colname associative arrays ($field amd pseq) */
            $pseq = $p['pseq'];

            $content = $p[$field];
            $tary = array($field => $content);
            if (isset($psary[$pseq])) {
                array_push($psary[$pseq], $tary);
            }
        }
    }


    $labels = array(
        'comment' => 'Comments',
        'title' => 'Title',
        'trans' => 'Translated Title',
        'trigrams' => 'Trigrams',
        'tri_upper' => 'Upper Trigram',
        'tri_lower' => 'Lower Trigram',
        'explanation' => 'Explanation of Trigrams',
        'judge_old' => 'Original Judgement',
        'judge_exp' => 'Explanation of the Judgment',
        'image_old' => 'Original Image Text',
        'image_exp' => 'Explanation of the Image',
        'line_1_org' => 'Original Line 1 Text',
        'line_1_exp' => 'Explanation on Line 1',
        'line_2_org' => 'Original Line 2 Text',
        'line_2_exp' => 'Explanation on Line 2',
        'line_3_org' => 'Original Line 3 Text',
        'line_3_exp' => 'Explanation on Line 3',
        'line_4_org' => 'Original Line 4 Text',
        'line_4_exp' => 'Explanation on Line 4',
        'line_5_org' => 'Original Line 5 Text',
        'line_5_exp' => 'Explanation on Line 5',
        'line_6_org' => 'Original Line 6 Text',
        'line_6_exp' => 'Explanation on Line 6'
    );

    $psary = array_filter($psary);
    /*
     * JWFIX I have a close DOV but no open, as that properly centers the page, but this means I 
     * have a div problem farther up :(  
     */
    $out .= "[<b>" . $searchStr . "</b>] found in " . count($psary) . " hexagrams</div>";
    for ($i = 0; $i < 64; $i++) {
        if (isset($psary[$i])) {
            $out .= "<div id='searchbox01'><img style='width:30px;margin:3px;' src='/images/hex/small/hexagram" . f($i) . ".png'><b><a target='_blank' href='http://babelbrowser.com/show.php?hex=" . $i . "'>" . $i . " / " . $GLOBALS['dbh']->getHexFieldByPseq("hexagrams", "trans", $i) . " </a></b>\n";
            foreach ($psary[$i] as $key => $ary) {
                $out .= "<div id='searchbox02'>" . $labels[key($ary)] . "\n";
                foreach ($ary as $field => $content) {
                    $out .= "<div id='searchbox03'>" . highlight($searchStr, $content) . "</div>\n";
                }
                $out .= "</div>\n";
            }
            $out .= "</div>\n";
        }
    }
    return($out);
}

function make_comparer() {
    /* Normalize criteria up front so that the comparer finds everything tidy */
    $criteria = func_get_args();
    foreach ($criteria as $index => $criterion) {
        $criteria[$index] = is_array($criterion) ? array_pad($criterion, 3, null) : array($criterion, SORT_ASC, null);
    }

    return function($first, $second) use (&$criteria) {
        foreach ($criteria as $criterion) {
            /* How will we compare this round? */
            list($column, $sortOrder, $projection) = $criterion;
            $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;

            /* If a projection was defined project the values now */
            if ($projection) {
                $lhs = call_user_func($projection, $first[$column]);
                $rhs = call_user_func($projection, $second[$column]);
            } else {
                $lhs = $first[$column];
                $rhs = $second[$column];
            }

            /* Do the actual comparison; do not return if equal */
            if ($lhs < $rhs) {
                return -1 * $sortOrder;
            } else if ($lhs > $rhs) {
                return 1 * $sortOrder;
            }
        }

        return 0; /* tiebreakers exhausted, so $first == $second.. (what does thismean?) */
    };
}

function mergeHex($t_image, $f_image) {

    /*
     * JWFIX hardcoded numbers ? :(
     */
    $x = 80;
    $y = 87;
    $png = imagecreatetruecolor($x * 3, $y);
    imagesavealpha($png, true);

    /*
     * JWFIX can't get th etansparency to work when I create the image... always black, so I set to white
     */

    $white = imagecolorallocate($png, 255, 255, 255);
    imagefill($png, 0, 0, $white);

    $firstUrl = $t_image;
    $secondUrl = $f_image;


    $outputImage = $png;

    $first = imagecreatefrompng($firstUrl);
    $second = imagecreatefrompng($secondUrl);

    imagecopymerge($outputImage, $first, 0, 0, 0, 0, $x, $y, 100);
    imagecopymerge($outputImage, $second, $x * 2, 0, 0, 0, $x, $y, 100);


    $uid = session_id();
    $fn = "/id/merge_${uid}.png";
    imagepng($outputImage, getRootDir() . $fn);
    imagedestroy($outputImage);
    return($fn);
}

function getServerPrefix() {

    $test_server_name = getTestServer();
    if (!isset($_SERVER['SERVER_NAME'])) { /* empty when running form (for testing) command line */
        $_SERVER['SERVER_NAME'] = $test_server_name;
    }

    return("http://" . $_SERVER['SERVER_NAME']);
}

function makeAlphaBox($x, $y) {
    $hexBox = imagecreatetruecolor($x, $y);
    imagesavealpha($hexBox, true);
    $white = imagecolorallocate($hexBox, 255, 255, 255);
    imagefill($hexBox, 0, 0, $white);
    return($hexBox);
}

function makeHexPng($t, $d, $f) {
    $ta = str_split($t);
    $fa = str_split($f);

    $x = 80; //width of a line
    $y = 11; // height of a line
    $y_border = 4;

    $newY = ($y * 6) + (5 * $y_border); // 6 lines plus borders
    /* same probl;am as above...
     * JWFIX can't get th etansparency to work when I create the image... always black, so I set to white
     */

    $hex1 = makeAlphaBox($x, $newY);


    /*     * **************************************************************
     * make hexagram 1 
     * ************************************************************** */
    $i = array();
    for ($k = 0; $k < 6; $k++) {
        if ($ta[$k] == 1) {          // line = yang
            if ($d[$k] == 1) {          // line = moving
                $i[$k] = getServerPrefix() . '/images/lines/9sm.png';
            } else {                    // line = static
                $i[$k] = getServerPrefix() . '/images/lines/7sm.png';
            }
        } else {                    // line = yin
            if ($d[$k] == 0) {          // line = static
                $i[$k] = getServerPrefix() . '/images/lines/8sm.png';
            } else {                    //  line = moving
                $i[$k] = getServerPrefix() . '/images/lines/6sm.png';
            }
        }
    }

    /* load files into array */
    $m = array();
    for ($k = 0; $k < 6; $k++) {
        $m[$k] = imagecreatefrompng($i[$k]);
    }
    /* stack on top of one another */
    for ($k = 0; $k < 6; $k++) {
        $dst_y = ($y * $k) + ($y_border * $k);
        imagecopymerge($hex1, $m[$k], 0, $dst_y, 0, 0, $x, $y, 100);
    }
    /* I use a UID to save to file ... JWFIX shoudl I use a session id instead? */
    $u = uniqid();

    /* make the filename for the temporary image */
    $hex1file = getRootDir() . "/id/hex1_tmp_" . session_id() . ".png";
    $hex1fileUrl = getServerPrefix() . "/id/hex1_tmp_" . session_id() . ".png";

    /* save the image */
    imagepng($hex1, $hex1file);

    /* clean up */
    imagedestroy($hex1);

    /*     * **************************************************************
     * make hexagram 2 
     * ************************************************************** */

    $hex2 = makeAlphaBox($x, $newY);

    $i = array();
    for ($k = 0; $k < 6; $k++) {
        if ($fa[$k] == 1) {
            $i[$k] = getServerPrefix() . '/images/lines/7sm.png';
        } else {
            $i[$k] = getServerPrefix() . '/images/lines/8sm.png';
        }
    }


    $m = array();
    for ($k = 0; $k < 6; $k++) {
        $m[$k] = imagecreatefrompng($i[$k]);
    }

    for ($k = 0; $k < 6; $k++) {
        $dst_y = ($y * $k) + ($y_border * $k);
        imagecopymerge($hex2, $m[$k], 0, $dst_y, 0, 0, $x, $y, 100);
    }

    /* make the filename for the temporary image */
    $hex2file = getRootDir() . "/id/hex2_tmp_" . session_id() . ".png";
    $hex2fileUrl = getServerPrefix() . "/id/hex2_tmp_" . session_id() . ".png";

    /* save the image */
    imagepng($hex2, $hex2file);

    /* clean up */
    imagedestroy($hex2);

    $isMovingLines = ($t != $f); /* 1 = moving lines, 0 = none */
    /* we make both hexes and mergethem, but if there is only one we just send back one */

    $finalFile = $hex1fileUrl;
    if ($isMovingLines) {
        $finalFile = getServerPrefix() . mergeHex($hex1fileUrl, $hex2fileUrl); /* mergeHex() does not rewturn URL, so do that here */
    }

    return(enlargeImage($finalFile, 2));
}

function enlargeImage($originalFile, $pct) {
    list($width, $height) = getimagesize($originalFile);

    $newWidth = $width * $pct;
    $uid = uniqid("tmp_") . "_" . session_id();
    /* assume png for now */
    $targetFile = getRootDir() . "/id/${uid}";


    $info = getimagesize($originalFile);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image_create_func = 'imagecreatefromjpeg';
            $image_save_func = 'imagejpeg';
            $new_image_ext = 'jpg';
            break;

        case 'image/png':
            $image_create_func = 'imagecreatefrompng';
            $image_save_func = 'imagepng';
            $new_image_ext = 'png';
            break;

        case 'image/gif':
            $image_create_func = 'imagecreatefromgif';
            $image_save_func = 'imagegif';
            $new_image_ext = 'gif';
            break;

        default:
            throw new Exception('Unknown image type.');
    }

    $img = $image_create_func($originalFile);
    list($width, $height) = getimagesize($originalFile);

    $newHeight = ($height / $width) * $newWidth;
    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    if (file_exists($targetFile)) {
        unlink($targetFile);
    }
    $image_save_func($tmp, "$targetFile.$new_image_ext");
    return("$targetFile.$new_image_ext");
}

/* * ******************************************************************* */
/* This is mainly an import of 'makemds.php' */
/* * ******************************************************************* */

function dbug($v, $force = null) {
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    if ((isset($_REQUEST['debugon'])) || ($force)) {
        $val = print_r($v, TRUE);
        print "<div id='dbug' >" . $caller['file'] . "=>" . $caller['line'] . "<pre style='font-weight:normal;font-size:8pt'>*$val*</pre></div>";
        return(TRUE);
    } else {
        return(FALSE);
    }
}

function makeMDfromTemplate($alldata) {
    /*
     * Set all the vars we need
     */
    $t = $alldata['t'];
    $d = $alldata['d'];
    $f = $alldata['f'];

    $hdate = $t['hdate'];
    $ddate = $t['ddate'];
    $question = $t['question'];

    $trx_pseq = $_SESSION['trx_pseq'];
    $txhex = $GLOBALS['dbh']->getHex($trx_pseq);

    $fpage = null;


    /*
     * make image of tossed and final hex, with colored moving lines, for pdf heading
     */
    $t_image = getServerPrefix() . "/id/hex1_tmp_" . session_id() . ".png";
    $f_image = getServerPrefix() . "/images/hex/small/hexagram" . f($f['pseq']) . ".png";

    /* makeHexPng() return the URL alrady */
    $m_image = makeHexPng($t['binary'], $d, $f['binary']); //makeHexPng();//mergeHex($t_image,$f_image);
    /*
     * load the template processing class
     */
    include(getRootDir() . "/book/templates/template.class.php");
    $type = 'pseq'; /* select the 'pseq' vale to search by */
    $cols = getcols(); /* get the column names from the database */

    /*
     * make sure they are 0 leading
     */
    $ftpseq = f($t['pseq']);
    $ffpseq = f($f['pseq']);

    $b_ftpseq = $GLOBALS['dbh']->getHexFieldByPseq("hexagrams", "binary", $ftpseq);
    $b_ffpseq = $GLOBALS['dbh']->getHexFieldByPseq("hexagrams", "binary", $ffpseq);

    $thex = getMergedData($_REQUEST['trans'], $b_ftpseq); /* this gets the first hex data from the database */
    $fhex = getMergedData($_REQUEST['trans'], $b_ffpseq); /* this gets the first hex data from the database */

    /*
     * create new template instances for each part of the final PDF
     */
    $page_title = new Template(getRootDir() . "/templates/pdf_title.tpl");
    $page_hex1 = new Template(getRootDir() . "/templates/pdf_hex1.tpl");
    $page_lines = new Template(getRootDir() . "/templates/pdf_lines.tpl");
    $page_hex2 = new Template(getRootDir() . "/templates/pdf_hex2.tpl");
    $page_trx = new Template(getRootDir() . "/templates/pdf_trx.tpl");

    /*
     * set the vars for the title template
     */
    $page_title->set("hdate", $hdate);
    $page_title->set("question", "'${question}'");
    $page_title->set("merged", $m_image);

    $tosstype = "";

    if ($_REQUEST['mode'] == "manual") {
        $tosstype = "Manually Selected Hexagrams";
    }
    if ($_REQUEST['mode'] == "plum") {
        $tosstype = "Modern Plum Method";
    }
    if ($_REQUEST['mode'] == "r-decay") {
        $tosstype = "Fermi Lab's 'HotBits' radioactive decay random number generator";
    }
    if ($_REQUEST['mode'] == "random.org") {
        $tosstype = "Random.org's random coin toss";
    }

    if ($_REQUEST['mode'] == "astro") {
        $tosstype = "Real-time planetary positions";
    }

    $page_title->set("tosstype", $tosstype);

    /*
     * set the vars for the transitional hexgram template
     */
    $page_trx->set("trx_judge_old", htmlize($txhex[0]['judge_old']));
    $page_trx->set("trx_judge_exp", htmlize($txhex[0]['judge_exp']));
    $trx_image = getServerPrefix() . "/images/hex/small/hexagram" . f($txhex[0]['pseq']) . ".png";
    $page_trx->set("trx_image", $trx_image);
    $page_trx->set("trx_transtitle", c($txhex[0]['pseq']) . " (" . c($txhex[0]['binary']) . " = " . c($txhex[0]['bseq']) . ") " . c($txhex[0]['trans']) . " / " . c($txhex[0]['title']));

    /*
     * JWFIX all the labels and  shoud probably be in a config file, with language support
     */
    $page_trx->set("trx_intro", "The moving lines are the lines that, due to their extreme condition, 'flip' to 
their opposite, and as a result, a new hexagram is created. Likewise, if we then subtract the binary value of the first hexagram from the 
final hexagram, we end up with a binary number that represents the difference 
between the two, and this binary number maps to yet another hexagram.  We call 
this hexagram 'transitional' as it a full hexagram that represent the moving lines. For the first and second hexagrams shown here, the transitional hexagram is ");
    $page_trx->set("trx_title", "The Transitioning Hexagram");
    $page_trx->set("label_judge_old", "The Judgment:");
    $page_trx->set("label_judge_exp", "An Explanation of the Judgment");

    /*
     * set the vars for the first hexagram template.
     * First we set all the label string values
     */

    $page_hex1->set("label_resulting_hex", "The Resulting Hexagram:");
    $page_hex1->set("label_hexagram", "Hexagram:");
    $page_hex1->set("label_binary", "Binary Sequence:");
    $page_hex1->set("label_dir", "Direction:");
    $page_hex1->set("label_upper_tri", "Upper trigram:");
    $page_hex1->set("label_lower_tri", "Lower trigram:");
    $page_hex1->set("label_explanation", "Explanation:");
    $page_hex1->set("label_judge_old", "The Judgment:");
    $page_hex1->set("label_judge_exp", "An Explanation of the Judgment");
    $page_hex1->set("label_image_old", "The 'IMAGE' of the hexagram");
    $page_hex1->set("label_image_exp", "An Explanation of the 'IMAGE'");

    $page_hex1->set("explanation_tho", "Tholonic Explanation:");
    $page_hex1->set("tri_upper_tho", "Tholonic Upper Trigram:");
    $page_hex1->set("tri_lower_tho", "Tholonic Lower Trigram:");
    $page_hex1->set("tri_upper_tho_ex", "Tholonic Upper Trigram Explanation:");
    $page_hex1->set("tri_lower_tho_ex", "Tholonic Lower Trigram Explanation:");
    /*
     * then we set the data values 
     */
    $page_hex1->set("t_image", htmlize($t_image));
    $page_hex1->set("t_id", f($thex[0]['pseq']));
    $page_hex1->set("t_trans", $thex[0]['trans']);
    $page_hex1->set("t_title", $thex[0]['title']);
    $page_hex1->set("t_transtitle", c($thex[0]['trans']) . " / " . c($thex[0]['title']));
    $page_hex1->set("t_pseq", f($thex[0]['pseq']));
    $page_hex1->set("t_bseq", f($thex[0]['bseq']));
    $page_hex1->set("t_binary", $thex[0]['binary']);
    $page_hex1->set("t_explanation", htmlize($thex[0]['explanation']));
    $page_hex1->set("t_tri_upper", htmlize($thex[0]['tri_upper']));
    $page_hex1->set("t_tri_lower", htmlize($thex[0]['tri_lower']));
    $page_hex1->set("t_judge_old", htmlize($thex[0]['judge_old']));
    $page_hex1->set("t_judge_exp", htmlize($thex[0]['judge_exp']));
    $page_hex1->set("t_image_old", htmlize($thex[0]['image_old']));
    $page_hex1->set("t_image_exp", htmlize($thex[0]['image_exp']));

    $page_hex1->set("explanation_tho", htmlize($thex[0]['explanation_tho']));
    $page_hex1->set("tri_upper_tho", htmlize($thex[0]['tri_upper_tho']));
    $page_hex1->set("tri_lower_tho", htmlize($thex[0]['tri_lower_tho']));
    $page_hex1->set("tri_upper_tho_ex", htmlize($thex[0]['tri_upper_tho_ex']));
    $page_hex1->set("tri_lower_tho_ex", htmlize($thex[0]['tri_lower_tho_ex']));
    /*
     * set isMovingLines flag
     */
    $isMovingLines = ($thex[0]['pseq'] != $fhex[0]['pseq']); /* 1 = moving lines, 0 = none */

    /*
     * set the label fro the moving lines
     */
    $movinglines = "There are no moving lines";
    $page_lines->set("movinglines", $movinglines);

    if ($isMovingLines) {
        $movinglines = "The Moving Lines";

        $page_lines->set("movinglines", $movinglines); /* override */
        /*
         * loop through the lines setting the vars using CSS to highlight the moving lines
         */
        for ($j = 0; $j < 6; $j++) {
            $i = 6 - $j;
            if ($d[$j]) {
                $page_lines->set("t_line_${i}", $thex[0]['line_' . $i]);
                $page_lines->set("t_line_${i}_org", htmlize($thex[0]['line_' . $i . '_org']));
                $page_lines->set("t_line_${i}_exp", htmlize($thex[0]['line_' . $i . '_exp']));
            } else {
                $page_lines->set("t_line_${i}", "<span style='color:darkgray'>" . $thex[0]['line_' . $i] . "</span>");
                $page_lines->set("t_line_${i}_org", "<span style='color:darkgray'>" . htmlize($thex[0]['line_' . $i . '_org']) . "</span>");
                $page_lines->set("t_line_${i}_exp", "<span style='color:darkgray'>" . htmlize($thex[0]['line_' . $i . '_exp']) . "</span>");
            }
        }

        /*
         * set vars for final hexagram
         */

        $page_hex2->set("label_resulting_hex", "The Resulting Hexagram:");
        $page_hex2->set("label_hexagram", "Hexagram:");
        $page_hex2->set("label_binary", "Binary Sequence:");
        $page_hex2->set("label_dir", "Direction:");
        $page_hex2->set("label_explanation", "Explanation:");
        $page_hex2->set("label_upper_tri", "Upper trigram:");
        $page_hex2->set("label_lower_tri", "Lower trigram:");
        $page_hex2->set("label_judge_old", "The Judgment:");
        $page_hex2->set("label_judge_exp", "An Explanation of the Judgment");
        $page_hex2->set("label_image_old", "The 'IMAGE' of the hexagram");
        $page_hex2->set("label_image_exp", "An Explanation of the 'IMAGE'");

        $page_hex2->set("explanation_tho", "Tholonic Explanation:");
        $page_hex2->set("tri_upper_tho", "Tholonic Upper Trigram:");
        $page_hex2->set("tri_lower_tho", "Tholonic Lower Trigram:");
        $page_hex2->set("tri_upper_tho_ex", "Tholonic Upper Trigram Explanation:");
        $page_hex2->set("tri_lower_tho_ex", "Tholonic Lower Trigram Explanation:");
        
        
        $page_hex2->set("f_image", $f_image);
        $page_hex2->set("f_id", f($fhex[0]['pseq']));
        $page_hex2->set("f_transtitle", c($fhex[0]['trans']) . " / " . c($fhex[0]['title']));
        $page_hex2->set("f_trans", $fhex[0]['trans']);
        $page_hex2->set("f_title", $fhex[0]['title']);
        $page_hex2->set("f_pseq", f($fhex[0]['pseq']));
        $page_hex2->set("f_bseq", f($fhex[0]['bseq']));
        $page_hex2->set("f_binary", "(" . $fhex[0]['binary'] . ")");
        $page_hex2->set("f_explanation", htmlize($fhex[0]['explanation']));
        $page_hex2->set("f_tri_upper", htmlize($fhex[0]['tri_upper']));
        $page_hex2->set("f_tri_lower", htmlize($fhex[0]['tri_lower']));
        $page_hex2->set("f_judge_old", htmlize($fhex[0]['judge_old']));
        $page_hex2->set("f_judge_exp", htmlize($fhex[0]['judge_exp']));
        $page_hex2->set("f_image_old", htmlize($fhex[0]['image_old']));
        $page_hex2->set("f_image_exp", htmlize($fhex[0]['image_exp']));

        $page_hex2->set("explanation_tho", htmlize($fhex[0]['explanation_tho']));
        $page_hex2->set("tri_upper_tho", htmlize($fhex[0]['tri_upper_tho']));
        $page_hex2->set("tri_lower_tho", htmlize($fhex[0]['tri_lower_tho']));
        $page_hex2->set("tri_upper_tho_ex", htmlize($fhex[0]['tri_upper_tho_ex']));
        $page_hex2->set("tri_lower_tho_ex", htmlize($fhex[0]['tri_lower_tho_ex']));

        /**
         * Loads our layout template, settings its title and content.
         * There is one layout template for one hex only, and one for 2 hexes
         */
        $layout = new Template(getRootDir() . "/templates/layout_moving.tpl");
        $layout->set("title", $page_title->output());
        $layout->set("hex1", $page_hex1->output());
        $layout->set("lines", $page_lines->output());
        $layout->set("hex2", $page_hex2->output());
        $layout->set("trx", $page_trx->output());
        $fpage = $layout->output();
    } else { /* there are no nomoving lines */
        $layout = new Template(getRootDir() . "/templates/layout_static.tpl");
        $layout->set("title", $page_title->output());
        $layout->set("hex1", $page_hex1->output());
        $fpage = $layout->output();
    }
    /**
     * Outputs the page.
     */
    $fpage = $layout->output();

    return($fpage);
}

/*
 * this is functions for makeMDfromTemplate()
 * JWFIX move this to the DataMapper class
 */

function getids($ary) {
    $dbh = new PDO('mysql:host=localhost;dbname=iching;charset=utf8mb4', 'ichingDBuser', '1q2w3e');
    $sql = "SELECT " . $ary['bseq'] . "," . $ary['pseq'] . " from hexagrams order by " . $ary['pseq'] . " asc";
    $sth = $dbh->prepare($sql);
    $sth->execute();
    $ids = $sth->fetchAll();
    return($ids);
}

/*
 * this is functions for makeMDfromTemplate()
 * JWFIX move this to the DataMapper class
 */

function getcols() {
    $dbh = new PDO('mysql:host=localhost;dbname=iching;charset=utf8mb4', 'ichingDBuser', '1q2w3e');
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

/*
 * this is functions for makeMDfromTemplate()
 * JWFIX move this to the DataMapper class
 */

//function mdgethex($pseq) {
//
//    $hex = $GLOBALS['dbh']->getDataAlt($pseq);
//    return($hex);
//}

/* * ******************************************************************** */

function saveToFile($t, $d, $f) {
//    
//    echo ">>>>>>>>>>>>>>>> IN SAVE2FILE";
//    var_dump($t);
//    var_dump($d);
//    var_dump($f);

    /* remove whitespces and extention from question to use as filename */
    $fname = "questions/" . mb_ereg_replace(" ", "_", $_REQUEST['question']);
    $fname = mb_ereg_replace("\?", "", $fname);
    $fname = mb_ereg_replace("\"", "", $fname);
    $fname = mb_ereg_replace("\'", "", $fname);
    $fname = mb_ereg_replace("\!", "", $fname);
    $fname = mb_ereg_replace("\,", "_", $fname);
    $fname .= "-" . $t['ddate'];


    $alldata = array(
        'question' => $_REQUEST['question'],
        't' => $t,
        'd' => $d,
        'f' => $f
    );

    /*
     * Now make the MarkDown file 
     */
    $out = makeMDfromTemplate($alldata);
    

    /*     * ************************************************** */
    /* make out filenames, and write the markdown to a file */
    /*     * ************************************************** */

    $outMd = getRootDir() . "/" . $fname . ".md";
    $outPdf = getRootDir() . "/" . $fname . ".pdf";
    $outHtml = getRootDir() . "/" . $fname . ".html";

    $f = tryFopen($outMd, "w");
    
    if (!is_writable($outMd)) {
       throw new RuntimeException('Unable to write file');
    }
    if (false === $f) {
       throw new RuntimeException('Unable to open log file for writing');
    }
    $bytes = fwrite($f, $out);
    
    fclose($f);
    
    $myfile = fopen($outMd, "r") or die("Unable to open file!");

    /*     * ************************************************** */
    /* convert MARKDOWN to HTML */
    /*     * ************************************************** */
    $markdown = file_get_contents($outMd);
    $markdownParser = new \Michelf\MarkdownExtra();
    $html = $markdownParser->transform($markdown);

    /*     * ************************************************** */
    /* add CSS to the HTML and save to file */
    /*     * ************************************************** */
    $cssfile = getServerPrefix() . "/css/pdf.css";
//    var_dump($cssfile);
    $html = "<html>\n<head>\n<link rel='stylesheet' type='text/css' href='$cssfile'>\n</head>\n<body>" . $html . "</body></html>";
//    $html = "<html>\n<head>\n</head>\n<body>" . $html . "</body></html>";

    $f = tryFopen($outHtml, "w");
    fwrite($f, $html);
    fclose($f);


    /*     * ************************************************** */
    /* load the HTML into a DOM parser and process any links */
    /*     * ************************************************** */
    $dom = \HTML5::loadHTML($html);
    $domain_name = $_SERVER['SERVER_NAME'];
    $links = htmlqp($dom, 'a');
    foreach ($links as $link) {
        $href = $link->attr('href');
        if (substr($href, 0, 1) == '/' && substr($href, 1, 1) != '/') {
            $link->attr('href', $domain_name . $href);
        }
    }
    $html = \HTML5::saveHTML($dom);

    $f = tryFopen($outHtml, "w");
    fwrite($f, $html);
    fclose($f);

    /*     * ************************************************** 
     * load the HTML into dompdf, render it and write it 
     * ************************************************** */

    /** !!! START - section was previously commenter out */
    //use Dompdf\Options;
    //$options = new Options();
    //$options->set('enable_html5_parser', true);
    //$dompdf = new Dompdf($options);

//    $dompdf = new \Dompdf\Dompdf();//  DOMPDF();
//    $dompdf->load_html($html);
//    //var_dump($dompdf);
//    $dompdf->render();
//    $output = $dompdf->output();
//    
//    $f = fopen($outPdf, "w");
//    fwrite($f, $output);
//    fclose($f);

    /** !!! END - section was previously commenter out */

    /*     * ************************************************** 
      /* have to mAke system call because dompdf is not Working
     * See docs on the more complicated aspects of doign this 
     * on a headless server :/  Needs virtual X11 frame buffers
     * ************************************************** */
    
    
    
    
    
    $call = getRootDir() . "/utils/makePdf.sh $outHtml $outPdf >> " . getRootDir() . "/log/wkhtmltopdf.log 2>&1";
//    $call = "nohup sudo -u " . getUser() . " " . $call . "  >> " . getRootDir() . "/log/wkhtmltopdf.log 2>&1";
//    $call = $call . "  >> " . getRootDir() . "/log/wkhtmltopdf.log 2>&1";
    
    
    $logcall = 'echo "' . $call . '" >> ' . getRootDir() . '/log/wkhtmltopdf.log 2>&1';

    system($logcall);

    system($call);

    /*
     * Save the final URL fro the download link on the homepage
     */
    $_SESSION['dlfile'] = getServerPrefix() . "/" . $fname . ".pdf";

//    unlink($outMd);
//    unlink($outHtml);

    return(TRUE);
}

function showFixes($t) {
    /* FIX is special column for editing notes.  Show any FIX commenst if they exist */
    if (isset($t['fix'])) {
        return("<div class='content btn btn-danger'>FIX :${t['fix']}</div>");
    }
}

function getDates() {
    /* calc dates for rpesenation and data */
    $dataDate = date("y.m.d.H.i.s", time()) . ".U"; /* 17.10.14.14.59.32.U */
    $humanDate = date("F d, l g:i:s A (T)", time()); /* October 14, Saturday 2:59:32 PM (UTC) */
    $dates = array('human' => $humanDate, 'data' => $dataDate,);
    return $dates;
}

function makeHuKua($t) {
    $h = array(0, 0, 0, 0, 0, 0);

    $h[5] = $t[4];
    $h[4] = $t[3];
    $h[3] = $t[2];
    $h[2] = $t[3];
    $h[1] = $t[2];
    $h[0] = $t[1];

    $bin = implode($h);

    $hex = $GLOBALS['dbh']->getHexFieldByBinary("hexagrams", "pseq", $bin);
//    var_dump($hex);
    return($hex);
}

function makeHex($tossed, $delta, $uid, $whichToFade) {
    $cssHex = new CssHex();
    $script = "";


    $out = "<div id='${uid}'>\n";
    /*
     * $hex1 = code that builds '$tossed' hex
     * $script = gathered code to print into page
     * $newHex = the resutl of $tossed and $delta
     */

    list($hex1, $script, $newHex) = $cssHex->drawHex($tossed, $delta, $script, 1, $uid);
    $out .= "<div id='tossed_${uid}' class='" . (($whichToFade == "fade_tossed") ? "faded" : "live") . "'>\n" . $hex1 . "</div>\n";
    $out .= "<div class='spacerbox'></div>\n";

    $a = implode($tossed);
    $b = implode($newHex);
    $a1 = bindec($a);
    $b1 = bindec($b);
    $c = ($b1 - $a1 < 0 ? ($b1 - $a1) + 63 : $b1 - $a1);
    $q = sprintf("%06d", decbin($c));
    $c1 = sprintf("%06d", $q);

    $Thex = str_split($c1);
    list($Thex2, $script, $TnewHex) = $cssHex->drawHex($Thex, array(0, 0, 0, 0, 0, 0), $script, 3, $uid);
    $out .= "<div  id='tossed_${uid}' class='trx_faded'>\n" . $Thex2 . "</div>\n";
    $out .= "<div class='spacerbox'></div>\n";

    list($hex2, $script, $newHex) = $cssHex->drawHex($newHex, array(0, 0, 0, 0, 0, 0), $script, 2, $uid);
    $out .= "<div  id='final_${uid}' class='" . (($whichToFade == "fade_final") ? "faded" : "live") . "'>\n" . $hex2 . "</div>\n";

    $tossed_pseq = $GLOBALS['dbh']->getHexFieldByBinary("hexagrams", "pseq", implode($tossed));
    $trx_pseq = $GLOBALS['dbh']->getHexFieldByBinary("hexagrams", "pseq", implode($Thex));
    $final_pseq = $GLOBALS['dbh']->getHexFieldByBinary("hexagrams", "pseq", implode($newHex));

    $_SESSION['trx_pseq'] = $trx_pseq;
    $tossed_bseq = $GLOBALS['dbh']->getHexFieldByBinary("hexagrams", "bseq", implode($tossed));
    $trx_bseq = $GLOBALS['dbh']->getHexFieldByBinary("hexagrams", "bseq", implode($Thex));
    $final_bseq = $GLOBALS['dbh']->getHexFieldByBinary("hexagrams", "bseq", implode($newHex));

    $tossed_trans = $GLOBALS['dbh']->getHexFieldByBinary("hexagrams", "trans", implode($tossed));
    $trx_trans = $GLOBALS['dbh']->getHexFieldByBinary("hexagrams", "trans", implode($Thex));
    $final_trans = $GLOBALS['dbh']->getHexFieldByBinary("hexagrams", "trans", implode($newHex));


    $t_sub = "<a target='_blank' href='http://babelbrowser.com/show.php?hex=${tossed_pseq}'><span class='st1'>$tossed_pseq</span></a><span> ($tossed_bseq)    </span><br><span class='st2'>$tossed_trans  <br>\n";
    $x_sub = "<a target='_blank' href='http://babelbrowser.com/show.php?hex=${trx_pseq}'><span class='st1'>$trx_pseq   </span></a><span> ($trx_bseq)       </span><br><span class='st2'>$trx_trans     <br>\n";
    $f_sub = "<a target='_blank' href='http://babelbrowser.com/show.php?hex=${final_pseq}'><span class='st1'>$final_pseq </span></a><span> ($final_bseq)     </span><br><span class='st2'>$final_trans  <br>\n";

    $out .= "</div>\n";
    $out .= "<div class='clear underHex' >"
            . "<table class='ttd'>"
            . "     <tr class='rtd'>"
            . "         <td class='htd'>"
            . "             $t_sub"
            . "         </td>"
            . "         <td class='htd'>"
            . "             $x_sub";
    if ($whichToFade == "fade_final") {
        $out .= "<br><a id='xsubtip' class='xsubtip' href='#'><img style='width:20px' src='/images/qmark-small-bw.png'/></a>";
    }
    $out .= "         </td>"
            . "         <td class='htd'>"
            . "             $f_sub"
            . "         </td>"
            . "     </tr>"
            . "</table>"
            . "</div>\n";


    $out .= "<script>\n$(document).ready(function () {\n" . $script . "});\n</script>\n";
    $out .= "</div>\n";

    return(array('hexes' => $out, 'tpseq' => $trx_pseq));
}

function getToss($trans = array("Wilhelm/Baynes")) {
    //dbug("in getToss",true);
    $tossed = tossit();

    $delta = array(0, 0, 0, 0, 0, 0);

    /* if we are using static number we have to do it diffenely */
    $newFinal = null;
    $newTossed = null;

    if (isset($_REQUEST['f_tossed'])) {
        $counter = 0;
        while ((count($newTossed) != 6) && ($counter < 3)) {
            $newTossed = str_split(sprintf("%06d", decbin($GLOBALS['dbh']->chex2bin($_REQUEST['f_tossed']))));
            $counter++;
        }
        $counter = 0;
        while ((count($newFinal) != 6) && ($counter < 3)) {
            $newFinal = str_split(sprintf("%06d", decbin($GLOBALS['dbh']->chex2bin($_REQUEST['f_final']))));
            $counter++;
        }

        if ($counter >= 3) {
            print ("<div stype='padding:15px;class='btn-danger'>count=" . count($newTossed) . "/" . count($newFinal) . "  Sorry, there is a problem somewhere.  Hit reset and try again</div> ");
            die();
        }
        for ($i = 0; $i < 6; $i++) {
            if (($newTossed[$i] != $newFinal[$i])) {
                if ($newFinal[$i] == 1) {   // if newFinal == 1 then newTossed == 0
                    $newTossed[$i] = 6;     // so newTossed mucgt be a moving YIN == 6
                    $delta[$i] = 1;         // and that is a delta
                    $newFinal[$i] = 9;      // and we change the newFinal accordingly, preserrving movement
                }
                if ($newFinal[$i] == 0) {   // if newFinal == 0 then newTossed == 1
                    $newTossed[$i] = 9;     // so newTossed must be a moving YANG == 9
                    $delta[$i] = 1;         // mark the delta
                    $newFinal[$i] = 6;      // change the new final to a YIN preserving movement
                }
            }
            if (($newTossed[$i] == $newFinal[$i])) {
                if ($newFinal[$i] == 1) {
                    $newTossed[$i] = 7;
                    $delta[$i] = 0;
                    $newFinal[$i] = 7;
                }
                if ($newFinal[$i] == 0) {
                    $newTossed[$i] = 8;
                    $delta[$i] = 0;
                    $newFinal[$i] = 8;
                }
            }
        }
        $tossed = $newTossed;

        /* it gets recalced later, so clear it */
        $delta = array(0, 0, 0, 0, 0, 0); //reset it 
    }

    /* back to the normal  processing */
    for ($i = 0; $i < 6; $i++) {
        if (($tossed[$i] == 6) || ($tossed[$i] == 9)) {
            $delta[$i] = 1;
        }
    }

    $final = getFinal($tossed);

    /* override if static */
    if (isset($_REQUEST['f_final'])) {
        $final = $newFinal;
    }

    $tossed_bin = tobin($tossed); /* convert (6,7,8,9) arrays to (1,0) arrays */
    $final_bin = tobin($final);

    /* JWFIX move to db class */

    $tossedData = getMergedData($trans, $tossed_bin);
    $finalData = getMergedData($trans, $final_bin);

    //dbug($tossedData,true);
    //dbug($finalData,true);

    $res = array(
        'tossed' => $tossedData
        , 'delta' => $delta
        , 'final' => $finalData
    );
    return($res);
}

function getMergedData($trans, $tf_bin) {

    $tfData = null;


    /* first get the defauly trans which will always be the first in the array */
//    if ($trans[0] == "Wilhelm/Baynes") {
    if (in_array("Wilhelm/Baynes", $trans)) {
        $tfData = $GLOBALS['dbh']->getData($tf_bin);
        //$tfData = mergeResults($tfData, $tfData0,"Wilhelm/Baynes");
    }
    //if ($trans[0] == "Duncan Stroud") {
    if (in_array("Duncan Stroud", $trans)) {
        $tfData1 = $GLOBALS['dbh']->getNotesData($tf_bin);
        $tfData = mergeResults($tfData, $tfData1, "Duncan Stroud");
    }
    //if ($trans[0] == "Qabalah") {
    if (in_array("Qabalah", $trans)) {
        $tfData2 = $GLOBALS['dbh']->getQabalahData($tf_bin);


        $tfData = mergeResults($tfData, $tfData2, "Qabalah");
    }
//    if ($trans[0] == "short") {
    if (in_array("short", $trans)) {
        $tfData = $GLOBALS['dbh']->getShortData($tf_bin);
    }
    /* remove the first from the first element */
//print "<div style='width:1000px'>";    var_dump($tfData);    print "</div>";
    unset($trans[0]);

    /*    if (in_array("Wilhelm/Baynes", $trans)) {
      $_tf = $GLOBALS['dbh']->getData($tf_bin);
      $tfData = mergeResults($tfData, $_tf,"Wilhelm/Baynes");
      }
      if (in_array("Duncan Stroud", $trans)) {
      $_tf = $GLOBALS['dbh']->getNotesData($tf_bin);
      $tfData = mergeResults($tfData, $_tf,"Duncan Stroud");
      }
      if (in_array("Qabalah", $trans)) {
      $_tf = $GLOBALS['dbh']->getQabalahData($tf_bin);
      $tfData = mergeResults($tfData, $_tf,"Qabalah");
      }
     */
    /* we ignore "short" because that is a handeled differently */
    return($tfData);
}

function getTri() {
    $sql = "SELECT * FROM trigrams";
    return($GLOBALS['dbh']->getData($sql));
}

function getFinal($tossed) {
    $final = $tossed;
    $delta = array(0, 0, 0, 0, 0, 0);
    for ($i = 0; $i < 6; $i++) {
        if ($tossed[$i] == 6) {
            $final[$i] = 9;
        }
        if ($tossed[$i] == 9) {
            $final[$i] = 6;
        }
    }
    return($final);
}

function tobin($ary) {
    $bstr = "";
    $cvt = array('6' => 0, '7' => 1, '8' => 0, '9' => 1);
    for ($i = 0; $i < 6; $i++) {
        try {
            $bstr .= ($cvt[$ary[$i]]);
        } catch (exception $e) {
            echo $e->getMessage();
            echo "tobin(".$ary.")";
        }
    }
    return($bstr);
}

function tossit() {
    $tosser = new Tosser();

    if (isset($_REQUEST['f_tossed'])) {
        /* return anything as it will get overwritten by the manually entered vals in getToss(); */
        $r = array(6, 7, 8, 9, 6, 7);
        return($r);
    }
    if (isset($_REQUEST['mode'])) {
        if ($_REQUEST['mode'] == "plum") {
            $r = $tosser->getPlum();
            return($r);
        }
        if ($_REQUEST['mode'] == "r-decay") {
            $r = $tosser->getHotBits();
            return($r);
        }
        if ($_REQUEST['mode'] == "random.org") {
            $r = $tosser->getRandomOrg();
        }

        if ($_REQUEST['mode'] == "astro") {
            $r = $tosser->getAstro();
        }
        return($r);
    }
}

function oldlogout($t, $str = null) {
    $dumpStr = str_replace("\n", '<br />', var_export($t, TRUE));
    $dumpStr = str_replace("\"", '\'', $dumpStr);
    $dumpStr = "<b>" . $str . "</b><hr>" . $dumpStr;
    $debugBlock = <<<EOX
    <script>
    $(document).ready(function () {
        $("#debug").prepend("${dumpStr}");
    });    
    </script>  
EOX;
    echo $debugBlock;
}

function c_sub($fr, $to) {
    $r = $to - $fr;
    echo "<span class='smallinfo'>$to-$fr (=" . $r . ")";
    if ($r < 0) {
        $r = $r + 63;
        echo " +63 ";
    }
    echo " = $r</span><br>";
    return($r);
}

function outProc1($a, $j) {
    echo "<a href='?consult.php?hex=" . $a[$j]['pseq'] . "'><img class='heximg' src='images/hex/hexagram" . f($a[$j]['pseq']) . ".png'>" . $a[$j]['pseq'] . " [b:" . $a[$j]['bseq'] . "] / " . $a[$j]['title'] . " / " . $a[$j]['trans'] . "</a>";
}

function logout($t) {
    $dumpStr = str_replace("\n", '<br />', var_export($t, TRUE));
    $dumpStr = str_replace("\"", '\'', $dumpStr);
    $debugBlock = <<<EOX
    <script>
    $(document).ready(function () {
        $("#debug").html("${dumpStr}");
    });    
    </script>  
EOX;
    echo $debugBlock;
}

function f($n) {
    return(sprintf("%02d", $n));
}

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

function secondsToTime($ss) {
    $s = $ss % 60;
    $m = floor(($ss % (60 * 60)) / (60));
    $h = floor(($ss % (60 * 60 * 24)) / (60 * 60));
    $d = floor(($ss % (60 * 60 * 24 * 30)) / (60 * 60 * 24));
    $M = floor(($ss % (60 * 60 * 24 * 30 * 12)) / (60 * 60 * 24 * 30));
    $Y = floor($ss / (60 * 60 * 24 * 30 * 12));
    return sprintf("%010d", $ss) . " = $Y years, $M months, $d days, $h hours, $m minutes, $s seconds\n";
}

/* see gitbook doc for an explanation of how this workls */

function putBtnExpand() {
    echo ""
    . "<span>"
    . "<a id ='btnEC' style='text-decoration: none' class='accordion-expand-all' href='#'>[+]</a>"
    . "</span>\n";
}

function putBtnEdit($bseq) {
    echo ""
    . "<span>"
    . "<a target='_blank' href='/cignite/index.php/main/hexagrams/edit/" . $bseq . "' target='_blank'>"
    . "<img class='uibtn'  src='/images/btn_edit.png'>"
    . "</a>"
    . "</span>\n";
}

function putBtnUpdate($bseq) {
    echo ""
    . "<span>"
    . "<a target='_blank' href='/cignite/index.php/main/notes/edit/" . $bseq . "' target='_blank'>"
    . "<img class='uibtn' src='/images/btn_update.png'>"
    . "</a>"
    . "</span>\n";
}

function putBtnUpdateQ($bseq) {
    $pathnum = $GLOBALS['dbh']->getPairsPathnumByDesBseq($bseq);

    echo ""
    . "<span>"
    . "<a target='_blank' href='/cignite/index.php/main/pairs/edit/" . $pathnum . "' target='_blank'>"
    . "<img class='uibtn' src='/images/btn_updateQ.png'>"
    . "</a>"
    . "</span>\n";
}

function putBtnSmTxt() {
    echo ""
    . "<span>"
    . "<img class='uibtn' id='larger1'  src='/images/btn_smalltxt.png'>"
    . "</span>\n";
}

function putBtnMedTxt() {
    echo ""
    . "<span>"
    . "<img class='uibtn' id='larger2'  src='/images/btn_medtxt.png'>"
    . "</span>\n";
}

function putBtnLgTxt() {
    echo ""
    . "<span>"
    . "<img class='uibtn' id='larger3'  src='/images/btn_lgtxt.png'>"
    . "</span>\n";
}

function c($s) {
    /* see ->   https://www.functions-online.com/preg_replace.html */
    $r = preg_replace('/<p>\s*(.*)\s*<\/p>\s*$/s', '$1', $s);
    return($r);
}

function htmlize($s) {
    /*  see ->   https://www.functions-online.com/preg_replace.html */
    $r = preg_replace("/\r/", "", $s);
    $r = preg_replace("/(\n)/", '<br/>$1', $r);
    $r = preg_replace("/(\n\n)/", '$1<p></p>$1', $r);

    return($r);
}

function mergeResults($prev, $new, $map) {
//  dbug($map,true);
//    dbug($prev,true);
//    dbug($new,true);
    $r = $prev;

    if (!isset($prev[0])) {
        /* OOPS!  there is a problem.  bouce! */
        dbug("OOPS!  There was a major error :(  missing 'prev[0]'; not set; functions.php:mergeResults():~1273; Start again from the  <a href='/'>homepage</a>", true);
    }
    $pd = $prev[0];  // BREAKPOINT -> Notice : Undefined offset: 0 in 

    $f = array();
    if (($map == "Wilhelm/Baynes") || ($map == "Duncan Stroud")) {
        /* there are the fields we can ,merge */
        $f = array(
            "comment",
            //"title",
            //"trans",
            "trigrams",
            "tri_upper",
            "tri_lower",
            "explanation",
            "judge_old",
            "judge_exp",
            "image_old",
            "image_exp",
            "line_1_org",
            "line_1_exp",
            "line_2_org",
            "line_2_exp",
            "line_3_org",
            "line_3_exp",
            "line_4_org",
            "line_4_exp",
            "line_5_org",
            "line_5_exp",
            "line_6_org",
            "line_6_exp"
        );

//        $styles_open = array("<div style='color:brown'>");
//        $styles_close = array("</div><div style='clear:both'></div>\n");
        //dbug($new,true);
        foreach ($pd as $key => $val) {
            if (in_array($key, $f)) {
                //$r[0][$key] .= "<HR><b>Duncan Commentary</b><br/>".$styles_open[0] . $new[0][$key] . $styles_close[0];

                /*
                 * addition translations would be add as...
                 * $r[0][$key] .= $styles_open[1] . $new[1][$key] . $styles_close[1];
                 * 
                 */
            }
        }
    }
    if ($map == "Qabalah") {
        //dbug($new,true);
        /* there are the fields we can merge */
        $f = array(
            "judge_exp" => array('des_meaning')
        );
//        $styles_open = array("<p style='color:green' id='t1' style='color:green'>");
//        $styles_close = array("</p>\n");

        if (!isset($new[0]['theme'])) {
            /* OOPS!  there is a problem.  bouce! */
            dbug("OOPS! There was a major error :( ; missing 'new[0]['theme']:functions.php:mergeResults():~1332: Start again from the  <a href='/'>homepage</a>", true);
        }
        $pathnum = $new[0]['pathnum'];
        $theme = $new[0]['theme'];
        $tarot = $new[0]['tarot'];
        $assiah = $new[0]['assiah'];
        $type = $new[0]['type'];
        $des_balance_desc = $new[0]['des_balance_desc'];
        $asc_balance_desc = $new[0]['asc_balance_desc'];
        $des_balance = $new[0]['des_balance'];
        $asc_balance = $new[0]['asc_balance'];
        $des_meaning = $new[0]['des_meaning'];
        $asc_meaning = $new[0]['asc_meaning'];
        $des_pseq = $new[0]['des_pseq'];
        $des_name = $new[0]['des_name'];
        $asc_pseq = $new[0]['asc_pseq'];
        $asc_name = $new[0]['asc_name'];
        $poetic = $prev[0]['pseq'];
        $poetic_opposite = $GLOBALS['dbh']->getHexnumOppositeByPseq($poetic);

//        $opseq = $GLOBALS['dbh']->getHexnumOppositeByPseq($prev[0]['pseq']) ;

        $des_movement = ($des_balance > $asc_balance ? $des_balance_desc : $asc_balance_desc);
        $asc_movement = ($des_balance < $asc_balance ? $des_balance_desc : $asc_balance_desc);
        $tarot_image = "images/tarot/t" . $pathnum . ".jpg"; //this is teh same for both opposites


        $str = "<HR><b>Qabalah/Tarot Equivalent (of hexagram pairs) pairs</b> (coming soon-ish)<br/>";

//dbug($poetic." == ".$des_pseq,true);        
/*
        if ($poetic == $des_pseq) { //check if this is descending val or not
            if (file_exists($tarot_image)) {
                $str .= "<div><img style='margin-right:10px;float:left;height:250px' src='http://babelbrowser.com/$tarot_image'><img style='margin-right:10px;float:left;height:250px;  transform: rotateX(180deg);' src='http://babelbrowser.com/$tarot_image'></div>";
            }

            $str .= "<p style='color:green'>"; //$styles_open[0];
            $str .= "This hexagram, <b><a target='_blank' href='http://babelbrowser.com/show.php?hex=${des_pseq}'>${des_name}</a></b>, ";
            $str .= "is the <b>descending</b> quality of the theme <b>'${theme}'</b>, and is expressed in the Tarot as the '<b>${tarot}</b>' card, which is associated with the <b>${type}</b> via <b>${assiah}</b>. ";
            $str .= "It is <b>${des_movement}</b> (with <b>${des_balance}</b> passive lines and <b>${asc_balance}</b> strong lines), and carries the message '<b>${des_meaning}</b>'. ";
            $str .= "</p>";

            if (file_exists($tarot_image)) {
                $str .= "<div style='clear:both'></div>";
            }

            $qab_image = "images/qab/q" . $poetic . ".png";
            $qab__opposite_image = "images/qab/q" . $poetic_opposite . ".png";

//dbug("DES:\n$tarot_image\n$qab_image\n$qab__opposite_image",true);

            if (file_exists($qab_image) && file_exists($qab__opposite_image)) {
                $str .= "<div  style='float:left;margin:10px;'><img style='width:100px' src='http://babelbrowser.com/$qab_image'><img style='width:100px' src='http://babelbrowser.com/$qab__opposite_image'></div>";
            }
            $str .= "<p style='color:green'>"; //$styles_open[0];
            $str .= "It is the opposite of <b><a target='_blank' href='http://babelbrowser.com/show.php?hex=${asc_pseq}'>${asc_name}</a></b>, which carries the message '<b>${asc_meaning}</b>'. ";
            $str .= "</p>";

            if (file_exists($qab_image) && file_exists($qab__opposite_image)) {
                $str .= "<div style='clear:both'></div>";
            }
        } else { // this is the ASASCENDING
            if (file_exists($tarot_image)) {
                $str .= "<div><img style='margin-right:10px;float:left;height:250px' src='http://babelbrowser.com/$tarot_image'><img style='margin-right:10px;float:left;height:250px;  transform: rotateX(180deg);' src='http://babelbrowser.com/$tarot_image'></div>";
            }

            $str .= "<p style='color:green'>"; //$styles_open[0];
            $str .= "This hexagram, <b><a target='_blank' href='http://babelbrowser.com/show.php?hex=${asc_pseq}'>${asc_name}</a></b>, ";
            $str .= "is the <b>ascending</b> quality of the theme <b>'${theme}'</b>, and is expressed in the Tarot as the '<b>${tarot}</b>' card, which is associated with the <b>${type}</b> via <b>${assiah}</b>. ";
            $str .= "It is <b>${asc_movement}</b> (with <b>${asc_balance}</b> passive lines and <b>${des_balance}</b> strong lines),  and carries the message '<b>${asc_meaning}</b>'.";
            $str .= "</p>\n"; //$styles_close[0];

            if (file_exists($tarot_image)) {
                $str .= "<div style='clear:both'></div>";
            }

            $qab_image = "images/qab/q" . $poetic . ".png";
            $qab__opposite_image = "images/qab/q" . $poetic_opposite . ".png";

//dbug("ASC:\n$tarot_image\n$qab_image\n$qab__opposite_image",true);

            if (file_exists($qab_image) && file_exists($qab__opposite_image)) {
                $str .= "<div  style='float:left;margin:10px;'><img style='width:100px' src='http://babelbrowser.com/$qab_image'><img style='width:100px' src='http://babelbrowser.com/$qab__opposite_image'></div>";
            }
            $str .= "<p  style='color:green'>"; //$styles_open[0];
            $str .= "It is the opposite of <b><a target='_blank' href='http://babelbrowser.com/show.php?hex=${des_pseq}'>${des_name}</a></b>, which carries the message '<b>${des_meaning}</b></p>'. ";
            //$str .= "</p>"; //$styles_close[0];
        }
        
       
 */       
        

        $r[0]['judge_exp'] .=  $str ;

        foreach ($f as $hkey => $qvals) {
            foreach ($qvals as $qkey) {

                //dbug("r[0][$hkey] = new[0][$qkey]",true);
                //$r[0][$hkey] .= $styles_open[0] . $new[0][$qkey] . $styles_close[0];
                /* addition translations would be add as...
                 * $r[0][$key] .= $styles_open[1] . $new[1][$key] . $styles_close[1];
                 */
            }
        }
    }
    return($r);
}

//function mergeField($field, $datas) {
//    $v = "";
//    for ($i = 0; $i < count($datas); $i++) {
//
//        if ($i == 0) {
//            $d = $datas[$i];
//            $v1 = $d[$field];
//            $v .= $v1;
//        }
//        if ($i == 1) {
//            $d = $datas[$i];
//            $v1 = $d[$field];
//            $v1 .= "<br><b style='color:red'>" . $v1 . "</b><br>";
//            $v .= $v1;
//        }
//    }
//    return(htmlize($v));
//}


function makeSHortText($t, $f, $d) {
    $short = "<div id='here2' class='container container-top'><b>";
    $short .= "<span style='color:red'>The Current Situation</span><br/>";
    $short .= $t['explanation'] . " ";
    $short .= $t['judge_old'] . " ";
    $short .= $t['judge_exp'] . " ";


    if ($t['bseq'] != $f['bseq']) {
        $short .= "<br/><br/><span style='color:red'>What Changes...</span><br/>";
        $short .= "<ul>\n";
        for ($i = 5; $i >= 0; $i--) {
            if ($d[$i] == 1) {
                $j = 6 - $i;
                $short .= "<li>" . $t['line_' . $j . '_org'] . " " . $t['line_' . $j . '_exp'] . "</li>\n";
            }
        }
        $short .= "</ul>\n";
        $short .= "<span style='color:red'>The Resulting Situation</span><br/>";
        $short .= $f['explanation'] . " ";
        $short .= $f['judge_old'] . " ";
        $short .= $f['judge_exp'] . " ";
    }
    $short .= "</b></div>";
    return($short);
}
