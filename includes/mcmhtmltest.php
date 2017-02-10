<?php
declare(strict_types = 1);

require('mcmhtml.php');
require('mcmlistings.php');
require('mccmlistings.php');

/**
 * @covers mcmhtml
 */
final class mcmhtmltest extends PHPUnit_Framework_TestCase
{

    public function testHtml_mcm_disk()
    {
        for ($nr = 1; $nr < 58; $nr++) {
            $this->assertRegexp("/<div/", mcm_disk($nr, '', ''), "there should be a div open " . $nr);
            $this->assertRegexp("/<\/div>/", mcm_disk($nr, '', ''), "there should be a div close " . $nr);

            $this->assertRegexp("/MCM-D/", mcm_disk($nr, '', ''), "diskname " . $nr);
        }
    }

    public function testHtml_mccm_disk()
    {
        for ($nr = 58; $nr < 91; $nr++) {
            $this->assertRegexp("/<div/", mccm_disk($nr, '', ''), "there should be a div open " . $nr);
            $this->assertRegexp("/<\/div>/", mccm_disk($nr, '', ''), "there should be a div close " . $nr);

            $this->assertRegexp("/DA/", mccm_disk($nr, '', ''), "diskname " . $nr);
        }
    }


}

?>