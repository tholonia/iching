var year_arg, month_arg, day_arg, hour_arg, minute_arg, sec_arg, msec_arg;
var is_default_time;
var astrodata = {};//new Array();
console.log(astrodata);

function DecodeArgs() {
    crack = window.location.search.split("?");
    //// decode arguments if any
    if (crack.length == 2) {
        is_default_time = false;
        var argv = crack[1].split("&");
        if (argv.length) {
            argc = argv.length;
            for (i = 0; i < argc; i++) {
                pair = argv[i].split("=");
                if (pair.length == 2) {
                    if (pair[0] == "year") {
                        year_arg = pair[1];
                    } else if (pair[0] == "month") {
                        month_arg = pair[1];
                        month_arg--;
                        month_arg++;
                    } else if (pair[0] == "day") {
                        day_arg = pair[1];
                        day_arg++;
                        day_arg--;
                    } else if (pair[0] == "hour")
                        hour_arg = pair[1];
                    else if (pair[0] == "minute")
                        minute_arg = pair[1];
                    else if (pair[0] == "sec")
                        sec_arg = pair[1];
                    else if (pair[0] == "msec")
                        msec_arg = pair[1];
                }
            }
        }
    } else {
        SetDefaultTime();
    }
}

function SetDefaultTime()
{
    is_default_time = true;
    var today = new Date();
    year_arg = today.getUTCFullYear();
    month_arg = today.getUTCMonth() + 1;
    day_arg = today.getUTCDate();
    hour_arg = today.getUTCHours();
    minute_arg = today.getUTCMinutes();
    sec_arg = today.getUTCSeconds();
    msec_arg = today.getUTCMilliseconds();
}

function OnLoad()
{
    //PopulateDateTimeForm();
}

function IntPart(x) {
    return Math.floor(x);
}

// degree <-> radian conversion macros
function D2R(_deg) {
    return _deg / 57.29578;
}
function R2D(_rad) {
    return _rad * 57.29578;
}

// published algorithm assumes trig is done in degrees
function SIN(_x) {
    return (Math.sin(D2R(_x)));
}
function COS(_x) {
    return (Math.cos(D2R(_x)));
}
function TAN(_x) {
    return (Math.tan(D2R(_x)));
}
function ASIN(_x) {
    return (R2D(Math.asin(_x)));
}
function ATAN2(_x, _y) {
    return (R2D(Math.atan2(_x, _y)));
}

var SEC_PER_DAY = 24 * 60 * 60;
var D360 = 360.0;
var D180 = 180.0;
var D1 = 1.0
function SQR(x) {
    return x * x;
}
function CUBE(x) {
    return x * x * x;
}
var PI = Math.PI;
// Julian date of 1/1/1980, 12 midnight (NB: Julian day starts at noon)
var JD_EPOCH = 2444238.5;

// names of signs of the zodiac
var zodiac = new Array(
        "Aries",
        "Taurus",
        "Gemini",
        "Cancer",
        "Leo",
        "Virgo",
        "Libra",
        "Scorpio",
        "Sagittarius",
        "Capricorn",
        "Aquarius",
        "Pisces"
        );


// obtain the right ascension and declination given geocentric
// 	latitude and longitude; note that w and v have to be set as follows: 
// 		w=COS(obliquity); v=SIN(obliquity);
// 	before using these macros
function GET_RA(_lon, _lat, w, v) {
    return(ATAN2((SIN((_lon)) * w) - (TAN((_lat)) * v), COS((_lon))));
}
function GET_DEC(_lon, _lat, w, v) {
    return(ASIN((SIN((_lat)) * w) + (COS((_lat)) * v * SIN((_lon)))));
}

// 0 based offset of a given planet
var MERCURY = 0
var VENUS = 1
var EARTH = 2
var MARS = 3
var JUPITER = 4
var SATURN = 5
var URANUS = 6
var NEPTUNE = 7
var PLUTO = 8
var MAXPLANET = 9
// is this an outer planet?
function OUTER(_i) {
    return((_i) > EARTH);
}

