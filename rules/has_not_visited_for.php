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

class has_not_visited_for extends rule_base implements rule_interface
{

	private $user;
	private $repository;

	public function __construct(\phpbb\user $user, \fq\boardnotices\repository\legacy_interface $repository)
	{
		$this->user = $user;
		$this->repository = $repository;
	}

	public function getDisplayName()
	{
		return array(
			$this->user->lang['RULE_HAS_NOT_VISITED_FOR_1'],
			$this->user->lang['RULE_HAS_NOT_VISITED_FOR_2'],
		);
	}

	public function getDisplayUnit()
	{
		return $this->user->lang['RULE_DAY(S)'];
	}

	public function getType()
	{
		return 'forums|int';
	}

	public function getDefault()
	{
		return array(0, 0);
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
		$days = $this->validateUniqueCondition($conditions);
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