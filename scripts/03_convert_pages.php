<?php

function rsearch($folder, $pattern) {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::MATCH);

    foreach($files as $file) {
        if ($file->isFile()) yield $file->getPathName();
    }
}

$dokuwikidir = $argv[1];

$pagesdir = dirname(__FILE__) . '/../temp/pages';

@mkdir($pagesdir);

foreach (rsearch($dokuwikidir . '/data/pages', '/.*\.txt$/') as $file) {
    passthru("php ./dokuwiki2mediawiki.php $dokuwikidir/data/pages \"$file\"");
}