// number of zodiac signs
var MAX_ZODIAC = 12


// constructor for planetary elements, initialized or derived
function Orbit(iPlanet, name, period, elon, perilon, ecc, semiaxis,
        incline, ascnodelon) {
    this.iPlanet = iPlanet;			// planet index
    this.name = name;					// name of planet
    this.period = period;				// period in tropical years
    this.elon = elon;					// longitude at epoch in degrees
    this.perilon = perilon;			// longitude of perihelion in degrees
    this.ecc = ecc;					// eccentricity of orbit
    this.semiaxis = semiaxis;			// semimajor axis of orbit in AU
    this.incline = incline;			// inclination of orbit in degrees
    this.ascnodelon = ascnodelon;		// longitude of ascending node in degress
}

// sun and moon are subclasses of Orbit with a different position() method
function Sun(iPlanet, name, period, elon, perilon, ecc, semiaxis,
        incline, ascnodelon) {
    this.superClass = Orbit;
    this.superClass(iPlanet, name, period, elon, perilon, ecc, semiaxis,
            incline, ascnodelon);
}

Sun.prototype = new Orbit();

function Moon(iPlanet, name, period, elon, perilon, ecc, semiaxis,
        incline, ascnodelon) {
    this.superClass = Orbit;
    this.superClass(iPlanet, name, period, elon, perilon, ecc, semiaxis,
            incline, ascnodelon);
}

Moon.prototype = new Orbit();

// the following data refers to the apparent orbit of the sun
var sun = new Sun(-1, "Sun",
        0.0, 278.833540, 282.596403, 0.016718, 0.0, 0.0, 0.0);
// ditto the moon;
var moon = new Moon(-1, "Moon",
        0.0, 64.975464, 349.383063, 0.054900, 0.0, 5.145396, 151.950429);

// array of planets
planets = new Array(MAXPLANET);
planets[MERCURY] = new Orbit(MERCURY, "Mercury",
        0.24085, 231.2973, 77.1442128, 0.2056306, 0.3870986, 7.0043579, 48.0941733);
planets[VENUS] = new Orbit(VENUS, "Venus",
        0.61521, 355.73352, 131.2895792, 0.0067826, 0.7233316, 3.394435, 76.4997524);
planets[EARTH] = new Orbit(EARTH, "Earth",
        1.00004, 98.833540, 102.596403, 0.016718, 1.0, 0.0, 0.0);
planets[MARS] = new Orbit(MARS, "Mars",
        1.88089, 126.30783, 335.6908166, 0.0933865, 1.5236883, 1.8498011, 49.4032001);
planets[JUPITER] = new Orbit(JUPITER, "Jupiter",
        11.86224, 146.966365, 14.0095493, 0.0484658, 5.202561, 1.3041819, 100.2520175);
planets[SATURN] = new Orbit(SATURN, "Saturn",
        29.45771, 165.322242, 92.6653974, 0.0556155, 9.554747, 2.4893741, 113.4888341);
planets[URANUS] = new Orbit(URANUS, "Uranus",
        84.01247, 228.0708551, 172.7363288, 0.0463232, 19.21814, 0.7729895, 73.8768642);
planets[NEPTUNE] = new Orbit(NEPTUNE, "Neptune",
        164.79558, 260.3578998, 47.8672148, 0.0090021, 30.10957, 1.7716017, 131.5606494);
planets[PLUTO] = new Orbit(PLUTO, "Pluto",
        250.9, 209.439, 222.972, 0.25387, 39.78459, 17.137, 109.941);

