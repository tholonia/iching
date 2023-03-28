<?php

require "../lib/functions.php";



//$str = '{"request":{"carrier":{},"codeType":{},"flightNumber":{},"departing":true,"date":{"year":"2013","month":"10","day":"4","interpreted":"2013-10-04","error":"The date specified is not within the expected range. Earliest allowed date 2017-09-16"},"url":"https://api.flightstats.com/flex/schedules/rest/v1/jsonp/flight/AA/100/departing/2013/10/4"},"scheduledFlights":[],"appendix":{},"error":{"httpStatusCode":400,"errorCode":"DATE_OUT_OF_RANGE","errorId":"a20d7562-a890-4917-8683-f6413e142559","errorMessage":"The date specified is not within the expected range. Earliest allowed date 2017-09-16"}}';
//$j = json_decode($str);
//var_dump($j);


$func = $_REQUEST['func'];
$pseq = $_REQUEST['pseq'];
//$_REQUEST['func'] = "getHexnumOppositeByPseq";
//$_REQUEST['pseq'] = 11;
//$func = $_REQUEST['func'];
//$pseq = $_REQUEST['pseq'];
$r = array();

if ($func == "getHexnumOppositeByPseq") {
/*
 * there is missing args in  the params
 */
    if (!$_REQUEST['pseq']) {
        $r['func'] = $func;
        $r['ret'] = 'error';
        $r['error'] = "arg 'pseq' missing";
        sendReturn($r);
    }
    /*
     * OK, send result
     */
    $r['req'] = $_SERVER['QUERY_STRING'];
    $r['func'] = $func;
    $r['ret'] = $func = $GLOBALS['dbh']->getHexnumOppositeByPseq($_REQUEST['pseq']);
    $r['error'] = null;
//    var_dump($r);
    sendReturn($r);
} else {
/*
 * The function name is eitehr missing ot misspelled
 */
    $r['func'] = $func;
    $r['ret'] = 'error';
    $r['error'] = "'${func}' does not exist";
    sendReturn($r);
}

function sendReturn($r) {
    $rJson = json_encode($r);
    print "callback($rJson)";
//    var_dump(json_decode($rJson));
//    print $rJson;
}
