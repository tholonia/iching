<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    exit();
  }

  require_once($_SERVER['DOCUMENT_ROOT'] . "/charting/mysqli_connect_online_calcs_db_MYSQLI.php");
  require_once($_SERVER['DOCUMENT_ROOT'] .  "/charting/my_functions_MYSQLI.php");

  // check if the form has been submitted
  if (isset($_POST['submitted']) Or isset($_POST['h_sys_submitted']))
  {
    $id1 = mysqlSafeEscapeString($conn, $_POST["id1"]);
    $id2 = mysqlSafeEscapeString($conn, $_POST["id2"]);

    if (!is_numeric($id1) Or !is_numeric($id2))
    {
      echo "<center><br /><br />You have forgotten to make an entry. Please try again.</center>";
      include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
      exit();
    }

    $username = $_SESSION['username'];

    //get data for person #1
    $sql = "SELECT * FROM astro_birth_info WHERE ID='$id1' And entered_by='$username'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $num_records = MYSQLI_NUM_rows($result);

    if ($num_records != 1)
    {
      echo "<center><br /><br />I cannot find person #1 in the database. Please try again.</center>";
      include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
      exit();
    }

    // get all variables from database
    $h_sys = mysqlSafeEscapeString($conn, $_POST["h_sys"]);
    $name1 = $row['name'];

    $month1 = $row['month'];
    $day1 = $row['day'];
    $year1 = $row['year'];

    $hour1 = $row['hour'];
    $minute1 = $row['minute'];

    $timezone1 = $row['timezone'];

    $long_deg1 = $row['long_deg'];
    $long_min1 = $row['long_min'];
    $ew1 = $row['ew'];

    $lat_deg1 = $row['lat_deg'];
    $lat_min1 = $row['lat_min'];
    $ns1 = $row['ns'];

    //get data for person #2
    $sql = "SELECT * FROM astro_birth_info WHERE ID='$id2' And entered_by='$username'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $num_records = MYSQLI_NUM_rows($result);

    if ($num_records != 1)
    {
      echo "<center><br /><br />I cannot find person #2 in the database. Please try again.</center>";
      include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
      exit();
    }

    // get all variables from database
    $name2 = $row['name'];

    $month2 = $row['month'];
    $day2 = $row['day'];
    $year2 = $row['year'];

    $hour2 = $row['hour'];
    $minute2 = $row['minute'];

    $timezone2 = $row['timezone'];

    $long_deg2 = $row['long_deg'];
    $long_min2 = $row['long_min'];
    $ew2 = $row['ew'];

    $lat_deg2 = $row['lat_deg'];
    $lat_min2 = $row['lat_min'];
    $ns2 = $row['ns'];


    include ('header_davison.html');        //here because of setting cookies above

    $h_sys = mysqlSafeEscapeString($conn, $_POST["h_sys"]);

    if ($ew1 < 0)
    {
      $ew1_txt = "w";
    }
    else
    {
      $ew1_txt = "e";
    }

    if ($ns1 > 0)
    {
      $ns1_txt = "n";
    }
    else
    {
      $ns1_txt = "s";
    }


    if ($ew2 < 0)
    {
      $ew2_txt = "w";
    }
    else
    {
      $ew2_txt = "e";
    }

    if ($ns2 > 0)
    {
      $ns2_txt = "n";
    }
    else
    {
      $ns2_txt = "s";
    }


    if (( isset($my_error) ?: "" ) != "") 
    {

    }
    else
    {
      // no errors in filling out form, so process form
      $swephsrc = './sweph';    //sweph MUST be in a folder no less than at this level
      $sweph = './sweph';

      putenv("PATH=".getenv('PATH').":$swephsrc");

      if (strlen($h_sys) != 1)
      {
        $h_sys = "p";
      }

//Person 1 data
      // Unset any variables not initialized elsewhere in the program
      unset($PATH,$out,$pl_name,$longitude1,$house_pos1);

      $inmonth1 = $month1;
      $inday1 = $day1;
      $inyear1 = $year1;

      $inhours1 = $hour1;
      $inmins1 = $minute1;
      $insecs1 = "0";

      $intz1 = $timezone1;

      $my_longitude1 = $ew1 * ($long_deg1 + ($long_min1 / 60));
      $my_latitude1 = $ns1 * ($lat_deg1 + ($lat_min1 / 60));

      if ($intz1 >= 0)
      {
        $whole1 = floor($intz1);
        $fraction1 = $intz1 - floor($intz1);
      }
      else
      {
        $whole1 = ceil($intz1);
        $fraction1 = $intz1 - ceil($intz1);
      }

      $inhours1 = $inhours1 - $whole1;
      $inmins1 = $inmins1 - ($fraction1 * 60);

      // adjust date and time for minus hour due to time zone taking the hour negative
      $utdatenow1 = strftime("%d.%m.%Y", mktime($inhours1, $inmins1, $insecs1, $inmonth1, $inday1, $inyear1));
      $utnow1 = strftime("%H:%M:%S", mktime($inhours1, $inmins1, $insecs1, $inmonth1, $inday1, $inyear1));


//Person 2 data
      $inmonth2 = $month2;
      $inday2 = $day2;
      $inyear2 = $year2;

      $inhours2 = $hour2;
      $inmins2 = $minute2;
      $insecs2 = "0";

      $intz2 = $timezone2;

      $my_longitude2 = $ew2 * ($long_deg2 + ($long_min2 / 60));
      $my_latitude2 = $ns2 * ($lat_deg2 + ($lat_min2 / 60));

      if ($intz2 >= 0)
      {
        $whole2 = floor($intz2);
        $fraction2 = $intz2 - floor($intz2);
      }
      else
      {
        $whole2 = ceil($intz2);
        $fraction2 = $intz2 - ceil($intz2);
      }

      $inhours2 = $inhours2 - $whole2;
      $inmins2 = $inmins2 - ($fraction2 * 60);

      // adjust date and time for minus hour due to time zone taking the hour negative
      $utdatenow2 = strftime("%d.%m.%Y", mktime($inhours2, $inmins2, $insecs2, $inmonth2, $inday2, $inyear2));
      $utnow2 = strftime("%H:%M:%S", mktime($inhours2, $inmins2, $insecs2, $inmonth2, $inday2, $inyear2));


      $my_longitude3 = ($my_longitude1 + $my_longitude2) / 2;
      $my_latitude3 = ($my_latitude1 + $my_latitude2) / 2;

      $jd1 = gregoriantojd($inmonth1, $inday1, $inyear1) - 0.5 + ($inhours1 / 24) + ($inmins1 / 1440);
      $jd2 = gregoriantojd($inmonth2, $inday2, $inyear2) - 0.5 + ($inhours2 / 24) + ($inmins2 / 1440);

      $jd3 = ($jd1 + $jd2) / 2;

//echo "jd1 = " . $jd1 . "<br>";
//echo "intz1 = " . $intz1 . "<br>";
//echo "whole1 = " . $whole1 . "<br>";
//echo "inhours1 = " . $inhours1 . "<br>";
//echo "minute1 = " . $minute1 . "<br>";
//echo "fraction1 = " . $fraction1 . "<br>";
//echo "inmins1 = " . $inmins1 . "<br>";

//echo "jd2 = " . $jd2 . "<br>";
//echo "intz2 = " . $intz2 . "<br>";
//echo "whole2 = " . $whole2 . "<br>";
//echo "inhours2 = " . $inhours2 . "<br>";
//echo "minute2 = " . $minute2 . "<br>";
//echo "fraction2 = " . $fraction2 . "<br>";
//echo "inmins2 = " . $inmins2 . "<br>";
//echo "jd3 = " . $jd3 . "<br>";

      exec ("swetest -edir$sweph -bj$jd3 -p0123456789DAttt -eswe -house$my_longitude3,$my_latitude3,$h_sys -flsj -g, -head", $out);

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = longitude
      // 1 = speed
      // 2 = house position
      // planets are index 0 - index (LAST_PLANET), house cusps are index (LAST_PLANET + 1) - (LAST_PLANET + 12)
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $longitude1[$key] = $row[0];
        $speed1[$key] = $row[1];
        $house_pos1[$key] = (isset($row[2]) ? $row[2] : null);
      };

      include("constants_eng.php");     // this is here because we must rename the planet names

      //calculate the Part of Fortune
      //is this a day chart or a night chart?
      if ($longitude1[LAST_PLANET + 1] > $longitude1[LAST_PLANET + 7])
      {
        if ($longitude1[0] <= $longitude1[LAST_PLANET + 1] And $longitude1[0] > $longitude1[LAST_PLANET + 7])
        {
          $day_chart = True;
        }
        else
        {
          $day_chart = False;
        }
      }
      else
      {
        if ($longitude1[0] > $longitude1[LAST_PLANET + 1] And $longitude1[0] <= $longitude1[LAST_PLANET + 7])
        {
          $day_chart = False;
        }
        else
        {
          $day_chart = True;
        }
      }

      if ($day_chart == True)
      {
        $longitude1[SE_POF] = $longitude1[LAST_PLANET + 1] + $longitude1[1] - $longitude1[0];
      }
      else
      {
        $longitude1[SE_POF] = $longitude1[LAST_PLANET + 1] - $longitude1[1] + $longitude1[0];
      }

      if ($longitude1[SE_POF] >= 360)
      {
        $longitude1[SE_POF] = $longitude1[SE_POF] - 360;
      }

      if ($longitude1[SE_POF] < 0)
      {
        $longitude1[SE_POF] = $longitude1[SE_POF] + 360;
      }

//add a planet - maybe some code needs to be put here

      //capture the Vertex longitude
      $longitude1[LAST_PLANET] = $longitude1[LAST_PLANET + 16];   //Asc = +13, MC = +14, RAMC = +15, Vertex = +16


//get house positions of planets here
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= LAST_PLANET; $y++)
        {
          $pl = $longitude1[$y] + (1 / 36000);
          if ($x < 12 And $longitude1[$x + LAST_PLANET] > $longitude1[$x + LAST_PLANET + 1])
          {
            If (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[$x + LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos1[$y] = $x;
              continue;
            }
          }

          if ($x == 12 And ($longitude1[$x + LAST_PLANET] > $longitude1[LAST_PLANET + 1]))
          {
            if (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos1[$y] = $x;
            }
            continue;
          }

          if (($pl >= $longitude1[$x + LAST_PLANET]) And ($pl < $longitude1[$x + LAST_PLANET + 1]) And ($x < 12))
          {
            $house_pos1[$y] = $x;
            continue;
          }

          if (($pl >= $longitude1[$x + LAST_PLANET]) And ($pl < $longitude1[LAST_PLANET + 1]) And ($x == 12))
          {
            $house_pos1[$y] = $x;
          }
        }
      }


