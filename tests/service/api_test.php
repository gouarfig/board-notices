<?php

namespace fq\boardnotices\tests\service;

use fq\boardnotices\service\phpbb\api;

class api_test extends \phpbb_test_case
{
	private function createDependencies()
	{
		/** @var \fq\boardnotices\service\phpbb\functions_interface $functions */
		$functions = $this->getMockBuilder('\fq\boardnotices\service\phpbb\functions_interface')->getMock();

		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\request\request $request */
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();

		return array($functions, $user, $language, $request);
	}

	public function testCanInstantiate()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$api = new api($functions, $user, $language, $request);
		$this->assertNotNull($api);
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserIsRegistered()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['is_registered'] = true;

		$api = new api($functions, $user, $language, $request);
		$this->assertTrue($api->isUserRegistered());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserIsNotRegistered()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['is_registered'] = false;

		$api = new api($functions, $user, $language, $request);
		$this->assertFalse($api->isUserRegistered());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserIsNotAnonymous()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['user_id'] = 1001;

		$api = new api($functions, $user, $language, $request);
		$this->assertFalse($api->isUserAnonymous());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserIsAnonymous()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['user_id'] = ANONYMOUS;

		$api = new api($functions, $user, $language, $request);
		$this->assertTrue($api->isUserAnonymous());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserHasNoId()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(0, $api->getUserId());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetUserId()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['user_id'] = 11;

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(11, $api->getUserId());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetDefaultGroupId()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['group_id'] = 11;

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(11, $api->getUserDefaultGroupId());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserHasNoRegistrationDate()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();

		$api = new api($functions, $user, $language, $request);
		$this->assertNull($api->getUserRegistrationDate());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserRegistrationDate()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['user_regdate'] = 100;

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(100, $api->getUserRegistrationDate());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testCanGetEmptyLanguageString()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$language->method('lang')->will($this->returnArgument(0));

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals('TEST_LANG', $api->lang('TEST_LANG'));
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testCanGetDateTime()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->method('create_datetime')->will($this->returnValue(100));

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(100, $api->createDateTime());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserHasNoBirthday()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals('', $api->getUserBirthday());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetUserBirthday()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['user_birthday'] = '31-12-2010';

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals('31-12-2010', $api->getUserBirthday());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetSessionId()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['session_id'] = 'session_id';

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals('session_id', $api->getSessionId());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetUserIpAddress()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->ip = '10.0.0.1';

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals('10.0.0.1', $api->getUserIpAddress());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetLastPostTimeIfNeverPosted()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(0, $api->getUserLastPostTime());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetUserLastPostTime()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['user_lastpost_time'] = 11;

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(11, $api->getUserLastPostTime());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetUserPostCount()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['user_posts'] = 110;

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(110, $api->getUserPostCount());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetUserStyle()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['user_style'] = 110;

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(110, $api->getUserStyle());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testCanLoadLanguageForAdminModule()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$language->expects($this->once())->method('add_lang');

		$api = new api($functions, $user, $language, $request);
		$api->addAdminLanguage();
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testNotInForum()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(0, $api->getCurrentForum());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testInCurrentForum()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$request->method('variable')->will($this->returnValue(1001));

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(1001, $api->getCurrentForum());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testNotInTopic()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(0, $api->getCurrentTopic());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testInCurrentTopic()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$request->method('variable')->will($this->returnValue(2002));

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals(2002, $api->getCurrentTopic());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetLanguage()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();
		$user->data['user_lang'] = 'en-test';

		$api = new api($functions, $user, $language, $request);
		$this->assertEquals('en-test', $api->getUserLanguage());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testLoggedInStatus()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();

		$api = new api($functions, $user, $language, $request);
		$user->data['user_type'] = USER_IGNORE;

		$this->assertEquals(false, $api->isUserLoggedIn());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testDefaultUserRankId()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();

		$api = new api($functions, $user, $language, $request);

		$this->assertEquals(0, $api->getUserRankId());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testDefaultUserRankTitle()
	{
		list($functions, $user, $language, $request) = $this->createDependencies();

		$api = new api($functions, $user, $language, $request);

		$this->assertEquals('', $api->getUserRankTitle());
	}
}
