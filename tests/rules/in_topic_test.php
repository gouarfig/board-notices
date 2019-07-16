<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\in_topic;

class in_topic_test extends rule_test_base
{
	private $current_topic = 10;
	private $another_topic = 11;

	public function testInstance()
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['f'] = 3;
		$user->data['t'] = $this->current_topic;

		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue($this->current_topic));
		// Make sure the mock works
		$this->assertEquals($this->current_topic, $request->variable('t', 0));

		$rule = new in_topic($this->getSerializer(), $user, $request);
		$this->assertNotNull($rule);

		return array($serializer, $user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
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
	 * @param in_topic $rule
	 */
	public function testGetType($args)
	{
		list($serializer, $user, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('multiple int'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
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
	 * @param in_topic $rule
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
	 * @param in_topic $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($serializer, $user, $rule) = $args;
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
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['f'] = 3;
		$user->data['t'] = $this->current_topic;

		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue($this->current_topic));

		$rule = new in_topic($this->getSerializer(), $user, $request);
		$this->assertEquals($result, $rule->isTrue($condition));
	}
}
