<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use \fq\boardnotices\rules\has_posted_less;

class has_posted_less_test extends rule_test_base
{
	public function testInstance()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$rule = new has_posted_less($this->getSerializer(), $user);
		$this->assertNotNull($rule);

		return array($user, $rule);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testGetDisplayName($args)
	{
		list($user, $rule) = $args;
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testGetType($args)
	{
		list($user, $rule) = $args;
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('int'));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testGetPossibleValues($args)
	{
		list($user, $rule) = $args;
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testGetAvailableVars($args)
	{
		list($user, $rule) = $args;
		$vars = $rule->getAvailableVars();
		$this->assertContains('POSTS', $vars);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testGetTemplateVars($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 63;
		$vars = $rule->getTemplateVars();
		$this->assertEquals(63, $vars['POSTS']);
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleEquals($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 63;

		$posts = serialize(array(63));
		$this->assertTrue($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleEquals2($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 63;

		$posts = $this->getSerializer()->encode(array(63));
		$this->assertTrue($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleEqualsNoArray($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 63;

		$posts = serialize(63);
		$this->assertTrue($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleEqualsNoArray2($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 63;

		$posts = $this->getSerializer()->encode(63);
		$this->assertTrue($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleEqualsNoSerialize($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 63;

		$posts = 63;
		$this->assertTrue($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleLessThan($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 53;

		$posts = 63;
		$this->assertTrue($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleLessThanSerialize($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 53;

		$posts = serialize(63);
		$this->assertTrue($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleLessThanSerialize2($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 53;

		$posts = $this->getSerializer()->encode(63);
		$this->assertTrue($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleLessThanSerializeArray($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 53;

		$posts = serialize(array(63));
		$this->assertTrue($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleLessThanSerializeArray2($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 53;

		$posts = $this->getSerializer()->encode(array(63));
		$this->assertTrue($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleMoreThan($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 73;

		$posts = 63;
		$this->assertFalse($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleMoreThanSerialize($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 73;

		$posts = serialize(63);
		$this->assertFalse($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleMoreThanSerialize2($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 73;

		$posts = $this->getSerializer()->encode(63);
		$this->assertFalse($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleMoreThanArraySerialize($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 73;

		$posts = serialize(array(63));
		$this->assertFalse($rule->isTrue($posts));
	}

	/**
	 * @depends testInstance
	 * @param \phpbb\user $user
	 * @param has_posted_less $rule
	 */
	public function testRuleMoreThanArraySerialize2($args)
	{
		list($user, $rule) = $args;
		$user->data['user_posts'] = 73;

		$posts = $this->getSerializer()->encode(array(63));
		$this->assertFalse($rule->isTrue($posts));
	}

}
