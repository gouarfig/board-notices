<?php
/**
*
* Board Notices extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* Estonian translation by phpBBestonia.eu <https://www.phpbbestonia.eu>
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
	'ACP_BOARD_NOTICES_SETTINGS'			=> 'Foorumi teadete seaded',
	'ACP_BOARD_NOTICES_SETTINGS_EXPLAIN'	=> 'Kõikide foorumiteadete üldised seaded',

	'ACP_BOARD_NOTICES_MANAGER'				=> 'Foorumi teatiste haldur',
	'ACP_BOARD_NOTICES_MANAGER_EXPLAIN'		=> "Lisage, redigeerige või kustutage oma foorumi teateid<br />"
											. "Pange tähele, et korraga saab kasutajale kuvada ainult ühte foorumi teadet: See on esimene, mis täidab kuvatavad tingimused.<br />",

	'ACP_BOARD_NOTICE_SETTINGS'				=> 'Foorumi teate seaded',
	'ACP_BOARD_NOTICE_SETTINGS_EXPLAIN'		=> 'Palun täitke foorumi teate informatsioon',

	'BOARD_NOTICES_SETTINGS_SAVED'			=> 'Foorumi teadete seaded on salvestatud.',

	'ACP_BOARD_NOTICE_RULES'				=> 'Foorumi teatise tingimused',
	'ACP_BOARD_NOTICE_RULES_EXPLAIN'		=> 'Muuda teate kuvamise tingimusi. Pange tähele, et <strong>kõik tingimused peavad olema täidetud</strong>.',

	'LABEL_BOARD_NOTICES_ACTIVE'			=> 'Luba foorumi teated',
	'BOARD_NOTICES_ACTIVE_EXPLAIN'			=> 'Siin saate keelata kõigi teadete kuvamise ja samal ajal säilitades laienduse aktiivsena.',

	'L_FORUMS_VISITS'						=> 'Külastusi',
	'LABEL_FORUMS_VISITS_ACTIVE'			=> 'Jälgige kuna kasutaja külastas viimati foorumit',
	'FORUMS_VISITS_ACTIVE_EXPLAIN'			=> 'See võimaldab või keelab kasutaja viimase külastuse jälgimise. Te peate selle aktiveerima, kui soovite kasutada "<i>foorumi, mida pole külastatud x päeva</i>" tingimuse jaoks.',

	'L_RESET_OPTIONS'						=> 'Lähtesta külastused',
	'L_RESET_FORUM_VISITS'					=> 'Lähtesta viimane aeg, kui foorumit külastatid',
	'L_RESET_FORUM_VISITS_EXPLAIN'			=> 'Kui te foorumi külastuse keelate ja selle uuesti sisse lülitate, on mõnel kasutajal viimane külastusaeg palju varem kui reaalajas. Soovitatav on lähtestada jälgimine pärast foorumi külastuste jälgimise keelamist ja uuesti lubamist.',

	'BOARD_NOTICE_TITLE'					=> 'Foorumi teated',
	'BOARD_NOTICE_RULES'					=> 'Tingimused',
	'BOARD_NOTICE_ADD'						=> 'Lisa uus teade',
	'BOARD_NOTICE_RULE_ADD'					=> 'Lisa uus reegel',

	'LABEL_BOARD_NOTICE_ACTIVE'				=> 'Foorumi teade on lubatud',
	'LABEL_BOARD_NOTICE_TITLE'				=> 'Foorumi teate nimi',
	'LABEL_BOARD_NOTICE_PREVIEW'			=> 'Sõnumi eelvaade',
	'LABEL_BOARD_NOTICE_TEXT'				=> 'Sõnum kuvatakse, kui tingimused on täidetud',
	'LABEL_BOARD_NOTICE_BGCOLOR'			=> 'Sõnumi taustavärv',
	'LABEL_BOARD_NOTICE_BGCOLOR_EXPLAIN'	=> 'Kui seda ei ole määratud, on vaikevärv #ECD5D8 (helepunane)',

	'LABEL_BOARD_NOTICE_STYLE'				=> '[Edasijõudnud kasutajad:] Sõnumi konteineri CSS klass',
	'LABEL_BOARD_NOTICE_STYLE_EXPLAIN'		=> 'Kui teil on vaja rohkem kontrollida, kuidas teate kuvatakse, saate määrata CSS-klassi, mis lisatakse üldisele DIV-le.',

	'L_INFORMATION'							=> 'Informatsioon',
	'VARIABLES_EXPLAIN'						=> 'Te võite teksti lisada järgmised erimuutujad:<br />'
												. '{SESSIONID}, {USERID}, {USERNAME}, {LASTVISIT}, {LASTPOST}, {REGISTERED} ja mõni muu, kui aktiveerite konkreetse reegli. Kontrollige reegleid, et näha, millised muutujad on saadaval.',

	'BOARD_NOTICE_RULE_NAME'				=> 'Reegel',
	'BOARD_NOTICE_RULE_VALUE'				=> 'Tingimused',
	'BOARD_NOTICE_RULE_VARIABLES'			=> 'Pakkumine',

	'NO_GUEST_OR_BOT'						=> 'Ei (Külaline või Bot)',

	'BOARD_NOTICE_SAVED'					=> 'Foorumi teade on salvestatud.',

	'ERROR_EMPTY_TITLE'						=> '<strong>Pealkiri</strong> ei tohi olla täitmata.',
	'ERROR_EMPTY_MESSAGE'					=> '<strong>Sõnum</strong> ei tohi olla tühi.',

	'MOVE_FIRST'							=> 'Liiguta esimesele kohale',
	'MOVE_LAST'								=> 'Liiguta viimasele positsioonile',

	// Rule names
	'RULE_ANNIVERSARY'						=> "Kasutajate registreerimise kuupäev",
	'RULE_BIRTHDAY'							=> "Kasutajate sünnipäevad",
	'RULE_DATE'								=> "Kuupäev on",
	'RULE_HAS_NEVER_POSTED'					=> "Kasutaja ei ole kunagi postitanud (võtab arvesse ka heakskiitu ootavaid postitusi)",
	'RULE_HAS_NEVER_POSTED_IN_FORUM'		=> "Kasutaja ei ole kunagi ühelegi nendest foorumitest postitanud (kaasa arvatud heakskiitu ootavad postitused)",
	'RULE_HAS_NOT_POSTED_FOR_1'				=> "Kasutaja ei ole postitanud ",
	'RULE_HAS_NOT_POSTED_FOR_2'				=> " päev(ad) või rohkem (kuid on varem postitanud)",
	'RULE_HAS_POSTED_EXACTLY'				=> "Nähtavate number postituses on võrdne",
	'RULE_HAS_POSTED_LESS'					=> "Nähtavate numberi arv on võrdne või väiksem kui",
	'RULE_HAS_POSTED_MORE'					=> "Nähtavate numberi arv on võrdne või suurem kui",
	'RULE_HAS_POSTED_IN_FORUM'				=> "Kasutaja on postitanud vähemalt ühes nendest foorumitest, (<strong>ei arvestata sisse</strong> kinnitusi ootavad postitused)",
	'RULE_HAS_NOT_VISITED_FOR_1'			=> "Kasutaja ei ole ühtegi nendest valitud foorumitest külastanud ",
	'RULE_HAS_NOT_VISITED_FOR_2'			=> " vähemalt ",
	'RULE_IN_DEFAULT_USERGROUP'				=> "Vaikimisi kasutajagrupp on",
	'RULE_IN_FORUM'							=> "Kasutaja sirvib praegu mõnda neist foorumitest",
	'RULE_IN_USERGROUP'						=> "Kasutaja kuulub ühte neist valitud gruppidest",
	'RULE_LANGUAGE'							=> "Kasutaja keel on üks neist valitud keeltest",
	'RULE_LOGGED_IN'						=> "Kas kasutaja on sisse loginud",
	'RULE_NOT_IN_USERGROUP'					=> "Kasutaja ei kuulu ühtegi valitud gruppi",
	'RULE_RANK'								=> "Kasutaja staatus on üks nendest valitud staatustest",
	'RULE_STYLE'							=> "Kasutaja stiil on üks nendest valitud stiilidest",

	// Rules units
	'RULE_DAY(S)'							=> 'päev(ad)',

	'RESET_FORUM_VISITS_SUCCESS'			=> 'Foorumi külastuse jälgimise andmed on edukalt puhastatud.',
	'RESET_FORUM_VISITS_CONFIRMATION'		=> 'Kas olete kindel, et soovite kõik foorumi külastuse andmed lähtestada?',
));
