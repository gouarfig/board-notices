<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\date;

class date_test extends rule_test_base
{

	public function testInstance()
	{
		$user = $this->getUser();
		$rule = new date($this->getSerializer(), $user);
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

	private function getDatetime(\phpbb\user $user, $time = null)
	{
		$now = $user->create_datetime($time);
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
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone('Pacific/Midway');
		$offset = $user->create_datetime()->getOffset();
		$date_in_timezone = getdate($local_timestamp + $offset);
		// 12h over there, same day
		$this->assertEquals(12, $date_in_timezone['hours']);
		$this->assertEquals(31, $date_in_timezone['mday']);
		$this->assertEquals(1, $date_in_timezone['mon']);
		$user->timezone = new \DateTimeZone('Pacific/Auckland');
		$offset = $user->create_datetime()->getOffset();
		$date_in_timezone = getdate($local_timestamp + $offset);
		// 12h over there, but the next day
		$this->assertEquals(12, $date_in_timezone['hours']);
		$this->assertEquals(1, $date_in_timezone['mday']);
		$this->assertEquals(2, $date_in_timezone['mon']);
		// That's good, we can calculate date with user timezone in mind
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testEmptyConditionIsTrue($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameDayIsTrue($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameMonthIsTrue($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, null, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameYearIsTrue($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, null, null, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameDayAndMonthIsTrue($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameDayAndYearIsTrue($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, null, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameMonthAndYearIsTrue($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, null, 0, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameDayAndMonthAndYearIsTrue($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, 0, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testPreviousDayOfFirstDayOfTheMonthIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user, '2019-10-01');
		$conditions = $this->buildConditions($now, -1, 0, 0);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");

		$conditions = $this->buildConditions($now, -1, null, null);
		$now = getdate();
		if ($now['mday'] == 30)
		{
			$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		}
		else
		{
			$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		}
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testPreviousDayIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, -1, 0, 0);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");

		$conditions = $this->buildConditions($now, -1, null, null);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testNextDayIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 1, 0, 0);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");

		$conditions = $this->buildConditions($now, 1, null, null);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testPreviousMonthOfJanuaryIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user, '2019-01-10');
		$conditions = $this->buildConditions($now, 0, -1, 0);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");

		$conditions = $this->buildConditions($now, null, -1, null);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testPreviousMonthIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, -1, 0);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");

		$conditions = $this->buildConditions($now, null, -1, null);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testNextMonthIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, 1, 0);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");

		$conditions = $this->buildConditions($now, null, 1, null);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testPreviousYearIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, 0, -1);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");

		$conditions = $this->buildConditions($now, null, null, -1);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testNextYearIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($this->getSerializer(), $user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, 0, 1);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");

		$conditions = $this->buildConditions($now, null, null, 1);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]} (array count=" . count($conditions) . ")");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}
}
