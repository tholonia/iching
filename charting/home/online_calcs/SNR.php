<?php
$useFile=$argv[1];
$useCmds=$argv[2];

if (!$useCmds) {
    $useCmds = "SNR.txt";
}

print "./SNR.php $useFile $useCmds\n";

$c1 = 'find -name "*.php" -exec grep "';
$c2 = <<<EOX
"  /dev/null {} \;|awk -F":" '{print $1}'|sort -u | awk '{print "sed -i   \"s@`cat F`@`cat R`@\" "$1 }'|sh 
EOX;
$c3 = <<<EOX
"  /dev/null {} \;|awk -F":" '{print $1}'|sort -u > L 
EOX;
$c4 = <<<EOX
"cat L | awk '{print "sed -i   \"s@`cat F`@`cat R`@\" "$1 }'|sh -x
EOX;
$c5 = <<<EOX
sed -i   "s@`cat F`@`cat R`@" 
EOX;


$lines = file($useCmds);
$sec = intval(count($lines)/4);


for ($e = 0;$e<$sec;$e++) {
    $c = $e*4;

    $search = $lines[$c++];

    if ($search[0] == "#") {
        exit;
    }
    file_put_contents("F", $search);

    $replace = $lines[$c++];
    file_put_contents("R", $replace);

    $filter = $lines[$c++];

    $nl = $lines[$c++];

    if (!$useFile) { 
        $useFile = "L";
        unlink("L");
    }    
    $list = $c1 . rtrim($filter) . $c3;
    system($list);

    $fs = file($useFile);
    $cmd = rtrim($c5);
    foreach ($fs as $f) {
//        print `cat F`;        
        print "[$cmd  ".rtrim($f)."]\n";
        `$cmd $f`;
    }

}
    