<?php

// foreach ($file in Get-ChildItem -Recurse -File ./data/pages/*.txt) {
// php .\lib\plugins\mediasyntax\tools\dokuwiki2mediawiki.php $file
// }
// dokuwiki2mediawiki.php (c) 2010-2015 by Thorsten Staerk
// This program reads files containing dokuwiki syntax and converts them into files
// containing mediawiki syntax.
// The source file is given by parameter, the target file is the source file plus a
// ".mod" suffix.

// TODO:
// filename: not only one file name!
// test if we overwrite a file
// test if file exists
// allow rowspan (::: in dokuwiki syntax)

$in_table = false;
$in_dataentry = null;
$in_imagemap = false;

$skipNS = [
    'bestuur', 'speeltuin', 'wiki',
];

$skipPage = ['sidebar'];

$dataEntryMap = [
    'Hv' => 'Huishoudelijke Vergadering',
    'Motie' => 'Motie',
    'Vb' => 'Vormingsbank',
    'Recepten' => 'Recept',
    'Kerkje' => 'Kerk',
    'Keuzevak' => 'Keuzevak',
    'Organisatie' => 'Organisatie',
    'Kluslijst' => 'Kluslijst',
];

$dataEntryKeyMap = [
    'Hv' => [
        'Nummer_hvpage' => 'Nummer',
        'Nummer_titleCreated' => 'Nummer',
        'Nummer_title' => 'Nummer',
        'Datum_dt' => 'Datum',
        'Bestuur_groep' => 'Bestuur',
        'Bestuur_groeps' => 'Bestuur',
        'Notulen_docs' => 'Notulen',
        'Groepinstallaties_groeps' => 'InstallatieGroepen',
        'Groepinstallaties_groep' => 'InstallatieGroepen',
        'Groepdecharges_groeps' => 'DechargeGroepen',
        'Groepdecharges_groep' => 'DechargeGroepen',
    ],
    'Motie' => [
        'HVnummer_hvpage' => 'HV Nummer',
        'Hvnummer_hvpage' => 'HV Nummer',
        'HVnummer_motiepage' => 'Motiepagina',
        'Motienummer' => 'Nummer',
        'Datum_dt' => 'Datum',
        'Einddatum' => 'Einddatum',
        'Besluit_motietag' => 'Besluit',
        'Geldigheid_motietag' => 'Geldigheid',
        'Labels_motietag' => 'Labels',
        'Labels_motietags' => 'Labels',
        'Relevantie_motietag' => 'Relevantie',
        'Ondertekend door_lids' => 'Ondertekend door',
        'Ondertekend door_lid' => 'Ondertekend door',
    ],
    'Vb' => [
        'Titel' => 'Titel',
        'Titels' => 'Titel',
        'Titel_title' => 'Titel',
        'Titel_wiki' => 'Titel',
        'Door_vbtags' => 'Door',
        'Door' => 'Door',
        'Doors' => 'Door',
        'Door_tags' => 'Door',
        'Collegejaar_vbtag' => 'Collegejaar',
        'Collegejaar_vbtags' => 'Collegejaar',
        'Collegejaar_tag' => 'Collegejaar',
        'Collegejaar' => 'Collegejaar',
        'Rubriek_vbtag' => 'Rubriek',
        '-Rubriek_vbtag' => 'Rubriek',
        '-Rubriek_vbtags' => 'Rubriek',
        'Rubriek_tags' => 'Rubriek',
        'Activiteit_vbtag' => 'Activiteit',
        'Activiteit_vbtags' => 'Activiteit',
        'Activiteit_tag' => 'Activiteit',
        'Activiteit' => 'Activiteit',
        'Datum' => 'Datum',
        'Datum_dt' => 'Datum',
        'Datum_dts' => 'Datum',
        'Rubriek_vbtags' => 'Rubriek',
        'Rubriek' => 'Rubriek',
        '#Webstek_webstek' => 'Webstek',
        'Webstek_webstek' => 'Webstek',
        'Webstek_groeps' => 'Webstek',
        'Webstek_websteks' => 'Webstek',
        'Vergelijkbaar_sames' => 'Vergelijkbaar',
        'Vergelijkbaar_same' => 'Vergelijkbaar',
        '#Vergelijkbaar_sames' => 'Vergelijkbaar',
        'Semester' => 'Semester',
    ],
    'Recepten' => [
        'Naam' => 'Naam',
        'Typering_tags' => 'Typering',
        'Gang_tags' => 'Gang',
        'Omschrijving_tags' => 'Omschrijving',
        'Aantal personen' => 'Aantal personen',
        'Personen' => 'Aantal personen',
    ],
    'Kerkje' => [
        'Kerknaam' => 'Kerknaam',
        'Denominatie_tags' => 'Denominatie',
        'Denominatie_tag' => 'Denominatie',
        'Website_url' => 'Website',
        'Grootte' => 'Grootte',
        'Kerkverband' => 'Kerkverband',
        'Voorganger' => 'Voorganger',
        'Voorgangers' => 'Voorganger',
        'Contactpersoon' => 'Contactpersoon',
        'Contactpersoon_tag' => 'Contactpersoon',
        'Aanvangstijd viering zondag_tags' => 'Aanvangstijd viering zondag',
        'Aanvangstijd viering zondag_tag' => 'Aanvangstijd viering zondag',
    ],
    'Keuzevak' => [
        'Vaknaam' => 'Vaknaam',
        'Code' => 'Code',
        'Link_url' => 'Link',
        'Link_wiki' => 'Link',
        'Ects' => 'Ects',
        'Moeilijk' => 'Moeilijk',
        'Vakgebied_tags' => 'Vakgebied',
        'Boekkosten' => 'Boekkosten',
        'Start' => 'Start',
    ],
    'Organisatie' => [
        'Organisatie' => 'Organisatie',
        'Doelgroep_tags' => 'Doelgroep',
        'Adres' => 'Adres',
        'Adres_' => 'Adres',
        'Postcode' => 'Postcode',
        'Plaats' => 'Plaats',
        'Plaats_' => 'Plaats',
        'Telefoon' => 'Telefoon',
        'Email_email' => 'Email',
        'Email_mail' => 'Email',
        'Website_url' => 'Website',
    ],
    'Kluslijst' => [
        'Frequentie' => 'Frequentie',
        'Door' => 'Door',
        'Laatst gedaan_dt' => 'Laatst gedaan',
        'Laatste keer gedaan' => 'Laatst gedaan',
        'Laatste keer gedaan_dt' => 'Laatst gedaan',
        'Planning' => 'Planning',
        'Planning_dt' => 'Planning',
        'Opmerking' => 'Opmerking',
    ],
];


