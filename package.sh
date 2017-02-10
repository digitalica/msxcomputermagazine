phpunit includes
echo
echo ================================================
echo
rm -f msxcomputermagazine.zip
zip msxcomputermagazine.zip msxcomputermagazine.php
zip msxcomputermagazine.zip includes/mcmutils.php
zip msxcomputermagazine.zip includes/mcmhtml.php
zip msxcomputermagazine.zip includes/mcmlistings.php
zip msxcomputermagazine.zip includes/mccmlistings.php
unzip -l msxcomputermagazine.zip