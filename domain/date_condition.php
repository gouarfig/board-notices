<?php

namespace fq\boardnotices\domain;

class date_condition
{
	private $day;
	private $month;
	private $year;

	private $days_per_month = array(
		1 => 31,
		2 => 28,
		3 => 31,
		4 => 30,
		5 => 31,
		6 => 30,
		7 => 31,
		8 => 31,
		9 => 30,
		10 => 31,
		11 => 30,
		12 => 31,
	);

	/**
	 * Parameter $date should be an array of 3 values: (day, month, year)
	 * A value of zero means "any"
	 * Parameter $now should come from getdate()
	 */
	public function __construct($date, $now)
	{
		$this->day = $date[0];
		$this->month = $date[1];
		$this->year = $date[2];
		$this->now = $now;
	}

	/**
	 * @return int
	 */
	public function getDay()
	{
		return $this->day;
	}

	/**
	 * @return int
	 */
	public function getMonth()
	{
		return $this->month;
	}

	/**
	 * @return int
	 */
	public function getYear()
	{
		return $this->year;
	}

	/**
	 * @return int[]
	 */
	public function getValue()
	{
		return array($this->getDay(), $this->getMonth(), $this->getYear());
	}

	/**
	 * @return boolean
	 */
	public function hasDay()
	{
		return $this->day > 0;
	}

	/**
	 * @return boolean
	 */
	public function hasMonth()
	{
		return $this->month > 0;
	}

	/**
	 * @return boolean
	 */
	public function hasYear()
	{
		return $this->year > 0;
	}

	/**
	 * @return boolean
	 */
	public function isFullDate()
	{
		return $this->hasDay() && $this->hasMonth() && $this->hasYear();
	}

	/**
	 * @return boolean
	 */
	public function isEmpty()
	{
		return !$this->hasDay() && !$this->hasMonth() && !$this->hasYear();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyDay()
	{
		return $this->hasDay() && !$this->hasMonth() && !$this->hasYear();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyMonth()
	{
		return !$this->hasDay() && $this->hasMonth() && !$this->hasYear();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyYear()
	{
		return !$this->hasDay() && !$this->hasMonth() && $this->hasYear();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyDayMonth()
	{
		return $this->hasDay() && $this->hasMonth() && !$this->hasYear();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyDayYear()
	{
		return $this->hasDay() && !$this->hasMonth() && $this->hasYear();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyMonthYear()
	{
		return !$this->hasDay() && $this->hasMonth() && $this->hasYear();
	}

	/**
	 * @param int
	 * @return date_condition
	 */
	public function setDay($day)
	{
		$this->day = (int) $day;
		return $this;
	}

	/**
	 * @param int
	 * @return date_condition
	 */
	public function setFirstDay()
	{
		return $this->setDay(1);
	}

	/**
	 * @param int
	 * @return date_condition
	 */
	public function setLastDay()
	{
		if (!empty($this->month))
		{
			return $this->setDay($this->days_per_month[(int) $this->month]);
		}
		return $this->setDay(31);
	}

	/**
	 * @param int
	 * @return date_condition
	 */
	public function setMonth($month)
	{
		$month = (int) $month;
		if ($month < 1)
		{
			$month = 12;
		}
		else if ($month > 12)
		{
			$month = 1;
		}
		$this->month = $month;
		return $this;
	}

	/**
	 * @return date_condition
	 */
	public function setCurrentMonth()
	{
		return $this->setMonth($this->now['mon']);
	}

	/**
	 * @return date_condition
	 */
	public function setPreviousMonth()
	{
		return $this->setMonth($this->now['mon'] -1);
	}

	/**
	 * @return date_condition
	 */
	public function setNextMonth()
	{
		return $this->setMonth($this->now['mon'] +1);
	}

	/**
	 * @param int
	 * @return date_condition
	 */
	public function setFirstMonth()
	{
		return $this->setMonth(1);
	}

	/**
	 * @param int
	 * @return date_condition
	 */
	public function setLastMonth()
	{
		return $this->setMonth(12);
	}


	/**
	 * @param int
	 * @return date_condition
	 */
	public function setYear($year)
	{
		$this->year = (int) $year;
		return $this;
	}

	/**
	 * @param int
	 * @return date_condition
	 */
	public function setCurrentYear()
	{
		return $this->setYear($this->now['year']);
	}

	/**
	 * @param int
	 * @return date_condition
	 */
	public function setPreviousYear()
	{
		return $this->setYear($this->now['year'] - 1);
	}

	/**
	 * @param int
	 * @return date_condition
	 */
	public function setNextYear()
	{
		return $this->setYear($this->now['year'] + 1);
	}

	/**
	 * Return a \DateTime of the date
	 *
	 * @return \DateTime
	 */
	public function getDateTime()
	{
		if (!$this->isFullDate())
		{
			throw new \Exception("Can only convert a full date to DateTime. Current date is day={$this->day}, month={$this->month}, year={$this->year}", 1);
		}
		return \DateTime::createFromFormat('!Y-m-d', "{$this->year}-{$this->month}-{$this->day}");
	}
}
