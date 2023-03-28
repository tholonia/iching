#<span style="color:gray"> Instructions for throwing I Ching</span>

I deploy three different methods of generating a hexagram:

* #####Modern Plum
 * Originally used casting hexagrams for the current moment in time.
* #####Planetary positions
 * Also used for real time hexagrams, and, in my opinion, the preferred method.
* #####Pseudo randomness
 * Simulation of traditinal human systems of casting the coins
* #####Pure Randomness
 * Pure randomness, beyond anything a human can achieve, and, by far the preferable method of personal consultation.

* #####Manually entered values
 * There is also a space where you can manually enter your own number if you have your preferred method.
 
##Throwing the Coins
* ### The Modern Plum Method

The Modern Plum technique is based on the ancient Mei Hua ("Plum Blossom") method of the Sung Dynasty (920-1279ad). It uses the current time as the "seed" for the casting. This modern version also uses the current time of a number of milliseconds since Jan. 1, 1970. An algorithm takes that number and transforms it to simulate three coins.

We start with the current 'microtime', which looks something like 158243487.8314

Each position represents a time period, for example:


```
1000000000 = 32 years
0100000000 = 3 years
0010000000 = 4 months
0001000000 = 11 days
0000100000 = 1 day
0000010000 = 3 hours
0000001000 = 16 minutes
0000000100 = 2 minutes
0000000010 = 10 seconds
0000000001 = 1 second
0000000000.1000 = 1/10 second
0000000000.0100 = 1/100 second
0000000000.0010 = 1/1000 second
0000000000.0001 = 1/10000 second
```



We drop the largest place because 32 years is beyond the scope of a divination. We also drop the last place because sometimes it is not returned.

Next, we take the largest and smallest place number and add them together, then the second largest and second smallest, etc.

So, if we started with a number of


```
1567203956.7129
```


we end up with


```
567203956712
```


so...



```
5+2=7
6+1=7
7+7=14
2+6=8
0+5=5
3+9=12
```



That is again reduced...



```
7+12=19
7+5=12
14+8=22
```



From these numbers, I take the modulo of 4 (because there are 4 possible states of a line), in the same manner as the yarrow stick method, but with 4 instead of 8, and like the yarrow method, add 4 if 0. This modulo is reduced to 1 and 0, based on odd or even, and 2 is added to simulate a coin toss _(this last part is unnecessary, but it makes debugging easier. Also, you may be wondering, why don't I simply take the modulo of 2 and just record whether is it odd or even. That would have the same results, but I plan to use the 4-state toss in the future.)_



```
mod(19) = 3 = 1 + 2 = 3
mod(12) = 4 = 0 + 2 = 2
mod(22) = 2 = 0 + 2 = 2
```



Now we have our "toss" of 3,2,2, which is 7, a non-moving yang line.

This is done six times, with a random number of milliseconds (10,000 - 100,000) pausing between each "toss."

[](#planetary)
### The Planetary Real-time positions

This takes the six classical astrological planets (The Sun and Moon are considered two sides of the same planetary energy), and use one for each line

Line 6 Saturn
Line 5 Jupiter
Line 4 Mars
Line 3 Venus
Line 2 Mercury
Line 1 Moon

>There is a natural relationship between these six planets and the six positions of the hexagrams, which makes using the positions of the planets all the more relevant.  It also opens the door to relating hexagrams with astrology and therefore many mythologies, beliefs systems, archetypes, etc. More on this later.

I calculate their positions to the high degree, the 'Seconds' part of the planets Right Ascension might look like 28.494882291617785. So even the very, very slow-moving planets are always changing. These are calculated the instant you press "submit."

I then sum up all of the numbers until we are down to one digit, and then take the remainder of the division by 4.

In my opinion, This is the preferred method over Modern Plum for getting a hexagram for "Now".

This planetary data is available in raw form from the API-ish URL of http://babelbrowser.com/astro/as.html

You get data something like...



```
"Neptune": {
"RA": {
"zodiac": "Pisces",
"H": 22.830755642695365,
"M": 49.84533856172192,
"S": 50.72031370331587,
"h": 22,
"m": 49
},
"dec": {
"dec": "+",
"deg": 26,
"min": 26,
"sec": 9
},
"aspects": {
"this_planet": "Neptune",
"this_sign": "Pisces",
"that_planet": "Moon",
"that_sign": "Scorpio",
"relation": "trine"
}
},
```





* ### Radioactive Decay

Historically, this has been the trickiest to deploy because getting pure random numbers is not as simple as it might sound. Initially, I had my Geiger counter collect background radiation, but it was far too slow.. only a few clicks per minute.  I solved this by getting my hands on some Strontium-90, but over the years, managing such material is, to say the least, problematic.  Fortunately, the kind folks at Fourmilab in Switzerland have granted me a license to use their random number generator, which is what I am using here.

It's pretty basic... I request three random numbers six times, reduce them down to 3's and 2's, and continue as if it was a coin toss.

Why is true randomness so important?  The basic concept behind that is I must use something that is beyond the reach of man, reason, or understanding, and then is pure chaos, that state from which all that exists has emerged from, and where it will all return.  Chaos is the beginning and the end of existence, of duality, and therefore, of reality as we know it. 

* ### Random.org coin toss

This is very straightforward.  I simply make a request to the random.org website for a three coin toss.  Random.org is so meticulous about the quality and integrity of their randomness that they actually have different results based on the type of coin you use. For now, we are using three simulated Bronze Sestertius coins from the Roman Empire of Antoninus Pius.

![](/assets/obverse.png) ![](/assets/reverse.png)

* ### Manually entered numbers

Just enter the two numbers you wish to see in the two fields at the bottom of the form.

_A note on those fields: They are always occupied by 'placeholder' values.  These are randomly generated number but they are always in a pair of exact opposites.  When you enter a number into the 1st field, the 2nd field's placeholder is automatically updated with the opposite.  You can ignore those numbers.  They are mainly there to use as a quick research tool to find opposites._