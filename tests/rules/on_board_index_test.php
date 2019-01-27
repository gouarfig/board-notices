<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\on_board_index;

class on_board_index_test extends rule_test_base
{
	public function testInstance()
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();

		/** @var \phpbb\template\template $template */
		$template = $this->getMockBuilder('\phpbb\template\template')->getMock();

		$rule = new on_board_index($this->getSerializer(), $user, $template);
		$this->assertNotNull($rule);

		return array($serializer, $user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param on_board_index $rule
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
	 * @param on_board_index $rule
	 */
	public function testGetDisplayUnit($args)
	{
		list($serializer, $user, $rule) = $args;
		$display = $rule->getDisplayUnit();
		$this->assertEmpty($display, "DisplayUnit is not empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param on_board_index $rule
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
	 * @param on_board_index $rule
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
	 * @param on_board_index $rule
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
	 * @param on_board_index $rule
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
			// Empty conditions not on board index
			array(false, null, true),
			array(false, serialize(null), true),
			array(false, $serializer->encode(null), true),
			array(false, array(null), true),
			array(false, serialize(array(null)), true),
			array(false, $serializer->encode(array(null)), true),
			// Empty conditions on board index
			array(true, null, false),
			array(true, serialize(null), false),
			array(true, $serializer->encode(null), false),
			array(true, array(null), false),
			array(true, serialize(array(null)), false),
			array(true, $serializer->encode(array(null)), false),
			// Not on board index with the condition being
			array(false, true, false),
			array(false, serialize(true), false),
			array(false, $serializer->encode(true), false),
			array(false, array(true), false),
			array(false, serialize(array(true)), false),
			array(false, $serializer->encode(array(true)), false),
			// On board index with the condition being
			array(true, true, true),
			array(true, serialize(true), true),
			array(true, $serializer->encode(true), true),
			array(true, array(true), true),
			array(true, serialize(array(true)), true),
			array(true, $serializer->encode(array(true)), true),
			// Not on board index with the condition not being
			array(false, false, true),
			array(false, serialize(false), true),
			array(false, $serializer->encode(false), true),
			array(false, array(false), true),
			array(false, serialize(array(false)), true),
			array(false, $serializer->encode(array(false)), true),
			// On board index with the condition not being
			array(true, false, false),
			array(true, serialize(false), false),
			array(true, $serializer->encode(false), false),
			array(true, array(false), false),
			array(true, serialize(array(false)), false),
			array(true, $serializer->encode(array(false)), false),
		);
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditions($onBoardIndex, $conditions, $result)
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();

		/** @var \phpbb\template\template $template */
		$template = $this->getMockBuilder('\phpbb\template\template')->getMock();
		$template->method('retrieve_var')->will($this->returnValue($onBoardIndex));

		$rule = new on_board_index($serializer, $user, $template);

		$this->assertEquals($result, $rule->isTrue($conditions));
	}

}
