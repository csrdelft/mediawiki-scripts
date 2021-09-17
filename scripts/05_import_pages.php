<?php

$mediawikidir = $argv[1];
$rootdir = dirname(__FILE__, 2);

foreach (glob("$rootdir/sjablonen/*") as $file) {
    passthru("php $mediawikidir\maintenance\importTextFiles.php --overwrite -p Sjabloon: \"$file\"");
}

foreach (glob("$rootdir/categorien/*") as $file) {
    passthru("php $mediawikidir\maintenance\importTextFiles.php --overwrite -p Categorie: \"$file\"");
}

foreach (glob("$rootdir/temp/pages/*.mod") as $file) {
    passthru("php $mediawikidir\maintenance\importTextFiles.php --overwrite \"$file\"");
}