//display natal data
      $secs = "0";
      if ($timezone1 < 0)
      {
        $tz1 = $timezone1;
      }
      else
      {
        $tz1 = "+" . $timezone1;
      }

      if ($timezone2 < 0)
      {
        $tz2 = $timezone2;
      }
      else
      {
        $tz2 = "+" . $timezone2;
      }

      $name_without_slashes = stripslashes($name1);

      echo "<center>";

      $name2_without_slashes = stripslashes($name2);

      $line1 = $name_without_slashes . ", born " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz1 hours)", mktime($hour1, $minute1, 0, $month1, $day1, $year1));
      $line1 = $line1 . " at " . $long_deg1 . $ew1_txt . sprintf("%02d", $long_min1) . " and " . $lat_deg1 . $ns1_txt . sprintf("%02d", $lat_min1);

      $line2 = $name2_without_slashes . ", born " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz2 hours)", mktime($hour2, $minute2, 0, $month2, $day2, $year2));
      $line2 = $line2 . " at " . $long_deg2 . $ew2_txt . sprintf("%02d", $long_min2) . " and " . $lat_deg2 . $ns2_txt . sprintf("%02d", $lat_min2);

      $tz3 = ($tz1 + $tz2) / 2;
      $relationship_date = ConvertJDtoDateandTime($jd3, $tz3);

      $line3 = "Davison Relationship Data for " . strftime('%A, %B %d, %Y at %H:%M:%S', strtotime($relationship_date)) . " (time zone = GMT $tz3 hours)";
      $line3 = $line3 . " at " . sprintf("%.3f", $my_longitude3) . " and " . sprintf("%.3f", $my_latitude3);

