<?php
declare(strict_types = 1);

require_once('msxmagutils.php');

/**
 * @covers mcmutils
 */
final class mcmutilstest extends PHPUnit\Framework\TestCase
{

    public function testUtil_mcm_nr_from_pagename()
    {
        $this->assertEquals("01", mcm_nr_from_pagename("nr. 1 - feb 1985"));
        $this->assertEquals("02", mcm_nr_from_pagename("nr. 2 – apr/mei 1985"));
        $this->assertEquals("02", mcm_nr_from_pagename("nr. 2 – apr/mei 1985"));
        $this->assertEquals("04", mcm_nr_from_pagename("nr. 4 - aug 1985"));
        $this->assertEquals("07", mcm_nr_from_pagename("nr. 7 – mrt 1986"));
        $this->assertEquals("19", mcm_nr_from_pagename("nr. 19 - feb 1985"));
        $this->assertEquals("49", mcm_nr_from_pagename("nr. 49 - aug 1985"));

        $this->assertEquals("49", mcm_nr_from_pagename("nr. 49 - augxxxx 1985"));
        $this->assertEquals("02", mcm_nr_from_pagename("nr. 2 - apr/mei 1985"));

        $this->assertEquals("23", mcm_nr_from_pagename("nr. 23 – jun 1988 – MSX Computer Magazine"));

        $this->assertEquals(91, mcm_nr_from_pagename("nr. 90 cd")); // voor: http://www.msxcomputermagazine.nl/archief/mccm-90cd/

        $this->assertEquals("101", mcm_nr_from_pagename("listingboek 1"));
        $this->assertEquals("102", mcm_nr_from_pagename("listingboek 2"));
        $this->assertEquals("101", mcm_nr_from_pagename("Listingboek 1"));
        $this->assertEquals("102", mcm_nr_from_pagename("Listingboek 2"));

        $this->assertEquals(0, mcm_nr_from_pagename(""));
        $this->assertEquals(0, mcm_nr_from_pagename("xxxxxxx"));
        $this->assertEquals(0, mcm_nr_from_pagename("123456"));
    }

    public function testIs_listingboek()
    {
        $this->assertEquals(false, is_listingboek(0));
        $this->assertEquals(false, is_listingboek(1));
        $this->assertEquals(false, is_listingboek(100));
        $this->assertEquals(false, is_listingboek(103));
        $this->assertEquals(false, is_listingboek(999));

        $this->assertEquals(true, is_listingboek(101));
        $this->assertEquals(true, is_listingboek(102));
    }

    public function testIs_magazine()
    {
        $this->assertEquals(false, is_magazine(0));
        $this->assertEquals(false, is_magazine(91));
        $this->assertEquals(false, is_magazine(100));
        $this->assertEquals(false, is_magazine(101));
        $this->assertEquals(false, is_magazine(102));
        $this->assertEquals(false, is_magazine(999));

        $this->assertEquals(true, is_magazine(1));
        $this->assertEquals(true, is_magazine(27));
        $this->assertEquals(true, is_magazine(90));
    }

    public function testIs_mccm()
    {
        $this->assertEquals(false, is_mccm(0));
        $this->assertEquals(false, is_mccm(33));
        $this->assertEquals(false, is_mccm(57));
        $this->assertEquals(false, is_mccm(92));
        $this->assertEquals(false, is_mccm(101));
        $this->assertEquals(false, is_mccm(102));
        $this->assertEquals(false, is_mccm(999));

        $this->assertEquals(true, is_mccm(58));
        $this->assertEquals(true, is_mccm(74));
        $this->assertEquals(true, is_mccm(90));
        $this->assertEquals(true, is_mccm(91)); // special case: extension of 90, in pdf on cd only.
    }

    public function testIs_pdf_available()
    {
        $this->assertEquals(false, is_pdf_available(0));
        $this->assertEquals(false, is_pdf_available(92));
        $this->assertEquals(false, is_pdf_available(100));
        $this->assertEquals(false, is_pdf_available(103));
        $this->assertEquals(false, is_pdf_available(999));

        $this->assertEquals(true, is_pdf_available(1));
        $this->assertEquals(true, is_pdf_available(27));
        $this->assertEquals(true, is_pdf_available(90));
        $this->assertEquals(true, is_pdf_available(91)); // special case: pdf van laatste cd
        $this->assertEquals(true, is_pdf_available(101));
        $this->assertEquals(true, is_pdf_available(102));
    }

    public function testIs_disk_available()
    {
        $this->assertEquals(false, is_disk_available(0));
        $this->assertEquals(false, is_disk_available(91));
        $this->assertEquals(false, is_disk_available(100));
        $this->assertEquals(false, is_disk_available(103));
        $this->assertEquals(false, is_disk_available(999));

        $this->assertEquals(true, is_disk_available(1));
        $this->assertEquals(true, is_disk_available(34));
        $this->assertEquals(true, is_disk_available(57));
        $this->assertEquals(true, is_disk_available(58)); // mccm
        $this->assertEquals(true, is_disk_available(82)); // mccm
        $this->assertEquals(true, is_disk_available(90)); // mccm
        $this->assertEquals(true, is_disk_available(101)); // listingboek 1
        $this->assertEquals(true, is_disk_available(102)); // listingboek 2
    }

