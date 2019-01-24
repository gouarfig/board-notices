<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\in_forum;

class in_forum_test extends rule_test_base
{
	public function testInstance()
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['f'] = 10;

		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));
		// Make sure the mock works
		$this->assertEquals(10, $request->variable('f', 0));

		$rule = new in_forum($this->getSerializer(), $user, $request);
		$this->assertNotNull($rule);

		return array($serializer, $user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
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
	 * @param in_forum $rule
	 */
	public function testGetType($args)
	{
		list($serializer, $user, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('forums'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
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
	 * @param in_forum $rule
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
	 * @param in_forum $rule
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
	 * @param in_forum $rule
	 */
	public function testRuleEmpty($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
	 */
	public function testRuleNotInForumSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$forum = serialize(array(11));
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
	 */
	public function testRuleNotInForumNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$forum = $serializer->encode(array(11));
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
	 */
	public function testRuleInForumSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$forum = serialize(array(10));
		$this->assertTrue($rule->isTrue($forum)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
	 */
	public function testRuleInForumNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$forum = $serializer->encode(array(10));
		$this->assertTrue($rule->isTrue($forum)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
	 */
	public function testRuleInForumNotArray($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$forum = 10;
		$this->assertTrue($rule->isTrue($forum)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
	 */
	public function testRuleNotInForumNotArray($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$forum = 11;
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
	 */
	public function testRuleInForumNotArraySerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$forum = serialize(10);
		$this->assertTrue($rule->isTrue($forum)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
	 */
	public function testRuleInForumNotArrayNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$forum = $serializer->encode(10);
		$this->assertTrue($rule->isTrue($forum)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
	 */
	public function testRuleNotInForumNotArraySerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$forum = serialize(11);
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_forum $rule
	 */
	public function testRuleNotInForumNotArrayNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		// The fecking mock stops working after being passed to the test method,
		// so we have to recreate it on each test
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$request->method('variable')->will($this->returnValue(10));

		$rule = new in_forum($this->getSerializer(), $user, $request);

		$forum = $serializer->encode(11);
		$this->assertFalse($rule->isTrue($forum));
	}
}