?>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <select name="h_sys" size="1">
          <?php
          echo "<option value='p' ";
          if ($h_sys == "p"){ echo " selected"; }
          echo "> Placidus </option>";

          echo "<option value='k' ";
          if ($h_sys == "k"){ echo " selected"; }
          echo "> Koch </option>";

          echo "<option value='r' ";
          if ($h_sys == "r"){ echo " selected"; }
          echo "> Regiomontanus </option>";

          echo "<option value='c' ";
          if ($h_sys == "c"){ echo " selected"; }
          echo "> Campanus </option>";

          echo "<option value='b' ";
          if ($h_sys == "b"){ echo " selected"; }
          echo "> Alcabitus </option>";

          echo "<option value='o' ";
          if ($h_sys == "o"){ echo " selected"; }
          echo "> Porphyrius </option>";

          echo "<option value='m' ";
          if ($h_sys == "m"){ echo " selected"; }
          echo "> Morinus </option>";

          echo "<option value='a' ";
          if ($h_sys == "a"){ echo " selected"; }
          echo "> Equal house - Asc </option>";

          echo "<option value='t' ";
          if ($h_sys == "t"){ echo " selected"; }
          echo "> Topocentric </option>";

          echo "<option value='v' ";
          if ($h_sys == "v"){ echo " selected"; }
          echo "> Vehlow </option>";
          ?>
        </select>

        <input type="hidden" name="id1" value="<?php echo $_POST['id1']; ?>">
        <input type="hidden" name="id2" value="<?php echo $_POST['id2']; ?>">
        <input type="hidden" name="name1" value="<?php echo stripslashes($_POST['name1']); ?>">
        <input type="hidden" name="month1" value="<?php echo $_POST['month1']; ?>">
        <input type="hidden" name="day1" value="<?php echo $_POST['day1']; ?>">
        <input type="hidden" name="year1" value="<?php echo $_POST['year1']; ?>">
        <input type="hidden" name="hour1" value="<?php echo $_POST['hour1']; ?>">
        <input type="hidden" name="minute1" value="<?php echo $_POST['minute1']; ?>">
        <input type="hidden" name="timezone1" value="<?php echo $_POST['timezone1']; ?>">
        <input type="hidden" name="long_deg1" value="<?php echo $_POST['long_deg1']; ?>">
        <input type="hidden" name="long_min1" value="<?php echo $_POST['long_min1']; ?>">
        <input type="hidden" name="ew1" value="<?php echo $_POST['ew1']; ?>">
        <input type="hidden" name="lat_deg1" value="<?php echo $_POST['lat_deg1']; ?>">
        <input type="hidden" name="lat_min1" value="<?php echo $_POST['lat_min1']; ?>">
        <input type="hidden" name="ns1" value="<?php echo $_POST['ns1']; ?>">

        <input type="hidden" name="name2" value="<?php echo stripslashes($_POST['name2']); ?>">
        <input type="hidden" name="month2" value="<?php echo $_POST['month2']; ?>">
        <input type="hidden" name="day2" value="<?php echo $_POST['day2']; ?>">
        <input type="hidden" name="year2" value="<?php echo $_POST['year2']; ?>">
        <input type="hidden" name="hour2" value="<?php echo $_POST['hour2']; ?>">
        <input type="hidden" name="minute2" value="<?php echo $_POST['minute2']; ?>">
        <input type="hidden" name="timezone2" value="<?php echo $_POST['timezone2']; ?>">
        <input type="hidden" name="long_deg2" value="<?php echo $_POST['long_deg2']; ?>">
        <input type="hidden" name="long_min2" value="<?php echo $_POST['long_min2']; ?>">
        <input type="hidden" name="ew2" value="<?php echo $_POST['ew2']; ?>">
        <input type="hidden" name="lat_deg2" value="<?php echo $_POST['lat_deg2']; ?>">
        <input type="hidden" name="lat_min2" value="<?php echo $_POST['lat_min2']; ?>">
        <input type="hidden" name="ns2" value="<?php echo $_POST['ns2']; ?>">

        <input type="hidden" name="h_sys_submitted" value="TRUE">
        <INPUT type="submit" name="submit" value="Go" align="middle" style="background-color:#66ff66;color:#000000;font-size:16px;font-weight:bold">
      </form>
