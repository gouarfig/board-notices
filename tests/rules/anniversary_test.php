<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\anniversary;

class anniversary_test extends \phpbb_test_case
{
//	static protected function setup_extensions()
//	{
//		return array('fq/boardnotices');
//	}

	public function testInstance()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$rule = new anniversary($user);
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
		$this->assertTrue(strpos($display, "anniversary") !== false);
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
			//array('Pacific/Midway'),
			array('Europe/London'),
			//array('Pacific/Auckland'),
			//array('Pacific/Norfolk'),
			//array('Pacific/Kiritimati'),
			//array('America/St_Johns'),
		);
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testTodayIsFalse($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->data['user_regdate'] = time() - date('Z');
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
		$user = new \phpbb\user('\phpbb\datetime');
		$user->data['user_regdate'] = time() - date('Z') - (60*60);
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
		$user = new \phpbb\user('\phpbb\datetime');
		$user->data['user_regdate'] = time() - date('Z') + (60*60);
		$rule = new anniversary($user);
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @dataProvider getTimezones
	 * @param string $timezone
	 */
	public function testLastYearIsTrue($timezone)
	{
		date_default_timezone_set($timezone);
		$user = new \phpbb\user('\phpbb\datetime');
		$user->data['user_regdate'] = strtotime('last year') - (60*60);
		$rule = new anniversary($user);
		$this->assertTrue($rule->isTrue(null), date('r', $user->data['user_regdate']) . ' is not last year!');
	}
}
