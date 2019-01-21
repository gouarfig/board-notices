<?php

namespace fq\boardnotices\tests\service;

use fq\boardnotices\serializer;

class serializer_test extends \phpbb_test_case
{
	public function getTestData()
	{
		return array(
			array(10),
			array('toto'),
			array(array(10)),
			array(array('toto')),
			array(array(10, 'toto')),
			array(true),
			array(false),
			array(null),
			array(0),
			array(-1),
		);
	}

	/**
	 * @dataProvider getTestData
	 * @param mixed $data
	 */
	public function testCanUnserialize($data)
	{
		$serialized = serialize($data);
		$serializer = new serializer();
		$this->assertEquals($data, $serializer->decode($serialized));
	}

	/**
	 * @dataProvider getTestData
	 * @param mixed $data
	 */
	public function testCanDecodeJson($data)
	{
		$serialized = "json:" . json_encode($data);
		$serializer = new serializer();
		$this->assertEquals($data, $serializer->decode($serialized));
	}

	/**
	 * @dataProvider getTestData
	 * @param mixed $data
	 */
	public function testCanEncodeAndDecode($data)
	{
		$serializer = new serializer();
		$serialized = $serializer->encode($data);
		$this->assertEquals($data, $serializer->decode($serialized));
	}

}
