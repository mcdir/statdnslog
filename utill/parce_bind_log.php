#!/usr/bin/env php
<?php
/**
 * run
 *    ./parce_bind_log.php /var/log/named/query.log
 *    ./parce_bind_log.php /var/log/named/query.log --verbose
 */
if(php_sapi_name()!=="cli"){
	echo "Error: run cli only\n";
	die();
}
$aParams = getopt('f::vsht',array(
	'file::',
	'verbose',
	'save',
	'help',
	'test'
));

require_once dirname(__FILE__).'/../web/include/functions.php';
// /usr/share/statdns/web/include/config.ini
$aConfig = file_exists(dirname(__FILE__).'/../web/include/config.ini')
	? parse_ini_file(dirname(__FILE__).'/../web/include/config.ini')
	: array();

if(isset($aParams['h'])
	|| isset($aParams['help'])
){
	echo <<<CLI

 Options:
  -f, --file      file path, with bind query log. Required, if not set in config.ini.
                  format file:
                    with time
                        01-Nov-2013 13:55:56.263 queries: info: client 192.168.1.2#60788: query: test.com IN A + (192.168.1.1)
                    not time
                        queries: info: client 192.168.1.2#52352: query: test.com IN A + (192.168.1.1)
                    not time and no queries
                        client 192.168.1.2#52352: query: test.com IN A + (192.168.1.1)
                    no queries
                        10-Nov-2013 12:15:22.468 client 192.168.1.2#1026: query: test.com IN A + (192.168.1.1)
  -h, --help      show help.
  -v, --verbose   verbose mode. shows what is going on.
  -t, --test      test mode. try parce log format, not save in base.
\n
CLI;

	die();
}
if(!$aConfig &&
	!isset($aParams['file']) && !isset($aParams['f'])
){
	echo "Required paraps mast set, try -h or --help for help \n";
	die();
}
// Required {{{
if(!isset($aParams['file']) && !isset($aParams['f']))
	$bind_query_log = $aConfig['bind_query_log_file'];
else
	$bind_query_log = isset($aParams['file'])
		? $aParams['file']
		: $aParams['f'];
// }}}
$bVerbose = (isset($aParams['v']) || isset($aParams['verbose']))
	? true
	: false;
$bTest = !isset($aParams['test']) && !isset($aParams['t'])
	? $aConfig['test']
	: (isset($aParams['test']) || isset($aParams['t'])
		? true
		: false);
$bSave = !isset($aParams['save']) && !isset($aParams['s'])
	? $aConfig['save']
	: $bTest;
if($bSave)
	include_once dirname(__FILE__).'/../web/include/db.php';

if(!$bind_query_log){
	print "Error: set file path for bind query log\n";
	die();
}
if(!file_exists($bind_query_log)){
	print 'Error: file '.$bind_query_log." not exists\n";
	die();
}

$iTimeIfnotSet = time();
$iDateIfnotSet = date("Y-m-d",$iTimeIfnotSet);

$aLog = file($bind_query_log);
foreach($aLog as $v)
{
	preg_match('/(.*)(queries: info: )*client ([^\#]+)\#([^\:]+)\: query: ([^\ ]+\ )([^\+]+)\+ \(([^\)]+)\)/is',trim($v),$aRez);
	if($aRez)
	{
		$aRez[1]=preg_replace('/ queries: info: /is','',$aRez[1]);
		$iTime = strtotime(trim($aRez[1]));
		$iTime = $iTime?$iTime:$iTimeIfnotSet;
		if($bVerbose)
			echo $aRez[0] . "\n";
		if($bTest)
		{
			echo 'Parce format: '.trim($v);
			foreach ($aRez as $k=>$a)
				switch ($k) {
					case '1': echo ' time: '.$iTime.' '.date("Y-m-d H:m:s",$iTime)."\n"; break;
					case '3': echo " client ip: ".$a."\n";break;
					case '4': echo " client port: ".$a."\n";break;
					case '5': echo " DNS: ".$a."\n";break;
					case '6': echo " type: ".$a."\n";break;
					case '7': echo " server ip: ".$a."\n";break;
				}
			echo "\n";
		}
		if($bSave)
			$dbh->exec(
				'INSERT IGNORE INTO `dns_log` (`dates`, `date_time`,`dns`, `client_ip`,`client_port`,`querys` )
				VALUES (
					FROM_DAYS(TO_DAYS(FROM_UNIXTIME('.$dbh->quote($iTime).'))),
					FROM_UNIXTIME('.$dbh->quote($iTime).'),
					'.$dbh->quote($aRez[5]).',
					INET_ATON('.$dbh->quote($aRez[3]).'),
					'.$dbh->quote($aRez[4]).',
					'.$dbh->quote($aRez[6]).
				')'
			);
	}
}