<?php
      echo "</center>";

      $hr_ob1 = $hour1;
      $min_ob1 = $minute1;

      $ubt1 = 0;
      if (($hr_ob1 == 12) And ($min_ob1 == 0))
      {
        $ubt1 = 1;        // this person has an unknown birth time
      }

      $hr_ob2 = $hour2;
      $min_ob2 = $minute2;

      $ubt2 = 0;
      if (($hr_ob2 == 12) And ($min_ob2 == 0))
      {
        $ubt2 = 1;        // this person has an unknown birth time
      }

      if ($ubt1 == 1 Or $ubt2 == 1)
      {
        $ubt1 = 1;
        $ubt2 = 1;
      }

      $rx1 = "";
      for ($i = 0; $i <= SE_TNODE; $i++)
      {
        if ($speed1[$i] < 0)
        {
          $rx1 .= "R";
        }
        else
        {
          $rx1 .= " ";
        }
      }

      $rx2 = $rx1;


      // to make GET string shorter (for IE6)
      for ($i = 0; $i <= LAST_PLANET; $i++)
      {
        $L1[$i] = $longitude1[$i];
        $L2[$i] = $longitude2[$i];
      }


      // save house cusp data
      for ($i = 1; $i <= LAST_PLANET; $i++)
      {
        $hc1[$i] = $longitude1[LAST_PLANET + $i];
      }


