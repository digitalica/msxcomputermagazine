<?php
/*
Plugin name: MSX Computer Magazine
Description: Voor de links naar de listings, disks en pdfs van MSX Computer Magazine
Version: 0.40
Author: Digitalica
GitHub Plugin URI: https://github.com/digitalica/msxcomputermagazine
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or die('No script kiddies please!');

include(plugin_dir_path(__FILE__) . 'includes/mcmutils.php');
include(plugin_dir_path(__FILE__) . 'includes/mcmlistings.php');

$mcm_emulatorUrl = 'http://webmsx.org';
$mcm_baseUrl = 'http://www.msxcomputermagazine.nl';
$mcm_baseListingUrl = $mcm_baseUrl . '/archief/listings/';
$mcm_baseDiskZipUrl = $mcm_baseUrl . '/archief/diskzips/';
$mcm_baseDiskUrl = $mcm_baseUrl . '/archief/disks/';
$mcm_basePdfUrl = $mcm_baseUrl . '/archief/bladen/';


add_shortcode('pdf', 'mcm_pdf');

function mcm_pdf($attr)
{
    global $post;
    global $mcm_basePdfUrl;

    $mcm_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));

    $pdfURL = $mcm_basePdfUrl . mcm_pdfbasename($mcm_nr) . $mcm_nr . ".pdf";
    $pdfHTML = "<div class='mcmpdf'>";
    $pdfHTML .= "<a href='$pdfURL' target='_blank'>";
    $pdfHTML .= "MSX Computer Magazine " . ($mcm_nr + 0);
    $pdfHTML .= "</a>";
    $pdfHTML .= "</div>";
    return $pdfHTML;
}

add_shortcode('disk', 'mcm_disk');

function mcm_disk($attr)
{
    global $post;
    global $mcm_emulatorUrl;
    global $mcm_baseDiskUrl;

    $mcm_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));

    $msx_version = 1;
    if ($mcm_nr > 6) { // first mention of MSX2 in nr 6, but in nr 7 first MSX2 programs on disk.
        $msx_version = 2;
    }

    $diskURL = $mcm_emulatorUrl . '?';
    $diskURL .= mcm_msx_version_url($msx_version);
    $diskURL .= '&DISKA_URL=';
    $diskURL .= $mcm_baseDiskUrl . 'mcmd' . mcm_disknr($mcm_nr) . ".di1";
    $diskHTML = "<div class='mcmdisk'>";
    $diskHTML = "<a href='$diskURL' target='_blank'>";
    $diskHTML .= "MCM-D" . (int)mcm_disknr($mcm_nr);
    $diskHTML .= "</a>";
    $diskHTML .= "</div>";
    return $diskHTML;
}

add_shortcode('listings', 'mcm_listings');

function show_programs($progList)
{
    global $mcm_emulatorUrl;
    global $mcm_baseListingUrl;
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
        $listingURL .= '&DISKA_FILES_URL=' . $mcm_baseDiskZipUrl . 'mcmd' . mcm_disknr($nr) . '.zip';
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
            global $mcm_basePdfUrl;
            $abspag = abs($pag);
            $pdfURL = $mcm_basePdfUrl . mcm_pdfbasename($nr) . sprintf("%02d", $nr) . ".pdf";
            $pagText = " (<a href='$pdfURL#page=$abspag' target='_blank'>pagina $abspag</a>)";
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

function mcm_listings($attr)
{
    global $post;
    global $mcm_listings;

    $mcm_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));

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
        $listHTML .= "<p>Listings in dit nummer:</p>";
        $listHTML .= "<ul>";
        $listHTML .= show_programs($listings);
        $listHTML .= "</ul>";
    }
    if (!empty($extras)) {
        $listHTML .= "<p>Extra's op MCM-D" . (int)mcm_disknr($mcm_nr) . ":</p>";
        $listHTML .= "<ul>";
        $listHTML .= show_programs($extras);
        $listHTML .= "</ul>";
    }
    $listHTML .= "</div>";
    return $listHTML;
}


?>
