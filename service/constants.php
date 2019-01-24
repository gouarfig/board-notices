<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) 2015 Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\service;

/**
 * All constants used in Board Notices Manager
 */
class constants
{
	public static $RULES_FOLDER = 'ext/fq/boardnotices/rules';
	public static $RULES_FILE_EXTENSION = '.php';
	public static $RULES_CLASS_PREFIX = 'fq.boardnotices.rules';

	public static $RULE_DISPLAY_NAME = 'display_name';
	public static $RULE_DISPLAY_UNIT = 'display_unit';

	public static $RULE_WITH_NO_TYPE = 'n/a';
	public static $RULE_TYPE_DATE = 'date';
	public static $RULE_TYPE_FORUMS = 'forums';
	public static $RULE_TYPE_INTEGER = 'int';
	public static $RULE_TYPE_LIST = 'list';
	public static $RULE_TYPE_MULTIPLE_CHOICE = 'multiple choice';
	public static $RULE_TYPE_YESNO = 'yesno';
}
