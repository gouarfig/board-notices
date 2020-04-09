<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class date extends rule_base implements rule_interface
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
		return $this->api->lang('RULE_DATE');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_DATE;
	}

	public function getDefault()
	{
		return array(0, 0, 0);
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$date = $this->validateArrayOfConditions($conditions);
		if (is_null($date) || !is_array($date) || (count($date) != 3))
		{
			return false;
		}
		$offset = $this->api->createDateTime()->getOffset();
		$now = getdate(time() - date('Z') + $offset);	// This gives the date in the user timezone
		$valid = ((($date[0] == 0) || ($now['mday'] == $date[0]))
				&& (($date[1] == 0) || ($now['mon'] == $date[1]))
				&& (($date[2] == 0) || ($now['year'] == $date[2])));
		return $valid;
	}

	public function validateValues($values)
	{
		// Make sure there's a least one value (either day, month or year)
		return is_array($values) && (count($values) == 3)
			&& (($values[0] > 0) || ($values[1] > 0) || ($values[2] > 0));
	}
}
