<?php
/**
*
* Board Notices extension for the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
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
	'ACP_BOARD_NOTICES_SETTINGS'			=> 'Board Notices Settings',
	'ACP_BOARD_NOTICES_SETTINGS_EXPLAIN'	=> 'General settings for all board notices',

	'ACP_BOARD_NOTICES_MANAGER'				=> 'Board Notices Manager',
	'ACP_BOARD_NOTICES_MANAGER_EXPLAIN'		=> "Add, edit or delete your board notices<br />"
											. "Please note that only one board notice can be displayed to the user at a time: It's the first one that fills the conditions that will be displayed.<br />",

	'ACP_BOARD_NOTICE_SETTINGS'				=> 'Board Notice Setup',
	'ACP_BOARD_NOTICE_SETTINGS_EXPLAIN'		=> 'Please fill-in the board notice information',

	'BOARD_NOTICES_SETTINGS_SAVED'			=> 'Board notices settings have been saved.',

	'ACP_BOARD_NOTICE_RULES'				=> 'Board Notice Conditions',
	'ACP_BOARD_NOTICE_RULES_EXPLAIN'		=> 'Edit the conditions for the message to be displayed. Click on the checkbox on the left to activate a condition.<br />Please note that <strong>all conditions should be met</strong>.',

	'LABEL_BOARD_NOTICES_ACTIVE'			=> 'Enable board notices',
	'BOARD_NOTICES_ACTIVE_EXPLAIN'			=> 'Here you can disable the display of all notices while keeping the extension enabled.',

	'LABEL_BOARD_NOTICE_DEFAULT_BGCOLOR'			=> 'Default background color of notices',
	'LABEL_BOARD_NOTICE_DEFAULT_BGCOLOR_EXPLAIN'	=> 'This is the default color of a background message when not specified per notice. If you don\'t set a value, <strong>no</strong> background color will be applied.',

	'L_FORUMS_VISITS'						=> 'Visits',
	'LABEL_FORUMS_VISITS_ACTIVE'			=> 'Keep track of the last time a user visited a forum',
	'FORUMS_VISITS_ACTIVE_EXPLAIN'			=> 'This enables or disables the tracking of last visits of each forum for each user. You need to activate it if you want to use the "<i>forum never visited</i>" or "<i>forum not visited for x days</i>" conditions.',

	'L_RESET_OPTIONS'						=> 'Reset visits',
	'L_RESET_FORUM_VISITS'					=> 'Reset last time each forum was visited',
	'L_RESET_FORUM_VISITS_EXPLAIN'			=> 'If you disable the forum visit track and re-enable it, some user will have a last visit time much earlier than the real time. It is recommended to reset the tracking after disabling and re-enabling forum visits tracking.',

	'BOARD_NOTICE_TITLE'					=> 'Board Notices',
	'BOARD_NOTICE_RULES'					=> 'Conditions',
	'BOARD_NOTICE_ADD'						=> 'Add new notice',
	'BOARD_NOTICE_RULE_ADD'					=> 'Add new rule',

	'COLUMN_CAN_DISMISS'					=> 'Can dismiss',

	'LABEL_BOARD_NOTICE_ACTIVE'				=> 'Board notice is enabled',
	'LABEL_BOARD_NOTICE_TITLE'				=> 'Board notice name',
	'LABEL_BOARD_NOTICE_DISMISSABLE'		=> 'Board notice can be dismissed',
	'LABEL_BOARD_NOTICE_DISMISS_EXPLAIN'	=> 'The user will have the option to click on a close button (which by default is a cross at the right corner of the notice) to hide the notice. It will come back depending on your configuration.',
	'LABEL_BOARD_NOTICE_RESET_AFTER'		=> 'Reset after',
	'LABEL_BOARD_NOTICE_RESET_EXPLAIN'		=> 'After this many days, the notice will be displayed back to the users who dismissed it. Keep the field blank if you never want it to reappear.',
	'LABEL_BOARD_NOTICE_PREVIEW'			=> 'Message preview',
	'LABEL_BOARD_NOTICE_TEXT'				=> 'Message displayed when conditions are met',
	'LABEL_BOARD_NOTICE_BGCOLOR'			=> 'Background color of the message',
	'LABEL_BOARD_NOTICE_BGCOLOR_EXPLAIN'	=> 'If not specified, the default color is going to be used. If the default color is empty, the background color will inherit the CSS style.',

	'LABEL_BOARD_NOTICE_STYLE'				=> '[Advanced users:] CSS class of the message container',
	'LABEL_BOARD_NOTICE_STYLE_EXPLAIN'		=> 'If you need more control on how the notice is displayed, you can specify a CSS class that will replace the default one. You can specify more than one class (separated by a space).',

	'L_INFORMATION'							=> 'Information',
	'VARIABLES_EXPLAIN'						=> 'You may include the following special variables in your text:<br />'
												. '{SESSIONID}, {USERID}, {USERNAME}, {LASTVISIT}, {LASTPOST}, {REGISTERED} and some other when you activate a particuliar rule. Check the rules to see which variables are available.',

	'BOARD_NOTICE_RULE_ACTIVE'				=> '',
	'BOARD_NOTICE_RULE_NAME'				=> 'Rule',
	'BOARD_NOTICE_RULE_VALUE'				=> 'Conditions',
	'BOARD_NOTICE_RULE_VARIABLES'			=> 'Providing',

	'NO_GUEST_OR_BOT'						=> 'No (Guest or Bot)',

	'BOARD_NOTICE_SAVED'					=> 'Board notice has been saved.',

	'ERROR_EMPTY_TITLE'						=> '<strong>Title</strong> cannot be empty.',
	'ERROR_EMPTY_MESSAGE'					=> '<strong>Message</strong> cannot be empty.',

	'MOVE_FIRST'							=> 'Move to first position',
	'MOVE_LAST'								=> 'Move to last position',

	// Rule names
	'RULE_ANNIVERSARY'						=> "On user's registration anniversary",
	'RULE_BIRTHDAY'							=> "On user's birthday",
	'RULE_DATE'								=> "The date is",
	'RULE_DATE_ANY'							=> 'Any',
	'RULE_DATE_RANGE_1'						=> 'The date is between',
	'RULE_DATE_RANGE_2'						=> '<br />and<br />',
	'RULE_HAS_NEVER_POSTED'					=> "User has never posted",
	'RULE_HAS_NEVER_POSTED_EXPLAIN'			=> "(takes into account posts waiting for approval)",
	'RULE_HAS_NEVER_POSTED_IN_FORUM'		=> "User has never posted in any of these forums",
	'RULE_HAS_NEVER_POSTED_FORUM_EXPLAIN'	=> "(including posts waiting for approval)",
	'RULE_HAS_NOT_POSTED_FOR_1'				=> "User has not posted for ",
	'RULE_HAS_NOT_POSTED_FOR_2'				=> " day(s) or more (but has posted previously)",
	'RULE_HAS_POSTED_EXACTLY'				=> "Number of visible posts equals",
	'RULE_HAS_POSTED_LESS'					=> "Number of visible posts equals or less than",
	'RULE_HAS_POSTED_MORE'					=> "Number of visible posts equals or more than",
	'RULE_HAS_POSTED_IN_FORUM'				=> "User has posted in at least one of these forums",
	'RULE_HAS_POSTED_IN_FORUM_EXPLAIN'		=> "(<strong>not</strong> including posts waiting for approval)",
	'RULE_HAS_NEVER_VISITED'				=> "User has never visited any of these forums",
	'RULE_HAS_NOT_VISITED_FOR_1'			=> "User has not visited any of these selected forums ",
	'RULE_HAS_NOT_VISITED_FOR_2'			=> " for at least ",
	'RULE_IN_DEFAULT_USERGROUP'				=> "Default user group is",
	'RULE_IN_FORUM'							=> "User is currently browsing one of these forum(s)",
	'RULE_IN_USERGROUP'						=> "User belongs to either one of these selected groups",
	'RULE_LANGUAGE'							=> "User language is either one of these selected languages",
	'RULE_LOGGED_IN'						=> "Is user logged in",
	'RULE_NOT_IN_USERGROUP'					=> "User does not belong to any of these selected groups",
	'RULE_ON_BOARD_INDEX'					=> "User is browsing the board index",
	'RULE_RANK'								=> "User rank is any of these selected ranks",
	'RULE_REGISTERED_LESS_THAN'				=> "User registration is less than",
	'RULE_STYLE'							=> "User style is either one of these selected styles",
	'RULE_REGISTERED_BEFORE'				=> "User registered before",
	'RULE_REGISTERED_AFTER'					=> "User registered after",
	'RULE_IN_TOPIC'							=> "User is currently browsing one of these topic(s)",
	'RULE_IN_TOPIC_EXPLAIN'					=> "(please input coma separated IDs of topics)",
	'RULE_RANDOM'							=> "Random - Chances to display the notice is one out of ...",

	// Rules units
	'RULE_DAY(S)'							=> 'day(s)',

	'RESET_FORUM_VISITS_SUCCESS'			=> 'The forum visit tracking data has been successfully cleaned.',
	'RESET_FORUM_VISITS_CONFIRMATION'		=> 'Are you sure you want to reset all the forum visit information?',
));
