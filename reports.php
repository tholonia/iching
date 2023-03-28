<?php

require $_SERVER['DOCUMENT_ROOT']."/lib/functions.php";

/* 'iching_root' is defined in the php.ini file, this way is it always correct 
 * for whatever maching is being used 
 */

require getRootDir(). "/elements/header.php";

$reports = 0;

if (isset($_REQUEST['message'])) {
    if ($_REQUEST['message'] = "messages") {
        $reports = 1;
    }
}

$reportData = null;
//if ($reports == 1) {
    
    $reportData = $GLOBALS['dbh']->getSuggestions();
//}

?>
<div style="position:fixed;top:0x;left:0px;z-index:100" class="btn btn-info"><a href="/">BACK</a></div>
    <?php

print "<pre>";
print_r($reportData);

