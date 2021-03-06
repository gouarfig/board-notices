<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class anniversary extends rule_base implements rule_interface
{
	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api)
	{
		$this->serializer = $serializer;
		$this->api = $api;
	}

	private function calculateYears($now, $regdate)
	{
		$anniversary = 0;
		if ($regdate['year'])
		{
			$diff = $now['mon'] - $regdate['mon'];
			if ($diff == 0)
			{
				$diff = ($now['mday'] - $regdate['mday'] < 0) ? 1 : 0;
			} else
			{
				$diff = ($diff < 0) ? 1 : 0;
			}

			$anniversary = max(0, (int) ($now['year'] - $regdate['year'] - $diff));
		}
		return $anniversary;
	}

	private function setTemplateVars($anniversary)
	{
		$this->template_vars = array(
			'ANNIVERSARY' => $anniversary
		);
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_ANNIVERSARY');
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
			$user_regdate = date('r', $this->api->getUserRegistrationDate());
			$now = $this->api->createDateTime();
			$regdate = $this->api->createDateTime($user_regdate);
			$now = getdate($now->getTimestamp());
			$regdate = getdate($regdate->getTimestamp());
			$valid = (($regdate['mday'] == $now['mday']) && ($regdate['mon'] == $now['mon']) && ($regdate['year'] < $now['year']));
			// We create the 'anniversary' variable in all cases (so it can be displayed in preview mode)
			$this->setTemplateVars($this->calculateYears($now, $regdate));
		}
		return $valid;
	}

	public function getAvailableVars()
	{
		return array('ANNIVERSARY');
	}

}
