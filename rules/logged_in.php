<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class logged_in extends rule_base implements rule_interface
{
	/** @var \phpbb\user $user */
	private $user;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user)
	{
		$this->serializer = $serializer;
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_LOGGED_IN');
	}

	public function getDisplayUnit()
	{
		return $this->user->lang('NO_GUEST_OR_BOT');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_YESNO;
	}

	public function getDefault()
	{
		return false;
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$logged_in_conditions = $this->validateUniqueCondition($conditions);
		$logged_in = ($this->user->data['user_type'] != USER_IGNORE);
		$valid = ($logged_in_conditions && $logged_in) || (!$logged_in_conditions && !$logged_in);
		return $valid;
	}

	public function getAvailableVars()
	{
		return array();
	}

	public function getTemplateVars()
	{
		return array();
	}

}
