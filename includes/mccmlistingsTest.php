<?php
declare(strict_types = 1);

require('mccmlistings.php');

/**
 *
 * tests the mccm listings (sanity check on the table)
 *
 * @covers mccmlistings
 */
final class mccmlistingstest extends PHPUnit_Framework_TestCase
{

    public function testlistings()
    {
        global $mccm_listings;

        $this->assertTrue(is_array($mccm_listings));
        $this->assertNotCount(0, $mccm_listings);
        $lastnr = 0;
        foreach ($mccm_listings as $listing) {
            $nr = $listing[0];
            $this->assertTrue(is_numeric($nr));
            $this->assertLessThan($nr, $lastnr);
            $lastnr = $nr - 1;
            $letter = $listing[1];
            $this->assertEquals(1, strlen($letter));
            $this->assertEquals(strtolower($letter), $letter);
            $pag = $listing[2];
            $this->assertTrue(is_numeric($pag));
            $filename = $listing[3];
            $this->assertTrue(is_string($filename));
            $this->assertNotEmpty($filename);
            $name = $listing[4];
            $this->assertTrue(is_string($name));
            $this->assertNotEmpty($name);
            $msx_version = $listing[5];
            $this->assertLessThan($msx_version, 0);
            $this->assertGreaterThan($msx_version, 4);
        }
    }
}