// no need to urlencode unless perhaps magic quotes is ON (??)
    $_SESSION['davison_p1'] = $L1;
    $_SESSION['davision_hc1'] = $hc1;
    $_SESSION['davision_house_pos1'] = $house_pos1;

    $wheel_width = 640;
    $wheel_height = $wheel_width + 50;    //includes space at top of wheel for header

    echo "<center>";
    echo "<img border='0' src='davison_wheel.php?rx1=$rx1&rx2=$rx2&ubt1=$ubt1&ubt2=$ubt2&l1=$line1&l2=$line2&l3=$line3' width='$wheel_width' height='$wheel_height'>";
    echo "<br><br>";
    echo "<img border='0' src='davison_aspect_grid.php?rx1=$rx1&rx2=$rx2&ubt1=$ubt1&ubt2=$ubt2' width='705' height='450'>";
    echo "</center>";
    echo "<br>";


// display Davison relationship data - planets
    echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">',"\n";

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Planet </b></font></td>";
    echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
    if ($ubt1 == 1)
    {
      echo "<td>&nbsp;</td>";
    }
    else
    {
      echo "<td><font color='#0000ff'><b> House<br>position </b></font></td>";
    }
    echo '</tr>';

    if ($ubt1 == 1)
    {
      $a1 = SE_TNODE;
    }
    else
    {
      $a1 = LAST_PLANET;
    }

    for ($i = 0; $i <= $a1; $i++)
    {
      echo '<tr>';
      echo "<td>" . $pl_name[$i] . "</td>";
      echo "<td><font face='Courier New'>" . Convert_Longitude($longitude1[$i]) . " " . Mid($rx1, $i + 1, 1) . "</font></td>";

        if ($ubt1 == 1)
        {
          echo "<td>&nbsp;</td>";
        }
        else
        {
          $hse = floor($house_pos1[$i]);
          if ($hse < 10)
          {
            echo "<td>&nbsp;&nbsp;&nbsp;&nbsp; " . $hse . "</td>";
          }
          else
          {
            echo "<td>&nbsp;&nbsp;&nbsp;" . $hse . "</td>";
          }
        }
        echo '</tr>';
      }

    echo '<tr>';
    echo "<td> &nbsp </td>";
    echo "<td> &nbsp </td>";
    echo "<td> &nbsp </td>";
    echo "<td> &nbsp </td>";
    echo '</tr>';

