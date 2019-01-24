<?php

namespace fq\boardnotices\tests;

use \fq\boardnotices\domain\rules;
use \fq\boardnotices\service\serializer;
use \fq\boardnotices\tests\mock_rules\mock_rule;

class rules_test extends \PHPUnit_Framework_TestCase
{
	public function testInstance()
	{
		$constants = new \fq\boardnotices\service\constants();
		// We fake the folder where the rules sit
		$constants::$RULES_FOLDER = 'mock_rules';
		$constants::$RULES_CLASS_PREFIX = 'fq.boardnotices.tests.mock_rules';
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

		$serializer = new serializer();
		// Creates a mock rule an adds it to the container
		$phpbb_container->set("fq.boardnotices.tests.mock_rules.mock_rule", new mock_rule($serializer));
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
		$this->assertEquals(1, count($definedRules));
	}

	/**
	 * @depends testInstance
	 * @param fq\boardnotices\domain\rules $rules
	 */
	public function testGetDisplayName($rules)
	{
		global $phpbb_container;

		$definedRules = $rules->getDefinedRules();
		$this->assertEquals("Mock Rule", $definedRules['mock_rule']);
	}
}