// the compute method for the Orbit object
function planet_position(edays, obliquity) {
    // the following fields are derived from the above data for a given date
    // heliolon;		-- heliocentric longitude
    // heliolat;		-- heliocentric latitude
    // rad;				-- length of radius vector
    // projheliolon;	-- projected heliocentric longitude
    // projrad;			-- projected radius vector
    // geolon;			-- geocentric longitude
    // geolat;			-- geocentric latitude
    // RA;				-- right ascension expressed as a double
    // declination;		-- declination expressed as a double

    var w, v;	// scratch doubles

    w = scale360((D360 / 365.2422) * (edays / this.period));

    // calculate mean anomoly
    this.meananom = w + this.elon - this.perilon;

    // calculate heliocentric longitude
    this.heliolon = scale360(w +
            ((D360 / PI) * this.ecc * SIN(this.meananom)) +
            this.elon);

    // calculate length of radius vector
    w = COS(this.heliolon - this.perilon);
    if (this.iPlanet == EARTH)
        this.rad = ((D1 - SQR(this.ecc))) / (D1 + (this.ecc * w));
    else
        this.rad = (this.semiaxis * (D1 - SQR(this.ecc))) /
                (D1 + (this.ecc * w));

    // calculate heliocentric latitude
    if (this.iPlanet == EARTH)
        this.heliolat = 0.0;
    else {
        // the further calculations are applicable only to the planets per se

        w = this.heliolon - this.ascnodelon;

        // calculate heliocentric latitude
        this.heliolat = ASIN(SIN(w) * SIN(this.incline));

        // calculate projected heliocentric longitude
        this.projheliolon = ATAN2(
                SIN(w) * COS(this.incline),
                COS(w)) + this.ascnodelon;

        // calculate projected radius vector
        this.projrad = this.rad * COS(this.heliolat);

        // calculate geocentric longitude
        // this is done differently for inner and outer planets
        if (OUTER(this.iPlanet)) {
            w = this.projheliolon - planets[EARTH].heliolon;
            this.geolon = ATAN2((planets[EARTH].rad * SIN(w)),
                    (this.projrad - (planets[EARTH].rad * COS(w)))) +
                    this.projheliolon;
        } else {
            w = planets[EARTH].heliolon - this.projheliolon;
            this.geolon = D180 + planets[EARTH].heliolon +
                    ATAN2((this.projrad * SIN(w)),
                            (planets[EARTH].rad - (this.projrad * COS(w))));
            this.geolon = scale360(this.geolon);
        }

        // calculate geocentric latitude
        this.geolat = ATAN2(
                (this.rad * TAN(this.heliolat) *
                        SIN(this.geolon - this.projheliolon)),
                (planets[EARTH].rad *
                        SIN(this.projheliolon - planets[EARTH].heliolon)));

        // lastly obtain the right ascension and declination
        w = COS(obliquity);
        v = SIN(obliquity);
        this.RA = GET_RA(this.geolon, this.geolat, w, v);
        this.declination = GET_DEC(this.geolon, this.geolat, w, v);
    }
}

// assign default position method to Orbit object
Orbit.prototype.position = planet_position;

// calculate the position of the sun at edays past epoch 1/1/1980
// obliquity is precalculated as for the other position calculations.
// the results are left in the global sun record
function sun_position(edays, obliquity)
{
    var N, Ec, w, v;

    N = scale360((D360 / 365.2422) * edays);
    this.meananom = scale360(N + this.elon - this.perilon);
    Ec = (D360 / PI) * this.ecc * SIN(this.meananom);
    this.geolon = scale360(N + Ec + this.elon);
    this.geolat = 0.0;				// sun's latitude always 0
    w = COS(obliquity);
    v = SIN(obliquity);
    this.RA = GET_RA(this.geolon, this.geolat, w, v);
    this.declination = GET_DEC(this.geolon, this.geolat, w, v);
}

// assign the sun position method
Sun.prototype.position = sun_position;

