all:	kdtree locations.csv

kdtree:	main.cpp kdtree.h
	g++ -std=c++11 -Wall -o $@ $^

locations.csv:	locations.json
	./convertjson.py > $@

clean:	kdtree locations.csv
	rm $^
