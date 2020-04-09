<?php

namespace fq\boardnotices\tests\mock;

class mock_api extends \phpbb_test_case implements \fq\boardnotices\service\phpbb\api_interface
{
	/** @var \phpbb\language\language $language */
	private $language;
	/** @var \phpbb\user $user */
	private $user;
	private $userRegistered = false;
	private $userLoggedIn = false;
	private $userId = null;
	private $defaultGroupId = null;
	private $ipAddress = '127.0.0.1';
	private $sessionId = 'session_id';
	private $userRegistrationDate = 0;
	private $userBirthday = '';
	private $userLastPostTime = 0;
	private $userPostCount = 0;
	private $groupNames = array(10 => 'Group Name');
	private $currentForum = 0;
	private $currentTopic = 0;
	private $userLang = 'en';
	private $userRankId = 0;
	private $userRankTitle = '';
	private $userAnonymous = false;
	private $userStyle = null;

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

	/**
	 * Please note this function will also set the registration date, user ID and default group ID
	 *
	 * @param bool $registered
	 * @return void
	 */
	public function setUserRegistered($registered, $userId = null, $defaultGroupId = null)
	{
		$this->userRegistered = $registered ? true : false;
		if ($this->userRegistered && empty($this->userRegistrationDate))
		{
			$this->userRegistrationDate = mktime(12, 11, 00, 3, 2, 2010);
			$this->userId = $userId;
			if (empty($this->userId))
			{
				$this->userId = 1;
			}
			$this->defaultGroupId = $defaultGroupId;
			if (empty($this->defaultGroupId))
			{
				$this->defaultGroupId = 1;
			}
		}
		else if (!$this->userRegistered)
		{
			$this->userRegistrationDate = 0;
			$this->userId = null;
			$this->defaultGroupId = null;
		}
	}

	public function setUserAnonymous($anonymous = true)
	{
		$this->userAnonymous = $anonymous ? true : false;
	}

	public function setUserLoggedIn($loggedIn)
	{
		$this->userLoggedIn = $loggedIn ? true : false;
	}

	/**
	 * Please note this method will also set the user registered
	 */
	public function setUserBirthday($birthday)
	{
		$this->userRegistered = true;
		$this->userBirthday = $birthday;
		return $this;
	}

	public function setUserLastPostTime($time)
	{
		$this->userLastPostTime = $time;
	}

	public function setUserPostCount($count)
	{
		$this->userPostCount = $count;
	}

	public function setUserRank($rankId, $rankTitle)
	{
		$this->userRankId = (int) $rankId;
		$this->userRankTitle = $rankTitle;
	}

	public function setUserStyle($style)
	{
		$this->userStyle = $style;
	}

	public function setCurrentForum($forumId, $topicId = null)
	{
		$this->currentForum = $forumId;
		if ($topicId !== null)
		{
			$this->currentTopic = $topicId;
		}
	}
	public function setUserLanguage($lang)
	{
		$this->userLang = $lang;
	}

	public function isUserRegistered()
	{
		return $this->userRegistered;
	}

	public function isUserAnonymous()
	{
		return $this->userAnonymous;
	}

	public function isUserLoggedIn()
	{
		return $this->userLoggedIn;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getUserDefaultGroupId()
	{
		return $this->defaultGroupId;
	}

	public function getUserIpAddress()
	{
		return $this->ipAddress;
	}

	public function getSessionId()
	{
		return $this->sessionId;
	}

	public function getUserRegistrationDate()
	{
		return $this->userRegistrationDate;
	}

	public function getUserBirthday()
	{
		return $this->userBirthday;
	}

	public function getUserLastPostTime()
	{
		return $this->userLastPostTime;
	}

	public function getUserPostCount()
	{
		return $this->userPostCount;
	}

	public function getUserRankId()
	{
		return $this->userRankId;
	}

	public function getUserRankTitle()
	{
		return $this->userRankTitle;
	}

	public function getUserStyle()
	{
		return $this->userStyle;
	}

	public function createDateTime($time = 'now', \DateTimeZone $timezone = null)
	{
		return $this->user->create_datetime($time, $timezone);
	}

	public function lang()
	{
		$args = func_get_args();
		return $args[0];
	}

	public function addAdminLanguage()
	{
		// Nothing to do here
	}

	public function getGroupName($groupId)
	{
		return !empty($this->groupNames[$groupId]) ? $this->groupNames[$groupId] : null;
	}

	public function getCurrentForum()
	{
		return $this->currentForum;
	}

	public function getCurrentTopic()
	{
		return $this->currentTopic;
	}

	public function getUserLanguage()
	{
		return $this->userLang;
	}
}
