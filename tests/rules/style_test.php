<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\style;
use fq\boardnotices\tests\mock\mock_api;

class style_test extends rule_test_base
{
	public function testInstance()
	{
		$serializer = $this->getSerializer();
		$api = new mock_api();

		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();

		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		$rule = new style($serializer, $api, $datalayer, $request, $config);
		$this->assertNotNull($rule);

		return array($serializer, $api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param style $rule
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
	 * @param style $rule
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
	 * @param style $rule
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
	 * @param style $rule
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
	 * @param style $rule
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
			array(null, null, false),
			array(null, serialize(null), false),
			array(null, $serializer->encode(null), false),
			// Wrong style from one selection
			array(1, 2, false),
			array(1, serialize(2), false),
			array(1, $serializer->encode(2), false),
			// Wrong style from one selection in an array
			array(1, array(2), false),
			array(1, serialize(array(2)), false),
			array(1, $serializer->encode(array(2)), false),
			// Wrong style from multiple selections
			array(1, array(2, 3, 4), false),
			array(1, serialize(array(2, 3, 4)), false),
			array(1, $serializer->encode(array(2, 3, 4)), false),
			// Right style from one selection
			array(1, 1, true),
			array(1, serialize(1), true),
			array(1, $serializer->encode(1), true),
			// Right style from one selection in an array
			array(1, array(1), true),
			array(1, serialize(array(1)), true),
			array(1, $serializer->encode(array(1)), true),
			// Right style from multiple selections
			array(1, array(2, 1, 4), true),
			array(1, serialize(array(2, 1, 4)), true),
			array(1, $serializer->encode(array(2, 1, 4)), true),
		);
	}

	/**
	 * @dataProvider conditionsProvider
	 * @param int $userStyle
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditionsForNormalUser($userStyle, $conditions, $result)
	{
		$serializer = $this->getSerializer();
		$api = new mock_api();
		$api->setUserAnonymous(false);
		$api->setUserStyle($userStyle);
		/** @var \fq\boardnotices\repository\users_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\users_interface')->getMock();

		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		$rule = new style($serializer, $api, $datalayer, $request, $config);

		$this->assertEquals($result, $rule->isTrue($conditions));
	}

}
