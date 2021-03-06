<?php

namespace fq\boardnotices\service;

/**
 * All constants used in Board Notices Manager
 */
class constants
{
	public static $SQL_SELECT = 'SELECT';
	public static $SQL_FROM = 'FROM';
	public static $SQL_WHERE = 'WHERE';

	public static $CONFIG_SQL_CACHE_TTL = 'boardnotices_sql_cache_ttl';
	public static $CONFIG_ACTIVE_NOTICES_CACHE_KEY = 'boardnotices_active_notices';
	public static $CONFIG_ALL_NOTICES_CACHE_KEY = 'boardnotices_all_notices';
	public static $CONFIG_RULES_CACHE_KEY = 'boardnotices_rules';
	public static $CONFIG_DISMISSED_CACHE_KEY = 'boardnotices_seen';

	public static $CONFIG_ENABLED = 'boardnotices_enabled';
	public static $CONFIG_DEFAULT_BGCOLOR = 'boardnotices_default_bgcolor';
	public static $CONFIG_TRACK_FORUMS_VISITS = 'track_forums_visits';
	public static $CONFIG_PREVIEW_KEY = 'boardnotices_previewkey';

	public static $RULES_FOLDER = 'ext/fq/boardnotices/rules';
	public static $RULES_FILE_EXTENSION = '.php';
	public static $RULES_CLASS_PREFIX = 'fq.boardnotices.rules';

	public static $RULE_DISPLAY_NAME = 'display_name';
	public static $RULE_DISPLAY_EXPLAIN = 'display_explain';
	public static $RULE_DISPLAY_UNIT = 'display_unit';

	public static $RULE_WITH_NO_TYPE = 'n/a';
	public static $RULE_TYPE_DATE = 'date';
	public static $RULE_TYPE_FULLDATE = 'fulldate';
	public static $RULE_TYPE_FORUMS = 'forums';
	public static $RULE_TYPE_INTEGER = 'int';
	public static $RULE_TYPE_LIST = 'list';
	public static $RULE_TYPE_MULTIPLE_CHOICE = 'multiple choice';
	public static $RULE_TYPE_YESNO = 'yesno';
	public static $RULE_TYPE_MULTIPLE_INTEGERS = 'multiple int';

	public static $CONTROLLER_ROUTING_ID = 'fq_boardnotices_controller';
	public static $ROUTING_CLOSE_HASH_ID = 'close_boardnotice';
}
