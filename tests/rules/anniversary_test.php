<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\anniversary;

class anniversary_test extends rule_test_base
{

	public function testInstance()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$rule = new anniversary($user);
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
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$user->data['user_regdate'] = time();
		$rule = new anniversary($user);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testOneHourBeforeTodayIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$user->data['user_regdate'] = time() - (60*60);
		$rule = new anniversary($user);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testOneMonthBeforeTodayIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$user->data['user_regdate'] = time() - (60*60*24*32);
		$rule = new anniversary($user);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testTodayPlusOneHourIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$user->data['user_regdate'] = time() + (60*60);
		$rule = new anniversary($user);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testTodayPlusOneMonthIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$user->data['user_regdate'] = time() + (60*60*24*32);
		$rule = new anniversary($user);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 *
	 * @return anniversary
	 */
	private function buildRuleWithLastYearUserRegistration($timezone)
	{
		$user = $this->getUser();
		$user->timezone = new \DateTimeZone($timezone);
		$user->data['user_regdate'] = strtotime('last year');
		$rule = new anniversary($user);
		return array($user, $rule);
	}
	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testLastYearIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		list($user, $rule) = $this->buildRuleWithLastYearUserRegistration($timezone);
		$this->assertTrue($rule->isTrue(null), date('r', $user->data['user_regdate']) . ' is not last year!');
		return $rule;
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testOneYearAfter($timezone)
	{
		date_default_timezone_set($timezone);
		list($user, $rule) = $this->buildRuleWithLastYearUserRegistration($timezone);
		// Run the rule to calculate the anniversary
		$rule->isTrue(null);
		$vars = $rule->getTemplateVars();
		$this->assertEquals(1, $vars['ANNIVERSARY']);
	}
}
