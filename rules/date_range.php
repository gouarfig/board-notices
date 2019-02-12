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
use fq\boardnotices\domain\date_comparator;

class date_range extends rule_base implements rule_interface
{
	private $time = null;

	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api)
	{
		$this->serializer = $serializer;
		$this->api = $api;
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
		return $this->api->lang('RULE_DATE_RANGE_1');
	}

	public function getDisplayUnit()
	{
		return array(
			$this->api->lang('RULE_DATE_RANGE_2'),
			'',
		);
	}

	public function getType()
	{
		return array(
			constants::$RULE_TYPE_DATE,
			constants::$RULE_TYPE_DATE,
		);
	}

	public function getDefault()
	{
		return array(array(0, 0, 0), array(0, 0, 0));
	}

	/**
	 * Overrides the current date: This is only to be used by unit testing!
	 */
	public function setDate($date)
	{
		// 0123-56-89
		$this->time = gmmktime(12, 0, 0, substr($date, 8, 2), substr($date, 5, 2), substr($date, 0, 4));
	}

	private function getDate()
	{
		if ($this->time === null)
		{
			$this->time = time();
		}
		$offset = $this->api->createDateTime()->getOffset();
		$now = getdate($this->time - date('Z') + $offset);	// This gives the date in the user timezone
		return $now;
	}

	public function isTrue($conditions)
	{
		$parameters = $this->validateConditions($conditions);
		// $conditions should be at least an array with 2 elements. If not, there's something going on
		if (!is_array($parameters) || (count($parameters) != 2))
		{
			return false;
		}
		$startDate = $this->validateArrayOfConditions($parameters[0]);
		$endDate = $this->validateArrayOfConditions($parameters[1]);

		if (!$this->isDateParameterValid($startDate) || !$this->isDateParameterValid($endDate))
		{
			return false;
		}

		$now = $this->getDate();
		$start = new \fq\boardnotices\domain\date_condition($startDate, $now);
		$end = new \fq\boardnotices\domain\date_condition($endDate, $now);

		return $this->validateDateCondition($now, $start, $end);
	}

	private function isDateParameterValid($date)
	{
		return (is_array($date) && (count($date) == 3));
	}

	private function validateDateCondition($now, \fq\boardnotices\domain\date_condition $start, \fq\boardnotices\domain\date_condition $end)
	{
		$dates = new date_comparator($start, $end, $now);
		if ($dates->areBothEmpty())
		{
			return true;
		}
		if ($dates->areSymmetrical())
		{
			return $this->validateSymmetricalDateCondition($now, $dates);
		}
		return false;
	}

	private function validateSymmetricalDateCondition($now, \fq\boardnotices\domain\date_comparator $dates)
	{
		if ($dates->hasOnlyBothDays())
		{
			if ($dates->getStartDateCondition()->getDay() <= $dates->getEndDateCondition()->getDay())
			{
				return ($dates->getStartDateCondition()->getDay() <= $now["mday"]) && ($now["mday"] <= $dates->getEndDateCondition()->getDay());
			}
			return ($dates->getStartDateCondition()->getDay() >= $now["mday"]) || ($now["mday"] >= $dates->getEndDateCondition()->getDay());
		}
		if ($dates->hasOnlyBothMonths())
		{
			if ($dates->getStartDateCondition()->getMonth() <= $dates->getEndDateCondition()->getMonth())
			{
				return ($dates->getStartDateCondition()->getMonth() <= $now["mon"]) && ($now["mon"] <= $dates->getEndDateCondition()->getMonth());
			}
			return ($dates->getStartDateCondition()->getMonth() >= $now["mon"]) || ($now["mon"] >= $dates->getEndDateCondition()->getMonth());
		}
		if ($dates->hasOnlyBothYears())
		{
			if ($dates->getStartDateCondition()->getYear() <= $dates->getEndDateCondition()->getYear())
			{
				return ($dates->getStartDateCondition()->getYear() <= $now["year"]) && ($now["year"] <= $dates->getEndDateCondition()->getYear());
			}
			return false;
		}
		if ($dates->hasBothFullDate())
		{
			// Full date comparison
			$today = $this->api->createDateTime($now["year"] . '-' . $now["mon"] . '-' . $now["mday"]);
			$startDateTime = $this->createDateTime($dates->getStartDateCondition()->getValue());
			$endDateTime = $this->createDateTime($dates->getEndDateCondition()->getValue(), "23:59:59");

			return ($startDateTime <= $today) && ($today <= $endDateTime);
		}
	}

	private function createDateTime($date, $time = "")
	{
		if ($time !== "")
		{
			$time = "T{$time}";
		}
		if ($date[0] == 32)
		{
			$temp = $this->api->createDateTime("{$date[2]}-{$date[1]}-1")->format('Y-m-t');
			$dateTime = $this->api->createDateTime($temp . $time);
		}
		else
		{
			$dateTime = $this->api->createDateTime("{$date[2]}-{$date[1]}-{$date[0]}" . $time);
		}
		return $dateTime;
	}

	public function getAvailableVars()
	{
		return array();
	}

	public function getTemplateVars()
	{
		return array();
	}

	public function validateValues($values)
	{
		// There are two parts
		if (!is_array($values) || (count($values) != 2))
		{
			return false;
		}
		// Each part is an array of 3 values
		if (!is_array($values[0]) || (count($values[0]) != 3)
		|| !is_array($values[1]) || (count($values[1]) != 3))
		{
			return false;
		}
		// Make sure at least one value is specified
		return ($values[0][0] > 0) || ($values[1][0] > 0)
			|| ($values[0][1] > 0) || ($values[1][1] > 0)
			|| ($values[0][2] > 0) || ($values[1][2] > 0);
	}

}
