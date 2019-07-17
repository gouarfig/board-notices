<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\in_forum;
use fq\boardnotices\tests\mock\mock_api;

class in_forum_test extends rule_test_base
{
	private $current_forum = 10;
	private $another_forum = 11;

	public function testInstance()
	{
		$serializer = $this->getSerializer();
		$api = new mock_api();

		$rule = new in_forum($serializer, $api);
		$this->assertNotNull($rule);

		return array($serializer, $api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_forum $rule
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
	 * @param in_forum $rule
	 */
	public function testGetType($args)
	{
		list($serializer, $api, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('forums'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_forum $rule
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
	 * @param in_forum $rule
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
	 * @param in_forum $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($serializer, $api, $rule) = $args;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(0, count($vars));
	}

	public function getTestData()
	{
		$serializer = $this->getSerializer();
		return array(
			array(null, false),
			array($this->current_forum, true),
			array($this->another_forum, false),
			array(serialize(null), false),
			array(serialize($this->current_forum), true),
			array(serialize($this->another_forum), false),
			array(serialize(array($this->current_forum)), true),
			array(serialize(array($this->another_forum)), false),
			array($serializer->encode(null), false),
			array($serializer->encode($this->current_forum), true),
			array($serializer->encode($this->another_forum), false),
			array($serializer->encode(array($this->current_forum)), true),
			array($serializer->encode(array($this->another_forum)), false),
		);
	}

	/**
	 * Test all conditions
	 * @dataProvider getTestData
	 * @param mixed $condition
	 * @param bool $result
	 * @return void
	 */
	public function testRule($condition, $result)
	{
		$api = new mock_api();
		$api->setCurrentForum($this->current_forum);

		$rule = new in_forum($this->getSerializer(), $api);
		$this->assertEquals($result, $rule->isTrue($condition));
	}
}
