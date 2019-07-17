<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\has_not_visited_for;
use fq\boardnotices\tests\mock\mock_api;

class has_not_visited_for_test extends rule_test_base
{
	public function testInstance()
	{
		$api = new mock_api();
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$rule = new has_not_visited_for($this->getSerializer(), $api, $datalayer);
		$this->assertNotNull($rule);

		return array($api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_visited_for $rule
	 */
	public function testHasMultipleParameters($args)
	{
		list($api, $rule) = $args;
		$multiple = $rule->hasMultipleParameters();
		$this->assertTrue($multiple, "This rule should have multiple parameters");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_visited_for $rule
	 */
	public function testGetDisplayName($args)
	{
		list($api, $rule) = $args;
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_visited_for $rule
	 */
	public function testGetType($args)
	{
		list($api, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo(array('forums', 'int')));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_visited_for $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($api, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertEquals(array(null, null), $values);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_visited_for $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($api, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_visited_for $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($api, $rule) = $args;
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
			array(array([], 0), false),
			array(serialize(array([], 0)), false),
			array($serializer->encode(array([], 0)), false),
			// No record of any visit
			array(array(array(4, 5, 6), 1), false),
			array(serialize(array(array(4, 5, 6), 1)), false),
			array($serializer->encode(array(array(4, 5, 6), 1)), false),
			// It hasn't been 2 days yet
			array(array(array(1), 2), false),
			array(serialize(array(array(1), 2)), false),
			array($serializer->encode(array(array(1), 2)), false),
			array(array(array(2), 2), false),
			array(serialize(array(array(2), 2)), false),
			array($serializer->encode(array(array(2), 2)), false),
			array(array(array(1, 2), 2), false),
			array(serialize(array(array(1, 2), 2)), false),
			array($serializer->encode(array(array(1, 2), 2)), false),
			// It's been more than 1 day
			array(array(array(3), 1), true),
			array(serialize(array(array(3), 1)), true),
			array($serializer->encode(array(array(3), 1)), true),
			array(array(array(2), 1), true),
			array(serialize(array(array(2), 1)), true),
			array($serializer->encode(array(array(2), 1)), true),
			array(array(array(1, 2), 1), true),
			array(serialize(array(array(1, 2), 1)), true),
			array($serializer->encode(array(array(1, 2), 1)), true),
		);
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditions($conditions, $result)
	{
		$api = new mock_api();
		$api->setUserRegistered(true, 11);
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('getForumsLastReadTime')->will($this->returnValue(array(
			1 => time() - 10,
			2 => time() - 86400 -10,
			3 => time() - (2 * 86400) -10
		)));
		$rule = new has_not_visited_for($this->getSerializer(), $api, $datalayer);

		$this->assertEquals($result, $rule->isTrue($conditions));
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditionsForNonRegisteredUser($conditions, $result)
	{
		$api = new mock_api();
		$api->setUserRegistered(false);
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('getForumsLastReadTime')->will($this->returnValue(array(
			1 => time(),
			2 => time() - 86400,
			3 => time() - (2 * 86400)
		)));
		$rule = new has_not_visited_for($this->getSerializer(), $api, $datalayer);

		// It's always going to be false for non-registered user
		$this->assertFalse($rule->isTrue($conditions));
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditionsForUserWithNoData($conditions, $result)
	{
		$api = new mock_api();
		$api->setUserRegistered(true, 11);
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('getForumsLastReadTime')->will($this->returnValue(array()));
		$rule = new has_not_visited_for($this->getSerializer(), $api, $datalayer);

		// It's always going to be false
		$this->assertFalse($rule->isTrue($conditions));
	}

}
