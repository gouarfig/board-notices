<?php

namespace fq\boardnotices\tests;

use \fq\boardnotices\domain\notice;
use \fq\boardnotices\service\serializer;
use \fq\boardnotices\tests\mock\rules\mock_rule_1;
use \fq\boardnotices\tests\mock\rules\mock_rule_2;

class notice_test extends \PHPUnit_Framework_TestCase
{

	public function testEmptyInstance()
	{
		$properties = array();
		$rules = array();
		$notice = new notice($properties, $rules);
		$this->assertNotNull($notice);

		return $notice;
	}

	public function testCannotBeDismissed()
	{
		$properties = array(
			'notice_id' => 11,
			'dismissable' => false,
		);
		$rules = array();
		$dismissed = array(
			11 => array(
				'seen' => time(),
			),
		);
		$notice = new notice($properties, $rules, $dismissed);
		$this->assertFalse($notice->isDismissed());
	}

	public function testIsNotDismissed()
	{
		$properties = array(
			'notice_id' => 11,
			'dismissable' => true,
		);
		$rules = array();
		$notice = new notice($properties, $rules);
		$this->assertFalse($notice->isDismissed());
	}

	public function testIsDismissed()
	{
		$properties = array(
			'notice_id' => 11,
			'dismissable' => true,
		);
		$rules = array();
		$dismissed = array(
			11 => array(
				'seen' => time() - (2 * 86400),
			),
		);
		$notice = new notice($properties, $rules, $dismissed);
		$this->assertTrue($notice->isDismissed());
	}

	public function testAnotherOneIsDismissed()
	{
		$properties = array(
			'notice_id' => 11,
			'dismissable' => true,
		);
		$rules = array();
		$dismissed = array(
			12 => array(
				'seen' => time() - (2 * 86400),
			),
		);
		$notice = new notice($properties, $rules, $dismissed);
		$this->assertFalse($notice->isDismissed());
	}

	public function testIsDismissedButNotExpired()
	{
		$properties = array(
			'notice_id' => 11,
			'dismissable' => true,
			'reset_after' => 3,
		);
		$rules = array();
		$dismissed = array(
			11 => array(
				'seen' => time() - (2 * 86400),
			),
		);
		$notice = new notice($properties, $rules, $dismissed);
		$this->assertTrue($notice->isDismissed());
	}

	public function testIsDismissedButExpired()
	{
		$properties = array(
			'notice_id' => 11,
			'dismissable' => true,
			'reset_after' => 1,
		);
		$rules = array();
		$dismissed = array(
			11 => array(
				'seen' => time() - (2 * 86400),
			),
		);
		$notice = new notice($properties, $rules, $dismissed);
		$this->assertFalse($notice->isDismissed());
	}

	public function testRuleConditionsAreValid()
	{
		global $phpbb_container;
		$phpbb_container = new \phpbb_mock_container_builder();

		$serializer = new serializer();
		// Creates the mock rules an adds them to the container
		$phpbb_container->set("fq.boardnotices.rules.mock_rule_1", new mock_rule_1($serializer));
		$phpbb_container->set("fq.boardnotices.rules.mock_rule_2", new mock_rule_2($serializer));

		$properties = array(
			'notice_id' => 11,
			'dismissable' => false,
		);
		$rules = array(
			array(
				'rule' => 'mock_rule_1',
				'conditions' => 'N;',
			),
		);
		$notice = new notice($properties, $rules);
		$this->assertTrue($notice->hasValidatedAllRules());

		unset($phpbb_container);
	}

	public function testRuleConditionsAreNotValid()
	{
		global $phpbb_container;
		$phpbb_container = new \phpbb_mock_container_builder();

		$serializer = new serializer();
		// Creates the mock rules an adds them to the container
		$phpbb_container->set("fq.boardnotices.rules.mock_rule_1", new mock_rule_1($serializer));
		$phpbb_container->set("fq.boardnotices.rules.mock_rule_2", new mock_rule_2($serializer));

		$properties = array(
			'notice_id' => 11,
			'dismissable' => false,
		);
		$rules = array(
			array(
				'rule' => 'mock_rule_2',
				'conditions' => 'N;',
			),
		);
		$notice = new notice($properties, $rules);
		$this->assertFalse($notice->hasValidatedAllRules());

		unset($phpbb_container);
	}

	public function testRuleConditionsAreValidButNoticeWasDismissed()
	{
		global $phpbb_container;
		$phpbb_container = new \phpbb_mock_container_builder();

		$serializer = new serializer();
		// Creates the mock rules an adds them to the container
		$phpbb_container->set("fq.boardnotices.rules.mock_rule_1", new mock_rule_1($serializer));
		$phpbb_container->set("fq.boardnotices.rules.mock_rule_2", new mock_rule_2($serializer));

		$properties = array(
			'notice_id' => 11,
			'dismissable' => true,
			'reset_after' => 0,
		);
		$rules = array(
			array(
				'rule' => 'mock_rule_1',
				'conditions' => 'N;',
			),
		);
		$dismissed = array(
			11 => array(
				'seen' => time() - (2 * 86400),
			),
		);
		$notice = new notice($properties, $rules, $dismissed);
		$this->assertFalse($notice->hasValidatedAllRules());

		unset($phpbb_container);
	}
}
