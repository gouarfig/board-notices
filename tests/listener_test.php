<?php

namespace fq\boardnotices\tests;

use fq\boardnotices\event\listener;

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
		$config = $this->getMockBuilder('\phpbb\config\config')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\template\template $template */
		$template = $this->getMockBuilder('\phpbb\template\template')->disableOriginalConstructor()->getMock();

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
}