// calculate the position of the moon at edays past epoch 1/1/1980.
// obliquity is precalculated as for the other position calculations.
// the results are left in the global moon record.
// Somewhat more complicated due to various corrections.
function moon_position(edays, obliquity)
{
    var N, // ascending node mean longitude
            Ev, // evection
            Ae, // annual equation
            A3, // third level correction
            C, // moon's heliocentric longitude - solar geocentri longitude
            Ec, // equation of the center
            A4, // fourth level correction
            v, w;	// scratch variables

    // calculate heliocentric longitude
    this.heliolon = scale360((13.1763966 * edays) + this.elon);

    // calculate mean anomoly
    this.meananom =
            scale360((this.heliolon - (0.1114041 * edays)) - this.perilon);

    N = scale360(this.ascnodelon - (0.0529539 * edays));

    C = this.heliolon - sun.geolon;
    // calculate evection
    Ev = 1.2739 * SIN((2.0 * C) - this.meananom);
    // calculate annual equation
    Ae = 0.1858 * SIN(sun.meananom);
    // calculate third level correction
    A3 = 0.37 * SIN(sun.meananom);

    // calculate corrected anomoly
    this.meananom = this.meananom + Ev - Ae - A3;
    Ec = 6.2886 * SIN(this.meananom);
    A4 = 0.214 * SIN(2.0 * this.meananom);
    // calculate corrected longitude
    this.geolon = this.heliolon + Ev + Ec - Ae + A4;
    this.geolon += 0.6583 * SIN(2.0 * (this.geolon - sun.geolon));

    N = N - 0.16 * SIN(sun.meananom);
    w = this.geolon - N;
    this.geolon = ATAN2(SIN(w) * COS(this.incline), COS(w)) + N;
    this.geolat = ASIN(SIN(w) * SIN(this.incline));

    // calculate the right ascension and declination
    w = COS(obliquity);
    v = SIN(obliquity);
    this.RA = GET_RA(this.geolon, this.geolat, w, v);
    this.declination = GET_DEC(this.geolon, this.geolat, w, v);
}

// assign the moon position method
Moon.prototype.position = moon_position;

// return Julian date for specified month/day/year
function caldate_to_juliandate(month, day, year)
{
    var A, B, C, D, x;	// used to compute Julian date
    var post_gregorian = true;

    // if date is later than 15 October 1582 gregorian kicks in
    if (year < 1582)
        post_gregorian = false;
    else if (year == 1582) {
        if (month < 10)
            post_gregorian = false;
        else if (month == 10) {
            if (day <= 15.0)
                post_gregorian = false;
        }
    }
    // if the month is january or february, decrement year
    // and add 12 months to the month.
    if (month == 1 || month == 2) {
        year--;
        month += 12;
    }
    if (post_gregorian) {
        // A <- integer part of year/100
        A = IntPart(year / 100.0);
        // B <- 2-A+integer part of A/4
        B = 2 - A + IntPart(A / 4.0);
    } else
        B = 0.0;
    // C <- integer part of (365.25 * year)
    C = IntPart(365.25 * year);
    // D <- integer part of (30.6001 * (month+1))
    D = IntPart(30.6001 * (month + 1));
    // now we can get the Julian date.
    return(B + C + D + day + 1720994.5);
}

// return the obliquity for the specified Julian date JD
// Algorithm from page 46 of Duffet-Smith
function get_obliquity(JD)
{
    var T;		// number of centuries since JD 1/1/1900

    T = (JD - 2415020.0) / 36525.0;
    return(23.452294 -
            (((46.845 * T) + (0.0059 * SQR(T)) - (0.00181 * CUBE(T))) / 3600.0));
}

// transform coordinate (in degrees) D to range 0..360
function scale360(D)
{
    D = D % 360;
    if (D < 0.0)
        D += D360;
    return(D);
}

