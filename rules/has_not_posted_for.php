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

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class has_not_posted_for extends rule_base implements rule_interface
{
	/** @var \phpbb\user $lang */
	private $user;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user)
	{
		$this->serializer = $serializer;
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_HAS_NOT_POSTED_FOR_1');
	}

	public function getDisplayExplain()
	{
		return '';
	}

	public function getDisplayUnit()
	{
		return $this->user->lang('RULE_HAS_NOT_POSTED_FOR_2');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_INTEGER;
	}

	public function getDefault()
	{
		return 0;
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function validateValues($values)
	{
		return true;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$days = $this->validateUniqueCondition($conditions);
		if ($this->user->data['user_lastpost_time'] > 0)
		{
			$valid = ((time() - $this->user->data['user_lastpost_time']) >= ($days * 24 * 60 * 60));
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
