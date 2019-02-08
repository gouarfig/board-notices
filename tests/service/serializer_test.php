<?php

namespace fq\boardnotices\tests\service;

use fq\boardnotices\service\serializer;

class serializer_test extends \phpbb_test_case
{
	public function getTestData()
	{
		return array(
			array(10),
			array('toto'),
			array(array()),
			array(array(10)),
			array(array('toto')),
			array(array(10, 'toto')),
			array(array(0, 0)),
			array(array('int' => 0, 'str' => 'toto')),
			array(array(0, 'int' => 1, 2)),
			array(array('"]}:;')),
			array(array(':;{["')),
			array(array(0, array(0), array('key' => 'value'))),
			array(array(array(null, array(true, false)))),
			array(array(null, null, null), array(array(array(null)))),
			array(array(true, null, false, null)),
			array(true),
			array(false),
			array(null),
			array(0),
			array(-1),
		);
	}

	public function getFailedSerializedData()
	{
		return array(
			array('b;'),
			array('b:;'),
			array('b:2;'),
			array('i:toto;'),
			array('s:'),
			array('s:;'),
			array('s:-1:"";'),
			array('s:3:;'),
			array('s:3:"12";'),
			array('s:"12";'),
			array('a:1:{i:0;}'),
			array('a:2:{i:0;i:10;}'),
			array('a:-1:{i:0;i:10;}'),
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

	/**
	 * @dataProvider getTestData
	 * @param mixed $data
	 */
	public function testCannotDecodeNonEncodedData($data)
	{
		$serializer = new serializer();
		$unserialized = $serializer->decode($data);
		$this->assertTrue($serializer->errorDetected());
		$this->assertNull($unserialized);
	}

	/**
	 * @dataProvider getFailedSerializedData
	 * @param string $data
	 */
	public function testFailedSerializedData($data)
	{
		$serializer = new serializer();
		$unserialized = $serializer->decode($data);
		$this->assertTrue($serializer->errorDetected());
		$this->assertNull($unserialized);
	}
}
