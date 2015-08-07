<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\date;

class date_test extends \phpbb_test_case
{
	public function testInstance()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$lang = &$user->lang;
		include 'phpBB/ext/fq/boardnotices/language/en/boardnotices_acp.php';
		$rule = new date($user);
		$this->assertThat($rule, $this->logicalNot($this->equalTo(null)));

		return $rule;
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetDisplayName($rule)
	{
		$display = $rule->getDisplayName();
		$this->assertTrue((strpos($display, "date") !== false), "Wrong DisplayName: '{$display}'");
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

	public function getDatetime($user)
	{
		$now = $user->create_datetime();
		$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());
		return $now;
	}

	public function buildConditions($now, $day = null, $month = null, $year = null)
	{
		$conditions = array(0 ,0, 0);
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
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testEmptyConditionIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameDayIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameMonthIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, null, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameYearIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, null, null, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameDayAndMonthIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameDayAndYearIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, null, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameMonthAndYearIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, null, 0, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testSameDayAndMonthAndYearIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, 0, 0);
		$this->assertTrue($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testPreviousDayIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, -1, 0, 0);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");

		$conditions = $this->buildConditions($now, -1, null, null);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testNextDayIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 1, 0, 0);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");

		$conditions = $this->buildConditions($now, 1, null, null);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testPreviousMonthIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, -1, 0);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");

		$conditions = $this->buildConditions($now, null, -1, null);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testNextMonthIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, 1, 0);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");

		$conditions = $this->buildConditions($now, null, 1, null);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testPreviousYearIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, 0, -1);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");

		$conditions = $this->buildConditions($now, null, null, -1);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testNextYearIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->timezone = new \DateTimeZone($timezone);
		$rule = new date($user);
		$now = $this->getDatetime($user);
		$conditions = $this->buildConditions($now, 0, 0, 1);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");

		$conditions = $this->buildConditions($now, null, null, 1);
		$this->assertFalse($rule->isTrue(serialize($conditions)), "Conditions should be met: mday={$conditions[0]} month={$conditions[1]} year={$conditions[2]}");
	}
}
