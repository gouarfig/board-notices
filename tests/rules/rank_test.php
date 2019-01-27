<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\rank;

class rank_test extends rule_test_base
{
	private $ranks = array(
		'normal' => array(
			1000 => array('rank_min' => 1000),
			100 => array('rank_min' => 100),
			10 => array('rank_min' => 10),
		)
	);

	public function testInstance()
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();

		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();

		$rule = new rank($this->getSerializer(), $user, $datalayer);
		$this->assertNotNull($rule);

		return array($serializer, $user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param rank $rule
	 */
	public function testGetDisplayName($args)
	{
		list($serializer, $user, $rule) = $args;
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param rank $rule
	 */
	public function testGetType($args)
	{
		list($serializer, $user, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('multiple choice'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param rank $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($serializer, $user, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\service\serializer $serializer
	 * @param \phpbb\user $user
	 * @param rank $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($serializer, $user, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertEquals(2, count($vars));
	}

	public function specialRankconditionsProvider()
	{
		$serializer = $this->getSerializer();
		return array(
			// Empty conditions
			array(null, null, false),
			array(null, serialize(null), false),
			array(null, $serializer->encode(null), false),
			// Wrong rank from one selection
			array(1, 2, false),
			array(1, serialize(2), false),
			array(1, $serializer->encode(2), false),
			// Wrong rank from one selection in an array
			array(1, array(2), false),
			array(1, serialize(array(2)), false),
			array(1, $serializer->encode(array(2)), false),
			// Wrong rank from multiple selections
			array(1, array(2, 3, 4), false),
			array(1, serialize(array(2, 3, 4)), false),
			array(1, $serializer->encode(array(2, 3, 4)), false),
			// Right rank from one selection
			array(1, 1, true),
			array(1, serialize(1), true),
			array(1, $serializer->encode(1), true),
			// Right rank from one selection in an array
			array(1, array(1), true),
			array(1, serialize(array(1)), true),
			array(1, $serializer->encode(array(1)), true),
			// Right rank from multiple selections
			array(1, array(2, 1, 4), true),
			array(1, serialize(array(2, 1, 4)), true),
			array(1, $serializer->encode(array(2, 1, 4)), true),
		);
	}

	/**
	 * @dataProvider specialRankconditionsProvider
	 * @param int $userRank
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditionsWithSpecialRank($userRank, $conditions, $result)
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['user_rank'] = $userRank;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();

		global $cache;
		$cache = $this->getMockBuilder('\phpbb\cache\service')->disableOriginalConstructor()->getMock();
		$cache->method('obtain_ranks')->will($this->returnValue($this->ranks));

		$rule = new rank($serializer, $user, $datalayer);

		$this->assertEquals($result, $rule->isTrue($conditions));
		unset($cache);
	}

	public function normalRankconditionsProvider()
	{
		$serializer = $this->getSerializer();
		return array(
			// Empty conditions
			array(null, null, false),
			array(null, serialize(null), false),
			array(null, $serializer->encode(null), false),
			// Wrong rank from one selection
			array(1, 10, false),
			array(1, serialize(10), false),
			array(1, $serializer->encode(10), false),
			// Wrong rank from one selection in an array
			array(1, array(10), false),
			array(1, serialize(array(10)), false),
			array(1, $serializer->encode(array(10)), false),
			// Wrong rank from multiple selections
			array(1, array(10, 100, 1000), false),
			array(1, serialize(array(10, 100, 1000)), false),
			array(1, $serializer->encode(array(10, 100, 1000)), false),
			// Right rank from one selection
			array(11, 10, true),
			array(11, serialize(10), true),
			array(11, $serializer->encode(10), true),
			array(101, 100, true),
			array(101, serialize(100), true),
			array(101, $serializer->encode(100), true),
			array(1001, 1000, true),
			array(1001, serialize(1000), true),
			array(1001, $serializer->encode(1000), true),
			// Right rank from one selection in an array
			array(11, array(10), true),
			array(11, serialize(array(10)), true),
			array(11, $serializer->encode(array(10)), true),
			array(101, array(100), true),
			array(101, serialize(array(100)), true),
			array(101, $serializer->encode(array(100)), true),
			array(1001, array(1000), true),
			array(1001, serialize(array(1000)), true),
			array(1001, $serializer->encode(array(1000)), true),
			// Right rank from multiple selections
			array(11, array(10, 100, 1000), true),
			array(11, serialize(array(10, 100, 1000)), true),
			array(11, $serializer->encode(array(10, 100, 1000)), true),
			array(101, array(10, 100, 1000), true),
			array(101, serialize(array(10, 100, 1000)), true),
			array(101, $serializer->encode(array(10, 100, 1000)), true),
			array(1001, array(10, 100, 1000), true),
			array(1001, serialize(array(10, 100, 1000)), true),
			array(1001, $serializer->encode(array(10, 100, 1000)), true),
		);
	}

	/**
	 * @dataProvider normalRankconditionsProvider
	 * @param int $userPosts
	 * @param mixed $conditions
	 * @param bool $result
	 */
	public function testRuleConditionsWithNormalRank($userPosts, $conditions, $result)
	{
		$serializer = $this->getSerializer();
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['user_posts'] = $userPosts;
		/** @var \fq\boardnotices\repository\legacy_interface $datalayer */
		$datalayer = $this->getMockBuilder('\fq\boardnotices\repository\legacy_interface')->getMock();

		global $cache;
		$cache = $this->getMockBuilder('\phpbb\cache\service')->disableOriginalConstructor()->getMock();
		$cache->method('obtain_ranks')->will($this->returnValue($this->ranks));

		$rule = new rank($serializer, $user, $datalayer);

		$this->assertEquals($result, $rule->isTrue($conditions));
		unset($cache);
	}

}
