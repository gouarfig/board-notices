<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\date;
use fq\boardnotices\tests\mock\mock_api;

class date_test extends rule_test_base
{

	public function testInstance()
	{
		$api = new mock_api();
		$rule = new date($this->getSerializer(), $api);
		$this->assertNotNull($rule);

		return $rule;
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
	public function testGetType($rule)
	{
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('date'));
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

	/**
	 * null values mean "any" and zero values mean "current one"
	 * @return int[]
	 */
	private function buildConditions($now, $day = null, $month = null, $year = null)
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

	/**
	 * This was used to make sense of all the timezone differences between the php functions
	 * I keep it here for reference...
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testTimestampGivesCorrectDate($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$timestamp = gmmktime(23, 55, 5, 1, 31, 2017);	// Timezone independant
		$local_timestamp = mktime(23, 55, 5, 1, 31, 2017);	// In local timezone
		$this->assertEquals($timestamp - date('Z'), $local_timestamp, "Wrong timezone difference!");

		// getdate() only work in local timezone (offset is automatically added inside the function)
		$utc = getdate($timestamp - date('Z'));
		$this->assertEquals(23, $utc['hours'], "It should be 23H");
		$this->assertEquals(31, $utc['mday'], "It should be the 31th");
		$this->assertEquals(1, $utc['mon'], "It should be January");

		// All good for local timezone, now try with user timezone
		$api = new mock_api();
		$api->setTimezone('Pacific/Midway');
		$offset = $api->createDateTime()->getOffset();
		$date_in_timezone = getdate($local_timestamp + $offset);
		// 12h over there, same day
		$this->assertEquals(12, $date_in_timezone['hours']);
		$this->assertEquals(31, $date_in_timezone['mday']);
		$this->assertEquals(1, $date_in_timezone['mon']);

		$api->setTimezone('Pacific/Auckland');
		$offset = $api->createDateTime()->getOffset();
		$date_in_timezone = getdate($local_timestamp + $offset);
		// 12h over there, but the next day
		$this->assertEquals(12, $date_in_timezone['hours']);
		$this->assertEquals(1, $date_in_timezone['mday']);
		$this->assertEquals(2, $date_in_timezone['mon']);
		// That's good, we can calculate date with user timezone in mind
		date_default_timezone_set($current_timezone);
	}

	public function getTestData()
	{
		$now = getdate();
		$data = array();
		$timezones = $this->getTimezones();
		foreach ($timezones as $timezone)
		{
			// array(timezone, now, day, month, year, result)
			$data = array_merge($data, array(
				array($timezone[0], null, null, null, null, true),			// Empty
				array($timezone[0], null, 0, null, null, true),				// Same day
				array($timezone[0], null, null, 0, null, true),				// Same month
				array($timezone[0], null, null, null, 0, true),				// Same year
				array($timezone[0], null, 0, 0, null, true),				// Same day and month
				array($timezone[0], null, 0, null, 0, true),				// Same day and year
				array($timezone[0], null, null, 0, 0, true),				// Same month and year
				array($timezone[0], null, 0, 0, 0, true),					// Same day, month and year
				array($timezone[0], null, -1, 0, 0, false),					// Previous day, same month and year
				array($timezone[0], null, -1, null, null, false),			// Previous day
				array($timezone[0], '2019-10-01', -1, 0, 0, false),						// Previous day, same month and year
				array($timezone[0], '2019-10-01', -1, null, null, $now['mday'] == 30),	// Previous day
				array($timezone[0], null, 1, 0, 0, false),					// Next day, same month and year
				array($timezone[0], null, 1, null, null, false),			// Next day
				array($timezone[0], null, 0, -1, 0, false),					// Previous month, same day and year
				array($timezone[0], null, null, -1, null, false),			// Previous month
				array($timezone[0], '2019-10-01', 0, -1, 0, false),			// Previous month of january, same day and year
				array($timezone[0], '2019-10-01', null, -1, null, false),	// Previous month of january
				array($timezone[0], null, 0, 1, 0, false),					// Next month, same day and year
				array($timezone[0], null, null, 1, null, false),			// Next month
				array($timezone[0], null, 0, 0, -1, false),					// Same day and month, previous year
				array($timezone[0], null, null, null, -1, false),			// Previous year
				array($timezone[0], null, 0, 0, 1, false),					// Same day and month, next year
				array($timezone[0], null, null, null, 1, false),			// Next year
			));
		}
		return $data;
	}

	/**
	 * @dataProvider getTestData
	 */
	public function testConditions($timezone, $date, $day, $month, $year, $result)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$api = new mock_api();
		$api->setTimezone($timezone);
		$rule = new date($this->getSerializer(), $api);
		$now = $this->getDatetime($api, $date);
		$conditions = $this->buildConditions($now, $day, $month, $year);
		$this->assertEquals(
			$result,
			$rule->isTrue(serialize($conditions)),
			"Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

}
