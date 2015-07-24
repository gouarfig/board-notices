<?php
/**
*
* Board Notices extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'L_ACP_BOARD_NOTICES_MANAGER'			=> 'Board Notices manager',
	'L_ACP_BOARD_NOTICES_MANAGER_EXPLAIN'	=> "Board Notices manager: Add, Edit or Delete your notices<br />Please note that only one notice will be displayed at a time. It's the first one that fills the conditions that will be displayed.",
	
	'L_BOARD_NOTICES_SETTINGS'				=> 'Board Notice settings',
	'L_BOARD_NOTICES_SETTINGS_EXPLAIN'		=> 'Please fill-in the notice information',
	
	'L_TITLE'								=> 'Board Notices',
	'L_ADD'									=> 'Add new notice',
	
	'BOARD_NOTICE_SAVED'					=> 'Board notice has been saved.',
	
	'ERROR_EMPTY_TITLE'						=> '<strong>Title</strong> cannot be empty.',
	'ERROR_EMPTY_MESSAGE'					=> '<strong>Message</strong> cannot be empty.',
));
