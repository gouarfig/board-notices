[![Build Status](https://travis-ci.org/gouarfig/board-notices.svg)](https://travis-ci.org/gouarfig/board-notices)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/4836ae0c5386422eb43a18d301b7119e)](https://www.codacy.com/app/gouarfig/board-notices?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=gouarfig/board-notices&amp;utm_campaign=Badge_Grade)

# Board Notices Manager for phpBB 3.2

This extension will help you manage various notices at the top of all the pages.

This is not the same as an announcement. An announcement is global, whereas notices are contextual.

A few examples:
  * Display a message to guests asking them to register
  * Display a happy birthday message on your birthday.
  * Display a welcoming message asking you to write an introduction if you never posted yet.
  * Congratulations for your 1,000th post
  * Congratulations for your 5th year registration anniversary
  * Some warning that you're browsing a specific forum (like a read-only section)
  * etc.

**Please note this extension is still at the development stage, and is probably not ready for production**, although I've been using it on my board for a few years without any issue.

## Installation notes

The easiest way is to clone into phpBB/ext/fq/boardnotices:

    git clone https://github.com/gouarfig/board-notices.git phpBB/ext/fq/boardnotices

Go to "ACP" > "Customise" > "Extensions" and enable the "Board Notices Manager" extension.

## License
[GPLv2](license.txt)
