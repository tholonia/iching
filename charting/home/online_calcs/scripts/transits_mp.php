<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False) { exit(); }

  require_once($_SERVER['DOCUMENT_ROOT'] . "/charting/mysqli_connect_online_calcs_db_MYSQLI.php");
  require_once($_SERVER['DOCUMENT_ROOT'] .  "/charting/my_functions_MYSQLI.php");

  $months = array (0 => 'Choose month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

  $no_interps = False;        //set this to False when you want interpretations

  // check if the form has been submitted
  if (isset($_POST['submitted']) Or isset($_POST['h_sys_submitted']))
  {
    $id1 = mysqlSafeEscapeString($conn, $_POST["id1"]);

    if (!is_numeric($id1))
    {
      echo "<center><br /><br />You have forgotten to make an entry. Please try again.</center>";
      include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
      exit();
    }

    $username = $_SESSION['username'];

    $sql = "SELECT * FROM astro_birth_info WHERE ID='$id1' And entered_by='$username'";

    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $num_records = MYSQLI_NUM_rows($result);

    if ($num_records != 1)
    {
      echo "<center><br /><br />I cannot find this person in the database. Please try again.</center>";
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


    include ('header_transits.html');       //here because of setting cookies above


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


    // get all variables from form - transit date
    $start_month = mysqlSafeEscapeString($conn, $_POST["start_month"]);
    $start_day = mysqlSafeEscapeString($conn, $_POST["start_day"]);
    $start_year = mysqlSafeEscapeString($conn, $_POST["start_year"]);

    if ($start_day < 1 Or $start_day > 31)
    {
      $start_day = strftime("%d", time());
    }


    // additional error checks on user-entered data
    if ($start_month != "" And $start_day != "" And $start_year != "")
    {
      if (!$date = checkdate($start_month, $start_day, $start_year))
      {
        $my_error .= "The transit date you entered is not valid.<br>";
      }
    }

    if (($start_year < 1200) Or ($start_year >= 2400))
    {
      $my_error .= "Transit date - please enter a year between 1200 and 2399.<br>";
    }


    if (( isset($my_error) ?: "" ) != "") 
    {
      echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'><tr><td><center><b>";
      echo "<font color='#ff0000' size=+2>Error! - The following error(s) occurred:</font><br>";

      echo $my_error;

      echo "</font>";
      echo "</b></center></td></tr></table>";

      include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
      exit();
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

//Person 1 calculations
      // Unset any variables not initialized elsewhere in the program
      unset($PATH,$out,$pl_name,$longitude1,$house_pos1);

      $inmonth = $month1;
      $inday = $day1;
      $inyear = $year1;

      $inhours = $hour1;
      $inmins = $minute1;
      $insecs = "0";

      $intz = $timezone1;

      $my_longitude = $ew1 * ($long_deg1 + ($long_min1 / 60));
      $my_latitude = $ns1 * ($lat_deg1 + ($lat_min1 / 60));

      $abs_tz = abs($intz);
      $the_hours = floor($abs_tz);
      $fraction_of_hour = $abs_tz - floor($abs_tz);
      $the_minutes = 60 * $fraction_of_hour;
      $whole_minutes = floor(60 * $fraction_of_hour);
      $fraction_of_minute = $the_minutes -$whole_minutes;
      $whole_seconds = round(60 * $fraction_of_minute);

      if ($intz >= 0)
      {
        $inhours = $inhours - $the_hours;
        $inmins = $inmins - $whole_minutes;
        $insecs =  $insecs - $whole_seconds;
      }
      else
      {
        $inhours = $inhours + $the_hours;
        $inmins = $inmins + $whole_minutes;
        $insecs =  $insecs + $whole_seconds;
      }

      // adjust date and time for minus hour due to time zone taking the hour negative
      $utdatenow = strftime("%d.%m.%Y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -house$my_longitude,$my_latitude,$h_sys -flsj -g, -head", $out);    //add a planet

      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $longitude1[$key] = $row[0];
        $speed1[$key] = $row[1];
        $house_pos1[$key] = $row[2];
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


//get house positions of planets here - for natal person
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

      //Transit calculations
      // Unset any variables not initialized elsewhere in the program
      unset($out,$longitude2,$speed2);

      // get all variables from form - Transits
      //get todays date and time
      $name2 = "Transits";
      $inmonth = $start_month;
      $inday = $start_day;
      $inyear = $start_year;

      $hour2 = gmdate("H");
      $minute2 = gmdate("i");
      $timezone2 = 0;

      $inhours = $hour2;
      $inmins = $minute2;
      $insecs = "0";

      $intz = $timezone2;

      // adjust date and time for minus hour due to time zone taking the hour negative
      $utdatenow = strftime("%d.%m.%Y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789DAttt -eswe -fls -g, -head", $out); //add a planet

      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $longitude2[$key] = $row[0];
        $speed2[$key] = $row[1];
      };

//get house positions of planets here - transit planets in natal houses
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= LAST_PLANET; $y++)
        {
          $pl = $longitude2[$y] + (1 / 36000);
          if ($x < 12 And $longitude1[$x + LAST_PLANET] > $longitude1[$x + LAST_PLANET + 1])
          {
            If (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[$x + LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos2[$y] = $x;
              continue;
            }
          }

          if ($x == 12 And ($longitude1[$x + LAST_PLANET] > $longitude1[LAST_PLANET + 1]))
          {
            if (($pl >= $longitude1[$x + LAST_PLANET] And $pl < 360) Or ($pl < $longitude1[LAST_PLANET + 1] And $pl >= 0))
            {
              $house_pos2[$y] = $x;
            }
            continue;
          }

          if (($pl >= $longitude1[$x + LAST_PLANET]) And ($pl < $longitude1[$x + LAST_PLANET + 1]) And ($x < 12))
          {
            $house_pos2[$y] = $x;
            continue;
          }

          if (($pl >= $longitude1[$x + LAST_PLANET]) And ($pl < $longitude1[LAST_PLANET + 1]) And ($x == 12))
          {
            $house_pos2[$y] = $x;
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

      $line1 = $name_without_slashes . ", born " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT $tz1 hours)", mktime($hour1, $minute1, $secs, $month1, $day1, $year1));
      $line1 = $line1 . " at " . $long_deg1 . $ew1_txt . sprintf("%02d", $long_min1) . " and " . $lat_deg1 . $ns1_txt . sprintf("%02d", $lat_min1);

      $line2 = $name2 . " on " . strftime("%A, %B %d, %Y at %H:%M (time zone = GMT)", mktime($hour2, $minute2, $secs, $start_month, $start_day, $start_year));
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

        <input type="hidden" name="start_month" value="<?php echo $_POST['start_month']; ?>">
        <input type="hidden" name="start_day" value="<?php echo $_POST['start_day']; ?>">
        <input type="hidden" name="start_year" value="<?php echo $_POST['start_year']; ?>">

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

      $ubt2 = 1;    //always assume an unknown time

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

      $rx2 = "";
      for ($i = 0; $i <= SE_TNODE; $i++)
      {
        if ($speed2[$i] < 0)
        {
          $rx2 .= "R";
        }
        else
        {
          $rx2 .= " ";
        }
      }

      // to make GET string shorter
      for ($i = 0; $i <= LAST_PLANET; $i++)
      {
        $L1[$i] = $longitude1[$i];
        $L2[$i] = $longitude2[$i];
      }

      for ($i = 1; $i <= LAST_PLANET; $i++)
      {
        $hc1[$i] = $longitude1[LAST_PLANET + $i];
        $hc2[$i] = $longitude2[LAST_PLANET + $i];
      }


// no need to urlencode unless perhaps magic quotes is ON (??)
    $_SESSION['transits_mp_p1'] = $L1;
    $_SESSION['transits_mp_p2'] = $L2;

    $_SESSION['transits_mp_hc1'] = $hc1;
    $_SESSION['transits_mp_hc2'] = $hc2;

    $_SESSION['transits_mp_hp1'] = $house_pos1;
    $_SESSION['transits_mp_hp2'] = $house_pos2;

    $wheel_width = 800;
    $wheel_height = $wheel_width + 50;    //includes space at top of wheel for header

    echo "<center>";
    echo "<img border='0' src='transit_wheel_2.php?rx1=$rx1&rx2=$rx2&ubt1=$ubt1&ubt2=$ubt2&l1=$line1&l2=$line2' width='$wheel_width' height='$wheel_height'>";
    echo "<br><br>";
    echo "<img border='0' src='transit_aspect_grid.php?rx1=$rx1&rx2=$rx2&ubt1=$ubt1&ubt2=$ubt2' width='830' height='475'>";
    echo "</center>";
    echo "<br>";


// display all data - planets
    echo '<center><table width="65%" cellpadding="0" cellspacing="0" border="0">';

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Natal </b></font></td>";
    echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
    if ($ubt1 == 1)
    {
      echo "<td>&nbsp;</td>";
    }
    else
    {
      echo "<td><font color='#0000ff'><b> House<br>position </b></font></td>";
    }
    echo "<td><font color='#0000ff'><b> Transits </b></font></td>";
    echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
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
      echo "<td><font face='Courier New'>" . Convert_Longitude($L1[$i]) . " " . Mid($rx1, $i + 1, 1) . "</font></td>";
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

      if ($i <= LAST_PLANET - 2)
      {
        echo "<td>" . $pl_name[$i] . "</td>";
      }
      else
      {
        echo "<td> &nbsp </td>";
      }
      if ($i <= LAST_PLANET - 2)
      {
        echo "<td><font face='Courier New'>" . Convert_Longitude($L2[$i]) . " " . Mid($rx2, $i + 1, 1) . "</font></td>";
      }
      else
      {
        echo "<td> &nbsp </td>";
      }
      echo '</tr>';
    }

    echo '<tr>';
    echo "<td> &nbsp </td>";
    echo "<td> &nbsp </td>";
    echo "<td> &nbsp </td>";
    echo "<td> &nbsp </td>";
    echo '</tr>';

// display house cusps
    if ($ubt1 == 0)
    {
      echo '<tr>';
      echo "<td><font color='#0000ff'><b> House </b></font></td>";
      echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
      echo '</tr>';

      for ($i = 1; $i <= 12; $i++)
      {
        echo '<tr>';
        if ($i == 1)
        {
          echo "<td>Ascendant </td>";
        }
        elseif ($i == 10)
        {
          echo "<td>MC (Midheaven) </td>";
        }
        else
        {
          echo "<td>House " . ($i) . "</td>";
        }
        echo "<td><font face='Courier New'>" . Convert_Longitude($hc1[$i]) . "</font></td>";
        echo '</tr>';
      }
    }

    echo '</table></center>';
    echo "<br /><br />";

// display transit data - aspect table
    $asp_name[1] = "Conjunction";
    $asp_name[2] = "Opposition";
    $asp_name[3] = "Trine";
    $asp_name[4] = "Square";
    $asp_name[5] = "Quincunx";
    $asp_name[6] = "Sextile";

    echo '<center><table width="40%" cellpadding="0" cellspacing="0" border="0">';

    echo '<tr>';
    echo "<td><font color='#0000ff'><b> Transits</b></font></td>";
    echo "<td><font color='#0000ff'><b> Aspect </b></font></td>";
    echo "<td><font color='#0000ff'><b> Natal Planet</b></font></td>";
    echo "<td><font color='#0000ff'><b> Orb </b></font></td>";
    echo '</tr>';

    // include Ascendant and MC
    $L1[LAST_PLANET + 1] = $hc1[1];
    $L1[LAST_PLANET + 2] = $hc1[10];

    $pl_name[LAST_PLANET + 1] = "Ascendant";
    $pl_name[LAST_PLANET + 2] = "Midheaven";

    for ($i = 0; $i <= LAST_PLANET - 2; $i++)
    {
      echo "<tr><td colspan='4'>&nbsp;</td></tr>";
      for ($j = 0; $j <= LAST_PLANET + 2; $j++)
      {
        $q = 0;
        $da = abs($L2[$i] - $L1[$j]);

        if ($da > 180)
        {
          $da = 360 - $da;
        }

        $orb = 2;

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

        if ($q > 0)
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
    $sql = "SELECT transit_to_natal_interps FROM astro_reports";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);
    $row = mysqli_fetch_array($result);
    $count = $row[transit_to_natal_interps] + 1;

    $sql = "UPDATE astro_reports SET transit_to_natal_interps = '$count'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);


//display the transit chart report
    if ($no_interps == True)
    {
      include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
      exit();
    }
    else
    {
      echo '<center><table width="61.8%" cellpadding="0" cellspacing="0" border="0">';
      echo '<tr><td><font face="Verdana" size="3">';

      //display philosophy of astrology
      echo "<center><font size='+1' color='#0000ff'><b>TRANSIT PLANET ASPECTS TO NATAL PLANETS</b></font></center>";

      $file = "transit_files/aspect.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $p_aspect_interp = nl2br($string);
      echo "<font size=2>" . $p_aspect_interp . "</font>";


      $days_back = 8;
      $days_ahead = 16;


      // loop through each planet
      for ($i = 0; $i <= 12; $i++)      //transit planets - was 5 - ended with Jupiter
      {
        for ($j = 0; $j <= LAST_PLANET + 2; $j++)   //natal planets - was 9 - ended with Pluto
        {
          //if (($i == 1) Or ($j == 1 And $ubt1 == 1))
          if (($j == 1 Or $j == LAST_PLANET + 1 Or $j == LAST_PLANET + 2) And $ubt1 == 1)
          {
            //continue;     // do not allow Moon aspects for transit planets, or for natal planets if birth time is unknown
            continue;     // do not allow Moon aspects for natal planets if birth time is unknown
          }

          $da = abs($L2[$i] - $L1[$j]);
          if ($da > 180)
          {
            $da = 360 - $da;
          }

          $orb = 2.0;       //orb = 2.0 degree

          // are planets within orb?
          $q = 1;
          if ($da <= $orb)
          {
            $q = 2;
          }
          elseif (($da <= 60 + $orb) And ($da >= 60 - $orb))
          {
            $q = 3;
          }
          elseif (($da <= 90 + $orb) And ($da >= 90 - $orb))
          {
            $q = 4;
          }
          elseif (($da <= 120 + $orb) And ($da >= 120 - $orb))
          {
            $q = 5;
          }
          elseif (($da <= 150 + $orb) And ($da >= 150 - $orb))
          {
            $q = 11;
          }
          elseif ($da >= 180 - $orb)
          {
            $q = 6;
          }

          if ($q > 1)
          {
            if ($q == 2)
            {
              $aspect = " Conjunct ";
              $angle = 0;
            }
            elseif ($q == 6)
            {
              $aspect = " Opposite ";
              $angle = 180;
            }
            elseif ($q == 3)
            {
              $aspect = " Sextile ";
              $angle = 60;
            }
            elseif ($q == 4)
            {
              $aspect = " Square ";
              $angle = 90;
            }
            elseif ($q == 5)
            {
              $aspect = " Trine ";
              $angle = 120;
            }
            elseif ($q == 11)
            {
              $aspect = " Quincunx ";
              $angle = 150;
            }

            $phrase_to_look_for = $pl_name[$i] . $aspect . $pl_name[$j];
            $file = "transit_files/" . strtolower($pl_name[$i]) . "_tr.txt";

            if (file_exists($file))
            {
              $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file, $pl_name[$i], $aspect, $pl_name[$j], $L2[$i], $speed2[$i]);
              $string = nl2br($string);
              echo "<font size='2'>" . $string . "</font>";

              if ($string != "")
              {
                $start_JD = gregoriantojd($start_month, $start_day, $start_year) - 0.5;   // find Julian day for specified date at midnight.
                $start_JD = $start_JD - $days_back;      // start a little early so we dont miss the day

                $p1_idx = $i;   // transit planet index into Swiss ephemeris

                $orb_rx = 2.0;

                //record ALL times when p1 is exact or -2 deg. or +2 deg. the n. planet
                $cnt = 0;
                $dt = array();

                $n_planet = $L1[$j] - $orb_rx;
                if ($n_planet < 0) { $n_planet = $n_planet + 360; }

                $jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $start_JD, $start_JD + $days_ahead, $angle);

                if ($jd_result > 0 )
                {
                  $dt[0][$cnt] = $jd_result;
                  $dt[1][$cnt] = 0;
                  $cnt++;
                  $jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $jd_result + 0.1, $start_JD + $days_ahead, $angle);       //look for another aspect in this time period

                  if ($jd_result > 0 )
                  {
                    $dt[0][$cnt] = $jd_result;
                    $dt[1][$cnt] = 0;
                    $cnt++;
                  }
                }

                $n_planet = $L1[$j];
                $jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $start_JD, $start_JD + $days_ahead, $angle);

                if ($jd_result > 0 )
                {
                  $dt[0][$cnt] = $jd_result;
                  $dt[1][$cnt] = 1;
                  $cnt++;
                  $jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $jd_result + 0.1, $start_JD + $days_ahead, $angle);       //look for another aspect in this time period

                  if ($jd_result > 0 )
                  {
                    $dt[0][$cnt] = $jd_result;
                    $dt[1][$cnt] = 1;
                    $cnt++;
                  }
                }

                $n_planet = $L1[$j] + $orb_rx;
                if ($n_planet >= 360) { $n_planet = $n_planet - 360; }

                $jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $start_JD, $start_JD + $days_ahead, $angle);

                if ($jd_result > 0 )
                {
                  $dt[0][$cnt] = $jd_result;
                  $dt[1][$cnt] = 0;
                  $cnt++;
                  $jd_result = Get_when_planet_is_at_certain_degree_TR($n_planet, $p1_idx, $pl_name, $jd_result + 0.1, $start_JD + $days_ahead, $angle);       //look for another aspect in this time period

                  if ($jd_result > 0 )
                  {
                    $dt[0][$cnt] = $jd_result;
                    $dt[1][$cnt] = 0;
                    $cnt++;
                  }
                }

                //sort the dates
                if (count($dt) > 0) { array_multisort($dt[0], SORT_NUMERIC, $dt[1], SORT_REGULAR); }

                if ($cnt == 0)
                {
                  $the_dt_1 = ConvertJDtoDateandTime_TR($start_JD, $timezone2);
                  $the_dt_2 = ConvertJDtoDateandTime_TR($start_JD + $days_ahead, $timezone2);

                  echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";
                }
                elseif ($cnt == 1)
                {
                  if ($dt[1][0] == 1)
                  {
                    $the_dt_1 = ConvertJDtoDateandTime_TR($start_JD, $timezone2);
                    $the_dt_2 = ConvertJDtoDateandTime_TR($start_JD + $days_ahead, $timezone2);

                    echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";

                    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);

                    echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT</font><br><br>";
                  }
                  elseif ($dt[1][0] == 0)
                  {
                    $time_period_1 = $start_JD + $days_ahead - $dt[0][0];
                    $time_period_2 = $dt[0][0] - $start_JD;

                    if ($time_period_1 > $time_period_2)
                    {
                      //it's either this one . . .
                      $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);
                      $the_dt_2 = ConvertJDtoDateandTime_TR($start_JD + $days_ahead, $timezone2);

                      echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";
                    }
                    else
                    {
                      //or this one
                      $the_dt_1 = ConvertJDtoDateandTime_TR($start_JD, $timezone2);
                      $the_dt_2 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);

                      echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";
                    }
                  }
                }
                elseif ($cnt == 2)
                {
                  //check for various conditions
                  if ($dt[1][0] == 1 And $dt[1][1] == 0)
                  {
                    $the_dt_1 = ConvertJDtoDateandTime_TR($start_JD, $timezone2);
                    $the_dt_2 = ConvertJDtoDateandTime_TR($dt[0][1], $timezone2);

                    echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";

                    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);

                    echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT</font><br><br>";
                  }
                  elseif ($dt[1][0] == 0 And $dt[1][1] == 1)
                  {
                    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);
                    $the_dt_2 = ConvertJDtoDateandTime_TR($start_JD + $days_ahead, $timezone2);

                    echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";

                    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][1], $timezone2);

                    echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT</font><br><br>";
                  }
                  elseif ($dt[1][0] == 1 And $dt[1][1] == 1)
                  {
                    $the_dt_1 = ConvertJDtoDateandTime_TR($start_JD, $timezone2);
                    $the_dt_2 = ConvertJDtoDateandTime_TR($start_JD + $days_ahead, $timezone2);

                    echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";

                    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);

                    echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT</font><br><br>";

                    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][1], $timezone2);

                    echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT</font><br><br>";
                  }
                  else
                  {
                    $the_dt_1 = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);
                    $the_dt_2 = ConvertJDtoDateandTime_TR($dt[0][1], $timezone2);

                    echo "<font size='2'>This aspect is active from " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_1[3], $the_dt_1[4], $the_dt_1[5], $the_dt_1[0], $the_dt_1[1], $the_dt_1[2])) . " GMT to " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt_2[3], $the_dt_2[4], $the_dt_2[5], $the_dt_2[0], $the_dt_2[1], $the_dt_2[2])) . " GMT</font><br><br>";
                  }
                }
                elseif ($cnt == 3)
                {
                  $the_dt = ConvertJDtoDateandTime_TR($dt[0][0], $timezone2);
                  echo "<font size='2'>This aspect starts on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2])) . " GMT</font><br>";

                  $the_dt = ConvertJDtoDateandTime_TR($dt[0][1], $timezone2);
                  echo "<font size='2'>This aspect is exact on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2])) . " GMT</font><br>";

                  $the_dt = ConvertJDtoDateandTime_TR($dt[0][2], $timezone2);
                  echo "<font size='2'>This aspect ends on " . strftime('%A, %B %d, %Y at %H:%M:%S', mktime($the_dt[3], $the_dt[4], $the_dt[5], $the_dt[0], $the_dt[1], $the_dt[2])) . " GMT</font><br><br><br>";
                }
              }
            }
          }
        }
      }


      //display closing
      $file = "transit_files/closing.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $closing = nl2br($string);
      echo "<font size=2>" . $closing . "</font><br /><center>";


      include ('moon_aspects_mp.php');


      $pl_name[LAST_PLANET + 2] = "Midheaven";


      //display midpoint aspects
      //get header first
      echo "<font size='+1' color='#0000ff'><b>TRANSIT MIDPOINTS</b></font></center><br />";

      //calculate various midpoints
      for ($i = 0; $i <= LAST_PLANET + 1; $i++)
      {
        for ($j = $i + 1; $j <= LAST_PLANET + 2; $j++)
        {
          $mp[$i][$j] = ($L2[$i] + $L2[$j]) / 2;

          //this finds the nearer midpoint, which may not be what is optimum
          $diff1 = $mp[$i][$j] - $L2[$i];
          $diff2 = $mp[$i][$j] - $L2[$j];

          if (abs($diff1) > 90 Or abs($diff2) > 90)
          {
            $mp[$i][$j] = $mp[$i][$j] + 180;
          }

          if ($mp[$i][$j] >= 360)
          {
            $mp[$i][$j] = $mp[$i][$j] - 360;
          }
        }
      }


      //list all midpoints
      echo '<center><table width="98%" cellpadding="0" cellspacing="0" border="0">';

      $cols = 3;
      for ($i = 0; $i <= LAST_PLANET + 1; $i++)
      {
        for ($j = $i + 1; $j <= LAST_PLANET + 2; $j = $j + $cols)
        {
          echo "<tr>";

          if ($j <= LAST_PLANET + 2)
          {
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j] . " = " . Convert_Longitude_no_secs($mp[$i][$j]) . "</td>";
          }
          else
          {
            echo "<td>&nbsp;</td>";
          }

          if ($j + 1 <= LAST_PLANET + 2)
          {
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j + 1] . " = " . Convert_Longitude_no_secs($mp[$i][$j + 1]) . "</td>";
          }
          else
          {
            echo "<td>&nbsp;</td>";
          }

          if ($j + 2 <= LAST_PLANET + 2)
          {
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j + 2] . " = " . Convert_Longitude_no_secs($mp[$i][$j + 2]) . "</td>";
          }
          else
          {
            echo "<td>&nbsp;</td>";
          }

          echo "</tr>";
        }

        echo "<tr><td colspan='$cols'>&nbsp;</td></tr>";
      }

      echo '</table></center><br />';


      //display midpoint aspects
      //get header first
      echo "<center><font size='+1' color='#0000ff'><b>NATAL MIDPOINTS</b></font></center><br />";

      //calculate various midpoints
      for ($i = 0; $i <= LAST_PLANET + 1; $i++)
      {
        for ($j = 0; $j <= LAST_PLANET + 2; $j++)
        {
          $mp[$i][$j] = ($L1[$i] + $L1[$j]) / 2;

          //this finds the nearer midpoint, which may not be what is optimum
          $diff1 = $mp[$i][$j] - $L1[$i];
          $diff2 = $mp[$i][$j] - $L1[$j];

          if (abs($diff1) > 90 Or abs($diff2) > 90)
          {
            $mp[$i][$j] = $mp[$i][$j] + 180;
          }

          if ($mp[$i][$j] >= 360)
          {
            $mp[$i][$j] = $mp[$i][$j] - 360;
          }
        }
      }


      //list all midpoints
      //calculate various natal midpoints
      echo '<center><table width="98%" cellpadding="0" cellspacing="0" border="0">';

      $cols = 3;
      for ($i = 0; $i <= LAST_PLANET + 1; $i++)
      {
        for ($j = $i + 1; $j <= LAST_PLANET + 2; $j = $j + $cols)
        {
          echo "<tr>";

          if ($j <= LAST_PLANET + 2)
          {
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j] . " = " . Convert_Longitude_no_secs($mp[$i][$j]) . "</td>";
          }
          else
          {
            echo "<td>&nbsp;</td>";
          }

          if ($j + 1 <= LAST_PLANET + 2)
          {
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j + 1] . " = " . Convert_Longitude_no_secs($mp[$i][$j + 1]) . "</td>";
          }
          else
          {
            echo "<td>&nbsp;</td>";
          }

          if ($j + 2 <= LAST_PLANET + 2)
          {
            echo "<td>" . $pl_name[$i] . "/" . $pl_name[$j + 2] . " = " . Convert_Longitude_no_secs($mp[$i][$j + 2]) . "</td>";
          }
          else
          {
            echo "<td>&nbsp;</td>";
          }

          echo "</tr>";
        }

        echo "<tr><td colspan='$cols'>&nbsp;</td></tr>";
      }

      echo '</table></center><br /><br />';


      echo '</font></td></tr>';
      echo '</table></center>';
      echo "<br /><br />";
      }
    }
  }

  include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
  exit();


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


