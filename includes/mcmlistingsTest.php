<?php
declare(strict_types = 1);

require_once('mcmlistings.php');

/**
 * tests the mcm listings (sanity check on the table)
 *
 * @covers mcmlistings
 */
final class mcmlistingstest extends PHPUnit\Framework\TestCase
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
            if ($filename == 'icp7.bin' || $filename == 'icp7b') {
                $this->assertEquals(2, $msx_version, "icp7 must be for MSX2, to allow typing MSX 2 listings (and prevert date prompt) for mcm " . $nr);
                $this->assertEquals('B', $listing[5], "icp7 must be bloaded for mcm " . $nr);
            }

        }
    }

    public function testkk()
    {
        global $mcm_listings;

        foreach ($mcm_listings as $listing) {
            $filename = strtolower($listing[2]);
            if (strpos($filename, 'k&k') === 0 || strpos($filename, 'kk') === 0 ) {
                $this->assertTrue($filename == $listing[2], "filename k&k should be lowercase: " . $listing[2]); // filename should be lowercase
                $name = $listing[3];
                $nameprefix = strtoupper($filename) . ': ';
                $nameprefix = str_replace('KK', 'K&K', $nameprefix); // met ampersant graag
                $this->assertTrue(strpos($name, $nameprefix) === 0, "name k&k should start with [" . $nameprefix . "] for [" . $name . ']');
            }
        }
    }

    public function testlistingboek1()
    {
        global $mcm_listings;
        foreach ($mcm_listings as $lblisting) {
            $lnnr = $lblisting[0];
            if ($lnnr == 101 || $lnnr == 102) {
                $lbfile = $lblisting[2];
                foreach ($mcm_listings as $bladlisting) {
                    $bladnr = $bladlisting[0];
                    $bladfile = $bladlisting[2];
                    if ($bladnr < 10 && $bladfile == $lbfile) {
                        $lbdesc = $lblisting[3];
                        $bladdesc = $bladlisting[3];
                        $lb = $lbfile . ' / ' . $lbdesc;
                        $blad = $bladfile . ' / ' . $bladdesc;
                        $this->assertEquals($blad, $lb, "\nLB: $lb\nBLAD: $blad\n");
                    }
                }
            }
        }
    }


}