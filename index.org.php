
<?php
require get_cfg_var("iching_root") . "/elements/header.php";
require get_cfg_var("iching_root") . "/vendor/autoload.php";
require get_cfg_var("iching_root") . "/conf/config.php";
require get_cfg_var("iching_root") . "/lib/init.php";
require get_cfg_var("iching_root") . "/lib/functions.php";

//var_dump($_dbh);
$a = null;
?>

<section>
    <!-- div class="content"><strong> The Dharma Clock Project's I Ching page</strong></div-->
</section>

<section id="pageContent">

    <div class="container">

        <div class="row1">
            <span class="btn btn-warning"><a href="index.php">RESET</a></span>
            <span class="btn btn-danger"><a style="color:white;font-weight: bold;" href="/book/ichingbook/_book/">DOCS</a></span>
            <span class="btn btn-danger"><a style="color:white;" href="show.php<?= (isset($_REQUEST['hex']) ? "?hex=" . $_REQUEST["hex"] : '') ?>" >Browse</a></span>    <p>
        </div>

        <!-- ------------------------------------------------------------>

        <?php
        //var_dump($_REQUEST);
        if (!isset($_REQUEST['flipped'])) {
            ?>
            <div class="qbox">

                <form id = "tosstype" method="POST" action="">
                    <input type="hidden" name="flipped" value="1">
                    <div class="row2">
                        <input id="qfield" type="text" name="question" placeholder="question" value=""></p>
                        <!-- a id="testtip" href="#"><img src="images/qmark.png"></a> <input type="radio" name="mode"  id="testmode"  value="testmode" > <span class="text_mdcaps" id="test-modemsg">test-mode</span></p -->

                        <a id="plumtip" class="plumtip" href="#"><img src="images/qmark.png"></a> 
                            <input type="radio" name="mode" id="plum" value="plum" checked > 
                            <span class="text_mdcaps" id="plummsg">Modern Plum</span>    
                        </p>

                        <a id="randomtip" class="randomtip" href="#"><img src="images/qmark.png"></a> 
                            <input type="radio" name="mode" id="random.org" value="random.org"> 
                            <span class="text_mdcaps" id="random.orgmsg">random.org</span>
                        </p>

                        <a id="r-decaytip" class="r-decaytip"  href="#"><img src="images/qmark.png"></a> 
                            <input type="radio" name="mode" id="r-decay" value="r-decay"> 
                            <span class="text_mdcaps" id="r-decaymsg">r-decay</span>
                        </p>

                        <!--a id="entropytip" class="entropytip qtip-content ui-widget-content"  href="#"><img src="images/qmark.png"></a> 
                            <input type="radio" name="mode" id="entropy" value="entropy"> 
                            <span class="text_mdcaps" id="entropymsg">entropy</span>
                        </p -->

                        <span class="text_mdcaps" id="baynesmsg">Wilhelm/Baynes</span> <input type="radio" name="trans" id="baynes" value="baynes" checked > <a id="baynestip" href="#"><img src="images/g-qmark.png"></a></p> 
                        <span class="text_mdcaps" id="aculturalmsg">Acultural</span> <input type="radio" name="trans" id="acultural" value="acultural"  > <a id="aculturaltip" href="#"><img src="images/g-qmark.png"></a></p>


                        <input class = "btn btn-info" style="width:100%" type="submit" value="Cast Coins">
                    </div>
                </form>
                <form method="POST" action="">
                    <input type="hidden" name="flipped" value="1">
                    <div class="row3">
                        <div class="text_smcaps">Or enter 2 hex nums    </div>
                        <input class = "doublenum" id="f_tossed" type="text" name="f_tossed" placeholder="<?php echo rand(1, 64) ?>" value="">
                        <input class = "doublenum" id="f_final" type="text" name="f_final" placeholder="<?php echo rand(1, 64) ?>" value="">
                        <input class = "btn btn-primary" type="submit" value="Show">
                    </div>
                </form>
            </div>
        </div>
        <?php
    } else {
        $_REQUEST['question'] = "no question asked";
        if (!isset($_REQUEST['kua'])) {
            $_REQUEST['kua']="Pen-Kua";
        }
        ?>
        <div class="question"><?= $_REQUEST['question'] ?></div>
        <div class="kua">(<?= $_REQUEST['kua'] ?>)</div>

        <?php
        $ary = null;
        $t = array();
        $f = array();
        $d = array();
        
        $ary = getToss();

        $a = $GLOBALS['dbh']->getAllHexes();

        $t = $ary['tossed'][0];
        $f = $ary['final'][0];
        $d = $ary['delta'];


        // remove whitespces and extention from question to use as filename
        $fn = "questions/" . mb_ereg_replace(" ", "_", $_REQUEST['question'] . ".txt");
        $json = json_encode(array(array('question' => $_REQUEST['question']), $t, $d, $f), JSON_PRETTY_PRINT);
        file_put_contents($fn, $json);
        ?>
        <?php
        // FIX is special column for editing notes
        if (isset($t['fix'])) {
            ?>
            <div class="content btn btn-danger">FIX :<?= $t['fix'] ?></div>
        <?php } ?>

        <?php
        //var_dump($t['binary']);
        //var_dump($d);
        
        $hexes = makeHex(str_split($t['binary']), $d, uniqid(), "fade_final");

        print $hexes;

        $t_hukua = makeHuKua($t['binary']);
        $f_hukua = makeHuKua($f['binary']);
                
        ?>
            
                        
            <div class="container">
             <?php 
             if (!isset($_REQUEST['t'])) {
             ?>
                <a style="font-size:16pt" href='/index.php?t=<?=$t['pseq']?>&f=<?=$f['pseq']?>&flipped=1&kua=Hu-Kua&f_tossed=<?= $t_hukua ?>&f_final=<?= $f_hukua ?>'>View the Hu Kua</a>
                
                        <a id="hukuatip" class="hukuatip"  href="#">
                            <img style="width:20px" src="/images/qmark-small-bw.png">
                            <span id="hukuatipmsg"></span>
                        </a> 
             <?php
             } else {
             ?>
                <a style="font-size:16pt" href='/index.php?flipped=1&f_tossed=<?= $_REQUEST['t'] ?>&f_final=<?= $_REQUEST['f'] ?>'>View the Pen Kua</a>
                
                        <a id="penkuatip" class="penkuatip"  href="#">
                            <img style="width:20px" src="/images/qmark-small-bw.png">
                            <span id="penkuatipmsg"></span>
                        </a> 
            </div>
            <?php
             }
             
             if (!$t['proofed']) {
                 //print "<div class='notice'>This content has yet to be proofed.  Please disregard the typos and other errors.</div>";
                 print "<div class='notice'>status: UNPROOFED</div>";
             }
             
             
             
             ?>
        <!-- div>
            <img class="heximg select" alt="<?= $t['pseq'] ?> / <?= $t['title'] ?>/<?= $t['trans'] ?>" src="images/hex/hexagram<?= sprintf("%02d", $t['pseq']) ?>.png">    
            <img class="heximg" alt="<?= $f['pseq'] ?> / <?= $f['title'] ?>/<?= $f['trans'] ?>" src="images/hex/hexagram<?= sprintf("%02d", $f['pseq']) ?>.png">
        </div -->    
        <div class="tossed">

            <div class="label">Hex # [bin] / Title / Translation</div>
            <div class="content" id="pseq"><?= $t['pseq'] ?> [b:<?= $t['bseq'] ?>]/ <?= $t['title'] ?>/<a target="blank_" href="show.php?hex=<?= (isset($t['pseq']) ? $t['pseq'] : 0) ?>"><?= $t['trans'] ?></a></div>

            <div class="label">The Upper Trigram</div>
            <div class="content" id="tri_upper"><?= $t['tri_upper'] ?></div>

            <div class="label">The Lower Trigram</div>
            <div class="content" id="tri_lower"><?= $t['tri_lower'] ?></div>

            <div class="label">Explanation of the Trigrams</div>
            <div class="content" id="explanation"><?= $t['explanation'] ?></div>

            <div class="label">The Judgment</div>
            <div class="content" id="judge_old"><?= $t['judge_old'] ?></div>

            <?php if (isset($t['comment'])) { ?>
                <div class="label">Comments</div>
                <div class="content comment" id="comment"><?= $t['comment'] ?></div>
            <?php } ?>

            <div class="label">Commentary an Explanation of the Judgement</div>
            <div class="content" id="judge_exp"><?= $t['judge_exp'] ?></div>

            <div class="label">The Ancient Assocated Image</div>
            <div class="content" id="image_old"><?= $t['image_exp'] ?></div>

            <div class="label">Commentary and Explanation of the Image</div>
            <div class="content" id="image_exp"><?= $t['image_exp'] ?></div>
        </div>
        
        
        
        

    <?php
    
    if ($t['bseq'] != $f['bseq'] ) {
        echo "<div> The Moving Lines </div>\n";

        for ($i = 0; $i < 6; $i++) {
            if ($d[$i] == 1) {
                $j = $i + 1;
                //var_dump($j);
                ?>
                    <div class="lines">
                        <div class="label">Line <?= $j ?></div>
                        <div class="content line_title" id="line_<?= $j ?>"><?= $t['line_' . $j] ?></div>

                        <div class="label">Original Text</div>
                        <div class="content line_org" id="line_<?= $j ?>_org"><?= $t['line_' . $j . '_org'] ?></div>

                        <div class="label">Expanded Text</div>
                        <div class="content line_exp" id="line_<?= $j ?>_exp"><?= $t['line_' . $j . '_exp'] ?></div>
                    </div>
                <?php
            }
        }
        ?>
            <?php if (isset($f['fix'])) { ?>
                <div class="content btn btn-danger">FIX :<?= $f['fix'] ?></div>
            <?php } ?>


            <?php
            $hexes = makeHex(str_split($t['binary']), $d, uniqid(), "fade_tossed"
            );
            print "<div class='container'>$hexes</div>";
            
            
            
             if (!$f['proofed']) {
                 //print "<div class='notice'>This content has yet to be proofed.  Please disregard the typos and other errors.</div>";
                 print "<div class='notice'>status: UNPROOFED</div>";
             }
            
            ?>

            <!-- div>
                < img class="heximg" alt="<?= $t['pseq'] ?> / <?= $t['title'] ?>/<?= $t['trans'] ?>" src="images/hex/hexagram<?= sprintf("%02d", $t['pseq']) ?>.png">    
                <img class="heximg select" alt="<?= $f['pseq'] ?> / <?= $f['title'] ?>/<?= $f['trans'] ?>" src="images/hex/hexagram<?= sprintf("%02d", $f['pseq']) ?>.png">
            </div -->    
            <div class="final">
                <div class="label">Hex # [bin]/ Title / Translation</div>
                <div class="content" id="pseq"><?= $f['pseq'] ?> [b:<?= $f['bseq'] ?>]/ <?= $f['title'] ?>/<a target="blank_" href="show.php?hex=<?= (isset($f['pseq']) ? $f['pseq'] : 0) ?>"><?= $f['trans'] ?></a></div>
                <div class="label">The Upper Trigram</div>
                <div class="content" id="tri_upper"><?= $f['tri_upper'] ?></div>
                <div class="label">The Lower Trigram</div>
                <div class="content" id="tri_lower">Below: <?= $f['tri_lower'] ?></div>
                <div class="label">Explanation of the Trigrams</div>
                <div class="content" id="explanation"><?= $f['explanation'] ?></div>
                <div class="label">The Judgment</div>
                <div class="content" id="judge_old"><?= $f['judge_old'] ?></div>

                <?php if (isset($f['comment'])) { ?>
                    <div class="label">Comments</div>
                    <div class="content comment" id="comment"><?= $f['comment'] ?></div>
                <?php } ?>

                <div class="label">Commentary an Explanation of the Judgement</div>
                <div class="content" id="judge_exp"><?= $f['judge_exp'] ?></div>
                <div class="label">The Ancient Assocated Image</div>
                <div class="content" id="image_old"><?= $f['image_exp'] ?></div>
                <div class="label">Commentary and Explanation of the Image</div>
                <div class="content" id="image_exp"><?= $f['image_exp'] ?></div>
            </div>

            <!--  end of cast -->
            
            
                
                
                
        <?php
        }else {
                echo "<div style='font-size:18pt'> There are no Moving Lines </div>\n";
            } 
        
        $ti = intval($t['bseq']);
        $fi = intval($f['bseq']);
        ?>
            <div class="extra">
                Two points form a line, three points form a direction, a flow. <br>
                The the binary values of the two hexagrams are added, you get...<p>
                <?php
                $added = $ti + $fi;
                $added = ($added > 63 ? $added - 63 : $added);
                outProc1($a, $added);
                ?>
                <hr>
                Our present is different from our past based on what motivated us in the past to make the decisions that lead us to where we are today.  The rose is red because it absorbs the green. <br>
                The 2nd hex subtracted from the 1st hex...<p>
                    <?php
                    $subFfrT = c_sub($ti, $fi);
                    outProc1($a, $subFfrT);
                    ?>
                <hr>
                By removing what was before from the now, we can see what it is that has developed in us.<br> 
                The 1st hex subtracted from the 2nd hex...<p>
                    <?php
                    $subTfrF = c_sub($fi, $ti);
                    outProc1($a, $subTfrF);
                    ?>
                <hr>
                <DIV style="background-color:yellow"> The following shows how you can get from the final hexagram to the next hexagram of your choice. </div>
                <hr>

                    <?php
                    $h_receptive = 2;
                    $b_receptive = $GLOBALS['dbh']->chex2bin($h_receptive);
                    $toReceptive = c_sub($fi, $b_receptive);
                    echo fromtoprint($b_receptive, $h_receptive, $f);
                    //                    var_dump($toReceptive);
                    outProc1($a, $toReceptive);
                    ?>
                <hr>

                <?php
                $h_peace = 11;
                $b_peace = $GLOBALS['dbh']->chex2bin($h_peace);
                $toPeace = c_sub($fi, $b_peace);
                echo fromtoprint($b_peace, $h_peace, $f);
                //                    var_dump($toPeace);
                outProc1($a, $toPeace);
                ?>
                <hr>
                <?php
                $h_completion = 63;
                $b_completion = $GLOBALS['dbh']->chex2bin($h_completion);
                $toCompletion = c_sub($fi, $b_completion);
                echo fromtoprint($b_completion, $h_completion, $f);
                outProc1($a, $toCompletion);
                ?>
                <hr>
                <?php
                $h_creative = 1;
                $b_creative = $GLOBALS['dbh']->chex2bin($h_creative);
                $toCreative = c_sub($fi, $b_creative);
                echo fromtoprint($b_creative, $h_creative, $f);
                outProc1($a, $toCreative);
                ?>
                <hr>
            </div>
        </div>
            <?php } 
    
    
    ?>
