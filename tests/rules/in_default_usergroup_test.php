<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\in_default_usergroup;
use fq\boardnotices\repository\legacy_interface;

class in_default_usergroup_test extends \phpbb_test_case
{
	public function testInstance()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$user->data['group_id'] = 10;
		$lang = &$user->lang;
		include 'phpBB/ext/fq/boardnotices/language/en/boardnotices_acp.php';

		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$rule = new in_default_usergroup($user, $datalayer);
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
		$this->assertTrue((strpos($display, "Default user group") !== false), "Wrong DisplayName: '{$display}'");
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
		$this->assertThat($type, $this->equalTo('list'));
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
		$this->assertContains('GROUPID', $vars);
		$this->assertContains('GROUPNAME', $vars);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_posted_for $rule
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
	 * @param has_not_posted_for $rule
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
	 * @param has_not_posted_for $rule
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
	 * @param has_not_posted_for $rule
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
	 * @param has_not_posted_for $rule
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
	 * @param has_not_posted_for $rule
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
	 * @param has_not_posted_for $rule
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
	 * @param has_not_posted_for $rule
	 */
	public function testRuleNotInGroupNotArraySerialize($args)
	{
		list($user, $rule) = $args;
		$user->data['group_id'] = 10;

		$groups = serialize(11);
		$this->assertFalse($rule->isTrue($groups));
	}

}
