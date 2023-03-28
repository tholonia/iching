#!/bin/sh
ID=$1 $2

echo "=> throw.sh $1 $2" >> log/toss.log
echo "----------------------------------" >> log/toss.log

wget -o /dev/null --no-cookies --no-cache --no-http-keep-alive --random-wait --output-document=-  https://www.random.org/coins/\?cur\=40-antique.antonius-pius\&num\=3 |grep jpg|grep obverse|wc -l > id/${1}
#echo "<div style='font-weight:bold'>Throwing line $2</div>"
