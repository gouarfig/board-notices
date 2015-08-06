<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\has_not_posted_for;

class has_not_posted_for_test extends \phpbb_test_case
{
	public function testInstance()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$lang = &$user->lang;
		include 'phpBB/ext/fq/boardnotices/language/en/boardnotices_acp.php';

		$rule = new has_not_posted_for($user);
		$this->assertThat($rule, $this->logicalNot($this->equalTo(null)));

		return array($user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_posted_for $rule
	 */
	public function testGetDisplayName($args)
	{
		list($user, $rule) = $args;
		$display = $rule->getDisplayName();
		$this->assertTrue((strpos($display, "not posted") !== false), "Wrong DisplayName: '{$display}'");
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_posted_for $rule
	 */
	public function testGetType($args)
	{
		list($user, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('int'));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_posted_for $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($user, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_posted_for $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($user, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_posted_for $rule
	 */
	public function testRuleTodayTrue($args)
	{
		list($user, $rule) = $args;
		$user->data['user_lastpost_time'] = time();

		$days = serialize(array(0));
		$this->assertTrue($rule->isTrue($days));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_posted_for $rule
	 */
	public function testRuleTodayFalse($args)
	{
		list($user, $rule) = $args;
		$user->data['user_lastpost_time'] = time();
		$days = serialize(array(1));
		$this->assertFalse($rule->isTrue($days));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_posted_for $rule
	 */
	public function testRuleYesterdayTrue($args)
	{
		list($user, $rule) = $args;
		$user->data['user_lastpost_time'] = time() - 24*60*60;

		$days = serialize(array(1));
		$this->assertTrue($rule->isTrue($days));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_posted_for $rule
	 */
	public function testRuleYesterdayFalse($args)
	{
		list($user, $rule) = $args;
		$user->data['user_lastpost_time'] = time() - 24*60*60;
		$days = serialize(array(2));
		$this->assertFalse($rule->isTrue($days));
	}

}