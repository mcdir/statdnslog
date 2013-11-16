#!/usr/bin/env bash
#base install to  /usr/share/statdns/

chmod 744 add_from_blacklist.php append_from_text_blacklist.php \
  parce_bind_log.php bindLogCheck.sh bindupdate.sh  create_db.php \
  install.sh

echo -e "create table in db\n"
./create_db.php

echo -e "\ninstall utill and site in /usr/share/statdns/ "
rm -rf /usr/share/statdns/*
mkdir -p /usr/share/statdns
rsync -r --exclude=.svn ./../ /usr/share/statdns
echo -e "done"

echo -e "reconfig  apache for done. Have fun ;)\n"