// display Davison relationship data - house cusps
    if ($ubt1 == 0)
    {
      echo '<tr>';
      echo "<td><font color='#0000ff'><b> House </b></font></td>";
      echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
      echo "<td> &nbsp </td>";
      echo '</tr>';

      for ($i = LAST_PLANET + 1; $i <= LAST_PLANET + 12; $i++)
      {
        echo '<tr>';
        if ($i == LAST_PLANET + 1)
        {
          echo "<td>Ascendant </td>";
        }
        elseif ($i == LAST_PLANET + 10)
        {
          echo "<td>MC (Midheaven) </td>";
        }
        else
        {
          echo "<td>House " . ($i - LAST_PLANET) . "</td>";
        }
        echo "<td><font face='Courier New'>" . Convert_Longitude($longitude1[$i]) . "</font></td>";
        echo "<td> &nbsp </td>";
        echo '</tr>';
      }
    }

    echo '</table></center>',"\n";
    echo "<br /><br />";


// display Davison relationship data - aspect table
    $asp_name[1] = "Conjunction";
    $asp_name[2] = "Opposition";
    $asp_name[3] = "Trine";
    $asp_name[4] = "Square";
    $asp_name[5] = "Quincunx";
    $asp_name[6] = "Sextile";

    echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Planet 1</b></font></td>";
    echo "<td><font color='#0000ff'><b> Aspect </b></font></td>";
    echo "<td><font color='#0000ff'><b> Planet 2</b></font></td>";
    echo "<td><font color='#0000ff'><b> Orb </b></font></td>";
    echo '</tr>';

    // include Ascendant and MC
    $longitude1[LAST_PLANET + 1] = $hc1[1];
    $longitude1[LAST_PLANET + 2] = $hc1[10];

    $pl_name[LAST_PLANET + 1] = "Ascendant";
    $pl_name[LAST_PLANET + 2] = "Midheaven";


    if ($ubt1 == 1)
    {
      $a1 = SE_TNODE;
    }
    else
    {
      $a1 = LAST_PLANET + 2;
    }

    for ($i = 0; $i <= $a1; $i++)
    {
      echo "<tr><td colspan='4'>&nbsp;</td></tr>";
      for ($j = $i + 1; $j <= $a1; $j++)
      {
        $q = 0;
        $da = Abs($longitude1[$i] - $longitude1[$j]);

        if ($da > 180)
        {
          $da = 360 - $da;
        }

        // set orb - 8 if Sun or Moon, 6 if not Sun or Moon
          if ($i == SE_POF Or $j == SE_POF)
          {
            $orb = 2;
          }
          elseif ($i == SE_LILITH Or $j == SE_LILITH)
          {
            $orb = 3;
          }
          elseif ($i == SE_TNODE Or $j == SE_TNODE)
          {
            $orb = 3;
          }
          elseif ($i == SE_VERTEX Or $j == SE_VERTEX)
          {
            $orb = 3;
          }
          elseif ($i == SE_SUN Or $i == SE_MOON Or $j == SE_SUN Or $j == SE_MOON)
          {
            $orb = 8;
          }
          else
          {
            $orb = 6;
          }

        // is there an aspect within orb?
        if ($da <= $orb)
        {
          $q = 1;
          $dax = $da;
        }
        elseif (($da <= (60 + $orb)) And ($da >= (60 - $orb)))
        {
          $q = 6;
          $dax = $da - 60;
        }
        elseif (($da <= (90 + $orb)) And ($da >= (90 - $orb)))
        {
          $q = 4;
          $dax = $da - 90;
        }
        elseif (($da <= (120 + $orb)) And ($da >= (120 - $orb)))
        {
          $q = 3;
          $dax = $da - 120;
        }
        elseif (($da <= (150 + $orb)) And ($da >= (150 - $orb)))
        {
          $q = 5;
          $dax = $da - 150;
        }
        elseif ($da >= (180 - $orb))
        {
          $q = 2;
          $dax = 180 - $da;
        }

        if ($q > 0 And $i != $j)
        {
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
    $sql = "SELECT davison_relationships FROM astro_reports";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result);
    $count = $row[davison_relationships] + 1;

    $sql = "UPDATE astro_reports SET davison_relationships = '$count'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


    include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
    exit();
  }
}


Function left($leftstring, $leftlength)
{
  return(substr($leftstring, 0, $leftlength));
}


Function Reduce_below_30($longitude)
{
  $lng = $longitude;

  while ($lng >= 30)
  {
    $lng = $lng - 30;
  }

  return $lng;
}


