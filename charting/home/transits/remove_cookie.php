<?php
  // remove cookie containing natal data here
  setcookie ('name', '', time() - 300, '/', '', 0);
  setcookie ('month', '', time() - 300, '/', '', 0);
  setcookie ('day', '', time() - 300, '/', '', 0);
  setcookie ('year', '', time() - 300, '/', '', 0);
  setcookie ('hour', '', time() - 300, '/', '', 0);
  setcookie ('minute', '', time() - 300, '/', '', 0);
  setcookie ('timezone', '', time() - 300, '/', '', 0);

  include ('header_for_transits.html');

  echo '<br><br><center><a href="index.php">Click here to return to the data entry form</a><br>(You may have to hit REFRESH after returning)</center><br><br>';

  include ('footer_data_entry.html');
?>
