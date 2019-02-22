<?php

namespace fq\boardnotices\tests\acp;

class board_notices_module_test extends \PHPUnit_Framework_TestCase
{
	private $request;
	private $settings;
	private $template;
	private $functions;

	public function setUp()
	{
		$this->request = null;
		$this->settings = null;
		$this->template = null;
		$this->functions = null;
	}

	public function testCanInstanciate()
	{
		$p_master = null;
		$module = new \fq\boardnotices\acp\board_notices_module($p_master);
		$this->assertNotNull($module);
	}

	public function container($arg)
	{
		$map = array(
			'language' => $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock(),
			'request' => $this->request,
			'template' => $this->template,
			'fq.boardnotices.acp.settings' => $this->settings,
			'fq.boardnotices.service.phpbb.functions' => $this->functions,
		);
		return $map[$arg];
	}

	public function testCanCallMainFunctionWithInvalidParameters()
	{
		global $phpbb_container, $phpbb_root_path, $phpEx;

		$phpbb_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
		$phpbb_container
			->method('get')
			->willReturnCallback(array($this, 'container'));
		$p_master = null;
		$module = new \fq\boardnotices\acp\board_notices_module($p_master);
		$module->main('\fq\boardnotices\acp\board_notices_module', '');

		unset($phpbb_container);
		unset($phpbb_root_path);
		unset($phpEx);
	}

	public function testCanDisplaySettingsForm()
	{
		global $phpbb_container, $phpbb_root_path, $phpEx;

		$this->request = new \phpbb_mock_request(
			/* GET  */ array('action' => ''),
			/* POST */ array()
		);
		$this->settings = $this->getMockBuilder('fq\boardnotices\acp\settings')->disableOriginalConstructor()->getMock();
		$this->settings->expects($this->never())->method('saveSettings');
		$this->settings->expects($this->once())->method('loadSettings')->willReturn(array(
			'boardnotices_enabled' => true,
			'track_forums_visits' => false,
			'boardnotices_default_bgcolor' => 'default',
		));
		$this->template = $this->getMock('phpbb\template\template');
		$this->template->expects($this->once())->method('assign_vars')->willReturnSelf();
		$phpbb_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
		$phpbb_container
			->method('get')
			->willReturnCallback(array($this, 'container'));
		$p_master = null;
		$module = new \fq\boardnotices\acp\board_notices_module($p_master);
		$module->main('\fq\boardnotices\acp\board_notices_module', 'settings');

		unset($phpbb_container);
		unset($phpbb_root_path);
		unset($phpEx);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testCanSaveSettings()
	{
		global $phpbb_container, $phpbb_root_path, $phpEx;

		$this->request = new \phpbb_mock_request(
			/* GET  */ array('action' => ''),
			/* POST */ array('submit' => 1)
		);
		$this->settings = $this->getMockBuilder('fq\boardnotices\acp\settings')->disableOriginalConstructor()->getMock();
		$this->settings->expects($this->never())->method('loadSettings');
		$this->template = $this->getMock('phpbb\template\template');
		$this->template->expects($this->never())->method('assign_vars')->willReturnSelf();
		$this->functions = $this->getMock('fq\boardnotices\service\phpbb\functions_interface');
		$phpbb_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
		$phpbb_container
			->method('get')
			->willReturnCallback(array($this, 'container'));
		$p_master = null;
		$module = new \fq\boardnotices\acp\board_notices_module($p_master);
		$module->main('\fq\boardnotices\acp\board_notices_module', 'settings');

		unset($phpbb_container);
		unset($phpbb_root_path);
		unset($phpEx);
	}

	public function testCanResetForumsVisits()
	{
		global $phpbb_container, $phpbb_root_path, $phpEx;

		$this->request = new \phpbb_mock_request(
			/* GET  */ array('action' => 'reset_forum_visits')
		);
		$this->settings = $this->getMockBuilder('fq\boardnotices\acp\settings')->disableOriginalConstructor()->getMock();
		$this->settings->expects($this->once())->method('resetForumVisits');
		$this->template = $this->getMock('phpbb\template\template');
		$this->functions = $this->getMock('fq\boardnotices\service\phpbb\functions_interface');
		$phpbb_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
		$phpbb_container
			->method('get')
			->willReturnCallback(array($this, 'container'));
		$p_master = null;
		$module = new \fq\boardnotices\acp\board_notices_module($p_master);
		$module->main('\fq\boardnotices\acp\board_notices_module', 'settings');

		unset($phpbb_container);
		unset($phpbb_root_path);
		unset($phpEx);
	}

	public function testCanDisplayNoticeManagerEmptyList()
	{
		global $phpbb_container, $phpbb_root_path, $phpEx;

		$this->request = new \phpbb_mock_request(
			/* GET  */ array('action' => ''),
			/* POST */ array()
		);
		$this->settings = $this->getMockBuilder('fq\boardnotices\acp\settings')->disableOriginalConstructor()->getMock();
		$this->settings->expects($this->once())->method('loadNotices')->willReturn(array());

		$this->template = $this->getMock('phpbb\template\template');
		$this->template->expects($this->once())->method('assign_vars')->willReturnSelf();
		$phpbb_container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
		$phpbb_container
			->method('get')
			->willReturnCallback(array($this, 'container'));
		$p_master = null;
		$module = new \fq\boardnotices\acp\board_notices_module($p_master);
		$module->main('\fq\boardnotices\acp\board_notices_module', 'manage');

		unset($phpbb_container);
		unset($phpbb_root_path);
		unset($phpEx);
	}

}
