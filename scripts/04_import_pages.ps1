param ($dokuwikidir, $mediawikidir)

foreach ($file in get-childitem ../sjablonen) {
    php $mediawikidir\maintenance\importTextFiles.php -p "Sjabloon:" $file
}

foreach ($file in Get-ChildItem ../categorien) {
    php $mediawikidir\maintenance\importTextFiles.php -p "Categorie:" $file
}

# Importeer mediawiki bestanden in mediawiki
foreach ($file in get-childitem ../temp/pages/*.mod) {
  php $mediawikidir\maintenance\importTextFiles.php $file
}
