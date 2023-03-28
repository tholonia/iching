<?php
/* Edited top work with PHP7 :JWX */

if(!isset($_SESSION)){ 
    session_start();
}
    
  require_once($_SERVER['DOCUMENT_ROOT']."/charting/mysqli_connect_online_calcs_db_MYSQLI.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/charting/my_functions_MYSQLI.php");

  require_once($_SERVER['DOCUMENT_ROOT']."/elements/header.php");

//  include ($_SERVER['DOCUMENT_ROOT']."/charting/home/header.php");

  $username = mysqlSafeEscapeString($conn, $_POST['username']);//JWX
  $password = mysqlSafeEscapeString($conn, $_POST['password']);

//  $username = "jw";
//  $password = "1q2w3e";

  $crypt_pwd = md5($password);

  $sql = "SELECT username FROM astro_member_info WHERE username='$username' And password='$crypt_pwd'";
  $result = @mysqli_query($conn, $sql);
  $num_rows1 = @MYSQLI_NUM_rows($result);

  $row = @mysqli_fetch_array($result);
  $username = $row['username'];

  // now analyze the results
  if ($num_rows1 != 1)
  {
    // cannot find valid user
    echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;I cannot find you as a valid user. Please go back and re-enter your information.";
    exit();
  }
  else
  {
  $_SESSION['username'] = $row['username'];

    //update member_info table in database for this record
    $date_now = date ("Y-m-d");
    $sql = "UPDATE astro_member_info SET last_login='$date_now' WHERE username='$username'";
    $result = @mysqli_query($conn, $sql) or error_log(mysqli_error($conn), 0);

    //example from PHP 4 - $string = 'Porcupine Pie; Vanilla Soup';
    $hashy = $row['username'];// . "; " . $my_hash_padding;
    $_SESSION['username_hash'] = md5($hashy);

    echo "<link href='styles.css' rel='stylesheet' type='text/css' />";

    echo "<div id='content'>";
    echo "<h1>Your personal database </h1>";

    echo "<br><strong>Welcome, " . $_SESSION['username'] . ". You are now logged in.</strong><br><br>";

    include 'logged_in_menu.php';
    echo "</div>";
  }

  include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
?>
