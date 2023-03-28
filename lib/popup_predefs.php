<?php /* wrap all these in a hidden div so they don't show up for a split secnd on the homepage */ ?>
<div style="display:none"> 

    <!-- ----------------------------------------------------------------------->
    <div id="xsubtipmsg" title="Transitional Hexagram">
        <p>
            This is the transitional hexagram that is the difference between the original and the resulting hexagram.  Typically this transition is represented only by the moving lines of the original hexagram.  We 
            arrive at this transition hexagram by subtracting the binary value of 
            the original hex from the final hex (and add 63 is less than zero) 
            final hexagram.  In this way, this transitional hex is the hexagram version of the moving lines.
        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
    <div id="plumtipmsg" title="Modern 'Plum Blossom'">
        <p>
            The Modern Plum technique is based on the ancient Mei Hua ("Plum Blossom") 
            method of the Sung Dynasty (920-1279ad).  It uses the current time as the "seed" for the casting.  This modern version also uses the current time of a number of milliseconds since Jan. 1, 1970. An 
            algorithm takes that number, <a href="/book/ichingbook/_book/instructions.html">
                transforms it to simulate three coins</a>.  This is done six time, 
            with a random number of milliseconds between each "toss".
        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
    <div id="testtipmsg" title="Test Data">
        <p>
            This randomly generates hexagrams using the PHP rand() function.  Mainly used for its speed as it does not access any services. Not a good option for proper use.
        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
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
    <!-- ----------------------------------------------------------------------->
    <div id="entropytipmsg" title="Entropy">
        <p>
            CURRENTLY DISABLED - Entropy is how random numbers are generated for encryption purposes.  This randomness is often collected from hardware sources (variance in fan noise or HDD), either pre-existing ones such as mouse movements or specially provided randomness generators.  The problem with this method is it takes time to "collect" entropy.  If it is all used up, it could take many 
            minutes to collect enough too throw the I Ching
        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
    <div id="r-decaytipmsg" title="True Random Numbers">
        <p>
            This is the "real" random, as it is theoretically impossible to predict decay.  We use the 
            <a href="http://www.fourmilab.ch/">Fermi Lab</a> 'HotBits' service, which provides these types of random numbers.
        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
    <div id="baynestipmsg" title="Wilhelm / Baynes Translation">
        <p>
            This is the popular, traditional English translation of the German translation 
            of the original Chinese text brought back from China by the Jesuits
        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
    <div id="aculturaltipmsg" title="Acultural Interpretation">
        <p>
            LIMITED DATA - This is an alternative interpretation, more modern and culturally
            relevant, that the highly moral 
            Confucian version, which is the only one traditional interpretation that has survived to this day.            
        </p>
        <p>
            This is a work-in-progress, so there are a some (many) fields that are not yet available

        </p>
    </div>    <!-- ----------------------------------------------------------------------->
    <div id="qabalahtipmsg" title="Qabalistic Cross Ref">
        <p>
            Because of the uncannily perfect relationship between the I Ching's 22 pairs of imbalanced hexagrams and 
            10 pairs of balanced hexagrams with the Qabalah's 22 dynamic paths and 10 static states, I have 
            included some qabalistic cross-references.            
        </p>
        <p>
            This is a work-in-progress, so more will be added with time.

        </p>
    </div>    <!-- ----------------------------------------------------------------------->
    <div id="astrotipmsg" title="Planetary Positions">
        <p>
            These are numbers generated from the current, exact, location of the planets. 
            We use the classical astrological assignment the six planets that make up the zodiac.  
            For more on this see the 
            <a href="/book/ichingbook/_book/instructions.html#planetary">documentation</a>. 
            <br>
            This option is also a good choice for throwing the hexagrams of "now".

        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
    <div id="hukuamsg" title="The Hu Kua">
        <p>
            The typical hexagrams (the "Pen Kua") show the beginning, middle, and end of a situation, but there is another, hidden, story to be told.  We can see this hidden story in the "Hu Kua".  This is where we take the 2nd, 3rd, and 4th lines and make the lower trigram of a new hexagram, and then take the 3rd, 4th, and 5th lines to create an upper trigram.  This newly created hexagram is the "Hu Kua".
            <img src="/images/hukua.png">
        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
    <div id="penkuamsg" title="The Hu Kua">
        <p>
            The "Pen Kua" are the hexagrams arrived at by tossing coins or yarrow sticks, etc. This is the most common form of the hexagrams; the ones we usually think of when we think of the I Ching.
        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
    <div id="donatemsg" title="Contribute">
        If you like this site, please consider supporting it.
        <p>
            <img src="/images/bitcoin_addr.png">
        <p></p>
        <span style="font-size:9pt">1JSPBvhepQMVV9znim5eo9bE7BGkK5N2te</span>  
        </p>
    </div>

    <div id="helptipmsg" style="padding:0px;margin:0px" title="Help">
        <div id="parentparent" style="overflow:scroll">
            <div id="parent" style="position:relative;">
                <div id="image">
                    <h3>    
                        See the <a href="/book/ichingbook/_book/instructions.html">documentation</a> for more details</br>
                    </h3>
                    <img style="width:100%" src="/images/help.png">
                </div>
            </div>
        </div>        
    </div>


    <div id="help2tipmsg" style="padding:0px;margin:0px" title="Help">
        <div id="parentparent" style="overflow:scroll">
            <div id="parent" style="position:relative;">
                <div id="image">
                    <h3>    
                        See the <a href="/book/ichingbook/_book/instructions.html">documentation</a> for more details</br>
                    </h3>
                    <img style="width:100%" src="/images/help2.png">
                </div>
            </div>
        </div>        
    </div>





    <!-- body > div:nth-child(17)
    <!-- ----------------------------------------------------------------------->
    <div id="shortbuttonmsg" style="width:80% !important" title="NOW and SHORT">
        <p>
            The "NOW" casts the hexagrams not for any particular query, but for the current moment.  
            It does this by looking at the oldest measure of time known to man, the stars.  I 
            calculate the position of the Moon, Mercury, Venus, Mars, Jupiter and Saturn down 
            to 10e-14 of an arc second (which, for Saturn, is about 1 micrometer, or one-millionth 
            of a millimeter).  So these numbers are always changing.  
        </p>
        <p>
            <b>The "SHORT" button is not yet working, but it will show a very short "answer", 
                like a few sentences, rather than a few pages.</b>   
        </p>
        <p>
            Typically random numbers are used for querying the oracle, but for the 
            "Now" an "Short" I use unpredictable numbers (in that is is impossible to predict 
            the number due to the fact they are changing many thousands of time per 
            second) but numbers that are based in an external reality that is still 
            beyond our influence.  Hence, it preserves the essence of "from beyond 
            the limits of man", which is the intention behind using real random 
            numbers, and, and yet is inextricably integrated into this world of 
            linear time.  For more on the significance and relationship between the 
            six lines and the six planets, see <a href="">Astrology and the Planets</a> <p>
        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
    <div id="qtr2tipmsg" title="The Oracle">
        <p>
            This is the basic query method where you type in a question or a thought and cat the "coins".  You can read about each type casting methods by clicking the "?" to the left of the method.  The interpretations are from the Wilhelm/Baynes translation, but, as I add new, updated modern interpretations they will be appended to the traditional ones, and they will be obviously marked as such in the readout.  This new interpretation will eventually form the "ACULTURAL" text.
        </p>
        <p>
            Personally, I have found the R-DECAY to be the most effective.  Unfortunately, the Fermi Lab's radioactive decay random number generator service is often down.  In that case I then use the <a href="random.org">RANDOM.ORG</a> data.        <p>
        </p>
    </div>
    <!-- ----------------------------------------------------------------------->
    <div id="qtr3tipmsg" title="Compare Two Hexes">
        <p>
            Here you simply type in two different hexagram numbers as you will see the same results as of you cast them. The "Placeholder" text in the field (the grey number that disappears when you type in a number) are randomly chosen and are always opposite of each other.  When you type in a number in the first field, as soon as your mouse leaves that field the 2nd fields "placeholder" value is set to the binary opposite of the first field.  In this way, these fields can act as a simple tool to find opposites.  It may seem a bit distracting at first, but you can simply ignore the "placeholder" values as they disappear as soon as you type in another number.  
        </p>
        <p>
            This is mainly used as a research tool, but it can also be used in other ways.  For example, say you cast a hexagram and it moved from one hexagram to another, but you want to see what is needed to have the original hexagram result in a different final hexagram.  Here you can type in the first hexagram number you cast, and the second hexagram number you want, and see the moving lines.  In this way you are using the I Ching more as a tool to create your destiny rather than accept whatever is delivered to you.
        <p>
        </p>
    </div>    <!-- ----------------------------------------------------------------------->



</div>

