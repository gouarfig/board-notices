<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\has_posted_in_forum;
use fq\boardnotices\tests\mock\mock_api;

class has_posted_in_forum_test extends rule_test_base
{
	public function testInstance()
	{
		$api = new mock_api();
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$rule = new has_posted_in_forum($this->getSerializer(), $api, $datalayer);
		$this->assertNotNull($rule);

		return array($api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param \fq\boardnotices\rules\has_posted_in_forum $rule
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
	 * @param \fq\boardnotices\rules\has_posted_in_forum $rule
	 */
	public function testGetType($args)
	{
		list($api, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('forums'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param \fq\boardnotices\rules\has_posted_in_forum $rule
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
	 * @param \fq\boardnotices\rules\has_posted_in_forum $rule
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
	 * @param \fq\boardnotices\rules\has_posted_in_forum $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($api, $rule) = $args;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(0, count($vars));
	}

	public function testTrueOnEmptyCondition()
	{
		$api = new mock_api();
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('approvedUserPosts')->will($this->returnCallback(array($this, 'getUserPosts')));

		$rule = new has_posted_in_forum($this->getSerializer(), $api, $datalayer);
		$this->assertTrue($rule->isTrue(null), "null value doesn't return false");
		$this->assertTrue($rule->isTrue(serialize(null)), "serialize(null) value doesn't return false");
		$this->assertTrue($rule->isTrue(''), "'' value doesn't return false");
		$this->assertTrue($rule->isTrue(serialize('')), "serialize('') value doesn't return false");
		$this->assertTrue($rule->isTrue(serialize(array())), "serialize(array()) value doesn't return false");
	}

	public function testTrueConditions()
	{
		$api = new mock_api();
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('approvedUserPosts')->will($this->returnCallback(array($this, 'getUserPosts')));

		$rule = new has_posted_in_forum($this->getSerializer(), $api, $datalayer);
		$this->assertFalse($rule->isTrue(serialize(array(4))));
		$this->assertFalse($rule->isTrue(serialize(array(5))));
		$this->assertFalse($rule->isTrue(serialize(array(4,5))));
	}

	public function testFalseConditions()
	{
		$api = new mock_api();
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();
		$datalayer->method('approvedUserPosts')->will($this->returnCallback(array($this, 'getUserPosts')));

		$rule = new has_posted_in_forum($this->getSerializer(), $api, $datalayer);
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
