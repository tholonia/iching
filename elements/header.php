<?php
if(!isset($_SESSION)){ 
    session_start();
}
#mb_internal_encoding("UTF-8");
#mb_regex_encoding("UTF-8");
?>
<!doctype html>
<html amp>
    <head>
        <meta charset="utf-8">
        <link rel="canonical" href="http://babelbrowser.com">
        <meta name="viewport" content="width=320, initial-scale=1">
        
        
<!-- meta property='og:image' content='https://www.concrete5.org/themes/version_4/images/logo.png' /-->
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>BabelBrowser</title>
        <meta name="description" content="I Ching is a book of wisdom, an oracle a math system, and a philosophy - access all of them here" />

        <!-- vendor includes -->
        
        <script type="text/javascript" src="/vendor/components/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="/vendor/twitter/bootstrap/dist/js/bootstrap.min.js"></script>    
        <script type="text/javascript" src="/vendor/qtip/jquery.qtip.js"></script>
        <script type="text/javascript" src="/js/jQpopup/dist/jquery.fullscreen-popup.js"></script>

        
        <!-- jquery -->
        
	<script type="text/javascript" src="/vendor/jquery-ui/jquery-ui.min.js"></script>        
	<link href="/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet"/>       
        
        <script type="text/javascript" src="/js/jquery.center/jquery.center.js"></script>    
        <script type="text/javascript" src="/js/jquery.redirect/jquery.redirect.js"></script>    

        <!-- local js -->
        
        <script type="text/javascript" src="/js/consult.js"></script>    
        <script type="text/javascript" src="/js/show.js"></script>    
        
        <!-- jquery accordian -->
	
        <script type="text/javascript" src="/js/accordian.js"></script>        
        
        <!-- the astrological calc stuff -->
        
        <script type="text/javascript" src="/astro/js/as.js"></script>
        
        <!--<meta name="viewport" content="width=device-width, initial-scale=1.0">//-->
 
        <link rel="stylesheet" media="screen" type="text/css" href="/css/style.css" />
        <link rel="stylesheet" media="screen" type="text/css" href="/css/drawhex.css" />
        <link rel="stylesheet" media="screen" type="text/css" href="/css/sides.css" />
        
        <!-- show.css if on  the show.php page this if other page -->
        
        <link rel="stylesheet" media="screen" type="text/css" href="/css<?= ($_SERVER['PHP_SELF'] == "/show.php" ? "/show" : "/consult") ?>.css" />

        <?php 
        /* override for the charting pages */
        if ( strpos($_SERVER['PHP_SELF'], "charting")) {
        ?>    
            <style>
                html { 
                    padding:5%;
                    background-color:  #e9e9e9; 
                    background-image:  none;
                }
            </style>
        <?php    
        }
        ?>

        
        <!-- Latest compiled and minified CSS -->

        <link rel="stylesheet" href="/vendor/twitter/bootstrap/dist/css/bootstrap.min.css">

        <!-- accordian     the order of the CSS is not trivial.  They crash into each other easily-->

        <link rel="stylesheet" href="/css/accordian.css">
    </head>
<body>
<?php
/*
<header>
</header>
 * 
 */
?>