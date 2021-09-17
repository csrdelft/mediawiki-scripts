# C.S.R. MediaWiki

Deze repository bevat een aantas scripts om de overstap van DokuWiki naar MediaWiki makkelijker te maken.

## Installatie

- Download de laatste versie van MediaWiki.
- Pak de map uit in de csrdelft.nl repository in de `htdocs/mediawiki` map
- Plaats de `PluggableAuth`, `WSOAuth`, `Realnames`, `MobileFrontend` en `Cargo` extensies in de `extensions` map.
- Kopieer de `extensions` map in deze repository naar de `mediawiki`map. Dit voegt onze uitbreidingen samen met de `WSOAuth` extensie.
- Plaats de `MinervaNueue` skin in de `skins` map.
- Kopieer `LocalSettings.php` naar de mediawiki map en loop alle waardes langs.
- Voer `maintenance/update.php` uit om zeker te zijn dat alle tabellen bestaan.
- Maak een nieuwe OAuth applicatie in de stek aan en zet de waardes in `LocalSettings.php` goed.
- Voer de scripts in de `scripts` map uit. Deze scripts hebben een verwijzing naar de dokuwiki en naar de mediawiki map nodig.

## Scripts

`01_prepare_media.ps1 <dokuwikidir> <mediawikidir>`: Trek media uit dokuwiki en kopieer naar één map.

`02_import_media.ps1 <dokuwikidir> <mediawikidir>` : Importeer media in mediawiki

`03_convert_pages.ps1 <dokuwikidir> <mediawikidir>` : Converteer dokuwiki naar mediawiki syntax

`04_import_pages.ps1 <dokuwikidir> <mediawikidir>` : Importeer sjablonen, categorien en pagina's in mediawiki.

## Extensions

### Standaard extensies

- [VisualEditor](https://www.mediawiki.org/wiki/Extension:VisualEditor) (Gebundeld)
- [TemplateData](https://www.mediawiki.org/wiki/Extension:TemplateData) (Gebundeld)
- [PluggableAuth](https://www.mediawiki.org/wiki/Extension:PluggableAuth)
- [Realnames](https://www.mediawiki.org/wiki/Extension:Realnames)
- [WSOAuth](https://www.mediawiki.org/wiki/Extension:WSOAuth)
- [MobileFrontend](https://www.mediawiki.org/wiki/Extension:MobileFrontend)
- [Cargo](https://www.mediawiki.org/wiki/Extension:Cargo)

### Eigen extensies

De WSOAuth extensie is uitgebreid met en specifieke CsrAuth AuthProvider.

## Skins

- [Vector](https://www.mediawiki.org/wiki/Skin:Vector)
- [MinervaNeue](https://www.mediawiki.org/wiki/Skin:MinervaNeue) (voor mobiel)

## Data pagina's

Met Cargo en TemplateData templates is het mogelijk om makkelijk formulieren te maken voor pagina's

De volgende types bestaan:

- Huishoudelijke Vergadering
    - Nummer
    - Datum
    - Bestuur
    - Notulen
    - InstallatieGroepen
    - DechargeGroepen
- Motie
    - HV Nummer
    - Nummer
    - Datum
    - Einddatum
    - Besluit
    - Geldigheid
    - Labels
    - Relevantie
    - Ondertekend door
- Vormingsbank
    - Titel
    - Door
    - Collegejaar
    - Rubriek
    - Activiteit
    - Datum
    - Webstek
    - Vergelijkbaar
    - Semester
- Recept
    - Naam
    - Typering
    - Gang
    - Omschrijving
    - Aantal personen
- Kerk
    - Kerknaam
    - Denominatie
    - Website
    - Grootte
    - Kerkverband
    - Voorganger
    - Contactpersoon
    - Aanvangstijd viering zondag
- Keuzevak
    - Vaknaam
    - Code
    - Link
    - Ects
    - Moeilijk
    - Vakgebied
    - Boekkosten
    - Start
- Organisatie
    - Organisatie
    - Doelgroep
    - Adres
    - Postcode
    - Plaats
    - Telefoon
    - Email
    - Website
- Kluslijst
    - Frequentie
    - Door
    - Laatst gedaan
    - Planning
    - Opmerking

## TODO

- Overige sjablonen maken voor verschillende data types
- Data lijsten maken
- Overige categorie pagina's aanmaken

## Pijnpunten

- Dokuwiki staat relatieve pagina-verwijzingen toe, dit zorgt voor problemen bij het converteren. Hierdoor ontstaan er veel rode linkjes
- imgref bestaat niet in mediawiki