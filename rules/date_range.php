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
			return false;
		}
		if ($this->emptyDate($startDate) && $this->emptyDate($endDate))
		{
			return true;
		}
		if ($this->fullDate($startDate) && $this->fullDate($endDate))
		{
			// Full date comparison
			$today = $this->api->createDateTime();
			$start = $this->createDateTime($startDate);
			$end = $this->createDateTime($endDate, "23:59:59");

			$valid = ($start <= $today) && ($today <= $end);
		}
		$offset = $this->api->createDateTime()->getOffset();
		$now = getdate(time() - date('Z') + $offset);	// This gives the date in the user timezone

		return $valid;
	}

	private function isDateParameterValid($date)
	{
		return (is_array($date) && (count($date) == 3));
	}

	private function emptyDate($date)
	{
		return empty($date[0]) && empty($date[1] && empty($date[2]));
	}

	private function fullDate($date)
	{
		return !empty($date[0]) && !empty($date[1]) && !empty($date[2]);
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

}
