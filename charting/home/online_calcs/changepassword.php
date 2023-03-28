<?php

include 'accesscontrol.php';
include ($_SERVER['DOCUMENT_ROOT']."/charting/home/header.php");
include ($_SERVER['DOCUMENT_ROOT']."/charting/home/constants.php");

$background_color = BACKGROUND_COLOR;


require_once($_SERVER['DOCUMENT_ROOT']."/charting/mysqli_connect_online_calcs_db_MYSQLI.php");
require_once($_SERVER['DOCUMENT_ROOT']."/charting/my_functions_MYSQLI.php");

if ($_POST['submitted'] == "change_pwd")
{
  echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'>";
    echo "<tr>";
      echo "<td>";
        echo "<center>";
        echo "<strong><font color='ff0000' size='+3'>Password Change</font></strong>";
        echo "</center>";
      echo "</td>";
    echo "</tr>";
  echo "</table>";

  $username = mysqlSafeEscapeString($conn, $_POST["username"]);
  $password = mysqlSafeEscapeString($conn, $_POST["password"]);
  $password1 = mysqlSafeEscapeString($conn, $_POST["password1"]);
  $password2 = mysqlSafeEscapeString($conn, $_POST["password2"]);

  if (!$username Or !$password Or !$password1 Or !$password2)
  {
    echo "<center><br><b>Please supply all requested data.<br><br>";
    echo post_back_message('Change password');
    echo "</b></center><br><br>";
    include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
    exit();
  }

  if ($password1 != $password2)
  {
    echo "<center><br><b>The two instances of your new password do not agree.<br><br>";
    echo post_back_message('Change password');
    echo "</b></center><br><br>";
    include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
    exit();
  }

  $crypt_pwd = md5($password);

  $sql = "SELECT ID FROM astro_member_info WHERE username='$username' And password='$crypt_pwd'";
  $result = mysqli_query($conn, $sql);
  $row = @mysqli_fetch_array($result);

  if ($result)
  {
    $num_rows1 = MYSQLI_NUM_rows($result);
  }
  else
  {
    $num_rows1 = 0;
  }

  if ($num_rows1 != 1)
  {
    echo "<br><center><font size=5 color=#'000000'><b>We cannot find a matching username/password record.<br>Please re-enter your correct data.</font><br><br>";
    echo "<font size=3>";
    echo post_back_message('Change password');
    echo "</b></font></center><br><br>";
    include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
    exit();
  }

  $id = $row['ID'];
  $crypt_pwd = md5($password1);

  $sql = "UPDATE astro_member_info SET password='$crypt_pwd' WHERE ID='$id'";
  $result = @mysqli_query($conn, $sql) or die('Sorry, but I cannot complete your update - your password was NOT changed. If you like, please try again.');


  echo "<center><font FACE='Verdana' size='6' color='#a30101'>Your password has been changed.<br>Please remember it.</font></center><br><br><br>";

  echo "<center><a href='signup_login.php'>Return to login page</a></center><br>";
  include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
  exit();
}
else
{
  ?>
  <style type='text/css'>
  .pa_textbox
  {
    FONT-WEIGHT: 400; FONT-SIZE: 11px; COLOR: #000000; FONT-FAMILY: Verdana, Arial, sans-serif
  }
  </style>

  <div id="content">

  <TABLE bgcolor="<?php echo $background_color; ?>" align=center cellSpacing=0 cellPadding=3 border=0>
    <tr>
      <td>
        <FORM name="form3" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" target="_blank">
        <TABLE cellSpacing=0 cellPadding=5 border=0>
          <TR>
            <TD align='center' colspan='2'>
              <center>
              <h4>Change your password?</h4>
              </center>
            </TD>
          </TR>

          <TR>
            <TD align='right'>
              <SPAN class='pa_textbox'>Username:</span>
            </TD>
            <TD align='left'>
              <INPUT class='pa_textbox' maxLength=16 size=16 name=username style="FONT-WEIGHT: 400; FONT-SIZE: 11px; COLOR: #000000; FONT-FAMILY: Verdana, Arial, sans-serif">
            </TD>
          </TR>

          <TR>
            <TD align='right'>
              <SPAN class='pa_textbox'>Old password:</span>
            </TD>
            <TD align='left'>
              <INPUT class='pa_textbox' type=password maxLength=16 size=16 name=password>
            </TD>
          </TR>

          <TR>
            <TD align='right'>
              <SPAN class='pa_textbox'>New password:</span>
            </TD>
            <TD align='left'>
              <INPUT class='pa_textbox' type=password maxLength=16 size=16 name=password1>
            </TD>
          </TR>

          <TR>
            <TD align='right'>
              <SPAN class='pa_textbox'>New password (again):</span>
            </TD>
            <TD align='left'>
              <INPUT class='pa_textbox' type=password maxLength=16 size=16 name=password2>
            </TD>
          </TR>

          <TR>
            <TD align='center' colspan='2'>
              <input type="hidden" name="submitted" value="change_pwd">
              <br><INPUT type=submit value='Change my password' style="width: 270px; BORDER-TOP-WIDTH: 1px; FONT-WEIGHT: bold; BORDER-LEFT-WIDTH: 1px; FONT-SIZE: 11px; BORDER-LEFT-COLOR: #ff9eb9; BACKGROUND: #b70000 no-repeat 5px 3px; BORDER-BOTTOM-WIDTH: 1px; BORDER-BOTTOM-COLOR: #990049; COLOR: #ffffff; BORDER-TOP-COLOR: #ff9eb9; FONT-FAMILY: Verdana, Arial, sans-serif; BORDER-RIGHT-WIDTH: 1px; BORDER-RIGHT-COLOR: #990049">
            </TD>
          </TR>
        </TABLE>
        </FORM>
      </td>
    </tr>
  </table>

  <p>&nbsp;</p>

  </div>

  <?php
}

include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");

?>
