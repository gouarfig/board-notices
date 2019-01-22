<?php

namespace fq\boardnotices\tests;

/**
 * This is a simple test to make sure the administation information module is giving the information
 * to build the admin interface
 */
class board_notices_info_test extends \PHPUnit_Framework_TestCase
{
	public function testCanInstanciate()
	{
		$info = new \fq\boardnotices\acp\board_notices_info();
		$this->assertNotNull($info);
	}

	/**
	 * @depends testCanInstanciate
	 */
	public function testModule()
	{
		$info = new \fq\boardnotices\acp\board_notices_info();
		$module = $info->module();
		$this->assertArrayHasKey('filename', $module);
		$this->assertArrayHasKey('title', $module);
		$this->assertArrayHasKey('modes', $module);
	}
}
