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

class has_not_posted_for implements rule
{

	private $user;

	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return $this->user->lang['RULE_HAS_NOT_POSTED_FOR'];
	}

	public function getType()
	{
		return 'int';
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$days = unserialize($conditions);
		if ($days === false)
		{
			$days = $conditions;
		}
		if (is_array($days))
		{
			$days = (int) $days[0];
		}
		if ($this->user->data['user_lastpost_time'] > 0)
		{
			$valid = ((time() - $this->user->data['user_lastpost_time']) >= ($days * 24 * 60 * 60));
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