function endsWith($haystack, $needle) {
    $length = strlen($needle);
    return $length > 0 ? substr($haystack, -$length) === $needle : true;
}

// |Nummer_hvpage=277
// |Datum_dt=2011-05-09 #JJJJ-MM-DD
// |Bestuur_groep=776
// |Notulen_docs=531, 520,  #nummers van documenten op csrdelft.nl
// |groepinstallaties_groeps=960, 933, 1027, 954, 46, 1017,
// |groepdecharges_groeps=1000, 912, 797, 750, 704,

function formatUrn($content, $prefix) {
    $content = preg_replace('/^\.\.?/', '', $content);

	if (preg_match('/\|/', $content)) {
		$parts = explode("|", $content);

		$urn = ucfirst(implode("/", array_map('ucfirst', explode(':', $parts[0]))));
        $urn = str_replace("_", " ", $urn);
        $urn = str_replace("cie", "Cie", $urn);
        $urn = str_replace("viCie", "ViCie", $urn);
		$urn = str_replace('/Hoofdpagina', '', $urn);
		$name = $parts[1];
		// Voorkom drama met tabellen door een placeholder te gebruiken voor |
		return $urn . "'linkpipe'" . $name;
	}

	$urn = ucfirst(implode("/", array_map('ucfirst', explode(':', $content))));
    $urn = str_replace("_", " ", $urn);
    $urn = str_replace("cie", "Cie", $urn);
    $urn = str_replace("viCie", "ViCie", $urn);
	$urn = str_replace('/Hoofdpagina', '', $urn);
	return $urn;
}

