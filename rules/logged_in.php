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
	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api)
	{
		$this->serializer = $serializer;
		$this->api = $api;
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_LOGGED_IN');
	}

	public function getDisplayUnit()
	{
		return $this->api->lang('NO_GUEST_OR_BOT');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_YESNO;
	}

	public function getDefault()
	{
		return false;
	}

	public function isTrue($conditions)
	{
		$logged_in_conditions = $this->validateUniqueCondition($conditions);
		$logged_in = $this->api->isUserLoggedIn();
		return ($logged_in_conditions && $logged_in) || (!$logged_in_conditions && !$logged_in);
	}

}
