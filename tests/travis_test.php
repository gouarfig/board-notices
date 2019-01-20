<?php

/**
 * This is a simple test to make sure the platform is working
 */

namespace fq\boardnotices\tests;

class TravisTest extends \PHPUnit_Framework_TestCase
{
	public function testTravis()
	{
		$stack = array();
		$this->assertEquals(0, count($stack));
	}
}
