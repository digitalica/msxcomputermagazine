## Synopsis

This Wordpress plugin has the data for the links to PDFs and (web)MSX emulator for disk images and listings.
It creates some shortcodes to be used in Wordpress.

## Code Example

[pdf mcm=3]

[disk mcm=3]

[listings mcm=3]

## Installation

As any Wordpress plugin

## translation

update POT file

xgettext --from-code=UTF-8 -o languages/msxcomputermagazine.pot *.php

generate MO files

msgfmt nl/LC_MESSAGES/msxcomputermagazine.po -o nl/LC_MESSAGES/msxcomputermagazine.mo
msgfmt en/LC_MESSAGES/msxcomputermagazine.po -o en/LC_MESSAGES/msxcomputermagazine.mo


## Tests

phpunit mcmlistingstest.php

phpunit mcmutilstest.php

## Contributors

Hayo Rubingh, Manuel Bilderbeek, Robbert Wethmar

## License

GPL

## TODO
- fix situations where one file loads another (MCM 9, like portet Wammes)
- maybe start all disk links in MSX 2+?
- make 'extra' stuff (pag 0) show in separate section
- add auto-update feature