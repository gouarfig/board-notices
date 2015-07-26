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

	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
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
		$data_layer = $this->getDataLayer();
		return $data_layer->getAllGroups();
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$group_id = (int) $conditions;
		$valid = $this->user->data['group_id'] == $group_id;
		return $valid;
	}

	public function getTemplateVars()
	{
		return array();
	}

	protected function getDataLayer()
	{
		global $phpbb_container;
		static $data_layer = null;

		if (is_null($data_layer))
		{
			$data_layer = $phpbb_container->get('fq.boardnotices.datalayer');
		}
		return $data_layer;
	}

}
