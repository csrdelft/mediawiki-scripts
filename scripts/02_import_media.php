<?php

$mediawikidir = $argv[1];
$currentdir = dirname(__FILE__);

passthru("php $mediawikidir\maintenance\importImages.php --search-recursively $currentdir/../temp/media/");
