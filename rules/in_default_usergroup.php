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

class in_default_usergroup extends rule_base implements rule_interface
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
		return $this->user->lang['RULE_IN_DEFAULT_USERGROUP'];
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
		$group_id = $this->validateUniqueCondition($conditions);
		$valid = $this->user->data['group_id'] == $group_id;
		return $valid;
	}

	public function getAvailableVars()
	{
		return array('GROUPID', 'GROUPNAME');
	}

	public function getTemplateVars()
	{
		// @codeCoverageIgnoreStart
		if (!function_exists('get_group_name'))
		{
			$this->includeUserFunctions();
		}
		// @codeCoverageIgnoreEnd
		return array(
			'GROUPID' => $this->user->data['group_id'],
			'GROUPNAME' => get_group_name($this->user->data['group_id']),
		);
	}

}
