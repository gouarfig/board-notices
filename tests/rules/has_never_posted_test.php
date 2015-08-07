<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\has_never_posted;
use \fq\boardnotices\tests\mock\datalayer_mock;

class has_never_posted_test extends \phpbb_test_case
{
	public function testInstance()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$lang = &$user->lang;
		include 'phpBB/ext/fq/boardnotices/language/en/boardnotices_acp.php';

		$datalayer = new datalayer_mock();
		$rule = new has_never_posted($user, $datalayer);
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
		$user = new \phpbb\user('\phpbb\datetime');

		$datalayer = new datalayer_mock(array('nonDeletedUserPosts' => 0));
		$rule = new has_never_posted($user, $datalayer);
		$this->assertTrue($rule->isTrue(null));
	}

	public function testRuleFalse()
	{
		$user = new \phpbb\user('\phpbb\datetime');

		$datalayer = new datalayer_mock(array('nonDeletedUserPosts' => 1));
		$rule = new has_never_posted($user, $datalayer);
		$this->assertFalse($rule->isTrue(null));
	}

}
