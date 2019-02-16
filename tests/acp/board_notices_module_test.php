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

	public function testCanFireMainFunctionWithInvalidParameters()
	{
		global $phpbb_container, $phpbb_root_path, $phpEx;

		$phpbb_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
		$p_master = null;
		$module = new \fq\boardnotices\acp\board_notices_module($p_master);
		$module->main('\fq\boardnotices\acp\board_notices_module', '');

		unset($phpbb_container);
		unset($phpbb_root_path);
		unset($phpEx);
	}
}
