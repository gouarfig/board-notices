<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\date_range;
use fq\boardnotices\tests\mock\mock_api;

class date_range_test extends rule_test_base
{

	public function testInstance()
	{
		$api = new mock_api();
		$rule = new date_range($this->getSerializer(), $api);
		$this->assertNotNull($rule);

		return $rule;
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testMultipleParameters($rule)
	{
		$this->assertTrue($rule->hasMultipleParameters());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetDisplayName($rule)
	{
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetDisplayUnit($rule)
	{
		$display = $rule->getDisplayUnit();
		$this->assertNotEmpty($display, "DisplayUnit is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetType($rule)
	{
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo(array('date', 'date')));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetPossibleValues($rule)
	{
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetAvailableVars($rule)
	{
		$vars = $rule->getAvailableVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetTemplateVars($rule)
	{
		$vars = $rule->getTemplateVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testIsFalseWithNullConditions($rule)
	{
		$this->assertFalse($rule->isTrue(null));
		$this->assertFalse($rule->isTrue(serialize(null)));
	}

	/**
	 * Lists a few random and extreme timezones
	 *
	 * @return array(string)
	 */
	public function getTimezones()
	{
		return array(
			array('Pacific/Midway'),
			array('Europe/London'),
			array('Pacific/Auckland'),
			array('Pacific/Norfolk'),
			array('Pacific/Kiritimati'),
			array('America/St_Johns'),
		);
	}

	private function getDatetime(\fq\boardnotices\service\phpbb\api_interface $api, $time = null)
	{
		$now = $api->createDateTime($time);
		$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());
		return $now;
	}

	private function buildConditions($now, $start, $end)
	{
		$conditions = array();
		$conditions[] = $this->buildDateCondition($now, $start[0], $start[1], $start[2]);
		$conditions[] = $this->buildDateCondition($now, $end[0], $end[1], $end[2]);
		return $conditions;
	}

	/**
	 * null values mean "any" and zero values mean "current one"
	 * @return int[]
	 */
	private function buildDateCondition($now, $day = null, $month = null, $year = null)
	{
		$conditions = array(0, 0, 0);
		if (!is_null($day))
		{
			$day = intval($day);
			if ($day == 0)
			{
				$conditions[0] = $now['mday'];
			}
			else
			{
				$conditions[0] = $now['mday'] + $day;
				// We can't get to zero, it means "any"
				if ($conditions[0] == 0)
				{
					$conditions[0] = 30;
				}
			}
		}

		if (!is_null($month))
		{
			$month = intval($month);
			if ($month == 0)
			{
				$conditions[1] = $now['mon'];
			}
			else
			{
				$conditions[1] = $now['mon'] + $month;
				// We can't get to zero, it means "any"
				if ($conditions[1] == 0)
				{
					$conditions[1] = 12;
				}
			}
		}

		if (!is_null($year))
		{
			$year = intval($year);
			if ($year == 0)
			{
				$conditions[2] = $now['year'];
			}
			else
			{
				$conditions[2] = $now['year'] + $year;
			}
		}
		return $conditions;
	}

	public function getTestData()
	{
		$now = getdate();
		$data = array();
		$timezones = $this->getTimezones();
		foreach ($timezones as $timezone)
		{
			// array(timezone, now, start, end, result)
			$data = array_merge($data, array(
				array($timezone[0], null, array(null, null, null), array(null, null, null), true),		// Empty
				array($timezone[0], null, array(0, 0, 0), array(0, 0, 0), true),						// Empty
				array($timezone[0], null, array(-1, 0, 0), array(0, 0, 0), true),						// Previous day until today
				array($timezone[0], null, array(0, 0, 0), array(1, 0, 0), true),						// From today to tomorrow
				array($timezone[0], null, array(0, -1, 0), array(0, 0, 0), true),						// Previous month until this month
				array($timezone[0], null, array(0, 0, 0), array(0, 1, 0), true),						// From this month to next
				array($timezone[0], null, array(0, 0, -1), array(0, 0, 0), true),						// Previous year until this year
				array($timezone[0], null, array(0, 0, 0), array(0, 0, 1), true),						// From this year to next
			));
		}
		return $data;
	}

	/**
	 * @dataProvider getTestData
	 */
	public function testConditions($timezone, $date, $start, $end, $result)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$api = new mock_api();
		$api->setTimezone($timezone);
		$rule = new date_range($this->getSerializer(), $api);
		$now = $this->getDatetime($api, $date);
		$conditions = $this->buildConditions($now, $start, $end);
		$this->assertEquals(
			$result,
			$rule->isTrue(serialize($conditions)),
			"From: mday={$conditions[0][0]} month={$conditions[0][1]} year={$conditions[0][2]}; " .
			" To: mday={$conditions[1][0]} month={$conditions[1][1]} year={$conditions[1][2]}");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

}
