<?php

/**
 * Extracts the mcm nr from pagename, formatted as ''
 *
 *
 * @param $pagename
 * @return int
 */
function mcm_nr_from_pagename($pagename)
{
    preg_match('/nr\. (\d+)/', $pagename, $matches);
    if (sizeof($matches) > 1 && $matches[1]) {
        return $matches[1];
    } else {
        return 0;
    }
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
    return urlencode($basename);
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