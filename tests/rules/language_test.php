<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\language;
use fq\boardnotices\tests\mock\mock_api;

class language_test extends rule_test_base
{
	public function testInstance()
	{
		$serializer = $this->getSerializer();
		$api = new mock_api();

		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();

		$rule = new language($this->getSerializer(), $api, $datalayer);
		$this->assertNotNull($rule);

		return array($serializer, $api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param language $rule
	 */
	public function testGetDisplayName($args)
	{
		list($serializer, $api, $rule) = $args;
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param language $rule
	 */
	public function testGetType($args)
	{
		list($serializer, $api, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('multiple choice'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param language $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($serializer, $api, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param language $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($serializer, $api, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param language $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($serializer, $api, $rule) = $args;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(0, count($vars));
	}

	public function conditionsProvider()
	{
		$serializer = $this->getSerializer();
		return array(
			// Empty conditions
			array(null, false),
			array(serialize(null), false),
			array($serializer->encode(null), false),
			// Not the user language
			array('en', false),
			array(serialize('en'), false),
			array($serializer->encode('en'), false),
			// Right language
			array('fr', true),
			array(serialize('fr'), true),
			array($serializer->encode('fr'), true),
			// Array not containing user language
			array(array('en', 'es'), false),
			array(serialize(array('en', 'es')), false),
			array($serializer->encode(array('en', 'es')), false),
			// Array containing the user language
			array(array('en', 'fr'), true),
			array(serialize(array('en', 'fr')), true),
			array($serializer->encode(array('en', 'fr')), true),
		);
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditions($conditions, $result)
	{
		$serializer = $this->getSerializer();
		$api = new mock_api();
		$api->setUserLanguage('fr');
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();

		$rule = new language($serializer, $api, $datalayer);

		$this->assertEquals($result, $rule->isTrue($conditions));
	}

}
