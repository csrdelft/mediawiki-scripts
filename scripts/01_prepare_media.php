<?php

/**
 * Copy all files from a dokuwiki installation to a single directory.
 */

function rsearch($folder, $pattern) {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::MATCH);

    foreach($ite as $file) {
        if ($file->isFile()) yield $file->getPathName();
    }
}

$dokuwikidir = $argv[1];

$newMediaDir = dirname(__FILE__) . '/../temp/media';

@mkdir($newMediaDir);

$dokuwikiMediaDir = realpath($dokuwikidir . '/data/media');

foreach(rsearch($dokuwikidir . '/data/media', '/.*/') as $file) {
    $realfile = realpath($file);

    if (strpos($realfile, $dokuwikiMediaDir) === 0) {
        $newfile = substr($realfile, strlen($dokuwikiMediaDir) + 1);

        $newfile = str_replace('/', '_', $newfile);
        $newfile = str_replace('\\', '_', $newfile);

        copy($file, $newMediaDir . '/' . $newfile);
    }
}
