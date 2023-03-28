<?php

Function safeEscapeString($string)
{
// replace HTML tags '<>' with '[]'
  $temp1 = str_replace("<", "[", $string);
  $temp2 = str_replace(">", "]", $temp1);

// but keep <br> or <br />
// turn <br> into <br /> so later it will be turned into ""
// using just <br> will add extra blank lines
  $temp1 = str_replace("[br]", "<br />", $temp2);
  $temp2 = str_replace("[br /]", "<br />", $temp1);

  if (get_magic_quotes_gpc())
  {
    return $temp2;
  }
  else
  {
    return mysql_escape_string($temp2);
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
            $string .= "<b>" . $pl1_name . $aspect . $pl2_name . "</b> - " . $pl1_name . " (Rx) at " . Convert_Longitude($pl_pos) . "<br>";
          }
          else
          {
            $string .= "<b>" . $pl1_name . $aspect . $pl2_name . "</b> - " . $pl1_name . " at " . Convert_Longitude($pl_pos) . "<br>";
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

  $months = array (0 => 'Choose month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
  $my_error = "";

  $name_from_cookie = $_COOKIE['name'];
  // check if the form has been submitted
  if (isset($_POST['submitted']) Or isset($_COOKIE['name']))
  {
    // get all variables from form
    if (isset($_COOKIE['name']))
    {
      $name = $_COOKIE['name'];
      $month = $_COOKIE['month'];
      $day = $_COOKIE['day'];
      $year = $_COOKIE['year'];
      $hour = $_COOKIE['hour'];
      $minute = $_COOKIE['minute'];
      $timezone = $_COOKIE['timezone'];
    }
    else
    {
      $name = safeEscapeString($_POST["name"]);

      $month = safeEscapeString($_POST["month"]);
      $day = safeEscapeString($_POST["day"]);
      $year = safeEscapeString($_POST["year"]);

      $hour = safeEscapeString($_POST["hour"]);
      $minute = safeEscapeString($_POST["minute"]);

      $timezone = safeEscapeString($_POST["timezone"]);

      // set cookie containing natal data here
      setcookie ('name', $name, time() + 60 * 60 * 24 * 30, '/', '', 0);			//cookie lasts 30 days
      setcookie ('month', $month, time() + 60 * 60 * 24 * 30, '/', '', 0);
      setcookie ('day', $day, time() + 60 * 60 * 24 * 30, '/', '', 0);
      setcookie ('year', $year, time() + 60 * 60 * 24 * 30, '/', '', 0);
      setcookie ('hour', $hour, time() + 60 * 60 * 24 * 30, '/', '', 0);
      setcookie ('minute', $minute, time() + 60 * 60 * 24 * 30, '/', '', 0);
      setcookie ('timezone', $timezone, time() + 60 * 60 * 24 * 30, '/', '', 0);
    }

    include ('header_for_transits.html');
    include("validation_class.php");

    //error check
    $my_form = new Validate_fields;

    $my_form->check_4html = true;

    $my_form->add_text_field("Name", $name, "text", "y", 40);

    $my_form->add_text_field("Month", $month, "text", "y", 2);
    $my_form->add_text_field("Day", $day, "text", "y", 2);
    $my_form->add_text_field("Year", $year, "text", "y", 4);

    $my_form->add_text_field("Hour", $hour, "text", "y", 2);
    $my_form->add_text_field("Minute", $minute, "text", "y", 2);

    $my_form->add_text_field("Time zone", $timezone, "text", "y", 5);

    // additional error checks on user-entered data
    if ($month != "" And $day != "" And $year != "")
    {
      if (!$date = checkdate(settype ($month, "integer"), settype ($day, "integer"), settype ($year, "integer")))
      {
        $my_error .= "The date of birth you entered is not valid.<br>";
      }
    }

    if (($year < 1900) Or ($year >= 2100))
    {
      $my_error .= "Please enter a year between 1900 and 2099.<br>";
    }

    if (($hour < 0) Or ($hour > 23))
    {
      $my_error .= "Birth hour must be between 0 and 23.<br>";
    }

    if (($minute < 0) Or ($minute > 59))
    {
      $my_error .= "Birth minute must be between 0 and 59.<br>";
    }

    $validation_error = $my_form->validation();

    if ((!$validation_error) || ($my_error != ""))
    {
      $error = $my_form->create_msg();
      echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'><tr><td><center><b>";
      echo "<font color='#ff0000' size=+2>Error! - The following error(s) occurred:</font><br>";

      if ($error)
      {
        echo $error . $my_error;
      }
      else
      {
        echo $error . "<br>" . $my_error;
      }

      echo "</font>";
      echo "<font color='#c020c0'";
      echo "<br>PLEASE RE-ENTER YOUR TIME ZONE DATA. THANK YOU.<br><br>";
      echo "</font>";
      echo "</b></center></td></tr></table>";
    }
    else
    {
      // no errors in filling out form, so process form
      // calculate astronomic data
      $swephsrc = './';
      $sweph = './';

      // Unset any variables not initialized elsewhere in the program
      unset($PATH,$out,$longitude2,$speed2);

      //get today's date and time
      $date_now = date ("Y-m-d");

      $inmonth = strftime("%m", time());
      $inday = strftime("%d", time());
      $inyear = strftime("%Y", time());

      $inhours = strftime("%H", time());
      $inmins = strftime("%M", time());
      $insecs = "0";

      $intz = (strftime("%z", time()) / 100);			//time zone on server

      if ($intz >= 0)
      {
        $whole = floor($intz);
        $fraction = $intz - floor($intz);
      }
      else
      {
        $whole = ceil($intz);
        $fraction = $intz - ceil($intz);
      }

      $inhours = $inhours - $whole;
      $inmins = $inmins - ($fraction * 60);

      // adjust date and time for minus hour due to time zone taking the hour negative
      $utdatenow = strftime("%d.%m.20%y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

      $transit_date_time = strftime("%d %B 20%y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear)) . " at " . strftime("%H:%M", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));;

      putenv("PATH=$PATH:$swephsrc");

      // get 10 planets
      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p012345 -eswe -fPls -g, -head", $out);

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = planet name
      // 1 = longitude
      // planets are index 0 - index 9
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $longitude2[$key] = $row[1];
        $speed2[$key] = $row[2];
      };


      //get natal data
      unset($out,$pl_name,$longitude1);

      //assign birth data from form to local variables
      $inmonth = $month;
      $inday = $day;
      $inyear = $year;

      $inhours = $hour;
      $inmins = $minute;
      $insecs = "0";

      $intz = $timezone;

      if ($intz >= 0)
      {
        $whole = floor($intz);
        $fraction = $intz - floor($intz);
      }
      else
      {
        $whole = ceil($intz);
        $fraction = $intz - ceil($intz);
      }

      $inhours = $inhours - $whole;
      $inmins = $inmins - ($fraction * 60);

      // adjust date and time for minus hour due to time zone taking the hour negative
      if ($inyear >= 2000)
      {
        $utdatenow = strftime("%d.%m.20%y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      }
      else
      {
        $utdatenow = strftime("%d.%m.19%y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      }

      $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

      // get 10 planets
      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789 -eswe -fPlj -g, -head", $out);

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = planet name
      // 1 = longitude
      // planets are index 0 - index 9
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $pl_name[$key] = $row[0];
        $longitude1[$key] = $row[1];
      };


      //generate transit interpretations here (and display natal data)
      echo "<center>";

      $existing_name = $name;

      echo "<FONT color='#ff0000' SIZE='5' FACE='Arial'><b>Name = $existing_name </b></font><br /><br />";

      $secs = "0";
      if ($timezone < 0)
      {
        $tz = $timezone;
      }
      else
      {
        $tz = "+" . $timezone;
      }

      echo "Date and time of transits = " . $transit_date_time . " GMT<br><br>";

      if ($year >= 2000)
      {
        echo '<b>Birth data: ' . strftime("%B %d, 20%y at %X (time zone = GMT $tz hours)</b><br /><br /><br />\n", mktime($hour, $minute, $secs, $month, $day, $year));
      }
      else
      {
        echo '<b>Birth data: ' . strftime("%B %d, 19%y at %X (time zone = GMT $tz hours)</b><br /><br /><br />\n", mktime($hour, $minute, $secs, $month, $day, $year));
      }

      echo "</center>";

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

      $hr_ob = $hour;
      $min_ob = $minute;

      $unknown_time = 0;
      if (($hr_ob == 12) And ($min_ob == 0))
      {
        $unknown_time = 1;				// this person has an unknown birth time
      }

      echo '<center><a href="remove_cookie.php">Click here to remove the cookie so that you may re-enter your birth data</a></center><br><br>';

      echo '<center><table width="61.8%" cellpadding="0" cellspacing="0" border="0">';
      echo '<tr><td><font face="Verdana" size="3">';

      //display planetary aspect interpretations
      //get header first
      echo "<center><font size='+1' color='#0000ff'><b>PLANETARY TRANSIT ASPECTS</b></font></center>";

      $file = "transit_files/aspect.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $string = nl2br($string);
      $p_aspect_interp = $string;

      echo "<font size=2>" . $p_aspect_interp . "</font>";

      // loop through each planet
      for ($i = 0; $i <= 5; $i++)
      {
        for ($j = 0; $j <= 9; $j++)
        {
          if (($i == 1) Or ($j == 1 And $unknown_time == 1))
          {
            continue;			// do not allow Moon aspects for transit planets, or for natal planets if birth time is unknown
          }

          $da = Abs($longitude2[$i] - $longitude1[$j]);
          if ($da > 180)
          {
            $da = 360 - $da;
          }

          $orb = 1.5;				//orb = 1.5 degree

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
          elseif ($da >= 180 - $orb)
          {
            $q = 6;
          }

          if ($q > 1)
          {
            if ($q == 2)
            {
              $aspect = " Conjunct ";
            }
            elseif ($q == 6)
            {
              $aspect = " Opposite ";
            }
            elseif ($q == 3)
            {
              $aspect = " Sextile ";
            }
            elseif ($q == 4)
            {
              $aspect = " Square ";
            }
            elseif ($q == 5)
            {
              $aspect = " Trine ";
            }

            $phrase_to_look_for = $pl_name[$i] . $aspect . $pl_name[$j];
            $file = "transit_files/" . strtolower($pl_name[$i]) . "_tr.txt";
            $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file, $pl_name[$i], $aspect, $pl_name[$j], $longitude2[$i], $speed2[$i]);
            $string = nl2br($string);
            echo "<font size=2>" . $string . "</font>";
          }
        }
      }


      //display closing
      $file = "transit_files/closing.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $closing = nl2br($string);
      echo "<font size=2>" . $closing . "</font>";

      echo '</font></td></tr>';
      echo '</table></center>';

      //display natal data
      echo '<center><table width="50%" cellpadding="0" cellspacing="0" border="0">',"\n";

      echo '<tr>';
      echo "<td><font color='#0000ff'><b> Name </b></font></td>";
      echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
      echo "<td>&nbsp;</td>";
      echo '</tr>';

      for ($i = 0; $i <= 9; $i++)
      {
        echo '<tr>';
        echo "<td>" . $pl_name[$i] . "</td>";
        echo "<td><font face='Courier New'>" . Convert_Longitude($longitude1[$i]) . "</font></td>";
        echo "<td>&nbsp;</td>";
        echo '</tr>';
      }

      echo '<tr>';
      echo "<td> &nbsp </td>";
      echo "<td> &nbsp </td>";
      echo "<td> &nbsp </td>";
      echo "<td> &nbsp </td>";
      echo '</tr>';

      echo '</table></center>',"\n";
      echo "<br /><br />";


      //display philosophy of astrology
      echo '<center><table width="61.8%" cellpadding="0" cellspacing="0" border="0">';
      echo '<tr><td><font face="Verdana" size="3">';
      echo "<center><font size='+1' color='#0000ff'><b>MY PHILOSOPHY OF ASTROLOGY</b></font></center>";

      $file = "transit_files/philo.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $philo = nl2br($string);
      echo "<font size=2>" . $philo . "</font>";
      echo "</td></tr></table>";

      echo '<br><center><a href="remove_cookie.php">Click here to remove the cookie so that you may re-enter your birth data</a></center><br>';

      include ('footer_data_entry.html');
      exit();
    }
  }

include ('header_for_transits.html');

?>

<table style="margin: 0px 20px;">
  <tr>
    <td>
      <font color='#ff0000' size=4>
      <b>Please Read This:</b><br>
      </font>

      <font color='#000000' size=2>
      This web page uses a cookie to store your birth data so you do not have to re-enter it every time you come back.
      If you make a mistake, then use your browser and delete the cookie for this website.
      <a href="remove_cookie.php">If needed, click here to remove the cookie so that you may re-enter your birth data</a>.
      <br><br>
      If you do not know all the information that is required by the form below, then here is where you may go<br>
      for time zone information (which is very important):<br><br>
      <a href="http://www.astro.com/atlas">http://www.astro.com/atlas</a><br><br>

      1) Click on SEARCH.<br>
      2) Click on the link that is your birth place.<br>
      3) Fill out the information in order to find the time zone at birth.<br>
      4) Click on Continue.<br>
      5) Locate the time zone information. For example:<br><br>
      &nbsp;&nbsp;&nbsp;&nbsp;<b>Time Zone: 5 h west,  Daylight Saving</b> (this means select the "GMT -04:00" option - one hour added for DST.<br><br>
      6) Enter your time zone information into the below form.<br><br>

      OR JUST GO DIRECTLY TO:<br><br>
      <a href="http://www.astro.com/cgi/ade.cgi">http://www.astro.com/cgi/ade.cgi</a><br><br>
      and fill out all the information. Then come back here and fill out the form with the data you now have.<br><br><br>
      </font>
    </td>
  </tr>
</table>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="margin: 0px 20px;">
  <fieldset><legend><font size=5><b>Data entry for Transit Interpretations</b></font></legend>

  &nbsp;&nbsp;<font color="#ff0000"><b>All fields are required.</b></font><br>

  <table style="font-size:12px;">
    <TR>
      <TD>
        <P align="right">Name:</P>
      </TD>

      <TD>
        <INPUT size="40" name="name" value="<?php echo $_POST['name']; ?>">
      </TD>
    </TR>

    <TR>
      <TD>
        <P align="right">Birth date:</P>
      </TD>

      <TD>
        <?php
        echo '<select name="month">';
        foreach ($months as $key => $value)
        {
          echo "<option value=\"$key\"";
          if ($key == $month)
          {
            echo ' selected="selected"';
          }
          echo ">$value</option>\n";
        }
        echo '</select>';
        ?>

        <INPUT size="2" maxlength="2" name="day" value="<?php echo $_POST['day']; ?>">
        <b>,</b>&nbsp;
        <INPUT size="4" maxlength="4" name="year" value="<?php echo $_POST['year']; ?>">
         <font color="#0000ff">
        (only years from 1900 through 2099 are valid)
        </font>
     </TD>
    </TR>

    <TR>
      <td valign="top"><P align="right">Birth time:</P></td>
      <TD>
        <INPUT maxlength="2" size="2" name="hour" value="<?php echo $_POST['hour']; ?>">
        <b>:</b>
        <INPUT maxlength="2" size="2" name="minute" value="<?php echo $_POST['minute']; ?>">

        <br>

        <font color="#0000ff">
        (please give time of birth in 24 hour format. If your birth time is unknown, please enter 12:00)<br>
        (if you were born EXACTLY at 12:00, then please enter 11:59 or 12:01 — 12:00 is reserved for unknown birth times only)
        <br><br>
        </font>
      </TD>
    </TR>

    <TR>
      <td valign="top">
        <P align="right"><font color="#ff0000">
        <b>IMPORTANT</b>
        </font></P>
      </td>

      <td>
        <font color="#ff0000">
        <b>NOTICE:</b>
        </font>
        <b>&nbsp;&nbsp;West longitudes are MINUS time zones.&nbsp;&nbsp;East longitudes are PLUS time zones.</b>
      </td>
    </TR>

    <TR>
      <td valign="top"><P align="right">Birth time zone:</P></td>

      <TD>
        <select name="timezone" size="1">
          <option value="" selected>Select Time Zone</option>
          <option value="-12" >GMT -12:00 hrs - IDLW</option>
          <option value="-11" >GMT -11:00 hrs - BET or NT</option>
          <option value="-10.5" >GMT -10:30 hrs - HST</option>
          <option value="-10" >GMT -10:00 hrs - AHST</option>
          <option value="-9.5" >GMT -09:30 hrs - HDT or HWT</option>
          <option value="-9" >GMT -09:00 hrs - YST or AHDT or AHWT</option>
          <option value="-8" >GMT -08:00 hrs - PST or YDT or YWT</option>
          <option value="-7" >GMT -07:00 hrs - MST or PDT or PWT</option>
          <option value="-6" >GMT -06:00 hrs - CST or MDT or MWT</option>
          <option value="-5" >GMT -05:00 hrs - EST or CDT or CWT</option>
          <option value="-4" >GMT -04:00 hrs - AST or EDT or EWT</option>
          <option value="-3.5" >GMT -03:30 hrs - NST</option>
          <option value="-3" >GMT -03:00 hrs - BZT2 or AWT</option>
          <option value="-2" >GMT -02:00 hrs - AT</option>
          <option value="-1" >GMT -01:00 hrs - WAT</option>
          <option value="0" >Greenwich Mean Time - GMT or UT</option>
          <option value="1" >GMT +01:00 hrs - CET or MET or BST</option>
          <option value="2" >GMT +02:00 hrs - EET or CED or MED or BDST or BWT</option>
          <option value="3" >GMT +03:00 hrs - BAT or EED</option>
          <option value="3.5" >GMT +03:30 hrs - IT</option>
          <option value="4" >GMT +04:00 hrs - USZ3</option>
          <option value="5" >GMT +05:00 hrs - USZ4</option>
          <option value="5.5" >GMT +05:30 hrs - IST</option>
          <option value="6" >GMT +06:00 hrs - USZ5</option>
          <option value="6.5" >GMT +06:30 hrs - NST</option>
          <option value="7" >GMT +07:00 hrs - SST or USZ6</option>
          <option value="7.5" >GMT +07:30 hrs - JT</option>
          <option value="8" >GMT +08:00 hrs - AWST or CCT</option>
          <option value="8.5" >GMT +08:30 hrs - MT</option>
          <option value="9" >GMT +09:00 hrs - JST or AWDT</option>
          <option value="9.5" >GMT +09:30 hrs - ACST or SAT or SAST</option>
          <option value="10" >GMT +10:00 hrs - AEST or GST</option>
          <option value="10.5" >GMT +10:30 hrs - ACDT or SDT or SAD</option>
          <option value="11" >GMT +11:00 hrs - UZ10 or AEDT</option>
          <option value="11.5" >GMT +11:30 hrs - NZ</option>
          <option value="12" >GMT +12:00 hrs - NZT or IDLE</option>
          <option value="12.5" >GMT +12:30 hrs - NZS</option>
          <option value="13" >GMT +13:00 hrs - NZST</option>
        </select>

        <br>

        <font color="#0000ff">
        (example: Chicago is "GMT -06:00 hrs" (standard time), Paris is "GMT +01:00 hrs" (standard time).<br>
        Add 1 hour if Daylight Saving was in effect when you were born (select next time zone down in the list).
        <br><br>
        </font>
      </TD>
    </TR>
  </table>

  <br>
  <center>
  <font color="#ff0000"><b>Most people mess up the time zone selection. Please make sure your selection is correct.</b></font><br><br>
  <input type="hidden" name="submitted" value="TRUE">
  <INPUT type="submit" name="submit" value="Submit data (AFTER DOUBLE-CHECKING IT FOR ERRORS)" align="middle" style="background-color:#66ff66;color:#000000;font-size:16px;font-weight:bold">
  </center>

  <br>
  </fieldset>
</form>

<?php
include ('footer_data_entry.html');
?>