    public function testUtil_mcm_disknr()
    {
        $this->assertEquals("01", mcm_disknr(1));
        $this->assertEquals("01", mcm_disknr(2));
        $this->assertEquals("22", mcm_disknr(23));
        $this->assertEquals("53", mcm_disknr(54));
        $this->assertEquals("55", mcm_disknr(55));
        $this->assertEquals("55", mcm_disknr(56));

        // for mccm disknrs are equal
        $this->assertEquals("58", mcm_disknr(58));
        $this->assertEquals("72", mcm_disknr(72));
        $this->assertEquals("90", mcm_disknr(90));
    }

    public function testUtil_mcm_pdfbasename()
    {
        $this->assertEquals("msx_computer_magazine_", mcm_pdfbasename(1));
        $this->assertEquals("msx_computer_magazine_", mcm_pdfbasename(21));
        $this->assertEquals("ms%28x%29dos_computer_magazine_", mcm_pdfbasename(22));
        $this->assertEquals("ms%28x%29dos_computer_magazine_", mcm_pdfbasename(35));
        $this->assertEquals("msx_computer_magazine_", mcm_pdfbasename(36));
        $this->assertEquals("msx_computer_magazine_", mcm_pdfbasename(57));
        $this->assertEquals("msx_computer_club_magazine_", mcm_pdfbasename(58));
        $this->assertEquals("msx_computer_club_magazine_", mcm_pdfbasename(90));

        $this->assertEquals("msx_computer_club_magazine_90_cd", mcm_pdfbasename(91));

        $this->assertEquals("mcm_listing_boek_1", mcm_pdfbasename(101));
        $this->assertEquals("mcm_listing_boek_2", mcm_pdfbasename(102));

        $this->assertEquals("UNKNOWNPDF", mcm_pdfbasename(0));
        $this->assertEquals("UNKNOWNPDF", mcm_pdfbasename(92));
        $this->assertEquals("UNKNOWNPDF", mcm_pdfbasename(100));
        $this->assertEquals("UNKNOWNPDF", mcm_pdfbasename(103));
        $this->assertEquals("UNKNOWNPDF", mcm_pdfbasename(999));
    }

    public function testUtil_magazine_name()
    {
        $this->assertEquals("MSX Computer Magazine 1", magazine_name(1));
        $this->assertEquals("MSX Computer Magazine 21", magazine_name(21));

        $this->assertEquals("MS(X)DOS Computer Magazine 22", magazine_name(22));
        $this->assertEquals("MS(X)DOS Computer Magazine 35", magazine_name(35));

        $this->assertEquals("MSX Computer Magazine 36", magazine_name(36));
        $this->assertEquals("MSX Computer Magazine 57", magazine_name(57));

        $this->assertEquals("MSX Computer Club Magazine 58", magazine_name(58));
        $this->assertEquals("MSX Computer Club Magazine 90", magazine_name(90));

        $this->assertEquals("MCM listingboek 1", magazine_name(101));
        $this->assertEquals("MCM listingboek 2", magazine_name(102));
    }

    public function testUtil_disk_file_name() {
        $this->assertEquals("mcm/mcmd01.di1", msx_disk_filename(2));
        $this->assertEquals("mcm/mcmd20.di1", msx_disk_filename(21));
        $this->assertEquals("mcm/mcmd55.di2", msx_disk_filename(56)); // dubbelzijdig


        // for mccm
        $this->assertEquals("mccm/disk58a.di2", msx_disk_filename(58,'a'));
        $this->assertEquals("mccm/disk58b.di2", msx_disk_filename(58,'b'));
        $this->assertEquals("mccm/disk58e.di1", msx_disk_filename(58,'e')); // single sided
    }

    public function testUtil_mcm_nr_from_attr_or_pagename() {
        $this->assertEquals(0, mcm_nr_from_attr_or_pagename("string", "somepagename"));
        $this->assertEquals(0, mcm_nr_from_attr_or_pagename(array(1,2,3), "somepagename"));

        $none = array();
        $mcmonly23 = array('mcm' => 23);
        $mccmonly73 = array('mccm' => 73);
        $both2373 = array('mcm' => 23, 'mccm' => 73);

        $mcmonly31 = array('mcm' => 31);
        $mccmonly62 = array('mccm' => 62);
        $both3162 = array('mcm' => 31, 'mccm' => 62);

        $this->assertEquals(0, mcm_nr_from_attr_or_pagename($none, "somepagename"));
        $this->assertEquals(23, mcm_nr_from_attr_or_pagename($mcmonly23, "somepagename"));
        $this->assertEquals(73, mcm_nr_from_attr_or_pagename($mccmonly73, "somepagename"));
        $this->assertEquals(23, mcm_nr_from_attr_or_pagename($both2373, "somepagename"));

        $this->assertEquals(31, mcm_nr_from_attr_or_pagename($mcmonly31, "somepagename"));
        $this->assertEquals(62, mcm_nr_from_attr_or_pagename($mccmonly62, "somepagename"));
        $this->assertEquals(31, mcm_nr_from_attr_or_pagename($both3162, "somepagename"));

    }


}

?>