param ($dokuwikidir, $mediawikidir)

Write-Output "Importing files into mediawiki"

# Importeer alle media in mediawiki
php $mediawikidir\maintenance\importImages.php --search-recursively ../temp/media/
