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

	public function getDay()
	{
		return $this->day;
	}

	public function getMonth()
	{
		return $this->month;
	}

	public function getYear()
	{
		return $this->year;
	}

	public function getValue()
	{
		return array($this->getDay(), $this->getMonth(), $this->getYear());
	}

	public function hasDay()
	{
		return $this->day > 0;
	}

	public function hasMonth()
	{
		return $this->month > 0;
	}

	public function hasYear()
	{
		return $this->year > 0;
	}

	public function isFullDate()
	{
		return $this->hasDay() && $this->hasMonth() && $this->hasYear();
	}

	public function isEmpty()
	{
		return !$this->hasDay() && !$this->hasMonth() && !$this->hasYear();
	}

	public function hasOnlyDayMonth()
	{
		return $this->hasDay() && $this->hasMonth() && !$this->hasYear();
	}

	public function hasOnlyDayYear()
	{
		return $this->hasDay() && !$this->hasMonth() && $this->hasYear();
	}

	public function hasOnlyMonthYear()
	{
		return !$this->hasDay() && $this->hasMonth() && $this->hasYear();
	}

	public function setDay($day)
	{
		$this->day = $day;
		return $this;
	}

	public function setFirstDay()
	{
		return $this->setDay(1);
	}

	public function setLastDay()
	{
		// Magic value that will be converted into the last day of the month
		return $this->setDay(32);
	}

	public function setMonth($month)
	{
		$this->month = $month;
		return $this;
	}

	public function setFirstMonth()
	{
		return $this->setMonth(1);
	}

	public function setLastMonth()
	{
		return $this->setMonth(12);
	}


	public function setYear($year)
	{
		$this->year = $year;
		return $this;
	}

	public function setCurrentYear()
	{
		return $this->setYear($this->now['year']);
	}

	public function setPreviousYear()
	{
		return $this->setYear($this->now['year'] - 1);
	}

	public function setNextYear()
	{
		return $this->setYear($this->now['year'] + 1);
	}
}
