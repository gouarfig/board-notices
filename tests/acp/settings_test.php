<?php

namespace fq\boardnotices\tests\acp;

class settings_test extends \PHPUnit_Framework_TestCase
{
	public function testCanInstanciate()
	{
		$api = new \fq\boardnotices\tests\mock\mock_api();
		$functions = $this->getMockBuilder('\fq\boardnotices\service\phpbb\functions_interface')->getMock();
		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();
		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$module = new \fq\boardnotices\acp\settings($api, $functions, $request, $notices_repository);
		$this->assertNotNull($module);
	}

	public function testCanResetForumVisits()
	{
		$api = new \fq\boardnotices\tests\mock\mock_api();
		$functions = $this->getMockBuilder('\fq\boardnotices\service\phpbb\functions_interface')->getMock();
		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();
		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$module = new \fq\boardnotices\acp\settings($api, $functions, $request, $notices_repository);
		$module->resetForumVisits('', '', '');
	}
}
