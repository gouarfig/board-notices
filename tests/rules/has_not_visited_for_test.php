<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\has_not_visited_for;

class has_not_visited_for_test extends rule_test_base
{
	public function testInstance()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$rule = new has_not_visited_for($this->getSerializer(), $user, $datalayer);
		$this->assertNotNull($rule);

		return array($user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_visited_for $rule
	 */
	public function testHasMultipleParameters($args)
	{
		list($user, $rule) = $args;
		$multiple = $rule->hasMultipleParameters();
		$this->assertTrue($multiple, "This rule should have multiple parameters");
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_visited_for $rule
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
	 * @param has_not_visited_for $rule
	 */
	public function testGetType($args)
	{
		list($user, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo(array('forums', 'int')));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_visited_for $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($user, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertEquals(array(null, null), $values);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_visited_for $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($user, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertEquals(4, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_not_visited_for $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($user, $rule) = $args;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(4, count($vars));
	}

}
