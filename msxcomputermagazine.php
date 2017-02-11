<?php
/*
Plugin name: MSX Computer Magazine
Description: Voor de links naar de listings, disks en pdfs van MSX Computer Magazine
Version: 0.53
Author: Digitalica
GitHub Plugin URI: https://github.com/digitalica/msxcomputermagazine
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or die('No script kiddies please!');

require_once(plugin_dir_path(__FILE__) . 'includes/msxmagutils.php');
require_once(plugin_dir_path(__FILE__) . 'includes/msxmaghtml.php');
require_once(plugin_dir_path(__FILE__) . 'includes/mcmlistings.php');
require_once(plugin_dir_path(__FILE__) . 'includes/mccmlistings.php');

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

    $pdfHTML = msxmag_pdf($attr);
    return $pdfHTML;
}


add_shortcode('disk', 'shortcode_disk');


function shortcode_disk($attr)
{
    global $post; // the Wordpress current post

    $msxmag_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));

    if (is_mccm($msxmag_nr)) {
        $diskHTML = mccm_disk($msxmag_nr);
    } else {
        $diskHTML = mcm_disk($msxmag_nr);
    }

    return $diskHTML;
}


add_shortcode('listings', 'shortcode_listings');


function shortcode_listings($attr)
{
    global $post; // the Wordpress current post

    $msxmag_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));

    if (is_mccm($msxmag_nr)) {
        $listHTML = mccm_listings($msxmag_nr);
    } else {
        $listHTML = mcm_listings($msxmag_nr);
    }

    return $listHTML;
}


?>
