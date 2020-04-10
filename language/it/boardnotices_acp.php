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
	'ACP_BOARD_NOTICES_SETTINGS'			=> 'Configurazione Avvisi dal Forum',
	'ACP_BOARD_NOTICES_SETTINGS_EXPLAIN'	=> 'Configurazione per tutti gli Avvisi',

	'ACP_BOARD_NOTICES_MANAGER'				=> 'Gestione Avvisi dal Forum',
	'ACP_BOARD_NOTICES_MANAGER_EXPLAIN'		=> "Aggiunge, modifica o cancella gli Avvisi dal Forum<br />"
											. "Nota che verrà pubblicata solo un avviso all'utente: sarà quella che per prima supera le condizioni.<br />",

	'ACP_BOARD_NOTICE_SETTINGS'				=> 'Configurazione Avvisi dal Forum',
	'ACP_BOARD_NOTICE_SETTINGS_EXPLAIN'		=> 'Si prega di compilare le informazioni di avviso del tabellone',

	'BOARD_NOTICES_SETTINGS_SAVED'			=> 'Configurazione Avvisi dal Forum salvata.',

	'ACP_BOARD_NOTICE_RULES'				=> 'Le Regole di Avvisi dal Forum',
	'ACP_BOARD_NOTICE_RULES_EXPLAIN'		=> 'Modifica le condizioni affinché l’avviso venga pubblicato. Nota che <strong>tutte le condizioni devono essere soddisfatte</strong>.',

	'LABEL_BOARD_NOTICES_ACTIVE'			=> 'Attiva Avvisi dal Forum',
	'BOARD_NOTICES_ACTIVE_EXPLAIN'			=> 'Qui puoi disabilitare la visualizzazione di tutte le notifiche mantenendo l’estensione abilitata.',

	'LABEL_BOARD_NOTICE_DEFAULT_BGCOLOR'			=> 'Colore di sfondo degli Avvisi',
	'LABEL_BOARD_NOTICE_DEFAULT_BGCOLOR_EXPLAIN'	=> 'Questo è il colore di sfondo di default quando non specificato.(#ECD5D8) Se lasciate in bianco, <strong>nessun</strong> colore di sfondo verrà applicato.',

	'L_FORUMS_VISITS'						=> 'Visite',
	'LABEL_FORUMS_VISITS_ACTIVE'			=> 'Tieni traccia del ultima volta che un utente ha visitato un forum',
	'FORUMS_VISITS_ACTIVE_EXPLAIN'			=> 'Ciò abilita o disabilita il tracciamento delle ultime visite di ciascun forum per ciascun utente. È necessario attivarlo se si desidera utilizzare la condizione "<i>il forum non  è stato visitato per x giorni</i>".',

	'L_RESET_OPTIONS'						=> 'Reset visite',
	'L_RESET_FORUM_VISITS'					=> 'Reset ultima volta che ogni forum è stato visitato',
	'L_RESET_FORUM_VISITS_EXPLAIN'			=> 'Se disabiliti il tracciamento delle visite al forum e la riabiliti, alcuni utenti avranno un ultimo orario di visita molto prima del tempo reale. Si consiglia di ripristinare il monitoraggio dopo aver disabilitato e riabilitato il monitoraggio delle visite al forum.',

	'BOARD_NOTICE_TITLE'					=> 'Titolo dell’avviso',
	'BOARD_NOTICE_RULES'					=> 'Condizioni',
	'BOARD_NOTICE_ADD'						=> 'Aggiungi nuovi Avvisi',
	'BOARD_NOTICE_RULE_ADD'					=> 'Aggiungi nuove regole',

	'COLUMN_CAN_DISMISS'					=> 'Può essere nascosto',

	'LABEL_BOARD_NOTICE_ACTIVE'				=> 'Le Avvisi dal forum sono abilitati',
	'LABEL_BOARD_NOTICE_TITLE'				=> 'Titolo dell’avviso',
	'LABEL_BOARD_NOTICE_DISMISSABLE'		=> 'Avvisi dal forum può essere nascosto',
	'LABEL_BOARD_NOTICE_DISMISS_EXPLAIN'	=> 'L’utente ha l’opzione chiudere l’avviso (che per default è una croce in alto a destra). L’avviso può riapparire dipendentemente dalsettaggio.',
	'LABEL_BOARD_NOTICE_RESET_AFTER'		=> 'Reset dopo',
	'LABEL_BOARD_NOTICE_RESET_EXPLAIN'		=> 'Dopo questi giorni, l’avviso verrà mostrato agli utenti che l’abbiano rimossa. Lascia in bianco se non desideri riappaia.',
	'LABEL_BOARD_NOTICE_PREVIEW'			=> 'Anteprima dell’avviso',
	'LABEL_BOARD_NOTICE_TEXT'				=> 'L’avviso viene pubblicato quando tutte le condizioni sono soddisfatte',
	'LABEL_BOARD_NOTICE_BGCOLOR'			=> 'Colore di sfondo dell’avviso',
	'LABEL_BOARD_NOTICE_BGCOLOR_EXPLAIN'	=> 'Se non specificato il colore di default è #ECD5D8 (light red), dipendentemente dalla configurazione il campo vuoto comporta nessuno sfondo',

	'LABEL_BOARD_NOTICE_STYLE'				=> '[Utenti avanzati:] Classi CSS del contenitore dell’avviso',
	'LABEL_BOARD_NOTICE_STYLE_EXPLAIN'		=> 'Se è necessario un maggiore controllo su come viene visualizzato avviso, è possibile specificare una classe CSS che verrà aggiunta al DIV generale.',

	'L_INFORMATION'							=> 'Informazione',
	'VARIABLES_EXPLAIN'						=> 'Puoi includere le seguenti variabili speciali nel tuo testo:<br />'
												. '{SESSIONID}, {USERID}, {USERNAME}, {LASTVISIT}, {LASTPOST}, {REGISTERED} ed altre quando attivi una particolare regola. Controlla le regole per vedere quali variabili sono disponibili.',

	'BOARD_NOTICE_RULE_ACTIVE'				=> '',
	'BOARD_NOTICE_RULE_NAME'				=> 'Regole',
	'BOARD_NOTICE_RULE_VALUE'				=> 'Condizioni',
	'BOARD_NOTICE_RULE_VARIABLES'			=> 'Fornitura',

	'NO_GUEST_OR_BOT'						=> ' (Ospiti o Bot)',

	'BOARD_NOTICE_SAVED'					=> 'L’Avviso dal Forum è stata salvata.',

	'ERROR_EMPTY_TITLE'						=> '<strong>Titolo</strong> non può essere vuoto.',
	'ERROR_EMPTY_MESSAGE'					=> '<strong>Messaggio</strong> non può essere vuoto.',

	'MOVE_FIRST'							=> 'Muovi in prima posizione',
	'MOVE_LAST'								=> 'Muovi in ultima posizione',

	// Rule names
	'RULE_ANNIVERSARY'						=> "Anniversario della registrazione dell'utente",
	'RULE_BIRTHDAY'							=> "Compleanno dell'utente",
	'RULE_DATE'								=> "Alla data del ",
	'RULE_DATE_ANY'							=> 'Tutti',
	'RULE_DATE_RANGE_1'						=> 'La data è compresa tra',
	'RULE_DATE_RANGE_2'						=> '<br />e<br />',
	'RULE_HAS_NEVER_POSTED'					=> "L'utente non ha immesso messaggi in questo forum",
	'RULE_HAS_NEVER_POSTED_EXPLAIN'			=> "(incluso messaggi in attesa di approvazione)",
	'RULE_HAS_NEVER_POSTED_IN_FORUM'		=> "L'utente non ha immesso messaggi in questi forum ",
	'RULE_HAS_NEVER_POSTED_FORUM_EXPLAIN'	=> "(controlla messaggi in attesa di approvazione)",
	'RULE_HAS_NOT_POSTED_FOR_1'				=> "L'utente non ha immesso messaggi da ",
	'RULE_HAS_NOT_POSTED_FOR_2'				=> "giorni o più (ma ha pubblicato in precedenza)",
	'RULE_HAS_POSTED_EXACTLY'				=> "Il numero di post visibili è uguale a",
	'RULE_HAS_POSTED_LESS'					=> "Il numero di post visibili è uguale a o meno di",
	'RULE_HAS_POSTED_MORE'					=> "Il numero di post visibili è uguale a o più di",
	'RULE_HAS_POSTED_IN_FORUM'				=> "l'utente ha postato l'ultima volta in questo forum",
	'RULE_HAS_POSTED_IN_FORUM_EXPLAIN'		=> "(<strong>non</strong> sono inclusi i messaggi in attesa di approvazione)",
	'RULE_HAS_NEVER_VISITED'				=> "L'Utente non ha visitato nessuno di questi forum",
	'RULE_HAS_NOT_VISITED_FOR_1'			=> "L’utente non ha visitato nessuno dei forum selezionati",
	'RULE_HAS_NOT_VISITED_FOR_2'			=> " per almeno ",
	'RULE_IN_DEFAULT_USERGROUP'				=> "Il gruppo predefinito è",
	'RULE_IN_FORUM'							=> "L'utente sta visitando uno di questi forum",
	'RULE_IN_USERGROUP'						=> "L'utente appartiene a uno di questi gruppi selezionati",
	'RULE_LANGUAGE'							=> "La lingua dell'utente è una di queste lingue selezionate",
	'RULE_LOGGED_IN'						=> "Se l'utente ha fatto Login",
	'RULE_NOT_IN_USERGROUP'					=> "L'utente non appartiene a nessuno di questi gruppi selezionati",
	'RULE_ON_BOARD_INDEX'					=> "L’Avviso è visibile solo nell'indice del forum",
	'RULE_RANK'								=> "Il rank dell'utente è uno qualsiasi di questi ranghi selezionati",
	'RULE_REGISTERED_LESS_THAN'				=> "La registrazione dell'utente è inferiore a",
	'RULE_STYLE'							=> "Lo stile dell'utente è uno di questi stili selezionati",
	'RULE_REGISTERED_BEFORE'				=> "Utente registrato prima del", 
	'RULE_REGISTERED_AFTER'					=> "Utente registrato dopo il", 
	'RULE_IN_TOPIC'							=> "Utente sta visitando uno di questi topic(s)", 
	'RULE_IN_TOPIC_EXPLAIN'					=> "(inserite ID topic separati dalla virgola)", 

	// Rules units
	'RULE_DAY(S)'							=> 'giorno (i) ',

	'RESET_FORUM_VISITS_SUCCESS'			=> 'I dati di tracciamento della visita del forum sono stati correttamente eliminati.',
	'RESET_FORUM_VISITS_CONFIRMATION'		=> 'Sei sicuro di voler ripristinare tutte le informazioni sulla visita del forum?',
));