Function Convert_Longitude($longitude)
{
  $signs = array (0 => 'Ari', 'Tau', 'Gem', 'Can', 'Leo', 'Vir', 'Lib', 'Sco', 'Sag', 'Cap', 'Aqu', 'Pis');

  $sign_num = floor($longitude / 30);
  $pos_in_sign = $longitude - ($sign_num * 30);
  $deg = floor($pos_in_sign);
  $full_min = ($pos_in_sign - $deg) * 60;
  $min = floor($full_min);
  $full_sec = round(($full_min - $min) * 60);

  if ($deg < 10)
  {
    $deg = "0" . $deg;
  }

  if ($min < 10)
  {
    $min = "0" . $min;
  }

  if ($full_sec < 10)
  {
    $full_sec = "0" . $full_sec;
  }

  return $deg . " " . $signs[$sign_num] . " " . $min . "' " . $full_sec . chr(34);
}


Function Convert_Longitude_no_secs($longitude)
{
  $signs = array (0 => 'Ari', 'Tau', 'Gem', 'Can', 'Leo', 'Vir', 'Lib', 'Sco', 'Sag', 'Cap', 'Aqu', 'Pis');

  $sign_num = floor($longitude / 30);
  $pos_in_sign = $longitude - ($sign_num * 30);
  $deg = floor($pos_in_sign);
  $full_min = ($pos_in_sign - $deg) * 60;

  if ($deg < 10)
  {
    $deg = "0" . $deg;
  }

  $fmin = sprintf("%.0f", $full_min);
  if ($fmin < 10)
  {
    $fmin = "0" . $fmin;
  }

  return $deg . " " . $signs[$sign_num] . " " . $fmin;
}


Function mid($midstring, $midstart, $midlength)
{
  return(substr($midstring, $midstart-1, $midlength));
}


Function Find_Specific_Report_Paragraph($phrase_to_look_for, $file)
{
  $string = "";
  $len = strlen($phrase_to_look_for);

  //put entire file contents into an array, line by line
  $file_array = file($file);

  // look through each line searching for $phrase_to_look_for
  for($i = 0; $i < count($file_array); $i++)
  {
    if (left(trim($file_array[$i]), $len) == $phrase_to_look_for)
    {
      $flag = 0;
      while (trim($file_array[$i]) != "*")
      {
        if ($flag == 0)
        {
          $string .= "<b>" . $file_array[$i] . "</b>";
        }
        else
        {
          $string .= $file_array[$i];
        }
        $flag = 1;
        $i++;
      }
      break;
    }
  }

  return $string;
}


Function Crunch($x)
{
  if ($x >= 0)
  {
    $y = $x - floor($x / 360) * 360;
  }
  else
  {
    $y = 360 + ($x - ((1 + floor($x / 360)) * 360));
  }

  return $y;
}


Function ConvertJDtoDateandTime($Result_JD, $current_tz)
{
  //returns date and time in local time, e.g. 9/3/2007 4:59 am
  //get calendar day - must adjust for the way the PHP function works by adding 0.5 days to the JD of interest
  $jd_to_use = $Result_JD + $current_tz / 24;

  $JDDate = jdtogregorian($jd_to_use + 0.5);

  $fraction = $jd_to_use - floor($jd_to_use);

  if ($fraction < 0.5)
  {
    $am_pm = "pm";
  }
  else
  {
    $fraction = $fraction - 0.5;
    $am_pm = "am";
  }

  $hh = $fraction * 24;
  if ($hh < 1)
  {
    $hh = $hh + 12;
  }

  $mm = $hh - floor($hh);
  $mins = floor($mm * 60);

  $secs = floor(($mm * 60 - floor($mm * 60)) * 60);
  if ($secs == 30)
  {
    $secs = "30";
  }
  else
  {
    $secs = "00";
  }

  if ($mins < 10)
  {
    return $JDDate . " " . floor($hh) . ":0" . floor($mm * 60) . ":" . $secs . " " . $am_pm;
  }
  else
  {
    return $JDDate . " " . floor($hh) . ":" . floor($mm * 60) . ":" . $secs . " " . $am_pm;
  }
}

?>
