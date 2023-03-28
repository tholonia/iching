<?php
include ($_SERVER['DOCUMENT_ROOT']."/charting/home/header.php");
include ($_SERVER['DOCUMENT_ROOT']."/charting/home/constants.php");


  require_once($_SERVER['DOCUMENT_ROOT']."/charting/mysqli_connect_online_calcs_db_MYSQLI.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/charting/my_functions_MYSQLI.php");

  $username = mysqlSafeEscapeString($conn, $_POST["username"]);
  $password1 = mysqlSafeEscapeString($conn, $_POST["password1"]);
  $password2 = mysqlSafeEscapeString($conn, $_POST["password2"]);
  $email = mysqlSafeEscapeString($conn, $_POST["email"]);

  $pattern = '/.*@.*\..*/';
  if (preg_match($pattern, $email) == 0)
  {
    echo error_message_header();
    echo "<center><br><b>The e-mail address you entered is not valid.<br><br>";
    echo post_back_message('Registration');
    echo "</b></center>";
    include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
    exit();
  }

  if ($password1 != $password2)
  {
    echo error_message_header();
    echo "<center><br><b>Your passwords do not match.<br><br>";
    echo post_back_message('Registration');
    echo "</b></center>";
    include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
    exit();
  }

  $missing_text = "";

  if (!$username)
  {
    $missing_text .= "Username<br>";
  }

  if (!$password1)
  {
    $missing_text .= "Password<br>";
  }


  if ($missing_text != "")
  {
    echo "<center><br><b>You did not fill in the form correctly.<br><br>The following items are either incorrect or missing:<br><br>";
    echo $missing_text . "<br><br>";
    echo post_back_message('Registration');
    echo "</b></center><br><br>";
    include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
    exit();
  }

  $sql = "SELECT * FROM astro_member_info WHERE username='$username'";
  $result = mysqli_query($conn, $sql);

  if ($result)
  {
    $num_rows1 = MYSQLI_NUM_rows($result);
  }
  else
  {
    $num_rows1 = 0;
  }

  if ($num_rows1 > 0)
  {
    echo "<center><br><b>That username is already taken.<br /><br />Please select another and try again.<br><br>";
    echo post_back_message('Sign up');
    echo "</b></center><br><br>";
    include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
    exit();
  }

  $date_now = date ("Y-m-d");

  $crypt_pwd = md5($password1);

  $sql = "INSERT INTO astro_member_info (ID,username,password,email,orig_email,account_opened,last_login,last_transaction) VALUES (0,'$username','$crypt_pwd','$email','$email','$date_now','$date_now','$date_now')";
  $result = mysqli_query($conn, $sql);

//the below is what needs changing according to individual situation - e-mail settings
  $emailTo = EMAIL_ADDRESS;

  $emailFrom =  $email;
  $emailSubject = YOUR_URL . " Registration Form Data";
  $emailText =  "This is the information submitted to " . YOUR_URL . ":\n\n";
//change the above to suit your situation

//here is the data to be submitted
  $emailText .= "Username              = $username \n";
  $emailText .= "E-mail address        = $email \n";
  $emailText .= "Request date          = $date_now \n\n";


  @mail($emailTo, $emailSubject, $emailText, "From: $email");
  
  @mail(EMAIL_ADDRESS, $emailSubject, $emailText, "From: $email");

  echo "<meta HTTP-EQUIV='REFRESH' content='0; url=now_registered.php'>";

  exit();


Function error_message_header()
{
  $msg = "<br><center><b>The following information you submitted to us was either incomplete or invalid:</b></center><br>";
  return($msg);
}

?>

<?php include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php"); ?>

