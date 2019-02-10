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
		$valid = false;
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
			$valid = false;
		}
		else if ($this->emptyDate($startDate) && $this->emptyDate($endDate))
		{
			$valid = true;
		}
		else if ($this->fullDate($startDate) && $this->fullDate($endDate))
		{
			// Full date comparison
			$today = $this->api->createDateTime();
			$start = $this->createDateTime($startDate);
			$end = $this->createDateTime($endDate, "23:59:59");

			$valid = ($start <= $today) && ($today <= $end);
		}
		else
		{
			$now = $this->getDate();
			var_dump($now);
			$valid = true;
			$valid = $valid && $this->yearConditionValid($now, $startDate, $endDate);
			$valid = $valid && $this->monthConditionValid($now, $startDate, $endDate);
			$valid = $valid && $this->dayConditionValid($now, $startDate, $endDate);
		}

		return $valid;
	}

	private function isDateParameterValid($date)
	{
		return (is_array($date) && (count($date) == 3));
	}

	private function emptyDate($date)
	{
		return empty($date[0]) && empty($date[1]) && empty($date[2]);
	}

	private function fullDate($date)
	{
		return !empty($date[0]) && !empty($date[1]) && !empty($date[2]);
	}

	private function yearConditionValid($now, $startDate, $endDate)
	{
		$valid = true;
		if ($startDate[2] > 0)
		{
			$valid = $valid && ($startDate[2] <= $now['year']);
		}
		if ($endDate[2] > 0)
		{
			$valid = $valid && ($now['year'] <= $endDate[2]);
		}
		return $valid;
	}

	private function monthConditionValid($now, $startDate, $endDate)
	{
		$valid = true;
		if ($startDate[1] > 0)
		{
			$valid = $valid && ($startDate[1] <= $now['mon']);
		}
		if ($endDate[1] > 0)
		{
			$valid = $valid && ($now['mon'] <= $endDate[1]);
		}
		return $valid;
	}

	private function dayConditionValid($now, $startDate, $endDate)
	{
		$valid = true;
		if ($startDate[0] > 0)
		{
			$valid = $valid && ($startDate[0] <= $now['mday']);
		}
		if ($endDate[0] > 0)
		{
			$valid = $valid && ($now['mday'] <= $endDate[0]);
		}
		return $valid;
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
