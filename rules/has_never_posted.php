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

class has_never_posted extends rule_base implements rule_interface
{

	private $user;
	private $data_layer;

	public function __construct(\phpbb\user $user, \fq\boardnotices\repository\legacy_interface $data_layer)
	{
		$this->user = $user;
		$this->data_layer = $data_layer;
	}

	public function getDisplayName()
	{
		return $this->user->lang['RULE_HAS_NEVER_POSTED'];
	}

	public function getType()
	{
		return 'n/a';
	}

	public function getDefault()
	{
		return null;
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
		$posts = $this->data_layer->nonDeletedUserPosts();
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
