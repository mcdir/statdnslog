#!/bin/sh
echo -e "[*] Going to update your AD list UmKay! [*]"
sleep 2
wget -O /etc/bind/named.conf.ads  "http://pgl.yoyo.org/adservers/serverlist.php?hostformat=bindconfig;showintro=0&mimetype=plaintext"
sleep 2
echo"reloading Bind9"
/etc/init.d/bind9 reload