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

class has_never_posted_in_forum implements rule
{

	private $user;
	private $data_layer;

	public function __construct(\phpbb\user $user, \fq\boardnotices\dac\datalayer_interface $data_layer)
	{
		$this->user = $user;
		$this->data_layer = $data_layer;
	}

	public function getDisplayName()
	{
		return $this->user->lang['RULE_HAS_NEVER_POSTED_IN_FORUM'];
	}

	public function getType()
	{
		return 'forums';
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$forums = array();
		if (!empty($conditions))
		{
			$forums = @unserialize($conditions);
			if ($forums === false)
			{
				// There's only one forum
				$forums = array((int) $conditions);
			}
			else if (!is_array($forums) && !empty($forums))
			{
				$forums = array((int) $forums);
			}
		}
		$posts = $this->data_layer->nonDeletedUserPosts($forums);
		$valid = ($posts == 0);
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
