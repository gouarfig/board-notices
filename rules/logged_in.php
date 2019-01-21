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

class logged_in extends rule_base implements rule_interface
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
		return "Is user logged in";
	}

	public function getDisplayUnit()
	{
		return '';
	}

	public function getType()
	{
		return 'yesno';
	}

	public function getDefault()
	{
		return false;
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
		$logged_in_conditions = $this->serializer->decode($conditions);
		if ($logged_in_conditions === false)
		{
			$logged_in_conditions = $conditions;
		}
		if (is_array($logged_in_conditions))
		{
			$logged_in_conditions = $logged_in_conditions[0];
		}
		$logged_in = ($this->user->data['user_type'] != USER_IGNORE);
		$valid = ($logged_in_conditions && $logged_in) || (!$logged_in_conditions && !$logged_in);
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
