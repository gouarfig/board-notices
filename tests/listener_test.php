<?php

namespace fq\boardnotices\tests;

use fq\boardnotices\event\listener;
use fq\boardnotices\service\constants;

class listener_test extends \PHPUnit_Framework_TestCase
{
	public function testSubscribedEvents()
	{
		$events = listener::getSubscribedEvents();
		$this->assertNotNull($events);
		$this->assertCount(1, $events);
	}

	public function testCanInstantiate()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\config\config $config */
		$config = new \phpbb\config\config(array());

		/** @var \phpbb\template\template $template */
		$template = $this->getMockBuilder('\phpbb\template\template')->disableOriginalConstructor()->getMock();
		// Make sure nothing is written out to the template
		$template->expects($this->never())->method('assign_vars');

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();

		/** @var \phpbb\controller\helper $controller_helper */
		$controller_helper = $this->getMockBuilder('\phpbb\controller\helper')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$listener = new listener($user, $config, $template, $request, $controller_helper, $language, $notices_repository, $notices_seen_repository);
		$this->assertNotNull($listener);
	}

	/**
	 * @depends testCanInstantiate
	 */
	function testExtensionNotEnabled()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\config\config $config */
		$config = new \phpbb\config\config(array(
			constants::$CONFIG_ENABLED => false,
		));

		/** @var \phpbb\template\template $template */
		$template = $this->getMockBuilder('\phpbb\template\template')->disableOriginalConstructor()->getMock();
		// Make sure nothing is written out to the template
		$template->expects($this->never())->method('assign_vars');

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request();

		/** @var \phpbb\controller\helper $controller_helper */
		$controller_helper = $this->getMockBuilder('\phpbb\controller\helper')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$listener = new listener($user, $config, $template, $request, $controller_helper, $language, $notices_repository, $notices_seen_repository);
		$listener->display_board_notices();
	}


	/**
	 * @depends testCanInstantiate
	 */
	function testPreviewWithWrongKey()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\config\config $config */
		$config = new \phpbb\config\config(array(
			constants::$CONFIG_ENABLED => false,
			constants::$CONFIG_PREVIEW_KEY => '123',
		));

		/** @var \phpbb\template\template $template */
		$template = $this->getMockBuilder('\phpbb\template\template')->disableOriginalConstructor()->getMock();
		// Make sure nothing is written out to the template
		$template->expects($this->never())->method('assign_vars');

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request(array('bnid' => 11, 'bnpk' => '456'));

		/** @var \phpbb\controller\helper $controller_helper */
		$controller_helper = $this->getMockBuilder('\phpbb\controller\helper')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$listener = new listener($user, $config, $template, $request, $controller_helper, $language, $notices_repository, $notices_seen_repository);
		$listener->display_board_notices();
	}

	/**
	 * @depends testCanInstantiate
	 */
	function testEmptyPreview()
	{
		global $phpbb_dispatcher;
		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();

		global $cache;
		$cache = $this->getMockBuilder('\phpbb\cache\service')->disableOriginalConstructor()->getMock();
		$cache->method('obtain_word_list')->will($this->returnValue(array()));

		global $user;
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\config\config $config */
		$config = new \phpbb\config\config(array(
			constants::$CONFIG_ENABLED => false,
			constants::$CONFIG_PREVIEW_KEY => 'pkey',
		));

		/** @var \phpbb\template\template $template */
		$template = $this->getMockBuilder('\phpbb\template\template')->disableOriginalConstructor()->getMock();
		// Make sure nothing is written out to the template
		$template->expects($this->never())->method('assign_vars');

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request(array('bnid' => 11, 'bnpk' => 'pkey'));

		/** @var \phpbb\controller\helper $controller_helper */
		$controller_helper = $this->getMockBuilder('\phpbb\controller\helper')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$listener = new listener($user, $config, $template, $request, $controller_helper, $language, $notices_repository, $notices_seen_repository);
		$listener->display_board_notices();

		unset($cache);
		unset($phpbb_dispatcher);
		unset($user);
	}

	/**
	 * @depends testCanInstantiate
	 */
	function testNoNotice()
	{
		global $phpbb_dispatcher;
		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();

		global $cache;
		$cache = $this->getMockBuilder('\phpbb\cache\service')->disableOriginalConstructor()->getMock();

		global $user;
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\config\config $config */
		$config = new \phpbb\config\config(array(
			constants::$CONFIG_ENABLED => true,
		));

		/** @var \phpbb\template\template $template */
		$template = $this->getMockBuilder('\phpbb\template\template')->disableOriginalConstructor()->getMock();
		// Make sure nothing is written out to the template
		$template->expects($this->never())->method('assign_vars');

		/** @var \phpbb\request\request_interface $request */
		$request = new \phpbb_mock_request(array());

		/** @var \phpbb\controller\helper $controller_helper */
		$controller_helper = $this->getMockBuilder('\phpbb\controller\helper')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
		$notices_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_interface')->getMock();

		/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
		$notices_seen_repository = $this->getMockBuilder('\fq\boardnotices\repository\notices_seen_interface')->getMock();

		$listener = new listener($user, $config, $template, $request, $controller_helper, $language, $notices_repository, $notices_seen_repository);
		$listener->display_board_notices();

		unset($cache);
		unset($phpbb_dispatcher);
		unset($user);
	}
}
