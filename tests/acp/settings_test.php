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

	public function testCanResetForumVisits()
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
}
