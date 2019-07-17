<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\in_default_usergroup;
use fq\boardnotices\tests\mock\mock_api;

class in_default_usergroup_test extends rule_test_base
{
	public function testInstance()
	{
		$api = new mock_api();
		$api->setUserRegistered(true, 11, 10);
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$rule = new in_default_usergroup($this->getSerializer(), $api, $datalayer);
		$this->assertNotNull($rule);

		return array($api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_default_usergroup $rule
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
	 * @param in_default_usergroup $rule
	 */
	public function testGetType($args)
	{
		list($api, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('list'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_default_usergroup $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($api, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_default_usergroup $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($api, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertContains('GROUPID', $vars);
		$this->assertContains('GROUPNAME', $vars);
	}

	/**
	 * @depends testInstance
	 * @runInSeparateProcess
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_default_usergroup $rule
	 */
	public function testGetTemplateVars($args)
	{
		// include 'phpBB/ext/fq/boardnotices/tests/functions.php';

		list($api, $rule) = $args;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(10, $vars['GROUPID']);
		$this->assertEquals('Group Name', $vars['GROUPNAME']);
	}

	public function getTestData()
	{
		$serializer = $this->getSerializer();
		return array(
			array(null, false),
			array(10, true),
			array(serialize(10), true),
			array(serialize(array(10)), true),
			array($serializer->encode(10), true),
			array($serializer->encode(array(10)), true),
			array(11, false),
			array(serialize(11), false),
			array(serialize(array(11)), false),
			array($serializer->encode(11), false),
			array($serializer->encode(array(11)), false),
		);
	}

	/**
	 * @dataProvider getTestData
	 *
	 * @param mixed $condition
	 * @param bool $result
	 * @return void
	 */
	public function testRules($condition, $result)
	{
		$api = new mock_api();
		$api->setUserRegistered(true, 11, 10);

		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$rule = new in_default_usergroup($this->getSerializer(), $api, $datalayer);

		$this->assertEquals($result, $rule->isTrue($condition));
	}

}
