#!/usr/bin/env python

import re

regex = r"^\s+{(?:\s?(?:(?:\"lat\":(?P<lat>[-.0-9]+))|(?:\"long\":(?P<long>[-.0-9]+))|(?:\"name\":\"(?P<name>[^\"]+)\")|(?:[^{,}]+?)),?)+\s},?$"

subst = "\g<long>,\g<lat>,\g<name>"

with open('locations.json', 'r') as content_file:
	str = content_file.read()
	mid = re.sub(regex, subst, str, 0, re.MULTILINE)

	result = '\n'.join([ line for line in mid.split('\n') if len(line) > 3 ]);

	if result:
		print (result)