// print angle ra in degrees in right ascension (hms) format along with the
// name of the corresponding zodiac sign
function printRA(ra)
{
    var abs_ra = ra < 0 ? ra + 360.0 : ra;
    var H = abs_ra / 15.0;		// h=(ra/360)*24
    var h = IntPart(H);
    var M = (H - h) * 60.0;
    var m = IntPart(M);
    var S = (H - (h + (m / 60))) * 3600.0;

    RA = {};//new Array();

    RA['zodiac'] = zodiac[IntPart(h / 2)];
    RA['H'] = H;
    RA['M'] = M;
    RA['S'] = S;

    RA['h'] = h;
    RA['m'] = m;


    //document.write(zodiac[IntPart(h / 2)]);
    //document.write("</span></TD>");
    //document.write("<TD><span class=\"p-small2\">");
    //document.write(" " + h + "h ");
    //document.write(" " + m + "m ");
    //document.write(" " + IntPart(S) + "s ");
    return(RA);
}

// print declination dec in degrees/minutes/seconds format
function printDec(dec)
{
    var abs_dec = dec < 0.0 ? -dec : dec;
    var deg = IntPart(abs_dec);
    var min = IntPart((abs_dec - deg) * 60.0);
    var sec = (abs_dec - (deg + (min / 60.0))) * 3600.0;

    var dec = {};//new Array();
    dec['dec'] = dec < 0.0 ? '-' : '+';
    dec['deg'] = min;
    dec['min'] = min;
    dec['sec'] = IntPart(sec);

    //document.write(dec < 0.0 ? '-' : '+');
    //document.write(deg + "d ");
    //document.write(min + "m ");
    //document.write(IntPart(sec) + "s");

    return(dec);
}

var month_names = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

function OutputLocalTime() {
    var mon, day, now, hour, min, ampm, time, str, tz, end, beg;
    day = new Array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
    d = new Date();
    sss = Math.round(d.getTime() / 1000);
    now = new Date(sss * 1000);
    hour = now.getHours();
    min = now.getMinutes();
    sec = now.getSeconds();
    ampm = (hour >= 12) ? "pm" : "am";
    hour = (hour == 0) ? 12 : (hour > 12) ? hour - 12 : hour;
    min = (min < 10) ? "0" + min : min;
    tz = "";
    time = hour + ":" + min + ":" + sec + ampm + tz +
            ", " + day[now.getDay()] +
            " " + month_names[now.getMonth()] +
            " " + now.getDate() +
            ", " + now.getFullYear();
    astrodata['localtime'] = time;
    //document.write("<span class=\"p-small2\">Local time is " + time + "</span>");
}


function display_positions(year, month, day, hour, minute, sec, msec)
{
    // add fractional days
    day += (hour / 24.0) +
            (minute / 1440.0) +
            (sec / 86400.0) +
            (msec / 86400000.0);
    JD = caldate_to_juliandate(month, day, year)
    // days since epoch can now be computed by subtracting
    // Julian date of epoch from JD
    var edays = JD - JD_EPOCH;
    obliquity = get_obliquity(JD);

    // do the calculations for earth
    astrodata['planet'] = planets[EARTH];

    planets[EARTH].position(edays, obliquity);


    //document.write("<BR>");

    //document.write("<TABLE BORDER=2>");
    //document.write("<TR>");
    //document.write("<TH><span class=\"p-small2\">");
    //document.write("Planet");
    //document.write("</span></TH>");
    //document.write("<TH><span class=\"p-small2\">");
    //document.write("Sign");
    //document.write("</span></TH>");
    //document.write("<TH><span class=\"p-small2\">");
    //document.write("Right Ascension");
    //document.write("</span></TH>");
    //document.write("<TH><span class=\"p-small2\">");
    //document.write("Declination");
    //document.write("</span></TH>");
    //document.write("</TR>");
    //document.write("<TR>");
    // and the sun
    sun.position(edays, obliquity);
    //document.write("<TD><span class=\"p-small2\">");
    //document.write("Sun");
    //document.write("</span></TD>");
    //document.write("<TD><span class=\"p-small2\">");
    printRA(sun.RA);
//astrodata['Sun']['RA']= printRA(sun.RA);
    //document.write("</span></TD>");
    //document.write("<TD><span class=\"p-small2\">");
    astrodata['Sun'] = printDec(sun.declination);
    //document.write("</span></TD>");
    //document.write("</TR>");

    //document.write("<TR>");
    //document.write("<TD><span class=\"p-small2\">");
    // and the moon
    moon.position(edays, obliquity);
    //document.write("Moon");
    //document.write("</span></TD>");
    //document.write("<TD><span class=\"p-small2\">");
    astrodata['Moon'] = {};//new Array();
    astrodata['Moon']['RA'] = printRA(moon.RA);
    //printRA(moon.RA);
    //document.write("</span></TD>");
    //document.write("<TD><span class=\"p-small2\">");
    astrodata['Moon']['dec'] = printDec(moon.declination);
    //printDec(moon.declination);
    //document.write("</span></TD>");
    //document.write("</TR>");

    var i;
    // then for the planets
    for (i = 0; i < MAXPLANET; i++) {
        if (i != EARTH) {
            //document.write("<TR>");
            //document.write("<TD><span class=\"p-small2\">");
            planets[i].position(edays, obliquity);
            //document.write(planets[i].name);
            //document.write("</span></TD>");
            //document.write("<TD><span class=\"p-small2\">");
            astrodata[planets[i].name] = {};//new Array();
            astrodata[planets[i].name]['RA'] = printRA(planets[i].RA);
//            printRA(planets[i].RA);
            //document.write("</span></TD>");
            //document.write("<TD><span class=\"p-small2\">");
            astrodata[planets[i].name]['dec'] = printDec(planets[i].declination);
//            printDec(planets[i].declination);
            //document.write("</span></TD>");
            //document.write("</TR>");
        }
    }
    //document.write("</TABLE>");
}



