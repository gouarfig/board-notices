<?php
/**
*
* Board Notices Manager
*
* @version 1.0.0
* @copyright (c) 2015 Fred Quointeau
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace fq\boardnotices\rules;

class in_forum implements rule
{
	private $user;
	private $request;

	public function __construct(\phpbb\user $user, \phpbb\request\request $request) {
		$this->user = $user;
		$this->request = $request;
	}

	public function getDisplayName()
	{
		return "User is currently browsing forum(s)";
	}

	public function getType()
	{
		return 'multiple choice';
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function isTrue($conditions) {
		$valid = false;
		$current_forum_id = $this->request->variable('f', '', false, \phpbb\request\request_interface::REQUEST);
		if (empty($current_forum_id))
		{
			// @TODO: session_forum_id is set after this code is running. That's quite annoying
			$current_forum_id = $this->user->data['session_forum_id'];
		}
		$forums = @unserialize($conditions);
		if ($forums === false)
		{
			// There's only one group
			$forums = array((int)$conditions);
		}
		if (!empty($forums))
		{
			foreach ($forums as $forum_id) {
				$valid = ($current_forum_id == $forum_id);
				if ($valid)
				{
					break;
				}
			}
		}
		return $valid;
	}

	public function getTemplateVars()
	{
		return array();
	}
}
