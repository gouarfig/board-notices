<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\has_posted_in_forum;

class has_posted_in_forum_test extends rule_test_base
{
	public function testInstance()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$rule = new has_posted_in_forum($this->getSerializer(), $user, $datalayer);
		$this->assertNotNull($rule);

		return array($user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param \fq\boardnotices\rules\has_posted_in_forum $rule
	 */
	public function testGetDisplayName($args)
	{
		list($user, $rule) = $args;
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param \fq\boardnotices\rules\has_posted_in_forum $rule
	 */
	public function testGetType($args)
	{
		list($user, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('forums'));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param \fq\boardnotices\rules\has_posted_in_forum $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($user, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param \fq\boardnotices\rules\has_posted_in_forum $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($user, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param \fq\boardnotices\rules\has_posted_in_forum $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($user, $rule) = $args;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(0, count($vars));
	}

	public function testTrueOnEmptyCondition()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('approvedUserPosts')->will($this->returnCallback(array($this, 'getUserPosts')));

		$rule = new has_posted_in_forum($this->getSerializer(), $user, $datalayer);
		$this->assertTrue($rule->isTrue(null), "null value doesn't return false");
		$this->assertTrue($rule->isTrue(serialize(null)), "serialize(null) value doesn't return false");
		$this->assertTrue($rule->isTrue(''), "'' value doesn't return false");
		$this->assertTrue($rule->isTrue(serialize('')), "serialize('') value doesn't return false");
		$this->assertTrue($rule->isTrue(serialize(array())), "serialize(array()) value doesn't return false");
	}

	public function testTrueConditions()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('approvedUserPosts')->will($this->returnCallback(array($this, 'getUserPosts')));

		$rule = new has_posted_in_forum($this->getSerializer(), $user, $datalayer);
		$this->assertFalse($rule->isTrue(serialize(array(4))));
		$this->assertFalse($rule->isTrue(serialize(array(5))));
		$this->assertFalse($rule->isTrue(serialize(array(4,5))));
	}

	public function testFalseConditions()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('approvedUserPosts')->will($this->returnCallback(array($this, 'getUserPosts')));

		$rule = new has_posted_in_forum($this->getSerializer(), $user, $datalayer);
		$this->assertTrue($rule->isTrue(serialize(array(1))));
		$this->assertTrue($rule->isTrue(serialize(1)));
		$this->assertTrue($rule->isTrue(2));
		$this->assertTrue($rule->isTrue(serialize(array(1,2))));
		$this->assertTrue($rule->isTrue(serialize(array(1,2,3))));
	}

	public function getUserPosts($forums)
	{
		// Simulated number of user posts per forum id
		$posts = array(
			1 => 10,
			2 => 20,
			3 => 30,
			4 => 0,
			5 => 0,
		);
		$result = 0;

		if (empty($forums))
		{
			// If no forums specified, we add up all the values
			foreach ($posts as $post)
			{
				$result += $post;
			}
		}
		else
		{
			foreach ($forums as $forum)
			{
				$result += $posts[$forum];
			}
		}
		return $result;
	}
}
