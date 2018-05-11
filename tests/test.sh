#!/bin/sh

src="locations.json"
file="locations.csv"
newfile="newfile"

wcl(){
	echo "$1" | wc -l
}

if [ $(pwd | grep tests | wc -l) -ne 1 ]; then
	cd tests
fi

#Setup locations.csv
../convertjson.py > $file

echo "Testing Makefile:"
cd ..
if [ $1 ]; then
	make clean
fi
make all
cd tests

echo "Let the testing commence:"

echo "\tTest: -h"

#No args test
echo "\t\t(no args)"
result=$(../kdtree | grep "Usage\|Flags" | wc -l)
if [ $result -lt 2 ]; then
	echo "Err: No args should show help message, which should show Usage and Flags"
	exit 1;
fi

#Help flag test
echo "\t\t-h"
result=$(../kdtree -h | grep "Usage\|Flags" | wc -l)
if [ $result -lt 2 ]; then
	echo "Err: Help message should show Usage and Flags"
	exit 2;
fi

#Invalid arg test
echo "\t\t-w"
result=$(../kdtree -w | grep "Usage\|Flags" | wc -l)
if [ $result -lt 2 ]; then
	echo "Err: Invalid flag should show Usage and Flags"
	exit 3;
fi

center=$(head -n 1 $file | sed -r "s/([^,]+),([^,]+),([^,]+,?)*/\1 \2/")
num=$(wc -l $file | cut -d ' ' -f 1)

echo "\tTest: -k"

#Easy test
echo "\t\t-k (one)"
result=$(../kdtree -k 1 $center)
if [ $result -ne 0 ]; then
	echo "Err: Only result: " $result "not 0";
	exit 4;
fi

#Counting test
echo "\t\t-k (mid)"
result=$(../kdtree -k 5 $center)
numlast=$(wcl "$result")
if [ $numlast -ne 5 ]; then
	echo "Err: Number of results:" $numlast ", not 5"
	exit 5;
fi

#All of them test
echo "\t\t-k (max)"
result=$(../kdtree -k $num $center)
numlast=$(wcl "$result")
if [ $numlast -ne $num ]; then
	echo "Err: Number of results:" $numlast ", not" $num
	exit 6;
fi

echo "\tTest: -f"

#File test
echo "\t\t-f"
mv $file $newfile
result=$(../kdtree -k $num -f $newfile $center)
numlast=$(wcl "$result")
if [ $numlast -ne $num ]; then
	echo "Err: Number of results:" $numlast ", not" $num
	exit 7;
fi

#No file test
echo "\t\t-f (no file)"
result=$(../kdtree -f $file $center)
numlast=$(wcl "$result")
if [ $numlast -lt 1 ]; then
	echo "Err: Should be no results, but had:" $numlast
	exit 8;
fi

mv $newfile $file

echo "\tTest: -q"

#Letter query test
echo "\t\t-q (simple)"
letter="r"
result=$(../kdtree -q $letter $center)
numnew=$(cat $file | grep $letter | wc -l)
numlast=$(wcl "$result")
if [ $numlast -lt $numnew ]; then
	echo "Err: Number of results:" $numlast ", not" $numnew
	exit 9;
fi

#Regex query test
echo "\t\t-q (regex)"
regex="e.*e"
result=$(../kdtree -q $regex $center)
numnew=$(cat $file | grep -E "$regex" | wc -l)
numlast=$(wcl "$result")
if [ $numlast -lt $numnew ]; then
	echo "Err: Number of results:" $numlast ", not" $numnew
	exit 10;
fi

echo "\tTest: -r"

#0 Distance test
echo "\t\t-r (no distance)"
result=$(../kdtree -r 0 $center)
numlast=$(wcl "$result")
if [ $numlast -ne 1 ]; then
	echo "Err: Number of results:" $numlast ", not 1"
	exit 11;
fi

#Some Distance test
echo "\t\t-r (average distance)"
result=$(../kdtree -r .2 $center)
numlast=$(wcl "$result")
if [ $numlast -eq 1 -o $numlast -eq $num ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 12;
fi

#Large Distance test
echo "\t\t-r (absurd distance)"
result=$(../kdtree -r 20000 $center)
numlast=$(wcl "$result")
if [ $numlast -ne $num ]; then
	echo "Err: Number of results:" $numlast ", not" $num
	exit 13;
fi

echo "\tTest: -p"

#No prices
echo "\t\t-p (no prices)"
result=$(../kdtree -p 0 $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -ne 0 ]; then
	echo "Err: Number of results:" $numlast ", not 0"
	exit 14;
fi

#Some prices test
echo "\t\t-p (cheap)"
result=$(../kdtree -p 3 $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -eq $num ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 15;
fi

#Large Distance test
echo "\t\t-p (all prices)"
result=$(../kdtree -p 15 $center)
numlast=$(wcl "$result")
if [ $numlast -ne $num ]; then
	echo "Err: Number of results:" $numlast ", not" $num
	exit 16;
fi

echo "\tTest: (combos)"

k=5
p=3
r=".2"
q=$letter

echo "\t\t-k -q"
result=$(../kdtree -k $k -q $q $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -gt $k ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 17;
fi

echo "\t\t-k -r"
result=$(../kdtree -k $k -r $r $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -gt $k ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 18;
fi

echo "\t\t-k -p"
result=$(../kdtree -k $k -p $p $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -gt $k ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 19;
fi

echo "\t\t-q -r"
result=$(../kdtree -q $q -r $r $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -eq $num ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 20;
fi

echo "\t\t-q -p"
result=$(../kdtree -q $q -p $p $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -eq $num ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 21;
fi

echo "\t\t-r -p"
result=$(../kdtree -r $r -p $p $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -eq $num ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 22;
fi

echo "\t\t-k -q -r"
result=$(../kdtree -k $k -q $q -r $r $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -eq $num ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 23;
fi

echo "\t\t-k -q -p"
result=$(../kdtree -k $k -q $q -p $p $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -eq $num ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 24;
fi

echo "\t\t-k -r -p"
result=$(../kdtree -k $k -r $r -p $p $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -eq $num ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 25;
fi

echo "\t\t-q -r -p"
result=$(../kdtree -q $q -r $r -p $p $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -eq $num ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 26;
fi

echo "\t\t-k -q -r -p"
result=$(../kdtree -k $k -q $q -r $r -p $p $center)
numlast=$(wcl "$result")
if [ $(echo "$result" | wc -w) -eq 0 -o $numlast -eq $num ]; then
	echo "Err: Number should be between min and max, but was:" $numlast
	exit 27;
fi

echo "Score: 100.0"

exit 0;
