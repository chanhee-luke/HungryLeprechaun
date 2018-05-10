
#include <iostream>
#include <array>
#include <vector>
#include <fstream>
#include <sstream>
#include <string>
#include <cstring>
#include <regex>

#include "kdtree.h"

using namespace std;

struct Descriptor {
	//info about a given location
	string name;
	int price;
};

// user-defined point type
// inherits array in order to use operator[]
class Location: public array <double, 2> {
	public:
		// dimension of space (or "k" of k-d tree)
		// KDTree class accesses this member
		static const int DIM = 2;
		Descriptor d;

		// the constructors
		Location() {}
		Location(double lon, double lat) {
			(*this)[0] = lon;
			(*this)[1] = lat;
		}
		Location(double lon, double lat, Descriptor &d) {
			(*this)[0] = lon;
			(*this)[1] = lat;
			this->d = d;
		}
};

struct Filter {
	regex query;
	int prices;
	bool operator()(Location l){
		if(!regex_search(l.d.name, query))
			return false;
		if(!l.d.price && !(2 << l.d.price & prices))
			return false;
		return true;
	}
};

void usage(string programname, int exitcode){
	cout << "Usage: " << programname << " flags... longitude latitude\n"
			  << "Flags: \n"
			  << "\t-h        : shows this prompt\n"
			  << "\t-f FILE   : chooses the file to load locations from\n"
			  << "\t-k NUMBER : select how many to return\n"
			  << "\t-r RADIUS : find within this radius\n"
			  << "\t-q QUERY  : find similar to this\n"
			  << "\t-p PRICES : find with selected prices (bitwise mask from 0 to 15, 1 = $, 8 = $$$$)\n";

			  //TODO additional args for when FILTER is implemented
	exit(exitcode);
}

static const char SEP = ',';

int main(int argc, char** argv) {

	double r = -1;
	int k = 10;
	int prices = 15;
	regex reg(".*");

	// PARSE
	int argind = 1;
	string filename = "locations.csv";
	string programname = string(argv[0]);
	while (argind < argc && strlen(argv[argind]) > 1 && argv[argind][0] == '-' && strlen(argv[argind]) < 3) {
		char *arg = argv[argind++];
		switch (arg[1]) {
			case 'h':
				usage(programname, 0);
				break;
			case 'f': {
					char* format = argv[argind++];
					filename = string(format);
				}
				break;
			case 'k': {
					char* num = argv[argind++];
					k = atoi(num);
				}
				break;
			case 'r': {
					char* dist = argv[argind++];
					r = atof(dist);
				}
				break;
			case 'q': {
					reg = regex(argv[argind++], regex_constants::icase | regex_constants::ECMAScript);
				}
				break;
			case 'p': {
					char* num = argv[argind++];
					prices = atoi(num);
				}
				break;
			default:
				usage(programname, 1);
				break;
		}
	}
	if(argc - argind < 2) usage(programname, 1);

	double longSearch = atof(argv[argind++]);
	double latSearch = atof(argv[argind]);


	// READ DATA FILE
	ifstream str(filename);
	vector<Location> locs;
	string line;
	while(getline(str, line)){
		stringstream ss(line);
		string word;
		getline(ss, word, SEP);
		double lon = stod(word);

		getline(ss, word, SEP);
		double lat = stod(word);


		getline(ss, word);
		string name = word;

		Descriptor d = {name, 0}; //TODO add price here

		Location l(lon, lat, d);//TODO fix long and lat switchup
		locs.push_back(l);
	}

	// SETUP
	kdt::KDTree <Location> kdtree(locs);

	Location query(longSearch, latSearch);//passed in params
	vector<int> result;

	Filter f = {reg, prices};

	// SEARCH
	if(r < 0){
		// k-nearest neigbors search
		result = kdtree.knnSearch(query, k, f);
	} else {
		// radius search
		result = kdtree.radiusSearch(query, r, f);
		result.resize(k);//reduces number to k
	}

	// OUTPUT
	for(auto it = result.begin(); it != result.end(); it++){
		cout << *it << "\n";//outputs the line number
	}

	return 0;
}
