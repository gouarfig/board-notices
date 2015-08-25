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

class in_forum extends rule_base implements rule_interface
{

	private $user;
	private $request;

	public function __construct(\phpbb\user $user, \phpbb\request\request $request)
	{
		$this->user = $user;
		$this->request = $request;
	}

	public function getDisplayName()
	{
		return "User is currently browsing one of these forum(s)";
	}

	public function getDisplayUnit()
	{
		return '';
	}

	public function getType()
	{
		return 'forums';
	}

	public function getDefault()
	{
		return array();
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function validateValues($values)
	{
		return true;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$current_forum_id = $this->request->variable('f', 0);

		$forums = @unserialize($conditions);
		if ($forums === false)
		{
			// There's only one group
			$forums = array((int) $conditions);
		}
		if (!empty($forums))
		{
			foreach ($forums as $forum_id)
			{
				$valid = ($current_forum_id == $forum_id);
				if ($valid)
				{
					break;
				}
			}
		}
		return $valid;
	}

	public function getAvailableVars()
	{
		return array();
	}

	public function getTemplateVars()
	{
		return array();
	}

}
