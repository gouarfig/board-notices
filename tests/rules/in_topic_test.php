<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\in_topic;

class in_topic_test extends rule_test_base
{
	public function testInstance()
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['f'] = 10;
		$user->data['t'] = 110;

		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));
		// Make sure the mock works
		$this->assertEquals(110, $request->variable('t', 0));

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

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleEmpty($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleNotInForumSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$topic = serialize(array(11));
		$this->assertFalse($rule->isTrue($topic));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleNotInForumNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$topic = $serializer->encode(array(11));
		$this->assertFalse($rule->isTrue($topic));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleInForumSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$topic = serialize(array(110));
		$this->assertTrue($rule->isTrue($topic)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleInForumNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$topic = $serializer->encode(array(110));
		$this->assertTrue($rule->isTrue($topic)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleInForumNotArray($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$topic = 110;
		$this->assertTrue($rule->isTrue($topic)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleNotInForumNotArray($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$topic = 11;
		$this->assertFalse($rule->isTrue($topic));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleInForumNotArraySerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$topic = serialize(110);
		$this->assertTrue($rule->isTrue($topic)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleInForumNotArrayNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$topic = $serializer->encode(110);
		$this->assertTrue($rule->isTrue($topic)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleNotInForumNotArraySerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$topic = serialize(11);
		$this->assertFalse($rule->isTrue($topic));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_topic $rule
	 */
	public function testRuleNotInForumNotArrayNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(110));

		$rule = new in_topic($this->getSerializer(), $user, $request);

		$topic = $serializer->encode(11);
		$this->assertFalse($rule->isTrue($topic));
	}
}
