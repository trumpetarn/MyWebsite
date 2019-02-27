<?php
/*
 yr.php  -  YR.no forecast on YOUR page!

 This script was downloaded from http://www.yr.no/verdata/1.5542682
 New page: http://om.yr.no/verdata/php/
 Please read the tips on that page on how you would/should use this script

 You need a webserver with PHP version 5 or later to run this script.
 A lot of comments are in Norwegian only. We will be translating to english whenever we have the opportunity.
 For feedback / bug repports / feature requests, please contact us: http://om.yr.no/sporsmal/kontakt-yr-no

 ###### Changelog

 Version 3.1 - Andreas Røste (andreas200@live.no) 2016.08.15 16.45
 * Fixed multiple declaretions in generateHTMLCached.
 * Corrected link to copyright.
 * Small bugfixes. This API now fully works with PHP 7.1!

 Version: 3.0 - Marius Undrum (marius.undrum@nrk.no) / NRK 2015.09.02
 * Changed encoding from ISO-8859-1 to UTF-8
 * Updated css url
 * Updated weather symbols url

 Versjon: 2.6 - Lennart André Rolland (lennart.andre.rolland@nrk.no) / NRK - 2008.11.11 11:48
 * Added option to remove banner ($yr_use_banner)
 * Added option to allow any target for yr.no urls ($yr_link_target)

 Versjon: 2.5 - Lennart André Rolland (lennart.andre.rolland@nrk.no) / NRK - 2008.09.25 09:24
 * Cache will now update on parameter changes (cache file is prefixed with md5 digest of all relevant parameters)
   This change will in the future make it easier to use the script for multiple locations in one go.
 * Most relevant comments translated to english

 Versjon 2.4 - Sven-Ove Bjerkan (sven-ove@smart-media.no) / Smart-Media AS - 2008.10.22 12:14
 * Endret funksjonalitet ifbm med visning av PHP-feil (fjernet blant annet alle "@", dette styres av error_reporting())
 * Ved feilmelding så ble denne lagret i lokal cache slik at man fikk opp feilmld hver gang inntil "$yr_maxage" inntreffer og den forsøker å laste på nytt - den cacher nå ikke hvis det oppstår en feil
 * $yr_use_text, $yr_use_links og $yr_use_table ble overstyrt til "true" uavhengig av brukerens innstilling - rettet!

 Versjon: 2.3 - Lennart André Rolland (lennart.andre.rolland@nrk.no) / NRK - 2008.09.25 09:24
 * File permissions updated
 * Caching is stored in HTML isntead of XML for security
 * Other security and efficiency improvements



 ###### INSTRUCTIONS:

 1. Edit this script in editors with UTF-8 character set.
 2. Edit the settings below
 3. Transfer the script to a folder in your webroot.
 4. Make sure that the webserver has write access to the folder where thsi script is placed. It will create a folder called yr-cache and place cached HTML data in that directory.

 */

///  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///  /
///  ///  ///  ///  ///  Settings  ///  ///  ///  ///  ///  ///  ///  ///  //
//  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///  ///

// 1. Lenke: Lenke til stedet på yr.no (Uten siste skråstrek. Bruk vanlig æøå i lenka )
//    Link: Link to the url for the location on yr.no (Without the last Slash.)
$yr_url='https://www.yr.no/sted/Sverige/V%C3%A4sterbotten/Ume%C3%A5/';

// 2. Stedsnavnet: Skriv inn navnet på stedet. La stå tom for å falle tilbake til navnet i lenken
//    Location: The name of the location. Leave empty to fallback to the location in the url.
$yr_name='Umeå';

// 3. Bruk header og footer: Velg om du vil ha med header og/eller  footer
//    Use Header and footers: Select to have HTML headers/footers wrapping the content (useful for debugging)
//PS: Header for HTML dokumentet er XHTML 1.0 Strict
//    Skrus som regel av når du inlemmer i eksisterende dokument!
//
$yr_use_header=$yr_use_footer=true;

// 4. Deler: Velg delene av varselet du vil ta med!
//    Parts: Choose which parts of the forecast to include
$yr_use_banner=true; //yr.no Banner
$yr_use_text=false;   //Tekstvarsel
$yr_use_links=true;  //Lenker til varsel på yr.no
$yr_use_table=true;  //Tabellen med varselet

// 5. Mellomlagringstid: Antall sekunder før nytt varsel hentes fra yr.no.
//    Cachetime: Number of seconds to keep forecast in local cache
//    Den anbefalt verdien på 1200 vil oppdatere siden hver 20. minutt.
//
//    PS: Vi ønsker at du setter 20 minutters mellomlagringstid fordi
//    det vil gi høyere ytelse, både for yr.no og deg! MEN for å få til dette
//    vil vi opprette en mappe og lagre en fil i denne mappen. Vi har gått
//    gjennom scriptet veldig nøye for å forsikre oss om at det er feilfritt.
//    Likevel er dette ikke helt uproblematisk i forhold til sikkerhet.
//    Hvis du har problemer med dette kan du sette $yr_maxage til 0 for å skru
//    av mellomlagringen helt!
$yr_maxage=1200;

// 6. Utløpstid: Denne instillingen lar deg velge hvor lenge yr.no har på å
//    levere varselet i sekunder.
//    Timeout: How long before this script gives up fetching data from yr.no
//
//    Hvis yr.no skulle være nede eller det er
//    forstyrrelser i båndbredden ellers, vil varselet erstattes med en
//    feilmelding til situasjonen er bedret igjen. PS: gjelder kun når nytt
//    varsel hentes! Påvirker ikke varsel mens siden viser varsel fra
//    mellomlageret. Den anbefalte verdien på 10 sekunder fungerer bra.
$yr_timeout=10;

// 7. Mellomlagrinsmappe: Velg navn på mappen til mellomlagret data.
//    Cachefolder: Where to put cache data
//
//Dette scriptet vil forsøke å opprette mappen om den ikke finnes.
$yr_datadir='yr_cache';


// 8. Lenke mål: Velg hvilken target som skal brukes på lenker til yr.no
//    Link target: Choose which target to use for links to yr.no
$yr_link_target='_top';

// 9. Vis feilmeldinger: Sett til "true" hvis du vil ha feilmeldinger.
//    Show errors: Useful while debugging.
//
//greit ved feilsøking, men bør ikke være aktivert i drift.
$yr_vis_php_feilmeldinger=true;


if($yr_vis_php_feilmeldinger) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
}
else {
    error_reporting(0);
    ini_set('display_errors', false);
}

print_r(loadXml($yr_url));

function loadXml($xml_url) {
	$xml_url.='/forecast.xml';
	$xml_dir=simplexml_load_file($xml_url) or die("Error: Cannot reach Dir");
	return $xml_dir;
}