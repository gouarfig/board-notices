<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\has_not_posted_for;
use fq\boardnotices\tests\mock\mock_api;

class has_not_posted_for_test extends rule_test_base
{
	public function testInstance()
	{
		$api = new mock_api();
		$rule = new has_not_posted_for($this->getSerializer(), $api);
		$this->assertNotNull($rule);

		return array($api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
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
	 * @param has_not_posted_for $rule
	 */
	public function testGetType($args)
	{
		list($api, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('int'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
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
	 * @param has_not_posted_for $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($api, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertEquals(4, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($api, $rule) = $args;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(4, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
	 */
	public function testRuleTodayTrue($args)
	{
		list($api, $rule) = $args;
		$api->setUserLastPostTime(time());

		$days = serialize(array(0));
		$this->assertTrue($rule->isTrue($days));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
	 */
	public function testRuleTodayFalse($args)
	{
		list($api, $rule) = $args;
		$api->setUserLastPostTime(time());
		$days = serialize(array(1));
		$this->assertFalse($rule->isTrue($days));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
	 */
	public function testRuleYesterdayTrue($args)
	{
		list($api, $rule) = $args;
		$api->setUserLastPostTime(time() - 24*60*60);

		$days = serialize(array(1));
		$this->assertTrue($rule->isTrue($days));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
	 */
	public function testRuleYesterdayFalse($args)
	{
		list($api, $rule) = $args;
		$api->setUserLastPostTime(time() - 24*60*60);
		$days = serialize(array(2));
		$this->assertFalse($rule->isTrue($days));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
	 */
	public function testRuleYesterdayWithNoSerializeTrue($args)
	{
		list($api, $rule) = $args;
		$api->setUserLastPostTime(time() - 24*60*60);

		$days = 1;
		$this->assertTrue($rule->isTrue($days));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
	 */
	public function testRuleYesterdayWithNoSerializeFalse($args)
	{
		list($api, $rule) = $args;
		$api->setUserLastPostTime(time() - 24*60*60);
		$days = 2;
		$this->assertFalse($rule->isTrue($days));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
	 */
	public function testRuleYesterdayWithNoArrayTrue($args)
	{
		list($api, $rule) = $args;
		$api->setUserLastPostTime(time() - 24*60*60);

		$days = serialize(1);
		$this->assertTrue($rule->isTrue($days));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_not_posted_for $rule
	 */
	public function testRuleYesterdayWithNoArrayFalse($args)
	{
		list($api, $rule) = $args;
		$api->setUserLastPostTime(time() - 24*60*60);
		$days = serialize(2);
		$this->assertFalse($rule->isTrue($days));
	}

}
