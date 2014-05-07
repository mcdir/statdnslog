StatDNSlog
==========

# DNS Bind Log Analyzer PHP

Simple analysis and SQL storage (mysql option) for Bind DNS server's logs.

The soft includes a web interface to analyze the data collected from the analyzed logs, and create config BIND9 for ban
DNS from list.

### Site 
http://statdns.nedze.com

### Demo 
http://demo.statdns.nedze.com
	
## Requirements

This gem was tested with:

- php > 5.3
- bind9
- mysql (option)
- apache (option)

## Installation

- download from zip or git
- config (see Configuration example):
copy file frome
    web/include/config.ini.example
to
    web/include/config.ini

example ( linux command line ):

    cp web/include/config.ini.example web/include/config.ini

## Configuration example

    [db]
    host=localhost
    port=3306
    dbname = statdns
    username = username
    password = password

    [bind config]
    bind_query_log_file = /var/log/named/query.log
    bind_blocklist_conf = /etc/bind/blocklist.conf
    bind_blocklist_adsblock_file = /root/statdns/named.conf.adsblock
    bind_blocklist_append_file = /etc/bind/ban.txt
    bind_blockzone_path = /etc/bind/block
    bind_zone_ip = 127.0.0.1
    bind_zone_notify_no = no

    [options]
    save=yes
    test=no

## Config DB

create db `statdns` mysql:

    echo "CREATE DATABASE IF NOT EXISTS statdns;" | mysql -uroot -p
run under root or sudo:

    bash utill/install.sh

### Bind9

To configure **Bind9** add these lines to _/etc/bind/named.conf.options_ (or whatever your s.o. and bind installation require)

    logging{
        channel "querylog" {
                file "/var/log/named/query.log";
                print-time yes;
        };

        category queries { querylog; };
    };

Restart bind and make sure than the _query.log_ file contains lines as this:

    with time
      01-Nov-2013 13:55:56.263 queries: info: client 192.168.0.1#60788: query: japi.icq.com IN A + (192.168.0.1)
    not time
      queries: info: client 192.168.29.2#52352: query: japi.icq.com IN A + (192.168.0.1)
    not time and no queries
       client 192.168.0.2#52352: query: japi.icq.com IN A + (192.168.0.1)
    no queries
       10-Nov-2013 12:15:22.468 client 192.168.0.100#1026: query: time-a.nist.gov IN A + (192.168.0.1)

or the regexp will fail :(


For parce log run:

    ./parce_bind_log.php -v


