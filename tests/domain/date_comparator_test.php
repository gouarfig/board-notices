<?php

namespace fq\boardnotices\tests\domain;

use \fq\boardnotices\domain\date_condition;
use \fq\boardnotices\domain\date_comparator;

class date_comparator_test extends \PHPUnit_Framework_TestCase
{
	public function testSymmetricalBothEmpty()
	{
		$now = getdate();
		$start = new date_condition(array(0, 0, 0), $now);
		$end = new date_condition(array(0, 0, 0), $now);

		$comparator = new date_comparator($start, $end, $now);

		$this->assertTrue($comparator->areBothEmpty());
		$this->assertFalse($comparator->hasBothDays());
		$this->assertFalse($comparator->hasBothMonths());
		$this->assertFalse($comparator->hasBothYears());
		$this->assertFalse($comparator->hasBothFullDate());
		$this->assertTrue($comparator->areSymmetrical());
		$this->assertFalse($comparator->hasOnlyBothDays());
		$this->assertFalse($comparator->hasOnlyBothMonths());
		$this->assertFalse($comparator->hasOnlyBothYears());
		$this->assertFalse($comparator->hasOnlyBothDaysMonths());
		$this->assertFalse($comparator->hasOnlyBothDaysYears());
		$this->assertFalse($comparator->hasOnlyBothMonthsYears());
	}

	public function testSymmetricalBothDaysOnly()
	{
		$now = getdate();
		$start = new date_condition(array(6, 0, 0), $now);
		$end = new date_condition(array(9, 0, 0), $now);

		$comparator = new date_comparator($start, $end, $now);

		$this->assertFalse($comparator->areBothEmpty());
		$this->assertTrue($comparator->hasBothDays());
		$this->assertFalse($comparator->hasBothMonths());
		$this->assertFalse($comparator->hasBothYears());
		$this->assertFalse($comparator->hasBothFullDate());
		$this->assertTrue($comparator->areSymmetrical());
		$this->assertTrue($comparator->hasOnlyBothDays());
		$this->assertFalse($comparator->hasOnlyBothMonths());
		$this->assertFalse($comparator->hasOnlyBothYears());
		$this->assertFalse($comparator->hasOnlyBothDaysMonths());
		$this->assertFalse($comparator->hasOnlyBothDaysYears());
		$this->assertFalse($comparator->hasOnlyBothMonthsYears());
	}

	public function testSymmetricalBothMonthsOnly()
	{
		$now = getdate();
		$start = new date_condition(array(0, 6, 0), $now);
		$end = new date_condition(array(0, 9, 0), $now);

		$comparator = new date_comparator($start, $end, $now);

		$this->assertFalse($comparator->areBothEmpty());
		$this->assertFalse($comparator->hasBothDays());
		$this->assertTrue($comparator->hasBothMonths());
		$this->assertFalse($comparator->hasBothYears());
		$this->assertFalse($comparator->hasBothFullDate());
		$this->assertTrue($comparator->areSymmetrical());
		$this->assertFalse($comparator->hasOnlyBothDays());
		$this->assertTrue($comparator->hasOnlyBothMonths());
		$this->assertFalse($comparator->hasOnlyBothYears());
		$this->assertFalse($comparator->hasOnlyBothDaysMonths());
		$this->assertFalse($comparator->hasOnlyBothDaysYears());
		$this->assertFalse($comparator->hasOnlyBothMonthsYears());
	}

	public function testSymmetricalBothYearsOnly()
	{
		$now = getdate();
		$start = new date_condition(array(0, 0, 2018), $now);
		$end = new date_condition(array(0, 0, 2019), $now);

		$comparator = new date_comparator($start, $end, $now);

		$this->assertFalse($comparator->areBothEmpty());
		$this->assertFalse($comparator->hasBothDays());
		$this->assertFalse($comparator->hasBothMonths());
		$this->assertTrue($comparator->hasBothYears());
		$this->assertFalse($comparator->hasBothFullDate());
		$this->assertTrue($comparator->areSymmetrical());
		$this->assertFalse($comparator->hasOnlyBothDays());
		$this->assertFalse($comparator->hasOnlyBothMonths());
		$this->assertTrue($comparator->hasOnlyBothYears());
		$this->assertFalse($comparator->hasOnlyBothDaysMonths());
		$this->assertFalse($comparator->hasOnlyBothDaysYears());
		$this->assertFalse($comparator->hasOnlyBothMonthsYears());
	}

