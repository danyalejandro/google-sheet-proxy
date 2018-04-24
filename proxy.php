<?php
// If needed, change this rule to something more strict
header("Access-Control-Allow-Origin: *");

// Grab the GET parameters
$key = (isset($_GET["key"])) ? $_GET["key"] : "";
$sheet = (isset($_GET["sheet"])) ? $_GET["sheet"] : "";

$keyLength = strlen($key);
$sheetLength = strlen($sheet);

if ($keyLength < 40 || $keyLength > 50) die("Invalid key or sheet.");
if ($sheetLength < 1 || $sheetLength > 50) die("Invalid key or sheet.");

// Sanitize the parameters; add any extra security here
$key = trim(filter_var($key, FILTER_SANITIZE_EMAIL));
$sheet = trim(filter_var($sheet, FILTER_SANITIZE_EMAIL));

// CSV Google spreadsheet url string (might change in the future depending on Google)
$url = "https://docs.google.com/spreadsheets/d/$key/gviz/tq?tqx=out:csv&sheet=$sheet";

try {
	// get csv contents from the google spreadsheet URL
	$csv = file_get_contents($url);
	if (!$csv) die("Unable to retrieve CSV data.");
	
	$csv = str_replace(',""', '', $csv);
	$csv = str_replace('," "', '', $csv);
	$csv = trim($csv);
	
	$lines = explode("\n", $csv);
	$n = count($lines);
	for ($i = 0 ; $i < $n ; $i++) {
		$lines[$i] = trim($lines[$i]);
	}

	$indexes = str_getcsv(array_shift($lines));
	$array = array_map(function ($e) use ($indexes) { return array_combine($indexes, str_getcsv($e)); }, $lines);
	$json = json_encode($array);
	print_r($json);
}
catch (Exception $e) {
    die("Unable to obtain the data.");
}