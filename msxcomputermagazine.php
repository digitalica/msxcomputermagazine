<?php
/*
Plugin name: MSX Computer Magazine
Description: Voor de links naar de listings, disks en pdfs van MSX Computer Magazine
Version: 0.45
Author: Digitalica
GitHub Plugin URI: https://github.com/digitalica/msxcomputermagazine
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or die('No script kiddies please!');

include(plugin_dir_path(__FILE__) . 'includes/mcmutils.php');
include(plugin_dir_path(__FILE__) . 'includes/mcmlistings.php');
include(plugin_dir_path(__FILE__) . 'includes/mccmlistings.php');

//$langdomain = 'msxcomputermagazine';
//$langdir = plugin_dir_path(__FILE__) . "languages";
////$locale = 'en_GB.utf8';
//$locale = 'nl_NL.utf8';
//
//$results = putenv('LC_ALL=' . $locale);
//if (!$results) {
//    exit ('setlocale failed: locale function is not available on this platform, or the given local does not exist in this environment');
//}
//$results = setlocale(LC_ALL, $locale);
//if (!$results) {
//    exit ('setlocale failed: locale function is not available on this platform, or the given local does not exist in this environment');
//}
//$results = bindtextdomain($langdomain, $langdir);
//echo 'new text domain is set: ' . $results. "\n";
//$results = textdomain($langdomain);
//echo 'current message domain is set: ' . $results. "\n";

$mcm_emulatorUrl = 'http://webmsx.org';
$mcm_baseUrl = 'http://www.msxcomputermagazine.nl';
$mcm_baseListingUrl = $mcm_baseUrl . '/archief/listings/';
$mcm_baseDiskZipUrl = $mcm_baseUrl . '/archief/diskzips/';
$mcm_baseDiskUrl = $mcm_baseUrl . '/archief/disks/';
$mcm_baseMagazinePdfUrl = $mcm_baseUrl . '/archief/bladen/';
$mcm_baseListingboekPdfUrl = $mcm_baseUrl . '/archief/lb/';


add_shortcode('pdf', 'shortcode_pdf');

function shortcode_pdf($attr)
{
    global $post;
    global $mcm_baseMagazinePdfUrl;
    global $mcm_baseListingboekPdfUrl;

    $mcm_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));
    if (is_magazine($mcm_nr)) {
        $pdfURL = $mcm_baseMagazinePdfUrl . mcm_pdfbasename($mcm_nr) . $mcm_nr . ".pdf";
    } else if (is_listingboek($mcm_nr)) {
        $pdfURL = $mcm_baseListingboekPdfUrl . mcm_pdfbasename($mcm_nr) . ".pdf";
    }
    $pdfHTML = "<div class='mcmpdf'>";
    if (is_pdf_available($mcm_nr)) {
        $pdfHTML .= "<a href='$pdfURL' target='_blank'>";
        $pdfHTML .= magazine_name($mcm_nr);
        $pdfHTML .= "</a>";
    } else {
        $pdfHTML .= _("Geen pdf beschikbaar");
    }
    $pdfHTML .= "</div>";
    return $pdfHTML;
}

add_shortcode('disk', 'shortcode_disk');

/**
 * returns de HTML for the disk, for MCM
 *
 * @param $mcm_nr
 * @param $mcm_emulatorUrl
 * @param $mcm_baseDiskUrl
 * @return string
 */
function mcm_disk($mcm_nr, $mcm_emulatorUrl, $mcm_baseDiskUrl)
{
    global $mcm_emulatorUrl;
    global $mcm_baseDiskUrl;

    $msx_version = 1;
    if ($mcm_nr > 6) { // first mention of MSX2 in nr 6, but in nr 7 first MSX2 programs on disk.
        $msx_version = 2;
    }

    $diskURL = $mcm_emulatorUrl . '?';
    $diskURL .= mcm_msx_version_url($msx_version);
    $diskURL .= '&DISKA_URL=';
    $diskURL .= $mcm_baseDiskUrl . msx_disk_filename($mcm_nr);
    $diskHTML = "<div class='mcmdisk'>";
    if (is_disk_available($mcm_nr)) {
        $diskHTML = "<a href='$diskURL' target='_blank'>";
        $diskHTML .= mcm_disk_name($mcm_nr);
        $diskHTML .= "</a>";
    } else {
        $diskHTML .= _("Geen disk beschikbaar");
    }
    $diskHTML .= "</div>";
    return $diskHTML;
}

/**
 * returns de HTML for the disk, for MCM
 *
 * @param $mcm_nr
 * @param $mcm_emulatorUrl
 * @param $mcm_baseDiskUrl
 * @return string
 */
function mccm_disk($mcm_nr, $mcm_emulatorUrl, $mcm_baseDiskUrl)
{
    global $mcm_emulatorUrl;
    global $mcm_baseDiskUrl;
    global $mccm_listings;


    $diskHTML = "<div class='mcmdisk'>";
    $diskHTML .= "<ul>";

    $nr = null;
    $letter = null;

    for ($i = 0; $i < sizeof($mccm_listings); $i++) {
        $listing = $mccm_listings[$i];

        if ($listing[0] == $mcm_nr) {
            if ($listing[0] !== $nr || $listing[1] !== $letter) {
                $nr = $listing[0];
                $letter = $listing[1];
                $diskHTML .= "<li>";
                $diskURL = $mcm_emulatorUrl . '?';
                $diskURL .= mcm_msx_version_url(2); // always MSX2
                $diskURL .= '&DISKA_URL=';
                $diskURL .= $mcm_baseDiskUrl . msx_disk_filename($mcm_nr, $letter);
                $diskHTML .= "<a href='$diskURL' target='_blank'>";
                $diskHTML .= mcm_disk_name($mcm_nr, $letter);
                $diskHTML .= "</a>";
                $diskHTML .= "</li>";
            }
        }
    }
    $diskHTML .= "</ul>";
    $diskHTML .= "</div>";
    return $diskHTML;

}


