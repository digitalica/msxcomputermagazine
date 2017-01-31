<?php
/*
Plugin name: MSX Computer Magazine
Description: Voor de links naar de listings, disks en pdfs van MSX Computer Magazine
Version: 0.22
Author: Digitalica
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or die('No script kiddies please!');

include(plugin_dir_path(__FILE__) . 'includes/mcmutils.php');
include(plugin_dir_path(__FILE__) . 'includes/mcmlistings.php');

$mcm_emulatorUrl = 'http://webmsx.org';
$mcm_baseUrl = 'http://www.msxcomputermagazine.nl';
$mcm_baseListingUrl = $mcm_baseUrl . '/archief/listings/';
$mcm_baseDiskUrl = $mcm_baseUrl . '/archief/disks/';
$mcm_basePdfUrl = $mcm_baseUrl . '/archief/bladen/';


add_shortcode('pdf', 'mcm_pdf');

function mcm_pdf($attr)
{
    global $post;
    global $mcm_basePdfUrl;

    $mcm_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));

    $pdfURL = $mcm_basePdfUrl . mcm_pdfbasename($mcm_nr) . $mcm_nr . ".pdf";
    $pdfHTML = "<a href='$pdfURL' target='_blank'>";
    $pdfHTML .= "MSX Computer Magazine " . ($mcm_nr + 0);
    $pdfHTML .= "</a>";
    return $pdfHTML;
}

add_shortcode('disk', 'mcm_disk');

function mcm_disk($attr)
{
    global $post;
    global $mcm_emulatorUrl;
    global $mcm_baseDiskUrl;

    $mcm_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));

    $diskURL = $mcm_emulatorUrl . '?DISKA_URL=';
    $diskURL .= $mcm_baseDiskUrl . 'mcmd' . mcm_disknr($mcm_nr) . ".di1";
    $diskHTML = "<a href='$diskURL' target='_blank'>";
    $diskHTML .= "disk";
    $diskHTML .= "</a>";
    return $diskHTML;
}

add_shortcode('listings', 'mcm_listings');

function mcm_listings($attr)
{
    global $post;
    global $mcm_emulatorUrl;
    global $mcm_baseListingUrl;
    global $mcm_listings;

    $mcm_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));

    $listHTML = "<ul>";
    for ($i = 0; $i < sizeof($mcm_listings); $i++) {
        $listing = $mcm_listings[$i];
        $nr = $listing[0];
        if ($nr == $mcm_nr) {

            $pag = $listing[1];
            $filename = $listing[2];
            $name = $listing[3];
            $msx_version = $listing[4];
            $listingURL = $mcm_emulatorUrl . '?';
            switch ($msx_version) {
                case 1:
                    $listingURL .= 'MACHINE=MSX1E';
                    break;
                case 2:
                    $listingURL .= 'MACHINE=MSX2E';
                    break;
                case 3: // msx 2+
                    $listingURL .= 'MACHINE=MSX2PE';
                    break;
                default: // none
                    $listingURL .= 'MACHINE=';
            }
            $listingURL .= '&DISKA_FILES_URL=' . $mcm_baseListingUrl . 'mcmd' . mcm_disknr($nr) . '.di1/' . urlencode($filename);
            $listingURL .= '&BASIC_RUN=' . urlencode($filename);
            $listHTML .= "<li><a href='$listingURL' target='_blank'>$name (pag: $pag)</a></li>";
        }
    }
    $listHTML .= "</ul>";
    return $listHTML;
}


?>