#!/bin/bash
while true
do 
sleep 2

pro="$(ps aux | grep newsave | grep php | sort -k3,3)"
allnum="$(ps aux | grep newsave | grep php | sort -k3,3 | wc -l)"
echo $pro
exit
for ((c=1;c<=$allnum;c++))
{
	echo $c
}
exit
mytime="$(date -d '1 hour ago' +%H%M)"

if [ "" == "$pro" ];then
continue
fi
time="$(echo $pro|awk '{split($9,tab,/:/);{print tab[1]tab[2]}}')"
pid="$(echo $pro| awk '{print $2}')"
echo $mytime
echo $time
echo $pid
nm="$(echo $time|grep /:/)"
if [ "" == $nm ];then
kill -9 "$pid"
echo "$pid" >> "/data/www/newsave/tools/kphp.txt"
elif [ $mytime -gt $time ];then
kill -9 "$pid"
echo "$pid" >> "/data/www/newsave/tools/kphp.txt"
#echo "yes"
fi
done
