<?php

if (!isset($_SESSION)) {
    session_start();
}
/* Edited top work with PHP7 :JWX */
include ('header_right_now.html');

require_once($_SERVER['DOCUMENT_ROOT'] . "/charting/mysqli_connect_online_calcs_db_MYSQLI.php");

// calculate astronomic data
$swephsrc = './sweph';    //sweph MUST be in a folder no less than at this level
$sweph = './sweph';

// Unset any variables not initialized elsewhere in the program
unset($PATH, $out, $pl_name, $longitude1, $speed1);

//get date and time right now
$date_now = date("Y-m-d");

$inmonth = gmdate("m");
$inday = gmdate("d");
$inyear = gmdate("Y");

$inhours = gmdate("H");
$inmins = gmdate("i");
$insecs = "0";

$intz = 0;

// adjust date and time for minus hour due to time zone taking the hour negative
$utdatenow = strftime("%d.%m.%Y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
$utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

putenv("PATH=" . getenv("PATH") . ":$swephsrc");

// get LAST_PLANET planets
$cmd = "swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -fls -g, -head";
exec($cmd, $out);
//  exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -fls -g, -head", $out);
// Each line of output data from swetest is exploded into array $row, giving these elements:
// 0 = longitude
// 1 = speed
// planets are index 0 - index (LAST_PLANET)
foreach ($out as $key => $line) {
    $row = explode(',', $line);
    $longitude1[$key] = $row[0];
    $speed1[$key] = $row[1];
};


include("constants_eng.php");     // this is here because we must rename the planet names
//add a planet - maybe some code needs to be put here
//display right now data
echo "<center>";

//  echo "<FONT color='#0000ff' SIZE='3' FACE='Arial'>";
//  echo "<b>Transits</b><br />";
//  echo '<b>On ' . strftime("%A, %B %d, %Y<br>%X (time zone = GMT)</b><br />\n", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
//  echo "</font>";

$line1 = "Transits on " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT)", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

echo "</center>";

$rx1 = "";
for ($i = 0; $i <= SE_TNODE; $i++) {
    if ($speed1[$i] < 0) {
        $rx1 .= "R";
    } else {
        $rx1 .= " ";
    }
}

$rx2 = $rx1;

// no need to urlencode unless perhaps magic quotes is ON (??)
$_SESSION['right_now_p1'] = $longitude1;

$wheel_width = 640;
$wheel_height = $wheel_width + 50;    //includes space at top of wheel for header

$qdata1 = array(
    'rx1' => $rx1,
    'l1' => $line1
);
$wargs1 = http_build_query($qdata1);

$_SESSION['wargs1'] = $wargs1;
//var_dump("right_now_wheel.php?${wargs1}");

echo "<center>";
echo "<img border='0' src='right_now_wheel.php?${wargs1}' height='$wheel_height'>";
//echo "<img border='0' src='right_now_wheel.php?rx1=$rx1&l1=$line1' width='$wheel_width' height='$wheel_height'>";
echo "</center>";
echo "<br>";

//display right now data
echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

echo '<tr>';
echo "<td><font color='#0000ff'><b> Planet </b></font></td>";
echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
echo '</tr>';

for ($i = 0; $i <= SE_TNODE; $i++) {
    echo '<tr>';
    echo "<td>" . $pl_name[$i] . "</td>";
    echo "<td><font face='Courier New'>" . Convert_Longitude($longitude1[$i]) . " " . Mid($rx1, $i + 1, 1) . "</font></td>";
    echo '</tr>';
}

echo '<tr>';
echo "<td> &nbsp </td>";
echo "<td> &nbsp </td>";
echo '</tr>';

echo '</table></center>';
echo "<br />";


// display right now data - aspect table
echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

echo '<tr>';
echo "<td><font color='#0000ff'><b> Planet </b></font></td>";
echo "<td><font color='#0000ff'><b> Aspect </b></font></td>";
echo "<td><font color='#0000ff'><b> Planet </b></font></td>";
echo "<td><font color='#0000ff'><b> Orb </b></font></td>";
echo '</tr>';

for ($i = 0; $i <= SE_TNODE; $i++) {
    echo "<tr><td colspan='4'>&nbsp;</td></tr>";
    for ($j = 0; $j <= SE_TNODE; $j++) {
        $q = 0;
        $da = Abs($longitude1[$i] - $longitude1[$j]);

        if ($da > 180) {
            $da = 360 - $da;
        }

        // set orb - 8 if Sun or Moon, 6 if not Sun or Moon
        if ($i == SE_POF Or $j == SE_POF) {
            $orb = 3;
        } elseif ($i == SE_LILITH Or $j == SE_LILITH) {
            $orb = 3;
        } elseif ($i == SE_TNODE Or $j == SE_TNODE) {
            $orb = 3;
        } elseif ($i == SE_VERTEX Or $j == SE_VERTEX) {
            $orb = 3;
        } elseif ($i == SE_SUN Or $i == SE_MOON Or $j == SE_SUN Or $j == SE_MOON) {
            $orb = 3;
        } else {
            $orb = 3;
        }

        // is there an aspect within orb?
        if ($da <= $orb) {
            $q = 1;
            $dax = $da;
        } elseif (($da <= (60 + $orb)) And ( $da >= (60 - $orb))) {
            $q = 6;
            $dax = $da - 60;
        } elseif (($da <= (90 + $orb)) And ( $da >= (90 - $orb))) {
            $q = 4;
            $dax = $da - 90;
        } elseif (($da <= (120 + $orb)) And ( $da >= (120 - $orb))) {
            $q = 3;
            $dax = $da - 120;
        } elseif (($da <= (150 + $orb)) And ( $da >= (150 - $orb))) {
            $q = 5;
            $dax = $da - 150;
        } elseif ($da >= (180 - $orb)) {
            $q = 2;
            $dax = 180 - $da;
        }

        if ($q > 0 And $i != $j) {
            // aspect exists
            echo '<tr>';
            echo "<td>" . $pl_name[$i] . "</td>";
            echo "<td>" . $asp_name[$q] . "</td>";
            echo "<td>" . $pl_name[$j] . "</td>";
            echo "<td>" . sprintf("%.2f", abs($dax)) . "</td>";
            echo '</tr>';
        }
    }
}

echo '</table></center>';
echo "<br /><br />";


// update count
$sql = "SELECT transits_right_now FROM astro_reports";
$result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
$row = mysqli_fetch_array($result);
$count = $row['transits_right_now'] + 1;

$sql = "UPDATE astro_reports SET transits_right_now = '$count'";
$result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


echo "<br /><br />";

include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
exit();

Function left($leftstring, $leftlength) {
    return(substr($leftstring, 0, $leftlength));
}

Function Reduce_below_30($longitude) {
    $lng = $longitude;

    while ($lng >= 30) {
        $lng = $lng - 30;
    }

    return $lng;
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

Function mid($midstring, $midstart, $midlength) {
    return(substr($midstring, $midstart - 1, $midlength));
}

?>
