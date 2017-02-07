<?php
/**
 * Created by PhpStorm.
 * User: robbert
 * Date: 2-2-17
 * Time: 18:46
 */

define('ABSPATH','');

require('msxcomputermagazine.php');


$issue = 34;

$attr = array(
    'mcm' => $issue
);

function plugin_dir_path($var) {
    return dirname($var).'/';
}

function add_shortcode() {

}

$title = "nr. $issue - test";
function get_the_title() {
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

echo mcm_pdf($attr);

echo mcm_disk($attr);

echo mcm_listings($attr);

?>

</body>
</html>
