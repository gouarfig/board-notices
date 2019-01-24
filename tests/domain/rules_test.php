<?php

namespace fq\boardnotices\tests;

use \fq\boardnotices\domain\rules;
use \fq\boardnotices\service\serializer;
use \fq\boardnotices\tests\mock_rules\mock_rule_1;
use \fq\boardnotices\tests\mock_rules\mock_rule_2;

class rules_test extends \PHPUnit_Framework_TestCase
{
	/**
	 * @return \fq\boardnotices\service\constants $constants
	 */
	private function getConstants()
	{
		/** @var \fq\boardnotices\service\constants $constants */
		static $constants;

		if (!empty($constants))
		{
			return $constants;
		}
		$constants = new \fq\boardnotices\service\constants();
		// We fake the folder where the rules sit
		$constants::$RULES_FOLDER = 'mock_rules';
		$constants::$RULES_CLASS_PREFIX = 'fq.boardnotices.tests.mock_rules';
		return $constants;
	}

	public function testInstance()
	{
		$constants = $this->getConstants();
		$root = __DIR__ . '/../';
		$rules = new rules($root, $constants);
		$this->assertNotNull($rules);

		return $rules;
	}

	public function setUp()
	{
		// We recreate a new container for each test
		global $phpbb_container;

		$phpbb_container = new \phpbb_mock_container_builder();

		$constants = $this->getConstants();
		$serializer = new serializer();
		// Creates the mock rules an adds them to the container
		$phpbb_container->set("fq.boardnotices.tests.mock_rules.mock_rule_1", new mock_rule_1($constants, $serializer));
		$phpbb_container->set("fq.boardnotices.tests.mock_rules.mock_rule_2", new mock_rule_2($constants, $serializer));
	}

	public function tearDown()
	{
		// We delete the DI container after each test
		global $phpbb_container;

		unset($phpbb_container);
	}

	/**
	 * @depends testInstance
	 * @param fq\boardnotices\domain\rules $rules
	 */
	public function testGetDefinedRules($rules)
	{
		global $phpbb_container;

		$definedRules = $rules->getDefinedRules();
		$this->assertEquals(2, count($definedRules));
	}

	/**
	 * @depends testInstance
	 * @param fq\boardnotices\domain\rules $rules
	 */
	public function testCannotGetUndefinedRule($rules)
	{
		global $phpbb_container;

		$definedRules = $rules->getDefinedRules();
		$this->assertNull($definedRules['rule_does_not_exist']);
	}

	/**
	 * @depends testInstance
	 * @param fq\boardnotices\domain\rules $rules
	 */
	public function testGetDisplayNameForSimpleName($rules)
	{
		global $phpbb_container;

		$definedRules = $rules->getDefinedRules();
		$this->assertEquals("Mock Rule 1", $definedRules['mock_rule_1']);
	}

	/**
	 * @depends testInstance
	 * @param fq\boardnotices\domain\rules $rules
	 */
	public function testGetDisplayNameWithUnit($rules)
	{
		global $phpbb_container;

		$definedRules = $rules->getDefinedRules();
		$expected = array(
			'display_name' => 'Mock Rule 2',
			'display_unit' => 'Mock Unit',
		);
		$this->assertEquals($expected, $definedRules['mock_rule_2']);
	}

	/**
	 * @depends testInstance
	 * @param fq\boardnotices\domain\rules $rules
	 */
	public function testGetRuleType($rules)
	{
		global $phpbb_container;

		$this->assertEquals("n/a", $rules->getRuleType('mock_rule_1'));
	}
}
