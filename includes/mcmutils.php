<?php

/**
 * Extracts the mcm nr from pagename, formatted as ''
 *
 * note: we return 101 and 102 for listingboeken
 *
 * @param $pagename
 * @return int
 */
function mcm_nr_from_pagename($pagename)
{
    // first check regular
    preg_match('/nr\. (\d+)/i', $pagename, $matches);
    if (sizeof($matches) > 1 && $matches[1]) {
        return $matches[1];
    }
    // then test for listingboek
    preg_match('/listingboek (\d+)/i', $pagename, $matches);
    if (sizeof($matches) > 1 && $matches[1]) {
        return $matches[1] + 100;
    }
    // if nothing worked...
    return 0;
}

/**
 * checks if the number corresponds to a magazine
 *
 * @param $nr
 * @return bool
 */
function is_magazine($nr)
{
    return $nr > 0 && $nr < 91;
}


/**
 * checks if the number corresponds to a listing boek
 *
 * @param $nr
 * @return bool
 */
function is_listingboek($nr)
{
    return $nr == 101 || $nr == 102;
}

/**
 * checks if we have pdf available for this number
 *
 * @param $nr
 * @return bool
 */
function is_pdf_available($nr)
{
    return is_magazine($nr) || is_listingboek($nr);
}

/**
 * checks if we have disk available for this number
 *
 * @param $nr
 * @return bool
 */
function is_disk_available($nr)
{
    return $nr > 0 && $nr < 58;
}


/**
 * get the mcm nr from shortcode attr (or from pagename)
 *
 * @param $attr
 * @return int the mcm_nr, formatted in 2 digits
 */
function mcm_nr_from_attr_or_pagename($attr, $pagename)
{
    global $post;

    $mcm_nr = $attr['mcm'];
    if (empty($mcm_nr)) {
        $mcm_nr = mcm_nr_from_pagename($pagename);
    }
    return sprintf("%02d", $mcm_nr);
}

/**
 * gets the correct disknr for a certain mcm issue...
 * listings for nr 1 & 2 are on disk 1 ;-)
 *
 * @param $nr
 * @return int the mcm disk_nr, formatted in 2 digits
 */
function mcm_disknr($nr)
{
    if ($nr == 1) {
        return "01";
    }
    if ($nr == 55) {
        return 55; // disk 54 is vergeten, zie mcm56, p42.  (thanks manuel)
    }
    if ($nr > 1) {
        return sprintf("%02d", $nr - 1);
    }
}

/**
 * Returns the basename for the pdf for a certain issue
 *
 * MCM was renamed to include msdos too for a while and finally merged with MSX Club Magazine
 *
 * @param $nr
 * @return string
 */
function mcm_pdfbasename($nr)
{
    $basename = 'UNKNOWNPDF';
    if ($nr >= 1 && $nr <= 21) {
        $basename = 'msx_computer_magazine_';
    }
    if ($nr >= 22 && $nr <= 35) {
        $basename = 'ms(x)dos_computer_magazine_';
    }
    if ($nr >= 36 && $nr <= 57) {
        $basename = 'msx_computer_magazine_';
    }
    if ($nr >= 58 && $nr <= 90) {
        $basename = 'msx_computer_club_magazine_';
    }
    if ($nr >= 101 && $nr <= 102) {
        $basename = 'mcm_listing_boek_' . ($nr - 100); // hmmm... hier maar met nr ;-)
    }

    return urlencode($basename);
}

/**
 * returns the name for the magazine of the number
 * (to be used in pdf link)
 *
 * @param $nr
 * @return string
 */
function magazine_name($nr)
{
    $magazine_name = 'UNKNOWN';
    if ($nr >= 1 && $nr <= 21) {
        $magazine_name = 'MSX Computer Magazine ' . ($nr + 0);
    }
    if ($nr >= 22 && $nr <= 35) {
        $magazine_name = 'MS(X)DOS Computer Magazine ' . ($nr + 0);
    }
    if ($nr >= 36 && $nr <= 57) {
        $magazine_name = 'MSX Computer Magazine ' . ($nr + 0);
    }
    if ($nr >= 58 && $nr <= 90) {
        $magazine_name = 'MSX Computer Club Magazine ' . ($nr + 0);
    }
    if ($nr >= 101 && $nr <= 102) {
        $magazine_name = 'MCM listingboek ' . ($nr - 100); // hmmm... hier maar met nr ;-)
    }

    return $magazine_name;
}


function mcm_msx_version_url($msx_version)
{
    switch ($msx_version) {
        case 1:
            $url = 'MACHINE=MSX1E';
            break;
        case 2:
            $url = 'MACHINE=MSX2E';
            break;
        case 3: // msx 2+
            $url = 'MACHINE=MSX2PE';
            break;
        default: // none
            $url = 'MACHINE=';
    }
    return $url;
}

?>