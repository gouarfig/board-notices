<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\has_never_posted_in_forum;
use fq\boardnotices\dac\datalayer_interface;

class has_never_posted_in_forum_test extends \phpbb_test_case
{
	public function testInstance()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$lang = &$user->lang;
		include 'phpBB/ext/fq/boardnotices/language/en/boardnotices_acp.php';

		$datalayer = $this->getMockBuilder('\fq\boardnotices\dac\datalayer_interface')->getMock();
		$rule = new has_never_posted_in_forum($user, $datalayer);
		$this->assertThat($rule, $this->logicalNot($this->equalTo(null)));

		return $rule;
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetDisplayName($rule)
	{
		$display = $rule->getDisplayName();
		$this->assertTrue((strpos($display, "never posted") !== false), "Wrong DisplayName: '{$display}'");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetType($rule)
	{
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('forums'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetPossibleValues($rule)
	{
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetAvailableVars($rule)
	{
		$vars = $rule->getAvailableVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetTemplateVars($rule)
	{
		$vars = $rule->getTemplateVars();
		$this->assertEquals(0, count($vars));
	}

	public function testFalseOnEmptyCondition()
	{
		$user = new \phpbb\user('\phpbb\datetime');

		$datalayer = $this->getMockBuilder('\fq\boardnotices\dac\datalayer_interface')->getMock();
		$datalayer->method('nonDeletedUserPosts')->will($this->returnCallback(array($this, 'getNonDeletedUserPosts')));

		$rule = new has_never_posted_in_forum($user, $datalayer);
		$this->assertFalse($rule->isTrue(null), "null value doesn't return false");
		$this->assertFalse($rule->isTrue(serialize(null)), "serialize(null) value doesn't return false");
		$this->assertFalse($rule->isTrue(''), "'' value doesn't return false");
		$this->assertFalse($rule->isTrue(serialize('')), "serialize('') value doesn't return false");
		$this->assertFalse($rule->isTrue(serialize(array())), "serialize(array()) value doesn't return false");
	}

	public function testTrueConditions()
	{
		$user = new \phpbb\user('\phpbb\datetime');

		$datalayer = $this->getMockBuilder('\fq\boardnotices\dac\datalayer_interface')->getMock();
		$datalayer->method('nonDeletedUserPosts')->will($this->returnCallback(array($this, 'getNonDeletedUserPosts')));

		$rule = new has_never_posted_in_forum($user, $datalayer);
		$this->assertTrue($rule->isTrue(serialize(array(4))));
		$this->assertTrue($rule->isTrue(serialize(array(5))));
		$this->assertTrue($rule->isTrue(serialize(array(4,5))));
	}

	public function testFalseConditions()
	{
		$user = new \phpbb\user('\phpbb\datetime');

		$datalayer = $this->getMockBuilder('\fq\boardnotices\dac\datalayer_interface')->getMock();
		$datalayer->method('nonDeletedUserPosts')->will($this->returnCallback(array($this, 'getNonDeletedUserPosts')));

		$rule = new has_never_posted_in_forum($user, $datalayer);
		$this->assertFalse($rule->isTrue(serialize(array(1))));
		$this->assertFalse($rule->isTrue(serialize(1)));
		$this->assertFalse($rule->isTrue(2));
		$this->assertFalse($rule->isTrue(serialize(array(1,2))));
		$this->assertFalse($rule->isTrue(serialize(array(1,2,3))));
	}

	public function getNonDeletedUserPosts($forums)
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