// definitions of events
var NO_EVENT = 0;
var OPPOSITION = 1;
var CONJUNCTION = 2;
var SQUARE = 3;
var TRINE = 4;
var SEXTILE = 5;

// definitions of offsets in event array
var E_SUN = 0;
var E_MERCURY = 1;
var E_VENUS = 2;
var E_MOON = 3;
var E_MARS = 4;
var E_JUPITER = 5;
var E_SATURN = 6;
var E_URANUS = 7;
var E_NEPTUNE = 8;
var E_PLUTO = 9;
var MAX_EVENT = 10;

// i is an event index; return the integer part of the right ascension
// of the given body
function getRA(i) {
    var RA, abs_RA;

    switch (i) {
        case E_SUN:
            RA = sun.RA;
            break;
        case E_MERCURY:
            RA = planets[MERCURY].RA;
            break;
        case E_VENUS:
            RA = planets[VENUS].RA;
            break;
        case E_MOON:
            RA = moon.RA;
            break;
        case E_MARS:
            RA = planets[MARS].RA;
            break;
        case E_JUPITER:
            RA = planets[JUPITER].RA;
            break;
        case E_SATURN:
            RA = planets[SATURN].RA;
            break;
        case E_URANUS:
            RA = planets[URANUS].RA;
            break;
        case E_NEPTUNE:
            RA = planets[NEPTUNE].RA;
            break;
        case E_PLUTO:
            RA = planets[PLUTO].RA;
            break;
    }
    abs_RA = RA < 0 ? RA + 360.0 : RA;	// map into 0..360
    return(abs_RA);
}

