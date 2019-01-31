<?php
/**
*
* Board Notices extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
* @traduzione a cura di twm49 (https://lnx.3rotaie.it/forum)
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
	'ACP_BOARD_NOTICES'				=> 'Notizie dal Forum',
	'ACP_BOARD_NOTICES_SETTINGS'	=> 'Configurazione',
	'ACP_BOARD_NOTICES_MANAGE'		=> 'Gestione Notizie dal Forum',

	// ACP Logs
	'LOG_BOARD_NOTICE_ERROR'		=> '<strong>Errore durante la creazione della Notizia dal Forum</strong><br />» Errore  %1$s alla linea %2$s: %3$s',
	'LOG_BOARD_NOTICES_SETTINGS'	=> "<strong>Configurazione Notizie dal Forum aggiornata </strong>",
	'LOG_BOARD_NOTICES_ADDED'		=> "<strong>Aggiunta la Notizia dal Forum '%s' </strong>",
	'LOG_BOARD_NOTICES_UPDATED'		=> "<strong>Aggiornata la Notizia dal Forum '%s' </strong>",
));