</section>


<div id="xsubtipmsg" title="Transitional Hexagram">
    <p>
        This is the transitional hexagram that is the difference between the original and the resulting hexagram.  Typically this transition is represented only by the moving lines of the original hexagram.  We 
        arrive at this transition hexagram by subtracting the binary value of 
        the original hex from the final hex (and add 63 is less than zero) 
        final hexagram.  In this way, this transitional hex is the hexagram version of the moving lines.
    </p>
</div>

<div id="plumtipmsg" title="Modern 'Plum Blossom'">
    <p>
        The Modern Plum technique is based on the ancient Mei Hua ("Plum Blossom") 
        method of the Sung Dynasty (920-1279ad).  It uses the current time as the "seed" for the casting.  This modern version also uses the current time of a number of milliseconds since Jan. 1, 1970. An 
        algorithm takes that number, <a href="/book/ichingbook/_book/instructions.html">
            transforms it to simulate three coins</a>.  This is done six time, 
        with a random number of milliseconds between each "toss".
    </p>
</div>

<div id="testtipmsg" title="Test Data">
    <p>
        This randomly generates hexagrams using the PHP rand() function.  Mainly used for its speed as it does not access any services. Not a good option for proper use.
    </p>
</div>

<div id="randomtipmsg" title="Better Random Numbers">
    <p>
        The "flipping" is actually being done by Random.Org, who are so meticulous 
        about the quality and integrity of their randomness that they actually have 
        different result based on the type of coin you use. For now, we are using 
        three Bronze Sestertius coins from the Roman Empire of Antoninus Pius
        <img src="/images/reverse.png" style="width:60px;height:60px;padding:5px;float:right;">
        <img src="/images/obverse.png" style="width:60px;height:60px;padding:5px;float:right;">
    </p>
