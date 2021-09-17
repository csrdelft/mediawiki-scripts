param ($dokuwikidir, $mediawikidir)

$currentDir = pwd

New-Item -ItemType "directory" -Force ($pwd.Path + "/../temp/media")

Write-Output "Copying files from media"

Push-Location $dokuwikidir/data/media

foreach ($file in get-childitem -recurse -file $dokuwikidir/data/media) {
  $path = $file.Directory | Resolve-Path -Relative
  $filePrefix = $path.TrimStart('.', '\').Replace('\', '_')
  $filename = $filePrefix + "_" + $file.Name
  $destPath = $currentDir.Path + "/../temp/media/" + $filename
  Write-Output $filename
  cp $file $destPath
}

Pop-Location
