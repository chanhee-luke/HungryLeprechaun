#!/usr/bin/env python

import re

min = 1
max = 100000

factor = 10

num = min

with open('locations.csv', 'r') as content_file:
	lines = content_file.read().split('\n')
	while num < max:
		i = 0
		with open('locations' + str(num) + '.csv', 'w') as out:
			while i < num:
				line = lines[i % len(lines)].split(",")
				if len(line) > 1:
					line[0] += str(i)
					line[1] += str(i)[::-1]
				line = ','.join(line)
				out.write(line + "\n")
				i += 1
		num *= factor
