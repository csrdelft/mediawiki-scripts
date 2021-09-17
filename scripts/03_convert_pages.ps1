param ($dokuwikidir, $mediawikidir)

mkdir -Force ../temp/pages

# Converteer dokuwiki pagina's naar mediawiki
foreach ($file in Get-ChildItem -Recurse -File $dokuwikidir/data/pages/*.txt) {
  php ./dokuwiki2mediawiki.php $dokuwikidir/data/pages $file
}
