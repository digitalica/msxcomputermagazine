<?php
declare(strict_types = 1);

require_once('msxmaghtml.php');
require_once('mcmlistings.php');
require_once('mccmlistings.php');

/**
 * @covers mcmhtml
 */
final class mcmhtmltest extends PHPUnit\Framework\TestCase
{


    public function testMsxmag_pdf_url()
    {
        $this->assertRegexp("/_01\.pdf/", msxmag_pdf_url(1,1), "there should be a prepending 0 for numbers below 10");
        $this->assertRegexp("/page=1/", msxmag_pdf_url(1,1), "there should be a correct pagenr for numbers below 10");

        $this->assertRegexp("/_01\.pdf/", msxmag_pdf_url("1","1"), "there should be a prepending 0 for numbers below 10, even if string");
        $this->assertRegexp("/_01\.pdf/", msxmag_pdf_url("01","01"), "there should be a prepending 0 for numbers below 10, even if string");

        $this->assertRegexp("/_12\.pdf/", msxmag_pdf_url(12,12), "there should be no prepending 0 for numbers above 10");
        $this->assertRegexp("/page=12/", msxmag_pdf_url(12,12), "there should be a correct pagenr for numbers above 10");

        $this->assertRegexp("/_34\.pdf/", msxmag_pdf_url(34,34), "there should be no prepending 0 for numbers above 10");
        $this->assertRegexp("/page=34/", msxmag_pdf_url(34,34), "there should be a correct pagenr for numbers above 10");

        $this->assertRegexp("/_01\.pdf/", msxmag_pdf_url("1","1"), "there should be a prepending 0 for numbers below 10, even if string");
        $this->assertRegexp("/_01\.pdf/", msxmag_pdf_url("01","01"), "there should be a prepending 0 for numbers below 10, even if string");
        $this->assertRegexp("/_21\.pdf/", msxmag_pdf_url("21","21"), "there should be no prepending 0 for numbers above 10, even if string");
        $this->assertRegexp("/_44\.pdf/", msxmag_pdf_url("44","44"), "there should be no prepending 0 for numbers above 10, even if string");
    }


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