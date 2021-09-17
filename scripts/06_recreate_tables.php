<?php
$mediawikidir = $argv[1];

passthru("php $mediawikidir/extensions/Cargo/maintenance/cargoRecreateData.php --table motie");
passthru("php $mediawikidir/extensions/Cargo/maintenance/cargoRecreateData.php --table huishoudelijke_vergadering");
passthru("php $mediawikidir/extensions/Cargo/maintenance/cargoRecreateData.php --table vormingsbank");
passthru("php $mediawikidir/extensions/Cargo/maintenance/cargoRecreateData.php --table recept");