Function Find_Specific_Report_Paragraph($phrase_to_look_for, $file, $pl1_name, $aspect, $pl2_name, $pl_pos, $pl_speed)
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
          if ($pl_speed < 0)
          {
            //retrograde
            $string .= "<b>Transit " . $pl1_name . $aspect . $pl2_name . "</b> - " . $pl1_name . " (Rx) at " . Convert_Longitude($pl_pos) . "<br>";
          }
          else
          {
            $string .= "<b>Transit " . $pl1_name . $aspect . $pl2_name . "</b> - " . $pl1_name . " at " . Convert_Longitude($pl_pos) . "<br>";
          }
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


Function Find_Specific_Report_Paragraph_HP($phrase_to_look_for, $file)
{
  $string = "";
  $len = strlen($phrase_to_look_for);

  if (!file_exists($file))
  {
    return "";
  }

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
          $string .= "<b>Transit " . $file_array[$i] . "</b>";
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


Function Find_Specific_Report_Paragraph_MP($phrase_to_look_for, $file)
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


Function Get_when_planet_is_at_certain_degree_TR($degr, $p1_idx, $pl_name, $starting_JD, $ending_JD, $angle)
{
//aspectarian for when a planet hits a certain degree of the zodiac
  for ($x = $starting_JD; $x <= $ending_JD; $x++)
  {
    //$angle = 0;
    $Result_JD = Secant_Method_one_degree_TR($x, $x + 1, 0.00007, 100, $angle, $p1_idx, $degr);

    $rnd_off = $Result_JD;
    if ($rnd_off >= $x And $rnd_off < $x + 1)
    {
      return $Result_JD;
    }
  }
}


Function Secant_Method_one_degree_TR($earlier_jd, $later_jd, $e, $m, $angle, $p1_idx, $degr)
{
  for ($n = 1; $n <= $m; $n++)
  {
    //get positions of both planets on JD = later_jd and JD = earlier_jd
    $y1 = Get_1_Planet_geo_TR($later_jd, $p1_idx);
    $y3 = Get_1_Planet_geo_TR($earlier_jd, $p1_idx);

    //get distance from exact aspect for both planets on JD = later_jd
    $dayy = $degr - $y1;
    $da = abs($degr - $y1);
    if ($da > 180)
    {
      $da = 360 - $da;
    }
    $dist1 = $da - $angle;
    if ($dayy <= -180 Or ($dayy >= 0 And $dayy < 180))
    {
      $dist1 = -$dist1;
    }

    //get distance from exact aspect for both planets on JD = earlier_jd
    $dayy = $degr - $y3;
    $da = abs($degr - $y3);
    if ($da > 180)
    {
      $da = 360 - $da;
    }
    $dist2 = $da - $angle;
    if ($dayy <= -180 Or ($dayy >= 0 And $dayy < 180))
    {
      $dist2 = -$dist2;
    }

    if ($dist1 - $dist2 == 0)
    {
      $later_jd = ($later_jd + $earlier_jd) / 2;
      $d = 0;
    }
    else
    {
      $d = (($later_jd - $earlier_jd) / ($dist1 - $dist2)) * $dist1;
    }

    if (abs($dist1 - $dist2) > 20 And $n >= 2)
    {
      //keep from looping needlessly AND
      //protect against case where dist1 = -dist2, which gives false aspect
      //example 21 March 2006 - Moon 120 Mars - there is no trine, but an opposition
      $later_jd = 0;
      break;
    }

    if (abs($d) < $e)
    {
      break;
    }

    $earlier_jd = $later_jd;

    if (abs($d) >= 1.001)
    {
      //out of range - there is no aspect in this time frame (1 day)
      $later_jd = 0;
      break;
    }
    else
    {
      $later_jd = $later_jd - $d;
    }
  }

  if ($n > $m)
  {
    return 0;
  }
  else
  {
    return $later_jd;
  }
}


Function Get_1_Planet_geo_TR($jd, $p_idx)
{
  //get longitude of planet indicated by $p_idx
  $swephsrc = './sweph';
  $sweph = './sweph';

  unset($out,$t_long);
  exec ("swetest -edir$sweph -bj$jd -ut -p$p_idx -eswe -fl -g, -head", $out);

  // Each line of output data from swetest is exploded into array $row, giving these elements:
  // 0 = longitude
  // 1 = speed
  foreach ($out as $key => $line)
  {
    $row = explode(',',$line);
    $t_long[$key] = $row[0];
  };

  return $t_long[0];
}


Function ConvertJDtoDateandTime_TR($Result_JD, $current_tz)
{
  $the_dt = array();

  //returns date and time in local time, e.g. 9/3/2007 4:59 am
  //get calendar day - must adjust for the way the PHP function works by adding 0.5 days to the JD of interest
  $jd_to_use = $Result_JD + $current_tz / 24;

  $JDDate = jdtogregorian($jd_to_use + 0.5);

  $time_stamp = strtotime($JDDate);
  $the_dt[0] = strftime("%m", $time_stamp);
  $the_dt[1] = strftime("%d", $time_stamp);
  $the_dt[2] = strftime("%Y", $time_stamp);

  $fraction = $jd_to_use - floor($jd_to_use);

  $hh = $fraction * 24;

  if ($fraction >= 0.5)
  {
    $hh = $hh - 12;
  }
  else
  {
    $hh = $hh + 12;
  }

  $mm = $hh - floor($hh);
  $secs = floor(($mm * 60 - floor($mm * 60)) * 60);

  $the_dt[3] = floor($hh);
  $the_dt[4] = floor($mm * 60);
  $the_dt[5] = $secs;

  return $the_dt;
}

?>