</div>

<div id="entropytipmsg" title="Entropy">
    <p>
        CURRENTLY DISABLED - Entropy is how random numbers are generated for encryption 
        purposed.  This randomness is often collected from hardware sources (variance in fan noise or HDD), either pre-existing ones such as mouse movements or specially provided randomness generators.  The problem with this method is it takes time to "collect" entropy.  If it is all used up, it could take many 
        minutes to collect enough too throw the I Ching
    </p>
</div>

<div id="r-decaytipmsg" title="True Random Numbers">
    <p>
        This is the "real" random, as it is theoretically impossible to predict decay.  We use the 
        <a href="http://www.fourmilab.ch/">Fermi Lab</a> 'HotBits' service, which provides these types of random numbers.
    </p>
</div>

<div id="baynestipmsg" title="Wilhelm / Baynes Translation">
    <p>
        This is the popular, traditional English translation of the German translation 
        of the original Chinese text brought back from China by the Jesuits
    </p>
</div>

<div id="aculturaltipmsg" title="Acultural Interpretation">
    <p>
        CURRENTLY UNAVAILABLE - This is for the upcoming new translation that redefines 
        the structure and the relationship of the hexagrams outside of the highly moral 
        Confucian version, which is the only one that survived to this day.  The Lao Tzu 
        version, undoubtedly less moralistic and judgmental, did not
    </p>
</div>
<div id="hukuamsg" title="The Hu Kua">
    <p>
        The typical hexagrams (the "Pen Kua") show the beginning, middle, and end of a situation, but there is another, hidden, story to be told.  We can see this hidden story in the "Hu Kua".  This is where we take the 2nd, 3rd, and 4th lines and make the lower trigram of a new hexagram, and then take the 3rd, 4th, and 5th lines to create an upper trigram.  This newly created hexagram is the "Hu Kua".
        <img src="/images/hukua.png">
    </p>
</div>
<div id="penkuamsg" title="The Hu Kua">
    <p>
        The "Pen Kua" are the hexagrams arrived at by tossing coins or yarrow sticks, etc. This is the most common form of the hexagrams; the ones we usually think of when he think of the I Ching.
    </p>
</div>

<?php
require "elements/footer.php";
?>
