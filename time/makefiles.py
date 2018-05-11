#!/usr/bin/env python

import re

min = 1
max = 512

factor = 4

num = min

with open('locations.csv', 'r') as content_file:
	lines = content_file.read().split('\n')
	while num < max:
		i = 0
		with open('locations' + str(num) + '.csv', 'w') as out:
			while i < num:
				out.write(lines[i % len(lines)])
				i += 1
		num *= factor
