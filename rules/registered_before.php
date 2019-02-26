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

class registered_before extends rule_base implements rule_interface
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
		return $this->api->lang('RULE_REGISTERED_BEFORE');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_FULLDATE;
	}

	public function getDefault()
	{
		return array(1, 1, 2018);
	}

	public function isTrue($conditions)
	{
		$date = $this->validateArrayOfConditions($conditions);
		if (is_null($date) || !is_array($date) || (count($date) != 3))
		{
			return false;
		}
		// user registration date was a call to time()
		$reference = gmmktime(0, 0, 0, $date[1], $date[0], $date[2]);
		$registered = $this->api->getUserRegistrationDate();
		return $registered < $reference;
	}

	public function validateValues($values)
	{
		// Make sure there's a full date
		return is_array($values) && (count($values) == 3)
			&& ($values[0] > 0) && ($values[1] > 0) && ($values[2] > 0);
	}
}
