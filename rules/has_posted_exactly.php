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

class has_posted_exactly extends rule_base implements rule_interface
{

	private $user;

	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return $this->user->lang['RULE_HAS_POSTED_EXACTLY'];
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
		$posts = unserialize($conditions);
		if ($posts === false)
		{
			$posts = $conditions;
		}
		if (is_array($posts))
		{
			$posts = (int) $posts[0];
		}
		$valid = ($this->user->data['user_posts'] == $posts);
		return $valid;
	}

	public function getAvailableVars()
	{
		return array('POSTS');
	}

	public function getTemplateVars()
	{
		return array('POSTS' => $this->user->data['user_posts']);
	}

}