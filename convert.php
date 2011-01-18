<?php

/* Convert the csv file to PHP array */
if($argc < 2)
{
	die('USAGE: php ' . $argv[0] . " <infile>\n");
}

$f = fopen($argv[1], 'r');
if($f === false)
{
	die('Could not open ' . $argv[1] . "\n");
}

$country_to_prefix = array();
$prefix_to_country = array();

while(($row = fgetcsv($f, 1000, ',')) !== false)
{
	/* Special case: more than one number supplied */
	if($row[3][0] == '(')
	{  
		$t = substr($row[3], 1, strlen($row[3]) - 2);
		$prefixes = explode('|', $t);
		$country_to_prefix[$row[1]] = $prefixes;
		foreach($prefixes as $prefix)
		{
			$code_to_country[$prefix] = $row[1];
		}
	}
	else
	{
		$country_to_prefix[$row[1]] = $row[3];
		$prefix_to_country[$row[3]] = array($row[1]);
	}
}
fclose($f);

file_put_contents('country_to_prefix.serialized', serialize($country_to_prefix));
file_put_contents('prefix_to_country.serialized', serialize($prefix_to_country));


