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

class in_usergroup implements rule
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
		return "User is in group";
	}

	public function getType()
	{
		return 'multiple choice';
	}

	public function getPossibleValues()
	{
		return $this->data_layer->getAllGroups();
	}

	public function isTrue($conditions)
	{
		$valid = false;

		$groups = @unserialize($conditions);
		if ($groups === false)
		{
			// There's only one group
			$groups = array((int) $conditions);
		}
		if (!empty($groups))
		{
			foreach ($groups as $group_id)
			{
				$valid = $this->data_layer->isUserInGroupId($group_id);
				if (!$valid)
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
