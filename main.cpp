
#include <iostream>
#include <array>
#include <vector>
#include <fstream>
#include <sstream>
#include <string>
#include <cstring>

#include "kdtree.h"

using namespace std;

struct Descriptor {
	//info about a given location
	string name;
};

struct Filter {

	bool operator()(){
		return true;
	}
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

void usage(string programname, int exitcode){
	cout << "Usage: " << programname << " flags... longitude latitude\n"
			  << "Flags: \n"
			  << "\t-h      : shows this prompt\n"
			  << "\t-f FILE : chooses the file to load locations from\n"
			  << "\t-k NUM  : select how many to return\n"
			  << "\t-r RAD  : find within this radius\n";

			  //additional args for when FILTER is implemented
	exit(exitcode);
}

int main(int argc, char** argv) {

	double r = -1;
	int k = 10;

	// PARSE
	int argind = 1;
	string filename = "locations.csv";
	string programname = string(argv[0]);
	while (argind < argc && strlen(argv[argind]) > 1 && argv[argind][0] == '-') {
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
		getline(ss, word, ';');
		double lon = stod(word);

		getline(ss, word, ';');
		double lat = stod(word);


		getline(ss, word);
		string name = word;

		Descriptor d = {name};

		Location l(lon, lat, d);
		locs.push_back(l);
	}

	// SETUP
	kdt::KDTree <Location> kdtree(locs);

	Location query(longSearch, latSearch);//passed in params
	vector<int> result;

	// SEARCH
	if(r < 0){
		// k-nearest neigbors search
		result = kdtree.knnSearch(query, k);

	} else {
		// radius search
		result = kdtree.radiusSearch(query, r);
		result.resize(k);//reduces number to k
	}

	// OUTPUT
	for(auto it = result.begin(); it != result.end(); it++){
		cout << *it << "\n";//outputs the line number
	}

	return 0;
}
