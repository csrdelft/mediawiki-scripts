<?php

$tempDir = dirname(__FILE__) . '/../temp/pages/';

if (!file_exists($tempDir)) {
    mkdir($tempDir);
}

$pagesDir = $argv[1];
$filename = $argv[2];
$inputfile = file_get_contents($filename);

$data = unserialize($inputfile);

$createdOn = date(DATE_ISO8601, $data['current']['date']['created']);

echo "Gemaakt op: " . $createdOn . PHP_EOL;
echo "Gemaakt door: " . $data['current']['creator'] . PHP_EOL;
