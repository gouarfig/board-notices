<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\has_never_posted;

class has_never_posted_test extends rule_test_base
{
	public function testInstance()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$rule = new has_never_posted($this->getConstants(), $this->getSerializer(), $user, $datalayer);
		$this->assertNotNull($rule);

		return $rule;
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetDisplayName($rule)
	{
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetType($rule)
	{
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('n/a'));
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

	public function testRuleTrue()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('nonDeletedUserPosts')->willReturn(0);
		$rule = new has_never_posted($this->getConstants(), $this->getSerializer(), $user, $datalayer);
		$this->assertTrue($rule->isTrue(null));
	}

	public function testRuleFalse()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();
		$datalayer->method('nonDeletedUserPosts')->willReturn(1);
		$rule = new has_never_posted($this->getConstants(), $this->getSerializer(), $user, $datalayer);
		$this->assertFalse($rule->isTrue(null));
	}

}
