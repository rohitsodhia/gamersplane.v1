#!/bin/bash

for file in data/*; do
	collection=`expr "$file" : 'data/\(.*\).json'`
	mongoimport -d gamersplane -c $collection $file
done
