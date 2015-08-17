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
	// ACP Module
	'ACP_BOARD_NOTICES'				=> 'Board Notices',
	'ACP_BOARD_NOTICES_SETTINGS'	=> 'Settings',
	'ACP_BOARD_NOTICES_MANAGE'		=> 'Manage Notices',

	// ACP Logs
	'LOG_BOARD_NOTICE_ERROR'		=> '<strong>Error while creating board notice</strong><br />» Error in %1$s on line %2$s: %3$s',
	'LOG_BOARD_NOTICES_SETTINGS'	=> "<strong>Board notices settings updated</strong>",
	'LOG_BOARD_NOTICES_ADDED'		=> "<strong>Board notice '%s' added</strong>",
	'LOG_BOARD_NOTICES_UPDATED'		=> "<strong>Board notice '%s' updated</strong>",
));
