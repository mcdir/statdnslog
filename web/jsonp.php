<?php
function fatal_error( $sErrorMessage = '' )
{
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
	die($sErrorMessage);
}

$bSearch = false;
if(!isset($_GET['GroupeByDNS'])){
	$aColumns = array(
		'dates',
		'date_time',
		'dns',
		'client_ip',
		'client_port',
		'querys'
	);
}
else
	$aColumns = array(
		'dates',
		'dns',
		'client_ip',
		'count_dns'
	);
$aConfig = file_exists(dirname(__FILE__).'/include/config.ini')
	? parse_ini_file(dirname(__FILE__).'/include/config.ini')
	: array();
include_once dirname(__FILE__).'/include/db.php';

// Ordering
$sOrder = "";
if(isset($_GET['iSortCol_0']))
{
	$sOrder = "ORDER BY  ";
	for( $i = 0; $i < intval($_GET['iSortingCols']); $i++ )
	{
		if($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true"){
			$sTmp = preg_replace('/.+ +AS +/is', '', $aColumns[intval($_GET['iSortCol_' . $i])]);
			$sOrder .=  $sTmp .' ' .
				($_GET['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc') . ", ";
		}
	}
	$sOrder = substr_replace($sOrder,"",-2);
	if($sOrder == "ORDER BY")
		$sOrder = "";
}

$sWhere = "";
if(isset($_GET['sSearch']) && $_GET['sSearch'] != "")
{
	$sWhere = ' WHERE (';
	for( $i = 0; $i < count($aColumns); $i++ )
		if(isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true"){
			$sTmp = preg_replace('/.+ +AS +/is', '', $aColumns[$i]);
			if( $aColumns[$i] =='dns' )
				$sWhere .= $aColumns[$i] . ' LIKE '. $dbh->quote(($_GET['sSearch']).'%'). ' OR ';
			else
				$sWhere .= $aColumns[$i] . ' = '. $dbh->quote($_GET['sSearch']). ' OR ';
			$bSearch = true;
		}
	$sWhere = substr_replace($sWhere,"",-3);
	$sWhere .= ')';
}

// Individual column filtering
for( $i = 0; $i < count($aColumns); $i++ )
{
	if(isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '')
	{
		if($sWhere == "")
			$sWhere = "WHERE ";
		else
			$sWhere .= " AND ";
		$sTmp = preg_replace('/.+ +AS +/is', '', $aColumns[$i]);
		if( $aColumns[$i] =='dns' )
			$sWhere .= $sTmp . ' LIKE '.$dbh->quote('%'.($_GET['sSearch_' . $i].'%')) . ' ';
		elseif($sTmp=='client_ip')
			$sWhere .= 'INET_NTOA(dns_log.client_ip) = '.$dbh->quote($_GET['sSearch_' . $i]) . ' ';
		else
			$sWhere .= $sTmp . ' = '.$dbh->quote($_GET['sSearch_' . $i]) . ' ';
		$bSearch = true;
	}
}

$iGroupeByDay='';
if(!$bSearch && isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] == '-1'){
	$sMaxDate = $dbh->query(
		'SELECT MAX(dates)
		FROM `dns_log`
		LIMIT 1'
	)->fetch(PDO::FETCH_COLUMN);
	$iGroupeByDay = ' `dates` = "'.$sMaxDate.'" ';

	if(!$sWhere && $iGroupeByDay)
		$sWhere = ' WHERE '.$iGroupeByDay;
	elseif($iGroupeByDay)
		$sWhere .= ' AND '.$iGroupeByDay;
}

$sGroupBy ='';
if(isset($_GET['GroupeByDNS']))
{
	$sGroupBy=' GROUP BY dns_log.dates,dns_log.dns,client_ip ';
}

$rResult = $dbh->query(
	'SELECT SQL_CALC_FOUND_ROWS dns_log.dates,dns_log.date_time,dns_log.dns,
		INET_NTOA(dns_log.client_ip) AS client_ip,dns_log.client_port,dns_log.querys,dns_info.type
		'.(isset($_GET['GroupeByDNS'])?',COUNT(dns_log.dns) AS count_dns ':'').'
	FROM `dns_log` '.
	(isset($_GET['onlyBan'])?'INNER':'LEFT' ).' JOIN dns_info ON `dns_info`.dnsinfo = dns_log.dns
	'.$sWhere.'
	'.$sGroupBy.'
	'.$sOrder.'
	'.( isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1'
			? " LIMIT
				" . abs(intval($_GET['iDisplayStart'],10)) . ', '.
					abs(intval($_GET['iDisplayLength'],10))
			: ''
	)
)->fetchAll(PDO::FETCH_ASSOC);

if(!$rResult)
	fatal_error('Some Error (');

// Data set length after filtering
$iFilteredTotal = $dbh->query(
	'SELECT FOUND_ROWS()'
)->fetch(PDO::FETCH_COLUMN);

// Total data set length
$iTotal = $dbh->query(
	'SELECT COUNT(dates)
	FROM `dns_log`'
)->fetch(PDO::FETCH_COLUMN);

// Output
$output = array(
	"sEcho" => intval($_GET['sEcho']),
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => array()
);
foreach ($rResult as $k=>$aRow)
{
	$row = array();
	for( $i = 0; $i < count($aColumns); $i++ )
	{
		if(isset($aColumns[$i]))
		{
			if($aColumns[$i] == "version")
				$row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
			elseif($aColumns[$i] != ' ')
			{
				$row[] = $aRow[$aColumns[$i]];
				//$sTmp = preg_replace('/.+ +AS +/is', '', $aColumns[$i]);
				//$row[] = $aRow[$sTmp];
			}
		}
	}
	if($aRow['type']){
		$row['DT_RowId'] = "row_".$k;
		$row['DT_RowClass'] = "gradeX";
	}
	$output['aaData'][] = $row;
}
if(isset($_GET['callback']))
	echo $_GET['callback'] . '(' . json_encode($output) . ');';
else
	echo  json_encode($output);
?>