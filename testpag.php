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

$issue = $_GET['issue'];
if ($issue<0 || $issue>102) {
    $issue = 1;
}
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
for ($i=1; $i<92; $i++) {
    echo "<a href='?issue=$i'>$i</a> ";
}
echo "<a href='?issue=101'>101</a> ";
echo "<a href='?issue=102'>102</a> ";



?>

<br clear="all">
<br clear="all">

<?php

echo shortcode_pdf($attr);

echo shortcode_disk($attr);

echo shortcode_listings($attr);

echo shortcode_info($attr);

?>

</body>
</html>
