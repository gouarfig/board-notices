<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\in_topic;
use fq\boardnotices\tests\mock\mock_api;

class in_topic_test extends rule_test_base
{
	private $current_topic = 10;
	private $another_topic = 11;

	public function testInstance()
	{
		$serializer = $this->getSerializer();
		$api = new mock_api();
		$api->setCurrentForum(3, $this->current_topic);

		$rule = new in_topic($this->getSerializer(), $api);
		$this->assertNotNull($rule);

		return array($serializer, $api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_topic $rule
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
	 * @param in_topic $rule
	 */
	public function testGetType($args)
	{
		list($serializer, $api, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('multiple int'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param in_topic $rule
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
	 * @param in_topic $rule
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
	 * @param in_topic $rule
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
			array($this->current_topic, true),
			array($this->another_topic, false),
			array("{$this->current_topic}, {$this->another_topic}", true),
			array("100, 101,102", false),
			array(serialize(null), false),
			array(serialize($this->current_topic), true),
			array(serialize($this->another_topic), false),
			array(serialize("{$this->current_topic}, {$this->another_topic}"), true),
			array(serialize("100, 101,102"), false),
			array(serialize(array($this->current_topic)), true),
			array(serialize(array($this->another_topic)), false),
			array(serialize(array("{$this->current_topic}, {$this->another_topic}")), true),
			array(serialize(array("100, 101,102")), false),
			array($serializer->encode(null), false),
			array($serializer->encode($this->current_topic), true),
			array($serializer->encode($this->another_topic), false),
			array($serializer->encode("{$this->current_topic}, {$this->another_topic}"), true),
			array($serializer->encode("100, 101,102"), false),
			array($serializer->encode(array($this->current_topic)), true),
			array($serializer->encode(array($this->another_topic)), false),
			array($serializer->encode(array("{$this->current_topic}, {$this->another_topic}")), true),
			array($serializer->encode(array("100, 101,102")), false),
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
		$api->setCurrentForum(3, $this->current_topic);

		$rule = new in_topic($this->getSerializer(), $api);
		$this->assertEquals($result, $rule->isTrue($condition));
	}
}
