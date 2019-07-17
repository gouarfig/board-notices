<?php

namespace fq\boardnotices\tests\rules;

class rule_base_test extends rule_test_base
{
	public function testInstance()
	{
		$serializer = $this->getSerializer();
		$rule = new test_rule($this->getSerializer());
		$this->assertNotNull($rule);
	}

	public function getDataMultipleInt()
	{
		return array(
			array(null, array()),
			array("toto", array()),
			array("10", array(10)),
			array("", array()),
			array(" 10 ", array(10)),
			array("10,11", array(10, 11)),
			array(" 10 , 11 ", array(10, 11)),
			array("10, toto ,11", array(10, 11)),
		);
	}

	/**
	 * @dataProvider getDataMultipleInt
	 * @param array $input
	 * @param array $result
	 */
	public function testMultipleInt($input, $result)
	{
		$serializer = $this->getSerializer();
		$rule = new test_rule($this->getSerializer());
		$this->assertEquals($result, $rule->protectedGetArrayOfConditionsForMultipleIntegers($input));
	}
}