	public function testSymmetricalBothDaysMonthsOnly()
	{
		$now = getdate();
		$start = new date_condition(array(6, 1, 0), $now);
		$end = new date_condition(array(9, 3, 0), $now);

		$comparator = new date_comparator($start, $end, $now);

		$this->assertFalse($comparator->areBothEmpty());
		$this->assertTrue($comparator->hasBothDays());
		$this->assertTrue($comparator->hasBothMonths());
		$this->assertFalse($comparator->hasBothYears());
		$this->assertFalse($comparator->hasBothFullDate());
		$this->assertTrue($comparator->areSymmetrical());
		$this->assertFalse($comparator->hasOnlyBothDays());
		$this->assertFalse($comparator->hasOnlyBothMonths());
		$this->assertFalse($comparator->hasOnlyBothYears());
		$this->assertTrue($comparator->hasOnlyBothDaysMonths());
		$this->assertFalse($comparator->hasOnlyBothDaysYears());
		$this->assertFalse($comparator->hasOnlyBothMonthsYears());
	}

	public function testSymmetricalBothDaysYearsOnly()
	{
		$now = getdate();
		$start = new date_condition(array(6, 0, 2018), $now);
		$end = new date_condition(array(9, 0, 2019), $now);

		$comparator = new date_comparator($start, $end, $now);

		$this->assertFalse($comparator->areBothEmpty());
		$this->assertTrue($comparator->hasBothDays());
		$this->assertFalse($comparator->hasBothMonths());
		$this->assertTrue($comparator->hasBothYears());
		$this->assertFalse($comparator->hasBothFullDate());
		$this->assertTrue($comparator->areSymmetrical());
		$this->assertFalse($comparator->hasOnlyBothDays());
		$this->assertFalse($comparator->hasOnlyBothMonths());
		$this->assertFalse($comparator->hasOnlyBothYears());
		$this->assertFalse($comparator->hasOnlyBothDaysMonths());
		$this->assertTrue($comparator->hasOnlyBothDaysYears());
		$this->assertFalse($comparator->hasOnlyBothMonthsYears());
	}

	public function testSymmetricalBothMonthsYearsOnly()
	{
		$now = getdate();
		$start = new date_condition(array(0, 1, 2018), $now);
		$end = new date_condition(array(0, 3, 2019), $now);

		$comparator = new date_comparator($start, $end, $now);

		$this->assertFalse($comparator->areBothEmpty());
		$this->assertFalse($comparator->hasBothDays());
		$this->assertTrue($comparator->hasBothMonths());
		$this->assertTrue($comparator->hasBothYears());
		$this->assertFalse($comparator->hasBothFullDate());
		$this->assertTrue($comparator->areSymmetrical());
		$this->assertFalse($comparator->hasOnlyBothDays());
		$this->assertFalse($comparator->hasOnlyBothMonths());
		$this->assertFalse($comparator->hasOnlyBothYears());
		$this->assertFalse($comparator->hasOnlyBothDaysMonths());
		$this->assertFalse($comparator->hasOnlyBothDaysYears());
		$this->assertTrue($comparator->hasOnlyBothMonthsYears());
	}

	public function testSymmetricalBothFullDate()
	{
		$now = getdate();
		$start = new date_condition(array(6, 1, 2018), $now);
		$end = new date_condition(array(9, 3, 2019), $now);

		$comparator = new date_comparator($start, $end, $now);

		$this->assertFalse($comparator->areBothEmpty());
		$this->assertTrue($comparator->hasBothDays());
		$this->assertTrue($comparator->hasBothMonths());
		$this->assertTrue($comparator->hasBothYears());
		$this->assertTrue($comparator->hasBothFullDate());
		$this->assertTrue($comparator->areSymmetrical());
		$this->assertFalse($comparator->hasOnlyBothDays());
		$this->assertFalse($comparator->hasOnlyBothMonths());
		$this->assertFalse($comparator->hasOnlyBothYears());
		$this->assertFalse($comparator->hasOnlyBothDaysMonths());
		$this->assertFalse($comparator->hasOnlyBothDaysYears());
		$this->assertFalse($comparator->hasOnlyBothMonthsYears());
	}

	public function testMakingSureComparatorHoldsReference()
	{
		$now = getdate();
		$start = new date_condition(array(6, 1, 2018), $now);
		$end = new date_condition(array(9, 3, 2019), $now);

		$dates = new date_comparator($start, $end, $now);
		$this->assertTrue($dates->hasBothFullDate());
		$this->assertTrue($dates->areSymmetrical());

		$start->setDay(0);
		$end->setMonth(0);
		$this->assertFalse($dates->hasBothFullDate());
		$this->assertFalse($dates->areSymmetrical());
	}
}
