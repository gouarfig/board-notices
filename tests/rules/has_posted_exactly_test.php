<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\has_posted_exactly;
use fq\boardnotices\tests\mock\mock_api;

class has_posted_exactly_test extends rule_test_base
{
	public function testInstance()
	{
		$api = new mock_api();
		$rule = new has_posted_exactly($this->getSerializer(), $api);
		$this->assertNotNull($rule);

		return array($api, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_posted_exactly $rule
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
	 * @param has_posted_exactly $rule
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
	 * @param has_posted_exactly $rule
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
	 * @param has_posted_exactly $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($api, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertContains('POSTS', $vars);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\tests\mock\mock_api $api
	 * @param has_posted_exactly $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($api, $rule) = $args;
		$api->setUserPostCount(63);
		$vars = $rule->getTemplateVars();
		$this->assertEquals(63, $vars['POSTS']);
	}

	public function getTestData()
	{
		$serializer = $this->getSerializer();
		return array(
			array(63, 63, true),
			array(63, serialize(63), true),
			array(63, serialize(array(63)), true),
			array(63, $serializer->encode(63), true),
			array(63, $serializer->encode(array(63)), true),
			array(53, 63, false),
			array(53, serialize(63), false),
			array(53, serialize(array(63)), false),
			array(53, $serializer->encode(63), false),
			array(53, $serializer->encode(array(63)), false),
			array(73, 63, false),
			array(73, serialize(63), false),
			array(73, serialize(array(63)), false),
			array(73, $serializer->encode(63), false),
			array(73, $serializer->encode(array(63)), false),
		);
	}

	/**
	 * @dataProvider getTestData
	 *
	 * @param int $posts
	 * @param mixed $condition
	 * @param bool $result
	 * @return void
	 */
	public function testRules($posts, $condition, $result)
	{
		$api = new mock_api();
		$rule = new has_posted_exactly($this->getSerializer(), $api);

		$api->setUserPostCount($posts);
		$this->assertEquals($result, $rule->isTrue($condition));
	}

}