function analyze_events(orb)
{
//console.log(orb);
    var e_names = new Array(
            "Sun", "Mercury", "Venus", "Moon", "Mars",
            "Jupiter", "Saturn", "Uranus", "Neptune", "Pluto"
            );
    // binary interactions between each possible pair of planet/sun/moon
    var events = new Array(MAX_EVENT);
    var iEvent;
    for (iEvent = 0; iEvent < MAX_EVENT; iEvent++)
        events[iEvent] = new Array(MAX_EVENT);

    var i, j, hi, hj;
    // clear out event matrix in entirety
    for (i = 0; i < MAX_EVENT; i++)
        for (j = 0; j < MAX_EVENT; j++)
            events[i][j] = NO_EVENT;
    // note that we only use half of the matrix and ignore the diagonal
    for (i = 0; i < MAX_EVENT; i++) {
        for (j = 0; j < i; j++) {
            var diff = getRA(i) - getRA(j);

            if (diff < 0.0)
                diff = -diff;

            if (diff >= -orb && diff <= orb)
                events[i][j] = CONJUNCTION;
            else if (diff >= (180.0 - orb) && diff <= (180.0 + orb))
                events[i][j] = OPPOSITION;
            else if (diff >= (120.0 - orb) && diff <= (120.0 + orb))
                events[i][j] = TRINE;
            else if (diff >= (90.0 - orb) && diff <= (90.0 + orb))
                events[i][j] = SQUARE;
            else if (diff >= (60.0 - orb) && diff <= (60.0 + orb))
                events[i][j] = SEXTILE;
        }
    }
    for (i = 0; i < MAX_EVENT; i++) {
        console.log(e_names[i]);
        console.log(astrodata[e_names[i]]);
        for (j = 0; j < i; j++) {
            if (events[i][j]) {		// if anything happening between these two
                // in this case we want to use the hour as an index into
                // the zodiac names array so we divide by two
                hi = IntPart(getRA(i) / 15.0) >> 1;
                hj = IntPart(getRA(j) / 15.0) >> 1;

                switch (events[i][j]) {
                    case OPPOSITION:
                        var aspects = {};
                        aspects['this_planet'] = e_names[i];
                        aspects['this_sign'] = zodiac[hi];
                        aspects['that_planet'] = e_names[j];
                        aspects['that_sign'] = zodiac[hj];
                        aspects['relation'] = 'opposition';

                        astrodata[e_names[i]]['aspects'] = {};
                        astrodata[e_names[i]]['aspects'] = aspects;

                        /*
                         document.write(
                         e_names[i] + " in " +
                         zodiac[hi] + " is in opposition to " +
                         e_names[j] + " in " +
                         zodiac[hj] + "<BR>");
                         */
                        break;
                    case CONJUNCTION:
                        var aspects = {};
                        aspects['this_planet'] = e_names[i];
                        aspects['this_sign'] = zodiac[hi];
                        aspects['that_planet'] = e_names[j];
                        aspects['that_sign'] = zodiac[hj];
                        aspects['relation'] = 'conjunction';

                        astrodata[e_names[i]]['aspects'] = aspects;
                        /*                        
                         document.write(
                         e_names[i] + " in " +
                         zodiac[hi] + " is in conjunction with " +
                         e_names[j] + " in " +
                         zodiac[hj] + "<BR>");
                         */
                        break;
                    case SQUARE:
                        var aspects = {};
                        aspects['this_planet'] = e_names[i];
                        aspects['this_sign'] = zodiac[hi];
                        aspects['that_planet'] = e_names[j];
                        aspects['that_sign'] = zodiac[hj];
                        aspects['relation'] = 'square';

                        astrodata[e_names[i]]['aspects'] = {};
                        astrodata[e_names[i]]['aspects'] = aspects;
                        /*
                         document.write(
                         e_names[i] + " in " +
                         zodiac[hi] + " is square with " +
                         e_names[j] + " in " +
                         zodiac[hj] + "<BR>");
                         */
                        break;
                    case TRINE:
                        var aspects = {};
                        aspects['this_planet'] = e_names[i];
                        aspects['this_sign'] = zodiac[hi];
                        aspects['that_planet'] = e_names[j];
                        aspects['that_sign'] = zodiac[hj];
                        aspects['relation'] = 'trine';

                        astrodata[e_names[i]]['aspects'] = {};
                        astrodata[e_names[i]]['aspects'] = aspects;
                        /*
                         
                         document.write(
                         e_names[i] + " in " +
                         zodiac[hi] + " is trine with " +
                         e_names[j] + " in " +
                         zodiac[hj] + "<BR>");
                         */
                        break;
                    case SEXTILE:
                        var aspects = {};
                        aspects['this_planet'] = e_names[i];
                        aspects['this_sign'] = zodiac[hi];
                        aspects['that_planet'] = e_names[j];
                        aspects['that_sign'] = zodiac[hj];
                        aspects['relation'] = 'sextile';

                        astrodata[e_names[i]]['aspects'] = {};
                        astrodata[e_names[i]]['aspects'] = aspects;
                        /*                       
                         document.write(
                         e_names[i] + " in " +
                         zodiac[hi] + " is sextile with " +
                         e_names[j] + " in " +
                         zodiac[hj] + "<BR>");
                         */
                        break;
                }
            }
        }
    }
}


