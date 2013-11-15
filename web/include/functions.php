<?php
if (PHP_SAPI != 'cli') {
	throw new Exception("Access denied!");
}
function gen_zone($origin,$ip) {
	return '
$ORIGIN  '.$origin.'.
$TTL 86400 ; 1 day
@ IN SOA localhost. root.localhost. (
        2008041501 ; serial
        10800 ; refresh (3 hours)
        900 ; retry (15 minutes)
        604800 ; expire (1 week)
        86400 ; minimum (1 day)
)
@ IN NS localhost.
@ IN A '.$ip.'
www IN CNAME '.$origin.'.

; Machine Names
ns1     IN      A       '.$ip.'
*       IN      A       '.$ip.'
';
}