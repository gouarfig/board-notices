<?php

namespace fq\boardnotices\service\phpbb;

/**
 * This class is encapsulating all the need to access the internals of phpBB
 * This will help with change inside phpBB (for example from 3.1 to 3.2)
 * And it will also help with unit testing
 */
class api implements api_interface
{
	/** @var \fq\boardnotices\service\phpbb\functions_interface $functions */
	private $functions;
	/** @var \phpbb\user $user */
	private $user;
	/** @var \phpbb\language\language $language */
	private $language;
	/** @var \phpbb\request\request $request */
	private $request;

	public function __construct(
		\fq\boardnotices\service\phpbb\functions_interface $functions,
		\phpbb\user $user,
		\phpbb\language\language $language,
		\phpbb\request\request $request)
	{
		$this->functions = $functions;
		$this->user = $user;
		$this->language = $language;
		$this->request = $request;
	}

	public function isUserRegistered()
	{
		return !empty($this->user->data['is_registered']);
	}

	public function isUserAnonymous()
	{
		return $this->getUserId() == ANONYMOUS;
	}

	public function isUserLoggedIn()
	{
		return $this->user->data['user_type'] != USER_IGNORE;
	}

	public function getUserId()
	{
		return isset($this->user->data['user_id']) ? $this->user->data['user_id'] : 0;
	}

	public function getUserDefaultGroupId()
	{
		return isset($this->user->data['group_id']) ? $this->user->data['group_id'] : 0;
	}

	public function getUserIpAddress()
	{
		return $this->user->ip;
	}

	public function getSessionId()
	{
		return $this->user->data['session_id'];
	}

	public function getUserRegistrationDate()
	{
		return isset($this->user->data['user_regdate']) ? $this->user->data['user_regdate'] : null;
	}

	public function getUserBirthday()
	{
		return isset($this->user->data['user_birthday']) ? $this->user->data['user_birthday'] : '';
	}

	public function getUserLastPostTime()
	{
		return isset($this->user->data['user_lastpost_time']) ? $this->user->data['user_lastpost_time'] : 0;
	}

	public function getUserPostCount()
	{
		return isset($this->user->data['user_posts']) ? $this->user->data['user_posts'] : 0;
	}

	public function getUserRankId()
	{
		return isset($this->user->data['user_rank']) ? $this->user->data['user_rank'] : 0;
	}

	public function getUserRankTitle()
	{
		return $this->functions->phpbb_get_user_rank(
			$this->user->data,
			($this->isUserAnonymous()) ? false : $this->getUserPostCount()
		);
	}

	public function getUserStyle()
	{
		return !empty($this->user->data['user_style']) ? $this->user->data['user_style'] : null;
	}

	public function createDateTime($time = 'now', \DateTimeZone $timezone = null)
	{
		return $this->user->create_datetime($time, $timezone);
	}

	public function lang()
	{
		$args = func_get_args();
		return call_user_func_array(array($this->language, 'lang'), $args);
	}

	public function addAdminLanguage()
	{
		$this->language->add_lang('boardnotices_acp', 'fq/boardnotices');
	}

	public function getGroupName($groupId)
	{
		return $this->functions->get_group_name($groupId);
	}

	public function getCurrentForum()
	{
		return $this->request->variable('f', 0);
	}

	public function getCurrentTopic()
	{
		return $this->request->variable('t', 0);
	}

	public function getUserLanguage()
	{
		return $this->user->data['user_lang'];
	}
}
