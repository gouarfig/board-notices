<?php

namespace fq\boardnotices\tests;

use \fq\boardnotices\domain\notice;

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
}
