<?php

require_once('msxmagutils.php');


function msxmag_pdf_url($msxmag_nr, $pag = 0)
{
    global $mcm_baseMagazinePdfUrl;
    global $mcm_baseListingboekPdfUrl;

    if (is_magazine($msxmag_nr)) {
        $pdfURL = $mcm_baseMagazinePdfUrl . mcm_pdfbasename($msxmag_nr) . $msxmag_nr . ".pdf";
    } else if (is_listingboek($msxmag_nr)) {
        $pdfURL = $mcm_baseListingboekPdfUrl . mcm_pdfbasename($msxmag_nr) . ".pdf";
    }
    if ($pag) {
        $pdfURL .= "#page=" . $pag;
    }
    return $pdfURL;
}


/**
 * returns de html for the pdf link to mcm / mccm
 *
 * @param $attr
 * @param $post
 * @param $mcm_baseMagazinePdfUrl
 * @param $mcm_baseListingboekPdfUrl
 * @return string
 */
function msxmag_pdf($attr)
{
    global $post; // the Wordpress current post

    $mcm_nr = mcm_nr_from_attr_or_pagename($attr, get_the_title($post->ID));

    $pdfHTML = "<div class='mcmpdf'>";
    $pdfHTML .= "Download: ";
    $pdfURL = msxmag_pdf_url($mcm_nr);
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


/**
 * returns de HTML for the disk, for MCM
 * just a single disk, containing the listings of an issue.
 *
 * @param $mcm_nr
 * @param $mcm_emulatorUrl
 * @param $mcm_baseDiskUrl
 * @return string
 */
function mcm_disk($mcm_nr)
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
    $diskHTML .= _("Start WebMSX met");
    if (is_disk_available($mcm_nr)) {
        $diskHTML .= " <a href='$diskURL' target='_blank'>";
        $diskHTML .= mcm_disk_name($mcm_nr);
        $diskHTML .= "</a>";
    } else {
        $diskHTML .= _(" Geen disk beschikbaar");
    }
    $diskHTML .= "</div>";
    return $diskHTML;
}

/**
 * returns de HTML for the disk, for MCCM
 * multiple disks ('diskabonnement')
 *
 * @param $mccm_nr
 * @param $mcm_emulatorUrl
 * @param $mcm_baseDiskUrl
 * @return string
 */
function mccm_disk($mccm_nr, $mcm_emulatorUrl, $mcm_baseDiskUrl)
{
    global $mcm_emulatorUrl;
    global $mcm_baseDiskUrl;
    global $mccm_listings;

    $diskHTML = "<div class='mcmdisk'>\n";
    $diskHTML .= _("Diskabonnement bij dit nummer:");
    $diskHTML .= "<br>\n";
    $diskHTML .= "Zie";
    $pdfURL = msxmag_pdf_url($mccm_nr, mccm_diskabopag($mccm_nr));
    $diskHTML .= " <a href='$pdfURL' target='_blank'>";
    $diskHTML .= sprintf(_("pagina %s"), mccm_diskabopag($mccm_nr));
    $diskHTML .= "</a>\n";
    $diskHTML .= "<ul>\n";

    $nr = null;
    $letter = null;

    for ($i = 0; $i < sizeof($mccm_listings); $i++) {
        $listing = $mccm_listings[$i];

        if ($listing[0] == $mccm_nr) {
            if ($listing[0] !== $nr || $listing[1] !== $letter) {
                $nr = $listing[0];
                $letter = $listing[1];
                $diskHTML .= "<li>";
                $diskURL = $mcm_emulatorUrl . '?';
                $diskURL .= mcm_msx_version_url(2); // always MSX2
                $diskURL .= '&DISKA_URL=';
                $diskURL .= $mcm_baseDiskUrl . msx_disk_filename($mccm_nr, $letter);
                $diskHTML .= "<a href='$diskURL' target='_blank'>";
                $diskHTML .= mcm_disk_name($mccm_nr, $letter);
                $diskHTML .= "</a>";
                $diskHTML .= "</li>\n";
            }
        }
    }
    $diskHTML .= "</ul>\n";
    $diskHTML .= "</div>\n";
    return $diskHTML;

}

function show_programs($progList)
{
    global $mcm_emulatorUrl;
    global $mcm_baseDiskUrl;
    global $mcm_baseDiskZipUrl;
    global $mcm_baseMagazinePdfUrl;

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
        if ($nr == 101 || $nr == 102) {
            $listingURL .= '&DISKA_URL=' . $mcm_baseDiskUrl . msx_disk_filename($nr);
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
            $abspag = abs($pag);
            $pdfURL = msxmag_pdf_url($nr, $abspag);
            $pagText = " (<a href='$pdfURL' target='_blank'>";
            $pagText .= _("p. ");
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
        $listHTML .= "</li>\n";
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
function mcm_listings($mcm_nr)
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
    $listHTML = "<div class='mcmlistings'>\n";
    if (!empty($listings)) {
        $listHTML .= "<p>";
        $listHTML .= _("Listings in dit nummer:");
        $listHTML .= "</p>\n";
        $listHTML .= "<ul>\n";
        $listHTML .= show_programs($listings);
        $listHTML .= "</ul>\n";
    }
    if (!empty($extras)) {
        $listHTML .= "<p>";
        $listHTML .= _("Extra's op disk");
        $listHTML .= " MCM-D" . (int)mcm_disknr($mcm_nr) . ":</p>\n";
        $listHTML .= "<ul>\n";
        $listHTML .= show_programs($extras);
        $listHTML .= "</ul>\n";
    }
    $listHTML .= "</div>\n";
    return $listHTML;
}

/**
 * toon disk en listings in MCCM formaat (zg. diskabonnement)
 *
 * @param $mccm_nr
 * @return string
 */
function mccm_listings($mccm_nr)
{
    global $mccm_listings;


//    $listHTML = "<div class='mcmlistings'>";
//    $listHTML .= "</div>";
    return $listHTML;
}


?>