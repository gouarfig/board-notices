<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\anniversary;
use fq\boardnotices\tests\mock\mock_api;

class anniversary_test extends rule_test_base
{
	public function testInstance()
	{
		$api = new mock_api();
		$rule = new anniversary($this->getSerializer(), $api);
		$this->assertNotNull($rule);

		return $rule;
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\anniversary $rule
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
		$this->assertThat($vars, $this->contains('ANNIVERSARY'));
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
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testTodayIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$api = new mock_api();
		$api->setTimezone($timezone);
		$api->setUserRegistrationDate(time());
		$rule = new anniversary($this->getSerializer(), $api);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testOneHourBeforeTodayIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$api = new mock_api();
		$api->setTimezone($timezone);
		$api->setUserRegistrationDate(time() - (60*60));
		$rule = new anniversary($this->getSerializer(), $api);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testOneMonthBeforeTodayIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$api = new mock_api();
		$api->setTimezone($timezone);
		$api->setUserRegistrationDate(time() - (60*60*24*32));
		$rule = new anniversary($this->getSerializer(), $api);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testTodayPlusOneHourIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$api = new mock_api();
		$api->setTimezone($timezone);
		$api->setUserRegistrationDate(time() + (60*60));
		$rule = new anniversary($this->getSerializer(), $api);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testTodayPlusOneMonthIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$api = new mock_api();
		$api->setTimezone($timezone);
		$api->setUserRegistrationDate(time() + (60*60*24*32));
		$rule = new anniversary($this->getSerializer(), $api);
		$this->assertFalse($rule->isTrue(null));
	}


	private function buildRuleWithLastYearUserRegistration($timezone)
	{
		$api = new mock_api();
		$api->setTimezone($timezone);
		$api->setUserRegistrationDate(strtotime('last year'));
		$rule = new anniversary($this->getSerializer(), $api);
		return array($api, $rule);
	}
	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testLastYearIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		list($api, $rule) = $this->buildRuleWithLastYearUserRegistration($timezone);
		$this->assertTrue($rule->isTrue(null), date('r', $api->getUserRegistrationDate()) . ' is not last year!');
		return $rule;
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testOneYearAfter($timezone)
	{
		date_default_timezone_set($timezone);
		list($api, $rule) = $this->buildRuleWithLastYearUserRegistration($timezone);
		// Run the rule to calculate the anniversary
		$rule->isTrue(null);
		$vars = $rule->getTemplateVars();
		$this->assertEquals(1, $vars['ANNIVERSARY']);
	}
}
