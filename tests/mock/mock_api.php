<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\tests\mock;

class mock_api extends \phpbb_test_case implements \fq\boardnotices\service\phpbb\api_interface
{
	/** @var \phpbb\language\language $language */
	private $language;
	/** @var \phpbb\user $user */
	private $user;
	private $userRegistered = false;
	private $userId = null;
	private $userRegistrationDate = 0;

	public function __construct()
	{
		// We still need some kind of user to generate datetime (or we would have to copy the code)
		$this->language = $this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock();
		$this->user = new \phpbb\user($this->language, '\phpbb\datetime');
	}

	public function setTimezone($timezone)
	{
		$this->user->timezone = new \DateTimeZone($timezone);
		return $this;
	}

	/**
	 * Please note this method will also set the user registered
	 */
	public function setUserRegistrationDate($userRegistrationDate)
	{
		$this->userRegistered = true;
		$this->userRegistrationDate = $userRegistrationDate;
		return $this;
	}

	public function isUserRegistered()
	{
		return $this->userRegistered;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getUserRegistrationDate()
	{
		return $this->userRegistrationDate;
	}

	public function createDateTime($time = 'now', \DateTimeZone $timezone = null)
	{
		return $this->user->create_datetime($time, $timezone);
	}

	function lang()
	{
		$args = func_get_args();
		return $args[0];
	}
}
