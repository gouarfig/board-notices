<?php

namespace fq\boardnotices\tests\rules;

include_once 'phpBB/includes/functions.php';

use fq\boardnotices\rules\registered_before;
use fq\boardnotices\tests\mock\mock_api;

class registered_before_test extends rule_test_base
{

	public function testInstance()
	{
		$api = new mock_api();
		$rule = new registered_before($this->getSerializer(), $api);
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
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetType($rule)
	{
		$type = $rule->getType();
		$this->assertThat($type, $this->equalTo('fulldate'));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetPossibleValues($rule)
	{
		$values = $rule->getPossibleValues();
		$this->assertThat($values, $this->isNull());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function getDefaultValue($rule)
	{
		$this->assertEquals(array(0, 0, 0), $rule->getDefault());
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\anniversary $rule
	 */
	public function testCannotValidateNullConditions($rule)
	{
		$this->assertFalse($rule->validateValues(null));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\anniversary $rule
	 */
	public function testCannotValidateEmptyConditions($rule)
	{
		$this->assertFalse($rule->validateValues(array(0, 0, 0)));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\anniversary $rule
	 */
	public function testCannotValidateWrongConditions($rule)
	{
		$this->assertFalse($rule->validateValues(array(1, 2, 3, 4)));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetAvailableVars($rule)
	{
		$vars = $rule->getAvailableVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testGetTemplateVars($rule)
	{
		$vars = $rule->getTemplateVars();
		$this->assertEquals(0, count($vars));
	}

	/**
	 * @depends testInstance
	 * @param \fq\boardnotices\rules\date $rule
	 */
	public function testIsFalseWithNullConditions($rule)
	{
		$this->assertFalse($rule->isTrue(null));
		$this->assertFalse($rule->isTrue(serialize(null)));
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

	public function getTestData()
	{
		$now = getdate();
		$data = array();
		$timezones = $this->getTimezones();
		foreach ($timezones as $timezone)
		{
			// array(timezone, day, month, year, result)
			$data = array_merge($data, array(
				array($timezone[0], null, null, null, false),			// Empty
				array($timezone[0], 1, 1, 2010, false),					// Before
				array($timezone[0], 1, 3, 2010, true),					// After
				array($timezone[0], 28, 2, 2010, false),				// Same day
			));
		}
		return $data;
	}

	/**
	 * @dataProvider getTestData
	 */
	public function testConditions($timezone, $day, $month, $year, $result)
	{
		$current_timezone = date_default_timezone_get();
		// Make sure this calculation is independant of the server timezone
		date_default_timezone_set($timezone);
		$api = new mock_api();
		$api->setTimezone($timezone);
		// User registered on 28th Feb 2010
		$api->setUserRegistrationDate(gmmktime(12, 22, 33, 2, 28, 2010));
		$rule = new registered_before($this->getSerializer(), $api);
		$this->assertEquals(
			$result,
			$rule->isTrue(serialize(array($day, $month, $year))));
		// Put the timezone back
		date_default_timezone_set($current_timezone);
	}

}
