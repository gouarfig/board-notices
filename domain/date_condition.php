<?php

namespace fq\boardnotices\domain;

class date_condition
{
	private $day;
	private $month;
	private $year;

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
		$this->day = $day;
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
		// Magic value that will be converted into the last day of the month
		return $this->setDay(32);
	}

	/**
	 * @param int
	 * @return date_condition
	 */
	public function setMonth($month)
	{
		$this->month = $month;
		return $this;
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
		$this->year = $year;
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
}
