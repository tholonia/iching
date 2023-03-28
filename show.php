<?php
require "lib/functions.php";
use PHPHtmlParser\Dom;
?>
<html lang="en" class="">
    <head>
        <!-- meta property='og:image' content='https://www.concrete5.org/themes/version_4/images/logo.png' /-->
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>BabelBrowser</title>
        <meta name="description" content="I Ching is a book of wisdom, an oracle a math system, and a philosophy - access all of them here" />
        
        <!-- vendor includes -->
        <script src="/vendor/components/jquery/jquery.min.js"></script>
        <script src="/vendor/twitter/bootstrap/dist/js/bootstrap.min.js"></script>    
        <script type="text/javascript" src="/vendor/qtip/jquery.qtip.js"></script>
        <!-- jquery -->
	<script src="/vendor/jquery-ui/jquery-ui.min.js"></script>        
	<link href="/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet">
        <!-- local js -->
        <script type="text/javascript" src="/js/consult.js"></script>    
        <script type="text/javascript" src="/js/show.js"></script>    
        <!-- accordian -->
        <script src="/js/accordian.js"></script>        

        <!-- overload this if other page -->
        <link rel="stylesheet" media="screen" type="text/css" href="/css/show.css" />
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="/vendor/twitter/bootstrap/dist/css/bootstrap.min.css">
    </head>
    <body>
        <?php dbug($_REQUEST,false); ?>    
        <section>
            <!-- div class="content"><strong> BabelBrowser's I Ching page</strong></div-->
        </section>
        <section id="pageContent">
            <span class="floatingcontainer">
                <?php
                $usebin = 0;
                $hexNum = 1;
                $binNum = 0;

                if (isset($_REQUEST['hex'])) {
                    $hexNum = $_REQUEST['hex'];
                    $binNum = $GLOBALS['dbh']->chex2bin($hexNum);
                }
                if (isset($_REQUEST['bin'])) {
                    $binNum = $_REQUEST['bin'];
                    $hexNum = $GLOBALS['dbh']->cbin2hex($binNum);
                }
                if (isset($_REQUEST['submit'])) {
                    if ($_REQUEST['submit'] == "Hex >") {
                        $hexNum++;
                        $hexNum = ($hexNum > 64 ? 1 : $hexNum);
                        $binNum = $GLOBALS['dbh']->chex2bin($hexNum);
                        $usebin = 0;
                    }
                    if ($_REQUEST['submit'] == "< Hex") {
                        $hexNum--;
                        $hexNum = ($hexNum < 1 ? 64 : $hexNum);
                        $binNum = $GLOBALS['dbh']->chex2bin($hexNum);
                        $usebin = 0;
                    }
                    if ($_REQUEST['submit'] == "Bin >") {
                        $binNum++;
                        $binNum = ($binNum > 63 ? 0 : $binNum);
                        $hexNum = $GLOBALS['dbh']->cbin2Hex($binNum);
                        $usebin = 1;
                    }
                    if ($_REQUEST['submit'] == "< Bin") {
                        $binNum--;
                        $binNum = ($binNum < 0 ? 63 : $binNum);
                        $hexNum = $GLOBALS['dbh']->cbin2Hex($binNum);
                        $usebin = 1;
                    }
                } else {
                    $_REQUEST['submit'] = "nosubmit";
                }
                if (isset($_REQUEST['gotohex'])) {
                    if ($_REQUEST['submit'] == "Go To Hex") {
                        $hexNum = ($_REQUEST['gotohex']);
                        $binNum = $GLOBALS['dbh']->chex2bin($hexNum);
                        $usebin = 0;
                    }
                }
                if (isset($_REQUEST['gotobin'])) {
                    if ($_REQUEST['submit'] == "Go To Bin") {
                        $binNum = ($_REQUEST['gotobin']);
                        $hexNum = $GLOBALS['dbh']->cbin2hex($binNum);
                        $usebin = 1;
                    }
                }
                ?>
                <form method="POST" action="">
                    <span class="question text_mdcaps"></span>
                    <span class="text_md-caps btn btn-danger" ><a style="color:white" target="blank_" href="/consult">CONSULT</a></span>
                    <input class="text_md-caps btn btn-primary" type="submit" name="submit" value="< Hex">
                    <input class="text_md-caps btn btn-success" type="submit" name="submit" value="Hex >">
                    <input class="text_md-caps btn btn-primary" type="submit" name="submit" value="< Bin">
                    <input class="text_md-caps btn btn-success" type="submit" name="submit" value="Bin >">

                    <input type="hidden" name="hex" value="<?= $hexNum ?>">
                    <input type="text" class = "doublenum"  name="gotohex" value="<?= $hexNum ?>">
                    <input class="text_mdcaps btn btn-info" style="color:black" type="submit" name="submit" value="Go To Hex">

                    <input type="hidden" name="bin" value="<?= $binNum ?>">
                    <input type="text" class = "doublenum" name="gotobin" value="<?= $binNum ?>">
                    <input class="text_mdcaps btn btn-info" style="color:black" type="submit" name="submit" value="Go To Bin">
                    <?php
                    $currentTerm = (isset($_REQUEST['search']) ? str_replace("\"","",$_REQUEST['search']) : null);
                    ?>
                    <input type="text" id="searchterm"  class = "search" placeholder="enter text" name="search" value="<?= $currentTerm ?>">
                    <img style="width:25px" id="quotes" src="/images/quotes.png">
                    <input class="text_mdcaps btn btn-info" style="color:black" type="submit" name="submit" value="Search">
                    <a id="searchtip" class="searchtip"  href="#"><img src="/images/qmark.png"></a> 
                </form>
            </span>
            <?php
            if ($_REQUEST['submit'] == "Search") {
                $search = ($_REQUEST['search']);
                print "<div class='container'><div>\n";
                echo $GLOBALS['dbh']->subSearch($search);
                print "</div></div>\n";
            }
            ?>
            <div class="container">
                <?php
                $ary = null;
                if ($usebin == 1) {
                    $ary = $GLOBALS['dbh']->getBin($binNum);
                }
                if ($usebin == 0) {
                    $ary = $GLOBALS['dbh']->getHex($hexNum);
                }
                $t = $ary[0];
                if ($_REQUEST['submit'] != "Search") {
                    if (isset($t['fix'])) {?>
                        <div class="content btn btn-danger">FIX :<?= $t['fix'] ?></div>
                        <?php 
                    } 

                    $dom = new Dom;

                    $filename = "book/ichingbook/_book/hexagrams/" . f($hexNum) . "-" . $t['filename'] . ".html";
                    $dom->load(file_get_contents($filename));
                    $c = $dom->find("#book-search-results > div.search-noresults > section");

                    echo $c->innerHtml();
                }?>                   
            </div>  
            <div style="display:none"> 
               <!-- ----------------------------------------------------------------------->
                <div id="searchtipmsg" title="Searching">
                    <p>
                        SEARCH performs a separate search for each section of data, and shows the results on a per sections basis.  
                    </p>
                    <p>
                        Search terms in quotes, like <em>"tree on top of mountain"</em> will search for the entire phrase only.  Without quotes, it will search for every word.  Common words, like <i>on</i>, <i>of</i>, <i>a</i>, etc., are deliberatly not ignored.
                    </p>
                    <p>
                        If you hit &lt;ENTER&gt; after entering a search term it will NOT do a search and go to whatever hexagram number is in the hex number box, or, if blank, go to hexagram 1.  You must click the SEARCH button to start the search. 
                    </p>
                </div>
            </div>
        </section>
        <footer>
                <!--address>
                        <!-- a href="mailto:duncan.stroud@gmail">Contact</a>
                </address-->
        </footer>
    </body>
</html>