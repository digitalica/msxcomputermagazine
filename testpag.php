<?php
/**
 * Created by PhpStorm.
 * User: robbert
 * Date: 2-2-17
 * Time: 18:46
 */

define('ABSPATH', '');

require('msxcomputermagazine.php');


// note: in issue below, 101 is listingboek 1, 102 is listingboek 2
$issue = 88;
$title = "nr. $issue - test";

$attr = array(
    'mcm' => $issue
);

$post = new stdClass();
$post->ID = ''; // no page id for now

function plugin_dir_path($var)
{
    return dirname($var) . '/';
}

function add_shortcode()
{

}

function get_the_title()
{
    global $title;

    return $title;
}


?>
<html>
<head>
    <title><?php echo $title ?></title>
</head>
<body>
<h1>testpag: MCM <?php echo $issue ?></h1>

<?php

echo shortcode_pdf($attr);

echo shortcode_disk($attr);

echo shortcode_listings($attr);

echo shortcode_info($attr);

?>

</body>
</html>
