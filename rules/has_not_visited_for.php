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

class has_not_visited_for extends rule_base implements rule_interface
{
	/** @var \phpbb\user $lang */
	private $user;
	/** @var \fq\boardnotices\repository\users_interface $repository */
	private $repository;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user, \fq\boardnotices\repository\users_interface $repository)
	{
		$this->serializer = $serializer;
		$this->user = $user;
		$this->repository = $repository;
	}

	/**
	 * Multiple parameters rule
	 * @overriden
	 */
	public function hasMultipleParameters()
	{
		return true;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_HAS_NOT_VISITED_FOR_1');
	}

	public function getDisplayExplain()
	{
		return '';
	}

	public function getDisplayUnit()
	{
		return array(
			$this->user->lang('RULE_HAS_NOT_VISITED_FOR_2'),
			$this->user->lang('RULE_DAY(S)'),
		);
	}

	public function getType()
	{
		return array(
			constants::$RULE_TYPE_FORUMS,
			constants::$RULE_TYPE_INTEGER,
		);
	}

	public function getDefault()
	{
		return array(array(0), array(0));
	}

	public function getPossibleValues()
	{
		return array(null, null);
	}

	public function validateValues($values)
	{
		return true;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$parameters = $this->validateConditions($conditions);
		// $conditions should be at least an array with 2 elements. If not, there's something going on
		if (!is_array($parameters) || (count($parameters) != 2))
		{
			return false;
		}
		$forums = $this->validateArrayOfConditions($parameters[0]);
		$days = $this->validateUniqueCondition($parameters[1]);
		return true;
		// if ($this->user->data['user_lastpost_time'] > 0)
		// {
		// 	$valid = ((time() - $this->user->data['user_lastpost_time']) >= ($days * 24 * 60 * 60));
		// }
		return $valid;
	}

	public function getAvailableVars()
	{
		return array('DAYS_NO_VISIT', 'WEEKS_NO_VISIT', 'MONTHS_NO_VISIT', 'YEARS_NO_VISIT');
	}

	public function getTemplateVars()
	{
		$daysNoVisit = 0;
		$weeksNoVisit = 0;
		$monthsNoVisit = 0;
		$yearsNoVisit = 0;

		// @toto!!!
		$user_lastpost_time = (int) $this->user->data['user_lastpost_time'];
		if ($user_lastpost_time > 0)
		{
			$noVisit = time() - $user_lastpost_time;
			$daysNoVisit = floor($noVisit / 86400);
			$weeksNoVisit = floor($noVisit / 604800);
			$monthsNoVisit = floor($noVisit / 2592000);
			$yearsNoVisit = floor($noVisit / 31536000);
		}

		return array(
			'DAYS_NO_VISIT' => $daysNoVisit,
			'WEEKS_NO_VISIT' => $weeksNoVisit,
			'MONTHS_NO_VISIT' => $monthsNoVisit,
			'YEARS_NO_VISIT' => $yearsNoVisit,
		);
	}

}
