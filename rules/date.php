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

class date extends rule_base implements rule_interface
{
	private $user;

	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return $this->user->lang['RULE_DATE'];
	}

	public function getType()
	{
		return 'date';
	}

	public function getDefault()
	{
		return array(0, 0, 0);
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
		$date = $this->validateArrayOfConditions($conditions);
		if (is_null($date) || !is_array($date) || (count($date) != 3))
		{
			return false;
		}
		$now = getdate();
		$valid = ((($date[0] == 0) || ($now['mday'] == $date[0]))
				&& (($date[1] == 0) || ($now['mon'] == $date[1]))
				&& (($date[2] == 0) || ($now['year'] == $date[2])));
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
