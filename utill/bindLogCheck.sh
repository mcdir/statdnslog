#!/bin/bash
# run watch -n 120 './bindLogCheck.sh'

echo "Top 20 Domains"
echo ""
cat /var/log/named/bind.log* | grep 'queries' | cut -d '/' -f 3 | \
   sed 's/www.//' | sort | uniq -c | sort -nr | head -n 20