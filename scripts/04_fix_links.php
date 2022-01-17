<?php
/**
 * Fix red links.
 * 
 * Go through all pages and look for:
 * 
 * - Wrong case
 * - Relative link
 * - Wrong case in relative link
 * - Relative parent link
 * - Wrong case in relative parent link
 */

$tempDir = realpath(dirname(__FILE__) . '/../temp/pages'). '/*';
$outDir = dirname(__FILE__) . '/../temp/fixed_pages/';
echo $outDir . "\n";
@mkdir($outDir);

$filenames = [];
$lowerFilenames = [];

function formatFileName($filename) {
    $formattedFileName = basename($filename, '.mod');
    $formattedFileName = implode(":", array_map('ucfirst', explode(";", $formattedFileName)));
    $formattedFileName = implode("/", array_map('ucfirst', explode("__", $formattedFileName)));
    $formattedFileName = str_replace("_", " ", $formattedFileName);

    return $formattedFileName;
}

/**
 * Ns/SubNs/File to Ns/SubNs
 */
function getNameSpace($formattedFileName) {
    $namespace = explode("/", $formattedFileName);
    $namespace = array_splice($namespace, 0, -1);
    $namespace = implode("/", $namespace);

    return $namespace;
}

foreach (glob($tempDir) as $file) {
    $filenames[] = formatFileName($file);
    $lowerFilenames[] = strtolower(formatFileName($file));
}

$redlinks = 0;

$state = (object)[
    'redlinks' => 0,
    'relativefix' => 0,
    'casefix' => 0,
    'nsfix' => 0,
    'nscasefix' => 0,
    'relativecasefix' => 0,
    'parentfix' => 0,
    'parentcasefix' => 0,
];

function processLine($line, $file, $filenames, $lowerFilenames) {
    return preg_replace_callback('/\[\[([^]]+)]]/', function ($matches) use ($line, $file, $filenames, $lowerFilenames) {
        global $state;
        $original = $matches[0];
        $linkContent = $matches[1];

        $parts = explode("|", $linkContent, 2);
        $link = trim($parts[0], " ./\n\r\t\v\0");

        $hash = '';
        $linkParts = explode("#", $link);

        if (isset($linkParts[1])) {
            $link = $linkParts[0];
            $hash = '#' . $linkParts[1];
        }

        $text = isset($parts[1]) ? ('|' . $parts[1]) : '';

        // Skip anchor links
        if ($link === '') return $original;
        // Skip categorie
        if (strpos($link, 'Categorie:') === 0) return $original;
        // Skip bestand
        if (strpos($link, 'Bestand:') === 0) return $original;
        // Skip document
        if (strpos($link, 'Document>') === 0) return $original;
        // Skip boek
        if (strpos($link, 'Boek>') === 0) return $original;
        // Skip groep
        if (strpos($link, 'Groep>') === 0) return $original;
        // Skip lid
        if (strpos($link, 'Lid>') === 0) return $original;
        // Skip doku links
        if (strpos($link, 'Doku>') === 0) return $original;
        // Skip correct links
        if (in_array($link, $filenames)) return $original;

        // Test errors in casing
        if (in_array(strtolower($link), $lowerFilenames)) {
            $correct = $filenames[array_search(strtolower($link), $lowerFilenames)];

            $state->casefix++;
            return "[[$correct$hash$text]]";
        }

        $formattedFileName = formatFileName($file);

        // Test namespace link
        $nsLink = $formattedFileName . '/' . $link;
        if (in_array($nsLink, $filenames)) {
            $state->nsfix++;
            return "[[$nsLink$hash$text]]";
        }

        // Test errors in casing in namespace link
        if (in_array(strtolower($nsLink), $lowerFilenames)) {
            $state->nscasefix++;

            $correct = $filenames[array_search(strtolower($nsLink), $lowerFilenames)];

            return "[[$correct$hash$text]]";
        }

        // Test relative path
        $relativeLink = getNameSpace($formattedFileName) . '/' . $link;

        if (in_array($relativeLink, $filenames)) {
            $state->relativefix++;
            return "[[$relativeLink$hash$text]]";
        }

        // Test errors in casing in relative path
        if (in_array(strtolower($relativeLink), $lowerFilenames)) {
            $state->relativecasefix++;

            $correct = $filenames[array_search(strtolower($relativeLink), $lowerFilenames)];

            return "[[$correct$hash$text]]";
        }

        $parentRelativeLink = getNameSpace(getNameSpace($formattedFileName)) . '/' . $link;

        if (in_array($parentRelativeLink, $filenames)) {
            $state->parentfix++;
            return "[[$parentRelativeLink$hash$text]]";
        }
        if (in_array(strtolower($parentRelativeLink), $lowerFilenames)) {
            $state->parentcasefix++;

            $correct = $filenames[array_search(strtolower($parentRelativeLink), $lowerFilenames)];
            return "[[$correct$hash$text]]";
        }
        $parentRelativeLink = getNameSpace(getNameSpace(getNameSpace($formattedFileName))) . '/' . $link;

        if (in_array($parentRelativeLink, $filenames)) {
            $state->parentfix++;
            return "[[$parentRelativeLink$hash$text]]";
        }
        if (in_array(strtolower($parentRelativeLink), $lowerFilenames)) {
            $state->parentcasefix++;

            $correct = $filenames[array_search(strtolower($parentRelativeLink), $lowerFilenames)];
            return "[[$correct$hash$text]]";
        }
        $parentRelativeLink = getNameSpace(getNameSpace(getNameSpace(getNameSpace($formattedFileName)))) . '/' . $link;

        if (in_array($parentRelativeLink, $filenames)) {
            $state->parentfix++;
            return "[[$parentRelativeLink$hash$text]]";
        }
        if (in_array(strtolower($parentRelativeLink), $lowerFilenames)) {
            $state->parentcasefix++;

            $correct = $filenames[array_search(strtolower($parentRelativeLink), $lowerFilenames)];
            return "[[$correct$hash$text]]";
        }

        $state->redlinks++;
        echo "Red link: " . $link.$hash. " in file $file" . PHP_EOL;

        // echo "Link " . $link . "|" . $text . PHP_EOL;
    }, $line);
}

foreach (glob($tempDir) as $file) {
    $contents = explode("\n", file_get_contents($file));

    $output = [];

    foreach ($contents as $line) {
        $output[] = processLine($line, $file, $filenames, $lowerFilenames);
    }

    $outputString = implode("\n", $output);

    file_put_contents($outDir . basename($file), $outputString);
}

print_r($state);
