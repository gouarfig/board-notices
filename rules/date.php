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

class date implements rule
{

	public function __construct()
	{
	}

	public function getDisplayName()
	{
		return "The date is";
	}

	public function getType()
	{
		return 'date';
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$conditions = unserialize($conditions);
		$now = getdate();
		$valid = ((($conditions[0] == 0) || ($now['mday'] == $conditions[0]))
				&& (($conditions[1] == 0) || ($now['mon'] == $conditions[1]))
				&& (($conditions[2] == 0) || ($now['year'] == $conditions[2])));
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
