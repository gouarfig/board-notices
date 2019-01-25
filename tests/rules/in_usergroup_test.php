<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\in_usergroup;

function isUserInGroupId($group)
{
	return $group === 10;
}

class in_usergroup_test extends rule_test_base
{
	public function testInstance()
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['f'] = 10;

		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);
		$this->assertNotNull($rule);

		return array($serializer, $user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
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
	 * @param in_usergroup $rule
	 */
	public function testGetType($args)
	{
		list($serializer, $user, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('multiple choice'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
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
	 * @param in_usergroup $rule
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
	 * @param in_usergroup $rule
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
	 * @param in_usergroup $rule
	 */
	public function testRuleEmpty($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleNotInUsergroupSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = serialize(array(11));
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleNotInUsergroupSerialize2($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = serialize(array(11, 12));
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleNotInUsergroupNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = $serializer->encode(array(11));
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleNotInUsergroupNewSerialize2($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = $serializer->encode(array(11, 12));
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleInUserGroupSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = serialize(array(10));
		$this->assertTrue($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleInUserGroupSerialize2($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = serialize(array(10, 11, 12));
		$this->assertTrue($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleInUserGroupNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = $serializer->encode(array(10));
		$this->assertTrue($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleInUserGroupNewSerialize2($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = $serializer->encode(array(10, 11, 12));
		$this->assertTrue($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleInUserGroupNotArray($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = 10;
		$this->assertTrue($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleInUserGroupNotArray2($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = array(10, 11, 12);
		$this->assertTrue($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleNotInUsergroupNotArray($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = 11;
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleNotInUsergroupNotArray2($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = array(11, 12);
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleInUserGroupNotArraySerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = serialize(10);
		$this->assertTrue($rule->isTrue($forum)); ///
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleInUserGroupNotArrayNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = $serializer->encode(10);
		$this->assertTrue($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleNotInUsergroupNotArraySerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = serialize(11);
		$this->assertFalse($rule->isTrue($forum));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param in_usergroup $rule
	 */
	public function testRuleNotInUsergroupNotArrayNewSerialize($args)
	{
		list($serializer, $user, $rule) = $args;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('isUserInGroupId')->will($this->returnCallback('\fq\boardnotices\tests\rules\isUserInGroupId'));

		$rule = new in_usergroup($this->getSerializer(), $user, $datalayer);

		$forum = $serializer->encode(11);
		$this->assertFalse($rule->isTrue($forum));
	}
}
