![phpBB extension](https://img.shields.io/badge/phpBB-3.2-green.svg)
![License](https://img.shields.io/github/license/gouarfig/board-notices.svg?style=flat)
[![Release](https://img.shields.io/github/release-pre/gouarfig/board-notices.svg?style=flat)](https://github.com/gouarfig/board-notices/releases)
[![Release](https://img.shields.io/github/release-date-pre/gouarfig/board-notices.svg?style=flat)](https://github.com/gouarfig/board-notices/releases)
![Last commit](https://img.shields.io/github/last-commit/gouarfig/board-notices.svg?style=flat)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=gouarfig_board-notices&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=gouarfig_board-notices)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=gouarfig_board-notices&metric=security_rating)](https://sonarcloud.io/dashboard?id=gouarfig_board-notices)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=gouarfig_board-notices&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=gouarfig_board-notices)

# Board Notices Manager for phpBB 3.2

This extension will help you manage various notices at the top of all the pages.

This is not the same as an announcement. An announcement is global, whereas notices are contextual.

A few examples:
*    Display a message to guests asking them to register
*    Display a happy birthday message on your birthday.
*    Display a welcoming message asking you to write an introduction if you never posted yet.
*    Congratulations for your 1,000th post
*    Congratulations for your 5th year registration anniversary
*    Some warning that you're browsing a specific forum (like a read-only section)
*    etc.

**Please note this extension is still at the development stage, and is probably not ready for production**, although I've been using it on my board for a few years without any issue.

## Installation notes

The easiest way is to clone into phpBB/ext/fq/boardnotices:

    git clone https://github.com/gouarfig/board-notices.git phpBB/ext/fq/boardnotices

Go to "ACP" > "Customise" > "Extensions" and enable the "Board Notices Manager" extension.

## License
[GPLv2](license.txt)
