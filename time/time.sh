#!/bin/sh

fileInUse="file.csv"

rm locations*.csv
cp ../locations.csv locations.csv
makefiles.py
rm locations.csv
make
echo "Normal:"
for file in $(ls -r locations*.csv); do
	grep , $file > $fileInUse
	echo $(echo "$file" | grep -o -E [0-9]+): $(measure ../kdtree -k 5 -f $fileInUse 10 -86.238754 41.69917 | tail -n 1 | cut -d ' ' -f 1)
done
echo "Price:"
for file in $(ls -r locations*.csv); do
        grep , $file > $fileInUse
        echo $(echo "$file" | grep -o -E [0-9]+): $(measure ../kdtree -k 5 -p 3 -f $fileInUse 10 -86.238754 41.69917 | tail -n 1 | cut -d ' ' -f 1)
done
echo "Query:"
for file in $(ls -r locations*.csv); do
        grep , $file > $fileInUse
        echo $(echo "$file" | grep -o -E [0-9]+): $(measure ../kdtree -k 5 -q r -f $fileInUse 10 -86.238754 41.69917 | tail -n 1 | cut -d ' ' -f 1)
done
rm locations*.csv $fileInUse
