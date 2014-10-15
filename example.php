<?php

require_once './Hobis_Api_Config_Parser.php';

$parser = new Hobis_Api_Config_Parser;

$parser->setConfigUri('./config.txt');

echo sprintf('host: %s%s', $parser->getSetting('host'), PHP_EOL);

echo sprintf('verbose: %b%s', $parser->getSetting('verbose'), PHP_EOL);

echo sprintf('debug_mode: %b%s', $parser->getSetting('debug_mode'), PHP_EOL);

echo sprintf('server_load_alarm: %.1f%s', $parser->getSetting('server_load_alarm'), PHP_EOL);

echo sprintf('%s', PHP_EOL);

foreach ($parser->getSettings() as $key => $value) {
	echo sprintf('Key: %s | Value: %s %s', $key, $value, PHP_EOL);
}