phpunit includes
echo
echo ================================================
echo
rm -f msxcomputermagazine.zip
zip msxcomputermagazine.zip msxcomputermagazine.php
zip msxcomputermagazine.zip includes/msxmagutils.php
zip msxcomputermagazine.zip includes/msxmaghtml.php
zip msxcomputermagazine.zip includes/mcmlistings.php
zip msxcomputermagazine.zip includes/mccmlistings.php
zip msxcomputermagazine.zip languages/en/LC_MESSAGES/msxcomputermagazine.mo
zip msxcomputermagazine.zip languages/nl/LC_MESSAGES/msxcomputermagazine.mo
unzip -l msxcomputermagazine.zip