function shortcode_disk($attr)
{
    global $post;

    $mcm_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));


    if (is_mccm($mcm_nr)) {
        $diskHTML = mccm_disk($mcm_nr);
    } else {
        $diskHTML = mcm_disk($mcm_nr);
    }


    return $diskHTML;
}


add_shortcode('listings', 'shortcode_listings');

function show_programs($progList)
{
    global $mcm_emulatorUrl;
    global $mcm_baseDiskUrl;
    global $mcm_baseDiskZipUrl;

    $listHTML = "";
    for ($i = 0; $i < sizeof($progList); $i++) {
        $listing = $progList[$i];
        $nr = $listing[0];

        $pag = $listing[1];
        $filename = $listing[2];
        $name = $listing[3];
        $msx_version = $listing[4];
        $runtype = 'R';
        if (sizeof($listing) > 5) {
            $runtype = $listing[5];
        }
        $listingURL = $mcm_emulatorUrl . '?';
        $listingURL .= mcm_msx_version_url($msx_version);
        //            $listingURL .= '&DISKA_FILES_URL=' . $mcm_baseListingUrl . 'mcmd' . mcm_disknr($nr) . '.di1/' . urlencode($filename);
        if ($nr == 101) {
            $listingURL .= '&DISKA_URL=' . $mcm_baseDiskUrl . 'lb/MCM-L1_MCM_Listingboekdiskette.dsk';
        } else {
            $listingURL .= '&DISKA_FILES_URL=' . $mcm_baseDiskZipUrl . 'mcmd' . mcm_disknr($nr) . '.zip';
        }
        if ($runtype == 'B') {
            // we use BASIC ENTER to 'fake' BLOAD option.
            // see https://github.com/ppeccin/WebMSX/issues/11
            $listingURL .= '&BASIC_ENTER=BLOAD "' . urlencode($filename) . '",r';
        } else { // use this as default, and even if an unknown option was used.
            $listingURL .= '&BASIC_RUN=' . urlencode($filename);
        }
        if ($pag == 0) {
            $pagText = "";
        } else {
            global $mcm_baseMagazinePdfUrl;
            $abspag = abs($pag);
            $pdfURL = $mcm_baseMagazinePdfUrl . mcm_pdfbasename($nr) . sprintf("%02d", $nr) . ".pdf";
            $pagText = " (<a href='$pdfURL#page=$abspag' target='_blank'>";
            $pagText .= _("pagina");
            $pagText .= " $abspag</a>)";
        }
        $listHTML .= "<li>";
        if ($runtype != 'X') {
            $listHTML .= "<a href='$listingURL' target='_blank'>";
        }
        $listHTML .= $name;
        if ($runtype != 'X') {
            $listHTML .= "</a>";
        }
        $listHTML .= $pagText;
        $listHTML .= "</li>";
    }
    return $listHTML;
}


/**
 * toon disk en listings in MCM formaat
 *
 * @param $mcm_listings
 * @param $mcm_nr
 * @return string
 */
function mcm_listings($mcm_listings, $mcm_nr)
{
    global $mcm_listings;

    $progsDitNr = array_values(array_filter(
        $mcm_listings,
        function ($elem) use ($mcm_nr) {
            return $elem[0] == $mcm_nr;
        }
    ));
    $listings = array_values(array_filter(
        $progsDitNr,
        function ($elem) use ($mcm_nr) {
            return $elem[1] > 0;
        }
    ));
    $extras = array_values(array_filter(
        $progsDitNr,
        function ($elem) use ($mcm_nr) {
            return $elem[1] <= 0;
        }
    ));
    $listHTML = "<div class='mcmlistings'>";
    if (!empty($listings)) {
        $listHTML .= "<p>";
        $listHTML .= _("Listings in dit nummer:");
        $listHTML .= "</p>";
        $listHTML .= "<ul>";
        $listHTML .= show_programs($listings);
        $listHTML .= "</ul>";
    }
    if (!empty($extras)) {
        $listHTML .= "<p>";
        $listHTML .= _("Extra's op disk");
        $listHTML .= " MCM-D" . (int)mcm_disknr($mcm_nr) . ":</p>";
        $listHTML .= "<ul>";
        $listHTML .= show_programs($extras);
        $listHTML .= "</ul>";
    }
    $listHTML .= "</div>";
    return $listHTML;
}

/**
 * toon disk en listings in MCCM formaat (zg. diskabonnement)
 *
 * @param $mcm_listings
 * @param $mcm_nr
 * @return string
 */
function mccm_listings($mcm_listings, $mcm_nr)
{
    global $mccm_listings;


//    $listHTML = "<div class='mcmlistings'>";
//    $listHTML .= "</div>";
    return $listHTML;
}


function shortcode_listings($attr)
{
    global $post;

    $mcm_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));

    if (is_mccm($mcm_nr)) {
        $listHTML = mccm_listings($mcm_nr);
    } else {
        $listHTML = mcm_listings($mcm_nr);
    }

    return $listHTML;
}


?>
