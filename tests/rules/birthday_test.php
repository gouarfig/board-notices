<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\birthday;
use fq\boardnotices\tests\mock\mock_api;

class birthday_test extends rule_test_base
{
	public function testInstance()
	{
		$api = new mock_api();
		$rule = new birthday($this->getSerializer(), $api);
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
	public function testCanValidateRuleValues($rule)
	{
		$this->assertTrue($rule->validateValues(null));
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
		$api = new mock_api();
		$api->setTimezone($timezone);
		$birthday = $api->createDateTime();
		$birthday = phpbb_gmgetdate($birthday->getTimestamp() + $birthday->getOffset() + $seconds_added);
		$api->setUserBirthday(sprintf('%2d-%2d-%4d', $birthday['mday'], $birthday['mon'], $birthday['year'] - $age_added));
		$rule = new birthday($this->getSerializer(), $api);
		return array($api, $rule);
	}

	public function testBirthdayIsFalseIfNotEntered()
	{
		$api = new mock_api();
		$rule = new birthday($this->getSerializer(), $api);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayIsTrue($timezone)
	{
		$current_timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
		list($api, $rule) = $this->buildRuleWithBirthday($timezone, 0);
		$this->assertTrue($rule->isTrue(null), $api->getUserBirthday() . ' birthday is not today!');
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayLastMonthIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
		list($api, $rule) = $this->buildRuleWithBirthday($timezone, -(24*60*60*32));
		$this->assertFalse($rule->isTrue(null), $api->getUserBirthday() . ' birthday is not today!');
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayYesterdayIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
		list($api, $rule) = $this->buildRuleWithBirthday($timezone, -(24*60*60));
		$this->assertFalse($rule->isTrue(null), $api->getUserBirthday() . ' birthday is not today!');
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayTomorrowIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
		list($api, $rule) = $this->buildRuleWithBirthday($timezone, 24*60*60);
		$this->assertFalse($rule->isTrue(null), $api->getUserBirthday() . ' birthday is not today!');
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayNextMonthIsFalse($timezone)
	{
		$current_timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
		list($api, $rule) = $this->buildRuleWithBirthday($timezone, 24*60*60*32);
		$this->assertFalse($rule->isTrue(null), $api->getUserBirthday() . ' birthday is not today!');
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testAge($timezone)
	{
		$current_timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
		list($api, $rule) = $this->buildRuleWithBirthday($timezone, 0, 30);
		$rule->isTrue(null);
		$age = $rule->getTemplateVars();
		$this->assertTrue($age['AGE'] == 30, "Age is not 30 but {$age['AGE']}");
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}
}
