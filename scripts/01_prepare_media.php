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

$skipNs = ['speeltuin', 'wiki'];

foreach(rsearch($dokuwikidir . '/data/media', '/.*/') as $file) {
    $realfile = realpath($file);

    if (strpos($realfile, $dokuwikiMediaDir) === 0) {
        $newfile = substr($realfile, strlen($dokuwikiMediaDir) + 1);

        $newfile = str_replace('/', '_', $newfile);
        $newfile = str_replace('\\', '_', $newfile);


        $skip = false;
        foreach ($skipNs as $skipping) {
            if (strpos($newfile, $skipping) === 0) {
                $skip = true;
            }
        }

        if (!$skip) copy($file, $newMediaDir . '/' . $newfile);
    }
}