/// for the new date form


function IsNumericValue(val, bNegOk) {
    var i;
    if (bNegOk) {
        if (val == "")
            return false;
    } else {
        if (val == "" || val <= 0) {
            return false;
        }
    }
    i = 0;
    if (bNegOk) {
        if (val.charAt(0) == "-") {
            i = 1;
        }
    }
    for (; i < val.length; i++) {
        if (val.charAt(i) < "0" || val.charAt(i) > "9") {
            return false;
        }
    }
    return true;
}

function PopulateDateTimeForm()
{
    DateTimeForm.year.value = year_arg;
    DateTimeForm.month.value = month_arg;
    DateTimeForm.day.value = day_arg;
    DateTimeForm.hour.value = hour_arg;
    DateTimeForm.minute.value = minute_arg;
    DateTimeForm.sec.value = sec_arg;
    DateTimeForm.msec.value = msec_arg;
}


function validateForm(theForm) {
    if (!IsNumericValue(theForm.year.value, true)) {
        alert("Please enter a number for the year");
        return false;
    }
    if (!IsNumericValue(theForm.month.value, false)) {
        alert("Please enter a number for the month");
        return false;
    }
    if (theForm.month.value < 1 || theForm.month.value > 12) {
        alert("Please enter a month between 1 and 12");
        return false;
    }
    if (!IsNumericValue(theForm.day.value, false)) {
        alert("Please enter a number for the day");
        return false;
    }
    if (theForm.day.value < 1 || theForm.day.value > 31) {
        alert("Please enter a day between 1 and 31");
        return false;
    }
    if (!IsNumericValue(theForm.hour.value, false)) {
        theForm.hour.value = 0;
        return true;
    }
    if (theForm.hour.value < 0 || theForm.hour.value > 23) {
        alert("Please enter an hour between 0 and 23");
        return false;
    }
    if (!IsNumericValue(theForm.minute.value, false)) {
        theForm.minute.value = "0";
        return true;
    }
    if (theForm.minute.value < 0 || theForm.minute.value > 59) {
        alert("Please enter a minute between 0 and 59");
        return false;
    }
    if (!IsNumericValue(theForm.sec.value, false)) {
        theForm.sec.value = "0";
        return true;
    }
    if (theForm.sec.value < 0 || theForm.sec.value > 59) {
        alert("Please enter a second between 0 and 59");
        return false;
    }
    if (!IsNumericValue(theForm.msec.value, false)) {
        theForm.msec.value = "0";
        return true;
    }
    if (theForm.msec.value < 0 || theForm.msec.value > 999) {
        alert("Please enter a millisecond between 0 and 999");
        return false;
    }
    return true;
}

//

function OutputTable() {
    if (is_default_time) {
        OutputLocalTime();
    } else {
        time_str = hour_arg + ":" +
                minute_arg + ":" +
                sec_arg + "." +
                msec_arg +
                ", " + day_arg +
                " " + month_names[month_arg - 1] +
                ", " + year_arg;
        astrodata['localtime_2'] = time_str;
        //document.write("<span class=\"p-small2\">" + time_str + "</span>");
    }
    display_positions(year_arg,
            month_arg,
            day_arg,
            hour_arg,
            minute_arg,
            sec_arg,
            msec_arg);
}
