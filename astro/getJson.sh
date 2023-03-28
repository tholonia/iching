#!/bin/bash

X="/usr/local/bin/phantomjs $1/js/savepage.js $2 > $1/js/astrodataJson.html"

echo "${X}" > /tmp/x

${X} >> $1/js/astrodataJson.html 2>&1


#nohup ${X} >>/tmp/getJson.log 2>&1


