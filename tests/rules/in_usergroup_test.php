<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\in_usergroup;
use fq\boardnotices\tests\mock\mock_api;

function inUsergroupTest_isUserInGroupId($group)
{
	return $group === 10;
}

class in_usergroup_test extends rule_test_base
{
	public function testInstance()
	{
		$serializer = $this->getSerializer();
		$api = new mock_api();

		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();

		$rule = new in_usergroup($this->getSerializer(), $api, $datalayer);
		$this->assertNotNull($rule);

		return array($serializer, $api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_usergroup $rule
	 */
	public function testGetDisplayName($args)
	{
		list($serializer, $api, $rule) = $args;
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_usergroup $rule
	 */
	public function testGetType($args)
	{
		list($serializer, $api, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('multiple choice'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_usergroup $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($serializer, $api, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_usergroup $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($serializer, $api, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_usergroup $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($serializer, $api, $rule) = $args;
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
			// Not in usergroup (single choice)
			array(11, false),
			array(serialize(11), false),
			array($serializer->encode(11), false),
			// Not in usergroup (array of single choice)
			array(array(11), false),
			array(serialize(array(11)), false),
			array($serializer->encode(array(11)), false),
			// Not in usergroup (array of multiple choices)
			array(array(11, 12), false),
			array(serialize(array(11, 12)), false),
			array($serializer->encode(array(11, 12)), false),
			// In usergroup (single choice)
			array(10, true),
			array(serialize(10), true),
			array($serializer->encode(10), true),
			// In usergroup (array of single choice)
			array(array(10), true),
			array(serialize(array(10)), true),
			array($serializer->encode(array(10)), true),
			// In usergroup (array of multiple choices)
			array(array(10, 11), true),
			array(serialize(array(10, 11)), true),
			array($serializer->encode(array(10, 11)), true),
		);
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditions($conditions, $result)
	{
		$serializer = $this->getSerializer();
		// /** @var \phpbb\user $user */
		// $user = $this->getUser();
		// $user->data['user_lang'] = 'fr';
		$api = new mock_api();
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\inUsergroupTest_isUserInGroupId'));

		$rule = new in_usergroup($serializer, $api, $datalayer);

		$this->assertEquals($result, $rule->isTrue($conditions));
	}

}
