#!/usr/bin/php
<?php
/**
 * run
 *   ./add_from_blacklist.php --ads=/etc/bind/named.conf.adsblock --conf=/etc/bind/blocklist.conf --zone=/etc/bind/block --ip=192.168.0.1
 * test
 *   ./add_from_blacklist.php --ads=/etc/bind/test_data/named.conf.adsblock --conf=test_data/blocklist.conf --zone=./test_data/zone --notify
 */
if(php_sapi_name()!=="cli"){
	echo "Error: run cli only\n";
	die();
}
require_once dirname(__FILE__).'/../web/include/functions.php';
$aParams = getopt('f::c::z::i:nvsh',array(
	'ads::',
	'conf::',
	'zone::',
	'ip::',
	'notify',
	'verbose',
	'save',
	'help'
));

$aConfig = file_exists(dirname(__FILE__).'/../web/include/config.ini')
	? parse_ini_file(dirname(__FILE__).'/../web/include/config.ini')
	: array();

if(isset($aParams['h'])
	|| isset($aParams['help'])
){
	echo <<<CLI

 Options:
  -f, --ads       file path, with bind blocklist adsblock. Required, if not set in config.ini.
                  format file:
                    zone "101com.com" { type master; file "/etc/bind/null.zone.file"; };
                  get frome:
                    http://pgl.yoyo.org/adservers/serverlist.php?hostformat=bindconfig;showintro=0
  -c, --conf      bind configure blocklist adsblock file path
  -z, --zone      bind blockzone path

  -h, --help      show help.
  -v, --verbose   verbose mode. shows what is going on.
\n
CLI;
	die();
}
if(!$aConfig && (
	(!isset($aParams['ads']) && !isset($aParams['f']))
	|| (!isset($aParams['conf']) && !isset($aParams['c']))
	|| (!isset($aParams['zone']) && !isset($aParams['z']))
	|| (!isset($aParams['ip']) && !isset($aParams['i']))
)){
	echo "Required paraps mast set, try -h or --help for help \n";
	die();
}

// Required {{{
if(!isset($aParams['ads']) && !isset($aParams['f']))
	$bind_blocklist_adsblock_file = $aConfig['bind_blocklist_adsblock_file'];
else
	$bind_blocklist_adsblock_file = isset($aParams['ads'])
		? $aParams['ads']
		: $aParams['f'];
if(!isset($aParams['conf']) && !isset($aParams['c']))
	$bind_blocklist_conf = $aConfig['bind_blocklist_conf'];
else
	$bind_blocklist_conf = isset($aParams['conf'])
		? $aParams['conf']
		: $aParams['c'];
if(!isset($aParams['zone']) && !isset($aParams['z']))
	$bind_blockzone_path = $aConfig['bind_blockzone_path'];
else
	$bind_blockzone_path = isset($aParams['zone'])
		? $aParams['zone']
		: $aParams['z'];
if(!isset($aParams['ip']) && !isset($aParams['i']))
	$bind_zone_ip = $aConfig['bind_zone_ip'];
else
	$bind_zone_ip = isset($aParams['ip'])
		? $aParams['ip']
		: $aParams['i'];
// Required }}}

$bind_zone_notify_no = !isset($aParams['notify']) && !isset($aParams['n'])
	? $aConfig['bind_zone_notify_no']
	: true;
$bSave = !isset($aParams['save']) && !isset($aParams['s'])
	? $aConfig['save']
	: true;
$bVerbose = (isset($aParams['v']) || isset($aParams['verbose']))
	? true
	: false;

if($bSave)
	include_once dirname(__FILE__).'/../web/include/db.php';

// error {{{
if (!file_exists($bind_blockzone_path)) {
	echo 'The path {'.$bind_blockzone_path.'} is not writable or exists'."\n";
	die();
}
if(!is_writable ($bind_blockzone_path) ) {
	echo 'The path {'.$bind_blockzone_path.'} is not writable'."\n";
	die();
}
if(!is_writable($bind_blocklist_conf)) {
	if(!file_exists($bind_blocklist_conf)){
		file_put_contents($bind_blocklist_conf,'');
	}else{
		echo 'The file {'.$bind_blocklist_conf.'} is not writable'."\n";
		die();
	}
}
if(!file_exists($bind_blocklist_adsblock_file)){
	echo 'The file {'.$bind_blocklist_adsblock_file.'} is not exists'."\n";
	die();
}
// }}}

$aList = file($bind_blocklist_adsblock_file);
$sConf = file_get_contents($bind_blocklist_conf);
if($bVerbose)
	echo "reade ".count($aList)." line frome {$bind_blocklist_adsblock_file}\n";
if(!count($aList))
{
	echo 'The file {'.$bind_blocklist_adsblock_file.'} is empty'."\n";
	die();
}
foreach ($aList as $v)
{
	if(preg_match('/(?<=zone )\"([^\"]+)\"/i', $v,$aRez) ){
		$sDns = $aRez[1];
		if(!preg_match('/'.$sDns.'/i', $sConf))
		{
			//zone "ads.specificpop.com" { type master; file "/etc/bind/block/db.empty"; };
			$data = 'zone "'.$sDns.'" { '.
				($bind_zone_notify_no ? ' notify no; ' : '').
				'type master; file "'.$bind_blockzone_path.'/db.'.$sDns.'"; };'."\n";

			$zone = gen_zone($sDns,$bind_zone_ip);

			file_put_contents($bind_blocklist_conf, $data,FILE_APPEND);
			file_put_contents($bind_blockzone_path.'/db.'.$sDns, $zone);

			if($bSave)
			{
				$dbh->exec(
					'INSERT IGNORE INTO `dns_info` (`dns`) VALUES ('.$dbh->quote($sDns).')'
				);
			}
			if($bVerbose)
				echo 'add '.$sDns."\n";
		}elseif($bVerbose)
			echo 'isset '.$sDns."\n";
	}
}