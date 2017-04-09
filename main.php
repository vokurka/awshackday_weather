<?php

require "vendor/autoload.php";
use Keboola\Json\Parser;

define('OUT_PATH', '/data/out/tables/');

$configFile = getenv('KBC_DATADIR') . DIRECTORY_SEPARATOR . 'config.json';
$config = json_decode(file_get_contents($configFile), true);

$api = new RestClient(array(
    'base_url' => "http://www.weather.gov.sg/wp-content/themes/wiptheme/page-functions/",
));

$responses = array();

foreach (array('tabType=Night', 'tabType=Afternoon', 'tabType=Morning') as $param)
{
	$result = $api->post(
		"functions-forecast24hr-ajax.php",
		$param
	);

	$response = json_decode($result->response);

	if ($response === NULL)
	{
		echo "Bad JSON.\n";
		exit;
	}

	$responses[] = $response;
}

// Create parser and parse the json
$parser = Parser::create(new \Monolog\Logger('json-parser'));
$parser->process($responses, 'weather');
$result = $parser->getCsvFiles();

foreach ($result as $f)
{
	copy($f->getPathName(), OUT_PATH.$config['parameters']['output_bucket'].".". substr($f->getFileName(), strpos($f->getFileName(), '-')+1));
}