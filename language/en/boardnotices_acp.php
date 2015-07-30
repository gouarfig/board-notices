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
	'ACP_BOARD_NOTICES_MANAGER'				=> 'Board Notices manager',
	'ACP_BOARD_NOTICES_MANAGER_EXPLAIN'		=> "Add, edit or delete your board notices<br />"
											. "Please note that only one board notice can be displayed to the user at a time: It's the first one that fills the conditions that will be displayed.<br />",

	'ACP_BOARD_NOTICE_SETTINGS'				=> 'Board Notice settings',
	'ACP_BOARD_NOTICE_SETTINGS_EXPLAIN'		=> 'Please fill-in the board notice information',

	'ACP_BOARD_NOTICE_RULES'				=> 'Board Notice conditions',
	'ACP_BOARD_NOTICE_RULES_EXPLAIN'		=> 'Edit the conditions for the message to be displayed. Please note that <strong>all conditions should be met</strong>.',

	'BOARD_NOTICE_TITLE'					=> 'Board Notices',
	'BOARD_NOTICE_RULES'					=> 'Conditions',
	'BOARD_NOTICE_ADD'						=> 'Add new notice',
	'BOARD_NOTICE_RULE_ADD'					=> 'Add new rule',

	'LABEL_BOARD_NOTICE_ACTIVE'				=> 'Board notice is enabled',
	'LABEL_BOARD_NOTICE_TITLE'				=> 'Board notice name',
	'LABEL_BOARD_NOTICE_PREVIEW'			=> 'Message preview',
	'LABEL_BOARD_NOTICE_TEXT'				=> 'Message displayed when conditions are met',
	'LABEL_BOARD_NOTICE_BGCOLOR'			=> 'Background color of the message',

	'L_INFORMATION'							=> 'Information',
	'VARIABLES_EXPLAIN'						=> 'You may include the following special variables in your text:<br />'
												. '{SESSIONID}, {USERID}, {USERNAME}, {LASTVISIT}, {LASTPOST}, {REGISTERED} and some other when you activate a particuliar rule. Check the rules to see which variables are available.',

	'BOARD_NOTICE_RULE_NAME'				=> 'Rule',
	'BOARD_NOTICE_RULE_VALUE'				=> 'Conditions',
	'BOARD_NOTICE_RULE_VARIABLES'			=> 'Providing',

	'BOARD_NOTICE_SAVED'					=> 'Board notice has been saved.',

	'ERROR_EMPTY_TITLE'						=> '<strong>Title</strong> cannot be empty.',
	'ERROR_EMPTY_MESSAGE'					=> '<strong>Message</strong> cannot be empty.',

	'MOVE_FIRST'							=> 'Move to first position',
	'MOVE_LAST'								=> 'Move to last position',
));
