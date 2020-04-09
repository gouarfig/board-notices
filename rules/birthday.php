<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class birthday extends rule_base implements rule_interface
{
	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api)
	{
		$this->serializer = $serializer;
		$this->api = $api;
	}

	private function age($bday_day, $bday_month, $bday_year)
	{
		$age = 0;
		if ($bday_year)
		{
			$now = $this->api->createDateTime();
			$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());

			$diff = $now['mon'] - $bday_month;
			if ($diff == 0)
			{
				$diff = ($now['mday'] - $bday_day < 0) ? 1 : 0;
			} else
			{
				$diff = ($diff < 0) ? 1 : 0;
			}

			$age = max(0, (int) ($now['year'] - $bday_year - $diff));
		}
		return $age;
	}

	private function setTemplateVars($age)
	{
		$this->template_vars = array(
			'AGE' => $age
		);
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_BIRTHDAY');
	}

	public function getType()
	{
		return constants::$RULE_WITH_NO_TYPE;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		if ($this->api->isUserRegistered())
		{
			$user_birthday = $this->api->getUserBirthday();
			if (empty($user_birthday))
			{
				return false;
			}
			list($bday_day, $bday_month, $bday_year) = array_map('intval', explode('-', $user_birthday));
			$now = $this->api->createDateTime();
			$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());
			$valid = (($bday_day == $now['mday']) && ($bday_month == $now['mon']));
			// We create the 'age' variable in all cases (so it can be displayed in preview mode)
			$this->setTemplateVars($this->age($bday_day, $bday_month, $bday_year));
		}
		return $valid;
	}

	public function getAvailableVars()
	{
		return array('AGE');
	}

}
