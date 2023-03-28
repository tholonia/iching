<?php

class CssHex {

    public $code;
    public $script;

    public function __construct() {
        
    }
    public function drawHex($bin, $delta, $script, $boxnum,$uid) {
//$boxnum = 7-$boxnum;
//var_dump($bin);
//var_dump($delta);
        
        $h = "<div class='box" . $boxnum . "' id='box" . $boxnum . "'>\n";
        for ($k = 0; $k < 6; $k++) {
            $l = $k + 1;
//            $l = 6-$k ;
            if ($bin[$k] == 1) { //yang
                if ($delta[$k] == 1) { //moving yang
                    list($code, $js) = $this->drawXYang($boxnum, $l,$uid);
                    $h .= $code;
                    $script .= $js;
                    $bin[$k] = 0; //set new line to yin
                    $delta[$k] = 0;
                } else {
                    list($code, $js) = $this->drawYang($boxnum, $l,$uid);
                    $h .= $code;
                    $script .= $js;

                    $bin[$k] = 1; //keep yang line
                    $delta[$k] = 0;
                }
            } else {
                if ($bin[$k] == 0) { // yin
                    if ($delta[$k] == 1) { //moving yin
                        list($code, $js) = $this->drawXYin($boxnum, $l,$uid);
                        $h .= $code;
                        $script .= $js;
                        $bin[$k] = 1; //set new line to yang
                        $delta[$k] = 0;
                    } else {
                        list($code, $js) = $this->drawYin($boxnum, $l,$uid);
                        $h .= $code;
                        $script .= $js;
                        $bin[$k] = 0; //keep new line to yin
                        $delta[$k] = 0;
                    }
                }
            }
        }
        $h .= "</div>\n";
        //var_dump($h);
        return(array($h, $script, $bin));
    }

    
   
    
    
    private function drawXYang($boxnum, $i,$uid) {
        $str = "    <div id='line${boxnum}-${i}' class='line${i}'>\n";
        $str .= "        <div id='part${boxnum}-${i}-1' class='part1'></div>\n";
        $str .= "        <div id='part${boxnum}-${i}-2' class='part2'></div>\n";
        $str .= "        <div id='part${boxnum}-${i}-3' class='part3'></div>\n";
        $str .= "    </div>\n";
        $str2  = "$('#${uid} #part${boxnum}-${i}-1').css('background-color','red');\n";
        $str2 .= "$('#${uid} #part${boxnum}-${i}-2').css('background-color','red');\n";
        $str2 .= "$('#${uid} #part${boxnum}-${i}-3').css('background-color','red');\n";
        return(array($str, $str2));
    }

    private function drawYang($boxnum, $i,$uid) {
        $str = "    <div id='line${boxnum}-${i}' class='line${i}'>\n";
        $str .= "        <div id='part${boxnum}-${i}-1' class='part1'></div>\n";
        $str .= "        <div id='part${boxnum}-${i}-2' class='part2'></div>\n";
        $str .= "        <div id='part${boxnum}-${i}-3' class='part3'></div>\n";
        $str .= "    </div>\n";
        $str2  = "$('#${uid} #part${boxnum}-${i}-1').css('background-color','black');\n";
        $str2 .= "$('#${uid} #part${boxnum}-${i}-2').css('background-color','black');\n";
        $str2 .= "$('#${uid} #part${boxnum}-${i}-3').css('background-color','black');\n";
        return(array($str, $str2));
    }

    private function drawYin($boxnum, $i,$uid) {
        $str = "    <div id='line${boxnum}-${i}' class='line${i}'>\n";
        $str .= "        <div id='part${boxnum}-${i}-1' class='part1'></div>\n";
        $str .= "        <div id='part${boxnum}-${i}-2' class='part2'></div>\n";
        $str .= "        <div id='part${boxnum}-${i}-3' class='part3'></div>\n";
        $str .= "    </div>\n";
        $str2  = "$('#${uid} #part${boxnum}-${i}-1').css('background-color','black');\n";
        $str2 .= "$('#${uid} #part${boxnum}-${i}-2').css('background-color','white');\n";
        $str2 .= "$('#${uid} #part${boxnum}-${i}-3').css('background-color','black');\n";
        return(array($str, $str2));
    }

    private function drawXYin($boxnum, $i,$uid) {
        $str = "    <div id='line${boxnum}-${i}' class='line${i}'>\n";
        $str .= "        <div id='part${boxnum}-${i}-1' class='part1'></div>\n";
        $str .= "        <div id='part${boxnum}-${i}-2' class='part2'></div>\n";
        $str .= "        <div id='part${boxnum}-${i}-3' class='part3'></div>\n";
        $str .= "    </div>\n";
        $str2  = "$('#${uid} #part${boxnum}-${i}-1').css('background-color','red');\n";
        $str2 .= "$('#${uid} #part${boxnum}-${i}-2').css('background-color','white');\n";
        $str2 .= "$('#${uid} #part${boxnum}-${i}-3').css('background-color','red');\n";
        return(array($str, $str2));
    }

}
