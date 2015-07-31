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

class in_default_usergroup implements rule
{

	private $user;
	private $data_layer;

	public function __construct(\phpbb\user $user, \fq\boardnotices\datalayer $data_layer)
	{
		$this->user = $user;
		$this->data_layer = $data_layer;
	}

	public function getDisplayName()
	{
		return "User default group is";
	}

	public function getType()
	{
		return 'list';
	}

	public function getPossibleValues()
	{
		return $this->data_layer->getAllGroups();
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$group_id = @unserialize($conditions);
		if ($group_id === false)
		{
			$group_id = (int) $conditions;
		}
		if (is_array($group_id))
		{
			$group_id = (int) $group_id[0];
		}
		$valid = $this->user->data['group_id'] == $group_id;
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
