<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\birthday;

class birthday_test extends \phpbb_test_case
{
	public function testInstance()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$lang = &$user->lang;
		include 'phpBB/ext/fq/boardnotices/language/en/boardnotices_acp.php';
		$rule = new birthday($user);
		$this->assertThat($rule, $this->logicalNot($this->equalTo(null)));

		return $rule;
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\anniversary $rule
	 */
	public function testGetDisplayName($rule)
	{
		$display = $rule->getDisplayName();
		$this->assertTrue((strpos($display, "birthday") !== false), "Wrong DisplayName: '{$display}'");
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
	 * @return anniversary
	 */
	private function buildRuleWithBirthdayYesterday()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$now = $user->create_datetime();
		$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset() - 24*60*60);
		$user->data['user_birthday'] = sprintf('%2d-%2d-%4d', $now['mday'], $now['mon'], '1974');
		$rule = new birthday($user);
		return array($user, $rule);
	}

	/**
	 *
	 * @return anniversary
	 */
	private function buildRuleWithBirthdayTomorrow()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$now = $user->create_datetime();
		$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset() + 24*60*60);
		$user->data['user_birthday'] = sprintf('%2d-%2d-%4d', $now['mday'], $now['mon'], '1974');
		$rule = new birthday($user);
		return array($user, $rule);
	}

	/**
	 *
	 * @return anniversary
	 */
	private function buildRuleWithBirthdayToday()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$now = $user->create_datetime();
		$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());
		$user->data['user_birthday'] = sprintf('%2d-%2d-%4d', $now['mday'], $now['mon'], '1974');
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
		list($user, $rule) = $this->buildRuleWithBirthdayToday();
		$this->assertTrue($rule->isTrue(null), $user->data['user_birthday'] . ' birthday is not today!');
		return $rule;
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayYesterdayIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		list($user, $rule) = $this->buildRuleWithBirthdayYesterday();
		$this->assertFalse($rule->isTrue(null), $user->data['user_birthday'] . ' birthday is not today!');
		return $rule;
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testBirthdayTomorrowIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		list($user, $rule) = $this->buildRuleWithBirthdayTomorrow();
		$this->assertFalse($rule->isTrue(null), $user->data['user_birthday'] . ' birthday is not today!');
		return $rule;
	}
}
