<?php

namespace fq\boardnotices\tests\rules;

use fq\boardnotices\rules\random;
use fq\boardnotices\tests\mock\mock_api;

class random_test extends rule_test_base
{

	public function testInstance()
	{
		$api = new mock_api();
		$rule = new random($this->getSerializer(), $api);
		$this->assertNotNull($rule);
		return $rule;
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\random $rule
	 */
	public function testGetDisplayName($rule)
	{
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\random $rule
	 */
	public function testGetType($rule)
	{
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('int'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\random $rule
	 */
	public function getDefaultValue($rule)
	{
		$this->assertEquals("2", $rule->getDefault());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\random $rule
	 */
	public function testCannotValidateNullConditions($rule)
	{
		$this->assertFalse($rule->validateValues(null));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\random $rule
	 */
	public function testCannotValidateEmptyConditions($rule)
	{
		$this->assertFalse($rule->validateValues(array()));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\random $rule
	 */
	public function testCannotValidateWrongConditions($rule)
	{
		$this->assertFalse($rule->validateValues(array(0)));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\random $rule
	 */
	public function testCannotValidateLessThanTwo($rule)
	{
		$this->assertFalse($rule->validateValues(array(1)));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\random $rule
	 */
	public function testCanValidateMoreThanOne($rule)
	{
		$this->assertTrue($rule->validateValues(array(2)));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\random $rule
	 */
	public function testCannotRuleRuleWithNullCondition($rule)
	{
		$this->assertFalse($rule->isTrue(null));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\random $rule
	 */
	public function testCannotRuleRuleWithNoCondition($rule)
	{
		$this->assertFalse($rule->isTrue(array()));
	}
}
