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

class date extends rule_base implements rule_interface
{
	/** @var \phpbb\user $user */
	private $user;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user)
	{
		$this->serializer = $serializer;
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_DATE');
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
		return constants::$RULE_TYPE_DATE;
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
