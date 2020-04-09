<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class has_not_posted_for extends rule_base implements rule_interface
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
		return $this->api->lang('RULE_HAS_NOT_POSTED_FOR_1');
	}

	public function getDisplayUnit()
	{
		return $this->api->lang('RULE_HAS_NOT_POSTED_FOR_2');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_INTEGER;
	}

	public function getDefault()
	{
		return 0;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$days = $this->validateUniqueCondition($conditions);
		if ($this->api->getUserLastPostTime() > 0)
		{
			$valid = ((time() - $this->api->getUserLastPostTime()) >= ($days * 24 * 60 * 60));
		}
		return $valid;
	}

	public function getAvailableVars()
	{
		return array(
			'DAYS_NO_POST',
			'WEEKS_NO_POST',
			'MONTHS_NO_POST',
			'YEARS_NO_POST',
		);
	}

	public function getTemplateVars()
	{
		$daysNoPosts = 0;
		$weeksNoPosts = 0;
		$monthsNoPosts = 0;
		$yearsNoPosts = 0;

		$user_lastpost_time = (int) $this->user->data['user_lastpost_time'];
		if ($user_lastpost_time > 0)
		{
			$noPosts = time() - $user_lastpost_time;
			$daysNoPosts = floor($noPosts / 86400);
			$weeksNoPosts = floor($noPosts / 604800);
			$monthsNoPosts = floor($noPosts / 2592000);
			$yearsNoPosts = floor($noPosts / 31536000);
		}

		return array(
			'DAYS_NO_POST' => $daysNoPosts,
			'WEEKS_NO_POST' => $weeksNoPosts,
			'MONTHS_NO_POST' => $monthsNoPosts,
			'YEARS_NO_POST' => $yearsNoPosts,
		);
	}

}
