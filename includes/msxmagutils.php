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
    // exception for 90 cd
    preg_match('/nr\. (\d+) cd/', $pagename, $matches);
    if (sizeof($matches) > 1 && $matches[1]) {
        return $matches[1] + 1; // voor mccm90 cd pagina, http://www.msxcomputermagazine.nl/archief/mccm-90cd/
    }
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
 * checks if the number corresponds to a mccm magazine
 * (msx computer & club magazine)
 *
 * @param $nr
 * @return bool
 */
function is_mccm($nr)
{
    return $nr >= 58 && $nr <= 90;
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
    return is_magazine($nr) || is_listingboek($nr) || $nr == 91;
}

/**
 * checks if we have disk available for this number
 *
 * @param $nr
 * @return bool
 */
function is_disk_available($nr)
{
    if ($nr >= 1 && $nr <= 57) {
        return true; // for mcm
    }
    if ($nr >= 58 && $nr <= 90) {
        return true; // for mccm
    }
    if ($nr == 101 || $nr == 102) {
        return true; // for listingboek 1
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
    if (is_mccm($nr)) {
        return $nr; // for MCCM the numbers are equal
    }
    if ($nr == 1) {
        return "01";
    }
    if ($nr == 55) {
        return 55; // disk 54 is vergeten, zie mcm56, p42.  (thanks manuel)
    }
    if ($nr > 1) {
        return sprintf("%02d", $nr - 1); // for MCM the disk nr is (usually) 1 less then the real nr.
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
    if ($nr == 91) {
        $basename = 'msx_computer_club_magazine_90_cd';
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
    if ($nr >= 58 && $nr <= 90) {
        $magazine_name = 'MSX Computer Club Magazine ' . ($nr + 0);
    }
    if ($nr == 91) {
        $magazine_name = 'MSX Computer Club Magazine 90 extra';
    }
    if ($nr >= 101 && $nr <= 102) {
        $magazine_name = 'MCM listingboek ' . ($nr - 100); // hmmm... hier maar met nr ;-)
    }

    return $magazine_name;
}

/**
 * returns the correct url options to set the MSX version in the WebMSX url
 *
 * @param $msx_version
 * @return string
 */
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

/**
 * Returns the (human readable) name of the disk for MCM nr
 *
 * @param $nr nummer van het blad (dus NIET van de disk)
 * @param $letter volgnummer (letter) van de disk, alleen voor MCCM
 * @return string
 */
function mcm_disk_name($nr, $letter = '')
{
    if (is_mccm($nr)) {
        return "DA" . $nr . strtoupper($letter);
    }
    if ($nr == 101) {
        return "MCM-L0"; // listing boek exception... samengesteld door manuel dus geen echte naam...
    }
    if ($nr == 102) {
        return "MCM-L1"; // listing boek exception
    }
    return "MCM-D" . (int)mcm_disknr($nr);
}

/**
 * Returns the filename of the disk for MCM nr
 *
 * @param $nr
 * @param $letter
 * @return string
 */
function msx_disk_filename($nr, $letter = '')
{
    if (is_mccm($nr)) {
        switch ($nr . $letter) {
            case '58e':
            case '75c':
            case '78c':
            case '79c':
            case '82c':
            case '84c':
            case '85c':
            case '87c':
            case '88c':
            case '88c':
            case '90f':
            case '90h':
                $extension = "di1";
                break;
            default:
                $extension = "di2";
                break;
        }
        return 'mccm/disk' . mcm_disknr($nr) . $letter . "." . $extension;
    }
    if ($nr == 101) { // note: gemaakt door Manuel, uit losse disks bij bladen
        return 'lb/Listingboek1.dsk'; // for listingboek 1
    }
    if ($nr == 102) { // note: geen idee waarom ie L1 heet...
        return 'lb/MCM-L1_MCM_Listingboekdiskette.dsk'; // for listingboek 2 (!)
    }
    $extension = "di1";
    if ($nr == 56) {
        $extension = "di2";
    }
    return 'mcm/mcmd' . mcm_disknr($nr) . "." . $extension;
}

/**
 * returns the page of the diskabo for MCCM
 *
 */
function mccm_diskabopag($nr)
{
    switch ($nr) {
        case 58:
            return 55;
        case 59:
            return 21;
        case 60:
            return 30;
        case 61:
            return 57;
        case 62:
            return 51;
        case 63:
            return 35;
        case 64:
            return 30;
        case 65:
            return 38;
        case 66:
            return 32;
        case 67:
            return 24;
        case 68:
            return 24;
        case 69:
            return 18;
        case 70:
            return 24;
        case 71:
            return 24;
        case 72:
            return 24;
        case 73:
            return 26;
        case 74:
            return 25;
        case 75:
            return 24;
        case 76:
            return 24;
        case 77:
            return 24;
        case 78:
            return 24;
        case 79:
            return 28;
        case 80:
            return 29;
        case 81:
            return 24;
        case 82:
            return 24;
        case 83:
            return 24;
        case 84:
            return 18;
        case 85:
            return 24;
        case 86:
            return 24;
        case 87:
            return 24;
        case 88:
            return 24;
        case 89:
            return 24;
        case 90:
            return 24;
        default:
            return 0;
    }
}


?>