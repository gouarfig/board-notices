<?php

namespace fq\boardnotices\domain;

class date_comparator
{
	/** @var \fq\boardnotices\domain\date_condition $start */
	private $start;
	/** @var \fq\boardnotices\domain\date_condition $end */
	private $end;

	private $now;

	public function __construct(date_condition $start, date_condition $end, $now)
	{
		$this->start = $start;
		$this->end = $end;
		$this->now = $now;
	}

	/**
	 * Return the start date
	 *
	 * @return \fq\boardnotices\domain\date_condition
	 */
	public function getStartDateCondition()
	{
		return $this->start;
	}

	/**
	 * Return the start date
	 *
	 * @return \fq\boardnotices\domain\date_condition
	 */
	public function getEndDateCondition()
	{
		return $this->end;
	}

	/**
	 * @return boolean
	 */
	public function areBothEmpty()
	{
		return $this->start->isEmpty() && $this->end->isEmpty();
	}

	/**
	 * @return boolean
	 */
	public function hasBothDays()
	{
		return $this->start->hasDay() && $this->end->hasDay();
	}

	/**
	 * @return boolean
	 */
	public function hasBothMonths()
	{
		return $this->start->hasMonth() && $this->end->hasMonth();
	}

	/**
	 * @return boolean
	 */
	public function hasBothYears()
	{
		return $this->start->hasYear() && $this->end->hasYear();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyBothDays()
	{
		return $this->start->hasOnlyDay() && $this->end->hasOnlyDay();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyBothMonths()
	{
		return $this->start->hasOnlyMonth() && $this->end->hasOnlyMonth();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyBothYears()
	{
		return $this->start->hasOnlyYear() && $this->end->hasOnlyYear();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyBothDaysMonths()
	{
		return $this->start->hasOnlyDayMonth() && $this->end->hasOnlyDayMonth();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyBothDaysYears()
	{
		return $this->start->hasOnlyDayYear() && $this->end->hasOnlyDayYear();
	}

	/**
	 * @return boolean
	 */
	public function hasOnlyBothMonthsYears()
	{
		return $this->start->hasOnlyMonthYear() && $this->end->hasOnlyMonthYear();
	}

	/**
	 * @return boolean
	 */
	public function hasBothFullDate()
	{
		return $this->start->isFullDate() && $this->end->isFullDate();
	}

	/**
	 * @return boolean
	 */
	public function areSymmetrical()
	{
		return $this->areBothEmpty()
			|| $this->hasOnlyBothDays()
			|| $this->hasOnlyBothMonths()
			|| $this->hasOnlyBothYears()
			|| $this->hasOnlyBothDaysMonths()
			|| $this->hasOnlyBothDaysYears()
			|| $this->hasOnlyBothMonthsYears()
			|| $this->hasBothFullDate()
			;
	}
}
