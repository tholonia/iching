<?php

/* Edited top work with PHP7 :JWX */
if (!isset($_SESSION)) {
    session_start();
}

include("constants_eng.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/charting/mysqli_connect_online_calcs_db_MYSQLI.php");
//require_once($_SERVER['DOCUMENT_ROOT'] .  "/charting/my_functions_MYSQLI.php");

$rx1 = mysqlSafeEscapeString($conn, $_GET["rx1"]);

$ubt1 = intval(mysqlSafeEscapeString($conn, $_GET["ubt1"]));

$longitude = $_SESSION['nL1'];
$hc1 = $_SESSION['nhc1'];

$longitude[LAST_PLANET + 1] = $hc1[1];
$longitude[LAST_PLANET + 2] = $hc1[10];


$height_cut_down = 0;
if ($ubt1 != 0) {
    $height_cut_down = 115;
}


// set the content-type
header("Content-type: image/png");

// create the blank image
$overall_size = 450;
$extra_width = 265;   //was 255 but that cut out the R - in order to make total width = 680
$margins = 20;        //left and right margins on the background graphic

$im = @imagecreatetruecolor($overall_size + $extra_width, $overall_size - $height_cut_down) or die("Cannot initialize new GD image stream");

// specify the colors
$white = imagecolorallocate($im, 255, 255, 255);
$red = imagecolorallocate($im, 255, 0, 0);
$blue = imagecolorallocate($im, 0, 0, 255);
$magenta = imagecolorallocate($im, 255, 0, 255);
$yellow = imagecolorallocate($im, 255, 255, 0);
$cyan = imagecolorallocate($im, 0, 255, 255);
$green = imagecolorallocate($im, 0, 224, 0);
$grey = imagecolorallocate($im, 127, 127, 127);
$black = imagecolorallocate($im, 0, 0, 0);
$lavender = imagecolorallocate($im, 160, 0, 255);
$orange = imagecolorallocate($im, 255, 127, 0);
$light_blue = imagecolorallocate($im, 239, 255, 255);
$background_color = imagecolorallocate($im, 192, 208, 255);

$pl_name[0] = "Sun";
$pl_name[1] = "Moon";
$pl_name[2] = "Mercury";
$pl_name[3] = "Venus";
$pl_name[4] = "Mars";
$pl_name[5] = "Jupiter";
$pl_name[6] = "Saturn";
$pl_name[7] = "Uranus";
$pl_name[8] = "Neptune";
$pl_name[9] = "Pluto";
$pl_name[10] = "Chiron";
$pl_name[11] = "Lilith";    //add a planet
$pl_name[12] = "True Node";
$pl_name[13] = "P. of Fortune";
$pl_name[14] = "Vertex";
$pl_name[15] = "Ascendant";
$pl_name[16] = "Midheaven";

$pl_glyph[0] = 81;
$pl_glyph[1] = 87;
$pl_glyph[2] = 69;
$pl_glyph[3] = 82;
$pl_glyph[4] = 84;
$pl_glyph[5] = 89;
$pl_glyph[6] = 85;
$pl_glyph[7] = 73;
$pl_glyph[8] = 79;
$pl_glyph[9] = 80;
$pl_glyph[10] = 77;
$pl_glyph[11] = 96;   //add a planet
$pl_glyph[12] = 141;
$pl_glyph[13] = 60;
$pl_glyph[14] = 109;    //Vertex
$pl_glyph[15] = 90;   //Ascendant
$pl_glyph[16] = 88;   //Midheaven

$asp_color[1] = $blue;
$asp_color[2] = $red;
$asp_color[3] = $green;
$asp_color[4] = $magenta;
$asp_color[5] = $cyan;
$asp_color[6] = $orange;

$asp_glyph[1] = 113;    //  0 deg
$asp_glyph[2] = 119;    //180 deg
$asp_glyph[3] = 101;    //120 deg
$asp_glyph[4] = 114;    // 90 deg
$asp_glyph[5] = 111;    //150 deg
$asp_glyph[6] = 116;    // 60 deg

