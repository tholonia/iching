<?php

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


  include ('header_for_transits.html');

  // calculate astronomic data
  $swephsrc = './';
  $sweph = './';
  $num_planets = 11;

  // Unset any variables not initialized elsewhere in the program
  unset($PATH,$out,$longitude,$speed);

  //get date and time right now
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
  exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789D -eswe -fPls -g, -head", $out);

  // Each line of output data from swetest is exploded into array $row, giving these elements:
  // 0 = planet name
  // 1 = longitude
  // 2 = speed
  // planets are index 0 - index ($num_planets - 1)
  foreach ($out as $key => $line)
  {
    $row = explode(',',$line);
    $longitude[$key] = $row[1];
    $speed[$key] = $row[2];
  };


  // display date and time for NOW
  echo "<center>";
  echo "Date and time of transits = " . $transit_date_time . " GMT<br><br>";
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
  $pl_name[10] = "Chiron";


  // find retrograde planets
  $retrograde = "";
  for ($i = 0; $i <= $num_planets - 1; $i++)
  {
    if ($speed[$i] < 0)
    {
      $retrograde = $retrograde . "R";
    }
    else
    {
      $retrograde = $retrograde . " ";
    }
  }


  // display chartwheel
  echo "<center>";
  echo "<img border='0' src='chartwheel.php?in_test=0&rx=$retrograde&p0=$longitude[0]&p1=$longitude[1]&p2=$longitude[2]&p3=$longitude[3]&p4=$longitude[4]&p5=$longitude[5]&p6=$longitude[6]&p7=$longitude[7]&p8=$longitude[8]&p9=$longitude[9]&p10=$longitude[10]' width='640' height='640'>";
  echo "</center>";

  echo "<br><br>";


  //display natal data
  echo '<center><table width="30%" cellpadding="0" cellspacing="0" border="0">',"\n";

  echo '<tr>';
  echo "<td><font color='#0000ff'><b> Name </b></font></td>";
  echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
  echo "<td>&nbsp;</td>";
  echo '</tr>';

  for ($i = 0; $i <= $num_planets - 1; $i++)
  {
    echo '<tr>';
    echo "<td>" . $pl_name[$i] . "</td>";

    if ($speed[$i] < 0)
    {
      echo "<td><font face='Courier New'>" . Convert_Longitude($longitude[$i]) . " R" . "</font></td>";
    }
    else
    {
      echo "<td><font face='Courier New'>" . Convert_Longitude($longitude[$i]) . "</font></td>";
    }
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

  include ('footer_data_entry.html');

?>
