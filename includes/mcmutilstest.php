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
        $this->assertEquals(mcm_nr_from_pagename("nr. 1 - feb 1985"), "01");
        $this->assertEquals(mcm_nr_from_pagename("nr. 4 - aug 1985"), "04");
        $this->assertEquals(mcm_nr_from_pagename("nr. 19 - feb 1985"), "19");
        $this->assertEquals(mcm_nr_from_pagename("nr. 49 - aug 1985"), "49");

        $this->assertEquals(mcm_nr_from_pagename(""), 0);
        $this->assertEquals(mcm_nr_from_pagename("xxxxxxx"), 0);
        $this->assertEquals(mcm_nr_from_pagename("123456"), 0);
    }

    public function testUtil_mcm_disknr()
    {
        $this->assertEquals(mcm_disknr(1), "01");
        $this->assertEquals(mcm_disknr(2), "01");
        $this->assertEquals(mcm_disknr(23), "22");
    }


    public function testUtil_mcm_pdfbasename()
    {
        $this->assertEquals(mcm_pdfbasename(1), "msx_computer_magazine_");
        $this->assertEquals(mcm_pdfbasename(21), "msx_computer_magazine_");
        $this->assertEquals(mcm_pdfbasename(22), "ms%28x%29dos_computer_magazine_");
        $this->assertEquals(mcm_pdfbasename(35), "ms%28x%29dos_computer_magazine_");
        $this->assertEquals(mcm_pdfbasename(36), "msx_computer_magazine_");
        $this->assertEquals(mcm_pdfbasename(57), "msx_computer_magazine_");
        $this->assertEquals(mcm_pdfbasename(58), "msx_computer_club_magazine_");
        $this->assertEquals(mcm_pdfbasename(90), "msx_computer_club_magazine_");
    }


}

?>