if ($argc == 1) {
	echo "dokuwiki2mediawiki.php (c) 2010-2012 by Thorsten Staerk\n";
	echo "This program converts dokuwiki syntax to mediawiki syntax.\n";
	echo "The source file is given as an argument, the target file is the same plus the suffix \".mod\"\n";
	echo "Usage: php dokuwiki2mediawiki <basedir> <file>\n";
	echo "Example: php dokuwiki2mediawiki wiki/data/pages start.txt\n";
} else {
    $tempDir = dirname(__FILE__) . '/../temp/pages/';

	if (!file_exists($tempDir)) {
		mkdir($tempDir);
	}

	$cells = [];
	$headers = '';

    $pagesDir = $argv[1];
    $filename = $argv[2];
    $inputfile = fopen($filename, "r");
    $i = 0;
    $output = "";
    if ($inputfile) {
        while (!feof($inputfile)) {
            $lines[$i++] = fgets($inputfile); //we start counting a 0
        }
        // Add empty line at the end to handle tables at the very end of the page.
        $lines[$i++] = "";
        fclose($inputfile);
    }
    $linecount = $i;
    $i = -1;

    $outputFilePath = substr(realpath($filename), strlen($pagesDir));
    $fileParts = str_replace('.txt', '', $outputFilePath);
    $fileParts = str_replace('/', '__', $fileParts);
    $fileParts = str_replace('\\', '__', $fileParts);
    $fileParts = str_replace('__hoofdpagina', '', $fileParts);
    $fileParts = str_replace('cie', 'Cie', $fileParts);
    $fileParts = str_replace('viCie', 'ViCie', $fileParts);

    $prefix = explode("__", $fileParts);
    $prefix = array_splice($prefix, 0, -1);
    $prefix = implode(":", $prefix) . ':';

    foreach ($skipNS as $skip) {
        if (strpos($fileParts, $skip) === 0) {
            echo "Skip $fileParts\n";
            exit;
        }
    }

    foreach ($skipPage as $skip) {
        if (endsWith($fileParts, $skip)) {
            echo "Skip $fileParts\n";
            exit;
        }
    }

    while (++$i < $linecount) {
        if ($in_table) $row++;
        $line = $lines[$i];

        if ($in_dataentry) {
            if (preg_match('/----/', $line)) {
                $in_dataentry = null;
                $line = "}}";
                $output .= "}}\n";
                continue;
            }

            // Comments inside dataentry
            $line = preg_replace('/\#(.+)$/', '<!-- $1 -->', $line);

            if (strpos($line, '<!--') === 0) continue;

            $parts = explode(":", $line, 2);
            $key = ucfirst(trim($parts[0]));

            if (!$key) continue;

            if (!isset($dataEntryKeyMap[$in_dataentry][$key])) {
                echo "Bestaat niet: $in_dataentry -> $key\n";
                continue;
            } else {
                $key = $dataEntryKeyMap[$in_dataentry][$key];
            }

            $val = trim($parts[1]);

            if ($key && $val) $output .= "|$key=$val\n";

            continue;
        }

        if ($in_imagemap) {
            if (preg_match('/\{\{\<map}}/', $line)) {
                $in_imagemap = false;
                $output .= "</imagemap>\n";
                continue;
            }

            // Skip empty lines
            if (trim($line) == "") continue;

            if (preg_match('/\s*\*\s*\[\[([^|]+)\|([^@]+)@([^]]+)]]/', $line, $matches)) {
                $link = formatUrn(trim($matches[1]), $prefix);
                $title = trim($matches[2]);
                $coords = trim($matches[3]);

                $type = "poly";
                if (count(explode(",", $coords)) == 4) {
                    $type = "rect";
                }

                $coords = str_replace(",", " ", $coords);
                $output .= "$type $coords [[$link|$title]]\n";
            }

            // Skip lines that don't match
            continue;
        }

        // replace headings
        // if (preg_match('/^ *======.*====== *$/', $line)) {
        //     $line = preg_replace('/^ *======/', '=', $line);
        //     $line = preg_replace('/====== *$/', '=', $line);
        // } elseif (preg_match('/^ *=====.*===== *$/', $line)) {
        //     $line = preg_replace('/^ *=====/', '==', $line);
        //     $line = preg_replace('/===== *$/', '==', $line);
        // } elseif (preg_match('/^ *====.*==== *$/', $line)) {
        //     $line = preg_replace('/^ *====/', '===', $line);
        //     $line = preg_replace('/==== *$/', '===', $line);
        // } elseif (preg_match('/^ *===.*=== *$/', $line)) {
        //     $line = preg_replace('/^ *===/', '====', $line);
        //     $line = preg_replace('/=== *$/', '====', $line);
        // } elseif (preg_match('/^ *==.*== *$/', $line)) {
        //     $line = preg_replace('/^ *==/', '=====', $line);
        //     $line = preg_replace('/== *$/', '=====', $line);
        // }
        // Move every heading one up.
        if (preg_match('/^ *======.*====== *$/', $line)) {
            $line = preg_replace('/^ *======/', '=', $line);
            $line = preg_replace('/====== *$/', '=', $line);
        } elseif (preg_match('/^ *=====.*===== *$/', $line)) {
            $line = preg_replace('/^ *=====/', '=', $line);
            $line = preg_replace('/===== *$/', '=', $line);
        } elseif (preg_match('/^ *====.*==== *$/', $line)) {
            $line = preg_replace('/^ *====/', '==', $line);
            $line = preg_replace('/==== *$/', '==', $line);
        } elseif (preg_match('/^ *===.*=== *$/', $line)) {
            $line = preg_replace('/^ *===/', '===', $line);
            $line = preg_replace('/=== *$/', '===', $line);
        } elseif (preg_match('/^ *==.*== *$/', $line)) {
            $line = preg_replace('/^ *==/', '====', $line);
            $line = preg_replace('/== *$/', '====', $line);
        }
        // end of replace headings

        // replace bulletpoints
        $level = 0; // level of bulletpoints, e.g. * is level 1, *** is level 3.
        while (preg_match('/^(  )+\*/', $line)) {
            $line = preg_replace("/^  /", "", $line);
            $level++;
        }
        while ($level > 1) {
            $line = "*" . $line;
            $level--;
        }
        // end of replace bulletpoints

        // replace ordered list items
        $level = 0; // level of list items, e.g. - is level 1, --- is level 3.
        while (preg_match('/^( {2})+\-/', $line)) {
            $line = preg_replace("/^ {2}/", "", $line);
            $level++;
            $line = preg_replace("/^-/", "#", $line);
        }

        while ($level > 1) {
            $line = "#" . $line;
            $level--;
        }
        // end of replace ordered list items

        // Rewrite external links
        if (preg_match('/\[\[http/', $line)) {
            $line = preg_replace_callback('/\[\[http([^]|]+)(\|[^]]*)?]]/', function ($match) {
                $url = $match[1];
                $text = '';
                if (isset($match[2])) {
                    $text = str_replace('|', "'linkpipe'", $match[2]);
                }
                return "[http$url$text]";
            }, $line);
        }
        // end rewrite external links

        // rewrite links
        // [[cie:diensten:hosting|test]] wordt [[Cie/Diensten/Hosting|test]]
        // [[cie:diensten:hoofdpagina]] wordt [[Cie/Diensten]]
        if (preg_match('/\[\[(.+?)]]/', $line, $matches)) {
            $line = preg_replace_callback('/\[\[(.+?)]]/', function ($match) use ($prefix) {
                $content = $match[1];
                if (preg_match('/\|/', $content)) {
                    $parts = explode("|", $content);

                    $urn = formatUrn($parts[0], $prefix);
                    $name = $parts[1];
                    // Voorkom drama met tabellen door een placeholder te gebruiken voor |
                    return "[[{$urn}'linkpipe'{$name}]]";
                }

                $urn = formatUrn($content, $prefix);
                return "[[$urn]]";
            }, $line);
        }
        // end rewrite links

        if (preg_match('/{{map>([^|]+)\|([^}]+)}}/', $line, $matches)) {
            $in_imagemap = true;

            $image = $matches[1];
            $title = $matches[2];

            $image = trim(str_replace(':', ' ', $image));
            $image = ucfirst($image);

            $output .= "<imagemap>\n";
            $output .= "Afbeelding:$image|omkaderd|gecentreerd|$title\n";
            continue;
        }

        // Rewrite videos
        if (preg_match("/{{(youtube|vimeo)\>.+}}/", $line)) {
            $line = preg_replace_callback("/{{(youtube|vimeo)\>([^}]+)}}/", function ($match) {
                $service = $match[1];
                $id = $match[2];
                return "<embedvideo service=\"$service\">$id</embedvideo>";

            }, $line);
        }
        // end rewrite videos

        // Rewrite external links2
        if (preg_match("/{{http(.+)}}/", $line)) {
            $line = preg_replace_callback("/{{http(.+)}}/", function ($match) {
                $link = $match[1];
                return "[http$link]";
            }, $line);
        }
        // Rewrite images
        // {{ ns:subns:file.jpg | Alt}}
        // [[Bestand:ns subns file.jpg |Alt|miniatuur]]
        if (preg_match("/{{(.+)}}/", $line)) {
            $line = preg_replace_callback("/{{([^?|}]+)\??([^|}]+)?\|?([^|}]*)?}}/", function ($match) {
                $file = $match[1];
                $file = trim(str_replace(':', ' ', $file));
                $file = ucfirst($file);
                $desc = "";
                if (isset($match[3])) {
                    $desc = $match[3];
                }
                return "[[Bestand:$file'linkpipe'$desc'linkpipe'miniatuur]]";
            }, $line);
        }
        // end rewrite images

        // Rewrite nav
        //{{Nav
        //| title = [[Lichtingen]]
        //| previous = [[Lichtingen/2018]]
        //| next = [[Lichtingen/2020]]
        //}}
        //
        //
        // <- lichtingen:2018:hoofdpagina ^ lichtingen:hoofdpagina ^ lichtingen:2020:hoofdpagina ->
        if (preg_match("/<-([^^]*)\^([^^]*)\^([^-]*)->/", $line)) {
            $line = preg_replace_callback("/<-([^^]*)\^([^^]*)\^([^-]*)->/", function ($match) use ($prefix) {
                $prev = formatUrn(trim($match[1]), $prefix);
                $title = formatUrn(trim($match[2]), $prefix);
                $next = formatUrn(trim($match[3]), $prefix);

                $prev = empty($prev) ? "" : "[[$prev|]]";
                $title = empty($title) ? "" : "[[$title|]]";
                $next = empty($next) ? "" : "[[$next|]]";

                return "{{Nav | title = $title | previous = $prev | next = $next}}";
            }, $line);
        }
        // end rewrite nav

        // replace **
        $line = preg_replace("/\*\*/", "'''", $line);
        // end of replace **

        // replace \\
        // thanks to Rakete Kalle
        $line = preg_replace("/\\\\\\\\/", "<br />", $line);
        // end of replace \\

        // replace //
        $line = preg_replace("/([^:])\/\//", "$1''", $line);
        $line = preg_replace("/^\/\//", "$1''", $line);
        // end of replace //

        // begin dataentry
        // ---- dataentry hv ----
        // Nummer_hvpage         : 281
        // Datum_dt             : 2011-10-27 #JJJJ-MM-DD
        // Bestuur_groep          : 956
        // Notulen_docs              : 643, 644,  #nummers van documenten op csrdelft.nl
        // groepinstallaties_groeps    : 1140, 961, 1177, 1162, 1183, 
        // groepdecharges_groeps       : 960, 907, 
        // ----
        if (preg_match("/---- dataentry (.+)----/", $line, $matches)) {
            
            $dataentryType = ucfirst(trim($matches[1]));
            $in_dataentry = $dataentryType;
            if (!isset($dataEntryMap[$dataentryType])) {
                echo "Bestaat niet : dataentry " . $dataentryType;
                $output .= '{{' . $dataentryType . "\n";
            } else {
                $output .= '{{' . $dataEntryMap[$dataentryType] . "\n";
            }
            continue;
        }

        // begin dataentry
        // ---- dataentry hv ----
        // Nummer_hvpage         : 281
        // Datum_dt             : 2011-10-27 #JJJJ-MM-DD
        // Bestuur_groep          : 956
        // Notulen_docs              : 643, 644,  #nummers van documenten op csrdelft.nl
        // groepinstallaties_groeps    : 1140, 961, 1177, 1162, 1183, 
        // groepdecharges_groeps       : 960, 907, 
        // ----
        if (preg_match("/---- datalist (.+) ----/", $line, $matches)) {
            $in_datalist = true;
            
            $dataentryType = $matches[1];
            $output .= '{{' . "$dataentryType lijst legacy\n";
            continue;
        }

        // begin care for tables
        if (preg_match("/^\|/", $line)) {
            $line = preg_replace("/\| *$/", "", $line);
            $line = preg_replace("/\n/", "", $line);
            if (!$in_table) {
                $in_table = true;
                $row = 1;
            }
            $cells[$row] = explode("|", preg_replace("/^\|/", "", $line));
        }

        // have we left a table?
        if ((!preg_match("/^\|/", $line)) && $in_table) {
            $in_table = false;
            $rowspancells = $cells;

            // each cell's rowspan value is 1
            for ($y = 1; $y < count($cells) + 1; $y = $y + 1)
                for ($x = 0; $x < count($cells[$y]); $x = $x + 1)
                    $rowspancells[$y][$x] = 1;

            // every cell that needs an attribute rowspan=x gets x as its rowspan value
            for ($y = 1; $y < count($cells); $y = $y + 1) {
                for ($x = 0; $x < count($cells[$y]); $x = $x + 1) {
                    $z = 1;
                    while (isset($cells[$y + $z][$x]) && (preg_match("/ *::: */", $cells[$y + $z][$x]))) {
                        $rowspancells[$y][$x] += 1;
                        $z += 1;
                    }
                }
            }

            // if the cell itself if :::, then its rowspan value is 0
            for ($y = 1; $y < count($cells) + 1; $y = $y + 1)
                for ($x = 0; $x < count($cells[$y]); $x = $x + 1)
                    if (preg_match("/ *::: */", $cells[$y][$x])) $rowspancells[$y][$x] = 0;

            // begin display the mediawiki table
            $tablesource = "{| class=\"wikitable sortable\" border=1\n";
            if ($headers != "") {
                $tablesource .= "!";
                for ($n = 0; $n < count($headers); $n = $n + 1) {
                    $tablesource .= $headers[$n];
                    if ($n < count($headers) - 1) $tablesource .= "!!";
                }
                $tablesource .= "\n|-\n";
            }
            for ($y = 1; $y < count($cells) + 1; $y = $y + 1) {
                $tablesource .= "| ";
                for ($x = 0; $x < count($cells[$y]); $x = $x + 1) {
                    if ($rowspancells[$y][$x] >= 1) {
                        if ($rowspancells[$y][$x] > 1) $tablesource .= "rowspan=" . $rowspancells[$y][$x] . "|";
                        $tablesource .= $cells[$y][$x];
                        if ($x < count($cells[$y]) - 1) $tablesource .= " || ";
                    }
                }
                $tablesource .= "\n|-\n";
            }
            $tablesource .= "|}\n";
            $tablesource = preg_replace("/'linkpipe'/", "|", $tablesource);
            $output .= $tablesource;
            //end display mediawiki table

            $headers = "";
            unset($cells);
            $cells = [];
            $row = 0;
        } // endif have we left a table

        // replace tables
        if (preg_match("/^\^/", $line)) {
            $in_table = true;
            $row = 0; // the header row is row 0. It may exist or not.
            $line = preg_replace("/^\^/", "", $line);
            $line = preg_replace("/\n/", "", $line);
            $line = preg_replace("/\^$/", "", $line);
            $headers = explode("^", $line);
        }

        if ($in_table) $line = ""; // if this is in a table then the table's content will be stored in $headers and $cells
        // end care for tables

        $line = preg_replace("/'linkpipe'/", "|", $line);

        $output .= $line;
    } //while (++$i<$linecount)
    // is the end of file also an end of table?

    $output .= "\n\n[[Categorie:Geimporteerd]]";


    $categories = explode("__", $fileParts);
    $laatsteCat = array_pop($categories);

    foreach ($categories as $category) {
        $category = ucfirst($category);
        $output .= "[[Categorie:$category]]";
    }


    echo "Created " . $fileParts . PHP_EOL;
    $outputfile = fopen($tempDir . $fileParts . ".mod", "w");
    fwrite($outputfile, trim($output) . "\n");
    fclose($outputfile);
}
?>
