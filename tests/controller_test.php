<?php

namespace fq\boardnotices\tests;

use fq\boardnotices\controller\controller;
use fq\boardnotices\service\constants;

class controller_test extends \PHPUnit_Framework_TestCase
{
	public function testCanInstantiate()
	{
		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();

		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$controller = new controller($config, $request, $user, $notices_repository, $notices_seen_repository);
		$this->assertNotNull($controller);
	}

	/**
	 * @depends testCanInstantiate
	 * @expectedException phpbb\exception\http_exception
	 */
	public function testWithNoNoticeId()
	{
		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();

		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$controller = new controller($config, $request, $user, $notices_repository, $notices_seen_repository);
		$controller->close_notice();
	}

	/**
	 * @depends testCanInstantiate
	 * @expectedException phpbb\exception\http_exception
	 */
	public function testWithUnknownNoticeId()
	{
		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request(array('notice_id' => 11));

		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$controller = new controller($config, $request, $user, $notices_repository, $notices_seen_repository);
		$controller->close_notice();
	}

	/**
	 * @depends testCanInstantiate
	 * @expectedException phpbb\exception\http_exception
	 */
	public function testWithNotDismissableNotice()
	{
		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request(array('notice_id' => 11));

		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$notices_repository->method('getNoticeFromId')->will($this->returnValue(array('notice_id' => 11, 'dismissable' => false)));

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$controller = new controller($config, $request, $user, $notices_repository, $notices_seen_repository);
		$controller->close_notice();
	}

	/**
	 * @depends testCanInstantiate
	 * @expectedException phpbb\exception\http_exception
	 */
	public function testWithNoHash()
	{
		global $user;

		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request(array('notice_id' => 11));

		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$notices_repository->method('getNoticeFromId')->will($this->returnValue(array('notice_id' => 11, 'dismissable' => true)));

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$controller = new controller($config, $request, $user, $notices_repository, $notices_seen_repository);
		$controller->close_notice();

		$user = null;
	}

	/**
	 * @depends testCanInstantiate
	 * @expectedException \PHPUnit_Framework_Error_Warning
	 * @expectedExceptionMessage INSECURE_REDIRECT
	 */
	public function testWithValidHashInPreviewModeNonAjaxRequest()
	{
		global $user;
		global $phpbb_dispatcher;
		global $phpbb_path_helper;

		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();

		$phpbb_path_helper = $this->getMockBuilder('\phpbb\path_helper')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request(array('notice_id' => 11, 'hash' => '12345678', 'preview' => 1, 'redirect' => '/done'));

		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->data["hash_" . constants::$ROUTING_CLOSE_HASH_ID] = '12345678';

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$notices_repository->method('getNoticeFromId')->will($this->returnValue(array('notice_id' => 11, 'dismissable' => true)));

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$controller = new controller($config, $request, $user, $notices_repository, $notices_seen_repository);
		$controller->close_notice();

		unset($phpbb_path_helper);
		unset($phpbb_dispatcher);
		unset($user);
	}

	/**
	 * @depends testCanInstantiate
	 * @_expectedException phpbb\exception\http_exception
	 */
	public function testWithValidHashInPreviewModeAjaxRequest()
	{
		global $user;
		global $phpbb_dispatcher;

		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();

		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\request\request_interface $request */
		$request = new \fq\boardnotices\tests\mock\mock_ajax_request(array('notice_id' => 11, 'hash' => '12345678', 'preview' => 1));

		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->data["hash_" . constants::$ROUTING_CLOSE_HASH_ID] = '12345678';

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$notices_repository->method('getNoticeFromId')->will($this->returnValue(array('notice_id' => 11, 'dismissable' => true)));

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$controller = new controller($config, $request, $user, $notices_repository, $notices_seen_repository);
		$controller->close_notice();

		$phpbb_dispatcher = null;
		$user = null;
	}


	/**
	 * @depends testCanInstantiate
	 * @expectedException \PHPUnit_Framework_Error_Warning
	 * @expectedExceptionMessage INSECURE_REDIRECT
	 */
	public function testWithValidNoticeForGuest()
	{
		global $user;
		global $phpbb_dispatcher;
		global $phpbb_path_helper;

		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();

		$phpbb_path_helper = $this->getMockBuilder('\phpbb\path_helper')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request(array('notice_id' => 11, 'hash' => '12345678', 'redirect' => 'http://localhost/done'));

		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->data["hash_" . constants::$ROUTING_CLOSE_HASH_ID] = '12345678';
		$user->data['is_registered'] = false;

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$notices_repository->method('getNoticeFromId')->will($this->returnValue(array('notice_id' => 11, 'dismissable' => true)));

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$controller = new controller($config, $request, $user, $notices_repository, $notices_seen_repository);
		$controller->close_notice();

		unset($phpbb_path_helper);
		unset($phpbb_dispatcher);
		unset($user);
	}

	/**
	 * @depends testCanInstantiate
	 * @expectedException \PHPUnit_Framework_Error_Warning
	 * @expectedExceptionMessage INSECURE_REDIRECT
	 */
	public function testWithValidNoticeForRegisteredUser()
	{
		global $user;
		global $phpbb_dispatcher;
		global $phpbb_path_helper;

		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();

		$phpbb_path_helper = $this->getMockBuilder('\phpbb\path_helper')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\config\config $config */
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request(array('notice_id' => 11, 'hash' => '12345678', 'redirect' => 'http://localhost/done'));

		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->data["hash_" . constants::$ROUTING_CLOSE_HASH_ID] = '12345678';
		$user->data['is_registered'] = true;
		$user->data['user_id'] = 10;

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();
		$notices_repository->method('getNoticeFromId')->will($this->returnValue(array('notice_id' => 11, 'dismissable' => true)));

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$controller = new controller($config, $request, $user, $notices_repository, $notices_seen_repository);
		$controller->close_notice();

		unset($phpbb_path_helper);
		unset($phpbb_dispatcher);
		unset($user);
	}
}
