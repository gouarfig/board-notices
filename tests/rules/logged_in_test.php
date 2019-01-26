<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\logged_in;

class logged_in_test extends rule_test_base
{
	public function testInstance()
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();

		$rule = new logged_in($this->getSerializer(), $user);
		$this->assertNotNull($rule);

		return array($serializer, $user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param logged_in $rule
	 */
	public function testGetDisplayName($args)
	{
		list($serializer, $user, $rule) = $args;
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param logged_in $rule
	 */
	public function testGetDisplayUnit($args)
	{
		list($serializer, $user, $rule) = $args;
		$display = $rule->getDisplayUnit();
		$this->assertNotEmpty($display, "DisplayUnit is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param logged_in $rule
	 */
	public function testGetType($args)
	{
		list($serializer, $user, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('yesno'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param logged_in $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($serializer, $user, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param logged_in $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($serializer, $user, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param logged_in $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($serializer, $user, $rule) = $args;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(0, count($vars));
	}

	public function conditionsProvider()
	{
		$serializer = $this->getSerializer();
		return array(
			// Empty conditions on guest (empty condition will result in NOT logged-in)
			array(USER_IGNORE, null, true),
			array(USER_IGNORE, serialize(null), true),
			array(USER_IGNORE, $serializer->encode(null), true),
			array(USER_IGNORE, array(null), true),
			array(USER_IGNORE, serialize(array(null)), true),
			array(USER_IGNORE, $serializer->encode(array(null)), true),
			// Empty conditions on user
			array(USER_NORMAL, null, false),
			array(USER_NORMAL, serialize(null), false),
			array(USER_NORMAL, $serializer->encode(null), false),
			array(USER_NORMAL, array(null), false),
			array(USER_NORMAL, serialize(array(null)), false),
			array(USER_NORMAL, $serializer->encode(array(null)), false),
			// Logged-in conditions on guest
			array(USER_IGNORE, true, false),
			array(USER_IGNORE, serialize(true), false),
			array(USER_IGNORE, $serializer->encode(true), false),
			array(USER_IGNORE, array(true), false),
			array(USER_IGNORE, serialize(array(true)), false),
			array(USER_IGNORE, $serializer->encode(array(true)), false),
			// Logged-in conditions on user
			array(USER_NORMAL, true, true),
			array(USER_NORMAL, serialize(true), true),
			array(USER_NORMAL, $serializer->encode(true), true),
			array(USER_NORMAL, array(true), true),
			array(USER_NORMAL, serialize(array(true)), true),
			array(USER_NORMAL, $serializer->encode(array(true)), true),
			// Guest conditions on guest
			array(USER_IGNORE, false, true),
			array(USER_IGNORE, serialize(false), true),
			array(USER_IGNORE, $serializer->encode(false), true),
			array(USER_IGNORE, array(false), true),
			array(USER_IGNORE, serialize(array(false)), true),
			array(USER_IGNORE, $serializer->encode(array(false)), true),
			// Guest conditions on user
			array(USER_NORMAL, false, false),
			array(USER_NORMAL, serialize(false), false),
			array(USER_NORMAL, $serializer->encode(false), false),
			array(USER_NORMAL, array(false), false),
			array(USER_NORMAL, serialize(array(false)), false),
			array(USER_NORMAL, $serializer->encode(array(false)), false),
		);
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditions($userType, $conditions, $result)
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['user_type'] = $userType;

		$rule = new logged_in($serializer, $user);

		$this->assertEquals($result, $rule->isTrue($conditions));
	}

}
