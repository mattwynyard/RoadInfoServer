#include "NZMGTransform.h"

#include <iostream>

int main (int argc, char *argv[]) {

	//double east = 2765191.269581673;
	//double north = 6412583.869434468;
	double east = atof(argv[1]);
	double north = atof(argv[2]);
	std::cout << "Northing " + std::to_string(north) << std::endl;
	std::cout << "Easting " + std::to_string(east) << std::endl;
	NZMGTransform x = NZMGTransform();
	double a[2] = {east, north};
	std::array<double, 2> b;

	b = x.nzmgToNZGD1949(a);
	std::cout << "Latitude " + std::to_string(b[0]) << std::endl;
	std::cout << "Longitude " + std::to_string(b[1]) << std::endl;
	
	return 0;
}
