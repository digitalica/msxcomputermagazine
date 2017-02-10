<?php
declare(strict_types = 1);

require_once('mcmlistings.php');

use PHPUnit_Framework_TestCase;

/**
 * tests the mcm listings (sanity check on the table)
 *
 * @covers mcmlistings
 */
final class mcmlistingstest extends PHPUnit_Framework_TestCase
{

    public function testlistings()
    {
        global $mcm_listings;

        $this->assertTrue(is_array($mcm_listings));
        $this->assertNotCount(0, $mcm_listings);
        $lastnr = 0;
        foreach ($mcm_listings as $listing) {
            $nr = $listing[0];
            $this->assertTrue(in_array(sizeof($listing), array(5, 6)));
            $this->assertTrue(is_numeric($nr));
            $this->assertLessThan($nr, $lastnr);
            $lastnr = $nr - 1;
            $pag = $listing[1];
            $this->assertTrue(is_numeric($pag));
            $filename = $listing[2];
            $this->assertTrue(is_string($filename));
            $this->assertNotEmpty($filename);
            $name = $listing[3];
            $this->assertTrue(is_string($name));
            $this->assertNotEmpty($name);
            $msx_version = $listing[4];
            $this->assertLessThan($msx_version, 0);
            $this->assertGreaterThan($msx_version, 4);
            if (sizeof($listing) > 5) { // optional
                $runtype = $listing[5];
                $this->assertTrue(in_array($runtype, array('X', 'B')));
            }
        }
    }
}