$sign_glyph[1] = 97;
$sign_glyph[2] = 115;
$sign_glyph[3] = 100;
$sign_glyph[4] = 102;
$sign_glyph[5] = 103;
$sign_glyph[6] = 104;
$sign_glyph[7] = 106;
$sign_glyph[8] = 107;
$sign_glyph[9] = 108;
$sign_glyph[10] = 122;
$sign_glyph[11] = 120;
$sign_glyph[12] = 99;

$cell_width = 25;
$cell_height = 25;

$last_planet_num = LAST_PLANET + 2;       //add a planet
$num_planets = $last_planet_num + 1;

$left_margin_planet_table = ($num_planets + 0.5) * $cell_width;

if ($ubt1 == 0) {
    $number_to_use = $last_planet_num;
} else {
    $number_to_use = $last_planet_num - 4;
}


// ------------------------------------------
// create rectangle on blank image
imagefilledrectangle($im, 0, 0, $overall_size + $extra_width, $overall_size - $height_cut_down, $background_color);    //was $white
// MUST BE HERE - I DO NOT KNOW WHY - MAYBE TO PRIME THE PUMP
imagettftext($im, 10, 0, 0, 0, $black, './arial.ttf', " ");

// ------------------------------------------
// draw the grid - horizontal lines
for ($i = 0; $i <= $number_to_use - 1; $i++) {
    imageline($im, $margins, $cell_height * ($i + 1), $margins + $cell_width * ($i + 1), $cell_height * ($i + 1), $black);
}


if ($ubt1 == 0) {
    imageline($im, $margins, $cell_height * $num_planets, $margins + $cell_width * $i, $cell_height * $num_planets, $black);
} else {
    imageline($im, $margins, $cell_height * ($num_planets - 4), $margins + $cell_width * $i, $cell_height * ($num_planets - 4), $black);
}


// draw the grid - vertical lines
for ($i = 1; $i <= $number_to_use; $i++) {
    if ($ubt1 == 0) {
        imageline($im, $margins + $cell_width * $i, $cell_height * $num_planets, $margins + $cell_width * $i, $cell_height * $i, $black);
    } else {
        imageline($im, $margins + $cell_width * $i, $cell_height * ($num_planets - 4), $margins + $cell_width * $i, $cell_height * $i, $black);
    }
}


if ($ubt1 == 0) {
    imageline($im, $margins, $cell_height * $num_planets, $margins, $cell_height, $black);
} else {
    imageline($im, $margins, $cell_height * ($num_planets - 4), $margins, $cell_height, $black);
}


// ------------------------------------------
// draw in the planet glyphs
for ($i = 0; $i <= $last_planet_num; $i++) {
    if ($ubt1 == 0 Or ( $ubt1 != 0 And $i <= SE_TNODE)) {
        drawboldtext($im, 18, 0, $margins + $i * $cell_width, $cell_height * ($i + 1), $black, './HamburgSymbols.ttf', chr($pl_glyph[$i]), 0);

        // display planet data in the right-hand table
        drawboldtext($im, 16, 0, $margins + $left_margin_planet_table, $cell_height * ($i + 1), $black, './HamburgSymbols.ttf', chr($pl_glyph[$i]), 0);
        imagettftext($im, 10, 0, $margins + $left_margin_planet_table + $cell_width * 2, $cell_height * ($i + 1) - 3, $blue, './arial.ttf', $pl_name[$i]);
        $sign_num = floor($longitude[$i] / 30) + 1;
        drawboldtext($im, 14, 0, $margins + $left_margin_planet_table + $cell_width * 5, $cell_height * ($i + 1), $black, './HamburgSymbols.ttf', chr($sign_glyph[$sign_num]), 0);

        imagettftext($im, 10, 0, $margins + $left_margin_planet_table + $cell_width * 6, $cell_height * ($i + 1) - 3, $blue, './arial.ttf', Convert_Longitude($longitude[$i]) . " " . (isset($rx1[$i]) ? $rx1[$i] : 0));
    }
}

