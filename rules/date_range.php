<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;
use fq\boardnotices\domain\date_comparator;

class date_range extends rule_base implements rule_interface
{
	private $time = null;
	private $date = null;

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
		$this->date = $date;
	}

	private function getDate()
	{
		// @todo Sould we get the user timestamp instead of the server one?
		$datetime = new \DateTime();
		if ($this->date !== null)
		{
			$datetime = \DateTime::createFromFormat('!Y-m-d', $this->date);
		}
		return array(
			'mday' => $datetime->format('d'),
			'mon' => $datetime->format('m'),
			'year' => $datetime->format('Y')
		);
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
			// Symmetrical type 1
			if ($dates->getStartDateCondition()->getDay() <= $dates->getEndDateCondition()->getDay())
			{
				return ($dates->getStartDateCondition()->getDay() <= $now["mday"]) && ($now["mday"] <= $dates->getEndDateCondition()->getDay());
			}
			return ($dates->getStartDateCondition()->getDay() >= $now["mday"]) || ($now["mday"] >= $dates->getEndDateCondition()->getDay());
		}
		if ($dates->hasOnlyBothMonths())
		{
			// Symmetrical type 2
			if ($dates->getStartDateCondition()->getMonth() <= $dates->getEndDateCondition()->getMonth())
			{
				return ($dates->getStartDateCondition()->getMonth() <= $now["mon"]) && ($now["mon"] <= $dates->getEndDateCondition()->getMonth());
			}
			return ($dates->getStartDateCondition()->getMonth() >= $now["mon"]) || ($now["mon"] >= $dates->getEndDateCondition()->getMonth());
		}
		if ($dates->hasOnlyBothYears())
		{
			// Symmetrical type 3
			if ($dates->getStartDateCondition()->getYear() <= $dates->getEndDateCondition()->getYear())
			{
				return ($dates->getStartDateCondition()->getYear() <= $now["year"]) && ($now["year"] <= $dates->getEndDateCondition()->getYear());
			}
			return false;
		}
		if ($dates->hasOnlyBothDaysMonths())
		{
			// Symmetrical type 4
			if ($dates->getStartDateCondition()->getMonth() <= $dates->getEndDateCondition()->getMonth())
			{
				// Months in order, just need to add the current year
				$dates->getStartDateCondition()->setCurrentYear();
				$dates->getEndDateCondition()->setCurrentYear();
				$comparison = \DateTime::createFromFormat('!Y-m-d', "{$now["year"]}-{$now["mon"]}-{$now["mday"]}");
				return ($dates->getStartDateCondition()->getDateTime() <= $comparison)
					&& ($dates->getEndDateCondition()->getDateTime() >= $comparison);
			}
			else
			{
				# Month in reverse order, doing it in to passes
				$dates->getStartDateCondition()->setPreviousYear();
				$dates->getEndDateCondition()->setCurrentYear();
				$comparison = \DateTime::createFromFormat('!Y-m-d', "{$now["year"]}-{$now["mon"]}-{$now["mday"]}");
				if (($dates->getStartDateCondition()->getDateTime() <= $comparison)
				&& ($dates->getEndDateCondition()->getDateTime() >= $comparison))
				{
					return true;
				}
				$dates->getStartDateCondition()->setCurrentYear();
				$dates->getEndDateCondition()->setNextYear();
				$comparison = \DateTime::createFromFormat('!Y-m-d', "{$now["year"]}-{$now["mon"]}-{$now["mday"]}");
				return ($dates->getStartDateCondition()->getDateTime() <= $comparison)
					&& ($dates->getEndDateCondition()->getDateTime() >= $comparison);
			}
		}
		if ($dates->hasOnlyBothMonthsYears())
		{
			// Symmetrical type 5
			$start = $dates->getStartDateCondition()->setFirstDay();
			$end = $dates->getEndDateCondition()->setLastDay();
			$comparison = \DateTime::createFromFormat('!Y-m-d', "{$now["year"]}-{$now["mon"]}-{$now["mday"]}");
			return ($start->getDateTime() <= $comparison)
				&& ($end->getDateTime() >= $comparison);
		}
		if ($dates->hasOnlyBothDaysYears())
		{
			// Symmetrical type 6
			// Create a virtual number for comparison
			$date1 = $dates->getStartDateCondition()->getYear() * 100 + $dates->getStartDateCondition()->getDay();
			$date2 = $dates->getEndDateCondition()->getYear() * 100 + $dates->getEndDateCondition()->getDay();
			if ($date1 <= $date2)
			{
				// Dates in order
				$start = $dates->getStartDateCondition()->setCurrentMonth();
				$end = $dates->getEndDateCondition()->setCurrentMonth();
				$comparison = \DateTime::createFromFormat('!Y-m-d', "{$now["year"]}-{$now["mon"]}-{$now["mday"]}");
				return ($start->getDateTime() <= $comparison)
					&& ($end->getDateTime() >= $comparison);
			}
			else
			{
				// Reverse order so need to do 2 checks, previous month and next month
				$dates->getStartDateCondition()->setPreviousMonth();
				$dates->getEndDateCondition()->setCurrentMonth();
				$comparison = \DateTime::createFromFormat('!Y-m-d', "{$now["year"]}-{$now["mon"]}-{$now["mday"]}");
				if (($dates->getStartDateCondition()->getDateTime() <= $comparison)
				&& ($dates->getEndDateCondition()->getDateTime() >= $comparison))
				{
					return true;
				}
				$dates->getStartDateCondition()->setCurrentMonth();
				$dates->getEndDateCondition()->setNextMonth();
				$comparison = \DateTime::createFromFormat('!Y-m-d', "{$now["year"]}-{$now["mon"]}-{$now["mday"]}");
				return ($dates->getStartDateCondition()->getDateTime() <= $comparison)
					&& ($dates->getEndDateCondition()->getDateTime() >= $comparison);
			}
		}
		if ($dates->hasBothFullDate())
		{
			// Symmetrical type 7 (full date comparison)
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
