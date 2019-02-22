<?php

namespace fq\boardnotices\tests\service;

use fq\boardnotices\service\phpbb\api;

class api_test extends \phpbb_test_case
{
	public function testCanInstantiate()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertNotNull($api);
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserIsRegistered()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->data['is_registered'] = true;

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertTrue($api->isUserRegistered());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserIsNotRegistered()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->data['is_registered'] = false;

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertFalse($api->isUserRegistered());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserHasNoId()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertEquals(0, $api->getUserId());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetUserId()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->data['user_id'] = 11;

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertEquals(11, $api->getUserId());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserHasNoRegistrationDate()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertNull($api->getUserRegistrationDate());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserRegistrationDate()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->data['user_regdate'] = 100;

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertEquals(100, $api->getUserRegistrationDate());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testCanGetEmptyLanguageString()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();
		$language->method('lang')->will($this->returnArgument(0));

		$api = new api($user, $language);
		$this->assertEquals('TEST_LANG', $api->lang('TEST_LANG'));
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testCanGetDateTime()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->method('create_datetime')->will($this->returnValue(100));

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertEquals(100, $api->createDateTime());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testUserHasNoBirthday()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertEquals('', $api->getUserBirthday());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetUserBirthday()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->data['user_birthday'] = '31-12-2010';

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertEquals('31-12-2010', $api->getUserBirthday());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetSessionId()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->data['session_id'] = 'session_id';

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertEquals('session_id', $api->getSessionId());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testGetUserIpAddress()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->ip = '10.0.0.1';

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();

		$api = new api($user, $language);
		$this->assertEquals('10.0.0.1', $api->getUserIpAddress());
	}

	/**
	 * @depends testCanInstantiate
	 */
	public function testCanLoadLanguageForAdminModule()
	{
		/** @var \phpbb\user $user */
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();

		/** @var \phpbb\language\language $language */
		$language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();
		$language->expects($this->once())->method('add_lang');

		$api = new api($user, $language);
		$api->addAdminLanguage();
	}
}