// ------------------------------------------
// display the aspect glyphs in the aspect grid
for ($i = 0; $i <= $last_planet_num - 1; $i++) {
    for ($j = $i + 1; $j <= $last_planet_num; $j++) {
        $q = 0;
        $da = Abs($longitude[$i] - $longitude[$j]);

        if ($da > 180) {
            $da = 360 - $da;
        }

        // set orb - 8 if Sun or Moon, 6 if not Sun or Moon
        $orb = 6;
        if ($i == 0 Or $i == 1 Or $j == 0 Or $j == 1) {
            $orb = 8;
        }

        // is there an aspect within orb?
        if ($da <= $orb) {
            $q = 1;
        } elseif (($da <= (60 + $orb)) And ( $da >= (60 - $orb))) {
            $q = 6;
        } elseif (($da <= (90 + $orb)) And ( $da >= (90 - $orb))) {
            $q = 4;
        } elseif (($da <= (120 + $orb)) And ( $da >= (120 - $orb))) {
            $q = 3;
        } elseif (($da <= (150 + $orb)) And ( $da >= (150 - $orb))) {
            $q = 5;
        } elseif ($da >= (180 - $orb)) {
            $q = 2;
        }

        if ($q > 0) {
            if ($ubt1 == 0 Or ( $ubt1 != 0 And $i <= SE_TNODE And $j <= SE_TNODE)) {
                drawboldtext($im, 14, 0, $margins + $cell_width * ($i + 0.20), $cell_height * ($j + 1 - 0.20), $asp_color[$q], './HamburgSymbols.ttf', chr($asp_glyph[$q]), 0);
            }
        }
    }
}


// draw the image in png format - using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);
exit();

Function mysqlSafeEscapeString($conn, $string) {
// replace HTML tags '<>' with '[]'
    $temp1 = str_replace("<", "[", $string);
    $temp2 = str_replace(">", "]", $temp1);

// but keep <br> or <br />
// turn <br> into <br /> so later it will be turned into ""
// using just <br> will add extra blank lines
    $temp1 = str_replace("[br]", "<br />", $temp2);
    $temp2 = str_replace("[br /]", "<br />", $temp1);

    if (get_magic_quotes_gpc()) {
        return $temp2;
    } else {
        return mysqli_real_escape_string($conn, $temp2);
    }
}

Function Convert_Longitude($longitude) {
    $signs = array(0 => 'Ari', 'Tau', 'Gem', 'Can', 'Leo', 'Vir', 'Lib', 'Sco', 'Sag', 'Cap', 'Aqu', 'Pis');

    $sign_num = floor($longitude / 30);
    $pos_in_sign = $longitude - ($sign_num * 30);
    $deg = floor($pos_in_sign);
    $full_min = ($pos_in_sign - $deg) * 60;
    $min = floor($full_min);
    $full_sec = round(($full_min - $min) * 60);

    if ($deg < 10) {
        $deg = "0" . $deg;
    }

    if ($min < 10) {
        $min = "0" . $min;
    }

    if ($full_sec < 10) {
        $full_sec = "0" . $full_sec;
    }

    return $deg . " " . $signs[$sign_num] . " " . $min . "' " . $full_sec . chr(34);
}

Function drawboldtext($image, $size, $angle, $x_cord, $y_cord, $clr_to_use, $fontfile, $text, $boldness) {
    $_x = array(1, 0, 1, 0, -1, -1, 1, 0, -1);
    $_y = array(0, -1, -1, 0, 0, -1, 1, 1, 1);

    for ($n = 0; $n <= $boldness; $n++) {
        ImageTTFText($image, $size, $angle, $x_cord + $_x[$n], $y_cord + $_y[$n], $clr_to_use, $fontfile, $text);
    }
}

?>
