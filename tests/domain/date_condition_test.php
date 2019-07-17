<?php

namespace fq\boardnotices\tests\domain;

use \fq\boardnotices\domain\date_condition;

class date_condition_test extends \PHPUnit_Framework_TestCase
{
	public function testDayOnly()
	{
		$value = array(5, 0, 0);
		$date = new date_condition($value, getdate());

		$this->assertTrue($date->hasDay());
		$this->assertTrue($date->hasOnlyDay());

		$this->assertFalse($date->hasMonth());
		$this->assertFalse($date->hasOnlyMonth());

		$this->assertFalse($date->hasYear());
		$this->assertFalse($date->hasOnlyYear());

		$this->assertFalse($date->hasOnlyDayMonth());
		$this->assertFalse($date->hasOnlyDayYear());
		$this->assertFalse($date->hasOnlyMonthYear());

		$this->assertFalse($date->isEmpty());
		$this->assertFalse($date->isFullDate());

		$this->assertEquals(5, $date->getDay());
		$this->assertEquals(0, $date->getMonth());
		$this->assertEquals(0, $date->getYear());
		$this->assertEquals($value, $date->getValue());
	}

	public function testMonthOnly()
	{
		$value = array(0, 5, 0);
		$date = new date_condition($value, getdate());

		$this->assertFalse($date->hasDay());
		$this->assertFalse($date->hasOnlyDay());

		$this->assertTrue($date->hasMonth());
		$this->assertTrue($date->hasOnlyMonth());

		$this->assertFalse($date->hasYear());
		$this->assertFalse($date->hasOnlyYear());

		$this->assertFalse($date->hasOnlyDayMonth());
		$this->assertFalse($date->hasOnlyDayYear());
		$this->assertFalse($date->hasOnlyMonthYear());

		$this->assertFalse($date->isEmpty());
		$this->assertFalse($date->isFullDate());

		$this->assertEquals(0, $date->getDay());
		$this->assertEquals(5, $date->getMonth());
		$this->assertEquals(0, $date->getYear());
		$this->assertEquals($value, $date->getValue());
	}

	public function testYearOnly()
	{
		$value = array(0, 0, 2019);
		$date = new date_condition($value, getdate());

		$this->assertFalse($date->hasDay());
		$this->assertFalse($date->hasOnlyDay());

		$this->assertFalse($date->hasMonth());
		$this->assertFalse($date->hasOnlyMonth());

		$this->assertTrue($date->hasYear());
		$this->assertTrue($date->hasOnlyYear());

		$this->assertFalse($date->hasOnlyDayMonth());
		$this->assertFalse($date->hasOnlyDayYear());
		$this->assertFalse($date->hasOnlyMonthYear());

		$this->assertFalse($date->isEmpty());
		$this->assertFalse($date->isFullDate());

		$this->assertEquals(0, $date->getDay());
		$this->assertEquals(0, $date->getMonth());
		$this->assertEquals(2019, $date->getYear());
		$this->assertEquals($value, $date->getValue());
	}

	public function testGeneratingDateTime()
	{
		$expected = \DateTime::createFromFormat('!Y-m-d', '2010-03-02');
		$date = new date_condition(
			array(2, 3, 2010),
			getdate()
		);
		$this->assertEquals($expected, $date->getDateTime());
	}

}
