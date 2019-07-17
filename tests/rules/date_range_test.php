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
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testMultipleParameters($rule)
	{
		$this->assertTrue($rule->hasMultipleParameters());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testGetDisplayName($rule)
	{
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testGetDisplayUnit($rule)
	{
		$display = $rule->getDisplayUnit();
		$this->assertNotEmpty($display, "DisplayUnit is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testGetType($rule)
	{
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo(array('date', 'date')));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testGetDefault($rule)
	{
		$default = $rule->getDefault();
		$this->assertThat($default, $this->equalTo(array(array(0, 0, 0), array(0, 0, 0))));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testGetPossibleValues($rule)
	{
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testGetAvailableVars($rule)
	{
		$vars = $rule->getAvailableVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testGetTemplateVars($rule)
	{
		$vars = $rule->getTemplateVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function getDefaultValue($rule)
	{
		$this->assertEquals(array(array(0, 0, 0, array(0, 0, 0))), $rule->getDefault());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testCannotValidateNullConditions($rule)
	{
		$this->assertFalse($rule->validateValues(null));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testCannotValidateEmptyConditions($rule)
	{
		$this->assertFalse($rule->validateValues(array(array(0, 0, 0), array(0, 0, 0))));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
	 */
	public function testCannotValidateWrongConditions($rule)
	{
		$this->assertFalse($rule->validateValues(array(array(1, 2, 3, 4), array(1, 2, 3, 4))));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date_range $rule
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
		$datetime = $api->createDateTime($time);
		$now = phpbb_gmgetdate($datetime->getTimestamp() + $datetime->getOffset());
		return $now;
	}

	private function buildConditions(\DateTimeImmutable $datetime, $start, $end)
	{
		$conditions = array();
		$conditions[] = $this->buildDateCondition($datetime, $start[0], $start[1], $start[2]);
		$conditions[] = $this->buildDateCondition($datetime, $end[0], $end[1], $end[2]);
		return $conditions;
	}

	/**
	 * null values mean "any" and zero values mean "current one"
	 * @return int[]
	 */
	private function buildDateCondition(\DateTimeImmutable $datetime, $day = null, $month = null, $year = null)
	{
		$computeDatetime = $datetime;
		if (!empty($day))
		{
			if ($day < 0)
			{
				$computeDatetime = $computeDatetime->sub(new \DateInterval('P' . abs($day) . 'D'));
			}
			else
			{
				$computeDatetime = $computeDatetime->add(new \DateInterval("P{$day}D"));
			}
		}
		if (!empty($month))
		{
			if ($month < 0)
			{
				$computeDatetime = $computeDatetime->sub(new \DateInterval('P' . abs($month) . 'M'));
			}
			else
			{
				$computeDatetime = $computeDatetime->add(new \DateInterval("P{$month}M"));
			}
		}
		if (!empty($year))
		{
			if ($year < 0)
			{
				$computeDatetime = $computeDatetime->sub(new \DateInterval('P' . abs($year) . 'Y'));
			}
			else
			{
				$computeDatetime = $computeDatetime->add(new \DateInterval("P{$year}Y"));
			}
		}
		$conditions = array(0, 0, 0);
		if (!is_null($day))
		{
			$conditions[0] = $computeDatetime->format('d');
		}
		if (!is_null($month))
		{
			$conditions[1] = $computeDatetime->format('m');
		}
		if (!is_null($year))
		{
			$conditions[2] = $computeDatetime->format('Y');
		}
		return $conditions;
	}

	public function getTestBuildConditionData()
	{
		$data = array();
		$timezones = $this->getTimezones();
		foreach ($timezones as $timezone)
		{
			$data = array_merge($data, array(
				array($timezone[0], '2010-01-01', null, null, null, array(0, 0, 0)),
				array($timezone[0], '2010-01-01', 0, 0, 0, array(1, 1, 2010)),
				array($timezone[0], '2011-10-05', 0, 0, 0, array(5, 10, 2011)),
				array($timezone[0], '2012-10-05', 1, -1, null, array(6, 9, 0)),
				array($timezone[0], '2012-01-01', -1, 0, 0, array(31, 12, 2011)),
				array($timezone[0], '2012-12-30', 3, 0, 0, array(2, 1, 2013)),
				array($timezone[0], '2012-12-30', null, 0, null, array(0, 12, 0)),
			));
		}
		return $data;
	}

	/**
	 * @dataProvider getTestBuildConditionData
	 */
	public function testBuildCondition($timezone, $date, $day, $month, $year, $result)
	{
		$current_timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
		$datetime = \DateTimeImmutable::createFromFormat('!Y-m-d', $date, new \DateTimeZone($timezone));
		$condition = $this->buildDateCondition($datetime, $day, $month, $year);
		$this->assertEquals($result, $condition);

		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	public function getTestData()
	{
		$data = array();
		$timezones = $this->getTimezones();
		foreach ($timezones as $timezone)
		{
			foreach (array(null, '2019-01-01', '2018-12-31', '2019-02-28', '2019-03-01', '2015-03-30') as $now)
			{
				// array(timezone, now, start, end, result)
				$data = array_merge($data, array(
					array($timezone[0], $now, array(null, null, null), array(null, null, null), true),		// Empty
					array($timezone[0], $now, array(0, 0, 0), array(0, 0, 0), true),						// Empty
					// Symmetrical type 1
					array($timezone[0], $now, array(0, null, null), array(0, null, null), true),			// This day
					array($timezone[0], $now, array(0, null, null), array(1, null, null), true),			// This day and next
					array($timezone[0], $now, array(-1, null, null), array(0, null, null), true),			// This day and previous
					array($timezone[0], $now, array(-1, null, null), array(-1, null, null), false),			// Last day
					array($timezone[0], $now, array(1, null, null), array(1, null, null), false),			// Next day
					// Symmetrical type 2
					array($timezone[0], $now, array(null, 0, null), array(null, 0, null), true),			// This month
					array($timezone[0], $now, array(null, 0, null), array(null, 1, null), true),			// This month and next
					array($timezone[0], $now, array(null, -1, null), array(null, 0, null), true),			// This month and previous
					array($timezone[0], $now, array(null, -2, null), array(null, -2, null), false),			// Month before
					array($timezone[0], $now, array(null, 1, null), array(null, 1, null), false),			// Next month
					// Symmetrical type 3
					array($timezone[0], $now, array(null, null, 0), array(null, null, 0), true),			// This year
					array($timezone[0], $now, array(null, null, 0), array(null, null, 1), true),			// This year and next
					array($timezone[0], $now, array(null, null, -1), array(null, null, 0), true),			// This year and previous
					array($timezone[0], $now, array(null, null, 1), array(null, null, -1), false),			// Years the other way around
					array($timezone[0], $now, array(null, null, -1), array(null, null, -1), false),			// Last year
					array($timezone[0], $now, array(null, null, 1), array(null, null, 1), false),			// Next year
					// Symmetrical type 4
					array($timezone[0], $now, array(0, 0, null), array(0, 0, null), true),					// From today to today
					array($timezone[0], $now, array(0, 0, null), array(1, 0, null), true),					// From today to tomorrow
					array($timezone[0], $now, array(-1, 0, null), array(0, 0, null), true),					// From yesterday to today
					array($timezone[0], $now, array(-1, 0, null), array(-1, 0, null), false),				// Previous day
					array($timezone[0], $now, array(1, 0, null), array(1, 0, null), false),					// Next day
					array($timezone[0], $now, array(-2, -1, null), array(-2, -1, null), false),				// Previous month
					array($timezone[0], $now, array(0, 1, null), array(0, 1, null), false),					// Next month
					array($timezone[0], $now, array(0, 1, null), array(0, -1, null), false),				// From next month to previous month
					// Symmetrical type 5
					array($timezone[0], $now, array(null, 0, 0), array(null, 0, 0), true),					// This month this year
					array($timezone[0], $now, array(null, -1, 0), array(null, 0, 0), true),					// Previous month to this month
					array($timezone[0], $now, array(null, 0, 0), array(null, 1, 0), true),					// This month to next month
					array($timezone[0], $now, array(null, 1, -1), array(null, -2, 0), false),				// In between last year and this year minus 2 months
					array($timezone[0], $now, array(null, 1, 0), array(null, -1, 1), false),				// In between this year +1 month and next year
					array($timezone[0], $now, array(null, 1, -1), array(null, 1, 0), true),					// In between last year and this year plus 1 month
					array($timezone[0], $now, array(null, -1, 0), array(null, -1, 1), true),				// In between this year -1 month and next year
					// Symmetrical type 6
					array($timezone[0], $now, array(0, null, 0), array(0, null, 0), true),					// Today this year
					array($timezone[0], $now, array(-1, null, 0), array(0, null, 0), true),					// Yesterday/today this year
					array($timezone[0], $now, array(0, null, 0), array(1, null, 0), true),					// Today/tomorrow this year
					array($timezone[0], $now, array(-1, null, 0), array(-1, null, 0), false),				// Yesterday this year
					array($timezone[0], $now, array(1, null, 0), array(1, null, 0), false),					// Tomorrow this year
					array($timezone[0], $now, array(0, null, -1), array(0, null, 0), true),					// Last year to this year
					array($timezone[0], $now, array(0, null, 0), array(0, null, 1), true),					// This year to next year
					// Type 7 = full date
					array($timezone[0], $now, array(0, 0, -1), array(-1, 0, 0), false),						// Until yesterday
					array($timezone[0], $now, array(1, 0, 0), array(0, 0, 1), false),						// Starting tomorrow
				));
			}
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
		if ($date !== null)
		{
			// Fix current date for testing
			$rule->setDate($date);
		}
		if ($date == null)
		{
			$date = date('Y-m-d');
		}
		$datetime = \DateTimeImmutable::createFromFormat('!Y-m-d', $date, new \DateTimeZone($timezone));
		$conditions = $this->buildConditions($datetime, $start, $end);
		$this->assertEquals(
			$result,
			$rule->isTrue(serialize($conditions)),
			"Datetime: {$datetime->format('Y-m-d')}; " .
			"From: mday={$conditions[0][0]} month={$conditions[0][1]} year={$conditions[0][2]}; " .
			"To: mday={$conditions[1][0]} month={$conditions[1][1]} year={$conditions[1][2]}");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

}
