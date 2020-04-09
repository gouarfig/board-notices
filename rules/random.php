<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class random extends rule_base implements rule_interface
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
		return $this->api->lang('RULE_RANDOM');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_INTEGER;
	}

	public function getDefault()
	{
		return "2";
	}

	public function isTrue($conditions)
	{
		$odds = $this->validateUniqueCondition($conditions);
		if (!is_numeric($odds))
		{
			return false;
		}
		// In case the user entered 1.5
		$odds = ceil($odds);
		return mt_rand(1, $odds) == 1;
	}

	public function validateValues($values)
	{
		// Value should be numeric and between 2 and max number allowed by the random function
		return is_array($values) && !empty($values[0]) && is_numeric($values[0]) && ($values[0] > 1) && ($values[0] <= mt_getrandmax());
	}
}
