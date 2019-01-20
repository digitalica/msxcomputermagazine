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
    } else if ($msxmag_nr == 91) { // special case, afsluitende cd
        $pdfURL = $mcm_baseMagazinePdfUrl . mcm_pdfbasename($msxmag_nr) . ".pdf";
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
function msxmag_pdf($msxmag_nr)
{
    $pdfHTML = "<div class='mcmpdf'>";
    $pdfHTML .= "Download: ";
    $pdfURL = msxmag_pdf_url($msxmag_nr);
    if (is_pdf_available($msxmag_nr)) {
        $pdfHTML .= "<a href='$pdfURL' target='_blank'>";
        $pdfHTML .= magazine_name($msxmag_nr);
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
    if (is_disk_available($mcm_nr)) {
        $diskHTML .= _("Start WebMSX met");
        $diskHTML .= " <a href='$diskURL' target='_blank'>";
        $diskHTML .= msx_disk_name($mcm_nr);
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
function mccm_disk($mccm_nr)
{
    global $mcm_emulatorUrl;
    global $mcm_baseDiskUrl;
    global $mccm_disks;

    $diskHTML = "<div class='mcmdisk'>\n";
    $diskHTML .= _("Diskabonnement bij dit nummer");

    if ($mccm_nr != 91) { // 91 was extra contents with 90 on cd (pdf) only
        $diskHTML .= " (zie";
        $pdfURL = msxmag_pdf_url($mccm_nr, mccm_diskabopag($mccm_nr));
        $diskHTML .= " <a href='$pdfURL' target='_blank'>";
        $diskHTML .= sprintf(_("pagina %s)"), mccm_diskabopag($mccm_nr));
        $diskHTML .= "</a>";
    } else {
        $mccm_nr = 90;
    }

    $diskHTML .= ":\n<ul>\n";

    for ($i = 0; $i < sizeof($mccm_disks); $i++) {
        $disk = $mccm_disks[$i];

        if ($disk[0] == $mccm_nr) {
            $letter = $disk[1];
            $diskHTML .= "<li>";
            $diskURL = $mcm_emulatorUrl . '?';
            $diskURL .= mcm_msx_version_url(2); // always MSX2
            $diskURL .= '&DISKA_URL=';
            $diskURL .= $mcm_baseDiskUrl . msx_disk_filename($mccm_nr, $letter);
            $diskHTML .= "<a href='$diskURL' target='_blank'>";
            $diskHTML .= msx_disk_name($mccm_nr, $letter);
            $diskHTML .= "</a>";
            if ($disk[2]) {
                $diskHTML .= " " . $disk[2];
            }
            if (sizeof($disk) > 3 && $disk[3]) {
                $pdfURL = msxmag_pdf_url($mccm_nr, $disk[3]);
                $diskHTML .= " <a href='$pdfURL' target='_blank'>";
                $diskHTML .= sprintf(_("(pagina %s)"), $disk[3]);
                $diskHTML .= "</a>";
            }
            $diskHTML .= "</li>\n";
        }
    }
    $diskHTML .= "</ul>\n";
    $diskHTML .= "</div>\n";
    return $diskHTML;

}


function show_programs($progList, $letter = '')
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
        if (strtolower($filename) == 'autoexec.bas') {
            $filename = 'autoexec.baz'; // all renamed in the zips to prevent execution on WebMSX
        }
        $name = $listing[3];
        $msx_version = $listing[4];
        $runtype = 'R';
        if (sizeof($listing) > 5) {
            $runtype = $listing[5];
        }
        $listingURL = $mcm_emulatorUrl . '?';
        $listingURL .= mcm_msx_version_url($msx_version);
        //            $listingURL .= '&DISKA_FILES_URL=' . $mcm_baseListingUrl . 'mcmd' . mcm_disknr($nr) . '.di1/' . urlencode($filename);
        if (is_mccm($nr)) {
            $listingURL .= '&DISKA_FILES_URL=' . $mcm_baseDiskZipUrl . 'disk' . $nr . $letter . '.zip';
        } else if ($nr == 101 || $nr == 102) {
            $listingURL .= '&DISKA_URL=' . $mcm_baseDiskUrl . msx_disk_filename($nr);
        } else {
            $listingURL .= '&DISKA_FILES_URL=' . $mcm_baseDiskZipUrl . 'mcmd' . mcm_disknr($nr) . '.zip';
        }
        if ($runtype == 'B') {
            // we use BASIC ENTER to 'fake' BLOAD option.
            // see https://github.com/ppeccin/WebMSX/issues/11
            $listingURL .= '&BASIC_BRUN=' . urlencode($filename);
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
        $listHTML .= " " . msx_disk_name($mcm_nr) . ":</p>\n";
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
    global $mccm_disks;
    global $mccm_listings;
    global $mcm_baseDiskUrl;
    global $mcm_emulatorUrl;

    $listings = array();

    if ($mccm_nr == 91) {
        $mccm_nr = 90;
    }

    for ($i = 0; $i < sizeof($mccm_listings); $i++) {
        $listing = $mccm_listings[$i];

        if ($listing[0] == $mccm_nr) {
            // echo $mccm_nr . " " . implode($listing, ', ') . " -->" . sizeof($listing) . " " . $listing[6] . "<br>";
            if (sizeof($listing < 7 || $listing[6] != 'X')) {
                $letter = $listing[1];
                if (!array_key_exists($letter, $listings)) {
                    $listings[$letter] = array();
                }
                if (sizeof($listing > 6)) { // note: we remove element 1 (the letter)
                    array_push($listings[$letter], array($listing[0], $listing[2], $listing[3], $listing[4], $listing[5], $listing[6]));
                } else {
                    array_push($listings[$letter], array($listing[0], $listing[2], $listing[3], $listing[4], $listing[5]));
                }
            }
        }
    }

    $mccm_disk_names = array();
    $mccm_disk_pagenrs = array();
    for ($i = 0; $i < sizeof($mccm_disks); $i++) {
        $disk = $mccm_disks[$i];
        if ($disk[0] == $mccm_nr) {
            $mccm_disk_names[$disk[1]] = $disk[2];
            if (sizeof($disk) > 3 && $disk[3]) {
                $mccm_disk_pagenrs[$disk[1]] = $disk[3]; // optional parameter for pagenr
            } else {
                $mccm_disk_pagenrs[$disk[1]] = 0;
            }
        }
    }

    $listHTML = "";
    foreach ($listings as $letter => $programs) {
        $listHTML .= "<div class='mcmdisk'>\n";
        $diskURL = $mcm_emulatorUrl . '?';
        $diskURL .= mcm_msx_version_url(2); // always MSX2
        $diskURL .= '&DISKA_URL=';
        $diskURL .= $mcm_baseDiskUrl . msx_disk_filename($mccm_nr, $letter);
        $listHTML .= _("Disk ");
//        $listHTML .= "<a href='$diskURL' target='_blank'>";
        $listHTML .= msx_disk_name($mccm_nr, $letter);
        $listHTML .= ": " . $mccm_disk_names[$letter];
        if ($mccm_disk_pagenrs[$letter]) {
            $pdfURL = msxmag_pdf_url($mccm_nr, $mccm_disk_pagenrs[$letter]);
            $listHTML .= " <a href='$pdfURL' target='_blank'>";
            $listHTML .= sprintf(_("(pagina %s)"), $mccm_disk_pagenrs[$letter]);
            $listHTML .= "</a>";
        }
//        $listHTML .= "</a>";
        $listHTML .= "<ul>\n";
        $listHTML .= show_programs($programs, $letter);
        $listHTML .= "</ul>\n";
        $listHTML .= "</div>\n";
    }

    return $listHTML;
}


/**
 * return infostring to show at bottom of a magazine
 * TODO: make multilingual
 *
 * @param $msxmag_nr
 * @return string
 */
function msxmag_info($msxmag_nr)
{
    $infoHTML = "";
    if (is_mccm($msxmag_nr)) {
        $infoHTML .= "<div class='mcminfo'>\n";
        $infoHTML .= "Zoek je meer MCCM info? Zie ook de speciale";
        $infoHTML .= " <a href=\"/mccm/\">website</a>";
        $infoHTML .= " met product support (Millenium cd-roms, MSX4PC), correcties en ";
        $infoHTML .= " <a href=\"/mccm/millennium/milc/index.htm\">MiLC</a>";
        $infoHTML .= " (MSX Informatie &amp; Listings Collectie).";
        $infoHTML .= "</div>\n";
    }
    return $infoHTML;
}


?>
