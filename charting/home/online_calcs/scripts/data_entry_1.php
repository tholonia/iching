<?php
  include ('../accesscontrol.php');

  if ($is_logged_in == False)
  {
    echo "You are not yet logged in.";
    exit();
  }

  include ('../constants.php');           //nedded because of "../footer.html" statements
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Data Entry Form - New Record</title>

<style type="text/css">
.my_h3 {
  background-color: #EEEEEE;
  font-size: 11px;
  font-weight: normal;
  padding: 16px;
  margin: 0px 150px;
}
</style>
</head>

<!-- Background white, links blue (unvisited), navy (visited), red (active) -->
<body bgcolor="#c0d0ff" text="#000000" link="#0000ff" vlink="#000080" alink="#ff0000">

<link href='styles.css' rel='stylesheet' type='text/css' />
<div id='header'><img border='0' src='../images/dummy_logo.jpg' alt='Astrology scripts'></div>


<div id='content'>
<h1>Your Personal Database</h1>
<p><strong>Note: All fields are required</strong></p>
</div>

<p class="my_h3"><strong>Please double-check your data when you enter it so you are not doing incorrect charts. After entering state/country and city, then clicking on the city you want, you should NOT need to change the time zone, longitude, or latitude.</p>

<br>

<form name="astro_input_form" action="add_to_db_1.php" method="POST" style='margin: 0px 150px;'>
 <fieldset><legend><font size='4'><b>Enter your birth data here</b></font></legend>
  &nbsp;<font color='#ff0000'><b>All fields are required</b></font><br>

  <table style='font-size:12px;'>
    <tr>
      <td>
        <P align='right'>Name:</P>
      </td>

      <td>
        <input type='text' size='28' maxlength='40' id='name' name='name'>
      </td>
    </tr>
  
    <tr>
      <td>
        <P align='right'>Sex:</P>
      </td>

      <td>
        <input type='text' size='2' maxlength='1' id='sex' name='sex'> (m for male and f for female)
      </td>
    </tr>
  
    <tr>
      <td>
        &nbsp;
      </td>

      <td>
        &nbsp;
      </td>
    </tr>

    <tr>
      <td>
        <P align='right'>Birth month:</P>
      </td>
  
      <td>
        <input type='text' size='5' maxlength='2' id='month' name='month'> (1 - 12)
      </td>
    </tr>
  
    <tr>
      <td>
        <P align='right'>Birth day:</P>
      </td>

      <td>
        <input type='text' size='5' maxlength='2' id='day' name='day'> (1 - 31)
      </td>
    </tr>
  
    <tr>
      <td>
        <P align='right'>Birth year:</P>
      </td>
  
      <td>
        <input type='text' size='5' maxlength='4' id='year' name='year'> (1200 - 2399)
      </td>
    </tr>
  
    <tr>
      <td>
        &nbsp;
      </td>

      <td>
        &nbsp;
      </td>
    </tr>

    <tr>
      <td>
        <P align='right'>Birth hour:</P>
      </td>
    
      <td>
        <input type='text' size='4' maxlength='2' id='hour' name='hour'> (1 - 12)
        &nbsp;(if you do not know the birth time, then enter 12:00 [for noon] and then select 'Unknown')
      </td>
    </tr>
  
    <tr>
      <td>
        <P align='right'>Birth minute:</P>
      </td>
  
      <td>
        <input type='text' size='4' maxlength='2' id='minute' name='minute'> (0 - 59)
      </td>
    </tr>
  
    <tr>
      <td>
        &nbsp;
      </td>

      <td>
        <select id="amorpm" name="amorpm" size="3">
          <option value="AM"> AM </option>
          <option value="PM"> PM </option>
          <option value="unknown"> Unknown </option>
        </select>
      </td>
    </tr>
    
    <tr>
      <td>
        &nbsp;
      </td>

      <td>
        &nbsp;
      </td>
    </tr>

    <tr>
      <td colspan='2' align='center' style='font-family:Arial'>
        <b>Please fill out the below and click NEXT in order to get your latitude, longitude, and time zone data.</b>
        <br>
        <iframe src="http://www.astrotheme.fr/partenaires/atlas.php?partenaire=9999&lang=en" frameborder="0" width="440" height="345"></iframe>
      </td>
    </tr>

    <tr>
      <td colspan='2' align='center' style='font-family:Arial'>
        <b>When you have completed filling out the above form and clicked NEXT, you should see something that looks like the below. The important 
        information you want is the data in the "Latitude:", "Longitude:", and Time Difference:" lines. Copy this information 
        into the textboxes below. For example, if the "Time Difference:" is shown as '5W00', then type in "-5" (without the quotes) in the 
        'Time zone' textbox. If the "Time Difference:" is shown as '3E00', then type in "3" (without the quotes) in the 
        'Time zone' textbox.</b>
      </td>
    </tr>

    <tr>
      <td colspan='2' align='center'>
        <img src='../images/atlas_example.jpg'>
        <br><br>
        <b><font color='#ff0000'>Using the sample data above as an example, here is how you should fill in the textboxes below.</font></b>
        <br><br>
        <img src='../images/textbox_entries.jpg' border='1'>
        <br><br><br>
        <b><font color='#ff0000'>Now you fill out the textboxes below with your own data.</font></b>
        <br><br>
      </td>
    </tr>

    <tr align='center'>
      <td colspan='2'>
        Time zone: <input size='10' id='timezone' name='timezone' style='text-align:center;'> (W is a minus number and E is a positive number)
      </td>
    </tr>

    <tr align='center'>
      <td colspan='2'>
        Longitude: <input maxlength='3' size='3' id='long_deg' name='long_deg' style='text-align:center;'>&nbsp;
        <input maxlength='1' size='1' id='ew' name='ew' style='text-align:center;'>&nbsp;
        <input maxlength='2' size='2' id='long_min' name='long_min' style='text-align:center;'> (the format here is like 88 W 37 - don't change the order)
      </td>
    </tr>

    <tr align='center'>
      <td colspan='2'>
        Latitude: <input maxlength='3' size='3' id='lat_deg' name='lat_deg' style='text-align:center;'>&nbsp;
        <input maxlength='1' size='1' id='ns' name='ns' style='text-align:center;'>&nbsp;
        <input maxlength='2' size='2' id='lat_min' name='lat_min' style='text-align:center;'> (the format here is like 42 N 1 - don't change the order)
      </td>
    </tr>

    <tr>
      <td>
        &nbsp;
      </td>

      <td>
        &nbsp;
      </td>
    </tr>
  </table>
  
  <input type="hidden" name="submitted" value="True">
  <center>
  <input type="submit" value="Add the above data to your database" style='background-color:#77ff77;height:45px;color:#000000;font-size:14px;font-weight:bold'>&nbsp;&nbsp;
  <input type="reset" value="Reset" style='background-color:#ff7777;height:45px;color:#000000;font-size:14px;font-weight:bold'>
  </center>
 </fieldset>
</form>

<?php
  include ('../footer.html');
?>

</body>
</html>

