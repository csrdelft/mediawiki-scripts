<?php

$dokuwikidir = $argv[1];
$mediawikidir = $argv[2];

passthru("php 01_prepare_media.php $dokuwikidir");
passthru("php 02_import_media.php $mediawikidir");
passthru("php 03_convert_pages.php $dokuwikidir");
passthru("php 04_fix_links.php");
passthru("php 05_import_pages.php $mediawikidir");
passthru("php 06_recreate_tables.php $mediawikidir");
