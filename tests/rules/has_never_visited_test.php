<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\has_never_visited;
use fq\boardnotices\tests\mock\mock_api;

class has_never_visited_test extends rule_test_base
{
	public function testInstance()
	{
		$api = new mock_api();
		$api->setUserRegistered(true, 11);
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$rule = new has_never_visited($this->getSerializer(), $api, $datalayer);
		$this->assertNotNull($rule);

		return array($api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_never_visited $rule
	 */
	public function testHasSingleParameter($args)
	{
		list($api, $rule) = $args;
		$multiple = $rule->hasMultipleParameters();
		$this->assertFalse($multiple, "This rule should not have multiple parameters");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_never_visited $rule
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
	 * @param has_never_visited $rule
	 */
	public function testGetType($args)
	{
		list($api, $rule) = $args;
		$type = $rule->getType();
		$this->assertEquals('forums', $type);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_never_visited $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($api, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertNull($values);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_never_visited $rule
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
	 * @param has_never_visited $rule
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
		$api = new mock_api();
		$api->setUserRegistered(true, 11);
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('getForumsLastReadTime')->will($this->returnValue(array(
			1 => time(),
			2 => time() - 86400,
			3 => time() - (2 * 86400)
		)));
		$rule = new has_never_visited($this->getSerializer(), $api, $datalayer);

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
		$rule = new has_never_visited($this->getSerializer(), $api, $datalayer);

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
		$rule = new has_never_visited($this->getSerializer(), $api, $datalayer);

		// It's always going to be true if the conditions are not empty
		$this->assertEquals(
			($conditions !== null) && ($conditions !== 'N;') && ($conditions !== 'json:null'),
			$rule->isTrue($conditions));
	}

}
