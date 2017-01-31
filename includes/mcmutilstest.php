<?php
declare(strict_types = 1);

require('mcmutils.php');

use PHPUnit_Framework_TestCase;

/**
 * @covers mcmutils
 */
final class mcmutilstest extends PHPUnit_Framework_TestCase
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

        $this->assertEquals(0, mcm_nr_from_pagename(""));
        $this->assertEquals(0, mcm_nr_from_pagename("xxxxxxx"));
        $this->assertEquals(0, mcm_nr_from_pagename("123456"));
    }

    public function testUtil_mcm_disknr()
    {
        $this->assertEquals("01", mcm_disknr(1));
        $this->assertEquals("01", mcm_disknr(2));
        $this->assertEquals("22", mcm_disknr(23));
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
    }


}

?>