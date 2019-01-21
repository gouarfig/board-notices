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

class has_not_posted_for extends rule_base implements rule_interface
{
	/** @var \phpbb\user $lang */
	private $user;

	public function __construct(\fq\boardnotices\serializer $serializer, \phpbb\user $user)
	{
		$this->serializer = $serializer;
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_HAS_NOT_POSTED_FOR_1');
	}

	public function getDisplayUnit()
	{
		return $this->user->lang('RULE_HAS_NOT_POSTED_FOR_2');
	}

	public function getType()
	{
		return 'int';
	}

	public function getDefault()
	{
		return 0;
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
