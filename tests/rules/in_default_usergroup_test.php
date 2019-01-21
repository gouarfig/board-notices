<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\in_default_usergroup;

class in_default_usergroup_test extends rule_test_base
{
	public function testInstance()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['group_id'] = 10;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$rule = new in_default_usergroup($user, $datalayer);
		$this->assertNotNull($rule);

		return array($user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
	 */
	public function testGetDisplayName($args)
	{
		list($user, $rule) = $args;
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
	 */
	public function testGetType($args)
	{
		list($user, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('list'));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
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
	 * @param in_default_usergroup $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($user, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertContains('GROUPID', $vars);
		$this->assertContains('GROUPNAME', $vars);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
	 */
	public function testGetTemplateVars($args)
	{
		include 'phpBB/ext/fq/boardnotices/tests/functions.php';

		list($user, $rule) = $args;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(10, $vars['GROUPID']);
		$this->assertEquals('Group Name', $vars['GROUPNAME']);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
	 */
	public function testRuleEmpty($args)
	{
		list($user, $rule) = $args;
		$user->data['group_id'] = 10;

		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
	 */
	public function testRuleNotInGroup($args)
	{
		list($user, $rule) = $args;
		$user->data['group_id'] = 10;

		$groups = serialize(array(11));
		$this->assertFalse($rule->isTrue($groups));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
	 */
	public function testRuleInGroup($args)
	{
		list($user, $rule) = $args;
		$user->data['group_id'] = 10;

		$groups = serialize(array(10));
		$this->assertTrue($rule->isTrue($groups));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
	 */
	public function testRuleInGroupNotArray($args)
	{
		list($user, $rule) = $args;
		$user->data['group_id'] = 10;

		$groups = 10;
		$this->assertTrue($rule->isTrue($groups));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
	 */
	public function testRuleNotInGroupNotArray($args)
	{
		list($user, $rule) = $args;
		$user->data['group_id'] = 10;

		$groups = 11;
		$this->assertFalse($rule->isTrue($groups));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
	 */
	public function testRuleInGroupNotArraySerialize($args)
	{
		list($user, $rule) = $args;
		$user->data['group_id'] = 10;

		$groups = serialize(10);
		$this->assertTrue($rule->isTrue($groups));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param in_default_usergroup $rule
	 */
	public function testRuleNotInGroupNotArraySerialize($args)
	{
		list($user, $rule) = $args;
		$user->data['group_id'] = 10;

		$groups = serialize(11);
		$this->assertFalse($rule->isTrue($groups));
	}

}
