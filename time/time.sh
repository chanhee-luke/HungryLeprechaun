#!/bin/sh

cp ../locations.csv locations.csv
makefiles.py
rm locations.csv
for file in $(ls locations*.csv); do
	time ../kdtree -f $file -86.238754 41.69917
done
