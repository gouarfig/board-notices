<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class has_never_visited extends rule_base implements rule_interface
{
	/** @var \phpbb\user $lang */
	private $user;
	/** @var \fq\boardnotices\repository\users_interface $repository */
	private $repository;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user, \fq\boardnotices\repository\users_interface $repository)
	{
		$this->serializer = $serializer;
		$this->user = $user;
		$this->repository = $repository;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_HAS_NEVER_VISITED');
	}

	public function getDisplayExplain()
	{
		return '';
	}

	public function getDisplayUnit()
	{
		return '';
	}

	public function getType()
	{
		return constants::$RULE_TYPE_FORUMS;
	}

	public function getDefault()
	{
		return array(0);
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
		$forums = $this->validateArrayOfConditions($conditions);
		if (empty($forums))
		{
			return false;
		}
		if (!$this->user->data['is_registered'])
		{
			return false;
		}
		$visits = $this->repository->getForumsLastReadTime($this->user->data['user_id']);
		if (empty($visits))
		{
			return true;
		}
		foreach ($forums as $forum_id)
		{
			if (!empty($visits[$forum_id]))
			{
				return false;
			}
		}
		return true;
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
