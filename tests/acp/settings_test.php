<?php

namespace fq\boardnotices\tests\acp;

class settings_test extends \PHPUnit_Framework_TestCase
{
	private function getDefaultConfig()
	{
		return new \phpbb\config\config(array(
			'boardnotices_enabled' => true,
			'track_forums_visits' => true,
			'boardnotices_default_bgcolor' => '',
		));
	}

	public function testCanInstanciate()
	{
		$api = new \fq\boardnotices\tests\mock\mock_api();
		$functions = $this->getMockBuilder('\fq\boardnotices\service\phpbb\functions_interface')->getMock();
		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();
		$config = $this->getDefaultConfig();
		$log = $this->getMockBuilder('\phpbb\log\log_interface')->getMock();
		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$module = new \fq\boardnotices\acp\settings($api, $functions, $request, $config, $log, $notices_repository);
		$this->assertNotNull($module);
	}

	public function testCanAskConfirmationForResetForumVisits()
	{
		$api = new \fq\boardnotices\tests\mock\mock_api();
		$functions = $this->getMockBuilder('\fq\boardnotices\service\phpbb\functions_interface')->getMock();
		$functions->expects($this->exactly(2))->method('confirm_box')->willReturn(false);
		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();
		$config = $this->getDefaultConfig();
		$log = $this->getMockBuilder('\phpbb\log\log_interface')->getMock();
		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$notices_repository->expects($this->never())->method('clearForumVisited');

		$module = new \fq\boardnotices\acp\settings($api, $functions, $request, $config, $log, $notices_repository);
		$module->resetForumVisits('', '', '');
	}

	public function testCanResetForumVisits()
	{
		$api = new \fq\boardnotices\tests\mock\mock_api();
		$functions = $this->getMockBuilder('\fq\boardnotices\service\phpbb\functions_interface')->getMock();
		$functions->expects($this->once())->method('confirm_box')->willReturn(true);	// This defines the result of the confirmation box
		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();
		$config = $this->getDefaultConfig();
		$log = $this->getMockBuilder('\phpbb\log\log_interface')->getMock();
		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$notices_repository->expects($this->once())->method('clearForumVisited');

		$module = new \fq\boardnotices\acp\settings($api, $functions, $request, $config, $log, $notices_repository);
		$module->resetForumVisits('', '', '');
	}

	public function testCanLoadSettings()
	{
		$api = new \fq\boardnotices\tests\mock\mock_api();
		$functions = $this->getMockBuilder('\fq\boardnotices\service\phpbb\functions_interface')->getMock();
		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();
		$config = $this->getDefaultConfig();
		$log = $this->getMockBuilder('\phpbb\log\log_interface')->getMock();
		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$module = new \fq\boardnotices\acp\settings($api, $functions, $request, $config, $log, $notices_repository);
		$settings = $module->loadSettings();
		$this->assertNotNull($settings);
	}

	public function testCanSaveSettings()
	{
		$api = new \fq\boardnotices\tests\mock\mock_api();
		$functions = $this->getMockBuilder('\fq\boardnotices\service\phpbb\functions_interface')->getMock();
		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();
		$config = $this->getDefaultConfig();
		$log = $this->getMockBuilder('\phpbb\log\log_interface')->getMock();
		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$module = new \fq\boardnotices\acp\settings($api, $functions, $request, $config, $log, $notices_repository);
		$module->saveSettings(array(
			'boardnotices_enabled' => true,
			'track_forums_visits' => true,
			'boardnotices_default_bgcolor' => '',
		));
	}

	public function testCanLoadNotices()
	{
		$api = new \fq\boardnotices\tests\mock\mock_api();
		$functions = $this->getMockBuilder('\fq\boardnotices\service\phpbb\functions_interface')->getMock();
		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();
		$config = $this->getDefaultConfig();
		$log = $this->getMockBuilder('\phpbb\log\log_interface')->getMock();
		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$notices_repository->expects($this->once())->method('getAllNotices')->willReturn(array(
			array('notice_id' => 1),
			array('notice_id' => 2),
		));
		$notices_repository->expects($this->exactly(2))->method('getRulesFor')->willReturn(array(
			array(),
			array(),
		));
		$module = new \fq\boardnotices\acp\settings($api, $functions, $request, $config, $log, $notices_repository);
		$notices = $module->loadNotices();
		$this->assertCount(2, $notices);
		$this->assertEquals(2, $notices[0]['rulesCount']);
		$this->assertEquals(2, $notices[1]['rulesCount']);
	}
}
