<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\has_never_visited;

class has_never_visited_test extends rule_test_base
{
	public function testInstance()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['is_registered'] = true;
		$user->data['user_id'] = 11;
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$rule = new has_never_visited($this->getSerializer(), $user, $datalayer);
		$this->assertNotNull($rule);

		return array($user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_never_visited $rule
	 */
	public function testHasSingleParameter($args)
	{
		list($user, $rule) = $args;
		$multiple = $rule->hasMultipleParameters();
		$this->assertFalse($multiple, "This rule should not have multiple parameters");
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_never_visited $rule
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
	 * @param has_never_visited $rule
	 */
	public function testGetType($args)
	{
		list($user, $rule) = $args;
		$type = $rule->getType();
		$this->assertEquals('forums', $type);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_never_visited $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($user, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertNull($values);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_never_visited $rule
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
	 * @param has_never_visited $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($user, $rule) = $args;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(0, count($vars));
	}

	public function conditionsProvider()
	{
		$serializer = $this->getSerializer();
		return array(
			// Empty conditions
			array(null, false),
			array(serialize(null), false),
			array($serializer->encode(null), false),
			// Not visited
			array(array(4, 5, 6), true),
			array(serialize(array(4, 5, 6)), true),
			array($serializer->encode(array(4, 5, 6)), true),
			// Partial visit
			array(array(2, 3, 4), false),
			array(serialize(array(2, 3, 4)), false),
			array($serializer->encode(array(2, 3, 4)), false),
			// All visited
			array(array(2, 3), false),
			array(serialize(array(2, 3)), false),
			array($serializer->encode(array(2, 3)), false),
		);
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditions($conditions, $result)
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['is_registered'] = true;
		$user->data['user_id'] = 11;
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('getForumsLastReadTime')->will($this->returnValue(array(
			1 => time(),
			2 => time() - 86400,
			3 => time() - (2 * 86400)
		)));
		$rule = new has_never_visited($this->getSerializer(), $user, $datalayer);

		$this->assertEquals($result, $rule->isTrue($conditions));
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditionsForNonRegisteredUser($conditions, $result)
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['is_registered'] = false;
		$user->data['user_id'] = 11;
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('getForumsLastReadTime')->will($this->returnValue(array(
			1 => time(),
			2 => time() - 86400,
			3 => time() - (2 * 86400)
		)));
		$rule = new has_never_visited($this->getSerializer(), $user, $datalayer);

		$this->assertFalse($rule->isTrue($conditions));
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditionsForUserWithNoData($conditions, $result)
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['is_registered'] = true;
		$user->data['user_id'] = 11;
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('getForumsLastReadTime')->will($this->returnValue(array()));
		$rule = new has_never_visited($this->getSerializer(), $user, $datalayer);

		// It's always going to be true if the conditions are not empty
		$this->assertEquals(
			($conditions !== null) && ($conditions !== 'N;') && ($conditions !== 'json:null'),
			$rule->isTrue($conditions));
	}

}
