<?php

namespace fq\boardnotices\tests;

class board_notices_module_test extends \PHPUnit_Framework_TestCase
{
	public function testCanInstanciate()
	{
		$p_master = null;
		$module = new \fq\boardnotices\acp\board_notices_module($p_master);
		$this->assertNotNull($module);
	}

}
