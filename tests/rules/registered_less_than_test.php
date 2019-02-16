<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\registered_less_than;

class registered_less_than_test extends rule_test_base
{
	public function testInstance()
	{
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$rule = new registered_less_than($this->getSerializer(), $user);
		$this->assertNotNull($rule);

		return $rule;
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetDisplayName($rule)
	{
		$display = $rule->getDisplayName();
		$this->assertNotEmpty($display, "DisplayName is empty");
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\registered_less_than $rule
	 */
	public function testGetType($rule)
	{
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('int'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\registered_less_than $rule
	 */
	public function testGetDefaultValue($rule)
	{
		$values = $rule->getDefault();
		$this->assertEquals(1, $values);
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\registered_less_than $rule
	 */
	public function testGetAvailableVars($rule)
	{
		$vars = $rule->getAvailableVars();
		$this->assertThat($vars, $this->contains('DAYS_SINCE_REGISTRATION'));
	}

	/**
	 * Lists a few random and extreme timezones
	 *
	 * @return array(string)
	 */
	public function getTimezones()
	{
		return array(
			array('Pacific/Midway'),
			array('Europe/London'),
			array('Pacific/Auckland'),
			array('Pacific/Norfolk'),
			array('Pacific/Kiritimati'),
			array('America/St_Johns'),
		);
	}

	public function dataProvider()
	{
		$timezones = array(
			'Pacific/Midway',
			'Europe/London',
			'Pacific/Auckland',
			'Pacific/Norfolk',
			'Pacific/Kiritimati',
			'America/St_Johns',
		);
		$data = array();
		foreach ($timezones as $timezone)
		{
			$data = array_merge($data, array(
				// Registered 2 minutes ago
				array($timezone, -120, 1, true),
				array($timezone, -120, serialize(1), true),
				// Registered 2 days ago
				array($timezone, -(2 * 86400), 1, false),
				array($timezone, -(2 * 86400), serialize(1), false),
				// Registered 2 days and 2 minutes ago
				array($timezone, -((2 * 86400) + 120), 2, true),
				array($timezone, -((2 * 86400) + 120), serialize(2), true),
				// Registered 23h and 59 minutes ago
				array($timezone, -((23 * 60 * 60) + (59 * 60)), 1, true),
				array($timezone, -((23 * 60 * 60) + (59 * 60)), serialize(1), true),
				// Registered 1 day and 1 minute ago
				array($timezone, -((24 * 60 * 60) + 600), 1, true),
				array($timezone, -((24 * 60 * 60) + 600), serialize(1), true),
			));
		}
		return $data;
	}

	/**
	 * @dataProvider dataProvider
	 * @param string $timezone
	 * @param int $registration
	 * @param int $days
	 * @param boolean $result
	 */
	public function testConditions($timezone, $registration, $days, $result)
	{
		$current_timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['is_registered'] = true;
		$user->timezone = new \DateTimeZone($timezone);
		$user->data['user_regdate'] = time() + $registration;
		$rule = new registered_less_than($this->getSerializer(), $user);
		$this->assertEquals($result, $rule->isTrue($days));
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

	/**
	 * @dataProvider dataProvider
	 * @param string $timezone
	 * @param int $registration
	 * @param int $days
	 * @param boolean $result
	 */
	public function testConditionsUnregisteredUser($timezone, $registration, $days, $result)
	{
		$current_timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
		/** @var \phpbb\user $user */
		$user = $this->getUser();
		$user->data['is_registered'] = false;
		$user->timezone = new \DateTimeZone($timezone);
		$user->data['user_regdate'] = time() + $registration;
		$rule = new registered_less_than($this->getSerializer(), $user);
		$this->assertFalse($rule->isTrue($days));
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

}
