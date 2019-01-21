<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\birthday;

class birthday_test extends rule_test_base
{
	public function testInstance()
	{
		$user = $this->getUser();
		$rule = new birthday($user);
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
	 * @param \fq\boardnotices\rules\anniversary $rule
	 */
	public function testGetType($rule)
	{
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('n/a'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\anniversary $rule
	 */
	public function testGetPossibleValues($rule)
	{
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\anniversary $rule
	 */
	public function testGetAvailableVars($rule)
	{
		$vars = $rule->getAvailableVars();
		$this->assertThat($vars, $this->contains('AGE'));
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

	/**
	 *
	 */
	private function buildRuleWithBirthday($timezone, $seconds_added = 0, $age_added = 30)
	{
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$birthday = $user->create_datetime();
		$birthday = phpbb_gmgetdate($birthday->getTimestamp() + $birthday->getOffset() + $seconds_added);
		$user->data['user_birthday'] = sprintf('%2d-%2d-%4d', $birthday['mday'], $birthday['mon'], $birthday['year'] - $age_added);
		$rule = new birthday($user);
		return array($user, $rule);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		list($user, $rule) = $this->buildRuleWithBirthday($timezone, 0);
		$this->assertTrue($rule->isTrue(null), $user->data['user_birthday'] . ' birthday is not today!');
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayLastMonthIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		list($user, $rule) = $this->buildRuleWithBirthday($timezone, -(24*60*60*32));
		$this->assertFalse($rule->isTrue(null), $user->data['user_birthday'] . ' birthday is not today!');
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayYesterdayIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		list($user, $rule) = $this->buildRuleWithBirthday($timezone, -(24*60*60));
		$this->assertFalse($rule->isTrue(null), $user->data['user_birthday'] . ' birthday is not today!');
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayTomorrowIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		list($user, $rule) = $this->buildRuleWithBirthday($timezone, 24*60*60);
		$this->assertFalse($rule->isTrue(null), $user->data['user_birthday'] . ' birthday is not today!');
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayNextMonthIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		list($user, $rule) = $this->buildRuleWithBirthday($timezone, 24*60*60*32);
		$this->assertFalse($rule->isTrue(null), $user->data['user_birthday'] . ' birthday is not today!');
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testAge($timezone)
	{
		date_default_timezone_set($timezone);
		list($user, $rule) = $this->buildRuleWithBirthday($timezone, 0, 30);
		$rule->isTrue(null);
		$age = $rule->getTemplateVars();
		$this->assertTrue($age['AGE'] == 30, "Age is not 30 but {$age['AGE']}");
	}
}
