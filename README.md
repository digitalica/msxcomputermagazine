## Synopsis

This Wordpress plugin has the data for the links to PDFs and (web)MSX emulator WebMSX for disk images and listings.
It creates some shortcodes in Wordpress to be used in the pages for each issue.

## Requirements

phpunit

```bash
sudo apt get phpunit
```

## Code Example

[pdf mcm=3]

[disk mcm=3]

[listings mcm=3]

[info mcm=3]


## Numbers

In the shortcodes above, the number of the magazine can be left out. The plugin
wil take the number from the page title automatically. The numbers are the numbers of the
magazine, the numbering of disk is different. (for MCM 1 less, for MCCM multiple disks per issue)

Special Numbers:
- 91: The PDF with extra pages for MCCM 90, the last issue.
- 101: MCM Listingboek 1
- 102: MCM Listingboek 2


## Files

This plugin requires the files to be available in the configured folders.
As there are many (large) files, we didn't make them part of the plugin itself.

To start single programs, the plugin expects ZIP files containing all files of 
the disks, where autoexec.bas is renamed to prevent automatic execution by WebMSX.
This allows programs that require other files to run as well. And opens the option
for users just browse the disk after a program has run.

Ideally renaming the autoexec.bas should not be needed, WebMSX is working on this.
then the zips wont be needed any more and the normal disk images can be used.
See https://github.com/ppeccin/WebMSX/issues/9

## Deploy and Installation

- update version nr in msxcomputermagazine.php, line 5
- run package.sh (this creates the zip)
- deploy msxcomputermagazine.zip, as any Wordpress plugin


## translation

update POT file

xgettext --from-code=UTF-8 -o languages/msxcomputermagazine.pot *.php

generate MO files

msgfmt nl/LC_MESSAGES/msxcomputermagazine.po -o nl/LC_MESSAGES/msxcomputermagazine.mo
msgfmt en/LC_MESSAGES/msxcomputermagazine.po -o en/LC_MESSAGES/msxcomputermagazine.mo


## Tests

phpunit includes/


## Contributors

Hayo Rubingh, Manuel Bilderbeek, Robbert Wethmar


## License

GPL


## TODO
- add auto-update feature (we now use github updater plugin)
