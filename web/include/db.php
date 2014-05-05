<?php
try
{
	$dbh = new PDO('mysql:host=' . $aConfig['host'] . ';port=' . $aConfig['port'] . ';dbname=' . $aConfig['dbname'],
	$aConfig['username'],
	$aConfig['password'],array(
		PDO::ATTR_PERSISTENT => false
	)
	);
}catch (PDOException $e)
{
	print "Error!: " . $e->getMessage() . PHP_EOL;
	die();
}